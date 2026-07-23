@extends('layouts.admin')
@section('title', 'Purchased Products - ' . $customer->name)

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-10" x-data="{ searchQuery: '{{ $search }}' }">
    
    <!-- Header & Action Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.manage', ['fiscal_year' => $fiscalYear]) }}" 
               class="group flex items-center justify-center w-9 h-9 rounded-full bg-slate-50 border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
            </a>
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">Customer Purchase Ledger</h2>
                <p class="text-xs text-slate-500">FY {{ $fiscalYear }} Product Due & Ledger Report</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Fiscal Year Filter -->
            <form method="GET" action="{{ route('admin.customers.purchased-products', $customer->id) }}" class="w-full sm:w-auto">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="relative">
                    <select name="fiscal_year" onchange="this.form.submit()" class="w-full sm:w-auto appearance-none bg-slate-50 border border-slate-200 text-slate-700 text-xs rounded-lg pl-3 pr-8 py-2 focus:ring-2 focus:ring-blue-500 font-semibold cursor-pointer hover:bg-slate-100 transition-colors">
                        @foreach($fiscalYears as $fy)
                            <option value="{{ $fy }}" {{ $fiscalYear == $fy ? 'selected' : '' }}>FY {{ $fy }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </form>

            <!-- Search Field -->
            <div class="w-full sm:w-auto relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Filter by product or invoice..." 
                       class="w-full sm:w-64 pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 text-xs rounded-lg focus:ring-2 focus:ring-blue-500 hover:bg-slate-100 text-slate-700 placeholder-slate-400 transition-colors">
            </div>
        </div>
    </div>

    <!-- Customer Overview Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Customer Name -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user text-base"></i>
            </div>
            <div class="overflow-hidden">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Customer</span>
                <h3 class="text-sm font-bold text-slate-900 truncate mt-0.5">{{ $customer->name }}</h3>
            </div>
        </div>

        <!-- Phone Number -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-phone text-base"></i>
            </div>
            <div class="overflow-hidden">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Phone Number</span>
                <h3 class="text-sm font-bold font-mono text-slate-900 truncate mt-0.5">{{ $customer->phone_number ?? 'N/A' }}</h3>
            </div>
        </div>

        <!-- PAN / VAT -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-id-card text-base"></i>
            </div>
            <div class="overflow-hidden">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PAN / VAT</span>
                <h3 class="text-sm font-bold font-mono text-slate-900 truncate mt-0.5">{{ $customer->pan_number ?? 'N/A' }}</h3>
            </div>
        </div>

        <!-- Address -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-location-dot text-base"></i>
            </div>
            <div class="overflow-hidden">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Address</span>
                <h3 class="text-sm font-bold text-slate-900 truncate mt-0.5" title="{{ $customer->address }}">{{ $customer->address ?? 'N/A' }}</h3>
            </div>
        </div>
    </div>

    <!-- Financial Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Quantity -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Quantity</span>
                <p class="text-xl font-extrabold font-mono text-slate-900 mt-1">{{ number_format($totalQuantity) }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                <i class="fa-solid fa-boxes-stacked text-base"></i>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Billed Amount</span>
                <p class="text-xl font-extrabold font-mono text-slate-900 mt-1">NPR {{ number_format($totalAmountSpent, 2) }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-receipt text-base"></i>
            </div>
        </div>

        <!-- Total Paid -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Total Amount Paid</span>
                <p class="text-xl font-extrabold font-mono text-emerald-600 mt-1">NPR {{ number_format($totalPaidAmount, 2) }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                <i class="fa-solid fa-circle-check text-base"></i>
            </div>
        </div>

        <!-- Remaining Due -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Outstanding Balance</span>
                <p class="text-xl font-extrabold font-mono text-rose-600 mt-1">NPR {{ number_format($totalRemainingDue, 2) }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-600">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Filter Tabs -->
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.purchased-products', ['customer' => $customer->id, 'fiscal_year' => $fiscalYear]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ !request('status') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    All Items
                </a>
                <a href="{{ route('admin.customers.purchased-products', ['customer' => $customer->id, 'fiscal_year' => $fiscalYear, 'status' => 'due']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('status') == 'due' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    <i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i> Due Items Only
                </a>
                <a href="{{ route('admin.customers.purchased-products', ['customer' => $customer->id, 'fiscal_year' => $fiscalYear, 'status' => 'paid']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('status') == 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Fully Paid Items
                </a>
            </div>
        </div>

        <!-- Purchased Products Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Invoice Reference</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Payment Status</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Invoice Balance</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Qty</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Unit Price</th>
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($purchasedItems as $index => $item)
                        @php
                            $pName = strtolower($item->product_name ?? ($item->product->name ?? ''));
                            $invNo = strtolower($item->invoice->invoice_number ?? ($item->invoice->invoice_no ?? ''));
                            
                            $grandTotal = $item->invoice->grand_total ?? 0;
                            $paidAmount = $item->invoice->paid_amount ?? 0;
                            $dueAmount  = max(0, $grandTotal - $paidAmount);
                            
                            if ($dueAmount <= 0) {
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check text-[9px]"></i> Fully Paid</span>';
                            } elseif ($paidAmount > 0) {
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><i class="fa-solid fa-clock text-[9px]"></i> Partial Paid</span>';
                            } else {
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-exclamation text-[9px]"></i> Unpaid</span>';
                            }
                        @endphp
                        
                        <tr class="hover:bg-slate-50/80 transition-colors"
                            x-show="searchQuery === '' || 
                                    '{{ $pName }}'.includes(searchQuery.toLowerCase()) || 
                                    '{{ $invNo }}'.includes(searchQuery.toLowerCase())">
                            
                            <td class="px-5 py-3.5 text-slate-400 font-medium">{{ $purchasedItems->firstItem() + $index }}</td>
                            
                            <!-- Product Name -->
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $item->product_name ?? ($item->product->name ?? 'Custom Item') }}
                            </td>

                            <!-- Invoice Details -->
                            <td class="px-5 py-3.5">
                                @if(isset($item->invoice))
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.invoices.show', $item->invoice->id) }}" class="inline-flex items-center gap-1 font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                            #{{ $item->invoice->invoice_number ?? ($item->invoice->invoice_no ?? $item->invoice->id) }}
                                        </a>
                                    </div>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">{{ \Carbon\Carbon::parse($item->invoice->invoice_date)->format('M d, Y') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="px-5 py-3.5">
                                {!! $statusBadge !!}
                            </td>

                            <!-- Invoice Balance Due -->
                            <td class="px-5 py-3.5 text-right font-mono font-bold">
                                @if($dueAmount > 0)
                                    <span class="text-rose-600">NPR {{ number_format($dueAmount, 2) }}</span>
                                @else
                                    <span class="text-slate-400">NPR 0.00</span>
                                @endif
                            </td>

                            <!-- Quantity -->
                            <td class="px-5 py-3.5 text-center font-bold font-mono text-slate-800">
                                {{ $item->qty }} <span class="text-[10px] text-slate-400 uppercase font-normal">{{ $item->unit ?? 'Pcs' }}</span>
                            </td>

                            <!-- Unit Price -->
                            <td class="px-5 py-3.5 text-right font-mono text-slate-600">
                                NPR {{ number_format($item->price, 2) }}
                            </td>

                            <!-- Line Item Total -->
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                NPR {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl text-slate-300 block mb-3"></i>
                                <p class="text-xs font-semibold text-slate-500">No matching purchased products or invoices found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($purchasedItems->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
                {{ $purchasedItems->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection