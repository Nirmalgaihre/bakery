@extends('layouts.admin')

@section('title', 'Stock Movement Report')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Stock Movement Report</h1>
            <p class="text-sm text-slate-500">Identify top-selling and slow-moving items.</p>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="mb-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
        <form action="{{ route('admin.reports.stock-movement') }}" method="GET" class="flex items-end gap-4 flex-wrap">
            <div class="flex-grow">
                <label for="period" class="block text-xs font-bold text-slate-500 uppercase mb-1">Select Period</label>
                <select name="period" id="period"
                        class="w-full p-2 border border-slate-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="1month" {{ $period == '1month' ? 'selected' : '' }}>Last 1 Month</option>
                    <option value="3months" {{ $period == '3months' ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="6months" {{ $period == '6months' ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="1year" {{ $period == '1year' ? 'selected' : '' }}>Last 1 Year</option>
                    <option value="all" {{ $period == 'all' ? 'selected' : '' }}>All Time</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">
                Apply Filter
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top-Selling Items --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800">Top-Selling Items ({{ ucfirst($period) }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="p-4">Product Name</th>
                            <th class="p-4 text-right">Total Qty Sold</th>
                            <th class="p-4 text-right">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topSellingItems as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-medium text-slate-900">{{ $item->product->name ?? 'N/A' }}</td>
                            <td class="p-4 text-right text-emerald-600 font-semibold">{{ number_format($item->total_quantity_sold, 2) }} {{ $item->product->inventory_unit ?? 'units' }}</td>
                            <td class="p-4 text-right font-bold text-slate-700">Rs. {{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-10 text-center text-slate-500">No top-selling items found for the selected period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Slow-Moving/Stagnant Stock --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800">Slow-Moving/Stagnant Stock ({{ ucfirst($period) }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="p-4">Product Name</th>
                            <th class="p-4 text-right">Current Stock</th>
                            <th class="p-4 text-right">Qty Sold (Period)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($slowMovingItems as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-medium text-slate-900">{{ $item->name }}</td>
                            <td class="p-4 text-right text-orange-600 font-semibold">{{ number_format($item->initial_stock, 2) }} {{ $item->inventory_unit ?? 'units' }}</td>
                            <td class="p-4 text-right text-slate-700">{{ number_format($item->invoiceItems->sum('qty'), 2) }} {{ $item->inventory_unit ?? 'units' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-10 text-center text-slate-500">No slow-moving items found for the selected period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection