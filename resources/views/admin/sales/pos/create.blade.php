@extends('layouts.admin')

@section('title', 'POS Billing Terminal')
@section('panel_title', 'Point-of-Sale Live Billing Interface')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start font-sans antialiased text-slate-800" id="pos-root">

    {{-- LEFT: Product Grid (3/5 width) --}}
    <div class="lg:col-span-3 space-y-4">

        {{-- Search + Category Filter Bar --}}
        <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="product-search" placeholder="Search by name or SKU..." autocomplete="off"
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white rounded-lg transition-all">
            </div>

            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 scrollbar-none">
                <button type="button"
                    class="category-filter-pill px-3 py-1.5 bg-blue-600 text-white rounded-full text-[11px] font-bold uppercase tracking-wider transition-colors whitespace-nowrap"
                    data-category="all">All Products</button>
                @isset($categories)
                    @foreach($categories as $category)
                    <button type="button"
                        class="category-filter-pill px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-full text-[11px] font-bold uppercase tracking-wider transition-colors whitespace-nowrap"
                        data-category="{{ strtolower($category->name) }}">{{ $category->name }}</button>
                    @endforeach
                @endisset
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-px bg-slate-100 max-h-[68vh] overflow-y-auto" id="product-grid">
                @forelse($products as $product)
                @php
                    $isOutOfStock = $product->initial_stock <= 0;
                    $isLow = $product->initial_stock <= ($product->alert_stock_level ?? 5);
                @endphp
                <button type="button"
                    class="product-list-row group relative bg-white text-left p-3 flex flex-col gap-2 transition-colors {{ $isOutOfStock ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-50/60 cursor-pointer' }}"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->selling_price }}"
                    data-initial-stock="{{ $product->initial_stock }}"
                    data-unit="{{ $product->inventory_unit }}"
                    data-category="{{ strtolower($product->category) }}"
                    data-alert-level="{{ $product->alert_stock_level ?? 5 }}"
                    {{ $isOutOfStock ? 'disabled' : '' }}>

                    <div class="flex items-start justify-between gap-2">
                        <span class="font-semibold text-slate-800 text-[12.5px] leading-snug line-clamp-2">{{ $product->name }}</span>
                        <span class="stock-badge shrink-0 font-mono text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ !$isLow ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                            {{ floatval($product->initial_stock) }} {{ $product->inventory_unit === 'piece' ? 'pc' : 'kg' }}
                        </span>
                    </div>

                    <div class="flex items-end justify-between mt-auto">
                        <span class="font-mono text-blue-600 font-bold text-sm">NPR {{ number_format($product->selling_price, 2) }}</span>
                        <span class="text-[10px] text-slate-400 font-medium">/ {{ $product->inventory_unit }}</span>
                    </div>

                    <span class="absolute inset-0 flex items-center justify-center bg-blue-600/90 text-white text-[11px] font-bold uppercase tracking-wider opacity-0 group-hover:opacity-100 transition-opacity {{ $isOutOfStock ? '!hidden' : '' }}">
                        <i class="fa-solid fa-cart-plus mr-1.5"></i> Add to order
                    </span>
                </button>
                @empty
                <div class="col-span-full text-center py-16 text-slate-400">
                    <i class="fa-solid fa-boxes-stacked text-3xl text-slate-200 block mb-2"></i>
                    No warehouse items found.
                </div>
                @endforelse
            </div>
            <p id="product-empty-filter" class="hidden text-center py-10 text-slate-400 text-xs">
                <i class="fa-solid fa-magnifying-glass text-2xl text-slate-200 block mb-2"></i>
                No products match your search.
            </p>
        </div>
    </div>

    {{-- RIGHT: Order Panel (2/5 width) --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col max-h-[calc(100vh-120px)] lg:col-span-2 sticky top-6 overflow-hidden"
        id="billing-ledger-card">

        {{-- Header --}}
        <div class="border-b border-slate-200 px-4 py-3 flex items-center justify-between bg-slate-50/70 shrink-0">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-receipt text-blue-600 text-xs"></i>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Current Order</h3>
                <span id="cart-badge-count"
                    class="bg-blue-600 text-white text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">0</span>
            </div>
            <button type="button" id="clear-cart-btn"
                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hover:text-red-600 transition-colors bg-transparent border-none outline-none cursor-pointer disabled:opacity-30 disabled:pointer-events-none" disabled>
                <i class="fa-solid fa-trash-can mr-1"></i>Clear
            </button>
        </div>

        {{-- Scrollable body: customer, supplier, cart, payment, totals --}}
        <div class="flex-1 overflow-y-auto px-4" id="pos-checkout-form">
            @csrf

            {{-- Transaction date --}}
            <div class="pt-3">
                <label class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-1.5">Transaction Date (BS)</label>
                <input type="text" id="transaction-date"
                    value="{{ $currentNepaliDate }}"
                    placeholder="2082-03-28"
                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-md text-xs font-mono text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
            </div>

            {{-- Step 1: Customer --}}
            <div class="space-y-1 relative pt-4" id="customer-field-wrapper">
                <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="flex items-center justify-center w-4 h-4 rounded-full bg-slate-800 text-white text-[9px]">1</span>
                    Customer <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-[11px] pointer-events-none"></i>
                    <input type="text" id="customer-search-input" autocomplete="off"
                           placeholder="Search customer by name or phone..."
                           class="customer-input w-full pl-8 pr-8 py-2 border border-slate-200 text-xs rounded-md bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-shadow">
                    <button type="button" id="customer-clear-btn"
                            class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-slate-300 hover:text-red-500 transition-colors bg-transparent border-none outline-none cursor-pointer">
                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                    </button>
                </div>
                <div id="customer-dropdown"
                     class="hidden absolute z-30 mt-1 w-full max-h-52 overflow-y-auto bg-white border border-slate-200 rounded-md shadow-lg divide-y divide-slate-50">
                    {{-- populated by JS --}}
                </div>
                <input type="hidden" id="selected-customer-id" value="">
                <p id="customer-selected-label" class="hidden text-[10px] text-emerald-600 font-semibold mt-1">
                    <i class="fa-solid fa-circle-check"></i> <span></span>
                </p>
                <p id="customer-error-label" class="hidden text-[10px] text-red-600 font-semibold mt-1">
                    <i class="fa-solid fa-circle-exclamation"></i> <span>Please select a customer to continue.</span>
                </p>
            </div>

            {{-- Step 2: Supplier (Optional) --}}
            <div class="space-y-1 relative pt-4" id="supplier-field-wrapper">
                <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="flex items-center justify-center w-4 h-4 rounded-full bg-slate-300 text-white text-[9px]">2</span>
                    Supplier <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-truck-field absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-[11px] pointer-events-none"></i>
                    <input type="text" id="supplier-search-input" autocomplete="off"
                        placeholder="Search supplier by name, contact or phone..."
                        class="w-full pl-8 pr-8 py-2 border border-slate-200 text-xs rounded-md bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    <button type="button" id="supplier-clear-btn"
                        class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-slate-300 hover:text-red-500 transition-colors bg-transparent border-none outline-none cursor-pointer">
                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                    </button>
                </div>
                <div id="supplier-dropdown"
                    class="hidden absolute z-30 mt-1 w-full max-h-52 overflow-y-auto bg-white border border-slate-200 rounded-md shadow-lg divide-y divide-slate-50">
                    {{-- populated by JS --}}
                </div>
                <input type="hidden" id="selected-supplier-id" value="">
                <p id="supplier-selected-label" class="hidden text-[10px] text-emerald-600 font-semibold mt-1">
                    <i class="fa-solid fa-circle-check"></i> <span></span>
                </p>
            </div>

            {{-- Step 3: Order Items --}}
            <div class="pt-4 space-y-1.5">
                <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="flex items-center justify-center w-4 h-4 rounded-full bg-slate-300 text-white text-[9px]">3</span>
                    Order Items
                </label>
                <div id="cart-scroll-box" class="divide-y divide-slate-100 border border-slate-100 rounded-md min-h-[110px] max-h-[260px] overflow-y-auto bg-slate-50/40">
                    <div id="cart-empty-state" class="text-center py-9 text-slate-400">
                        <i class="fa-solid fa-cart-shopping text-2xl mb-1.5 text-slate-200 block"></i>
                        <p class="text-[11px] font-medium text-slate-500">No items yet</p>
                        <p class="text-[10px] text-slate-400 mt-0.5 max-w-[200px] mx-auto">Tap a product on the left to add it here.</p>
                    </div>
                </div>
            </div>

            {{-- Step 4: Payment Method --}}
            <div class="space-y-1.5 pt-4">
                <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="flex items-center justify-center w-4 h-4 rounded-full bg-slate-300 text-white text-[9px]">4</span>
                    Payment Method
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                        class="pay-method-btn active-method px-2 py-2 border border-blue-600 bg-slate-800 text-white font-bold text-[10px] uppercase text-center rounded-md transition-all outline-none"
                        data-method="Cash"><i class="fa-solid fa-money-bill-wave mr-1"></i>Cash</button>
                    <button type="button"
                        class="pay-method-btn px-2 py-2 border border-slate-200 bg-white text-slate-600 font-bold text-[10px] uppercase text-center rounded-md transition-all outline-none hover:border-slate-300"
                        data-method="Online Payment"><i class="fa-solid fa-mobile-screen mr-1"></i>Online</button>
                    <button type="button"
                        class="pay-method-btn px-2 py-2 border border-slate-200 bg-white text-slate-600 font-bold text-[10px] uppercase text-center rounded-md transition-all outline-none hover:border-slate-300"
                        data-method="Bank Transfer"><i class="fa-solid fa-building-columns mr-1"></i>Bank</button>
                    <button type="button"
                        class="pay-method-btn px-2 py-2 border border-slate-200 bg-white text-slate-600 font-bold text-[10px] uppercase text-center rounded-md transition-all outline-none hover:border-slate-300"
                        data-method="Credit Sale"><i class="fa-solid fa-file-invoice-dollar mr-1"></i>Credit</button>
                </div>
                <input type="hidden" id="selected-payment-method" value="Cash">
            </div>

            <div id="credit-payment-box" class="p-3 bg-amber-50 border border-amber-200 rounded-md hidden mt-3">
                <label class="text-[10px] font-bold text-amber-800 uppercase block mb-1">Advance Paid Amount (NPR)</label>
                <input type="number" id="credit-paid-input" step="0.01" min="0" value="0.00"
                    class="w-full px-2 py-1.5 bg-white border border-amber-200 font-mono text-xs rounded-md text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-100">
                <p class="text-[10px] text-amber-700 mt-1">
                    Remaining balance due: <span id="credit-remaining-lbl" class="font-bold font-mono">NPR 0.00</span>
                </p>
            </div>

            {{-- VAT + Discount --}}
            <div class="flex items-center justify-between p-2.5 border border-slate-200 rounded-md bg-slate-50/50 mt-3">
                <span class="text-[10px] font-bold text-slate-700 uppercase">Include 13% VAT</span>
                <label class="relative inline-flex items-center cursor-pointer select-none" for="vat-toggle">
                    <input type="checkbox" id="vat-toggle" checked class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                    </div>
                </label>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-3">
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-slate-500 uppercase block">Discount (NPR)</label>
                    <input type="number" id="discount-input" step="0.01" min="0" value="0.00"
                        class="w-full px-2 py-1.5 bg-white border border-slate-200 font-mono text-xs rounded-md text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    <p id="discount-error-label" class="hidden text-[10px] text-red-600 font-semibold">Discount can't exceed subtotal.</p>
                </div>
                <div id="paid-amount-wrapper" class="space-y-1">
                    <label class="text-[10px] font-semibold text-slate-500 uppercase block">Paid Amount (NPR)</label>
                    <input type="number" id="paid-amount-input" step="0.01" min="0" value="0.00"
                        class="w-full px-2 py-1.5 bg-white border border-slate-200 font-mono text-xs rounded-md text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                </div>
            </div>

            <div class="space-y-1 mt-3 pb-3">
                <label class="text-[10px] font-semibold text-slate-500 uppercase block">Remarks</label>
                <input type="text" id="remarks-input" placeholder="Optional notes..."
                    class="w-full px-2 py-1.5 bg-white border border-slate-200 text-xs rounded-md text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
            </div>
        </div>

        {{-- Sticky footer: totals + submit (always visible, real-time) --}}
        <div class="border-t border-slate-200 bg-white px-4 pt-3 pb-4 space-y-2 shrink-0">
            <div class="space-y-1 font-medium text-[11px]">
                <div class="flex justify-between items-center text-slate-400">
                    <span>Subtotal</span>
                    <span class="font-mono text-slate-700" id="summary-subtotal">NPR 0.00</span>
                </div>
                <div class="flex justify-between items-center text-slate-400">
                    <span>Taxable</span>
                    <span class="font-mono text-slate-700" id="summary-taxable">NPR 0.00</span>
                </div>
                <div class="flex justify-between items-center text-slate-400">
                    <span>VAT 13%</span>
                    <span class="font-mono text-slate-700" id="summary-vat">NPR 0.00</span>
                </div>
                <div class="flex justify-between items-center text-sm pt-1.5 border-t border-dashed border-slate-200">
                    <span class="font-bold text-slate-800 uppercase text-[11px]">Grand Total</span>
                    <span class="font-mono font-bold text-blue-600 text-base" id="summary-grandtotal">NPR 0.00</span>
                </div>
            </div>

            <div id="checkout-error-box" class="hidden p-2.5 bg-red-50 border border-red-200 rounded-md">
                <p class="text-[11px] text-red-700 font-medium flex items-start gap-1.5" id="checkout-error-msg">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span></span>
                </p>
            </div>

            <button type="button" id="checkout-submit-btn" disabled
                class="w-full py-3 bg-blue-600 text-white text-xs font-bold uppercase rounded-lg tracking-wider shadow-sm hover:bg-blue-700 active:scale-[0.99] transition-all disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2 outline-none border-none cursor-pointer">
                <i class="fa-solid fa-lock"></i> <span id="checkout-btn-label">Add items to continue</span>
            </button>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="pos-toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-full max-w-sm pointer-events-none"></div>

{{-- Data for the searchable fields below. Already deduped by phone number and pre-built as plain arrays in the controller. --}}
<script>
    window.CUSTOMERS_DATA = @json($customerOptions);
    window.SUPPLIERS_DATA = @json($supplierOptions);
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ---------------------------------------------------------------
    // Toast notifications (replaces alert() everywhere)
    // ---------------------------------------------------------------
    const toastContainer = document.getElementById('pos-toast-container');
    const TOAST_STYLES = {
        success: { icon: 'fa-circle-check', color: 'text-emerald-500', bar: 'bg-emerald-500' },
        warning: { icon: 'fa-triangle-exclamation', color: 'text-amber-500', bar: 'bg-amber-500' },
        error:   { icon: 'fa-circle-exclamation', color: 'text-red-500', bar: 'bg-red-500' },
        info:    { icon: 'fa-circle-info', color: 'text-blue-500', bar: 'bg-blue-500' },
    };

    function showToast(message, type = 'info', title = null, durationMs = 3500) {
        const style = TOAST_STYLES[type] || TOAST_STYLES.info;
        const id = 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        const defaultTitles = { success: 'Success', warning: 'Heads up', error: 'Error', info: 'Notice' };
        const heading = title || defaultTitles[type];

        const el = document.createElement('div');
        el.id = id;
        el.className = 'pointer-events-auto relative flex items-start bg-white p-4 pb-5 shadow-xl rounded-lg border border-slate-100 transition-all duration-500 ease-out translate-x-full opacity-0 overflow-hidden';
        el.innerHTML = `
            <div class="flex-shrink-0 ${style.color} mr-3">
                <i class="fa-solid ${style.icon} text-base"></i>
            </div>
            <div class="flex-1 pt-0.5">
                <h3 class="text-sm font-bold text-slate-800">${heading}</h3>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">${message}</p>
            </div>
            <button class="toast-dismiss text-slate-300 hover:text-slate-400 ml-4 transition-colors bg-transparent border-none outline-none cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
            <div class="toast-bar absolute bottom-0 left-0 h-1 ${style.bar} w-full origin-left transition-transform ease-linear scale-x-0"></div>
        `;
        toastContainer.appendChild(el);

        const bar = el.querySelector('.toast-bar');
        bar.style.transitionDuration = durationMs + 'ms';

        requestAnimationFrame(() => {
            el.classList.remove('translate-x-full', 'opacity-0');
            el.classList.add('translate-x-0', 'opacity-100');
            requestAnimationFrame(() => bar.classList.replace('scale-x-0', 'scale-x-100'));
        });

        function dismiss() {
            el.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => el.remove(), 400);
        }
        el.querySelector('.toast-dismiss').addEventListener('click', dismiss);

        const timer = setTimeout(dismiss, durationMs);
        return { dismiss: () => { clearTimeout(timer); dismiss(); } };
    }

    // ---------------------------------------------------------------
    // State
    // ---------------------------------------------------------------
    let cart = [];
    let autoSyncPay = true;
    const initialProductsData = {}; // productId -> static product info
    const currentStockLevels = {};  // productId -> stock remaining after current cart

    // ---------------------------------------------------------------
    // Element refs
    // ---------------------------------------------------------------
    const searchInput = document.getElementById('product-search');
    const productGrid = document.getElementById('product-grid');
    const productEmptyFilter = document.getElementById('product-empty-filter');
    const filterPills = document.querySelectorAll('.category-filter-pill');
    const cartScrollBox = document.getElementById('cart-scroll-box');
    const cartEmptyState = document.getElementById('cart-empty-state');
    const cartBadgeCount = document.getElementById('cart-badge-count');
    const clearCartBtn = document.getElementById('clear-cart-btn');
    const vatToggle = document.getElementById('vat-toggle');
    const discountInput = document.getElementById('discount-input');
    const discountErrorLbl = document.getElementById('discount-error-label');
    const paidAmtInput = document.getElementById('paid-amount-input');
    const paidAmtWrapper = document.getElementById('paid-amount-wrapper');
    const creditPaidInput = document.getElementById('credit-paid-input');
    const creditRemaining = document.getElementById('credit-remaining-lbl');
    const creditBox = document.getElementById('credit-payment-box');
    const payMethodBtns = document.querySelectorAll('.pay-method-btn');
    const hiddenPayInput = document.getElementById('selected-payment-method');
    const submitBtn = document.getElementById('checkout-submit-btn');
    const checkoutBtnLabel = document.getElementById('checkout-btn-label');
    const errorBox = document.getElementById('checkout-error-box');
    const errorMsg = document.getElementById('checkout-error-msg').querySelector('span');
    const lblSubtotal = document.getElementById('summary-subtotal');
    const lblTaxable = document.getElementById('summary-taxable');
    const lblVat = document.getElementById('summary-vat');
    const lblGrandTotal = document.getElementById('summary-grandtotal');
    const csrfToken = document.querySelector('input[name="_token"]').value;
    const checkoutUrl = "{{ route('admin.sales.pos.store') }}";

    // Customer searchable select
    const customerInput = document.getElementById('customer-search-input');
    const customerDropdown = document.getElementById('customer-dropdown');
    const customerHiddenId = document.getElementById('selected-customer-id');
    const customerClearBtn = document.getElementById('customer-clear-btn');
    const customerSelectedLbl = document.getElementById('customer-selected-label');
    const customerErrorLbl = document.getElementById('customer-error-label');
    let selectedCustomerId = null;

    // Supplier searchable select
    const suppliersData = window.SUPPLIERS_DATA || [];
    const supplierInput = document.getElementById('supplier-search-input');
    const supplierDropdown = document.getElementById('supplier-dropdown');
    const supplierHiddenId = document.getElementById('selected-supplier-id');
    const supplierClearBtn = document.getElementById('supplier-clear-btn');
    const supplierSelectedLbl = document.getElementById('supplier-selected-label');

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------
    function escapeHtml(str) {
        return (str ?? '').toString()
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showFormError(msg) {
        errorMsg.textContent = msg;
        errorBox.classList.remove('hidden');
    }

    function hideFormError() {
        errorBox.classList.add('hidden');
        errorMsg.textContent = '';
    }

    function resetSubmitBtn() {
        submitBtn.innerHTML = '<i class="fa-solid fa-lock"></i> <span id="checkout-btn-label"></span>';
        updateSubmitState();
    }

    // Re-evaluate, in real time, whether checkout is allowed and why not.
    function updateSubmitState() {
        const label = document.getElementById('checkout-btn-label') || checkoutBtnLabel;
        let reason = null;

        if (cart.length === 0) {
            reason = 'Add items to continue';
        } else if (!selectedCustomerId) {
            reason = 'Select a customer to continue';
        } else if (parseFloat(discountInput.value) > parseFloat(lblSubtotal.dataset.raw || 0)) {
            reason = 'Fix the discount amount';
        }

        if (reason) {
            submitBtn.disabled = true;
            label.textContent = reason;
            submitBtn.querySelector('i').className = 'fa-solid fa-lock';
        } else {
            submitBtn.disabled = false;
            label.textContent = 'Process Checkout';
            submitBtn.querySelector('i').className = 'fa-solid fa-receipt';
        }
    }

    function calculateTotals() {
        let subtotal = 0;

        cart.forEach((item, index) => {
            const weight = parseFloat(item.quantity_kg) + (parseFloat(item.quantity_gm) / 1000);
            const itemTotal = parseFloat(item.rate_per_kg) * weight;
            subtotal += itemTotal;

            const totalLabel = cartScrollBox.querySelectorAll('.item-total-lbl')[index];
            if (totalLabel) totalLabel.textContent = `NPR ${itemTotal.toFixed(2)}`;
        });

        const discount = parseFloat(discountInput.value) || 0;
        const discountInvalid = discount > subtotal;
        discountErrorLbl.classList.toggle('hidden', !discountInvalid);
        discountInput.classList.toggle('border-red-400', discountInvalid);
        discountInput.classList.toggle('ring-2', discountInvalid);
        discountInput.classList.toggle('ring-red-100', discountInvalid);

        const taxable = Math.max(0, subtotal - discount);
        const vat = vatToggle.checked ? parseFloat((taxable * 0.13).toFixed(2)) : 0;
        const grandTotal = parseFloat((taxable + vat).toFixed(2));

        lblSubtotal.textContent = `NPR ${subtotal.toFixed(2)}`;
        lblSubtotal.dataset.raw = subtotal;
        lblTaxable.textContent = `NPR ${taxable.toFixed(2)}`;
        lblVat.textContent = `NPR ${vat.toFixed(2)}`;
        lblGrandTotal.textContent = `NPR ${grandTotal.toFixed(2)}`;

        if (hiddenPayInput.value === 'Credit Sale') {
            const advancePaid = parseFloat(creditPaidInput.value) || 0;
            const remaining = Math.max(0, grandTotal - advancePaid);
            creditRemaining.textContent = `NPR ${remaining.toFixed(2)}`;
        } else if (autoSyncPay) {
            paidAmtInput.value = grandTotal.toFixed(2);
        }

        updateSubmitState();
    }

    // ---------------------------------------------------------------
    // Product grid: populate data, search + filter
    // ---------------------------------------------------------------
    let productRows = Array.from(document.querySelectorAll('.product-list-row'));

    productRows.forEach(row => {
        const productId = parseInt(row.dataset.id);
        initialProductsData[productId] = {
            id: productId,
            name: row.dataset.name,
            selling_price: parseFloat(row.dataset.price),
            initial_stock: parseFloat(row.dataset.initialStock),
            inventory_unit: row.dataset.unit,
            alert_stock_level: parseFloat(row.dataset.alertLevel),
        };
        currentStockLevels[productId] = parseFloat(row.dataset.initialStock);
    });

    function filterProducts() {
        const query = searchInput.value.toLowerCase().trim();
        const activePill = document.querySelector('.category-filter-pill.bg-blue-600');
        const targetCategory = activePill ? activePill.dataset.category : 'all';
        let visibleCount = 0;

        productRows.forEach(row => {
            const matchesName = row.dataset.name.toLowerCase().includes(query);
            const matchesCat = targetCategory === 'all' || row.dataset.category === targetCategory;
            const visible = matchesName && matchesCat;
            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        productEmptyFilter.classList.toggle('hidden', visibleCount !== 0);
        productGrid.classList.toggle('hidden', visibleCount === 0);
    }

    if (searchInput) searchInput.addEventListener('input', filterProducts);
    filterPills.forEach(pill => {
        pill.addEventListener('click', function() {
            filterPills.forEach(p => {
                p.classList.remove('bg-blue-600', 'text-white');
                p.classList.add('bg-slate-100', 'hover:bg-slate-200', 'text-slate-600');
            });
            this.classList.remove('bg-slate-100', 'hover:bg-slate-200', 'text-slate-600');
            this.classList.add('bg-blue-600', 'text-white');
            filterProducts();
        });
    });

    function updateProductStockDisplay() {
        const cartQuantities = {};
        cart.forEach(item => {
            const totalQuantity = parseFloat(item.quantity_kg) + (parseFloat(item.quantity_gm) / 1000);
            cartQuantities[item.id] = (cartQuantities[item.id] || 0) + totalQuantity;
        });

        productRows.forEach(row => {
            const productId = parseInt(row.dataset.id);
            const info = initialProductsData[productId];
            const quantityInCart = cartQuantities[productId] || 0;
            const effectiveStock = info.initial_stock - quantityInCart;
            currentStockLevels[productId] = effectiveStock;

            const badge = row.querySelector('.stock-badge');
            if (!badge) return;

            const unitLabel = info.inventory_unit === 'piece' ? 'pc' : 'kg';
            badge.textContent = `${effectiveStock.toFixed(info.inventory_unit === 'piece' ? 0 : 2)} ${unitLabel}`;

            badge.classList.remove('bg-emerald-50', 'text-emerald-600', 'bg-red-50', 'text-red-600');
            row.classList.remove('opacity-50', 'cursor-not-allowed');
            row.disabled = false;

            if (effectiveStock <= 0) {
                badge.classList.add('bg-red-50', 'text-red-600');
                row.classList.add('opacity-50', 'cursor-not-allowed');
                row.disabled = true;
            } else if (effectiveStock <= info.alert_stock_level) {
                badge.classList.add('bg-red-50', 'text-red-600');
            } else {
                badge.classList.add('bg-emerald-50', 'text-emerald-600');
            }
        });
    }

    productGrid.addEventListener('click', function(event) {
        const row = event.target.closest('.product-list-row');
        if (!row || row.disabled) return;

        const id = parseInt(row.dataset.id);
        const info = initialProductsData[id];
        const availableStock = currentStockLevels[id];

        if (availableStock <= 0) {
            showToast(`${info.name} is out of stock.`, 'warning');
            return;
        }

        const existing = cart.find(item => item.id === id);
        if (existing) {
            const currentWeight = parseFloat(existing.quantity_kg) + (parseFloat(existing.quantity_gm) / 1000);
            const increment = info.inventory_unit === 'piece' ? 1 : 1;
            if (currentWeight + increment > availableStock + currentWeight) {
                showToast(`Only ${availableStock.toFixed(2)} ${info.inventory_unit} left for ${info.name}.`, 'warning');
                return;
            }
            existing.quantity_kg += increment;
        } else {
            cart.push({
                id,
                name: info.name,
                rate_per_kg: info.selling_price,
                quantity_kg: 1,
                quantity_gm: 0,
                unit: info.inventory_unit,
            });
        }

        renderCart();
        showToast(`${info.name} added to order.`, 'success', null, 1600);
    });

    // ---------------------------------------------------------------
    // Cart rendering
    // ---------------------------------------------------------------
    function renderCart() {
        cartScrollBox.querySelectorAll('.cart-item-row').forEach(r => r.remove());

        clearCartBtn.disabled = cart.length === 0;

        if (cart.length === 0) {
            cartEmptyState.style.display = '';
            cartBadgeCount.textContent = '0';
            autoSyncPay = true;
            lblSubtotal.textContent = 'NPR 0.00';
            lblSubtotal.dataset.raw = 0;
            lblTaxable.textContent = 'NPR 0.00';
            lblVat.textContent = 'NPR 0.00';
            lblGrandTotal.textContent = 'NPR 0.00';
            paidAmtInput.value = '0.00';
            updateProductStockDisplay();
            updateSubmitState();
            return;
        }

        cartEmptyState.style.display = 'none';

        let totalQtyDisplay = 0;

        cart.forEach((item, index) => {
            totalQtyDisplay += 1;

            const row = document.createElement('div');
            row.className = 'cart-item-row py-2.5 px-2.5 flex items-center justify-between gap-1 text-xs text-slate-700 bg-white';

            const isPieceUnit = item.unit === 'piece';
            const qtyGmDisplay = isPieceUnit ? 'hidden' : 'flex items-center gap-0.5';
            const qtyGmValue = isPieceUnit ? 0 : item.quantity_gm;
            const qtyGmLabel = isPieceUnit ? '' : '<span class="text-[9px] text-slate-400">gm</span>';

            row.innerHTML = `
                <div class="flex-1 min-w-0">
                    <span class="font-bold uppercase text-slate-800 block truncate text-[12px]">${escapeHtml(item.name)}</span>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[9px] text-slate-400">Rate</span>
                        <input type="number" step="0.01" class="item-rate-field w-14 text-center font-mono py-0.5 border border-slate-200 rounded text-[10px]" data-index="${index}" value="${parseFloat(item.rate_per_kg).toFixed(2)}">
                    </div>
                </div>

                <div class="flex items-center gap-0.5 flex-shrink-0">
                    <button type="button" class="qty-minus-btn text-slate-400 hover:text-slate-700 w-4 h-4 flex items-center justify-center border border-slate-200 rounded" data-type="kg" data-index="${index}"><i class="fa-solid fa-minus text-[8px]"></i></button>
                    <input type="number" class="qty-kg-field w-9 text-center font-mono py-0.5 border border-slate-200 rounded text-[11px]" data-index="${index}" min="0" value="${item.quantity_kg}">
                    <button type="button" class="qty-plus-btn text-slate-400 hover:text-slate-700 w-4 h-4 flex items-center justify-center border border-slate-200 rounded" data-type="kg" data-index="${index}"><i class="fa-solid fa-plus text-[8px]"></i></button>
                    <span class="text-[9px] text-slate-400">${isPieceUnit ? 'pc' : 'kg'}</span>

                    <span class="${qtyGmDisplay}">
                        <button type="button" class="qty-minus-btn text-slate-400 hover:text-slate-700 w-4 h-4 flex items-center justify-center border border-slate-200 rounded ml-1" data-type="gm" data-index="${index}"><i class="fa-solid fa-minus text-[8px]"></i></button>
                        <input type="number" class="qty-gm-field w-9 text-center font-mono py-0.5 border border-slate-200 rounded text-[11px]" data-index="${index}" min="0" max="999" value="${qtyGmValue}">
                        <button type="button" class="qty-plus-btn text-slate-400 hover:text-slate-700 w-4 h-4 flex items-center justify-center border border-slate-200 rounded" data-type="gm" data-index="${index}"><i class="fa-solid fa-plus text-[8px]"></i></button>
                        ${qtyGmLabel}
                    </span>
                </div>

                <div class="text-right min-w-[72px]">
                    <span class="font-mono font-bold block text-slate-900 item-total-lbl text-[11px]">NPR 0.00</span>
                    <button type="button" class="item-remove text-[9px] text-red-500 uppercase font-semibold hover:underline" data-index="${index}">Remove</button>
                </div>
            `;

            cartScrollBox.appendChild(row);
        });

        cartBadgeCount.textContent = totalQtyDisplay;
        bindCartRowEvents();
        calculateTotals();
        updateProductStockDisplay();
    }

    function clampQuantity(item, newKg, newGm, availableStock) {
        // availableStock already reflects stock net of THIS item's current cart quantity,
        // so the ceiling for the new total weight is: current cart weight + remaining stock.
        const currentWeight = parseFloat(item.quantity_kg) + (parseFloat(item.quantity_gm) / 1000);
        const ceiling = availableStock + currentWeight;
        const requestedWeight = newKg + (newGm / 1000);

        if (requestedWeight > ceiling) {
            showToast(`Only ${ceiling.toFixed(2)} ${item.unit} available for ${item.name}. Adjusted to max.`, 'warning');
            newKg = Math.floor(ceiling);
            newGm = Math.round((ceiling - newKg) * 1000);
        }
        return { kg: Math.max(0, newKg), gm: Math.max(0, newGm) };
    }

    function bindCartRowEvents() {
        cartScrollBox.querySelectorAll('.qty-minus-btn, .qty-plus-btn, .qty-kg-field, .qty-gm-field, .item-rate-field, .item-remove')
            .forEach(el => el.replaceWith(el.cloneNode(true)));

        cartScrollBox.querySelectorAll('.qty-minus-btn, .qty-plus-btn').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                const type = this.dataset.type;
                const item = cart[index];
                let tempKg = parseFloat(item.quantity_kg);
                let tempGm = parseFloat(item.quantity_gm);
                const availableStock = currentStockLevels[item.id];

                if (item.unit === 'piece' && type === 'gm') return;

                const isPlus = this.classList.contains('qty-plus-btn');

                if (type === 'kg') {
                    tempKg += isPlus ? 1 : -1;
                } else {
                    tempGm += isPlus ? 100 : -100;
                    if (tempGm >= 1000) {
                        tempKg += Math.floor(tempGm / 1000);
                        tempGm %= 1000;
                    } else if (tempGm < 0) {
                        if (tempKg > 0) { tempKg -= 1; tempGm += 1000; }
                        else { tempGm = 0; }
                    }
                }

                tempKg = Math.max(0, tempKg);
                tempGm = Math.max(0, tempGm);

                const clamped = clampQuantity(item, tempKg, tempGm, availableStock);
                const newTotalWeight = clamped.kg + (clamped.gm / 1000);

                if (newTotalWeight <= 0) {
                    cart.splice(index, 1);
                } else {
                    item.quantity_kg = clamped.kg;
                    item.quantity_gm = clamped.gm;
                }
                renderCart();
            });
        });

        cartScrollBox.querySelectorAll('.qty-kg-field, .qty-gm-field').forEach(input => {
            input.addEventListener('input', function() {
                const index = parseInt(this.dataset.index);
                const item = cart[index];
                const availableStock = currentStockLevels[item.id];

                let newKg = parseFloat(item.quantity_kg);
                let newGm = parseFloat(item.quantity_gm);

                if (this.classList.contains('qty-kg-field')) {
                    newKg = parseFloat(this.value) || 0;
                } else {
                    newGm = parseFloat(this.value) || 0;
                    if (item.unit === 'piece') newGm = 0;
                    if (newGm > 999) { newGm = 999; this.value = 999; }
                }

                newKg = Math.max(0, newKg);
                newGm = Math.max(0, newGm);

                const clamped = clampQuantity(item, newKg, newGm, availableStock);
                const newTotalWeight = clamped.kg + (clamped.gm / 1000);

                if (newTotalWeight <= 0) {
                    cart.splice(index, 1);
                } else {
                    item.quantity_kg = clamped.kg;
                    item.quantity_gm = clamped.gm;
                }
                renderCart();
            });
        });

        cartScrollBox.querySelectorAll('.item-rate-field').forEach(input => {
            input.addEventListener('input', function() {
                const val = parseFloat(this.value);
                cart[parseInt(this.dataset.index)].rate_per_kg = isNaN(val) || val < 0 ? 0 : val;
                calculateTotals();
            });
        });

        cartScrollBox.querySelectorAll('.item-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                cart.splice(parseInt(this.dataset.index), 1);
                renderCart();
            });
        });
    }

    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function() {
            if (cart.length === 0) return;
            cart = [];
            renderCart();
            showToast('Order cleared.', 'info', null, 1600);
        });
    }

    // ---------------------------------------------------------------
    // Payment method + adjustments
    // ---------------------------------------------------------------
    payMethodBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            payMethodBtns.forEach(b => {
                b.classList.remove('bg-slate-800', 'text-white', 'border-blue-600');
                b.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
            });

            this.classList.add('bg-slate-800', 'text-white', 'border-blue-600');
            this.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');

            const method = this.dataset.method;
            hiddenPayInput.value = method;

            if (method === 'Credit Sale') {
                creditBox.classList.remove('hidden');
                paidAmtWrapper.classList.add('opacity-40', 'pointer-events-none');
                paidAmtInput.value = '0.00';
                creditPaidInput.value = '0.00';
                autoSyncPay = false;
            } else {
                creditBox.classList.add('hidden');
                paidAmtWrapper.classList.remove('opacity-40', 'pointer-events-none');
                autoSyncPay = true;
            }

            calculateTotals();
        });
    });

    creditPaidInput.addEventListener('input', calculateTotals);
    discountInput.addEventListener('input', calculateTotals);
    vatToggle.addEventListener('change', calculateTotals);
    paidAmtInput.addEventListener('input', () => autoSyncPay = false);

    // ---------------------------------------------------------------
    // Customer searchable select
    // ---------------------------------------------------------------
    const customersData = window.CUSTOMERS_DATA || [];

    function matchCustomers(query) {
        if (!query) return customersData;
        const q = query.toLowerCase();
        return customersData.filter(c =>
            (c.name || '').toLowerCase().includes(q) ||
            (c.phone_number || '').toLowerCase().includes(q)
        );
    }

    function renderCustomerDropdown(list) {
        if (!list.length) {
            customerDropdown.innerHTML = '<div class="px-3 py-2 text-[11px] text-slate-400">No customer found.</div>';
            customerDropdown.classList.remove('hidden');
            return;
        }

        customerDropdown.innerHTML = list.map(c => `
            <button type="button" class="customer-option w-full text-left px-3 py-2 hover:bg-blue-50 transition-colors"
                data-id="${c.id}" data-name="${escapeHtml(c.name)}">
                <span class="block text-[11px] font-semibold text-slate-800">${escapeHtml(c.name)}</span>
                <span class="block text-[10px] text-slate-400">
                    ${c.phone_number ? escapeHtml(c.phone_number) : 'No phone'}
                    ${c.previous_due > 0 ? ` &middot; <span class="text-amber-600 font-semibold">Due NPR ${parseFloat(c.previous_due).toFixed(2)}</span>` : ''}
                </span>
            </button>
        `).join('');

        customerDropdown.classList.remove('hidden');

        customerDropdown.querySelectorAll('.customer-option').forEach(btn => {
            btn.addEventListener('click', function() {
                selectCustomer(this.dataset.id, this.dataset.name);
            });
        });
    }

    function selectCustomer(id, name) {
        selectedCustomerId = id;
        customerHiddenId.value = id;
        customerInput.value = name;
        customerDropdown.classList.add('hidden');
        customerClearBtn.classList.remove('hidden');
        customerSelectedLbl.classList.remove('hidden');
        customerSelectedLbl.querySelector('span').textContent = 'Selected: ' + name;
        customerErrorLbl.classList.add('hidden');
        customerInput.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
        updateSubmitState();
    }

    function clearCustomer() {
        selectedCustomerId = null;
        customerHiddenId.value = '';
        customerInput.value = '';
        customerClearBtn.classList.add('hidden');
        customerSelectedLbl.classList.add('hidden');
        customerInput.focus();
        updateSubmitState();
    }

    if (customerInput) {
        customerInput.addEventListener('focus', function() {
            renderCustomerDropdown(matchCustomers(this.value.trim()));
        });

        customerInput.addEventListener('input', function() {
            selectedCustomerId = null;
            customerHiddenId.value = '';
            customerClearBtn.classList.add('hidden');
            customerSelectedLbl.classList.add('hidden');
            renderCustomerDropdown(matchCustomers(this.value.trim()));
            updateSubmitState();
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#customer-field-wrapper')) {
                customerDropdown.classList.add('hidden');
            }
        });
    }

    if (customerClearBtn) customerClearBtn.addEventListener('click', clearCustomer);

    // ---------------------------------------------------------------
    // Supplier searchable select (optional)
    // ---------------------------------------------------------------
    function matchSuppliers(query) {
        if (!query) return suppliersData;
        const q = query.toLowerCase();
        return suppliersData.filter(s =>
            (s.name || '').toLowerCase().includes(q) ||
            (s.contact_person || '').toLowerCase().includes(q) ||
            (s.phone || '').toLowerCase().includes(q)
        );
    }

    function renderSupplierDropdown(list) {
        if (!list.length) {
            supplierDropdown.innerHTML = '<div class="px-3 py-2 text-[11px] text-slate-400">No supplier found.</div>';
            supplierDropdown.classList.remove('hidden');
            return;
        }

        supplierDropdown.innerHTML = list.map(s => `
            <button type="button" class="supplier-option w-full text-left px-3 py-2 hover:bg-blue-50 transition-colors"
                data-id="${s.id}" data-name="${escapeHtml(s.name)}">
                <span class="block text-[11px] font-semibold text-slate-800">${escapeHtml(s.name)}</span>
                <span class="block text-[10px] text-slate-400">
                    ${s.contact_person ? escapeHtml(s.contact_person) + ' &middot; ' : ''}${s.phone ? escapeHtml(s.phone) : 'No phone'}
                </span>
            </button>
        `).join('');

        supplierDropdown.classList.remove('hidden');

        supplierDropdown.querySelectorAll('.supplier-option').forEach(btn => {
            btn.addEventListener('click', function() {
                selectSupplier(this.dataset.id, this.dataset.name);
            });
        });
    }

    function selectSupplier(id, name) {
        supplierHiddenId.value = id;
        supplierInput.value = name;
        supplierDropdown.classList.add('hidden');
        supplierClearBtn.classList.remove('hidden');
        supplierSelectedLbl.classList.remove('hidden');
        supplierSelectedLbl.querySelector('span').textContent = 'Linked to: ' + name;
    }

    function clearSupplier() {
        supplierHiddenId.value = '';
        supplierInput.value = '';
        supplierClearBtn.classList.add('hidden');
        supplierSelectedLbl.classList.add('hidden');
        supplierInput.focus();
    }

    if (supplierInput) {
        supplierInput.addEventListener('focus', function() {
            renderSupplierDropdown(matchSuppliers(this.value.trim()));
        });

        supplierInput.addEventListener('input', function() {
            supplierHiddenId.value = '';
            supplierClearBtn.classList.add('hidden');
            supplierSelectedLbl.classList.add('hidden');
            renderSupplierDropdown(matchSuppliers(this.value.trim()));
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#supplier-field-wrapper')) {
                supplierDropdown.classList.add('hidden');
            }
        });
    }

    if (supplierClearBtn) supplierClearBtn.addEventListener('click', clearSupplier);

    // ---------------------------------------------------------------
    // Checkout
    // ---------------------------------------------------------------
    submitBtn.addEventListener('click', function() {
        hideFormError();

        if (cart.length === 0) {
            showFormError('Your order is empty. Add at least one item.');
            return;
        }

        if (!selectedCustomerId) {
            customerErrorLbl.classList.remove('hidden');
            customerInput.classList.add('border-red-400', 'ring-2', 'ring-red-100');
            showFormError('Please select a customer before processing the checkout.');
            customerInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const isCreditSale = hiddenPayInput.value === 'Credit Sale';
        const finalPaid = isCreditSale ? parseFloat(creditPaidInput.value) : parseFloat(paidAmtInput.value);

        const payload = {
            customer_id: selectedCustomerId,
            supplier_id: supplierHiddenId.value || null,
            payment_method: hiddenPayInput.value,
            include_vat: vatToggle.checked ? 1 : 0,
            discount: parseFloat(discountInput.value) || 0,
            paid_amount: finalPaid || 0,
            remarks: document.getElementById('remarks-input').value.trim(),
            transaction_date: document.getElementById('transaction-date').value.trim(),
            items: cart.map(item => ({
                id: item.id,
                rate_per_kg: item.rate_per_kg,
                quantity_kg: parseFloat(item.quantity_kg) || 0,
                quantity_gm: parseFloat(item.quantity_gm) || 0,
            })),
        };

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

        fetch(checkoutUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            })
            .then(async res => {
                const rawText = await res.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    if (res.status === 419) throw new Error('Your session expired. Please refresh the page and try again.');
                    if (res.status === 500) throw new Error('Server error (HTTP 500). Check laravel.log for details.');
                    throw new Error(`Unexpected server response (HTTP ${res.status}).`);
                }
                return { status: res.status, data };
            })
            .then(result => {
                if (result.status === 200 && result.data.success) {
                    showToast(result.data.message || 'Invoice processed successfully!', 'success', 'Invoice saved', 4000);
                    setTimeout(() => { window.location.href = result.data.redirect; }, 1300);
                } else {
                    showFormError(result.data.message || 'Request failed.');
                    resetSubmitBtn();
                }
            })
            .catch(err => {
                showFormError(err.message || 'Request failed.');
                resetSubmitBtn();
            });
    });

    // ---------------------------------------------------------------
    // Init
    // ---------------------------------------------------------------
    updateProductStockDisplay();
    filterProducts();
    renderCart();
});
</script>
@endsection