<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Management Dashboard') - PaitoBella</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

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
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        #sidebar.open {
            left: 0;
        }
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            display: none;
        }
        .sidebar-overlay.active {
            display: block;
        }
        .top-bar {
            background: #fff7ed;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 0 24px;
            height: 65px;
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
            gap: 12px;
        }
        #menuIconBtn {
            background: transparent;
            border: none;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 8px;
            color: #c2410c;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        #menuIconBtn:hover {
            background: #ffedd5;
        }
        main {
            margin-top: 65px;
            padding: 20px;
            transition: margin-left 0.3s ease;
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
        /* Section header — only shown if at least one child is visible */
        .nav-section-label {
            padding: 20px 16px 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #fda4af;
            font-weight: 600;
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
                <h2 class="text-xl font-bold tracking-tight">PaitoBella</h2>
                <p class="text-xs text-orange-200 mt-1">GM Dashboard</p>
            </div>
            <button id="closeSidebarBtn"
                    class="text-white hover:text-orange-200 bg-transparent border-none cursor-pointer p-1 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="sidebar-nav-scroll">
        <nav class="mt-4">

            {{-- ========== CORE OPERATIONS ========== --}}
            @canNavAny(['view_requisitions', 'view_purchase_orders', 'view_goods_received', 'view_vendors'])
            <div class="nav-section-label text-orange-300">Core Operations</div>
            @endCanNavAny

            {{-- Dashboard — always visible --}}
            <a href="{{ route('management.dashboard') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.dashboard') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            {{-- Requisitions --}}
            @canNav('view_requisitions')
            <a href="{{ route('management.requisitions.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.requisitions.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Requisitions
            </a>
            @endCanNav

            {{-- Department Requisitions --}}
            @canNav('view_requisitions')
            <a href="{{ route('management.department-requisitions.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('department-requisitions.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Department Requisitions
            </a>
            @endCanNav

            {{-- Purchase Orders --}}
            @canNav('view_purchase_orders')
            <a href="{{ route('management.purchase-orders.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.purchase-orders.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Purchase Orders
            </a>
            @endCanNav

            {{-- GRNs --}}
            @canNav('view_goods_received')
            <a href="{{ route('management.grns.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.grns.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                GRNs
            </a>
            @endCanNav

            {{-- Vendors --}}
            @canNav('view_vendors')
            <a href="{{ route('management.vendors.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.vendors.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Vendors
            </a>
            @endCanNav

            {{-- ========== INVENTORY & STOCK ========== --}}
            @canNavAny(['view_stock_movements', 'view_stock_counts'])
            <div class="nav-section-label text-orange-300">Inventory & Stock</div>
            @endCanNavAny

            {{-- Stock Movements --}}
            @canNav('view_stock_movements')
            <a href="{{ route('management.stock-movements.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.stock-movements.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Stock Movements
            </a>
            @endCanNav

            {{-- Stock Counts --}}
            @canNav('view_stock_counts')
            <a href="{{ route('management.stock-counts.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.stock-counts.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-6 4h6m-6-8h6"/>
                </svg>
                Stock Count / Reconciliation
            </a>
            @endCanNav

            {{-- ========== MENU & ITEMS ========== --}}
            @canNavAny(['view_menus', 'view_menu_items', 'view_menu_item_categories', 'view_tables', 'view_reservations'])
            <div class="nav-section-label text-orange-300">Menu & Items</div>
            @endCanNavAny

            {{-- Menu Management --}}
            @canNav('view_menus')
            <a href="{{ route('management.menus.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.menus.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-utensils w-5 h-5 mr-3 text-orange-200"></i>
                <span>Menu Management</span>
            </a>
            @endCanNav

            {{-- Menu Items --}}
            @canNav('view_menu_items')
            <a href="{{ route('management.menu-items.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.menu-items.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-concierge-bell w-5 h-5 mr-3 text-orange-200"></i>
                <span>Menu Items</span>
            </a>
            @endCanNav

            {{-- Menu Item Categories --}}
            @canNav('view_menu_item_categories')
            <a href="{{ route('management.menu-item-categories.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.menu-item-categories.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-th-list w-5 h-5 mr-3 text-orange-200"></i>
                <span>Menu Item Categories</span>
            </a>
            @endCanNav

            {{-- Tables --}}
            @canNav('view_tables')
            <a href="{{ route('management.tables.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.tables.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-table w-5 h-5 mr-3 text-orange-200"></i>
                <span>Tables</span>
            </a>
            @endCanNav

            {{-- Reservations --}}
            @canNav('view_reservations')
            <a href="{{ route('management.reservations.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.reservations.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-calendar-alt w-5 h-5 mr-3 text-orange-200"></i>
                <span>Reservations</span>
            </a>
            @endCanNav

            {{-- ========== PROCUREMENT & ANALYTICS ========== --}}
            @canNavAny(['view_prices', 'view_financial_reports'])
            <div class="nav-section-label text-orange-300">Procurement & Analytics</div>
            @endCanNavAny

            {{-- Price Management --}}
            @canNav('view_prices')
            <a href="{{ route('management.prices.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.prices.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-tag w-5 h-5 mr-3 text-orange-200"></i>
                <span>Price Management</span>
            </a>
            @endCanNav

            {{-- Reports --}}
            @canNav('view_financial_reports')
            <a href="{{ route('management.reports.purchase-orders') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.reports.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reports
            </a>
            @endCanNav

            {{-- Analytics --}}
            @canNav('view_financial_reports')
            <a href="{{ route('management.analytics.procurement') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('management.analytics.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics
            </a>
            @endCanNav

            {{-- User profile section --}}
            <div class="px-4 pt-6 pb-3 mt-2 border-t border-orange-700/40">
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-white">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </span>
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-orange-200">{{ Auth::user()->role ?? 'Manager' }}</p>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</aside>


{{-- TOP BAR --}}
<div class="top-bar">
    <div class="top-bar-left">
        <button id="menuIconBtn" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <div class="text-base font-semibold text-orange-800">Management Dashboard</div>
            <div class="text-xs text-orange-600">PaitoBella · Executive View</div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <span class="text-sm text-stone-600 hidden sm:inline">@yield('page-title', 'Overview')</span>
        <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
    </div>
</div>


{{-- MAIN CONTENT --}}
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
