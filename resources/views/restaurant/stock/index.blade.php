{{-- resources/views/restaurant/stock/index.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'My Stock')

@section('page-title', 'My Stock')

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
    .stat-issued { border-left-color: #10b981; }
    .stat-consumed { border-left-color: #f59e0b; }
    .stat-remaining { border-left-color: #8b5cf6; }

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
    }
    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .data-table tr:hover {
        background: #fef3c7;
    }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    .stock-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .stock-high { background: #d1fae5; color: #065f46; }
    .stock-medium { background: #fef3c7; color: #92400e; }
    .stock-low { background: #fee2e2; color: #991b1b; }

    .search-wrapper {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
    }
    .live-search-input {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 300px;
    }
    .result-badge {
        font-size: 0.7rem;
        color: #6b7280;
    }
    .highlight {
        background-color: #fef3c7;
        font-weight: bold;
    }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-boxes mr-2"></i>
                    My Stock
                </h2>
                <p class="text-orange-100 mt-1">Current inventory in restaurant possession</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-chart-line mr-1"></i> Total Items</p>
                <p class="text-2xl font-bold">{{ $totalItems }}</p>
            </div>

            <a href="{{ route('restaurant.requisitions.create') }}" class="bg-white text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                <i class="fas fa-plus mr-1"></i> Request from Store
            </a>

            <a href="{{ route('restaurant.stock.summary') }}" class="bg-white text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                <i class="fas fa-file-alt mr-1"></i> View Summary
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card stat-total">
            <h3><i class="fas fa-boxes mr-1"></i> Total Items</h3>
            <div class="value">{{ number_format($totalItems) }}</div>
            <p class="text-xs text-gray-500 mt-1">Unique items in stock</p>
        </div>
        <div class="stat-card stat-issued">
            <h3><i class="fas fa-arrow-down mr-1"></i> Total Issued</h3>
            <div class="value">{{ number_format($totalIssued, 2) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Received from store</p>
        </div>
        <div class="stat-card stat-consumed">
            <h3><i class="fas fa-fire mr-1"></i> Total Consumed</h3>
            <div class="value">{{ number_format($totalConsumed, 2) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Used in operations</p>
        </div>
        <div class="stat-card stat-remaining">
            <h3><i class="fas fa-box-open mr-1"></i> Current Stock</h3>
            <div class="value">{{ number_format($totalStockValue, 2) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Available for use</p>
        </div>
    </div>

    {{-- Filters with Live Search --}}
    <div class="filter-card">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Live Search</label>
                <input type="text" id="liveSearch" class="filter-input" placeholder="🔍 Search by name or code...">
                <span id="searchResultCount" class="result-badge"></span>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Category Filter</label>
                <select id="categoryFilter" class="filter-input">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ strtolower($cat->name) }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button id="resetFilters" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-xs hover:bg-gray-400">
                    <i class="fas fa-times mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Stock Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="stockTable">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 25%">Item Name</th>
                        <th style="width: 10%">Code</th>
                        <th style="width: 8%">Unit</th>
                        <th style="width: 12%" class="text-right">Issued</th>
                        <th style="width: 12%" class="text-right">Consumed</th>
                        <th style="width: 12%" class="text-right">Returned</th>
                        <th style="width: 12%" class="text-right">Current Stock</th>
                        <th style="width: 8%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @php $counter = 1; @endphp
                    @forelse($stockData as $item)
                    @php
                        $currentStock = $item['current_stock'];
                        if ($currentStock > 20) {
                            $stockClass = 'stock-high';
                            $stockText  = 'High';
                        } elseif ($currentStock > 5) {
                            $stockClass = 'stock-medium';
                            $stockText  = 'Medium';
                        } else {
                            $stockClass = 'stock-low';
                            $stockText  = 'Low';
                        }
                    @endphp
                    <tr data-name="{{ strtolower($item['item_name']) }}"
                        data-code="{{ strtolower($item['item_code']) }}"
                        data-category="{{ strtolower($item['category']) }}">
                        <td class="text-center counter-cell">{{ $counter++ }}</td>
                        <td class="font-medium text-gray-800 name-cell">
                            {{ $item['item_name'] }}
                            {{-- No pack_type/pack_size here — stock is aggregated across
                                 multiple requisitions, each potentially with different pack types.
                                 All quantities are already in base units. --}}
                        </td>
                        <td class="text-gray-500 code-cell">{{ $item['item_code'] }}</td>
                        <td class="unit-cell">{{ $item['unit'] }}</td>
                        <td class="text-right issued-cell">{{ number_format($item['total_issued_pieces'], 2) }}</td>
                        <td class="text-right consumed-cell">{{ number_format($item['total_consumed'], 2) }}</td>
                        <td class="text-right returned-cell">{{ number_format($item['total_returned'], 2) }}</td>
                        <td class="text-right current-cell">
                            <span class="stock-badge {{ $stockClass }}">
                                {{ number_format($currentStock, 2) }} {{ $item['unit'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="stock-badge {{ $stockClass }}">{{ $stockText }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="9" class="text-center text-gray-500 py-8">
                            <i class="fas fa-box-open text-4xl mb-2 block"></i>
                            No stock items found. Items will appear here once issued from store.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-xs font-bold uppercase text-gray-500">Totals</td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">{{ number_format($totalIssued, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-amber-600">{{ number_format($totalConsumed, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-purple-600">{{ number_format($totalReturned, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600">{{ number_format($totalStockValue, 2) }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const liveSearch        = document.getElementById('liveSearch');
        const categoryFilter    = document.getElementById('categoryFilter');
        const resetBtn          = document.getElementById('resetFilters');
        const tableBody         = document.getElementById('tableBody');
        const searchResultCount = document.getElementById('searchResultCount');
        const noResultsRow      = document.getElementById('noResultsRow');

        // Store original innerHTML once so highlights can be removed cleanly
        const rows = Array.from(tableBody.querySelectorAll('tr[data-name]'));
        rows.forEach(row => {
            const nameCell = row.querySelector('.name-cell');
            const codeCell = row.querySelector('.code-cell');
            if (nameCell) nameCell.dataset.originalName = nameCell.innerHTML;
            if (codeCell) codeCell.dataset.originalCode = codeCell.innerHTML;
        });

        function performSearch() {
            const term     = liveSearch.value.toLowerCase().trim();
            const category = categoryFilter.value.toLowerCase();
            let visible    = 0;

            rows.forEach(row => {
                const matchesSearch   = !term || row.dataset.name.includes(term) || row.dataset.code.includes(term);
                const matchesCategory = !category || row.dataset.category === category;

                if (matchesSearch && matchesCategory) {
                    row.style.display = '';
                    visible++;
                    applyHighlight(row, term);
                } else {
                    row.style.display = 'none';
                    removeHighlight(row);
                }
            });

            // Renumber visible rows
            let counter = 1;
            rows.filter(r => r.style.display !== 'none').forEach(r => {
                const cell = r.querySelector('.counter-cell');
                if (cell) cell.textContent = counter++;
            });

            searchResultCount.textContent = term || category ? `${visible} result${visible !== 1 ? 's' : ''} found` : '';

            if (noResultsRow) {
                noResultsRow.style.display = visible === 0 && rows.length > 0 ? '' : 'none';
            }
        }

        function applyHighlight(row, term) {
            const nameCell = row.querySelector('.name-cell');
            const codeCell = row.querySelector('.code-cell');
            if (!term) { removeHighlight(row); return; }

            const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            if (nameCell) nameCell.innerHTML = nameCell.dataset.originalName.replace(regex, '<span class="highlight">$1</span>');
            if (codeCell) codeCell.innerHTML = codeCell.dataset.originalCode.replace(regex, '<span class="highlight">$1</span>');
        }

        function removeHighlight(row) {
            const nameCell = row.querySelector('.name-cell');
            const codeCell = row.querySelector('.code-cell');
            if (nameCell && nameCell.dataset.originalName) nameCell.innerHTML = nameCell.dataset.originalName;
            if (codeCell && codeCell.dataset.originalCode) codeCell.innerHTML = codeCell.dataset.originalCode;
        }

        liveSearch.addEventListener('input', performSearch);
        categoryFilter.addEventListener('change', performSearch);
        resetBtn.addEventListener('click', () => {
            liveSearch.value    = '';
            categoryFilter.value = '';
            performSearch();
        });
    });
</script>
@endsection
