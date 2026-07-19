<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Invoice; 
use App\Models\InventoryAdjustment;
use App\Models\Customer;
use App\Models\Supplier; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase; 
use Illuminate\Support\Facades\Mail;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Mail\LowStockAlertMail;
use App\Helpers\FiscalYearHelper;
use App\Models\InvoiceItem;

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
            $invoices = Invoice::with('supplier')->latest()->get(); 
        } else {
            $invoices = Invoice::with('supplier')->latest()->paginate(20);
        }

        $groupedInvoices = Invoice::with('supplier')->latest()->get()->groupBy('customer_id');
        
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
            'supplier_id' => 'nullable|exists:suppliers,id',
            'items'       => 'required|array|min:1',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $invoice = Invoice::create([
                    'invoice_no' => 'INV-' . strtoupper(uniqid()),
                    'invoice_date' => date('Y-m-d'),
                    'customer_id' => $validated['customer_id'],
                    'supplier_id' => $validated['supplier_id'] ?? null,
                    'patient_name' => Customer::find($validated['customer_id'])->name ?? 'Walk-in',
                    'grand_total' => 0, 
                ]);

                $grandTotal = 0;

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['id']);
                    if ($product->initial_stock < $item['qty']) {
                        throw new \Exception("Insufficient stock: {$product->name}");
                    }

                    $product->decrement('initial_stock', $item['qty']);
                    $product->refresh();
                    
                    $this->checkAndSendLowStockAlert($product);

                    $itemTotal = $item['qty'] * $product->selling_price;
                    $grandTotal += $itemTotal;

                    InvoiceItem::create([
                        'invoice_id'   => $invoice->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'qty'          => $item['qty'],
                        'unit'         => $product->inventory_unit ?? 'pcs',
                        'price'        => $product->selling_price,
                        'total'        => $itemTotal,
                    ]);

                    InventoryAdjustment::create([
                        'product_id' => $product->id,
                        'quantity'   => $item['qty'],
                        'type'       => 'sell',
                        'unit_cost'  => $product->selling_price,
                        'reference_note' => 'POS Invoice: #' . $invoice->invoice_no,
                    ]);
                }

                $invoice->update(['grand_total' => $grandTotal]);
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
            'supplier_id'    => 'required|exists:suppliers,id',
            'quantity'       => 'required|numeric|min:0.01',
            'purchase_cost'  => 'required|numeric|min:0',
            'reference_note' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $product = \App\Models\Product::findOrFail($request->product_id);

            $product->increment('initial_stock', $request->quantity);

            \App\Models\Purchase::create([
                'item_name'      => $product->name,
                'quantity'       => $request->quantity,
                'price_per_unit' => $request->purchase_cost,
                'total_amount'   => ($request->purchase_cost * $request->quantity),
                'supplier_id'    => $request->supplier_id,
                'supplier_name'  => Supplier::find($request->supplier_id)->name ?? 'Unknown',
                'purchase_date'  => now(),
                'notes'          => $request->reference_note ?? 'Manual Add Stock',
                'unit'           => $product->unit ?? 'pcs', 
                'created_at'     => now(),
            ]);

            \App\Models\InventoryAdjustment::create([
                'product_id'     => $product->id,
                'quantity'       => $request->quantity,
                'type'           => 'purchase', 
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

    public function showWebInvoice($id)
    {
        $invoice = \App\Models\Invoice::with(['customer', 'items.product'])->findOrFail($id);

        $calculatedSubtotal = $invoice->items->sum(function($item) {
            return $item->quantity * $item->price;
        });

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
        $products = \App\Models\Product::orderBy('name', 'asc')->paginate(10);
        return view('admin.inventory.add_stock_select', compact('products'));
    }

    public function stockPosition(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', FiscalYearHelper::getCurrentFiscalYear());
        $range = FiscalYearHelper::getFiscalYearDateRange($fiscalYear);
        $fromDate = $range['ad_start'];
        $toDate = $range['ad_end'];

        $stockReport = \App\Models\Product::query()
            ->select('id', 'name', 'item_code', 'purchase_cost', 'initial_stock', 'inventory_unit')
            ->withSum(['purchases as total_purchase' => function($q) use ($fromDate, $toDate) {
                $q->whereBetween('purchase_date', [$fromDate, $toDate]);
            }], 'quantity') 
            ->withSum(['purchases as total_purchase_value' => function($q) use ($fromDate, $toDate) {
                $q->whereBetween('purchase_date', [$fromDate, $toDate]);
            }], 'total_amount')
            ->withSum(['invoiceItems as total_sale' => function($q) use ($fromDate, $toDate) {
                $q->whereHas('invoice', function($inv) use ($fromDate, $toDate) {
                    $inv->whereBetween('invoice_date', [$fromDate, $toDate]);
                });
            }], 'qty') 
            ->withSum(['inventoryAdjustments as total_adjustment' => function($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            }], 'quantity')
            ->get();

        $totals = [
            'opening' => $stockReport->sum('initial_stock'),
            'purchase_qty' => $stockReport->sum('total_purchase'),
            'purchase_val' => $stockReport->sum('total_purchase_value'),
            'sale' => $stockReport->sum('total_sale'),
            'adjustment' => $stockReport->sum('total_adjustment'),
            'balance_qty' => $stockReport->sum(function($i) {
                return $i->initial_stock + ($i->total_purchase ?? 0) - ($i->total_sale ?? 0) + ($i->total_adjustment ?? 0);
            }),
            'balance_value' => $stockReport->sum(function($i) {
                $qty = $i->initial_stock + ($i->total_purchase ?? 0) - ($i->total_sale ?? 0) + ($i->total_adjustment ?? 0);
                return $qty * ($i->purchase_cost ?? 0);
            }),
        ];

        return view('admin.inventory.position', [
            'stockReport'        => $stockReport,
            'selectedFiscalYear' => $fiscalYear,
            'fromDate'           => $range['bs_start'], 
            'toDate'             => $range['bs_end'],   
            'totals'             => $totals
        ]);
    }

    public function stockAgeing(Request $request)
    {
        $s1 = (int) $request->input('s1', 30);
        $s2 = (int) $request->input('s2', 60);
        $s3 = (int) $request->input('s3', 90);

        $products = \App\Models\Product::with('purchases')->get();

        $reportData = $products->map(function ($product) use ($s1, $s2, $s3) {
            $slabs = [
                's1' => ['q' => 0, 'v' => 0], 's2' => ['q' => 0, 'v' => 0], 
                's3' => ['q' => 0, 'v' => 0], 's4' => ['q' => 0, 'v' => 0]
            ];

            foreach ($product->purchases as $p) {
                $daysOld = now()->diffInDays($p->purchase_date);
                $qty = $p->quantity;
                $val = $qty * $p->price_per_unit;

                if ($daysOld < $s1) { $slabs['s1']['q'] += $qty; $slabs['s1']['v'] += $val; }
                elseif ($daysOld < $s2) { $slabs['s2']['q'] += $qty; $slabs['s2']['v'] += $val; }
                elseif ($daysOld < $s3) { $slabs['s3']['q'] += $qty; $slabs['s3']['v'] += $val; }
                else { $slabs['s4']['q'] += $qty; $slabs['s4']['v'] += $val; }
            }

            return [
                'name' => $product->name,
                'total_qty' => $product->purchases->sum('quantity'),
                'slabs' => $slabs
            ];
        });

        return view('admin.products.stock_ageing', compact('reportData', 's1', 's2', 's3'));
    }

    public function monthlyMovementReport(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $initialStocks = Product::pluck('initial_stock', 'name');

        $inward = DB::table('purchases')
            ->select(DB::raw('MONTH(purchase_date) as month'), 'item_name as product', 
                     DB::raw('SUM(quantity) as qty'), DB::raw('SUM(total_amount) as total_val'), DB::raw("'Inward' as type"))
            ->whereYear('purchase_date', $year)->groupBy('month', 'item_name');

        $outward = DB::table('invoice_items')
            ->select(DB::raw('MONTH(created_at) as month'), 'product_name as product', 
                     DB::raw('SUM(qty) as qty'), DB::raw('SUM(total) as total_val'), DB::raw("'Outward' as type"))
            ->whereYear('created_at', $year)->groupBy('month', 'product_name');

        $movements = $inward->unionAll($outward)->get()->groupBy('month');

        return view('admin.products.monthly_movement', compact('movements', 'year', 'initialStocks'));
    }

    public function stockMovement(Request $request)
    {
        $allSuppliers = Supplier::orderBy('name')->get();
        $supplierId = $request->input('supplier_id');

        $inwards = collect();
        $outwards = collect();

        if ($supplierId) {
            // 1. INWARD: Products bought from this supplier (taken directly from products table)
            $inwards = Product::where('supplier_id', $supplierId)->get();
            
            // 2. OUTWARD: Sales transactions of this supplier's products to buyers
            // Links invoice_items to invoices to fetch customer details and grand totals
            $outwards = InvoiceItem::whereIn('product_id', function($query) use ($supplierId) {
                    $query->select('id')->from('products')->where('supplier_id', $supplierId);
                })
                ->with(['invoice.customer']) // Eager load the invoice and customer info
                ->get();
        }

        return view('admin.reports.stock-movement', compact('allSuppliers', 'inwards', 'outwards'));
    }

    public function stockMovementReport(Request $request)
    {
        $query = InventoryAdjustment::with('product');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $movements = $query->latest()->paginate(20);
        $year = null; 

        return view('admin.products.monthly_movement', compact('movements', 'year'));
    }

public function productTraceability(Request $request)
{
    $allSuppliers = Supplier::orderBy('name', 'asc')->get();
    $activeSupplierId = $request->input('supplier_id');
    
    $inwards = collect();
    $outwards = collect();
    $activeSupplier = null;
    
    if ($activeSupplierId) {
        $activeSupplier = Supplier::find($activeSupplierId);
        
        if ($activeSupplier) {
            // 1. INWARD: Products from the 'products' table belonging to this supplier
            $inwards = Product::where('supplier_id', $activeSupplierId)->get();

            // 2. OUTWARD: Sales items belonging to this supplier's products
            $outwards = InvoiceItem::whereIn('product_id', function($query) use ($activeSupplierId) {
                    $query->select('id')->from('products')->where('supplier_id', $activeSupplierId);
                })
                ->with(['invoice']) // Eager loads invoice to get patient_name, patient_city, etc.
                ->get();
        }
    }

    return view('admin.products.product_traceability', compact(
        'allSuppliers', 
        'activeSupplier', 
        'inwards', 
        'outwards'
    ));
}

    public function getProductTraceability($productId)
    {
        $product = \App\Models\Product::findOrFail($productId);

        $inwards = \App\Models\Purchase::where('item_name', $product->name)
            ->orderBy('purchase_date', 'desc')->get();

        $outwards = \App\Models\InvoiceItem::where('product_name', $product->name)
            ->with('invoice.customer')->orderBy('created_at', 'desc')->get();

        return view('admin.products.product_traceability', compact('product', 'inwards', 'outwards'));
    }
}