@extends('layouts.admin')
@section('title', 'Manage Inventory')

@section('content')
<!-- Tightened container: removed max-w-4xl for a wider, cleaner look -->
<div class="my-6">
    
    <!-- Search Input: Styled to be compact -->
    <div class="mb-4">
        <input 
            type="text" 
            id="searchInput" 
            onkeyup="searchTable()" 
            placeholder="Search products..." 
            class="w-full md:w-1/4 px-3 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
        >
    </div>

    <!-- Table Container: Matches the minimalist border/background style -->
    <div class="bg-white border border-slate-200 rounded shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-md font-bold text-slate-700">Product Inventory</h2>
        </div>

        <div class="overflow-x-auto">
            <table id="inventoryTable" class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-500 uppercase text-[11px] font-bold">
                        <th class="px-6 py-4">SN</th>
                        <th class="px-6 py-4">Product Name</th>
                        <th class="px-6 py-4">Current Stock</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($products as $product)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3 text-slate-400 font-mono text-xs">{{ $loop->iteration }}</td>
                        <td class="px-6 py-3 text-slate-700">{{ $product->name }}</td>
                        <td class="px-6 py-3 text-slate-600 font-bold">
                            {{ $product->initial_stock }} 
                            <span class="text-[10px] text-slate-400">{{ strtoupper($product->inventory_unit) }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if($product->initial_stock <= $product->alert_stock_level)
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase bg-orange-100 text-orange-600 rounded">Low</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase bg-emerald-100 text-emerald-600 rounded">Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.inventory.create', $product->id) }}" 
                               class="text-[11px] font-bold text-blue-600 hover:text-blue-800 uppercase">
                               Manage
                            </a>
                        </td>
                    </tr>
                    @endforeach
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
        const td = tr[i].getElementsByTagName("td")[1]; // Target Product Name
        if (td) {
            tr[i].style.display = (td.textContent || td.innerText).toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}
</script>
@endsection