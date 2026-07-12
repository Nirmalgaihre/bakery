@extends('layouts.admin')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-6 bg-slate-50/50 min-h-screen font-sans text-slate-700 antialiased">

    <!-- Header Block -->
    <div class="mb-6 no-print flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight drop-shadow-sm flex items-center gap-3">
                <i class="fa-solid fa-address-book text-indigo-600"></i> Statement: {{ $customerName }}
            </h2>
            <p class="text-xs font-medium text-slate-400 mt-1">Select an active invoice transaction from the list to preview live bill document detail.</p>
        </div>
    </div>

    <!-- Master-Detail Canvas Layout -->
    <div class="flex flex-col md:flex-row gap-6 h-auto md:h-[750px]">

        {{-- Left Pane: Invoice List (Tactile 3D Cards) --}}
        <div class="w-full md:w-1/3 overflow-y-auto space-y-4 pr-2 no-print border-b md:border-b-0 pb-4 md:pb-0 select-none custom-scrollbar">
            @foreach($customerInvoices as $invoice)
            @php $due = $invoice->grand_total - $invoice->paid_amount; @endphp
            
            <div onclick="showInvoice('{{ $invoice->id }}')" id="invoice-card-{{ $invoice->id }}"
                class="invoice-card bg-gradient-to-br from-white to-slate-50 p-4 rounded-xl border border-slate-200 shadow-[0_4px_10px_rgba(0,0,0,0.03),inset_0_1px_0_#fff] cursor-pointer hover:border-indigo-400 hover:shadow-[0_8px_16px_rgba(79,70,229,0.08)] active:scale-[0.99] transition-all duration-200 group">
                
                <div class="flex justify-between items-center">
                    <span class="font-bold font-mono text-xs text-indigo-600 group-hover:text-indigo-700 bg-indigo-50 px-2 py-1 rounded-md transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-file-invoice text-[10px]"></i> {{ $invoice->invoice_no }}
                    </span>
                    <span class="font-black text-sm text-slate-800 tracking-tight">Rs. {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                
                <div class="mt-4 text-[11px] font-medium text-slate-400 flex justify-between items-center">
                    <span class="flex items-center gap-1"><i class="fa-solid fa-calendar text-[9px]"></i> {{ $invoice->invoice_date }}</span>
                    @if($due > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                            Due: Rs. {{ number_format($due, 2) }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <i class="fa-solid fa-circle-check mr-1 text-[9px]"></i> Paid
                        </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Right Pane: Premium Paper-Look Invoice Document Preview --}}
        <div class="w-full md:w-2/3 bg-white p-8 md:p-12 rounded-2xl border border-slate-200/80 shadow-[0_15px_35px_-5px_rgba(0,0,0,0.05),0_10px_20px_-8px_rgba(0,0,0,0.03)] overflow-y-auto print-container flex flex-col justify-between"
            id="invoice-preview">

            <!-- Empty Placeholder Canvas -->
            <div id="preview-placeholder"
                class="flex flex-col items-center justify-center h-full py-24 text-slate-400 text-xs no-print text-center space-y-3">
                <div class="w-14 h-14 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center shadow-sm text-slate-300">
                    <i class="fa-solid fa-receipt text-xl"></i>
                </div>
                <p class="font-medium">Select an invoice from the side panel to view official transaction details.</p>
            </div>

            <!-- Main Document Structure -->
            <div id="preview-document" class="hidden space-y-8">
                
                <!-- Invoice Document Header Corporate Style -->
                <div class="text-center border-b border-slate-100 pb-6 relative">
                    <h1 class="text-2xl font-black uppercase text-slate-900 tracking-tight">Deurali Chemicals Pvt Ltd.</h1>
                    <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">Kathmandu, Nepal &bull; VAT No: 609932843</p>
                    <div class="inline-block bg-slate-900 text-white px-6 py-1.5 mt-4 font-black text-xs uppercase tracking-widest rounded-md shadow-sm">
                        Tax Invoice
                    </div>
                </div>

                <!-- Metadata Split Columns -->
                <div class="grid grid-cols-2 gap-6 text-xs bg-slate-50 p-4 border border-slate-200/50 rounded-xl shadow-[inset_0_1px_2px_rgba(0,0,0,0.02)]">
                    <div class="space-y-1.5">
                        <p class="text-slate-400 font-medium">Invoice Number: <span id="preview-no" class="font-bold font-mono text-slate-800"></span></p>
                        <p class="text-slate-400 font-medium">Buyer / Customer Name: <span class="font-bold text-slate-900">{{ $customerName }}</span></p>
                    </div>
                    <div class="text-right space-y-1.5">
                        <p class="text-slate-400 font-medium">Transaction Date: <span id="preview-date" class="font-bold font-mono text-slate-800"></span></p>
                    </div>
                </div>

                <!-- Products/Ledger Items Grid Table -->
                <div class="border border-slate-200/60 rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-b from-slate-50 to-slate-100 text-slate-600 uppercase font-bold border-b border-slate-200">
                                <th class="py-3 px-4 w-12 text-center">S.N.</th>
                                <th class="py-3 px-2">Item Description</th>
                                <th class="py-3 px-2 text-center w-20">Qty</th>
                                <th class="py-3 px-4 text-right w-28">Rate</th>
                                <th class="py-3 px-4 text-right w-32">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="preview-items" class="divide-y divide-slate-100 font-medium text-slate-700">
                            <!-- Injected Dynamically by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Totals & Footnotes Panel Summary -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2" id="preview-totals-container">
                    <div class="text-xs text-slate-400/90 font-medium italic bg-slate-50/50 border border-slate-200/40 rounded-xl p-3" id="preview-remarks">
                        <!-- Remarks Content -->
                    </div>
                    <div class="space-y-2 font-mono text-xs text-right border border-slate-200/60 bg-slate-50/80 p-4 rounded-xl shadow-sm" id="preview-totals">
                        <!-- Mathematical summary dynamic insertion -->
                    </div>
                </div>

                <!-- Functional Operations Footbar actions -->
                <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end no-print">
                    <button onclick="window.print()"
                        class="inline-flex items-center justify-center bg-gradient-to-b from-slate-800 to-slate-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-[0_4px_10px_rgba(0,0,0,0.15),inset_0_1px_0_rgba(255,255,255,0.15)] hover:from-slate-900 hover:to-black transition-all active:scale-[0.98] gap-2">
                        <i class="fa-solid fa-print text-sm"></i> Print Official Document
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styled slim layout scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

@media print {
    body * {
        visibility: hidden;
    }
    .print-container,
    .print-container * {
        visibility: visible;
    }
    .no-print,
    .no-print * {
        display: none !important;
    }
    .print-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<script>
const invoices = @json($customerInvoices);

function showInvoice(id) {
    const invoice = invoices.find(i => i.id == id);
    if (!invoice) return;

    // Reset visual dynamic selection style states
    document.querySelectorAll('.invoice-card').forEach(c => {
        c.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-500/10', 'bg-indigo-50/20');
    });
    
    // Highlight the active dashboard layout row selection card
    const activeCard = document.getElementById(`invoice-card-${id}`);
    if (activeCard) {
        activeCard.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-500/10', 'bg-indigo-50/20');
    }

    document.getElementById('preview-placeholder').classList.add('hidden');
    document.getElementById('preview-document').classList.remove('hidden');

    document.getElementById('preview-no').innerText = invoice.invoice_no;
    document.getElementById('preview-date').innerText = invoice.invoice_date;
    document.getElementById('preview-remarks').innerHTML = `<span class="block font-bold not-italic text-slate-500 text-[10px] uppercase tracking-wider mb-1">Invoice Notes / Remarks</span>${invoice.remarks || 'No remarks provided for this transaction ledger.'}`;

    let itemsHtml = '';
    invoice.items.forEach((item, index) => {
        const total = item.qty * item.price;
        itemsHtml += `<tr class="hover:bg-slate-50/50 transition-colors">
            <td class="py-3 px-4 text-center font-mono text-slate-400 font-bold">${index + 1}</td>
            <td class="py-3 px-2 font-bold text-slate-800">${item.product_name}</td>
            <td class="py-3 px-2 text-center font-bold font-mono">${item.qty}</td>
            <td class="py-3 px-4 text-right font-mono text-slate-600">Rs. ${parseFloat(item.price).toFixed(2)}</td>
            <td class="py-3 px-4 text-right font-bold font-mono text-slate-800">Rs. ${total.toFixed(2)}</td>
        </tr>`;
    });
    document.getElementById('preview-items').innerHTML = itemsHtml;

    // Financial Calculation Block Redesign using layout grid properties
    let totalsHtml = `
        <div class="flex justify-between items-center text-slate-500 font-medium">
            <span>Sub Total:</span> 
            <span class="text-slate-800 font-bold">Rs. ${parseFloat(invoice.subtotal).toFixed(2)}</span>
        </div>`;
        
    if (parseFloat(invoice.vat_amount) > 0) {
        totalsHtml += `
            <div class="flex justify-between items-center text-slate-500 font-medium">
                <span>VAT (13%):</span> 
                <span class="text-slate-800 font-bold">Rs. ${parseFloat(invoice.vat_amount).toFixed(2)}</span>
            </div>`;
    }
    
    totalsHtml += `
        <div class="flex justify-between items-center font-black text-sm border-t border-slate-200/80 pt-2 mt-2 text-slate-900">
            <span>Grand Total:</span> 
            <span>Rs. ${parseFloat(invoice.grand_total).toFixed(2)}</span>
        </div>
        <div class="flex justify-between items-center text-[11px] font-bold text-emerald-600 mt-2 bg-emerald-50/50 px-2 py-1 rounded-md">
            <span>Paid Amount:</span> 
            <span>Rs. ${parseFloat(invoice.paid_amount || 0).toFixed(2)}</span>
        </div>`;

    const due = parseFloat(invoice.grand_total) - parseFloat(invoice.paid_amount || 0);
    
    if (due > 0) {
        totalsHtml += `
            <div class="flex justify-between items-center text-xs font-black text-rose-700 bg-gradient-to-b from-rose-50 to-rose-100 border border-rose-200/60 rounded-lg px-2.5 py-1.5 mt-2 shadow-[inset_0_1px_0_#fff]">
                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Balance Due:</span> 
                <span>Rs. ${due.toFixed(2)}</span>
            </div>`;
    } else {
        totalsHtml += `
            <div class="flex justify-between items-center text-xs font-black text-emerald-700 bg-gradient-to-b from-emerald-50 to-emerald-100 border border-emerald-200/60 rounded-lg px-2.5 py-1.5 mt-2 shadow-[inset_0_1px_0_#fff]">
                <span>Status:</span> 
                <span class="uppercase tracking-wider"><i class="fa-solid fa-circle-check mr-1"></i> Fully Settled</span>
            </div>`;
    }

    document.getElementById('preview-totals').innerHTML = totalsHtml;
}
</script>
@endsection