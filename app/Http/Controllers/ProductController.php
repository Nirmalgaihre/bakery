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
     * Store a newly created product in the central registry database matrix.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255|unique:products,name',
            'category_id'       => 'required|exists:sector_categories,id', 
            'purchase_cost'     => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'inventory_unit'    => 'required|string|in:kg,paau,bottle,cartoon,boxes',
            'initial_stock'     => 'required|integer|min:0',
            'alert_stock_level' => 'required|integer|min:0',
        ]);

        $categoryModel = SectorCategory::findOrFail($validated['category_id']);

        Product::create([
            'name'              => $validated['name'],
            'category'          => $categoryModel->name, 
            'purchase_cost'     => $validated['purchase_cost'],
            'selling_price'     => $validated['selling_price'],
            'inventory_unit'    => $validated['inventory_unit'],
            'initial_stock'     => $validated['initial_stock'], 
            'alert_stock_level' => $validated['alert_stock_level'], 
        ]);

        // Clean redirection with NO hardcoded flash notification strings passed down
        return redirect()->route('admin.products.index');
    }
}