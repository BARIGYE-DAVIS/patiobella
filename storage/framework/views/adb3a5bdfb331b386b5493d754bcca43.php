<?php $__env->startSection('title', 'Stock Movement Details'); ?>

<?php $__env->startSection('page-title', 'Stock Movement Details'); ?>

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
    .movement-in {
        color: #10b981;
        background-color: #d1fae5;
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-block;
    }
    .movement-out {
        color: #ef4444;
        background-color: #fee2e2;
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-block;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Movement #<?php echo e($movement->movement_number); ?></h3>
            <p class="text-sm text-gray-500">Recorded on <?php echo e($movement->created_at->format('F d, Y g:i A')); ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('store.stock-movements.index')); ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Back to List
            </a>
        </div>
    </div>

    <div class="p-6">
        
        <div class="detail-card">
            <div class="flex justify-between items-center flex-wrap gap-3">
                <div>
                    <span class="text-gray-500 text-sm">Movement Type</span>
                    <div class="mt-1">
                        <?php if($movement->movementType->sign == '+'): ?>
                            <span class="movement-in">📥 STOCK IN (+)</span>
                        <?php else: ?>
                            <span class="movement-out">📤 STOCK OUT (-)</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Status</span>
                    <div class="mt-1">
                        <?php if($movement->approved_at): ?>
                            <span class="text-green-600">✓ Approved</span>
                        <?php else: ?>
                            <span class="text-yellow-600">⏳ Pending Approval</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Movement Date</span>
                    <div class="mt-1 font-semibold"><?php echo e($movement->movement_date->format('F d, Y')); ?></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="detail-card">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Item Information
                </h4>
                <div class="space-y-3">
                    <div>
                        <span class="detail-label">Item Name:</span>
                        <span class="font-semibold text-gray-800"><?php echo e($movement->inventoryItem->name ?? 'N/A'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Item Code:</span>
                        <span><?php echo e($movement->inventoryItem->item_code ?? 'N/A'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Category:</span>
                        <span><?php echo e($movement->inventoryItem->category->name ?? 'N/A'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Current Stock:</span>
                        <span class="font-semibold text-blue-600"><?php echo e(number_format($movement->inventoryItem->current_stock ?? 0, 2)); ?> units</span>
                    </div>
                </div>
            </div>

            
            <div class="detail-card">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Movement Details
                </h4>
                <div class="space-y-3">
                    <div>
                        <span class="detail-label">Movement Number:</span>
                        <span class="font-mono text-sm"><?php echo e($movement->movement_number); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Movement Type:</span>
                        <span><?php echo e($movement->movementType->name ?? 'N/A'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Approved By:</span>
                        <span><?php echo e($movement->approvedBy->name ?? 'System'); ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Approved At:</span>
                        <span><?php echo e($movement->approved_at ? date('F d, Y g:i A', strtotime($movement->approved_at)) : '—'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="detail-card">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Quantity Details
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="bg-white rounded-lg p-4 border">
                    <p class="text-sm text-gray-500 mb-2">Quantity Received/Issued</p>
                    <?php if($movement->pack_type): ?>
                        <div class="text-2xl font-bold text-amber-600">
                            <?php echo e(number_format($movement->number_of_packs)); ?> <?php echo e(ucfirst($movement->pack_type)); ?>(s)
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            × <?php echo e(number_format($movement->pack_size)); ?> pieces per <?php echo e($movement->pack_type); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-2xl font-bold text-green-600">
                            <?php echo e(number_format($movement->quantity, 2)); ?>

                            <span class="text-sm text-gray-500"><?php echo e($movement->inventoryItem->default_unit_of_measure_id ?? 'units'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="bg-white rounded-lg p-4 border">
                    <p class="text-sm text-gray-500 mb-2">Total Pieces (Base Unit)</p>
                    <div class="text-2xl font-bold text-blue-600">
                        <?php echo e(number_format($movement->quantity_in_base_unit, 2)); ?>

                        <span class="text-sm text-gray-500">pieces</span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="detail-card hidden">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Financial Details
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4 border">
                    <p class="text-sm text-gray-500 mb-2">Unit Cost</p>
                    <div class="text-xl font-semibold">UGX <?php echo e(number_format($movement->unit_cost ?? 0, 2)); ?></div>
                </div>
                <div class="bg-white rounded-lg p-4 border">
                    <p class="text-sm text-gray-500 mb-2">Total Value</p>
                    <div class="text-xl font-semibold text-green-600">UGX <?php echo e(number_format($movement->total_value ?? 0, 2)); ?></div>
                </div>
            </div>
        </div>

        
        <?php if($movement->reason): ?>
        <div class="detail-card">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Reason / Notes
            </h4>
            <div class="bg-white rounded-lg p-4 border">
                <p class="text-gray-700"><?php echo e($movement->reason); ?></p>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="detail-card">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reference Documents
            </h4>
            <div class="bg-white rounded-lg p-4 border">
                <?php if($movement->purchase_order_id): ?>
                    <p class="mb-1"><span class="font-medium">Purchase Order ID:</span> <?php echo e($movement->purchase_order_id); ?></p>
                <?php endif; ?>
                <?php if($movement->goods_received_note_id): ?>
                    <p class="mb-1"><span class="font-medium">GRN ID:</span> <?php echo e($movement->goods_received_note_id); ?></p>
                <?php endif; ?>
                <?php if($movement->reversed_by_movement_id): ?>
                    <p class="mb-1"><span class="font-medium">Reversed by Movement:</span> <?php echo e($movement->reversed_by_movement_id); ?></p>
                <?php endif; ?>
                <?php if(!$movement->purchase_order_id && !$movement->goods_received_note_id && !$movement->reversed_by_movement_id): ?>
                    <p class="text-gray-500">No reference documents attached</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="detail-card">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">Created By</span>
                    <div class="font-medium"><?php echo e($movement->createdBy->name ?? 'System'); ?></div>
                    <div class="text-xs text-gray-400"><?php echo e($movement->created_at->format('F d, Y g:i A')); ?></div>
                </div>
                <?php if($movement->updated_at != $movement->created_at): ?>
                <div>
                    <span class="text-sm text-gray-500">Last Updated By</span>
                    <div class="font-medium"><?php echo e($movement->updatedBy->name ?? 'System'); ?></div>
                    <div class="text-xs text-gray-400"><?php echo e($movement->updated_at->format('F d, Y g:i A')); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/store/stock_movements/show.blade.php ENDPATH**/ ?>