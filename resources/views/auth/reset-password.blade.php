<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Set New Password | Deurali Chemicals</title>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 font-sans text-slate-900">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-sm border border-slate-200 p-10">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-900">Set new password</h2>
            <p class="text-slate-500 text-sm mt-2">Password must be at least 8 characters long.</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" id="reset-form" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request()->email }}">

            <!-- Password -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">New Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none transition-all">
                <p id="length-error" class="text-xs text-red-500 mt-2 hidden">Password must be at least 8 characters.</p>
            </div>

            <!-- Confirm -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm Password</label>
                <input type="password" id="confirm_password" name="password_confirmation" required autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none transition-all">
                <p id="match-error" class="text-xs text-red-500 mt-2 hidden">Passwords do not match.</p>
            </div>

            <button type="submit" id="submit-btn" disabled
                class="w-full bg-slate-900 text-white font-bold py-3.5 rounded-xl opacity-50 cursor-not-allowed transition-all">
                Update Password
            </button>
        </form>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        const btn = document.getElementById('submit-btn');
        const lenErr = document.getElementById('length-error');
        const matchErr = document.getElementById('match-error');

        function validate() {
            const isLongEnough = password.value.length >= 8;
            const matches = password.value === confirm.value && password.value !== "";

            lenErr.classList.toggle('hidden', isLongEnough || password.value.length === 0);
            matchErr.classList.toggle('hidden', matches || confirm.value === "");

            if (isLongEnough && matches) {
                btn.disabled = false;
                btn.classList.replace('opacity-50', 'opacity-100');
                btn.classList.replace('cursor-not-allowed', 'cursor-pointer');
                btn.classList.add('hover:bg-slate-800', 'shadow-lg');
            } else {
                btn.disabled = true;
                btn.classList.replace('opacity-100', 'opacity-50');
                btn.classList.replace('cursor-pointer', 'cursor-not-allowed');
            }
        }

        password.addEventListener('input', validate);
        confirm.addEventListener('input', validate);
    </script>
</body>
</html>