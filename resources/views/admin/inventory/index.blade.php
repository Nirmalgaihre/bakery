@extends('layouts.admin') {{-- Adjust this to match your admin layout file --}}

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Management</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Current Stock</th>
                        <th>Unit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku ?? 'N/A' }}</td>
                        <td>{{ $product->initial_stock }}</td>
                        <td>{{ $product->inventory_unit }}</td>
                        <td>
                            @if($product->initial_stock <= 5)
                                <span class="badge bg-danger">Low Stock</span>
                            @else
                                <span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No products found in inventory.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection