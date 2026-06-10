<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deurali Chemicals - Inventory Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8 border border-slate-200">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <span class="text-xs font-bold tracking-widest text-teal-600 uppercase block mb-1">Enterprise Portal</span>
            <h2 class="text-2xl font-black text-slate-900">Deurali Chemicals Pvt. Ltd.</h2>
            <p class="text-sm text-slate-500 mt-1">Bakery Inventory Control Console</p>
        </div>

        <!-- Alert Windows -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-rose-50 text-sm text-rose-700 rounded-lg border border-rose-200">
                <ul class="list-disc pl-5 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Input -->
            <div class="mb-4">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Admin Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@deuralichemicals.com"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-600 focus:border-teal-600 transition">
            </div>

            <!-- Password Input -->
            <div class="mb-5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Secure Password</label>
                <input id="password" type="password" name="password" required placeholder="••••••••"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-600 focus:border-teal-600 transition">
            </div>

            <!-- Remember Me Flag -->
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center select-none cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" 
                        class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-slate-300 rounded">
                    <span class="ml-2 text-sm text-slate-600 font-medium">Keep terminal authenticated</span>
                </label>
            </div>

            <!-- Access CTA -->
            <div>
                <button type="submit" 
                    class="w-full bg-teal-700 hover:bg-teal-800 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2 transition duration-150 ease-in-out">
                    Authenticate & Access Inventory
                </button>
            </div>
        </form>
    </div>

</body>
</html>