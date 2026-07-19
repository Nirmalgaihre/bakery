<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        // Eager load the supplier relation structure to avoid N+1 query slow-downs
        $purchases = Purchase::with('supplier')->latest()->paginate(20);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items'       => 'required|array',
            'items.*.id'  => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.cost'=> 'required|numeric|min:0'
        ]);

        $supplier = Supplier::findOrFail($validated['supplier_id']);

        DB::transaction(function () use ($validated, $supplier) {
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                
                // 1. Maintain active calculations across product metrics
                $product->increment('stock', $item['qty']);
                $product->increment('initial_stock', $item['qty']);
                
                // 2. Generate detailed tracking entries for your flat records table
                Purchase::create([
                    'supplier_id'    => $supplier->id,
                    'supplier_name'  => $supplier->name,
                    'item_name'      => $product->name,
                    'quantity'       => $item['qty'],
                    'unit'           => $product->inventory_unit,
                    'price_per_unit' => $item['cost'],
                    'total_amount'   => $item['qty'] * $item['cost'],
                    'purchase_date'  => now()->toDateString(),
                    'notes'          => 'Stock replenishment auto-logged.',
                ]);
            }
        });

        return back()->with('success', 'Stock replenished successfully.');
    }
}