@extends('layouts.admin')

@section('title', 'Invoice Registry Ledger')
@section('panel_title', 'Financial Invoice Ledger')

@section('content')
<link href="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/css/nepali.datepicker.v5.0.6.min.css" rel="stylesheet" type="text/css"/>

<div class="space-y-6 pb-12 font-sans antialiased text-slate-600">
    
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-xl flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-xl flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Header & Action Controls -->
    <div class="bg-white p-5 border border-slate-200 rounded-xl shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-2xs">
                <i class="fa-solid fa-file-invoice-dollar text-base"></i>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">Invoice Registry Ledger</h1>
                <p class="text-xs text-slate-400">Overview of system transaction histories grouped by customer</p>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Payment Status Select Filter -->
            <div class="relative w-full sm:w-44">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-filter text-xs"></i>
                </span>
                <select id="statusFilter" 
                        class="w-full pl-9 pr-8 py-2 bg-slate-50/50 border border-slate-200 rounded-lg text-xs font-semibold shadow-2xs focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors appearance-none text-slate-700 cursor-pointer">
                    <option value="ALL">All Statuses</option>
                    <option value="PAID">Paid Only</option>
                    <option value="PARTIAL">Partial Due</option>
                    <option value="CREDIT">Full Credit</option>
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </span>
            </div>

            <!-- Search Bar -->
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="ledgerSearch" 
                       placeholder="Search invoice, customer..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-lg text-xs font-medium shadow-2xs focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors">
            </div>
        </div>
    </div>

    @php
        $calcInvoices = $invoices ?? collect();
        $totalGrandSum = $calcInvoices->sum(fn($i) => floatval($i->grand_total));
        $totalPaidSum = $calcInvoices->sum(fn($i) => floatval($i->paid_amount));
        $totalDueSum = max(0, $totalGrandSum - $totalPaidSum);
    @endphp

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Invoiced</span>
                <p class="text-base font-bold font-mono text-slate-800 mt-0.5">Rs. {{ number_format($totalGrandSum, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-receipt text-sm"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Collected</span>
                <p class="text-base font-bold font-mono text-emerald-600 mt-0.5">Rs. {{ number_format($totalPaidSum, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-circle-dollar-to-slot text-sm"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Outstanding Due</span>
                <p class="text-base font-bold font-mono text-rose-600 mt-0.5">Rs. {{ number_format($totalDueSum, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Invoices Count</span>
                <p class="text-base font-bold font-mono text-slate-800 mt-0.5">{{ number_format($calcInvoices->count()) }}</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-hashtag text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table id="ledgerDataTable" class="w-full border-collapse text-left text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4">Customer Details</th>
                        <th class="py-3.5 px-4">Invoice Number</th>
                        <th class="py-3.5 px-4">Date (BS)</th>
                        <th class="py-3.5 px-4">Payment Method</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Paid Amount</th>
                        <th class="py-3.5 px-4 text-right">Grand Total</th>
                        <th class="py-3.5 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody" class="divide-y divide-slate-100 text-slate-700">
                    @forelse($invoices as $invoice)
                    @php
                        $grandTotal = floatval($invoice->grand_total);
                        $paidAmount = floatval($invoice->paid_amount);
                        $remainingDue = max(0, $grandTotal - $paidAmount);
                        
                        if ($paidAmount >= $grandTotal && $grandTotal > 0) {
                            $statusText = 'Paid';
                            $statusValue = 'PAID';
                            $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        } elseif ($paidAmount > 0 && $paidAmount < $grandTotal) {
                            $statusText = 'Partial';
                            $statusValue = 'PARTIAL';
                            $statusClass = 'bg-amber-50 text-amber-700 border-amber-200';
                        } else {
                            $statusText = 'Credit';
                            $statusValue = 'CREDIT';
                            $statusClass = 'bg-rose-50 text-rose-700 border-rose-200';
                        }
                    @endphp
                    
                    <tr class="ledger-row hover:bg-blue-50/40 transition-colors cursor-pointer"
                        data-status="{{ $statusValue }}"
                        data-href="{{ route('admin.invoices.show', $invoice->id) }}">
                        
                        <td class="py-3.5 px-4 font-bold text-slate-900 search-target">
                            {{ $invoice->patient_name ?? ($invoice->customer->name ?? 'Walk-in Customer') }}
                        </td>
                        
                        <td class="py-3.5 px-4 font-mono font-semibold text-blue-600 text-xs search-target">
                            #{{ $invoice->invoice_no }}
                        </td>
                        
                        <td class="py-3.5 px-4 font-mono text-slate-600">
                            {{ $invoice->nepali_date ?? $invoice->invoice_date }}
                        </td>
                        
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                                {{ $invoice->payment_method ?? 'Cash' }}
                            </span>
                        </td>
                        
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        
                        <td class="py-3.5 px-4 text-right font-mono text-slate-600">
                            Rs. {{ number_format($paidAmount, 2) }}
                        </td>
                        
                        <td class="py-3.5 px-4 text-right font-bold font-mono text-slate-900">
                            Rs. {{ number_format($grandTotal, 2) }}
                        </td>
                        
                        <td class="py-3.5 px-4 text-center ledger-action-cell">
                            @if($remainingDue > 0)
                                <button type="button" 
                                    data-payment-id="{{ $invoice->id }}"
                                    data-payment-invoice-no="{{ $invoice->invoice_no }}"
                                    data-payment-due-amount="{{ $remainingDue }}"
                                    class="payment-button inline-flex items-center px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold rounded-lg shadow-2xs transition-colors gap-1">
                                    <i class="fa-solid fa-hand-holding-dollar"></i> Pay
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-lg border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Cleared
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="noRecordsRow">
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <i class="fa-solid fa-folder-open text-3xl block mb-2 text-slate-300"></i>
                            No transaction logs matched for this criteria.
                        </td>
                    </tr>
                    @endforelse
                    
                    <tr id="dynamicNoRecordsRow" class="hidden">
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-3xl block mb-2 text-slate-300"></i>
                            No results match your selected filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination Controls -->
        <div class="bg-slate-50/80 px-4 py-3 flex items-center justify-between border-t border-slate-200 text-xs select-none">
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="btnPrevMobile" class="relative inline-flex items-center px-4 py-1.5 border border-slate-300 rounded-md bg-white font-medium text-slate-700 hover:bg-slate-50 transition-colors">Previous</button>
                <button id="btnNextMobile" class="relative inline-flex items-center px-4 py-1.5 border border-slate-300 rounded-md bg-white font-medium text-slate-700 hover:bg-slate-50 transition-colors">Next</button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-slate-500">
                        Showing <span id="lblRangeStart" class="font-bold text-slate-800">0</span> to <span id="lblRangeEnd" class="font-bold text-slate-800">0</span> of <span id="lblTotalEntries" class="font-bold text-slate-800">0</span> entries
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-lg shadow-2xs -space-x-px" aria-label="Pagination">
                        <button id="btnPrevDesktop" class="relative inline-flex items-center px-3 py-1.5 rounded-l-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </button>
                        <div id="desktopPageContainer" class="flex -space-x-px"></div>
                        <button id="btnNextDesktop" class="relative inline-flex items-center px-3 py-1.5 rounded-r-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Collect Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all duration-300">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        
        <div class="bg-slate-50 px-5 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-emerald-600 text-sm"></i> 
                Collect Due Payment (<span id="modal_invoice_no" class="font-mono text-blue-600 font-bold"></span>)
            </h3>
            <button type="button" onclick="closePaymentModal()" class="h-7 w-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center text-sm font-bold">✕</button>
        </div>
        
        <form id="paymentForm" method="POST" action="">
            @csrf
            <div class="p-5 space-y-4">
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Outstanding Due Amount</label>
                    <div class="relative">
                        <input type="text" id="modal_due_amount" readonly 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm font-mono font-bold text-slate-700 rounded-xl outline-none select-none">
                        <span class="absolute right-3 top-2.5 text-[10px] font-bold text-slate-400">NPR</span>
                    </div>
                </div>

                <!-- Nepali Date Input -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Payment Date (BS) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="payment_date_bs" name="payment_date_bs" required readonly
                            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer bg-white">
                        <i class="fa-solid fa-calendar-days absolute right-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Received Amount <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rs.</span>
                        <input type="number" step="0.01" name="received_amount" required id="received_amount" min="0.01" placeholder="0.00"
                            class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-0.5">Enter payment amount handed over by customer.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Remarks / Notes</label>
                    <textarea name="remarks" rows="2" placeholder="Write payment notes or transaction details..."
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                </div>
            </div>
            
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end gap-2.5">
                <button type="button" onclick="closePaymentModal()" 
                    class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-2xs">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-colors shadow-2xs flex items-center gap-1.5">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Save Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('ledgerSearch');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = Array.from(document.querySelectorAll('.ledger-row'));
    const dynamicNoRecords = document.getElementById('dynamicNoRecordsRow');
    const nativeEmptyRow = document.getElementById('noRecordsRow');

    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredRows = [...tableRows];

    const lblRangeStart = document.getElementById('lblRangeStart');
    const lblRangeEnd = document.getElementById('lblRangeEnd');
    const lblTotalEntries = document.getElementById('lblTotalEntries');
    const desktopPageContainer = document.getElementById('desktopPageContainer');

    function applyCombinedFilters() {
        const searchQuery = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;

        filteredRows = tableRows.filter(function(row){
            const matchesStatus = selectedStatus === "ALL" || row.dataset.status === selectedStatus;
            let matchesSearch = true;

            if(searchQuery !== ""){
                matchesSearch = row.innerText.toLowerCase().includes(searchQuery);
            }

            return matchesStatus && matchesSearch;
        });

        currentPage = 1;
        updatePagination();
    }

    function updatePagination(){
        const totalRows = filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));

        if(currentPage > totalPages){
            currentPage = totalPages;
        }

        tableRows.forEach(function(row){
            row.classList.add('hidden');
        });

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        filteredRows.slice(start, end).forEach(function(row){
            row.classList.remove('hidden');
        });

        if(totalRows === 0){
            dynamicNoRecords.classList.remove('hidden');
            if(nativeEmptyRow){
                nativeEmptyRow.classList.add('hidden');
            }
        } else {
            dynamicNoRecords.classList.add('hidden');
        }

        lblRangeStart.innerHTML = totalRows == 0 ? 0 : start + 1;
        lblRangeEnd.innerHTML = Math.min(end, totalRows);
        lblTotalEntries.innerHTML = totalRows;

        desktopPageContainer.innerHTML = "";

        for(let i = 1; i <= totalPages; i++){
            const btn = document.createElement("button");
            btn.type = "button";
            btn.innerHTML = i;
            btn.className = "relative inline-flex items-center px-3 py-1.5 border text-xs font-semibold " +
                (i === currentPage
                ? "bg-blue-600 text-white border-blue-600 z-10"
                : "bg-white border-slate-200 text-slate-600 hover:bg-slate-50");

            btn.addEventListener("click", function(){
                currentPage = i;
                updatePagination();
            });

            desktopPageContainer.appendChild(btn);
        }

        document.getElementById("btnPrevDesktop").disabled = currentPage === 1;
        document.getElementById("btnNextDesktop").disabled = currentPage === totalPages;
        document.getElementById("btnPrevMobile").disabled = currentPage === 1;
        document.getElementById("btnNextMobile").disabled = currentPage === totalPages;
    }

    searchInput.addEventListener("keyup", applyCombinedFilters);
    statusFilter.addEventListener("change", applyCombinedFilters);

    document.getElementById("btnPrevDesktop").addEventListener("click", function(){
        if(currentPage > 1){
            currentPage--;
            updatePagination();
        }
    });

    document.getElementById("btnNextDesktop").addEventListener("click", function(){
        if(currentPage < Math.ceil(filteredRows.length / rowsPerPage)){
            currentPage++;
            updatePagination();
        }
    });

    document.getElementById("btnPrevMobile").addEventListener("click", function(){
        if(currentPage > 1){
            currentPage--;
            updatePagination();
        }
    });

    document.getElementById("btnNextMobile").addEventListener("click", function(){
        if(currentPage < Math.ceil(filteredRows.length / rowsPerPage)){
            currentPage++;
            updatePagination();
        }
    });

    document.getElementById("ledgerTableBody").addEventListener("click", function(e){
        const payBtn = e.target.closest(".payment-button");
        if(payBtn){
            e.preventDefault();
            e.stopPropagation();
            openPaymentModal(
                payBtn.dataset.paymentId,
                payBtn.dataset.paymentInvoiceNo,
                payBtn.dataset.paymentDueAmount
            );
            return;
        }

        const row = e.target.closest(".ledger-row");
        if(row){
            window.location.href = row.dataset.href;
        }
    });

    updatePagination();
});

/* Payment Modal Functions */
let nepaliPickerLoaded = false;

function openPaymentModal(id, invoiceNo, dueAmount) {
    const modal = document.getElementById("paymentModal");
    document.getElementById("modal_invoice_no").innerText = invoiceNo;
    document.getElementById("modal_due_amount").value = parseFloat(dueAmount).toFixed(2);

    const amountInput = document.getElementById("received_amount");
    amountInput.value = "";
    amountInput.max = parseFloat(dueAmount);

    document.getElementById("paymentForm").action = "/admin/sales/" + id + "/update-payment";

    const dateInput = document.getElementById("payment_date_bs");
    if (!nepaliPickerLoaded) {
        dateInput.NepaliDatePicker({
            dateFormat: "YYYY-MM-DD",
            closeOnDateSelect: true
        });
        nepaliPickerLoaded = true;
    }

    modal.classList.remove("hidden");
    modal.classList.add("flex");
    setTimeout(function () {
        amountInput.focus();
    }, 200);
}

function closePaymentModal() {
    const modal = document.getElementById("paymentModal");
    modal.classList.remove("flex");
    modal.classList.add("hidden");
    document.getElementById("paymentForm").reset();
}

window.addEventListener("click", function (e) {
    const modal = document.getElementById("paymentModal");
    if (e.target === modal) {
        closePaymentModal();
    }
});

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        closePaymentModal();
    }
});

/* Payment Form Validation */
const paymentForm = document.getElementById("paymentForm");
if (paymentForm) {
    paymentForm.addEventListener("submit", function (e) {
        const due = parseFloat(document.getElementById("modal_due_amount").value);
        const received = parseFloat(document.getElementById("received_amount").value);
        const paymentDate = document.getElementById("payment_date_bs").value;

        if (paymentDate === "") {
            alert("Please select payment date.");
            e.preventDefault();
            return false;
        }

        if (isNaN(received) || received <= 0) {
            alert("Enter valid payment amount.");
            e.preventDefault();
            return false;
        }

        if (received > due) {
            alert("Received amount cannot exceed Due Amount.");
            e.preventDefault();
            return false;
        }
    });
}
</script>
<script src="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/js/nepali.datepicker.v5.0.6.min.js"></script>
@endsection