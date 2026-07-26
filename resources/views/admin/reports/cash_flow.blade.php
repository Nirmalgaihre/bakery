@extends('layouts.admin')

@section('title', 'Cash Flow Report')
@section('panel_title', 'Financial Analytics & Cash Flow')

@section('content')
<div class="space-y-6 pb-12 font-sans antialiased text-slate-600">
    
    <!-- Top Action Bar -->
    <div class="bg-white p-5 border border-slate-200 rounded-xl shadow-xs flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-2xs">
                <i class="fa-solid fa-chart-line text-base"></i>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">Cash Flow Report</h1>
                <p class="text-xs text-slate-400">Aggregated sales revenue vs. operational purchases for net profit calculation</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 border border-slate-200 shadow-2xs">
                <i class="fa-solid fa-print text-xs"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Date Range Filter Card -->
    <div class="bg-white p-5 border border-slate-200 rounded-xl shadow-xs">
        <form action="{{ route('admin.reports.cash-flow') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Start Date</label>
                    <div class="relative">
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label for="end_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">End Date</label>
                    <div class="relative">
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition shadow-2xs flex items-center justify-center gap-2">
                        <i class="fa-solid fa-filter text-xs"></i> Apply Date Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    @php
        $margin = $sales > 0 ? (($netProfit / $sales) * 100) : 0;
    @endphp

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Sales (Inflow) -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Revenue (Sales)</span>
                <p class="text-base font-bold font-mono text-emerald-600 mt-0.5">Rs. {{ number_format($sales, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-circle-arrow-down text-sm"></i>
            </div>
        </div>

        <!-- Total Purchases (Outflow) -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Expenses (Purchases)</span>
                <p class="text-base font-bold font-mono text-rose-600 mt-0.5">Rs. {{ number_format($purchases, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-circle-arrow-up text-sm"></i>
            </div>
        </div>

        <!-- Net Profit / Loss -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Net Operating Cash Flow</span>
                <p class="text-base font-bold font-mono {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }} mt-0.5">
                    Rs. {{ number_format($netProfit, 2) }}
                </p>
            </div>
            <div class="w-9 h-9 rounded-lg {{ $netProfit >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center font-bold">
                <i class="fa-solid {{ $netProfit >= 0 ? 'fa-scale-balanced' : 'fa-triangle-exclamation' }} text-sm"></i>
            </div>
        </div>

        <!-- Profit Margin -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Profit Margin Ratio</span>
                <p class="text-base font-bold font-mono {{ $margin >= 0 ? 'text-blue-600' : 'text-rose-600' }} mt-0.5">
                    {{ number_format($margin, 1) }}%
                </p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-percent text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Daily Cash Flow Breakdown Table -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-200/80 bg-slate-50/50 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Daily Breakdown ({{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})
            </h2>
            <span class="text-[11px] font-mono text-slate-400">{{ count($cashFlowData ?? []) }} entries logged</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-3.5 pl-5">Date</th>
                        <th class="p-3.5 text-right">Sales Revenue (Inflow)</th>
                        <th class="p-3.5 text-right">Purchase Costs (Outflow)</th>
                        <th class="p-3.5 text-right pr-5">Net Profit / Loss</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($cashFlowData as $data)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-3.5 pl-5 font-mono font-bold text-slate-800">
                            <i class="fa-solid fa-calendar-day text-slate-400 text-xs mr-1.5"></i>
                            {{ $data['date'] }}
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-emerald-600">
                            Rs. {{ number_format($data['sales'], 2) }}
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-rose-600">
                            Rs. {{ number_format($data['purchases'], 2) }}
                        </td>
                        <td class="p-3.5 pr-5 text-right font-mono font-bold {{ $data['net_profit'] >= 0 ? 'text-emerald-700 bg-emerald-50/40' : 'text-rose-700 bg-rose-50/40' }}">
                            Rs. {{ number_format($data['net_profit'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center text-slate-400">
                            <i class="fa-solid fa-chart-pie text-3xl mb-2 text-slate-300 block"></i>
                            <p class="text-xs font-semibold text-slate-600">No cash flow data found for the selected period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @if(count($cashFlowData ?? []) > 0)
                <tfoot class="bg-slate-100/80 border-t-2 border-slate-300">
                    <tr>
                        <td class="p-4 pl-5 font-bold text-slate-900 uppercase text-[11px] tracking-wider">PERIOD GRAND TOTALS</td>
                        <td class="p-4 text-right font-mono font-extrabold text-emerald-600 text-sm">
                            Rs. {{ number_format($sales, 2) }}
                        </td>
                        <td class="p-4 text-right font-mono font-extrabold text-rose-600 text-sm">
                            Rs. {{ number_format($purchases, 2) }}
                        </td>
                        <td class="p-4 pr-5 text-right font-mono font-extrabold {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }} text-sm">
                            Rs. {{ number_format($netProfit, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection