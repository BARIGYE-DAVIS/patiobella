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
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
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
    .tabs { display: flex; gap: 0.5rem; border-bottom: 1px solid #e5e7eb; }
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
    .tab-btn.active          { color: #ea580c; border-bottom-color: #ea580c; }
    .tab-btn:hover:not(.active) { color: #374151; }
    .tab-content             { display: none; }
    .tab-content.active      { display: block; }

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
    canvas { max-height: 350px; width: 100% !important; }

    /* =========================================
       PILL BUTTONS
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
    .pill-btn.active               { background: #ea580c; color: white; }
    .pill-btn:not(.active):hover   { background: #e5e7eb; }

    /* =========================================
       EXPORT BUTTONS
    ========================================= */
    .export-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: background 0.2s, transform 0.1s;
    }
    .export-btn:hover            { transform: translateY(-1px); text-decoration: none; }
    .export-btn-excel            { background: #16a34a; color: white; }
    .export-btn-excel:hover      { background: #15803d; color: white; }
    .export-btn-pdf              { background: #dc2626; color: white; }
    .export-btn-pdf:hover        { background: #b91c1c; color: white; }

    /* =========================================
       LIST PANEL
    ========================================= */
    .list-panel { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
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
       SEARCH
    ========================================= */
    .search-wrapper { position: relative; display: inline-flex; align-items: center; }
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
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
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
    .data-table thead tr th.text-right  { text-align: right; }
    .data-table thead tr th.text-center { text-align: center; }
    .data-table tbody tr td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #374151;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td      { background: #fff7ed; }
    .data-table .col-amount  { text-align: right; font-weight: 600; white-space: nowrap; }
    .data-table .col-action  { text-align: center; }
    .invoice-num  { font-family: 'Courier New', monospace; font-size: 0.75rem; font-weight: 700; color: #1e293b; }
    .cell-datetime { font-size: 0.75rem; color: #64748b; white-space: nowrap; }

    /* =========================================
       BADGES
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
    .pay-method  { font-size: 0.78rem; white-space: nowrap; }

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
    .empty-state { padding: 3rem 1rem; text-align: center; color: #9ca3af; }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 0.5rem; }

    /* =========================================
       PERIOD BADGE
    ========================================= */
    .period-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
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
                <div class="mt-2">
                    @if($period === 'all_time')
                        <span class="period-badge"><i class="fas fa-infinity mr-1"></i> All Time</span>
                    @else
                        <span class="period-badge">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Export buttons --}}
            <div class="flex flex-col gap-2 items-end">
                <div class="flex gap-2">
                    <a href="{{ route('restaurant.sales.export.excel', array_merge(request()->query(), ['period' => $period, 'start_date' => $from, 'end_date' => $to])) }}"
                       class="export-btn export-btn-excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('restaurant.sales.export.pdf', array_merge(request()->query(), ['period' => $period, 'start_date' => $from, 'end_date' => $to])) }}"
                       class="export-btn export-btn-pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
                <p class="text-orange-200 text-xs">Exports reflect the current filter period</p>
            </div>
        </div>
    </div>

    {{-- ===== DATE FILTER BAR ===== --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('restaurant.sales.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

                {{-- Quick period --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quick Period</label>
                    <select name="period" id="periodSelect"
                            class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-500">
                        <option value="all_time"    {{ $period === 'all_time'    ? 'selected' : '' }}>All Time</option>
                        <option value="today"       {{ $period === 'today'       ? 'selected' : '' }}>Today</option>
                        <option value="yesterday"   {{ $period === 'yesterday'   ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week"   {{ $period === 'this_week'   ? 'selected' : '' }}>This Week</option>
                        <option value="this_month"  {{ $period === 'this_month'  ? 'selected' : '' }}>This Month</option>
                        <option value="last_month"  {{ $period === 'last_month'  ? 'selected' : '' }}>Last Month</option>
                        <option value="custom"      {{ $period === 'custom'      ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                {{-- Custom date inputs (hidden unless custom selected) --}}
                <div id="customDateRange"
                     class="md:col-span-4"
                     style="display: {{ $period === 'custom' ? 'flex' : 'none' }}; gap: 8px;">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date"
                               class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-500"
                               value="{{ request('start_date', $from) }}">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date"
                               class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-500"
                               value="{{ request('end_date', $to) }}">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-sync-alt mr-1"></i> Apply
                    </button>
                    <a href="{{ route('restaurant.sales.index') }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                </div>
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

            <div class="list-panel-header">
                {{-- Item type filter --}}
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

                {{-- Search + row count --}}
                <div class="flex items-center gap-3">
                    <span id="rowCountBadge"
                          class="text-xs bg-orange-100 text-orange-700 font-semibold px-3 py-1 rounded-full">
                        {{ $salesList->count() }} orders
                    </span>
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text"
                               id="salesSearchInput"
                               class="search-input"
                               placeholder="Search invoice # or item…">
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div style="max-height: 560px; overflow-y: auto;">

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
                            data-order-number="{{ strtolower($sale->order_number) }}"
                            data-items="{{ strtolower(implode(',', $sale->items->pluck('item_name')->toArray())) }}"
                            data-has-menu="{{ $sale->items->filter(fn($i) => in_array($i->item_name, $menuItemNames))->count() > 0 ? '1' : '0' }}"
                            data-has-other="{{ $sale->items->filter(fn($i) => !in_array($i->item_name, $menuItemNames))->count() > 0 ? '1' : '0' }}">

                            <td><span class="invoice-num">{{ $sale->order_number }}</span></td>

                            <td class="cell-datetime">
                                {{ $sale->created_at->format('d/m/Y') }}<br>
                                <span class="text-gray-400">{{ $sale->created_at->format('h:i A') }}</span>
                            </td>

                            <td style="font-size:0.8rem;">{{ $sale->cashier->first_name ?? '—' }}</td>

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
                        <tr id="emptyInitial">
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

            {{-- List footer: totals --}}
            @if($salesList->count() > 0)
            <div style="padding: 0.75rem 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <span id="visibleSummary" class="text-xs text-gray-500">
                    Showing all {{ $salesList->count() }} orders
                </span>
                <span class="text-sm font-bold text-gray-800">
                    Visible Total: <span id="visibleTotal" class="text-orange-600">
                        UGX {{ number_format($salesData['total_sales'], 0) }}
                    </span>
                </span>
            </div>
            @endif
        </div>
    </div>

</div>{{-- end .space-y-6 --}}

{{-- ===== SCRIPTS ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    'use strict';

    /* ─────────────────────────────────────────────────────
       SERVER DATA
    ───────────────────────────────────────────────────── */
    const menuItemNames  = @json($menuItemNames ?? []);
    const allProducts    = @json($topProducts);
    const dailyTrendData = @json($dailyTrend);
    const hourlyData     = @json($hourlySales);
    const paymentData    = @json($paymentMethods);

    /* ─────────────────────────────────────────────────────
       CATEGORISE PRODUCTS
    ───────────────────────────────────────────────────── */
    const menuProducts      = allProducts.filter(p =>  menuItemNames.includes(p.item_name));
    const inventoryProducts = allProducts.filter(p => !menuItemNames.includes(p.item_name));

    /* ─────────────────────────────────────────────────────
       SHARED OPTIONS
    ───────────────────────────────────────────────────── */
    const ugxLabel = ctx => 'UGX ' + ctx.raw.toLocaleString();

    const baseOpts = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'top' } },
    };

    /* ─────────────────────────────────────────────────────
       DAILY TREND
    ───────────────────────────────────────────────────── */
    const dailyTrendChart = new Chart(document.getElementById('dailyTrendChart'), {
        type: 'line',
        data: {
            labels:   dailyTrendData.map(d => d.date),
            datasets: [{
                label:            'Sales (UGX)',
                data:             dailyTrendData.map(d => d.total),
                borderColor:      '#ea580c',
                backgroundColor:  'rgba(234,88,12,0.1)',
                tension:          0.4,
                fill:             true,
                pointRadius:      4,
                pointHoverRadius: 6,
            }],
        },
        options: { ...baseOpts, plugins: { ...baseOpts.plugins, tooltip: { callbacks: { label: ugxLabel } } } },
    });

    /* ─────────────────────────────────────────────────────
       HOURLY
    ───────────────────────────────────────────────────── */
    const hourLabels = Array.from({ length: 24 }, (_, i) => {
        const h = i % 12 || 12;
        return `${h}:00 ${i >= 12 ? 'PM' : 'AM'}`;
    });

    const hourlySalesChart = new Chart(document.getElementById('hourlySalesChart'), {
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
        options: { ...baseOpts, plugins: { ...baseOpts.plugins, tooltip: { callbacks: { label: ugxLabel } } } },
    });

    /* ─────────────────────────────────────────────────────
       PAYMENT PIE
    ───────────────────────────────────────────────────── */
    const payLabelMap = { cash: '💵 Cash', card: '💳 Card', mobile_money: '📱 Mobile Money' };

    const paymentMethodsChart = new Chart(document.getElementById('paymentMethodsChart'), {
        type: 'pie',
        data: {
            labels:   paymentData.map(p => payLabelMap[p.payment_method] ?? p.payment_method),
            datasets: [{
                data:            paymentData.map(p => p.total),
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
                        label(ctx) {
                            const sum  = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct  = ((ctx.raw / sum) * 100).toFixed(1);
                            return `${ctx.label}: UGX ${ctx.raw.toLocaleString()} (${pct}%)`;
                        },
                    },
                },
            },
        },
    });

    /* ─────────────────────────────────────────────────────
       TOP PRODUCTS BAR (dynamic)
    ───────────────────────────────────────────────────── */
    let topProductsChart = null;

    function buildTopProducts(filter) {
        const cfg = {
            all:       { data: allProducts,       color: '#f59e0b', label: '(All Items)' },
            menu:      { data: menuProducts,      color: '#10b981', label: '(Menu Items)' },
            inventory: { data: inventoryProducts, color: '#3b82f6', label: '(Other Items)' },
        };
        const { data, color, label } = cfg[filter] || cfg.all;

        document.getElementById('productFilterLabel').textContent = label;

        if (topProductsChart) topProductsChart.destroy();

        topProductsChart = new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels:   data.map(p => p.item_name.length > 22 ? p.item_name.slice(0, 22) + '…' : p.item_name),
                datasets: [{
                    label:           'Revenue (UGX)',
                    data:            data.map(p => p.total_revenue),
                    backgroundColor: color,
                    borderRadius:    8,
                }],
            },
            options: { ...baseOpts, plugins: { ...baseOpts.plugins, tooltip: { callbacks: { label: ugxLabel } } } },
        });
    }

    buildTopProducts('all');

    /* ─────────────────────────────────────────────────────
       CHART FILTER BUTTONS
    ───────────────────────────────────────────────────── */
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            buildTopProducts(btn.dataset.filter);
        });
    });

    /* ─────────────────────────────────────────────────────
       TABS
    ───────────────────────────────────────────────────── */
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(`tab-${tabId}`).classList.add('active');

            if (tabId === 'analytics') {
                setTimeout(() => {
                    [dailyTrendChart, hourlySalesChart, paymentMethodsChart].forEach(c => c?.update());
                    buildTopProducts(document.querySelector('[data-filter].active')?.dataset.filter ?? 'all');
                }, 80);
            }
        });
    });

    /* ─────────────────────────────────────────────────────
       CUSTOM DATE TOGGLE
    ───────────────────────────────────────────────────── */
    document.getElementById('periodSelect').addEventListener('change', function () {
        document.getElementById('customDateRange').style.display =
            this.value === 'custom' ? 'flex' : 'none';
    });

    /* ─────────────────────────────────────────────────────
       SALES LIST — SEARCH + FILTER
    ───────────────────────────────────────────────────── */
    let listFilter = 'all';
    let searchTerm = '';

    function applyListFilters() {
        const rows    = document.querySelectorAll('#salesListBody .sale-row');
        let   visible = 0;
        let   visibleTotal = 0;

        rows.forEach(row => {
            const orderNum = row.dataset.orderNumber ?? '';
            const items    = row.dataset.items ?? '';
            const hasMenu  = row.dataset.hasMenu === '1';
            const hasOther = row.dataset.hasOther === '1';

            // Search match
            const matchSearch = !searchTerm
                || orderNum.includes(searchTerm)
                || items.includes(searchTerm);

            // Filter match
            let matchFilter = true;
            if (listFilter === 'menu')      matchFilter = hasMenu;
            if (listFilter === 'inventory') matchFilter = hasOther;

            const show = matchSearch && matchFilter;
            row.style.display = show ? '' : 'none';

            if (show) {
                visible++;
                // Sum from the amount cell (strip non-numeric)
                const amountCell = row.querySelector('.col-amount');
                if (amountCell) {
                    const num = parseFloat(amountCell.textContent.replace(/[^0-9.]/g, ''));
                    if (!isNaN(num)) visibleTotal += num;
                }
            }
        });

        // No-results message
        const existing = document.getElementById('noResultsMessage');
        if (visible === 0 && rows.length > 0) {
            if (!existing) {
                const tr    = document.createElement('tr');
                tr.id       = 'noResultsMessage';
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

        // Update count badge
        const badge = document.getElementById('rowCountBadge');
        if (badge) badge.textContent = `${visible} order${visible !== 1 ? 's' : ''}`;

        // Update visible total
        const totalEl = document.getElementById('visibleTotal');
        if (totalEl) totalEl.textContent = 'UGX ' + visibleTotal.toLocaleString();

        // Update summary text
        const summaryEl = document.getElementById('visibleSummary');
        if (summaryEl) {
            summaryEl.textContent = visible === rows.length
                ? `Showing all ${visible} orders`
                : `Showing ${visible} of ${rows.length} orders`;
        }
    }

    // List filter pills
    document.querySelectorAll('[data-list-filter]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-list-filter]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            listFilter = btn.dataset.listFilter;
            applyListFilters();
        });
    });

    // Search input
    document.getElementById('salesSearchInput')?.addEventListener('input', function () {
        searchTerm = this.value.toLowerCase().trim();
        applyListFilters();
    });

})();
</script>

@endsection
