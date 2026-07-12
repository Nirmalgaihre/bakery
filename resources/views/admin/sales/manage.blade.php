@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6">
    
    {{-- Top Telemetry Status Dashboard & Header Actions --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Sales Management</h1>
            <p class="text-xs text-slate-500 mt-1">Review active ledger transactions, track operational billing, and modify records.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            {{-- Context Summary Pill for User Experience --}}
            <div class="bg-slate-100 text-slate-700 font-medium text-xs px-3 py-2 rounded-lg border border-slate-200/60 flex items-center gap-2">
                <i class="fa-solid fa-calculator text-slate-400"></i>
                <span>Showing <strong class="font-bold text-slate-900">{{ $sales->count() }}</strong> entries this page</span>
            </div>
            
            <a href="#" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg shadow-xs transition-all uppercase tracking-wide focus:ring-2 focus:ring-blue-500/20 outline-none">
                <i class="fa-solid fa-plus text-sm"></i> Create New Sale
            </a>
        </div>
    </div>
    
    {{-- Main Ledger Table Wrapper --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 tracking-wider uppercase select-none">
                        <th class="px-5 py-4 text-center font-semibold w-16">S.N.</th>
                        <th class="px-5 py-4 text-left font-semibold">Transaction Date</th>
                        <th class="px-5 py-4 text-left font-semibold">Customer Account</th>
                        <th class="px-5 py-4 text-right font-semibold">Grand Total</th>
                        <th class="px-5 py-4 text-center font-semibold w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($sales as $index => $sale)
                    <tr class="hover:bg-slate-50/60 group transition-all">
                        {{-- Paginated Serial Number (Calculates context across pages dynamically) --}}
                        <td class="px-5 py-4 text-center whitespace-nowrap font-mono text-xs font-medium text-slate-400 group-hover:text-slate-600">
                            {{ ($sales->currentPage() - 1) * $sales->perPage() + $index + 1 }}
                        </td>

                        {{-- Date Column --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-regular fa-calendar text-slate-400 group-hover:text-slate-500 transition-colors"></i>
                                <span class="font-medium text-slate-900">{{ $sale->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-slate-400 hidden sm:inline">({{ $sale->created_at->format('H:i') }})</span>
                            </div>
                        </td>
                        
                        {{-- Customer Identity Column --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($sale->customer)
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-full bg-slate-100 group-hover:bg-blue-50 group-hover:text-blue-600 flex items-center justify-center text-[10px] font-bold text-slate-600 uppercase transition-all tracking-wider border border-transparent group-hover:border-blue-100">
                                        {{ substr($sale->customer->name, 0, 2) }}
                                    </div>
                                    <span class="font-semibold text-slate-800 tracking-tight group-hover:text-slate-900">{{ $sale->customer->name }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[11px] font-medium bg-amber-50 text-amber-800 rounded border border-amber-200/50 tracking-wide select-none">
                                    <i class="fa-solid fa-user-tag text-[9px] text-amber-600"></i> Walk-in Customer
                                </span>
                            @endif
                        </td>
                        
                        {{-- Currency Formatted Pricing Column --}}
                        <td class="px-5 py-4 whitespace-nowrap text-right font-mono font-bold text-slate-900">
                            <span class="text-xs font-sans text-slate-400 font-normal mr-0.5 select-none">Rs.</span>{{ number_format($sale->grand_total, 2) }}
                        </td>
                        
                        {{-- Actions Engine Interface Column --}}
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.invoices.edit', $sale->id) }}" 
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50/40 transition-all shadow-xs focus:ring-2 focus:ring-blue-500/20 outline-none"
                                   title="Modify Data Record">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                
                                <form action="{{ route('admin.invoices.destroy', $sale->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently void this transaction record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50/40 transition-all shadow-xs focus:ring-2 focus:ring-rose-500/20 outline-none"
                                            title="Void / Delete Transaction">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- Interactive Fallback Panel if No Sales Match Dataset --}}
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center bg-slate-50/30">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                    <i class="fa-solid fa-receipt text-xl"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-slate-800">No active transaction records</p>
                                    <p class="text-xs text-slate-400 max-w-xs mx-auto">Sales invoices logs and walking point-of-sale customer data strings display here once populated.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Responsive Tail Pagination Footers --}}
        @if($sales->hasPages())
            <div class="p-4 bg-slate-50/70 border-t border-slate-200">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection