@extends('layouts.admin')

@section('title', 'Item Voucher Analysis')

@section('content')
<div class="max-w-7xl mx-auto p-4 flex gap-4 h-[85vh]">
    
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

    <div class="w-2/4 bg-white border border-slate-200 rounded-lg shadow-sm flex flex-col overflow-hidden">
        <div class="p-3 bg-slate-50 border-b font-bold text-[10px] uppercase text-slate-500 tracking-wider">3. Transaction Log: {{ $selectedProduct ?? 'Select Product' }}</div>
        
        <div class="flex-1 overflow-y-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-white sticky top-0 border-b">
                    <tr class="text-slate-400">
                        <th class="p-3">Date</th>
                        <th class="p-3">Voucher #</th>
                        <th class="p-3 text-right">Qty</th>
                        <th class="p-3 text-right">Rate</th>
                        <th class="p-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($productHistory as $h)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 text-slate-500">{{ $h->invoice_date }}</td>
                            <td class="p-3 font-mono font-bold">{{ $h->invoice_no }}</td>
                            <td class="p-3 text-right">{{ number_format($h->qty, 2) }}</td>
                            <td class="p-3 text-right">Rs. {{ number_format($h->price, 2) }}</td>
                            <td class="p-3 text-right font-bold text-slate-800">Rs. {{ number_format($h->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-10 text-center text-slate-400 italic">No transactions found for this selection.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productHistory->count() > 0)
            <div class="p-4 bg-blue-600 text-white grid grid-cols-3 text-xs font-bold shadow-inner">
                <div>TOTAL QTY: <span class="font-mono">{{ number_format($totalQty, 2) }}</span></div>
                <div class="col-span-2 text-right">GRAND TOTAL: <span class="font-mono">Rs. {{ number_format($grandTotal, 2) }}</span></div>
            </div>
        @endif
    </div>
</div>
@endsection