@extends('layouts.admin')

@section('title', 'Stock Position Report')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Company Header -->
    <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900 uppercase tracking-wide">Deurali Chemicals</h1>
        <h2 class="text-md font-semibold text-gray-700">Stock Position Report</h2>
        <div class="inline-block mt-2 px-4 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold border border-blue-200">
            Fiscal Year: {{ $selectedFiscalYear }} | Period: {{ $fromDate }} to {{ $toDate }}
        </div>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto shadow-sm rounded-lg border border-gray-200 bg-white">
        <table class="w-full text-[11px] text-left border-collapse">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th rowspan="2" class="border px-3 py-3 uppercase">Particulars</th>
                    <th colspan="3" class="border px-3 py-2 text-center uppercase">Opening Balance</th>
                    <th colspan="3" class="border px-3 py-2 text-center uppercase">Inwards (Purchase)</th>
                    <th colspan="3" class="border px-3 py-2 text-center uppercase">Outwards (Sales/Adj)</th>
                    <th colspan="2" class="border px-3 py-2 text-center uppercase">Closing Balance</th>
                </tr>
                <tr class="bg-gray-700 text-gray-200">
                    <th class="border px-2 py-2 text-right">Qty</th><th class="border px-2 py-2 text-right">Rate</th><th class="border px-2 py-2 text-right">Value</th>
                    <th class="border px-2 py-2 text-right">Qty</th><th class="border px-2 py-2 text-right">Rate</th><th class="border px-2 py-2 text-right">Value</th>
                    <th class="border px-2 py-2 text-right">Sales</th><th class="border px-2 py-2 text-right">Adj.</th><th class="border px-2 py-2 text-right">Net</th>
                    <th class="border px-2 py-2 text-right">Qty</th><th class="border px-2 py-2 text-right">Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($stockReport as $item)
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
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="border px-3 py-2 font-semibold text-gray-700">{{ $item->name }} <span class="text-[9px] text-gray-400">({{ $item->item_code }})</span></td>
                        <td class="border px-2 py-2 text-right">{{ number_format($item->initial_stock, 2) }} {{ $unit }}</td>
                        <td class="border px-2 py-2 text-right">{{ number_format($item->purchase_cost, 2) }}</td>
                        <td class="border px-2 py-2 text-right">{{ number_format($item->initial_stock * $item->purchase_cost, 2) }}</td>
                        <td class="border px-2 py-2 text-right">{{ number_format($pQty, 2) }} {{ $unit }}</td>
                        <td class="border px-2 py-2 text-right">{{ number_format($pRate, 2) }}</td>
                        <td class="border px-2 py-2 text-right">{{ number_format($pVal, 2) }}</td>
                        <td class="border px-2 py-2 text-right">{{ number_format($sQty, 2) }}</td>
                        <td class="border px-2 py-2 text-right">{{ number_format($aQty, 2) }}</td>
                        <td class="border px-2 py-2 text-right font-medium">{{ number_format($netOut, 2) }}</td>
                        <td class="border px-2 py-2 text-right font-bold text-blue-900">{{ number_format($balanceQty, 2) }} {{ $unit }}</td>
                        <td class="border px-2 py-2 text-right font-bold">{{ number_format($balanceValue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-300">
                <tr>
                    <td class="border px-3 py-3">GRAND TOTAL</td>
                    <td class="border px-2 py-2 text-right">{{ number_format($totals['opening'], 2) }}</td>
                    <td class="border px-2 py-2 text-right">-</td>
                    <td class="border px-2 py-2 text-right">-</td>
                    <td class="border px-2 py-2 text-right">{{ number_format($totals['purchase_qty'], 2) }}</td>
                    <td class="border px-2 py-2 text-right">-</td>
                    <td class="border px-2 py-2 text-right">{{ number_format($totals['purchase_val'], 2) }}</td>
                    <td class="border px-2 py-2 text-right">{{ number_format($totals['sale'], 2) }}</td>
                    <td class="border px-2 py-2 text-right">{{ number_format($totals['adjustment'], 2) }}</td>
                    <td class="border px-2 py-2 text-right">-</td>
                    <td class="border px-2 py-2 text-right">{{ number_format($totals['balance_qty'], 2) }}</td>
                    <td class="border px-2 py-2 text-right">{{ number_format($totals['balance_value'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection