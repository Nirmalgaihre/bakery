@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-2 px-2 h-screen flex flex-col">
    <div class="flex-grow bg-white border border-slate-200 shadow-sm rounded-lg flex flex-col overflow-hidden">
        
        <!-- Header -->
        <div class="bg-slate-800 p-4 text-white flex justify-between items-center shrink-0">
            <div>
                <h1 class="text-xl font-bold uppercase">Deurali Chemicals Pvt. Ltd.</h1>
                <p class="text-xs text-slate-300">Kuleshwor, Kathmandu - 14</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold">INV #{{ $invoice->invoice_no }}</p>
                <p class="text-[10px]">{{ $invoice->invoice_date }}</p>
            </div>
        </div>

        <!-- Table Area -->
        <div class="flex-grow overflow-auto p-4">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b bg-slate-50">
                        <th class="p-2">Item Name</th>
                        <th class="p-2 text-right">Quantity</th>
                        <th class="p-2 text-right">Unit Price</th>
                        <th class="p-2 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="p-2 font-medium text-slate-700">
                            {{ $item->product->name ?? 'Chemical Item' }}
                        </td>
                        <!-- This is the part that was missing -->
                        <td class="p-2 text-right font-mono text-slate-900 font-bold">
                            {{ number_format($item->quantity ?? 0, 2) }}
                        </td>
                        <td class="p-2 text-right font-mono text-slate-600">
                            Rs {{ number_format($item->price ?? 0, 2) }}
                        </td>
                        <td class="p-2 text-right font-bold text-slate-900">
                            Rs {{ number_format(($item->quantity ?? 0) * ($item->price ?? 0), 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t shrink-0">
            <div class="flex justify-between items-center">
                <div class="text-xs text-slate-500">
                    Subtotal: Rs {{ number_format($calculatedSubtotal ?? 0, 2) }}
                </div>
                <div class="text-lg font-bold text-slate-900">
                    Grand Total: Rs {{ number_format($invoice->grand_total ?? 0, 2) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection