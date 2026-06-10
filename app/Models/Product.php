<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'purchase_cost',
        'selling_price',
        'inventory_unit',
        'initial_stock',
        'alert_stock_level',
    ];

    /**
     * Relationship link to historical ledger transactions.
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\StockTransaction::class);
    }
}