@extends('layouts.admin')

@section('title', 'Cash Flow Report')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Cash Flow Report</h1>
            <p class="text-sm text-slate-500">Aggregated sales vs. purchases for net profit calculation.</p>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="mb-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
        <form action="{{ route('admin.reports.cash-flow') }}" method="GET" class="flex items-end gap-4 flex-wrap">
            <div class="flex-grow">
                <label for="start_date" class="block text-xs font-bold text-slate-500 uppercase mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                       class="w-full p-2 border border-slate-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex-grow">
                <label for="end_date" class="block text-xs font-bold text-slate-500 uppercase mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                       class="w-full p-2 border border-slate-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">
                Apply Filter
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 border border-slate-200 rounded-lg shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Total Sales</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1">Rs. {{ number_format($sales, 2) }}</h3>
        </div>
        <div class="bg-white p-5 border border-slate-200 rounded-lg shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Total Purchases</p>
            <h3 class="text-2xl font-bold text-rose-600 mt-1">Rs. {{ number_format($purchases, 2) }}</h3>
        </div>
        <div class="bg-white p-5 border border-slate-200 rounded-lg shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Net Profit</p>
            <h3 class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">Rs. {{ number_format($netProfit, 2) }}</h3>
        </div>
    </div>

    {{-- Daily Breakdown Table --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Daily Breakdown ({{ $startDate }} to {{ $endDate }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-right">Sales</th>
                        <th class="p-4 text-right">Purchases</th>
                        <th class="p-4 text-right">Net Profit/Loss</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cashFlowData as $data)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-medium text-slate-900">{{ $data['date'] }}</td>
                        <td class="p-4 text-right text-emerald-600 font-semibold">Rs. {{ number_format($data['sales'], 2) }}</td>
                        <td class="p-4 text-right text-rose-600 font-semibold">Rs. {{ number_format($data['purchases'], 2) }}</td>
                        <td class="p-4 text-right font-bold {{ $data['net_profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">Rs. {{ number_format($data['net_profit'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-slate-500">No cash flow data found for the selected period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection