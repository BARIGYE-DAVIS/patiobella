

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<!-- Only Super Admin sees these cards -->
<?php if(Auth::user()->is_super_admin || Auth::user()->can_create_users): ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Total Users Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-primary">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Users</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo e($usersCount); ?></p>
            </div>
            <div class="bg-primary bg-opacity-10 p-3 rounded-full">
                <i class="fas fa-users text-primary text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Active Users Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Active Users</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo e($activeUsersCount); ?></p>
            </div>
            <div class="bg-green-500 bg-opacity-10 p-3 rounded-full">
                <i class="fas fa-user-check text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Welcome Section -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-2">Welcome back, <?php echo e(Auth::user()->first_name); ?>!</h2>
    <p class="text-gray-600">
        You are logged in as <span class="font-medium"><?php echo e(ucfirst(Auth::user()->role)); ?></span>
        <?php if(Auth::user()->is_super_admin): ?>
            <span class="ml-2 bg-primary text-white text-xs px-2 py-1 rounded">Super Admin</span>
        <?php endif; ?>
    </p>
    
    <hr class="my-4">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <!-- Quick Actions - Only for Super Admin -->
        <?php if(Auth::user()->is_super_admin || Auth::user()->can_create_users): ?>
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="font-semibold text-gray-800 mb-2">Admin Actions</h3>
            <ul class="space-y-2">
                <li>
                    <a href="<?php echo e(route('users.index')); ?>" class="text-primary hover:underline">
                        <i class="fas fa-users mr-2"></i> Manage Users
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('users.create')); ?>" class="text-primary hover:underline">
                        <i class="fas fa-plus-circle mr-2"></i> Create New User
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>

        <!-- System Info - Visible to everyone -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="font-semibold text-gray-800 mb-2">System Info</h3>
            <ul class="space-y-2 text-sm">
                <li class="text-gray-600">
                    <i class="fas fa-calendar-alt mr-2 text-primary"></i> 
                    Today: <?php echo e(now()->format('F j, Y')); ?>

                </li>
                <li class="text-gray-600">
                    <i class="fas fa-clock mr-2 text-primary"></i> 
                    Time: <?php echo e(now()->format('g:i A')); ?>

                </li>
                <li class="text-gray-600">
                    <i class="fas fa-envelope mr-2 text-primary"></i> 
                    Logged in as: <?php echo e(Auth::user()->email); ?>

                </li>
                <li class="text-gray-600">
                    <i class="fas fa-shield-alt mr-2 text-primary"></i> 
                    Role: <?php echo e(ucfirst(Auth::user()->role)); ?>

                </li>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/dashboard.blade.php ENDPATH**/ ?>