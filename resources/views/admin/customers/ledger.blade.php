@extends('layouts.admin')

@section('title', 'Customer Ledger - ' . $customer->name)

@section('content')
<div class="max-w-6xl mx-auto">
    
    {{-- Header: Customer Info & Balance Card --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="col-span-2 bg-white p-6 rounded-lg border border-slate-200 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">{{ $customer->name }}</h2>
            <p class="text-slate-500 text-sm mt-1"><i class="fa-solid fa-phone mr-2"></i>{{ $customer->phone_number }}</p>
            <p class="text-slate-500 text-sm"><i class="fa-solid fa-location-dot mr-2"></i>{{ $customer->address }}</p>
        </div>
        
        {{-- Outstanding Balance Card --}}
        <div class="bg-blue-600 p-6 rounded-lg shadow-md text-white">
            <h3 class="text-blue-100 uppercase text-xs font-bold tracking-wider">Outstanding Balance</h3>
            <p class="text-3xl font-bold mt-2">Rs. {{ number_format($customer->previous_due, 2) }}</p>
            <button class="mt-4 w-full bg-white text-blue-600 py-2 rounded font-bold text-sm hover:bg-blue-50 transition">
                Receive Payment
            </button>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-700">Transaction History</h3>
            <button class="text-blue-600 text-sm font-semibold hover:underline">
                <i class="fa-solid fa-print mr-1"></i> Print Statement
            </button>
        </div>
        
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold">
                <tr>
                    <th class="p-4">Date</th>
                    <th class="p-4">Description</th>
                    <th class="p-4 text-right">Debit (Dr)</th>
                    <th class="p-4 text-right">Credit (Cr)</th>
                    <th class="p-4 text-right">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                {{-- यहाँ तपाईंको ट्रान्जेक्सन लुप हुन्छ --}}
                @foreach($transactions as $tx)
                <tr>
                    <td class="p-4">{{ $tx->created_at->format('Y-m-d') }}</td>
                    <td class="p-4">{{ $tx->description }}</td>
                    <td class="p-4 text-right text-red-600">{{ number_format($tx->debit, 2) }}</td>
                    <td class="p-4 text-right text-emerald-600">{{ number_format($tx->credit, 2) }}</td>
                    <td class="p-4 text-right font-bold">{{ number_format($tx->balance, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection