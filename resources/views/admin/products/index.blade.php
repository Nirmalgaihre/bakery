@extends('layouts.admin')

@section('title', 'Product Registry - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Product Catalog Registry')

@section('content')
<!-- Custom Styles to Match Dashboard Theme Exactly -->
<style>
:root {
    --dash-blue: #6366f1;
    --dash-indigo: #4f46e5;
    --dash-green: #10b981;
    --dash-teal: #059669;
    --dash-purple: #8b5cf6;
    --dash-red: #ef4444;
    --dash-orange: #f97316;
}

/* Primary Vibrant Stat Card Styles (Top Row Dashboard Matching) */
.card-stat-blue {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #ffffff;
}

.card-stat-green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
}

.card-stat-purple {
    background: linear-gradient(135deg, #a855f7 0%, #8b5cf6 100%);
    color: #ffffff;
}

.card-stat-emerald {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    color: #ffffff;
}

/* Secondary White Stat Cards with Accent Bars (Middle Row Dashboard Matching) */
.card-white-accent {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05);
}

/* Dashboard Action Bar Tabs */
.dash-tab-item {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    padding: 6px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.dash-tab-item:hover,
.dash-tab-item.active {
    color: #4f46e5;
    background-color: #f0fdf4;
}

/* Quick Bar Action Buttons */
.dash-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 6px 10px;
    border-radius: 8px;
    color: #475569;
    font-size: 10px;
    font-weight: 700;
    transition: all 0.15s ease;
}

.dash-action-btn:hover {
    background-color: #f1f5f9;
    color: #4f46e5;
}
</style>

<div class="w-full mx-auto space-y-5 font-sans text-slate-800">

    <!-- Dashboard Quick Tools Ribbon Bar -->
    {{-- ================================================================= --}}
    {{-- SECTION 1 — PRODUCT CATALOG RIBBON (INDIGO)                      --}}
    {{-- ================================================================= --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-3 border-l-4 border-l-indigo-500">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-1 sm:gap-2 flex-wrap">
                <div
                    class="text-[9px] font-black uppercase text-indigo-400 tracking-wider pr-2 border-r border-slate-200">
                    Product Actions
                </div>

                @can('create', \App\Models\Product::class)
                <a href="{{ route('admin.products.create') }}"
                    class="dash-action-btn bg-indigo-50 text-[var(--dash-indigo)] hover:bg-indigo-100">
                    <i class="fa-solid fa-circle-plus text-base"></i>
                    <span>Add Item</span>
                </a>
                @endcan

                <a href="{{ route('admin.products.export', ['type' => 'xlsx']) }}" class="dash-action-btn">
                    <i class="fa-solid fa-file-excel text-base text-emerald-600"></i>
                    <span>Export Excel</span>
                </a>

                <a href="{{ route('admin.products.export', ['type' => 'csv']) }}" class="dash-action-btn">
                    <i class="fa-solid fa-file-csv text-base text-slate-600"></i>
                    <span>Export CSV</span>
                </a>

                <a href="{{ route('admin.products.import.form') }}" class="dash-action-btn">
                    <i class="fa-solid fa-file-arrow-up text-base text-orange-500"></i>
                    <span>Import File</span>
                </a>

                @if(auth()->check() && auth()->user()->email === 'gaihrenirmal2021@gmail.com')
                <button type="button" onclick="triggerBulkDeleteSubmit()" id="bulkDeleteQuickBtn"
                    class="dash-action-btn bg-rose-50 text-rose-600 hover:bg-rose-100 opacity-50 cursor-not-allowed"
                    disabled>
                    <i class="fa-solid fa-trash-arrow-up text-base"></i>
                    <span>Bulk Delete (<span id="quickDeleteCount">0</span>)</span>
                </button>
                @endif
            </div>

            <div
                class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100">
                Total Products: <span
                    class="text-indigo-950 font-extrabold">{{ number_format($products->total()) }}</span>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- SECTION 2 — PURCHASE REGISTRY RIBBON (EMERALD)                   --}}
    {{-- ================================================================= --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-3 border-l-4 border-l-emerald-500">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-1 sm:gap-2 flex-wrap">
                <div
                    class="text-[9px] font-black uppercase text-emerald-500 tracking-wider pr-2 border-r border-slate-200">
                    Purchase Actions
                </div>

                <a href="{{ route('admin.purchases.create') }}"
                    class="dash-action-btn purchase-btn bg-emerald-50 text-emerald-700 hover:bg-emerald-100">
                    <i class="fa-solid fa-cart-plus text-base"></i>
                    <span>New Purchase</span>
                </a>

                {{-- IMPORT TRIGGER BUTTON --}}
                <button type="button" onclick="openBakeryImportModal()" 
                    class="dash-action-btn purchase-btn bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 cursor-pointer">
                    <i class="fa-solid fa-file-arrow-up text-base text-emerald-600"></i>
                    <span>Import</span>
                </button>

                <a href="{{ route('admin.purchases.export', ['type' => 'xlsx']) }}"
                    class="dash-action-btn purchase-btn">
                    <i class="fa-solid fa-file-excel text-base text-emerald-600"></i>
                    <span>Export Excel</span>
                </a>

                <a href="{{ route('admin.purchases.export', ['type' => 'csv']) }}" class="dash-action-btn purchase-btn">
                    <i class="fa-solid fa-file-csv text-base text-slate-600"></i>
                    <span>Export CSV</span>
                </a>

                @if(auth()->check() && auth()->user()->email === 'gaihrenirmal2021@gmail.com')
                <button type="button" onclick="triggerPurchaseBulkDeleteSubmit()" id="bulkDeletePurchaseBtn"
                    class="dash-action-btn bg-rose-50 text-rose-600 hover:bg-rose-100 opacity-50 cursor-not-allowed"
                    disabled>
                    <i class="fa-solid fa-trash-arrow-up text-base"></i>
                    <span>Bulk Delete (<span id="quickPurchaseDeleteCount">0</span>)</span>
                </button>
                @endif
            </div>

            <div
                class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100">
                Total Purchases: <span
                    class="text-emerald-950 font-extrabold">{{ number_format(isset($purchases) ? $purchases->total() : \App\Models\Purchase::count()) }}</span>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- IMPORT PURCHASE INVENTORY MODAL (WITH MITI / NEPALI DATE)          --}}
    {{-- ================================================================= --}}
    <div id="bakeryImportModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-slate-100 p-6 space-y-5 animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                        <i class="fa-solid fa-file-import text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Import Purchase Inventory</h3>
                        <p class="text-xs text-slate-500">Ensure your Excel file headers match the Tally export format below (including Nepali Date / Miti).</p>
                    </div>
                </div>
                <button type="button" onclick="closeBakeryImportModal()" class="text-slate-400 hover:text-slate-600 text-lg p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Import Form -->
            <form action="{{ route('admin.purchases.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <!-- File Input Dropzone -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Select Excel File (.xlsx, .xls, .csv)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100/80 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-emerald-500 mb-2"></i>
                                <p class="text-sm text-slate-600 font-medium" id="modal-file-label">Click to upload or drag & drop</p>
                                <p class="text-xs text-slate-400">XLSX, XLS or CSV (Max 10MB)</p>
                            </div>
                            <input type="file" name="file" accept=".xlsx, .xls, .csv" required class="hidden" 
                                onchange="document.getElementById('modal-file-label').innerText = this.files[0]?.name || 'Click to upload or drag & drop'">
                        </label>
                    </div>
                </div>

                <!-- Excel Format Guidelines & Preview -->
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 uppercase">Required Excel Layout (Tally Export Format)</span>
                        @if(Route::has('admin.purchases.import.template'))
                            <a href="{{ route('admin.purchases.import.template') }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                                <i class="fa-solid fa-download mr-1"></i>Download Template
                            </a>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500">The import parser will process columns matching your Tally sheet layout starting from the row header.</p>

                    <!-- Header Preview Table -->
                    <div class="overflow-x-auto rounded-lg border border-slate-200 mt-2">
                        <table class="w-full text-left text-[11px] text-slate-600">
                            <thead class="bg-slate-200/70 text-slate-700 uppercase font-bold text-[10px]">
                                <tr>
                                    <th class="px-2.5 py-1.5 border-r border-slate-300">Miti</th>
                                    <th class="px-2.5 py-1.5 border-r border-slate-300">Date</th>
                                    <th class="px-2.5 py-1.5 border-r border-slate-300">Particulars</th>
                                    <th class="px-2.5 py-1.5 border-r border-slate-300">Vch Type</th>
                                    <th class="px-2.5 py-1.5 border-r border-slate-300">Vch No.</th>
                                    <th class="px-2.5 py-1.5 border-r border-slate-300">Debit</th>
                                    <th class="px-2.5 py-1.5">Credit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white font-mono text-[10.5px]">
                                <tr>
                                    <td class="px-2.5 py-1 text-slate-500 border-r border-slate-100">4/1/2082</td>
                                    <td class="px-2.5 py-1 text-slate-500 border-r border-slate-100">17/07/2025</td>
                                    <td class="px-2.5 py-1 text-slate-700 font-sans border-r border-slate-100">SG NEPAL PVT.LTD.</td>
                                    <td class="px-2.5 py-1 text-slate-500 border-r border-slate-100">Purchase</td>
                                    <td class="px-2.5 py-1 text-slate-500 border-r border-slate-100">1</td>
                                    <td class="px-2.5 py-1 text-slate-800 font-bold border-r border-slate-100">49147.00</td>
                                    <td class="px-2.5 py-1 text-slate-400">-</td>
                                </tr>
                                <tr>
                                    <td class="px-2.5 py-1 text-slate-500 border-r border-slate-100">4/2/2082</td>
                                    <td class="px-2.5 py-1 text-slate-500 border-r border-slate-100">18/07/2025</td>
                                    <td class="px-2.5 py-1 text-slate-700 font-sans border-r border-slate-100">SAJILO TRADERS</td>
                                    <td class="px-2.5 py-1 text-slate-500 border-r border-slate-100">Purchase</td>
                                    <td class="px-2.5 py-1 text-slate-500 border-r border-slate-100">5</td>
                                    <td class="px-2.5 py-1 text-slate-800 font-bold border-r border-slate-100">1140.00</td>
                                    <td class="px-2.5 py-1 text-slate-400">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeBakeryImportModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-xs">
                        <i class="fa-solid fa-upload mr-1.5"></i>Upload & Process Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Toggle Scripts --}}
    <script>
        function openBakeryImportModal() {
            document.getElementById('bakeryImportModal').classList.remove('hidden');
        }
        function closeBakeryImportModal() {
            document.getElementById('bakeryImportModal').classList.add('hidden');
        }
    </script>

    <!-- Vibrant Metric Cards Row (Matches Dashboard Top Row) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Net Sales Style Card (Blue) -->
        <div class="card-stat-blue rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div
                    class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-lg backdrop-blur-xs">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <span
                    class="bg-white/20 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full backdrop-blur-xs">
                    Catalog Total
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-100">TOTAL CATALOG PRODUCTS
                </p>
                <p class="text-3xl font-black mt-1 tracking-tight">
                    {{ number_format($totalProductsCount ?? $products->total()) }}
                </p>
            </div>
        </div>

        <!-- Supply Purchases Style Card (Green) -->
        <div class="card-stat-green rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div
                    class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-lg backdrop-blur-xs">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <span
                    class="bg-white/20 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full backdrop-blur-xs">
                    Units Total
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-100">WAREHOUSE STOCK UNITS
                </p>
                <p class="text-3xl font-black mt-1 tracking-tight">
                    {{ number_format($totalStockUnits ?? 0) }}
                </p>
            </div>
        </div>

        <!-- Cost Of Goods Style Card (Purple) -->
        <div class="card-stat-purple rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div
                    class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-lg backdrop-blur-xs">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <span
                    class="bg-white/20 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full backdrop-blur-xs">
                    Active Items
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-purple-100">PRODUCTS IN STOCK</p>
                <p class="text-3xl font-black mt-1 tracking-tight">
                    {{ number_format(($products->total() - ($lowStockCount ?? 0))) }}
                </p>
            </div>
        </div>

        <!-- Net Profit Style Card (Teal) -->
        <div class="card-stat-emerald rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div
                    class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-lg backdrop-blur-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <span
                    class="bg-white/20 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full backdrop-blur-xs">
                    Reorder Alert
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-100">LOW STOCK WARNINGS</p>
                <p class="text-3xl font-black mt-1 tracking-tight">
                    {{ number_format($lowStockCount ?? 0) }}
                </p>
            </div>
        </div>

    </div>

    <!-- Secondary White Cards with Colored Accent Bars (Matches Dashboard Second Row) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="card-white-accent rounded-2xl p-4">
            <p class="text-2xl font-black text-slate-900">{{ number_format($totalProductsCount ?? $products->total()) }}
            </p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">TOTAL REGISTERED PRODUCTS
            </p>
            <div class="w-full bg-slate-100 h-1 rounded-full mt-3 overflow-hidden">
                <div class="bg-emerald-500 h-full w-full"></div>
            </div>
        </div>

        <div class="card-white-accent rounded-2xl p-4">
            <p class="text-2xl font-black text-slate-900">{{ number_format($totalStockUnits ?? 0) }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">ACTIVE INVENTORY COUNT</p>
            <div class="w-full bg-slate-100 h-1 rounded-full mt-3 overflow-hidden">
                <div class="bg-indigo-500 h-full w-full"></div>
            </div>
        </div>

        <div class="card-white-accent rounded-2xl p-4">
            <p class="text-2xl font-black text-slate-900">
                {{ number_format(($products->total() - ($lowStockCount ?? 0))) }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">SUFFICIENT STOCK ITEMS</p>
            <div class="w-full bg-slate-100 h-1 rounded-full mt-3 overflow-hidden">
                <div class="bg-orange-500 h-full w-full"></div>
            </div>
        </div>

        <div class="card-white-accent rounded-2xl p-4">
            <p class="text-2xl font-black text-slate-900">{{ number_format($lowStockCount ?? 0) }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">LOW / OUT OF STOCK</p>
            <div class="w-full bg-slate-100 h-1 rounded-full mt-3 overflow-hidden">
                <div class="bg-red-500 h-full w-full"></div>
            </div>
        </div>

    </div>

    <!-- Main Table Container -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xl overflow-hidden relative">

        <!-- Controls Bar -->
        <div
            class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/60 flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">

            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-md shadow-indigo-500/30">
                    <i class="fa-solid fa-table-list"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-slate-800 tracking-wide uppercase">Product Catalog Master List
                    </h3>
                    <p class="text-[10px] font-medium text-slate-400">Search and filter active inventory items</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">

                <!-- Search -->
                <div class="relative w-full sm:w-52">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-xs text-slate-400"></i>
                    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Quick Search Page..."
                        class="w-full text-xs pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[var(--dash-indigo)]/20 focus:border-[var(--dash-indigo)] outline-none transition-all font-medium">
                </div>

                <!-- Sort -->
                <form method="GET" action="{{ route('admin.products.index') }}" class="inline-block">
                    <select name="sort" onchange="this.form.submit()"
                        class="text-xs border border-slate-200 font-bold rounded-xl px-3 py-2 focus:ring-2 focus:ring-[var(--dash-indigo)]/20 outline-none bg-white text-slate-700 cursor-pointer shadow-2xs">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>↓ Newest First
                        </option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>↑ Oldest First
                        </option>
                        <option value="alpha_asc" {{ request('sort') == 'alpha_asc' ? 'selected' : '' }}>A → Z
                            (Alphabetical)</option>
                        <option value="alpha_desc" {{ request('sort') == 'alpha_desc' ? 'selected' : '' }}>Z → A
                            (Alphabetical)</option>
                        <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Stock: High
                            to Low</option>
                        <option value="stock_low" {{ request('sort') == 'stock_low' ? 'selected' : '' }}>Stock: Low to
                            High</option>
                    </select>
                </form>

                <!-- Stock Filter -->
                <select id="stockFilter" onchange="filterTable()"
                    class="text-xs border border-slate-200 font-bold rounded-xl px-3 py-2 focus:ring-2 focus:ring-[var(--dash-indigo)]/20 outline-none bg-white text-slate-700 cursor-pointer shadow-2xs">
                    <option value="ALL">All Statuses</option>
                    <option value="LOW">Low Stock Only</option>
                    <option value="GOOD">Good Stock Only</option>
                </select>

            </div>
        </div>

        <!-- Filter Count Sub-ribbon -->
        <div class="px-5 py-2 bg-slate-50 border-b border-slate-200/60 flex items-center justify-between text-xs">
            <span id="filteredCount" class="text-[11px] text-slate-500 font-semibold">
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of
                {{ number_format($products->total()) }} items
            </span>
        </div>

        {{-- Floating Action Bar for Bulk Delete (Restricted to gaihrenirmal2021@gmail.com) --}}
        @if(auth()->check() && auth()->user()->email === 'gaihrenirmal2021@gmail.com')
        <div id="bulkDeleteBar"
            class="hidden bg-rose-900 text-white px-5 py-2.5 flex items-center justify-between transition-all duration-300">
            <div class="flex items-center gap-2 text-xs font-bold">
                <i class="fa-solid fa-circle-check text-rose-300"></i>
                <span><span id="selectedCountDisplay">0</span> item(s) selected for deletion</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="triggerBulkDeleteSubmit()"
                    class="px-3.5 py-1 bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can"></i> Delete Selected
                </button>
                <button type="button" onclick="deselectAllCheckboxes()"
                    class="text-rose-200 hover:text-white text-xs font-semibold underline">
                    Cancel Selection
                </button>
            </div>
        </div>

        {{-- Form for Bulk Deletion --}}
        <form id="bulkDeleteForm" action="{{ route('admin.products.bulk-destroy') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
            <div id="bulkDeleteInputsContainer"></div>
        </form>
        @endif

        <!-- Main Data Table Container -->
        <div class="w-full overflow-x-auto block" style="-webkit-overflow-scrolling: touch;">
            <table class="w-full text-left border-collapse text-xs text-slate-700 min-w-[1200px]" id="productTable">
                <thead>
                    <tr
                        class="bg-slate-100/80 border-b border-slate-200 text-[10px] font-black uppercase tracking-wider text-slate-500 sticky top-0">
                        @if(auth()->check() && auth()->user()->email === 'gaihrenirmal2021@gmail.com')
                        <th class="py-3 px-3 text-center w-8">
                            <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)"
                                class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                        </th>
                        @endif
                        <th class="py-3 px-4 text-center w-12">#</th>
                        <th class="py-3 px-4">Product Details</th>
                        <th class="py-3 px-4">Item Code</th>
                        <th class="py-3 px-4">Specs</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4 text-right">Purchase Cost</th>
                        <th class="py-3 px-4 text-right">Selling Price</th>
                        <th class="py-3 px-4 text-center">Unit</th>
                        <th class="py-3 px-4 text-center">Stock</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right pr-6">Actions</th>
                    </tr>
                </thead>

                <!-- Skeleton Placeholders -->
                <tbody id="skeletonRows" class="divide-y divide-slate-100">
                    @for ($i = 0; $i < 6; $i++) <tr class="animate-pulse">
                        @if(auth()->check() && auth()->user()->email === 'gaihrenirmal2021@gmail.com')
                        <td class="p-4 text-center">
                            <div class="h-3 w-3 bg-slate-200 rounded mx-auto"></div>
                        </td>
                        @endif
                        <td class="p-4 text-center">
                            <div class="h-3 w-4 bg-slate-200 rounded mx-auto"></div>
                        </td>
                        <td class="p-4">
                            <div class="h-4 w-36 bg-slate-200 rounded"></div>
                        </td>
                        <td class="p-4">
                            <div class="h-3 w-16 bg-slate-200 rounded"></div>
                        </td>
                        <td class="p-4">
                            <div class="h-3 w-20 bg-slate-200 rounded"></div>
                        </td>
                        <td class="p-4">
                            <div class="h-3 w-24 bg-slate-200 rounded"></div>
                        </td>
                        <td class="p-4">
                            <div class="h-3 w-16 bg-slate-200 rounded ml-auto"></div>
                        </td>
                        <td class="p-4">
                            <div class="h-3 w-16 bg-slate-200 rounded ml-auto"></div>
                        </td>
                        <td class="p-4 text-center">
                            <div class="h-4 w-8 bg-slate-200 rounded mx-auto"></div>
                        </td>
                        <td class="p-4 text-center">
                            <div class="h-3 w-8 bg-slate-200 rounded mx-auto"></div>
                        </td>
                        <td class="p-4 text-center">
                            <div class="h-4 w-12 bg-slate-200 rounded mx-auto"></div>
                        </td>
                        <td class="p-4 text-right pr-6">
                            <div class="h-4 w-20 bg-slate-200 rounded ml-auto"></div>
                        </td>
                        </tr>
                        @endfor
                </tbody>

                <!-- Table Content -->
                <tbody id="tableContent" class="hidden divide-y divide-slate-100">
                    @forelse($products as $product)
                    @php
                    $stockVal = intval($product->stock ?? 0);
                    $alertLevel = intval($product->alert_stock_level ?? 0);
                    $isLowStock = $stockVal <= $alertLevel; @endphp <tr
                        class="hover:bg-indigo-50/30 transition-colors data-row"
                        data-stock-status="{{ $isLowStock ? 'LOW' : 'GOOD' }}">

                        @if(auth()->check() && auth()->user()->email === 'gaihrenirmal2021@gmail.com')
                        <td class="py-3 px-3 text-center">
                            <input type="checkbox" value="{{ $product->id }}" onchange="updateBulkDeleteState()"
                                class="product-select-checkbox rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                        </td>
                        @endif

                        <td class="py-3 px-4 text-center text-slate-400 font-mono text-[11px]">
                            {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                        </td>

                        <td
                            class="py-3 px-4 font-bold text-slate-900 hover:text-[var(--dash-indigo)] transition-colors">
                            {{ $product->name }}
                        </td>

                        <td class="py-3 px-4 font-mono text-slate-600">
                            <span class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md text-[11px]">
                                {{ $product->item_code ?? 'N/A' }}
                            </span>
                        </td>

                        <td class="py-3 px-4 text-slate-500 text-[11px]">
                            {{ $product->specs ?? '—' }}
                        </td>

                        <td class="py-3 px-4 font-medium text-slate-600">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </td>

                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-700">
                            Rs. {{ number_format($product->purchase_cost ?? 0, 2) }}
                        </td>

                        <td class="py-3 px-4 text-right font-mono font-extrabold text-slate-900">
                            Rs. {{ number_format($product->selling_price ?? 0, 2) }}
                        </td>

                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 uppercase border border-slate-200">
                                {{ $product->unit ?? 'Pcs' }}
                            </span>
                        </td>

                        <td class="py-3 px-4 text-center font-mono font-bold text-slate-800">
                            {{ number_format($stockVal) }}
                        </td>

                        <td class="py-3 px-4 text-center">
                            @if($isLowStock)
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center gap-1 w-max mx-auto">
                                    <i class="fa-solid fa-circle-exclamation text-[9px]"></i> Low Stock
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center gap-1 w-max mx-auto">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> In Stock
                                </span>
                            @endif
                        </td>

                        <td class="py-3 px-4 text-right pr-6">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.inventory.create', $product->id) }}" 
                                   class="p-1.5 text-[var(--dash-green)] hover:bg-emerald-50 rounded-lg transition-colors" 
                                   title="Add Stock">
                                    <i class="fa-solid fa-circle-plus text-base"></i>
                                </a>

                                @can('update', $product)
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                    title="Edit Product">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                @endcan

                                @can('delete', $product)
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                        title="Delete Product">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
                                <p class="text-sm font-semibold">No products found in the catalog.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($products->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $products->links() }}
        </div>
        @endif

    </div>
</div>

<!-- Scripts for Search, Table Filtering, Skeleton & Bulk Selection -->
<script>
    // Skeleton hide on load
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            const skeleton = document.getElementById('skeletonRows');
            const content = document.getElementById('tableContent');
            if (skeleton) skeleton.classList.add('hidden');
            if (content) content.classList.remove('hidden');
        }, 150);
    });

    // Quick Search & Status Filter Logic
    function filterTable() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const statusValue = document.getElementById('stockFilter').value;
        const rows = document.querySelectorAll('#tableContent .data-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const stockStatus = row.getAttribute('data-stock-status');

            const matchesSearch = text.includes(searchValue);
            const matchesStatus = (statusValue === 'ALL') || (stockStatus === statusValue);

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countDisplay = document.getElementById('filteredCount');
        if (countDisplay) {
            countDisplay.innerText = `Showing ${visibleCount} matching item(s) on this page`;
        }
    }

    // Bulk Delete Checkbox Controls
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.product-select-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
        updateBulkDeleteState();
    }

    function deselectAllCheckboxes() {
        const master = document.getElementById('selectAllCheckbox');
        if (master) master.checked = false;
        
        const checkboxes = document.querySelectorAll('.product-select-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        updateBulkDeleteState();
    }

    function updateBulkDeleteState() {
        const selected = document.querySelectorAll('.product-select-checkbox:checked');
        const count = selected.length;

        const countDisplay = document.getElementById('selectedCountDisplay');
        const quickCountDisplay = document.getElementById('quickDeleteCount');
        const bulkBar = document.getElementById('bulkDeleteBar');
        const quickBtn = document.getElementById('bulkDeleteQuickBtn');

        if (countDisplay) countDisplay.innerText = count;
        if (quickCountDisplay) quickCountDisplay.innerText = count;

        if (count > 0) {
            if (bulkBar) bulkBar.classList.remove('hidden');
            if (quickBtn) {
                quickBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                quickBtn.removeAttribute('disabled');
            }
        } else {
            if (bulkBar) bulkBar.classList.add('hidden');
            if (quickBtn) {
                quickBtn.classList.add('opacity-50', 'cursor-not-allowed');
                quickBtn.setAttribute('disabled', 'true');
            }
        }
    }

    function triggerBulkDeleteSubmit() {
        const selected = document.querySelectorAll('.product-select-checkbox:checked');
        if (selected.length === 0) return;

        if (confirm(`Are you sure you want to delete ${selected.length} selected product(s)? This action cannot be undone.`)) {
            const container = document.getElementById('bulkDeleteInputsContainer');
            if (!container) return;

            container.innerHTML = '';
            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });

            document.getElementById('bulkDeleteForm').submit();
        }
    }
</script>
@endsection