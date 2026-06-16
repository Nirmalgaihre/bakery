<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function create(Product $product)
    {
        return view('admin.stock.add', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity'       => 'required|integer|min:1',
            'purchase_cost'  => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'reference_note' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($product, $validated) {
            StockTransaction::create([
                'product_id'        => $product->id,
                'quantity'          => $validated['quantity'],
                'type'              => 'addition',
                'old_purchase_cost' => $product->purchase_cost,
                'new_purchase_cost' => $validated['purchase_cost'],
                'old_selling_price' => $product->selling_price,
                'new_selling_price' => $validated['selling_price'],
                'reference_note'    => $validated['reference_note'],
            ]);

            $product->increment('stock', $validated['quantity']);
            
            $product->update([
                'purchase_cost' => $validated['purchase_cost'],
                'selling_price' => $validated['selling_price'],
                'initial_stock' => $product->initial_stock + $validated['quantity'] 
            ]);
        });

        return redirect()->route('admin.products.index')
                         ->with('success', "Stock for {$product->name} updated successfully.");
    }
}