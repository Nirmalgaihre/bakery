<?php

namespace App\Imports;

use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class PurchasesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * Map each Excel row into Supplier and Purchase models.
     */
    public function model(array $row)
    {
        // 1. Extract and clean the supplier name from "Particulars" (e.g. "To SG NEPAL PVT.LTD." -> "SG NEPAL PVT.LTD.")
        $rawParticulars = trim($row['particulars'] ?? '');
        
        if (empty($rawParticulars)) {
            return null; // Skip empty rows
        }

        $supplierName = preg_replace('/^To\s+/i', '', $rawParticulars);

        // 2. Debit column represents the total purchase amount
        $debitAmount = floatval($row['debit'] ?? 0);
        
        // Skip rows where purchase debit is 0 or negative
        if ($debitAmount <= 0) {
            return null;
        }

        // 3. Find or Create Supplier in `suppliers` table
        $supplier = Supplier::firstOrCreate(
            ['name' => $supplierName],
            [
                'contact_person' => null,
                'email'          => null,
                'phone'          => null,
                'address'        => null,
            ]
        );

        // 4. Format Purchase Date
        $purchaseDate = $this->transformDate($row['date'] ?? null);
        $nepaliDate   = $row['miti'] ?? null;

        // 5. Create Purchase record in `purchases` table
        return new Purchase([
            'supplier_id'      => $supplier->id,
            'supplier_name'    => $supplier->name,
            'item_name'        => 'Tally Import Purchase (Vch #' . ($row['vch_no'] ?? 'N/A') . ')',
            'quantity'         => 1,
            'unit'             => 'lot',
            'price_per_unit'   => $debitAmount,
            'total_amount'     => $debitAmount,
            'purchase_date'    => $purchaseDate,
            'nepali_date'      => $nepaliDate,
            'notes'            => 'Imported from Tally. Vch Type: ' . ($row['vch_type'] ?? 'Purchase') . ', Vch No: ' . ($row['vch_no'] ?? 'N/A'),
        ]);
    }

    /**
     * Validation rules for each row of the sheet.
     */
    public function rules(): array
    {
        return [
            'particulars' => 'required',
            'debit'       => 'nullable|numeric',
        ];
    }

    /**
     * Helper to transform Excel date into standard Y-m-d format.
     */
    private function transformDate($value)
    {
        if (empty($value)) {
            return now()->format('Y-m-d');
        }

        try {
            // Handle Excel numeric timestamp
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Handle DD/MM/YYYY
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', trim($value))) {
                return Carbon::createFromFormat('d/m/Y', trim($value))->format('Y-m-d');
            }

            // Fallback parsing for YYYY-MM-DD or standard formats
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}