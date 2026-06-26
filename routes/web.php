<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\{
    DashboardController, CustomerController, ProductController as AdminProductController,
    SectorCategoryController, StockController, InventoryMovementController,
    InvoiceController, ChequeController, SalesController, BackupController,
    WastageController, SalesDashboardController, CustomerLedgerController,
    StaffController, RoleController, PurchaseDashboardController, ActivityLogController
};

// १. Root Gateway
Route::get('/', fn() => Auth::check() ? redirect()->route('admin.dashboard') : redirect()->route('login'));
Route::get('/invoice/share/{token}', [InvoiceController::class, 'showWebInvoice'])->name('invoice.public_share');



// २. Authentication Suite (Guest only)
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/verify-otp', [LoginController::class, 'showOtpForm'])->name('otp.view');
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('otp.verify');
    Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/update', [LoginController::class, 'resetPassword'])->name('password.update');
});

// Check for auth.php file
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

// ३. SECURE ADMIN MATRIX (Auth & verified users only)
Route::middleware(['web', 'auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Shared routes (Admin & Accountant)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- ROLE: ADMIN वा ACCOUNTANT दुवैका लागि ---
    Route::middleware(['role:admin|accountant'])->group(function () {
        
        // Customers, Products, Categories
        Route::resource('customers', CustomerController::class);
        Route::resource('products', AdminProductController::class);
        Route::resource('categories', SectorCategoryController::class);

        // Inventory Management (सच्याइएको रुटहरू)
        Route::prefix('inventory')->name('inventory.')->group(function () {
    
            // Inventory Index
            Route::get('/', [SalesController::class, 'index'])->name('index');
            
            // नयाँ 'Add Stock' रुट (अघिको त्रुटी सच्याइएको)
            Route::get('/add', [App\Http\Controllers\Admin\InventoryMovementController::class, 'createAddStock'])
                ->name('add');

            // Add Stock Routes
            Route::get('/add-stock/{product}', [StockController::class, 'create'])->name('create');
            Route::post('/store/{product}', [StockController::class, 'store'])->name('store');

            // Adjustment Routes
            Route::get('/adjust/{product}', [InventoryMovementController::class, 'create'])->name('adjust.create');
            Route::post('/adjust/{product}', [InventoryMovementController::class, 'store'])->name('adjust.store');

            // Low Stock Management
            Route::get('/low-stock', [InventoryMovementController::class, 'manageLowStock'])->name('manageLowStock');
            Route::get('/low-stock-manager', [InventoryMovementController::class, 'manageLowStock'])->name('low_stock_manager');
        });

        // --- REPLACED INVOICE MANAGEMENT BLOCK ---
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('index');
    Route::get('/create', [InvoiceController::class, 'create'])->name('create');
    Route::post('/store', [InvoiceController::class, 'store'])->name('store');
    
    // This is the route for viewing detailed invoice items
    Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
    
    Route::get('/print/{invoice}', [InvoiceController::class, 'printInvoicePDF'])->name('print');
    Route::get('/view/{invoice}', [InvoiceController::class, 'showWebInvoice'])->name('show_web');
    Route::post('/generate-link/{invoice}', [InvoiceController::class, 'generateShareLink'])->name('generate_link');
    Route::get('/generate-image/{id}', [InvoiceController::class, 'generateShareableImage'])->name('generate_image');
});

        // Report & Customer Search Routes
        Route::prefix('reports')->name('reports.')->middleware(['role:admin|accountant'])->group(function () {
            // Report पेज (जहाँ filter र list हुन्छ)
            Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        });

   // Sales & POS
Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/dashboard', [SalesDashboardController::class, 'index'])->name('dashboard');
    Route::get('/create', [SalesController::class, 'create'])->name('create');
    Route::post('/pos', [SalesController::class, 'store'])->name('pos.store');
    Route::get('/pos/{product?}', [SalesController::class, 'createSale'])->name('pos.create');
    Route::get('/analysis', [SalesController::class, 'itemAnalysis'])->name('item-analysis');
    
    // यो नयाँ रुट थप्नुहोस् (फोन नम्बरबाट लेजर हेर्न)
    Route::get('/ledger-by-phone/{phone}', [CustomerLedgerController::class, 'showByPhone'])->name('customer-ledger-by-phone');
    
    // पुराना रुटहरू (जुन तपाईंले प्रयोग गरिरहनुभएको छ)
    Route::get('/ledger/{customerId}', [CustomerLedgerController::class, 'showCustomerLedger'])->name('customer-ledger');
    Route::get('/customer/{id}', [CustomerLedgerController::class, 'showCustomerLedger'])->name('customer-ledger-old');
    Route::post('/{id}/update-payment', [SalesController::class, 'updatePayment'])->name('update-payment');
    
    Route::get('/logs', [InventoryMovementController::class, 'salesIndex'])->name('index');
    Route::get('/invoices/print/{invoice}', [InvoiceController::class, 'printInvoicePDF'])->name('invoices.print');
});
        // Cheques Management
        Route::prefix('cheques')->name('cheques.')->group(function () {
            Route::get('/', [ChequeController::class, 'index'])->name('index');
            Route::get('/create', [ChequeController::class, 'create'])->name('create');
            Route::post('/', [ChequeController::class, 'store'])->name('store');
            Route::get('/trigger-reminders', [ChequeController::class, 'sendMaturityEmail'])->name('send_reminders');
        });

        // Purchases Management
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/dashboard', [PurchaseDashboardController::class, 'index'])->name('dashboard');
            Route::get('/create', [PurchaseDashboardController::class, 'create'])->name('create');
            Route::post('/store', [App\Http\Controllers\Admin\InventoryMovementController::class, 'storeAddStock'])->name('store');
            Route::get('/{purchase}', [PurchaseDashboardController::class, 'show'])->name('show');
            Route::get('/edit/{purchase}', [PurchaseDashboardController::class, 'edit'])->name('edit');
            Route::post('/update/{purchase}', [PurchaseDashboardController::class, 'update'])->name('update');
            Route::delete('/destroy/{purchase}', [PurchaseDashboardController::class, 'destroy'])->name('destroy');
        });
        

        // Returns & Wastage
        Route::get('returns-wastage', [WastageController::class, 'index'])->name('wastage.index');
        Route::get('returns-wastage/create', [WastageController::class, 'create'])->name('wastage.create');
        Route::post('returns-wastage', [WastageController::class, 'store'])->name('wastage.store');
        Route::get('returns-wastage/{id}', [WastageController::class, 'show'])->name('wastage.show');
// --- यहाँ राख्नुहोस् (यो तपाईंको रुट हो) ---
        // Customer Ledger (यो लाइन तपाईंको फाइलमा छ, यसलाई केही नगर्नुहोस्)
        Route::get('/customer-ledger/{id}', [CustomerLedgerController::class, 'show'])->name('ledger.show');
        Route::post('/customer-ledger/{id}/payment', [CustomerLedgerController::class, 'storePayment'])->name('payments.store');
        // ... तपाईंको बाँकी रुटहरू भन्दा माथि वा admin ग्रुप भित्र ...
Route::prefix('profile')->name('profile.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::patch('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::get('/change-password', [App\Http\Controllers\ProfileController::class, 'passwordEdit'])->name('change');
    Route::patch('/update-password', [App\Http\Controllers\ProfileController::class, 'passwordUpdate'])->name('update-password');
});

// 'User Guide' को लागि (यदि तपाईंको ड्यासबोर्ड भित्रै छ भने)
Route::get('/user-guide', [App\Http\Controllers\Admin\DashboardController::class, 'guide'])->name('user-guide');
    });

    // --- ROLE: ADMIN only (संवेदनशील पहुँच) ---
    Route::middleware(['role:admin'])->group(function () {
        
        // Backup Management
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/', [BackupController::class, 'store'])->name('store');
            Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
            Route::delete('/destroy/{filename}', [BackupController::class, 'destroy'])->name('destroy');
        });

        // Staff & Roles Management
        Route::resource('staff', StaffController::class);
        Route::resource('roles', RoleController::class);

        // Activity Logs
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{id}', [ActivityLogController::class, 'show'])->name('logs.show');

        // Debugging routes (Development only)
        Route::get('/debug-mail', function () {
            try {
                \Mail::raw('यो एक टेस्ट ईमेल हो।', function ($message) {
                    $message->to('gaihrenirmal2021@gmail.com')->subject('Test Email');
                });
                return "ईमेल सफल भयो!";
            } catch (\Exception $e) {
                return "एरर आयो: " . $e->getMessage();
            }
        });

        Route::get('/test-notification', function () {
            \OneSignal::sendNotificationToAll(
                "Test Notification: Yo OneSignal ko test ho!",
                null, null, null, null,
                "Deurali Chemicals System"
            );
            return "Notification pathayo! Check your browser.";
        });
    });
});