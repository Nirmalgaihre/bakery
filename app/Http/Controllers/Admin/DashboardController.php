<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InventoryAdjustment; // Corrected model name
use App\Models\Cheque;
use App\Models\Purchase; // Assuming a Purchase model exists with 'purchase_date' and 'total_amount'
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $year = $request->input('year', $currentYear);
        $month = $request->input('month'); // This will be null if "Full Year Summary" is selected
        $dailyRange = $request->input('daily_range', 'today'); // Default to 'today' as per blade

        $startDate = null;
        $endDate = null;
        $dateRange = 'Overall Summary'; // Variable name for blade compatibility

        // Prioritize daily_range filter
        if (!empty($dailyRange) && $dailyRange !== '-- Select --') { // Check for actual selection, not just default or empty string
            switch ($dailyRange) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::now();
                    $dateRange = 'Today';
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday();
                    $endDate = Carbon::yesterday()->endOfDay();
                    $dateRange = 'Yesterday';
                    break;
                case '3days':
                    $startDate = Carbon::today()->subDays(2); // Includes today
                    $endDate = Carbon::now();
                    $dateRange = 'Last 3 Days';
                    break;
                case '7days':
                    $startDate = Carbon::today()->subDays(6); // Includes today
                    $endDate = Carbon::now();
                    $dateRange = 'Last 7 Days';
                    break;
                case '14days':
                    $startDate = Carbon::today()->subDays(13); // Includes today
                    $endDate = Carbon::now();
                    $dateRange = 'Last 14 Days';
                    break;
                case '28days':
                    $startDate = Carbon::today()->subDays(27); // Includes today
                    $endDate = Carbon::now();
                    $dateRange = 'Last 28 Days';
                    break;
            }
        } else { // If no specific daily range is selected or it's '-- Select --', use year/month filters
            if ($month) {
                $startDate = Carbon::create($year, $month, 1)->startOfDay();
                $endDate = Carbon::create($year, $month)->endOfMonth()->endOfDay();
                $dateRange = Carbon::createFromFormat('!m', $month)->format('F') . ' ' . $year;
            } else {
                // Full Year Summary for the selected year
                $startDate = Carbon::create($year, 1, 1)->startOfDay();
                $endDate = Carbon::create($year, 12, 31)->endOfDay();
                $dateRange = 'Full Year ' . $year;
            }
        }

        // --- Dashboard Data Queries (Filtered by $startDate and $endDate) ---

        // Sales Metrics
        $salesQuery = Invoice::query()->whereBetween('invoice_date', [$startDate, $endDate]);
        $totalSales = $salesQuery->sum('grand_total');
        $invoiceCount = $salesQuery->count();

        // Purchase Metrics (Assuming a 'Purchase' model with 'total_amount' and 'purchase_date')
        $purchaseQuery = Purchase::query()->whereBetween('purchase_date', [$startDate, $endDate]);
        $totalSpent = $purchaseQuery->sum('total_amount');
        $purchaseCount = $purchaseQuery->count();

        // Cost of Goods Sold & Stock Out Quantity
        $stockOutQty = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->sum('invoice_items.qty');

        $costOfGoodsSold = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->select(DB::raw('SUM(invoice_items.qty * products.purchase_cost) as total_cost'))
            ->value('total_cost') ?? 0;

        $netProfit = $totalSales - $costOfGoodsSold;

        // Product Statistics (Generally not date-range specific, but can be if needed)
        $totalProducts = Product::count();
        $inStock = Product::where('initial_stock', '>', 0)->count();
        $outOfStock = Product::where('initial_stock', '<=', 0)->count();
        $totalCustomers = Customer::count(); // Add this line to count total customers
        $nearExpiry = Product::whereColumn('initial_stock', '<=', 'alert_stock_level')->count(); // Count of products near low stock

        // Cheque Statistics
        $chequesPending = Cheque::where('status', 'Pending')->whereBetween('maturity_date_ad', [$startDate, $endDate])->count();
        $chequesBounced = Cheque::where('status', 'Bounced')->whereBetween('maturity_date_ad', [$startDate, $endDate])->count();
        $chequesCleared = Cheque::where('status', 'Cleared')->whereBetween('maturity_date_ad', [$startDate, $endDate])->count();

        $dueTodayCheques = Cheque::whereDate('maturity_date_ad', Carbon::today())->get();
        $watchlistCheques = Cheque::where('status', 'Pending')
                                ->where('maturity_date_ad', '>', Carbon::today())
                                ->orderBy('maturity_date_ad')
                                ->limit(5)
                                ->get();

        // Low Stock Products (Global)
        $lowStockProducts = Product::whereColumn('initial_stock', '<=', 'alert_stock_level')->get();

        // Due Customers (Global)
        $dueCustomers = Customer::where('previous_due', '>', 0)->orderByDesc('previous_due')->limit(5)->get();

        // Recent Adjustments
        $recentAdjustments = InventoryAdjustment::with('product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(5)
            ->get();

        // Wastage & Adjustments Breakdown Chart
        $adjustments = InventoryAdjustment::whereBetween('created_at', [$startDate, $endDate])
            ->select('type', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('type')
            ->get();

        $adjustmentLabels = $adjustments->pluck('type')->map(fn($type) => ucfirst($type))->toArray();
        $adjustmentValues = $adjustments->pluck('total_quantity')->toArray();
        $adjustmentColors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6']; // Example colors
        $totalWastage = array_sum($adjustmentValues);

        // Monthly/Daily Sales & Purchases for Charts
        $monthlyLabels = [];
        $monthlySales = [];
        $monthlyPurchases = [];

        if (!empty($dailyRange) && $dailyRange !== '-- Select --') {
            // Aggregate by day for daily ranges
            $period = Carbon::parse($startDate)->toPeriod($endDate, '1 day');
            foreach ($period as $date) {
                $dayLabel = $date->format('M d');
                $monthlyLabels[] = $dayLabel;
                $monthlySales[] = Invoice::whereDate('invoice_date', $date)->sum('grand_total');
                $monthlyPurchases[] = Purchase::whereDate('purchase_date', $date)->sum('total_amount');
            }
        } else {
            // Aggregate by month for monthly/yearly ranges
            $queryStartDate = Carbon::create($year, 1, 1)->startOfDay();
            $queryEndDate = Carbon::create($year, 12, 31)->endOfDay();

            $period = Carbon::parse($queryStartDate)->toPeriod($queryEndDate, '1 month');
            foreach ($period as $date) {
                // If a specific month is selected, only include that month
                if ($month && $date->month != $month) {
                    continue;
                }
                $monthLabel = $date->format('M');
                $monthlyLabels[] = $monthLabel;
                $monthlySales[] = Invoice::whereYear('invoice_date', $date->year)
                                        ->whereMonth('invoice_date', $date->month)
                                        ->sum('grand_total');
                $monthlyPurchases[] = Purchase::whereYear('purchase_date', $date->year)
                                            ->whereMonth('purchase_date', $date->month)
                                            ->sum('total_amount');
            }
        }

        $data = [
            'totalSales' => $totalSales,
            'invoiceCount' => $invoiceCount,
            'totalSpent' => $totalSpent,
            'purchaseCount' => $purchaseCount,
            'stockOutQty' => $stockOutQty,
            'costOfGoodsSold' => $costOfGoodsSold,
            'netProfit' => $netProfit,
            'totalProducts' => $totalProducts,
            'inStock' => $inStock,
            'outOfStock' => $outOfStock,
            'totalCustomers' => $totalCustomers, // Add this line to pass total customers to the view
            'nearExpiry' => $nearExpiry,
            'chequesPending' => $chequesPending,
            'chequesBounced' => $chequesBounced,
            'chequesCleared' => $chequesCleared,
            'dueTodayCheques' => $dueTodayCheques,
            'watchlistCheques' => $watchlistCheques,
            'lowStockProducts' => $lowStockProducts,
            'dueCustomers' => $dueCustomers,
            'recentAdjustments' => $recentAdjustments,
            'adjustmentLabels' => $adjustmentLabels,
            'adjustmentValues' => $adjustmentValues,
            'adjustmentColors' => $adjustmentColors,
            'totalWastage' => $totalWastage,
            'monthlyLabels' => $monthlyLabels,
            'monthlySales' => $monthlySales,
            'monthlyPurchases' => $monthlyPurchases,
        ];

        return view('admin.dashboard', compact('data', 'dateRange'));
    }
}