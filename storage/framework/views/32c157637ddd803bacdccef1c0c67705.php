<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Patio Bella - Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1abc9c',
                        dark: '#2c3e50',
                    }
                }
            }
        }
    </script>
    <style>
        /* Sidebar — icon-only by default */
        #sidebar {
            width: 64px;
            transition: width 0.25s ease;
            overflow: hidden;
            flex-shrink: 0;
        }

        #sidebar.expanded {
            width: 240px;
        }

        /* Labels hidden until expanded */
        #sidebar .nav-label {
            opacity: 0;
            white-space: nowrap;
            transition: opacity 0.15s ease;
            pointer-events: none;
        }

        #sidebar.expanded .nav-label {
            opacity: 1;
            pointer-events: auto;
        }

        /* Brand text */
        #sidebar .brand-text {
            opacity: 0;
            max-width: 0;
            overflow: hidden;
            transition: opacity 0.15s ease, max-width 0.25s ease;
            white-space: nowrap;
        }

        #sidebar.expanded .brand-text {
            opacity: 1;
            max-width: 180px;
        }

        /* Logout label */
        #sidebar .logout-label {
            display: none;
        }

        #sidebar.expanded .logout-label {
            display: inline;
        }

        /* Tooltip on collapsed icons */
        #sidebar:not(.expanded) .nav-item {
            position: relative;
        }

        #sidebar:not(.expanded) .nav-item:hover::after {
            content: attr(data-tooltip);
            position: fixed;
            left: 70px;
            background: #2c3e50;
            color: #fff;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
            z-index: 9999;
            pointer-events: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">

        <?php if(auth()->guard()->check()): ?>
        <!-- ===== SIDEBAR ===== -->
        <div id="sidebar" class="bg-dark min-h-screen flex flex-col z-50">

            <!-- Brand (display only) -->
            <div class="flex items-center py-4 px-[18px] gap-3">
                <i class="fas fa-warehouse text-primary text-xl flex-shrink-0"></i>
                <div class="brand-text">
                    <p class="text-white font-bold text-sm leading-tight">Patio Bella</p>
                    <p class="text-gray-400 text-xs">Inventory System</p>
                </div>
            </div>

            <hr class="border-gray-700">

            <!-- Nav links — each with a Font Awesome icon + label -->
            <nav class="mt-2 flex-1">

                <a href="<?php echo e(route('dashboard')); ?>"
                   data-tooltip="Dashboard"
                   class="nav-item flex items-center px-[18px] py-3 gap-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors <?php echo e(request()->routeIs('dashboard') ? 'bg-primary text-white' : ''); ?>">
                    <i class="fas fa-tachometer-alt w-5 text-center flex-shrink-0"></i>
                    <span class="nav-label">Dashboard</span>
                </a>

                <a href="<?php echo e(route('users.index')); ?>"
                   data-tooltip="Users"
                   class="nav-item flex items-center px-[18px] py-3 gap-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors <?php echo e(request()->routeIs('users.*') ? 'bg-primary text-white' : ''); ?>">
                    <i class="fas fa-users w-5 text-center flex-shrink-0"></i>
                    <span class="nav-label">Users</span>
                </a>

                <a href="<?php echo e(route('departments.index')); ?>"
                   data-tooltip="Departments"
                   class="nav-item flex items-center px-[18px] py-3 gap-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors <?php echo e(request()->routeIs('departments.*') ? 'bg-primary text-white' : ''); ?>">
                    <i class="fas fa-building w-5 text-center flex-shrink-0"></i>
                    <span class="nav-label">Departments</span>
                </a>

                <a href="<?php echo e(route('roles.index')); ?>"
                   data-tooltip="Roles &amp; Permissions"
                   class="nav-item flex items-center px-[18px] py-3 gap-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors <?php echo e(request()->routeIs('roles.*') ? 'bg-primary text-white' : ''); ?>">
                    <i class="fas fa-user-shield w-5 text-center flex-shrink-0"></i>
                    <span class="nav-label">Roles &amp; Permissions</span>
                </a>

            </nav>

            <hr class="border-gray-700">

            <!-- Logout -->
            <div class="p-3">
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            data-tooltip="Logout"
                            class="nav-item w-full bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 transition flex items-center justify-center gap-2">
                        <i class="fas fa-sign-out-alt flex-shrink-0"></i>
                        <span class="logout-label text-sm">Logout</span>
                    </button>
                </form>
            </div>

        </div>
        <!-- ===== END SIDEBAR ===== -->
        <?php endif; ?>

        <!-- ===== MAIN AREA ===== -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Navbar -->
            <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
                <div class="px-4 py-3 flex items-center justify-between gap-4">

                    <!-- Left: hamburger icon + page title -->
                    <div class="flex items-center gap-3">
                        <?php if(auth()->guard()->check()): ?>
                        <button id="sidebarToggle"
                                class="text-gray-600 hover:text-gray-900 focus:outline-none w-9 h-9 flex items-center justify-center rounded hover:bg-gray-100 transition-colors"
                                aria-label="Toggle menu">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <?php endif; ?>
                        <h1 class="text-xl font-semibold text-gray-800"><?php echo $__env->yieldContent('title', 'Patio Bella'); ?></h1>
                    </div>

                    <!-- Right: user dropdown -->
                    <?php if(auth()->guard()->check()): ?>
                    <div class="relative">
                        <button class="flex items-center text-gray-700 hover:text-gray-900 focus:outline-none gap-2" id="userMenuButton">
                            <i class="fas fa-user-circle text-2xl"></i>
                            <span class="hidden sm:inline text-sm"><?php echo e(Auth::user()->first_name); ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50" id="userMenuDropdown">
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user w-4 text-center"></i> Profile
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt w-4 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </nav>

            <!-- Page Content -->
            <div class="p-6 flex-1">

                <?php if(session('success')): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded flex items-start justify-between" role="alert">
                        <span><i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?></span>
                        <button type="button" class="text-green-700 ml-4" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded flex items-start justify-between" role="alert">
                        <span><i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?></span>
                        <button type="button" class="text-red-700 ml-4" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </div>

        </div>
        <!-- ===== END MAIN AREA ===== -->

    </div>

    <script>
        const sidebar  = document.getElementById('sidebar');
        const toggle   = document.getElementById('sidebarToggle');
        const navLinks = sidebar ? sidebar.querySelectorAll('nav a') : [];

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('expanded');
        }

        // Hamburger — toggle open / close
        if (toggle) {
            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('expanded');
            });
        }

        // Close on nav link click
        navLinks.forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });

        // Close on scroll
        window.addEventListener('scroll', closeSidebar, { passive: true });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (sidebar && toggle && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                closeSidebar();
            }
        });

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

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/layouts/app.blade.php ENDPATH**/ ?>