@extends('layouts.admin')

@section('title', 'Refurbish Stock Inventory - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Warehouse Inventory Adjustments')

@section('content')
<div class="max-w-xl w-full mx-auto">

    @if($errors->any())
        <div class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded text-sm shadow-xs">
            <div class="font-bold text-xs uppercase tracking-wide text-rose-700 mb-1">Adjustment Processing Failed:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.stock.store', $product->id) }}" method="POST" class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        @csrf
        
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
            <i class="fa-solid fa-circle-arrow-up text-emerald-600 text-sm"></i> Logistics Inventory & Pricing Adjustment Management
        </div>

        <div class="p-6 space-y-5">
            {{-- Product Reference Info Panel Element --}}
            <div class="bg-slate-50 p-4 border border-slate-200/60 rounded flex flex-col gap-1 text-sm">
                <div class="text-xs text-slate-400 uppercase font-bold tracking-wider">Target Selected Profile Item</div>
                <div class="text-base font-bold text-slate-800">{{ $product->name }}</div>
                <div class="text-xs font-medium text-slate-500 mt-1 flex gap-4">
                    <span>Category Matrix: <strong class="text-slate-700">{{ $product->category }}</strong></span>
                    <span>Current Inventory Balances: <strong class="text-slate-700 font-mono">{{ $product->initial_stock }} {{ strtoupper($product->inventory_unit) }}</strong></span>
                </div>
            </div>

            {{-- Quantity Input --}}
            <div class="space-y-1">
                <label for="quantity" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Incoming Addition Units Quantity *</label>
                <div class="relative">
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" 
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono font-bold" required>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 uppercase">
                        {{ $product->inventory_unit }}
                    </span>
                </div>
            </div>

            {{-- NEW INTERACTIVE ELEMENT: Flexible Pricing Grid System Layout Configuration --}}
            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                
                {{-- Dynamic Purchase Cost Parameter Entry Field --}}
                <div class="space-y-1">
                    <label for="purchase_cost" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Current Purchase Cost (Rs.) *</label>
                    <input type="number" step="0.01" name="purchase_cost" id="purchase_cost" value="{{ old('purchase_cost', $product->purchase_cost) }}" 
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono" required>
                    <p class="text-[10px] text-slate-400">Purano Purchase Cost: <span class="font-bold">Rs. {{ number_format($product->purchase_cost, 2) }}</span></p>
                </div>

                {{-- Dynamic Market Distribution Valuation Sale Price Entry Field --}}
                <div class="space-y-1">
                    <label for="selling_price" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">New Market Selling Price (Rs.) *</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" 
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono font-bold text-emerald-700" required>
                    <p class="text-[10px] text-slate-400">Purano Selling Price: <span class="font-bold">Rs. {{ number_format($product->selling_price, 2) }}</span></p>
                </div>
            </div>

            {{-- Audit Trail Journal Explanatory Note Entry Node --}}
            <div class="space-y-1">
                <label for="reference_note" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Reference Journal Log Note / Invoice Code</label>
                <input type="text" name="reference_note" id="reference_note" value="{{ old('reference_note') }}" placeholder="e.g., Rate badhera aayeko naya batch invoice #991..." 
                    class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
            </div>
        </div>

        {{-- Form Actions Control Section Footer (With Emerald Green Form Action Button) --}}
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 font-semibold text-xs px-4 py-2 rounded transition-colors uppercase tracking-wide flex items-center">Cancel</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2 rounded shadow-xs transition-colors uppercase tracking-wide flex items-center gap-1.5">
                <i class="fa-solid fa-square-check"></i> Commit Stock & Price Adjustments
            </button>
        </div>
    </form>
</div>
@endsection