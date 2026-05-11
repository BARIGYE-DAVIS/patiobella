<?php $__env->startSection('title', 'Management Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="space-y-6">
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="<?php echo e(route('management.requisitions.index', ['tab' => 'pending'])); ?>"
           class="bg-white rounded-xl shadow-md p-6 stat-card block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Requisitions</p>
                    <p class="text-2xl font-bold text-yellow-600"><?php echo e($pendingCount); ?></p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('management.requisitions.index', ['tab' => 'approved'])); ?>"
           class="bg-white rounded-xl shadow-md p-6 stat-card block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Approved Requisitions</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo e($approvedCount); ?></p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('management.requisitions.index', ['tab' => 'rejected'])); ?>"
           class="bg-white rounded-xl shadow-md p-6 stat-card block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Rejected Requisitions</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo e($rejectedCount); ?></p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('management.requisitions.index', ['tab' => 'all'])); ?>"
           class="bg-white rounded-xl shadow-md p-6 stat-card block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Requisitions</p>
                    <p class="text-2xl font-bold text-blue-600"><?php echo e($totalCount); ?></p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
            <h3 class="text-lg font-semibold text-white">Recent Requisitions</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Requisition #</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                        <th class="p-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="p-3 text-right text-xs font-medium text-gray-500 uppercase">Total Qty</th>
                        <th class="p-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="p-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $recentRequisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-mono font-semibold"><?php echo e($req->requisition_number); ?></td>
                        <td class="p-3"><?php echo e($req->store->name ?? 'N/A'); ?></td>
                        <td class="p-3"><?php echo e($req->requestedBy ? $req->requestedBy->first_name . ' ' . $req->requestedBy->last_name : 'N/A'); ?></td>
                        <td class="p-3 text-center"><?php echo e($req->created_at->format('Y-m-d')); ?></td>
                        <td class="p-3 text-right"><?php echo e(number_format($req->items->sum('quantity_requested'), 2)); ?></td>
                        <td class="p-3 text-center">
                            <?php
                                $statusClass = 'bg-gray-100 text-gray-800';
                                if ($req->status == 'pending') {
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                } elseif ($req->status == 'approved') {
                                    $statusClass = 'bg-green-100 text-green-800';
                                } elseif ($req->status == 'rejected') {
                                    $statusClass = 'bg-red-100 text-red-800';
                                }
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($statusClass); ?>">
                                <?php echo e(ucfirst($req->status)); ?>

                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <a href="<?php echo e(route('management.requisitions.show', $req->id)); ?>" class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">No requisitions found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.management', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/management/dashboard.blade.php ENDPATH**/ ?>