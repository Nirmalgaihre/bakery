<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SectorCategory; 
use App\Models\Product;       
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel; 

class ProductController extends Controller
{
    public function __construct()
    {
        // Only admins can create, store, edit, update, or import products.
        $this->middleware('can:create,App\Models\Product')->only(['create', 'store', 'importForm', 'import']);
        $this->middleware('can:update,product')->only(['edit', 'update']);
        // Note: Deletion policy would go here if you have a destroy method.
    }

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
 * Display the specified product.
 */
public function show($id)
{
    // Redirect to index because we don't have a dedicated "show" page
    return redirect()->route('admin.products.index');
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
public function export(Request $request)
{
    $type = $request->query('type', 'xlsx');
    $filename = 'products_' . now()->format('Ymd_His');

    if ($type === 'csv') {
        return Excel::download(new ProductsExport(), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    return Excel::download(new ProductsExport(), $filename . '.xlsx');
}

public function importForm()
{
    return view('admin.products.import');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
    ]);

    $import = new ProductsImport();

    try {
        Excel::import($import, $request->file('file'));
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();
        $messages = [];
        foreach ($failures as $failure) {
            $messages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
        return redirect()->back()->with('error', 'Import had errors: ' . implode(' | ', $messages));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
    }

    $message = "Import complete. Created: {$import->createdCount}, Updated: {$import->updatedCount}.";
    return redirect()->route('admin.products.index')->with('success', $message);
}

public function importTemplate()
{
    $headings = ['name', 'category', 'purchase_cost', 'selling_price', 'inventory_unit', 'initial_stock', 'current_stock', 'alert_stock_level'];

    return Excel::download(
        new class($headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private array $headings;
            public function __construct(array $headings) { $this->headings = $headings; }
            public function array(): array { return []; }
            public function headings(): array { return $this->headings; }
        },
        'product_import_template.xlsx'
    );
}
}