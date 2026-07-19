@extends('layouts.admin')

@section('title', 'Stock Position Report')

@section('content')
<!-- Removed max-w-7xl limit to allow the view to expand completely wide across your dashboard layout -->
<div class="w-full mx-auto space-y-4" style="font-family: 'Times New Roman', Times, serif;">

    <!-- Top Telemetry Header & Fiscal Selector Interface Matrix -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs p-4 px-5 mt-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight uppercase">Deurali Chemicals Pvt. Ltd.</h1>
                <p class="text-xs text-slate-500 mt-0.5">Stock Position Ledger Matrix • Live Inventory Valuation</p>
            </div>
            
            <!-- Dynamic Fiscal Filtering Action Form Container -->
            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 flex-wrap w-full md:w-auto">
                <div class="flex flex-col">
                    <label for="fiscal_year" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Fiscal Year Matrix</label>
                    <select name="fiscal_year" id="fiscal_year" onchange="this.form.submit()" 
                        class="text-xs border border-slate-300 rounded px-2 py-1.5 bg-slate-50 focus:ring-1 focus:ring-blue-500 outline-none w-40 font-semibold text-slate-700">
                        @foreach(\App\Helpers\FiscalYearHelper::getFiscalYearList() as $fy)
                            <option value="{{ $fy }}" {{ $selectedFiscalYear == $fy ? 'selected' : '' }}>
                                FY {{ $fy }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Live Date Scope</label>
                    <div class="bg-slate-100 text-slate-700 font-mono text-xs px-3 py-1.5 rounded border border-slate-200 font-semibold whitespace-nowrap">
                        {{ $fromDate }} TO {{ $toDate }}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table Registry and Search Controls Container -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Valuation & Stock Balances Data Grid
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <!-- Advanced Real-time Search Input -->
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search particulars or item codes..." 
                    class="text-xs border border-slate-300 rounded px-3 py-1.5 focus:ring-1 focus:ring-blue-500 outline-none w-full sm:w-64">
            </div>
        </div>

        <!-- Scroll wraps stripped down to prevent artificial containment -->
        <div class="w-full block">
            <!-- Table min-width baseline removed. table-layout fixed allows structural fluid compression without spilling -->
            <table class="w-full text-left border-collapse text-[11px] text-slate-700 table-fixed" id="stockReportTable">
                <thead>
                    <tr class="bg-slate-800 text-white text-[10px] uppercase tracking-wider font-bold">
                        <th rowspan="2" class="border border-slate-700 p-2 pl-3 w-[15%]">Particulars</th>
                        <th colspan="3" class="border border-slate-700 p-1 text-center">Opening Balance</th>
                        <th colspan="3" class="border border-slate-700 p-1 text-center">Inwards (Purchase)</th>
                        <th colspan="3" class="border border-slate-700 p-1 text-center">Outwards (Sales/Adj)</th>
                        <th colspan="2" class="border border-slate-700 p-1 text-center pr-3">Closing Balance</th>
                    </tr>
                    <tr class="bg-slate-700 text-slate-200 text-[9px] uppercase font-bold tracking-wide">
                        <th class="border border-slate-600 p-1.5 text-right w-[7%]">Qty</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[6%]">Rate</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[8%]">Value</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[7%]">Qty</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[6%]">Rate</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[8%]">Value</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[6%]">Sales</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[6%]">Adj.</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[7%]">Net</th>
                        <th class="border border-slate-600 p-1.5 text-right w-[8%]">Qty</th>
                        <th class="border border-slate-600 p-1.5 text-right pr-3 w-[11%]">Value</th>
                    </tr>
                </thead>
                <tbody id="tableContent" class="divide-y divide-slate-200 bg-white">
                    @forelse($stockReport as $item)
                        @php
                            $unit = $item->inventory_unit ?? '';
                            $pQty = $item->total_purchase ?? 0;
                            $pVal = $item->total_purchase_value ?? 0;
                            $pRate = $pQty > 0 ? ($pVal / $pQty) : 0;
                            $sQty = $item->total_sale ?? 0;
                            $aQty = $item->total_adjustment ?? 0;
                            $netOut = $sQty + $aQty;
                            $balanceQty = $item->initial_stock + $pQty - $sQty + $aQty;
                            $balanceValue = $balanceQty * $item->purchase_cost;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors data-row">
                            <!-- Particular Name / Item Code Matrix Fields with text truncation to save horizontal width -->
                            <td class="border border-slate-100 p-2 pl-3 font-semibold text-slate-900 search-item truncate" title="{{ $item->name }}">
                                <span class="search-name block truncate">{{ $item->name }}</span>
                                <span class="block text-[9px] font-normal text-slate-400 font-mono search-code truncate">Code: {{ $item->item_code }}</span>
                            </td>
                            
                            <!-- Condensed tabular structures with compact padding properties -->
                            <td class="border border-slate-100 p-1.5 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format($item->initial_stock, 2) }} <span class="text-[9px] font-sans text-slate-400 font-normal uppercase">{{ $unit }}</span></td>
                            <td class="border border-slate-100 p-1.5 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format($item->purchase_cost, 2) }}</td>
                            <td class="border border-slate-100 p-1.5 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format($item->initial_stock * $item->purchase_cost, 2) }}</td>
                            
                            <td class="border border-slate-100 p-1.5 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format($pQty, 2) }} <span class="text-[9px] font-sans text-slate-400 font-normal uppercase">{{ $unit }}</span></td>
                            <td class="border border-slate-100 p-1.5 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format($pRate, 2) }}</td>
                            <td class="border border-slate-100 p-1.5 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format($pVal, 2) }}</td>
                            
                            <td class="border border-slate-100 p-1.5 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format($sQty, 2) }}</td>
                            <td class="border border-slate-100 p-1.5 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format($aQty, 2) }}</td>
                            <td class="border border-slate-100 p-1.5 text-right font-mono font-medium text-slate-800 whitespace-nowrap">{{ number_format($netOut, 2) }}</td>
                            
                            <td class="border border-slate-100 p-1.5 text-right font-mono font-bold text-blue-700 whitespace-nowrap">{{ number_format($balanceQty, 2) }} <span class="text-[9px] font-sans text-slate-400 font-normal uppercase">{{ $unit }}</span></td>
                            <td class="border border-slate-100 p-1.5 text-right font-mono font-bold text-slate-900 pr-3 whitespace-nowrap">Rs. {{ number_format($balanceValue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="p-8 text-center text-sm text-slate-400 bg-slate-50/50">No stock history balance metrics calculated matching this data grid parameter.</td>
                        </tr>
                    @endforelse
                </tbody>
                
                <!-- Summary Aggregations Matrix Footer Row -->
                <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-300 text-slate-800 text-right text-[10px]">
                    <tr>
                        <td class="border border-slate-200 p-2 pl-3 text-left font-bold tracking-wider uppercase truncate">Total</td>
                        <td class="border border-slate-200 p-1.5 font-mono whitespace-nowrap">{{ number_format($totals['opening'], 2) }}</td>
                        <td class="border border-slate-200 p-1.5 text-center text-slate-400 font-normal">-</td>
                        <td class="border border-slate-200 p-1.5 text-center text-slate-400 font-normal">-</td>
                        <td class="border border-slate-200 p-1.5 font-mono whitespace-nowrap">{{ number_format($totals['purchase_qty'], 2) }}</td>
                        <td class="border border-slate-200 p-1.5 text-center text-slate-400 font-normal">-</td>
                        <td class="border border-slate-200 p-1.5 font-mono whitespace-nowrap">{{ number_format($totals['purchase_val'], 2) }}</td>
                        <td class="border border-slate-200 p-1.5 font-mono whitespace-nowrap">{{ number_format($totals['sale'], 2) }}</td>
                        <td class="border border-slate-200 p-1.5 font-mono whitespace-nowrap">{{ number_format($totals['adjustment'], 2) }}</td>
                        <td class="border border-slate-200 p-1.5 text-center text-slate-400 font-normal">-</td>
                        <td class="border border-slate-200 p-1.5 font-mono text-blue-800 whitespace-nowrap">{{ number_format($totals['balance_qty'], 2) }}</td>
                        <td class="border border-slate-200 p-1.5 font-mono text-slate-900 pr-3 whitespace-nowrap">Rs. {{ number_format($totals['balance_value'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase().trim();
    let tableContent = document.getElementById("tableContent");
    let rows = tableContent.getElementsByClassName("data-row");

    for (let i = 0; i < rows.length; i++) {
        let nameField = rows[i].getElementsByClassName("search-name")[0];
        let codeField = rows[i].getElementsByClassName("search-code")[0];
        
        if (nameField || codeField) {
            let nameText = nameField ? (nameField.textContent || nameField.innerText) : "";
            let codeText = codeField ? (codeField.textContent || codeField.innerText) : "";
            
            if (nameText.toUpperCase().indexOf(filter) > -1 || codeText.toUpperCase().indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}
</script>
@endsection