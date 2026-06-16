@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6 px-4 max-w-3xl font-sans antialiased text-slate-600">
    
    <div class="mb-4">
        <a href="{{ route('admin.staff.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Staff Directory
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Register New Staff Operator</h1>
        <p class="text-xs text-slate-400 mt-0.5">Create a secure system portal access profile for management or administrative teams.</p>
    </div>

    <div class="bg-white border border-slate-200/60 rounded-lg shadow-sm overflow-hidden p-6">
        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">Full Name <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user text-xs"></i>
                    </span>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Enter employee full name" 
                           class="w-full pl-9 pr-4 py-2 bg-white border @error('name') border-rose-400 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror rounded-md text-xs shadow-sm focus:outline-none focus:ring-1 transition-colors">
                </div>
                @error('name')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">Email Address <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="username@domain.com" 
                           class="w-full pl-9 pr-4 py-2 bg-white border @error('email') border-rose-400 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror rounded-md text-xs shadow-sm focus:outline-none focus:ring-1 transition-colors">
                </div>
                @error('email')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">Account Password <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                               placeholder="••••••••" 
                               class="w-full pl-9 pr-4 py-2 bg-white border @error('password') border-rose-400 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror rounded-md text-xs shadow-sm focus:outline-none focus:ring-1 transition-colors">
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">Confirm Password <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                        </span>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="••••••••" 
                               class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-md text-xs shadow-sm focus:outline-none focus:ring-1 transition-colors">
                    </div>
                </div>
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">Assign Baseline Role <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user-shield text-xs"></i>
                    </span>
                    <select id="role" name="role" required
                            class="w-full pl-9 pr-4 py-2 bg-white border @error('role') border-rose-400 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror rounded-md text-xs shadow-sm focus:outline-none focus:ring-1 transition-colors appearance-none">
                        <option value="">Select a specific group access rule...</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="accountant" {{ old('role') == 'accountant' ? 'selected' : '' }}>Accountant Hub</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </span>
                </div>
                @error('role')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.staff.index') }}" 
                   class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition-colors shadow-xs">
                    Cancel Process
                </a>
                <button type="submit" 
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition-colors shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-cloud-arrow-up text-[11px]"></i> Save System Operator
                </button>
            </div>
        </form>
    </div>
</div>
@endsection