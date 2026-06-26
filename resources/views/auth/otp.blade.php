<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Verify OTP</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm text-center">
    <h2 class="text-2xl font-bold mb-4 text-blue-900">Enter Verification Code</h2>
    <p class="text-gray-600 mb-6 text-sm">We sent a 6-digit code to your email.</p>
    
    <form action="{{ route('otp.verify') }}" method="POST" id="otp-form">
        @csrf
        <div class="flex justify-between gap-2 mb-6" id="otp-inputs">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" 
                    class="w-12 h-12 text-center text-xl font-bold border rounded-md focus:ring-2 focus:ring-blue-500 outline-none" 
                    oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)">
            @endfor
        </div>
        <input type="hidden" name="otp" id="full-otp">
        
        <button type="submit" class="w-full bg-blue-900 text-white py-2 rounded font-semibold hover:bg-blue-800 transition">
            Verify Code
        </button>
    </form>
</div>

<script>
function moveToNext(input) {
    if (input.value.length === 1) {
        let next = input.nextElementSibling;
        if (next) next.focus();
    }
    updateFullOtp();
}
function moveToPrev(e, input) {
    if (e.key === "Backspace" && input.value.length === 0) {
        let prev = input.previousElementSibling;
        if (prev) prev.focus();
    }
}
function updateFullOtp() {
    let inputs = document.querySelectorAll('#otp-inputs input');
    let otp = "";
    inputs.forEach(i => otp += i.value);
    document.getElementById('full-otp').value = otp;
}
</script>
</body>
</html>