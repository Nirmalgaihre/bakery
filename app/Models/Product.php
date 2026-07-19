<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockAlert;
use App\Models\User; // <-- ADDED: Crucial namespace fix for the booted() method

class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'supplier_id',
        'purchase_cost',
        'selling_price',
        'inventory_unit',
        'initial_stock',
        'stock',
        'alert_stock_level',
        'alert_sent',
        'item_code',
        'color',
        'size',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function category()
    {
        return $this->belongsTo(SectorCategory::class, 'category_id');
    }

    public function transactions() 
    {
        return $this->hasMany(Transaction::class, 'product_id');
    }

    protected static function booted()
    {
        static::saved(function ($product) {
            // FIXED: Changed logic to track 'stock' instead of 'initial_stock'
            if ($product->stock <= $product->alert_stock_level && !$product->alert_sent) {
                $adminUsers = User::where('role', 'admin')->get();
                if ($adminUsers->isNotEmpty()) {
                    Notification::send($adminUsers, new LowStockAlert($product));
                }
                $product->updateQuietly(['alert_sent' => true]);
            } elseif ($product->stock > $product->alert_stock_level && $product->alert_sent) {
                $product->updateQuietly(['alert_sent' => false]);
            }
        });
    }

    public function checkAndSendAlert()
    {
        // FIXED: Track active 'stock' level for standard email routines
        if ($this->stock <= $this->alert_stock_level) {
            $cacheKey = 'low_stock_alert_' . $this->id;
            if (!Cache::has($cacheKey)) {
                $adminEmail = 'gaihrenirmal2021@gmail.com';
                $productName = $this->name;

                Mail::raw(
                    "CRITICAL STOCK ALERT!\n\nProduct: {$this->name}\nRemaining Stock: {$this->stock} {$this->inventory_unit}\nThreshold: {$this->alert_stock_level}",
                    function ($message) use ($adminEmail, $productName) {
                        $message->to($adminEmail)->subject('Low Stock Alert: ' . $productName);
                    }
                );
                Cache::put($cacheKey, true, now()->addHours(24));
            }
        }
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'product_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'item_name', 'name');
    }

    public function inventoryAdjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'product_id', 'id');
    }
}