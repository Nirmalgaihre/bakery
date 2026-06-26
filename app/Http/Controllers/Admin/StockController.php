<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\SectorCategory; // Import your model
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function create(Product $product)
    {
        // Fetch categories to allow the user to potentially re-categorize during stock entry
        $categories = SectorCategory::all();
        
        return view('admin.stock.add', compact('product', 'categories'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity'       => 'required|numeric|min:0.001',
            'purchase_cost'  => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'category_id'    => 'nullable|exists:sector_categories,id', // Added validation
            'reference_note' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($product, $validated) {
            // 1. Log the transaction
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

            // 2. Update Product details
            $updateData = [
                'purchase_cost' => $validated['purchase_cost'],
                'selling_price' => $validated['selling_price'],
                'initial_stock' => $product->initial_stock + $validated['quantity'] 
            ];

            // Only update category if provided
            if (!empty($validated['category_id'])) {
                $updateData['sector_category_id'] = $validated['category_id'];
            }

            $product->update($updateData);
        });

        return redirect()->route('admin.products.index')
                         ->with('success', "Stock for {$product->name} updated successfully.");
    }
}