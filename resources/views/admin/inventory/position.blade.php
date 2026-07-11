@extends('layouts.admin')

@section('title', 'Stock Position Report')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-700">Stock Position Report</h2>
        <div class="text-sm text-gray-500">Period: {{ $fromDate }} to {{ $toDate }}</div>
    </div>

    <div class="overflow-x-auto shadow-md border border-gray-300 bg-white">
        <table class="w-full text-xs text-left border-collapse">
            <thead class="bg-gray-100 font-bold uppercase text-gray-700">
                <tr>
                    <th class="px-2 py-3 border">Code</th>
                    <th class="px-2 py-3 border">Item Name</th>
                    <th class="px-2 py-3 border">Color</th>
                    <th class="px-2 py-3 border">Size</th>
                    <th class="px-2 py-3 border">Unit</th>
                    <th class="px-2 py-3 border text-right">Opening</th>
                    <th class="px-2 py-3 border text-right">Purchase</th>
                    <th class="px-2 py-3 border text-right">Sales</th>
                    <th class="px-2 py-3 border text-right">Adj.</th>
                    <th class="px-2 py-3 border text-right">Bal. Qty</th>
                    <th class="px-2 py-3 border text-right">Bal. Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($stockReport as $item)
                    @php
                        $balanceQty = $item->initial_stock + $item->total_purchase - $item->total_sale + $item->total_adjustment;
                        $balanceValue = $balanceQty * $item->purchase_cost;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-2 border">{{ $item->item_code }}</td>
                        <td class="px-2 py-2 border font-medium">{{ $item->name }}</td>
                        <td class="px-2 py-2 border">{{ $item->color }}</td>
                        <td class="px-2 py-2 border">{{ $item->size }}</td>
                        <td class="px-2 py-2 border">{{ $item->inventory_unit }}</td>
                        <td class="px-2 py-2 border text-right">{{ $item->initial_stock }}</td>
                        <td class="px-2 py-2 border text-right">{{ $item->total_purchase }}</td>
                        <td class="px-2 py-2 border text-right">{{ $item->total_sale }}</td>
                        <td class="px-2 py-2 border text-right">{{ $item->total_adjustment }}</td>
                        <td class="px-2 py-2 border text-right font-bold">{{ $balanceQty }}</td>
                        <td class="px-2 py-2 border text-right">{{ number_format($balanceValue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection