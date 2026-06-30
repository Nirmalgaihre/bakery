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

    /**
     * WithHeadingRow turns your file's header "Purchase Cost" into the
     * array key 'purchase_cost', "Alert Stock Level" into 'alert_stock_level', etc.
     */
    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        // Upsert by name since there's no SKU column in this schema.
        // If you'd rather always insert new rows, replace this block with:
        //   $product = new Product(); $existed = false;
        $product = Product::firstOrNew(['name' => $row['name']]);
        $existed = $product->exists;

        $product->name               = $row['name'];
        $product->category           = $row['category'] ?? $product->category;
        $product->purchase_cost      = $row['purchase_cost'] ?? $product->purchase_cost ?? 0;
        $product->selling_price      = $row['selling_price'] ?? $product->selling_price ?? 0;
        $product->inventory_unit     = $row['inventory_unit'] ?? $product->inventory_unit;
        $product->initial_stock      = $row['initial_stock'] ?? $product->initial_stock ?? 0;
        $product->stock              = $row['current_stock'] ?? $row['stock'] ?? $product->stock ?? 0;
        $product->alert_stock_level  = $row['alert_stock_level'] ?? $product->alert_stock_level ?? 0;

        $product->save();

        $existed ? $this->updatedCount++ : $this->createdCount++;

        return null;
    }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'purchase_cost'     => 'nullable|numeric|min:0',
            'selling_price'     => 'nullable|numeric|min:0',
            'initial_stock'     => 'nullable|integer|min:0',
            'alert_stock_level' => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The Name column is required on every row.',
        ];
    }
}
