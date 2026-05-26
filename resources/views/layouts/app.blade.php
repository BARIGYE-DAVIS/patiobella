<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Patio Bella - Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        /* Sidebar — icon-only by default, expands on hover */
        #sidebar {
            width: 68px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            flex-shrink: 0;
            background: #1e293b;
        }

        #sidebar:hover {
            width: 260px;
        }

        /* Labels hidden until hover */
        #sidebar .nav-label {
            opacity: 0;
            white-space: nowrap;
            transition: opacity 0.2s ease 0.1s;
            margin-left: 12px;
        }

        #sidebar:hover .nav-label {
            opacity: 1;
        }

        /* Brand text */
        #sidebar .brand-text {
            opacity: 0;
            max-width: 0;
            overflow: hidden;
            transition: opacity 0.2s ease, max-width 0.3s ease;
            white-space: nowrap;
        }

        #sidebar:hover .brand-text {
            opacity: 1;
            max-width: 180px;
        }

        /* Logout label */
        #sidebar .logout-label {
            display: none;
            margin-left: 8px;
        }

        #sidebar:hover .logout-label {
            display: inline;
        }

        /* Logout button on hover */
        #sidebar .logout-btn {
            justify-content: center;
        }

        #sidebar:hover .logout-btn {
            justify-content: flex-start;
            padding-left: 18px;
        }

        /* Tooltip on collapsed icons - hidden on hover */
        #sidebar:not(:hover) .nav-item {
            position: relative;
        }

        #sidebar:not(:hover) .nav-item:hover::after {
            content: attr(data-tooltip);
            position: fixed;
            left: 76px;
            background: #f97316;
            color: #fff;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
            white-space: nowrap;
            z-index: 9999;
            pointer-events: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.5px;
        }

        /* Active nav item styling */
        .nav-item.active {
            background: #f97316;
            color: white;
            border-left: 3px solid #f97316;
        }

        .nav-item.active i {
            color: white;
        }

        .nav-item:not(.active):hover {
            background: #334155;
        }

        /* Smooth transitions for all interactive elements */
        .nav-item {
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">

        @auth
        <!-- ===== SIDEBAR - HOVER TO EXPAND ===== -->
        <div id="sidebar" class="min-h-screen flex flex-col z-50 shadow-xl">

            <!-- Brand -->
            <div class="flex items-center py-5 px-5 gap-3 border-b border-gray-700">
                <div class="bg-primary rounded-lg p-2 flex-shrink-0">
                    <i class="fas fa-utensils text-white text-lg"></i>
                </div>
                <div class="brand-text">
                    <p class="text-white font-bold text-sm leading-tight">Patio Bella</p>
                    <p class="text-gray-400 text-xs">Inventory System</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 flex-1 px-2 space-y-1">
                <a href="{{ route('dashboard') }}"
                   data-tooltip="Dashboard"
                   class="nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Dashboard</span>
                </a>

                <a href="{{ route('users.index') }}"
                   data-tooltip="Users"
                   class="nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Users</span>
                </a>

                <a href="{{ route('departments.index') }}"
                   data-tooltip="Departments"
                   class="nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg transition-colors {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                    <i class="fas fa-building w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Departments</span>
                </a>

                <a href="{{ route('roles.index') }}"
                   data-tooltip="Roles"
                   class="nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg transition-colors {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Roles</span>
                </a>

                <a href="{{ route('permissions.index') }}"
                   data-tooltip="Permissions"
                   class="nav-item flex items-center px-3 py-3 gap-3 text-gray-300 hover:text-white rounded-lg transition-colors {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                    <i class="fas fa-key w-5 text-center flex-shrink-0 text-lg"></i>
                    <span class="nav-label text-sm font-medium">Permissions</span>
                </a>
            </nav>

            <!-- Logout Section -->
            <div class="p-3 mb-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            data-tooltip="Logout"
                            class="logout-btn nav-item w-full bg-red-600 hover:bg-red-700 text-white px-3 py-3 rounded-lg transition flex items-center gap-3">
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
                <div class="px-6 py-4 flex items-center justify-between gap-4">

                    <!-- Left: page title -->
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            @yield('title', 'Patio Bella')
                        </h1>
                    </div>

                    <!-- Right: user dropdown -->
                    @auth
                    <div class="relative">
                        <button class="flex items-center text-gray-700 hover:text-primary focus:outline-none gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors" id="userMenuButton">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-full w-8 h-8 flex items-center justify-center">
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
        // User menu dropdown
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
