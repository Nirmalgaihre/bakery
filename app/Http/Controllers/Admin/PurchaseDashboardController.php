<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PurchaseDashboardController extends Controller
{
    public function index(Request $request)
{
    $fy = $request->input('fiscal_year', \App\Helpers\FiscalYearHelper::getCurrentFiscalYear());
    $range = $request->input('range', 'all');

    // Date Range calculation for the display string
    $dateRange = "Fiscal Year " . $fy;
    if ($range !== 'all') {
        $dateRange .= " (" . ucfirst($range) . ")";
    }

    $years = explode('/', $fy);
    $startYear = $years[0];
    $startDate = $startYear . '-04-01';
    $endDate = ($startYear + 1) . '-03-31';

    $query = Purchase::whereBetween('purchase_date', [$startDate, $endDate]);

    if ($range === 'today') {
        $query->whereDate('purchase_date', today());
    } elseif ($range === '7days') {
        $query->where('purchase_date', '>=', now()->subDays(7));
    } elseif ($range === '1month') {
        $query->where('purchase_date', '>=', now()->subMonth());
    }

    $totalPurchased = (clone $query)->sum('total_amount');
    $purchaseCount = (clone $query)->count();
    $averagePurchase = $purchaseCount > 0 ? ($totalPurchased / $purchaseCount) : 0;
    
    $purchasesToday = Purchase::whereDate('purchase_date', today())->sum('total_amount');
    $totalProducts = Product::count();
    $inStock = Product::where('stock', '>', 0)->count();
    $outOfStock = Product::where('stock', '<=', 0)->count();

    $chartData = (clone $query)->selectRaw('DATE(purchase_date) as date, SUM(total_amount) as total')
        ->groupBy('date')->orderBy('date', 'ASC')->get();
            
    $recentPurchases = (clone $query)->latest()->take(10)->get();

    $suppliers = (clone $query)
        ->select('supplier_name', \DB::raw('count(*) as purchase_count'))
        ->groupBy('supplier_name')->orderBy('purchase_count', 'desc')->take(5)->get();

    return view('admin.purchases.dashboard', compact(
        'totalPurchased', 'purchasesToday', 'purchaseCount', 'averagePurchase',
        'totalProducts', 'inStock', 'outOfStock', 'chartData', 'suppliers', 
        'recentPurchases', 'range', 'fy', 'dateRange' // Added 'dateRange' here
    ));
}
    public function create()
{
    $categories = \App\Models\SectorCategory::all();
    
    // यहाँ 'admin.purchases.create' को ठाउँमा 'admin.products.create' राख्नुहोस्
    return view('admin.products.create', compact('categories'));
}
}