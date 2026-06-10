<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $table = 'stock_transactions';

    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'old_purchase_cost',
        'new_purchase_cost',
        'old_selling_price',
        'new_selling_price',
        'reference_note'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}