@extends('layouts.app')
@define('panel_title', 'Billing Invoice Presentation Node')

@section('content')
<div class="max-w-4xl mx-auto bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden p-8" id="printable-invoice">
    
    <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
        <div>
            <h1 class="text-xl font-bold uppercase text-slate-800">Deurali Chemicals Pvt. Ltd.</h1>
            <p class="text-xs text-slate-500 mt-1">Kuleshwor, Kathmandu, Nepal</p>
            <p class="text-xs text-slate-500">VAT No: 602345678</p>
        </div>
        <div class="text-right">
            <h2 class="text-lg font-extrabold text-blue-600 uppercase tracking-tight">Tax Invoice</h2>
            <p class="text-xs font-mono font-semibold text-slate-700 mt-1"># {{ $sale->invoice_number }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 text-xs mb-8">
        <div>
            <h4 class="font-bold text-slate-400 uppercase tracking-wider mb-2">Billed To:</h4>
            <p class="text-sm font-bold text-slate-800">{{ $sale->customer->name }}</p>
            <p class="text-slate-600 mt-1">{{ $sale->customer->address ?? 'N/A' }}</p>
            <p class="text-slate-600">Contact: {{ $sale->customer->phone ?? 'N/A' }}</p>
        </div>
        <div class="text-right space-y-1">
            <h4 class="font-bold text-slate-400 uppercase tracking-wider mb-2 text-right">Date Manifest:</h4>
            <p class="text-slate-700"><span class="font-semibold">Invoice Date (AD):</span> {{ $sale->transaction_date }}</p>
            <p class="text-slate-800"><span class="font-semibold text-blue-600">मिति (BS):</span> {{ $sale->nepali_date }}</p>
        </div>
    </div>

    <table class="w-full text-left border-collapse text-xs mb-6">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700 uppercase">
                <th class="p-3">S.N.</th>
                <th class="p-3">Product Description</th>
                <th class="p-3 text-center">Quantity</th>
                <th class="p-3 text-right">Rate (NPR)</th>
                <th class="p-3 text-right text-slate-800">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr class="border-b border-slate-100 text-slate-600">
                <td class="p-3 font-mono">{{ $index + 1 }}</td>
                <td class="p-3 font-medium text-slate-800">{{ $item->product->name }}</td>
                <td class="p-3 text-center font-mono">{{ $item->quantity }}</td>
                <td class="p-3 text-right font-mono">{{ number_format($item->rate, 2) }}</td>
                <td class="p-3 text-right font-mono text-slate-800 font-semibold">{{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="w-72 ml-auto text-xs space-y-2.5 border-t border-slate-100 pt-4">
        <div class="flex justify-between text-slate-600">
            <span>Sub Total:</span>
            <span class="font-mono font-medium">Rs. {{ number_format($sale->sub_total, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-600">
            <span>Discount:</span>
            <span class="font-mono font-medium">Rs. {{ number_format($sale->discount, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-600 border-t border-slate-100 pt-2">
            <span>Taxable Amount:</span>
            <span class="font-mono font-medium">Rs. {{ number_format($sale->taxable_amount, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-600">
            <span>VAT (13%):</span>
            <span class="font-mono font-medium">Rs. {{ number_format($sale->tax_amount, 2) }}</span>
        </div>
        <div class="flex justify-between text-sm font-bold text-slate-800 border-t border-slate-200 pt-2">
            <span>Grand Total:</span>
            <span class="font-mono text-blue-600">Rs. {{ number_format($sale->grand_total, 2) }}</span>
        </div>
    </div>
    
    @if($sale->remarks)
        <div class="mt-8 border-t border-slate-100 pt-4 text-[11px] text-slate-500">
            <span class="font-semibold block mb-1 uppercase tracking-wider text-slate-400">Remarks:</span>
            {{ $sale->remarks }}
        </div>
    @endif
</div>

<div class="max-w-4xl mx-auto flex justify-end gap-3 mt-4">
    <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-4 py-2 rounded shadow-sm transition-all">
        <i class="fa-solid fa-print mr-1.5"></i> Print Invoice
    </button>
</div>
@endsection