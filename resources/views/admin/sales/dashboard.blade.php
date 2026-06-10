@extends('layouts.admin')

@section('title', 'Sales Ledger Control Board')

@section('content')
<div class="w-full mx-auto my-4 p-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Sales Counter Dashboard</h1>
            <p class="text-xs text-slate-500">Real-time point-of-sale accounting tracking matrix entries.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded shadow-xs uppercase tracking-wider">
            <i class="fa-solid fa-plus mr-1"></i> New Counter Sale Dispatch
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs rounded font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase font-bold text-slate-600 tracking-wider">
                    <th class="p-3 pl-5">Timestamp</th>
                    <th class="p-3">Product Item Name</th>
                    <th class="p-3 text-center">Sold Qty</th>
                    <th class="p-3 text-right">Unit Pricing (Rs.)</th>
                    <th class="p-3 text-right">Gross Valuation (Rs.)</th>
                    <th class="p-3 pl-6">Cashier Memo/Ref Note</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="p-3 pl-5 text-slate-400 font-mono">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-3 font-bold text-slate-900">{{ $sale->product->name }}</td>
                        <td class="p-3 text-center font-mono font-bold text-slate-600">{{ $sale->quantity }} {{ strtoupper($sale->product->inventory_unit) }}</td>
                        <td class="p-3 text-right font-mono">Rs. {{ number_format($sale->unit_cost, 2) }}</td>
                        <td class="p-3 text-right font-mono text-emerald-700 font-bold">Rs. {{ number_format($sale->quantity * $sale->unit_cost, 2) }}</td>
                        <td class="p-3 pl-6 text-slate-400 italic font-normal">{{ $sale->reference_note ?? 'Counter Cash Sale' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400 font-medium">No sales transactions documented inside the current cycle register ledger.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($sales->hasPages())
            <div class="p-3 bg-slate-50 border-t border-slate-100">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection