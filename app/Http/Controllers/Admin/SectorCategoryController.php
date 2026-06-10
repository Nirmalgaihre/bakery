<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SectorCategory;
use Illuminate\Http\Request;

class SectorCategoryController extends Controller {
    
    public function index() {
        $categories = SectorCategory::all();
        return view('admin.categories', compact('categories'))->with('editingCategory', null);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255|unique:sector_categories,name'
        ]);

        try {
            SectorCategory::create(['name' => $request->name]);
            return redirect()->route('admin.categories.index')->with('success', 'Your category has been successfully registered!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was a problem processing this record. Please try again.');
        }
    }

    public function edit($id) {
        $categories = SectorCategory::all();
        $editingCategory = SectorCategory::findOrFail($id);
        return view('admin.categories', compact('categories', 'editingCategory'));
    }

    public function update(Request $request, $id) {
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