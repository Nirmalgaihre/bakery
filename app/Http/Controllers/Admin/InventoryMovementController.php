<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryAdjustment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class InventoryMovementController extends Controller
{
    /**
     * Display the Dedicated Sales Log Dashboard
     */
    public function salesDashboard()
    {
        $sales = InventoryAdjustment::where('type', 'sell')
            ->with(['product', 'customer'])
            ->latest()
            ->paginate(15);

        return view('admin.sales.dashboard', compact('sales'));
    }

    /**
     * Show the dedicated Sales Processing form (POS).
     */
    public function createSale(Product $product = null)
    {
        $customers = Customer::orderBy('name', 'asc')->get();

        $products = null;
        if (!$product || !$product->exists) {
            $products = Product::where('initial_stock', '>', 0)->orderBy('name', 'asc')->get();
            $product = null;
        }

        $lastSale = InventoryAdjustment::where('type', 'sell')->latest('id')->first();
        $nextId = $lastSale ? ($lastSale->id + 1) : 1;
        $paddedInvoiceNumber = str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $currentEngDate = date('Y-m-d');
        $currentNepaliDate = LaravelNepaliDate::from($currentEngDate)->toNepaliDate();

        return view('admin.sales.sell', compact(
            'product', 
            'products', 
            'customers', 
            'paddedInvoiceNumber', 
            'currentNepaliDate'
        ));
    }

    /**
     * Show general operational adjustments form (Wastage, Damage, etc.)
     */
    public function create(Product $product)
    {
        return view('admin.inventory.adjust', compact('product'));
    }

    /**
     * Route gateway pointing explicitly to the multi-row checkout processor
     */
    public function storeSale(Request $request)
    {
        return $this->processMultiRowInvoice($request);
    }

    /**
     * Store and process structural changes inside the inventory logs matrix
     */
    public function store(Request $request, Product $product = null)
    {
        if ($request->has('items')) {
            return $this->processMultiRowInvoice($request);
        }

        $validated = $request->validate([
            'quantity'       => 'required|numeric|min:0.001',
            'type'           => 'required|string|in:sell,expired,damaged,returned_defective,internal_use,wastage',
            'reference_note' => 'nullable|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($product, $validated) {
                $quantity = $validated['quantity'];
                $type = $validated['type'];

                if (in_array($type, ['sell', 'expired', 'damaged', 'internal_use', 'wastage'])) {
                    if ($product->initial_stock < $quantity) {
                        throw new \Exception("Insufficient stock balances! Available: {$product->initial_stock}");
                    }
                    $product->initial_stock -= $quantity;
                    $costTrack = ($type === 'sell') ? $product->selling_price : $product->purchase_cost;
                } else {
                    $product->initial_stock += $quantity;
                    $costTrack = $product->selling_price;
                }

                InventoryAdjustment::create([
                    'product_id'     => $product->id,
                    'quantity'       => $quantity,
                    'type'           => $type,
                    'unit_cost'      => $costTrack,
                    'reference_note' => $validated['reference_note'],
                ]);

                $product->save();
            });

            if ($validated['type'] === 'sell') {
                return redirect()->route('admin.sales.dashboard')->with('success', 'Retail point-of-sale transaction recorded successfully!');
            }
            return redirect()->route('admin.products.index')->with('success', 'Inventory adjustment finalized!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Internal Helper method to handle and safely process multiple rows submitted from the POS interface
     */
    protected function processMultiRowInvoice(Request $request)
    {
        // Fixed Validation rules to match your clean KG and Gram structure fields
        $request->validate([
            'customer_id'            => 'required|exists:customers,id',
            'invoice_number'         => 'required|string',
            'invoice_date'           => 'required|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.price'          => 'required|numeric|min:0',
            'items.*.weight_kg'      => 'required|numeric|min:0',
            'items.*.weight_gram'    => 'nullable|numeric|min:0|max:999',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->input('items') as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    
                    // Core Metric Math calculation matching frontend formulas
                    $kgPart = floatval($item['weight_kg']);
                    $gramPart = isset($item['weight_gram']) ? (floatval($item['weight_gram']) / 1000) : 0;
                    
                    $totalCalculatedQuantity = $kgPart + $gramPart;

                    if ($totalCalculatedQuantity <= 0) {
                        throw new \Exception("The total quantity for item '{$product->name}' must be greater than zero.");
                    }

                    // Verify dynamic inventory safety thresholds
                    if ($product->initial_stock < $totalCalculatedQuantity) {
                        throw new \Exception("Insufficient stock for product: {$product->name}! Remaining: {$product->initial_stock} KG");
                    }

                    // Deduct the dynamic transactional batch stock metric balances
                    $product->initial_stock -= $totalCalculatedQuantity;
                    $product->save();

                    // Generate log records inside the Inventory Adjustment matrix tracking ledger row
                    InventoryAdjustment::create([
                        'product_id'     => $product->id,
                        'customer_id'    => $request->input('customer_id'),
                        'quantity'       => $totalCalculatedQuantity, // Saves combined KG decimal total directly
                        'type'           => 'sell',
                        'unit_cost'      => floatval($item['price']),
                        'reference_note' => 'Invoice Ref: #' . $request->input('invoice_number') . ' | Dated B.S: ' . $request->input('invoice_date'),
                    ]);
                }
            });

            return redirect()->route('admin.sales.dashboard')->with('success', 'POS Invoiced ledger transaction processed and committed successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}