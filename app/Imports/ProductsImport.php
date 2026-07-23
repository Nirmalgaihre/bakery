<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\SectorCategory;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading; // Added
use Maatwebsite\Excel\Concerns\WithBatchInserts; // Added

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithChunkReading, WithBatchInserts
{
    use Importable, SkipsErrors, SkipsFailures;

    public int $createdCount = 0;
    public int $updatedCount = 0;

    // Cache categories in memory for this batch run to avoid redundant SELECT queries
    private array $categoryCache = [];

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        $productName = trim($row['name']);

        // 1. Find existing product or start a new one
        $product = Product::firstOrNew(['name' => $productName]);
        $existed = $product->exists;

        // 2. Optimized Category Logic using local cache memory
        $categoryName = isset($row['category']) ? trim($row['category']) : 'Uncategorized';
        
        if (!isset($this->categoryCache[$categoryName])) {
            $category = SectorCategory::firstOrCreate(['name' => $categoryName]);
            $this->categoryCache[$categoryName] = $category->id;
        }
        $categoryId = $this->categoryCache[$categoryName];

        // 3. Fast item_code assignment without hitting a nested database loop
        if (!$existed && empty($product->item_code)) {
            $product->item_code = 'PROD-' . strtoupper(Str::random(5)) . '-' . rand(100, 999);
        }

        // 4. Map the data structure
        $product->category_id        = $categoryId;
        $product->purchase_cost      = $row['purchase_cost'] ?? $product->purchase_cost ?? 0;
        $product->selling_price      = $row['selling_price'] ?? $product->selling_price ?? 0;
        $product->inventory_unit     = $this->sanitizeUnit($row['inventory_unit'] ?? $product->inventory_unit ?? 'kg');
        $product->initial_stock      = $row['initial_stock'] ?? $product->initial_stock ?? 0;
        // For new products, if 'stock' or 'current_stock' is not provided,
        // default 'stock' to 'initial_stock' to match manual creation behavior.
        if (!$existed && !isset($row['current_stock']) && !isset($row['stock'])) {
            $product->stock = $product->initial_stock;
        } else {
            $product->stock = $row['current_stock'] ?? $row['stock'] ?? $product->stock ?? 0;
        }
        $product->alert_stock_level  = $row['alert_stock_level'] ?? $product->alert_stock_level ?? 0;
        $product->alert_sent         = $product->alert_sent ?? false;

        $existed ? $this->updatedCount++ : $this->createdCount++;

        // Note: Do NOT manually call $product->save() when using ToModel with Batch Inserts. 
        // Returning the model lets Maatwebsite handle database batching optimizations automatically.
        return $product;
    }

    /**
     * Set how many rows are safely sent to the database in one single query statement
     */
    public function batchSize(): int
    {
        return 250; 
    }

    /**
     * Set how many rows are read into server memory at any given time 
     */
    public function chunkSize(): int
    {
        return 250;
    }

    private function sanitizeUnit(string $unit): string
    {
        $unit = strtolower(trim($unit));
        $allowed = ['kg', 'paau', 'bottle', 'cartoon', 'boxes'];
        return in_array($unit, $allowed) ? $unit : 'kg';
    }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'purchase_cost'     => 'nullable|numeric|min:0',
            'selling_price'     => 'nullable|numeric|min:0',
            'initial_stock'     => 'nullable|numeric|min:0',
            'alert_stock_level' => 'nullable|integer|min:0',
        ];
    }
}