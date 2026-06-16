@extends('layouts.admin')

@section('panel_title', 'Customer Financial Ledger')

@section('content')
<div class="space-y-6">
    <!-- Payment Entry & Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4 uppercase text-xs">Post New Payment</h3>
            <form action="{{ route('admin.payments.store', $customer->id) }}" method="POST" class="grid grid-cols-2 gap-4">
                @csrf
                <input type="number" name="amount" placeholder="Amount (NPR)" class="border border-slate-200 rounded-lg p-2 text-sm" required>
                <input type="date" name="date" class="border border-slate-200 rounded-lg p-2 text-sm" value="{{ date('Y-m-d') }}" required>
                <textarea name="remarks" placeholder="Remarks..." class="col-span-2 border border-slate-200 rounded-lg p-2 text-sm"></textarea>
                <button type="submit" class="col-span-2 bg-blue-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-blue-700">RECORD PAYMENT</button>
            </form>
        </div>

        <div class="bg-slate-900 p-6 rounded-xl text-white">
            <h3 class="text-slate-400 text-[10px] uppercase font-bold">Total Outstanding</h3>
            <p class="text-3xl font-bold mt-1">Rs. {{ number_format($customer->due_amount, 2) }}</p>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px]">
                <tr>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-right">Debit</th>
                    <th class="px-6 py-3 text-right">Credit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($ledgerLogs as $log)
                <tr>
                    <td class="px-6 py-4">{{ $log->date }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-[10px] {{ $log->type == 'PAYMENT' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $log->type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">{{ $log->debit > 0 ? 'Rs. '.number_format($log->debit, 2) : '-' }}</td>
                    <td class="px-6 py-4 text-right font-bold {{ $log->credit > 0 ? 'text-emerald-600' : '' }}">
                        {{ $log->credit > 0 ? 'Rs. '.number_format($log->credit, 2) : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection