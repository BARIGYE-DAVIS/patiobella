{{-- resources/views/procurement/cost-prices/index.blade.php --}}

@extends('layouts.procurement')

@section('title', 'Cost Price Management')

@section('page-title', 'Cost Price Management')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        margin-bottom: 1rem;
    }
    .stat-card h3 {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    .stat-card .value {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .stat-total { border-left-color: #3b82f6; }
    .stat-cost { border-left-color: #10b981; }
    .stat-updated { border-left-color: #f59e0b; }

    .filter-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .filter-input {
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 100%;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        position: sticky;
        top: 0;
    }
    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .data-table tr:hover {
        background: #f0fdf4;
    }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .badge-bulk {
        background: #fef3c7;
        color: #92400e;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 0.6rem;
    }
    .btn-edit {
        background: #3b82f6;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-edit:hover {
        background: #2563eb;
        color: white;
    }
    .search-box {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 250px;
    }
    .highlight {
        background-color: #fef3c7;
        font-weight: bold;
    }
    .result-badge {
        font-size: 0.7rem;
        color: #6b7280;
        margin-left: 0.5rem;
    }
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #e5e7eb;
        border-top-color: #10b981;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-left: 0.5rem;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-dollar-sign mr-2"></i>
                    Cost Price Management
                </h2>
                <p class="text-emerald-100 mt-1">Update purchase costs for inventory items</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card stat-total">
            <h3><i class="fas fa-boxes mr-1"></i> Total Items</h3>
            <div class="value" id="totalItemsCount">{{ $items->total() }}</div>
            <p class="text-xs text-gray-500 mt-1">Active inventory items</p>
        </div>
        <div class="stat-card stat-cost">
            <h3><i class="fas fa-chart-line mr-1"></i> Avg Unit Cost</h3>
            <div class="value">UGX {{ number_format($items->avg('unit_cost') ?? 0, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Average across all items</p>
        </div>
        <div class="stat-card stat-updated">
            <h3><i class="fas fa-history mr-1"></i> Last Updated</h3>
            <div class="value">{{ $items->max('updated_at') ? $items->max('updated_at')->diffForHumans() : 'Never' }}</div>
            <p class="text-xs text-gray-500 mt-1">Most recent cost update</p>
        </div>
    </div>

    {{-- Filters with Live Search --}}
    <div class="filter-card">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Live Search</label>
                <input type="text" id="liveSearch" class="filter-input" placeholder=" Search by name or code...">
                <span id="searchResultCount" class="result-badge"></span>
                <div id="loadingIndicator" class="loading-spinner" style="display: none;"></div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Category Filter</label>
                <select id="categoryFilter" class="filter-input">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <div class="flex gap-2">
                    <button id="applyFiltersBtn" class="bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-emerald-700">
                        <i class="fas fa-search mr-1"></i> Apply Filters
                    </button>
                    <button id="clearFiltersBtn" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-xs hover:bg-gray-400">
                        <i class="fas fa-times mr-1"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Table with Live Search --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 25%">Item Name</th>
                        <th style="width: 10%">Code</th>
                        <th style="width: 10%">Category</th>
                        <th style="width: 10%">Unit</th>
                        <th style="width: 15%" class="text-right">Current Unit Cost</th>
                        <th style="width: 15%">Last Updated</th>
                        <th style="width: 10%">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @php $counter = 1; @endphp
                    @forelse($items as $item)
                    <tr data-id="{{ $item->id }}"
                        data-name="{{ strtolower($item->name) }}"
                        data-code="{{ strtolower($item->item_code ?? '') }}"
                        data-category="{{ $item->category_id }}"
                        data-category-name="{{ strtolower($item->category->name ?? '') }}">
                        <td class="text-center counter-cell">{{ $counter++ }}</td>
                        <td class="font-medium text-gray-800 name-cell">
                            {{ $item->name }}
                            @if($item->default_unit_of_measure_id && in_array($item->default_unit_of_measure_id, ['carton', 'crate', 'box', 'dozen', 'pack']))
                                <span class="badge-bulk ml-2"><i class="fas fa-cubes mr-1"></i> Bulk Item</span>
                            @endif
                        </td>
                        <td class="text-gray-500 code-cell">{{ $item->item_code ?? '—' }}</td>
                        <td class="category-cell">{{ $item->category->name ?? '—' }}</td>
                        <td class="text-gray-500">{{ $item->base_unit ?? 'piece' }}</td>
                        <td class="text-right font-semibold text-emerald-600 cost-cell">
                            UGX {{ number_format($item->unit_cost ?? 0, 2) }}
                        </td>
                        <td class="text-gray-500 text-xs">{{ $item->updated_at ? $item->updated_at->diffForHumans() : 'Never' }}</td>
                        <td class="text-center">
                            <a href="{{ route('procurement.cost-prices.edit', $item->id) }}" class="btn-edit">
                                <i class="fas fa-edit mr-1"></i> Edit Cost
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="8" class="text-center text-gray-500 py-8">
                            <i class="fas fa-boxes text-4xl mb-2 block"></i>
                            No inventory items found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="paginationLinks" class="px-4 py-3 border-t border-gray-200">
            {{ $items->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const liveSearch = document.getElementById('liveSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const applyFiltersBtn = document.getElementById('applyFiltersBtn');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        const tableBody = document.getElementById('tableBody');
        const searchResultCount = document.getElementById('searchResultCount');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const totalItemsCount = document.getElementById('totalItemsCount');

        let searchTimeout;
        let currentRows = [];

        // Store original rows data
        function storeOriginalRows() {
            currentRows = [];
            const rows = tableBody.querySelectorAll('tr[data-id]');
            rows.forEach(row => {
                currentRows.push({
                    element: row,
                    id: row.dataset.id,
                    name: row.dataset.name || '',
                    code: row.dataset.code || '',
                    category: row.dataset.category || '',
                    categoryName: row.dataset.categoryName || '',
                    visible: true
                });
            });
        }
        storeOriginalRows();

        // Perform live search and filter
        function performSearch() {
            const searchTerm = liveSearch.value.toLowerCase();
            const selectedCategory = categoryFilter.value;

            let visibleCount = 0;

            currentRows.forEach(row => {
                const matchesSearch = searchTerm === '' ||
                    row.name.includes(searchTerm) ||
                    row.code.includes(searchTerm) ||
                    row.categoryName.includes(searchTerm);

                const matchesCategory = selectedCategory === '' || row.category === selectedCategory;

                if (matchesSearch && matchesCategory) {
                    row.element.style.display = '';
                    visibleCount++;
                    if (searchTerm !== '') {
                        highlightText(row.element, searchTerm);
                    } else {
                        removeHighlight(row.element);
                    }
                } else {
                    row.element.style.display = 'none';
                }
            });

            // Update counter numbers
            updateRowNumbers();

            // Update result count
            searchResultCount.textContent = `${visibleCount} results found`;

            // Update total items count display
            if (totalItemsCount) {
                totalItemsCount.textContent = visibleCount;
            }

            // Show/hide no results message
            let noResultsRow = document.getElementById('noResultsRow');
            if (visibleCount === 0 && currentRows.length > 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = '<td colspan="8" class="text-center text-gray-500 py-8"><i class="fas fa-search text-4xl mb-2 block"></i>No items match your search.</td>';
                    tableBody.appendChild(noResultsRow);
                }
                noResultsRow.style.display = '';
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }

        // Update row numbers after filtering
        function updateRowNumbers() {
            let counter = 1;
            const visibleRows = tableBody.querySelectorAll('tr[data-id]:not([style*="display: none"])');
            visibleRows.forEach(row => {
                const counterCell = row.querySelector('.counter-cell');
                if (counterCell) {
                    counterCell.textContent = counter++;
                }
            });
        }

        // Highlight matching text
        function highlightText(row, term) {
            const nameCell = row.querySelector('.name-cell');
            const codeCell = row.querySelector('.code-cell');
            const categoryCell = row.querySelector('.category-cell');

            // Highlight item name
            if (nameCell) {
                const originalName = nameCell.getAttribute('data-original-name');
                if (!originalName) {
                    nameCell.setAttribute('data-original-name', nameCell.innerHTML);
                }
                const text = nameCell.getAttribute('data-original-name');
                if (text && term) {
                    const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    nameCell.innerHTML = text.replace(regex, '<span class="highlight">$1</span>');
                }
            }

            // Highlight item code
            if (codeCell) {
                const originalCode = codeCell.getAttribute('data-original-code');
                if (!originalCode) {
                    codeCell.setAttribute('data-original-code', codeCell.innerHTML);
                }
                const text = codeCell.getAttribute('data-original-code');
                if (text && term && text !== '—') {
                    const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    codeCell.innerHTML = text.replace(regex, '<span class="highlight">$1</span>');
                }
            }
        }

        function removeHighlight(row) {
            const nameCell = row.querySelector('.name-cell');
            const codeCell = row.querySelector('.code-cell');

            if (nameCell) {
                const original = nameCell.getAttribute('data-original-name');
                if (original) {
                    nameCell.innerHTML = original;
                    nameCell.removeAttribute('data-original-name');
                }
            }

            if (codeCell) {
                const original = codeCell.getAttribute('data-original-code');
                if (original) {
                    codeCell.innerHTML = original;
                    codeCell.removeAttribute('data-original-code');
                }
            }
        }

        // Reset all filters
        function resetFilters() {
            liveSearch.value = '';
            categoryFilter.value = '';
            performSearch();
        }

        // Apply filters (full page reload with GET parameters)
        function applyFilters() {
            const params = new URLSearchParams();
            if (liveSearch.value) {
                params.append('search', liveSearch.value);
            }
            if (categoryFilter.value) {
                params.append('category_id', categoryFilter.value);
            }
            window.location.href = '{{ route("procurement.cost-prices.index") }}?' + params.toString();
        }

        // Event listeners for live search
        liveSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            loadingIndicator.style.display = 'inline-block';
            searchTimeout = setTimeout(() => {
                performSearch();
                loadingIndicator.style.display = 'none';
            }, 300);
        });

        // Category filter change
        categoryFilter.addEventListener('change', function() {
            performSearch();
        });

        // Apply filters button
        applyFiltersBtn.addEventListener('click', applyFilters);

        // Clear filters button
        clearFiltersBtn.addEventListener('click', function() {
            resetFilters();
        });

        // Enter key in search
        liveSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                performSearch();
                loadingIndicator.style.display = 'none';
            }
        });

        // Initialize stored data for highlighting
        document.querySelectorAll('#tableBody tr[data-id]').forEach(row => {
            const nameCell = row.querySelector('.name-cell');
            const codeCell = row.querySelector('.code-cell');
            if (nameCell) nameCell.setAttribute('data-original-name', nameCell.innerHTML);
            if (codeCell) codeCell.setAttribute('data-original-code', codeCell.innerHTML);
        });
    });
</script>
@endsection
