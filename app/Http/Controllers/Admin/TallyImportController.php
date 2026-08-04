<?php

namespace App\Http\Controllers\Admin;

use App\Models\SalesImport;
use App\Models\SalesCustomer;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TallyImportController extends Controller
{
    public function importForm()
    {
        return view('admin.sales.tally.import-form');
    }

    public function import(Request $request)
    {
        $request->validate([
            'ledger_name' => 'required|string|max:255',
            'data' => 'required|string',
            'import_date_from' => 'required|date',
            'import_date_to' => 'required|date|after_or_equal:import_date_from',
        ]);

        DB::beginTransaction();

        try {
            $import = new SalesImport([
                'import_name' => $request->input('ledger_name'),
                'import_type' => 'Tally',
                'status' => 'Processing',
                'import_date' => now(),
                'created_by' => auth()->id(),
            ]);
            $import->save();

            $errorLogs = [];
            $successCount = 0;

            // Parse Tally data
            $lines = array_filter(array_map('trim', explode("\n", $request->input('data'))));
            $data = $this->parseTallyData($lines);

            // Get or create customer
            $customerName = $data['customer_name'] ?? $request->input('ledger_name');
            $customer = SalesCustomer::firstOrCreate(
                ['customer_code' => strtoupper(str_replace(' ', '_', substr($customerName, 0, 20)))],
                [
                    'customer_name' => $customerName,
                    'opening_balance' => 0,
                    'balance_type' => 'Debit',
                    'status' => 'Active',
                    'created_by' => auth()->id(),
                ]
            );

            // Create invoice
            $invoice = SalesInvoice::create([
                'invoice_number' => 'IMP-' . now()->format('YmdHis'),
                'invoice_date' => now(),
                'customer_id' => $customer->id,
                'date_from' => $request->input('import_date_from'),
                'date_to' => $request->input('import_date_to'),
                'subtotal' => 0,
                'status' => 'Draft',
                'import_reference' => 'Tally-' . $import->id,
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0;

            // Process invoice items
            foreach ($data['items'] ?? [] as $itemData) {
                try {
                    $item = SalesItem::where('item_code', strtoupper(substr($itemData['name'], 0, 50)))
                        ->first();

                    if (!$item) {
                        $item = SalesItem::create([
                            'item_name' => $itemData['name'],
                            'item_code' => strtoupper(substr($itemData['name'], 0, 50)),
                            'unit' => $itemData['unit'] ?? 'pcs',
                            'opening_price' => $itemData['rate'] ?? 0,
                            'current_price' => $itemData['rate'] ?? 0,
                            'status' => 'Active',
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $lineAmount = (float)$itemData['quantity'] * (float)$itemData['rate'];

                    SalesInvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_id' => $item->id,
                        'item_name' => $itemData['name'],
                        'item_code' => $item->item_code,
                        'quantity' => $itemData['quantity'],
                        'unit' => $itemData['unit'] ?? 'pcs',
                        'rate' => $itemData['rate'],
                        'line_amount' => $lineAmount,
                        'discount' => $itemData['discount'] ?? 0,
                        'net_amount' => $lineAmount - ($itemData['discount'] ?? 0),
                    ]);

                    $subtotal += $lineAmount - ($itemData['discount'] ?? 0);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorLogs[] = "Item: {$itemData['name']} - {$e->getMessage()}";
                }
            }

            // Update invoice totals
            $invoice->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'outstanding_amount' => $subtotal,
            ]);

            $import->update([
                'successfully_imported' => $successCount,
                'failed_records' => count($errorLogs),
                'error_logs' => $errorLogs,
                'status' => 'Completed',
                'total_records' => count($data['items'] ?? []),
            ]);

            DB::commit();

            return redirect()->route('admin.sales.imports.index')
                ->with('success', "Tally data imported successfully! Invoice: {$invoice->invoice_number}");

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($import)) {
                $import->update([
                    'status' => 'Failed',
                    'error_logs' => [$e->getMessage()],
                ]);
            }

            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function parseTallyData($lines)
    {
        $data = [
            'customer_name' => '',
            'items' => []
        ];

        foreach ($lines as $line) {
            // Skip header rows and empty lines
            if (empty($line) || strpos($line, 'Miti') === 0 || strpos($line, 'Date') === 0 || strpos($line, 'Particulars') === 0) {
                continue;
            }

            // Extract customer name (usually after "By")
            if (strpos($line, 'By') !== false && empty($data['customer_name'])) {
                $parts = explode('By', $line);
                if (isset($parts[1])) {
                    $data['customer_name'] = trim(str_replace(['Sales', 'Vch Type', 'Vch No.', 'Debit', 'Credit'], '', $parts[1]));
                }
                continue;
            }

            // Parse item lines (containing quantity and rate)
            if (preg_match('/^\s*(.+?)\s+(\d+(?:\.\d+)?\s*(?:kg|pcs|roll|g|ml|ltr|bag))\s+(\d+(?:\.\d+)?)\s*\/\s*\w+\s+(\d+(?:\.\d+)?(?:,\d{3})*)\s*$/', $line, $matches)) {
                $itemName = trim($matches[1]);
                $unit = trim($matches[2]);
                $rate = (float) str_replace(',', '', $matches[3]);
                $amount = (float) str_replace(',', '', $matches[4]);
                $quantity = $amount / $rate;

                // Extract unit type
                $unitType = 'pcs';
                if (preg_match('/(kg|g|ml|ltr|bag|roll)/', $unit, $unitMatch)) {
                    $unitType = $unitMatch[1];
                }

                $data['items'][] = [
                    'name' => $itemName,
                    'quantity' => $quantity,
                    'unit' => $unitType,
                    'rate' => $rate,
                    'discount' => 0,
                ];
            }

            // Parse discount lines
            if (preg_match('/Discount\s*(-?\d+(?:\.\d+)?(?:,\d{3})*)/', $line, $matches)) {
                if (!empty($data['items'])) {
                    $lastItem = &$data['items'][count($data['items']) - 1];
                    $lastItem['discount'] = (float) str_replace(',', '', $matches[1]);
                }
            }
        }

        return $data;
    }
}
