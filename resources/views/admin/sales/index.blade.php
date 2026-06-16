@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6 px-4 max-w-7xl font-sans antialiased text-slate-600">
    
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center shadow-sm">
            <i class="fa-solid fa-circle-check mr-2 text-emerald-500 text-base"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium rounded-xl flex items-center shadow-sm">
            <i class="fa-solid fa-circle-exclamation mr-2 text-rose-500 text-base"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
        <div>
            <h1 class="text-lg font-bold text-slate-900 tracking-tight">Invoice Registry Ledger</h1>
            <p class="text-xs text-slate-400 mt-0.5">Overview of system transaction histories grouped by customer.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            
            <div class="relative w-full sm:w-44">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-filter text-xs"></i>
                </span>
                <select id="statusFilter" 
                        class="w-full pl-9 pr-8 py-1.5 bg-white border border-slate-200 rounded-md text-xs font-medium shadow-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors appearance-none text-slate-700 cursor-pointer">
                    <option value="ALL">All Payment Status</option>
                    <option value="PAID">Paid Only</option>
                    <option value="PARTIAL">Partial Due</option>
                    <option value="CREDIT">Full Credit</option>
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </span>
            </div>

            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="ledgerSearch" 
                       placeholder="Search invoice, customer..." 
                       class="w-full pl-9 pr-4 py-1.5 bg-white border border-slate-200 rounded-md text-xs shadow-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200/60 rounded-lg shadow-sm overflow-hidden">
        <table id="ledgerDataTable" class="w-full border-collapse text-left text-xs whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-semibold uppercase tracking-wider text-[10px]">
                    <th class="py-3 px-4">Customer Details</th>
                    <th class="py-3 px-4">Invoice Number</th>
                    <th class="py-3 px-4">Date (BS)</th>
                    <th class="py-3 px-4">Payment Method</th>
                    <th class="py-3 px-4 text-center">Payment Status</th>
                    <th class="py-3 px-4 text-right">Paid Amount</th>
                    <th class="py-3 px-4 text-right">Grand Total</th>
                    <th class="py-3 px-4 text-center">Action</th>
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
                
                <tr class="ledger-row hover:bg-slate-50 transition-colors cursor-pointer"
                    data-status="{{ $statusValue }}"
                    onclick="window.location='{{ route('admin.sales.customer-ledger', $invoice->customer_id) }}'">

                    <td class="py-3 px-4 font-medium text-slate-900 search-target">{{ $invoice->patient_name ?? ($invoice->customer->name ?? 'Walk-in Customer') }}</td>
                    <td class="py-3 px-4 font-mono text-slate-500 text-[11px] search-target">{{ $invoice->invoice_no }}</td>
                    <td class="py-3 px-4 font-mono text-slate-600">{{ $invoice->nepali_date ?? $invoice->invoice_date }}</td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-slate-100 text-slate-700 font-medium">
                            {{ $invoice->payment_method ?? 'Cash' }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right font-mono text-slate-500">Rs. {{ number_format($paidAmount, 2) }}</td>
                    <td class="py-3 px-4 text-right font-semibold font-mono text-slate-900">Rs. {{ number_format($grandTotal, 2) }}</td>
                    
                    <td class="py-3 px-4 text-center" onclick="event.stopPropagation();">
                        @if($remainingDue > 0)
                            <button type="button" 
                                onclick="openPaymentModal('{{ $invoice->id }}', '{{ $invoice->invoice_no }}', '{{ $remainingDue }}')" 
                                class="inline-flex items-center px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold rounded shadow-sm transition-colors">
                                <i class="fa-solid fa-hand-holding-dollar mr-1"></i> Pay
                            </button>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-400 text-[11px] font-medium rounded border border-slate-200">
                                <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Cleared
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr id="noRecordsRow">
                    <td colspan="8" class="text-center py-12 text-slate-400">
                        <i class="fa-solid fa-folder-open text-2xl block mb-2 text-slate-200"></i>
                        No transaction logs matched for this criteria.
                    </td>
                </tr>
                @endforelse
                
                <tr id="dynamicNoRecordsRow" class="hidden">
                    <td colspan="8" class="text-center py-12 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-2xl block mb-2 text-slate-200"></i>
                        No results match your selected filters.
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-slate-200/80 sm:px-6 text-xs select-none">
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="btnPrevMobile" class="relative inline-flex items-center px-4 py-1.5 border border-slate-300 rounded-md bg-white font-medium text-slate-700 hover:bg-slate-50 transition-colors">Previous</button>
                <button id="btnNextMobile" class="relative inline-flex items-center px-4 py-1.5 border border-slate-300 rounded-md bg-white font-medium text-slate-700 hover:bg-slate-50 transition-colors">Next</button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-slate-500">
                        Showing <span id="lblRangeStart" class="font-medium text-slate-800">0</span> to <span id="lblRangeEnd" class="font-medium text-slate-800">0</span> of <span id="lblTotalEntries" class="font-medium text-slate-800">0</span> transaction paths
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button id="btnPrevDesktop" class="relative inline-flex items-center px-2 py-1.5 rounded-l-md border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </button>
                        <div id="desktopPageContainer" class="flex -space-x-px"></div>
                        <button id="btnNextDesktop" class="relative inline-flex items-center px-2 py-1.5 rounded-r-md border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="paymentModal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all duration-300">
    <div class="bg-white rounded-xl w-full max-w-md shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        
        <div class="bg-slate-50 px-5 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-emerald-600 text-sm"></i> 
                Collect Due Payment (<span id="modal_invoice_no" class="font-mono text-emerald-700 font-bold"></span>)
            </h3>
            <button type="button" onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold p-1">✕</button>
        </div>
        
        <form id="paymentForm" method="POST" action="">
            @csrf
            <div class="p-5 space-y-4">
                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase">Outstanding Due Amount</label>
                    <div class="relative">
                        <input type="text" id="modal_due_amount" readonly 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm font-mono font-bold text-slate-500 rounded-lg outline-none select-none">
                        <span class="absolute right-3 top-2.5 text-[10px] font-bold text-slate-400">NPR</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-700 uppercase">Received Amount <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rs.</span>
                        <input type="number" step="0.01" name="received_amount" required id="received_amount" min="0.01" placeholder="0.00"
                            class="w-full pl-9 pr-3 py-1.5 border border-slate-200 rounded-lg text-sm font-mono font-bold text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <p class="text-[10px] text-slate-400">Enter cash or transaction amount returned by client.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-700 uppercase">Remarks / Notes</label>
                    <textarea name="remarks" rows="2" placeholder="Write payment notes here if any..."
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none resize-none"></textarea>
                </div>
            </div>
            
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end gap-2.5">
                <button type="button" onclick="closePaymentModal()" 
                    class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-medium rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Update Ledger
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

    // Configuration Engine Parameters
    const rowsPerPage = 10; 
    let currentPage = 1;
    let filteredRows = [...tableRows];

    // Layout Selectors
    const lblRangeStart = document.getElementById('lblRangeStart');
    const lblRangeEnd = document.getElementById('lblRangeEnd');
    const lblTotalEntries = document.getElementById('lblTotalEntries');
    const desktopPageContainer = document.getElementById('desktopPageContainer');

    // Central Filter Coordination Engine (Handles both search & dropdown simultaneously)
    function applyCombinedFilters() {
        const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : "";
        const selectedStatus = statusFilter ? statusFilter.value : "ALL";

        filteredRows = tableRows.filter(row => {
            // Check status condition
            const matchesStatus = (selectedStatus === "ALL") || (row.getAttribute('data-status') === selectedStatus);
            
            // Check text search condition
            let matchesSearch = true;
            if (searchQuery !== "") {
                const targets = row.querySelectorAll('.search-target');
                matchesSearch = Array.from(targets).some(target => 
                    target.textContent.toLowerCase().includes(searchQuery)
                );
            }

            return matchesStatus && matchesSearch;
        });

        currentPage = 1; // Always reset map back to entry index 1
        updatePaginationUI();
    }

    function updatePaginationUI() {
        const total = filteredRows.length;
        const totalPages = Math.ceil(total / rowsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIdx = (currentPage - 1) * rowsPerPage;
        const endIdx = Math.min(startIdx + rowsPerPage, total);

        // Reset visibility structure matrix
        tableRows.forEach(row => row.classList.add('hidden'));
        filteredRows.slice(startIdx, endIdx).forEach(row => row.classList.remove('hidden'));

        // Handle structural message display states
        if (total === 0) {
            if (nativeEmptyRow) nativeEmptyRow.classList.add('hidden');
            dynamicNoRecords.classList.remove('hidden');
            lblRangeStart.textContent = '0';
            lblRangeEnd.textContent = '0';
        } else {
            dynamicNoRecords.classList.add('hidden');
            lblRangeStart.textContent = total > 0 ? startIdx + 1 : 0;
            lblRangeEnd.textContent = endIdx;
        }
        lblTotalEntries.textContent = total;

        // Render Numeric Links Panel
        desktopPageContainer.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const isCurrent = i === currentPage;
            const btn = document.createElement('button');
            btn.className = `relative inline-flex items-center px-3 py-1.5 border text-xs font-medium transition-colors ${
                isCurrent 
                ? 'z-10 bg-slate-800 border-slate-800 text-white font-semibold' 
                : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'
            }`;
            btn.textContent = i;
            btn.addEventListener('click', () => {
                currentPage = i;
                updatePaginationUI();
            });
            desktopPageContainer.appendChild(btn);
        }

        // Toggle state disabled styles
        const isFirst = currentPage === 1;
        const isLast = currentPage === totalPages;
        
        document.getElementById('btnPrevDesktop').disabled = isFirst;
        document.getElementById('btnNextDesktop').disabled = isLast;
        document.getElementById('btnPrevMobile').disabled = isFirst;
        document.getElementById('btnNextMobile').disabled = isLast;
    }

    // Event Triggers
    if (searchInput) {
        searchInput.addEventListener('input', applyCombinedFilters);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', applyCombinedFilters);
    }

    // Pagination Click Controls
    document.getElementById('btnPrevDesktop').addEventListener('click', () => { if(currentPage > 1) { currentPage--; updatePaginationUI(); } });
    document.getElementById('btnNextDesktop').addEventListener('click', () => { if(currentPage * rowsPerPage < filteredRows.length) { currentPage++; updatePaginationUI(); } });
    document.getElementById('btnPrevMobile').addEventListener('click', () => { if(currentPage > 1) { currentPage--; updatePaginationUI(); } });
    document.getElementById('btnNextMobile').addEventListener('click', () => { if(currentPage * rowsPerPage < filteredRows.length) { currentPage++; updatePaginationUI(); } });

    // Initial Bootstrap
    updatePaginationUI();
});

/**
 * Functional Logic to Trigger Dynamic Credit Recovery Terminal Model
 */
function openPaymentModal(id, invoiceNo, dueAmount) {
    document.getElementById('modal_invoice_no').innerText = invoiceNo;
    document.getElementById('modal_due_amount').value = parseFloat(dueAmount).toFixed(2);
    
    const amountInput = document.getElementById('received_amount');
    amountInput.value = ''; 
    amountInput.max = dueAmount; 

    document.getElementById('paymentForm').action = "/admin/sales/" + id + "/update-payment";
    
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

/**
 * Handles Closing Operations for Payment Modal Context
 */
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target == modal) {
        closePaymentModal();
    }
}
</script>
@endsection