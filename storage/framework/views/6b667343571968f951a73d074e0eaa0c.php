<?php $__env->startSection('title', 'Inventory Items'); ?>

<?php $__env->startSection('page-title', 'Inventory Management'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .search-input {
        transition: all 0.3s ease;
    }
    .search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        outline: none;
    }
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Inventory Items</h3>
            <p class="text-sm text-gray-500">Manage all stock items in the store</p>
        </div>
        <div class="flex gap-3">
            
            <div class="relative">
                <input type="text"
                       id="liveSearch"
                       class="search-input pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-64 focus:border-blue-500"
                       placeholder="Search by name or code...">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <div id="searchSpinner" class="absolute right-3 top-2.5 hidden">
                    <div class="loading-spinner"></div>
                </div>
            </div>
            <a href="<?php echo e(route('store.inventory.create')); ?>"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Item
            </a>
        </div>
    </div>

    <div class="p-6 overflow-x-auto">
        <div id="itemsTableContainer">
            <table class="w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <?php echo $__env->make('store.inventory.partials.table_rows', ['items' => $items], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </tbody>
            </table>
            <div class="mt-4" id="paginationLinks">
                <?php echo e($items->links()); ?>

            </div>
        </div>
        <div id="noResults" class="hidden text-center py-8 text-gray-500">
            No inventory items found matching your search.
        </div>
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const tableBody = document.getElementById('itemsTableBody');
    const paginationLinks = document.getElementById('paginationLinks');
    const noResults = document.getElementById('noResults');
    const itemsTableContainer = document.getElementById('itemsTableContainer');
    const searchSpinner = document.getElementById('searchSpinner');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);

        const query = this.value.trim();

        // Show spinner
        searchSpinner.classList.remove('hidden');

        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 500);
    });

    function performSearch(query) {
        fetch(`<?php echo e(route('store.inventory.index')); ?>?search=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            searchSpinner.classList.add('hidden');

            if (data.html) {
                tableBody.innerHTML = data.html;

                if (data.pagination) {
                    paginationLinks.innerHTML = data.pagination;
                } else {
                    paginationLinks.innerHTML = '';
                }

                if (data.total === 0) {
                    itemsTableContainer.classList.add('hidden');
                    noResults.classList.remove('hidden');
                } else {
                    itemsTableContainer.classList.remove('hidden');
                    noResults.classList.add('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            searchSpinner.classList.add('hidden');
        });
    }

    function viewItem(id) {
        window.location.href = `/store/inventory/${id}`;
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\patiobella\resources\views/store/inventory/index.blade.php ENDPATH**/ ?>