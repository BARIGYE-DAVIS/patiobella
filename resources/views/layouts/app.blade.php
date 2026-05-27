<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Patio Bella - Inventory System</title>
   <!-- <script src="https://cdn.tailwindcss.com"></script> -->
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        'primary-dark': '#ea580c',
                        'primary-light': '#fed7aa',
                        dark: '#1e293b',
                        'dark-light': '#334155',
                    }
                }
            }
        }
    </script>
    <style>
        /* Sidebar — fully closed by default */
        #sidebar {
            width: 0;
            min-width: 0;
            overflow: hidden;
            flex-shrink: 0;
            background: #1e293b;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 100;
        }

        #sidebar.open {
            width: 260px;
        }

        /* Labels hidden until sidebar opens */
        #sidebar .nav-label,
        #sidebar .brand-text,
        #sidebar .logout-label {
            opacity: 0;
            white-space: nowrap;
            transition: opacity 0.15s ease;
        }

        #sidebar.open .nav-label,
        #sidebar.open .brand-text,
        #sidebar.open .logout-label {
            opacity: 1;
            transition: opacity 0.2s ease 0.15s;
        }

        /* Backdrop overlay */
        #sidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 99;
            backdrop-filter: blur(2px);
        }

        #sidebarOverlay.active {
            display: block;
        }

        /* Active nav item */
        .nav-item.active {
            background: #f97316;
            color: white;
        }
        .nav-item.active i {
            color: white;
        }
        .nav-item:not(.active):hover {
            background: #334155;
        }
        .nav-item {
            transition: all 0.2s ease;
        }

        /* Hamburger animation */
        #toggleSidebar .bar {
            display: block;
            width: 20px;
            height: 2px;
            background: #374151;
            border-radius: 2px;
            transition: all 0.3s ease;
            transform-origin: center;
        }
        #toggleSidebar.is-open .bar:nth-child(1) {
            transform: translateY(6px) rotate(45deg);
        }
        #toggleSidebar.is-open .bar:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }
        #toggleSidebar.is-open .bar:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg);
        }
    </style>
</head>
<body class="bg-gray-100">

    {{-- Backdrop — clicking this closes the sidebar --}}
    <div id="sidebarOverlay"></div>

    <div class="flex min-h-screen">

        @auth
        <!-- ===== SIDEBAR ===== -->
        <div id="sidebar" class="flex flex-col shadow-xl">

            <!-- Brand -->
            <div class="flex items-center py-5 px-5 gap-3 border-b border-gray-700 flex-shrink-0">
                <div class="bg-primary rounded-lg p-2 flex-shrink-0">
                    <i class="fas fa-utensils text-white text-lg"></i>
                </div>
                <div class="brand-text flex-shrink-0">
                    <p class="text-white font-bold text-sm leading-tight">Patio Bella</p>
                    <p class="text-gray-400 text-xs">Inventory System</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 flex-1 px-2 space-y-1 overflow-hidden">

                <a href="{{ route('dashboard') }}"
                   class="nav-link nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Dashboard</span>
                </a>

                <a href="{{ route('users.index') }}"
                   class="nav-link nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Users</span>
                </a>

                <a href="{{ route('departments.index') }}"
                   class="nav-link nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                    <i class="fas fa-building w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Departments</span>
                </a>

                <a href="{{ route('roles.index') }}"
                   class="nav-link nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Roles</span>
                </a>

                <a href="{{ route('permissions.index') }}"
                   class="nav-link nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                    <i class="fas fa-key w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Permissions</span>
                </a>

            </nav>

            <!-- Logout -->
            <div class="p-3 mb-4 border-t border-gray-700 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="nav-item w-full bg-red-600 hover:bg-red-700 text-white px-3 py-3 rounded-lg flex items-center gap-3">
                        <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0 text-lg"></i>
                        <span class="logout-label text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>
        <!-- ===== END SIDEBAR ===== -->
        @endauth

        <!-- ===== MAIN AREA ===== -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Navbar -->
            <nav class="bg-white shadow-md border-b border-gray-200 sticky top-0 z-40">
                <div class="px-4 py-4 flex items-center justify-between gap-4">

                    <!-- Left: hamburger + page title -->
                    <div class="flex items-center gap-3">
                        @auth
                        <button id="toggleSidebar"
                                class="flex flex-col gap-1.5 p-2 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none"
                                aria-label="Toggle sidebar">
                            <span class="bar"></span>
                            <span class="bar"></span>
                            <span class="bar"></span>
                        </button>
                        @endauth

                        <h1 class="text-xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            @yield('title', 'Patio Bella')
                        </h1>
                    </div>

                    <!-- Right: user dropdown -->
                    @auth
                    <div class="relative">
                        <button class="flex items-center text-gray-700 hover:text-primary focus:outline-none gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors" id="userMenuButton">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <span class="hidden sm:inline text-sm font-medium">{{ Auth::user()->first_name }}</span>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg hidden z-50 border border-gray-100 overflow-hidden" id="userMenuDropdown">
                            <div class="px-4 py-3 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-user w-4 text-primary"></i> Profile Settings
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-sign-out-alt w-4"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth

                </div>
            </nav>

            <!-- Page Content -->
            <div class="p-6 flex-1">

                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg shadow-sm flex items-start justify-between" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3 text-green-500"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" class="text-green-700 ml-4 hover:text-green-900" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-sm flex items-start justify-between" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" class="text-red-700 ml-4 hover:text-red-900" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>

        </div>
        <!-- ===== END MAIN AREA ===== -->

    </div>

    <script>
        const sidebar   = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const overlay   = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('open');
            toggleBtn && toggleBtn.classList.add('is-open');
            overlay.classList.add('active');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            toggleBtn && toggleBtn.classList.remove('is-open');
            overlay.classList.remove('active');
        }

        // Hamburger toggle button
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
            });
        }

        // Click on backdrop → close
        overlay.addEventListener('click', closeSidebar);

        // Click anywhere outside the sidebar → close
        document.addEventListener('click', function (e) {
            if (
                sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                toggleBtn && !toggleBtn.contains(e.target)
            ) {
                closeSidebar();
            }
        });

        // Click on a nav link → close sidebar, then navigate
        document.querySelectorAll('#sidebar .nav-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                closeSidebar();
                // Small delay so the close animation plays before navigating
                setTimeout(function () {
                    window.location.href = href;
                }, 280);
            });
        });

        // Escape key → close
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });

        // ── User menu dropdown ──────────────────────────────────────────
        const userMenuBtn      = document.getElementById('userMenuButton');
        const userMenuDropdown = document.getElementById('userMenuDropdown');

        if (userMenuBtn) {
            userMenuBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
