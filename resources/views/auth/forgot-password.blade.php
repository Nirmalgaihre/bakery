<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Forgot Password | Deurali Chemicals</title>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden w-full max-w-md p-8 border border-gray-100">
        
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-blue-900 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Forgot Password?</h2>
            <p class="text-gray-500 text-sm mt-2">
                No worries! Enter your registered email address below, and we'll send you instructions to reset your password.
            </p>
        </div>
        
        <!-- Password Reset Form -->
        <form action="#" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" required 
                    placeholder="name@company.com"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition duration-200">
            </div>

            <button type="submit" 
                class="w-full flex justify-center py-3 px-4 rounded-lg shadow-md text-sm font-bold text-white bg-blue-900 hover:bg-blue-800 transition-all duration-200 transform hover:scale-[1.01] active:scale-95">
                Send Reset Link
            </button>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-8">
            <a href="{{ route('login') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900 hover:underline transition">
                &larr; Back to Login
            </a>
        </div>
        
    </div>
</body>
</html>