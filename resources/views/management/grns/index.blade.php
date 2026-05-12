@extends('layouts.management')

@section('title', 'Goods Received Notes')

@section('page-title', 'Goods Received Notes')

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
    .stat-completed { border-left-color: #10b981; }
    .stat-updated { border-left-color: #06b6d4; }

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
    .status-completed { background: #d1fae5; color: #065f46; }
    .status-inventory_updated { background: #dbeafe; color: #1e40af; }

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
    .sub-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding-left: 1rem;
    }
    .sub-tab-btn {
        padding: 0.4rem 1rem;
        font-size: 0.75rem;
        background: #f3f4f6;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s;
        color: #6b7280;
    }
    .sub-tab-btn:hover { background: #e5e7eb; }
    .sub-tab-btn.active {
        background: #3b82f6;
        color: white;
    }
    .tab-content {
        display: none;
        padding: 1.5rem 0;
    }
    .tab-content.active { display: block; }
    .sub-tab-content {
        display: none;
    }
    .sub-tab-content.active { display: block; }

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
    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }
    .trend-neutral { color: #6b7280; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="space-y-6">

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="stat-card stat-total">
            <h3>Total GRNs</h3>
            <div class="value">{{ number_format($totalGRNs ?? 0) }}</div>
            <p class="text-xs text-gray-500 mt-1">All goods received notes</p>
        </div>
        <div class="stat-card stat-value">
            <h3>Total Value</h3>
            <div class="value">UGX {{ number_format($totalValue ?? 0, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">All received goods</p>
        </div>
        <div class="stat-card stat-pending">
            <h3>Draft</h3>
            <div class="value">{{ number_format($pendingGRNs ?? 0) }}</div>
            <p class="text-xs text-gray-500 mt-1">Pending completion</p>
        </div>
        <div class="stat-card stat-completed">
            <h3>Completed</h3>
            <div class="value">{{ number_format($completedGRNs ?? 0) }}</div>
            <p class="text-xs text-gray-500 mt-1">Ready for inventory</p>
        </div>
        <div class="stat-card stat-updated">
            <h3>Inventory Updated</h3>
            <div class="value">{{ number_format($inventoryUpdatedGRNs ?? 0) }}</div>
            <p class="text-xs text-gray-500 mt-1">Stock added</p>
        </div>
    </div>

    {{-- MAIN TABS --}}
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-btn active" data-tab="tab-analytics">📊 Analytics & Charts</button>
            <button class="tab-btn" data-tab="tab-grns">📋 Goods Received Notes</button>
        </div>

        {{-- TAB 1: ANALYTICS & CHARTS --}}
        <div id="tab-analytics" class="tab-content active">

            <div class="sub-tabs">
                <button class="sub-tab-btn active" data-subtab="daily">📅 Daily Trend</button>
                <button class="sub-tab-btn" data-subtab="weekly">📆 Weekly Trend</button>
                <button class="sub-tab-btn" data-subtab="monthly">📊 Monthly Trend</button>
            </div>

            {{-- DAILY TREND --}}
            <div id="daily" class="sub-tab-content active">
                <div class="two-col">
                    <div class="chart-container">
                        <div class="chart-title">Daily GRN Value Trend (Last 30 Days)</div>
                        <canvas id="dailyChart" width="400" height="250"></canvas>
                    </div>
                    <div class="chart-container">
                        <div class="chart-title">Daily GRN Count Trend (Last 30 Days)</div>
                        <canvas id="dailyCountChart" width="400" height="250"></canvas>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="chart-title">Daily GRN Summary</div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Average Daily Value</div>
                            <div class="text-lg font-bold text-blue-600">UGX {{ number_format($avgDailyValue ?? 0, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Highest Day Value</div>
                            <div class="text-lg font-bold text-green-600">UGX {{ number_format($maxDailyValue ?? 0, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Peak Day</div>
                            <div class="text-sm font-bold text-purple-600">{{ $peakDay ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WEEKLY TREND --}}
            <div id="weekly" class="sub-tab-content">
                <div class="two-col">
                    <div class="chart-container">
                        <div class="chart-title">Weekly GRN Value Trend (Last 12 Weeks)</div>
                        <canvas id="weeklyChart" width="400" height="250"></canvas>
                    </div>
                    <div class="chart-container">
                        <div class="chart-title">Weekly GRN Count Trend (Last 12 Weeks)</div>
                        <canvas id="weeklyCountChart" width="400" height="250"></canvas>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="chart-title">Weekly GRN Summary</div>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Average Weekly Value</div>
                            <div class="text-lg font-bold text-blue-600">UGX {{ number_format($avgWeeklyValue ?? 0, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Highest Week Value</div>
                            <div class="text-lg font-bold text-green-600">UGX {{ number_format($maxWeeklyValue ?? 0, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Week-over-Week Change</div>
                            <div class="text-lg font-bold">
                                @php $wowChangeValue = $wowChange ?? 0; @endphp
                                {{ $wowChangeValue >= 0 ? '+' : '' }}{{ number_format($wowChangeValue, 1) }}%
                                <span class="{{ $wowChangeValue >= 0 ? 'trend-up' : 'trend-down' }}">{{ $wowChangeValue >= 0 ? '↑' : '↓' }}</span>
                            </div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Peak Week</div>
                            <div class="text-sm font-bold text-purple-600">{{ $peakWeek ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MONTHLY TREND --}}
            <div id="monthly" class="sub-tab-content">
                <div class="two-col">
                    <div class="chart-container">
                        <div class="chart-title">Monthly GRN Value Trend</div>
                        <canvas id="monthlyChart" width="400" height="250"></canvas>
                    </div>
                    <div class="chart-container">
                        <div class="chart-title">Monthly GRN Count Trend</div>
                        <canvas id="monthlyCountChart" width="400" height="250"></canvas>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="chart-title">Monthly GRN Summary</div>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Average Monthly Value</div>
                            <div class="text-lg font-bold text-blue-600">UGX {{ number_format($avgMonthlyValue ?? 0, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Highest Month Value</div>
                            <div class="text-lg font-bold text-green-600">UGX {{ number_format($maxMonthlyValue ?? 0, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Month-over-Month Change</div>
                            <div class="text-lg font-bold">
                                @php $momChangeValue = $momChange ?? 0; @endphp
                                {{ $momChangeValue >= 0 ? '+' : '' }}{{ number_format($momChangeValue, 1) }}%
                                <span class="{{ $momChangeValue >= 0 ? 'trend-up' : 'trend-down' }}">{{ $momChangeValue >= 0 ? '↑' : '↓' }}</span>
                            </div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500">Peak Month</div>
                            <div class="text-sm font-bold text-purple-600">{{ $peakMonth ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Distribution Pie Chart --}}
            <div class="two-col">
                <div class="chart-container">
                    <div class="chart-title">GRN Status Distribution</div>
                    <canvas id="statusChart" width="400" height="250"></canvas>
                </div>
                <div class="chart-container">
                    <div class="chart-title">Quick Statistics</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $grns->count() }}</div>
                            <div class="text-xs text-gray-500">Showing in current view</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">UGX {{ number_format($grns->sum('grn_total_amount'), 2) }}</div>
                            <div class="text-xs text-gray-500">Total value in view</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600">{{ $grns->where('status', 'draft')->count() }}</div>
                            <div class="text-xs text-gray-500">Draft GRNs</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $grns->where('status', 'inventory_updated')->count() }}</div>
                            <div class="text-xs text-gray-500">Inventory updated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: GOODS RECEIVED NOTES --}}
        <div id="tab-grns" class="tab-content">
            <div class="search-wrapper">
                <input type="text" id="liveSearch" class="live-search-input" placeholder="🔍 Search by GRN #, Vendor, Delivery Note..." autocomplete="off">
                <span id="searchResultCount" class="result-badge"></span>
            </div>

            <div class="filter-card">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select id="filterStatus" class="filter-input">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Vendor</label>
                        <select id="filterVendor" class="filter-input">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" id="filterDateFrom" class="filter-input">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" id="filterDateTo" class="filter-input">
                    </div>
                    <div class="flex items-end">
                        <button id="applyFilters" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-blue-700 w-full">Apply Filters</button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mb-4">
                <button id="exportExcelBtn" class="btn-export btn-excel">📊 Export to Excel</button>
                <button id="exportPdfBtn" class="btn-export btn-pdf">📄 Export to PDF</button>
            </div>

            <div class="section-title">
                <span>📋 Goods Received Notes List</span>
                <span class="text-xs text-gray-500">{{ $grns->total() }} records</span>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>GRN Number</th>
                            <th>Received Date</th>
                            <th>Vendor</th>
                            <th>PO Number</th>
                            <th>Delivery Note #</th>
                            <th class="text-right">Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @include('management.grns.partials.table_rows', ['grns' => $grns])
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $grns->links() }}</div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // TAB SWITCHING
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.getAttribute('data-tab');
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // SUB TAB SWITCHING
        const subTabBtns = document.querySelectorAll('.sub-tab-btn');
        const subTabContents = document.querySelectorAll('.sub-tab-content');

        subTabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const subTabId = btn.getAttribute('data-subtab');
                subTabBtns.forEach(b => b.classList.remove('active'));
                subTabContents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(subTabId).classList.add('active');
            });
        });

        // DAILY CHART
        @if(isset($dailyLabels) && count($dailyLabels) > 0)
        const dailyCtx = document.getElementById('dailyChart')?.getContext('2d');
        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dailyLabels) !!},
                    datasets: [{
                        label: 'GRN Value (UGX)',
                        data: {!! json_encode($dailyValues) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderRadius: 4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }
        @endif

        // DAILY COUNT CHART
        @if(isset($dailyLabels) && count($dailyLabels) > 0)
        const dailyCountCtx = document.getElementById('dailyCountChart')?.getContext('2d');
        if (dailyCountCtx) {
            new Chart(dailyCountCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyLabels) !!},
                    datasets: [{
                        label: 'Number of GRNs',
                        data: {!! json_encode($dailyCounts) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }
        @endif

        // WEEKLY CHART
        @if(isset($weeklyLabels) && count($weeklyLabels) > 0)
        const weeklyCtx = document.getElementById('weeklyChart')?.getContext('2d');
        if (weeklyCtx) {
            new Chart(weeklyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($weeklyLabels) !!},
                    datasets: [{
                        label: 'GRN Value (UGX)',
                        data: {!! json_encode($weeklyValues) !!},
                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
                        borderRadius: 4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }
        @endif

        // WEEKLY COUNT CHART
        @if(isset($weeklyLabels) && count($weeklyLabels) > 0)
        const weeklyCountCtx = document.getElementById('weeklyCountChart')?.getContext('2d');
        if (weeklyCountCtx) {
            new Chart(weeklyCountCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($weeklyLabels) !!},
                    datasets: [{
                        label: 'Number of GRNs',
                        data: {!! json_encode($weeklyCounts) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }
        @endif

        // MONTHLY CHART
        @if(isset($monthlyLabels) && count($monthlyLabels) > 0)
        const monthlyCtx = document.getElementById('monthlyChart')?.getContext('2d');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels) !!},
                    datasets: [{
                        label: 'Total Value (UGX)',
                        data: {!! json_encode($monthlyValues) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }
        @endif

        // MONTHLY COUNT CHART
        @if(isset($monthlyLabels) && count($monthlyLabels) > 0)
        const monthlyCountCtx = document.getElementById('monthlyCountChart')?.getContext('2d');
        if (monthlyCountCtx) {
            new Chart(monthlyCountCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyLabels) !!},
                    datasets: [{
                        label: 'Number of GRNs',
                        data: {!! json_encode($monthlyCounts) !!},
                        backgroundColor: 'rgba(236, 72, 153, 0.7)',
                        borderRadius: 4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }
        @endif

        // STATUS PIE CHART
        const statusCtx = document.getElementById('statusChart')?.getContext('2d');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($statusData['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($statusData['data']) !!},
                        backgroundColor: ['#f59e0b', '#10b981', '#3b82f6'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
            });
        }

        // LIVE SEARCH
        const searchInput = document.getElementById('liveSearch');
        const tableBody = document.getElementById('tableBody');
        const searchResultCount = document.getElementById('searchResultCount');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr');
                let count = 0;
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(term)) {
                        row.style.display = '';
                        count++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                searchResultCount.textContent = count + ' results found';
            });
        }

        // FILTERS
        document.getElementById('applyFilters')?.addEventListener('click', function() {
            const params = new URLSearchParams();
            const status = document.getElementById('filterStatus').value;
            const vendor = document.getElementById('filterVendor').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            if (status) params.append('status', status);
            if (vendor) params.append('vendor_id', vendor);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            window.location.href = '{{ route("management.grns.index") }}?' + params.toString();
        });

        document.getElementById('exportExcelBtn')?.addEventListener('click', function() {
            const params = new URLSearchParams();
            const status = document.getElementById('filterStatus').value;
            const vendor = document.getElementById('filterVendor').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            if (status) params.append('status', status);
            if (vendor) params.append('vendor_id', vendor);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            window.location.href = '{{ route("management.grns.export.excel") }}?' + params.toString();
        });

        document.getElementById('exportPdfBtn')?.addEventListener('click', function() {
            const params = new URLSearchParams();
            const status = document.getElementById('filterStatus').value;
            const vendor = document.getElementById('filterVendor').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            if (status) params.append('status', status);
            if (vendor) params.append('vendor_id', vendor);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            window.location.href = '{{ route("management.grns.export.pdf") }}?' + params.toString();
        });
    });
</script>
@endsection
