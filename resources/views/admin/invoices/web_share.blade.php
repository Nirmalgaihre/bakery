@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Invoice: {{ $invoice->invoice_no }}</h1>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm hover:bg-slate-700">
            <i class="fa-solid fa-print mr-2"></i> Print Invoice
        </button>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Customer</p>
                <p class="font-semibold">{{ $invoice->patient_name ?? 'Walk-in Customer' }}</p>
            </div>
            <div class="text-right">
                <p class="text-slate-400 font-bold uppercase text-[10px]">Date</p>
                <p class="font-semibold">{{ $invoice->invoice_date }}</p>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead class="border-b border-slate-200">
                <tr class="text-slate-400 uppercase text-[10px] text-left">
                    <th class="pb-3">Product</th>
                    <th class="pb-3 text-right">Quantity</th>
                    <th class="pb-3 text-right">Unit Cost</th>
                    <th class="pb-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($relatedAdjustments as $item)
                <tr class="text-slate-700">
                    <td class="py-3">{{ $item->product->name ?? 'N/A' }}</td>
                    <td class="py-3 text-right">{{ $item->quantity }}</td>
                    <td class="py-3 text-right">Rs. {{ number_format($item->unit_cost, 2) }}</td>
                    <td class="py-3 text-right">Rs. {{ number_format($item->quantity * $item->unit_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6 border-t pt-4 text-right">
            <p class="text-sm">Subtotal: <span class="font-bold">Rs. {{ number_format($calculatedSubtotal, 2) }}</span></p>
            <p class="text-xl font-bold text-emerald-600 mt-2">Grand Total: Rs. {{ number_format($invoice->grand_total, 2) }}</p>
        </div>
    </div>
</div>
@endsection