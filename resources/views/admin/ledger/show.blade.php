@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <h2 class="text-xl font-bold mb-4">Ledger for: {{ $customer->name }}</h2>
    
    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="p-4">Invoice No</th>
                    <th class="p-4">Date</th>
                    <th class="p-4 text-right">Amount</th>
                    <th class="p-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer->invoices as $invoice)
                <tr class="border-b hover:bg-slate-50">
                    <td class="p-4 font-mono">#{{ $invoice->invoice_no }}</td>
                    <td class="p-4">{{ $invoice->invoice_date }}</td>
                    <td class="p-4 text-right font-bold">Rs {{ number_format($invoice->grand_total, 2) }}</td>
                    <td class="p-4 text-center">
                        {{-- यहाँ क्लिक गर्दा Invoice View मा जान्छ --}}
                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" 
                           class="text-blue-600 hover:underline font-bold text-sm">
                           View Details
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection