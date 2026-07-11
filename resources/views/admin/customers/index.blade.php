@extends('layouts.admin')
@section('title', 'Customers Ledger')

@section('content')
<div class="space-y-6">
    <!-- Header & Filters -->
    <div class="bg-white p-4 border border-slate-200 rounded-md flex flex-wrap gap-4 items-center justify-between">
        <h3 class="text-sm font-bold text-slate-700">Financial Ledger: FY {{ $fiscalYear }}</h3>
        
        <div class="flex gap-4">
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <select name="fiscal_year" onchange="this.form.submit()" class="text-xs border-slate-300 rounded-md p-1">
                    @foreach($fiscalYears as $fy)
                        <option value="{{ $fy }}" {{ $fiscalYear == $fy ? 'selected' : '' }}>FY {{ $fy }}</option>
                    @endforeach
                </select>
            </form>

            <div x-data>
                <form method="GET" action="{{ route('admin.customers.index') }}" x-on:input.debounce.500ms="$el.submit()">
                    <input type="hidden" name="fiscal_year" value="{{ $fiscalYear }}">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search..." class="text-xs border-slate-300 rounded-md p-1 w-48">
                </form>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
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
                        // URL for the row click
                        $url = route('admin.customers.monthly-summary', $item->id) . '?fiscal_year=' . $fiscalYear;
                    @endphp
                    <tr class="hover:bg-slate-50 cursor-pointer transition-colors group" onclick="window.location='{{ $url }}'">
                        <td class="p-4 text-slate-400">{{ $index + 1 }}</td>
                        <td class="p-4 font-medium text-slate-800 group-hover:text-blue-600 transition-colors">
                            {{ $item->name }}
                        </td>
                        <td class="p-4 font-mono text-slate-500">{{ $item->phone_number ?? '-' }}</td>
                        <td class="p-4 text-slate-500">{{ $item->address ?? '-' }}</td>
                        <td class="p-4 font-mono">NPR {{ number_format($opening, 2) }}</td>
                        <td class="p-4 font-mono {{ $net >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($net, 2) }}
                        </td>
                        <td class="p-4 font-mono font-bold text-slate-900">NPR {{ number_format($closing, 2) }}</td>
                        
                        <!-- Actions Cell: Added event.stopPropagation() so clicking 'Edit' doesn't trigger the row click -->
                        <td class="p-4 text-right" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.customers.edit', $item->id) }}" class="text-blue-600 hover:underline font-semibold">Edit</a>
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
@endsection