<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Patio Bella</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1abc9c',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Patio Bella</h2>
                <p class="text-gray-600 mt-2">Inventory Management System</p>
            </div>

            <!-- Display Single Error Message with Auto-Hide -->
            @if(session('error'))
                <div id="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->has('email'))
                <div id="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>Invalid email or password</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('email') border-red-500 @enderror">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('password') border-red-500 @enderror">
                </div>

                <div class="mb-6 flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
            </form>

            @if(\App\Models\User::count() === 0)
            <div class="mt-6 text-center">
                <a href="{{ route('register.form') }}" class="text-primary hover:underline">
                    No user exists? Register as Super Administrator
                </a>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Auto-hide error message after 10 seconds
        setTimeout(function() {
            var errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.remove();
                }, 500);
            }
        }, 10000);
    </script>
</body>
</html>