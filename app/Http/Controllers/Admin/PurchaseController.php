<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index()
    {
        // Eager load the supplier relation structure to avoid N+1 query slow-downs
        $purchases = Purchase::with('supplier')->latest()->paginate(20);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items'       => 'required|array',
            'items.*.id'  => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.cost'=> 'required|numeric|min:0'
        ]);

        $supplier = Supplier::findOrFail($validated['supplier_id']);

        DB::transaction(function () use ($validated, $supplier) {
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                
                // 1. Maintain active calculations across product metrics
                $product->increment('stock', $item['qty']);
                $product->increment('initial_stock', $item['qty']);
                
                // 2. Generate detailed tracking entries for your flat records table
                Purchase::create([
                    'supplier_id'    => $supplier->id,
                    'supplier_name'  => $supplier->name,
                    'item_name'      => $product->name,
                    'quantity'       => $item['qty'],
                    'unit'           => $product->inventory_unit,
                    'price_per_unit' => $item['cost'],
                    'total_amount'   => $item['qty'] * $item['cost'],
                    'purchase_date'  => now()->toDateString(),
                    'notes'          => 'Stock replenishment auto-logged.',
                ]);
            }
        });

        return back()->with('success', 'Stock replenished successfully.');
    }

    /**
     * Import Tally Purchase Excel File (.xls / .xlsx)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Read rows from the uploaded Excel file
                $rows = Excel::toArray([], $request->file('excel_file'))[0] ?? [];

                if (empty($rows)) {
                    throw new \Exception("The uploaded file is empty.");
                }

                // Locate column header index dynamically or fallback to default Tally export structure
                $headers = array_map(function($h) {
                    return strtolower(trim((string)$h));
                }, $rows[0]);

                $mitiIdx       = array_search('miti', $headers);
                $dateIdx       = array_search('date', $headers);
                $particularIdx = array_search('particulars', $headers);
                $vchNoIdx      = array_search('vch no.', $headers);
                $debitIdx      = array_search('debit', $headers);

                // Fallbacks if exact headers are not matched
                $mitiIdx       = ($mitiIdx !== false) ? $mitiIdx : 0;
                $dateIdx       = ($dateIdx !== false) ? $dateIdx : 1;
                $particularIdx = ($particularIdx !== false) ? $particularIdx : 2;
                $vchNoIdx      = ($vchNoIdx !== false) ? $vchNoIdx : 4;
                $debitIdx      = ($debitIdx !== false) ? $debitIdx : 5;

                // Process rows starting after header (index 1)
                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];

                    if (!isset($row[$particularIdx])) {
                        continue;
                    }

                    // 1. Clean up supplier name (removes "To " prefix from Tally)
                    $rawParticular = trim((string)$row[$particularIdx]);
                    $supplierName  = trim(preg_replace('/^To\s+/i', '', $rawParticular));

                    // Parse Debit amount
                    $rawDebit = str_replace(',', '', (string)($row[$debitIdx] ?? '0'));
                    $debitAmount = (float)$rawDebit;

                    // Skip empty rows or zero amounts
                    if (empty($supplierName) || $debitAmount <= 0) {
                        continue;
                    }

                    // 2. Find existing supplier or create a new one
                    $supplier = Supplier::firstOrCreate(
                        ['name' => $supplierName]
                    );

                    // 3. Format dates
                    $nepaliDate  = trim((string)($row[$mitiIdx] ?? ''));
                    $adDateRaw   = trim((string)($row[$dateIdx] ?? ''));
                    $purchaseDate = null;

                    if (!empty($adDateRaw)) {
                        try {
                            $purchaseDate = Carbon::createFromFormat('d/m/Y', $adDateRaw)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $purchaseDate = date('Y-m-d', strtotime($adDateRaw));
                        }
                    }

                    $vchNo = trim((string)($row[$vchNoIdx] ?? ''));

                    // 4. Create Purchase record
                    Purchase::create([
                        'supplier_id'    => $supplier->id,
                        'supplier_name'  => $supplier->name,
                        'item_name'      => 'General Purchase', // Default name for Tally ledger imports
                        'quantity'       => 1,
                        'unit'           => 'pcs',
                        'price_per_unit' => $debitAmount,
                        'total_amount'   => $debitAmount,
                        'purchase_date'  => $purchaseDate ?? now()->toDateString(),
                        'nepali_date'    => $nepaliDate ?: null,
                        'notes'          => 'Tally Import | Vch No: ' . $vchNo,
                    ]);
                }
            });

            return back()->with('success', 'Tally Purchase Excel imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import Excel: ' . $e->getMessage());
        }
    }
}