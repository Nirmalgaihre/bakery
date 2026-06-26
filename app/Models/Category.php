<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Category; // Ensure this matches the namespace of your Category model
use Illuminate\Support\Facades\DB;

class PurchaseDashboardController extends Controller
{
    // ... your existing index method ...

    public function create()
    {
        // 1. Fetch categories to populate your dropdown or selection list
        $categories = Category::all();

        // 2. Return the view with the categories variable
        // Make sure the path matches your resources/views/admin/products/create.blade.php
        return view('admin.products.create', compact('categories'));
    }
}