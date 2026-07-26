<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Password reset link confirmation for Deurali Chemicals portal.">
    <title>Email Sent | Deurali Chemicals</title>

    <!-- Link / Favicon Icon -->
    <link rel="icon" type="image/png" href="https://deuralichemicals.com.np/storage/img/dcl.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center justify-center p-4 font-sans antialiased text-slate-100">

    <!-- Main Container -->
    <div class="w-full max-w-md">
        
        <!-- Glassmorphism Card -->
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 shadow-2xl rounded-2xl p-8 sm:p-10 text-center">
            
            <!-- Header & Branding -->
            <div class="mb-6">
                <!-- Website Link Badge -->
                <a href="https://deuralichemicals.com.np" target="_blank" title="Visit Main Website" 
                   class="inline-flex items-center justify-center p-3 bg-slate-900/80 hover:bg-slate-900 rounded-2xl border border-slate-700/50 mb-4 shadow-inner group transition duration-200">
                    <img src="https://deuralichemicals.com.np/storage/img/dcl.png" alt="Deurali Chemicals Logo" class="h-10 w-auto object-contain">
                    <span class="ml-2 text-slate-500 group-hover:text-blue-400 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </span>
                </a>
            </div>

            <!-- Glowing Icon Badge -->
            <div class="relative w-16 h-16 mx-auto mb-6 flex items-center justify-center rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 shadow-lg shadow-emerald-500/10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <!-- Checkmark Overlay Dot -->
                <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 text-slate-900 ring-2 ring-slate-800">
                    <svg class="w-2.5 h-2.5 stroke-slate-900 stroke-[3]" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-white mb-2">Reset Link Sent!</h1>
            
            <p class="text-xs text-slate-300 leading-relaxed mb-8 max-w-xs mx-auto">
                We have dispatched a password recovery link to your email address. Please check your inbox (and spam folder) to reset your credentials.
            </p>

            <!-- Actions -->
            <div class="space-y-4">
                <a href="{{ route('login') }}" 
                   class="w-full bg-blue-600 hover:bg-blue-500 active:scale-[0.99] transition duration-200 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-600/25 text-sm flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Return to Sign In</span>
                </a>
            </div>

            <!-- Helpful Notice -->
            <div class="mt-8 pt-6 border-t border-slate-700/50 text-center">
                <p class="text-xs text-slate-400">
                    Didn't receive an email? 
                    <a href="{{ route('password.request') }}" class="text-blue-400 hover:text-blue-300 font-semibold ml-1 transition underline">Try resending</a>
                </p>
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