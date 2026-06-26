@extends('layouts.admin')

@section('title', 'Customers Ledger')
@section('panel_title', 'Customer Workspace Matrix')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Customer Registry Table</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Regular accounts, cafes, wholesale dealers, and delivery channels</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                            <th class="p-3">Customer Name</th>
                            <th class="p-3">Phone</th>
                            <th class="p-3">PAN/VAT</th>
                            <th class="p-3">Opening Bal</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($customers as $item)
                            <tr class="customer-row cursor-pointer hover:bg-blue-50/40 transition-all border-l-4 border-l-transparent" data-id="{{ $item->id }}">
                                <td class="p-3 font-medium text-slate-800">{{ $item->name }}</td>
                                <td class="p-3 font-mono text-slate-500">
                                    <i class="fa-solid fa-phone text-[10px] text-slate-300 mr-1"></i>{{ $item->phone_number }}
                                </td>
                                <td class="p-3 font-mono text-slate-400">{{ $item->pan_number ?? 'N/A' }}</td>
                                <td class="p-3 font-mono text-slate-500">NPR {{ number_format($item->previous_due ?? 0, 2) }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-green-50 text-green-600 uppercase tracking-wider">Active</span>
                                </td>
                                <td class="p-3 text-right" onclick="event.stopPropagation();">
                                    <a href="{{ route('admin.customers.edit', $item->id) }}" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-bold rounded transition-colors uppercase">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 text-xs">No customer profile nodes found in the database directory.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-md shadow-sm p-6 sticky top-6" id="dynamic-details-panel">
        <div class="text-center py-16 text-slate-400">
            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3 text-slate-300 border border-slate-100">
                <i class="fa-solid fa-address-book text-lg"></i>
            </div>
            <p class="text-xs font-medium text-slate-700">No Customer Selected</p>
            <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Select a customer record row from the table list directory to view live financial spendings and balance history timelines.</p>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.customer-row');
    const displayPanel = document.getElementById('dynamic-details-panel');

    rows.forEach(row => {
        row.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            rows.forEach(r => r.classList.remove('bg-blue-50/60', 'border-l-blue-600'));
            this.classList.add('bg-blue-50/60', 'border-l-blue-600');

            displayPanel.innerHTML = `
                <div class="animate-pulse space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-slate-100 rounded-full"></div>
                        <div class="space-y-2 flex-1">
                            <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                            <div class="h-2.5 bg-slate-100 rounded w-1/3"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="h-14 bg-slate-50 rounded border border-slate-100/50"></div>
                        <div class="h-14 bg-slate-50 rounded border border-slate-100/50"></div>
                    </div>
                    <div class="space-y-2 pt-2">
                        <div class="h-3 bg-slate-100 rounded"></div>
                        <div class="h-3 bg-slate-100 rounded w-5/6"></div>
                    </div>
                </div>`;

            fetch(`/admin/customers/${id}`)
                .then(response => {
                    if (!response.ok) throw new Error('Database response data lookup error');
                    return response.text();
                })
                .then(htmlPartial => {
                    displayPanel.innerHTML = htmlPartial;
                })
                .catch(error => {
                    console.error('AJAX Failure Error Trace:', error);
                    displayPanel.innerHTML = `
                        <div class="text-center py-10 text-red-500 text-xs">
                            <i class="fa-solid fa-triangle-exclamation text-2xl mb-2"></i>
                            <p class="font-bold uppercase tracking-wider">Lookup Failed</p>
                            <p class="text-slate-400 mt-1">Could not load accounting ledger data summaries.</p>
                        </div>`;
                });
        });
    });
});
</script>
@endsection