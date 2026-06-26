@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Account Settings</h2>
            <p class="text-sm text-gray-500">Manage your profile information and account details.</p>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                </div>

                <!-- Email (Read-Only) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <input type="email" value="{{ auth()->user()->email }}" disabled 
                            class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-500 cursor-not-allowed">
                        <span class="absolute right-3 top-2.5 text-xs text-gray-400">
                            <i class="fa-solid fa-lock"></i> Locked
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1 italic">Email address cannot be changed.</p>
                </div>

                <!-- Role (Read-Only) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned Role</label>
                    <input type="text" value="{{ ucfirst(auth()->user()->role) }}" disabled 
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 font-medium cursor-not-allowed">
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t pt-6">
                <a href="{{ route('admin.dashboard') }}" 
                    class="px-5 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancel</a>
                <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection