<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Invoice; 
use App\Models\InventoryAdjustment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase; // <--- यो लाइन अनिवार्य थप्नुहोस्
use Illuminate\Support\Facades\Mail;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Mail\LowStockAlertMail;

class InventoryMovementController extends Controller
{
    public function salesDashboard()
    {
        $sales = InventoryAdjustment::where('type', 'sell')
            ->with(['product'])
            ->latest()
            ->paginate(15);

        return view('admin.sales.dashboard', compact('sales'));
    }

    public function salesIndex(Request $request)
    {
        if ($request->has('all') && $request->input('all') == 1) {
            $invoices = Invoice::latest()->get(); 
        } else {
            $invoices = Invoice::latest()->paginate(20);
        }

        $groupedInvoices = Invoice::latest()->get()->groupBy('customer_id');
        
        return view('admin.sales.index', compact('invoices', 'groupedInvoices'));
    }

    public function printInvoicePDF(Invoice $invoice)
    {
        if (ob_get_contents()) ob_end_clean();

        $pdf = new \FPDF('P', 'mm', 'A5'); 
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetTextColor(33, 37, 41); 
        $pdf->Cell(128, 6, 'DEURALI CHEMICAL PVT. LTD.', 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->SetTextColor(108, 117, 125); 
        $pdf->Cell(128, 4.5, 'Kuleshwor, Kathmandu Metropolitan - 14, Nepal', 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(128, 4.5, 'PAN / VAT No: 6789786567', 0, 1, 'C');
        
        $pdf->Ln(3);
        $pdf->SetDrawColor(206, 212, 218); 
        $pdf->Line(10, $pdf->GetY(), 138, $pdf->GetY());
        $pdf->Ln(4);

        $pdf->SetTextColor(33, 37, 41);
        
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(25, 5, 'Invoice No:', 0, 0);
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->Cell(45, 5, $invoice->invoice_no, 0, 0);
        
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(15, 5, 'Date:', 0, 0);
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->Cell(43, 5, $invoice->invoice_date, 0, 1);

        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(25, 5, 'Customer:', 0, 0);
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->Cell(45, 5, $invoice->patient_name ?? 'Walk-in Customer', 0, 0);

        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(15, 5, 'Address:', 0, 0);
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->Cell(43, 5, $invoice->patient_address ?? 'Kathmandu, Nepal', 0, 1);

        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->SetFillColor(248, 249, 250); 
        $pdf->SetDrawColor(180, 185, 190);
        
        $pdf->Cell(10, 7, 'S.N.', 1, 0, 'C', true);
        $pdf->Cell(65, 7, ' Particulars', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Unit Cost ', 1, 0, 'R', true);
        $pdf->Cell(28, 7, 'Total Sum ', 1, 1, 'R', true);

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

            $pdf->Cell(10, 6.5, $sn++, 1, 0, 'C');
            $pdf->Cell(65, 6.5, ' ' . $nameString . ' (x' . floatval($item->quantity) . ')', 1, 0, 'L');
            $pdf->Cell(25, 6.5, 'Rs ' . number_format($item->unit_cost, 2) . ' ', 1, 0, 'R');
            $pdf->Cell(28, 6.5, 'Rs ' . number_format($totalRowCost, 2) . ' ', 1, 1, 'R');
        }

        $pdf->Ln(3);
        
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(100, 5.5, 'Sub Total:', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->Cell(28, 5.5, 'Rs ' . number_format($calculatedSubtotal, 2) . ' ', 0, 1, 'R');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(100, 6.5, 'Grand Total:', 0, 0, 'R');
        $pdf->SetFillColor(233, 236, 239);
        $pdf->Cell(28, 6.5, 'Rs ' . number_format($invoice->grand_total, 2) . ' ', 1, 1, 'R', true);

        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'I', 7.5);
        $pdf->SetTextColor(108, 117, 125);
        $pdf->Cell(128, 4, 'Thank you for your business! Goods sold are subject to company terms & conditions.', 0, 1, 'C');
        $pdf->Cell(128, 4, 'System Generated Document - No Signature Required.', 0, 1, 'C');

        return response($pdf->Output('I', 'Invoice-' . $invoice->invoice_no . '.pdf'), 200)
            ->header('Content-Type', 'application/pdf');
    }

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

        $currentNepaliDate = LaravelNepaliDate::from(date('Y-m-d'))->toNepaliDate();

        return view('admin.sales.sell', compact('product', 'products', 'customers', 'paddedInvoiceNumber', 'currentNepaliDate'));
    }

    public function storeSale(Request $request)
    {
        Log::info("👉 POS Request initiated!");
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items'       => 'required|array|min:1',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $invoice = Invoice::create([
                    'invoice_no' => 'INV-' . strtoupper(uniqid()),
                    'invoice_date' => date('Y-m-d'),
                    'patient_name' => Customer::find($validated['customer_id'])->name ?? 'Walk-in',
                    'grand_total' => 0, 
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['id']);
                    if ($product->initial_stock < $item['qty']) {
                        throw new \Exception("Insufficient stock: {$product->name}");
                    }

                    $product->decrement('initial_stock', $item['qty']);
                    $product->refresh();
                    
                    $this->checkAndSendLowStockAlert($product);

                    InventoryAdjustment::create([
                        'product_id' => $product->id,
                        'quantity'   => $item['qty'],
                        'type'       => 'sell',
                        'unit_cost'  => $product->selling_price,
                        'reference_note' => 'POS Invoice: #' . $invoice->invoice_no,
                    ]);
                }
            });
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function storeAddStock(Request $request)
{
    $request->validate([
        'product_id'     => 'required|exists:products,id',
        'quantity'       => 'required|numeric|min:0.01',
        'purchase_cost'  => 'required|numeric|min:0',
        'reference_note' => 'nullable|string'
    ]);

    DB::transaction(function () use ($request) {
        $product = \App\Models\Product::findOrFail($request->product_id);

        // १. स्टक बढाउने
        $product->increment('initial_stock', $request->quantity);

        // २. Purchase टेबलमा रेकर्ड राख्ने
        \App\Models\Purchase::create([
            'item_name'      => $product->name,
            'quantity'       => $request->quantity,
            'price_per_unit' => $request->purchase_cost,
            'total_amount'   => ($request->purchase_cost * $request->quantity),
            'supplier_name'  => 'Internal Stock Update',
            'purchase_date'  => now(),
            'notes'          => $request->reference_note ?? 'Manual Add Stock',
            'unit'           => $product->unit ?? 'pcs', 
            'created_at'     => now(),
        ]);

        // ३. InventoryAdjustment मा लग राख्ने
        // 'type' मा 'add_stock' को सट्टा डेटाबेसले स्विकार्ने कुनै भ्यालु (जस्तै: 'purchase') राख्नुहोस्।
        // यदि डेटाबेस ENUM हो भने, त्यहाँ भएको विकल्प मात्र प्रयोग गर्नुहोस्।
        \App\Models\InventoryAdjustment::create([
            'product_id'     => $product->id,
            'quantity'       => $request->quantity,
            'type'           => 'purchase', // <--- यहाँ 'add_stock' को सट्टा 'purchase' प्रयास गर्नुहोस्
            'reference_note' => $request->reference_note ?? 'Manual Stock Entry',
            'user_id'        => auth()->id()
        ]);
    });

    return redirect()->route('admin.purchases.dashboard')->with('success', 'Stock added successfully!');
}

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'type'           => 'required|string',
            'quantity'       => 'required|numeric|min:0.01',
            'reference_note' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $product) {
            // Logic: 'returned_defective' थप्ने, अरु घटाउने
            $addTypes = ['returned_defective'];
            
            if (in_array($request->type, $addTypes)) {
                $product->increment('initial_stock', $request->quantity);
            } else {
                $product->decrement('initial_stock', $request->quantity);
            }

            InventoryAdjustment::create([
                'product_id'     => $product->id,
                'quantity'       => $request->quantity,
                'type'           => $request->type,
                'reference_note' => $request->reference_note,
                'user_id'        => auth()->id()
            ]);
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Adjustment recorded!');
    }

    /**
     * Real-time Single Product Low Stock Monitor via Laravel Mail Facade
     */
    private function checkAndSendLowStockAlert($product)
    {
        Log::info("DEBUG 1: Checking stock -> Product: " . $product->name . " | Current Stock: " . $product->initial_stock . " | Alert Level: " . $product->alert_stock_level);

        if ($product->initial_stock > $product->alert_stock_level) {
            Log::info("DEBUG 2: Stock is safe. Skipping email.");
            return;
        }

        Log::info("DEBUG 3: Condition matched! Preparing to send email via Laravel Mail...");

        try {
            Mail::to('ciphernirmal@gmail.com')->send(new LowStockAlertMail($product));
            
            Log::info("DEBUG 4: SUCCESS! Email sent successfully.");
        } catch (\Exception $e) {
            Log::error("DEBUG 5: Mail ERROR -> Exception: " . $e->getMessage());
        }
    }

    public function showCustomerLedger(Request $request, $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $customerName = $customer->name;
        $query = \App\Models\Invoice::where('customer_id', $id);

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
        $totalSales = $customerInvoices->sum('grand_total');
        $totalInvoices = $customerInvoices->count();
        $averageInvoice = $totalInvoices > 0 ? ($totalSales / $totalInvoices) : 0;

        return view('admin.sales.customer-ledger', compact('customerInvoices', 'customerName', 'totalSales', 'totalInvoices', 'averageInvoice'));
    }

    // In App\Http\Controllers\Admin\InventoryMovementController.php

public function showWebInvoice($id)
{
    // Fetch the invoice with its customer and items
    // Ensure your Invoice model has an 'items' relationship defined
    $invoice = \App\Models\Invoice::with(['customer', 'items.product'])->findOrFail($id);

    // Calculate subtotal from the items
    $calculatedSubtotal = $invoice->items->sum(function($item) {
        return $item->quantity * $item->price;
    });

    // Return the view instead of a PDF
    return view('admin.invoices.show', compact('invoice', 'calculatedSubtotal'));
}

    public function create(Product $product)
    {
        return view('admin.inventory.adjust', compact('product'));
    }

    public function manageLowStock()
    {
        $lowStockProducts = Product::whereColumn('initial_stock', '<=', 'alert_stock_level')
            ->get()
            ->map(function ($product) {
                $product->email_sent = Cache::has('low_stock_alert_' . $product->id);
                return $product;
            });

        return view('admin.inventory.low_stock_manager', compact('lowStockProducts'));
    }

    public function createAddStock()
{
    // Fetch products to choose from
    $products = \App\Models\Product::orderBy('name', 'asc')->get();
    return view('admin.inventory.add_stock_select', compact('products'));
}
public function stockPosition(Request $request)
{
    $fromDate = $request->input('from_date', '2025-07-17');
    $toDate = $request->input('to_date', date('Y-m-d'));

    $stockReport = \App\Models\Product::query()
        ->leftJoin('stock_transactions as st', function($join) use ($fromDate, $toDate) {
            $join->on('products.id', '=', 'st.product_id')
                 ->whereBetween('st.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        })
        ->leftJoin('inventory_adjustments as ia', function($join) use ($fromDate, $toDate) {
            $join->on('products.id', '=', 'ia.product_id')
                 ->whereBetween('ia.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        })
        ->select(
            'products.id', 'products.name', 'products.item_code', 'products.color', 
            'products.size', 'products.inventory_unit', 'products.initial_stock', 'products.purchase_cost',
            DB::raw("COALESCE(SUM(CASE WHEN st.type = 'purchase' THEN st.quantity ELSE 0 END), 0) as total_purchase"),
            DB::raw("COALESCE(SUM(CASE WHEN st.type = 'sale' THEN st.quantity ELSE 0 END), 0) as total_sale"),
            DB::raw("COALESCE(SUM(ia.quantity), 0) as total_adjustment")
        )
        ->groupBy('products.id', 'products.name', 'products.item_code', 'products.color', 'products.size', 'products.inventory_unit', 'products.initial_stock', 'products.purchase_cost')
        ->get();

    return view('admin.inventory.position', compact('stockReport', 'fromDate', 'toDate'));
}
}