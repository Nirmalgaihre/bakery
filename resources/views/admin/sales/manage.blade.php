@extends('layouts.admin')

@section('title', 'Sales Management - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Sales Registry Ledger')

@section('content')
<!-- Clean, modern system sans-serif font stack -->
<div class="max-w-7xl w-full mx-auto font-sans">

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded text-xs font-semibold flex items-center justify-between">
        <span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden mt-4">
        
        <!-- Header & Main Controls -->
        <div class="p-4 px-5 border-b border-slate-200 bg-slate-50/70 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Sales Ledger Matrix
            </div>
            
            <div class="flex items-center gap-2 w-full md:w-auto flex-wrap justify-end">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search customer or date..." 
                        class="text-xs border border-slate-300 rounded-md pl-8 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full">
                </div>

                <!-- Create Sale Button -->
                <a href="{{ route('admin.invoices.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs px-3.5 py-2 rounded-md shadow-sm transition flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i> New Sale
                </a>
            </div>
        </div>

        <!-- Toolbar: Import/Export Data & Status Bar -->
        <div class="p-3 px-5 border-b border-slate-200 bg-slate-50/30 flex flex-wrap items-center justify-between gap-3 text-xs">
            <!-- Counter Badge -->
            <div class="text-slate-600 font-medium flex items-center gap-2">
                <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded border border-slate-200 font-mono">
                    <i class="fa-solid fa-list-check text-slate-400 mr-1"></i> {{ $sales->count() }} Entries
                </span>
            </div>

            <!-- Import / Export Action Buttons -->
            <div class="flex items-center gap-2">
                <!-- Redirect Link to Import Page -->
                <a href="{{ route('admin.sales.import') }}" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium px-3 py-1.5 rounded-md transition flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-file-import text-blue-600"></i>
                    <span>Import</span>
                </a>

                <!-- Export Options Dropdown/Group -->
                <div class="inline-flex rounded-md shadow-2xs" role="group">
                    <a href="{{ route('admin.sales.export.excel') }}" title="Export to Excel" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium px-2.5 py-1.5 rounded-l-md border-r-0 transition flex items-center gap-1">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
                    </a>
                    <a href="{{ route('admin.sales.export.pdf') }}" title="Export to PDF" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium px-2.5 py-1.5 rounded-r-md transition flex items-center gap-1">
                        <i class="fa-solid fa-file-pdf text-rose-600"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Master Table Container -->
        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[900px] text-left border-collapse text-xs text-slate-700" id="salesTable">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 font-semibold uppercase tracking-wider text-slate-600">
                        <th class="p-3 pl-5 w-16 text-center">S.N.</th>
                        <th class="p-3">Transaction Date</th>
                        <th class="p-3">Customer Account</th>
                        <th class="p-3 text-right">Grand Total</th>
                        <th class="p-3 text-center pr-5 w-40">Manage</th>
                    </tr>
                </thead>

                <tbody id="tableContent" class="divide-y divide-slate-100">
                    @forelse($sales as $index => $sale)
                    <tr class="hover:bg-slate-50 transition-colors data-row">
                        <td class="p-3 pl-5 text-center text-slate-500 font-mono">
                            {{ ($sales->currentPage() - 1) * $sales->perPage() + $index + 1 }}
                        </td>
                        
                        <td class="p-3 text-slate-600 search-date">
                            {{ $sale->created_at->format('M d, Y') }} <span class="text-slate-400">({{ $sale->created_at->format('H:i') }})</span>
                        </td>
                        
                        <td class="p-3 font-medium text-slate-900 search-customer">
                            @if($sale->customer)
                                {{ $sale->customer->name }}
                            @else
                                <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-semibold uppercase px-2 py-0.5 rounded">Walk-in Customer</span>
                            @endif
                        </td>
                        
                        <td class="p-3 text-right font-semibold text-slate-900">
                            Rs. {{ number_format($sale->grand_total, 2) }}
                        </td>
                        
                        <!-- Manage / Action Column -->
                        <td class="p-3 text-center pr-5">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- View / Print -->
                                <a href="{{ route('admin.invoices.show', $sale->id) }}" title="View Sale" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded transition">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                
                                <!-- Edit -->
                                <a href="{{ route('admin.invoices.edit', $sale->id) }}" title="Edit Record" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                <!-- Delete -->
                                <form action="{{ route('admin.invoices.destroy', $sale->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently void this transaction record?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" title="Delete Record" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">
                            <i class="fa-solid fa-inbox text-2xl mb-2 text-slate-300"></i>
                            <p>No transaction records found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>

<script>
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