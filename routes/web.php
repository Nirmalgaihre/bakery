<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Exports\ProductsExport;
use App\Http\Controllers\Admin\AiAssistantController;
use App\Http\Controllers\Admin\{
    DashboardController, CustomerController, ProductController as AdminProductController,
    SectorCategoryController, StockController, InventoryMovementController,
    InvoiceController, ChequeController, SalesController, BackupController, WastageController,
    SalesDashboardController, CustomerLedgerController, StaffController, RoleController,
    PurchaseDashboardController, ActivityLogController
};


// 1. Root Gateway
Route::get('/', fn() => Auth::check() ? redirect()->route('admin.dashboard') : redirect()->route('login'));
Route::get('/invoice/share/{token}', [InvoiceController::class, 'showWebInvoice'])->name('invoice.public_share');

// 2. Authentication Suite (Guest only)
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

// 3. SECURE ADMIN MATRIX (Auth & verified users only)
Route::middleware(['web', 'auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // NOTE ON ORDERING:
    // The "admin only" (create/store/edit/update/destroy) block is registered
    // BEFORE the "admin|accountant" (index/show) block on purpose.
    // Laravel matches routes in registration order, and literal segments like
    // "products/create" must be registered before a wildcard route such as
    // "products/{product}" — otherwise the wildcard swallows "create" as if
    // it were the {product} parameter and calls show() instead of create().

    // --- Routes accessible to Admin ONLY (create, edit, delete, and specific management tasks) ---
    Route::middleware(['role:admin'])->group(function () {
        // Customers (manage)
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
Route::get('customers/{customer}/monthly-summary', [CustomerController::class, 'monthlySummary'])->name('customers.monthly-summary');
Route::get('customers/{customer}/month/{month}', [CustomerController::class, 'monthInvoices'])->name('customers.month-invoices');
        // Products (manage)
        Route::get('products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        // Product Import/Export routes
        Route::get('/products/export/{type}', [AdminProductController::class, 'export'])->name('products.export');
        Route::get('products/import', [AdminProductController::class, 'importForm'])->name('products.import.form');
        Route::post('products/import', [AdminProductController::class, 'import'])->name('products.import');
        Route::get('products/import/template', [AdminProductController::class, 'importTemplate'])->name('products.import.template');

       // ... inside the admin group (around line 35)
        Route::middleware(['role:admin'])->group(function () {
            
            // ... existing routes ...

            // Categories (manage)
            Route::post('categories', [SectorCategoryController::class, 'store'])->name('categories.store');
            Route::get('categories/{category}/edit', [SectorCategoryController::class, 'edit'])->name('categories.edit');
            Route::put('categories/{category}', [SectorCategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [SectorCategoryController::class, 'destroy'])->name('categories.destroy');

            // ADD IT HERE:
            Route::get('trash', [App\Http\Controllers\Admin\TrashController::class, 'index'])->name('trash.index');

            // ... existing routes ...
        });
        // Inventory Management (manage)
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/add', [App\Http\Controllers\Admin\InventoryMovementController::class, 'createAddStock'])->name('add');
            Route::get('/add-stock/{product}', [StockController::class, 'create'])->name('create');
            Route::post('/store/{product}', [StockController::class, 'store'])->name('store');
            Route::get('/adjust/{product}', [InventoryMovementController::class, 'create'])->name('adjust.create');
            Route::post('/adjust/{product}', [InventoryMovementController::class, 'store'])->name('adjust.store');
        });

        // Invoice Management (manage)
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/store', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
            Route::post('/generate-link/{invoice}', [InvoiceController::class, 'generateShareLink'])->name('generate_link');
            Route::get('/generate-image/{id}', [InvoiceController::class, 'generateShareableImage'])->name('generate_image');
        });

        // Sales & POS (manage)
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/create', [SalesController::class, 'create'])->name('create');
            Route::post('/pos', [SalesController::class, 'store'])->name('pos.store');
            Route::get('/pos/{product?}', [SalesController::class, 'createSale'])->name('pos.create');
            Route::post('/{id}/update-payment', [SalesController::class, 'updatePayment'])->name('update-payment');
            Route::get('/manage', [SalesController::class, 'manage'])->name('manage');
            });

        // Cheques Management (manage)
        Route::prefix('cheques')->name('cheques.')->group(function () {
            Route::get('/create', [ChequeController::class, 'create'])->name('create');
            Route::post('/', [ChequeController::class, 'store'])->name('store');
            Route::get('/trigger-reminders', [ChequeController::class, 'sendMaturityEmail'])->name('send_reminders');
        });

        // Purchases Management (manage)
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/create', [PurchaseDashboardController::class, 'create'])->name('create');
            Route::post('/store', [App\Http\Controllers\Admin\InventoryMovementController::class, 'storeAddStock'])->name('store');
            Route::get('/edit/{purchase}', [PurchaseDashboardController::class, 'edit'])->name('edit');
            Route::post('/update/{purchase}', [PurchaseDashboardController::class, 'update'])->name('update');
            Route::delete('/destroy/{purchase}', [PurchaseDashboardController::class, 'destroy'])->name('destroy');
        });

        // Returns & Wastage (manage)
        Route::get('returns-wastage/create', [WastageController::class, 'create'])->name('wastage.create');
        Route::post('returns-wastage', [WastageController::class, 'store'])->name('wastage.store');

        // Customer Ledger (manage payments)
        Route::post('/customer-ledger/{id}/payment', [CustomerLedgerController::class, 'storePayment'])->name('payments.store');

        // Backup Management (manage)
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::post('/', [BackupController::class, 'store'])->name('store');
            Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
            Route::delete('/destroy/{filename}', [BackupController::class, 'destroy'])->name('destroy');
        });

        // Staff & Roles Management (manage)
        Route::get('staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

        // Explicitly define CUD routes for roles
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // --- Routes accessible to both Admin and Accountant (primarily view/read operations) ---
    Route::middleware(['role:admin|accountant'])->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Customers (view only)
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        // Products (view only)
        Route::get('products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [AdminProductController::class, 'show'])->name('products.show');
        // Categories (view only)
        Route::get('categories', [SectorCategoryController::class, 'index'])->name('categories.index');

        // Inventory (view only)
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [SalesController::class, 'showInventoryProducts'])->name('index'); 
            Route::get('/position', [App\Http\Controllers\Admin\InventoryMovementController::class, 'stockPosition'])->name('position');
            Route::get('/low-stock', [InventoryMovementController::class, 'manageLowStock'])->name('manageLowStock');
            Route::get('/low-stock-manager', [InventoryMovementController::class, 'manageLowStock'])->name('low_stock_manager');
        });

        // Invoice Management (view only)
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/print/{invoice}', [InvoiceController::class, 'printInvoicePDF'])->name('print');
            Route::get('/view/{invoice}', [InvoiceController::class, 'showWebInvoice'])->name('show_web');
        });

        // Reports & Customer Search Routes (view only)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/cash-flow', [App\Http\Controllers\Admin\ReportController::class, 'cashFlowReport'])->name('cash-flow');
            Route::get('/stock-movement', [App\Http\Controllers\Admin\ReportController::class, 'stockMovementReport'])->name('stock-movement');
        });

        // Sales & POS (view only)
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/dashboard', [SalesDashboardController::class, 'index'])->name('dashboard');
            Route::get('/analysis', [SalesController::class, 'itemAnalysis'])->name('item-analysis'); // Kept this route as it's a valid feature
            Route::get('/ledger-by-phone/{phone}', [CustomerLedgerController::class, 'showByPhone'])->name('customer-ledger-by-phone');
            Route::get('/ledger/{customerId}', [CustomerLedgerController::class, 'showCustomerLedger'])->name('customer-ledger');
            Route::get('/customer/{id}', [CustomerLedgerController::class, 'showCustomerLedger'])->name('customer-ledger-old');
            Route::get('/logs', [InventoryMovementController::class, 'salesIndex'])->name('index');
            Route::get('/invoices/print/{invoice}', [InvoiceController::class, 'printInvoicePDF'])->name('invoices.print');
            Route::get('/all', [SalesController::class, 'index'])->name('all');
        });

        // Cheques Management (view only)
        Route::prefix('cheques')->name('cheques.')->group(function () {
            Route::get('/', [ChequeController::class, 'index'])->name('index');
        });

        // Purchases Management (view only)
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/dashboard', [PurchaseDashboardController::class, 'index'])->name('dashboard');
            Route::get('/{purchase}', [PurchaseDashboardController::class, 'show'])->name('show');
        });

        // Release notes (view only)
        Route::prefix('release-notes')->name('release-notes.')->group(function () {
            Route::get('/', function () {
                return view('admin.release-notes');
            })->name('index');
        });

        // Returns & Wastage (view only)
        Route::get('returns-wastage', [WastageController::class, 'index'])->name('wastage.index');
        Route::get('returns-wastage/{id}', [WastageController::class, 'show'])->name('wastage.show');

        // Customer Ledger (view only)
        Route::get('/customer-ledger/{id}', [CustomerLedgerController::class, 'show'])->name('ledger.show');

        // User Profile (both can manage their own profile)
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
            Route::patch('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
            Route::get('/change-password', [App\Http\Controllers\ProfileController::class, 'passwordEdit'])->name('change');
            Route::patch('/update-password', [App\Http\Controllers\ProfileController::class, 'passwordUpdate'])->name('update-password');
        });

        // User Guide (view only)
        Route::get('/user-guide', [App\Http\Controllers\Admin\DashboardController::class, 'guide'])->name('user-guide');

        // Backup Management (view only)
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
        });

        // Staff & Roles Management (view only)
        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/{staff}', [StaffController::class, 'show'])->name('staff.show'); // Assuming a show method for staff
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show'); // Assuming a show method for roles

        // Activity Logs (view only)
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{id}', [ActivityLogController::class, 'show'])->name('logs.show');
        Route::post('/ai/query', [AiAssistantController::class, 'query'])->name('ai.query');
    });
    
});