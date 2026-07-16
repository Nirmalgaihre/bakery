<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity | Deurali Chemicals</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 font-sans text-slate-900">

    <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-bold text-slate-900">Verification Required</h1>
            <p class="text-sm text-slate-500 mt-2">Enter the 6-digit code sent to your email.</p>
        </div>

        <!-- Success Message -->
        @if(session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700">{{ session('status') }}</p>
        </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-700">{{ $errors->first('otp') }}</p>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('otp.verify') }}" method="POST" id="otp-form" class="space-y-6">
            @csrf
            <div class="flex justify-center gap-2" id="otp-inputs">
                @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="\d*"
                    class="w-10 h-12 text-center text-lg font-bold border border-slate-300 rounded-lg focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none transition"
                    oninput="handleInput(this)" onkeydown="moveToPrev(event, this)">
                @endfor
            </div>

            <input type="hidden" name="otp" id="full-otp">

            <button type="submit" id="submit-btn"
                class="w-full bg-slate-900 text-white font-medium py-2.5 rounded-lg hover:bg-slate-800 transition active:scale-[0.98] text-sm shadow-sm">
                Verify Code
            </button>
        </form>

        <!-- Loading State (Hidden by default) -->
        <div id="loading-state" class="hidden mt-6">
            <div class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-slate-600">Verifying, please wait...</span>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-slate-400">
                Didn't receive the code?
                <a href="#" class="text-slate-900 font-semibold hover:underline">Resend</a>
            </p>
        </div>
    </div>

    <script>
    const form = document.getElementById('otp-form');
    const btn = document.getElementById('submit-btn');
    const loadingState = document.getElementById('loading-state');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent immediate submit
        
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

    function updateFullOtp() {
        let inputs = document.querySelectorAll('#otp-inputs input');
        document.getElementById('full-otp').value = Array.from(inputs).map(i => i.value).join('');
    }
    </script>
</body>
</html>