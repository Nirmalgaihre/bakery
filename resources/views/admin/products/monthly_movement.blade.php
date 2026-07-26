@extends('layouts.admin')

@section('title', 'Monthly Stock Movement')
@section('panel_title', 'Inventory Movement Analytics')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 font-sans antialiased text-slate-600">

    <!-- Top Action Bar & Navigation -->
    <div class="bg-white p-5 border border-slate-200 rounded-xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-2xs">
                <i class="fa-solid fa-arrows-left-right text-base"></i>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">Monthly Stock Movement</h1>
                <p class="text-xs text-slate-400">Overview of monthly stock arrivals, departures, and net inventory balances</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Navigation View Switcher -->
            <div class="bg-slate-100 p-1 rounded-lg flex items-center gap-1 border border-slate-200">
                <a href="{{ route('admin.reports.monthly-movement') }}"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-md transition-all {{ $year ? 'bg-white text-blue-600 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <i class="fa-solid fa-calendar-days text-xs mr-1"></i> Monthly Summary
                </a>
                <a href="{{ route('admin.reports.stock-movement') }}"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-md transition-all {{ !$year ? 'bg-white text-blue-600 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <i class="fa-solid fa-list-check text-xs mr-1"></i> Audit Trail
                </a>
            </div>

            <button onclick="window.print()"
                class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-2xs transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Filter Toolbar & Live Search -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-5 print:hidden flex flex-col md:flex-row items-center justify-between gap-4">
        
        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-52">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Select Year</label>
                <select name="year" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-bold rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach(range(date('Y'), date('Y')-4) as $y)
                    <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-2xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-filter text-xs"></i> Apply
            </button>
        </form>

        <!-- Client-Side Search -->
        <div class="w-full md:w-72">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Quick Product Search</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" id="liveProductSearch" placeholder="Type product name..." 
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-8 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- Yearly KPI Summary Metrics -->
    @php
        $totalAnnualIn = 0;
        $totalAnnualOut = 0;
        foreach($movements as $mKey => $mItems) {
            $totalAnnualIn += collect($mItems)->where('type', 'Inward')->sum('qty');
            $totalAnnualOut += collect($mItems)->where('type', 'Outward')->sum('qty');
        }
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Inward Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs flex items-center justify-between">
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-600">Total Inward Stock (+)</span>
                <span class="text-2xl font-black font-mono text-emerald-600 mt-1 block">+{{ number_format($totalAnnualIn) }}</span>
            </div>
            <div class="h-10 w-10 bg-emerald-50 rounded-xl text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-arrow-down-long text-base"></i>
            </div>
        </div>

        <!-- Outward Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs flex items-center justify-between">
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-rose-600">Total Outward Stock (-)</span>
                <span class="text-2xl font-black font-mono text-rose-600 mt-1 block">-{{ number_format($totalAnnualOut) }}</span>
            </div>
            <div class="h-10 w-10 bg-rose-50 rounded-xl text-rose-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-arrow-up-long text-base"></i>
            </div>
        </div>

        <!-- Net Balance Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs flex items-center justify-between">
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-blue-600">Net Movement Difference</span>
                <span class="text-2xl font-black font-mono text-slate-900 mt-1 block">
                    {{ number_format($totalAnnualIn - $totalAnnualOut) }}
                </span>
            </div>
            <div class="h-10 w-10 bg-blue-50 rounded-xl text-blue-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-scale-balanced text-base"></i>
            </div>
        </div>
    </div>

    <!-- Monthly Accordion Breakdown -->
    <div class="space-y-4">
        @forelse($movements as $month => $items)
            @php
                $groupedProducts = collect($items)->groupBy('product');
                $mIn = collect($items)->where('type', 'Inward')->sum('qty');
                $mOut = collect($items)->where('type', 'Outward')->sum('qty');
                $monthName = date("F", mktime(0, 0, 0, $month, 1));
            @endphp

            <details class="group bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden" {{ $loop->first ? 'open' : '' }}>
                
                <!-- Month Header Banner -->
                <summary class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50/80 hover:bg-blue-50/40 cursor-pointer list-none select-none border-b border-slate-200/80 transition-colors">
                    <div class="flex items-center space-x-3">
                        <span class="px-2.5 py-1 bg-blue-600 text-white font-mono font-bold text-xs rounded-md shadow-2xs">
                            M{{ sprintf('%02d', $month) }}
                        </span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">
                                {{ $monthName }}
                            </h3>
                            <span class="text-[11px] text-slate-400 font-medium">{{ $groupedProducts->count() }} unique items moved</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-2 sm:mt-0">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-mono font-bold rounded-md border border-emerald-200">
                            In: +{{ number_format($mIn) }}
                        </span>
                        <span class="px-2.5 py-1 bg-rose-50 text-rose-700 text-xs font-mono font-bold rounded-md border border-rose-200">
                            Out: -{{ number_format($mOut) }}
                        </span>
                        <div class="h-7 w-7 rounded-lg bg-white border border-slate-200 text-slate-400 group-open:rotate-180 transition-transform flex items-center justify-center">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </summary>

                <!-- Monthly Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-5">Product Name</th>
                                <th class="py-3 px-4 text-center">Initial Stock</th>
                                <th class="py-3 px-4 text-center">In (+)</th>
                                <th class="py-3 px-4 text-center">Out (-)</th>
                                <th class="py-3 px-4 text-center">Net Stock</th>
                                <th class="py-3 px-4 text-center print:hidden">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($groupedProducts as $name => $data)
                                @php
                                    $in = collect($data)->where('type', 'Inward')->sum('qty');
                                    $out = collect($data)->where('type', 'Outward')->sum('qty');
                                    $initial = $initialStocks[$name] ?? 0;
                                    $net = $initial + $in - $out;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors product-table-row" data-name="{{ strtolower($name) }}">
                                    <td class="py-3.5 px-5 font-bold text-slate-800 flex items-center gap-2">
                                        <i class="fa-solid fa-box text-slate-400 text-xs"></i>
                                        {{ $name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono text-slate-500">
                                        {{ number_format($initial) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-600">
                                        {{ $in > 0 ? '+'.number_format($in) : '0' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-rose-600">
                                        {{ $out > 0 ? '-'.number_format($out) : '0' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold">
                                        <span class="px-2.5 py-0.5 rounded-md text-xs {{ $net < 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-900 border border-slate-200' }}">
                                            {{ number_format($net) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center print:hidden">
                                        <a href="{{ route('admin.reports.stock-movement') }}?search={{ urlencode($name) }}" 
                                           class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-md transition-colors">
                                            <i class="fa-solid fa-eye"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </details>
        @empty
            <div class="bg-white rounded-xl p-12 text-center border border-dashed border-slate-300">
                <i class="fa-solid fa-box-archive text-3xl text-slate-300 mb-2 block"></i>
                <p class="text-sm font-bold text-slate-700">No Stock Movements Found</p>
                <p class="text-xs text-slate-400 mt-1">Select a different year to review historical data.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- Client-Side Live Product Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveProductSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                
                document.querySelectorAll('.product-table-row').forEach(row => {
                    const productName = row.getAttribute('data-name');
                    row.style.display = productName.includes(term) ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection