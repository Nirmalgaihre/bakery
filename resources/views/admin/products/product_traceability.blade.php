@extends('layouts.admin')

@section('title', $activeSupplier ? 'Item Traceability - ' . $activeSupplier->name : 'Item Traceability Matrix')
@section('panel_title', 'Item Movement Analysis')

@section('content')
@php
    $totalInwardQty = 0;
    $totalOutwardQty = 0;
    $totalInwardValue = 0;
    $totalOutwardValue = 0;

    if ($activeSupplier) {
        $totalInwardQty     = (float) $inwards->sum('initial_stock'); 
        $totalOutwardQty    = (float) $outwards->sum('qty');
        $totalInwardValue   = (float) $inwards->sum(fn($r) => $r->initial_stock * $r->purchase_cost);
        $totalOutwardValue  = (float) $outwards->sum('total');
    }
@endphp

<div class="mx-auto max-w-7xl space-y-5">

    {{-- ============================= GLOBAL SUPPLIER SELECTOR ============================= --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm print:hidden">
        <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Select Active Supplier Ledger</label>
                <select name="supplier_id" onchange="this.form.submit()" class="w-full text-sm border border-slate-300 rounded-lg p-2 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">-- Choose a Supplier to View Traceability Movement Analysis --</option>
                    @foreach($allSuppliers as $sup)
                        <option value="{{ $sup->id }}" {{ optional($activeSupplier)->id == $sup->id ? 'selected' : '' }}>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                @if($activeSupplier)
                    <a href="{{ url()->current() }}" class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold text-slate-600 hover:bg-slate-100 transition">
                        <i class="fa-solid fa-rotate-left"></i> Reset Selection
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if(!$activeSupplier)
        <div class="rounded-xl border border-dashed border-slate-300 bg-white py-16 text-center shadow-sm">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 text-slate-400">
                <i class="fa-solid fa-truck-field text-lg"></i>
            </div>
            <h3 class="mt-4 text-sm font-semibold text-slate-900">No Supplier Profile Selected</h3>
            <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Please select a corporate vendor from the dropdown options above to pull item records.</p>
        </div>
    @else
        {{-- ============================= KPI METRIC STATUS BLOCKS ============================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Initial Stock Loaded</span>
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700"><i class="fa-solid fa-arrow-down-long"></i></span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-xl font-bold text-slate-900">{{ number_format($totalInwardQty, 2) }}</span>
                    <span class="text-xs font-semibold text-slate-500">units</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-500">Procurement Valued at <span class="font-semibold text-slate-800">Rs {{ number_format($totalInwardValue, 2) }}</span></div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Units Dispatched</span>
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-50 text-orange-700"><i class="fa-solid fa-arrow-up-long"></i></span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-xl font-bold text-slate-900">{{ number_format($totalOutwardQty, 2) }}</span>
                    <span class="text-xs font-semibold text-slate-500">units</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-500">Gross Sales Volume <span class="font-semibold text-slate-800">Rs {{ number_format($totalOutwardValue, 2) }}</span></div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Remaining Floor Balance</span>
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-700"><i class="fa-solid fa-boxes-stacked"></i></span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-xl font-bold text-blue-600">{{ number_format($inwards->sum('stock'), 2) }}</span>
                    <span class="text-xs font-semibold text-slate-500">units on hand</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-500">Current running dynamic stock count</div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Supplier Account</span>
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700"><i class="fa-solid fa-building"></i></span>
                </div>
                <div class="mt-2 truncate">
                    <span class="text-sm font-bold text-indigo-900 block truncate">{{ $activeSupplier->name }}</span>
                    <span class="text-[10px] text-slate-400 font-medium block truncate">Supplier Identifier Code: #{{ $activeSupplier->id }}</span>
                </div>
            </div>
        </div>

        {{-- ============================= MOVEMENT ANALYSIS DATA MATRIX ============================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- INWARD LEDGER BLOCK (PRODUCTS TABLE) --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">What We Buy (Products Catalog Summary)</h2>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase">
                                <th class="p-3">Item Code</th>
                                <th class="p-3">Product Name</th>
                                <th class="p-3 text-right">Initial Stock</th>
                                <th class="p-3 text-right">Purchase Cost</th>
                                <th class="p-3 text-right">Dynamic Cost Allocation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                            @forelse($inwards as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 text-slate-400 font-mono">{{ $product->item_code ?? 'N/A' }}</td>
                                    <td class="p-3 font-semibold text-slate-800">
                                        {{ $product->name }}
                                        @if($product->size || $product->color)
                                            <span class="block text-[10px] font-normal text-slate-400">
                                                {{ $product->color }} {{ $product->size ? '| ' . $product->size : '' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right font-medium text-slate-900">
                                        {{ number_format($product->initial_stock, 2) }} 
                                        <span class="text-[10px] text-slate-400 font-normal">{{ $product->inventory_unit ?? 'units' }}</span>
                                    </td>
                                    <td class="p-3 text-right text-slate-500">Rs {{ number_format($product->purchase_cost, 2) }}</td>
                                    <td class="p-3 text-right font-semibold text-emerald-600">Rs {{ number_format($product->initial_stock * $product->purchase_cost, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 italic">No catalog entry products found under this supplier profile.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- OUTWARD LEDGER BLOCK (INVOICE ITEMS) --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Sold To Customers (Dispatched Invoices)</h2>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase">
                                <th class="p-3">Invoice No</th>
                                <th class="p-3">Customer / Patient Name</th>
                                <th class="p-3">Product Item Name</th>
                                <th class="p-3 text-right">Qty</th>
                                <th class="p-3 text-right">Gross Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                            @forelse($outwards as $item)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 font-semibold text-blue-600">
                                        {{ data_get($item, 'invoice.invoice_no') ?? data_get($item, 'invoice.invoice_number') ?? 'N/A' }}
                                    </td>
                                    <td class="p-3 font-medium text-slate-900">
                                        {{ data_get($item, 'invoice.patient_name') ?? 'Walk-in Buyer' }}
                                        @if(!empty(data_get($item, 'invoice.patient_city')))
                                            <span class="block text-[10px] text-slate-400 font-normal">
                                                <i class="fa-solid fa-location-dot text-[9px]"></i> {{ $item->invoice->patient_city }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-700 font-medium text-left">{{ $item->product_name }}</td>
                                    <td class="p-3 text-right text-slate-900">
                                        {{ number_format($item->qty, 2) }}
                                        <span class="text-[10px] text-slate-400 font-normal">{{ $item->unit ?? 'units' }}</span>
                                    </td>
                                    <td class="p-3 text-right font-semibold text-orange-600">Rs {{ number_format($item->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 italic">No matching sales transactions found for this supplier's products.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection