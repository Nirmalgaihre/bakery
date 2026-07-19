@extends('layouts.admin')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-6 bg-white min-h-screen text-slate-800">
    
    <!-- Simple Clean Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-hourglass-half text-slate-700"></i> Stock Ageing Analysis
            </h1>
            <p class="text-xs text-slate-500 mt-1">Breakdown of current inventory values and quantities grouped by holding periods.</p>
        </div>
        <div class="text-xs font-semibold px-3 py-1.5 bg-slate-100 rounded-lg text-slate-600 border border-slate-200/60">
            Fiscal Year: {{ request('fiscal_year', \App\Helpers\FiscalYearHelper::getCurrentFiscalYear()) }}
        </div>
    </div>

    @php
        // Pre-calculating aggregates for summary row
        $totalQty = 0;
        $s1q = 0; $s1v = 0;
        $s2q = 0; $s2v = 0;
        $s3q = 0; $s3v = 0;
        $s4q = 0; $s4v = 0;

        foreach($reportData as $row) {
            $totalQty += $row['total_qty'];
            $s1q += $row['slabs']['s1']['q']; $s1v += $row['slabs']['s1']['v'];
            $s2q += $row['slabs']['s2']['q']; $s2v += $row['slabs']['s2']['v'];
            $s3q += $row['slabs']['s3']['q']; $s3v += $row['slabs']['s3']['v'];
            $s4q += $row['slabs']['s4']['q']; $s4v += $row['slabs']['s4']['v'];
        }
    @endphp

    <!-- Minimal Filter Form Container -->
    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-5">
        <form method="GET" action="{{ route('admin.reports.stock_ageing') }}" class="space-y-4">
            <!-- Row 1: Timeframes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Fiscal Year</label>
                    <select name="fiscal_year" class="w-full text-xs text-slate-700 rounded-lg border-slate-300 focus:border-slate-500 focus:ring-0 transition-all cursor-pointer">
                        @foreach(\App\Helpers\FiscalYearHelper::getFiscalYearList() as $fy)
                            <option value="{{ $fy }}" {{ request('fiscal_year', \App\Helpers\FiscalYearHelper::getCurrentFiscalYear()) == $fy ? 'selected' : '' }}>
                                {{ $fy }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Period</label>
                    <select id="period" name="period" class="w-full text-xs text-slate-700 rounded-lg border-slate-300 focus:border-slate-500 focus:ring-0 transition-all cursor-pointer">
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="fy" {{ request('period', 'fy') == 'fy' ? 'selected' : '' }}>Current Fiscal Year</option>
                        <option value="last_fy" {{ request('period') == 'last_fy' ? 'selected' : '' }}>Previous Fiscal Year</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    </select>
                </div>

                <div id="fromBox">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">From Date (BS)</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="w-full text-xs text-slate-700 rounded-lg border-slate-300 focus:border-slate-500 focus:ring-0 transition-all">
                </div>

                <div id="toBox">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">To Date (BS)</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="w-full text-xs text-slate-700 rounded-lg border-slate-300 focus:border-slate-500 focus:ring-0 transition-all">
                </div>
            </div>

            <!-- Row 2: Slab Thresholds -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2 border-t border-slate-200/60">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Slab 1 (Days)</label>
                    <input type="number" name="s1" value="{{ $s1 }}" class="w-full text-xs text-slate-700 rounded-lg border-slate-300 focus:border-slate-500 focus:ring-0 transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Slab 2 (Days)</label>
                    <input type="number" name="s2" value="{{ $s2 }}" class="w-full text-xs text-slate-700 rounded-lg border-slate-300 focus:border-slate-500 focus:ring-0 transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Slab 3 (Days)</label>
                    <input type="number" name="s3" value="{{ $s3 }}" class="w-full text-xs text-slate-700 rounded-lg border-slate-300 focus:border-slate-500 focus:ring-0 transition-all">
                </div>

                <div class="flex items-end">
                    <button class="w-full bg-slate-900 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-slate-800 transition-all active:scale-[0.99] shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Clean, Simple Data Table Area -->
    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="font-bold text-slate-600 bg-slate-50 border-b border-slate-200">
                        <th class="py-3 px-4 min-w-[200px]">Particulars</th>
                        <th class="py-3 px-4 text-center border-l border-slate-200/60 bg-slate-100/30 w-28">Total Qty</th>
                        <th class="py-3 px-4 border-l border-slate-200/60 min-w-[140px]">&lt; {{ $s1 }} Days</th>
                        <th class="py-3 px-4 border-l border-slate-200/60 min-w-[140px]">{{ $s1 }} - {{ $s2 }} Days</th>
                        <th class="py-3 px-4 border-l border-slate-200/60 min-w-[140px]">{{ $s2 }} - {{ $s3 }} Days</th>
                        <th class="py-3 px-4 border-l border-slate-200/60 min-w-[140px]">&gt; {{ $s3 }} Days</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reportData as $row)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Particulars / Item Name -->
                            <td class="px-4 py-3.5 font-semibold text-slate-900">
                                {{ $row['name'] }}
                            </td>

                            <!-- Total Quantity Display -->
                            <td class="text-center font-bold text-slate-700 bg-slate-50/20 font-mono border-l border-slate-100">
                                {{ number_format($row['total_qty']) }}
                            </td>

                            <!-- Data Slabs Mapping -->
                            @foreach(['s1', 's2', 's3', 's4'] as $slab)
                                <td class="px-4 py-3.5 border-l border-slate-100 font-mono">
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span class="text-slate-400 font-sans">Qty:</span>
                                        <span class="font-semibold text-slate-700">{{ number_format($row['slabs'][$slab]['q']) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-0.5">
                                        <span class="text-slate-400 font-sans text-[10px]">Val:</span>
                                        <span class="font-bold text-slate-800">Rs. {{ number_format($row['slabs'][$slab]['v'], 2) }}</span>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium bg-slate-50/20">
                                <i class="fa-solid fa-box-open mb-2 text-lg text-slate-300 block"></i>
                                No logs matched this specific configuration layout.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($reportData))
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono font-bold text-slate-900">
                        <tr>
                            <td class="px-4 py-4 font-sans font-bold text-slate-600 tracking-wide uppercase text-[11px]">Totals</td>
                            <td class="text-center text-sm font-extrabold border-l border-slate-200 bg-slate-100/50 text-slate-900">{{ number_format($totalQty) }}</td>
                            
                            <!-- Totals Slabs Columns -->
                            <td class="px-4 py-3 text-right border-l border-slate-200/60">
                                <div class="text-[10px] text-slate-400"><span class="font-sans">Q:</span> {{ number_format($s1q) }}</div>
                                <div class="text-xs text-slate-900 font-bold mt-0.5">Rs. {{ number_format($s1v, 2) }}</div>
                            </td>
                            <td class="px-4 py-3 text-right border-l border-slate-200/60">
                                <div class="text-[10px] text-slate-400"><span class="font-sans">Q:</span> {{ number_format($s2q) }}</div>
                                <div class="text-xs text-slate-900 font-bold mt-0.5">Rs. {{ number_format($s2v, 2) }}</div>
                            </td>
                            <td class="px-4 py-3 text-right border-l border-slate-200/60">
                                <div class="text-[10px] text-slate-400"><span class="font-sans">Q:</span> {{ number_format($s3q) }}</div>
                                <div class="text-xs text-slate-900 font-bold mt-0.5">Rs. {{ number_format($s3v, 2) }}</div>
                            </td>
                            <td class="px-4 py-3 text-right border-l border-slate-200/60">
                                <div class="text-[10px] text-slate-400"><span class="font-sans">Q:</span> {{ number_format($s4q) }}</div>
                                <div class="text-xs text-slate-900 font-bold mt-0.5">Rs. {{ number_format($s4v, 2) }}</div>
                            </td>
                        </tr>
                    </footer>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const period = document.getElementById('period');
    const from = document.getElementById('fromBox');
    const to = document.getElementById('toBox');

    function toggleDates() {
        if (period.value === 'custom') {
            from.style.display = 'block';
            to.style.display = 'block';
        } else {
            from.style.display = 'none';
            to.style.display = 'none';
        }
    }

    toggleDates();
    period.addEventListener('change', toggleDates);
});
</script>
@endsection