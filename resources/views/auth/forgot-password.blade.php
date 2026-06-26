<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Forgot Password | Deurali Chemicals</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-lg rounded-lg overflow-hidden w-full max-w-md p-8">
        
        <!-- Header -->
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Forgot Password?</h2>
        <p class="text-gray-600 text-sm mb-6">
            Enter your email address and we'll send you a link to reset your password.
        </p>
        
        <!-- Password Reset Form -->
        <form action="#" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" required 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <button type="submit" 
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-900 hover:bg-blue-800 focus:outline-none">
                Send Reset Link
            </button>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">
                Back to Login
            </a>
        </div>
        
    </div>
</body>
</html>