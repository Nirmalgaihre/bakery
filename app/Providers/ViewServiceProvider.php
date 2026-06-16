<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\Cheque; // Ensure this model exists

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // This will now pass $hasNotification to your layout.admin view
        View::composer('layouts.admin', function ($view) {
            $hasLowStock = Product::where('initial_stock', '<=', 5)->exists();
            
            // Check if Cheque table exists, otherwise handle gracefully
            $hasChequeDue = false;
            if (class_exists(Cheque::class)) {
                $hasChequeDue = Cheque::whereDate('due_date', now()->toDateString())->exists();
            }

            $view->with('hasNotification', $hasLowStock || $hasChequeDue);
        });
    }
}