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
                        <th class="p-4 text-right">Purchase</th>
                        <th class="p-4 text-right">Selling</th>
                        <th class="p-4 text-center">Unit</th>
                        <th class="p-4 text-center bg-slate-50 border-x border-slate-200/60">Initial Stock</th>
                        <th class="p-4 text-center bg-blue-50/40 text-blue-900 font-bold">Current Stock</th>
                        <th class="p-4 text-center bg-blue-100/30 text-blue-900 font-bold">Total</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        @php 
                            $initial = $product->initial_stock ?? 0;
                            $current = $product->stock ?? 0;
                            $totalAvailable = $initial + $current;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 px-5 font-semibold text-slate-900">{{ $product->name }}</td>
                            <td class="p-4">
                                <span class="bg-slate-100 text-slate-700 text-xs px-2.5 py-1 rounded-full font-medium border border-slate-200/50">{{ $product->category }}</span>
                            </td>
                            <td class="p-4 text-right font-mono text-xs text-slate-600">Rs. {{ number_format($product->purchase_cost, 2) }}</td>
                            <td class="p-4 text-right font-mono text-xs font-bold text-slate-900">Rs. {{ number_format($product->selling_price, 2) }}</td>
                            <td class="p-4 text-center">
                                <span class="uppercase font-mono text-[11px] bg-slate-100 text-slate-600 border border-slate-200 px-2 py-0.5 rounded">{{ $product->inventory_unit }}</span>
                            </td>
                            
                            {{-- Stocks --}}
                            <td class="p-4 text-center font-mono text-slate-500 bg-slate-50/50 border-x">{{ $initial }}</td>
                            <td class="p-4 text-center font-mono text-slate-700">{{ $current }}</td>
                            
                            {{-- Combined Total --}}
                            <td class="p-4 text-center font-bold font-mono text-blue-700 bg-blue-50/20">
                                {{ $totalAvailable }}
                            </td>
                            
                            {{-- Status Alert --}}
                            <td class="p-4 text-center">
                                @if($totalAvailable <= $product->alert_stock_level)
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold uppercase px-2 py-0.5 rounded animate-pulse">Low</span>
                                @else
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase px-2 py-0.5 rounded">Good</span>
                                @endif
                            </td>
                            
                            <td class="p-4 text-right px-5">
                                <a href="{{ route('admin.inventory.create', $product->id) }}" class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] px-2.5 py-1 rounded uppercase shadow-xs">
                                    Add
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="p-8 text-center text-sm text-slate-400">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="p-4 border-t bg-slate-50/50">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection