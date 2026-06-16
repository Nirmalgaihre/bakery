<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\InvoiceItem;
use App\Models\InventoryAdjustment; // 👈 फिक्स: पुरानो Wastage को सट्टा यो मोडल राखिएको छ
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // 👈 थपिएको: GROUP BY र SUM को लागि

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        
        // ड्यासबोर्ड फिल्टर रेन्ज कन्फिगरेसन
        $range = $request->get('range', '1month'); 
        $startDate = match ($range) {
            'today'     => now()->startOfDay(),
            '3days'     => now()->subDays(3)->startOfDay(),
            '7days'     => now()->subDays(7)->startOfDay(),
            '1month'    => now()->subMonth()->startOfDay(),
            '6months'   => now()->subMonths(6)->startOfDay(),
            '12months'  => now()->subYear()->startOfDay(),
            'yearwise'  => now()->startOfYear(), 
            default     => now()->subMonth()->startOfDay(),
        };

        // वित्तीय तथा बिक्री रेकर्डहरू (फिल्टर रेन्ज अनुसार)
        $invoiceCount   = Invoice::where('created_at', '>=', $startDate)->count();
        $totalSales     = Invoice::where('created_at', '>=', $startDate)->sum('grand_total'); 
        $purchaseCount  = Purchase::where('created_at', '>=', $startDate)->count();
        $totalSpent     = Purchase::where('created_at', '>=', $startDate)->sum('total_amount'); 
        
        $stockInQty     = Purchase::where('created_at', '>=', $startDate)->sum('quantity');
        $stockOutQty    = InvoiceItem::where('created_at', '>=', $startDate)->sum('qty');

        // 💡 डायनामिक वेस्टेज फिक्स: InventoryAdjustment टेबलबाट (Customer Return बाहेक) सम निकालिएको छ
        $totalWastage   = InventoryAdjustment::where('created_at', '>=', $startDate)
                            ->where('type', '!=', 'returned_defective')
                            ->sum('quantity');

        // 💡 ब्रेकडाउन लोजिक: कुन सामान के कारणले (Expired/Damaged आदि) कति वेस्ट भयो र कति नोक्सान भयो भन्ने डेटा
        $wastageBreakdown = InventoryAdjustment::with('product')
                            ->select('product_id', 'type', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * unit_cost) as total_loss_amt'))
                            ->where('created_at', '>=', $startDate)
                            ->where('type', '!=', 'returned_defective')
                            ->groupBy('product_id', 'type')
                            ->get();

        // रियल-टाइम इन्भेन्टरी (हालको मौज्दात अवस्था)
        $totalProducts     = Product::count();
        $totalCustomers    = Customer::count();
        $rawMaterialsCount = Product::where('category', 'raw_material')->count();
        $inStock           = Product::where('stock', '>', 0)->count();      
        $outOfStock        = Product::where('stock', '<=', 0)->count();    
        $nearExpiry        = 0; 

        // आजको दिनको वित्तीय अवस्था
        $salesToday        = Invoice::whereDate('created_at', $today)->sum('grand_total');
        $costToday         = Purchase::whereDate('created_at', $today)->sum('total_amount');

        // चेक व्यवस्थापन प्रणाली
        $chequesPending    = \App\Models\Cheque::where('status', 'pending')->count();
        $chequesCleared    = \App\Models\Cheque::where('status', 'cleared')->count();
        $chequesBounced    = \App\Models\Cheque::where('status', 'bounced')->count();

        $data = compact(
            'invoiceCount', 'totalSales', 'purchaseCount', 'totalSpent',
            'stockInQty', 'stockOutQty', 'totalWastage', 'totalProducts',
            'totalCustomers', 'rawMaterialsCount', 'inStock', 'outOfStock',
            'nearExpiry', 'salesToday', 'costToday', 'chequesPending',
            'chequesCleared', 'chequesBounced', 'wastageBreakdown' // 👈 यो भेरिएबल ब्लेडमा पास गरिएको छ
        );

        return view('admin.dashboard', compact('data', 'range'));
    }
}