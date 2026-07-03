<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InventoryAdjustment;
use App\Models\InvoiceItem;
use App\Models\Purchase;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year', date('Y'));
        $month = $request->input('month');

        $queryStart = Carbon::create($year, $month ?? 1, 1)->startOfDay();
        $queryEnd   = $month
            ? $queryStart->copy()->endOfMonth()
            : $queryStart->copy()->endOfYear();

        $invoiceQuery    = Invoice::whereBetween('created_at', [$queryStart, $queryEnd]);
        $purchaseQuery   = Purchase::whereBetween('created_at', [$queryStart, $queryEnd]);
        $adjustmentQuery = InventoryAdjustment::whereBetween('created_at', [$queryStart, $queryEnd]);

        $invoiceCount = (clone $invoiceQuery)->count();
        $totalSales   = (clone $invoiceQuery)->sum('grand_total');

        $purchaseCount = (clone $purchaseQuery)->count();
        $totalSpent    = (clone $purchaseQuery)->sum('total_amount');

        $stockInQty = (clone $purchaseQuery)->sum('quantity');

        $stockOutQty = InvoiceItem::whereHas('invoice', function ($query) use ($queryStart, $queryEnd) {
                $query->whereBetween('created_at', [$queryStart, $queryEnd]);
            })
            ->sum('qty');

        $costOfGoodsSold = InvoiceItem::whereHas('invoice', function ($query) use ($queryStart, $queryEnd) {
                $query->whereBetween('created_at', [$queryStart, $queryEnd]);
            })
            ->with('product')
            ->get()
            ->sum(fn ($item) => $item->qty * ($item->product->purchase_cost ?? 0));

        $totalProducts     = Product::count();
        $totalCustomers    = Customer::count();
        $rawMaterialsCount = Product::where('category', 'raw_material')->count();
        $inStock           = Product::where('stock', '>', 0)->count();
        $outOfStock        = Product::where('stock', '<=', 0)->count();
 
        // Updated "Near Expiry" to be based on alert_stock_level < 10
        $nearExpiry = Product::where('alert_stock_level', '<', 10)
            ->count();

        $today      = Carbon::today();
        $salesToday = Invoice::whereDate('created_at', $today)->sum('grand_total');
        $costToday  = Purchase::whereDate('created_at', $today)->sum('total_amount');

        $chequesPending = Cheque::where('status', 'pending')->count();
        $chequesCleared = Cheque::where('status', 'cleared')->count();
        $chequesBounced = Cheque::where('status', 'bounced')->count();

        $netProfit = $totalSales - $costOfGoodsSold;

        $damageSpoiled = (clone $adjustmentQuery)->where('type', 'damage_spoiled')->sum('quantity');
        $customerReturn = (clone $adjustmentQuery)->where('type', 'customer_return')->sum('quantity');
        $internalUse = (clone $adjustmentQuery)->where('type', 'internal_use')->sum('quantity');
        $wastage = (clone $adjustmentQuery)->where('type', 'wastage')->sum('quantity');
        $scrap = (clone $adjustmentQuery)->where('type', 'scrap')->sum('quantity');

        $totalWastage = $damageSpoiled + $customerReturn + $internalUse + $wastage + $scrap;

        $adjustmentLabels = ['Damage / Spoiled', 'Customer Return', 'Internal Use', 'Wastage', 'Scrap'];
        $adjustmentValues = [$damageSpoiled, $customerReturn, $internalUse, $wastage, $scrap];
        $adjustmentColors = ['#ef4444', '#3b82f6', '#f59e0b', '#8b5cf6', '#6b7280'];

        $totalWastageAmount = InventoryAdjustment::whereBetween('created_at', [$queryStart, $queryEnd])
            ->whereIn('type', ['damage_spoiled', 'customer_return', 'internal_use', 'wastage', 'scrap'])
            ->sum(DB::raw('quantity * unit_cost'));

        $monthlyLabels = [];
        $monthlySales = [];
        $monthlyPurchases = [];

        if ($month) {
            $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
            $monthEnd   = $monthStart->copy()->endOfMonth();

            $monthlyLabels[] = $monthStart->format('M Y');
            $monthlySales[] = Invoice::whereBetween('created_at', [$monthStart, $monthEnd])->sum('grand_total');
            $monthlyPurchases[] = Purchase::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $monthStart = Carbon::create($year, $m, 1)->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();

                $monthlyLabels[] = $monthStart->format('M');
                $monthlySales[] = Invoice::whereBetween('created_at', [$monthStart, $monthEnd])->sum('grand_total');
                $monthlyPurchases[] = Purchase::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
            }
        }

        $lowStockProducts = Product::whereColumn('stock', '<=', 'alert_stock_level')
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->take(8)
            ->get();

        $stagnantProducts = Product::where('stock', '>', 0)
            ->whereDoesntHave('invoiceItems', function ($query) use ($queryStart, $queryEnd) {
                $query->whereHas('invoice', function ($q) use ($queryStart, $queryEnd) {
                    $q->whereBetween('created_at', [$queryStart, $queryEnd]);
                });
            })
            ->orderBy('stock', 'desc')
            ->take(8)
            ->get();

        $dueCustomers = Customer::where('previous_due', '>', 0)
            ->orderByDesc('previous_due')
            ->take(8)
            ->get();

        $watchlistCheques = Cheque::where('status', 'pending')
            ->orderBy('maturity_date_ad', 'asc')
            ->take(8)
            ->get();

        $dueTodayCheques = Cheque::where('status', 'pending')
            ->whereDate('maturity_date_ad', Carbon::today())
            ->get();

        $recentAdjustments = InventoryAdjustment::with('product')
            ->whereBetween('created_at', [$queryStart, $queryEnd])
            ->latest()
            ->take(8)
            ->get();

        $recentInvoices = Invoice::with(['customer'])
            ->latest()
            ->take(8)
            ->get();

        $data = compact(
            'invoiceCount',
            'totalSales',
            'purchaseCount',
            'totalSpent',
            'stockInQty',
            'stockOutQty',
            'totalProducts',
            'totalCustomers',
            'rawMaterialsCount',
            'inStock',
            'outOfStock',
            'nearExpiry',
            'salesToday',
            'costToday',
            'chequesPending',
            'chequesCleared',
            'chequesBounced',
            'costOfGoodsSold',
            'netProfit',
            'damageSpoiled',
            'customerReturn',
            'internalUse',
            'wastage',
            'scrap',
            'totalWastage',
            'totalWastageAmount',
            'adjustmentLabels',
            'adjustmentValues',
            'adjustmentColors',
            'monthlyLabels',
            'monthlySales',
            'monthlyPurchases',
            'lowStockProducts',
            'stagnantProducts',
            'dueCustomers',
            'watchlistCheques',
            'recentAdjustments',
            'dueTodayCheques',
            'recentInvoices'
        );

        return view('admin.dashboard', [
            'data'          => $data,
            'selectedYear'  => $year,
            'selectedMonth' => $month,
            'dateRange'     => $queryStart->format('M Y') . ($month ? '' : ' (Full Year)'),
        ]);
    }

    public function guide()
    {
        return view('admin.user-guide');
    }
}