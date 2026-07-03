@extends('layouts.admin')
@section('title', 'Import Products')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6 text-slate-800">Import Products</h1>

    {{-- Success Feedback --}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-emerald-100 text-emerald-800 rounded font-bold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Feedback --}}
    @if (session('error'))
        <div class="mb-4 p-4 bg-rose-100 text-rose-800 rounded whitespace-pre-line">
            {{ session('error') }}
        </div>
    @endif

    {{-- Import Form --}}
    <form id="importForm" action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm">
        @csrf
        <div class="mb-4">
            <label class="block mb-2 font-bold text-slate-700">Select File (.xlsx, .csv)</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full border border-slate-300 rounded p-2 text-sm">
            <p class="text-xs text-slate-500 mt-2">Ensure your Excel file headers match the format below exactly.</p>
        </div>
        <button type="submit" id="submitBtn" class="px-6 py-2 bg-amber-600 text-white font-bold rounded hover:bg-amber-700 transition-colors">
            Upload & Process Import
        </button>
    </form>

    {{-- Format Reference Table --}}
    <div class="mt-8">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Required Excel Format</h2>
        <table class="w-full text-sm border-collapse bg-white shadow-sm rounded-lg overflow-hidden">
            <thead class="bg-slate-100">
                <tr class="text-slate-600 uppercase text-xs">
                    <th class="p-3 border text-left">Header Name</th>
                    <th class="p-3 border text-left">Example</th>
                </tr>
            </thead>
            <tbody class="divide-y text-slate-700">
                <tr><td class="p-3 border font-mono font-bold">name</td><td class="p-3 border">Item A</td></tr>
                <tr><td class="p-3 border font-mono font-bold">category</td><td class="p-3 border">Electronics</td></tr>
                <tr><td class="p-3 border font-mono font-bold">purchase_cost</td><td class="p-3 border">500.00</td></tr>
                <tr><td class="p-3 border font-mono font-bold">selling_price</td><td class="p-3 border">750.00</td></tr>
                <tr><td class="p-3 border font-mono font-bold">inventory_unit</td><td class="p-3 border">pcs</td></tr>
                <tr><td class="p-3 border font-mono font-bold">initial_stock</td><td class="p-3 border">10</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Simple script to prevent double submission
    document.getElementById('importForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerText = 'Processing... Please wait';
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    });
</script>
@endsection