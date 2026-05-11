
<?php $__env->startSection('title', 'Requisitions Management'); ?>
<?php $__env->startSection('page-title', 'Requisitions Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="<?php echo e(route('management.requisitions.index')); ?>?tab=pending" 
                   class="px-6 py-3 text-sm font-medium <?php echo e(request('tab', 'pending') == 'pending' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                    Pending Approval
                </a>
                <a href="<?php echo e(route('management.requisitions.index')); ?>?tab=approved" 
                   class="px-6 py-3 text-sm font-medium <?php echo e(request('tab') == 'approved' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                    Approved
                </a>
                <a href="<?php echo e(route('management.requisitions.index')); ?>?tab=rejected" 
                   class="px-6 py-3 text-sm font-medium <?php echo e(request('tab') == 'rejected' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                    Rejected
                </a>
                <a href="<?php echo e(route('management.requisitions.index')); ?>?tab=all" 
                   class="px-6 py-3 text-sm font-medium <?php echo e(request('tab') == 'all' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                    All Requisitions
                </a>
            </nav>
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 border-b">
            <form method="GET" action="<?php echo e(route('management.requisitions.index')); ?>" class="flex flex-wrap gap-4 items-end">
                <input type="hidden" name="tab" value="<?php echo e(request('tab', 'pending')); ?>">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" 
                           class="form-input border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" 
                           class="form-input border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="Requisition # or Store..." 
                           class="form-input border-gray-300 rounded-lg w-64">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="<?php echo e(route('management.requisitions.index')); ?>?tab=<?php echo e(request('tab', 'pending')); ?>" 
                       class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">
                <?php if(request('tab', 'pending') == 'pending'): ?> Requisitions Pending Approval
                <?php elseif(request('tab') == 'approved'): ?> Approved Requisitions
                <?php elseif(request('tab') == 'rejected'): ?> Rejected Requisitions
                <?php else: ?> All Requisitions
                <?php endif; ?>
            </h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border text-left">Req #</th>
                        <th class="p-3 border text-left">Store</th>
                        <th class="p-3 border text-left">Requested By</th>
                        <th class="p-3 border text-center">Date</th>
                        <th class="p-3 border text-center">Date Needed</th>
                        <th class="p-3 border text-center">Items</th>
                        <th class="p-3 border text-right">Total Qty</th>
                        <th class="p-3 border text-center">Status</th>
                        <th class="p-3 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $requisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 border font-semibold"><?php echo e($req->requisition_number); ?></td>
                        <td class="p-3 border"><?php echo e($req->store->name ?? 'N/A'); ?></td>
                        <td class="p-3 border">
                            <?php echo e($req->requestedBy ? $req->requestedBy->first_name . ' ' . $req->requestedBy->last_name : 'N/A'); ?>

                        </td>
                        <td class="p-3 border text-center"><?php echo e($req->created_at->format('Y-m-d')); ?></td>
                        <td class="p-3 border text-center"><?php echo e($req->date_needed ? date('Y-m-d', strtotime($req->date_needed)) : 'N/A'); ?></td>
                        <td class="p-3 border text-center"><?php echo e($req->items->count()); ?></td>
                        <td class="p-3 border text-right"><?php echo e(number_format($req->items->sum('quantity_requested'), 2)); ?></td>
                        <td class="p-3 border text-center">
                            <?php
                                $statusClass = 'bg-gray-100 text-gray-800';
                                $statusText = ucfirst(str_replace('_', ' ', $req->status));
                                if ($req->status == 'pending') {
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                } elseif ($req->status == 'approved') {
                                    $statusClass = 'bg-green-100 text-green-800';
                                } elseif ($req->status == 'rejected') {
                                    $statusClass = 'bg-red-100 text-red-800';
                                } elseif ($req->status == 'fulfilled') {
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                }
                            ?>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($statusClass); ?>">
                                <?php echo e($statusText); ?>

                            </span>
                        </td>
                        <td class="p-3 border text-center">
                            <a href="<?php echo e(route('management.requisitions.show', $req->id)); ?>" 
                               class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="p-8 text-center text-gray-500">No requisitions found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="mt-4">
                <?php echo e($requisitions->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.management', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/management/requisitions/index.blade.php ENDPATH**/ ?>