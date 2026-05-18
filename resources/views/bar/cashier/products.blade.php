{{-- resources/views/bar/cashier/products.blade.php --}}

@extends('layouts.bar-cashier')

@section('title', 'Bar Products')

@section('page-title', 'Bar Products')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        transition: all 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        margin-top: 0.5rem;
    }
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #6b7280;
    }
    .filter-bar {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
        table-layout: fixed;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .data-table th.text-right {
        text-align: right;
    }
    .data-table th.text-center {
        text-align: center;
    }
    .data-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .data-table tr:hover {
        background: #fef3c7;
    }
    .search-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .search-icon {
        position: absolute;
        left: 0.65rem;
        color: #9ca3af;
        font-size: 0.75rem;
        pointer-events: none;
    }
    .search-input {
        padding: 0.5rem 0.75rem 0.5rem 2rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 260px;
    }
    .search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59,130,246,0.15);
    }
    .stock-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .stock-high   { background: #d1fae5; color: #065f46; }
    .stock-medium { background: #fef3c7; color: #92400e; }
    .stock-low    { background: #fee2e2; color: #991b1b; }
    .pack-info {
        display: inline-block;
        background: #e5e7eb;
        padding: 0.1rem 0.4rem;
        border-radius: 4px;
        font-size: 0.65rem;
        margin-right: 0.25rem;
        margin-bottom: 0.2rem;
    }

    /* Column widths */
    .col-num    { width: 5%; }
    .col-name   { width: 32%; }
    .col-code   { width: 12%; }
    .col-unit   { width: 8%; }
    .col-price  { width: 15%; }
    .col-stock  { width: 28%; }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-wine-bottle mr-2"></i>
                    Bar Products
                </h2>
                <p class="text-blue-100 mt-1">Products available for sale from current bar stock</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-chart-line mr-1"></i> Total Products</p>
                <p class="text-2xl font-bold">{{ $totalProducts }}</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-label"><i class="fas fa-boxes mr-1"></i> Total Products</div>
            <div class="stat-value text-blue-600">{{ number_format($totalProducts) }}</div>
            <p class="text-xs text-gray-500 mt-1">Unique products in stock</p>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-label"><i class="fas fa-box-open mr-1"></i> Total Stock Value</div>
            <div class="stat-value text-green-600">{{ number_format($totalStockValue, 0) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Available for sale</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-bar">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <div class="flex gap-3 items-center flex-1">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="liveSearch" class="search-input" placeholder="Search by name or code..." value="{{ request('search') }}">
                </div>
                <span id="searchResultCount" class="text-xs text-gray-500"></span>
            </div>
            <div>
                <a href="{{ route('bar.cashier.pos') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm inline-flex items-center gap-1.5">
                    <i class="fas fa-cash-register"></i> Go to POS
                </a>
            </div>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="productsTable">
                <thead>
                    <tr>
                        <th class="col-num text-center">#</th>
                        <th class="col-name">Product Name</th>
                        <th class="col-code">Code</th>
                        <th class="col-unit">Unit</th>
                        <th class="col-price text-right">Selling Price</th>
                        <th class="col-stock text-right">Available Stock</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @php $counter = 1; @endphp
                    @forelse($products as $product)
                    @php
                        $availableStock = $product['available_stock'];
                        $unit           = $product['base_unit'];

                        if ($unit === 'bottle' || $unit === 'piece') {
                            $pieces          = $availableStock;
                            $packs           = 0;
                            $packSize        = 0;
                            $packType        = null;
                            $remainingPieces = $pieces;
                        } else {
                            $packSize        = $product['pack_size'] ?? 12;
                            $packType        = $product['pack_type'] ?? 'crate';
                            $packs           = floor($availableStock / $packSize);
                            $remainingPieces = $availableStock % $packSize;
                        }

                        if ($availableStock > 20) {
                            $stockClass = 'stock-high';
                        } elseif ($availableStock > 5) {
                            $stockClass = 'stock-medium';
                        } else {
                            $stockClass = 'stock-low';
                        }
                    @endphp
                    <tr class="product-row"
                        data-name="{{ strtolower($product['name']) }}"
                        data-code="{{ strtolower($product['item_code']) }}">

                        {{-- # --}}
                        <td class="text-center text-gray-500 counter-cell">{{ $counter++ }}</td>

                        {{-- Product Name --}}
                        <td class="name-cell">
                            <p class="font-medium text-gray-800">{{ $product['name'] }}</p>
                            @if($product['barcode'])
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <i class="fas fa-barcode mr-1"></i>{{ $product['barcode'] }}
                                </p>
                            @endif
                        </td>

                        {{-- Code --}}
                        <td class="text-gray-500 font-mono code-cell">{{ $product['item_code'] }}</td>

                        {{-- Unit --}}
                        <td class="text-gray-600 unit-cell">{{ $unit }}</td>

                        {{-- Selling Price --}}
                        <td class="text-right font-semibold text-green-600 whitespace-nowrap">
                            UGX {{ number_format($product['selling_price'], 0) }}
                        </td>

                        {{-- Available Stock --}}
                        <td class="text-right current-cell">
                            @if($unit === 'bottle' || $unit === 'piece')
                                <span class="stock-badge {{ $stockClass }}">
                                    {{ number_format($pieces, 0) }} {{ $unit }}(s)
                                </span>
                            @else
                                <div class="flex flex-wrap justify-end gap-1 items-center">
                                    @if($packs > 0)
                                        <span class="pack-info">{{ $packs }} {{ $packType }}(s)</span>
                                    @endif
                                    @if($remainingPieces > 0)
                                        <span class="pack-info">{{ $remainingPieces }} {{ $unit }}(s)</span>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <span class="stock-badge {{ $stockClass }}">
                                        {{ number_format($availableStock, 0) }} total {{ $unit }}(s)
                                    </span>
                                </div>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="6" class="text-center text-gray-500 py-8">
                            <i class="fas fa-box-open text-4xl mb-2 block"></i>
                            No products found. Products will appear here once items are issued from store.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
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

        function performSearch() {
            const term = liveSearch.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('.product-row');
            let visible = 0;

            rows.forEach(row => {
                const name = row.dataset.name || '';
                const code = row.dataset.code || '';
                const matches = !term || name.includes(term) || code.includes(term);
                row.style.display = matches ? '' : 'none';
                if (matches) visible++;
            });

            // Re-number visible rows
            let counter = 1;
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cell = row.querySelector('.counter-cell');
                    if (cell) cell.textContent = counter++;
                }
            });

            searchResultCount.textContent = term
                ? `${visible} result${visible !== 1 ? 's' : ''} found`
                : '';

            if (noResultsRow) {
                noResultsRow.style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
            }
        }

        if (liveSearch) {
            liveSearch.addEventListener('input', performSearch);
        }
    });
</script>
@endsection
