@extends('layouts.admin')
@section('title', 'Low Stock Alerts')

@section('content')
<div class="my-6">
    
    <!-- Search Input: Styled to match -->
    <div class="mb-4">
        <input 
            type="text" 
            id="searchInput" 
            onkeyup="searchTable()" 
            placeholder="Search low stock products..." 
            class="w-full md:w-1/4 px-3 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
        >
    </div>

    <!-- Table Container -->
    <div class="bg-white border border-slate-200 rounded shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-md font-bold text-slate-700">Low Stock Alerts</h2>
        </div>

        <div class="overflow-x-auto">
            <table id="inventoryTable" class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-500 uppercase text-[11px] font-bold">
                        <th class="px-6 py-4">SN</th>
                        <th class="px-6 py-4">Product Name</th>
                        <th class="px-6 py-4">Current Stock</th>
                        <th class="px-6 py-4">Alert Level</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lowStockProducts as $product)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3 text-slate-400 font-mono text-xs">{{ $loop->iteration }}</td>
                        <td class="px-6 py-3 text-slate-700 font-medium">{{ $product->name }}</td>
                        <td class="px-6 py-3 text-slate-600 font-bold">
                            {{ $product->initial_stock }} 
                            <span class="text-[10px] text-slate-400 font-normal">{{ strtoupper($product->inventory_unit) }}</span>
                        </td>
                        <td class="px-6 py-3 text-slate-500 font-mono text-xs">{{ $product->alert_stock_level }}</td>
                        <td class="px-6 py-3">
                            @if($product->email_sent)
                                <div class="flex items-center gap-1.5 text-emerald-600">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-[10px] font-bold uppercase">Sent</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 text-orange-500">
                                    <span class="text-[10px] font-bold uppercase">Pending</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                               class="text-[11px] font-bold text-blue-600 hover:text-blue-800 uppercase">
                               Restock
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-xs">
                            All stock levels are currently optimal.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function searchTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toUpperCase();
    const table = document.getElementById("inventoryTable");
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        // Target index 1 for Product Name
        const td = tr[i].getElementsByTagName("td")[1]; 
        if (td) {
            tr[i].style.display = (td.textContent || td.innerText).toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}
</script>
@endsection