@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-7xl font-sans text-slate-700 antialiased">
    <div class="mb-6 no-print">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tax Invoices Receipts Ledger</h2>
        <p class="text-xs text-slate-400 mt-0.5">Select an entry from the registry pane below to preview or print the official statement.</p>
    </div>

    {{-- Split View Container --}}
    <div class="flex flex-col md:flex-row gap-6 h-auto md:h-[750px]">
        
        {{-- Left Pane: List of Invoices --}}
        <div class="w-full md:w-1/3 overflow-y-auto space-y-3 pr-2 no-print border-b md:border-b-0 pb-4 md:pb-0">
            @foreach($customerInvoices as $invoice)
            <div onclick="showInvoice('{{ $invoice->id }}')" 
                 id="invoice-card-{{ $invoice->id }}"
                 class="invoice-card bg-white p-4 rounded-xl border border-slate-200/80 cursor-pointer hover:border-indigo-500 hover:shadow-md transition-all duration-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <span class="font-bold font-mono text-xs text-indigo-700">{{ $invoice->invoice_no }}</span>
                    <span class="font-bold text-sm text-slate-900">NPR {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between items-center mt-2 text-[11px] text-slate-400">
                    <p class="truncate max-w-[180px] font-medium text-slate-500">{{ $invoice->patient_name ?? $customerName }}</p>
                    <p class="font-mono">{{ $invoice->nepali_date ?? $invoice->invoice_date }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Right Pane: Detailed Tax Invoice Preview --}}
        <div class="w-full md:w-2/3 bg-white p-8 md:p-12 rounded-xl border border-slate-200/80 shadow-lg overflow-y-auto print-container" id="invoice-preview">
            
            {{-- Default Placeholder State --}}
            <div id="preview-placeholder" class="flex flex-col items-center justify-center h-full py-20 text-slate-400 text-xs">
                <i class="fa-solid fa-receipt text-4xl mb-3 text-slate-200"></i>
                <p>Select an invoice from the left registry panel to see details.</p>
            </div>

            {{-- Real Interactive Document Structure (Hidden until selected) --}}
            <div id="preview-document" class="hidden">
                <div class="text-center border-b border-slate-300 pb-5">
                    <h1 class="text-xl font-black uppercase text-slate-900 tracking-wide">Deurali Chemicals Pvt Ltd.</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kathmandu, Nepal</p>
                    <p class="text-xs font-semibold text-slate-700 font-mono mt-1">VAT Registration No: 609932843</p>
                    <div class="inline-block border-2 border-black px-5 py-1 mt-3 font-bold text-xs tracking-wider text-slate-900 bg-slate-50">TAX INVOICE (कर बिजक)</div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs mt-6 mb-6">
                    <div class="space-y-1">
                        <p class="text-slate-500">Invoice Number: <span id="preview-no" class="font-bold font-mono text-slate-900 text-sm"></span></p>
                        <p class="text-slate-500">Buyer Name: <span class="font-semibold text-slate-900">{{ $customerName }}</span></p>
                        <p class="text-slate-500">Patient Reference: <span id="preview-patient" class="font-medium text-slate-800"></span></p>
                    </div>
                    <div class="text-right space-y-1">
                        <p class="text-slate-500">Date (Miti): <span id="preview-date" class="font-mono text-slate-900 font-medium"></span></p>
                        <p class="text-slate-500">Payment Method: <span id="preview-method" class="font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-800 text-[10px]"></span></p>
                    </div>
                </div>

                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-t border-b border-slate-300 font-bold uppercase tracking-wider text-[10px] text-slate-600">
                            <th class="py-2.5 px-2 w-12 text-center">S.N.</th>
                            <th class="py-2.5 px-2">Description of Goods</th>
                            <th class="py-2.5 px-2 text-center w-24">Qty</th>
                            <th class="py-2.5 px-2 text-right w-24">Rate (Rs)</th>
                            <th class="py-2.5 px-2 text-right w-28">Amount (Rs)</th>
                        </tr>
                    </thead>
                    <tbody id="preview-items" class="divide-y divide-slate-100 text-slate-800">
                        {{-- Items injected dynamically via script --}}
                    </tbody>
                </table>

                {{-- Summary Calculation Engine Breakdown Footer Grid --}}
                <div class="mt-6 border-t border-slate-200 pt-4 flex justify-between text-xs">
                    <div class="w-1/2 pr-4">
                        <p class="text-[11px] text-slate-400 font-medium uppercase tracking-wider">Remarks / Notes:</p>
                        <p id="preview-remarks" class="text-slate-600 italic mt-1 text-[11px] leading-relaxed">-</p>
                    </div>
                    <div class="w-64 space-y-1.5 text-slate-600 font-mono" id="preview-totals">
                        {{-- Populated by script context --}}
                    </div>
                </div>

                {{-- Action Panel Footer Trigger --}}
                <div class="mt-10 pt-4 border-t border-slate-100 flex justify-end gap-3 no-print">
                    <button onclick="window.print()" class="bg-slate-800 text-white px-5 py-2 rounded-md font-bold text-xs hover:bg-slate-900 shadow-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-print"></i> Print Official Invoice
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .no-print, .no-print * { display: none !important; }
    .print-container, .print-container * { visibility: visible; }
    .print-container { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; padding: 0 !important; margin: 0 !important; }
}
</style>

<script>
const invoices = @json($customerInvoices);

function showInvoice(id) {
    const invoice = invoices.find(i => i.id == id);
    if (!invoice) return;

    // 1. Highlight active selection card
    document.querySelectorAll('.invoice-card').forEach(card => {
        card.classList.remove('border-indigo-600', 'bg-indigo-50/30', 'ring-1', 'ring-indigo-600');
    });
    const activeCard = document.getElementById(`invoice-card-${id}`);
    if(activeCard) {
        activeCard.classList.add('border-indigo-600', 'bg-indigo-50/30', 'ring-1', 'ring-indigo-600');
    }

    // 2. Toggle main preview layout container elements
    document.getElementById('preview-placeholder').classList.add('hidden');
    document.getElementById('preview-document').classList.remove('hidden');

    // 3. Set standard text elements mapping your actual model columns
    document.getElementById('preview-no').innerText = invoice.invoice_no || invoice.invoice_number;
    document.getElementById('preview-date').innerText = invoice.nepali_date ? invoice.nepali_date : invoice.invoice_date;
    document.getElementById('preview-patient').innerText = invoice.patient_name || 'Walk-in Customer';
    document.getElementById('preview-method').innerText = invoice.payment_method || 'Cash / Credit';
    document.getElementById('preview-remarks').innerText = invoice.remarks ? invoice.remarks : 'N/A';
    
    // 4. Map lines dynamically utilizing explicit columns from InvoiceController store function
    let itemsHtml = '';
    const invoiceItems = invoice.items || invoice.invoice_items || [];
    
    if(invoiceItems.length === 0) {
        itemsHtml = `<tr><td colspan="5" class="py-4 text-center text-slate-400 italic">No line items matched. Make sure ->with('items') is used in your controller index/ledger loader.</td></tr>`;
    } else {
        invoiceItems.forEach((item, index) => {
            // Check if product object exists from nested eager load, fallback to explicit naming records
            const productName = item.product ? item.product.name : (item.product_name || 'Product ID: ' + item.product_id);
            
            // Map keys explicitly used in your controller store loop method
            const quantity = parseFloat(item.qty || 0);
            const rate = parseFloat(item.price || 0);
            const total = quantity * rate;
            const unitMarker = item.unit || 'Unit';

            itemsHtml += `
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="py-2.5 px-2 text-center font-mono text-slate-400">${index + 1}</td>
                <td class="py-2.5 px-2 font-medium text-slate-800">${productName}</td>
                <td class="py-2.5 px-2 text-center font-mono">${quantity} <span class="text-[10px] text-slate-400">${unitMarker}</span></td>
                <td class="py-2.5 px-2 text-right font-mono">Rs. ${rate.toFixed(2)}</td>
                <td class="py-2.5 px-2 text-right font-mono font-semibold text-slate-900">Rs. ${total.toFixed(2)}</td>
            </tr>`;
        });
    }
    document.getElementById('preview-items').innerHTML = itemsHtml;

    // 5. Build financial calculations overview summary module
    const subtotal = parseFloat(invoice.subtotal || invoice.grand_total || 0);
    const discount = parseFloat(invoice.discount || 0);
    const taxableAmount = parseFloat(invoice.taxable_amount || (subtotal - discount));
    const vatAmount = parseFloat(invoice.vat_amount || 0);
    const grandTotal = parseFloat(invoice.grand_total || 0);

    document.getElementById('preview-totals').innerHTML = `
        <div class="flex justify-between text-slate-550">
            <span>Sub Total:</span> 
            <span>Rs. ${subtotal.toFixed(2)}</span>
        </div>
        <div class="flex justify-between text-rose-600">
            <span>Discount (-) :</span> 
            <span>Rs. ${discount.toFixed(2)}</span>
        </div>
        <div class="flex justify-between text-slate-550 border-t border-dashed pt-1">
            <span>Taxable Amt:</span> 
            <span>Rs. ${taxableAmount.toFixed(2)}</span>
        </div>
        <div class="flex justify-between text-slate-550">
            <span>VAT (13%):</span> 
            <span>Rs. ${vatAmount.toFixed(2)}</span>
        </div>
        <div class="flex justify-between font-bold text-slate-900 text-sm border-t-2 border-slate-800 pt-2 mt-2">
            <span>Grand Total:</span> 
            <span>Rs. ${grandTotal.toFixed(2)}</span>
        </div>
    `;
}
</script>
@endsection