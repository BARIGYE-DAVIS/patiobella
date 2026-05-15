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
    .stat-sold { border-left-color: #ef4444; }
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

    .pack-info {
        font-size: 0.65rem;
        color: #6b7280;
        margin-top: 0.25rem;
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
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
        <div class="stat-card stat-sold">
            <h3><i class="fas fa-shopping-cart mr-1"></i> Total Sold</h3>
            <div class="value">{{ number_format($totalSold, 2) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Sold to customers</p>
        </div>
        <div class="stat-card stat-remaining">
            <h3><i class="fas fa-box-open mr-1"></i> Current Stock</h3>
            <div class="value">{{ number_format($totalStockValue, 2) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Available for use/sale</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('restaurant.stock.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Live Search</label>
                <input type="text" name="search" id="liveSearch" class="filter-input" placeholder="🔍 Search by name or code..." value="{{ request('search') }}">
                <span id="searchResultCount" class="result-badge"></span>
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
                    <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-blue-700">
                        <i class="fas fa-search mr-1"></i> Apply
                    </button>
                    <a href="{{ route('restaurant.stock.index') }}" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-xs hover:bg-gray-400">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stock Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="stockTable">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">Item Name</th>
                        <th style="width: 8%">Code</th>
                        <th style="width: 5%">Unit</th>
                        <th style="width: 8%" class="text-right">Issued</th>
                        <th style="width: 8%" class="text-right">Sold</th>
                        <th style="width: 8%" class="text-right">Consumed</th>
                        <th style="width: 8%" class="text-right">Returned</th>
                        <th style="width: 10%" class="text-right">Current Stock</th>
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
                            $stockText = 'High';
                        } elseif ($currentStock > 5) {
                            $stockClass = 'stock-medium';
                            $stockText = 'Medium';
                        } else {
                            $stockClass = 'stock-low';
                            $stockText = 'Low';
                        }
                    @endphp
                    <tr data-name="{{ strtolower($item['item_name']) }}"
                        data-code="{{ strtolower($item['item_code']) }}">
                        <td class="text-center counter-cell">{{ $counter++ }}</td>
                        <td class="font-medium text-gray-800 name-cell">
                            {{ $item['item_name'] }}
                            @if(isset($item['pack_type']) && $item['pack_type'])
                                <div class="pack-info">
                                    <i class="fas fa-cubes"></i> {{ $item['pack_type'] }} ({{ $item['pack_size'] }} {{ $item['unit'] }}/pack)
                                </div>
                            @endif
                        </td>
                        <td class="text-gray-500 code-cell">{{ $item['item_code'] }}</td>
                        <td class="unit-cell">{{ $item['unit'] }}</td>
                        <td class="text-right issued-cell">{{ number_format($item['issued'], 2) }}</td>
                        <td class="text-right sold-cell">
                            <span class="font-semibold {{ $item['sold'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                {{ number_format($item['sold'], 2) }}
                            </span>
                        </td>
                        <td class="text-right consumed-cell">{{ number_format($item['consumed'], 2) }}</td>
                        <td class="text-right returned-cell">{{ number_format($item['returned'], 2) }}</td>
                        <td class="text-right current-cell">
                            <span class="stock-badge {{ $stockClass }}">
                                {{ number_format($currentStock, 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="stock-badge {{ $stockClass }}">{{ $stockText }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="10" class="text-center text-gray-500 py-8">
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
                        <td class="px-4 py-3 text-right font-bold text-red-600">{{ number_format($totalSold, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-amber-600">{{ number_format($totalConsumed, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-purple-600">{{ number_format($totalReturned, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600">{{ number_format($totalStockValue, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const liveSearch        = document.getElementById('liveSearch');
        const searchResultCount = document.getElementById('searchResultCount');
        const tableBody         = document.getElementById('tableBody');
        const noResultsRow      = document.getElementById('noResultsRow');

        function storeOriginalText() {
            const rows = tableBody.querySelectorAll('tr[data-name]');
            rows.forEach(row => {
                const nameCell = row.querySelector('.name-cell');
                const codeCell = row.querySelector('.code-cell');
                if (nameCell && !nameCell.dataset.originalText) {
                    nameCell.dataset.originalText = nameCell.textContent;
                }
                if (codeCell && !codeCell.dataset.originalText) {
                    codeCell.dataset.originalText = codeCell.textContent;
                }
            });
        }

        function performSearch() {
            const term = liveSearch.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('tr[data-name]');
            let visible = 0;

            rows.forEach(row => {
                const name = row.dataset.name || '';
                const code = row.dataset.code || '';
                const matchesSearch = !term || name.includes(term) || code.includes(term);

                if (matchesSearch) {
                    row.style.display = '';
                    visible++;
                    applyHighlight(row, term);
                } else {
                    row.style.display = 'none';
                    removeHighlight(row);
                }
            });

            let counter = 1;
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cell = row.querySelector('.counter-cell');
                    if (cell) cell.textContent = counter++;
                }
            });

            if (term) {
                searchResultCount.textContent = `${visible} result${visible !== 1 ? 's' : ''} found`;
            } else {
                searchResultCount.textContent = '';
            }

            if (noResultsRow) {
                noResultsRow.style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
            }
        }

        function applyHighlight(row, term) {
            if (!term) return;

            const nameCell = row.querySelector('.name-cell');
            const codeCell = row.querySelector('.code-cell');
            const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');

            if (nameCell && nameCell.dataset.originalText) {
                const originalText = nameCell.dataset.originalText;
                const packDiv = nameCell.querySelector('.pack-info');
                if (packDiv) {
                    const nameWithoutPack = originalText.replace(packDiv.textContent, '').trim();
                    const highlightedName = nameWithoutPack.replace(regex, '<span class="highlight">$1</span>');
                    nameCell.innerHTML = highlightedName + ' ' + packDiv.outerHTML;
                } else {
                    nameCell.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
                }
            }

            if (codeCell && codeCell.dataset.originalText) {
                codeCell.innerHTML = codeCell.dataset.originalText.replace(regex, '<span class="highlight">$1</span>');
            }
        }

        function removeHighlight(row) {
            const nameCell = row.querySelector('.name-cell');
            const codeCell = row.querySelector('.code-cell');

            if (nameCell && nameCell.dataset.originalText) {
                const packDiv = nameCell.querySelector('.pack-info');
                if (packDiv) {
                    const nameWithoutPack = nameCell.dataset.originalText.replace(packDiv.textContent, '').trim();
                    nameCell.innerHTML = nameWithoutPack + ' ' + packDiv.outerHTML;
                } else {
                    nameCell.innerHTML = nameCell.dataset.originalText;
                }
            }

            if (codeCell && codeCell.dataset.originalText) {
                codeCell.innerHTML = codeCell.dataset.originalText;
            }
        }

        storeOriginalText();

        if (liveSearch) {
            liveSearch.addEventListener('input', performSearch);
        }
    });
</script>
@endsection
