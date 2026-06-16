<?php

namespace App\Observers;

use App\Models\Product;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Notification;

class ProductObserver
{
    public function updated(Product $product)
    {
        // Check if stock is <= 5 AND it was previously > 5 (prevents duplicate emails)
        if ($product->initial_stock <= 5 && $product->getOriginal('initial_stock') > 5) {
            
            // You can replace the email address with your config('mail.from.address')
            Notification::route('mail', 'manager@bakery.com')
                ->notify(new LowStockAlert($product));
        }
    }
}