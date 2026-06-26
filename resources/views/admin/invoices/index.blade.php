@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Customer Ledger</h1>
            <p class="text-sm text-slate-500">Track all customer invoices and outstanding dues.</p>
        </div>
        <a href="{{ route('admin.invoices.create') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">
            <i class="fa-solid fa-plus mr-2"></i> Create Invoice
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="ledgerTable">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Phone Number</th>
                        <th class="p-4">Customer Name</th>
                        <th class="p-4">Billing Summary</th>
                        <th class="p-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $phone => $group)
                    @php
                    $totalSpent = $group->sum('grand_total');
                    $totalPaid = $group->sum('paid_amount');
                    $totalDue = $totalSpent - $totalPaid;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors cursor-pointer group"
                        onclick="window.location='{{ route('admin.sales.customer-ledger-by-phone', $phone) }}'">

                        <td class="p-4 font-medium text-slate-900">{{ $phone }}</td>

                        <td class="p-4 font-bold text-slate-900">
                            {{ $group->first()->customer->name ?? 'Walk-in Customer' }}
                        </td>

                        <td class="p-4 text-xs space-y-1">
                            <div class="text-slate-600">Total: Rs. {{ number_format($totalSpent, 2) }}</div>
                            <div class="{{ $totalDue > 0 ? 'text-rose-600 font-bold' : 'text-emerald-600' }}">
                                Due: Rs. {{ number_format($totalDue, 2) }}
                            </div>
                        </td>

                        <td class="p-4 text-right">
                            <span class="text-indigo-600 font-bold group-hover:underline text-xs">View Full Ledger
                                &rarr;</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-slate-500">No records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}

.group:hover .group-hover\:underline {
    text-decoration: underline;
}
</style>
<script>
// टेबल सर्च फंक्शन
function filterLedger() {
    let input = document.getElementById("searchBox");
    let filter = input.value.toUpperCase();
    let rows = document.querySelectorAll("#ledgerTable tbody tr");
    rows.forEach(row => {
        let text = row.innerText.toUpperCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
}
</script>
@endsection