<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Email Sent | Deurali Chemicals</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md text-center">
        <div class="mb-4 text-green-500">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Reset Link Sent!</h2>
        <p class="text-gray-600 mb-6">
            We have sent a password reset link to your email. Please check your inbox (and spam folder) to continue.
        </p>
        <a href="{{ route('login') }}" class="block w-full bg-blue-900 text-white py-2 rounded hover:bg-blue-800 transition">
            Back to Login
        </a>
    </div>
</body>
</html>