<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SectorCategory; 
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Imports\PurchasesImport; // Added for Purchase inventory imports
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Validators\ValidationException;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create,App\Models\Product')->only(['create', 'store', 'importForm', 'import', 'importTemplate']);
        // Note: Ensure your route parameter in routes/web.php is named exactly {product}
        $this->middleware('can:update,product')->only(['edit', 'update']);
    }

// 2. Update the index method
public function index(Request $request)
{
    // Build query with requested sorting
    $query = Product::query();

    // Apply Sorting Options
    switch ($request->get('sort')) {
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        case 'alpha_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'alpha_desc':
            $query->orderBy('name', 'desc');
            break;
        case 'stock_high':
            $query->orderBy('stock', 'desc');
            break;
        case 'stock_low':
            $query->orderBy('stock', 'asc');
            break;
        case 'newest':
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    // 1. Fetch 50 products per page with active query string preserved
    $products = $query->paginate(50)->withQueryString();

    // 2. Fetch purchases pagination or count for the view
    $purchases = Purchase::latest()->paginate(50);

    // 3. Aggregate metrics for the ENTIRE database
    $totalProductsCount = Product::count();
    $lowStockCount      = Product::whereRaw('CAST(stock AS SIGNED) <= CAST(alert_stock_level AS SIGNED)')->count();
    $totalStockUnits    = Product::sum('stock');

    return view('admin.products.index', compact(
        'products',
        'purchases', // <--- Pass $purchases to the view
        'totalProductsCount',
        'lowStockCount',
        'totalStockUnits'
    ));
}

    public function create()
    {
        $categories = SectorCategory::all();
        $suppliers = Supplier::all();

        return view('admin.products.create', compact('categories', 'suppliers'));
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
            'category_id'       => 'required|exists:sector_categories,id',
            'supplier_id'       => 'nullable|exists:suppliers,id',
            'color'             => 'nullable|string|max:50',
            'size'              => 'nullable|string|max:50',
            'purchase_cost'     => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'inventory_unit'    => 'required|string|in:pcs,kg,paau,bottle,cartoon,boxes',
            'initial_stock'     => 'required|numeric|min:0',
            'alert_stock_level' => 'required|numeric|min:0',
        ]);

        Product::create([
            'item_code'         => $validated['item_code'],
            'name'              => $validated['name'],
            'category_id'       => $validated['category_id'],
            'supplier_id'       => $validated['supplier_id'] ?? null,
            'purchase_cost'     => $validated['purchase_cost'],
            'selling_price'     => $validated['selling_price'],
            'inventory_unit'    => $validated['inventory_unit'],
            'initial_stock'     => $validated['initial_stock'],
            'stock'             => $validated['initial_stock'],
            'alert_stock_level' => $validated['alert_stock_level'],
            'alert_sent'        => false,
            'color'             => $validated['color'],
            'size'              => $validated['size'],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product registered successfully!');
    }

    public function edit(Product $product)
    {
        $categories = SectorCategory::all();
        $suppliers = Supplier::all(); 

        return view('admin.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'item_code'         => 'required|string|max:50|unique:products,item_code,' . $product->id,
            'name'              => 'required|string|max:255|unique:products,name,' . $product->id,
            'category_id'       => 'required|exists:sector_categories,id',
            'supplier_id'       => 'nullable|exists:suppliers,id',
            'color'             => 'nullable|string|max:50',
            'size'              => 'nullable|string|max:50',
            'purchase_cost'     => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'inventory_unit'    => 'required|string|in:pcs,kg,paau,bottle,cartoon,boxes',
            'initial_stock'     => 'required|numeric|min:0',
            'alert_stock_level' => 'required|numeric|min:0',
        ]);

        $product->update([
            'item_code'         => $validated['item_code'],
            'name'              => $validated['name'],
            'category_id'       => $validated['category_id'],
            'supplier_id'       => $validated['supplier_id'] ?? null,
            'purchase_cost'     => $validated['purchase_cost'],
            'selling_price'     => $validated['selling_price'],
            'inventory_unit'    => $validated['inventory_unit'],
            'initial_stock'     => $validated['initial_stock'],
            'alert_stock_level' => $validated['alert_stock_level'],
            'color'             => $validated['color'],
            'size'              => $validated['size'],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
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
        $import = new ProductsImport;
        Excel::import($import, $request->file('file'));

        $issues = [];

        foreach ($import->failures() as $failure) {
            $issues[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }

        foreach ($import->errors() as $error) {
            $issues[] = 'Import error: ' . $error->getMessage();
        }

        // Custom, human-readable problems (duplicates, missing category, etc.)
        $issues = array_merge($issues, $import->customIssues);

        $nothingImported = $import->createdCount === 0 && $import->updatedCount === 0;

        if ($nothingImported && !empty($issues)) {
            // Pure failure — show ONLY the error, no "successfully" wording at all.
            return back()->with('error', "No products were imported:\n" . implode("\n", $issues));
        }

        if ($nothingImported) {
            return back()->with('error', 'No products were imported. Check that your file headers match the required format.');
        }

        if (!empty($issues)) {
            // Partial success — keep success and problems visually/textually separate.
            return back()
                ->with('success', "Created: {$import->createdCount}, Updated: {$import->updatedCount}.")
                ->with('error', "Some rows had issues:\n" . implode("\n", $issues));
        }

        return back()->with('success', "Products imported successfully! Created: {$import->createdCount}, Updated: {$import->updatedCount}.");

    } catch (ValidationException $e) {
        $errorMessages = [];
        foreach ($e->failures() as $failure) {
            $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
        return back()->with('error', "Validation failed during import:\n" . implode("\n", $errorMessages));
    } catch (\Exception $e) {
        Log::error('Product Import Failed: ' . $e->getMessage(), ['exception' => $e]);
        return back()->with('error', 'An unexpected error occurred during import.');
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
    public function bulkDestroy(Request $request)
{
    // Restrict operation to the specific email address
    if (auth()->user()->email !== 'gaihrenirmal2021@gmail.com') {
        abort(403, 'Unauthorized action.');
    }

    $validated = $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:products,id',
    ]);

    $count = Product::whereIn('id', $validated['ids'])->delete();

    return redirect()->route('admin.products.index')
        ->with('success', "Successfully deleted {$count} product(s).");
}
public function importPurchases(Request $request)
    {
        if (!$request->hasFile('file')) {
            return back()->with('error', 'Please select a Tally Excel/CSV file to upload.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new PurchasesImport, $request->file('file'));

            return back()->with('success', 'Purchase inventory imported successfully into purchases table!');
        } catch (ValidationException $e) {
            $errorMessages = [];
            foreach ($e->failures() as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return back()->with('error', "Validation failed during purchase import:\n" . implode("\n", $errorMessages));
        } catch (\Exception $e) {
            Log::error('Purchase Import Failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
}