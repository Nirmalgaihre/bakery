@extends('layouts.admin')

@section('title', 'Add New Customer - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Customer Registry Panel')

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

    {{-- Interactive Customer Entry Engine Form --}}
    <form action="{{ route('admin.customers.store') }}" method="POST" class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        @csrf
        
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-blue-600"></i> New Customer Registration Form
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Customer Name --}}
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Full Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter full name..." 
                        class="w-full text-sm p-2 px-3 border rounded outline-none transition-all @error('name') border-red-500 focus:ring-1 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @enderror" required>
                    @error('name') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Phone Number --}}
                <div class="space-y-1">
                    <label for="phone_number" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Phone Number *</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" placeholder="Enter mobile number..." 
                        class="w-full text-sm p-2 px-3 border rounded outline-none transition-all @error('phone_number') border-red-500 focus:ring-1 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @enderror" required>
                    @error('phone_number') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- PAN Number --}}
                <div class="space-y-1">
                    <label for="pan_number" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">PAN Number</label>
                    <input type="text" name="pan_number" id="pan_number" value="{{ old('pan_number') }}" placeholder="Enter PAN number..." 
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>

                {{-- Previous Due --}}
                <div class="space-y-1">
                    <label for="previous_due" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Opening Balance (Due Amount)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rs.</span>
                        <input type="number" name="previous_due" id="previous_due" value="{{ old('previous_due', 0) }}" step="0.01" min="0" placeholder="0.00" 
                            class="w-full text-sm p-2 pl-9 pr-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="space-y-1">
                <label for="address" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Address *</label>
                <textarea name="address" id="address" rows="3" placeholder="Enter customer address..." 
                    class="w-full text-sm p-2 px-3 border rounded outline-none transition-all @error('address') border-red-500 focus:ring-1 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @enderror" required>{{ old('address') }}</textarea>
                @error('address') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Footer --}}
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.customers.index') }}" class="bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 font-semibold text-xs px-4 py-2 rounded transition-colors uppercase tracking-wide">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2 rounded shadow-xs transition-colors uppercase tracking-wide flex items-center gap-1.5">
                <i class="fa-solid fa-user-check"></i> Register Customer
            </button>
        </div>
    </form>
</div>
@endsection