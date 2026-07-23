@extends('layouts.admin')

@section('title', 'Invoices Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;" x-data="{ searchQuery: '{{ request('search') }}' }">

    <!-- Top Header & Create Button -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-slate-900 tracking-tight">Tax Invoices</h1>
            <p class="text-xs text-slate-500">Manage, search, and view customer sales invoices</p>
        </div>

        @if(Route::has('admin.invoices.create'))
            <a href="{{ route('admin.invoices.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                <i class="fa-solid fa-plus text-xs"></i> Create New Invoice
            </a>
        @endif
    </div>

    <!-- Financial Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Invoices -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Invoices</span>
                <p class="text-xl font-extrabold font-mono text-slate-900 mt-1">{{ number_format($invoices->total() ?? count($invoices)) }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                <i class="fa-solid fa-file-invoice text-base"></i>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Invoiced</span>
                <p class="text-xl font-extrabold font-mono text-slate-900 mt-1">NPR {{ number_format($totalInvoicedAmount ?? $invoices->sum('grand_total'), 2) }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-receipt text-base"></i>
            </div>
        </div>

        <!-- Paid Collection -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Total Collected</span>
                <p class="text-xl font-extrabold font-mono text-emerald-600 mt-1">NPR {{ number_format($totalPaidAmount ?? $invoices->sum('paid_amount'), 2) }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                <i class="fa-solid fa-circle-check text-base"></i>
            </div>
        </div>

        <!-- Total Unpaid Due -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Outstanding Due</span>
                <p class="text-xl font-extrabold font-mono text-rose-600 mt-1">
                    NPR {{ number_format($totalDueAmount ?? ($invoices->sum('grand_total') - $invoices->sum('paid_amount')), 2) }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
            </div>
        </div>
    </div>

    <!-- Main Invoices List Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Filter Bar & Search Header -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            
            <!-- Payment Status Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto pb-1 md:pb-0">
                <a href="{{ route('admin.invoices.index') }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ !request('status') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    All Invoices
                </a>
                <a href="{{ route('admin.invoices.index', ['status' => 'paid']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request('status') == 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Paid
                </a>
                <a href="{{ route('admin.invoices.index', ['status' => 'partial']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request('status') == 'partial' ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    <i class="fa-solid fa-clock text-[10px] mr-1"></i> Partial
                </a>
                <a href="{{ route('admin.invoices.index', ['status' => 'unpaid']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request('status') == 'unpaid' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    <i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i> Unpaid
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search invoice # or buyer..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-200 text-xs rounded-lg focus:ring-2 focus:ring-blue-500 text-slate-700 placeholder-slate-400">
                </div>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-900 transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <!-- Invoices Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                        <th class="py-3.5 px-4">Invoice No</th>
                        <th class="py-3.5 px-4">Buyer / Customer</th>
                        <th class="py-3.5 px-4">Invoice Date</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Grand Total</th>
                        <th class="py-3.5 px-4 text-right">Paid</th>
                        <th class="py-3.5 px-4 text-right">Balance Due</th>
                        <th class="py-3.5 px-4 text-center w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-800">
                    @forelse($invoices as $index => $invoice)
                        @php
                            $grandTotal = $invoice->grand_total ?? 0;
                            $paidAmount = $invoice->paid_amount ?? 0;
                            $dueAmount  = max(0, $grandTotal - $paidAmount);

                            if ($dueAmount <= 0 && $grandTotal > 0) {
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check text-[9px]"></i> PAID</span>';
                            } elseif ($paidAmount > 0 && $dueAmount > 0) {
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><i class="fa-solid fa-clock text-[9px]"></i> PARTIAL</span>';
                            } else {
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-exclamation text-[9px]"></i> UNPAID</span>';
                            }
                        @endphp

                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Index / Row Number -->
                            <td class="py-3.5 px-4 text-center text-slate-400 font-medium">
                                {{ method_exists($invoices, 'firstItem') ? $invoices->firstItem() + $index : $index + 1 }}
                            </td>

                            <!-- Invoice Number -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    #{{ $invoice->invoice_number ?? $invoice->invoice_no ?? $invoice->id }}
                                </a>
                                @if(!empty($invoice->fiscal_year))
                                    <span class="text-[10px] text-slate-400 block font-normal">FY {{ $invoice->fiscal_year }}</span>
                                @endif
                            </td>

                            <!-- Customer Information -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">
                                    {{ $invoice->customer->name ?? $invoice->customer_name ?? 'N/A' }}
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    PAN: {{ $invoice->customer->pan_number ?? $invoice->pan_number ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Date Details -->
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-700 block">
                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
                                </span>
                                @if(!empty($invoice->nepali_date))
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        BS: {{ $invoice->nepali_date }}
                                    </span>
                                @endif
                            </td>

                            <!-- Payment Status Badge -->
                            <td class="py-3.5 px-4 text-center">
                                {!! $statusBadge !!}
                            </td>

                            <!-- Grand Total -->
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                NPR {{ number_format($grandTotal, 2) }}
                            </td>

                            <!-- Paid Amount -->
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-600">
                                NPR {{ number_format($paidAmount, 2) }}
                            </td>

                            <!-- Remaining Due -->
                            <td class="py-3.5 px-4 text-right font-mono font-bold">
                                @if($dueAmount > 0)
                                    <span class="text-rose-600">NPR {{ number_format($dueAmount, 2) }}</span>
                                @else
                                    <span class="text-slate-400">NPR 0.00</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- View Invoice -->
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" 
                                       title="View Invoice"
                                       class="w-7 h-7 rounded-md bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    @if(Route::has('admin.invoices.pdf'))
                                        <!-- PDF Download -->
                                        <a href="{{ route('route', $invoice->id) }}" 
                                           title="Download PDF"
                                           class="w-7 h-7 rounded-md bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-file-pdf text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-file-invoice text-3xl text-slate-300 block mb-3"></i>
                                <p class="text-xs font-semibold text-slate-500">No invoices found matching your criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if(method_exists($invoices, 'hasPages') && $invoices->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
</div>
@endsection