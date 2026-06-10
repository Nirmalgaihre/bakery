<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Token Verification (Offline Sandbox)</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8 border border-slate-200">
        <div class="text-center mb-4">
            <span class="text-xs font-bold tracking-widest text-amber-600 bg-amber-50 px-2 py-1 rounded uppercase inline-block mb-2">Local Sandbox Mode</span>
            <h2 class="text-2xl font-black text-slate-900">Identity Check</h2>
            <p class="text-sm text-slate-500 mt-1">Provide your 6-digit administrative bypass key to authorize your connection.</p>
        </div>

        @if(session('dev_otp_bypass'))
            <div class="mb-6 p-4 bg-teal-50 border border-teal-200 rounded-lg text-center shadow-inner">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-800 block mb-1">Generated Offline Verification Key:</span>
                <span class="text-3xl font-mono font-black tracking-widest text-teal-700 select-all">{{ session('dev_otp_bypass') }}</span>
                <p class="text-[10px] text-teal-600 mt-1">Copy this code and input it below to complete your authentication flow.</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-rose-50 text-sm text-rose-700 rounded-lg border border-rose-200">
                <ul class="list-disc pl-5 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf
            <div class="mb-6">
                <label for="otp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 text-center">6-Digit Security Token</label>
                <input id="otp" type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus placeholder="000000"
                    class="w-full text-center text-2xl tracking-[1em] font-mono px-3 py-3 bg-slate-50 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-600 focus:border-teal-600 transition">
            </div>

            <div>
                <button type="submit" class="w-full bg-teal-700 hover:bg-teal-800 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-150 ease-in-out">
                    Confirm Identity & Login
                </button>
            </div>
        </form>
    </div>
</body>
</html>