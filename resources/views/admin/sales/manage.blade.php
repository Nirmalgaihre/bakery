@extends('layouts.admin')

@section('title', 'Sales Management - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Sales Registry Ledger')

@section('content')
<!-- Wrapped entire component to force Times New Roman globally -->
<div class="max-w-7xl w-full mx-auto" style="font-family: 'Times New Roman', Times, serif;">

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded text-xs font-semibold">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden mt-4">
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Active Warehouse Sales Ledger Matrix
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto flex-wrap">
                <!-- Advanced Real-time Search Input -->
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search customer or date..." 
                    class="text-xs border border-slate-300 rounded px-3 py-1.5 focus:ring-1 focus:ring-blue-500 outline-none w-full sm:w-64">

                <a href="#"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded shadow-xs transition-colors uppercase tracking-wide flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i> Create New Sale
                </a>
            </div>
        </div>

        <div class="p-4 flex items-center gap-2 flex-wrap border-b border-slate-100 bg-slate-50/20">
            <div class="uppercase text-[11px] bg-slate-100 font-mono text-slate-600 px-3 py-1.5 rounded border border-slate-200/60">
                <i class="fa-solid fa-calculator text-slate-400 mr-1"></i> Showing <strong class="text-slate-900">{{ $sales->count() }}</strong> entries this page
            </div>
        </div>

        <!-- Master Scroll Container -->
        <div class="w-full overflow-x-auto block position-relative" style="-webkit-overflow-scrolling: touch;">
            <!-- Forced Width Layout Constraints to Keep Rows Linear -->
            <table class="w-full min-w-[1000px] text-left border-collapse text-sm text-slate-700 table-auto" id="salesTable">
                <thead>
                    <tr class="bg-slate-100/70 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="p-4 pl-5 w-16 text-center">S.N.</th>
                        <th class="p-4">Transaction Date</th>
                        <th class="p-4">Customer Account</th>
                        <th class="p-4 text-right">Grand Total</th>
                        <th class="p-4 text-right pr-5 w-32">Actions</th>
                    </tr>
                </thead>

                <!-- Dynamic Sales Ledger Row Data Matrix -->
                <tbody id="tableContent" class="divide-y divide-slate-100">
                    @forelse($sales as $index => $sale)
                    <tr class="hover:bg-slate-50/80 transition-colors data-row">
                        <td class="p-4 pl-5 text-center text-xs text-slate-500 font-mono">
                            {{ ($sales->currentPage() - 1) * $sales->perPage() + $index + 1 }}
                        </td>
                        
                        <td class="p-4 text-xs text-slate-600 search-date">
                            {{ $sale->created_at->format('M d, Y') }} <span class="text-slate-400">({{ $sale->created_at->format('H:i') }})</span>
                        </td>
                        
                        <td class="p-4 font-semibold text-slate-900 search-customer">
                            @if($sale->customer)
                                {{ $sale->customer->name }}
                            @else
                                <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase px-2 py-0.5 rounded">Walk-in Customer</span>
                            @endif
                        </td>
                        
                        <td class="p-4 text-right font-bold text-slate-900 text-xs">
                            Rs. {{ number_format($sale->grand_total, 2) }}
                        </td>
                        
                        <td class="p-4 text-right pr-5 flex items-center justify-end gap-2">
                            <a href="{{ route('admin.invoices.edit', $sale->id) }}" class="text-blue-600 hover:text-blue-800 p-1">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                            <form action="{{ route('admin.invoices.destroy', $sale->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently void this transaction record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 p-1">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-sm text-slate-400">No transaction records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
        <div class="p-4 border-t bg-slate-50/50">{{ $sales->links() }}</div>
        @endif
    </div>
</div>

<script>
// Upgraded real-time search logic mapping custom names and transaction dates concurrently
function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase().trim();
    let tableContent = document.getElementById("tableContent");
    let rows = tableContent.getElementsByClassName("data-row");

    for (let i = 0; i < rows.length; i++) {
        let customerField = rows[i].getElementsByClassName("search-customer")[0];
        let dateField = rows[i].getElementsByClassName("search-date")[0];
        
        if (customerField || dateField) {
            let customerText = customerField ? (customerField.textContent || customerField.innerText) : "";
            let dateText = dateField ? (dateField.textContent || dateField.innerText) : "";
            
            if (customerText.toUpperCase().indexOf(filter) > -1 || dateText.toUpperCase().indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}
</script>
@endsection