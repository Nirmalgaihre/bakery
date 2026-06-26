@extends('layouts.admin')

@section('title', 'Manage Inventory Adjustments')

@section('content')
<div class="max-w-xl mx-auto my-6">

    @if($errors->any() || session('error'))
        <div class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded text-sm">
            <span class="font-bold">Transaction Failed:</span> {{ session('error') ?: $errors->first() }}
        </div>
    @endif

    <form action="{{ route('admin.inventory.adjust.store', $product->id) }}" method="POST" class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        @csrf
        
        <div class="p-4 bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
            <i class="fa-solid fa-boxes-stacked mr-1"></i> Stock Deductions & Movement Manifest
        </div>

        <div class="p-6 space-y-5">
            {{-- Product Reference Info card --}}
            <div class="bg-slate-50 p-4 border rounded text-sm">
                <div class="text-xs text-slate-400 uppercase font-bold">Item Under Review</div>
                <div class="text-base font-bold text-slate-800">{{ $product->name }}</div>
                <div class="text-xs font-medium text-slate-500 mt-1">
                    Current Balance: <span class="text-slate-800 font-mono font-bold">{{ $product->initial_stock }} {{ strtoupper($product->inventory_unit) }}</span>
                </div>
            </div>

            {{-- Adjustment Type Selector dropdown menu --}}
            <div class="space-y-1">
                <label for="type" class="block text-xs font-bold text-slate-700 uppercase">Adjustment Reason Scenario *</label>
                <select name="type" id="type" class="w-full text-sm p-2 border rounded outline-none bg-white focus:border-blue-500" required>
                    <option value="" disabled selected>-- Choose Category Action --</option>
                    <option value="sell">Standard Sell (Reduces Stock)</option>
                    <option value="expired">Expired Stock Item (Reduces Stock)</option>
                    <option value="damaged">Damaged Material (Reduces Stock)</option>
                    <option value="returned_defective">Customer Return / Defective (Adds Back to Stock Tally)</option>
                    <option value="internal_use">Used Internally / Tasting / Testing (Reduces Stock)</option>
                    <option value="wastage">Bakery Counter Wastage / Dough Spoilage (Reduces Stock)</option>
                </select>
            </div>

            {{-- Quantity Input --}}
            <div class="space-y-1">
                <label for="quantity" class="block text-xs font-bold text-slate-700 uppercase">Units Volume Target *</label>
                <div class="relative">
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" 
                        class="w-full text-sm p-2 border rounded outline-none focus:border-blue-500 font-mono font-bold" required>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-slate-400 uppercase">
                        {{ $product->inventory_unit }}
                    </span>
                </div>
            </div>

            {{-- Explanatory Notes --}}
            <div class="space-y-1">
                <label for="reference_note" class="block text-xs font-bold text-slate-700 uppercase">Journal Notes / Internal Log Audit Code</label>
                <input type="text" name="reference_note" id="reference_note" value="{{ old('reference_note') }}" 
                    placeholder="e.g., Rats bit package / Burned batch in oven / Counter sale..." 
                    class="w-full text-sm p-2 border rounded outline-none focus:border-blue-500">
            </div>
        </div>

        {{-- Footer Controls --}}
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="bg-white border text-slate-600 font-semibold text-xs px-4 py-2 rounded uppercase">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2 rounded uppercase tracking-wide">
                Process Log Movement
            </button>
        </div>
    </form>
</div>
@endsection