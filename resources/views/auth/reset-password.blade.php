<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Reset Password | Deurali Chemicals</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-blue-900">Set New Password</h2>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request()->email }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="password" required 
                    placeholder="Enter at least 6 characters"
                    class="w-full p-2 border rounded mt-1 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" required 
                    placeholder="Confirm your new password"
                    class="w-full p-2 border rounded mt-1 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit" 
                class="w-full bg-blue-900 text-white py-2 rounded hover:bg-blue-800 transition duration-200 font-semibold">
                Reset Password
            </button>
        </form>
    </div>
</body>
</html>