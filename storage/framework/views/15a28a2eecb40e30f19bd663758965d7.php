
<?php $__env->startSection('title', 'Purchase Orders'); ?>
<?php $__env->startSection('page-title', 'Purchase Orders'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
    <div class="alert alert-success mb-4"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="mb-4 flex justify-between items-center">
    <a href="<?php echo e(route('procurement.purchase-orders.create')); ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Create Purchase Order
    </a>
</div>

<div class="bg-white rounded shadow-sm p-4 overflow-x-auto">
    <table class="min-w-full table-auto border border-gray-200">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-2 border">PO Number</th>
                <th class="px-4 py-2 border">Vendor</th>
                <th class="px-4 py-2 border">PO Date</th>
                <th class="px-4 py-2 border">Expected Delivery</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Total Amount</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-4 py-2 border font-mono"><?php echo e($po->po_number); ?></td>
                    <td class="px-4 py-2 border"><?php echo e($po->vendor->name ?? '—'); ?></td>
                    <td class="px-4 py-2 border"><?php echo e(\Carbon\Carbon::parse($po->po_date)->format('Y-m-d')); ?></td>
                    <td class="px-4 py-2 border">
                        <?php echo e($po->expected_delivery_date ? \Carbon\Carbon::parse($po->expected_delivery_date)->format('Y-m-d') : '—'); ?>

                    </td>
                    <td class="px-4 py-2 border">
                        <span class="inline-block px-2 py-1 text-xs rounded
                            <?php if($po->status=='draft'): ?> bg-yellow-100 text-yellow-800
                            <?php elseif($po->status=='approved'): ?> bg-green-100 text-green-800
                            <?php elseif($po->status=='cancelled'): ?> bg-red-100 text-red-800
                            <?php elseif($po->status=='sent'): ?> bg-blue-100 text-blue-800
                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                            <?php echo e(ucfirst($po->status)); ?>

                        </span>
                    </td>
                    <td class="px-4 py-2 border text-right font-mono"><?php echo e(number_format($po->total_amount, 2)); ?></td>
                    <td class="px-4 py-2 border">
                        <a href="<?php echo e(route('procurement.purchase-orders.show', $po->id)); ?>" class="text-blue-600 hover:underline">View</a>
                        <a href="<?php echo e(route('procurement.purchase-orders.edit', $po->id)); ?>" class="ml-1 text-green-600 hover:underline">Edit</a>
                        <form action="<?php echo e(route('procurement.purchase-orders.destroy', $po->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this PO?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="ml-1 text-red-600 hover:underline bg-transparent border-none p-0">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 border text-center text-gray-500">No purchase orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="mt-4">
        <?php echo e($purchaseOrders->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.procurement', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/procurement/purchase_orders/index.blade.php ENDPATH**/ ?>