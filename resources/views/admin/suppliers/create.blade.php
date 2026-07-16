@extends('layouts.admin')

@section('title', 'Add New Supplier - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Supplier Management Panel')

@section('content')
<div class="max-w-4xl w-full mx-auto">
    
    {{-- Form Validation Failure Warning Window --}}
    @if($errors->any())
        <div class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded shadow-xs text-sm">
            <div class="font-semibold mb-1 flex items-center gap-2 text-rose-700">
                <i class="fa-solid fa-triangle-exclamation"></i> Form Validation Failed:
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Interactive Supplier Entry Engine Form --}}
    <form action="{{ route('admin.suppliers.store') }}" method="POST" class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        @csrf
        
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-truck-field text-blue-600"></i> New Supplier Registration Form
            </div>
            <a href="{{ route('admin.suppliers.index') }}" class="text-slate-500 hover:text-blue-600 transition-colors">
                &larr; Back to List
            </a>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Supplier Name --}}
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Supplier Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Deurali Chemicals Pvt. Ltd." 
                        class="w-full text-sm p-2 px-3 border rounded outline-none transition-all @error('name') border-red-500 focus:ring-1 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @enderror" required>
                    @error('name') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Contact Person --}}
                <div class="space-y-1">
                    <label for="contact_person" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Contact Person</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" placeholder="e.g. Ram Sharma" 
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Phone --}}
                <div class="space-y-1">
                    <label for="phone" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="e.g. 98XXXXXXXX" 
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>

                {{-- Email --}}
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="e.g. contact@supplier.com" 
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
            </div>

            {{-- Address --}}
            <div class="space-y-1">
                <label for="address" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Business Address</label>
                <textarea name="address" id="address" rows="3" placeholder="Street, City, District"
                    class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">{{ old('address') }}</textarea>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button type="reset" class="bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 font-semibold text-xs px-4 py-2 rounded transition-colors uppercase tracking-wide">Clear</button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2 rounded shadow-xs transition-colors uppercase tracking-wide flex items-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i> Save Supplier Record
            </button>
        </div>
    </form>
</div>
@endsection