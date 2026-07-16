@extends('layouts.admin')

@section('content')

<!-- Header Controls -->
<div class="flex justify-between items-center mb-6 print:hidden">
    <div class="inline-flex rounded-md shadow-sm">
        <a href="{{ route('admin.reports.monthly-movement') }}"
            class="px-4 py-2 text-sm font-medium border {{ $year ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }} rounded-l-lg">
            Monthly Summary
        </a>
        <a href="{{ route('admin.reports.stock-movement') }}"
            class="px-4 py-2 text-sm font-medium border {{ !$year ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }} rounded-r-lg">
            Audit Trail
        </a>
    </div>
    <button onclick="window.print()"
        class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
        <i class="fas fa-print mr-2"></i> Print Report
    </button>
</div>

<!-- Filter Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-6 print:hidden">
    <form method="GET" action="{{ url()->current() }}" class="flex items-end gap-4">
        <div class="w-64">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Select Year</label>
            <select name="year"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach(range(date('Y'), date('Y')-4) as $y)
                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Apply Filter
        </button>
    </form>
</div>

<!-- Report Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h5 class="text-lg font-bold text-blue-900">Monthly Report: {{ $year ?? date('Y') }}</h5>
    </div>

    @foreach($movements as $month => $items)
    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
        <h6 class="text-sm uppercase tracking-widest text-gray-700 font-bold">
            {{ date("F", mktime(0, 0, 0, $month, 1)) }}
        </h6>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Product</th>
                    <th class="px-6 py-4 text-sm font-semibold text-center">Initial</th>
                    <th class="px-6 py-4 text-sm font-semibold text-center">In</th>
                    <th class="px-6 py-4 text-sm font-semibold text-center">Out</th>
                    <th class="px-6 py-4 text-sm font-semibold text-center">Net Stock</th>
                    <th class="px-6 py-4 text-sm font-semibold text-center print:hidden">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach(collect($items)->groupBy('product') as $name => $data)
                @php
                $in = collect($data)->where('type', 'Inward')->sum('qty');
                $out = collect($data)->where('type', 'Outward')->sum('qty');
                $initial = $initialStocks[$name] ?? 0;
                $net = $initial + $in - $out;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-900">{{ $name }}</td>
                    <td class="px-6 py-4 text-center">{{ number_format($initial) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span
                            class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">+{{ number_format($in) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span
                            class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">-{{ number_format($out) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center font-bold {{ $net < 0 ? 'text-red-600' : 'text-blue-600' }}">
                        {{ number_format($net) }}
                    </td>
                    <td class="px-6 py-4 text-center print:hidden">
                        <a href="#"
                            class="text-sm text-gray-600 hover:text-blue-600 border border-gray-200 px-3 py-1 rounded">Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>

@endsection