@extends('layouts.store')

@section('title', 'Stock Movements')

@section('page-title', 'Stock Movements History')

@section('content')
<style>
    .compact-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.7rem;
    }
    .compact-table th {
        padding: 10px 6px;
        background: #f8fafc;
        font-weight: 600;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }
    .compact-table td {
        padding: 8px 6px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .compact-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badge Styles */
    .badge-sm {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.6rem;
        font-weight: 600;
    }
    .badge-in { background: #d1fae5; color: #065f46; }
    .badge-out { background: #fee2e2; color: #991b1b; }

    /* Quantity Display */
    .qty-pack { font-weight: 600; color: #d97706; }
    .qty-direct { font-weight: 600; color: #059669; }
    .qty-base { font-weight: 700; color: #2563eb; }
    .stock-before { color: #f59e0b; font-weight: 500; }
    .stock-after { color: #10b981; font-weight: 500; }

    /* Filter Card */
    .filter-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }

    /* Toolbar Buttons */
    .toolbar-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        font-size: 0.7rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-excel { background: #10b981; color: white; }
    .btn-excel:hover { background: #059669; }
    .btn-pdf { background: #ef4444; color: white; }
    .btn-pdf:hover { background: #dc2626; }
    .btn-print { background: #6b7280; color: white; }
    .btn-print:hover { background: #4b5563; }

    .view-link { color: #3b82f6; font-size: 0.65rem; text-decoration: none; }
    .view-link:hover { text-decoration: underline; }

    .search-input-sm {
        padding: 6px 10px 6px 30px;
        font-size: 0.7rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        width: 100%;
    }
    .search-input-sm:focus {
        border-color: #3b82f6;
        outline: none;
    }
    .search-wrapper { position: relative; }
    .search-icon {
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        color: #94a3b8;
    }
    .loading-spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid #e2e8f0;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Column Widths */
    .col-movement { width: 12%; }
    .col-item { width: 15%; }
    .col-type { width: 8%; }
    .col-qty { width: 6%; text-align: center; }
    .col-breakdown { width: 15%; }
    .col-before { width: 8%; text-align: center; }
    .col-after { width: 8%; text-align: center; }
    .col-cost { width: 8%; text-align: right; }
    .col-value { width: 10%; text-align: right; }
    .col-date { width: 8%; }
    .col-action { width: 5%; text-align: center; }
</style>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center flex-wrap gap-3">
        <div>
            <h3 class="text-md font-semibold text-gray-800">Stock Movements</h3>
            <p class="text-xs text-gray-500">Track all stock in and out movements</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('store.stock-movements.export-excel', request()->query()) }}" class="toolbar-btn btn-excel">📊 Excel</a>
            <a href="{{ route('store.stock-movements.export-pdf', request()->query()) }}" class="toolbar-btn btn-pdf">📄 PDF</a>
            <button onclick="window.print()" class="toolbar-btn btn-print">🖨️ Print</button>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="filter-card mx-4 mt-3">
        <form method="GET" action="{{ route('store.stock-movements.index') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-7 gap-3">
            <div class="md:col-span-2">
                <div class="search-wrapper">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="liveSearch" class="search-input-sm" placeholder="Search by #, item, reason...">
                </div>
                <div id="searchSpinner" class="hidden text-center mt-1"><div class="loading-spinner mx-auto"></div></div>
            </div>
            <div>
                <select name="item_id" id="item_id" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-xs">
                    <option value="">All Items</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ Str::limit($item->name, 25) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="movement_type_id" id="movement_type_id" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-xs">
                    <option value="">All Types</option>
                    @foreach($movementTypes as $type)
                        <option value="{{ $type->id }}" {{ request('movement_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-xs"></div>
            <div><input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-xs"></div>
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700">Filter</button>
                <a href="{{ route('store.stock-movements.index') }}" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg text-xs hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    {{-- Results Table --}}
    <div class="px-4 pb-4">
        <div id="tableContainer">
            <table class="compact-table">
                <thead>
                    <tr>
                        <th class="col-movement">Movement #</th>
                        <th class="col-item">Item</th>
                        <th class="col-type">Type</th>
                        <th class="col-qty text-center">Qty</th>
                        <th class="col-breakdown">Breakdown</th>
                        <th class="col-before text-center">Before</th>
                        <th class="col-after text-center">After</th>
                        <th class="col-cost text-right">Unit Cost</th>
                        <th class="col-value text-right">Total Value</th>
                        <th class="col-date">Date</th>
                        <th class="col-action text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="movementsTableBody">
                    @include('store.stock_movements.partials.table_rows')
                </tbody>
            </table>
        </div>
        <div class="mt-4" id="paginationLinks">{{ $movements->appends(request()->query())->links() }}</div>
        <div id="noResults" class="hidden text-center py-8 text-gray-400 text-xs">No stock movements found matching your search.</div>
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const searchSpinner = document.getElementById('searchSpinner');
    const itemSelect = document.getElementById('item_id');
    const movementTypeSelect = document.getElementById('movement_type_id');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    const tableContainer = document.getElementById('tableContainer');
    const paginationLinks = document.getElementById('paginationLinks');
    const noResults = document.getElementById('noResults');

    function performSearch() {
        const query = searchInput.value.trim();
        const itemId = itemSelect.value;
        const movementTypeId = movementTypeSelect.value;
        const fromDate = dateFrom.value;
        const toDate = dateTo.value;

        let url = `{{ route('store.stock-movements.index') }}?${new URLSearchParams({
            search: query,
            item_id: itemId,
            movement_type_id: movementTypeId,
            date_from: fromDate,
            date_to: toDate,
            ajax: 1
        }).toString()}`;

        searchSpinner.classList.remove('hidden');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(data => {
                searchSpinner.classList.add('hidden');
                if (data.html) {
                    document.getElementById('movementsTableBody').innerHTML = data.html;
                    paginationLinks.innerHTML = data.pagination || '';
                    if (data.total === 0) {
                        tableContainer.classList.add('hidden');
                        noResults.classList.remove('hidden');
                    } else {
                        tableContainer.classList.remove('hidden');
                        noResults.classList.add('hidden');
                    }
                }
            })
            .catch(() => searchSpinner.classList.add('hidden'));
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    [itemSelect, movementTypeSelect, dateFrom, dateTo].forEach(el => { if (el) el.addEventListener('change', performSearch); });
</script>

<style media="print">
    .filter-card, .toolbar-btn, .pagination, .sidebar, header { display: none !important; }
    body { padding: 0; margin: 0; background: white; }
    .compact-table th { background: #e2e8f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
</style>
@endsection
