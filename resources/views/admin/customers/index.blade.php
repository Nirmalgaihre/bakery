@extends('layouts.admin')
@section('title', 'Customers Ledger')

@section('content')
<div class="space-y-6" x-data="{ viewMode: 'list' }">
    <!-- Header & Controls -->
    <div class="bg-white p-4 border border-slate-200 rounded-xl shadow-xs flex flex-wrap gap-4 items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-xs">
                <i class="fa-solid fa-address-book text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Financial Ledger</h3>
                <p class="text-[11px] text-slate-400">Fiscal Year: FY {{ $fiscalYear }}</p>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="bg-slate-100 p-0.5 rounded-lg flex items-center gap-0.5 border border-slate-200">
                <button type="button" 
                    @click="viewMode = 'list'" 
                    :class="viewMode === 'list' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                    class="px-2.5 py-1 text-xs rounded-md transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all text-xs"></i> List Section
                </button>
                <button type="button" 
                    @click="viewMode = 'table'" 
                    :class="viewMode === 'table' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                    class="px-2.5 py-1 text-xs rounded-md transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-table-cells text-xs"></i> Table View
                </button>
            </div>

            <!-- Fiscal Year Filter -->
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <input type="hidden" name="search" value="{{ $search }}">
                <select name="fiscal_year" onchange="this.form.submit()" class="text-xs border-slate-300 rounded-lg py-1.5 px-2.5 bg-slate-50 text-slate-700 font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($fiscalYears as $fy)
                        <option value="{{ $fy }}" {{ $fiscalYear == $fy ? 'selected' : '' }}>FY {{ $fy }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Search Bar -->
            <div>
                <form method="GET" action="{{ route('admin.customers.index') }}" x-on:input.debounce.500ms="$el.submit()">
                    <input type="hidden" name="fiscal_year" value="{{ $fiscalYear }}">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search customer..." class="text-xs border-slate-300 rounded-lg pl-8 pr-3 py-1.5 w-48 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- LIST SECTION (CARDS) -->
    <div x-show="viewMode === 'list'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($customers as $index => $item)
            @php
                $opening = $item->opening_balance ?? 0;
                $net = $item->net_transactions ?? 0;
                $closing = $opening + $net;
                $url = route('admin.customers.monthly-summary', $item->id) . '?fiscal_year=' . $fiscalYear;
            @endphp
            <div onclick="window.location='{{ $url }}'" 
                 class="group relative bg-white p-4 rounded-xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-blue-300 transition-all duration-200 cursor-pointer flex flex-col justify-between">
                
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-slate-100 group-hover:bg-blue-50 text-slate-600 group-hover:text-blue-600 flex items-center justify-center font-bold text-xs transition-colors border border-slate-200 group-hover:border-blue-200 shrink-0">
                                {{ strtoupper(substr($item->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors truncate">
                                    {{ $item->name }}
                                </h4>
                                <p class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-[9px]"></i> {{ $item->phone_number ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div onclick="event.stopPropagation()" class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ $url }}" 
                               class="px-2 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-semibold border border-emerald-200 transition-colors flex items-center gap-1"
                               title="View Purchased Products">
                                <i class="fa-solid fa-bag-shopping text-[10px]"></i> Items
                            </a>
                            <a href="{{ route('admin.customers.edit', $item->id) }}" 
                               class="h-7 w-7 rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-400 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200"
                               title="Edit Customer">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                        </div>
                    </div>

                    @if($item->address)
                    <div class="text-[11px] text-slate-500 mb-3 flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-md border border-slate-100">
                        <i class="fa-solid fa-location-dot text-[10px] text-slate-400 shrink-0"></i>
                        <span class="truncate">{{ $item->address }}</span>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50/80 p-2.5 rounded-lg border border-slate-100 mb-3">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Opening</span>
                            <span class="font-mono font-medium text-slate-700">NPR {{ number_format($opening, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Net Trans.</span>
                            <span class="font-mono font-medium {{ $net >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ number_format($net, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Closing Balance</span>
                    <span class="font-mono font-bold text-xs text-slate-900 bg-slate-100 group-hover:bg-blue-50 group-hover:text-blue-700 px-2.5 py-1 rounded-md transition-colors border border-slate-200/60">
                        NPR {{ number_format($closing, 2) }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-xl border border-slate-200 text-center text-slate-400">
                <i class="fa-solid fa-address-book text-2xl mb-2 text-slate-300"></i>
                <p class="text-xs">No customer records found.</p>
            </div>
        @endforelse
    </div>

    <!-- TABLE SECTION -->
    <div x-show="viewMode === 'table'" x-cloak class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <th class="p-4 w-12">SN</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Phone</th>
                        <th class="p-4">Address</th>
                        <th class="p-4">Opening</th>
                        <th class="p-4">Net Trans.</th>
                        <th class="p-4">Closing</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($customers as $index => $item)
                        @php
                            $opening = $item->opening_balance ?? 0;
                            $net = $item->net_transactions ?? 0;
                            $closing = $opening + $net;
                            $url = route('admin.customers.monthly-summary', $item->id) . '?fiscal_year=' . $fiscalYear;
                        @endphp
                        <tr class="hover:bg-slate-50 cursor-pointer transition-colors group" onclick="window.location='{{ $url }}'">
                            <td class="p-4 text-slate-400 font-mono">{{ $index + 1 }}</td>
                            <td class="p-4 font-medium text-slate-800 group-hover:text-blue-600 transition-colors">
                                {{ $item->name }}
                            </td>
                            <td class="p-4 font-mono text-slate-500">{{ $item->phone_number ?? '-' }}</td>
                            <td class="p-4 text-slate-500">{{ $item->address ?? '-' }}</td>
                            <td class="p-4 font-mono text-slate-600">NPR {{ number_format($opening, 2) }}</td>
                            <td class="p-4 font-mono {{ $net >= 0 ? 'text-emerald-600 font-semibold' : 'text-rose-600 font-semibold' }}">
                                {{ number_format($net, 2) }}
                            </td>
                            <td class="p-4 font-mono font-bold text-slate-900">NPR {{ number_format($closing, 2) }}</td>
                            
                            <!-- Actions Cell -->
                            <td class="p-4 text-right" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ $url }}" class="text-emerald-600 hover:text-emerald-700 font-semibold bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200 transition-colors">
                                        <i class="fa-solid fa-bag-shopping text-[10px] mr-1"></i> Items Bought
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection