@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Import Products</h1>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded whitespace-pre-line">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.products.import.template') }}" class="text-blue-600 underline">
            Download blank template (.xlsx)
        </a>
    </div>

    <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block mb-1 font-medium">File (.xlsx, .xls, or .csv)</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full border rounded p-2">
            @error('file')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <p class="text-sm text-gray-500">
            Expected columns: <code>name, sku, category, price, initial_stock</code>.
            If a row's SKU matches an existing product, that product will be updated instead of duplicated.
        </p>
        <button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded hover:bg-amber-700">
            Upload &amp; Import
        </button>
    </form>
</div>
@endsection
