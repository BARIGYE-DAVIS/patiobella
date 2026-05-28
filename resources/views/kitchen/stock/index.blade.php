@extends('layouts.kitchen')

@section('title', 'Kitchen Stock')

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-8">

    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kitchen Stock Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">View current inventory in kitchen department</p>
        </div>
        <a href="{{ route('kitchen.stock.export-pdf', request()->query()) }}"
           target="_blank"
           class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition">
            <i class="fas fa-file-pdf mr-1"></i> Export PDF
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Total Items</p>
                    <p class="text-xl font-bold text-gray-800" id="totalItems">{{ $totalItems }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">In Stock</p>
                    <p class="text-xl font-bold text-green-600" id="inStockCount">0</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Low Stock</p>
                    <p class="text-xl font-bold text-yellow-600" id="lowStockCount">0</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Out of Stock</p>
                    <p class="text-xl font-bold text-red-600" id="outOfStockCount">0</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Total Stock Value</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($totalStockValue, 0) }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="liveSearch"
                               placeholder="Live search by item name or code..."
                               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                    </div>
                </div>
                <div class="w-48">
                    <select id="categoryFilter" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-48">
                    <select id="stockStatusFilter" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                        <option value="all">All Stock Status</option>
                        <option value="in_stock">In Stock (>0)</option>
                        <option value="low_stock">Low Stock (≤10)</option>
                        <option value="out_of_stock">Out of Stock (=0)</option>
                    </select>
                </div>
                <button onclick="resetFilters()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition">
                    <i class="fas fa-redo mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Stock Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Issued</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Consumed</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Returned</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="stockTableBody">
                    @forelse($stockData as $item)
                    @php
                        $statusClass = '';
                        $statusText = '';
                        if ($item['current_stock'] <= 0) {
                            $statusClass = 'bg-red-100 text-red-700';
                            $statusText = 'Out of Stock';
                        } elseif ($item['current_stock'] <= 10) {
                            $statusClass = 'bg-yellow-100 text-yellow-700';
                            $statusText = 'Low Stock';
                        } else {
                            $statusClass = 'bg-green-100 text-green-700';
                            $statusText = 'In Stock';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition stock-row"
                        data-item-name="{{ strtolower($item['item_name']) }}"
                        data-item-code="{{ strtolower($item['item_code']) }}"
                        data-category-id="{{ $item['category_id'] }}"
                        data-current-stock="{{ $item['current_stock'] }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">
                            {{ $item['item_name'] }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $item['item_code'] }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @php
                                $category = App\Models\Category::find($item['category_id']);
                            @endphp
                            {{ $category->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ ucfirst($item['unit']) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-semibold text-green-600">
                            {{ number_format($item['issued'], 0) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-orange-600">
                            {{ number_format($item['consumed'], 0) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-yellow-600">
                            {{ number_format($item['returned'], 0) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-bold {{ $item['current_stock'] > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ number_format($item['current_stock'], 0) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('kitchen.stock.show', $item['inventory_item_id']) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium rounded-lg transition">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-boxes text-4xl mb-3 block opacity-30"></i>
                            <p class="text-sm">No stock items found</p>
                            <p class="text-xs mt-1">No items have been issued to kitchen yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateStats() {
        let inStock = 0, lowStock = 0, outOfStock = 0;
        document.querySelectorAll('.stock-row').forEach(row => {
            let stock = parseFloat(row.getAttribute('data-current-stock'));
            if (stock <= 0) outOfStock++;
            else if (stock <= 10) lowStock++;
            else inStock++;
        });
        document.getElementById('inStockCount').textContent = inStock;
        document.getElementById('lowStockCount').textContent = lowStock;
        document.getElementById('outOfStockCount').textContent = outOfStock;
        document.getElementById('totalItems').textContent = document.querySelectorAll('.stock-row').length;
    }

    function filterTable() {
        const searchTerm = document.getElementById('liveSearch').value.toLowerCase();
        const categoryId = document.getElementById('categoryFilter').value;
        const stockStatus = document.getElementById('stockStatusFilter').value;

        document.querySelectorAll('.stock-row').forEach(row => {
            let itemName = row.getAttribute('data-item-name') || '';
            let itemCode = row.getAttribute('data-item-code') || '';
            let rowCategoryId = row.getAttribute('data-category-id') || '';
            let currentStock = parseFloat(row.getAttribute('data-current-stock')) || 0;

            let matchesSearch = searchTerm === '' || itemName.includes(searchTerm) || itemCode.includes(searchTerm);
            let matchesCategory = categoryId === '' || rowCategoryId === categoryId;
            let matchesStatus = true;

            if (stockStatus === 'in_stock') matchesStatus = currentStock > 0;
            else if (stockStatus === 'low_stock') matchesStatus = currentStock > 0 && currentStock <= 10;
            else if (stockStatus === 'out_of_stock') matchesStatus = currentStock <= 0;

            if (matchesSearch && matchesCategory && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        updateStats();
    }

    function resetFilters() {
        document.getElementById('liveSearch').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('stockStatusFilter').value = 'all';
        filterTable();
    }

    document.getElementById('liveSearch').addEventListener('keyup', filterTable);
    document.getElementById('categoryFilter').addEventListener('change', filterTable);
    document.getElementById('stockStatusFilter').addEventListener('change', filterTable);

    setTimeout(updateStats, 100);
</script>
@endsection
