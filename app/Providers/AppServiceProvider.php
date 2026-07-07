<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate; // <--- YOU NEED THIS IMPORT
use App\Models\Product;
use App\Models\Cheque;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $lowStockProducts = Product::where('initial_stock', '<=', 5)->get();

            $chequesDue = collect();
            if (Schema::hasTable('cheques') && Schema::hasColumn('cheques', 'due_date')) {
                $chequesDue = Cheque::whereDate('due_date', now()->toDateString())->get();
            }

            $totalCount = $lowStockProducts->count() + $chequesDue->count();

            $view->with([
                'notifications'     => ['lowStock' => $lowStockProducts, 'cheques' => $chequesDue],
                'hasNotification'   => $totalCount > 0,
                'notificationCount' => $totalCount
            ]);
        });
        // 2. The Permission Gate (INSIDE the boot method)
        Gate::define('manage_categories', function ($user) {
            // This is the "Security Guard" rule
            return $user->role === 'admin'; 
        });
        
    }
}