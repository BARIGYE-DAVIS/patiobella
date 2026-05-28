<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Procurement Module') - {{ config('app.name') }}</title>

    <!-- Tailwind CSS -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->

    <!-- Vite -->
     @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
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

        /* ── Sidebar (Orange theme) ── */
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

        /* ── Overlay ── */
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

        /* ── Top Bar ── */
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

        /* ── Hamburger button ── */
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

        /* ── Main content ── */
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

        /* Scrollable nav area */
        .sidebar-nav-scroll {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 20px;
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- Sidebar Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ── SIDEBAR (Orange theme, logout at bottom) ── --}}
<aside id="sidebar" class="text-white shadow-xl">

    <!-- Header - stays at top -->
    <div class="p-4 border-b border-orange-700/60 flex-shrink-0">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold tracking-tight">PROCUREMENT</h2>
                <p class="text-xs text-orange-200 mt-1">Purchase & Supplies</p>
            </div>
            <button id="closeSidebarBtn"
                    class="text-white hover:text-orange-200 bg-transparent border-none cursor-pointer p-1 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Scrollable navigation area -->
    <div class="sidebar-nav-scroll">
        <nav class="mt-4">
            <a href="{{ route('procurement.dashboard') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('procurement.dashboard') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('procurement.requisitions.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('procurement.requisitions.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Requisitions
                @if(isset($pendingCount) && $pendingCount > 0)
                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('procurement.purchase-orders.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('procurement.purchase-orders.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Purchase Orders
            </a>

            <a href="{{ route('procurement.lpo.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('procurement.lpo.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                LPOs
            </a>

            <a href="{{ route('procurement.approved-lpos.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('procurement.approved-lpos.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-file-alt mr-3 w-5"></i>
                <span>Approved LPOs</span>
                <span class="ml-auto text-xs bg-yellow-600 px-2 py-0.5 rounded-full">EPO</span>
            </a>

            <a href="{{ route('procurement.vendors.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('procurement.vendors.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Vendors
            </a>

            <a href="{{ route('procurement.goods-received.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('procurement.goods-received.*') ? 'sidebar-active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Goods Received
            </a>

            <a href="{{ route('procurement.cost-prices.index') }}"
               class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                      {{ request()->routeIs('procurement.cost-prices.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-dollar-sign w-5 h-5 mr-3"></i>
                <span>Cost Prices</span>
            </a>

            <!-- User profile section (inside scrollable area) -->
            <div class="px-4 pt-6 pb-3 mt-4 border-t border-orange-700/40">
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-white">
                            {{ substr(Auth::user()->first_name ?? 'U', 0, 1) }}{{ substr(Auth::user()->last_name ?? '', 0, 1) }}
                        </span>
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}</p>
                        <p class="text-xs text-orange-200">{{ Auth::user()->role ?? 'Procurement Officer' }}</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- LOGOUT - absolute bottom, last element -->
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


{{-- ── TOP BAR ── --}}
<div class="top-bar">
    {{-- Left: Hamburger + Branding --}}
    <div class="top-bar-left">
        <button id="menuIconBtn" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <div class="text-base font-semibold text-orange-800">Procurement Module</div>
            <div class="text-xs text-orange-600">Purchase & Supplies</div>
        </div>
    </div>

    {{-- Right: Main Dashboard link + Notification Bell --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-orange-700 text-sm hidden sm:inline-flex items-center gap-1">
            <i class="fas fa-tachometer-alt"></i>
            <span>Main Dashboard</span>
        </a>

        {{-- Notification Bell --}}
        <div class="relative">
            <button id="notificationBell" class="relative text-gray-600 hover:text-orange-700 p-1" aria-label="Notifications">
                <i class="fas fa-bell text-lg"></i>
                <span id="notificationBadge"
                      class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 hidden">
                    0
                </span>
            </button>

            {{-- Dropdown --}}
            <div id="notificationDropdown"
                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 hidden">
                <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="font-semibold text-gray-800">Notifications</h4>
                    <button id="clearNotifications" class="text-xs text-gray-400 hover:text-gray-600">Clear all</button>
                </div>
                <div id="notificationList" class="max-h-96 overflow-y-auto">
                    <div class="p-4 text-center text-gray-500 text-sm">No new notifications</div>
                </div>
                <div class="p-2 border-t border-gray-100 text-center">
                    <a href="{{ route('procurement.requisitions.index') }}"
                       class="text-xs text-orange-600 hover:underline">View all requisitions</a>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ── MAIN CONTENT ── --}}
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
    // ── Sidebar toggle ──────────────────────────────────────────────
    const sidebar       = document.getElementById('sidebar');
    const overlay       = document.getElementById('sidebarOverlay');
    const menuIconBtn   = document.getElementById('menuIconBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const navLinks      = document.querySelectorAll('.sidebar-nav-link');

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


    // ── Notifications ───────────────────────────────────────────────
    let notificationInterval;

    function checkPendingRequisitions() {
        fetch('{{ route("procurement.notifications.check") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) return;

            const badge            = document.getElementById('notificationBadge');
            const notificationList = document.getElementById('notificationList');

            if (data.pending_count > 0) {
                badge.textContent = data.pending_count;
                badge.classList.remove('hidden');

                notificationList.innerHTML = data.pending_requisitions.map(req => `
                    <a href="/procurement/requisitions/${req.id}"
                       class="block p-3 hover:bg-gray-50 border-b border-gray-100 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-medium text-sm text-gray-800">${req.requisition_number}</span>
                                <p class="text-xs text-gray-500 mt-0.5">Requested by: ${req.requested_by}</p>
                                <p class="text-xs text-gray-500">Date needed: ${req.date_needed}</p>
                            </div>
                            <span class="text-xs text-gray-400 ml-2 whitespace-nowrap">${req.created_at}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Items: ${req.items_count}</p>
                    </a>
                `).join('');
            } else {
                badge.classList.add('hidden');
                notificationList.innerHTML =
                    '<div class="p-4 text-center text-gray-500 text-sm">No pending requisitions</div>';
            }
        })
        .catch(err => console.error('Notification fetch error:', err));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const bellBtn  = document.getElementById('notificationBell');
        const dropdown = document.getElementById('notificationDropdown');
        const clearBtn = document.getElementById('clearNotifications');
        const notificationList = document.getElementById('notificationList');
        const badge    = document.getElementById('notificationBadge');

        // Toggle dropdown
        if (bellBtn) {
            bellBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            // Close dropdown on outside click
            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && e.target !== bellBtn) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        // Clear notifications (UI only)
        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                notificationList.innerHTML =
                    '<div class="p-4 text-center text-gray-500 text-sm">No pending requisitions</div>';
                badge.classList.add('hidden');
            });
        }

        // Initial check + poll every 10 seconds
        checkPendingRequisitions();
        notificationInterval = setInterval(checkPendingRequisitions, 10000);
    });
</script>

@stack('scripts')
</body>
</html>
