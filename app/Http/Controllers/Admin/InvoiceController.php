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
use Illuminate\Support\Facades\Storage; // Ensured Storage facade is imported for your image generation
use Illuminate\Support\Facades\Log; // Added for logging errors

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
public function index()
{
    // फोन नम्बर अनुसार Grouping गरेर डेटा लिने
    $invoices = \App\Models\Invoice::with(['customer', 'items'])
        ->get()
        ->groupBy(function($invoice) {
            return $invoice->customer ? $invoice->customer->phone_number : 'Walk-in';
        });

    return view('admin.invoices.index', compact('invoices'));
}

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        // 1. Fetch Customers
        $customers = Customer::all();

        // 2. Fetch Products with stock
        $products = Product::where('initial_stock', '>', 0)->get(); // Changed from 'stock' to 'initial_stock' for consistency

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
                    'invoice_date'    => $request->invoice_date,
                    'customer_id'     => $request->customer_id,
                    'patient_name'    => $request->patient_name ?? 'Walk-in',
                    'patient_address' => $request->patient_address ?? 'N/A',
                    'patient_city'    => $request->patient_city ?? 'N/A',
                    'grand_total'     => $request->grand_total,
                ]);

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $qty = ($item['unit'] === 'g') ? ($item['qty'] / 1000) : $item['qty'];

                    if ($product->initial_stock < $qty) throw new \Exception("Insufficient stock for {$product->name}"); // Changed from 'stock' to 'initial_stock'

                    $product->decrement('initial_stock', $qty); // Changed from 'stock' to 'initial_stock'
                    $invoice->items()->create([
                        'product_id' => $item['product_id'],
                        'qty'        => $item['qty'],
                        'unit'       => $item['unit'],
                        'price'      => $product->selling_price
                    ]);
                }
                // Note: This store method is simpler than SalesController::store (POS)
                // It doesn't handle discount, vat, paid_amount, payment_method, status, remarks, nepali_date.
                // The edit/update methods below are more comprehensive, aligning with SalesController::store.
                // This might lead to inconsistencies if invoices are created via both methods.
            });
            return response()->json(['success' => true, 'redirect' => route('admin.invoices.index')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
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
            'product_id' => $item->product_id,
            'rate_per_kg' => $item->price,
            'quantity_kg' => floor($item->qty),
            'quantity_gm' => round(($item->qty - floor($item->qty)) * 1000),
            'total' => $item->total,
            '_delete' => false,
        ];
    })->values();

    $customers = Customer::orderBy('name')->get();
    $products = Product::orderBy('name')->get();

    return view('admin.invoices.edit', compact('invoice', 'itemLines', 'customers', 'products'));
}

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_date'        => 'required|date', // Add this line for AD date
            'customer_id'         => 'required|exists:customers,id',
            'payment_method'      => 'required|string|in:Cash,Online Payment,Bank Transfer,Credit Sale',
            'include_vat'         => 'required|boolean',
            'discount'            => 'required|numeric|min:0',
            'paid_amount'         => 'required|numeric|min:0',
            'remarks'             => 'nullable|string|max:1000',
            'transaction_date'    => 'nullable|string|max:20', // Assuming this is nepali_date
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|integer|exists:products,id',
            'items.*.invoice_item_id' => 'nullable|integer|exists:invoice_items,id', // Existing item ID
            'items.*.rate_per_kg' => 'required|numeric|min:0',
            'items.*.quantity_kg' => 'required|numeric|min:0',
            'items.*.quantity_gm' => 'required|numeric|min:0|max:999.99',
            'items.*._delete'     => 'nullable|boolean', // Flag to delete item
        ]);

        // Check for valid quantities among non-deleted items
        $hasValidQty = collect($validated['items'])->contains(function ($item) {
            return !isset($item['_delete']) && ((float)$item['quantity_kg'] + (float)$item['quantity_gm'] / 1000) > 0;
        });

        if (! $hasValidQty) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid quantity for at least one item.'], 422);
        }

        try {
            DB::transaction(function () use ($validated, $invoice) {
                $subtotal = 0;
                $processedItemIds = []; // To track items that are updated/created

                // Step 1: Revert stock for all existing items in the invoice
                // This ensures we start with a clean slate for stock calculation
                foreach ($invoice->items as $existingItem) {
                    $product = Product::lockForUpdate()->find($existingItem->product_id);
                    if ($product) {
                        $product->increment('initial_stock', $existingItem->qty);
                    }
                }

                // Step 2: Process new/updated/deleted items
                foreach ($validated['items'] as $itemInput) {
                    // Handle item deletion
                    if (isset($itemInput['_delete']) && $itemInput['_delete']) {
                        if (isset($itemInput['invoice_item_id'])) {
                            $invoice->items()->where('id', $itemInput['invoice_item_id'])->delete();
                        }
                        continue; // Skip to next item
                    }

                    $totalWeight = (float)$itemInput['quantity_kg'] + ((float)$itemInput['quantity_gm'] / 1000);
                    if ($totalWeight <= 0) continue; // Skip items with zero quantity

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
                        // Update existing item
                        $invoice->items()->where('id', $itemInput['invoice_item_id'])->update($itemData);
                        $processedItemIds[] = $itemInput['invoice_item_id'];
                    } else {
                        // Create new item
                        $newItem = $invoice->items()->create($itemData);
                        $processedItemIds[] = $newItem->id;
                    }

                    // Decrement stock for the new/updated quantity
                    $product->decrement('initial_stock', $totalWeight);
                }

                // Step 3: Delete any remaining old items that were not in the validated input
                $invoice->items()->whereNotIn('id', $processedItemIds)->delete();

                // Step 4: Recalculate invoice totals and update customer due
                $discount   = (float)$validated['discount'];
                $taxable    = max(0, $subtotal - $discount);
                $vat        = (bool)$validated['include_vat'] ? round($taxable * 0.13, 2) : 0.00;
                $grandTotal = round($taxable + $vat, 2);
                $paidAmount = round($validated['payment_method'] === 'Credit Sale' ? min((float)$validated['paid_amount'], $grandTotal) : $grandTotal, 2);

                // Update customer's previous_due based on old vs new grand_total and paid_amount
                $oldDue = $invoice->grand_total - $invoice->paid_amount;
                $newDue = $grandTotal - $paidAmount;

                if ($oldDue != $newDue) {
                    $customer = Customer::find($invoice->customer_id); // Use invoice's customer_id
                    if ($customer) {
                        $customer->decrement('previous_due', $oldDue); // Revert old due
                        $customer->increment('previous_due', $newDue); // Apply new due
                        $customer->save();
                    }
                }

                $invoice->update([
                    'invoice_date'    => $validated['invoice_date'], // Add this line
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
                'message'  => 'Invoice #' . $invoice->invoice_no . ' updated successfully!',
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
                // Revert stock for all items in the invoice
                foreach ($invoice->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('initial_stock', $item->qty);
                    }
                }

                // Adjust customer's previous_due if this was a credit sale
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

    public function generateShareableImage($id)
    {
        $invoice = \App\Models\Invoice::with('customer')->findOrFail($id);
        
        // 1. Render the HTML view as a string
        $html = view('admin.pdf.statement', compact('invoice'))->render();

        // 2. Define the path
        $fileName = 'Statement_' . $invoice->invoice_no . '_' . time() . '.png';
        $directory = 'public/shares';
        
        // Ensure directory exists
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        $path = storage_path('app/' . $directory . '/' . $fileName);

        // 3. Generate the PNG Image
        Browsershot::html($html) // Assuming Browsershot is configured
            ->setScreenshotType('png')
            ->windowSize(1080, 1350)
            ->deviceScaleFactor(2)
            ->save($path);

        // 4. Return the public URL for the share link
        return asset('storage/shares/' . $fileName);
    }

    /**
     * Generate a secure single-use share token link for an invoice.
     */
    public function generateShareLink(Invoice $invoice)
    {
        try {
            // Reset visibility parameter whenever admin creates a fresh communication share link
            $invoice->update(['is_shared_viewed' => false]);

            // Construct secure single-use token mapping strings
            $payload = $invoice->id . '-' . time() . '-' . uniqid();
            $token = Crypt::encryptString($payload);

            return response()->json([
                'success' => true,
                'share_url' => route('invoice.public_share', ['token' => $token])
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Token encryption error.'], 500);
        }
    }

    /**
     * Display the public shared view of the invoice using a secure token.
     */
    public function showWebInvoice(Invoice $invoice)
    {
        $relatedAdjustments = \App\Models\InventoryAdjustment::where('reference_note', 'LIKE', '%' . $invoice->invoice_no . '%')
            ->with('product')
            ->get();

        $calculatedSubtotal = $relatedAdjustments->sum(function($item) {
            return $item->quantity * $item->unit_cost;
        });

        return view('admin.invoices.web_share', compact('invoice', 'relatedAdjustments', 'calculatedSubtotal'));
    }

    /**
     * ==========================================
     * ADDED: Customer Financial & Invoice Ledger
     * ==========================================
     */
    public function customerLedger($customer_id)
    {
        // 1. Fetch target customer data details safely
        $customer = Customer::findOrFail($customer_id);
        $customerName = $customer->name;

        // 2. Fetch customer invoices and EAGER LOAD internal row line items and nested product relations
        $customerInvoices = Invoice::where('customer_id', $customer_id)
            ->with('items.product') 
            ->latest()
            ->get();

        // 3. Fallback ledger tracking queries if applicable for transaction grids
        $ledgerLogs = DB::table('ledger_logs')->where('customer_id', $customer_id)->get(); 

        return view('admin.sales.customer-ledger', compact('customerInvoices', 'customerName', 'customer', 'ledgerLogs'));
    }
    // App\Http\Controllers\Admin\InvoiceController.php
public function show($id)
{
    // Fetch the invoice
    $invoice = \App\Models\Invoice::with(['items.product'])->findOrFail($id);

    // If you are using 'invoice_items' table:
    $calculatedSubtotal = $invoice->items->sum(function($item) {
        return $item->qty * $item->price; // Corrected from 'quantity' to 'qty'
    });

    return view('admin.invoices.show', compact('invoice', 'calculatedSubtotal'));
}
}