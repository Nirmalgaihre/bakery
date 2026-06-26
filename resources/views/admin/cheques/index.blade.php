@extends('layouts.admin')

@section('title', 'Cheque Ledger Matrix')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden mt-4">
        
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 flex justify-between items-center">
            <div class="text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-blue-600"></i> Financial Maturity Matrix
            </div>
            
            <form action="{{ route('admin.cheques.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search ledger..." 
                    class="px-3 py-1.5 border border-slate-200 rounded text-xs focus:outline-none focus:border-blue-500 w-48">
                <button type="submit" class="bg-slate-800 text-white px-3 py-1.5 rounded text-xs font-bold uppercase hover:bg-black">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-700">
                <thead>
                    <tr class="bg-slate-100/70 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="p-4 px-5">Reference</th>
                        <th class="p-4">Party & Bank</th>
                        <th class="p-4 text-right">Amount</th>
                        <th class="p-4 text-center">Maturity Date</th>
                        <th class="p-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cheques as $cheque)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 px-5 font-semibold text-slate-900 font-mono">#{{ $cheque->cheque_no }}</td>
                            <td class="p-4">
                                <div class="text-xs font-bold text-slate-800">{{ $cheque->party_name }}</div>
                                <div class="text-[10px] text-slate-400 uppercase">{{ $cheque->bank_name }}</div>
                            </td>
                            <td class="p-4 text-right font-mono text-xs font-bold text-slate-900">Rs. {{ number_format($cheque->amount, 2) }}</td>
                            <td class="p-4 text-center font-mono text-xs text-slate-600">
                                {{ \Carbon\Carbon::parse($cheque->maturity_date_ad)->format('d M, Y') }}
                            </td>
                            
                            <td class="p-4 text-center">
                                @if($cheque->email_sent_at)
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase px-2 py-0.5 rounded">Sent</span>
                                @elseif(\Carbon\Carbon::now()->isSameDay($cheque->maturity_date_ad))
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase px-2 py-0.5 rounded animate-pulse">Due Today</span>
                                @else
                                    <span class="bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-bold uppercase px-2 py-0.5 rounded">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($cheques->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $cheques->links() }}
            </div>
        @endif
    </div>
</div>
@endsection