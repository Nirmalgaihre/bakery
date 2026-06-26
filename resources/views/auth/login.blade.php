<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login | Deurali Chemicals</title>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <!-- Main Container: Added md:flex-row for responsiveness and removed fixed height -->
    <div class="bg-white shadow-lg flex flex-col md:flex-row rounded-lg overflow-hidden w-full max-w-4xl">

        <!-- Left Side: Branding Section -->
        <div class="w-full md:w-1/2 bg-gray-100 flex flex-col items-center justify-center p-8 md:p-10 border-b md:border-b-0 md:border-r border-gray-200">
            <div class="mb-6">
                <img src="{{ asset('storage/img/dcl.png') }}" alt="Deurali Chemicals Logo" class="w-24 h-24 md:w-32 md:h-32 object-contain">
            </div>
            <h1 class="text-xl font-bold text-blue-900 text-center mb-2">Deurali Chemicals Pvt Ltd</h1>
            <p class="text-gray-600 text-center text-sm">Kuleshwor, Kathmandu</p>
            <p class="text-gray-600 text-center text-sm">Kathmandu Metro-14</p>
        </div>

        <!-- Right Side: Login Form Section -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Login Portal</h2>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 outline-none" required
                        placeholder="Enter your email">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 outline-none" required
                        placeholder="Enter your password">
                </div>

                @if ($errors->any())
                <div class="text-red-500 text-sm mb-4">
                    {{ $errors->first() }}
                </div>
                @endif

                <button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 transition text-white py-2 rounded font-semibold">
                    Sign In
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
            </div>
        </div>
    </div>
</body>

</html>