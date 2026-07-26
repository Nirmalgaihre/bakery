<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verify identity for Deurali Chemicals portal access.">
    <title>Verify Identity | Deurali Chemicals</title>

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
            <div class="mb-8">
                <!-- Website Link Badge -->
                <a href="https://deuralichemicals.com.np" target="_blank" title="Visit Main Website" 
                   class="inline-flex items-center justify-center p-3 bg-slate-900/80 hover:bg-slate-900 rounded-2xl border border-slate-700/50 mb-4 shadow-inner group transition duration-200">
                    <img src="https://deuralichemicals.com.np/storage/img/dcl.png" alt="Deurali Chemicals Logo" class="h-10 w-auto object-contain">
                    <span class="ml-2 text-slate-500 group-hover:text-blue-400 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </span>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-white">Verification Required</h1>
                <p class="text-xs text-slate-400 mt-2 max-w-xs mx-auto">
                    Enter the 6-digit security code sent to your registered email address.
                </p>
            </div>

            <!-- Status Flash Message -->
            @if(session('status'))
            <div class="mb-6 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-medium flex items-center space-x-2 text-left" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            <!-- Error Flash Message -->
            @if($errors->any())
            <div class="mb-6 p-3.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-medium flex items-center space-x-2 text-left" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first('otp') ?? $errors->first() }}</span>
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('otp.verify') }}" method="POST" id="otp-form" class="space-y-6">
                @csrf
                
                <!-- 6-Digit Code Inputs -->
                <div class="flex justify-center gap-2 sm:gap-2.5" id="otp-inputs">
                    @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" inputmode="numeric" pattern="\d*" required
                        class="w-11 h-13 text-center text-xl font-bold bg-slate-900/60 border border-slate-700 rounded-xl text-slate-100 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200"
                        oninput="handleInput(this)" onkeydown="moveToPrev(event, this)" onpaste="handlePaste(event)">
                    @endfor
                </div>

                <input type="hidden" name="otp" id="full-otp">

                <!-- Submit Button -->
                <button type="submit" id="submit-btn"
                    class="w-full bg-blue-600 hover:bg-blue-500 active:scale-[0.99] transition duration-200 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-600/25 text-sm flex items-center justify-center space-x-2">
                    <span>Verify Security Code</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>

            <!-- Loading State (Hidden by default) -->
            <div id="loading-state" class="hidden my-6">
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700/50 flex items-center justify-center space-x-3">
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs font-medium text-slate-300">Verifying code, please wait...</span>
                </div>
            </div>

            <!-- Resend Link & Navigation -->
            <div class="mt-8 pt-6 border-t border-slate-700/50 flex flex-col items-center space-y-3">
                <p class="text-xs text-slate-400">
                    Didn't receive the code?
                    <a href="#" class="text-blue-400 hover:text-blue-300 font-semibold ml-1 transition underline">Resend Code</a>
                </p>
                <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-medium text-slate-400 hover:text-white transition space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
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

    <!-- Script Handling OTP Navigation & Paste -->
    <script>
    const form = document.getElementById('otp-form');
    const btn = document.getElementById('submit-btn');
    const loadingState = document.getElementById('loading-state');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent immediate submit
        
        updateFullOtp();

        // Show loading state
        btn.classList.add('hidden');
        loadingState.classList.remove('hidden');
        
        // Disable inputs
        document.querySelectorAll('#otp-inputs input').forEach(input => {
            input.disabled = true;
        });

        // Wait 4 seconds, then submit
        setTimeout(() => {
            form.submit();
        }, 4000);
    });

    function handleInput(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
        if (input.value.length === 1) {
            let next = input.nextElementSibling;
            if (next) next.focus();
        }
        updateFullOtp();
    }

    function moveToPrev(e, input) {
        if (e.key === "Backspace" && !input.value) {
            let prev = input.previousElementSibling;
            if (prev) prev.focus();
        }
    }

    function handlePaste(e) {
        e.preventDefault();
        const clipboardData = (e.clipboardData || window.clipboardData).getData('text');
        const digits = clipboardData.replace(/[^0-9]/g, '').slice(0, 6).split('');
        const inputs = document.querySelectorAll('#otp-inputs input');

        digits.forEach((digit, index) => {
            if (inputs[index]) {
                inputs[index].value = digit;
            }
        });

        if (digits.length > 0) {
            const nextFocusIndex = Math.min(digits.length, inputs.length - 1);
            inputs[nextFocusIndex].focus();
        }
        
        updateFullOtp();
    }

    function updateFullOtp() {
        let inputs = document.querySelectorAll('#otp-inputs input');
        document.getElementById('full-otp').value = Array.from(inputs).map(i => i.value).join('');
    }
    </script>
</body>
</html>