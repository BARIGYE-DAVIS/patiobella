<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Store Module'); ?> - <?php echo e(config('app.name')); ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
        }
        .sidebar-active {
            background-color: #1e40af;
            color: white;
        }
        .sidebar-active svg {
            color: white;
        }
        /* Sidebar hidden by default */
        #sidebar {
            position: fixed;
            left: -280px;
            top: 0;
            width: 280px;
            height: 100%;
            transition: left 0.3s ease;
            z-index: 50;
            background-color: #1e3a8a;
        }
        #sidebar.open {
            left: 0;
        }
        /* Overlay when sidebar is open */
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
        /* Custom Menu Button */
        .custom-menu-btn {
            background: transparent;
            color: #1e3a8a;
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
            background: #e5e7eb;
        }
        /* Top Bar - Fixed */
        .top-bar {
            background: white;
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
            color: #1e3a8a;
        }
        .department-subtitle {
            font-size: 12px;
            color: #6b7280;
        }
        /* Main content */
        main {
            margin-top: 65px;
            transition: margin-left 0.3s ease;
            padding: 20px;
        }
        /* When sidebar opens, shift main content */
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


<aside id="sidebar" class="text-white shadow-xl overflow-y-auto">
    <div class="p-4 border-b border-blue-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">STORE</h2>
                <p class="text-xs text-blue-300 mt-1">Store & Inventory</p>
            </div>
            <button id="closeSidebarBtn" class="text-white hover:text-gray-300 bg-transparent border-none cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="mt-6">
        <a href="<?php echo e(route('store.dashboard')); ?>" class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link <?php echo e(request()->routeIs('store.dashboard') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="<?php echo e(route('store.inventory.index')); ?>" class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link <?php echo e(request()->routeIs('store.inventory.*') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Inventory
        </a>

        <a href="<?php echo e(route('store.stock-movements.index')); ?>" class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Stock Movements
        </a>

        <a href="<?php echo e(route('store.requisitions.index')); ?>" class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link <?php echo e(request()->routeIs('store.requisitions.*') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Requisitions
        </a>

        <a href="<?php echo e(route('store.categories.index')); ?>" class="flex items-center px-4 py-3 text-sm hover:bg-blue-700 transition sidebar-nav-link <?php echo e(request()->routeIs('store.categories.*') ? 'sidebar-active' : ''); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            Categories
        </a>




    </nav>

    <div class="absolute bottom-0 w-full p-4 border-t border-blue-700">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center">
                <span class="text-sm font-bold"><?php echo e(substr(Auth::user()->first_name ?? 'U', 0, 1)); ?><?php echo e(substr(Auth::user()->last_name ?? '', 0, 1)); ?></span>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium"><?php echo e(Auth::user()->first_name); ?> <?php echo e(Auth::user()->last_name); ?></p>
                <p class="text-xs text-blue-300"><?php echo e(Auth::user()->role ?? 'Store Keeper'); ?></p>
            </div>
        </div>
        <a href="<?php echo e(route('logout')); ?>"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="mt-3 flex items-center text-sm text-blue-300 hover:text-white transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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
        <a href="<?php echo e(route('dashboard')); ?>" class="text-gray-600 hover:text-gray-800 text-sm">
            <i class="fas fa-tachometer-alt mr-1"></i> Main Dashboard
        </a>
        <div class="relative">
            <button class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-bell"></i>
            </button>
        </div>
    </div>
</div>


<main>
    <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</main>

<script>
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

    menuIconBtn.addEventListener('click', openSidebar);
    closeSidebarBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar when a navigation link is clicked
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            closeSidebar();
        });
    });
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\patiobella\resources\views/layouts/store.blade.php ENDPATH**/ ?>