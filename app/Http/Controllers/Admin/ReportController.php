<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use App\Models\Product;


class ReportController extends Controller
{
    public function index(Request $request)
{
    // कतिवटा डेटा देखाउने (डिफल्ट १५, नत्र प्रयोगकर्ताले रोजेको)
    $perPage = $request->input('per_page', 15);

    $customers = Customer::query();

    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $customers->where(function($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone_number', 'like', '%' . $searchTerm . '%');
        });
    }

    $customers = $customers->orderBy('name')->paginate($perPage);

    return view('admin.reports.report', compact('customers'));
}

    /**
     * Daily Cash Flow Report: Aggregates sales vs purchases for net profit.
     */
    public function cashFlowReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $sales = Invoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('grand_total');
        $purchases = Purchase::whereBetween('purchase_date', [$startDate, $endDate])->sum('total_amount');

        $netProfit = $sales - $purchases;

        // You might want to fetch daily breakdown for a chart or table
        $dailySales = Invoice::select(DB::raw('DATE(invoice_date) as date'), DB::raw('SUM(grand_total) as total_sales'))
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyPurchases = Purchase::select(DB::raw('DATE(purchase_date) as date'), DB::raw('SUM(total_amount) as total_purchases'))
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Merge daily sales and purchases for a combined view
        $cashFlowData = collect();
        $allDates = $dailySales->pluck('date')->merge($dailyPurchases->pluck('date'))->unique()->sort();

        foreach ($allDates as $date) {
            $sale = $dailySales->firstWhere('date', $date)?->total_sales ?? 0;
            $purchase = $dailyPurchases->firstWhere('date', $date)?->total_purchases ?? 0;
            $cashFlowData->push([
                'date' => $date,
                'sales' => $sale,
                'purchases' => $purchase,
                'net_profit' => $sale - $purchase,
            ]);
        }

        return view('admin.reports.cash_flow', compact('sales', 'purchases', 'netProfit', 'cashFlowData', 'startDate', 'endDate'));
    }

    /**
     * Stock Movement Report: Top-selling items vs. slow-moving/stagnant stock.
     */
    public function stockMovementReport(Request $request)
    {
        $period = $request->input('period', '3months'); // Default to last 3 months
        $startDate = Carbon::now()->subMonths(3)->startOfDay();

        switch ($period) {
            case '1month': $startDate = Carbon::now()->subMonth()->startOfDay(); break;
            case '6months': $startDate = Carbon::now()->subMonths(6)->startOfDay(); break;
            case '1year': $startDate = Carbon::now()->subYear()->startOfDay(); break;
            case 'all': $startDate = Carbon::minValue(); break;
        }

        // Top-selling items
        $topSellingItems = InvoiceItem::select('product_id', DB::raw('SUM(qty) as total_quantity_sold'), DB::raw('SUM(total) as total_revenue'))
            ->whereHas('invoice', fn($query) => $query->where('invoice_date', '>=', $startDate))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity_sold')
            ->limit(10)
            ->get();

        // Slow-moving/Stagnant stock (simplified: products not in top sellers and with low sales)
        $soldProductIds = $topSellingItems->pluck('product_id')->toArray();
        $slowMovingItems = Product::with(['invoiceItems' => fn($query) => $query->whereHas('invoice', fn($q) => $q->where('invoice_date', '>=', $startDate))])
            ->get()
            ->filter(fn($product) => !in_array($product->id, $soldProductIds) && $product->invoiceItems->sum('qty') < 5) // Threshold of <5 units sold
            ->sortBy('initial_stock');

        return view('admin.reports.stock_movement', compact('topSellingItems', 'slowMovingItems', 'period'));
    }
}