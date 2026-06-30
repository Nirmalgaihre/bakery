<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Product::orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Category',
            'Purchase Cost',
            'Selling Price',
            'Inventory Unit',
            'Initial Stock',
            'Current Stock',
            'Alert Stock Level',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->category,
            $product->purchase_cost ?? 0,
            $product->selling_price ?? 0,
            $product->inventory_unit,
            $product->initial_stock ?? 0,
            $product->stock ?? 0,
            $product->alert_stock_level ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
