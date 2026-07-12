@extends('layouts.admin')

@section('title', 'POS Billing Terminal')
@section('panel_title', 'Point-of-Sale Live Billing Interface')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start font-sans antialiased text-slate-800">

    {{-- LEFT: Product Grid --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Search + Category Filter Bar --}}
        <div
            class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:w-72">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="product-search" placeholder="Search by name or SKU..."
                    class="w-full pl-9 pr-4 py-1.5 bg-slate-50 border border-slate-200 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white rounded-md transition-all">
            </div>

            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 scrollbar-none">
                <button type="button"
                    class="category-filter-pill px-3 py-1.5 bg-blue-600 text-white rounded-md text-[11px] font-bold uppercase tracking-wider transition-colors"
                    data-category="all">All Products</button>
                @isset($categories)
                    @foreach($categories as $category)
                    <button type="button"
                        class="category-filter-pill px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-md text-[11px] font-bold uppercase tracking-wider transition-colors"
                        data-category="{{ strtolower($category->name) }}">{{ $category->name }}</button>
                    @endforeach
                @endisset
            </div>
        </div>

        {{-- Product Table --}}
        <div class="border border-slate-200 rounded-lg overflow-hidden">
            <table class="w-full text-left text-xs" id="product-list-table">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-2 px-3">Product Name</th>
                        <th class="py-2 px-3 text-right">Price (NPR)</th>
                        <th class="py-2 px-3 text-center">Unit</th>
                        <th class="py-2 px-3 text-center">Stock</th>
                        <th class="py-2 px-3 text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                    @php
                    $isOutOfStock = $product->initial_stock <= 0;
                    $isLow = $product->initial_stock <= ($product->alert_stock_level ?? 5);
                            @endphp
                            <tr class="product-list-row hover:bg-slate-50 transition-colors cursor-pointer {{ $isOutOfStock ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"
                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                data-price="{{ $product->selling_price }}" data-initial-stock="{{ $product->initial_stock }}"
                                data-unit="{{ $product->inventory_unit }}"
                                data-category="{{ strtolower($product->category) }}"
                                data-alert-level="{{ $product->alert_stock_level ?? 5 }}">
                                <td class="py-2 px-3 font-medium text-slate-800">{{ $product->name }}</td>
                                <td class="py-2 px-3 text-right font-mono text-blue-600">
                                    {{ number_format($product->selling_price, 2) }}</td>
                                <td class="py-2 px-3 text-center text-slate-600">{{ $product->inventory_unit }}</td>
                                <td class="py-2 px-3 text-center">
                                    <span
                                        class="font-bold font-mono px-2 py-0.5 rounded {{ !$isLow ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                        {{ floatval($product->initial_stock) }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-center"></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-boxes-stacked text-3xl text-slate-200 block mb-2"></i>
                                    No warehouse items found.
                                </td>
                            </tr>
                            @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIGHT: Billing Cart --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 sticky top-6 flex flex-col max-h-[calc(100vh-140px)]"
        id="billing-ledger-card">

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

        <div class="bg-slate-50 border border-slate-200 rounded-md p-3 mt-2.5">
            <label class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-2">Transaction Date
                (BS)</label>
            <input type="text" id="transaction-date"
                value="{{ \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from(date('Y-m-d'))->toNepaliDate(format: 'Y-m-d') }}"
                placeholder="2082-03-28"
                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm font-mono text-blue-700 focus:outline-none focus:border-blue-500">
        </div>

        <div id="cart-scroll-box"
            class="flex-1 overflow-y-auto divide-y divide-slate-100 my-2 pr-1 min-h-[150px] max-h-[300px]">
            <div id="cart-empty-state" class="text-center py-12 text-slate-400">
                <i class="fa-solid fa-cart-shopping text-3xl mb-2 text-slate-200 block"></i>
                <p class="text-[11px] font-medium text-slate-600">No products selected</p>
                <p class="text-[10px] text-slate-400 mt-0.5 max-w-[200px] mx-auto">Click a product row on the left to
                    add it to invoice items.</p>
            </div>
        </div>

        <div id="pos-checkout-form" class="border-t border-slate-200 pt-3 space-y-3 text-xs text-slate-700">
            @csrf

            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider block">1. Customer
                    Selection</label>
                <select id="customer-select"
                    class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded-md bg-white text-slate-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled {{ !isset($selectedCustomer) ? 'selected' : '' }}>Select a customer
                    </option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ $customer->name == 'Walk-in Customer' ? 'selected' : '' }}>
                        {{ $customer->name }} ({{ $customer->phone_number ?? 'No Phone' }})
                    </option>
                    @endforeach
                </select>
            </div>

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

            <div id="credit-payment-box" class="p-3 bg-amber-50 border border-amber-200 rounded-md hidden">
                <label class="text-[10px] font-bold text-amber-800 uppercase block mb-1">Advance Paid Amount
                    (NPR)</label>
                <input type="number" id="credit-paid-input" step="0.01" min="0" value="0.00"
                    class="w-full px-2 py-1.5 bg-white border border-amber-200 font-mono text-xs rounded-md text-amber-900">
                <p class="text-[10px] text-amber-700 mt-1">
                    Remaining Balance Due: <span id="credit-remaining-lbl" class="font-bold font-mono">NPR 0.00</span>
                </p>
            </div>

            <div class="flex items-center justify-between p-2 border border-slate-200 rounded-md bg-slate-50/50">
                <span class="text-[10px] font-bold text-slate-700 uppercase">Include 13% VAT</span>
                <label class="relative inline-flex items-center cursor-pointer select-none" for="vat-toggle">
                    <input type="checkbox" id="vat-toggle" checked class="sr-only peer">
                    <div
                        class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                    </div>
                </label>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-slate-500 uppercase block">Discount (NPR)</label>
                    <input type="number" id="discount-input" step="0.01" min="0" value="0.00"
                        class="w-full px-2 py-1.5 bg-white border border-slate-200 font-mono text-xs rounded-md text-slate-700 focus:outline-none focus:border-blue-500">
                </div>
                <div id="paid-amount-wrapper" class="space-y-1">
                    <label class="text-[10px] font-semibold text-slate-500 uppercase block">Paid Amount (NPR)</label>
                    <input type="number" id="paid-amount-input" step="0.01" min="0" value="0.00"
                        class="w-full px-2 py-1.5 bg-white border border-slate-200 font-mono text-xs rounded-md text-slate-700 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-semibold text-slate-500 uppercase block">Transaction Remarks</label>
                <input type="text" id="remarks-input" placeholder="Optional notes..."
                    class="w-full px-2 py-1.5 bg-white border border-slate-200 text-xs rounded-md text-slate-700 focus:outline-none focus:border-blue-500">
            </div>

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

            <div id="checkout-error-box" class="hidden p-3 bg-red-50 border border-red-200 rounded-md my-2">
                <p class="text-[11px] text-red-700 font-medium" id="checkout-error-msg"></p>
            </div>

            <button type="button" id="checkout-submit-btn" disabled
                class="w-full py-2.5 bg-blue-600 text-white text-xs font-bold uppercase rounded-md tracking-wider shadow-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 outline-none border-none cursor-pointer">
                <i class="fa-solid fa-receipt"></i> Process Secure Checkout
            </button>
        </div>
    </div>
</div>

<script>
function dismissDynamicAlert(alertId) {
    const alertElement = document.getElementById(alertId);
    if (alertElement) {
        alertElement.classList.remove('translate-x-0', 'opacity-100');
        alertElement.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => alertElement.remove(), 500);
    }
}
window.dismissDynamicAlert = dismissDynamicAlert;

document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    let autoSyncPay = true;

    let initialProductsData = {}; // Store initial product data
    const searchInput = document.getElementById('product-search'); // Moved after initialProductsData
    let productRows = [];
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
    const checkoutUrl = "{{ route('admin.sales.pos.store') }}";

    // Populate initialProductsData and productRows
    document.querySelectorAll('.product-list-row').forEach(row => {
        const productId = parseInt(row.dataset.id);
        initialProductsData[productId] = {
            id: productId,
            name: row.dataset.name,
            selling_price: parseFloat(row.dataset.price),
            initial_stock: parseFloat(row.dataset.initialStock), // Use initial-stock
            inventory_unit: row.dataset.unit,
            alert_stock_level: parseFloat(row.dataset.alertLevel)
        };
        productRows.push(row); // Store DOM element
    });

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
        } else if (autoSyncPay) {
            paidAmtInput.value = grandTotal.toFixed(2);
        }
    }

    function filterProducts() {
        const query = searchInput.value.toLowerCase().trim();
        const activePill = document.querySelector('.category-filter-pill.bg-blue-600');
        const targetCategory = activePill ? activePill.dataset.category : 'all';

        productRows.forEach(card => {
            const matchesName = card.dataset.name.toLowerCase().includes(query);
            const matchesCat = targetCategory === 'all' || card.dataset.category === targetCategory;
            card.style.display = (matchesName && matchesCat) ? '' : 'none';
        });
    }

    // New function to update product stock display
    function updateProductStockDisplay() {
        const cartQuantities = {};
        cart.forEach(item => {
            const totalQuantity = parseFloat(item.quantity_kg) + (parseFloat(item.quantity_gm) / 1000);
            cartQuantities[item.id] = (cartQuantities[item.id] || 0) + totalQuantity;
        });

        productRows.forEach(row => {
            const productId = parseInt(row.dataset.id);
            const initialStock = initialProductsData[productId].initial_stock;
            const inventoryUnit = initialProductsData[productId].inventory_unit;
            const alertLevel = initialProductsData[productId].alert_stock_level;

            const quantityInCart = cartQuantities[productId] || 0;
            const effectiveStock = initialStock - quantityInCart;

            const stockSpan = row.querySelector('td:nth-child(4) span'); // Assuming 4th td contains stock
            if (stockSpan) {
                stockSpan.textContent = effectiveStock.toFixed(inventoryUnit === 'piece' ? 0 : 2);

                row.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                stockSpan.classList.remove('bg-green-50', 'text-green-600', 'bg-red-50', 'text-red-600');

                if (effectiveStock <= 0) {
                    row.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                    stockSpan.classList.add('bg-red-50', 'text-red-600');
                } else if (effectiveStock <= alertLevel) {
                    stockSpan.classList.add('bg-red-50', 'text-red-600');
                } else {
                    stockSpan.classList.add('bg-green-50', 'text-green-600');
                }
            }
        });
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

    document.getElementById('product-list-table').addEventListener('click', function(event) {
        const row = event.target.closest('.product-list-row');
        if (!row || row.classList.contains('pointer-events-none')) return;

        const id = parseInt(row.dataset.id);
        const name = row.dataset.name;
        const price = parseFloat(row.dataset.price);
        const initialStock = initialProductsData[id].initial_stock; // Use initial stock for validation
        const unit = row.dataset.unit;

        if (initialStock <= 0) {
            alert('Out of stock!');
            return;
        }

        const existing = cart.find(item => item.id === id);
        if (existing) {
            const currentWeight = parseFloat(existing.quantity_kg) + (parseFloat(existing.quantity_gm) /
                1000);
            if (currentWeight + 1 > initialStock) {
                alert(`Cannot add more. Only ${initialStock} ${unit} available for ${name}.`);
                return;
            }
            existing.quantity_kg += 1;
        } else {
            cart.push({
                id,
                name,
                rate_per_kg: price,
                quantity_kg: 1,
                quantity_gm: 0,
                stock: initialStock, // Store initial stock in cart item for reference
                unit
            });
        }

        renderCart();
        row.classList.add('bg-blue-100');
        setTimeout(() => row.classList.remove('bg-blue-100'), 400);
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

            const isPieceUnit = item.unit === 'piece';
            const qtyGmDisplay = isPieceUnit ? 'hidden' : 'flex items-center gap-0.5';
            const qtyGmValue = isPieceUnit ? 0 : item.quantity_gm;
            const qtyGmLabel = isPieceUnit ? '' : '<span class="text-[10px] text-slate-400">gm</span>';

            row.innerHTML = `
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1 mb-1">
                        <input type="number" step="0.01" class="item-rate-field w-16 text-center font-mono py-0.5 border border-slate-200 rounded text-[11px]" data-index="${index}" value="${parseFloat(item.rate_per_kg).toFixed(2)}">
                        <span class="text-[10px] text-slate-500 font-mono">/ ${item.unit}</span>
                    </div>
                    <span class="font-bold uppercase text-slate-800 block truncate text-sm">${item.name}</span>
                </div>

                <div class="flex items-center gap-0.5 flex-shrink-0">
                    <button type="button" class="qty-minus-btn text-slate-500 hover:text-slate-700 w-3 h-3 flex items-center justify-center" data-type="kg" data-index="${index}"><i class="fa-solid fa-minus text-[9px]"></i></button>
                    <input type="number" class="qty-kg-field w-8 text-center font-mono py-0.5 border border-slate-200 rounded text-[11px]" data-index="${index}" min="0" value="${item.quantity_kg}">
                    <button type="button" class="qty-plus-btn text-slate-500 hover:text-slate-700 w-3 h-3 flex items-center justify-center" data-type="kg" data-index="${index}"><i class="fa-solid fa-plus text-[9px]"></i></button>
                    <span class="text-[10px] text-slate-400">${isPieceUnit ? 'pc' : 'kg'}</span>

                    <span class="${qtyGmDisplay}">
                        <button type="button" class="qty-minus-btn text-slate-500 hover:text-slate-700 w-3 h-3 flex items-center justify-center ml-1" data-type="gm" data-index="${index}"><i class="fa-solid fa-minus text-[9px]"></i></button>
                        <input type="number" class="qty-gm-field w-8 text-center font-mono py-0.5 border border-slate-200 rounded text-[11px]" data-index="${index}" min="0" max="999" value="${qtyGmValue}">
                        <button type="button" class="qty-plus-btn text-slate-500 hover:text-slate-700 w-3 h-3 flex items-center justify-center" data-type="gm" data-index="${index}"><i class="fa-solid fa-plus text-[9px]"></i></button>
                        ${qtyGmLabel}
                    </span>
                </div>

                <div class="text-right min-w-[80px]">
                    <span class="font-mono font-bold block text-slate-900 item-total-lbl">NPR 0.00</span>
                    <button type="button" class="item-remove text-[9px] text-red-500 uppercase font-semibold" data-index="${index}">Remove</button>
                </div>
            `;

            cartScrollBox.appendChild(row);
        });
        cartBadgeCount.textContent = totalQtyDisplay.toFixed(2);
        bindCartRowEvents();
        calculateTotals();
        updateProductStockDisplay(); // Update stock display after cart changes
    }

    function bindCartRowEvents() {
        // Remove existing event listeners to prevent duplicates
        cartScrollBox.querySelectorAll('.qty-minus-btn, .qty-plus-btn, .qty-kg-field, .qty-gm-field, .item-rate-field, .item-remove')
            .forEach(el => el.replaceWith(el.cloneNode(true)));

        cartScrollBox.querySelectorAll('.qty-minus-btn, .qty-plus-btn').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                const type = this.dataset.type;
                const item = cart[index];
                let tempKg = parseFloat(item.quantity_kg); // Use item.quantity_kg directly
                let tempGm = parseFloat(item.quantity_gm);
                const initialStock = initialProductsData[item.id].initial_stock;

                if (item.unit === 'piece' && type === 'gm') {
                    return; // Grams not applicable for 'piece' unit
                }

                if (this.classList.contains('qty-plus-btn')) {
                    if (type === 'kg') {
                        tempKg += 1;
                    } else {
                        tempGm += 100;
                        if (tempGm >= 1000) {
                            tempKg += Math.floor(tempGm / 1000);
                            tempGm %= 1000;
                        }
                    }
                } else {
                    if (type === 'kg') {
                        tempKg -= 1;
                    } else {
                        tempGm -= 100;
                        if (tempGm < 0) {
                            if (tempKg > 0) {
                                tempKg -= 1;
                                tempGm += 1000;
                            } else {
                                tempGm = 0; // Cannot go below 0 grams if kg is 0
                            }
                        }
                    }
                }

                tempKg = Math.max(0, tempKg);
                tempGm = Math.max(0, tempGm);

                const newTotalWeight = tempKg + (tempGm / 1000);

                if (newTotalWeight > initialStock) {
                    alert(
                        `Cannot exceed stock. Only ${initialStock} ${item.unit} available for ${item.name}.`); // Use initialStock
                    return;
                }
                if (newTotalWeight <= 0 && cart.length > 1) { // If quantity becomes 0, remove item unless it's the only one
                    cart.splice(index, 1);
                } else if (newTotalWeight <= 0 && cart.length === 1) { // If only one item and quantity becomes 0, clear cart
                    cart = [];
                } else {
                    item.quantity_kg = tempKg;
                    item.quantity_gm = tempGm;
                }
                renderCart(); // Re-render to update totals and stock display
            });
        });

        cartScrollBox.querySelectorAll('.qty-kg-field, .qty-gm-field').forEach(input => {
            input.addEventListener('input', function() {
                const index = parseInt(this.dataset.index);
                const item = cart[index];
                const initialStock = initialProductsData[item.id].initial_stock; // Use initialStock

                let newKg = parseFloat(item.quantity_kg);
                let newGm = parseFloat(item.quantity_gm);

                if (this.classList.contains('qty-kg-field')) {
                    newKg = parseFloat(this.value) || 0;
                } else {
                    newGm = parseFloat(this.value) || 0; // qty-gm-field
                    if (item.unit === 'piece') newGm = 0; // Ensure grams are 0 for pieces
                    if (newGm > 999) {
                        newGm = 999;
                        this.value = 999;
                    }
                }

                newKg = Math.max(0, newKg);
                newGm = Math.max(0, newGm);

                const newTotalWeight = newKg + (newGm / 1000);

                if (newTotalWeight > initialStock) {
                    alert(
                        `Cannot exceed stock. Only ${initialStock} ${item.unit} available for ${item.name}.`); // Use initialStock
                    if (this.classList.contains('qty-kg-field')) { // Revert input value
                        this.value = item.quantity_kg;
                    } else {
                        this.value = item.quantity_gm;
                    }
                    return;
                }

                if (newTotalWeight <= 0 && cart.length > 1) {
                    cart.splice(index, 1);
                } else if (newTotalWeight <= 0 && cart.length === 1) {
                    cart = [];
                } else {
                    item.quantity_kg = newKg;
                    item.quantity_gm = newGm;
                }
                renderCart(); // Re-render to update totals and stock display
            });
        });

        cartScrollBox.querySelectorAll('.item-rate-field').forEach(input => {
            input.addEventListener('input', function() {
                cart[parseInt(this.dataset.index)].rate_per_kg = parseFloat(this.value) || 0;
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

    productRows = Array.from(document.querySelectorAll('.product-list-row'));
    updateProductStockDisplay(); // Initial stock display update
    filterProducts();
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function() {
            cart = [];
            renderCart();
        });
    }

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
                    if (res.status === 419) throw new Error(
                        'Your session expired. Please refresh the page and try again.');
                    if (res.status === 500) throw new Error(
                        'Server error (HTTP 500). Check laravel.log for details.');
                    throw new Error(`Unexpected server response (HTTP ${res.status}).`);
                }
                return {
                    status: res.status,
                    data
                };
            })
            .then(result => {
                if (result.status === 200 && result.data.success) {
                    let alertContainer = document.querySelector('.fixed.top-5.right-5.z-50');
                    if (!alertContainer) {
                        alertContainer = document.createElement('div');
                        alertContainer.className =
                            "fixed top-5 right-5 z-50 flex flex-col gap-4 w-full max-w-sm pointer-events-none";
                        document.body.appendChild(alertContainer);
                    }

                    const toastId = 'dynamic-alert-' + Date.now();
                    const progressId = 'dynamic-progress-' + Date.now();

                    const toastHTML = `
                    <div id="${toastId}" class="pointer-events-auto relative flex items-start bg-white p-4 pb-5 shadow-xl rounded border border-slate-100 transition-all duration-500 ease-out translate-x-full opacity-0 overflow-hidden">
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
                showError(err.message || 'Request failed.');
                resetSubmitBtn();
            });
    });
});
</script>
@endsection