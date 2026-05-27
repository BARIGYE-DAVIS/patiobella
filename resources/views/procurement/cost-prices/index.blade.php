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
        vertical-align: middle;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .active-search-banner {
        background: #ecfdf5;
        border: 1px solid #6ee7b7;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        color: #065f46;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
            <div class="value">{{ $items->total() }}</div>
            <p class="text-xs text-gray-500 mt-1">
                @if(request('search') || request('category_id'))
                    Matching your search
                @else
                    Active inventory items
                @endif
            </p>
        </div>
        <div class="stat-card stat-cost">
            <h3><i class="fas fa-chart-line mr-1"></i> Avg Unit Cost</h3>
            <div class="value">UGX {{ number_format($avgCost ?? 0, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Average across all items</p>
        </div>
        <div class="stat-card stat-updated">
            <h3><i class="fas fa-history mr-1"></i> Last Updated</h3>
            <div class="value">{{ $lastUpdated ?? 'Never' }}</div>
            <p class="text-xs text-gray-500 mt-1">Most recent cost update</p>
        </div>
    </div>

    {{-- Active Search Banner --}}
    @if(request('search') || request('category_id'))
    <div class="active-search-banner">
        <i class="fas fa-filter"></i>
        <span>
            Showing <strong>{{ $items->total() }}</strong> result(s)
            @if(request('search'))
                for <strong>"{{ request('search') }}"</strong>
            @endif
            @if(request('category_id') && isset($selectedCategoryName))
                in category <strong>{{ $selectedCategoryName }}</strong>
            @endif
            &mdash; across all pages.
        </span>
        <a href="{{ route('procurement.cost-prices.index') }}" class="ml-auto text-emerald-700 hover:underline font-medium">
            <i class="fas fa-times mr-1"></i> Clear filters
        </a>
    </div>
    @endif

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('procurement.cost-prices.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Search
                        <span id="loadingIndicator" class="loading-spinner" style="display: none;"></span>
                    </label>
                    <input
                        type="text"
                        name="search"
                        id="liveSearch"
                        class="filter-input"
                        placeholder="Search by name or code..."
                        value="{{ request('search') }}"
                        autocomplete="off"
                    >
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category Filter</label>
                    <select name="category_id" id="categoryFilter" class="filter-input">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <div class="flex gap-2">
                        <button type="submit" class="bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-emerald-700">
                            <i class="fas fa-search mr-1"></i> Search
                        </button>
                        <a href="{{ route('procurement.cost-prices.index') }}" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-xs hover:bg-gray-400">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Items Table --}}
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
                    @php $counter = $items->firstItem() ?? 1; @endphp
                    @forelse($items as $item)
                    <tr>
                        <td class="text-center">{{ $counter++ }}</td>
                        <td class="font-medium text-gray-800">
                            {{-- Highlight search term in name --}}
                            @if(request('search'))
                                {!! preg_replace(
                                    '/(' . preg_quote(e(request('search')), '/') . ')/i',
                                    '<span class="highlight">$1</span>',
                                    e($item->name)
                                ) !!}
                            @else
                                {{ $item->name }}
                            @endif
                            @if($item->default_unit_of_measure_id && in_array($item->default_unit_of_measure_id, ['carton', 'crate', 'box', 'dozen', 'pack']))
                                <span class="badge-bulk ml-2"><i class="fas fa-cubes mr-1"></i> Bulk Item</span>
                            @endif
                        </td>
                        <td class="text-gray-500">
                            {{-- Highlight search term in code --}}
                            @if(request('search') && $item->item_code)
                                {!! preg_replace(
                                    '/(' . preg_quote(e(request('search')), '/') . ')/i',
                                    '<span class="highlight">$1</span>',
                                    e($item->item_code)
                                ) !!}
                            @else
                                {{ $item->item_code ?? '—' }}
                            @endif
                        </td>
                        <td>{{ $item->category->name ?? '—' }}</td>
                        <td class="text-gray-500">{{ $item->base_unit ?? 'piece' }}</td>
                        <td class="text-right font-semibold text-emerald-600">
                            UGX {{ number_format($item->unit_cost ?? 0, 2) }}
                        </td>
                        <td class="text-gray-500 text-xs">
                            {{ $item->updated_at ? $item->updated_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('procurement.cost-prices.edit', $item->id) }}" class="btn-edit">
                                <i class="fas fa-edit mr-1"></i> Edit Cost
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-8">
                            <i class="fas fa-search text-4xl mb-2 block"></i>
                            @if(request('search') || request('category_id'))
                                No items match your search. <a href="{{ route('procurement.cost-prices.index') }}" class="text-emerald-600 underline">Clear filters</a>
                            @else
                                No inventory items found.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination — appends search params so pagination links carry the search query --}}
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $items->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const liveSearch = document.getElementById('liveSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const filterForm = document.getElementById('filterForm');
        const loadingIndicator = document.getElementById('loadingIndicator');

        let searchTimeout;

        // Submit form after user stops typing (debounced — 500ms)
        // This hits the server so ALL pages are searched, not just current DOM rows
        liveSearch.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            loadingIndicator.style.display = 'inline-block';
            searchTimeout = setTimeout(function () {
                filterForm.submit();
            }, 500);
        });

        // Submit immediately on category change
        categoryFilter.addEventListener('change', function () {
            loadingIndicator.style.display = 'inline-block';
            filterForm.submit();
        });

        // Show spinner on Enter key
        liveSearch.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                loadingIndicator.style.display = 'inline-block';
                filterForm.submit();
            }
        });
    });
</script>
@endsection
