@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Item Movement Analysis</h1>
            <p class="text-gray-600">Product: {{ $product->name }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Inward Movement -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-100 bg-gray-50 font-semibold text-gray-700">Movement Inward (Suppliers)</div>
                <table class="w-full text-sm">
                    @foreach($transactions->where('transaction_type', 'inward') as $in)
                    <tr class="border-b">
                        <td class="p-3">{{ $in->partner_name }}</td>
                        <td class="p-3 text-right">{{ $in->quantity }} kg</td>
                        <td class="p-3 text-right">{{ number_format($in->rate, 2) }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>

            <!-- Outward Movement -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-100 bg-gray-50 font-semibold text-gray-700">Movement Outward (Buyers)</div>
                <table class="w-full text-sm">
                    @foreach($transactions->where('transaction_type', 'outward') as $out)
                    <tr class="border-b">
                        <td class="p-3">{{ $out->partner_name }}</td>
                        <td class="p-3 text-right">{{ $out->quantity }} kg</td>
                        <td class="p-3 text-right">{{ number_format($out->rate, 2) }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@endsection