@extends('layouts.admin')

@section('title', 'Item Voucher Analysis')
@section('panel_title', 'Customer Item Rate & Voucher Analysis')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 pb-12 font-sans antialiased text-slate-600">

    <!-- Top Action Bar -->
    <div class="bg-white p-4 border border-slate-200 rounded-xl shadow-xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold shadow-2xs">
                <i class="fa-solid fa-calculator text-base"></i>
            </div>
            <div>
                <h1 class="text-base font-extrabold text-slate-900 tracking-tight">Item Voucher & Rate Analysis</h1>
                <p class="text-xs text-slate-400">Check previous selling rates (Agillo Sold Rate) and transaction logs by customer</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales.item-analysis') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors border border-slate-200 flex items-center gap-1.5">
                <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset Selection
            </a>
        </div>
    </div>

    <!-- 3-Column Analysis Grid Container -->
    <div class="flex flex-col lg:flex-row gap-4 h-auto lg:h-[78vh]">
        
        {{-- =========================================================
             STEP 1: CUSTOMER SELECTOR PANEL
        ========================================================== --}}
        <div class="w-full lg:w-1/4 bg-white border border-slate-200 rounded-xl shadow-xs flex flex-col overflow-hidden max-h-[400px] lg:max-h-full">
            <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <span class="font-bold text-[10px] uppercase text-slate-500 tracking-wider flex items-center gap-1.5">
                    <span class="h-4 w-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[9px] font-bold">1</span>
                    Select Customer
                </span>
                <span class="text-[10px] text-slate-400 font-mono font-semibold">{{ count($customers ?? []) }} parties</span>
            </div>

            <!-- Customer Search Filter -->
            <div class="p-2 border-b border-slate-100 bg-slate-50/50">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2 text-slate-400 text-[11px]"></i>
                    <input type="text" id="custSearchInput" placeholder="Search customer..." 
                        class="w-full pl-7 pr-3 py-1 bg-white border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="overflow-y-auto flex-1 divide-y divide-slate-100 no-scrollbar" id="customerListContainer">
                @forelse($customers as $c)
                    @php
                        $isSelected = request('customer_id') == $c->id;
                    @endphp
                    <a href="{{ route('admin.sales.item-analysis', ['customer_id' => $c->id]) }}" 
                       class="customer-item block p-3 text-xs transition-all {{ $isSelected ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'hover:bg-blue-50/60 text-slate-700' }}"
                       data-name="{{ strtolower($c->name) }}">
                       <div class="flex items-center justify-between">
                           <span class="truncate">{{ $c->name }}</span>
                           @if($isSelected)
                               <i class="fa-solid fa-chevron-right text-[10px] text-white/80"></i>
                           @endif
                       </div>
                    </a>
                @empty
                    <div class="p-4 text-slate-400 text-xs italic text-center">No customers available.</div>
                @endforelse
            </div>
        </div>

        {{-- =========================================================
             STEP 2: PRODUCT SELECTOR PANEL
        ========================================================== --}}
        <div class="w-full lg:w-1/4 bg-white border border-slate-200 rounded-xl shadow-xs flex flex-col overflow-hidden max-h-[400px] lg:max-h-full">
            <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <span class="font-bold text-[10px] uppercase text-slate-500 tracking-wider flex items-center gap-1.5">
                    <span class="h-4 w-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[9px] font-bold">2</span>
                    Select Product
                </span>
                <span class="text-[10px] text-slate-400 font-mono font-semibold">{{ count($products ?? []) }} items</span>
            </div>

            <!-- Product Search Filter -->
            <div class="p-2 border-b border-slate-100 bg-slate-50/50">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2 text-slate-400 text-[11px]"></i>
                    <input type="text" id="prodSearchInput" placeholder="Search product..." 
                        class="w-full pl-7 pr-3 py-1 bg-white border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="overflow-y-auto flex-1 divide-y divide-slate-100 no-scrollbar" id="productListContainer">
                @forelse($products as $p)
                    @php
                        $isSelectedProd = request('product_id') == $p->product_id;
                    @endphp
                    <a href="{{ route('admin.sales.item-analysis', ['customer_id' => request('customer_id'), 'product_id' => $p->product_id, 'product_name' => $p->product_name]) }}" 
                       class="product-item block p-3 text-xs transition-all {{ $isSelectedProd ? 'bg-slate-900 text-white font-bold border-l-4 border-amber-500' : 'hover:bg-slate-50 text-slate-700' }}"
                       data-name="{{ strtolower($p->product_name) }}">
                       <div class="flex items-center justify-between">
                           <span class="truncate">{{ $p->product_name }}</span>
                           @if($isSelectedProd)
                               <i class="fa-solid fa-check text-[10px] text-amber-400"></i>
                           @endif
                       </div>
                    </a>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs italic">
                        <i class="fa-solid fa-arrow-left text-slate-300 text-base mb-1 block"></i>
                        Select a customer first to load their purchased items list.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- =========================================================
             STEP 3: TRANSACTION LOG & LAST SOLD RATE HIGHLIGHT PANEL
        ========================================================== --}}
        <div class="w-full lg:w-2/4 bg-white border border-slate-200 rounded-xl shadow-xs flex flex-col overflow-hidden">
            
            <!-- Panel Header -->
            <div class="p-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                <span class="font-bold text-[10px] uppercase text-slate-700 tracking-wider flex items-center gap-1.5">
                    <span class="h-4 w-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[9px] font-bold">3</span>
                    Transaction Log: <span class="text-blue-600 font-bold ml-1">{{ request('product_name') ?? $selectedProduct ?? 'Select Product' }}</span>
                </span>
                <span class="text-[10px] bg-amber-50 text-amber-700 border border-amber-200 font-bold px-2 py-0.5 rounded-md">
                    <i class="fa-solid fa-calendar-days mr-1"></i> BS Date Included
                </span>
            </div>

            {{-- CLIENT REQUIREMENT: "AGILLO CHOTI KATI RATE MA DEKO RAIXAM" SPOTLIGHT BANNER --}}
            @if(isset($productHistory) && $productHistory->count() > 0)
                @php 
                    $lastTransaction = $productHistory->first(); // Most recent transaction
                @endphp
                <div class="p-3.5 bg-gradient-to-r from-amber-500/10 via-amber-50/60 to-white border-b border-amber-200/80 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-amber-500 text-white rounded-md text-[10px] font-bold uppercase shadow-2xs tracking-wider">
                            <i class="fa-solid fa-tag mr-1"></i> Agillo Sold Rate
                        </span>
                        <span class="text-xs font-mono font-medium text-slate-500">
                            ({{ $lastTransaction->nepali_date ?? $lastTransaction->invoice_date }})
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] text-slate-400 font-semibold uppercase">Last Rate:</span>
                        <span class="text-base font-black font-mono text-emerald-600 ml-1.5">
                            Rs. {{ number_format($lastTransaction->price, 2) }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- History Log Table --}}
            <div class="flex-1 overflow-y-auto no-scrollbar">
                <table class="w-full text-xs text-left whitespace-nowrap border-collapse">
                    <thead class="bg-slate-50 sticky top-0 border-b border-slate-200 z-10 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="p-3 pl-4">Date (BS)</th>
                            <th class="p-3">Date (AD)</th>
                            <th class="p-3">Voucher #</th>
                            <th class="p-3 text-right">Qty</th>
                            <th class="p-3 text-right">Rate</th>
                            <th class="p-3 text-right pr-4">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($productHistory ?? [] as $index => $h)
                            <tr class="hover:bg-blue-50/40 transition-colors {{ $index === 0 ? 'bg-amber-50/50 font-medium' : '' }}">
                                
                                {{-- Nepali Date --}}
                                <td class="p-3 pl-4 text-amber-900 font-semibold font-mono">
                                    {{ $h->nepali_date ?? '-' }}
                                </td>
                                
                                {{-- English Date --}}
                                <td class="p-3 text-slate-400 font-mono text-[11px]">
                                    {{ $h->invoice_date }}
                                </td>

                                {{-- Voucher # --}}
                                <td class="p-3 font-mono font-bold text-blue-600">
                                    #{{ $h->invoice_no }}
                                    @if($index === 0)
                                        <span class="ml-1 text-[9px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-bold uppercase">Latest</span>
                                    @endif
                                </td>

                                {{-- Quantity --}}
                                <td class="p-3 text-right font-mono font-bold text-slate-700">
                                    {{ number_format($h->qty, 2) }}
                                </td>

                                {{-- Rate (Highlighted for last rate check) --}}
                                <td class="p-3 text-right font-mono {{ $index === 0 ? 'font-black text-emerald-600 bg-emerald-50/60' : 'text-slate-800' }}">
                                    Rs. {{ number_format($h->price, 2) }}
                                </td>

                                {{-- Total Amount --}}
                                <td class="p-3 pr-4 text-right font-bold text-slate-900 font-mono">
                                    Rs. {{ number_format($h->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-slate-400">
                                    <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-2 block"></i>
                                    No item voucher records found for this selection. Select a customer and product to view rate history.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Summary Ribbon --}}
            @if(isset($productHistory) && $productHistory->count() > 0)
                <div class="p-3.5 bg-slate-900 text-white flex flex-wrap items-center justify-between text-xs font-bold shadow-inner">
                    <div class="font-mono">TOTAL QTY: <span class="text-amber-400">{{ number_format($totalQty, 2) }}</span></div>
                    <div class="font-mono text-right">GRAND TOTAL: <span class="text-emerald-400">Rs. {{ number_format($grandTotal, 2) }}</span></div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Client-Side Search Filters Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Customer search
    const custInput = document.getElementById('custSearchInput');
    if(custInput) {
        custInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.customer-item').forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(term) ? '' : 'none';
            });
        });
    }

    // Product search
    const prodInput = document.getElementById('prodSearchInput');
    if(prodInput) {
        prodInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(term) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection