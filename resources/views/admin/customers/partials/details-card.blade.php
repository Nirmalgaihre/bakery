<div class="space-y-6">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
            <i class="fa-solid fa-user text-xl"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-800">{{ $customer->name }}</h3>
            <p class="text-xs text-slate-400 font-mono">PAN: {{ $customer->pan_number ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="p-4 border border-slate-100 rounded bg-slate-50/50">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Inward Orders</span>
            <span class="text-sm font-bold text-slate-700 mt-1 block">{{ $totalInwardOrders }} Invoices</span>
        </div>
        <div class="p-4 border border-slate-100 rounded bg-slate-50/50">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Spendings</span>
            <span class="text-sm font-bold text-blue-600 mt-1 block">NPR {{ number_format($totalSpendings, 2) }}</span>
        </div>
    </div>

    <div class="space-y-2 pt-2">
        <div class="flex justify-between items-center text-xs">
            <span class="text-slate-400">Opening Balance:</span>
            <span class="font-mono font-bold text-slate-700">NPR 0.00</span>
        </div>
        <div class="flex justify-between items-center text-xs">
            <span class="text-slate-400">Outstanding / Unpaid Sales Dues:</span>
            <span class="font-mono font-bold text-red-500">NPR {{ number_format($outstandingDues, 2) }}</span>
        </div>
    </div>

    <div class="space-y-3 pt-4 border-t border-slate-100">
        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Customer Sales Ledger Timeline</h4>
        
        @forelse($customer->invoices as $invoice)
            <div class="p-3 border border-slate-100 rounded flex items-center justify-between text-xs hover:bg-slate-50 transition-colors">
                <div>
                    <span class="font-mono font-bold text-blue-600 block">#{{ $invoice->invoice_number }}</span>
                    <span class="text-[10px] text-slate-400 font-mono">Date (BS): {{ $invoice->nepali_date }}</span>
                </div>
                <div class="text-right">
                    <span class="font-mono font-bold text-slate-700 block">NPR {{ number_format($invoice->grand_total, 2) }}</span>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $invoice->status === 'Paid' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                        {{ $invoice->status }}
                    </span>
                </div>
            </div>
        @empty
            <div class="p-6 border border-dashed border-slate-200 rounded text-center">
                <p class="text-xs text-slate-400">Any future POS invoice assigned to this company will automatically map here.</p>
            </div>
        @endforelse
    </div>
</div>