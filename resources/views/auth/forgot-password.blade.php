<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery | Deurali Chemicals</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 font-sans text-slate-900">

    <div class="w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-xl font-bold text-slate-900">Reset Password</h1>
                <p class="text-sm text-slate-500 mt-2">Enter your email to receive a recovery link.</p>
            </div>

            <!-- Status Message -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-lg bg-emerald-50 text-emerald-800 text-sm border border-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <input type="email" 
                           name="email" 
                           required 
                           autocomplete="email" 
                           placeholder="Email address" 
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none transition text-sm">
                </div>

                <button type="submit" 
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-2.5 rounded-lg transition active:scale-[0.98] text-sm">
                    Send Link
                </button>
            </form>

            <!-- Back Link -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-slate-900 transition">
                    Return to login
                </a>
            </div>
        </div>
    </div>
</body>
</html>