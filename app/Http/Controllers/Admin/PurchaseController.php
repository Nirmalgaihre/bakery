<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::latest()->paginate(20);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required',
            'items'       => 'required|array',
            'items.*.id'  => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.cost'=> 'required|numeric'
        ]);

        DB::transaction(function () use ($validated) {
            $purchase = new Purchase;
            $purchase->supplier_id = $validated['supplier_id'];
            $purchase->save();

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                // INCREMENT stock for purchases
                $product->increment('initial_stock', $item['qty']);
                
                // Track in PurchaseItems table
                $purchase->items()->create([
                    'product_id' => $item['id'],
                    'qty'        => $item['qty'],
                    'cost'       => $item['cost'],
                ]);
            }
        });

        return back()->with('success', 'Stock replenished successfully.');
    }
}