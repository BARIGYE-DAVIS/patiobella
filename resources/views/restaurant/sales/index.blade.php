{{-- resources/views/restaurant/sales/index.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'Sales Reports')
@section('page-title', 'Sales Analytics Dashboard')

@section('content')

<style>
    /* =========================================
       BASE & LAYOUT
    ========================================= */
    .space-y-6 > * + * { margin-top: 1.5rem; }

    /* =========================================
       STAT CARDS
    ========================================= */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .stat-card .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
    }
    .stat-card .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin-top: 0.5rem;
        line-height: 1.2;
    }

    /* =========================================
       FILTER BAR
    ========================================= */
    .filter-bar {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
    }

    /* =========================================
       TABS
    ========================================= */
    .tabs {
        display: flex;
        gap: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .tab-btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        color: #6b7280;
        transition: color 0.2s, border-color 0.2s;
    }
    .tab-btn.active  { color: #ea580c; border-bottom-color: #ea580c; }
    .tab-btn:hover:not(.active) { color: #374151; }

    .tab-content         { display: none; }
    .tab-content.active  { display: block; }

    /* =========================================
       CHART CONTAINERS
    ========================================= */
    .chart-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        min-height: 400px;
    }
    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        border-left: 4px solid #ea580c;
        padding-left: 0.75rem;
        margin-bottom: 1.5rem;
    }
    canvas {
        max-height: 350px;
        width: 100% !important;
    }

    /* =========================================
       PILL / FILTER BUTTONS
    ========================================= */
    .pill-btn {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: background 0.2s, color 0.2s;
        background: #f3f4f6;
        color: #374151;
    }
    .pill-btn.active         { background: #ea580c; color: white; }
    .pill-btn:not(.active):hover { background: #e5e7eb; }

    /* =========================================
       SALES LIST PANEL
    ========================================= */
    .list-panel {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
    }
    .list-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    /* =========================================
       SEARCH INPUT
    ========================================= */
    .search-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .search-wrapper .search-icon {
        position: absolute;
        left: 0.65rem;
        color: #9ca3af;
        font-size: 0.75rem;
        pointer-events: none;
    }
    .search-input {
        padding: 0.45rem 0.75rem 0.45rem 2rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.8rem;
        width: 260px;
        outline: none;
        transition: border-color 0.2s;
    }
    .search-input:focus { border-color: #ea580c; }

    /* =========================================
       DATA TABLE
    ========================================= */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
    }

    /* Sticky header */
    .data-table thead tr th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f8fafc;
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .data-table thead tr th.text-right { text-align: right; }
    .data-table thead tr th.text-center { text-align: center; }

    /* Body rows */
    .data-table tbody tr td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #374151;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td    { background: #fff7ed; }

    /* Column-specific alignment */
    .data-table .col-amount { text-align: right; font-weight: 600; white-space: nowrap; }
    .data-table .col-action { text-align: center; }

    /* Invoice number */
    .invoice-num {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e293b;
    }

    /* Date/time */
    .cell-datetime { font-size: 0.75rem; color: #64748b; white-space: nowrap; }

    /* Cashier */
    .cell-cashier { font-size: 0.8rem; }

    /* =========================================
       ITEM BADGES
    ========================================= */
    .badge {
        display: inline-block;
        padding: 0.15rem 0.5rem;
        border-radius: 10px;
        font-size: 0.65rem;
        font-weight: 600;
        margin: 0.1rem 0.1rem 0.1rem 0;
        white-space: nowrap;
    }
    .badge-menu  { background: #d1fae5; color: #065f46; }
    .badge-other { background: #fef3c7; color: #92400e; }

    /* =========================================
       PAYMENT METHOD PILL
    ========================================= */
    .pay-method { font-size: 0.78rem; white-space: nowrap; }

    /* =========================================
       VIEW BUTTON
    ========================================= */
    .view-btn {
        display: inline-block;
        background: #ea580c;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.2s;
    }
    .view-btn:hover { background: #c2410c; }

    /* =========================================
       EMPTY STATE
    ========================================= */
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #9ca3af;
    }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 0.5rem; }
</style>

<div class="space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-chart-line mr-2"></i>Sales Analytics Dashboard
                </h2>
                <p class="text-orange-100 mt-1 text-sm">Comprehensive sales reports and trends</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-calendar mr-1"></i> Reporting Period</p>
                <p class="text-lg font-bold">
                    {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
                    &ndash;
                    {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ===== DATE FILTER BAR ===== --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('restaurant.sales.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Quick Period</label>
                <select name="period" id="periodSelect" class="w-full p-2 border border-gray-300 rounded-lg text-sm">
                    <option value="today"      {{ $period == 'today'      ? 'selected' : '' }}>Today</option>
                    <option value="yesterday"  {{ $period == 'yesterday'  ? 'selected' : '' }}>Yesterday</option>
                    <option value="this_week"  {{ $period == 'this_week'  ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="custom"     {{ request('start_date')   ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <div id="customDateRange"
                 style="display: {{ request('start_date') ? 'flex' : 'none' }}; gap: 8px; grid-column: span 2;">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date"
                           class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                           value="{{ request('start_date', $from) }}">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date"
                           class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                           value="{{ request('end_date', $to) }}">
                </div>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-sync-alt mr-1"></i> Apply
                </button>
                <a href="{{ route('restaurant.sales.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-label"><i class="fas fa-dollar-sign mr-1"></i> Total Revenue</div>
            <div class="stat-value text-green-600">UGX {{ number_format($salesData['total_sales'], 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-label"><i class="fas fa-receipt mr-1"></i> Total Orders</div>
            <div class="stat-value text-blue-600">{{ number_format($salesData['total_orders']) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-label"><i class="fas fa-chart-line mr-1"></i> Avg Order Value</div>
            <div class="stat-value text-orange-600">UGX {{ number_format($salesData['average_order_value'], 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-label"><i class="fas fa-boxes mr-1"></i> Total Items Sold</div>
            <div class="stat-value text-red-600">{{ number_format($salesData['total_items_sold']) }}</div>
        </div>
    </div>

    {{-- ===== TABS ===== --}}
    <div class="tabs">
        <button class="tab-btn active" data-tab="analytics">
            <i class="fas fa-chart-pie mr-2"></i> Analytics &amp; Charts
        </button>
        <button class="tab-btn" data-tab="sales-list">
            <i class="fas fa-list mr-2"></i> Sales List
        </button>
    </div>

    {{-- ===== TAB 1: ANALYTICS ===== --}}
    <div id="tab-analytics" class="tab-content active">

        {{-- Product type filter --}}
        <div class="filter-bar mb-6">
            <div class="flex flex-wrap gap-3 items-center">
                <span class="text-xs font-semibold text-gray-600">Filter Products in Chart:</span>
                <div class="flex gap-2">
                    <button type="button" class="pill-btn active" data-filter="all">
                        <i class="fas fa-utensils mr-1"></i> All Items
                    </button>
                    <button type="button" class="pill-btn" data-filter="menu">
                        <i class="fas fa-hamburger mr-1"></i> Menu Items
                    </button>
                    <button type="button" class="pill-btn" data-filter="inventory">
                        <i class="fas fa-box mr-1"></i> Other Items
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chart-line text-orange-600 mr-2"></i> Daily Sales Trend
                </div>
                <canvas id="dailyTrendChart"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-trophy text-orange-600 mr-2"></i> Top Selling Products
                    <span id="productFilterLabel" class="text-xs text-gray-400 ml-1">(All Items)</span>
                </div>
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-clock text-orange-600 mr-2"></i> Sales by Hour
                </div>
                <canvas id="hourlySalesChart"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-credit-card text-orange-600 mr-2"></i> Payment Methods
                </div>
                <canvas id="paymentMethodsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ===== TAB 2: SALES LIST ===== --}}
    <div id="tab-sales-list" class="tab-content">
        <div class="list-panel">

            {{-- List header: filters + search --}}
            <div class="list-panel-header">
                <div class="flex gap-2 flex-wrap">
                    <button type="button" class="pill-btn active" data-list-filter="all">
                        <i class="fas fa-list mr-1"></i> All Sales
                    </button>
                    <button type="button" class="pill-btn" data-list-filter="menu">
                        <i class="fas fa-hamburger mr-1"></i> Menu Items
                    </button>
                    <button type="button" class="pill-btn" data-list-filter="inventory">
                        <i class="fas fa-box mr-1"></i> Other Items
                    </button>
                </div>

                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           id="salesSearchInput"
                           class="search-input"
                           placeholder="Search invoice # or item name…">
                </div>
            </div>

            {{-- Table --}}
            <div style="max-height: 520px; overflow-y: auto;">
                @php
                    $salesList = \App\Models\SalesOrder::with('items')
                        ->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to)
                        ->where('payment_status', 'paid')
                        ->orderBy('created_at', 'desc')
                        ->get();
                @endphp

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date &amp; Time</th>
                            <th>Cashier</th>
                            <th>Items</th>
                            <th>Payment</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="salesListBody">
                        @forelse($salesList as $sale)
                        <tr class="sale-row"
                            data-order-number="{{ $sale->order_number }}"
                            data-items="{{ implode(',', $sale->items->pluck('item_name')->toArray()) }}">

                            <td><span class="invoice-num">{{ $sale->order_number }}</span></td>

                            <td class="cell-datetime">
                                {{ $sale->created_at->format('d/m/Y') }}<br>
                                <span class="text-gray-400">{{ $sale->created_at->format('h:i A') }}</span>
                            </td>

                            <td class="cell-cashier">{{ $sale->cashier->first_name ?? '—' }}</td>

                            <td>
                                @foreach($sale->items as $item)
                                    <span class="badge {{ in_array($item->item_name, $menuItemNames ?? []) ? 'badge-menu' : 'badge-other' }}">
                                        {{ $item->item_name }} &times;{{ $item->quantity }}
                                    </span>
                                @endforeach
                            </td>

                            <td class="pay-method">
                                @if($sale->payment_method === 'cash')
                                    <span class="text-green-600">💵 Cash</span>
                                @elseif($sale->payment_method === 'card')
                                    <span class="text-blue-600">💳 Card</span>
                                @else
                                    <span class="text-orange-600">📱 Mobile Money</span>
                                @endif
                            </td>

                            <td class="col-amount">UGX {{ number_format($sale->total_amount, 0) }}</td>

                            <td class="col-action">
                                <a href="{{ route('restaurant.sales.show', $sale->id) }}" class="view-btn">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-receipt"></i>
                                    No sales found for this period
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>{{-- end .space-y-6 --}}

{{-- ===== SCRIPTS ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    'use strict';

    /* -------------------------------------------------------
       DATA FROM SERVER
    ------------------------------------------------------- */
    const menuItemNames   = @json($menuItemNames ?? []);
    const allProducts     = @json($topProducts);
    const dailyTrendData  = @json($dailyTrend);
    const hourlyData      = @json($hourlySales);
    const paymentData     = @json($paymentMethods);

    /* -------------------------------------------------------
       CATEGORISE PRODUCTS
    ------------------------------------------------------- */
    const menuProducts      = allProducts.filter(p =>  menuItemNames.includes(p.item_name));
    const inventoryProducts = allProducts.filter(p => !menuItemNames.includes(p.item_name));

    /* -------------------------------------------------------
       SHARED CHART OPTIONS
    ------------------------------------------------------- */
    const baseLineOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'top' } },
    };

    function ugxTooltip(context) {
        return 'UGX ' + context.raw.toLocaleString();
    }

    /* -------------------------------------------------------
       DAILY TREND CHART
    ------------------------------------------------------- */
    const dailyTrendChart = new Chart(
        document.getElementById('dailyTrendChart'),
        {
            type: 'line',
            data: {
                labels:   dailyTrendData.map(d => d.date),
                datasets: [{
                    label:           'Sales (UGX)',
                    data:            dailyTrendData.map(d => d.total),
                    borderColor:     '#ea580c',
                    backgroundColor: 'rgba(234,88,12,0.1)',
                    tension:         0.4,
                    fill:            true,
                    pointRadius:     4,
                    pointHoverRadius: 6,
                }],
            },
            options: {
                ...baseLineOptions,
                plugins: {
                    ...baseLineOptions.plugins,
                    tooltip: { callbacks: { label: ugxTooltip } },
                },
            },
        }
    );

    /* -------------------------------------------------------
       HOURLY SALES CHART
    ------------------------------------------------------- */
    const hourLabels = Array.from({ length: 24 }, (_, i) => {
        const ampm = i >= 12 ? 'PM' : 'AM';
        const h    = i % 12 || 12;
        return `${h}:00 ${ampm}`;
    });

    const hourlySalesChart = new Chart(
        document.getElementById('hourlySalesChart'),
        {
            type: 'line',
            data: {
                labels:   hourLabels,
                datasets: [{
                    label:           'Sales (UGX)',
                    data:            Object.values(hourlyData),
                    borderColor:     '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    tension:         0.4,
                    fill:            true,
                    pointRadius:     3,
                }],
            },
            options: {
                ...baseLineOptions,
                plugins: {
                    ...baseLineOptions.plugins,
                    tooltip: { callbacks: { label: ugxTooltip } },
                },
            },
        }
    );

    /* -------------------------------------------------------
       PAYMENT METHODS PIE CHART
    ------------------------------------------------------- */
    const paymentLabelMap = { cash: '💵 Cash', card: '💳 Card', mobile_money: '📱 Mobile Money' };
    const paymentLabels   = paymentData.map(p => paymentLabelMap[p.payment_method] ?? p.payment_method);
    const paymentTotals   = paymentData.map(p => p.total);

    const paymentMethodsChart = new Chart(
        document.getElementById('paymentMethodsChart'),
        {
            type: 'pie',
            data: {
                labels:   paymentLabels,
                datasets: [{
                    data:            paymentTotals,
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const total      = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${context.label}: UGX ${context.raw.toLocaleString()} (${percentage}%)`;
                            },
                        },
                    },
                },
            },
        }
    );

    /* -------------------------------------------------------
       TOP PRODUCTS CHART (dynamic filter)
    ------------------------------------------------------- */
    let topProductsChart = null;

    function buildTopProductsChart(filter) {
        const filterConfig = {
            all:       { data: allProducts,      color: '#f59e0b', label: '(All Items)' },
            menu:      { data: menuProducts,     color: '#10b981', label: '(Menu Items Only)' },
            inventory: { data: inventoryProducts, color: '#3b82f6', label: '(Other Items Only)' },
        };
        const { data, color, label } = filterConfig[filter] ?? filterConfig.all;

        document.getElementById('productFilterLabel').textContent = label;

        if (topProductsChart) topProductsChart.destroy();

        topProductsChart = new Chart(
            document.getElementById('topProductsChart'),
            {
                type: 'bar',
                data: {
                    labels:   data.map(p => p.item_name.length > 25 ? p.item_name.slice(0, 25) + '…' : p.item_name),
                    datasets: [{
                        label:           'Revenue (UGX)',
                        data:            data.map(p => p.total_revenue),
                        backgroundColor: color,
                        borderRadius:    8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { callbacks: { label: ugxTooltip } },
                    },
                },
            }
        );
    }

    buildTopProductsChart('all');

    /* -------------------------------------------------------
       PRODUCT FILTER BUTTONS (chart tab)
    ------------------------------------------------------- */
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            buildTopProductsChart(btn.dataset.filter);
        });
    });

    /* -------------------------------------------------------
       TABS
    ------------------------------------------------------- */
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.dataset.tab;

            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(`tab-${tabId}`).classList.add('active');

            // Resize charts when switching back to analytics
            if (tabId === 'analytics') {
                setTimeout(() => {
                    [dailyTrendChart, hourlySalesChart, paymentMethodsChart].forEach(c => c?.update());
                    buildTopProductsChart(
                        document.querySelector('[data-filter].active')?.dataset.filter ?? 'all'
                    );
                }, 80);
            }
        });
    });

    /* -------------------------------------------------------
       CUSTOM DATE RANGE TOGGLE
    ------------------------------------------------------- */
    document.getElementById('periodSelect').addEventListener('change', function () {
        document.getElementById('customDateRange').style.display =
            this.value === 'custom' ? 'flex' : 'none';
    });

    /* -------------------------------------------------------
       SALES LIST — SEARCH & FILTER
    ------------------------------------------------------- */
    let currentListFilter = 'all';
    let currentSearch     = '';

    function applyListFilters() {
        const rows = document.querySelectorAll('#salesListBody .sale-row');
        let visible = 0;

        rows.forEach(row => {
            const orderNum = (row.dataset.orderNumber ?? '').toLowerCase();
            const items    = (row.dataset.items ?? '').toLowerCase();
            const matchesSearch = !currentSearch || orderNum.includes(currentSearch) || items.includes(currentSearch);

            let matchesFilter = true;
            if (currentListFilter !== 'all') {
                const badges     = row.querySelectorAll('.badge');
                matchesFilter    = [...badges].some(badge =>
                    currentListFilter === 'menu'
                        ? badge.classList.contains('badge-menu')
                        : badge.classList.contains('badge-other')
                );
            }

            const show = matchesSearch && matchesFilter;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Toggle "no results" row
        const existing = document.getElementById('noResultsMessage');
        if (visible === 0 && rows.length > 0) {
            if (!existing) {
                const tr = document.createElement('tr');
                tr.id    = 'noResultsMessage';
                tr.innerHTML = `<td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        No matching sales found
                    </div>
                </td>`;
                document.getElementById('salesListBody').appendChild(tr);
            }
        } else {
            existing?.remove();
        }
    }

    // List filter buttons
    document.querySelectorAll('[data-list-filter]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-list-filter]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentListFilter = btn.dataset.listFilter;
            applyListFilters();
        });
    });

    // Search input
    document.getElementById('salesSearchInput')?.addEventListener('input', function () {
        currentSearch = this.value.toLowerCase().trim();
        applyListFilters();
    });

})();
</script>

@endsection
