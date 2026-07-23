@extends('layouts.admin')
@section('title', 'Invoice #' . ($invoice->invoice_number ?? $invoice->invoice_no ?? $invoice->id))

@section('content')
@php
    $grandTotal = $invoice->grand_total ?? 0;
    $paidAmount = $invoice->paid_amount ?? 0;
    $dueAmount  = max(0, $grandTotal - $paidAmount);

    if ($dueAmount <= 0 && $grandTotal > 0) {
        $statusLabel = 'PAID';
        $statusBadgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
    } elseif ($paidAmount > 0 && $dueAmount > 0) {
        $statusLabel = 'PARTIAL';
        $statusBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
    } else {
        $statusLabel = 'UNPAID';
        $statusBadgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
    }
@endphp

<!-- CSS Styles specifically for Print Isolation -->
<style>
    @media print {
        /* Hide everything on the page */
        body * {
            visibility: hidden !important;
        }

        /* Show ONLY the invoice section and its children */
        #printable-invoice, #printable-invoice * {
            visibility: visible !important;
        }

        /* Position invoice at top left of paper without extra web padding */
        #printable-invoice {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* Hide the top buttons bar explicitly */
        .no-print {
            display: none !important;
        }

        /* Force signature section to display on print */
        .print-signature {
            display: flex !important;
        }
    }
</style>

<!-- Main Wrapper with Global Arial Font Applied -->
<div class="max-w-5xl mx-auto space-y-6 pb-12" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">

    <!-- Top Navigation Actions (Hidden when printing via .no-print) -->
    <div class="no-print flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" 
               class="group flex items-center justify-center w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Invoice #{{ $invoice->invoice_number ?? $invoice->invoice_no ?? $invoice->id }}</h2>
                <p class="text-xs text-slate-500">View and print invoice details</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Print Button -->
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-900 transition-colors shadow-sm cursor-pointer">
                <i class="fa-solid fa-print"></i> Print Invoice
            </button>
            
            @if(Route::has('admin.invoices.pdf'))
                <!-- PDF Download -->
                <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-download"></i> Download PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Printable Invoice Container -->
    <div id="printable-invoice" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-10">
        
        <!-- Company Header (Deurali Chemicals) -->
        <div class="text-center border-b-2 border-slate-800 pb-6 mb-6">
            <h1 class="text-3xl font-black text-slate-900 tracking-wider uppercase" style="font-family: Arial, sans-serif;">Deurali Chemicals</h1>
            <p class="text-xs font-medium text-slate-600 mt-1">Kathmandu-14, Kalimati Kuleshwor, Nepal</p>
            <p class="text-xs font-bold text-slate-800 mt-1">VAT / PAN No: <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">609932843</span></p>
            
            <div class="mt-4 inline-block px-4 py-1 bg-slate-800 text-white text-xs font-bold uppercase tracking-widest rounded-sm">
                TAX INVOICE
            </div>
        </div>

        <!-- Invoice Details Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-200 pb-6 gap-6 text-xs">
            <div>
                <p class="text-slate-500">Invoice No: <span class="font-bold text-slate-900 text-sm">#{{ $invoice->invoice_number ?? $invoice->invoice_no ?? $invoice->id }}</span></p>
                <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusBadgeClass }}">
                    {{ $statusLabel }}
                </div>
            </div>

            <!-- Date Details -->
            <div class="text-left sm:text-right space-y-1">
                <p class="text-slate-500">Invoice Date (AD): <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</span></p>
                @if(!empty($invoice->nepali_date))
                    <p class="text-slate-500">Invoice Date (BS): <span class="font-semibold text-slate-800">{{ $invoice->nepali_date }}</span></p>
                @endif
                @if(!empty($invoice->fiscal_year))
                    <p class="text-slate-500">Fiscal Year: <span class="font-semibold text-slate-800">FY {{ $invoice->fiscal_year }}</span></p>
                @endif
            </div>
        </div>

        <!-- Customer & Billing Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 my-6 p-5 bg-slate-50 rounded-xl border border-slate-100 text-xs">
            <!-- Customer Section -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Buyer's Details (Billed To)</span>
                <h3 class="text-sm font-bold text-slate-900">{{ $invoice->customer->name ?? $invoice->customer_name ?? 'N/A' }}</h3>
                <p class="text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i> {{ $invoice->customer->phone_number ?? 'N/A' }}
                </p>
                <p class="text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-slate-400 text-[10px]"></i> Buyer's PAN / VAT: <span class="font-semibold">{{ $invoice->customer->pan_number ?? 'N/A' }}</span>
                </p>
                <p class="text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-slate-400 text-[10px]"></i> {{ $invoice->customer->address ?? 'N/A' }}
                </p>
            </div>

            <!-- Payment Summary Quick View -->
            <div class="space-y-2 border-t sm:border-t-0 sm:border-l border-slate-200 pt-4 sm:pt-0 sm:pl-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Invoice Summary</span>
                <div class="flex justify-between text-slate-600">
                    <span>Grand Total:</span>
                    <span class="font-bold text-slate-900">NPR {{ number_format($grandTotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-600">
                    <span>Paid Amount:</span>
                    <span class="font-bold">NPR {{ number_format($paidAmount, 2) }}</span>
                </div>
                @if($dueAmount > 0)
                    <div class="flex justify-between text-rose-600 border-t border-slate-200 pt-1.5">
                        <span class="font-bold">Remaining Due:</span>
                        <span class="font-bold">NPR {{ number_format($dueAmount, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Invoice Items Table -->
        <div class="overflow-x-auto my-6">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-100 border-y border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Item Particulars</th>
                        <th class="py-3 px-4 text-center">Qty</th>
                        <th class="py-3 px-4 text-center">Unit</th>
                        <th class="py-3 px-4 text-right">Rate (NPR)</th>
                        <th class="py-3 px-4 text-right">Amount (NPR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-800">
                    @forelse($invoice->items as $index => $item)
                        <tr>
                            <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-semibold">
                                {{ $item->product_name ?? ($item->product->name ?? 'Item #' . ($index + 1)) }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold">
                                {{ $item->qty }}
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-600 uppercase">
                                {{ $item->unit ?? 'Pcs' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                {{ number_format($item->price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold">
                                {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No line items present in this invoice.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Invoice Totals Calculation Box -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-t border-slate-200 pt-6">
            <!-- Remarks -->
            <div class="text-xs text-slate-500 max-w-md">
                @if(!empty($invoice->remarks ?? $invoice->notes))
                    <p class="font-bold text-slate-700 mb-1">Remarks:</p>
                    <p class="italic">{{ $invoice->remarks ?? $invoice->notes }}</p>
                @endif
            </div>

            <!-- Total Calculations -->
            <div class="w-full sm:w-72 space-y-2 text-xs">
                @if(isset($invoice->sub_total) && $invoice->sub_total > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span>NPR {{ number_format($invoice->sub_total, 2) }}</span>
                    </div>
                @endif

                @if(isset($invoice->discount_amount) && $invoice->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount:</span>
                        <span>- NPR {{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif

                @if(isset($invoice->tax_amount) && $invoice->tax_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Taxable Amount / VAT:</span>
                        <span>+ NPR {{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                @endif

                <div class="flex justify-between text-slate-900 font-bold text-sm border-t border-slate-300 pt-2.5">
                    <span>Grand Total:</span>
                    <span>NPR {{ number_format($grandTotal, 2) }}</span>
                </div>

                <div class="flex justify-between text-emerald-700 font-semibold pt-1">
                    <span>Paid Amount:</span>
                    <span>NPR {{ number_format($paidAmount, 2) }}</span>
                </div>

                @if($dueAmount > 0)
                    <div class="flex justify-between text-rose-700 font-bold border-t border-dashed border-slate-200 pt-2">
                        <span>Balance Due:</span>
                        <span>NPR {{ number_format($dueAmount, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer Signatures (Only visible when printing) -->
        <div class="print-signature hidden justify-between items-end mt-20 pt-8 border-t border-slate-200 text-xs text-slate-500">
            <div class="text-center w-40 border-t border-slate-300 pt-1">
                Customer's Signature
            </div>
            <div class="text-center w-40 border-t border-slate-300 pt-1 font-semibold text-slate-700">
                For Deurali Chemicals
            </div>
        </div>

    </div>
</div>
@endsection