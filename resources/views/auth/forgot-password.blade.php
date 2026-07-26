<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset your Deurali Chemicals portal password.">
    <title>Account Recovery | Deurali Chemicals</title>

    <!-- Link / Favicon Icon -->
    <link rel="icon" type="image/png" href="https://deuralichemicals.com.np/storage/img/dcl.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                <h1 class="text-2xl font-bold tracking-tight text-white">Reset Password</h1>
                <p class="text-xs text-slate-400 mt-2 max-w-xs mx-auto">
                    Enter your email address and we'll send you a password reset link.
                </p>
            </div>

            <!-- Session Status Alert -->
            @if (session('status'))
                <div class="mb-6 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-medium flex items-center space-x-2" role="alert">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Validation Error Alert -->
            @if ($errors->any())
                <div class="mb-6 p-3.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-medium flex items-center space-x-2" role="alert">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold tracking-wider text-slate-300 uppercase mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <!-- Envelope Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                        <input type="email" id="email" name="email" required autocomplete="email" value="{{ old('email') }}"
                            class="w-full pl-11 pr-4 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200"
                            placeholder="name@deuralichemicals.com">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 active:scale-[0.99] transition duration-200 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-600/25 text-sm flex items-center justify-center space-x-2">
                    <span>Send Reset Link</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>

            <!-- Return to Login Link -->
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

</body>
</html>