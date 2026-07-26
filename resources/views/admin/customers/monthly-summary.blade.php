@extends('layouts.admin')

@section('title', 'Monthly Summary - ' . $customer->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;" x-data="{ activeTab: 'months' }">
    
    <!-- Top Action Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index', ['fiscal_year' => $fiscalYear]) }}" 
               class="group flex items-center justify-center w-9 h-9 rounded-full bg-slate-50 border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Customer Financial & Product Ledger</h2>
                <p class="text-xs text-slate-500">{{ $customer->name }} • Fiscal Year {{ $fiscalYear }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- View Mode Switcher -->
            <div class="bg-slate-100 p-1 rounded-lg flex items-center gap-1 border border-slate-200">
                <button type="button" 
                    @click="activeTab = 'months'" 
                    :class="activeTab === 'months' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                    class="px-3 py-1.5 text-xs rounded-md transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-days text-xs"></i> Monthly Ledger
                </button>
                <button type="button" 
                    @click="activeTab = 'products'" 
                    :class="activeTab === 'products' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                    class="px-3 py-1.5 text-xs rounded-md transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-box-open text-xs"></i> Purchased Products & Prices
                </button>
            </div>

            <a href="{{ route('admin.customers.index', ['fiscal_year' => $fiscalYear]) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
               <i class="fa-solid fa-book text-[10px]"></i> Back to Ledger
            </a>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <!-- Opening Balance Card -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Opening Balance</span>
                <p class="text-base font-bold font-mono text-slate-800 mt-1">NPR {{ number_format($openingBalance, 2) }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                <i class="fa-solid fa-wallet text-xs"></i>
            </div>
        </div>

        <!-- Customer Name Card -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Customer Name</span>
                <p class="text-sm font-bold text-slate-900 mt-1 truncate max-w-[140px]" title="{{ $customer->name }}">{{ $customer->name }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                <i class="fa-solid fa-user text-xs"></i>
            </div>
        </div>

        <!-- Active Fiscal Year Card -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Active Fiscal Year</span>
                <p class="text-base font-bold font-mono text-slate-800 mt-1">FY {{ $fiscalYear }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                <i class="fa-solid fa-calendar-days text-xs"></i>
            </div>
        </div>

        <!-- Contact Phone Card -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Contact Number</span>
                <p class="text-sm font-bold font-mono text-slate-800 mt-1">{{ $customer->phone_number ?? 'N/A' }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                <i class="fa-solid fa-phone text-xs"></i>
            </div>
        </div>
    </div>

    {{-- =========================================================
         TAB 1: MONTHLY BREAKDOWN TABLE
    ========================================================== --}}
    <div x-show="activeTab === 'months'" x-cloak class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nepali Calendar Monthly Summary</span>
            <span class="text-[11px] text-slate-400"><i class="fa-solid fa-mouse-pointer text-[10px] mr-1"></i> Click any row to view month invoices</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-3.5 text-left pl-5">Month</th>
                        <th class="p-3.5 text-right">Net Transactions (NPR)</th>
                        <th class="p-3.5 text-right pr-5">Running Balance (NPR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    
                    {{-- Opening Balance Row --}}
                    <tr class="bg-slate-50/70">
                        <td class="p-3.5 pl-5 font-bold text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-arrow-right-to-bracket text-slate-400 text-[10px]"></i>
                            Opening Balance
                        </td>
                        <td class="p-3.5 text-right font-mono text-slate-400">-</td>
                        <td class="p-3.5 pr-5 text-right font-mono font-bold text-slate-900">
                            NPR {{ number_format($openingBalance, 2) }}
                        </td>
                    </tr>

                    @php 
                        $runningBalance = $openingBalance; 
                        $totalTransactions = 0;
                    @endphp

                    @foreach($nepaliMonths as $monthNum => $monthName)
                        @php 
                            $monthlyTotal = $monthlyData->get($monthNum, 0);
                            $runningBalance += $monthlyTotal;
                            $totalTransactions += $monthlyTotal;
                        @endphp
                        
                        <tr class="hover:bg-blue-50/60 cursor-pointer transition-colors group" 
                            onclick="window.location.href='{{ route('admin.customers.month-invoices', [$customer->id, $monthNum]) }}?fiscal_year={{ $fiscalYear }}'">
                            
                            <td class="p-3.5 pl-5 font-medium text-slate-700 group-hover:text-blue-600 transition-colors flex items-center justify-between pr-4">
                                <span>{{ $monthName }}</span>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-blue-500 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-1"></i>
                            </td>

                            <td class="p-3.5 text-right font-mono font-semibold {{ $monthlyTotal > 0 ? 'text-slate-800' : ($monthlyTotal < 0 ? 'text-rose-600' : 'text-slate-400') }}">
                                {{ number_format($monthlyTotal, 2) }}
                            </td>

                            <td class="p-3.5 pr-5 text-right font-mono font-bold text-slate-900">
                                {{ number_format($runningBalance, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                
                {{-- Grand Total Footer --}}
                <tfoot class="bg-slate-100/80 border-t-2 border-slate-300">
                    <tr>
                        <td class="p-4 pl-5 font-bold text-slate-900 uppercase text-[11px] tracking-wider">GRAND TOTAL</td>
                        <td class="p-4 text-right font-mono font-extrabold text-slate-900 text-sm">
                            NPR {{ number_format($totalTransactions, 2) }}
                        </td>
                        <td class="p-4 pr-5 text-right font-mono font-extrabold text-slate-900 text-sm">
                            NPR {{ number_format($runningBalance, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- =========================================================
         TAB 2: PURCHASED PRODUCTS & PRICES BREAKDOWN TABLE
         (Safely pulls from `invoices` and `invoice_items`)
    ========================================================== --}}
    <div x-show="activeTab === 'products'" x-cloak class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Purchased Products & Price History</span>
                <p class="text-[11px] text-slate-400 mt-0.5">Itemized purchase records from customer invoices</p>
            </div>
            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100">FY {{ $fiscalYear }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-3.5 text-left pl-5">SN</th>
                        <th class="p-3.5 text-left">Invoice Date</th>
                        <th class="p-3.5 text-center">Invoice #</th>
                        <th class="p-3.5 text-left">Product Name</th>
                        <th class="p-3.5 text-right">Quantity</th>
                        <th class="p-3.5 text-right">Unit Price (NPR)</th>
                        <th class="p-3.5 text-right pr-5">Total Price (NPR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @php
                        $productItems = collect();
                        $grandTotalSum = 0;
                        $grandQtySum = 0;

                        // Safely extract items from $customer->invoices database relation
                        if (isset($customer->invoices)) {
                            foreach ($customer->invoices as $inv) {
                                $invNo = $inv->invoice_no ?? $inv->invoice_number ?? ('INV-' . $inv->id);
                                $invDate = $inv->invoice_date ?? $inv->nepali_date ?? ($inv->created_at ? $inv->created_at->format('Y-m-d') : '-');
                                
                                // Get items array/relation on invoice
                                $items = $inv->items ?? $inv->invoiceItems ?? [];
                                foreach ($items as $item) {
                                    $qty = $item->qty ?? $item->quantity ?? 1;
                                    $unit = $item->unit ?? 'pcs';
                                    $price = $item->price ?? $item->unit_price ?? 0;
                                    $total = $item->total ?? $item->total_amount ?? ($qty * $price);
                                    
                                    $grandTotalSum += $total;
                                    $grandQtySum += $qty;

                                    $productItems->push([
                                        'date' => $invDate,
                                        'invoice_no' => $invNo,
                                        'product_name' => $item->product_name ?? 'Item',
                                        'qty' => $qty,
                                        'unit' => $unit,
                                        'price' => $price,
                                        'total' => $total,
                                    ]);
                                }
                            }
                        }
                    @endphp

                    @forelse($productItems as $idx => $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-3.5 pl-5 font-mono text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3.5 font-mono text-slate-600">{{ $row['date'] }}</td>
                            <td class="p-3.5 text-center font-mono">
                                <span class="bg-blue-50 text-blue-700 px-2.5 py-0.5 rounded font-semibold text-[11px] border border-blue-100">
                                    #{{ $row['invoice_no'] }}
                                </span>
                            </td>
                            <td class="p-3.5 font-bold text-slate-800">
                                <i class="fa-solid fa-box text-slate-400 text-xs mr-1"></i>
                                {{ $row['product_name'] }}
                            </td>
                            <td class="p-3.5 text-right font-mono font-semibold text-slate-700">
                                {{ number_format($row['qty']) }} {{ $row['unit'] }}
                            </td>
                            <td class="p-3.5 text-right font-mono text-slate-600">
                                {{ number_format($row['price'], 2) }}
                            </td>
                            <td class="p-3.5 pr-5 text-right font-mono font-bold text-slate-900">
                                {{ number_format($row['total'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-2xl mb-2 text-slate-300 block"></i>
                                No invoice items found for this customer. Click any month in the <strong>Monthly Ledger</strong> tab to see month details.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($productItems->count() > 0)
                <tfoot class="bg-slate-100/80 border-t-2 border-slate-300">
                    <tr>
                        <td colspan="4" class="p-4 pl-5 font-bold text-slate-900 uppercase text-[11px] tracking-wider">TOTAL PURCHASES</td>
                        <td class="p-4 text-right font-mono font-extrabold text-slate-900 text-sm">
                            {{ number_format($grandQtySum) }} units
                        </td>
                        <td class="p-4 text-right font-mono text-slate-400">-</td>
                        <td class="p-4 pr-5 text-right font-mono font-extrabold text-slate-900 text-sm">
                            NPR {{ number_format($grandTotalSum, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection