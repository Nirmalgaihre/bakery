@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-7xl font-sans text-slate-700 antialiased">

    <div class="mb-6 no-print">
        <a href="{{ route('admin.customers.monthly-summary', $customer->id) }}?fiscal_year={{ $fiscalYear }}" class="text-xs text-indigo-600 hover:underline">← Back to Monthly Summary</a>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-2">Customer: {{ $customerName }} ({{ $monthName }})</h2>
        <p class="text-xs text-slate-400 mt-0.5">Select an invoice to preview the details.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-6 h-auto md:h-[750px]">

        {{-- Left Pane: Invoice List --}}
        <div class="w-full md:w-1/3 overflow-y-auto space-y-3 pr-2 no-print border-b md:border-b-0 pb-4 md:pb-0">
            @foreach($customerInvoices as $invoice)
            @php $due = $invoice->grand_total - $invoice->paid_amount; @endphp
            <div onclick="showInvoice('{{ $invoice->id }}')" id="invoice-card-{{ $invoice->id }}"
                class="bg-white p-4 rounded-xl border border-slate-200/80 cursor-pointer hover:border-indigo-500 shadow-sm transition-all">
                <div class="flex justify-between items-center">
                    <span class="font-bold font-mono text-xs text-indigo-700">{{ $invoice->invoice_no }}</span>
                    <span class="font-bold text-sm text-slate-900">Rs. {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="mt-2 text-[11px] text-slate-400 flex justify-between">
                    <span>{{ $invoice->invoice_date }}</span>
                    <span class="{{ $due > 0 ? 'text-rose-600 font-bold' : 'text-emerald-600' }}">
                        {{ $due > 0 ? 'Due: Rs. '.number_format($due, 2) : 'Paid' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Right Pane: Preview --}}
        <div class="w-full md:w-2/3 bg-white p-8 md:p-12 rounded-xl border border-slate-200/80 shadow-lg overflow-y-auto print-container" id="invoice-preview">
            <div id="preview-placeholder" class="flex flex-col items-center justify-center h-full py-20 text-slate-400 text-xs no-print">
                <p>Select an invoice to preview.</p>
            </div>

            <div id="preview-document" class="hidden">
                <div class="text-center border-b pb-5">
                    <h1 class="text-xl font-black uppercase text-slate-900">Deurali Chemicals Pvt Ltd.</h1>
                    <p class="text-xs text-slate-500 mt-1">Kathmandu, Nepal | VAT No: 609932843</p>
                </div>
                <div class="grid grid-cols-2 gap-4 text-xs mt-6 mb-6">
                    <div>
                        <p class="text-slate-500">Invoice No: <span id="preview-no" class="font-bold font-mono text-slate-900"></span></p>
                        <p class="text-slate-500">Buyer Name: <span class="font-semibold text-slate-900">{{ $customerName }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-slate-500">Date: <span id="preview-date" class="font-mono text-slate-900"></span></p>
                    </div>
                </div>
                <table class="w-full text-xs text-left border-collapse">
                    <thead><tr class="bg-slate-50 border-y text-slate-600 uppercase"><th class="py-2 px-2">S.N.</th><th class="py-2">Description</th><th class="py-2 text-center">Qty</th><th class="py-2 text-right">Rate</th><th class="py-2 text-right">Amount</th></tr></thead>
                    <tbody id="preview-items" class="divide-y divide-slate-100"></tbody>
                </table>
                <div class="mt-6 border-t pt-4 flex justify-between text-xs">
                    <div class="w-1/2 text-slate-500 italic" id="preview-remarks"></div>
                    <div class="w-64 space-y-1.5 font-mono" id="preview-totals"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure values() is used so the JS array index matches properly
    const invoices = @json($customerInvoices->values());

    function showInvoice(id) {
        const invoice = invoices.find(i => i.id == id);
        if (!invoice) return;

        document.getElementById('preview-placeholder').classList.add('hidden');
        document.getElementById('preview-document').classList.remove('hidden');
        document.getElementById('preview-no').innerText = invoice.invoice_no;
        document.getElementById('preview-date').innerText = invoice.invoice_date;
        document.getElementById('preview-remarks').innerText = invoice.remarks || 'N/A';

        let itemsHtml = '';
        invoice.items.forEach((item, index) => {
            itemsHtml += `<tr>
                <td class="py-2 px-2">${index + 1}</td>
                <td class="py-2">${item.product_name}</td>
                <td class="py-2 text-center">${item.qty}</td>
                <td class="py-2 text-right">${parseFloat(item.price).toFixed(2)}</td>
                <td class="py-2 text-right font-bold">${(item.qty * item.price).toFixed(2)}</td>
            </tr>`;
        });
        document.getElementById('preview-items').innerHTML = itemsHtml;

        let totalsHtml = `<div class="flex justify-between"><span>Sub Total:</span> <span>${parseFloat(invoice.subtotal).toFixed(2)}</span></div>`;
        if (parseFloat(invoice.vat_amount) > 0) totalsHtml += `<div class="flex justify-between"><span>VAT (13%):</span> <span>${parseFloat(invoice.vat_amount).toFixed(2)}</span></div>`;
        totalsHtml += `<div class="flex justify-between font-bold border-t pt-1 mt-1"><span>Grand Total:</span> <span>${parseFloat(invoice.grand_total).toFixed(2)}</span></div>`;
        document.getElementById('preview-totals').innerHTML = totalsHtml;
    }
</script>
@endsection