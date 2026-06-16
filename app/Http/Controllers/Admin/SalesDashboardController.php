<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesDashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        
        // फिल्टर रेन्ज सेटिङ (Default: Last 3 Months)
        $range = $request->get('range', '3months');
        $startDate = match ($range) {
            'today'     => now()->startOfDay(),
            'yesterday' => now()->subDay()->startOfDay(),
            '1week'     => now()->subWeek(),
            '1month'    => now()->subMonth(),
            '3months'   => now()->subMonths(3),
            '6months'   => now()->subMonths(6),
            '12months'  => now()->subYear(),
            default     => now()->subMonths(3),
        };

        // १. वित्तीय मेट्रिक्स (सेलेक्ट गरिएको टाइम रेन्ज अनुसार)
        $totalRevenue  = Invoice::where('created_at', '>=', $startDate)->sum('grand_total');
        $invoiceCount  = Invoice::where('created_at', '>=', $startDate)->count();
        $salesToday    = Invoice::whereDate('created_at', $today)->sum('grand_total');
        $averageInvoice = $invoiceCount > 0 ? ($totalRevenue / $invoiceCount) : 0;

        // २. इन्भेन्टरी मेट्रिक्स (हालको रियल-टाइम स्टक अवस्था)
        $totalProducts = Product::count();
        $inStock       = Product::where('stock', '>', 0)->count();      // stock कोलम अनुसार बाँकी सामान
        $outOfStock    = Product::where('stock', '<=', 0)->count();    // सकिएका सामान

        // ३. होल पाई चार्टका लागि बिक्री विवरण (Labels र Dataset)
        $chartData = Invoice::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // ४. टप ५ हाइ-भोल्युम ग्राहकहरू
        $customers = Customer::withCount(['invoices' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->orderBy('invoices_count', 'desc')
            ->limit(5)
            ->get();

        // ५. हालै काटिएका ५ वटा लाइभ बिलहरू
        $recentInvoices = Invoice::with('customer')->latest()->limit(5)->get();

        return view('admin.sales.dashboard', compact(
            'range',
            'totalRevenue',
            'invoiceCount',
            'salesToday',
            'averageInvoice',
            'totalProducts',
            'inStock',
            'outOfStock',
            'chartData',
            'customers',
            'recentInvoices'
        ));
    }
}