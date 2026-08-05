<?php

namespace App\Imports;

use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class PurchasesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // 1. Filter: Only import rows where Voucher Type is Purchase
        $vchType = trim($row['vch_type'] ?? '');
        if (!empty($vchType) && stripos($vchType, 'purchase') === false) {
            return null;
        }

        // 2. Particulars is Supplier Name
        $supplierName = trim($row['particulars'] ?? $row['supplier_name'] ?? '');
        $supplierName = preg_replace('/\s+/', ' ', $supplierName);

        if (empty($supplierName)) {
            return null;
        }

        // 3. Debit (Purchase Cost) Amount
        $debitAmount = floatval($row['debit'] ?? $row['amount'] ?? 0);
        if ($debitAmount <= 0) {
            return null;
        }

        // 4. Find or Create Supplier
        $supplier = Supplier::firstOrCreate(
            ['name' => $supplierName]
        );

        // 5. Item Name (Sets NULL if empty)
        $itemName = !empty($row['item_name']) ? trim($row['item_name']) : null;

        // 6. Format Dates
        $purchaseDate = $this->transformDate($row['date'] ?? null);
        $nepaliDate   = $this->formatNepaliDateString($row['miti'] ?? $row['nepali_date'] ?? null, $purchaseDate);

        // 7. Save Purchase Record
        return new Purchase([
            'supplier_id'    => $supplier->id,
            'supplier_name'  => $supplier->name,
            'item_name'      => $itemName,                         // Now stores NULL
            'quantity'       => floatval($row['quantity'] ?? 1.00),
            'unit'           => trim($row['unit'] ?? 'lot'),
            'price_per_unit' => $debitAmount,
            'total_amount'   => $debitAmount,
            'purchase_date'  => $purchaseDate,
            'nepali_date'    => $nepaliDate,                       // Converted to YYYY-MM-DD
            'notes'          => 'Imported from Tally. Vch No: ' . ($row['vch_no'] ?? 'N/A'),
        ]);
    }

    public function rules(): array
    {
        return [
            'particulars' => 'required',
            'debit'       => 'nullable|numeric',
        ];
    }

    /**
     * Converts Nepali date strings like 4/1/2082 or Excel serial numbers into YYYY-MM-DD (e.g. 2082-04-01).
     */
    private function formatNepaliDateString($value, $fallbackGregorianDate = null)
    {
        if (empty($value)) {
            return $fallbackGregorianDate 
                ? LaravelNepaliDate::from($fallbackGregorianDate)->toNepaliDate(format: 'Y-m-d')
                : null;
        }

        $cleanValue = trim((string) $value);

        // Check if string is already YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $cleanValue)) {
            return $cleanValue;
        }

        // Parse M/D/YYYY or MM/DD/YYYY format (e.g., 4/1/2082 -> 2082-04-01)
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $cleanValue, $matches)) {
            $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $day   = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year  = $matches[3];

            return "{$year}-{$month}-{$day}";
        }

        // Parse YYYY/M/D or YYYY/MM/DD format (e.g., 2082/4/1 -> 2082-04-01)
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $cleanValue, $matches)) {
            $year  = $matches[1];
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $day   = str_pad($matches[3], 2, '0', STR_PAD_LEFT);

            return "{$year}-{$month}-{$day}";
        }

        // If Excel stored it as a numeric timestamp (e.g. 66567)
        if (is_numeric($cleanValue)) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$cleanValue);
                return LaravelNepaliDate::from($dt->format('Y-m-d'))->toNepaliDate(format: 'Y-m-d');
            } catch (\Exception $e) {
                if ($fallbackGregorianDate) {
                    return LaravelNepaliDate::from($fallbackGregorianDate)->toNepaliDate(format: 'Y-m-d');
                }
            }
        }

        return $cleanValue;
    }

    /**
     * Helper to transform Excel Gregorian date into standard Y-m-d format.
     */
    private function transformDate($value)
    {
        if (empty($value)) {
            return now()->format('Y-m-d');
        }

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', trim($value))) {
                return Carbon::createFromFormat('d/m/Y', trim($value))->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}