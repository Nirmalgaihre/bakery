<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Verify OTP | Deurali Chemicals</title>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-sm text-center border border-gray-100">
        <!-- Icon -->
        <div id="icon-container" class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-blue-900 mb-4 transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-3.367l.54-3.061A2.17 2.17 0 005.07 12.8a2.17 2.17 0 00-2.31 1.76l-.54 3.061m12.75-9.37l.54 3.061a2.17 2.17 0 01-2.31 1.76 2.17 2.17 0 01-1.44-1.29l.54-3.061M12 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>

        <h2 id="status-text" class="text-2xl font-bold text-gray-900 mb-2">Verification Code</h2>
        <p class="text-gray-500 text-sm mb-6">Enter the 6-digit code sent to your email address.</p>
        
        <form action="{{ route('otp.verify') }}" method="POST" id="otp-form" class="space-y-6">
            @csrf
            <div class="flex justify-between gap-2" id="otp-inputs">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" inputmode="numeric" pattern="\d*"
                        class="w-10 h-12 text-center text-xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition" 
                        oninput="handleInput(this)" onkeydown="moveToPrev(event, this)">
                @endfor
            </div>
            
            <input type="hidden" name="otp" id="full-otp">
            
            <button type="submit" id="submit-btn" class="w-full bg-blue-900 text-white py-3 rounded-lg font-bold hover:bg-blue-800 transition duration-200 transform hover:scale-[1.01] active:scale-95 shadow-md">
                Verify Code
            </button>
        </form>
    </div>

<script>
    const form = document.getElementById('otp-form');
    const btn = document.getElementById('submit-btn');

    function handleInput(input) {
        // Only allow numbers
        input.value = input.value.replace(/[^0-9]/g, '');
        
        if (input.value.length === 1) {
            let next = input.nextElementSibling;
            if (next) {
                next.focus();
            } else {
                // Last input filled, trigger auto-verify
                triggerVerify();
            }
        }
        updateFullOtp();
    }

    function triggerVerify() {
        // Visual feedback
        btn.innerHTML = "Verifying...";
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');

        // Delay for 1.5 seconds before submitting
        setTimeout(() => {
            form.submit();
        }, 1500);
    }

    function moveToPrev(e, input) {
        if (e.key === "Backspace" && input.value.length === 0) {
            let prev = input.previousElementSibling;
            if (prev) prev.focus();
        }
    }

    function updateFullOtp() {
        let inputs = document.querySelectorAll('#otp-inputs input');
        let otp = Array.from(inputs).map(i => i.value).join('');
        document.getElementById('full-otp').value = otp;
    }
</script>
</body>
</html>