<?php $__env->startSection('title', 'Inventory'); ?>

<?php $__env->startSection('content'); ?>


<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Inventory Items</h2>
        <p class="text-sm text-gray-500 mt-0.5"><?php echo e($items->total()); ?> items total</p>
    </div>
    <a href="<?php echo e(route('store.inventory.create')); ?>"
       class="inline-flex items-center gap-2 bg-blue-800 hover:bg-blue-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Item
    </a>
</div>


<?php
    $totalItems = \App\Models\InventoryItem::count();
    $inStock    = \App\Models\InventoryItem::where('current_stock', '>', 0)
                    ->where(function($q){
                        $q->where('minimum_stock', 0)->orWhereColumn('current_stock', '>', 'minimum_stock');
                    })->count();
    $lowStock   = \App\Models\InventoryItem::whereColumn('current_stock', '<=', 'minimum_stock')
                    ->where('minimum_stock', '>', 0)->where('current_stock', '>', 0)->count();
    $outOfStock = \App\Models\InventoryItem::where('current_stock', '<=', 0)->count();
?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Items</p>
        <p class="text-2xl font-bold text-gray-800"><?php echo e($totalItems); ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">In Stock</p>
        <p class="text-2xl font-bold text-green-700"><?php echo e($inStock); ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Low Stock</p>
        <p class="text-2xl font-bold text-yellow-600"><?php echo e($lowStock); ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Out of Stock</p>
        <p class="text-2xl font-bold text-red-700"><?php echo e($outOfStock); ?></p>
    </div>
</div>


<form method="GET" action="<?php echo e(route('store.inventory.index')); ?>" class="flex flex-wrap items-center gap-3 mb-4">
    <input
        type="text"
        name="search"
        value="<?php echo e(request('search')); ?>"
        placeholder="Search by name or item code…"
        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-64 bg-white"
    />
    <select name="status"
        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white">
        <option value="">All statuses</option>
        <option value="in_stock"     <?php echo e(request('status') === 'in_stock'     ? 'selected' : ''); ?>>In stock</option>
        <option value="low_stock"    <?php echo e(request('status') === 'low_stock'    ? 'selected' : ''); ?>>Low stock</option>
        <option value="out_of_stock" <?php echo e(request('status') === 'out_of_stock' ? 'selected' : ''); ?>>Out of stock</option>
    </select>
    <button type="submit"
        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg transition font-medium">
        Filter
    </button>
    <?php if(request('search') || request('status')): ?>
        <a href="<?php echo e(route('store.inventory.index')); ?>"
           class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Clear
        </a>
    <?php endif; ?>
    <span class="text-sm text-gray-400 ml-auto">
        Showing <?php echo e($items->firstItem() ?? 0); ?>–<?php echo e($items->lastItem() ?? 0); ?> of <?php echo e($items->total()); ?>

    </span>
</form>


<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Item</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Category</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Receiving Unit</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Base Unit</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Current Stock</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Status</th>
                    <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Active</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition-colors">

                    
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 leading-tight"><?php echo e($item->name); ?></p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5"><?php echo e($item->item_code); ?></p>
                    </td>

                    
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                        <?php echo e($item->category->name ?? '—'); ?>

                    </td>

                    
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1.5">
                            <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded-md whitespace-nowrap">
                                <?php echo e(ucfirst($item->default_unit_of_measure_id ?? '—')); ?>

                            </span>
                            <?php if($item->is_bulk_item): ?>
                                <span class="text-xs text-gray-400">bulk</span>
                            <?php endif; ?>
                        </div>
                    </td>

                    
                    <td class="px-4 py-3">
                        <span class="bg-orange-50 text-orange-700 text-xs font-medium px-2 py-1 rounded-md">
                            <?php echo e(ucfirst($item->base_unit_label)); ?>

                        </span>
                    </td>

                    
                    <td class="px-4 py-3 text-right">
                        <span class="font-semibold text-gray-800 tabular-nums">
                            <?php echo e(number_format($item->current_stock, 2)); ?>

                        </span>
                        <span class="text-xs text-gray-400 ml-0.5"><?php echo e($item->base_unit_label); ?>(s)</span>

                        <?php if($item->minimum_stock > 0): ?>
                            <?php
                                $maxRef   = $item->maximum_stock > 0 ? $item->maximum_stock : ($item->minimum_stock * 2);
                                $pct      = $maxRef > 0 ? min(100, ($item->current_stock / $maxRef) * 100) : 0;
                                $barClass = match($item->stock_status) {
                                    'out_of_stock' => 'bg-red-500',
                                    'low_stock'    => 'bg-yellow-400',
                                    default        => 'bg-green-500',
                                };
                            ?>
                            <div class="w-20 h-1.5 bg-gray-100 rounded-full mt-1.5 ml-auto overflow-hidden">
                                <div class="<?php echo e($barClass); ?> h-full rounded-full" style="width: <?php echo e($pct); ?>%"></div>
                            </div>
                        <?php endif; ?>
                    </td>

                    
                    <td class="px-4 py-3 whitespace-nowrap">
                        <?php
                            $badgeClass = match($item->stock_status) {
                                'out_of_stock' => 'bg-red-100 text-red-700',
                                'low_stock'    => 'bg-yellow-100 text-yellow-700',
                                default        => 'bg-green-100 text-green-700',
                            };
                        ?>
                        <span class="inline-block <?php echo e($badgeClass); ?> text-xs font-semibold px-2.5 py-1 rounded-full">
                            <?php echo e(ucfirst(str_replace('_', ' ', $item->stock_status))); ?>

                        </span>
                    </td>

                    
                    <td class="px-4 py-3 text-center">
                        <?php if($item->is_active): ?>
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500" title="Active"></span>
                        <?php else: ?>
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-gray-300" title="Inactive"></span>
                        <?php endif; ?>
                    </td>

                    
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1.5">
                            <a href="<?php echo e(route('store.inventory.show', $item->id)); ?>"
                               class="inline-flex items-center gap-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View
                            </a>
                            <a href="<?php echo e(route('store.inventory.edit', $item->id)); ?>"
                               class="inline-flex items-center border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-4 py-16">
                        <div class="flex flex-col items-center gap-3 text-gray-400">
                            <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500">No inventory items found.</p>
                            <a href="<?php echo e(route('store.inventory.create')); ?>"
                               class="text-sm text-blue-700 hover:underline font-medium">
                                Add your first item →
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($items->hasPages()): ?>
    <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50">
        <p class="text-sm text-gray-400">
            Page <?php echo e($items->currentPage()); ?> of <?php echo e($items->lastPage()); ?>

        </p>
        <?php echo e($items->appends(request()->query())->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/store/inventory/index.blade.php ENDPATH**/ ?>