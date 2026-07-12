@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6" x-data="invoiceEditor(
        @js($itemLines),
        {
            aggregateTotal: {{ $invoice->grand_total }},
            subtotal: {{ $invoice->subtotal }},
            discount: {{ $invoice->discount }},
            vatAmount: {{ $invoice->vat_amount }},
            taxableAmount: {{ $invoice->taxable_amount }},
            paidAmount: {{ $invoice->paid_amount }},
            paymentMethod: @js($invoice->payment_method),
            includeVat: {{ $invoice->vat_amount > 0 ? 'true' : 'false' }}
        }
     )">

    <form id="invoiceForm" action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST"
        @submit.prevent="submitForm" class="bg-white p-6 rounded shadow-sm border border-slate-200 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Invoice Details</h3>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice No</label>
                    <input type="text" readonly name="invoice_number" value="{{ $invoice->invoice_no }}"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-600 font-mono rounded">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice Date (AD)</label>
                    <input type="date" name="invoice_date" required
                        value="{{ $invoice->invoice_date->format('Y-m-d') }}"
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-700 rounded focus:border-emerald-500 font-mono">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice Date (BS)</label>
                    <input type="text" name="transaction_date" required value="{{ $invoice->nepali_date }}"
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-700 rounded focus:border-emerald-500 font-mono">
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Customer Details</h3>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Name <span
                            class="text-red-500">*</span></label>
                    <select name="customer_id" required @change="bindClientProfile($event)"
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-700 rounded focus:border-emerald-500">
                        <option value="" data-address="" data-phone="" data-pan="">-- Choose Customer --</option>
                        @foreach($customers ?? [] as $customer)
                        <option value="{{ $customer->id }}" data-address="{{ $customer->address }}"
                            data-phone="{{ $customer->phone_number }}" data-pan="{{ $customer->pan_number }}"
                            {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Address</label>
                    <input type="text" id="display_address" readonly value="{{ $invoice->customer->address ?? 'N/A' }}"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Contact Number</label>
                        <input type="text" id="display_phone" readonly
                            value="{{ $invoice->customer->phone_number ?? 'N/A' }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">PAN</label>
                        <input type="text" id="display_pan" readonly
                            value="{{ $invoice->customer->pan_number ?? 'N/A' }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Product Pricing Matrix</h3>

            <div class="border border-slate-200 rounded overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700">
                            <th class="p-3 w-[35%]">Product</th>
                            <th class="p-3 w-[15%]">Rate (KG)</th>
                            <th class="p-3 w-[25%]">Weight</th>
                            <th class="p-3 w-[15%]">Total (Rs.)</th>
                            <th class="p-3 text-center w-[10%]">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(line, index) in itemLines" :key="line.invoice_item_id ?? index">
                            <tr x-show="!line._delete" data-invoice-row>
                                <td class="p-2.5">
                                    <input type="hidden" :name="'items['+index+'][invoice_item_id]'"
                                        x-model="line.invoice_item_id">
                                    <select :name="'items['+index+'][product_id]'" required x-model="line.product_id"
                                        @change="updateItemPrice($event, index)"
                                        class="w-full px-2 py-1.5 bg-white border border-slate-200 text-xs rounded">
                                        <option value="">Select Product</option>
                                        @foreach($products ?? [] as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                                            {{ $product->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="p-2.5">
                                    <input type="number" :name="'items['+index+'][rate_per_kg]'" step="0.01" required
                                        x-model.number="line.rate_per_kg" @input="updateLineTotal(index)"
                                        class="w-full px-2 py-1.5 border border-slate-200 text-xs rounded">
                                </td>

                                <td class="p-2.5 flex gap-1">
                                    <input type="number" :name="'items['+index+'][quantity_kg]'" placeholder="KG"
                                        x-model.number="line.quantity_kg" @input="updateLineTotal(index)"
                                        class="w-1/2 px-2 py-1.5 border border-slate-200 text-xs rounded">
                                    <input type="number" :name="'items['+index+'][quantity_gm]'" placeholder="GM"
                                        x-model.number="line.quantity_gm" @input="updateLineTotal(index)"
                                        class="w-1/2 px-2 py-1.5 border border-slate-200 text-xs rounded">
                                </td>

                                <td class="p-2.5">
                                    <input type="text" readonly :value="Number(line.total || 0).toFixed(2)"
                                        class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 text-xs rounded font-mono">
                                </td>

                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeItemLine(index)"
                                        class="text-red-500 font-bold">x</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <tfoot>
                        <tr class="bg-slate-50">
                            <td class="p-2.5">
                                <button type="button" @click="appendItemLine()"
                                    class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded">
                                    + Add Row
                                </button>
                            </td>
                            <td colspan="2" class="p-2.5 text-right font-semibold text-slate-700">Total</td>
                            <td class="p-2.5">
                                <input type="text" readonly x-model="aggregateTotal.toFixed(2)"
                                    class="w-full px-2 py-1.5 bg-slate-50 border font-mono rounded">
                            </td>
                            <td class="p-2.5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3 items-center">
                        <label class="text-sm font-medium text-slate-700">Discount</label>
                        <input type="number" name="discount" step="0.01" x-model.number="discount"
                            @input="calculateTotals()" class="w-full px-2 py-1.5 bg-white border font-mono rounded">
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-center">
                        <label class="text-sm font-medium text-slate-700">Include VAT (13%)</label>
                        <div class="flex justify-start">
                            <input type="checkbox" name="include_vat" x-model="includeVat" @change="calculateTotals()"
                                class="form-checkbox h-4 w-4 text-emerald-600 rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-center">
                        <label class="text-sm font-medium text-slate-700">VAT Amount</label>
                        <input type="text" readonly x-model="vatAmount.toFixed(2)"
                            class="w-full px-2 py-1.5 bg-slate-50 border font-mono rounded">
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3 items-center">
                        <label class="text-sm font-medium text-slate-700">Payment Method</label>
                        <select name="payment_method" x-model="paymentMethod" @change="adjustPaidAmount()"
                            class="w-full px-2 py-1.5 bg-white border text-xs rounded">
                            <option value="Cash">Cash</option>
                            <option value="Online Payment">Online Payment</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Sale">Credit Sale</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-center">
                        <label class="text-sm font-medium text-slate-700">Paid Amount</label>
                        <input type="number" name="paid_amount" step="0.01" x-model.number="paidAmount"
                            :readonly="paymentMethod !== 'Credit Sale'"
                            class="w-full px-2 py-1.5 bg-white border font-mono rounded"
                            :class="{'bg-slate-50': paymentMethod !== 'Credit Sale'}">
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-start">
                        <label class="text-sm font-medium text-slate-700 pt-2">Remarks</label>
                        <textarea name="remarks"
                            class="w-full px-2 py-1.5 bg-white border text-xs rounded">{{ $invoice->remarks }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="px-6 py-2 bg-emerald-700 text-white rounded text-sm font-bold">Update
            Invoice</button>
    </form>
</div>

<script>
function invoiceEditor(initialItems, invoice) {
    return {
        itemLines: initialItems || [],
        aggregateTotal: parseFloat(invoice.aggregateTotal ?? invoice.grand_total ?? 0),
        subtotal: parseFloat(invoice.subtotal ?? 0),
        discount: parseFloat(invoice.discount ?? 0),
        vatAmount: parseFloat(invoice.vatAmount ?? invoice.vat_amount ?? 0),
        taxableAmount: parseFloat(invoice.taxableAmount ?? invoice.taxable_amount ?? 0),
        paidAmount: parseFloat(invoice.paidAmount ?? invoice.paid_amount ?? 0),
        paymentMethod: invoice.paymentMethod ?? invoice.payment_method ?? 'Cash',
        includeVat: invoice.includeVat ?? (parseFloat(invoice.vat_amount ?? 0) > 0),

        init() {
            this.$watch('itemLines', () => this.calculateTotals());
            this.$watch('discount', () => this.calculateTotals());
            this.$watch('includeVat', () => this.calculateTotals());
            this.$watch('paymentMethod', () => this.adjustPaidAmount());
            this.calculateTotals();
        },

        updateItemPrice(event, index) {
            const select = event.target;
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price') || 0;
            this.itemLines[index].rate_per_kg = parseFloat(price) || 0;
            this.updateLineTotal(index);

            this.$nextTick(() => {
                const row = document.querySelectorAll('[data-invoice-row]:not([style*="display: none"])')[
                index];
                if (!row) return;
                const nextInput = row.querySelector('input[name*="[quantity_kg]"]');
                if (nextInput) nextInput.focus();
            });
        },

        updateLineTotal(index) {
            let line = this.itemLines[index];
            const totalWeight = parseFloat(line.quantity_kg || 0) + (parseFloat(line.quantity_gm || 0) / 1000);
            line.total = parseFloat(line.rate_per_kg || 0) * totalWeight;
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.itemLines.reduce((sum, item) => sum + (item._delete ? 0 : (parseFloat(item.total) ||
                0)), 0);
            this.taxableAmount = Math.max(0, this.subtotal - parseFloat(this.discount || 0));
            this.vatAmount = this.includeVat ? Math.round(this.taxableAmount * 0.13 * 100) / 100 : 0.00;
            this.aggregateTotal = Math.round((this.taxableAmount + this.vatAmount) * 100) / 100;
            this.adjustPaidAmount();
        },

        adjustPaidAmount() {
            if (this.paymentMethod !== 'Credit Sale') {
                this.paidAmount = this.aggregateTotal;
            }
        },

        appendItemLine() {
            this.itemLines.push({
                invoice_item_id: null,
                product_id: '',
                rate_per_kg: 0,
                quantity_kg: 0,
                quantity_gm: 0,
                total: 0,
                _delete: false
            });

            this.calculateTotals();

            this.$nextTick(() => {
                setTimeout(() => {
                    const rows = document.querySelectorAll(
                        '[data-invoice-row]:not([style*="display: none"])');
                    const lastRow = rows[rows.length - 1];
                    if (lastRow) {
                        lastRow.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        const firstSelect = lastRow.querySelector('select');
                        if (firstSelect) firstSelect.focus();
                    }
                }, 0);
            });
        },

        removeItemLine(index) {
            if (this.itemLines[index].invoice_item_id) {
                this.itemLines[index]._delete = true;
            } else {
                this.itemLines.splice(index, 1);
            }
            this.calculateTotals();
        },

        bindClientProfile(event) {
            const option = event.target.options[event.target.selectedIndex];
            document.getElementById('display_address').value = option.getAttribute('data-address') || '';
            document.getElementById('display_phone').value = option.getAttribute('data-phone') || '';
            document.getElementById('display_pan').value = option.getAttribute('data-pan') || '';
        },

        submitForm() {
            const form = document.getElementById('invoiceForm');
            const formData = new FormData(form);

            this.itemLines.forEach((item, index) => {
                if (!item._delete) {
                    formData.append(`items[${index}][product_id]`, item.product_id);
                    formData.append(`items[${index}][invoice_item_id]`, item.invoice_item_id || '');
                    formData.append(`items[${index}][rate_per_kg]`, item.rate_per_kg);
                    formData.append(`items[${index}][quantity_kg]`, item.quantity_kg);
                    formData.append(`items[${index}][quantity_gm]`, item.quantity_gm);
                } else if (item.invoice_item_id) {
                    formData.append(`items[${index}][invoice_item_id]`, item.invoice_item_id);
                    formData.append(`items[${index}][_delete]`, true);
                }
            });

            formData.append('subtotal', this.subtotal);
            formData.append('taxable_amount', this.taxableAmount);
            formData.append('vat_amount', this.vatAmount);
            formData.append('grand_total', this.aggregateTotal);
            formData.append('paid_amount', this.paidAmount);
            formData.append('payment_method', this.paymentMethod);
            formData.append('include_vat', this.includeVat ? 1 : 0);
            formData.append('discount', this.discount);

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) { // Display success toast and then redirect
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
                                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">${data.message || 'Invoice updated successfully!'}</p>
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
                                window.location.href = data.redirect;
                            }, 500);
                        }, 4000);
                    } else {
                        // Handle error messages from the server
                        const errorMessage = data.message || 'An unknown error occurred.';
                        alert('Error: ' + errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An unexpected error occurred.');
                });
        }
    }
}

// Global function for dismissing dynamic alerts (can be moved to layouts.admin.blade.php for reusability)
function dismissDynamicAlert(alertId) {
    const alertElement = document.getElementById(alertId);
    if (alertElement) {
        alertElement.classList.remove('translate-x-0', 'opacity-100');
        alertElement.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => alertElement.remove(), 500);
    }
}
</script>
@endsection