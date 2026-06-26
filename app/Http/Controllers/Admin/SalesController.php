<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    /**
     * Show the main interactive POS Billing Terminal.
     */
    public function create()
    {
        $products  = Product::orderBy('name', 'asc')->get();
        $customers = Customer::orderBy('name', 'asc')->get();

        $fiscalYearCode      = "FY-2082/83";
        $invoiceCount        = Invoice::count() + 1;
        $next_invoice_number = "DC-" . $fiscalYearCode . "-" . str_pad($invoiceCount, 6, '0', STR_PAD_LEFT);
        $currentNepaliDate   = \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from(date('Y-m-d'))->toNepaliDate(format: 'Y-m-d');

        return view('admin.sales.pos.create', compact(
            'products', 'customers', 'currentNepaliDate', 'next_invoice_number'
        ));
    }
    

    /**
     * Dashboard with Chart Data and Customer List.
     */
    public function dashboard(Request $request)
    {
        $range = $request->get('range', '3months');

        $startDate = match ($range) {
            'today'     => now()->startOfDay(),
            'yesterday' => now()->subDay()->startOfDay(),
            '1week'     => now()->subWeek(),
            '14days'    => now()->subDays(14),
            '1month'    => now()->subMonth(),
            '3months'   => now()->subMonths(3),
            '6months'   => now()->subMonths(6),
            '12months'  => now()->subYear(),
            'ytd'       => now()->startOfYear(),
            default     => now()->subMonths(3),
        };

        $chartData = Invoice::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $totalSales     = Invoice::where('created_at', '>=', $startDate)->sum('grand_total');
        $totalInvoices  = Invoice::where('created_at', '>=', $startDate)->count();
        $averageInvoice = $totalInvoices > 0 ? ($totalSales / $totalInvoices) : 0;

        $customers = Customer::withCount('invoices')
            ->orderBy('invoices_count', 'desc')
            ->limit(5)
            ->get();

        $recentInvoices = Invoice::with('customer')->latest()->limit(5)->get();

        return view('admin.sales.dashboard', compact(
            'range', 'chartData', 'totalSales', 'totalInvoices',
            'averageInvoice', 'customers', 'recentInvoices'
        ));
    }

    /**
     * Customer Ledger Detail View.
     */
    public function customerLedger(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $query    = Invoice::where('customer_id', $id);

        if ($request->filled('range')) {
            switch ($request->range) {
                case 'today': $query->whereDate('invoice_date', date('Y-m-d')); break;
                case '3d':    $query->where('invoice_date', '>=', now()->subDays(3)); break;
                case '7d':    $query->where('invoice_date', '>=', now()->subDays(7)); break;
                case '1m':    $query->where('invoice_date', '>=', now()->subMonth()); break;
                case '3m':    $query->where('invoice_date', '>=', now()->subMonths(3)); break;
                case '6m':    $query->where('invoice_date', '>=', now()->subMonths(6)); break;
                case 'ty':    $query->whereYear('invoice_date', date('Y')); break;
            }
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('invoice_date', [$request->from_date, $request->to_date]);
        }

        $customerInvoices = $query->latest()->get();

        return view('admin.sales.customer-ledger', [
            'customerInvoices' => $customerInvoices,
            'customerName'     => $customer->name,
            'totalSales'       => $customerInvoices->sum('grand_total'),
            'totalInvoices'    => $customerInvoices->count(),
            'averageInvoice'   => $customerInvoices->avg('grand_total') ?? 0,
        ]);
    }

    /**
     * Sales List Index.
     */
    public function index(Request $request)
    {
        // Fetch products with pagination
        $products = Product::latest()->paginate(15);
        
        return view('admin.inventory.index', compact('products'));
    }

    /**
     * Atomically process checkout from POS Terminal.
     * Receives JSON payload from the frontend fetch() call.
     */
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'customer_id'         => 'required|exists:customers,id',
            'payment_method'      => 'required|string|in:Cash,Online Payment,Bank Transfer,Credit Sale',
            'include_vat'         => 'required|boolean',
            'discount'            => 'required|numeric|min:0',
            'paid_amount'         => 'required|numeric|min:0',
            'remarks'             => 'nullable|string|max:1000',
            'transaction_date'    => 'nullable|string|max:20',
            'items'               => 'required|array|min:1',
            'items.*.id'          => 'required|integer|exists:products,id',
            'items.*.rate_per_kg' => 'required|numeric|min:0',
            'items.*.quantity_kg' => 'required|numeric|min:0',
            'items.*.quantity_gm' => 'required|numeric|min:0|max:999.99',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => implode(' | ', $e->validator->errors()->all()),
        ], 422);
    }

    // Check at least one item has a real quantity
    $hasValidQty = collect($validated['items'])->contains(function ($item) {
        return ((float)$item['quantity_kg'] + (float)$item['quantity_gm'] / 1000) > 0;
    });

    if (! $hasValidQty) {
        return response()->json([
            'success' => false,
            'message' => 'Please enter a valid quantity for at least one item.',
        ], 422);
    }

    try {
        // Fetch customer records once to avoid repeated queries in transaction loop
        $customer = Customer::findOrFail($validated['customer_id']);

        $invoice = DB::transaction(function () use ($validated, $customer) {
            $subtotal  = 0;
            $itemsData = [];

            foreach ($validated['items'] as $itemInput) {
                $kg          = (float)$itemInput['quantity_kg'];
                $gm          = (float)$itemInput['quantity_gm'];
                $totalWeight = $kg + ($gm / 1000);

                if ($totalWeight <= 0) {
                    continue; // skip zero-qty rows
                }

                // Lock the row so concurrent checkouts don't oversell
                $product = Product::lockForUpdate()->findOrFail($itemInput['id']);

                if ((float)$product->initial_stock < $totalWeight) {
                    throw new \Exception(
                        "Insufficient stock for \"{$product->name}\". " .
                        "Available: {$product->initial_stock} {$product->inventory_unit}, " .
                        "Requested: {$totalWeight} {$product->inventory_unit}."
                    );
                }

                $rate         = (float)$itemInput['rate_per_kg'];
                $itemSubtotal = $rate * $totalWeight;
                $subtotal    += $itemSubtotal;

                $itemsData[] = [
                    'product'      => $product,
                    'total_weight' => $totalWeight,
                    'rate'         => $rate,
                    'total'        => $itemSubtotal,
                    'unit'         => $product->inventory_unit ?? 'KG',
                ];
            }

            if (empty($itemsData)) {
                throw new \Exception('No items with a valid quantity were found in the cart.');
            }

            // Financial calculations
            $discount   = (float)$validated['discount'];
            $taxable    = max(0, $subtotal - $discount);
            $vat        = (bool)$validated['include_vat'] ? round($taxable * 0.13, 2) : 0.00;
            $grandTotal = round($taxable + $vat, 2);

            $isCreditSale = ($validated['payment_method'] === 'Credit Sale');
            $paidAmount   = $isCreditSale
                ? min((float)$validated['paid_amount'], $grandTotal)
                : $grandTotal;
            $paidAmount   = round($paidAmount, 2);

            $status    = ($paidAmount >= $grandTotal) ? 'Paid' : 'Credit';
            $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            $now       = now();

            // Persist invoice using the explicitly matched model fields
            // NOTE: Change "Invoice::create" to "Sale::create" if your model is configured as Sale
            $invoice = Invoice::create([
                'invoice_no'     => $invoiceNo,
                'invoice_number' => $invoiceNo,
                'invoice_date'   => $now->toDateString(),
                'nepali_date'    => $validated['transaction_date'] ?? '',
                'customer_id'    => $customer->id,
                'patient_name'   => $customer->name ?? 'Walk-in Customer',
                'patient_address'=> $customer->address ?? 'N/A',
                'subtotal'       => round($subtotal, 2),
                'discount'       => $discount,
                'taxable_amount' => $taxable,
                'vat_amount'     => $vat,
                'grand_total'    => $grandTotal,
                'paid_amount'    => $paidAmount,
                'payment_method' => $validated['payment_method'],
                'status'         => $status,
                'remarks'        => $validated['remarks'] ?? null,
            ]);

            // Persist invoice items and decrement stock
            foreach ($itemsData as $entry) {
                DB::table('invoice_items')->insert([
                    'invoice_id'   => $invoice->id,
                    'product_id'   => $entry['product']->id,
                    'product_name' => $entry['product']->name,
                    'qty'          => $entry['total_weight'],
                    'unit'         => $entry['unit'],
                    'price'        => $entry['rate'],
                    'total'        => $entry['total'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                $entry['product']->decrement('initial_stock', $entry['total_weight']);
            }

            // Update customer outstanding due if credit occurs
            $due = $grandTotal - $paidAmount;
            if ($due > 0) {
                Customer::where('id', $customer->id)
                    ->increment('previous_due', $due);
            }

            return $invoice;
        });

        return response()->json([
            'success'  => true,
            'message'  => 'Invoice #' . $invoice->invoice_no . ' saved successfully!',
            'redirect' => route('admin.sales.index'),
        ]);

    } catch (\Exception $e) {
        Log::error('POS Checkout Error', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

/**
 * क्रेडिट बिलको भुक्तानी अपडेट गर्ने (Invoice-wise Payment)
 */
public function updatePayment(Request $request, $id)
{
    $request->validate([
        'received_amount' => 'required|numeric|min:0.01',
        'remarks' => 'nullable|string|max:255'
    ]);

    try {
        $invoice = Invoice::findOrFail($id);
        $receivedAmount = round((float)$request->received_amount, 2);
        
        // कति उधारो बाँकी छ हिसाब गर्ने
        $dueAmount = $invoice->grand_total - $invoice->paid_amount;

        if ($receivedAmount > $dueAmount) {
            return redirect()->back()->with('error', 'प्राप्त रकम बाँकी उधारो (Rs. ' . $dueAmount . ') भन्दा बढी हुन सक्दैन।');
        }

        DB::transaction(function () use ($invoice, $receivedAmount, $dueAmount) {
            // १. बिलको paid_amount र status अपडेट गर्ने
            $invoice->paid_amount += $receivedAmount;
            
            if ($invoice->paid_amount >= $invoice->grand_total) {
                $invoice->status = 'Paid';
            } else {
                $invoice->status = 'Credit';
            }
            $invoice->save();

            // २. ग्राहकको कुल बाँकी उधारो (previous_due) घटाउने
            if ($invoice->customer_id) {
                Customer::where('id', $invoice->customer_id)
                    ->decrement('previous_due', $receivedAmount);
            }
        });

        return redirect()->back()->with('success', 'भुक्तानी सफलतापूर्वक अपडेट गरियो।');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'त्रुटि: ' . $e->getMessage());
    }
}

public function itemAnalysis(Request $request)
{
    $customers = Customer::orderBy('name', 'asc')->get();
    
    $products = collect();
    $productHistory = collect();
    $totalQty = 0;
    $grandTotal = 0;
    $selectedProduct = null;

    // Tier 2: If customer selected, get all unique products purchased
    if ($request->filled('customer_id')) {
        $products = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.customer_id', $request->customer_id)
            ->select('product_id', 'product_name')
            ->distinct()
            ->get();
    }

    // Tier 3: If product selected, get detailed transaction history
    if ($request->filled('customer_id') && $request->filled('product_id')) {
        $selectedProduct = $request->product_name;
        $productHistory = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.customer_id', $request->customer_id)
            ->where('invoice_items.product_id', $request->product_id)
            ->select('invoices.invoice_no', 'invoices.invoice_date', 'invoice_items.qty', 'invoice_items.price', 'invoice_items.total')
            ->orderBy('invoices.invoice_date', 'desc')
            ->get();
        
        $totalQty = $productHistory->sum('qty');
        $grandTotal = $productHistory->sum('total');
    }

    return view('admin.sales.item-analysis', compact(
        'customers', 'products', 'productHistory', 'totalQty', 'grandTotal', 'selectedProduct'
    ));
}
}