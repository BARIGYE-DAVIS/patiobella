<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Waiter Module') - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #ebecee;
        }
        .sidebar-active {
            background-color: #ea580c;
            color: white;
        }
        .sidebar-active svg {
            color: white;
        }
        .sidebar-active i {
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
            color: #9a3412;
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

        .no-print {
            print-color-adjust: exact;
        }

        .toast-notification {
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside id="sidebar" class="text-white shadow-xl">
    <div class="p-4 border-b border-orange-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">WAITER</h2>
                <p class="text-xs text-orange-300 mt-1">Food & Beverage Service</p>
            </div>
            <button id="closeSidebarBtn" class="text-white hover:text-gray-300 bg-transparent border-none cursor-pointer p-1 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="mt-6">
        <a href="{{ route('waiter.dashboard') }}"
           class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                  {{ request()->routeIs('waiter.dashboard') ? 'sidebar-active' : '' }}">
            <i class="fas fa-concierge-bell w-5 h-5 mr-3"></i>
            Take Order
        </a>

        <a href="{{ route('waiter.bills.index') }}"
           class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link
                  {{ request()->routeIs('waiter.bills.*') ? 'sidebar-active' : '' }}">
            <i class="fas fa-receipt w-5 h-5 mr-3"></i>
            Bills
        </a>

        <a href="{{ route('waiter.active-orders') }} "
           class="flex items-center px-4 py-3 text-sm hover:bg-orange-700 transition sidebar-nav-link hidden
                  {{ request()->routeIs('waiter.active-orders') ? 'sidebar-active' : '' }}">
            <i class="fas fa-clipboard-list w-5 h-5 mr-3"></i>
            Active Orders
        </a>
    </nav>

    <div class="absolute bottom-0 w-full p-4 border-t border-orange-700 bg-[#9a3412]">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold">
                    {{ substr(Auth::user()->first_name ?? 'W', 0, 1) }}{{ substr(Auth::user()->last_name ?? '', 0, 1) }}
                </span>
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-sm font-medium truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                <p class="text-xs text-orange-300">Waiter</p>
            </div>
        </div>

        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="mt-3 flex items-center text-sm text-orange-300 hover:text-white transition">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    </div>
</aside>

<div class="top-bar no-print">
    <div class="top-bar-left">
        <button id="menuIconBtn" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <div class="text-base font-semibold text-orange-900">Waiter Panel</div>
            <div class="text-xs text-gray-500">Order Management</div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-600 hidden sm:block">
            <i class="fas fa-clock mr-1"></i>
            <span id="liveClock"></span>
        </div>

        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800 text-sm hidden sm:inline-flex items-center gap-1">
            <i class="fas fa-tachometer-alt"></i>
            <span>Main Dashboard</span>
        </a>

        <div class="relative">
            <button id="notificationBell" class="relative text-gray-600 hover:text-gray-800 p-1" aria-label="Notifications">
                <i class="fas fa-bell text-lg"></i>
                <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 hidden">0</span>
            </button>
        </div>
    </div>
</div>

<main>
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

<div id="toastContainer" class="fixed bottom-6 right-6 z-50 space-y-2"></div>

<script>
    function updateClock() {
        const clock = document.getElementById('liveClock');
        if (clock) {
            clock.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
    }
    updateClock();
    setInterval(updateClock, 10000);

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuIconBtn = document.getElementById('menuIconBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const navLinks = document.querySelectorAll('.sidebar-nav-link');

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

    if (menuIconBtn) menuIconBtn.addEventListener('click', openSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    navLinks.forEach(link => link.addEventListener('click', closeSidebar));

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-orange-500');
        const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');

        toast.className = `toast-notification ${bgColor} text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[250px]`;
        toast.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;
        document.getElementById('toastContainer').appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function fetchActiveOrdersCount() {
        fetch('{{ route("waiter.active-orders") }}')
            .then(response => response.json())
            .then(orders => {
                const badge = document.getElementById('notificationBadge');
                if (orders && orders.length > 0) {
                    badge.textContent = orders.length;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            })
            .catch(err => console.error('Error fetching orders:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchActiveOrdersCount();
        setInterval(fetchActiveOrdersCount, 30000);
    });
</script>

@stack('scripts')
</body>
</html>
