@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Low Stock Alert</h1>
            <p class="text-slate-500 text-sm">Items that have reached or dropped below the alert threshold.</p>
        </div>
        <a href="{{ route('admin.products.index') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
            View All Products
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr
                    class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase text-[11px] tracking-wider font-semibold">
                    <th class="px-6 py-4">Product Name</th>
                    <th class="px-6 py-4 text-center">Current Stock</th>
                    <th class="px-6 py-4 text-center">Alert Level</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($lowStockProducts as $product)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-medium text-slate-900">{{ $product->name }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-md font-bold text-xs">
                            {{ $product->initial_stock }} {{ $product->inventory_unit }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-slate-600 font-medium">{{ $product->alert_stock_level }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($product->email_sent)
                        <div class="flex items-center justify-center gap-1.5 text-green-700">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            <span class="text-[11px] font-bold uppercase tracking-wider">Sent</span>
                        </div>
                        <p class="text-[9px] text-slate-400 mt-0.5">Cooldown Active</p>
                        @else
                        <div class="flex items-center justify-center gap-1.5 text-amber-600">
                            <i class="fa-solid fa-hourglass-half text-[10px]"></i>
                            <span class="text-[11px] font-bold uppercase tracking-wider">Pending</span>
                        </div>
                        <p class="text-[9px] text-slate-400 mt-0.5">Ready to Trigger</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.products.edit', $product->id) }}"
                            class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                            Restock Item
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                        <div class="flex flex-col items-center">
                            <i class="fa-solid fa-check-double text-3xl mb-2 text-green-400"></i>
                            <p>All stock levels are optimal!</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection