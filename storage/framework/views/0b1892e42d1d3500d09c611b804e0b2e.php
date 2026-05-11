<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Management Dashboard'); ?> - PaitoBella</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #ebecee;
        }
        .sidebar-active {
            background-color: #1e40af;
            color: white;
        }
        .sidebar-active svg {
            color: white;
        }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed;
            left: -280px;
            top: 0;
            width: 280px;
            height: 100%;
            transition: left 0.3s ease;
            z-index: 50;
            background-color: #1e3a8a;
            overflow-y: auto;
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
            color: #1e3a8a;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        #menuIconBtn:hover {
            background: #e5e7eb;
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
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>


<div class="sidebar-overlay" id="sidebarOverlay"></div>


<aside id="sidebar" class="text-white shadow-xl">

    <div class="p-4 border-b border-blue-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">PaitoBella</h2>
                <p class="text-xs text-blue-300 mt-1">General Manager Dashboard</p>
            </div>
            <button id="closeSidebarBtn"
                    class="text-white hover:text-gray-300 bg-transparent border-none cursor-pointer p-1 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="mt-6">
        <a href="<?php echo e(route('management.dashboard')); ?>"
           class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link
                  <?php echo e(request()->routeIs('management.dashboard') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="<?php echo e(route('management.reports.purchase-orders')); ?>"
           class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link
                  <?php echo e(request()->routeIs('management.reports.*') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Reports
        </a>

        <a href="<?php echo e(route('management.analytics.procurement')); ?>"
           class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link
                  <?php echo e(request()->routeIs('management.analytics.*') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Analytics
        </a>

        <a href="<?php echo e(route('management.vendors.index')); ?>"
           class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link
                  <?php echo e(request()->routeIs('management.vendors.*') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Vendors
        </a>

        <a href="<?php echo e(route('management.requisitions.index')); ?>"
           class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link
                  <?php echo e(request()->routeIs('management.requisitions.*') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Requisitions
        </a>
    </nav>

    
    <div class="absolute bottom-0 w-full p-4 border-t border-blue-700 bg-[#1e3a8a]">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold">
                    <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                </span>
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-sm font-medium truncate"><?php echo e(Auth::user()->name); ?></p>
                <p class="text-xs text-blue-300"><?php echo e(Auth::user()->role ?? 'Manager'); ?></p>
            </div>
        </div>

        <a href="<?php echo e(route('logout')); ?>"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="mt-3 flex items-center text-sm text-blue-300 hover:text-white transition">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </a>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="hidden">
            <?php echo csrf_field(); ?>
        </form>
    </div>
</aside>



<div class="top-bar">
    
    <div class="top-bar-left">
        <button id="menuIconBtn" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <div class="text-base font-semibold text-blue-900">Management Dashboard</div>
            <div class="text-xs text-gray-500">PaitoBella</div>
        </div>
    </div>

    
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600 hidden sm:inline"><?php echo $__env->yieldContent('page-title'); ?></span>
        <div class="w-8 h-8 bg-blue-700 rounded-full flex items-center justify-center text-white font-bold text-sm">
            <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

        </div>
    </div>
</div>



<main>
    <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</main>


<script>
    // ── Sidebar toggle ──────────────────────────────────────────────
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

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/layouts/management.blade.php ENDPATH**/ ?>