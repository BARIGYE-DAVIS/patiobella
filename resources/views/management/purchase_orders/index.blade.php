@extends('layouts.management')

@section('title', 'Purchase Orders')

@section('page-title', 'Purchase Orders')

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
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    .stat-card .value {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .stat-total { border-left-color: #3b82f6; }
    .stat-value { border-left-color: #8b5cf6; }
    .stat-pending { border-left-color: #f59e0b; }
    .stat-approved { border-left-color: #10b981; }
    .stat-completed { border-left-color: #06b6d4; }

    .filter-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge-status {
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
        display: inline-block;
    }
    .status-draft { background: #fef3c7; color: #92400e; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-sent { background: #dbeafe; color: #1e40af; }
    .status-partially_received { background: #fed7aa; color: #9c4221; }
    .status-fully_received { background: #a7f3d0; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }

    .table-container {
        overflow-x: auto;
        margin-bottom: 1.5rem;
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
        background: #f8fafc;
    }

    .filter-input {
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 100%;
    }

    .btn-export {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-excel { background: #10b981; color: white; }
    .btn-excel:hover { background: #059669; }
    .btn-pdf { background: #ef4444; color: white; }
    .btn-pdf:hover { background: #dc2626; }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    .chart-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        text-align: center;
    }
    .two-col {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .col { flex: 1; }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.7rem;
        margin-bottom: 5px;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }

    /* Tabs Styles */
    .tabs-container {
        margin-bottom: 1.5rem;
    }
    .tabs {
        display: flex;
        gap: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
        flex-wrap: wrap;
    }
    .tab-btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        color: #6b7280;
        margin-bottom: -2px;
    }
    .tab-btn:hover { color: #374151; }
    .tab-btn.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }
    .tab-content {
        display: none;
        padding: 1.5rem 0;
    }
    .tab-content.active { display: block; }

    /* Live Search */
    .live-search-input {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 300px;
    }
    .search-wrapper {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
    }
    .result-badge {
        font-size: 0.7rem;
        color: #6b7280;
    }
    .highlight {
        background-color: #fef3c7;
        font-weight: bold;
    }
    .no-results {
        text-align: center;
        padding: 2rem;
        color: #9ca3af;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #e5e7eb;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="space-y-6">

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="stat-card stat-total">
            <h3>Total POs</h3>
            <div class="value" id="totalPOs">{{ number_format($totalPOs) }}</div>
            <p class="text-xs text-gray-500 mt-1">All purchase orders</p>
        </div>
        <div class="stat-card stat-value">
            <h3>Total Value</h3>
            <div class="value" id="totalValue">UGX {{ number_format($totalValue, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">All orders combined</p>
        </div>
        <div class="stat-card stat-pending">
            <h3>Pending</h3>
            <div class="value" id="pendingPOs">{{ number_format($pendingPOs) }}</div>
            <p class="text-xs text-gray-500 mt-1">Draft / Awaiting approval</p>
        </div>
        <div class="stat-card stat-approved">
            <h3>Approved</h3>
            <div class="value" id="approvedPOs">{{ number_format($approvedPOs) }}</div>
            <p class="text-xs text-gray-500 mt-1">Approved & sent</p>
        </div>
        <div class="stat-card stat-completed">
            <h3>Completed</h3>
            <div class="value" id="completedPOs">{{ number_format($completedPOs) }}</div>
            <p class="text-xs text-gray-500 mt-1">Fully received</p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-btn active" data-tab="tab-analytics">📊 Analytics & Charts</button>
            <button class="tab-btn" data-tab="tab-orders">📋 Purchase Orders</button>
        </div>

        {{-- TAB 1: ANALYTICS & CHARTS --}}
        <div id="tab-analytics" class="tab-content active">
            <div class="two-col">
                {{-- Monthly Trend Chart --}}
                <div class="chart-container">
                    <div class="chart-title">Monthly Purchase Order Value Trend</div>
                    <canvas id="monthlyChart" width="400" height="250"></canvas>
                </div>

                {{-- Status Distribution Pie Chart --}}
                <div class="chart-container">
                    <div class="chart-title">Purchase Order Status Distribution</div>
                    <canvas id="statusChart" width="400" height="250"></canvas>
                </div>
            </div>

            {{-- Additional Stats --}}
            <div class="chart-container">
                <div class="chart-title">Quick Statistics</div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $purchaseOrders->count() }}</div>
                        <div class="text-xs text-gray-500">Showing in current view</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">UGX {{ number_format($purchaseOrders->sum('total_amount'), 2) }}</div>
                        <div class="text-xs text-gray-500">Total value in view</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">{{ $purchaseOrders->where('status', 'draft')->count() }}</div>
                        <div class="text-xs text-gray-500">Draft orders</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ $purchaseOrders->where('status', 'fully_received')->count() }}</div>
                        <div class="text-xs text-gray-500">Completed orders</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: PURCHASE ORDERS --}}
        <div id="tab-orders" class="tab-content">
            {{-- Live Search Bar --}}
            <div class="search-wrapper">
                <input type="text"
                       id="liveSearch"
                       class="live-search-input"
                       placeholder="🔍 Search by PO Number, Vendor, Item, or Status..."
                       autocomplete="off">
                <span id="searchResultCount" class="result-badge"></span>
                <div id="loadingIndicator" class="loading-spinner" style="display: none;"></div>
            </div>

            {{-- Filters --}}
            <div class="filter-card">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select id="filterStatus" class="filter-input">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Vendor</label>
                        <select id="filterVendor" class="filter-input">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" id="filterDateFrom" class="filter-input" value="{{ request('date_from') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" id="filterDateTo" class="filter-input" value="{{ request('date_to') }}">
                    </div>
                    <div class="flex items-end">
                        <button id="applyFilters" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-blue-700 w-full">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            {{-- Export Buttons --}}
            <div class="flex justify-end gap-2 mb-4">
                <button id="exportExcelBtn" class="btn-export btn-excel">
                    📊 Export to Excel
                </button>
                <button id="exportPdfBtn" class="btn-export btn-pdf">
                    📄 Export to PDF
                </button>
            </div>

            {{-- Purchase Orders Table (Live Search enabled) --}}
            <div class="section-title">
                <span>📋 Purchase Orders List</span>
                <span id="totalRecordsSpan" class="text-xs text-gray-500">{{ $purchaseOrders->total() }} records</span>
            </div>
            <div class="table-container">
                <table class="data-table" id="purchaseOrdersTable">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>PO Date</th>
                            <th>Vendor</th>
                            <th class="text-right">Total Amount</th>
                            <th>Status</th>
                            <th>Ordered By</th>
                            <th>Approved By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @include('management.purchase_orders.partials.table_rows', ['purchaseOrders' => $purchaseOrders])
                    </tbody>
                </table>
            </div>
            <div id="paginationLinks" class="mt-4">
                {{ $purchaseOrders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================================
        // TAB SWITCHING
        // ============================================================
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.getAttribute('data-tab');
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(tabId).classList.add('active');

                // Refresh charts if switching to analytics tab
                if (tabId === 'tab-analytics') {
                    setTimeout(() => {
                        if (window.monthlyChart) window.monthlyChart.update();
                        if (window.statusChart) window.statusChart.update();
                    }, 100);
                }
            });
        });

        // ============================================================
        // CHARTS
        // ============================================================
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        window.monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyData->pluck('month')->toArray()) !!},
                datasets: [{
                    label: 'Total Value (UGX)',
                    data: {!! json_encode($monthlyData->pluck('total')->toArray()) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top' } }
            }
        });

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        window.statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($statusData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($statusData['data']) !!},
                    backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#f97316', '#06b6d4', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // ============================================================
        // LIVE SEARCH
        // ============================================================
        const searchInput = document.getElementById('liveSearch');
        const tableBody = document.getElementById('tableBody');
        const searchResultCount = document.getElementById('searchResultCount');
        const totalRecordsSpan = document.getElementById('totalRecordsSpan');

        let searchTimeout;

        function performLiveSearch() {
            const searchTerm = searchInput.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm) || searchTerm === '') {
                    row.style.display = '';
                    visibleCount++;
                    // Highlight matching text
                    if (searchTerm !== '') {
                        highlightText(row, searchTerm);
                    } else {
                        removeHighlight(row);
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            // Update result count
            searchResultCount.textContent = `${visibleCount} results found`;

            // Show no results message if needed
            let noResultsRow = document.getElementById('noResultsRow');
            if (visibleCount === 0 && rows.length > 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = '<td colspan="8" class="text-center text-gray-500 py-4">No purchase orders match your search</td>';
                    tableBody.appendChild(noResultsRow);
                }
                noResultsRow.style.display = '';
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }

        function highlightText(row, term) {
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                const originalText = cell.getAttribute('data-original-text');
                if (!originalText) {
                    cell.setAttribute('data-original-text', cell.innerHTML);
                }
                const text = cell.getAttribute('data-original-text');
                if (text && term) {
                    const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    cell.innerHTML = text.replace(regex, '<span class="highlight">$1</span>');
                }
            });
        }

        function removeHighlight(row) {
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                const original = cell.getAttribute('data-original-text');
                if (original) {
                    cell.innerHTML = original;
                }
            });
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performLiveSearch, 300);
        });

        // ============================================================
        // FILTERS & EXPORTS
        // ============================================================
        function getFilterParams() {
            const params = new URLSearchParams();
            if (document.getElementById('filterStatus').value) {
                params.append('status', document.getElementById('filterStatus').value);
            }
            if (document.getElementById('filterVendor').value) {
                params.append('vendor_id', document.getElementById('filterVendor').value);
            }
            if (document.getElementById('filterDateFrom').value) {
                params.append('date_from', document.getElementById('filterDateFrom').value);
            }
            if (document.getElementById('filterDateTo').value) {
                params.append('date_to', document.getElementById('filterDateTo').value);
            }
            return params;
        }

        document.getElementById('applyFilters').addEventListener('click', function() {
            const params = getFilterParams();
            window.location.href = '{{ route("management.purchase-orders.index") }}?' + params.toString();
        });

        document.getElementById('exportExcelBtn').addEventListener('click', function() {
            const params = getFilterParams();
            window.location.href = '{{ route("management.purchase-orders.export.excel") }}?' + params.toString();
        });

        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            const params = getFilterParams();
            window.location.href = '{{ route("management.purchase-orders.export.pdf") }}?' + params.toString();
        });

        // Enter key on search triggers filter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performLiveSearch();
            }
        });
    });
</script>
@endsection
