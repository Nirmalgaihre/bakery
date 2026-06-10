@extends('layouts.admin')

@section('title', 'Process Counter Register Retail Sale')
@section('panel_title', 'Generate Invoice')

@section('content')
<div class="max-w-7xl mx-auto" x-data="invoiceEngine()">

    @if(session('error'))
        <div class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded text-sm font-medium">
            <span class="font-bold">Register Validation Rejection:</span> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.sales.store') }}" id="pos_sales_form" method="POST" class="bg-white border border-slate-200 rounded shadow-sm overflow-hidden">
        @csrf
        <input type="hidden" name="type" value="sell">
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-slate-100 bg-slate-50/30">
            
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1 h-3.5 bg-emerald-600 inline-block rounded-xs"></span>
                    Invoice Details
                </h3>
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice No</label>
                    <input type="text" readonly name="invoice_number" value="{{ $paddedInvoiceNumber }}" 
                        class="w-full px-3 py-2 bg-slate-100 border border-slate-200 text-sm font-mono font-bold text-emerald-800 rounded outline-none cursor-not-allowed">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice Date (B.S.) <span class="text-xs text-slate-400 font-normal">(Editable)</span></label>
                    <input type="text" name="invoice_date" value="{{ $currentNepaliDate }}" 
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-800 rounded font-mono font-bold focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1 h-3.5 bg-emerald-600 inline-block rounded-xs"></span>
                    Patient / Customer Details
                </h3>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Name <span class="text-red-500">*</span></label>
                    <select name="customer_id" required @change="bindCustomerProfileCard($event)"
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-800 font-medium rounded focus:outline-none focus:border-emerald-500 transition-colors">
                        <option value="" data-address="" data-phone="" data-pan="" data-due="0.00">-- Choose Customer Entity --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                    data-address="{{ $customer->address }}" 
                                    data-phone="{{ $customer->phone_number }}"
                                    data-pan="{{ $customer->pan_number ?? 'N/A' }}"
                                    data-due="{{ $customer->previous_due }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Address</label>
                    <input type="text" id="patient_address" readonly placeholder="Customer address will auto-fill" 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded outline-none cursor-not-allowed truncate">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Contact Phone</label>
                        <input type="text" id="patient_phone" readonly placeholder="N/A" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded outline-none cursor-not-allowed font-mono">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">PAN Registration</label>
                        <input type="text" id="patient_pan" readonly placeholder="N/A" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded outline-none cursor-not-allowed font-mono uppercase">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Previous Due (Rs.)</label>
                        <input type="text" id="patient_due" readonly placeholder="0.00" 
                            class="w-full px-3 py-2 bg-rose-50/50 border border-rose-100 text-sm text-rose-700 font-bold rounded outline-none cursor-not-allowed font-mono">
                    </div>
                </div>
            </div>

        </div>

        <div class="p-6 space-y-4">
            <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1 h-3.5 bg-emerald-600 inline-block rounded-xs"></span>
                Product Details (Weight System: KG / Optional Grams)
            </h3>

            <div class="border border-slate-200 rounded-md overflow-x-auto bg-white shadow-3xs">
                <table class="w-full text-left border-collapse min-w-[750px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700">
                            <th class="p-3 w-[35%]">Product</th>
                            <th class="p-3 w-[15%]">Rate (Per KG)</th>
                            <th class="p-3 w-[25%] text-center">Quantity / Weight</th>
                            <th class="p-3 w-[15%] text-right">Total (Rs.)</th>
                            <th class="p-3 text-center w-[10%]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        
                        <template x-for="(line, index) in itemLines" :key="index">
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="p-3">
                                    <select :name="'items['+index+'][product_id]'" required @change="syncCatalogRate($event, index)"
                                        class="w-full px-2.5 py-1.5 bg-white border border-slate-200 text-xs text-slate-800 font-medium rounded focus:outline-none focus:border-emerald-500 transition-colors">
                                        <option value="">Select Product</option>
                                        @foreach($products as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}">
                                                {{ $item->name }} (Stock: {{ $item->initial_stock }} {{ strtoupper($item->inventory_unit) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="p-3">
                                    <input type="number" :name="'items['+index+'][price]'" step="0.01" required min="0"
                                        x-model.number="line.price" @input="evaluateRowSum(index)" placeholder="0.00"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 text-xs text-slate-800 font-mono rounded focus:outline-none focus:border-emerald-500 transition-colors">
                                </td>

                                <td class="p-3">
                                    <div class="flex items-center justify-center gap-4">
                                        <div class="flex items-center space-x-1">
                                            <input type="number" :name="'items['+index+'][weight_kg]'" min="0" required placeholder="0"
                                                x-model.number="line.kg" @input="evaluateRowSum(index)"
                                                class="w-16 px-2 py-1 text-center bg-white border border-slate-200 text-xs text-slate-800 font-mono rounded focus:outline-none focus:border-emerald-500">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase">KG</span>
                                        </div>

                                        <div class="flex items-center space-x-1">
                                            <input type="number" :name="'items['+index+'][weight_gram]'" min="0" max="999" placeholder="Optional"
                                                x-model="line.gram" @input="evaluateRowSum(index)"
                                                class="w-24 px-2 py-1 text-center bg-white border border-slate-200 text-xs text-slate-800 font-mono rounded focus:outline-none focus:border-emerald-500">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase">Grams</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-3 text-right font-mono text-xs font-semibold text-slate-700 pr-5">
                                    Rs. <span x-text="line.total.toFixed(2)">0.00</span>
                                </td>

                                <td class="p-3 text-center">
                                    <button type="button" @click="removeItemLineRow(index)" :disabled="itemLines.length === 1"
                                        class="h-7 w-7 bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed rounded flex items-center justify-center font-bold text-xs transition-all">
                                        x
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <tr class="bg-slate-50/70 font-semibold text-slate-700 border-t border-slate-200">
                            <td colspan="3" class="p-3.5 text-xs font-bold text-slate-500 text-right uppercase tracking-wider">Total Matrix Value:</td>
                            <td class="p-3.5 text-right font-mono text-xs font-bold text-emerald-700 pr-5">
                                Rs. <span x-text="grandAggregateSum.toFixed(2)">0.00</span>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-1 flex items-center justify-between">
                <button type="button" @click="appendItemLineRow()"
                    class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded transition-all shadow-2xs flex items-center gap-1">
                    <i class="fa-solid fa-plus text-[10px]"></i> Add Row
                </button>
            </div>
        </div>

        <div class="p-4 px-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
            <a href="{{ route('admin.sales.dashboard') }}" class="bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 font-bold text-xs px-4 py-2 rounded transition-colors uppercase tracking-wider">
                Sales Register Return
            </a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded shadow-xs transition-all uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i> Save Invoice
            </button>
        </div>
    </form>
</div>

<script>
function invoiceEngine() {
    return {
        itemLines: [
            { product_id: '', price: 0, kg: 1, gram: '', total: 0 }
        ],
        grandAggregateSum: 0,

        bindCustomerProfileCard(event) {
            const selectIndex = event.target.selectedIndex;
            const elementNode = event.target.options[selectIndex];
            
            document.getElementById('patient_address').value = elementNode.getAttribute('data-address') || '';
            document.getElementById('patient_phone').value = elementNode.getAttribute('data-phone') || '';
            
            const fallbackPan = elementNode.getAttribute('data-pan');
            document.getElementById('patient_pan').value = fallbackPan ? 'PAN: ' + fallbackPan : '';
            
            const dueVal = parseFloat(elementNode.getAttribute('data-due')) || 0;
            document.getElementById('patient_due').value = dueVal.toLocaleString('en-US', { minimumFractionDigits: 2 });
        },

        syncCatalogRate(event, index) {
            const element = event.target.options[event.target.selectedIndex];
            const targetRateValue = parseFloat(element.getAttribute('data-price')) || 0;
            
            this.itemLines[index].price = targetRateValue;
            this.evaluateRowSum(index);
        },

        evaluateRowSum(index) {
            const lineItem = this.itemLines[index];
            
            const kgPart = parseFloat(lineItem.kg) || 0;
            
            // Treat empty/blank string inputs gracefully as 0 grams instead of throwing NaN
            const gramInput = lineItem.gram === '' ? 0 : parseFloat(lineItem.gram);
            const gramPart = (gramInput || 0) / 1000;
            
            const totalWeightInKg = kgPart + gramPart;
            
            lineItem.total = totalWeightInKg * (parseFloat(lineItem.price) || 0);
            this.sumWholeInvoiceVoucher();
        },

        appendItemLineRow() {
            this.itemLines.push({ product_id: '', price: 0, kg: 1, gram: '', total: 0 });
        },

        removeItemLineRow(index) {
            if (this.itemLines.length > 1) {
                this.itemLines.splice(index, 1);
                this.sumWholeInvoiceVoucher();
            }
        },

        sumWholeInvoiceVoucher() {
            this.grandAggregateSum = this.itemLines.reduce((acc, obj) => acc + obj.total, 0);
        }
    }
}
</script>
@endsection