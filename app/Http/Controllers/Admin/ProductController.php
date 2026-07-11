<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SectorCategory; 
use App\Models\Product;       
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel; 
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Validators\ValidationException;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create,App\Models\Product')->only(['create', 'store', 'importForm', 'import', 'importTemplate']);
        // Ensure your Route Model Binding is set up for 'product'
        $this->middleware('can:update,product')->only(['edit', 'update']);
    }

    public function index()
    {
        $products = Product::latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = SectorCategory::orderBy('name', 'asc')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function show($id)
    {
        return redirect()->route('admin.products.index');
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'item_code'         => 'required|string|max:50|unique:products,item_code',
        'name'              => 'required|string|max:255|unique:products,name',
        'category_id'       => 'required|string|max:255', 
        'color'             => 'nullable|string|max:50',
        'size'              => 'nullable|string|max:50',
        'purchase_cost'     => 'required|numeric|min:0',
        'selling_price'     => 'required|numeric|min:0',
        'inventory_unit'    => 'required|string|in:kg,paau,bottle,cartoon,boxes',
        'initial_stock'     => 'required|numeric|min:0', 
        'alert_stock_level' => 'required|integer|min:0',
    ]);

    \App\Models\Product::create([
        'item_code'         => $validated['item_code'],
        'name'              => $validated['name'],
        'category_id'       => $validated['category_id'], // Now stores 'cat-oki'
        'category'          => $validated['category_id'], // Storing the same string
        'color'             => $validated['color'],
        'size'              => $validated['size'],
        'purchase_cost'     => $validated['purchase_cost'],
        'selling_price'     => $validated['selling_price'],
        'inventory_unit'    => $validated['inventory_unit'],
        'initial_stock'     => $validated['initial_stock'], 
        'stock'             => $validated['initial_stock'],
        'alert_stock_level' => $validated['alert_stock_level'],
        'alert_sent'        => false,
    ]);

    return redirect()->route('admin.products.index')
                     ->with('success', 'Product registered successfully!');
}
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function export(Request $request, $type)
    {
        $extension = ($type === 'csv') ? 'csv' : 'xlsx';
        $writerType = ($type === 'csv') ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;
        
        return Excel::download(new ProductsExport, 'products_registry_' . now()->format('Y-m-d') . '.' . $extension, $writerType);
    }

    public function importForm()
    {
        return view('admin.products.import');
    }

    public function import(Request $request)
    {
        if (!$request->hasFile('file')) {
            return back()->with('error', 'Please select a file to upload.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new \App\Imports\ProductsImport;
            Excel::import($import, $request->file('file'));

            $successMessage = 'Products imported successfully!';
            if ($import->createdCount > 0 || $import->updatedCount > 0) {
                $successMessage .= " Created: {$import->createdCount}, Updated: {$import->updatedCount}.";
            }

            if ($import->failures()->isNotEmpty()) {
                $failureMessages = [];
                foreach ($import->failures() as $failure) {
                    $failureMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
                }
                return back()->with('error', $successMessage . "\nSome rows failed to import:\n" . implode("\n", $failureMessages));
            }
            
            return back()->with('success', $successMessage);

        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Validation failed during import:\n' . implode("\n", $errorMessages));
        } catch (\Exception $e) {
            Log::error('Product Import Failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'An unexpected error occurred during import: ' . $e->getMessage());
        }
    }

    public function importTemplate()
    {
        $headings = ['name', 'category', 'purchase_cost', 'selling_price', 'inventory_unit', 'initial_stock', 'current_stock', 'alert_stock_level'];
        
        try {
            return Excel::download(
                new class($headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    private array $headings;
                    public function __construct(array $headings) { $this->headings = $headings; }
                    public function array(): array { return []; }
                    public function headings(): array { return $this->headings; }
                },
                'product_import_template.xlsx'
            );
        } catch (\Exception $e) {
            Log::error("Product import template download failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download template.');
        }
    }
}