@extends('layouts.admin')

@section('title', 'Manage Suppliers - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Supplier Directory')

@section('content')
<div class="max-w-6xl w-full mx-auto py-6">
    
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-bold text-slate-800 uppercase tracking-tight">Suppliers Directory</h1>
            <p class="text-xs text-slate-500 font-medium">Manage and monitor your chemical supply partners.</p>
        </div>
        
        @role('admin')
        <a href="{{ route('admin.suppliers.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2 rounded shadow-xs transition-colors uppercase tracking-wide flex items-center gap-1.5">
            <i class="fa-solid fa-plus"></i> Add New Supplier
        </a>
        @endrole
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-5 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded shadow-xs text-sm">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Search & Controls --}}
    <div class="mb-4 flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.suppliers.index') }}" method="GET" class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by name, contact, phone, or email..."
                class="w-full text-sm p-2.5 pl-9 border border-slate-200 rounded shadow-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Supplier Name</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Contact Person</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Phone</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 text-sm font-semibold text-slate-800">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $supplier->name }}
                                </a>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $supplier->contact_person ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $supplier->phone ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $supplier->email ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-right space-x-3">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="text-slate-400 hover:text-slate-600 transition-colors" title="View"><i class="fa-solid fa-eye"></i></a>
                                @role('admin')
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Delete this supplier? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endrole
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">
                                <i class="fa-solid fa-box-open block text-3xl mb-2 text-slate-200"></i>
                                No supplier records found in the database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination footer --}}
        @if($suppliers->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection