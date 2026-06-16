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
    protected static function booted()
{
    static::saved(function ($product) {
        if ($product->initial_stock <= $product->alert_stock_level && !$product->alert_sent) {
            
            // Fetch all users who are admins
            // Assuming you have a 'role' column or similar way to identify admins
            $adminEmails = User::where('role', 'admin')->pluck('email');

            if ($adminEmails->isNotEmpty()) {
                Mail::to($adminEmails)->send(new LowStockAlert($product));
            }
            
            $product->updateQuietly(['alert_sent' => true]);
            
        } elseif ($product->initial_stock > $product->alert_stock_level && $product->alert_sent) {
            
            $product->updateQuietly(['alert_sent' => false]);
        }
    });
}
}