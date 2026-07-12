@extends('layouts.admin')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-8 bg-slate-50/50 min-h-screen">
    
    <!-- Top Bar: Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight drop-shadow-sm flex items-center gap-3">
                <i class="fa-solid fa-layer-group text-indigo-600"></i> Customer Ledger
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Track customer financial accounts, total billings, and outstanding liabilities.</p>
        </div>
        @can('manage_invoices')
        <a href="{{ route('admin.invoices.create') }}"
            class="inline-flex items-center justify-center bg-gradient-to-b from-indigo-500 to-indigo-700 text-white px-6 py-3 rounded-xl text-sm font-bold tracking-wide shadow-[0_4px_12px_rgba(79,70,229,0.35),inset_0_1px_0_rgba(255,255,255,0.4)] hover:from-indigo-600 hover:to-indigo-800 hover:shadow-[0_2px_4px_rgba(79,70,229,0.2)] active:scale-[0.98] transition-all duration-150 gap-2.5">
            <i class="fa-solid fa-square-plus text-base"></i> Create New Invoice
        </a>
        @endcan
    </div>

    <!-- 4-Column High-Contrast 3D Metrics Grid -->
    @php
        $grandTotalSpent = 0;
        $grandTotalPaid = 0;
        $dueAccountsCount = 0;
        foreach($invoices as $group) {
            $spent = $group->sum('grand_total');
            $paid = $group->sum('paid_amount');
            $grandTotalSpent += $spent;
            $grandTotalPaid += $paid;
            if (($spent - $paid) > 0) {
                $dueAccountsCount++;
            }
        }
        $grandTotalDue = $grandTotalSpent - $grandTotalPaid;
    @endphp
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <!-- Card 1: Blue (Total Sales) -->
        <div class="relative overflow-hidden h-[150px] rounded-[20px] p-5 text-white flex flex-col justify-between shadow-[0_10px_25px_-5px_rgba(37,99,235,0.35),inset_0_1px_1px_rgba(255,255,255,0.4)] bg-gradient-to-br from-blue-500 to-blue-700">
            <div class="relative z-10 flex justify-between items-center">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 backdrop-blur-md border border-white/10 shadow-sm">
                    <i class="fa-solid fa-wallet text-lg text-white"></i>
                </div>
                <span class="text-[0.72rem] bg-white/25 backdrop-blur-md px-2.5 py-1 rounded-full font-semibold uppercase tracking-wider">Gross</span>
            </div>
            <div class="relative z-10">
                <h4 class="text-[0.8rem] font-medium opacity-90 m-0 uppercase tracking-wider">Total Invoiced</h4>
                <h2 class="text-2xl font-black m-0 tracking-tight">Rs. {{ number_format($grandTotalSpent, 2) }}</h2>
            </div>
        </div>

        <!-- Card 2: Green (Total Collected) -->
        <div class="relative overflow-hidden h-[150px] rounded-[20px] p-5 text-white flex flex-col justify-between shadow-[0_10px_25px_-5px_rgba(16,185,129,0.35),inset_0_1px_1px_rgba(255,255,255,0.4)] bg-gradient-to-br from-emerald-500 to-emerald-700">
            <div class="relative z-10 flex justify-between items-center">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 backdrop-blur-md border border-white/10 shadow-sm">
                    <i class="fa-solid fa-circle-check text-lg text-white"></i>
                </div>
                <span class="text-[0.72rem] bg-white/25 backdrop-blur-md px-2.5 py-1 rounded-full font-semibold uppercase tracking-wider">Received</span>
            </div>
            <div class="relative z-10">
                <h4 class="text-[0.8rem] font-medium opacity-90 m-0 uppercase tracking-wider">Total Collected</h4>
                <h2 class="text-2xl font-black m-0 tracking-tight">Rs. {{ number_format($grandTotalPaid, 2) }}</h2>
            </div>
        </div>

        <!-- Card 3: Orange/Red (Outstanding Dues Amount) -->
        <div class="relative overflow-hidden h-[150px] rounded-[20px] p-5 text-white flex flex-col justify-between shadow-[0_10px_25px_-5px_rgba(244,63,94,0.35),inset_0_1px_1px_rgba(255,255,255,0.4)] bg-gradient-to-br from-rose-500 to-rose-700">
            <div class="relative z-10 flex justify-between items-center">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 backdrop-blur-md border border-white/10 shadow-sm">
                    <i class="fa-solid fa-hand-holding-dollar text-lg text-white"></i>
                </div>
                <span class="text-[0.72rem] bg-white/25 backdrop-blur-md px-2.5 py-1 rounded-full font-semibold uppercase tracking-wider">Pending</span>
            </div>
            <div class="relative z-10">
                <h4 class="text-[0.8rem] font-medium opacity-90 m-0 uppercase tracking-wider">Receivable Dues</h4>
                <h2 class="text-2xl font-black m-0 tracking-tight">Rs. {{ number_format($grandTotalDue, 2) }}</h2>
            </div>
        </div>

        <!-- Card 4: Purple (Defaulter / Due Accounts Counter) -->
        <div class="relative overflow-hidden h-[150px] rounded-[20px] p-5 text-white flex flex-col justify-between shadow-[0_10px_25px_-5px_rgba(139,92,246,0.35),inset_0_1px_1px_rgba(255,255,255,0.4)] bg-gradient-to-br from-purple-500 to-purple-700">
            <div class="relative z-10 flex justify-between items-center">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 backdrop-blur-md border border-white/10 shadow-sm">
                    <i class="fa-solid fa-users text-lg text-white"></i>
                </div>
                <span class="text-[0.72rem] bg-white/25 backdrop-blur-md px-2.5 py-1 rounded-full font-semibold uppercase tracking-wider">Ledger Accounts</span>
            </div>
            <div class="relative z-10">
                <h4 class="text-[0.8rem] font-medium opacity-90 m-0 uppercase tracking-wider">Accounts with Dues</h4>
                <h2 class="text-2xl font-black m-0 tracking-tight">{{ $dueAccountsCount }} <span class="text-xs font-normal opacity-75">Active</span></h2>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-gradient-to-b from-white to-slate-50 border border-slate-200/80 rounded-2xl p-4 shadow-[0_4px_12px_rgba(0,0,0,0.03)]">
        <div class="relative max-w-md w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
            </span>
            <input type="text" id="searchBox" onkeyup="filterLedger()" 
                placeholder="Type customer name, phone number, or amounts to filter..."
                class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 text-sm text-slate-800 placeholder-slate-400 rounded-xl shadow-[inset_0_2px_4px_rgba(0,0,0,0.04)] focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>
    </div>

    <!-- 3D Table Workspace Container -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_15px_35px_-5px_rgba(0,0,0,0.07),0_10px_20px_-8px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse" id="ledgerTable">
                <thead>
                    <tr class="text-xs font-bold text-slate-500 uppercase tracking-wider bg-gradient-to-b from-slate-50 to-slate-100/80 border-b border-slate-200">
                        <th class="py-4 px-6 w-20 text-center">Index</th>
                        <th class="py-4 px-6">Customer Profile</th>
                        <th class="py-4 px-6">Statement Summary</th>
                        <th class="py-4 px-6">Account Health</th>
                        <th class="py-4 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $phone => $group)
                    @php
                        $totalSpent = $group->sum('grand_total');
                        $totalPaid = $group->sum('paid_amount');
                        $totalDue = $totalSpent - $totalPaid;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors duration-150 cursor-pointer group"
                        onclick="window.location='{{ route('admin.sales.customer-ledger-by-phone', $phone) }}'">

                        <!-- Index Column -->
                        <td class="py-5 px-6 text-center font-mono text-xs text-slate-400 font-bold">{{ $loop->iteration }}</td>
                        
                        <!-- Profile Column -->
                        <td class="py-5 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 flex items-center justify-center bg-gradient-to-b from-slate-100 to-slate-200 text-slate-600 font-bold rounded-lg shadow-[inset_0_1px_0_#fff,0_2px_4px_rgba(0,0,0,0.05)] border border-slate-300/40">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">
                                        {{ $group->first()->customer->name ?? 'Walk-in Customer' }}
                                    </div>
                                    <div class="text-xs font-medium text-slate-400 mt-0.5 flex items-center gap-1.5">
                                        <i class="fa-solid fa-phone text-[10px]"></i> {{ $phone }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Ledger Financial Details -->
                        <td class="py-5 px-6 space-y-1">
                            <div class="text-xs font-medium text-slate-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-basket-shopping text-blue-500"></i> Total: 
                                <span class="font-bold text-slate-700">Rs. {{ number_format($totalSpent, 2) }}</span>
                            </div>
                            <div class="text-xs font-medium text-slate-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-square-check text-emerald-500"></i> Paid: 
                                <span class="font-bold text-emerald-600">Rs. {{ number_format($totalPaid, 2) }}</span>
                            </div>
                        </td>

                        <!-- Account Status Tag with 3D details -->
                        <td class="py-5 px-6">
                            @if($totalDue > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-b from-rose-50 to-rose-100 text-rose-700 border border-rose-200/60 shadow-[0_2px_4px_rgba(244,63,94,0.06),inset_0_1px_0_#fff]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    Due: Rs. {{ number_format($totalDue, 2) }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-b from-emerald-50 to-emerald-100 text-emerald-700 border border-emerald-200/60 shadow-[0_2px_4px_rgba(16,185,129,0.06),inset_0_1px_0_#fff]">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Settled
                                </span>
                            @endif
                        </td>

                        <!-- Action Button -->
                        <td class="py-5 px-6 text-center">
                            <span class="inline-flex items-center justify-center bg-gradient-to-b from-white to-slate-100 hover:from-indigo-50 hover:to-indigo-100 text-slate-700 hover:text-indigo-700 font-bold text-xs px-4 py-2 rounded-xl border border-slate-200 shadow-[0_2px_4px_rgba(0,0,0,0.04),inset_0_1px_0_#fff] group-hover:border-indigo-200 transition-all duration-150 gap-2">
                                <i class="fa-solid fa-folder-open text-xs"></i> View Ledger
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center text-slate-400 font-medium bg-slate-50/50">
                            <div class="w-16 h-16 bg-white rounded-2xl shadow-[0_8px_20px_rgba(0,0,0,0.05)] border border-slate-200/60 flex items-center justify-center mx-auto mb-4 text-slate-300">
                                <i class="fa-solid fa-box-open text-2xl"></i>
                            </div>
                            No customer records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterLedger() {
    let input = document.getElementById("searchBox");
    let filter = input.value.toUpperCase();
    let rows = document.querySelectorAll("#ledgerTable tbody tr");
    rows.forEach(row => {
        if(row.cells.length > 1) {
            let text = row.innerText.toUpperCase();
            row.style.display = text.includes(filter) ? "" : "none";
        }
    });
}
</script>
@endsection