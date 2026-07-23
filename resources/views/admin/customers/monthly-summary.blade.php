@extends('layouts.admin')

@section('title', 'Monthly Summary - ' . $customer->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
    
    <!-- Top Action Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index', ['fiscal_year' => $fiscalYear]) }}" 
               class="group flex items-center justify-center w-9 h-9 rounded-full bg-slate-50 border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Monthly Financial Summary</h2>
                <p class="text-xs text-slate-500">{{ $customer->name }} • Fiscal Year {{ $fiscalYear }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.customers.index', ['fiscal_year' => $fiscalYear]) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
               <i class="fa-solid fa-book text-[10px]"></i> Back to Ledger
            </a>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Opening Balance Card -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Opening Balance</span>
                <p class="text-lg font-bold font-mono text-slate-800 mt-1">NPR {{ number_format($openingBalance, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                <i class="fa-solid fa-wallet text-xs"></i>
            </div>
        </div>

        <!-- Calculated Summary Items (Pre-calculated below or calculated on load) -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Customer Name</span>
                <p class="text-sm font-bold text-slate-900 mt-1 truncate max-w-[180px]" title="{{ $customer->name }}">{{ $customer->name }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-user text-xs"></i>
            </div>
        </div>

        <!-- FY Year Badge Card -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Active Fiscal Year</span>
                <p class="text-base font-bold font-mono text-slate-800 mt-1">FY {{ $fiscalYear }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i class="fa-solid fa-calendar-days text-xs"></i>
            </div>
        </div>
    </div>

    <!-- Main Monthly Breakdown Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nepali Calendar Monthly Summary</span>
            <span class="text-[11px] text-slate-400"><i class="fa-solid fa-mouse-pointer text-[10px] mr-1"></i> Click any row to view month invoices</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-3.5 text-left pl-5">Month</th>
                        <th class="p-3.5 text-right">Net Transactions (NPR)</th>
                        <th class="p-3.5 text-right pr-5">Running Balance (NPR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    
                    {{-- Opening Balance Row --}}
                    <tr class="bg-slate-50/70">
                        <td class="p-3.5 pl-5 font-bold text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-arrow-right-to-bracket text-slate-400 text-[10px]"></i>
                            Opening Balance
                        </td>
                        <td class="p-3.5 text-right font-mono text-slate-400">-</td>
                        <td class="p-3.5 pr-5 text-right font-mono font-bold text-slate-900">
                            NPR {{ number_format($openingBalance, 2) }}
                        </td>
                    </tr>

                    @php 
                        $runningBalance = $openingBalance; 
                        $totalTransactions = 0;
                    @endphp

                    @foreach($nepaliMonths as $monthNum => $monthName)
                        @php 
                            $monthlyTotal = $monthlyData->get($monthNum, 0);
                            $runningBalance += $monthlyTotal;
                            $totalTransactions += $monthlyTotal;
                        @endphp
                        
                        <tr class="hover:bg-blue-50/60 cursor-pointer transition-colors group" 
                            onclick="window.location.href='{{ route('admin.customers.month-invoices', [$customer->id, $monthNum]) }}?fiscal_year={{ $fiscalYear }}'">
                            
                            <td class="p-3.5 pl-5 font-medium text-slate-700 group-hover:text-blue-600 transition-colors flex items-center justify-between pr-4">
                                <span>{{ $monthName }}</span>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-blue-500 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-1"></i>
                            </td>

                            <td class="p-3.5 text-right font-mono font-semibold {{ $monthlyTotal > 0 ? 'text-slate-800' : ($monthlyTotal < 0 ? 'text-rose-600' : 'text-slate-400') }}">
                                {{ number_format($monthlyTotal, 2) }}
                            </td>

                            <td class="p-3.5 pr-5 text-right font-mono font-bold text-slate-900">
                                {{ number_format($runningBalance, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                
                {{-- Grand Total Footer --}}
                <tfoot class="bg-slate-100/80 border-t-2 border-slate-300">
                    <tr>
                        <td class="p-4 pl-5 font-bold text-slate-900 uppercase text-[11px] tracking-wider">GRAND TOTAL</td>
                        <td class="p-4 text-right font-mono font-extrabold text-slate-900 text-sm">
                            NPR {{ number_format($totalTransactions, 2) }}
                        </td>
                        <td class="p-4 pr-5 text-right font-mono font-extrabold text-slate-900 text-sm">
                            NPR {{ number_format($runningBalance, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection