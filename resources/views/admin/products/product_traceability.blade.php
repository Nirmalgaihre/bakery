@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Traceability: {{ $product->name }}</h4>
            <small>Category: {{ $product->category->name ?? 'N/A' }}</small>
        </div>
        <div class="card-body">
            <!-- Inward Movement (Suppliers) -->
            <h5 class="text-success mt-4">Movement Inwards (Supplier Traceability)</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th><th>Supplier</th><th>Qty</th><th>Basic Rate</th><th>Effective Rate</th><th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inwards as $in)
                    <tr>
                        <td>{{ $in->purchase_date }}</td>
                        <td>{{ $in->supplier_name }}</td>
                        <td>{{ number_format($in->quantity) }}</td>
                        <td>{{ number_format($in->rate, 2) }}</td>
                        <td>{{ number_format($in->effective_rate ?? $in->rate, 2) }}</td>
                        <td>{{ number_format($in->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Outward Movement (Buyers) -->
            <h5 class="text-danger mt-4">Movement Outwards (Buyer Traceability)</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th><th>Buyer</th><th>Qty</th><th>Basic Rate</th><th>Effective Rate</th><th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outwards as $out)
                    <tr>
                        <td>{{ $out->created_at->format('Y-m-d') }}</td>
                        <td>{{ $out->invoice->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ number_format($out->qty) }}</td>
                        <td>{{ number_format($out->rate, 2) }}</td>
                        <td>{{ number_format($out->effective_rate ?? $out->rate, 2) }}</td>
                        <td>{{ number_format($out->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection