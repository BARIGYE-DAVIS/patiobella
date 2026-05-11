<?php $__env->startSection('title', 'Inventory Item Details'); ?>

<?php $__env->startSection('page-title', 'Inventory Item Details'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .detail-card {
        background-color: #f9fafb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .detail-label {
        font-weight: 600;
        color: #4b5563;
        width: 160px;
        display: inline-block;
    }
    .pack-badge {
        display: inline-block;
        background-color: #fef3c7;
        color: #92400e;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .stock-low {
        color: #ef4444;
        background-color: #fee2e2;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        font-size: 12px;
        font-weight: 600;
    }
    .stock-normal {
        color: #10b981;
        background-color: #d1fae5;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        font-size: 12px;
        font-weight: 600;
    }
    .stock-out {
        color: #6b7280;
        background-color: #f3f4f6;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        font-size: 12px;
        font-weight: 600;
    }
    .movement-row {
        transition: background-color 0.2s;
    }
    .movement-row:hover {
        background-color: #f9fafb;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800"><?php echo e($item->name); ?></h3>
            <p class="text-sm text-gray-500">Item Code: <?php echo e($item->item_code ?? $item->code ?? 'N/A'); ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('store.inventory.index')); ?>" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <a href="<?php echo e(route('store.inventory.edit', $item->id)); ?>" class="text-amber-600 hover:text-amber-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    <div class="p-6">
        
        <div class="mb-6 flex flex-wrap gap-2">
            <?php if($item->is_active): ?>
                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">✓ Active</span>
            <?php else: ?>
                <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">✗ Inactive</span>
            <?php endif; ?>
            <?php if($item->is_perishable): ?>
                <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">📅 Perishable</span>
            <?php endif; ?>

            <?php
                $currentStock = $item->current_stock ?? 0;
                $minStock = $item->minimum_stock ?? 0;
                if ($currentStock <= 0) {
                    echo '<span class="stock-out">📦 Out of Stock</span>';
                } elseif ($minStock > 0 && $currentStock <= $minStock) {
                    echo '<span class="stock-low">⚠️ Low Stock</span>';
                } else {
                    echo '<span class="stock-normal">✅ In Stock</span>';
                }
            ?>
        </div>

        
        <div class="detail-card">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Item Information
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div>
                        <span class="detail-label">Item Name:</span>
                        <span class="font-semibold text-gray-800"><?php echo e($item->name); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Item Code:</span>
                        <span class="font-mono text-sm"><?php echo e($item->item_code ?? 'N/A'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Barcode:</span>
                        <span><?php echo e($item->barcode ?? '—'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Category:</span>
                        <span><?php echo e($item->category ? $item->category->name : '—'); ?></span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <span class="detail-label">Unit of Measure (Base):</span>
                        <span class="pack-badge"><?php echo e($item->default_unit_of_measure_id ?? 'pieces'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Current Stock:</span>
                        <span class="font-semibold text-blue-600"><?php echo e(number_format($currentStock, 2)); ?> <?php echo e($item->default_unit_of_measure_id ?? 'units'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Unit Cost:</span>
                        <span>UGX <?php echo e(number_format($item->unit_cost ?? 0, 2)); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Selling Price:</span>
                        <span>UGX <?php echo e(number_format($item->selling_price ?? 0, 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="detail-card">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Stock Settings
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-2"><span class="detail-label">Minimum Stock:</span><span><?php echo e(number_format($item->minimum_stock ?? 0, 2)); ?> <?php echo e($item->default_unit_of_measure_id ?? 'units'); ?></span></div>
                    <div class="mb-2"><span class="detail-label">Maximum Stock:</span><span><?php echo e(number_format($item->maximum_stock ?? 0, 2)); ?> <?php echo e($item->default_unit_of_measure_id ?? 'units'); ?></span></div>
                </div>
                <div>
                    <div class="mb-2"><span class="detail-label">Reorder Quantity:</span><span><?php echo e(number_format($item->reorder_quantity ?? 0, 2)); ?> <?php echo e($item->default_unit_of_measure_id ?? 'units'); ?></span></div>
                    <?php if($item->shelf_life_days): ?><div class="mb-2"><span class="detail-label">Shelf Life:</span><span><?php echo e($item->shelf_life_days); ?> days</span></div><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="detail-card">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Supplier Information
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><span class="detail-label">Manufacturer:</span><span><?php echo e($item->manufacturer ?? '—'); ?></span></div>
                <div><span class="detail-label">Brand:</span><span><?php echo e($item->brand ?? '—'); ?></span></div>
            </div>
        </div>

        
        <?php if($item->notes): ?>
        <div class="detail-card">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Notes
            </h4>
            <div class="bg-white rounded-lg p-4 border">
                <p class="text-gray-700"><?php echo e($item->notes); ?></p>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="detail-card">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Recent Stock Movements
            </h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg text-sm">
                    <thead class="bg-gray-50">
                        <tr class="border-b">
                            <th class="px-4 py-2 text-left">Movement #</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-center">Quantity Received</th>
                            <th class="px-4 py-2 text-center">Breakdown</th>
                            <th class="px-4 py-2 text-center">Total Pieces</th>
                            <th class="px-4 py-2 text-center">Date</th>
                            <th class="px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $stockMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="movement-row border-b">
                            <td class="px-4 py-2 font-mono text-xs"><?php echo e($movement->movement_number); ?></td>
                            <td class="px-4 py-2">
                                <?php if($movement->movementType->sign == '+'): ?>
                                    <span class="text-green-600">Stock In</span>
                                <?php else: ?>
                                    <span class="text-red-600">Stock Out</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <?php if($movement->pack_type): ?>
                                    <span class="font-semibold text-amber-600"><?php echo e(number_format($movement->number_of_packs)); ?></span>
                                <?php else: ?>
                                    <span class="font-semibold"><?php echo e(number_format($movement->quantity, 2)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <?php if($movement->pack_type): ?>
                                    <div class="font-semibold text-amber-600">
                                        <?php echo e(number_format($movement->number_of_packs)); ?> <?php echo e(ucfirst($movement->pack_type)); ?>(s)
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        × <?php echo e(number_format($movement->pack_size)); ?> pieces per <?php echo e($movement->pack_type); ?>

                                    </div>
                                <?php else: ?>
                                    <div class="text-gray-500 text-sm">
                                        <?php echo e(number_format($movement->quantity, 2)); ?> <?php echo e($item->default_unit_of_measure_id ?? 'units'); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 text-center font-bold text-blue-600">
                                <?php echo e(number_format($movement->quantity_in_base_unit, 2)); ?> pieces
                            </td>
                            <td class="px-4 py-2 text-center"><?php echo e($movement->movement_date->format('Y-m-d')); ?></td>
                            <td class="px-4 py-2 text-center">
                                <a href="<?php echo e(route('store.stock-movements.show', $movement->id)); ?>" class="text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No stock movements found for this item.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="detail-card">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">Created By</span>
                    <div class="font-medium"><?php echo e($item->creator ? $item->creator->first_name . ' ' . $item->creator->last_name : 'System'); ?></div>
                    <div class="text-xs text-gray-400"><?php echo e($item->created_at ? $item->created_at->format('F d, Y g:i A') : 'N/A'); ?></div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Last Updated By</span>
                    <div class="font-medium"><?php echo e($item->updater ? $item->updater->first_name . ' ' . $item->updater->last_name : 'Never'); ?></div>
                    <div class="text-xs text-gray-400"><?php echo e($item->updated_at ? $item->updated_at->format('F d, Y g:i A') : 'N/A'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/store/inventory/show.blade.php ENDPATH**/ ?>