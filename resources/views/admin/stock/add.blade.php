@extends('layouts.admin')

@section('title', 'Refurbish Stock Inventory')

@section('content')
<div class="max-w-xl w-full mx-auto" x-data="{ 
    qty: {{ old('quantity', 1) }}, 
    cost: {{ old('purchase_cost', $product->purchase_cost ?? 0) }},
    price: {{ old('selling_price', $product->selling_price ?? 0) }}
}">

    {{-- यहाँ route मा admin.purchases.store प्रयोग गरिएको छ --}}
    <form action="{{ route('admin.purchases.store') }}" method="POST" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        @csrf
        
        {{-- product_id लाई hidden फिल्डको रूपमा पठाउन अनिवार्य छ --}}
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Update Stock Manifest</h2>
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Refurbishing: {{ $product->name }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Summary Card --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Current Inventory</p>
                    <p class="text-lg font-mono font-bold text-slate-800">{{ $product->initial_stock }} <span class="text-xs text-slate-400">{{ strtoupper($product->inventory_unit) }}</span></p>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3">
                    <p class="text-[9px] text-emerald-600 font-bold uppercase">New Total After Update</p>
                    <p class="text-lg font-mono font-bold text-emerald-800" x-text="parseFloat(qty) + parseFloat({{ $product->initial_stock }})"></p>
                </div>
            </div>

            {{-- Inputs --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Incoming Addition (Quantity)</label>
                    <input type="number" x-model="qty" name="quantity" class="w-full text-sm p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none font-bold" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Purchase Cost (Rs)</label>
                        <input type="number" step="0.01" x-model="cost" name="purchase_cost" class="w-full text-sm p-3 border border-slate-200 rounded-lg focus:border-emerald-500 outline-none transition-all font-mono" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Selling Price (Rs)</label>
                        <input type="number" step="0.01" x-model="price" name="selling_price" class="w-full text-sm p-3 border border-emerald-500 rounded-lg bg-emerald-50 outline-none font-bold text-emerald-700 transition-all font-mono">
                    </div>
                </div>
            </div>

            <input type="text" name="reference_note" placeholder="Add a log note for this update..." 
                class="w-full text-sm p-3 border border-slate-200 rounded-lg focus:border-blue-500 outline-none">
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-xs font-bold text-slate-500 uppercase hover:text-slate-800 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-lg shadow-emerald-200 transition-all flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Save Inventory Update
            </button>
        </div>
    </form>
</div>
@endsection