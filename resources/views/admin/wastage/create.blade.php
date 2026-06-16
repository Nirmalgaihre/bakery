@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6" x-data="{ 
    wasteLines: [{ product_id: '', type: 'damaged', rate_per_kg: 0, current_available_stock: 0, current_unit: 'units', quantity_kg: 0, quantity_gm: 0, total_loss: 0, reference_note: '', stock_exceeded: false }],
    grandLossTotal: 0,
    hasStockErrors: false,
    
    updateItemCost(event, index) {
        const select = event.target;
        const selectedOption = select.options[select.selectedIndex];
        
        const cost = selectedOption.getAttribute('data-cost') || 0;
        const stock = selectedOption.getAttribute('data-stock') || 0;
        const unit = selectedOption.getAttribute('data-unit') || 'units';
        
        this.wasteLines[index].rate_per_kg = parseFloat(cost);
        this.wasteLines[index].current_available_stock = parseFloat(stock);
        this.wasteLines[index].current_unit = unit;
        
        this.updateLineLoss(index);
    },

    updateLineLoss(index) {
        let line = this.wasteLines[index];
        
        let totalWeight = 0;
        if(line.current_unit === 'kg') {
            totalWeight = parseFloat(line.quantity_kg || 0) + (parseFloat(line.quantity_gm || 0) / 1000);
        } else {
            totalWeight = parseFloat(line.quantity_kg || 0);
        }
        
        line.total_loss = parseFloat(line.rate_per_kg || 0) * totalWeight;
        
        // स्टक चेक नियम
        if (line.type !== 'returned_defective' && line.product_id !== '') {
            line.stock_exceeded = totalWeight > line.current_available_stock;
        } else {
            line.stock_exceeded = false;
        }
        
        this.calculateGrandTotal();
    },

    appendWasteLine() {
        this.wasteLines.push({ product_id: '', type: 'damaged', rate_per_kg: 0, current_available_stock: 0, current_unit: 'units', quantity_kg: 0, quantity_gm: 0, total_loss: 0, reference_note: '', stock_exceeded: false });
    },

    removeWasteLine(index) {
        if(this.wasteLines.length > 1) {
            this.wasteLines.splice(index, 1);
            this.calculateGrandTotal();
        }
    },

    calculateGrandTotal() {
        this.grandLossTotal = this.wasteLines.reduce((sum, item) => sum + (parseFloat(item.total_loss) || 0), 0);
        this.hasStockErrors = this.wasteLines.some(line => line.stock_exceeded === true);
    }
}">

    <div class="mb-4">
        <h2 class="text-xl font-bold text-slate-800">Returns & Spoils / Stock Adjustments</h2>
        <p class="text-xs text-slate-500">Record stock reductions using dynamic matching item configuration units.</p>
    </div>

    {{-- Alert Messages (Success/Errors) --}}
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm">
            <ul class="list-disc list-inside text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.wastage.store') }}" method="POST" class="bg-white p-6 rounded shadow-sm border border-slate-200 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
            <div class="bg-slate-50 border border-slate-200 rounded-md p-3">
                <label class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-2">Transaction Date (BS)</label>
                <input type="text" name="transaction_date" value="{{ \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from(date('Y-m-d'))->toNepaliDate(format: 'Y-m-d') }}" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm font-mono text-blue-700 focus:outline-none">
            </div>
            <div class="space-y-1.5 pb-0.5">
                <label class="block text-xs font-semibold text-slate-700">Master Audit Summary Note</label>
                <input type="text" name="master_remarks" placeholder="Reason breakdown summary..." class="w-full px-3 py-2 bg-white border border-slate-200 text-sm rounded focus:outline-none">
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="text-xs font-bold text-red-600 uppercase tracking-wider">Adjustment Matrix</h3>

            <div class="border border-slate-200 rounded overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700">
                            <th class="p-3 w-[30%]">Product</th>
                            <th class="p-3 w-[18%]">Movement Type</th>
                            <th class="p-3 w-[12%]">Unit Cost (Rs.)</th>
                            <th class="p-3 w-[18%]">Adjustment Qty</th>
                            <th class="p-3 w-[14%]">Line Note</th>
                            <th class="p-3 text-center w-[8%]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(line, index) in wasteLines" :key="index">
                            <tr class="hover:bg-slate-50/50 transition-colors" :class="line.stock_exceeded ? 'bg-red-50/70' : ''">
                                
                                {{-- Product Selection --}}
                                <td class="p-2.5">
                                    <select :name="'items['+index+'][product_id]'" x-model="line.product_id" required @change="updateItemCost($event, index)"
                                        class="w-full px-2 py-1.5 bg-white border border-slate-200 text-xs rounded focus:outline-none">
                                        <option value="">Select Item</option>
                                        @foreach($products ?? [] as $product)
                                            {{-- 💡 कम्बाइन कन्डिसन: यदि stock छैन वा ० छ भने initial_stock देखाउने, अन्यथा थपघट भएको stock देखाउने --}}
                                            @php 
                                                $displayStock = ($product->stock && $product->stock > 0) ? $product->stock : $product->initial_stock; 
                                            @endphp
                                            <option value="{{ $product->id }}" 
                                                    data-cost="{{ $product->selling_price ?? 0 }}" 
                                                    data-stock="{{ $displayStock }}"
                                                    data-unit="{{ $product->inventory_unit ?? 'units' }}">
                                                {{ $product->name }} ({{ $displayStock }} {{ strtoupper($product->inventory_unit ?? 'units') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <div class="text-[10px] mt-1 text-slate-500 font-medium pl-1" x-show="line.product_id">
                                        Available Stock: <span class="font-mono text-slate-700" x-text="line.current_available_stock + ' ' + line.current_unit.toUpperCase()"></span>
                                    </div>
                                </td>

                                {{-- Movement Type --}}
                                <td class="p-2.5">
                                    <select :name="'items['+index+'][type]'" x-model="line.type" required @change="updateLineLoss(index)"
                                        class="w-full px-2 py-1.5 bg-white border border-slate-200 text-xs rounded font-medium text-slate-700 focus:outline-none">
                                        <option value="expired">Expired Products</option>
                                        <option value="damaged">Damaged & Spoiled</option>
                                        <option value="returned_defective">Customer Return</option>
                                        <option value="internal_use">Internal Use</option>
                                        <option value="wastage">Wastage & Scrap</option>
                                    </select>
                                </td>

                                {{-- Unit Cost --}}
                                <td class="p-2.5">
                                    <input type="number" :name="'items['+index+'][rate_per_kg]'" step="0.01" required 
                                        x-model.number="line.rate_per_kg" @input="updateLineLoss(index)"
                                        class="w-full px-2 py-1.5 border border-slate-200 text-xs rounded font-mono focus:outline-none">
                                </td>

                                {{-- Weight / Quantity Split --}}
                                <td class="p-2.5">
                                    <div class="flex gap-1">
                                        <input type="number" :name="'items['+index+'][quantity_kg]'" :placeholder="line.current_unit.toUpperCase()" min="0" required
                                            x-model.number="line.quantity_kg" @input="updateLineLoss(index)"
                                            class="px-2 py-1.5 border border-slate-200 text-xs text-center rounded font-mono focus:outline-none"
                                            :class="[line.current_unit === 'kg' ? 'w-1/2' : 'w-full', line.stock_exceeded ? 'border-red-400 bg-red-50 text-red-700' : '']">
                                        
                                        <template x-if="line.current_unit === 'kg'">
                                            <input type="number" :name="'items['+index+'][quantity_gm]'" placeholder="GM" min="0" max="999"
                                                x-model.number="line.quantity_gm" @input="updateLineLoss(index)"
                                                class="w-1/2 px-2 py-1.5 border border-slate-200 text-xs text-center rounded font-mono focus:outline-none"
                                                :class="line.stock_exceeded ? 'border-red-400 bg-red-50 text-red-700' : ''">
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-red-600 font-bold mt-1 pl-1" x-show="line.stock_exceeded">
                                        <i class="fa-solid fa-circle-exclamation"></i> Exceeds Available Stock!
                                    </div>
                                </td>

                                {{-- Note --}}
                                <td class="p-2.5">
                                    <input type="text" :name="'items['+index+'][reference_note]'" placeholder="Reason..." x-model="line.reference_note"
                                        class="w-full px-2 py-1.5 border border-slate-200 text-xs rounded focus:outline-none">
                                </td>

                                {{-- Action Button --}}
                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeWasteLine(index)" class="text-slate-400 hover:text-red-500 p-1">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-700">
                            <td colspan="4" class="p-3 text-right text-xs uppercase tracking-wider">Estimated Adjustment Loss:</td>
                            <td colspan="2" class="p-3">
                                <span class="font-mono text-sm text-red-600 font-bold" x-text="'Rs. ' + grandLossTotal.toFixed(2)"></span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <button type="button" @click="appendWasteLine()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i> Add Line Item
            </button>
        </div>

        <div class="pt-2">
            <button type="submit" :disabled="hasStockErrors" class="px-6 py-2 rounded text-sm font-bold shadow-sm flex items-center gap-2 text-white transition-colors" :class="hasStockErrors ? 'bg-slate-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'">
                <i class="fa-solid fa-square-check"></i> Commit & Adjust Inventory Records
            </button>
        </div>
    </form>
</div>
@endsection