@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6" x-data="{ 
    itemLines: [{ product_id: '', rate_per_kg: 0, quantity_kg: 0, quantity_gm: 0, total: 0 }],
    aggregateTotal: 0,
    
    // Updates the rate when a product is selected from the dropdown
    updateItemPrice(event, index) {
        const select = event.target;
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        this.itemLines[index].rate_per_kg = parseFloat(price);
        this.updateLineTotal(index);
    },

    updateLineTotal(index) {
        let line = this.itemLines[index];
        line.total = line.rate_per_kg * (line.quantity_kg + (line.quantity_gm / 1000));
        this.aggregateTotal = this.itemLines.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
    },

    appendItemLine() {
        this.itemLines.push({ product_id: '', rate_per_kg: 0, quantity_kg: 0, quantity_gm: 0, total: 0 });
    },

    removeItemLine(index) {
        if(this.itemLines.length > 1) {
            this.itemLines.splice(index, 1);
            this.aggregateTotal = this.itemLines.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
        }
    },

    // Handles Customer detail binding
    bindClientProfile(event) {
        const option = event.target.options[event.target.selectedIndex];
        document.getElementById('display_address').value = option.getAttribute('data-address') || '';
        document.getElementById('display_phone').value = option.getAttribute('data-phone') || '';
        document.getElementById('display_pan').value = option.getAttribute('data-pan') || '';
    }
}">

    <form action="{{ route('admin.sales.store') }}" method="POST" class="bg-white p-6 rounded shadow-sm border border-slate-200 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider border-b border-slate-100 pb-2">Invoice Details</h3>
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice No</label>
                    <input type="text" readonly name="invoice_number" value="{{ $next_invoice_number ?? 'INV-00001' }}" 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-600 font-mono rounded">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Invoice Date</label>
                    <input type="text" name="invoice_date" required value="{{ $currentNepaliDate }}" 
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-700 rounded focus:border-emerald-500 font-mono">
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider border-b border-slate-100 pb-2">Customer Details</h3>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Name <span class="text-red-500">*</span></label>
                    <select name="customer_id" required @change="bindClientProfile($event)"
                        class="w-full px-3 py-2 bg-white border border-slate-200 text-sm text-slate-700 rounded focus:border-emerald-500">
                        <option value="" data-address="" data-phone="" data-pan="">-- Choose Customer --</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" 
                                    data-address="{{ $customer->address }}" 
                                    data-phone="{{ $customer->phone_number }}"
                                    data-pan="{{ $customer->pan_number }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Address</label>
                    <input type="text" id="display_address" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Contact Number</label>
                        <input type="text" id="display_phone" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">PAN</label>
                        <input type="text" id="display_pan" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm text-slate-500 rounded">
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
                        <template x-for="(line, index) in itemLines" :key="index">
                            <tr>
                                <td class="p-2.5">
                                    <select :name="'items['+index+'][product_id]'" required @change="updateItemPrice($event, index)"
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
                                    <input type="text" readonly :value="line.total.toFixed(2)"
                                        class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 text-xs rounded font-mono">
                                </td>
                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeItemLine(index)" class="text-red-500 font-bold">x</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 font-bold">
                            <td colspan="3" class="p-3 text-right">Grand Total:</td>
                            <td colspan="2" class="p-3">
                                <input type="text" name="grand_total" readonly :value="aggregateTotal.toFixed(2)"
                                    class="w-full px-2 py-1.5 bg-white border font-mono rounded">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" @click="appendItemLine()" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded">Add Row</button>
        </div>

        <button type="submit" class="px-6 py-2 bg-emerald-700 text-white rounded text-sm font-bold">Save Invoice</button>
    </form>
</div>
@endsection