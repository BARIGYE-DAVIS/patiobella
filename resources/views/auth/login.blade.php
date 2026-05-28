<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PaitoBella POS</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
        .shake { animation: shake 0.4s ease; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .pulse { animation: pulse 2s infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-900 via-orange-800 to-amber-900 min-h-screen">

<div class="pos-root min-h-screen flex flex-col">
    <div class="flex-1 flex">

        <!-- LEFT SIDE - LOGO & BRANDING -->
        <div class="hidden lg:flex w-1/2 flex-col items-center justify-center p-12 relative">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative z-10 text-center">
                <div class="w-28 h-28 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <i class="fas fa-utensils text-5xl text-white"></i>
                </div>
                <h1 class="text-5xl font-bold text-white mb-3">Paito<span class="text-orange-400">Bella</span></h1>
                <p class="text-orange-200/70 text-lg">Point of Sale System</p>
                <div class="w-16 h-0.5 bg-orange-500/50 mx-auto my-6"></div>
                <p class="text-orange-200/50 text-sm max-w-sm mx-auto">Secure, fast, and reliable retail management for your business</p>
            </div>
        </div>

        <!-- RIGHT SIDE - LOGIN FORM -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-utensils text-3xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white">Paito<span class="text-orange-400">Bella</span></h2>
                    <p class="text-orange-200/60 text-sm mt-1">Point of Sale System</p>
                </div>

                <!-- Login Card -->
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-orange-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-orange-500/30">
                            <i class="fas fa-lock text-2xl text-orange-400"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-white">Operator Login</h3>
                        <p class="text-orange-200/50 text-sm mt-1">Enter your password to open a session</p>
                    </div>

                    <!-- Error Message from Session -->
                    @if(session('error'))
                    <div class="mb-6 bg-red-500/20 border-l-4 border-red-500 rounded-r-lg p-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                            <span class="text-red-200 text-sm">{{ session('error') }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Error Message from Validation -->
                    @error('password')
                    <div class="mb-6 bg-red-500/20 border-l-4 border-red-500 rounded-r-lg p-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                            <span class="text-red-200 text-sm">{{ $message }}</span>
                        </div>
                    </div>
                    @enderror

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-orange-200/70 text-xs uppercase tracking-wider mb-2">
                                <i class="fas fa-key mr-2"></i>Password
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                       class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white text-lg font-mono tracking-wider focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition @error('password') border-red-500 @enderror"
                                       placeholder="Enter password" autofocus>
                                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-orange-400 transition">
                                    <i id="eyeIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-amber-600 transition shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In
                        </button>
                    </form>

                    <div class="text-center mt-6">
                        <a href="#" onclick="alert('Contact your supervisor to reset password')" class="text-orange-400/60 hover:text-orange-400 text-sm transition">Forgot password?</a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-orange-200/30 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full pulse"></div>
                        <span>System Online</span>
                    </div>
                    <span>•</span>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-store"></i>
                        <span>Branch: Main</span>
                    </div>
                    <span>•</span>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock"></i>
                        <span id="currentTime">--:-- --</span>
                    </div>
                    <span>•</span>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-tv"></i>
                        <span>Terminal: 01</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    // Update time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('currentTime').textContent = timeString;
    }
    updateTime();
    setInterval(updateTime, 1000);
</script>
</body>
</html>
