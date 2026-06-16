@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6 text-slate-800">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Inventory Adjustments Ledger</h2>
            <p class="text-xs text-slate-500">Historical records of spoils, damage wastes, and returns.</p>
        </div>
        <a href="{{ route('admin.wastage.create') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Adjustment Entry
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700">
                    <th class="p-3 w-[18%]">Transaction Date (BS)</th>
                    <th class="p-3 w-[22%]">Product</th>
                    <th class="p-3 w-[15%]">Type</th>
                    <th class="p-3 w-[15%]">Quantity Adjusted</th>
                    <th class="p-3 w-[12%]">Rate (Rs.)</th>
                    <th class="p-3 w-[18%]">Reference Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($adjustments as $log)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-3 text-slate-700 font-medium">
                            <div class="font-mono text-blue-700">
                                {{ \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from($log->created_at->format('Y-m-d'))->toNepaliDate(format: 'Y-m-d') }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                {{ $log->created_at->format('h:i A') }}
                            </div>
                        </td>
                        <td class="p-3 font-semibold text-slate-700">{{ $log->product->name ?? 'N/A' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide
                                {{ $log->type === 'returned_defective' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                {{ str_replace('_', ' ', $log->type) }}
                            </span>
                        </td>
                        <td class="p-3 font-mono font-bold text-slate-700">
                            {{ number_format($log->quantity, 3) }} KG
                        </td>
                        <td class="p-3 font-mono text-slate-600">
                            Rs. {{ number_format($log->unit_cost, 2) }}
                        </td>
                        <td class="p-3 text-slate-500 max-w-xs truncate">
                            {{ $log->reference_note ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">
                            <i class="fa-solid fa-folder-open text-2xl mb-2 block"></i> No adjustment records logged yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($adjustments->hasPages())
            <div class="p-3 bg-slate-50 border-t border-slate-200">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection