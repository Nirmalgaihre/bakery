@extends('layouts.admin')

@section('title', 'Import Sales Data - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Import Sales Ledger Records')

@section('content')
<div class="max-w-5xl w-full mx-auto font-sans">

    <!-- Header Navigation -->
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-blue-600 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Sales Ledger
        </a>
    </div>

    @if(session('error'))
    <div class="mb-4 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- File Upload Section -->
        <div class="lg:col-span-1 bg-white border border-slate-200 rounded-lg shadow-sm p-5 h-fit">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4 flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-blue-600"></i> Upload File
            </h3>

            <form action="{{ route('admin.invoices.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Select Excel / CSV File</label>
                    <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-lg p-4 text-center cursor-pointer bg-slate-50 transition" onclick="document.getElementById('fileInput').click()">
                        <i class="fa-solid fa-file-excel text-3xl text-emerald-600 mb-2"></i>
                        <p class="text-xs text-slate-600 font-medium">Click to browse file</p>
                        <p class="text-[10px] text-slate-400 mt-1">Supports .xlsx, .xls, .csv (Max: 5MB)</p>
                        <input type="file" id="fileInput" name="excel_file" class="hidden" accept=".xlsx, .xls, .csv" required onchange="displayFileName(this)">
                    </div>
                    <p id="fileName" class="mt-2 text-xs text-blue-600 font-semibold truncate hidden"></p>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2 px-4 rounded-md shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-upload"></i> Start Import
                </button>
            </form>
        </div>

        <!-- Demo Data & Guidelines Section -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Expected Excel Format -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div class="text-xs font-bold text-slate-700 uppercase tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-table text-emerald-600"></i> Excel Template & Demo Structure
                    </div>
                    <!-- Template Download Link -->
                    <a href="#" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs px-3 py-1.5 rounded transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-download"></i> Download Demo Excel
                    </a>
                </div>

                <div class="p-4">
                    <p class="text-xs text-slate-600 mb-3">Your import file must strictly follow the header column sequence shown below:</p>

                    <!-- Demo Excel Table -->
                    <div class="overflow-x-auto border border-slate-200 rounded-md">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100 border-b border-slate-200 text-slate-700 font-bold uppercase">
                                    <th class="p-2 border-r border-slate-200 text-center w-12 bg-slate-200/60">#</th>
                                    <th class="p-2 border-r border-slate-200">A: Transaction_Date</th>
                                    <th class="p-2 border-r border-slate-200">B: Customer_Name</th>
                                    <th class="p-2 border-r border-slate-200 text-right">C: Amount</th>
                                    <th class="p-2">D: Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono text-[11px] text-slate-600">
                                <tr>
                                    <td class="p-2 border-r border-slate-200 text-center bg-slate-50 text-slate-400">1</td>
                                    <td class="p-2 border-r border-slate-200">2026-07-01</td>
                                    <td class="p-2 border-r border-slate-200 font-semibold text-slate-800">Apex Traders Pvt Ltd</td>
                                    <td class="p-2 border-r border-slate-200 text-right">25000.00</td>
                                    <td class="p-2"><span class="text-emerald-600 font-bold">Paid</span></td>
                                </tr>
                                <tr>
                                    <td class="p-2 border-r border-slate-200 text-center bg-slate-50 text-slate-400">2</td>
                                    <td class="p-2 border-r border-slate-200">2026-07-02</td>
                                    <td class="p-2 border-r border-slate-200 font-semibold text-slate-800">Walk-in Customer</td>
                                    <td class="p-2 border-r border-slate-200 text-right">4500.50</td>
                                    <td class="p-2"><span class="text-amber-600 font-bold">Pending</span></td>
                                </tr>
                                <tr>
                                    <td class="p-2 border-r border-slate-200 text-center bg-slate-50 text-slate-400">3</td>
                                    <td class="p-2 border-r border-slate-200">2026-07-03</td>
                                    <td class="p-2 border-r border-slate-200 font-semibold text-slate-800">Himalayan Agro Corp</td>
                                    <td class="p-2 border-r border-slate-200 text-right">120000.00</td>
                                    <td class="p-2"><span class="text-emerald-600 font-bold">Paid</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-xs text-blue-900 space-y-2">
                <p class="font-bold uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i> Important Rules
                </p>
                <ul class="list-disc pl-5 space-y-1 text-slate-700">
                    <li>Dates must follow the <code class="bg-white px-1 py-0.5 rounded border border-blue-200">YYYY-MM-DD</code> format.</li>
                    <li>Ensure customer names match existing ledger accounts or leave blank for <strong>Walk-in Customers</strong>.</li>
                    <li>Row 1 must be the exact header titles as shown in the demo table above.</li>
                </ul>
            </div>

        </div>
    </div>
</div>

<script>
function displayFileName(input) {
    const fileNameElement = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileNameElement.textContent = 'Selected: ' + input.files[0].name;
        fileNameElement.classList.remove('hidden');
    }
}
</script>
@endsection