@extends('layouts.admin')

@section('title', 'POS Billing Terminal')
@section('panel_title', 'Point-of-Sale Live Billing Interface')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start font-sans antialiased text-slate-800">

    {{-- ── LEFT: Product Grid ───────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Search + Category Filter Bar --}}
        <div
            class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:w-72">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="product-search" placeholder="Search by name or SKU..."
                    class="w-full pl-9 pr-4 py-1.5 bg-slate-50 border border-slate-200 text-xs text-slate-700
                           placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white rounded-md transition-all">
            </div>
            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 scrollbar-none">
                <button type="button"
                    class="category-filter-pill px-3 py-1.5 bg-blue-600 text-white rounded-md text-[11px] font-bold uppercase tracking-wider transition-colors"
                    data-category="all">All Products</button>
                <button type="button"
                    class="category-filter-pill px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-md text-[11px] font-bold uppercase tracking-wider transition-colors"
                    data-category="dry yeast">Dry Yeast</button>
            </div>
        </div>

        {{-- Product Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" id="product-grid">
            @forelse($products as $product)
            @php $isOutOfStock = $product->initial_stock <= 0; @endphp <div class="product-card bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:border-blue-500
                        hover:shadow-md transition-all cursor-pointer relative overflow-hidden"
                {{ $isOutOfStock ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"
                data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->selling_price }}"
                data-stock="{{ $product->initial_stock }}" data-unit="{{ $product->inventory_unit }}"
                data-category="{{ strtolower($product->category) }}">

                <div class="flex flex-col h-full justify-between space-y-3">
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide truncate">
                            {{ $product->name }}</h3>
                        <span
                            class="inline-flex items-center mt-1 px-1.5 py-0.5 rounded text-[10px] font-mono bg-slate-100 text-slate-600">
                            Unit: {{ $product->inventory_unit }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase font-medium">Selling Price</span>
                            <span class="text-xs font-bold text-blue-600 font-mono">NPR
                                {{ number_format($product->selling_price, 2) }}</span>
                        </div>
                        <div class="text-right">
                            @php $isLow = $product->initial_stock <= ($product->alert_stock_level ?? 5); @endphp
                                <span
                                    class="text-[10px] font-bold font-mono px-2 py-0.5 rounded {{ !$isLow ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                    {{ floatval($product->initial_stock) }} Left
                                </span>
                        </div>
                    </div>
                </div>
        </div>
        @empty
        <div
            class="col-span-full bg-white border border-slate-200 border-dashed rounded-lg p-12 text-center text-slate-400 text-xs">
            <i class="fa-solid fa-boxes-stacked text-3xl text-slate-200 block mb-2"></i>
            No warehouse items found.
        </div>
        @endforelse
    </div>
</div>

{{-- ── RIGHT: Billing Cart ──────────────────────────────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 sticky top-6 flex flex-col max-h-[calc(100vh-140px)]"
    id="billing-ledger-card">

    {{-- Cart Header --}}
    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Billing Cart</h3>
            <span id="cart-badge-count"
                class="bg-blue-50 text-blue-700 text-[10px] font-mono font-bold px-1.5 py-0.5 rounded border border-blue-100">0</span>
        </div>
        <button type="button" id="clear-cart-btn"
            class="text-[10px] font-bold text-red-500 uppercase tracking-wider hover:text-red-700 transition-colors bg-transparent border-none outline-none cursor-pointer">
            Clear Cart
        </button>
    </div>

    {{-- Transaction Date --}}
    <div class="bg-slate-50 border border-slate-200 rounded-md p-3 mt-2.5">
        <label class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-2">Transaction Date
            (BS)</label>
        <input type="text" id="transaction-date"
            value="{{ \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from(date('Y-m-d'))->toNepaliDate(format: 'Y-m-d') }}"
            placeholder="2082-03-28"
            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm font-mono text-blue-700 focus:outline-none focus:border-blue-500">
    </div>

    {{-- Cart Items Scroll Box --}}
    <div id="cart-scroll-box"
        class="flex-1 overflow-y-auto divide-y divide-slate-100 my-2 pr-1 min-h-[150px] max-h-[300px]">
        <div id="cart-empty-state" class="text-center py-12 text-slate-400">
            <i class="fa-solid fa-cart-shopping text-3xl mb-2 text-slate-200 block"></i>
            <p class="text-[11px] font-medium text-slate-600">No products selected</p>
            <p class="text-[10px] text-slate-400 mt-0.5 max-w-[200px] mx-auto">Click a product card on the left to
                populate invoice items.</p>
        </div>
    </div>

    {{-- Checkout Form --}}
    {{-- NOTE: action is only used as the URL reference; submission is via fetch() in JS --}}
    <div id="pos-checkout-form" class="border-t border-slate-200 pt-3 space-y-3 text-xs text-slate-700">
        @csrf {{-- token read by JS --}}

        {{-- 1. Customer --}}
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider block">1. Customer
                Selection</label>
            <select id="customer-select"
                class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded-md bg-white text-slate-700 focus:outline-none focus:border-blue-500">

                <!-- The placeholder option -->
                <option value="" disabled {{ !isset($selectedCustomer) ? 'selected' : '' }}>
                    Select a customer
                </option>

                @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ $customer->name == 'Walk-in Customer' ? 'selected' : '' }}>
                    {{ $customer->name }} ({{ $customer->phone_number ?? 'No Phone' }})
                </option>
                @endforeach
            </select>
        </div>

        {{-- 2. Payment Method --}}
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider block">2. Payment
                Method</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button"
                    class="pay-method-btn active-method px-2 py-1.5 border border-blue-600 bg-slate-800 text-white font-bold text-[10px] uppercase text-center rounded-md transition-all outline-none"
                    data-method="Cash">Cash</button>
                <button type="button"
                    class="pay-method-btn px-2 py-1.5 border border-slate-200 bg-white text-slate-600 font-bold text-[10px] uppercase text-center rounded-md transition-all outline-none"
                    data-method="Online Payment">Online Payment</button>
                <button type="button"
                    class="pay-method-btn px-2 py-1.5 border border-slate-200 bg-white text-slate-600 font-bold text-[10px] uppercase text-center rounded-md transition-all outline-none"
                    data-method="Bank Transfer">Bank Transfer</button>
                <button type="button"
                    class="pay-method-btn px-2 py-1.5 border border-slate-200 bg-white text-slate-600 font-bold text-[10px] uppercase text-center rounded-md transition-all outline-none"
                    data-method="Credit Sale">Credit Sale</button>
            </div>
            <input type="hidden" id="selected-payment-method" value="Cash">
        </div>

        {{-- Credit Sale Advance Box --}}
        <div id="credit-payment-box" class="p-3 bg-amber-50 border border-amber-200 rounded-md hidden">
            <label class="text-[10px] font-bold text-amber-800 uppercase block mb-1">Advance Paid Amount
                (NPR)</label>
            <input type="number" id="credit-paid-input" step="0.01" min="0" value="0.00"
                class="w-full px-2 py-1.5 bg-white border border-amber-200 font-mono text-xs rounded-md text-amber-900">
            <p class="text-[10px] text-amber-700 mt-1">
                Remaining Balance Due: <span id="credit-remaining-lbl" class="font-bold font-mono">NPR 0.00</span>
            </p>
        </div>

        {{-- VAT Toggle --}}
        <div class="flex items-center justify-between p-2 border border-slate-200 rounded-md bg-slate-50/50">
            <span class="text-[10px] font-bold text-slate-700 uppercase">Include 13% VAT</span>
            <label class="relative inline-flex items-center cursor-pointer select-none">
                <label class="relative inline-flex items-center cursor-pointer select-none" for="vat-toggle">
                    <input type="checkbox" id="vat-toggle" checked class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:border-slate-300 after:border after:rounded-full
                    after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
        </div>

        {{-- Discount + Paid Amount --}}
        <div class="grid grid-cols-2 gap-2">
            <div class="space-y-1">
                <label class="text-[10px] font-semibold text-slate-500 uppercase block">Discount (NPR)</label>
                <input type="number" id="discount-input" step="0.01" min="0" value="0.00" class="w-full px-2 py-1.5 bg-white border border-slate-200 font-mono text-xs rounded-md
                   text-slate-700 focus:outline-none focus:border-blue-500">
            </div>
            <div id="paid-amount-wrapper" class="space-y-1">
                <label class="text-[10px] font-semibold text-slate-500 uppercase block">Paid Amount (NPR)</label>
                <input type="number" id="paid-amount-input" step="0.01" min="0" value="0.00" class="w-full px-2 py-1.5 bg-white border border-slate-200 font-mono text-xs rounded-md
                   text-slate-700 focus:outline-none focus:border-blue-500">
            </div>
        </div>

        {{-- Remarks --}}
        <div class="space-y-1">
            <label class="text-[10px] font-semibold text-slate-500 uppercase block">Transaction Remarks</label>
            <input type="text" id="remarks-input" placeholder="Optional notes..." class="w-full px-2 py-1.5 bg-white border border-slate-200 text-xs rounded-md
               text-slate-700 focus:outline-none focus:border-blue-500">
        </div>

        {{-- Summary Totals --}}
        <div class="pt-2.5 border-t border-slate-200 space-y-1.5 font-medium text-[11px]">
            <div class="flex justify-between items-center text-slate-400">
                <span>Subtotal:</span>
                <span class="font-mono text-slate-700" id="summary-subtotal">NPR 0.00</span>
            </div>
            <div class="flex justify-between items-center text-slate-400">
                <span>Taxable:</span>
                <span class="font-mono text-slate-700" id="summary-taxable">NPR 0.00</span>
            </div>
            <div class="flex justify-between items-center text-slate-400">
                <span>VAT 13%:</span>
                <span class="font-mono text-slate-700" id="summary-vat">NPR 0.00</span>
            </div>
            <div class="flex justify-between items-center text-xs pt-1.5 border-t border-dashed border-slate-200">
                <span class="font-bold text-slate-800 uppercase">Grand Total:</span>
                <span class="font-mono font-bold text-blue-600 text-sm" id="summary-grandtotal">NPR 0.00</span>
            </div>
        </div>

        {{-- Error banner --}}
        <div id="checkout-error-box" class="hidden p-3 bg-red-50 border border-red-200 rounded-md my-2">
            <p class="text-[11px] text-red-700 font-medium" id="checkout-error-msg"></p>
        </div>

        {{-- Submit Button --}}
        <button type="button" id="checkout-submit-btn" disabled class="w-full py-2.5 bg-blue-600 text-white text-xs font-bold uppercase rounded-md tracking-wider
           shadow-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed
           flex items-center justify-center gap-2 outline-none border-none cursor-pointer">
            <i class="fa-solid fa-receipt"></i> Process Secure Checkout
        </button>
    </div>
</div>
</div>
<script>
// Global function exposed to handle manual exit triggers of dynamically spawned toasts
function dismissDynamicAlert(alertId) {
    const alertElement = document.getElementById(alertId);
    if (alertElement) {
        alertElement.classList.remove('translate-x-0', 'opacity-100');
        alertElement.classList.add('translate-x-full', 'opacity-0');

        setTimeout(() => {
            alertElement.remove();
        }, 500);
    }
}
window.dismissDynamicAlert = dismissDynamicAlert;

document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    let autoSyncPay = true;

    // DOM Elements
    const searchInput = document.getElementById('product-search');
    const productCards = document.querySelectorAll('.product-card');
    const filterPills = document.querySelectorAll('.category-filter-pill');
    const cartScrollBox = document.getElementById('cart-scroll-box');
    const cartEmptyState = document.getElementById('cart-empty-state');
    const cartBadgeCount = document.getElementById('cart-badge-count');
    const clearCartBtn = document.getElementById('clear-cart-btn');
    const vatToggle = document.getElementById('vat-toggle');
    const discountInput = document.getElementById('discount-input');
    const paidAmtInput = document.getElementById('paid-amount-input');
    const paidAmtWrapper = document.getElementById('paid-amount-wrapper');
    const creditPaidInput = document.getElementById('credit-paid-input');
    const creditRemaining = document.getElementById('credit-remaining-lbl');
    const creditBox = document.getElementById('credit-payment-box');
    const payMethodBtns = document.querySelectorAll('.pay-method-btn');
    const hiddenPayInput = document.getElementById('selected-payment-method');
    const submitBtn = document.getElementById('checkout-submit-btn');
    const errorBox = document.getElementById('checkout-error-box');
    const errorMsg = document.getElementById('checkout-error-msg');
    const lblSubtotal = document.getElementById('summary-subtotal');
    const lblTaxable = document.getElementById('summary-taxable');
    const lblVat = document.getElementById('summary-vat');
    const lblGrandTotal = document.getElementById('summary-grandtotal');

    const csrfToken = document.querySelector('input[name="_token"]').value;

    // Synced path pointing to SalesController target route rules
    const checkoutUrl = "{{ route('admin.sales.pos.store') }}";

    function showError(msg) {
        errorMsg.textContent = msg;
        errorBox.classList.remove('hidden');
    }

    function hideError() {
        errorBox.classList.add('hidden');
        errorMsg.textContent = '';
    }

    function resetSubmitBtn() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-receipt"></i> Process Secure Checkout';
    }

    // Calculations Engine
    function calculateTotals() {
        let subtotal = 0;
        const rows = cartScrollBox.querySelectorAll('.cart-item-row');

        cart.forEach((item, index) => {
            const weight = parseFloat(item.quantity_kg) + (parseFloat(item.quantity_gm) / 1000);
            const itemTotal = item.rate_per_kg * weight;
            subtotal += itemTotal;

            if (rows[index]) {
                rows[index].querySelector('.item-total-lbl').textContent =
                `NPR ${itemTotal.toFixed(2)}`;
            }
        });

        const discount = parseFloat(discountInput.value) || 0;
        const taxable = Math.max(0, subtotal - discount);
        const vat = vatToggle.checked ? parseFloat((taxable * 0.13).toFixed(2)) : 0;
        const grandTotal = parseFloat((taxable + vat).toFixed(2));

        lblSubtotal.textContent = `NPR ${subtotal.toFixed(2)}`;
        lblTaxable.textContent = `NPR ${taxable.toFixed(2)}`;
        lblVat.textContent = `NPR ${vat.toFixed(2)}`;
        lblGrandTotal.textContent = `NPR ${grandTotal.toFixed(2)}`;

        if (hiddenPayInput.value === 'Credit Sale') {
            const advancePaid = parseFloat(creditPaidInput.value) || 0;
            const remaining = Math.max(0, grandTotal - advancePaid);
            creditRemaining.textContent = `NPR ${remaining.toFixed(2)}`;
        } else {
            if (autoSyncPay) {
                paidAmtInput.value = grandTotal.toFixed(2);
            }
        }
    }

    // Listen for real-time live advance input matching
    creditPaidInput.addEventListener('input', calculateTotals);
    discountInput.addEventListener('input', calculateTotals);
    vatToggle.addEventListener('change', calculateTotals);
    paidAmtInput.addEventListener('input', () => {
        autoSyncPay = false;
    });

    // Payment Selection Trigger Logic
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

    // --- Standard Search & Render routines ---
    function filterProducts() {
        const query = searchInput.value.toLowerCase().trim();
        const activePill = document.querySelector('.category-filter-pill.bg-blue-600');
        const targetCategory = activePill ? activePill.dataset.category : 'all';

        productCards.forEach(card => {
            const matchesName = card.dataset.name.toLowerCase().includes(query);
            const matchesCat = targetCategory === 'all' || card.dataset.category === targetCategory;
            card.style.display = (matchesName && matchesCat) ? '' : 'none';
        });
    }
    if (searchInput) searchInput.addEventListener('input', filterProducts);

    productCards.forEach(card => {
        card.addEventListener('click', function() {
            const id = parseInt(this.dataset.id);
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const stock = parseFloat(this.dataset.stock);
            const unit = this.dataset.unit;

            if (stock <= 0) {
                alert('Out of stock!');
                return;
            }


            const existing = cart.find(i => i.id === id);
            if (existing) {
                const currentTotalWeight = parseFloat(existing.quantity_kg) + (parseFloat(
                    existing.quantity_gm) / 1000);
                if (currentTotalWeight + 1 > stock) { // Check if adding 1kg exceeds stock
                    alert(`Cannot add more. Only ${stock} ${unit} available for ${name}.`);
                    return;
                }
                existing.quantity_kg++; // Increment by 1kg by default
            } else {
                cart.push({
                    id,
                    name,
                    rate_per_kg: price,
                    quantity_kg: 1,
                    quantity_gm: 0,
                    stock,
                    unit
                });
            }
            renderCart();

            // Visual feedback for product card
            this.classList.add('bg-blue-100', 'border-blue-500');
            setTimeout(() => {
                this.classList.remove('bg-blue-100', 'border-blue-500');
            }, 500); // Remove highlight after 0.5 seconds
        });
    });

    function renderCart() {
        cartScrollBox.querySelectorAll('.cart-item-row').forEach(r => r.remove());
        if (cart.length === 0) {
            cartEmptyState.style.display = '';
            submitBtn.disabled = true;
            cartBadgeCount.textContent = '0';
            autoSyncPay = true;
            lblSubtotal.textContent = 'NPR 0.00';
            lblTaxable.textContent = 'NPR 0.00';
            lblVat.textContent = 'NPR 0.00';
            lblGrandTotal.textContent = 'NPR 0.00';
            paidAmtInput.value = '0.00';
            return;
        }
        cartEmptyState.style.display = 'none';
        submitBtn.disabled = false;

        let totalQtyDisplay = 0;
        cart.forEach((item, index) => {
            totalQtyDisplay += parseFloat(item.quantity_kg) + (parseFloat(item.quantity_gm) / 1000);
            const row = document.createElement('div');
            row.className =
                'cart-item-row py-2.5 flex items-center justify-between text-xs text-slate-700 border-b border-slate-100';
            row.innerHTML = `
                <div class="max-w-[35%]">
                    <span class="font-bold uppercase text-slate-800 block truncate text-sm">${item.name}</span>
                    <span class="text-[10px] text-slate-500 font-mono">NPR ${item.rate_per_kg.toFixed(2)} / ${item.unit}</span>
                </div>
                <div class="flex items-center gap-0.5">
                    <button type="button" class="qty-minus-btn text-slate-500 hover:text-slate-700 w-4 h-4 flex items-center justify-center" data-type="kg" data-index="${index}"><i class="fa-solid fa-minus text-[9px]"></i></button>
                    <input type="number" class="qty-kg-field w-10 text-center font-mono py-0.5 border border-slate-200 rounded text-[11px]" data-index="${index}" min="0" value="${item.quantity_kg}">
                    <button type="button" class="qty-plus-btn text-slate-500 hover:text-slate-700 w-4 h-4 flex items-center justify-center" data-type="kg" data-index="${index}"><i class="fa-solid fa-plus text-[9px]"></i></button>
                    <span class="text-[10px] text-slate-400">kg</span>

                    <button type="button" class="qty-minus-btn text-slate-500 hover:text-slate-700 w-4 h-4 flex items-center justify-center" data-type="gm" data-index="${index}"><i class="fa-solid fa-minus text-[9px]"></i></button>
                    <input type="number" class="qty-gm-field w-10 text-center font-mono py-0.5 border border-slate-200 rounded text-[11px]" data-index="${index}" min="0" max="999" value="${item.quantity_gm}">
                    <button type="button" class="qty-plus-btn text-slate-500 hover:text-slate-700 w-4 h-4 flex items-center justify-center" data-type="gm" data-index="${index}"><i class="fa-solid fa-plus text-[9px]"></i></button>
                    <span class="text-[10px] text-slate-400">gm</span>
                </div>
                <div class="text-right min-w-[80px]">
                    <span class="font-mono font-bold block text-slate-900 item-total-lbl">NPR 0.00</span>
                    <button type="button" class="item-remove text-[9px] text-red-500 uppercase font-semibold" data-index="${index}">Remove</button>
                </div>`;

            cartScrollBox.appendChild(row);
        });
        cartBadgeCount.textContent = totalQtyDisplay.toFixed(2);
        bindCartRowEvents();
        calculateTotals();
    }

    function bindCartRowEvents() {
        cartScrollBox.querySelectorAll('.qty-minus-btn, .qty-plus-btn').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                const type = this.dataset.type; // 'kg' or 'gm'
                const item = cart[index];
                let currentValue;
                let inputField;

                if (type === 'kg') {
                    currentValue = parseFloat(item.quantity_kg);
                    inputField = this.parentNode.querySelector('.qty-kg-field');
                } else {
                    currentValue = parseFloat(item.quantity_gm);
                    inputField = this.parentNode.querySelector('.qty-gm-field');
                }

                let newValue = currentValue;
                if (this.classList.contains('qty-plus-btn')) {
                    newValue = currentValue + (type === 'kg' ? 1 :
                    100); // Increment by 1kg or 100gm
                } else {
                    newValue = currentValue - (type === 'kg' ? 1 :
                    100); // Decrement by 1kg or 100gm
                }

                if (type === 'gm') {
                    newValue = Math.min(Math.max(0, newValue),
                    999); // Ensure gm is between 0 and 999
                } else { // kg
                    newValue = Math.max(0, newValue);
                }

                if (type === 'kg') {
                    item.quantity_kg = newValue;
                } else {
                    item.quantity_gm = newValue;
                }
                inputField.value = newValue; // Update the input field directly
                calculateTotals();
            });
        });
        cartScrollBox.querySelectorAll('.qty-kg-field').forEach(input => {
            input.addEventListener('input', function() {
                cart[parseInt(this.dataset.index)].quantity_kg = parseFloat(this.value) || 0;
                calculateTotals();
            });
        });
        cartScrollBox.querySelectorAll('.qty-gm-field').forEach(input => {
            input.addEventListener('input', function() {
                let val = parseFloat(this.value) || 0;
                if (val > 999) {
                    val = 999;
                    this.value = 999;
                }
                cart[parseInt(this.dataset.index)].quantity_gm = val;
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
            cart = [];
            renderCart();
        });
    }

    // Submit Logic execution
    submitBtn.addEventListener('click', function() {
        hideError();
        if (cart.length === 0) {
            showError('Your cart is empty.');
            return;
        }

        const isCreditSale = hiddenPayInput.value === 'Credit Sale';
        const finalPaid = isCreditSale ? parseFloat(creditPaidInput.value) : parseFloat(paidAmtInput
            .value);

        const payload = {
            customer_id: document.getElementById('customer-select').value,
            payment_method: hiddenPayInput.value,
            include_vat: vatToggle.checked ? 1 : 0,
            discount: parseFloat(discountInput.value) || 0,
            paid_amount: finalPaid || 0,
            remarks: document.getElementById('remarks-input').value.trim(),
            transaction_date: document.getElementById('transaction-date') ? document.getElementById(
                'transaction-date').value.trim() : '',
            items: cart.map(item => ({
                id: item.id,
                rate_per_kg: item.rate_per_kg,
                quantity_kg: parseFloat(item.quantity_kg) || 0,
                quantity_gm: parseFloat(item.quantity_gm) || 0,
            })),
        };

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

        // ===================== REPLACE FROM HERE =====================
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
                // Read the response as TEXT first — never assume it's JSON.
                // Laravel can return an HTML page (419 session expired, 500 error page, etc.)
                const rawText = await res.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (parseErr) {
                    console.error('Non-JSON response from server (first 500 chars):', rawText
                        .slice(0, 500));
                    let friendlyMsg;
                    if (res.status === 419) {
                        friendlyMsg =
                            'Your session expired. Please refresh the page and try again.';
                    } else if (res.status === 500) {
                        friendlyMsg =
                            'Server error (HTTP 500). Check storage/logs/laravel.log for details.';
                    } else {
                        friendlyMsg =
                            `Unexpected server response (HTTP ${res.status}). Check Laravel logs.`;
                    }
                    throw new Error(friendlyMsg);
                }
                return {
                    status: res.status,
                    data
                };
            })
            .then(result => {
                if (result.status === 200 && result.data.success) {
                    // 1. Locate or safely create the master toast wrapper layout container 
                    let alertContainer = document.querySelector('.fixed.top-5.right-5.z-50');
                    if (!alertContainer) {
                        alertContainer = document.createElement('div');
                        alertContainer.className =
                            "fixed top-5 right-5 z-50 flex flex-col gap-4 w-full max-w-sm pointer-events-none";
                        document.body.appendChild(alertContainer);
                    }

                    // 2. Build the precise structural clone of partials/alert.blade.php
                    const toastId = 'dynamic-alert-' + Date.now();
                    const progressId = 'dynamic-progress-' + Date.now();

                    const toastHTML = `
                    <div id="${toastId}" 
                         class="pointer-events-auto relative flex items-start bg-white p-4 pb-5 shadow-xl rounded border border-slate-100 transition-all duration-500 ease-out translate-x-full opacity-0 overflow-hidden">
                        <div class="flex-shrink-0 text-emerald-500 mr-3">
                            <i class="fa-solid fa-circle-check text-base"></i>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <h3 class="text-sm font-bold text-slate-800">Success</h3>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">${result.data.message || 'Invoice processed successfully!'}</p>
                        </div>
                        <button onclick="dismissDynamicAlert('${toastId}')" class="text-slate-300 hover:text-slate-400 ml-4 transition-colors">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>

                        <div class="absolute bottom-0 left-0 h-1 bg-emerald-500 w-full origin-left transition-transform duration-[5000ms] ease-linear scale-x-0" id="${progressId}"></div>
                    </div>
                `;

                    alertContainer.insertAdjacentHTML('beforeend', toastHTML);

                    const dynamicAlert = document.getElementById(toastId);
                    const dynamicBar = document.getElementById(progressId);

                    setTimeout(() => {
                        if (dynamicAlert) {
                            dynamicAlert.classList.remove('translate-x-full', 'opacity-0');
                            dynamicAlert.classList.add('translate-x-0', 'opacity-100');
                        }
                    }, 100);

                    setTimeout(() => {
                        if (dynamicBar) {
                            dynamicBar.classList.remove('scale-x-0');
                            dynamicBar.classList.add('scale-x-100');
                        }
                    }, 300);

                    setTimeout(() => {
                        dismissDynamicAlert(toastId);
                        setTimeout(() => {
                            window.location.href = result.data.redirect;
                        }, 500);
                    }, 4000);

                } else {
                    showError('Error: ' + (result.data.message || 'Request failed.'));
                    resetSubmitBtn();
                }
            })
            .catch(err => {
                console.error("POS Submission Debug Log:", err);
                showError(err.message || 'Request failed. Check browser console for details.');
                resetSubmitBtn();
            });
        // ===================== REPLACE UNTIL HERE =====================
    });
});
</script>
@endsection