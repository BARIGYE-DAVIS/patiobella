
<?php $__env->startSection('title', 'Local Purchase Orders'); ?>
<?php $__env->startSection('page-title', 'Local Purchase Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="<?php echo e(route('director.lpos.index')); ?>?tab=pending" 
                   class="px-6 py-3 text-sm font-medium <?php echo e(request('tab', 'pending') == 'pending' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                    Pending Approval
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">
                        <?php echo e($pendingCount); ?>

                    </span>
                </a>
                <a href="<?php echo e(route('director.lpos.index')); ?>?tab=approved" 
                   class="px-6 py-3 text-sm font-medium <?php echo e(request('tab') == 'approved' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                    Approved
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">
                        <?php echo e($approvedCount); ?>

                    </span>
                </a>
                <a href="<?php echo e(route('director.lpos.index')); ?>?tab=rejected" 
                   class="px-6 py-3 text-sm font-medium <?php echo e(request('tab') == 'rejected' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                    Rejected
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800">
                        <?php echo e($rejectedCount); ?>

                    </span>
                </a>
            </nav>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-red-800 to-red-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">
                <?php if(request('tab', 'pending') == 'pending'): ?> LPOs Pending Approval
                <?php elseif(request('tab') == 'approved'): ?> Approved LPOs
                <?php elseif(request('tab') == 'rejected'): ?> Rejected LPOs
                <?php endif; ?>
            </h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border text-left">LPO Number</th>
                        <th class="p-3 border text-left">Requisition #</th>
                        <th class="p-3 border text-left">Vendor</th>
                        <th class="p-3 border text-center">LPO Date</th>
                        <th class="p-3 border text-right">Total Amount</th>
                        <th class="p-3 border text-center">Status</th>
                        <th class="p-3 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $lpos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lpo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 border font-mono font-semibold"><?php echo e($lpo->lpo_number); ?></td>
                        <td class="p-3 border"><?php echo e($lpo->requisition->requisition_number ?? 'N/A'); ?></td>
                        <td class="p-3 border"><?php echo e($lpo->vendor->name ?? 'N/A'); ?></td>
                        <td class="p-3 border text-center"><?php echo e($lpo->lpo_date->format('Y-m-d')); ?></td>
                        <td class="p-3 border text-right font-semibold text-green-600">UGX <?php echo e(number_format($lpo->total_amount, 2)); ?></td>
                        <td class="p-3 border text-center">
                            <?php if($lpo->status == 'pending_director'): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            <?php elseif($lpo->status == 'director_approved'): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 border text-center">
                            <a href="<?php echo e(route('director.lpos.show', $lpo->id)); ?>" class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">No LPOs found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="mt-4">
                <?php echo e($lpos->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.director', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/director/lpos/index.blade.php ENDPATH**/ ?>