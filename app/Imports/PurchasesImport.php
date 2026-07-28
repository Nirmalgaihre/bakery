<?php
namespace App\Imports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class PurchasesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['particulars']) && empty($row['item_name'])) {
            return null;
        }

        $itemName = trim($row['particulars'] ?? $row['item_name']);
        $totalAmount = !empty($row['debit']) ? (float) str_replace(',', '', $row['debit']) : 0.0;
        $qty = !empty($row['quantity']) ? (float) str_replace(',', '', $row['quantity']) : 1.0;

        // Standardize AD Date
        $purchaseDate = null;
        if (!empty($row['date'])) {
            try {
                $purchaseDate = Carbon::createFromFormat('d/m/Y', trim($row['date']))->format('Y-m-d');
            } catch (\Exception $e) {
                $purchaseDate = date('Y-m-d');
            }
        }

        // Process Nepali Date (Miti field)
        $nepaliDate = null;
        if (!empty($row['miti'])) {
            $rawMiti = trim($row['miti']);
            $parts = explode('/', $rawMiti);
            if (count($parts) === 3) {
                $month = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $day   = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                $year  = $parts[2];
                $nepaliDate = "{$year}-{$month}-{$day}";
            } else {
                $nepaliDate = $rawMiti;
            }
        }

        return new Purchase([
            'supplier_name'  => $itemName,
            'item_name'      => $itemName,
            'quantity'       => $qty,
            'unit'           => trim($row['unit'] ?? 'pcs'),
            'price_per_unit' => $qty > 0 ? ($totalAmount / $qty) : $totalAmount,
            'total_amount'   => $totalAmount,
            'purchase_date'  => $purchaseDate,
            'nepali_date'    => $nepaliDate,
            'notes'          => isset($row['vch_no']) ? 'Vch No: ' . $row['vch_no'] : null,
        ]);
    }
}