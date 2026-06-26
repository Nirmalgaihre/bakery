@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-12">
        
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Change your password</h2>
            
            <form action="{{ route('admin.profile.update-password') }}" method="POST">
                @csrf @method('PATCH')

                <div class="mb-6 relative">
                    <label class="block text-sm font-medium text-gray-700">Current Password</label>
                    <div class="relative mt-1">
                        <input type="password" name="current_password" id="current_password" required placeholder="Enter current password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 outline-none pr-10">
                        <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-eye" id="current_password-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4 relative">
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <div class="relative mt-1">
                        <input type="password" name="password" id="password" required placeholder="Enter new password"
                            oninput="validatePassword()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 outline-none pr-10">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-6 relative">
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <div class="relative mt-1">
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Confirm new password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 outline-none pr-10">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-eye" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold py-2.5 rounded-lg transition shadow-md">
                    Change my password
                </button>
            </form>
        </div>

        <div class="md:w-64">
            <h3 class="font-bold text-gray-900 mb-3">Password must contain:</h3>
            <ul class="text-sm text-gray-600 space-y-2">
                <li id="length"><i class="fa-solid fa-circle text-[8px] mr-2"></i> At least 8 characters</li>
                <li id="uppercase"><i class="fa-solid fa-circle text-[8px] mr-2"></i> At least 1 upper case letter (A-Z)</li>
                <li id="number"><i class="fa-solid fa-circle text-[8px] mr-2"></i> At least 1 number (0-9)</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Show/Hide Password Toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Password Requirements Validator
function validatePassword() {
    const val = document.getElementById('password').value;
    document.getElementById('length').style.color = val.length >= 8 ? '#16a34a' : '#4b5563';
    document.getElementById('uppercase').style.color = /[A-Z]/.test(val) ? '#16a34a' : '#4b5563';
    document.getElementById('number').style.color = /\d/.test(val) ? '#16a34a' : '#4b5563';
}
</script>
@endsection