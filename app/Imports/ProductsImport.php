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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithChunkReading, WithBatchInserts
{
    use Importable, SkipsErrors, SkipsFailures;

    public int $createdCount = 0;
    public int $updatedCount = 0;

    public array $customIssues = [];

    private array $categoryCache = [];
    private array $usedItemCodes = [];
    private array $seenNames = [];

    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        $productName = trim($row['name']);
        $nameKey = mb_strtolower($productName);

        if (isset($this->seenNames[$nameKey])) {
            $this->customIssues[] = "Duplicate product \"{$productName}\" appears more than once in this file — only the first occurrence was used.";
            return null;
        }
        $this->seenNames[$nameKey] = true;

        $product = Product::where('name', $productName)->first();
        $existed = (bool) $product;

        if (!$existed) {
            $product = new Product(['name' => $productName]);
        }

        // category_id can be a string PK (e.g. "cat-baking-agents-additives")
        // or a numeric PK depending on your SectorCategory model — support both.
        $categoryId = null;

        if (!empty($row['category_id'])) {
            $categoryId = $row['category_id'];
        } elseif (!empty($row['category'])) {
            $categoryName = trim($row['category']);
            if (!isset($this->categoryCache[$categoryName])) {
                $category = SectorCategory::firstOrCreate(['name' => $categoryName]);
                $this->categoryCache[$categoryName] = $category->id;
            }
            $categoryId = $this->categoryCache[$categoryName];
        }

        if ($categoryId === null && !$existed) {
            $this->customIssues[] = "Row for \"{$productName}\": no valid category_id/category given — skipped.";
            return null;
        }

        if (!$existed) {
            $product->item_code = $this->generateItemCode($productName, $categoryId);
        }

        $product->category_id        = $categoryId ?? $product->category_id;
        $product->purchase_cost      = $row['purchase_cost'] ?? $product->purchase_cost ?? 0;
        $product->selling_price      = $row['selling_price'] ?? $product->selling_price ?? 0;
        $product->inventory_unit     = $this->sanitizeUnit($row['inventory_unit'] ?? $product->inventory_unit ?? 'kg');
        $product->initial_stock      = $row['initial_stock'] ?? $product->initial_stock ?? 0;

        if (!$existed && !isset($row['current_stock']) && !isset($row['stock'])) {
            $product->stock = $product->initial_stock;
        } else {
            $product->stock = $row['current_stock'] ?? $row['stock'] ?? $product->stock ?? 0;
        }

        $product->alert_stock_level  = $row['alert_stock_level'] ?? $product->alert_stock_level ?? 0;
        $product->alert_sent         = $product->alert_sent ?? false;

        if ($existed) {
            try {
                $product->save();
                $this->updatedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                $this->customIssues[] = $this->friendlyDbError($productName, $e);
            }
            return null;
        }

        $this->createdCount++;

        return $product;
    }

    public function batchSize(): int
    {
        return 250;
    }

    public function chunkSize(): int
    {
        return 250;
    }

    private function sanitizeUnit(string $unit): string
    {
        $unit = strtolower(trim($unit));
        $allowed = ['pcs', 'kg', 'paau', 'bottle', 'cartoon', 'boxes'];
        return in_array($unit, $allowed) ? $unit : 'kg';
    }

    private function generateItemCode(string $name, string|int|null $categoryId): string
    {
        $prefix = $this->buildPrefix($name, $categoryId);

        do {
            $code = $prefix . '-' . strtoupper(Str::random(6));
        } while (
            isset($this->usedItemCodes[$code])
            || Product::where('item_code', $code)->exists()
        );

        $this->usedItemCodes[$code] = true;

        return $code;
    }

    private function buildPrefix(string $name, string|int|null $categoryId): string
    {
        if ($categoryId) {
            $categoryName = array_search($categoryId, $this->categoryCache, true);
            if ($categoryName) {
                $prefix = strtoupper(Str::slug(Str::limit($categoryName, 12, ''), ''));
                if ($prefix !== '') {
                    return $prefix;
                }
            }
        }

        $prefix = strtoupper(Str::slug(Str::limit($name, 12, ''), ''));

        return $prefix !== '' ? $prefix : 'PRD';
    }

    private function friendlyDbError(string $productName, \Illuminate\Database\QueryException $e): string
    {
        $code = $e->errorInfo[1] ?? null;

        if ($code == 1062) {
            return "\"{$productName}\": a duplicate entry already exists and could not be saved.";
        }

        if ($code == 1452) {
            return "\"{$productName}\": references a category that doesn't exist.";
        }

        return "\"{$productName}\": could not be saved due to a database error.";
    }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'category_id'       => 'nullable|string|exists:sector_categories,id',
            'purchase_cost'     => 'nullable|numeric|min:0',
            'selling_price'     => 'nullable|numeric|min:0',
            'initial_stock'     => 'nullable|numeric|min:0',
            'alert_stock_level' => 'nullable|integer|min:0',
        ];
    }
}