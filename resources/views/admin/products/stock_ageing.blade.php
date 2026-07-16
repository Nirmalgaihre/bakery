@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">

    <div class="max-w-7xl mx-auto px-4">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Stock Ageing Analysis
                    </h2>
                    <p class="text-gray-500 mt-1">
                        Inventory Ageing Report
                    </p>
                </div>

                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-700">
                        Fiscal Year :
                        {{ request('fiscal_year', \App\Helpers\FiscalYearHelper::getCurrentFiscalYear()) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">

            <form method="GET"
                  action="{{ route('admin.reports.stock_ageing') }}"
                  class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                    <!-- Fiscal Year -->

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Fiscal Year
                        </label>

                        <select name="fiscal_year"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                            @foreach(\App\Helpers\FiscalYearHelper::getFiscalYearList() as $fy)

                                <option value="{{ $fy }}"
                                    {{ request('fiscal_year', \App\Helpers\FiscalYearHelper::getCurrentFiscalYear())==$fy ? 'selected':'' }}>
                                    {{ $fy }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <!-- Period -->

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Period
                        </label>

                        <select id="period"
                                name="period"
                                class="w-full rounded-lg border-gray-300">

                            <option value="today" {{ request('period')=='today'?'selected':'' }}>
                                Today
                            </option>

                            <option value="week" {{ request('period')=='week'?'selected':'' }}>
                                This Week
                            </option>

                            <option value="month" {{ request('period')=='month'?'selected':'' }}>
                                This Month
                            </option>

                            <option value="last_month" {{ request('period')=='last_month'?'selected':'' }}>
                                Last Month
                            </option>

                            <option value="fy"
                                {{ request('period','fy')=='fy'?'selected':'' }}>
                                Current Fiscal Year
                            </option>

                            <option value="last_fy"
                                {{ request('period')=='last_fy'?'selected':'' }}>
                                Previous Fiscal Year
                            </option>

                            <option value="custom"
                                {{ request('period')=='custom'?'selected':'' }}>
                                Custom Date
                            </option>

                        </select>

                    </div>

                    <!-- From -->

                    <div id="fromBox">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            From (BS)
                        </label>

                        <input
                            type="date"
                            name="from"
                            value="{{ request('from') }}"
                            class="w-full rounded-lg border-gray-300">

                    </div>

                    <!-- To -->

                    <div id="toBox">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            To (BS)
                        </label>

                        <input
                            type="date"
                            name="to"
                            value="{{ request('to') }}"
                            class="w-full rounded-lg border-gray-300">

                    </div>

                </div>

                <hr class="my-6">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Slab 1 (&lt; Days)
                        </label>

                        <input
                            type="number"
                            name="s1"
                            value="{{ $s1 }}"
                            class="w-full rounded-lg border-gray-300">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Slab 2 (&lt; Days)
                        </label>

                        <input
                            type="number"
                            name="s2"
                            value="{{ $s2 }}"
                            class="w-full rounded-lg border-gray-300">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Slab 3 (&lt; Days)
                        </label>

                        <input
                            type="number"
                            name="s3"
                            value="{{ $s3 }}"
                            class="w-full rounded-lg border-gray-300">

                    </div>

                    <div class="flex items-end">

                        <button
                            class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 transition">

                            Generate Report

                        </button>

                    </div>

                </div>

            </form>

        </div>

        <!-- Table -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="overflow-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-800 sticky top-0">

                    <tr>

                        <th class="px-6 py-4 text-left text-white uppercase text-xs">
                            Particulars
                        </th>

                        <th class="px-6 py-4 text-center text-white uppercase text-xs">
                            Total Qty
                        </th>

                        <th class="px-6 py-4 text-center text-white uppercase text-xs">
                            &lt; {{ $s1 }} Days
                        </th>

                        <th class="px-6 py-4 text-center text-white uppercase text-xs">
                            {{ $s1 }}-{{ $s2 }} Days
                        </th>

                        <th class="px-6 py-4 text-center text-white uppercase text-xs">
                            {{ $s2 }}-{{ $s3 }} Days
                        </th>

                        <th class="px-6 py-4 text-center text-white uppercase text-xs">
                            &gt; {{ $s3 }} Days
                        </th>

                    </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200">

                    @php

                        $totalQty=0;

                        $s1q=0;$s1v=0;
                        $s2q=0;$s2v=0;
                        $s3q=0;$s3v=0;
                        $s4q=0;$s4v=0;

                    @endphp

                    @forelse($reportData as $row)

                        @php

                            $totalQty += $row['total_qty'];

                            $s1q += $row['slabs']['s1']['q'];
                            $s1v += $row['slabs']['s1']['v'];

                            $s2q += $row['slabs']['s2']['q'];
                            $s2v += $row['slabs']['s2']['v'];

                            $s3q += $row['slabs']['s3']['q'];
                            $s3v += $row['slabs']['s3']['v'];

                            $s4q += $row['slabs']['s4']['q'];
                            $s4v += $row['slabs']['s4']['v'];

                        @endphp

                        <tr class="hover:bg-blue-50">

                            <td class="px-6 py-4 font-medium text-gray-700">
                                {{ $row['name'] }}
                            </td>

                            <td class="text-center font-semibold">
                                {{ number_format($row['total_qty']) }}
                            </td>

                            @foreach(['s1','s2','s3','s4'] as $slab)

                                <td class="px-4 py-4">

                                    <div class="flex justify-between">
                                        <span class="text-gray-500 text-sm">Qty</span>
                                        <span class="font-semibold">
                                            {{ number_format($row['slabs'][$slab]['q']) }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between mt-1">
                                        <span class="text-gray-500 text-sm">Value</span>
                                        <span class="text-sm font-medium">
                                            {{ number_format($row['slabs'][$slab]['v'],2) }}
                                        </span>
                                    </div>

                                </td>

                            @endforeach

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                class="py-12 text-center text-gray-500">

                                No records found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                    @if(count($reportData))

                    <tfoot class="bg-gray-100 font-bold">

                    <tr>

                        <td class="px-6 py-4">
                            TOTAL
                        </td>

                        <td class="text-center">
                            {{ number_format($totalQty) }}
                        </td>

                        <td class="px-4 py-4 text-center">
                            {{ number_format($s1q) }}<br>
                            <span class="text-sm text-gray-600">
                                {{ number_format($s1v,2) }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-center">
                            {{ number_format($s2q) }}<br>
                            <span class="text-sm text-gray-600">
                                {{ number_format($s2v,2) }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-center">
                            {{ number_format($s3q) }}<br>
                            <span class="text-sm text-gray-600">
                                {{ number_format($s3v,2) }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-center">
                            {{ number_format($s4q) }}<br>
                            <span class="text-sm text-gray-600">
                                {{ number_format($s4v,2) }}
                            </span>
                        </td>

                    </tr>

                    </tfoot>

                    @endif

                </table>

            </div>

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded',function(){

    const period=document.getElementById('period');

    const from=document.getElementById('fromBox');

    const to=document.getElementById('toBox');

    function toggleDates(){

        if(period.value==='custom'){

            from.style.display='block';
            to.style.display='block';

        }else{

            from.style.display='none';
            to.style.display='none';

        }

    }

    toggleDates();

    period.addEventListener('change',toggleDates);

});

</script>

@endsection