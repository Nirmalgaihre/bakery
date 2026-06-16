<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Cheque;
use App\Observers\ProductObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Observer to automate email alerts
        Product::observe(ProductObserver::class);

        // 2. View Composer for the UI Bell Icon
        View::composer('layouts.admin', function ($view) {
            $lowStockProducts = Product::where('initial_stock', '<=', 5)->get();
            
            $chequesDue = collect();
            if (Schema::hasTable('cheques') && Schema::hasColumn('cheques', 'due_date')) {
                $chequesDue = Cheque::whereDate('due_date', now()->toDateString())->get();
            }

            $totalCount = $lowStockProducts->count() + $chequesDue->count();

            $view->with([
                'notifications' => ['lowStock' => $lowStockProducts, 'cheques' => $chequesDue],
                'hasNotification' => $totalCount > 0,
                'notificationCount' => $totalCount
            ]);
        });
    }
}