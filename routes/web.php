<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SectorCategoryController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\InventoryMovementController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ChequeController; 
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\WastageController;
use App\Http\Controllers\Admin\SalesDashboardController;
use App\Http\Controllers\Admin\CustomerLedgerController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ActivityLogController;
/*
|--------------------------------------------------------------------------
| Web Routes Configuration
|--------------------------------------------------------------------------
*/

// Smart root route routing based on authentication node state
Route::get('/', function () {
    return Auth::check() ? redirect()->route('admin.dashboard') : redirect()->route('login');
});

//=========================================
// PUBLIC CLIENT GATEWAY (SINGLE-USE TOKEN VERIFICATION ENGINE)
//=========================================
Route::get('/invoice/share/{token}', [InvoiceController::class, 'showWebInvoice'])->name('invoice.public_share');

//=========================================
// AUTHENTICATION SUITE (GUEST / SCADA)
//=========================================
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // OTP Pipeline verification 
    Route::get('/verify-otp', [LoginController::class, 'showOtpForm'])->name('otp.view');
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('otp.verify');
});

if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}
//=========================================
// SECURE ADMIN MATRIX CONTROL GROUP
//=========================================
Route::middleware(['web', 'auth'])->group(function () {
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware(['verified'])->prefix('admin')->name('admin.')->group(function () {
        
        // 1. Core Dashboard Control
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // 2. Unified Ledger Modules
        Route::resource('customers', CustomerController::class);
        Route::resource('products', AdminProductController::class);
        Route::resource('categories', SectorCategoryController::class);

        // 3. Inventory & Warehouse Logistics
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/add-stock/{product}', [StockController::class, 'create'])->name('create');
            Route::post('/store/{product}', [StockController::class, 'store'])->name('store');
            
            Route::get('/adjust/{product}', [InventoryMovementController::class, 'create'])->name('adjust.create');
            Route::post('/adjust/{product}', [InventoryMovementController::class, 'store'])->name('adjust.store');
        });

        // 4. Invoice Processing Hub
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/store', [InvoiceController::class, 'store'])->name('store');
            
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/print/{invoice}', [InventoryMovementController::class, 'printInvoicePDF'])->name('print');
            Route::get('/view/{invoice}', [InvoiceController::class, 'showWebInvoice'])->name('show_web');
            Route::post('/generate-link/{invoice}', [InvoiceController::class, 'generateShareLink'])->name('generate_link');
            Route::get('/generate-image/{id}', [InvoiceController::class, 'generateShareableImage'])->name('generate_image');
        });

        // 5. Sales & POS Terminal Infrastructure
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/dashboard', [SalesDashboardController::class, 'index'])->name('dashboard');
            Route::get('/ledger/{customerId}', [InventoryMovementController::class, 'showCustomerLedger'])->name('customer-ledger');
            Route::get('/customer/{id}', [SalesController::class, 'customerLedger'])->name('customer-ledger-old');
            Route::get('/create', [SalesController::class, 'create'])->name('create');
            Route::post('/pos', [SalesController::class, 'store'])->name('pos.store'); 
            Route::post('/{id}/update-payment', [SalesController::class, 'updatePayment'])->name('update-payment');
            Route::get('/logs', [InventoryMovementController::class, 'salesIndex'])->name('index');
            Route::get('/invoices/print/{invoice}', [InventoryMovementController::class, 'printInvoicePDF'])->name('invoices.print');
            Route::get('/pos/{product?}', [InventoryMovementController::class, 'createSale'])->name('pos.create');
        });

        // Cheques
        Route::prefix('cheques')->name('cheques.')->group(function () {
            Route::get('/', [ChequeController::class, 'index'])->name('index');
            Route::get('/create', [ChequeController::class, 'create'])->name('create');
            Route::post('/', [ChequeController::class, 'store'])->name('store');
            // Manual trigger route kept for emergency use
            Route::get('/trigger-reminders', [ChequeController::class, 'sendMaturityEmail'])->name('send_reminders');
        });
      // Find this block (Section 7) in your current file and replace it:

        // 7. Backup Management Matrix
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/', [BackupController::class, 'store'])->name('store');
            Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
            Route::delete('/delete/{filename}', [BackupController::class, 'destroy'])->name('destroy');
        });

        // WITH YOUR NEW CODE:
        Route::prefix('backups')->group(function () {
            Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
            Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
            Route::get('backups/download/{filename}', [BackupController::class, 'download'])->name('backups.download');
            Route::delete('backups/destroy/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');
        });

        // 8. New Returns & Spoils Section Routes
        Route::get('returns-wastage', [WastageController::class, 'index'])->name('wastage.index');
        Route::get('returns-wastage/create', [WastageController::class, 'create'])->name('wastage.create');
        Route::post('returns-wastage', [WastageController::class, 'store'])->name('wastage.store');
        Route::get('returns-wastage/{id}', [WastageController::class, 'show'])->name('wastage.show');
    
        // 9. Customer Ledger Explicit Absolute Routing
        // (तपाईंको साविकको नेमस्पेस 'admin.ledger.show' लाई जोगाउन यहाँ बिना प्रिफिक्स ओभरराइड गरिएको छ)
        Route::get('/customer-ledger/{id}', [CustomerLedgerController::class, 'show'])->name('ledger.show');
        Route::post('/customer-ledger/{id}/payment', [CustomerLedgerController::class, 'storePayment'])->name('payments.store');

        // 10. Admin Settings & User Controls Infrastructure
        Route::resource('staff', StaffController::class);
        Route::resource('roles', RoleController::class);
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{id}', [ActivityLogController::class, 'show'])->name('logs.show');

    });
});