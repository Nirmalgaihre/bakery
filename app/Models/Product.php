<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockAlert;

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
        'alert_sent',
    ];

    public function transactions()
    {
        return $this->hasMany(\App\Models\StockTransaction::class);
    }

    protected static function booted()
    {
        static::saved(function ($product) {
            if ($product->initial_stock <= $product->alert_stock_level && !$product->alert_sent) {

                $adminUsers = User::where('role', 'admin')->get();

                if ($adminUsers->isNotEmpty()) {
                    Notification::send($adminUsers, new LowStockAlert($product));
                }

                $product->updateQuietly(['alert_sent' => true]);

            } elseif ($product->initial_stock > $product->alert_stock_level && $product->alert_sent) {

                $product->updateQuietly(['alert_sent' => false]);
            }
        });
    }

    public function checkAndSendAlert()
    {
        if ($this->initial_stock <= $this->alert_stock_level) {

            $cacheKey = 'low_stock_alert_' . $this->id;

            if (!Cache::has($cacheKey)) {
                $adminEmail = 'gaihrenirmal2021@gmail.com';
                $productName = $this->name;

                Mail::raw(
                    "CRITICAL STOCK ALERT!\n\nProduct: {$this->name}\nRemaining Stock: {$this->initial_stock} {$this->inventory_unit}\nThreshold: {$this->alert_stock_level}",
                    function ($message) use ($adminEmail, $productName) {
                        $message->to($adminEmail)->subject('Low Stock Alert: ' . $productName);
                    }
                );

                Cache::put($cacheKey, true, now()->addHours(24));
            }
        }
    }
}