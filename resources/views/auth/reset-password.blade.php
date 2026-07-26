<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Set new account password for Deurali Chemicals portal access.">
    <title>Set New Password | Deurali Chemicals</title>

    <!-- Link / Favicon Icon -->
    <link rel="icon" type="image/png" href="https://deuralichemicals.com.np/storage/img/dcl.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- AlpineJS for interactive features like show/hide password -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center justify-center p-4 font-sans antialiased text-slate-100">

    <!-- Main Container -->
    <div class="w-full max-w-md">
        
        <!-- Glassmorphism Card -->
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 shadow-2xl rounded-2xl p-8 sm:p-10">
            
            <!-- Header & Branding -->
            <div class="text-center mb-8">
                <!-- Website Link Badge -->
                <a href="https://deuralichemicals.com.np" target="_blank" title="Visit Main Website" 
                   class="inline-flex items-center justify-center p-3 bg-slate-900/80 hover:bg-slate-900 rounded-2xl border border-slate-700/50 mb-4 shadow-inner group transition duration-200">
                    <img src="https://deuralichemicals.com.np/storage/img/dcl.png" alt="Deurali Chemicals Logo" class="h-10 w-auto object-contain">
                    <span class="ml-2 text-slate-500 group-hover:text-blue-400 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </span>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-white">Set New Password</h1>
                <p class="text-xs text-slate-400 mt-2 max-w-xs mx-auto">
                    Create a strong, secure password to protect your account.
                </p>
            </div>

            <!-- Server-Side Error Flash -->
            @if ($errors->any())
            <div class="mb-6 p-3.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-medium flex items-center space-x-2" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.update') }}" method="POST" id="reset-form" x-data="{ showPass: false, showConfirm: false }" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request()->email }}">

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold tracking-wider text-slate-300 uppercase mb-2">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <!-- Lock Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" id="password" name="password" required autocomplete="new-password"
                            class="w-full pl-11 pr-11 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200"
                            placeholder="••••••••">
                        
                        <!-- Visibility Toggle Button -->
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200 focus:outline-none">
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="confirm_password" class="block text-xs font-semibold tracking-wider text-slate-300 uppercase mb-2">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <!-- Shield Check Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <input :type="showConfirm ? 'text' : 'password'" id="confirm_password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full pl-11 pr-11 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200"
                            placeholder="••••••••">
                        
                        <!-- Visibility Toggle Button -->
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200 focus:outline-none">
                            <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Live Password Validation Badges -->
                <div class="space-y-1.5 pt-1 text-xs">
                    <div id="len-badge" class="flex items-center space-x-2 text-slate-400 transition-colors">
                        <svg id="len-icon" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>At least 8 characters long</span>
                    </div>
                    <div id="match-badge" class="flex items-center space-x-2 text-slate-400 transition-colors">
                        <svg id="match-icon" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Passwords match</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-btn" disabled
                    class="w-full bg-blue-600 hover:bg-blue-500 transition duration-200 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-600/25 text-sm flex items-center justify-center space-x-2 opacity-50 cursor-not-allowed">
                    <span>Update Password</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>

            <!-- Navigation Link -->
            <div class="mt-8 pt-6 border-t border-slate-700/50 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-medium text-slate-400 hover:text-white transition space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back to login</span>
                </a>
            </div>
        </div>

        <!-- Copyright & Site Link -->
        <p class="text-center text-xs text-slate-500 mt-6 flex items-center justify-center space-x-1">
            <span>&copy; {{ date('Y') }} Deurali Chemicals Pvt Ltd.</span>
            <a href="https://deuralichemicals.com.np" target="_blank" class="hover:text-slate-300 underline inline-flex items-center">
                <span>Main Site</span>
                <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </p>
    </div>

    <!-- Client-side Validation Logic -->
    <script>
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        const btn = document.getElementById('submit-btn');
        const lenBadge = document.getElementById('len-badge');
        const matchBadge = document.getElementById('match-badge');

        function validate() {
            const val = password.value;
            const confirmVal = confirm.value;

            const isLongEnough = val.length >= 8;
            const matches = val === confirmVal && confirmVal !== "";

            // Length Badge Updates
            if (val.length === 0) {
                lenBadge.className = 'flex items-center space-x-2 text-slate-400 transition-colors';
            } else if (isLongEnough) {
                lenBadge.className = 'flex items-center space-x-2 text-emerald-400 transition-colors';
            } else {
                lenBadge.className = 'flex items-center space-x-2 text-red-400 transition-colors';
            }

            // Match Badge Updates
            if (confirmVal.length === 0) {
                matchBadge.className = 'flex items-center space-x-2 text-slate-400 transition-colors';
            } else if (matches) {
                matchBadge.className = 'flex items-center space-x-2 text-emerald-400 transition-colors';
            } else {
                matchBadge.className = 'flex items-center space-x-2 text-red-400 transition-colors';
            }

            // Submit Button Enabled State
            if (isLongEnough && matches) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.classList.add('active:scale-[0.99]');
            } else {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.classList.remove('active:scale-[0.99]');
            }
        }

        password.addEventListener('input', validate);
        confirm.addEventListener('input', validate);
    </script>
</body>
</html>