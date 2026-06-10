@extends('layouts.admin')

@section('title', 'Manage Customers')
@section('panel_title', 'Customer Ledger Management Matrix')

@section('content')
<div class="space-y-4 max-w-6xl mx-auto">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Active Customers System Index</h2>
            <p class="text-xs text-slate-400 mt-0.5">Overviewing all registered local merchant and party profiles.</p>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded transition-colors shadow-xs flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add New Account
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-4 w-12 text-center">ID</th>
                        <th class="p-4">Customer / Party Information</th>
                        <th class="p-4">PAN Number</th>
                        <th class="p-4">Phone Number</th>
                        <th class="p-4 text-right">Previous Due</th>
                        <th class="p-4 text-center w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-4 text-center font-mono text-xs text-slate-400">#{{ $customer->id }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $customer->name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot text-[10px]"></i> {{ Str::limit($customer->address, 45) }}
                                </div>
                            </td>
                            <td class="p-4">
                                @if($customer->pan_number)
                                    <span class="font-mono text-xs bg-slate-100 px-2 py-0.5 border border-slate-200 rounded text-slate-700 uppercase font-medium">{{ $customer->pan_number }}</span>
                                @else
                                    <span class="text-xs italic text-slate-300">Not Provided</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono text-xs">{{ $customer->phone_number }}</td>
                            <td class="p-4 text-right font-mono font-bold @if($customer->previous_due > 0) text-red-500 @else text-slate-400 @endif">
                                Rs. {{ number_format($customer->previous_due, 2) }}
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="inline-flex items-center justify-center h-7 w-7 rounded bg-slate-50 text-slate-500 border border-slate-200 hover:text-blue-600 hover:border-blue-300 transition-all shadow-2xs" title="Edit Customer Profile">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fa-solid fa-address-book text-3xl text-slate-200"></i>
                                    <p class="text-sm font-medium">No customer profiles discovered in the database matrix.</p>
                                </div>
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection