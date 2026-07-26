<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure portal login for Deurali Chemicals employees.">
    <title>Sign In | Deurali Chemicals</title>
    <link rel="icon" type="image/png" href="https://deuralichemicals.com.np/storage/img/dcl.png">
    <!-- Tailwind CSS -->
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
                <div class="inline-flex items-center justify-center p-3 bg-slate-900/80 rounded-2xl border border-slate-700/50 mb-4 shadow-inner">
                    <img src="https://deuralichemicals.com.np/storage/img/dcl.png" alt="Deurali Chemicals Logo" class="h-10 w-auto object-contain">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Deurali Chemicals</h1>
                <p class="text-xs font-semibold tracking-wider text-blue-400 uppercase mt-1">Enterprise Portal</p>
            </div>

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" x-data="{ showPassword: false }" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold tracking-wider text-slate-300 uppercase mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <!-- Envelope Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                        <input type="email" id="email" name="email" required autocomplete="email" value="{{ old('email') }}"
                            class="w-full pl-11 pr-4 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200"
                            placeholder="Enter your Email address">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold tracking-wider text-slate-300 uppercase">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-blue-400 hover:text-blue-300 font-medium transition">Forgot?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <!-- Lock Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                            class="w-full pl-11 pr-11 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200"
                            placeholder="Enter your Password">
                        
                        <!-- Toggle Password Visibility -->
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200 focus:outline-none">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" 
                        class="w-4 h-4 text-blue-600 bg-slate-900 border-slate-700 rounded focus:ring-blue-500/20 focus:ring-offset-slate-800">
                    <label for="remember" class="ml-2.5 text-xs text-slate-300 cursor-pointer select-none">Remember this device</label>
                </div>

                <!-- Error Flash Messages -->
                @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-xl text-xs font-medium flex items-center space-x-2" role="alert">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 active:scale-[0.99] transition duration-200 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-600/25 text-sm flex items-center justify-center space-x-2">
                    <span>Sign In to Account</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>

            <!-- Security Footer Badge -->
            <div class="mt-8 pt-6 border-t border-slate-700/50 flex items-center justify-center text-slate-400 text-xs space-x-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span>256-bit SSL Encrypted Access</span>
            </div>
        </div>

        <!-- Copyright -->
        <p class="text-center text-xs text-slate-500 mt-6">
            &copy; {{ date('Y') }} Deurali Chemicals Pvt Ltd. All rights reserved.
        </p>
    </div>

</body>
</html>