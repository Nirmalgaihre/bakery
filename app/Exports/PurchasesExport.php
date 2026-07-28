<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Purchase::orderBy('purchase_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Supplier Name',
            'Item Name',
            'Quantity',
            'Unit',
            'Price Per Unit (Rs.)',
            'Total Amount (Rs.)',
            'Purchase Date',
            'Notes',
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->id,
            $purchase->supplier_name,
            $purchase->item_name,
            $purchase->quantity,
            $purchase->unit,
            number_format((float) $purchase->price_per_unit, 2, '.', ''),
            number_format((float) $purchase->total_amount, 2, '.', ''),
            optional($purchase->purchase_date)->format('Y-m-d') ?? $purchase->purchase_date,
            $purchase->notes,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}