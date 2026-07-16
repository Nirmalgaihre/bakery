@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Transaction Form -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Record New Transaction</h2>
            <form action="{{ route('admin.transactions.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                @csrf
                <input type="number" name="product_id" placeholder="Product ID" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <input type="text" name="partner_name" placeholder="Supplier/Buyer" class="col-span-2 border-gray-300 rounded-md shadow-sm" required>
                <select name="transaction_type" class="border-gray-300 rounded-md shadow-sm" required>
                    <option value="inward">Inward</option>
                    <option value="outward">Outward</option>
                </select>
                <input type="number" name="quantity" placeholder="Qty" class="border-gray-300 rounded-md shadow-sm" required>
                <button type="submit" class="bg-blue-600 text-white font-medium py-2 px-4 rounded-md hover:bg-blue-700 transition">Save</button>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-700">Date</th>
                        <th class="p-4 text-sm font-semibold text-gray-700">Partner</th>
                        <th class="p-4 text-sm font-semibold text-gray-700">Type</th>
                        <th class="p-4 text-sm font-semibold text-gray-700">Qty</th>
                        <th class="p-4 text-sm font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm text-gray-600">{{ $transaction->transaction_date }}</td>
                        <td class="p-4 text-sm text-gray-800 font-medium">{{ $transaction->partner_name }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $transaction->transaction_type == 'inward' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($transaction->transaction_type) }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-gray-600">{{ $transaction->quantity }}</td>
                        <td class="p-4">
                            <a href="{{ route('admin.transactions.report.movement', $transaction->product_id) }}" class="text-blue-600 hover:underline text-sm font-medium">Analyze</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection