<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use App\Models\Invoice;
use App\Models\InventoryAdjustment; 
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with search, filtering, and summary metrics.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'items', 'supplier']);

        // 1. Search Filter (Invoice #, Customer Name, or PAN)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cQuery) use ($search) {
                      $cQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('phone_number', 'like', "%{$search}%")
                             ->orWhere('pan_number', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Payment Status Filter
        if ($request->filled('status')) {
            $status = strtolower($request->status);
            if ($status === 'paid') {
                $query->whereRaw('paid_amount >= grand_total AND grand_total > 0');
            } elseif ($status === 'partial') {
                $query->whereRaw('paid_amount > 0 AND paid_amount < grand_total');
            } elseif ($status === 'unpaid') {
                $query->whereRaw('paid_amount = 0 OR paid_amount IS NULL');
            }
        }

        // 3. Fiscal Year Filter (Optional)
        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        // 4. Pre-calculate Summary Totals before applying pagination
        $totalInvoicedAmount = (clone $query)->sum('grand_total');
        $totalPaidAmount     = (clone $query)->sum('paid_amount');
        $totalDueAmount      = max(0, $totalInvoicedAmount - $totalPaidAmount);

        // 5. Fetch Paginated Records
        $invoices = $query->latest()->paginate(15)->appends($request->query());

        return view('admin.invoices.index', compact(
            'invoices',
            'totalInvoicedAmount',
            'totalPaidAmount',
            'totalDueAmount'
        ));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        // 1. Fetch Customers
        $customers = Customer::orderBy('name')->get();

        // 2. Fetch Products with stock
        $products = Product::where('initial_stock', '>', 0)->get();

        // 3. Generate Invoice Number
        $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // 4. Set Nepali Date
        $nepaliDate = "2083-02-29"; 

        return view('admin.invoices.create', compact('customers', 'products', 'invoiceNo', 'nepaliDate'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $invoice = Invoice::create([
                    'invoice_no'      => $request->invoice_no,
                    'invoice_date'    => $request->invoice_date ?? now()->toDateString(),
                    'customer_id'     => $request->customer_id,
                    'patient_name'    => $request->patient_name ?? 'Walk-in',
                    'patient_address' => $request->patient_address ?? 'N/A',
                    'patient_city'    => $request->patient_city ?? 'N/A',
                    'grand_total'     => $request->grand_total,
                ]);

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $qty = ($item['unit'] === 'g') ? ($item['qty'] / 1000) : $item['qty'];

                    if ($product->initial_stock < $qty) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }

                    $product->decrement('initial_stock', $qty);
                    $invoice->items()->create([
                        'product_id' => $item['product_id'],
                        'qty'        => $item['qty'],
                        'unit'       => $item['unit'],
                        'price'      => $product->selling_price ?? $item['price'] ?? 0
                    ]);
                }
            });

            return response()->json(['success' => true, 'redirect' => route('admin.invoices.index')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Display single invoice details.
     */
    public function show($id)
    {
        $invoice = Invoice::with(['items.product', 'supplier', 'customer'])->findOrFail($id);

        $calculatedSubtotal = $invoice->items->sum(function($item) {
            return $item->qty * $item->price;
        });

        return view('admin.invoices.show', compact('invoice', 'calculatedSubtotal'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['items.product', 'customer']);

        $itemLines = $invoice->items->map(function ($item) {
            return [
                'invoice_item_id' => $item->id,
                'product_id'      => $item->product_id,
                'rate_per_kg'     => $item->price,
                'quantity_kg'     => floor($item->qty),
                'quantity_gm'     => round(($item->qty - floor($item->qty)) * 1000),
                'total'           => $item->total,
                '_delete'         => false,
            ];
        })->values();

        $customers = Customer::orderBy('name')->get();
        $products  = Product::orderBy('name')->get();

        return view('admin.invoices.edit', compact('invoice', 'itemLines', 'customers', 'products'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_date'            => 'required|date',
            'customer_id'             => 'required|exists:customers,id',
            'payment_method'          => 'required|string|in:Cash,Online Payment,Bank Transfer,Credit Sale',
            'include_vat'             => 'required|boolean',
            'discount'                => 'required|numeric|min:0',
            'paid_amount'             => 'required|numeric|min:0',
            'remarks'                 => 'nullable|string|max:1000',
            'transaction_date'        => 'nullable|string|max:20',
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|integer|exists:products,id',
            'items.*.invoice_item_id' => 'nullable|integer|exists:invoice_items,id',
            'items.*.rate_per_kg'     => 'required|numeric|min:0',
            'items.*.quantity_kg'     => 'required|numeric|min:0',
            'items.*.quantity_gm'     => 'required|numeric|min:0|max:999.99',
            'items.*._delete'         => 'nullable|boolean',
        ]);

        $hasValidQty = collect($validated['items'])->contains(function ($item) {
            return !isset($item['_delete']) && ((float)$item['quantity_kg'] + (float)$item['quantity_gm'] / 1000) > 0;
        });

        if (! $hasValidQty) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid quantity for at least one item.'], 422);
        }

        try {
            DB::transaction(function () use ($validated, $invoice) {
                $subtotal = 0;
                $processedItemIds = [];

                // Step 1: Revert stock for all existing items in the invoice
                foreach ($invoice->items as $existingItem) {
                    $product = Product::lockForUpdate()->find($existingItem->product_id);
                    if ($product) {
                        $product->increment('initial_stock', $existingItem->qty);
                    }
                }

                // Step 2: Process new/updated/deleted items
                foreach ($validated['items'] as $itemInput) {
                    if (isset($itemInput['_delete']) && $itemInput['_delete']) {
                        if (isset($itemInput['invoice_item_id'])) {
                            $invoice->items()->where('id', $itemInput['invoice_item_id'])->delete();
                        }
                        continue;
                    }

                    $totalWeight = (float)$itemInput['quantity_kg'] + ((float)$itemInput['quantity_gm'] / 1000);
                    if ($totalWeight <= 0) continue;

                    $product = Product::lockForUpdate()->findOrFail($itemInput['product_id']);
                    if ((float)$product->initial_stock < $totalWeight) {
                        throw new \Exception("Insufficient stock for \"{$product->name}\". Available: {$product->initial_stock}, Requested: {$totalWeight}.");
                    }

                    $itemSubtotal = (float)$itemInput['rate_per_kg'] * $totalWeight;
                    $subtotal += $itemSubtotal;

                    $itemData = [
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'qty'          => $totalWeight,
                        'unit'         => $product->inventory_unit ?? 'KG',
                        'price'        => (float)$itemInput['rate_per_kg'],
                        'total'        => $itemSubtotal,
                    ];

                    if (isset($itemInput['invoice_item_id'])) {
                        $invoice->items()->where('id', $itemInput['invoice_item_id'])->update($itemData);
                        $processedItemIds[] = $itemInput['invoice_item_id'];
                    } else {
                        $newItem = $invoice->items()->create($itemData);
                        $processedItemIds[] = $newItem->id;
                    }

                    $product->decrement('initial_stock', $totalWeight);
                }

                // Step 3: Delete old items omitted from validated payload
                $invoice->items()->whereNotIn('id', $processedItemIds)->delete();

                // Step 4: Recalculate totals & adjust customer balance
                $discount   = (float)$validated['discount'];
                $taxable    = max(0, $subtotal - $discount);
                $vat        = (bool)$validated['include_vat'] ? round($taxable * 0.13, 2) : 0.00;
                $grandTotal = round($taxable + $vat, 2);
                $paidAmount = round($validated['payment_method'] === 'Credit Sale' ? min((float)$validated['paid_amount'], $grandTotal) : $grandTotal, 2);

                $oldDue = $invoice->grand_total - $invoice->paid_amount;
                $newDue = $grandTotal - $paidAmount;

                if ($oldDue != $newDue) {
                    $customer = Customer::find($invoice->customer_id);
                    if ($customer) {
                        $customer->decrement('previous_due', $oldDue);
                        $customer->increment('previous_due', $newDue);
                        $customer->save();
                    }
                }

                $invoice->update([
                    'invoice_date'    => $validated['invoice_date'],
                    'customer_id'     => $validated['customer_id'],
                    'payment_method'  => $validated['payment_method'],
                    'include_vat'     => $validated['include_vat'],
                    'discount'        => $discount,
                    'paid_amount'     => $paidAmount,
                    'remarks'         => $validated['remarks'] ?? null,
                    'nepali_date'     => $validated['transaction_date'] ?? null,
                    'subtotal'        => round($subtotal, 2),
                    'taxable_amount'  => $taxable,
                    'vat_amount'      => $vat,
                    'grand_total'     => $grandTotal,
                    'status'          => ($paidAmount >= $grandTotal) ? 'Paid' : 'Credit',
                ]);
            });

            return response()->json([
                'success'  => true,
                'message'  => 'Invoice #' . ($invoice->invoice_no ?? $invoice->id) . ' updated successfully!',
                'redirect' => route('admin.invoices.show', $invoice->id),
            ]);

        } catch (\Throwable $e) {
            Log::error('Invoice Update Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        try {
            DB::transaction(function () use ($invoice) {
                // Revert stock for all items
                foreach ($invoice->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('initial_stock', $item->qty);
                    }
                }

                // Adjust customer due
                $dueAmount = $invoice->grand_total - $invoice->paid_amount;
                if ($dueAmount > 0 && $invoice->customer_id) {
                    $customer = Customer::find($invoice->customer_id);
                    if ($customer) {
                        $customer->decrement('previous_due', $dueAmount);
                    }
                }

                $invoice->delete();
            });

            return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted successfully and stock/dues reverted.');
        } catch (\Exception $e) {
            Log::error('Invoice Deletion Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    /**
     * Generate PNG image for social/messenger sharing.
     */
    public function generateShareableImage($id)
    {
        $invoice = Invoice::with('customer')->findOrFail($id);
        
        $html = view('admin.pdf.statement', compact('invoice'))->render();

        $fileName = 'Statement_' . ($invoice->invoice_no ?? $invoice->id) . '_' . time() . '.png';
        $directory = 'public/shares';
        
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        $path = storage_path('app/' . $directory . '/' . $fileName);

        Browsershot::html($html)
            ->setScreenshotType('png')
            ->windowSize(1080, 1350)
            ->deviceScaleFactor(2)
            ->save($path);

        return asset('storage/shares/' . $fileName);
    }

    /**
     * Generate a secure single-use share token link for an invoice.
     */
    public function generateShareLink(Invoice $invoice)
    {
        try {
            $invoice->update(['is_shared_viewed' => false]);

            $payload = $invoice->id . '-' . time() . '-' . uniqid();
            $token = Crypt::encryptString($payload);

            return response()->json([
                'success'   => true,
                'share_url' => route('invoice.public_share', ['token' => $token])
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Token encryption error.'], 500);
        }
    }

    /**
     * Display public shared web invoice view.
     */
    public function showWebInvoice(Invoice $invoice)
    {
        $relatedAdjustments = InventoryAdjustment::where('reference_note', 'LIKE', '%' . ($invoice->invoice_no ?? $invoice->id) . '%')
            ->with('product')
            ->get();

        $calculatedSubtotal = $relatedAdjustments->sum(function($item) {
            return $item->quantity * $item->unit_cost;
        });

        return view('admin.invoices.web_share', compact('invoice', 'relatedAdjustments', 'calculatedSubtotal'));
    }

    /**
     * Display Customer Financial & Invoice Ledger.
     */
    public function customerLedger($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $customerName = $customer->name;

        $customerInvoices = Invoice::where('customer_id', $customer_id)
            ->with('items.product') 
            ->latest()
            ->get();

        $ledgerLogs = DB::table('ledger_logs')->where('customer_id', $customer_id)->get(); 

        return view('admin.sales.customer-ledger', compact('customerInvoices', 'customerName', 'customer', 'ledgerLogs'));
    }
}