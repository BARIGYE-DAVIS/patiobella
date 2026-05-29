{{-- resources/views/layouts/bar.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bar Dashboard') - PatioBella</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
        }
        .sidebar-active {
            background-color: #ea580c;
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
            background-color: #111827;
            overflow-y: auto;
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
            background: #eff1f3;
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
            color: #ea580c;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        #menuIconBtn:hover {
            background: #e5e7eb;
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
        .sidebar-nav-link {
            transition: all 0.2s ease;
        }
        .sidebar-nav-link:hover {
            background-color: #c2410c;
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside id="sidebar" class="text-white shadow-xl">

    <div class="p-4 border-b border-gray-700">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fas fa-wine-bottle text-orange-500 text-xl"></i>
                <div>
                    <h2 class="text-xl font-bold">PATIO BELLA <span class="text-orange-500">BAR</span></h2>
                    <p class="text-xs text-gray-400 mt-0.5">Bar Management System</p>
                </div>
            </div>
            <button id="closeSidebarBtn"
                    class="text-white hover:text-gray-300 bg-transparent border-none cursor-pointer p-1 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="mt-4">

        {{-- Main Menu --}}
        <div class="px-3 mb-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 px-3">Main Menu</p>

            {{-- Dashboard — always visible --}}
            <a href="{{ route('bar.dashboard') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link
                      {{ request()->routeIs('bar.dashboard') ? 'sidebar-active' : '' }}">
                <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                Dashboard
            </a>

            {{-- Point of Sale --}}
            @canNav('view_pos')
            <a href="{{ route('bar.pos') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link
                      {{ request()->routeIs('bar.pos') ? 'sidebar-active' : '' }}">
                <i class="fas fa-cash-register w-5 h-5 mr-3"></i>
                Point of Sale
            </a>
            @endCanNav

            {{-- Requisitions --}}
            @canNav('view_requisitions')
            <a href="{{ route('bar.requisitions.index') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link
                      {{ request()->routeIs('bar.requisitions.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-clipboard-list w-5 h-5 mr-3"></i>
                Requisitions
            </a>
            @endCanNav

            {{-- Order Tickets --}}
            @canNav('view_order_tickets')
            <a href="{{ route('bar.order-tickets') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link
                      {{ request()->routeIs('bar.order-tickets') ? 'sidebar-active' : '' }}">
                <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                Order Tickets
            </a>
            @endCanNav

            {{-- My Stock --}}
            @canNav('view_stock')
            <a href="{{ route('bar.stock.index') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link
                      {{ request()->routeIs('bar.stock') ? 'sidebar-active' : '' }}">
                <i class="fas fa-boxes w-5 h-5 mr-3"></i>
                My Stock
            </a>
            @endCanNav

            {{-- Invoices / Payslips --}}
            @canNav('view_invoices')
            <a href="{{ route('bar.invoices.index') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link
                      {{ request()->routeIs('bar.invoices.index') ? 'sidebar-active' : '' }}">
                <i class="fas fa-receipt w-5 h-5 mr-3"></i>
                Invoices / Payslips
            </a>
            @endCanNav
        </div>

        {{-- Staff --}}
        @canNav('view_cashiers')
        <div class="px-3 mb-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 px-3">Staff</p>
            <a href="{{ route('bar.cashiers.index') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link
                      {{ request()->routeIs('bar.cashiers.index') ? 'sidebar-active' : '' }}">
                <i class="fas fa-users w-5 h-5 mr-3"></i>
                Cashiers
            </a>
        </div>
        @endCanNav

        {{-- Reports --}}
        @canNav('view_reports')
        <div class="px-3">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 px-3">Reports</p>
            <a href="{{ route('bar.reports.daily') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link
                      {{ request()->routeIs('bar.reports.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-calendar-day w-5 h-5 mr-3"></i>
                Daily Report
            </a>
            <a href="{{ route('bar.reports.monthly') }}"
               class="flex items-center px-4 py-3 text-sm rounded-md sidebar-nav-link">
                <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                Monthly Report
            </a>
        </div>
        @endCanNav

    </nav>

    {{-- Sidebar Footer --}}
    <div class="absolute bottom-0 w-full p-4 border-t border-gray-700 bg-gray-900">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold text-white">
                    {{ substr(Auth::user()->first_name ?? 'B', 0, 1) }}{{ substr(Auth::user()->last_name ?? 'S', 0, 1) }}
                </span>
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-sm font-medium truncate">{{ Auth::user()->first_name ?? 'Bar' }} {{ Auth::user()->last_name ?? 'Staff' }}</p>
                <p class="text-xs text-gray-400">{{ Auth::user()->role ?? 'Bar Staff' }}</p>
            </div>
        </div>

        <a href="{{ route('bar.profile.edit') }}"
           class="mt-3 flex items-center text-sm text-gray-400 hover:text-white transition">
            <i class="fas fa-user w-4 h-4 mr-2 flex-shrink-0"></i>
            My Profile
        </a>

        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="mt-2 flex items-center text-sm text-orange-400 hover:text-white transition">
            <i class="fas fa-sign-out-alt w-4 h-4 mr-2 flex-shrink-0"></i>
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
        <button id="menuIconBtn" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <div class="text-base font-semibold text-orange-800">Bar Department</div>
            <div class="text-xs text-gray-500">Bar Management System</div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800 text-sm hidden sm:inline-flex items-center gap-1">
            <i class="fas fa-tachometer-alt"></i>
            <span>Main Dashboard</span>
        </a>

        {{-- Notification Bell --}}
        <div class="relative">
            <button id="notificationBell" class="relative text-gray-600 hover:text-gray-800 p-1" aria-label="Notifications">
                <i class="fas fa-bell text-lg"></i>
                <span id="notificationBadge"
                      class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 hidden">
                    0
                </span>
            </button>

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
                    <a href="{{ route('bar.requisitions.index') }}"
                       class="text-xs text-orange-600 hover:underline">View all requisitions</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<main>
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2 alert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center gap-2 alert">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Bar Dashboard')</h1>
    </div>

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

    // ── Notifications ───────────────────────────────────────────────
    function checkPendingRequisitions() {
        fetch('{{ route("bar.notifications.check") }}', {
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
                    <a href="/bar/requisitions/${req.id}"
                       class="block p-3 hover:bg-gray-50 border-b border-gray-100 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-medium text-sm text-gray-800">${req.requisition_number}</span>
                                <p class="text-xs text-gray-500 mt-0.5">Date needed: ${req.date_needed || 'Not set'}</p>
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
        const badge    = document.getElementById('notificationBadge');

        if (bellBtn) {
            bellBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && e.target !== bellBtn) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                document.getElementById('notificationList').innerHTML =
                    '<div class="p-4 text-center text-gray-500 text-sm">No pending requisitions</div>';
                badge.classList.add('hidden');
            });
        }

        checkPendingRequisitions();
        setInterval(checkPendingRequisitions, 10000);
    });

    // ── Auto-hide alerts ────────────────────────────────────────────
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>

@stack('scripts')
</body>
</html>
