@extends('layouts.admin')

@section('title', 'Modify Customer Data Node')
@section('panel_title', 'Customer Record Update Terminal')

@section('content')
<div class="max-w-4xl mx-auto bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
    
    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-1 h-4 bg-amber-500 inline-block rounded-xs"></span>
                Edit Customer: {{ $customer->name }}
            </h2>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                Update account settings, current standing ledgers, and contact coordinates.
            </p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold uppercase tracking-wider flex items-center gap-1.5 transition-colors">
            <i class="fa-solid fa-arrow-left-long"></i> Back To Index
        </a>
    </div>

    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Customer / Party Name <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="name" required value="{{ old('name', $customer->name) }}" 
                        class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 text-sm text-slate-700 focus:outline-none focus:border-blue-500 rounded transition-colors">
                </div>
                @error('name') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">PAN Card Number</label>
                    <span class="text-[10px] text-slate-400 font-medium lowercase italic">optional field</span>
                </div>
                <div class="relative">
                    <i class="fa-solid fa-address-card absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="pan_number" value="{{ old('pan_number', $customer->pan_number) }}" 
                        class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 text-sm text-slate-700 focus:outline-none focus:border-blue-500 rounded transition-colors uppercase font-mono">
                </div>
                @error('pan_number') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Contact Phone Number <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="fa-solid fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="tel" name="phone_number" required value="{{ old('phone_number', $customer->phone_number) }}" 
                        class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 text-sm text-slate-700 focus:outline-none focus:border-blue-500 rounded transition-colors font-mono">
                </div>
                @error('phone_number') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Previous Due Amount</label>
                </div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold font-mono">Rs.</span>
                    <input type="number" name="previous_due" step="0.01" min="0" value="{{ old('previous_due', $customer->previous_due) }}" 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 text-sm text-slate-700 focus:outline-none focus:border-blue-500 rounded transition-colors font-mono">
                </div>
                @error('previous_due') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Physical Delivery & Billing Address <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="fa-solid fa-map-location-dot absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <textarea name="address" required rows="3" 
                        class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 text-sm text-slate-700 focus:outline-none focus:border-blue-500 rounded transition-colors resize-none">{{ old('address', $customer->address) }}</textarea>
                </div>
                @error('address') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

        </div>

        <hr class="border-slate-100">

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs uppercase tracking-wider rounded transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded transition-all shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Apply Mutations
            </button>
        </div>

    </form>
</div>
@endsection