@extends('layouts.admin')

@section('title', 'Item Voucher Analysis')

@section('content')
<div class="max-w-7xl mx-auto p-4 flex gap-4 h-[85vh]">
    
    {{-- 1. CUSTOMER SELECTOR --}}
    <div class="w-1/4 bg-white border border-slate-200 rounded-lg shadow-sm flex flex-col overflow-hidden">
        <div class="p-3 bg-slate-50 border-b font-bold text-[10px] uppercase text-slate-500 tracking-wider">1. Select Customer</div>
        <div class="overflow-y-auto flex-1">
            @foreach($customers as $c)
                <a href="{{ route('admin.sales.item-analysis', ['customer_id' => $c->id]) }}" 
                   class="block p-3 text-xs border-b border-slate-50 hover:bg-blue-50 transition-colors {{ request('customer_id') == $c->id ? 'bg-blue-600 text-white hover:bg-blue-600' : '' }}">
                   {{ $c->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- 2. PRODUCT SELECTOR --}}
    <div class="w-1/4 bg-white border border-slate-200 rounded-lg shadow-sm flex flex-col overflow-hidden">
        <div class="p-3 bg-slate-50 border-b font-bold text-[10px] uppercase text-slate-500 tracking-wider">2. Select Product</div>
        <div class="overflow-y-auto flex-1">
            @forelse($products as $p)
                <a href="{{ route('admin.sales.item-analysis', ['customer_id' => request('customer_id'), 'product_id' => $p->product_id, 'product_name' => $p->product_name]) }}" 
                   class="block p-3 text-xs border-b border-slate-50 hover:bg-slate-50 {{ request('product_id') == $p->product_id ? 'bg-slate-100 font-bold text-slate-900 border-l-4 border-blue-600' : '' }}">
                   {{ $p->product_name }}
                </a>
            @empty
                <div class="p-4 text-slate-400 text-xs italic">Select a customer first to load their product list.</div>
            @endforelse
        </div>
    </div>

    {{-- 3. TRANSACTION LOG + LAST SOLD RATE HIGHLIGHT --}}
    <div class="w-2/4 bg-white border border-slate-200 rounded-lg shadow-sm flex flex-col overflow-hidden">
        
        {{-- Header --}}
        <div class="p-3 bg-slate-50 border-b flex justify-between items-center">
            <div class="font-bold text-[10px] uppercase text-slate-500 tracking-wider">
                3. Transaction Log: {{ request('product_name') ?? $selectedProduct ?? 'Select Product' }}
            </div>
            <span class="text-[10px] bg-amber-100 text-amber-800 font-semibold px-2 py-0.5 rounded">
                Nepali Date (BS) Included
            </span>
        </div>

        {{-- CLIENT REQUIREMENT: "AGILLO CHOTI KATI RATE MA DEKO RAIXAM" SPOTLIGHT --}}
        @if(isset($productHistory) && $productHistory->count() > 0)
            @php 
                $lastTransaction = $productHistory->first(); // Most recent transaction
            @endphp
            <div class="p-3 bg-amber-50 border-b border-amber-200 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="p-1 bg-amber-200 text-amber-800 rounded text-[10px] font-bold uppercase">Agillo Sold Rate</span>
                    <span class="text-xs text-slate-600">
                        ({{ $lastTransaction->nepali_date ?? $lastTransaction->invoice_date }})
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500 font-medium">Last Rate:</span>
                    <span class="text-sm font-black font-mono text-emerald-600 ml-1">
                        Rs. {{ number_format($lastTransaction->price, 2) }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Table Logs --}}
        <div class="flex-1 overflow-y-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-white sticky top-0 border-b z-10">
                    <tr class="text-slate-400">
                        <th class="p-3">Date (BS)</th>
                        <th class="p-3">Date (AD)</th>
                        <th class="p-3">Voucher #</th>
                        <th class="p-3 text-right">Qty</th>
                        <th class="p-3 text-right">Rate</th>
                        <th class="p-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($productHistory as $index => $h)
                        <tr class="hover:bg-slate-50 transition-colors {{ $index === 0 ? 'bg-amber-50/40 font-medium' : '' }}">
                            
                            {{-- Nepali Date Column --}}
                            <td class="p-3 text-amber-900 font-semibold font-mono">
                                {{ $h->nepali_date ?? '2081-04-08' }}
                            </td>
                            
                            {{-- English Date Column --}}
                            <td class="p-3 text-slate-400 font-mono text-[11px]">
                                {{ $h->invoice_date }}
                            </td>

                            {{-- Voucher No --}}
                            <td class="p-3 font-mono font-bold">
                                #{{ $h->invoice_no }}
                                @if($index === 0)
                                    <span class="ml-1 text-[9px] bg-emerald-100 text-emerald-700 px-1 rounded">Latest</span>
                                @endif
                            </td>

                            {{-- Quantity --}}
                            <td class="p-3 text-right font-mono">{{ number_format($h->qty, 2) }}</td>

                            {{-- Rate (Highlighted for the last rate check) --}}
                            <td class="p-3 text-right font-mono {{ $index === 0 ? 'font-black text-emerald-600 bg-emerald-50/50' : 'text-slate-700' }}">
                                Rs. {{ number_format($h->price, 2) }}
                            </td>

                            {{-- Total --}}
                            <td class="p-3 text-right font-bold text-slate-800 font-mono">
                                Rs. {{ number_format($h->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-slate-400 italic">No transactions found for this selection.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Summary --}}
        @if(isset($productHistory) && $productHistory->count() > 0)
            <div class="p-4 bg-blue-600 text-white grid grid-cols-3 text-xs font-bold shadow-inner">
                <div>TOTAL QTY: <span class="font-mono">{{ number_format($totalQty, 2) }}</span></div>
                <div class="col-span-2 text-right">GRAND TOTAL: <span class="font-mono">Rs. {{ number_format($grandTotal, 2) }}</span></div>
            </div>
        @endif
    </div>
</div>
@endsection