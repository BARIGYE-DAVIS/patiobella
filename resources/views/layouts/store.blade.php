<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Store Module') - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #fef7e8;
        }
        .sidebar-active {
            background-color: #ea580c;
            color: white;
        }
        .sidebar-active svg {
            color: white;
        }
        #sidebar {
            position: fixed;
            left: -280px;
            top: 0;
            width: 280px;
            height: 100%;
            transition: left 0.3s ease;
            z-index: 50;
            background-color: #9a3412;
            display: flex;
            flex-direction: column;
        }
        #sidebar.open {
            left: 0;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            display: none;
        }
        .sidebar-overlay.active {
            display: block;
        }
        .custom-menu-btn {
            background: transparent;
            color: #c2410c;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            margin-right: 15px;
            border-radius: 8px;
        }
        .custom-menu-btn:hover {
            background: #ffedd5;
        }
        .top-bar {
            background: #fff7ed;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 30;
        }
        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .department-title {
            font-size: 18px;
            font-weight: 600;
            color: #9a3412;
        }
        .department-subtitle {
            font-size: 12px;
            color: #78350f;
        }
        main {
            margin-top: 65px;
            transition: margin-left 0.3s ease;
            padding: 20px;
        }
        body.sidebar-open main {
            margin-left: 280px;
        }
        @media (max-width: 768px) {
            body.sidebar-open main {
                margin-left: 0;
            }
        }
        .sidebar-nav-scroll {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 20px;
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside id="sidebar" class="text-white shadow-xl">

    <div class="p-4 border-b border-orange-700/60 flex-shrink-0">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold tracking-tight">STORE</h2>
                <p class="text-xs text-orange-200 mt-1">Store & Inventory</p>
            </div>
            <button id="closeSidebarBtn" class="text-white hover:text-orange-200 bg-transparent border-none cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="sidebar-nav-scroll">
        <nav class="mt-4">

            {{-- Dashboard — always visible --}}
            <a href="{{ route('store.dashboard') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('store.dashboard') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            {{-- Inventory --}}
            @canNav('view_inventory')
            <a href="{{ route('store.inventory.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('store.inventory.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Inventory
            </a>
            @endCanNav

            {{-- Batches --}}
            @canNav('view_batches')
            <a href="{{ route('store.batches.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('store.batches.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Batches
            </a>
            @endCanNav

            {{-- Stock Movements --}}
            @canNav('view_stock_movements')
            <a href="{{ route('store.stock-movements.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('store.stock-movements.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Stock Movements
            </a>
            @endCanNav

                        {{-- Categories --}}
            @canNav('view_stock_counts')
            <a href="{{ route('store.stock-counts.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('store.stock-counts.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Stock Counts
            </a>
            @endCanNav

            {{-- Requisitions --}}
            @canNav('view_requisitions')
            <a href="{{ route('store.requisitions.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('store.requisitions.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Requisitions
            </a>
            @endCanNav

            {{-- Department Requisitions --}}
            @canNav('view_requisitions')
            <a href="{{ route('store.department-requisitions.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('store.department-requisitions.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Department Requisitions
                <span class="ml-auto text-xs bg-yellow-600 px-2 py-0.5 rounded-full">New</span>
            </a>
            @endCanNav

            {{-- Categories --}}
            @canNav('view_categories')
            <a href="{{ route('store.categories.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('store.categories.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Categories
            </a>
            @endCanNav

            {{-- User profile section --}}
            <div class="px-4 pt-6 pb-3 mt-4 border-t border-orange-700/40">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-white">
                            {{ substr(Auth::user()->first_name ?? 'U', 0, 1) }}{{ substr(Auth::user()->last_name ?? '', 0, 1) }}
                        </span>
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}</p>
                        <p class="text-xs text-orange-200">{{ Auth::user()->role ?? 'Store Keeper' }}</p>
                    </div>
                </div>
            </div>

        </nav>
    </div>

    <div class="flex-shrink-0 border-t border-orange-700/60 bg-[#9a3412] p-4">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="flex items-center text-sm text-orange-200 hover:text-white transition group">
            <svg class="w-4 h-4 mr-2 flex-shrink-0 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</aside>

{{-- Top Bar --}}
<div class="top-bar">
    <div class="top-bar-left">
        <button class="custom-menu-btn" id="menuIconBtn">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <h1 class="department-title">Store & Inventory Department</h1>
            <p class="department-subtitle">Stock Management | Inventory Control | Requisitions</p>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-orange-700 text-sm flex items-center gap-1">
            <i class="fas fa-tachometer-alt"></i>
            <span>Main Dashboard</span>
        </a>
        <div class="relative">
            <button class="text-gray-600 hover:text-orange-700">
                <i class="fas fa-bell"></i>
            </button>
        </div>
    </div>
</div>

{{-- Main Content --}}
<main>
    @if(session('success'))
        <div class="mb-4 p-4 bg-amber-50 border-l-4 border-orange-500 text-amber-800 rounded-r-lg flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-orange-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg flex items-center gap-2 shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

<script>
    const sidebar         = document.getElementById('sidebar');
    const overlay         = document.getElementById('sidebarOverlay');
    const menuIconBtn     = document.getElementById('menuIconBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const navLinks        = document.querySelectorAll('.sidebar-nav-link');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
    }

    menuIconBtn.addEventListener('click', openSidebar);
    closeSidebarBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
    navLinks.forEach(link => link.addEventListener('click', closeSidebar));
</script>

@stack('scripts')
</body>
</html>
