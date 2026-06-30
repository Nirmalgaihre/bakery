<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login | Deurali Chemicals</title>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <!-- Main Container -->
    <div class="bg-white shadow-xl flex flex-col md:flex-row rounded-2xl overflow-hidden w-full max-w-4xl">

        <!-- Left Side: Branding Section -->
        <div class="w-full md:w-1/2 bg-blue-50 flex flex-col items-center justify-center p-8 md:p-10">
            <div class="mb-6">
                <img src="{{ asset('storage/img/dcl.png') }}" alt="Deurali Chemicals Logo" class="w-28 h-28 object-contain">
            </div>
            <h1 class="text-2xl font-bold text-blue-900 text-center mb-2">Deurali Chemicals Pvt Ltd</h1>
            <p class="text-gray-600 text-center text-sm">Kuleshwor, Kathmandu</p>
            <p class="text-gray-600 text-center text-sm">Kathmandu Metro-14</p>
        </div>

        <!-- Right Side: Login Form Section -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Welcome Back</h2>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <!-- Email Field -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="Enter a valid Email Address">
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="••••••••">
                </div>

                <!-- Remember Me & Forgot Password Row -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mr-2">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline font-medium">Forgot password?</a>
                </div>

                <!-- Error Handling -->
                @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4 border border-red-200">
                    {{ $errors->first() }}
                </div>
                @endif

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 transition-all duration-200 text-white py-2.5 rounded-lg font-semibold shadow-md">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>

</html>