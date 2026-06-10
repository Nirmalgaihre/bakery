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
            $oldPurchaseCost = $product->purchase_cost;
            $oldSellingPrice = $product->selling_price;

            // Log Transaction Audit Trail
            StockTransaction::create([
                'product_id'        => $product->id,
                'quantity'          => $validated['quantity'],
                'type'              => 'addition',
                'old_purchase_cost' => $oldPurchaseCost,
                'new_purchase_cost' => $validated['purchase_cost'],
                'old_selling_price' => $oldSellingPrice,
                'new_selling_price' => $validated['selling_price'],
                'reference_note'    => $validated['reference_note'],
            ]);

            // Update Master Stock Quantities and Price Variations
            $product->initial_stock += $validated['quantity'];
            $product->purchase_cost = $validated['purchase_cost'];
            $product->selling_price = $validated['selling_price'];
            $product->save();
        });

        return redirect()->route('admin.products.index')->with('success', 'Stock entries updated alongside pricing metrics successfully!');
    }
}