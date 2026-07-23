@extends('layouts.admin')
@section('title', 'Low Stock Alerts')

@section('content')
<div class="my-6">
    
    <!-- Search Input & Results Counter -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="relative w-full md:w-1/3">
            <input 
                type="text" 
                id="searchInput" 
                oninput="searchTable()" 
                placeholder="Search products, stock, or status..." 
                class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm placeholder:text-slate-400"
            >
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <div id="searchCounter" class="text-xs text-slate-500 font-medium hidden">
            Showing <span id="visibleCount" class="font-bold text-slate-700">0</span> result(s)
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-md font-bold text-slate-700">Low Stock Alerts</h2>
            <span class="text-xs text-slate-400 font-mono">Total: {{ count($lowStockProducts) }} items</span>
        </div>

        <div class="overflow-x-auto">
            <table id="inventoryTable" class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <th class="px-6 py-3.5">SN</th>
                        <th class="px-6 py-3.5">Product Name</th>
                        <th class="px-6 py-3.5">Current Stock</th>
                        <th class="px-6 py-3.5">Alert Level</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lowStockProducts as $product)
                    <tr class="product-row hover:bg-slate-50/80 transition-colors">
                        <td class="sn-cell px-6 py-3 text-slate-400 font-mono text-xs">{{ $loop->iteration }}</td>
                        <td class="product-name px-6 py-3 text-slate-700 font-semibold">{{ $product->name }}</td>
                        <td class="px-6 py-3 text-slate-600 font-bold">
                            {{ $product->initial_stock }} 
                            <span class="text-[10px] text-slate-400 font-normal uppercase">{{ $product->inventory_unit }}</span>
                        </td>
                        <td class="px-6 py-3 text-slate-500 font-mono text-xs">{{ $product->alert_stock_level }}</td>
                        <td class="px-6 py-3">
                            @if($product->email_sent)
                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Sent</span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-50 text-amber-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Pending</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                               class="inline-flex items-center text-[11px] font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wider transition-colors">
                               Restock &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyDbRow">
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-xs">
                            All stock levels are currently optimal.
                        </td>
                    </tr>
                    @endforelse

                    <!-- Dynamic JavaScript "No Match" Row -->
                    <tr id="noResultsRow" class="hidden">
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-xs">
                            No matching products found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function searchTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.trim().toLowerCase();
    const rows = document.querySelectorAll("#inventoryTable tbody tr.product-row");
    const noResultsRow = document.getElementById("noResultsRow");
    const counterContainer = document.getElementById("searchCounter");
    const visibleCountEl = document.getElementById("visibleCount");

    let visibleCount = 0;

    rows.forEach(row => {
        // Search across all text content in row (Product name, Stock, Alert level, Status)
        const rowText = row.innerText.toLowerCase();

        if (rowText.includes(filter)) {
            row.style.display = "";
            visibleCount++;
            
            // Re-index visible SN column dynamically
            const snCell = row.querySelector(".sn-cell");
            if (snCell) {
                snCell.textContent = visibleCount;
            }
        } else {
            row.style.display = "none";
        }
    });

    // Toggle "No Match" row display
    if (noResultsRow) {
        noResultsRow.style.display = (visibleCount === 0 && rows.length > 0) ? "" : "none";
    }

    // Toggle search result counter
    if (filter !== "") {
        counterContainer.classList.remove("hidden");
        visibleCountEl.textContent = visibleCount;
    } else {
        counterContainer.classList.add("hidden");
    }
}
</script>
@endsection