@extends('layouts.admin')
@section('title', 'Manage Inventory')

@section('content')
<div class="max-w-4xl mx-auto my-6">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h2 class="text-sm font-bold text-slate-700 uppercase">Product Inventory & Restock</h2>
            <span class="text-[11px] text-slate-400 font-medium">Select a product to perform adjustments</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold">
                        <th class="px-6 py-3">Product Name</th>
                        <th class="px-6 py-3">Current Stock</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($products as $product)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $product->name }}</td>
                        <td class="px-6 py-4 font-mono font-bold text-slate-600">
                            {{ $product->initial_stock }} <span class="text-[10px] text-slate-400">{{ strtoupper($product->inventory_unit) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($product->initial_stock <= $product->alert_stock_level)
                                <span class="px-2 py-1 text-[10px] font-bold uppercase bg-orange-100 text-orange-700 rounded-full">Low Stock</span>
                            @else
                                <span class="px-2 py-1 text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 rounded-full">Healthy</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
    <a href="{{ route('admin.inventory.create', $product->id) }}" 
       class="text-[11px] font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wide">
       Manage
    </a>
</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection