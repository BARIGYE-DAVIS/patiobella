<?php $__env->startSection('title', 'Stock Movements'); ?>

<?php $__env->startSection('page-title', 'Stock Movements History'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .filter-card {
        background-color: #f9fafb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .movement-in {
        color: #10b981;
    }
    .movement-out {
        color: #ef4444;
    }
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-purchase {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .badge-sale {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .badge-adjustment {
        background-color: #fef3c7;
        color: #92400e;
    }
    .badge-transfer {
        background-color: #e0e7ff;
        color: #3730a3;
    }
    .badge-grn {
        background-color: #d1fae5;
        color: #065f46;
    }
    .view-btn {
        color: #3b82f6;
        transition: color 0.2s;
    }
    .view-btn:hover {
        color: #2563eb;
    }
    .pack-display {
        font-weight: 600;
        color: #d97706;
    }
    .direct-display {
        color: #059669;
        font-weight: 500;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Stock Movements</h3>
        <p class="text-sm text-gray-500">Track all stock in and out movements</p>
    </div>

    
    <div class="filter-card mx-6 mt-4">
        <form method="GET" action="<?php echo e(route('store.stock-movements.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                <select name="item_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- All Items --</option>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id); ?>" <?php echo e(request('item_id') == $item->id ? 'selected' : ''); ?>>
                            <?php echo e($item->name); ?> (<?php echo e($item->item_code); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Movement Type</label>
                <select name="movement_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- All Types --</option>
                    <?php $__currentLoopData = $movementTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->id); ?>" <?php echo e(request('movement_type_id') == $type->id ? 'selected' : ''); ?>>
                            <?php echo e($type->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                <a href="<?php echo e(route('store.stock-movements.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>

    
    <div class="p-6 overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Movement #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity Received</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Pieces</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Value</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono"><?php echo e($movement->movement_number); ?></td>
                    <td class="px-4 py-3 text-sm">
                        <span class="font-semibold"><?php echo e($movement->inventoryItem->name ?? 'N/A'); ?></span>
                        <br>
                        <span class="text-xs text-gray-500"><?php echo e($movement->inventoryItem->item_code ?? ''); ?></span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <?php
                            $typeClass = 'badge-adjustment';
                            if (str_contains($movement->movementType->name ?? '', 'Purchase')) $typeClass = 'badge-purchase';
                            elseif (str_contains($movement->movementType->name ?? '', 'Sale')) $typeClass = 'badge-sale';
                            elseif (str_contains($movement->movementType->name ?? '', 'GRN')) $typeClass = 'badge-grn';
                            elseif (str_contains($movement->movementType->name ?? '', 'Transfer')) $typeClass = 'badge-transfer';
                        ?>
                        <span class="badge <?php echo e($typeClass); ?>"><?php echo e($movement->movementType->name ?? 'N/A'); ?></span>
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        <?php if($movement->pack_type): ?>
                            <div class="pack-display">
                                <?php echo e(number_format($movement->number_of_packs)); ?>

                                <?php echo e(ucfirst($movement->pack_type)); ?>(s)
                                <span class="text-gray-500 text-xs">× <?php echo e(number_format($movement->pack_size)); ?> pcs</span>
                            </div>
                        <?php else: ?>
                            <div class="direct-display">
                                <?php echo e(number_format($movement->quantity, 2)); ?>

                                <span class="text-gray-500 text-xs"><?php echo e($movement->inventoryItem->default_unit_of_measure_id ?? 'units'); ?></span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-center font-bold text-blue-600">
                        <?php echo e(number_format($movement->quantity_in_base_unit, 2)); ?>

                        <span class="text-xs text-gray-500">pieces</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-right">UGX <?php echo e(number_format($movement->unit_cost ?? 0, 2)); ?></td>
                    <td class="px-4 py-3 text-sm text-right">UGX <?php echo e(number_format($movement->total_value ?? 0, 2)); ?></td>
                    <td class="px-4 py-3 text-sm text-center"><?php echo e($movement->movement_date->format('Y-m-d')); ?></td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?php echo e(route('store.stock-movements.show', $movement->id)); ?>"
                           class="view-btn inline-flex items-center gap-1" title="View Details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">No stock movements found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="mt-4">
            <?php echo e($movements->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/store/stock_movements/index.blade.php ENDPATH**/ ?>