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
    // १. Fiscal Year र Range लिने
    $fy = $request->input('fiscal_year', \App\Helpers\FiscalYearHelper::getCurrentFiscalYear());
    $range = $request->input('range', 'all'); // 'all' डिफल्ट राख्नु राम्रो हुन्छ

    // २. Fiscal Year अनुसार Date Range निकाल्ने
    $years = explode('/', $fy);
    $startYear = $years[0];
    $startDate = $startYear . '-04-01';
    $endDate = ($startYear + 1) . '-03-31';

    // ३. Base Query (Fiscal Year अनुसार फिल्टर गर्ने)
    $query = Purchase::whereBetween('purchase_date', [$startDate, $endDate]);

    // ४. यदि थप 'Range' फिल्टर पनि छ भने त्यसलाई पनि थप्ने
    if ($range === 'today') {
        $query->whereDate('purchase_date', today());
    } elseif ($range === '3days') {
        $query->where('purchase_date', '>=', now()->subDays(3));
    } elseif ($range === '7days') {
        $query->where('purchase_date', '>=', now()->subDays(7));
    } elseif ($range === '1month') {
        $query->where('purchase_date', '>=', now()->subMonth());
    }

    // ५. गणना गर्ने (Clone प्रयोग गरेर)
    $totalPurchased = (clone $query)->sum('total_amount');
    $purchaseCount = (clone $query)->count();
    $averagePurchase = $purchaseCount > 0 ? ($totalPurchased / $purchaseCount) : 0;
    
    // आजको खरिद (यो Fiscal Year सँग सम्बन्धित नहुन सक्छ, त्यसैले छुट्टै राखेको)
    $purchasesToday = Purchase::whereDate('purchase_date', today())->sum('total_amount');

    // अन्य डेटा
    $totalProducts = Product::count();
    $inStock = Product::where('stock', '>', 0)->count();
    $outOfStock = Product::where('stock', '<=', 0)->count();

    // Chart Data
    $chartData = (clone $query)->selectRaw('DATE(purchase_date) as date, SUM(total_amount) as total')
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();
            
    $recentPurchases = (clone $query)->latest()->take(10)->get();

    // Suppliers
    $suppliers = (clone $query)
        ->select('supplier_name', \DB::raw('count(*) as purchase_count'))
        ->groupBy('supplier_name')
        ->orderBy('purchase_count', 'desc')
        ->take(5)
        ->get();

    return view('admin.purchases.dashboard', compact(
        'totalPurchased', 'purchasesToday', 'purchaseCount', 'averagePurchase',
        'totalProducts', 'inStock', 'outOfStock', 'chartData', 'suppliers', 'recentPurchases', 'range'
    ));
}
    public function create()
{
    $categories = \App\Models\SectorCategory::all();
    
    // यहाँ 'admin.purchases.create' को ठाउँमा 'admin.products.create' राख्नुहोस्
    return view('admin.products.create', compact('categories'));
}
}