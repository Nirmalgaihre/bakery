@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Top Action Bar & Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Monthly Stock Movement</h1>
            <p class="text-xs text-slate-500 mt-1">
                Overview of monthly stock arrivals, departures, and net balances.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-1 shadow-sm">
                <a href="{{ route('admin.reports.monthly-movement') }}"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-md transition-all {{ $year ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    Monthly Summary
                </a>
                <a href="{{ route('admin.reports.stock-movement') }}"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-md transition-all {{ !$year ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    Audit Trail
                </a>
            </div>

            <button onclick="window.print()"
                class="flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    {{-- Filter Toolbar & Live Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 print:hidden flex flex-col md:flex-row items-center justify-between gap-4">
        
        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-52">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Select Year</label>
                <select name="year" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs font-bold rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach(range(date('Y'), date('Y')-4) as $y)
                    <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-sm transition">
                Apply Filter
            </button>
        </form>

        {{-- Client-Side Search --}}
        <div class="w-full md:w-72">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Quick Product Search</label>
            <input type="text" id="liveProductSearch" placeholder="Type product name..." 
                class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
    </div>

    {{-- Yearly KPI Summary --}}
    @php
        $totalAnnualIn = 0;
        $totalAnnualOut = 0;
        foreach($movements as $mKey => $mItems) {
            $totalAnnualIn += collect($mItems)->where('type', 'Inward')->sum('qty');
            $totalAnnualOut += collect($mItems)->where('type', 'Outward')->sum('qty');
        }
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-700">Total Inward (+)</span>
                <span class="text-2xl font-black font-mono text-emerald-900 mt-1 block">+{{ number_format($totalAnnualIn) }}</span>
            </div>
            <div class="p-2.5 bg-emerald-100 rounded-lg text-emerald-700">
                <i class="fas fa-arrow-down text-lg"></i>
            </div>
        </div>

        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 flex items-center justify-between">
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-rose-700">Total Outward (-)</span>
                <span class="text-2xl font-black font-mono text-rose-900 mt-1 block">-{{ number_format($totalAnnualOut) }}</span>
            </div>
            <div class="p-2.5 bg-rose-100 rounded-lg text-rose-700">
                <i class="fas fa-arrow-up text-lg"></i>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between">
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-blue-700">Net Movement Difference</span>
                <span class="text-2xl font-black font-mono text-blue-900 mt-1 block">
                    {{ number_format($totalAnnualIn - $totalAnnualOut) }}
                </span>
            </div>
            <div class="p-2.5 bg-blue-100 rounded-lg text-blue-700">
                <i class="fas fa-exchange-alt text-lg"></i>
            </div>
        </div>
    </div>

    {{-- Monthly Accordion Breakdown --}}
    <div class="space-y-4">
        @forelse($movements as $month => $items)
            @php
                $groupedProducts = collect($items)->groupBy('product');
                $mIn = collect($items)->where('type', 'Inward')->sum('qty');
                $mOut = collect($items)->where('type', 'Outward')->sum('qty');
                $monthName = date("F", mktime(0, 0, 0, $month, 1));
            @endphp

            <details class="group bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" {{ $loop->first ? 'open' : '' }}>
                
                {{-- Month Header --}}
                <summary class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/80 cursor-pointer list-none select-none border-b border-slate-200">
                    <div class="flex items-center space-x-3">
                        <span class="px-2.5 py-1 bg-indigo-600 text-white font-mono font-bold text-xs rounded-md">
                            M{{ sprintf('%02d', $month) }}
                        </span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">
                                {{ $monthName }}
                            </h3>
                            <span class="text-[11px] text-slate-400 font-medium">{{ $groupedProducts->count() }} items moved</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-2 sm:mt-0">
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-mono font-bold rounded-full">
                            In: +{{ number_format($mIn) }}
                        </span>
                        <span class="px-2.5 py-1 bg-rose-100 text-rose-800 text-xs font-mono font-bold rounded-full">
                            Out: -{{ number_format($mOut) }}
                        </span>
                        <div class="p-1 rounded-full bg-white border border-slate-200 text-slate-400 group-open:rotate-180 transition-transform">
                            <i class="fas fa-chevron-down text-xs w-4 h-4 flex items-center justify-center"></i>
                        </div>
                    </div>
                </summary>

                {{-- Table View --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-100/60 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-5">Product Name</th>
                                <th class="py-3 px-4 text-center">Initial Stock</th>
                                <th class="py-3 px-4 text-center">In (+)</th>
                                <th class="py-3 px-4 text-center">Out (-)</th>
                                <th class="py-3 px-4 text-center">Net Stock</th>
                                <th class="py-3 px-4 text-center print:hidden">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($groupedProducts as $name => $data)
                                @php
                                    $in = collect($data)->where('type', 'Inward')->sum('qty');
                                    $out = collect($data)->where('type', 'Outward')->sum('qty');
                                    $initial = $initialStocks[$name] ?? 0;
                                    $net = $initial + $in - $out;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors product-table-row" data-name="{{ strtolower($name) }}">
                                    <td class="py-3.5 px-5 font-bold text-slate-800">
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
                                        <span class="px-2 py-0.5 rounded-md {{ $net < 0 ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-900' }}">
                                            {{ number_format($net) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center print:hidden">
                                        <a href="#" class="inline-block text-xs text-slate-600 hover:text-blue-600 hover:bg-blue-50 border border-slate-200 px-2.5 py-1 rounded transition">
                                            Details
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
                <p class="text-sm font-bold text-slate-700">No Stock Movements Found</p>
                <p class="text-xs text-slate-400 mt-1">Select a different year to review historical data.</p>
            </div>
        @endforelse
    </div>

</div>

{{-- Live Product Filter Script --}}
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