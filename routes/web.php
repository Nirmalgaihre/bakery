<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SectorCategoryController;
use App\Http\Controllers\Admin\CustomerController; 
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\InventoryMovementController;
// Aliased to avoid conflicts with public configurations
use App\Http\Controllers\Admin\ProductController as AdminProductController; 

/*
|--------------------------------------------------------------------------
| Public & Universal Open Routes
|--------------------------------------------------------------------------
*/
// Smart root route: Sends logged-in users straight to the Admin Dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard'); 
    }
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Guest Authentication Pipeline (Unauthenticated Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Phase 1: Basic Password Verification
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Phase 2: Local OTP Verification Screen
    Route::get('/login/verify-token', [LoginController::class, 'showOtpForm'])->name('otp.view');
    Route::post('/login/verify-token', [LoginController::class, 'verifyOtp'])->name('otp.verify');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Requires Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Secure Terminate Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Strictly Enforced Admin Room Protection (Verified Users Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['verified'])->prefix('admin')->name('admin.')->group(function () {
        
        // Admin Core Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Sector Categories Component Management Architecture
        Route::get('/categories', [SectorCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [SectorCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [SectorCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [SectorCategoryController::class, 'update'])->name('categories.update');
        
        // Customer Management Registry Endpoints (Handled via Resource Controller Matrix)
        Route::resource('customers', CustomerController::class);

        // SECTION 1: Product Master Configuration Node (Resource Controller Matrix)
        Route::resource('products', AdminProductController::class);

        // SECTION 2: Isolated Inventory Management & Logistics Node
        Route::prefix('inventory')->name('inventory.')->group(function () {
            // Standard Stock Intake Pipeline (Named: admin.inventory.create / store)
            Route::get('/add-stock/{product}', [StockController::class, 'create'])->name('create');
            Route::post('/add-stock/{product}', [StockController::class, 'store'])->name('store');
            
            // General Backend Operational Adjustments (Named: admin.inventory.adjust.create / store)
            Route::get('/adjust/{product}', [InventoryMovementController::class, 'create'])->name('adjust.create');
            Route::post('/adjust/{product}', [InventoryMovementController::class, 'store'])->name('adjust.store');
        });

        // SECTION 3: Dedicated POS Billing & Sales Tracking Subsystem Modules
        Route::prefix('sales')->name('sales.')->group(function () {
            // Core POS Terminal Interfaces
            Route::get('/pos/{product?}', [InventoryMovementController::class, 'createSale'])->name('create');
            Route::post('/pos', [InventoryMovementController::class, 'storeSale'])->name('store');
            
            // Historical Audit Logs & Analytical Dashboards
            Route::get('/logs', [InventoryMovementController::class, 'salesIndex'])->name('index');
            Route::get('/dashboard', [InventoryMovementController::class, 'salesDashboard'])->name('dashboard');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Fallback Verification Notice Route (Prevents 404 Intercepts)
    |--------------------------------------------------------------------------
    */
    Route::get('/email/verify', function () {
        return response()->json([
            'message' => 'Your email address is not verified. Please check your database table `users` and ensure `email_verified_at` is populated.'
        ], 403);
    })->name('verification.notice');

});