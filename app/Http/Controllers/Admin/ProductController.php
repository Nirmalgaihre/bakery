<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SectorCategory; 
use App\Models\Product;        

class ProductController extends Controller
{
    /**
     * Display a listing of the registered products.
     */
    public function index()
    {
        // Fetches all product models ordered by their creation date with pagination
        $products = Product::latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form engine for creating a new warehouse product item file.
     */
    public function create()
    {
        $categories = SectorCategory::orderBy('name', 'asc')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in the database matching your migration schema.
     */
    public function store(Request $request)
    {
        // 1. Strict Validation pipeline execution matching your specific schema rules
        $validated = $request->validate([
            'name'              => 'required|string|max:255|unique:products,name',
            'category_id'       => 'required|exists:sector_categories,id', 
            'purchase_cost'     => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'inventory_unit'    => 'required|string|in:kg,paau,bottle,cartoon,boxes',
            'initial_stock'     => 'required|numeric|min:0', 
            'alert_stock_level' => 'required|integer|min:0',
        ], [
            'category_id.exists' => 'The selected system master category configuration is invalid.',
            'inventory_unit.in'  => 'Please select a valid inventory packaging unit from the dropdown list.'
        ]);

        // Find the category model to extract its text name string
        $categoryModel = SectorCategory::findOrFail($validated['category_id']);

        // 2. Persistent storage generation mapping parameters down to your columns
        Product::create([
            'name'              => $validated['name'],
            'category'          => $categoryModel->name, 
            'purchase_cost'     => $validated['purchase_cost'],
            'selling_price'     => $validated['selling_price'],
            'inventory_unit'    => $validated['inventory_unit'],
            'initial_stock'     => $validated['initial_stock'], 
            'stock'             => $validated['initial_stock'], // सुरुमा Current Stock र Initial Stock बराबर हुन्छ
            'alert_stock_level' => $validated['alert_stock_level'], 
        ]);

        // 3. Automated redirection accompanied by a global success banner token state
        return redirect()->route('admin.products.index')
                         ->with('success', 'Product registered in the system inventory matrix successfully!');
    }
    public function edit($id)
{
    $product = \App\Models\Product::findOrFail($id);
    // आफ्नो आवश्यकता अनुसार view को पाथ मिलाउनुहोस्
    return view('admin.products.edit', compact('product'));
}
}