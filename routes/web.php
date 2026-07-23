<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Exports\ProductsExport;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AiAssistantController;
use App\Http\Controllers\Admin\TransactionController; // Ensure this matches the file location
use App\Http\Controllers\Admin\{
    DashboardController, CustomerController, ProductController as AdminProductController,
    SectorCategoryController, StockController, InventoryMovementController,
    InvoiceController, ChequeController, SalesController, BackupController, WastageController,
    SalesDashboardController, CustomerLedgerController, StaffController, RoleController,
    PurchaseDashboardController, SupplierController, ActivityLogController, TrashController
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

    // =====================================================================
    // ADMIN-ONLY ZONE
    // Strictly restricted to Administrators. Accountants and other staff
    // are blocked from every route in this block.
    //   - Dashboard (Inventory / Sales / Purchase dashboards)
    //   - Cheque Management
    //   - Backup & Restore
    //   - Trash Bin (Recycler)
    //   - User Control (Staff & Role management)
    //
    // NOTE: Dashboard + Cheques were moved here from the shared
    // admin+accountant block below — accountants no longer have route-level
    // access to these, matching the sidebar/layout changes.
    // =====================================================================
    Route::middleware(['role:admin'])->group(function () {

        // Dashboards
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('sales/dashboard', [SalesDashboardController::class, 'index'])->name('sales.dashboard');
        Route::get('purchases/dashboard', [PurchaseDashboardController::class, 'index'])->name('purchases.dashboard');

        // Cheque Management (full CRUD)
        Route::prefix('cheques')->name('cheques.')->group(function () {
            Route::get('/create', [ChequeController::class, 'create'])->name('create');
            Route::post('/', [ChequeController::class, 'store'])->name('store');
            Route::get('/trigger-reminders', [ChequeController::class, 'sendMaturityEmail'])->name('send_reminders');
            Route::get('/', [ChequeController::class, 'index'])->name('index');
        });

        // Trash Bin (Recycler)
        Route::get('trash', [TrashController::class, 'index'])->name('trash.index');

        // Backup & Restore
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/', [BackupController::class, 'store'])->name('store');
            Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
            Route::delete('/destroy/{filename}', [BackupController::class, 'destroy'])->name('destroy');
            Route::post('/import', [BackupController::class, 'import'])->name('import');
            Route::get('/restore/{filename}', [BackupController::class, 'restore'])->name('restore');
        });

        // User Control: Staff Management
        Route::get('staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/{staff}', [StaffController::class, 'show'])->name('staff.show');

        // User Control: Role & Permission Management
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });

    // =====================================================================
    // ADMIN + ACCOUNTANT ZONE
    // Full authority (create, edit, view, manage) over the core
    // operational & financial pipeline: Categories, Suppliers, Products,
    // Customers, Sales & Invoicing, Inventory, Returns/Wastage, Reports.
    //
    // Dashboard and Cheque Management were REMOVED from this block — they
    // now live exclusively in the admin-only zone above.
    //
    // NOTE ON ORDERING:
    // Literal segments like "products/create" are registered before the
    // wildcard "products/{product}" route — otherwise the wildcard would
    // swallow "create" as if it were the {product} parameter.
    // =====================================================================
    Route::middleware(['role:admin|accountant'])->group(function () {

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // ---------------------------------------------------------------
        // Step 1: Category Management (full CRUD)
        // ---------------------------------------------------------------
        Route::post('categories', [SectorCategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [SectorCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [SectorCategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [SectorCategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('categories', [SectorCategoryController::class, 'index'])->name('categories.index');

        // ---------------------------------------------------------------
        // Step 2: Supplier Management (full CRUD)
        // ---------------------------------------------------------------
        Route::prefix('suppliers')->name('suppliers.')->group(function () {
            Route::get('/create', [SupplierController::class, 'create'])->name('create');
            Route::post('/', [SupplierController::class, 'store'])->name('store');
            Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
            Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
            Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
            Route::get('/', [SupplierController::class, 'index'])->name('index');
            Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
        });

        // ---------------------------------------------------------------
        // Step 3: Product Management (full CRUD + import/export)
        // ---------------------------------------------------------------
        Route::get('products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products/export/{type}', [AdminProductController::class, 'export'])->name('products.export');
        Route::get('products/import', [AdminProductController::class, 'importForm'])->name('products.import.form');
        Route::post('products/import', [AdminProductController::class, 'import'])->name('products.import');
        Route::get('products/import/template', [AdminProductController::class, 'importTemplate'])->name('products.import.template');
        Route::get('products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('customers/{customer}/purchased-products', [CustomerController::class, 'purchasedProducts'])->name('customers.purchased-products');
        Route::get('products/{product}', [AdminProductController::class, 'show'])->name('products.show');

        // ---------------------------------------------------------------
        // Step 4: Customer Management (full CRUD)
        // ---------------------------------------------------------------
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('customers/manage', [CustomerController::class, 'manage'])->name('customers.manage');
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::get('customers/{customer}/monthly-summary', [CustomerController::class, 'monthlySummary'])->name('customers.monthly-summary');
        Route::get('customers/{customer}/month/{month}', [CustomerController::class, 'monthInvoices'])->name('customers.month-invoices');
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        // ---------------------------------------------------------------
        // Step 5: Sales & Invoice Processing (full CRUD)
        // ---------------------------------------------------------------
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/store', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
            Route::post('/generate-link/{invoice}', [InvoiceController::class, 'generateShareLink'])->name('generate_link');
            Route::get('/generate-image/{id}', [InvoiceController::class, 'generateShareableImage'])->name('generate_image');
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/print/{invoice}', [InvoiceController::class, 'printInvoicePDF'])->name('print');
            Route::get('/view/{invoice}', [InvoiceController::class, 'showWebInvoice'])->name('show_web');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        });

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/create', [SalesController::class, 'create'])->name('create');
            Route::post('/pos', [SalesController::class, 'store'])->name('pos.store');
            Route::get('/pos/{product?}', [SalesController::class, 'createSale'])->name('pos.create');
            Route::post('/{id}/update-payment', [SalesController::class, 'updatePayment'])->name('update-payment');
            Route::get('/manage', [SalesController::class, 'manage'])->name('manage');
            Route::get('/analysis', [SalesController::class, 'itemAnalysis'])->name('item-analysis');
            Route::get('/ledger-by-phone/{phone}', [CustomerLedgerController::class, 'showByPhone'])->name('customer-ledger-by-phone');
            Route::get('/ledger/{customerId}', [CustomerLedgerController::class, 'showCustomerLedger'])->name('customer-ledger');
            Route::get('/customer/{id}', [CustomerLedgerController::class, 'showCustomerLedger'])->name('customer-ledger-old');
            Route::get('/product-traceability', [InventoryMovementController::class, 'productTraceability'])->name('product_traceability');
            Route::get('/logs', [InventoryMovementController::class, 'salesIndex'])->name('index');
            Route::get('/invoices/print/{invoice}', [InvoiceController::class, 'printInvoicePDF'])->name('invoices.print');
            Route::get('/all', [SalesController::class, 'index'])->name('all');
        });

        // Customer Ledger (manage payments + view)
        Route::post('/customer-ledger/{id}/payment', [CustomerLedgerController::class, 'storePayment'])->name('payments.store');
        Route::get('/customer-ledger/{id}', [CustomerLedgerController::class, 'show'])->name('ledger.show');

        // ---------------------------------------------------------------
        // Step 6: Inventory Tracking (full CRUD)
        // ---------------------------------------------------------------
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/add', [InventoryMovementController::class, 'createAddStock'])->name('add');
            Route::get('/add-stock/{product}', [StockController::class, 'create'])->name('create');
            Route::post('/store/{product}', [StockController::class, 'store'])->name('store');
            Route::get('/adjust/{product}', [InventoryMovementController::class, 'create'])->name('adjust.create');
            Route::post('/adjust/{product}', [InventoryMovementController::class, 'store'])->name('adjust.store');
            Route::get('/', [SalesController::class, 'showInventoryProducts'])->name('index');
            Route::get('/position', [InventoryMovementController::class, 'stockPosition'])->name('position');
            Route::get('/low-stock', [InventoryMovementController::class, 'manageLowStock'])->name('manageLowStock');
            Route::get('/low-stock-manager', [InventoryMovementController::class, 'manageLowStock'])->name('low_stock_manager');
        });

        // Purchases Management (full CRUD) — receiving stock from suppliers
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/create', [PurchaseDashboardController::class, 'create'])->name('create');
            Route::post('/store', [InventoryMovementController::class, 'storeAddStock'])->name('store');
            Route::get('/edit/{purchase}', [PurchaseDashboardController::class, 'edit'])->name('edit');
            Route::post('/update/{purchase}', [PurchaseDashboardController::class, 'update'])->name('update');
            Route::delete('/destroy/{purchase}', [PurchaseDashboardController::class, 'destroy'])->name('destroy');
            Route::get('/{purchase}', [PurchaseDashboardController::class, 'show'])->name('show');
        });

        // Transactions
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index');
            Route::post('/', [TransactionController::class, 'store'])->name('store');
            Route::get('/report/movement/{productId}', [TransactionController::class, 'showReport'])->name('report.movement');
        });

        // ---------------------------------------------------------------
        // Step 7: Returns Management (Customer & Supplier returns / wastage)
        // ---------------------------------------------------------------
        Route::get('returns-wastage/create', [WastageController::class, 'create'])->name('wastage.create');
        Route::post('returns-wastage', [WastageController::class, 'store'])->name('wastage.store');
        Route::get('returns-wastage', [WastageController::class, 'index'])->name('wastage.index');
        Route::get('returns-wastage/{id}', [WastageController::class, 'show'])->name('wastage.show');

        // ---------------------------------------------------------------
        // Step 8: Analytical Reports
        // ---------------------------------------------------------------
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/cash-flow', [ReportController::class, 'cashFlowReport'])->name('cash-flow');
            Route::get('/stock-ageing', [InventoryMovementController::class, 'stockAgeing'])->name('stock_ageing');
            Route::get('/monthly-movement', [InventoryMovementController::class, 'monthlyMovementReport'])->name('monthly-movement');
            Route::get('/stock-movement', [InventoryMovementController::class, 'productTraceability'])->name('stock-movement');
        });

        // ---------------------------------------------------------------
        // Shared utility / non-operational routes (both roles need these)
        // ---------------------------------------------------------------

        // Release notes
        Route::prefix('release-notes')->name('release-notes.')->group(function () {
            Route::get('/', function () {
                return view('admin.release-notes');
            })->name('index');
        });

        // User Profile (each user manages their own profile)
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
            Route::patch('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
            Route::get('/change-password', [App\Http\Controllers\ProfileController::class, 'passwordEdit'])->name('change');
            Route::patch('/update-password', [App\Http\Controllers\ProfileController::class, 'passwordUpdate'])->name('update-password');
        });

        // User Guide
        Route::get('/user-guide', [DashboardController::class, 'guide'])->name('user-guide');

        // Activity Logs
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{id}', [ActivityLogController::class, 'show'])->name('logs.show');

        // AI Assistant
        Route::post('/ai/query', [AiAssistantController::class, 'query'])->name('ai.query');
    });
});