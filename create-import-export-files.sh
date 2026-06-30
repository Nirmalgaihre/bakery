#!/bin/bash
set -e
PROJECT_ROOT="$(pwd)"
echo "Creating files inside: $PROJECT_ROOT"
mkdir -p app/Exports app/Imports resources/views/admin/products

cat > app/Exports/ProductsExport.php << 'INNEREOF'
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
        return ['ID', 'Name', 'SKU', 'Category', 'Price', 'Initial Stock', 'Created At'];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku ?? '',
            $product->category ?? '',
            $product->price ?? 0,
            $product->initial_stock ?? 0,
            optional($product->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
INNEREOF
echo "  created app/Exports/ProductsExport.php"

cat > app/Imports/ProductsImport.php << 'INNEREOF'
<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    public int $createdCount = 0;
    public int $updatedCount = 0;

    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        if (!empty($row['sku'])) {
            $product = Product::firstOrNew(['sku' => $row['sku']]);
            $existed = $product->exists;
        } else {
            $product = new Product();
            $existed = false;
        }

        $product->name           = $row['name'];
        $product->sku            = $row['sku'] ?? $product->sku;
        $product->category       = $row['category'] ?? null;
        $product->price          = $row['price'] ?? 0;
        $product->initial_stock  = $row['initial_stock'] ?? 0;

        $product->save();

        $existed ? $this->updatedCount++ : $this->createdCount++;

        return null;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'initial_stock' => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The Name column is required on every row.',
            'price.numeric' => 'Price must be a number.',
        ];
    }
}
INNEREOF
echo "  created app/Imports/ProductsImport.php"

cat > resources/views/admin/products/import.blade.php << 'INNEREOF'
@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Import Products</h1>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded whitespace-pre-line">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.products.import.template') }}" class="text-blue-600 underline">
            Download blank template (.xlsx)
        </a>
    </div>

    <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block mb-1 font-medium">File (.xlsx, .xls, or .csv)</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full border rounded p-2">
            @error('file')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <p class="text-sm text-gray-500">
            Expected columns: <code>name, sku, category, price, initial_stock</code>.
            If a row's SKU matches an existing product, that product will be updated instead of duplicated.
        </p>
        <button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded hover:bg-amber-700">
            Upload &amp; Import
        </button>
    </form>
</div>
@endsection
INNEREOF
echo "  created resources/views/admin/products/import.blade.php"

echo ""
echo "Done. Remaining manual steps:"
echo "  1. composer require maatwebsite/excel"
echo "  2. Add export/import controller methods to ProductController"
echo "  3. Add routes to routes/web.php"
echo "  4. Add buttons to products index blade view"
