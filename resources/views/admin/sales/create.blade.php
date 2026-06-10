@extends('layouts.admin')

@section('title', 'Generate Invoice')
@section('panel_title', 'Generate Invoice Node')

@section('content')
<div class="max-w-7xl mx-auto bg-white border border-slate-200 rounded shadow-xs overflow-hidden" x-data="invoiceEngine()">
    
    <!-- Header Block Component -->
    <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-1 h-4 bg-emerald-600 inline-block rounded-xs"></span>
                Billing Transaction Console
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Establish new invoice vouchers. Mapping a customer account automatically references backend ledger metrics.
            </p>
        </div>
    </div>

    <!-- Processing Form Matrix -->
    <form action="{{ route('admin.sales.store') }}" method="POST" class="p-6 space-y-6">
        @csrf

        <!-- TOP METADATA BLOCK: MATCHING Screenshot_2026-06-09_20_01_20.png -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Left Side: Invoice Structural Details -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Invoice Details
                </h3>
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice No</label>
                    <input type="text" readonly name="invoice_number" value="{{ $next_invoice_number ?? '7' }}" 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-600 font-mono rounded outline-none cursor-not-allowed">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice Date</label>
                    <input type="date" name="invoice_date" required value="{{ date('Y-m-d') }}" 
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-700 rounded focus:outline-none focus:border-emerald-500 font-mono transition-colors">
                </div>
            </div>

            <!-- Right Side: Dynamic Customer / Patient Selection Details -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Customer / Patient Details
                </h3>

                <!-- Dropdown Client Profile Lookup -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Name <span class="text-red-500">*</span></label>
                    <select name="customer_id" required @change="bindClientProfile($event)"
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-700 rounded focus:outline-none focus:border-emerald-500 transition-colors">
                        <option value="" data-address="" data-phone="" data-pan="">-- Choose Customer Entity --</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" 
                                    data-address="{{ $customer->address }}" 
                                    data-phone="{{ $customer->phone_number }}"
                                    data-pan="{{ $customer->pan_number }}">
                                {{ $customer->name }} {{ $customer->pan_number ? '['.$customer->pan_number.']' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Address Input Node -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Address</label>
                    <input type="text" id="display_address" readonly placeholder="Select customer to load address automatically" 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded outline-none cursor-not-allowed truncate">
                </div>

                <!-- Combined Sub-Metadata Group -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Contact Number</label>
                        <input type="text" id="display_phone" readonly placeholder="N/A" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 font-mono rounded outline-none cursor-not-allowed">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">PAN Registration</label>
                        <input type="text" id="display_pan" readonly placeholder="N/A" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 font-mono rounded outline-none cursor-not-allowed uppercase">
                    </div>
                </div>
            </div>

        </div>

        <!-- LINE ITEMS INTERFACE GRID TABLE -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider">
                Product Details
            </h3>

            <div class="border border-slate-200 rounded overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700">
                            <th class="p-3 w-[35%]">Product</th>
                            <th class="p-3 w-[20%]">Price (Rs.)</th>
                            <th class="p-3 w-[15%]">Qty</th>
                            <th class="p-3 w-[20%]">Total (Rs.)</th>
                            <th class="p-3 text-center w-[10%]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        
                        <template x-for="(line, index) in itemLines" :key="index">
                            <tr class="bg-white hover:bg-slate-50/40 transition-colors">
                                <!-- Item Picker Dropdown -->
                                <td class="p-2.5">
                                    <select :name="'items['+index+'][product_id]'" required @change="updateItemPrice($event, index)"
                                        class="w-full px-2 py-1.5 bg-white border border-slate-200 text-xs text-slate-700 rounded focus:outline-none focus:border-emerald-500">
                                        <option value="">Select Product</option>
                                        @foreach($products ?? [] as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                {{ $product->name }} (Stock: {{ $product->stock ?? 0 }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Rate Unit Input -->
                                <td class="p-2.5">
                                    <input type="number" :name="'items['+index+'][price]'" step="0.01" required min="0"
                                        x-model.number="line.price" @input="updateLineTotal(index)"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 text-xs text-slate-700 font-mono rounded focus:outline-none focus:border-emerald-500">
                                </td>

                                <!-- Qty Matrix Input -->
                                <td class="p-2.5">
                                    <input type="number" :name="'items['+index+'][qty]'" required min="1"
                                        x-model.number="line.qty" @input="updateLineTotal(index)" placeholder="Enter Product Quantity"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 text-xs text-slate-700 font-mono rounded focus:outline-none focus:border-emerald-500">
                                </td>

                                <!-- Line Sub-total Calculation -->
                                <td class="p-2.5">
                                    <input type="number" readonly :value="line.total.toFixed(2)"
                                        class="w-full px-3 py-1.5 bg-slate-50 border border-slate-100 text-xs text-slate-600 font-mono rounded outline-none cursor-not-allowed">
                                </td>

                                <!-- Delete Action Icon Button -->
                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeItemLine(index)" :disabled="itemLines.length === 1"
                                        class="px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-100 disabled:opacity-30 disabled:hover:bg-red-50 disabled:hover:text-red-600 rounded text-xs font-bold transition-all">
                                        x
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <!-- Aggregate Calculations Summary Bar Row -->
                        <tr class="bg-slate-50/60 font-semibold text-slate-700 border-t border-slate-200">
                            <td colspan="3" class="p-3 text-sm text-right font-medium">Total:</td>
                            <td class="p-3">
                                <input type="number" readonly :value="aggregateTotal.toFixed(2)"
                                    class="w-full px-3 py-1.5 bg-white border border-slate-200 text-xs font-bold text-slate-800 font-mono rounded outline-none">
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Append Line Item Control -->
            <div class="pt-1">
                <button type="button" @click="appendItemLine()"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded transition-colors shadow-2xs flex items-center gap-1">
                    <i class="fa-solid fa-plus text-[10px]"></i> Add Row
                </button>
            </div>
        </div>

        <hr class="border-slate-200">

        <!-- Execution Control Segment -->
        <div class="flex items-center justify-start">
            <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs uppercase tracking-wide rounded transition-all shadow-xs flex items-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i> Save Invoice
            </button>
        </div>

    </form>
</div>

<!-- Alpine Interface Calculation Engine Component -->
<script>
function invoiceEngine() {
    return {
        itemLines: [
            { product_id: '', price: 0, qty: 1, total: 0 }
        ],
        aggregateTotal: 0,

        bindClientProfile(event) {
            const index = event.target.selectedIndex;
            const node = event.target.options[index];
            
            document.getElementById('display_address').value = node.getAttribute('data-address') || '';
            document.getElementById('display_phone').value = node.getAttribute('data-phone') || '';
            document.getElementById('display_pan').value = node.getAttribute('data-pan') || '';
        },

        updateItemPrice(event, index) {
            const targetNode = event.target.options[event.target.selectedIndex];
            const baseValue = parseFloat(targetNode.getAttribute('data-price')) || 0;
            
            this.itemLines[index].price = baseValue;
            this.updateLineTotal(index);
        },

        updateLineTotal(index) {
            const currentItem = this.itemLines[index];
            currentItem.total = (parseFloat(currentItem.price) || 0) * (parseInt(currentItem.qty) || 0);
            this.calculateSummaryAggregate();
        },

        appendItemLine() {
            this.itemLines.push({ product_id: '', price: 0, qty: 1, total: 0 });
        },

        removeItemLine(index) {
            if (this.itemLines.length > 1) {
                this.itemLines.splice(index, 1);
                this.calculateSummaryAggregate();
            }
        },

        calculateSummaryAggregate() {
            this.aggregateTotal = this.itemLines.reduce((acc, current) => acc + current.total, 0);
        }
    }
}
</script>
@endsection