<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryAdjustment; 
use Illuminate\Support\Facades\DB;

class WastageController extends Controller
{
    /**
     * Display a listing of the inventory adjustments history.
     */
    public function index()
    {
        $adjustments = InventoryAdjustment::with('product')
            ->latest()
            ->paginate(15);

        return view('admin.wastage.index', compact('adjustments'));
    }

    /**
     * Display the dynamic entry matrix form with exact real-time stock limits.
     */
    public function create()
    {
        $products = Product::select('id', 'name', 'selling_price', 'initial_stock', 'stock', 'inventory_unit')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.wastage.create', compact('products'));
    }

    /**
     * Commit adjustment line entries to database records.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.type' => 'required|in:expired,damaged,returned_defective,internal_use,wastage',
            'items.*.rate_per_kg' => 'required|numeric',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    
                    $product = Product::findOrFail($item['product_id']);
                    
                    $totalQuantity = 0;
                    if ($product->inventory_unit === 'kg') {
                        $totalQuantity = floatval($item['quantity_kg'] ?? 0) + (floatval($item['quantity_gm'] ?? 0) / 1000);
                    } else {
                        $totalQuantity = floatval($item['quantity_kg'] ?? 0);
                    }

                    if ($totalQuantity <= 0) {
                        continue;
                    }

                    $currentAvailable = ($product->stock && $product->stock > 0) ? $product->stock : $product->initial_stock;
                    
                    if ($item['type'] !== 'returned_defective') {
                        if ($totalQuantity > $currentAvailable) {
                            $unitUpper = strtoupper($product->inventory_unit ?? 'units');
                            throw new \Exception("Cannot deduct {$totalQuantity} {$unitUpper} from '{$product->name}'. Only {$currentAvailable} {$unitUpper} available.");
                        }
                    }

                    InventoryAdjustment::create([
                        'product_id'     => $item['product_id'],
                        'quantity'       => $totalQuantity,
                        'type'           => $item['type'],
                        'unit_cost'      => $item['rate_per_kg'],
                        'reference_note' => $item['reference_note'] ?? $request->master_remarks,
                    ]);

                    if (! $product->stock || $product->stock == 0) {
                        $product->stock = $product->initial_stock;
                    }

                    if ($item['type'] === 'returned_defective') {
                        $product->increment('stock', $totalQuantity);
                    } else {
                        $product->decrement('stock', $totalQuantity);
                    }
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['stock_error' => $e->getMessage()]);
        }

        return redirect()->route('admin.wastage.index')->with('success', 'Adjustments recorded successfully.');
    }
}