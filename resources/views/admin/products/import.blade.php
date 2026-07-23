@extends('layouts.admin')
@section('title', 'Import Bakery Products')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6 text-slate-800">Import Bakery Inventory</h1>

    {{-- Success Feedback --}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-emerald-100 text-emerald-800 rounded font-bold border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Feedback --}}
    @if (session('error'))
        <div class="mb-4 p-4 bg-rose-100 text-rose-800 rounded whitespace-pre-line border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Import Form --}}
    <form id="importForm" action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm">
        @csrf
        <div class="mb-4">
            <label class="block mb-2 font-bold text-slate-700">Select Bakery File (.xlsx, .csv)</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full border border-slate-300 rounded p-2 text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
            <p class="text-xs text-slate-500 mt-2">Ensure your Excel file headers match the bakery template preview below exactly.</p>
        </div>
        <button type="submit" id="submitBtn" class="px-6 py-2 bg-amber-600 text-white font-bold rounded hover:bg-amber-700 transition-colors">
            Upload & Process Import
        </button>
    </form>

    {{-- Spreadsheet Preview Section --}}
    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Required Excel Layout (Bakery Format)</h2>
                <p class="text-xs text-slate-500">Row 1 must contain these exact column header names in lowercase.</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-amber-500" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                </svg>
                Bakery Template Preview
            </span>
        </div>

        {{-- Excel Visual Container --}}
        <div class="overflow-x-auto rounded-lg border border-slate-300 shadow-sm bg-white">
            <table class="w-full text-sm border-collapse font-sans min-w-[650px]">
                {{-- Excel Column Letter Headers (A, B, C, D...) --}}
                <thead>
                    <tr class="bg-slate-200 text-slate-500 font-mono text-xs text-center select-none border-b border-slate-300">
                        <th class="w-10 bg-slate-300 border-r border-slate-400 p-1 font-normal"></th>
                        <th class="p-1 border-r border-slate-300 font-normal">A</th>
                        <th class="p-1 border-r border-slate-300 font-normal">B</th>
                        <th class="p-1 border-r border-slate-300 font-normal">C</th>
                        <th class="p-1 border-r border-slate-300 font-normal">D</th>
                        <th class="p-1 border-r border-slate-300 font-normal">E</th>
                        <th class="p-1 border-r border-slate-300 font-normal">F</th>
                    </tr>
                    
                    {{-- Row 1: Header Names (Required) --}}
                    <tr class="bg-emerald-50 text-emerald-900 font-mono font-bold text-xs border-b border-slate-300">
                        <td class="bg-slate-200 text-slate-500 font-mono text-xs text-center border-r border-slate-400 select-none font-normal">1</td>
                        <td class="p-2 border-r border-slate-300">name</td>
                        <td class="p-2 border-r border-slate-300">category</td>
                        <td class="p-2 border-r border-slate-300 text-right">purchase_cost</td>
                        <td class="p-2 border-r border-slate-300 text-right">selling_price</td>
                        <td class="p-2 border-r border-slate-300">inventory_unit</td>
                        <td class="p-2 text-right">initial_stock</td>
                    </tr>
                </thead>

                {{-- Rows 2, 3 & 4: Bakery Sample Data Rows --}}
                <tbody class="divide-y divide-slate-200 text-slate-700 font-sans text-xs">
                    <tr>
                        <td class="bg-slate-200 text-slate-500 font-mono text-xs text-center border-r border-slate-400 select-none">2</td>
                        <td class="p-2.5 border-r border-slate-200 font-medium text-slate-900">All-Purpose Flour (25kg Bag)</td>
                        <td class="p-2.5 border-r border-slate-200">Raw Ingredients</td>
                        <td class="p-2.5 border-r border-slate-200 text-right font-mono">18.50</td>
                        <td class="p-2.5 border-r border-slate-200 text-right font-mono">25.00</td>
                        <td class="p-2.5 border-r border-slate-200">kg</td>
                        <td class="p-2.5 text-right font-mono">50</td>
                    </tr>
                    <tr class="bg-slate-50/50">
                        <td class="bg-slate-200 text-slate-500 font-mono text-xs text-center border-r border-slate-400 select-none">3</td>
                        <td class="p-2.5 border-r border-slate-200 font-medium text-slate-900">Unsalted French Butter</td>
                        <td class="p-2.5 border-r border-slate-200">Dairy & Fats</td>
                        <td class="p-2.5 border-r border-slate-200 text-right font-mono">4.20</td>
                        <td class="p-2.5 border-r border-slate-200 text-right font-mono">6.50</td>
                        <td class="p-2.5 border-r border-slate-200">lbs</td>
                        <td class="p-2.5 text-right font-mono">30</td>
                    </tr>
                    <tr>
                        <td class="bg-slate-200 text-slate-500 font-mono text-xs text-center border-r border-slate-400 select-none">4</td>
                        <td class="p-2.5 border-r border-slate-200 font-medium text-slate-900">Chocolate Croissant</td>
                        <td class="p-2.5 border-r border-slate-200">Pastries</td>
                        <td class="p-2.5 border-r border-slate-200 text-right font-mono">1.10</td>
                        <td class="p-2.5 border-r border-slate-200 text-right font-mono">3.75</td>
                        <td class="p-2.5 border-r border-slate-200">pcs</td>
                        <td class="p-2.5 text-right font-mono">120</td>
                    </tr>
                    <tr class="bg-slate-50/50">
                        <td class="bg-slate-200 text-slate-500 font-mono text-xs text-center border-r border-slate-400 select-none">5</td>
                        <td class="p-2.5 border-r border-slate-200 font-medium text-slate-900">Custom Cake Box (10x10)</td>
                        <td class="p-2.5 border-r border-slate-200">Packaging</td>
                        <td class="p-2.5 border-r border-slate-200 text-right font-mono">0.45</td>
                        <td class="p-2.5 border-r border-slate-200 text-right font-mono">1.00</td>
                        <td class="p-2.5 border-r border-slate-200">pack</td>
                        <td class="p-2.5 text-right font-mono">200</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <p class="text-xs text-slate-500 mt-2">
            <span class="font-bold text-slate-700">Note:</span> Do not change the order or spelling of the header names in Row 1.
        </p>
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