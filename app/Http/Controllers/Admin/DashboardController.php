<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\FiscalYearHelper;
use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InventoryAdjustment;
use App\Models\InvoiceItem;
use App\Models\Purchase;
use App\Models\Product;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    // 1. Get selected Year/Month or default to current date
    $year = $request->input('year', date('Y'));
    $month = $request->input('month');

    // 2. Define Date Range using Carbon
    $queryStart = Carbon::create($year, $month ?? 1, 1)->startOfDay();
    $queryEnd = $month 
        ? $queryStart->copy()->endOfMonth() 
        : $queryStart->copy()->endOfYear();

    // 3. Define Base Queries
    $invoiceQuery     = Invoice::whereBetween('created_at', [$queryStart, $queryEnd]);
    $purchaseQuery    = Purchase::whereBetween('created_at', [$queryStart, $queryEnd]);
    $adjustmentQuery  = InventoryAdjustment::whereBetween('created_at', [$queryStart, $queryEnd]);

    // 4. Calculate Aggregate Data
    $invoiceCount   = (clone $invoiceQuery)->count();
    $totalSales     = (clone $invoiceQuery)->sum('grand_total');
    
    $purchaseCount  = (clone $purchaseQuery)->count();
    $totalSpent     = (clone $purchaseQuery)->sum('total_amount');

    $stockInQty     = (clone $purchaseQuery)->sum('quantity');
    
    $stockOutQty    = InvoiceItem::whereHas('invoice', function ($query) use ($queryStart, $queryEnd) {
                            $query->whereBetween('created_at', [$queryStart, $queryEnd]);
                      })->sum('qty');

    $totalWastage   = (clone $adjustmentQuery)
                        ->where('type', '!=', 'returned_defective')
                        ->sum('quantity');

    $wastageBreakdown = InventoryAdjustment::with('product')
                        ->select('product_id', 'type', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * unit_cost) as total_loss_amt'))
                        ->whereBetween('created_at', [$queryStart, $queryEnd])
                        ->where('type', '!=', 'returned_defective')
                        ->groupBy('product_id', 'type')
                        ->get();

    // 5. Global Stats
    $totalProducts     = Product::count();
    $totalCustomers    = Customer::count();
    $rawMaterialsCount = Product::where('category', 'raw_material')->count();
    $inStock           = Product::where('stock', '>', 0)->count();
    $outOfStock        = Product::where('stock', '<=', 0)->count();
    
    // Removed expiry logic as requested
    $nearExpiry = 0; 

    // 6. Financials Today
    $today          = Carbon::today();
    $salesToday     = Invoice::whereDate('created_at', $today)->sum('grand_total');
    $costToday      = Purchase::whereDate('created_at', $today)->sum('total_amount');

    // 7. Cheque Status
    $chequesPending = Cheque::where('status', 'pending')->count();
    $chequesCleared = Cheque::where('status', 'cleared')->count();
    $chequesBounced = Cheque::where('status', 'bounced')->count();

    $data = compact(
        'invoiceCount', 'totalSales', 'purchaseCount', 'totalSpent',
        'stockInQty', 'stockOutQty', 'totalWastage', 'totalProducts',
        'totalCustomers', 'rawMaterialsCount', 'inStock', 'outOfStock',
        'nearExpiry', 'salesToday', 'costToday', 'chequesPending',
        'chequesCleared', 'chequesBounced', 'wastageBreakdown'
    );

    return view('admin.dashboard', [
        'data'          => $data,
        'selectedYear'  => $year,
        'selectedMonth' => $month,
        'dateRange'     => $queryStart->format('M Y') . ($month ? '' : ' (Full Year)')
    ]);
}
    public function guide()
    {
        return view('admin.user-guide');
    }
}