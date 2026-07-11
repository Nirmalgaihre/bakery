@extends('layouts.admin')

@section('title', 'All Sales by Customer')
@section('panel_title', 'Comprehensive Sales Ledger')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">All Sales by Customer</h1>

    @forelse($customersWithSales as $customer)
        @if($customer->invoices->isNotEmpty())
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm mb-8 p-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4">{{ $customer->name }} ({{ $customer->phone_number }})</h2>

                @forelse($customer->invoices as $invoice)
                    <div class="border border-slate-100 rounded-md p-4 mb-4 bg-slate-50">
                        <div class="flex justify-between items-center mb-3 border-b pb-2 border-slate-200">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Invoice No: <span class="font-mono">{{ $invoice->invoice_no }}</span></p>
                                <p class="text-xs text-slate-500">Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</p>
                            </div>
                            <p class="text-lg font-bold text-green-600">Total: NPR {{ number_format($invoice->grand_total, 2) }}</p>
                        </div>

                        <h4 class="text-xs font-bold uppercase text-slate-500 mb-2">Items Sold:</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-100 text-[10px] font-bold text-slate-500 uppercase">
                                    <tr>
                                        <th class="px-3 py-2">Product</th>
                                        <th class="px-3 py-2 text-right">Qty</th>
                                        <th class="px-3 py-2 text-right">Price/Unit</th>
                                        <th class="px-3 py-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($invoice->items as $item)
                                        <tr>
                                            <td class="px-3 py-2">{{ $item->product_name ?? ($item->product->name ?? 'N/A') }}</td>
                                            <td class="px-3 py-2 text-right">{{ floatval($item->qty) }} {{ $item->unit }}</td>
                                            <td class="px-3 py-2 text-right">NPR {{ number_format($item->price, 2) }}</td>
                                            <td class="px-3 py-2 text-right">NPR {{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-2 text-center text-slate-400 italic">No items for this invoice.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    {{-- This block should ideally not be reached if $customer->invoices->isNotEmpty() check works --}}
                    <p class="text-slate-500 italic">No sales records for this customer.</p>
                @endforelse
            </div>
        @endif
    @empty
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 text-center text-slate-500">
            <p class="text-lg font-semibold">No customers with sales found.</p>
            <p class="text-sm mt-2">Start by making some sales!</p>
        </div>
    @endforelse
</div>
@endsection