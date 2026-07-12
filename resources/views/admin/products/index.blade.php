@extends('layouts.admin')

@section('title', 'Product Registry - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Product Catalog Registry')

@section('content')
<div class="max-w-7xl w-full mx-auto">

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded text-xs font-semibold">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden mt-4">
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 flex justify-between items-center">
            <div class="text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Active Warehouse Catalog Matrix
            </div>
            
            <!-- Real-time Search Input -->
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search products..." 
                class="text-xs border border-slate-300 rounded px-3 py-1.5 focus:ring-1 focus:ring-blue-500 outline-none w-64">

            @can('create', \App\Models\Product::class)
            <a href="{{ route('admin.products.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded shadow-xs transition-colors uppercase tracking-wide flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Add New Item
            </a>
            @endcan
        </div>

        <div class="p-4 flex items-center gap-2">
            <a href="{{ route('admin.products.export', ['type' => 'xlsx']) }}" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] px-3 py-2 rounded uppercase tracking-wide shadow-xs"><i class="fa-solid fa-file-excel"></i> Export Excel</a>
            <a href="{{ route('admin.products.export', ['type' => 'csv']) }}" class="inline-flex items-center gap-1.5 bg-slate-600 hover:bg-slate-700 text-white font-bold text-[11px] px-3 py-2 rounded uppercase tracking-wide shadow-xs"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
            <a href="{{ route('admin.products.import.form') }}" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-[11px] px-3 py-2 rounded uppercase tracking-wide shadow-xs"><i class="fa-solid fa-file-arrow-up"></i> Import</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-700" id="productTable">
                <thead>
                    <tr class="bg-slate-100/70 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="p-4 pl-5">#</th>
                        <th class="p-4">Product Details</th>
                        <th class="p-4">Item Code</th>
                        <th class="p-4">Specs</th>
                        <th class="p-4">Category</th>
                        <th class="p-4 text-right">Purchase</th>
                        <th class="p-4 text-right">Selling</th>
                        <th class="p-4 text-center">Unit</th>
                        <th class="p-4 text-center">Initial</th>
                        <th class="p-4 text-center">Current</th>
                        <th class="p-4 text-center">Total</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right pr-5">Actions</th>
                    </tr>
                </thead>

                <!-- Skeleton Loader Rows (Visible by default) -->
                <tbody id="skeletonRows" class="divide-y divide-slate-100">
                    @for ($i = 0; $i < 5; $i++)
                    <tr class="animate-pulse">
                        <td class="p-4 pl-5"><div class="h-3 w-4 bg-slate-200 rounded"></div></td>
                        <td class="p-4"><div class="h-4 w-40 bg-slate-200 rounded"></div></td>
                        <td class="p-4"><div class="h-3 w-16 bg-slate-200 rounded"></div></td>
                        <td class="p-4"><div class="h-3 w-20 bg-slate-200 rounded"></div></td>
                        <td class="p-4"><div class="h-3 w-24 bg-slate-200 rounded"></div></td>
                        <td class="p-4"><div class="h-3 w-16 bg-slate-200 rounded ml-auto"></div></td>
                        <td class="p-4"><div class="h-3 w-16 bg-slate-200 rounded ml-auto"></div></td>
                        <td class="p-4 text-center"><div class="h-4 w-8 bg-slate-200 rounded mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="h-3 w-8 bg-slate-200 rounded mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="h-3 w-8 bg-slate-200 rounded mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="h-3 w-8 bg-slate-200 rounded mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="h-4 w-12 bg-slate-200 rounded mx-auto"></div></td>
                        <td class="p-4 text-right pr-5"><div class="h-4 w-20 bg-slate-200 rounded ml-auto"></div></td>
                    </tr>
                    @endfor
                </tbody>

                <!-- Actual Dynamic Data (Hidden until DOM loaded) -->
                <tbody id="tableContent" class="hidden divide-y divide-slate-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-4 pl-5 text-xs text-slate-500">{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                        <td class="p-4 font-semibold text-slate-900">{{ $product->name }}</td>
                        <td class="p-4 font-mono text-xs text-slate-600">{{ $product->item_code ?? 'N/A' }}</td>
                        <td class="p-4 text-xs text-slate-600">{{ $product->color ?? '-' }} / {{ $product->size ?? '-' }}</td>
                        <td class="p-4 text-xs">{{ $product->category }}</td>
                        <td class="p-4 text-right font-mono text-xs text-slate-600">Rs. {{ number_format($product->purchase_cost, 2) }}</td>
                        <td class="p-4 text-right font-mono text-xs font-bold text-slate-900">Rs. {{ number_format($product->selling_price, 2) }}</td>
                        <td class="p-4 text-center"><span class="uppercase font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded">{{ $product->inventory_unit }}</span></td>
                        <td class="p-4 text-center font-mono text-slate-500">{{ $product->initial_stock ?? 0 }}</td>
                        <td class="p-4 text-center font-mono text-slate-700">{{ $product->stock ?? 0 }}</td>
                        <td class="p-4 text-center font-bold font-mono text-blue-700">{{ ($product->initial_stock ?? 0) + ($product->stock ?? 0) }}</td>
                        <td class="p-4 text-center">
                            @if((($product->initial_stock ?? 0) + ($product->stock ?? 0)) <= $product->alert_stock_level)
                                <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold uppercase px-2 py-0.5 rounded animate-pulse">Low</span>
                            @else
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase px-2 py-0.5 rounded">Good</span>
                            @endif
                        </td>
                        <td class="p-4 text-right pr-5 flex items-center justify-end gap-2">
                            <a href="{{ route('admin.inventory.create', $product->id) }}" class="text-emerald-600 hover:text-emerald-800 p-1"><i class="fa-solid fa-plus-circle"></i></a>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800 p-1"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 p-1"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="13" class="p-8 text-center text-sm text-slate-400">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="p-4 border-t bg-slate-50/50">{{ $products->links() }}</div>
        @endif
    </div>
</div>

<script>
// Toggle skeleton loader off when content finishes loading
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("skeletonRows").style.display = "none";
    document.getElementById("tableContent").classList.remove("hidden");
});

function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("productTable");
    let tr = table.getElementsByTagName("tr");
    
    // Adjust start index if skeleton rows are technically hidden but still in DOM
    let startIndex = document.getElementById("skeletonRows").style.display === "none" ? 6 : 1; 

    for (let i = 1; i < tr.length; i++) {
        // Skip skeleton rows during data search filtering
        if(tr[i].parentNode.id === 'skeletonRows') continue; 
        
        let td = tr[i].getElementsByTagName("td")[1]; 
        if (td) {
            let txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}
</script>
@endsection