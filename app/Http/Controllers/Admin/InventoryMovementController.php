<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Invoice; 
use App\Models\InventoryAdjustment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class InventoryMovementController extends Controller
{
    /**
     * Display the Dedicated Sales Log Dashboard
     */
    public function salesDashboard()
    {
        $sales = InventoryAdjustment::where('type', 'sell')
            ->with(['product'])
            ->latest()
            ->paginate(15);

        return view('admin.sales.dashboard', compact('sales'));
    }

    /**
     * Display the Invoices list index table
     */
    public function salesIndex(Request $request)
    {
        // 1. Keep your logic for pagination/fetching
        if ($request->has('all') && $request->input('all') == 1) {
            $invoices = Invoice::latest()->get(); 
        } else {
            $invoices = Invoice::latest()->paginate(20);
        }

        // 2. Create the grouped collection for the view to use
        // We use the full collection to ensure the ledger displays all data
        $groupedInvoices = Invoice::latest()->get()->groupBy('customer_id');
        
        // 3. Pass BOTH variables if you need pagination for some parts 
        // and grouping for others, or just $groupedInvoices if that is your primary view
        return view('admin.sales.index', compact('invoices', 'groupedInvoices'));
    }

    /**
     * Generate dynamic, highly responsive print documents using standard FPDF structures
     */
    public function printInvoicePDF(Invoice $invoice)
{
    // Suppress buffer output errors before compiling canvas sizing layout
    if (ob_get_contents()) ob_end_clean();

    // Instantiate standard FPDF class definition (A5 Portrait: 148mm x 210mm)
    // Printable width = 148mm - 20mm (margins) = 128mm
    $pdf = new \FPDF('P', 'mm', 'A5'); 
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    
    // --- 1. HEADER SEGMENT (Corporate Identity) ---
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->SetTextColor(33, 37, 41); // Dark Charcoal
    $pdf->Cell(128, 6, 'DEURALI CHEMICAL PVT. LTD.', 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 8.5);
    $pdf->SetTextColor(108, 117, 125); // Slate Gray
    $pdf->Cell(128, 4.5, 'Kuleshwor, Kathmandu Metropolitan - 14, Nepal', 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(128, 4.5, 'PAN / VAT No: 6789786567', 0, 1, 'C');
    
    // Sleek Divider Line
    $pdf->Ln(3);
    $pdf->SetDrawColor(206, 212, 218); // Light gray border
    $pdf->Line(10, $pdf->GetY(), 138, $pdf->GetY());
    $pdf->Ln(4);

    // --- 2. METADATA BLOCK (Two-Column Grid) ---
    $pdf->SetTextColor(33, 37, 41);
    
    // Row 1
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(25, 5, 'Invoice No:', 0, 0);
    $pdf->SetFont('Arial', '', 8.5);
    $pdf->Cell(45, 5, $invoice->invoice_no, 0, 0);
    
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(15, 5, 'Date:', 0, 0);
    $pdf->SetFont('Arial', '', 8.5);
    $pdf->Cell(43, 5, $invoice->invoice_date, 0, 1);

    // Row 2
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(25, 5, 'Customer:', 0, 0);
    $pdf->SetFont('Arial', '', 8.5);
    $pdf->Cell(45, 5, $invoice->patient_name ?? 'Walk-in Customer', 0, 0);

    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(15, 5, 'Address:', 0, 0);
    $pdf->SetFont('Arial', '', 8.5);
    $pdf->Cell(43, 5, $invoice->patient_address ?? 'Kathmandu, Nepal', 0, 1);

    $pdf->Ln(5);
    
    // --- 3. ITEMIZATION TABLE (No Padding Rows) ---
    // Table Headers
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->SetFillColor(248, 249, 250); // Soft Light Background
    $pdf->SetDrawColor(180, 185, 190);
    
    $pdf->Cell(10, 7, 'S.N.', 1, 0, 'C', true);
    $pdf->Cell(65, 7, ' Particulars', 1, 0, 'L', true);
    $pdf->Cell(25, 7, 'Unit Cost ', 1, 0, 'R', true);
    $pdf->Cell(28, 7, 'Total Sum ', 1, 1, 'R', true);

    // Data Rows
    $pdf->SetFont('Arial', '', 8.5);
    $relatedAdjustments = InventoryAdjustment::where('reference_note', 'LIKE', '%' . $invoice->invoice_no . '%')
        ->with('product')
        ->get();

    $sn = 1;
    $calculatedSubtotal = 0;

    foreach ($relatedAdjustments as $item) {
        $nameString = $item->product ? $item->product->name : 'Chemical Commodity Item';
        $totalRowCost = $item->quantity * $item->unit_cost;
        $calculatedSubtotal += $totalRowCost;

        // Clean padding format inside cells
        $pdf->Cell(10, 6.5, $sn++, 1, 0, 'C');
        $pdf->Cell(65, 6.5, ' ' . $nameString . ' (x' . floatval($item->quantity) . ')', 1, 0, 'L');
        $pdf->Cell(25, 6.5, 'Rs ' . number_format($item->unit_cost, 2) . ' ', 1, 0, 'R');
        $pdf->Cell(28, 6.5, 'Rs ' . number_format($totalRowCost, 2) . ' ', 1, 1, 'R');
    }

    // --- 4. FINANCIAL SUMMARY ---
    $pdf->Ln(3);
    
    // Subtotal Row
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(100, 5.5, 'Sub Total:', 0, 0, 'R');
    $pdf->SetFont('Arial', '', 8.5);
    $pdf->Cell(28, 5.5, 'Rs ' . number_format($calculatedSubtotal, 2) . ' ', 0, 1, 'R');

    // Grand Total Row (Highlighted with light grey background instead of double lines)
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(100, 6.5, 'Grand Total:', 0, 0, 'R');
    $pdf->SetFillColor(233, 236, 239);
    $pdf->Cell(28, 6.5, 'Rs ' . number_format($invoice->grand_total, 2) . ' ', 1, 1, 'R', true);

    // --- 5. FOOTER DISCLAIMER ---
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'I', 7.5);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(128, 4, 'Thank you for your business! Goods sold are subject to company terms & conditions.', 0, 1, 'C');
    $pdf->Cell(128, 4, 'System Generated Document - No Signature Required.', 0, 1, 'C');

    // Stream PDF directly to view
    return response($pdf->Output('I', 'Invoice-' . $invoice->invoice_no . '.pdf'), 200)
        ->header('Content-Type', 'application/pdf');
}

    /**
     * Show the dedicated Sales Processing form (POS).
     */
    public function createSale(Product $product = null)
    {
        $customers = Customer::orderBy('name', 'asc')->get();

        $products = null;
        if (!$product || !$product->exists) {
            $products = Product::where('initial_stock', '>', 0)->orderBy('name', 'asc')->get();
            $product = null;
        }

        $lastSale = InventoryAdjustment::where('type', 'sell')->latest('id')->first();
        $nextId = $lastSale ? ($lastSale->id + 1) : 1;
        $paddedInvoiceNumber = str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $currentEngDate = date('Y-m-d');
        $currentNepaliDate = LaravelNepaliDate::from($currentEngDate)->toNepaliDate();

        return view('admin.sales.sell', compact(
            'product', 
            'products', 
            'customers', 
            'paddedInvoiceNumber', 
            'currentNepaliDate'
        ));
    }

    /**
     * Show general operational adjustments form (Wastage, Damage, etc.)
     */
    public function create(Product $product)
    {
        return view('admin.inventory.adjust', compact('product'));
    }

    /**
 * Customer Specific Ledger Page
 */
public function showCustomerLedger(Request $request, $id)
{
    // 1. Fetch the Customer
    $customer = \App\Models\Customer::findOrFail($id);
    $customerName = $customer->name;

    // 2. Base Query
    $query = \App\Models\Invoice::where('customer_id', $id);

    // 3. Apply Quick Range Filters
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

    // 4. Apply Custom Date Range Filter
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('invoice_date', [$request->from_date, $request->to_date]);
    }

    // 5. Get Invoices
    $customerInvoices = $query->latest()->get();

    // 6. Calculate Stats
    $totalSales = $customerInvoices->sum('grand_total');
    $totalInvoices = $customerInvoices->count();
    $averageInvoice = $totalInvoices > 0 ? ($totalSales / $totalInvoices) : 0;

    // 7. Return View
    return view('admin.sales.customer-ledger', compact(
        'customerInvoices', 
        'customerName', 
        'totalSales', 
        'totalInvoices', 
        'averageInvoice'
    ));
}

    /**
     * Route gateway pointing explicitly to the multi-row checkout processor
     */
    public function storeSale(Request $request)
    {
        if (!$request->expectsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        $paymentInput = strtolower($request->input('payment_method', 'cash'));
        if (str_contains($paymentInput, 'cash')) {
            $paymentMethod = 'cash';
        } elseif (str_contains($paymentInput, 'credit')) {
            $paymentMethod = 'credit';
        } else {
            $paymentMethod = 'online'; 
        }

        $request->merge([
            'payment_method' => $paymentMethod,
            'customer_id'    => $request->filled('customer_id') ? (int) $request->input('customer_id') : null,
        ]);

        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'payment_method' => 'required|in:cash,online,credit',
            'discount'       => 'nullable|numeric|min:0',
            'paid_amount'    => 'nullable|numeric|min:0',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|numeric|min:0.001',
        ]);

        try {
            $redirectUrl = DB::transaction(function () use ($validated, $request) {
                $subtotal = 0;
                $processedItems = [];

                foreach ($validated['items'] as $cartItem) {
                    $product = Product::findOrFail($cartItem['id']);
                    
                    if ($product->initial_stock < $cartItem['qty']) {
                        throw new \Exception("Insufficient stock level for '{$product->name}'! Remaining: {$product->initial_stock}");
                    }

                    $itemTotal = $product->selling_price * $cartItem['qty'];
                    $subtotal += $itemTotal;

                    $processedItems[] = [
                        'product'    => $product,
                        'qty'        => $cartItem['qty'],
                        'unit_price' => $product->selling_price,
                        'total'      => $itemTotal
                    ];
                }

                $discount = floatval($validated['discount'] ?? 0);
                $taxableAmount = max(0, $subtotal - $discount);
                
                $includeVat = $request->input('include_vat') == 1 || $request->input('include_vat') === true;
                $taxAmount = $includeVat ? ($taxableAmount * 0.13) : 0;
                $grandTotal = $taxableAmount + $taxAmount;

                $customer = Customer::find($validated['customer_id']);
                $customerIdentifier = $customer ? $customer->name : 'Walk-in Customer';

                $invoice = Invoice::create([
                    'invoice_no'      => 'INV-' . strtoupper(uniqid()),
                    'invoice_date'    => date('Y-m-d'),
                    'patient_name'    => $customerIdentifier,
                    'patient_address' => $customer ? $customer->address : 'N/A',
                    'patient_city'    => 'Kathmandu', 
                    'grand_total'     => $grandTotal,
                ]);

                foreach ($processedItems as $item) {
                    $product = $item['product'];

                    $product->decrement('initial_stock', $item['qty']);

                    InventoryAdjustment::create([
                        'product_id'     => $product->id,
                        'quantity'       => $item['qty'],
                        'type'           => 'sell',
                        'unit_cost'      => $item['unit_price'],
                        'reference_note' => 'POS Session Invoice: #' . $invoice->invoice_no,
                    ]);
                }

                return route('admin.sales.index');
            });

            return response()->json([
                'success'  => true,
                'message'  => 'POS Secure checkout transaction finalized successfully!',
                'redirect' => $redirectUrl
            ], 200);

        } catch (\Exception $e) {
            Log::error('POS Processing Execution Fault trace: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store adjustments from backend structural forms
     */
    public function store(Request $request, Product $product = null)
    {
        if ($request->has('items')) {
            return $this->processMultiRowInvoice($request);
        }

        $validated = $request->validate([
            'quantity'       => 'required|numeric|min:0.001',
            'type'           => 'required|string|in:sell,expired,damaged,returned_defective,internal_use,wastage',
            'reference_note' => 'nullable|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($product, $validated) {
                $quantity = $validated['quantity'];
                $type = $validated['type'];

                if (in_array($type, ['sell', 'expired', 'damaged', 'internal_use', 'wastage'])) {
                    if ($product->initial_stock < $quantity) {
                        throw new \Exception("Insufficient stock balances! Available: {$product->initial_stock}");
                    }
                    $product->initial_stock -= $quantity;
                    $costTrack = ($type === 'sell') ? $product->selling_price : $product->purchase_cost;
                } else {
                    $product->initial_stock += $quantity;
                    $costTrack = $product->selling_price;
                }

                InventoryAdjustment::create([
                    'product_id'     => $product->id,
                    'quantity'       => $quantity,
                    'type'           => $type,
                    'unit_cost'      => $costTrack,
                    'reference_note' => $validated['reference_note'],
                ]);

                $product->save();
            });

            if ($validated['type'] === 'sell') {
                return redirect()->route('admin.sales.dashboard')->with('success', 'Retail point-of-sale transaction recorded successfully!');
            }
            return redirect()->route('admin.products.index')->with('success', 'Inventory adjustment finalized!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showWebInvoice(Invoice $invoice)
{
    // Fetch the related adjustments exactly like before
    $relatedAdjustments = InventoryAdjustment::where('reference_note', 'LIKE', '%' . $invoice->invoice_no . '%')
        ->with('product')
        ->get();

    $calculatedSubtotal = 0;
    foreach ($relatedAdjustments as $item) {
        $calculatedSubtotal += ($item->quantity * $item->unit_cost);
    }

    // Pass everything to a clean Blade view
    return view('invoices.web_share', compact('invoice', 'relatedAdjustments', 'calculatedSubtotal'));
}

    /**
     * Internal Helper method for fallback forms
     */
    protected function processMultiRowInvoice(Request $request)
    {
        $request->validate([
            'customer_id'            => 'required|exists:customers,id',
            'invoice_number'         => 'required|string',
            'invoice_date'           => 'required|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.price'          => 'required|numeric|min:0',
            'items.*.weight_kg'      => 'required|numeric|min:0',
            'items.*.weight_gram'    => 'nullable|numeric|min:0|max:999',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->input('items') as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    
                    $kgPart = floatval($item['weight_kg']);
                    $gramPart = isset($item['weight_gram']) ? (floatval($item['weight_gram']) / 1000) : 0;
                    
                    $totalCalculatedQuantity = $kgPart + $gramPart;

                    if ($totalCalculatedQuantity <= 0) {
                        throw new \Exception("The total quantity for item '{$product->name}' must be greater than zero.");
                    }

                    if ($product->initial_stock < $totalCalculatedQuantity) {
                        throw new \Exception("Insufficient stock for product: {$product->name}! Remaining: {$product->initial_stock} KG");
                    }

                    $product->initial_stock -= $totalCalculatedQuantity;
                    $product->save();

                    InventoryAdjustment::create([
                        'product_id'     => $product->id,
                        'quantity'       => $totalCalculatedQuantity, 
                        'type'           => 'sell',
                        'unit_cost'      => floatval($item['price']),
                        'reference_note' => 'Invoice Ref: #' . $request->input('invoice_number') . ' | Dated B.S: ' . $request->input('invoice_date'),
                    ]);
                }
            });

            return redirect()->route('admin.sales.dashboard')->with('success', 'POS Invoiced ledger transaction processed and committed successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}