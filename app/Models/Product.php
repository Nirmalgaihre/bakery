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
    public function checkAndSendAlert()
    {
        // 1. Check if stock is low
        if ($this->initial_stock <= $this->alert_stock_level) {
            
            $cacheKey = 'low_stock_alert_' . $this->id;

            // 2. Prevent spam (24-hour cooldown)
            if (!Cache::has($cacheKey)) {
                $adminEmail = 'gaihrenirmal2021@gmail.com';
                
                Mail::raw("CRITICAL STOCK ALERT!\n\nProduct: {$this->name}\nRemaining Stock: {$this->initial_stock} {$this->inventory_unit}\nThreshold: {$this->alert_stock_level}", 
                function ($message) use ($adminEmail) {
                    $message->to($adminEmail)->subject('Low Stock Alert: ' . $this->name);
                });

                Cache::put($cacheKey, true, now()->addHours(24));
            }
        }
    }
}