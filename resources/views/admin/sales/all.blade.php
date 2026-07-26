@extends('layouts.admin')

@section('title', 'All Sales by Customer')
@section('panel_title', 'Comprehensive Sales Ledger')

@section('content')
<div class="space-y-6 pb-12" x-data="{ 
    search: '', 
    allExpanded: false,
    toggleAll() {
        this.allExpanded = !this.allExpanded;
        $dispatch('toggle-all-customers', { state: this.allExpanded });
    }
}">

    <!-- Page Title & Top Control Header -->
    <div class="bg-white p-5 border border-slate-200 rounded-xl shadow-xs flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-xs">
                <i class="fa-solid fa-users-between-lines text-base"></i>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">All Sales by Customer</h1>
                <p class="text-xs text-slate-500">Comprehensive view of all customer transactions and sold products</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Search Input -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" 
                       x-model="search" 
                       placeholder="Search customer, phone, invoice..." 
                       class="text-xs border-slate-300 rounded-lg pl-8 pr-3 py-2 w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50/50">
            </div>

            <!-- Accordion Expand/Collapse Master Toggle -->
            <button type="button" 
                    @click="toggleAll()" 
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 border border-slate-200">
                <i class="fa-solid" :class="allExpanded ? 'fa-compress' : 'fa-expand'"></i>
                <span x-text="allExpanded ? 'Collapse All' : 'Expand All'"></span>
            </button>
        </div>
    </div>

    @php
        $totalCustomers = $customersWithSales->filter(fn($c) => $c->invoices->isNotEmpty())->count();
        $totalInvoicesCount = $customersWithSales->flatMap(fn($c) => $c->invoices)->count();
        $grandTotalSalesSum = $customersWithSales->flatMap(fn($c) => $c->invoices)->sum('grand_total');
        $avgInvoiceValue = $totalInvoicesCount > 0 ? ($grandTotalSalesSum / $totalInvoicesCount) : 0;
    @endphp

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Sales Value -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Sales Value</span>
                <p class="text-base font-bold font-mono text-emerald-600 mt-0.5">NPR {{ number_format($grandTotalSalesSum, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-coins text-sm"></i>
            </div>
        </div>

        <!-- Total Active Customers -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Active Customers</span>
                <p class="text-base font-bold font-mono text-slate-800 mt-0.5">{{ number_format($totalCustomers) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-user-check text-sm"></i>
            </div>
        </div>

        <!-- Total Invoices Issued -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Invoices Issued</span>
                <p class="text-base font-bold font-mono text-slate-800 mt-0.5">{{ number_format($totalInvoicesCount) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-file-invoice text-sm"></i>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Average Invoice</span>
                <p class="text-base font-bold font-mono text-slate-800 mt-0.5">NPR {{ number_format($avgInvoiceValue, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-chart-line text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Customers & Invoices List Accordion Container -->
    <div class="space-y-4">
        @forelse($customersWithSales as $customer)
            @if($customer->invoices->isNotEmpty())
                @php
                    $custInvoiceCount = $customer->invoices->count();
                    $custTotalSpent = $customer->invoices->sum('grand_total');
                @endphp

                <!-- Customer Card Accordion -->
                <div x-data="{ 
                        open: false,
                        customerName: '{{ strtolower(addslashes($customer->name)) }}',
                        customerPhone: '{{ strtolower(addslashes($customer->phone_number ?? '')) }}'
                     }"
                     x-on:toggle-all-customers.window="open = $event.detail.state"
                     x-show="search === '' || customerName.includes(search.toLowerCase()) || customerPhone.includes(search.toLowerCase())"
                     x-cloak
                     class="bg-white border border-slate-200 rounded-xl shadow-2xs overflow-hidden transition-all duration-200">
                    
                    <!-- Customer Card Banner Header (Clickable) -->
                    <div @click="open = !open" 
                         class="p-4 bg-slate-50/80 hover:bg-blue-50/40 cursor-pointer flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 transition-colors">
                        
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs shrink-0 border border-blue-200">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 flex items-center gap-2">
                                    {{ $customer->name }}
                                    @if($customer->phone_number)
                                        <span class="text-xs font-mono font-medium text-slate-400">({{ $customer->phone_number }})</span>
                                    @endif
                                </h2>
                                <p class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span><i class="fa-solid fa-file-invoice text-slate-400 mr-1"></i> {{ $custInvoiceCount }} Invoices</span>
                                    @if($customer->address)
                                        <span>• <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> {{ $customer->address }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <!-- Total Spend Badge -->
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Spent</span>
                                <span class="text-sm font-bold font-mono text-emerald-600">NPR {{ number_format($custTotalSpent, 2) }}</span>
                            </div>

                            <!-- Expand/Collapse Chevron -->
                            <div class="h-8 w-8 rounded-lg bg-white border border-slate-200 text-slate-400 flex items-center justify-center transition-transform duration-200"
                                 :class="open ? 'rotate-180 text-blue-600 border-blue-200' : ''">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible Invoice List Content -->
                    <div x-show="open" x-cloak x-collapse class="p-4 bg-slate-50/30 space-y-4 divide-y divide-slate-100">
                        @forelse($customer->invoices as $invoice)
                            <div class="pt-4 first:pt-0 bg-white border border-slate-200/90 rounded-lg p-4 shadow-2xs">
                                
                                <!-- Invoice Header Summary Bar -->
                                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                            <i class="fa-solid fa-receipt"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800">
                                                Invoice No: <span class="font-mono text-blue-600">#{{ $invoice->invoice_no ?? $invoice->invoice_number ?? $invoice->id }}</span>
                                            </p>
                                            <p class="text-[11px] text-slate-400">
                                                Date: {{ \Carbon\Carbon::parse($invoice->invoice_date ?? $invoice->created_at)->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        @if(isset($invoice->payment_method))
                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $invoice->payment_method }}
                                            </span>
                                        @endif

                                        <p class="text-sm font-extrabold font-mono text-emerald-600 bg-emerald-50 px-3 py-1 rounded-md border border-emerald-100">
                                            NPR {{ number_format($invoice->grand_total, 2) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Sold Items Table -->
                                <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1">
                                    <i class="fa-solid fa-boxes-stacked text-[10px]"></i> Items Sold in this Invoice:
                                </h4>

                                <div class="overflow-x-auto rounded-lg border border-slate-100">
                                    <table class="min-w-full text-left text-xs text-slate-700">
                                        <thead class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                                            <tr>
                                                <th class="px-3.5 py-2">Product</th>
                                                <th class="px-3.5 py-2 text-right">Qty</th>
                                                <th class="px-3.5 py-2 text-right">Price / Unit</th>
                                                <th class="px-3.5 py-2 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            @forelse($invoice->items as $item)
                                                <tr class="hover:bg-slate-50/80 transition-colors">
                                                    <td class="px-3.5 py-2 font-medium text-slate-800">
                                                        {{ $item->product_name ?? ($item->product->name ?? 'N/A Product') }}
                                                    </td>
                                                    <td class="px-3.5 py-2 text-right font-mono font-semibold text-slate-600">
                                                        {{ floatval($item->qty ?? $item->quantity ?? 1) }} {{ $item->unit ?? 'pcs' }}
                                                    </td>
                                                    <td class="px-3.5 py-2 text-right font-mono text-slate-500">
                                                        NPR {{ number_format($item->price ?? $item->unit_price ?? 0, 2) }}
                                                    </td>
                                                    <td class="px-3.5 py-2 text-right font-mono font-bold text-slate-900">
                                                        NPR {{ number_format($item->total ?? $item->total_amount ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="px-3 py-3 text-center text-slate-400 italic">No items for this invoice.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 italic text-xs p-2">No sales records for this customer.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        @empty
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs p-8 text-center text-slate-400">
                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300"></i>
                <p class="text-base font-bold text-slate-700">No customers with sales found.</p>
                <p class="text-xs text-slate-400 mt-1">Start by recording some customer sales in POS!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection