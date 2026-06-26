<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SectorCategory;
use Illuminate\Http\Request;

class SectorCategoryController extends Controller 
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        $categories = SectorCategory::all();
        return view('admin.categories', compact('categories'))->with('editingCategory', null);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sector_categories,name'
        ]);

        try {
            // This now correctly calls the Eloquent create method
            SectorCategory::create(['name' => $request->name]);
            return redirect()->route('admin.categories.index')->with('success', 'Your category has been successfully registered!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was a problem processing this record. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) 
    {
        $categories = SectorCategory::all();
        $editingCategory = SectorCategory::findOrFail($id);
        return view('admin.categories', compact('categories', 'editingCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) 
    {
        $category = SectorCategory::findOrFail($id);
        
        $request->validate([
            'name' => "required|string|max:255|unique:sector_categories,name,{$id}"
        ]);

        try {
            $category->update(['name' => $request->name]);
            return redirect()->route('admin.categories.index')->with('success', 'Your changes have been saved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was a problem updating your file. Please try again.');
        }
    }
}