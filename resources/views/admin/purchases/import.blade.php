@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-6xl mx-auto space-y-6">
    
    <!-- Top Nav Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.purchases.create') }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Import Tally Purchase Ledger</h1>
                <p class="text-sm text-slate-500">Upload Excel spreadsheet (.xlsx / .xls / .csv) exported from Tally</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('import_warning'))
        <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            <span>{{ session('import_warning') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 space-y-2">
            <div class="flex items-center gap-2 font-semibold">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>There were issues with your submission:</span>
            </div>
            <ul class="list-disc list-inside text-sm pl-2 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Required Data Format Reference Box -->
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-table text-emerald-600"></i>
                Required Excel Format Guide
            </h2>
            <span class="text-xs font-medium px-2.5 py-1 rounded bg-emerald-100 text-emerald-800">
                Tally Standard Format
            </span>
        </div>

        <p class="text-sm text-slate-600">
            Make sure your Excel sheet contains the following column headers in the <strong>first row</strong>:
        </p>

        <!-- Visual Table Format Preview -->
        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-100 text-xs font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Miti</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Particulars</th>
                        <th class="px-4 py-3">Vch Type</th>
                        <th class="px-4 py-3">Vch No.</th>
                        <th class="px-4 py-3 text-right">Debit</th>
                        <th class="px-4 py-3 text-right">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono text-xs">
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5">4/1/2082</td>
                        <td class="px-4 py-2.5">17/07/2025</td>
                        <td class="px-4 py-2.5 font-medium text-slate-900">To SG NEPAL PVT.LTD.</td>
                        <td class="px-4 py-2.5">Purchase</td>
                        <td class="px-4 py-2.5">1</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-emerald-600">149147.00</td>
                        <td class="px-4 py-2.5 text-right text-slate-400">0.00</td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5">4/2/2082</td>
                        <td class="px-4 py-2.5">18/07/2025</td>
                        <td class="px-4 py-2.5 font-medium text-slate-900">To SAJILO TRADERS</td>
                        <td class="px-4 py-2.5">Purchase</td>
                        <td class="px-4 py-2.5">5</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-emerald-600">1140.00</td>
                        <td class="px-4 py-2.5 text-right text-slate-400">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-slate-500 pt-1">
            <div class="flex items-start gap-2">
                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                <span><strong>Particulars:</strong> Supplier names automatically strip "To " prefixes and create missing records in the database.</span>
            </div>
            <div class="flex items-start gap-2">
                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                <span><strong>Debit:</strong> Represents the purchase transaction total. Zero/blank rows are ignored automatically.</span>
            </div>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <form action="{{ route('admin.purchases.importExcel') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Select Tally Excel File</label>
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:border-emerald-500 transition cursor-pointer bg-slate-50 relative">
                    <input type="file" name="file" accept=".xls,.xlsx,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required onchange="displayFileName(this)">
                    
                    <div class="space-y-2 pointer-events-none" id="dropzone-text">
                        <i class="fa-solid fa-file-excel text-4xl text-emerald-600"></i>
                        <p class="text-sm font-medium text-slate-700">Click to browse or drop your Tally Excel file here</p>
                        <p class="text-xs text-slate-400">Supports .XLS, .XLSX, and .CSV (Max 5MB)</p>
                    </div>

                    <div id="file-name-display" class="hidden font-semibold text-emerald-700 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-circle-check text-xl"></i>
                        <span id="file-name-text"></span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.purchases.create') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-medium transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Start Import Process</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function displayFileName(input) {
    const display = document.getElementById('file-name-display');
    const text = document.getElementById('file-name-text');
    const dropzoneText = document.getElementById('dropzone-text');

    if (input.files && input.files[0]) {
        text.textContent = input.files[0].name;
        display.classList.remove('hidden');
        dropzoneText.classList.add('hidden');
    }
}
</script>
@endsection