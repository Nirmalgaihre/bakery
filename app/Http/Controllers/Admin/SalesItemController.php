<?php

namespace App\Http\Controllers\Admin;

use App\Models\SalesItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SalesItemController extends Controller
{
    public function index()
    {
        $items = SalesItem::latest()->paginate(15);
        return view('admin.sales.items.index', compact('items'));
    }

    public function create()
    {
        return view('admin.sales.items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|unique:sales_items,item_code|max:50',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'opening_price' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
            'quantity_in_hand' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'remarks' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        SalesItem::create($validated);

        return redirect()->route('admin.sales.items.index')
            ->with('success', 'Sales Item created successfully!');
    }

    public function edit(SalesItem $item)
    {
        return view('admin.sales.items.edit', compact('item'));
    }

    public function update(Request $request, SalesItem $item)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|unique:sales_items,item_code,' . $item->id . '|max:50',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'opening_price' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
            'quantity_in_hand' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'remarks' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $item->update($validated);

        return redirect()->route('admin.sales.items.index')
            ->with('success', 'Sales Item updated successfully!');
    }

    public function destroy(SalesItem $item)
    {
        $item->delete();

        return redirect()->route('admin.sales.items.index')
            ->with('success', 'Sales Item deleted successfully!');
    }

    public function export()
    {
        return Excel::download(new \App\Exports\SalesItemsExport, 'sales_items.xlsx');
    }

    public function importForm()
    {
        return view('admin.sales.items.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            Excel::import(new \App\Imports\SalesItemsImport, $request->file('file'));
            return redirect()->route('admin.sales.items.index')
                ->with('success', 'Sales Items imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
