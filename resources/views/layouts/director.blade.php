<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Director Dashboard') - PaitoBella</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-nav-link {
            transition: all 0.2s ease;
        }
        .sidebar-nav-link:hover {
            background-color: #374151;
            padding-left: 1.25rem;
        }
        .sidebar-active {
            background-color: #1e3a8a;
            border-left: 4px solid #3b82f6;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        {{-- Sidebar --}}
        <div class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="p-5 border-b border-gray-800">
                <h1 class="text-xl font-bold">PaitoBella</h1>
                <p class="text-sm text-gray-400 mt-1">Director Dashboard</p>
            </div>
            
            <nav class="flex-1 mt-6">
                <a href="{{ route('director.dashboard') }}" 
                   class="flex items-center px-4 py-3 text-sm hover:bg-gray-800 transition sidebar-nav-link
                          {{ request()->routeIs('director.dashboard') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('director.lpos.index') }}" 
                   class="flex items-center px-4 py-3 text-sm hover:bg-gray-800 transition sidebar-nav-link
                          {{ request()->routeIs('director.lpos.*') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Local Purchase Orders
                </a>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-800 rounded transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-800">@yield('page-title')</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr(Auth::user()->first_name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>