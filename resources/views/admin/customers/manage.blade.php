@extends('layouts.admin')
@section('title', 'Manage Customers')

@section('content')
<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-white p-4 border border-slate-200 rounded-md flex flex-wrap gap-4 items-center justify-between shadow-sm">
        <div class="flex items-center gap-4">
            <h3 class="text-sm font-bold text-slate-800">Customer Management</h3>
            <a href="{{ route('admin.customers.create') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold transition-colors">
                + Add Customer
            </a>
        </div>

        <div class="flex gap-4">
            <!-- Fiscal Year Filter -->
            <form method="GET" action="{{ route('admin.customers.manage') }}">
                <select name="fiscal_year" onchange="this.form.submit()" class="text-xs border-slate-300 rounded-md p-1.5">
                    @foreach($fiscalYears as $fy)
                        <option value="{{ $fy }}" {{ $fiscalYear == $fy ? 'selected' : '' }}>FY {{ $fy }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Search -->
            <form method="GET" action="{{ route('admin.customers.manage') }}">
                <input type="hidden" name="fiscal_year" value="{{ $fiscalYear }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, PAN..." class="text-xs border-slate-300 rounded-md p-1.5 w-52">
            </form>
        </div>
    </div>

    <!-- Customer Table -->
    <div class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <th class="p-4 w-12">SN</th>
                    <th class="p-4">Customer Name</th>
                    <th class="p-4">Phone Number</th>
                    <th class="p-4">PAN / VAT No.</th>
                    <th class="p-4">Address</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                @forelse($customers as $index => $item)
                    @php
                        // URL redirects directly to the Purchased Products list
                        $url = route('admin.customers.purchased-products', $item->id) . '?fiscal_year=' . $fiscalYear;
                    @endphp
                    <tr class="hover:bg-slate-50 cursor-pointer transition-colors group" onclick="window.location='{{ $url }}'">
                        <td class="p-4 text-slate-400">{{ $customers->firstItem() + $index }}</td>
                        <td class="p-4 font-semibold text-slate-800 group-hover:text-blue-600 transition-colors">
                            {{ $item->name }}
                        </td>
                        <td class="p-4 font-mono text-slate-600">{{ $item->phone_number ?? '-' }}</td>
                        <td class="p-4 font-mono text-slate-600">{{ $item->pan_number ?? '-' }}</td>
                        <td class="p-4 text-slate-500">{{ $item->address ?? '-' }}</td>
                        
                        <!-- Stop Propagation prevents clicking the edit link from opening the row redirect -->
                        <td class="p-4 text-right" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.customers.edit', $item->id) }}" class="text-blue-600 hover:underline font-semibold">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection