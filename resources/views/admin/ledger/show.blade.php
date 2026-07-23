@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6">
    
    {{-- Customer Profile & Summary Header --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-full bg-amber-100 border border-amber-200 flex items-center justify-center text-amber-700 font-bold text-lg shadow-sm">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $customer->name }}</h1>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mt-0.5">Customer Ledger Statement</p>
                </div>
            </div>

            <div class="flex items-center gap-6 border-t md:border-t-0 pt-4 md:pt-0 border-slate-100">
                <div class="text-left md:text-right">
                    <span class="block text-xs uppercase font-semibold text-slate-400">Total Invoices</span>
                    <span class="text-lg font-bold text-slate-700">{{ $customer->invoices->count() }}</span>
                </div>
                <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
                <div class="text-left md:text-right">
                    <span class="block text-xs uppercase font-semibold text-slate-400">Total Billed</span>
                    <span class="text-xl font-black text-emerald-600 font-mono">
                        Rs {{ number_format($customer->invoices->sum('grand_total'), 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Ledger Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm tracking-wide uppercase">Transaction History</h3>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-200/70 text-slate-700 rounded-full">
                Showing {{ $customer->invoices->count() }} Records
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/70 text-slate-500 text-xs uppercase font-bold tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-6">Invoice Ref</th>
                        <th class="py-3.5 px-6">Billing Date</th>
                        <th class="py-3.5 px-6 text-right">Amount</th>
                        <th class="py-3.5 px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-sm">
                    @forelse($customer->invoices as $invoice)
                    <tr class="hover:bg-amber-50/40 transition-colors duration-150">
                        {{-- Invoice Number --}}
                        <td class="py-4 px-6 font-mono font-semibold text-slate-900">
                            <div class="inline-flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>#{{ $invoice->invoice_no }}</span>
                            </div>
                        </td>

                        {{-- Date --}}
                        <td class="py-4 px-6 font-medium text-slate-600">
                            {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
                        </td>

                        {{-- Amount --}}
                        <td class="py-4 px-6 text-right font-mono font-bold text-slate-900">
                            Rs {{ number_format($invoice->grand_total, 2) }}
                        </td>

                        {{-- Action Button --}}
                        <td class="py-4 px-6 text-center">
                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-amber-600 text-white text-xs font-semibold rounded-md shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1">
                               <span>View Invoice</span>
                               <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                               </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    {{-- Empty State --}}
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-400">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l2-2 4 4m5-6a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-base font-semibold text-slate-600">No Invoices Found</p>
                            <p class="text-xs text-slate-400 mt-1">This customer does not have any recorded transactions yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection