@extends('layouts.store')

@section('title', 'Inventory')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Inventory Items</h2>
        <p class="text-sm text-gray-500 mt-0.5" id="totalItemsDisplay">{{ $items->total() }} items total</p>
    </div>
    <a href="{{ route('store.inventory.create') }}"
       class="inline-flex items-center gap-2 bg-blue-800 hover:bg-blue-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Item
    </a>
</div>

{{-- Summary Cards (Now calculated from batches) --}}
@php
    $totalItems = \App\Models\InventoryItem::where('is_active', true)->count();

    // Calculate stock from batches
    $inStock = \App\Models\Batch::where('batch_status', 'active')
        ->where('remaining_quantity', '>', 0)
        ->distinct('inventory_item_id')
        ->count('inventory_item_id');

    $outOfStock = \App\Models\InventoryItem::where('is_active', true)
        ->whereNotIn('id', function($q) {
            $q->select('inventory_item_id')
              ->from('batches')
              ->where('batch_status', 'active')
              ->where('remaining_quantity', '>', 0);
        })
        ->count();

    // Low stock: items with total stock below minimum_stock (calculated from batches)
    $lowStock = 0;
    $allItems = \App\Models\InventoryItem::where('is_active', true)->get();
    foreach ($allItems as $item) {
        $totalStock = \App\Models\Batch::where('inventory_item_id', $item->id)
            ->where('batch_status', 'active')
            ->sum('remaining_quantity');
        if ($totalStock > 0 && $totalStock <= ($item->minimum_stock ?? 0)) {
            $lowStock++;
        }
    }
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Items</p>
        <p class="text-2xl font-bold text-gray-800" id="statTotalItems">{{ $totalItems }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">In Stock</p>
        <p class="text-2xl font-bold text-green-700" id="statInStock">{{ $inStock }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Low Stock</p>
        <p class="text-2xl font-bold text-yellow-600" id="statLowStock">{{ $lowStock }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Out of Stock</p>
        <p class="text-2xl font-bold text-red-700" id="statOutOfStock">{{ $outOfStock }}</p>
    </div>
</div>

{{-- Live Search Filters --}}
<div class="flex flex-wrap items-center gap-3 mb-4">
    <div class="relative flex-1 max-w-md">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input
            type="text"
            id="searchInput"
            placeholder="Live search by name or item code…"
            class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white"
            autocomplete="off"
        />
        <div id="searchLoader" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
            <svg class="w-4 h-4 text-blue-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
    </div>
    <select id="statusFilter"
        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white">
        <option value="">All statuses</option>
        <option value="in_stock">In stock</option>
        <option value="low_stock">Low stock</option>
        <option value="out_of_stock">Out of stock</option>
    </select>
    <button id="clearFiltersBtn"
        class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Clear
    </button>
    <span class="text-sm text-gray-400 ml-auto" id="resultsSummary">
        Showing <span id="showingStart">0</span>–<span id="showingEnd">0</span> of <span id="totalResults">0</span>
    </span>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Item</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Category</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Unit</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Current Stock</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Status</th>
                    <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Active</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">Batches</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody id="inventoryTableBody" class="divide-y divide-gray-50">
                @include('store.inventory.partials.table_rows', ['items' => $items])
            </tbody>
        </table>
    </div>

    <div id="paginationContainer" class="px-4 py-3 border-t border-gray-100 bg-gray-50">
        {{ $items->appends(request()->query())->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let debounceTimer;
    let currentPage = 1;
    let isLoading = false;

    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const tableBody = document.getElementById('inventoryTableBody');
    const paginationContainer = document.getElementById('paginationContainer');
    const searchLoader = document.getElementById('searchLoader');
    const showingStart = document.getElementById('showingStart');
    const showingEnd = document.getElementById('showingEnd');
    const totalResults = document.getElementById('totalResults');
    const statTotalItems = document.getElementById('statTotalItems');
    const statInStock = document.getElementById('statInStock');
    const statLowStock = document.getElementById('statLowStock');
    const statOutOfStock = document.getElementById('statOutOfStock');

    function fetchLiveResults() {
        if (isLoading) return;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const params = new URLSearchParams();
            params.append('page', currentPage);
            params.append('ajax', '1');

            if (searchInput && searchInput.value.trim()) {
                params.append('search', searchInput.value.trim());
            }
            if (statusFilter && statusFilter.value) {
                params.append('status', statusFilter.value);
            }

            if (searchLoader) searchLoader.classList.remove('hidden');
            isLoading = true;

            fetch(`{{ route('store.inventory.index') }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    tableBody.innerHTML = data.html;
                }
                if (data.pagination) {
                    paginationContainer.innerHTML = data.pagination;
                }
                if (data.summary) {
                    showingStart.textContent = data.summary.start;
                    showingEnd.textContent = data.summary.end;
                    totalResults.textContent = data.summary.total;
                }
                if (data.stats) {
                    statTotalItems.textContent = data.stats.total_items;
                    statInStock.textContent = data.stats.in_stock;
                    statLowStock.textContent = data.stats.low_stock;
                    statOutOfStock.textContent = data.stats.out_of_stock;
                }
                isLoading = false;
                if (searchLoader) searchLoader.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                isLoading = false;
                if (searchLoader) searchLoader.classList.add('hidden');
            });
        }, 300);
    }

    // Live search on input
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    // Filter on change
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    // Clear filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            currentPage = 1;
            fetchLiveResults();
        });
    }

    // Handle pagination clicks (event delegation)
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.getAttribute('href')) {
                e.preventDefault();
                const url = new URL(link.href);
                const page = url.searchParams.get('page');
                if (page) {
                    currentPage = parseInt(page);
                    fetchLiveResults();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        });
    }
});
</script>
@endpush

@endsection
