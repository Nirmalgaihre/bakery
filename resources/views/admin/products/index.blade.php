@extends('layouts.admin')

@section('title', 'Product Registry - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Product Catalog Registry')

@section('content')
<div class="max-w-7xl w-full mx-auto">
    
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden mt-4">
        
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 flex justify-between items-center">
            <div class="text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Active Warehouse Catalog Matrix
            </div>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded shadow-xs transition-colors uppercase tracking-wide flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Add New Item
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-700">
                <thead>
                    <tr class="bg-slate-100/70 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="p-4 px-5">Product Details</th>
                        <th class="p-4">Category</th>
                        <th class="p-4 text-right">Purchase Cost</th>
                        <th class="p-4 text-right">Selling Price</th>
                        <th class="p-4 text-center">Unit</th>
                        <th class="p-4 text-center bg-slate-50 border-x border-slate-200/60">Initial Stock</th> {{-- 👈 थपिएको कोलम --}}
                        <th class="p-4 text-center bg-blue-50/40 text-blue-900 font-bold">Current Available</th> {{-- 👈 थपिएको कोलम --}}
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 px-5 font-semibold text-slate-900">{{ $product->name }}</td>
                            <td class="p-4">
                                <span class="bg-slate-100 text-slate-700 text-xs px-2.5 py-1 rounded-full font-medium border border-slate-200/50">
                                    {{ $product->category }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-mono text-xs text-slate-600">Rs. {{ number_format($product->purchase_cost, 2) }}</td>
                            <td class="p-4 text-right font-mono text-xs font-bold text-slate-900">Rs. {{ number_format($product->selling_price, 2) }}</td>
                            <td class="p-4 text-center">
                                <span class="uppercase font-mono text-[11px] bg-slate-100 text-slate-600 border border-slate-200 px-2 py-0.5 rounded">
                                    {{ $product->inventory_unit }}
                                </span>
                            </td>
                            
                            {{-- 1. Initial Stock Column (सुरुको स्टक जतिको त्यति नै बस्छ) --}}
                            <td class="p-4 text-center font-mono text-slate-500 bg-slate-50/50 border-x border-slate-200/40">
                                {{ $product->initial_stock }}
                                <span class="text-[9px] uppercase font-sans text-slate-400 ml-0.5">{{ $product->inventory_unit }}</span>
                            </td>
                            
                            {{-- 2. Current Available Stock Column (स्टक एड वा वेस्टेज भएपछि कम्बाइन भएर घटबढ देखिने ठाउँ) --}}
                            <td class="p-4 text-center font-bold font-mono text-blue-700 bg-blue-50/20">
                                {{ $product->stock ?? $product->initial_stock }}
                                <span class="text-[9px] uppercase font-sans text-blue-400 font-bold ml-0.5">{{ $product->inventory_unit }}</span>
                            </td>
                            
                            {{-- Status Column --}}
                            <td class="p-4 text-center">
                                @if(($product->stock ?? $product->initial_stock) <= $product->alert_stock_level)
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold uppercase tracking-wide px-2 py-0.5 rounded flex items-center justify-center gap-1 max-w-[90px] mx-auto animate-pulse">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Low
                                    </span>
                                @else
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold uppercase tracking-wide px-2 py-0.5 rounded flex items-center justify-center gap-1 max-w-[90px] mx-auto">
                                        <i class="fa-solid fa-check"></i> Good
                                    </span>
                                @endif
                            </td>
                            
                            <td class="p-4 text-right px-5 whitespace-nowrap">
                                <a href="{{ route('admin.inventory.create', $product->id) }}" class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] px-2.5 py-1 rounded transition-colors uppercase tracking-wide shadow-xs">
                                    <i class="fa-solid fa-square-plus"></i> Add Stock
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-sm text-slate-400 bg-slate-50/30">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-inbox text-3xl text-slate-300"></i>
                                    <span>No records located within the central database.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $products->links() }}
            </div>
        @endif
        
    </div>
</div>
@endsection