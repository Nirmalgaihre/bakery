@extends('layouts.admin')

@section('title', 'Customer Sales Report - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Customer Sales Ledger Report')

@section('content')
<div class="max-w-6xl w-full mx-auto">
    
    {{-- Search and Pagination Control Panel --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs p-4 mb-6">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            
            {{-- Search Box --}}
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name or phone..." 
                       class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
            </div>

            {{-- Per Page Dropdown --}}
            <select name="per_page" onchange="this.form.submit()" class="border border-slate-200 rounded text-sm px-3 py-2 outline-none focus:border-blue-500 transition-all cursor-pointer">
                @foreach([10, 25, 50, 100, 500] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>
                        {{ $option }} per page
                    </option>
                @endforeach
            </select>
            
            {{-- Submit Button --}}
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded text-sm font-bold uppercase tracking-wide transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter"></i> Apply
            </button>

            @if(request('search'))
                <a href="{{ route('admin.reports.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded text-sm font-semibold transition-colors flex items-center justify-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Data Table Panel --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center justify-between">
            <span><i class="fa-solid fa-list-ul text-blue-600 mr-2"></i> Registered Customers List</span>
            <span class="text-[10px] text-slate-400">{{ $customers->total() }} Records Found</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-100">
                        <th class="p-4 font-bold">S.N.</th>
                        <th class="p-4 font-bold">Customer Name</th>
                        <th class="p-4 font-bold">Phone Number</th>
                        <th class="p-4 font-bold text-center">Status</th>
                        <th class="p-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($customers as $customer)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/80 transition-colors">
                        <td class="p-4 text-slate-500 font-mono text-xs">
                            {{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
                        </td>
                        <td class="p-4 font-medium text-slate-800">{{ $customer->name }}</td>
                        <td class="p-4 text-slate-600 font-mono text-xs">{{ $customer->phone_number }}</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase">Active</span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.ledger.show', $customer->id) }}" 
                               class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-bold text-[11px] uppercase tracking-wide hover:underline">
                               View Ledger <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 text-sm italic">No records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $customers->appends(request()->query())->links() }}
        </div>
    </div>
</div> 
@endsection