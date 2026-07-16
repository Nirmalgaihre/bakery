@extends('layouts.admin')

@section('title', 'Supplier Details - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Supplier Profile')

@section('content')
<div class="max-w-4xl w-full mx-auto py-6">
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-bold text-slate-800 uppercase tracking-tight">Supplier Profile</h1>
        <a href="{{ route('admin.suppliers.index') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 uppercase tracking-wide">
            &larr; Back to List
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="font-bold text-slate-700">{{ $supplier->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Contact Person</label>
                    <p class="text-sm font-semibold text-slate-700">{{ $supplier->contact_person ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Phone Number</label>
                    <p class="text-sm font-semibold text-slate-700">{{ $supplier->phone ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email Address</label>
                    <p class="text-sm font-semibold text-slate-700">{{ $supplier->email ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Added On</label>
                    <p class="text-sm font-semibold text-slate-700">{{ $supplier->created_at->format('d M, Y') }}</p>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Business Address</label>
                <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded border border-slate-100">{{ $supplier->address ?? 'No address provided.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection