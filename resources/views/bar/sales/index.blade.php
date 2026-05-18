{{-- resources/views/bar/sales/index.blade.php --}}

@extends('layouts.bar')

@section('title', 'Sales Reports')

@section('page-title', 'Sales Analytics Dashboard')

@section('content')
<style>
    /* ============================================
       STAT CARDS
    ============================================ */
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

    /* ============================================
       FILTER BAR
    ============================================ */
    .filter-bar {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }

    /* ============================================
       CHART CONTAINERS
    ============================================ */
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #374151;
        border-left: 4px solid #ea580c;
        padding-left: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .chart-toggle {
        display: flex;
        gap: 0.5rem;
    }
    .chart-toggle-btn {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        cursor: pointer;
        border: 1px solid #d1d5db;
        background: white;
        color: #374151;
        transition: all 0.2s;
    }
    .chart-toggle-btn.active {
        background: #ea580c;
        color: white;
        border-color: #ea580c;
    }
    canvas {
        max-height: 300px;
        width: 100%;
    }

    /* ============================================
       TABLES
    ============================================ */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
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

    /* ============================================
       TEXT ALIGNMENT
    ============================================ */
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    /* ============================================
       BUTTONS
    ============================================ */
    .btn-export {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-block;
        border: none;
        cursor: pointer;
    }
    .btn-export:hover {
        background: #059669;
    }
</style>

<div class="space-y-6">

    {{-- ============================================
         HEADER SECTION
    ============================================ --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-chart-line mr-2"></i>
                    Bar Sales Analytics
                </h2>
                <p class="text-blue-100 mt-1">Comprehensive sales reports and trends</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-calendar mr-1"></i> Period</p>
                <p class="text-lg font-bold">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================
         FILTERS SECTION
    ============================================ --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('bar.sales.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Period</label>
                <select name="period" id="periodSelect" class="w-full p-2 border rounded-lg text-sm">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $period == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="this_week" {{ $period == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="last_week" {{ $period == 'last_week' ? 'selected' : '' }}>Last Week</option>
                    <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>This Year</option>
                    <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom</option>
                </select>
            </div>

            <div id="customDateRange" style="display: {{ request('start_date') ? 'flex' : 'none' }}; gap: 8px; grid-column: span 2;">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" class="w-full p-2 border rounded-lg text-sm" value="{{ request('start_date', $from) }}">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" class="w-full p-2 border rounded-lg text-sm" value="{{ request('end_date', $to) }}">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Cashier</label>
                <select name="cashier_id" class="w-full p-2 border rounded-lg text-sm">
                    <option value="">All Cashiers</option>
                    @foreach($cashiers as $cashier)
                        <option value="{{ $cashier->id }}" {{ $cashierId == $cashier->id ? 'selected' : '' }}>
                            {{ $cashier->first_name }} {{ $cashier->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Product</label>
                <select name="item_id" class="w-full p-2 border rounded-lg text-sm">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $itemId == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-sync-alt mr-1"></i> Apply
                </button>
                <a href="{{ route('bar.sales.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
                <button type="button" onclick="openExportModal()" class="btn-export">
                    <i class="fas fa-file-excel mr-1"></i> Export
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================
         STATISTICS CARDS
    ============================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value text-green-600">UGX {{ number_format($stats['total_sales'], 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value text-blue-600">{{ number_format($stats['total_orders']) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-label">Average Order</div>
            <div class="stat-value text-orange-600">UGX {{ number_format($stats['avg_order_value'], 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-label">Items Sold</div>
            <div class="stat-value text-red-600">{{ number_format($stats['total_items']) }}</div>
        </div>
    </div>

    {{-- ============================================
         CHARTS ROW 1
    ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Daily Sales Trend --}}
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-chart-line text-orange-600 mr-2"></i> Daily Sales Trend
            </div>
            <canvas id="dailyTrendChart" height="250"></canvas>
        </div>

        {{-- Top Products --}}
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-trophy text-orange-600 mr-2"></i> Top Selling Products
            </div>
            <canvas id="topProductsChart" height="250"></canvas>
        </div>
    </div>

    {{-- ============================================
         CHARTS ROW 2
    ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Payment Methods --}}
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-credit-card text-orange-600 mr-2"></i> Payment Methods
            </div>
            <canvas id="paymentMethodsChart" height="250"></canvas>
        </div>

        {{-- Hourly Sales Distribution --}}
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-clock text-orange-600 mr-2"></i> Sales by Hour
            </div>
            <canvas id="hourlySalesChart" height="250"></canvas>
        </div>
    </div>

    {{-- ============================================
         BEST PERFORMING DAYS - INTERACTIVE CHART
    ============================================ --}}
    <div class="chart-container">
        <div class="chart-title">
            <span><i class="fas fa-calendar-week text-orange-600 mr-2"></i> Best Performing Days</span>
            <div class="chart-toggle">
                <button class="chart-toggle-btn active" data-chart="revenue" data-type="bar">📊 Revenue (Bar)</button>
                <button class="chart-toggle-btn" data-chart="revenue" data-type="line">📈 Revenue (Line)</button>
                <button class="chart-toggle-btn" data-chart="orders" data-type="bar">📋 Orders (Bar)</button>
                <button class="chart-toggle-btn" data-chart="orders" data-type="line">📈 Orders (Line)</button>
            </div>
        </div>
        <div>
            <canvas id="dayPerformanceChart" height="250"></canvas>
        </div>
    </div>

    {{-- ============================================
         PERFORMANCE TABLES
    ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Cashier Performance --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-users mr-2 text-blue-600"></i> Cashier Performance
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-left">Cashier</th>
                            <th class="text-right">Orders</th>
                            <th class="text-right">Revenue</th>
                            <th class="text-right">Avg Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cashierPerformance as $cashier)
                        <tr>
                            <td class="text-left font-medium">{{ $cashier['name'] }}</td>
                            <td class="text-right">{{ number_format($cashier['orders']) }}</td>
                            <td class="text-right">UGX {{ number_format($cashier['revenue'], 0) }}</td>
                            <td class="text-right">UGX {{ number_format($cashier['orders'] > 0 ? $cashier['revenue'] / $cashier['orders'] : 0, 0) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-400">No cashier data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Monthly Performance --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-calendar-alt mr-2 text-blue-600"></i> Monthly Performance
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-left">Month</th>
                            <th class="text-right">Orders</th>
                            <th class="text-right">Revenue</th>
                            <th class="text-right">Avg Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthPerformance as $month => $data)
                        <tr>
                            <td class="text-left font-medium">{{ $month }}</td>
                            <td class="text-right">{{ number_format($data['orders']) }}</td>
                            <td class="text-right">UGX {{ number_format($data['revenue'], 0) }}</td>
                            <td class="text-right">UGX {{ number_format($data['orders'] > 0 ? $data['revenue'] / $data['orders'] : 0, 0) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-400">No monthly data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     EXPORT MODAL
============================================ --}}
<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl w-96 max-w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">
                <i class="fas fa-download text-green-600 mr-2"></i> Export Sales Report
            </h3>
            <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Export Type</label>
                <select id="exportType" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="all">All Sales (No Date Filter)</option>
                    <option value="current">Current Filtered Period</option>
                    <option value="custom">Custom Date Range</option>
                </select>
            </div>

            <div id="customDateDiv" style="display: none;">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Start Date</label>
                        <input type="date" id="exportStartDate" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">End Date</label>
                        <input type="date" id="exportEndDate" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="exportExcel()" class="bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i> Excel
                </button>
                <button onclick="exportPdf()" class="bg-red-600 text-white py-2 rounded-lg font-semibold hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     SCRIPTS
============================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ============================================
    // EXPORT MODAL FUNCTIONS
    // ============================================
    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    document.getElementById('exportType').addEventListener('change', function() {
        const customDiv = document.getElementById('customDateDiv');
        customDiv.style.display = this.value === 'custom' ? 'block' : 'none';
    });

    function exportExcel() {
        const exportType = document.getElementById('exportType').value;
        let url = '{{ route("bar.sales.export.excel") }}?export_type=' + exportType;

        if (exportType === 'custom') {
            url += '&start_date=' + document.getElementById('exportStartDate').value;
            url += '&end_date=' + document.getElementById('exportEndDate').value;
        } else if (exportType === 'current') {
            url += '&start_date={{ $from }}&end_date={{ $to }}';
            @if($cashierId)
                url += '&cashier_id={{ $cashierId }}';
            @endif
            @if($itemId)
                url += '&item_id={{ $itemId }}';
            @endif
        }

        window.location.href = url;
        closeExportModal();
    }

    function exportPdf() {
        const exportType = document.getElementById('exportType').value;
        let url = '{{ route("bar.sales.export.pdf") }}?export_type=' + exportType;

        if (exportType === 'custom') {
            url += '&start_date=' + document.getElementById('exportStartDate').value;
            url += '&end_date=' + document.getElementById('exportEndDate').value;
        } else if (exportType === 'current') {
            url += '&start_date={{ $from }}&end_date={{ $to }}';
            @if($cashierId)
                url += '&cashier_id={{ $cashierId }}';
            @endif
            @if($itemId)
                url += '&item_id={{ $itemId }}';
            @endif
        }

        window.location.href = url;
        closeExportModal();
    }

    // ============================================
    // DAILY TREND CHART
    // ============================================
    const dailyLabels = @json(array_keys($dailyTrend->toArray()));
    const dailyData = @json(array_values($dailyTrend->toArray()));

    const dailyCtx = document.getElementById('dailyTrendChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Sales (UGX)',
                    data: dailyData,
                    borderColor: '#ea580c',
                    backgroundColor: 'rgba(234, 88, 12, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    }

    // ============================================
    // TOP PRODUCTS CHART
    // ============================================
    const productNames = @json($topProducts->pluck('item_name'));
    const productRevenues = @json($topProducts->pluck('total_revenue'));

    const topProductsCtx = document.getElementById('topProductsChart');
    if (topProductsCtx) {
        new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: productNames,
                datasets: [{
                    label: 'Revenue (UGX)',
                    data: productRevenues,
                    backgroundColor: '#f59e0b',
                    borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    }

    // ============================================
    // PAYMENT METHODS CHART
    // ============================================
    const paymentLabels = @json(array_keys($paymentMethods->toArray()));
    const paymentTotals = @json(array_values(array_column($paymentMethods->toArray(), 'total')));

    const paymentDisplayLabels = paymentLabels.map(label => {
        switch(label) {
            case 'cash': return '💵 Cash';
            case 'card': return '💳 Card';
            case 'mobile_money': return '📱 Mobile Money';
            default: return label;
        }
    });

    const paymentCtx = document.getElementById('paymentMethodsChart');
    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: paymentDisplayLabels,
                datasets: [{
                    data: paymentTotals,
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // ============================================
    // HOURLY SALES CHART
    // ============================================
    const hourlyData = @json(array_values($hourlySales));
    const hours = Array.from({length: 24}, (_, i) => i + ':00');

    const hourlyCtx = document.getElementById('hourlySalesChart');
    if (hourlyCtx) {
        new Chart(hourlyCtx, {
            type: 'line',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Sales (UGX)',
                    data: hourlyData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    }

    // ============================================
    // DAY PERFORMANCE CHART WITH TOGGLE
    // ============================================
    const daysOfWeek = @json($daysOfWeek);
    const revenueData = @json($dayRevenueData);
    const orderData = @json($dayOrderData);

    let dayChart = null;
    let currentChartType = 'bar';
    let currentChartData = 'revenue';

    function initDayChart() {
        const ctx = document.getElementById('dayPerformanceChart');
        if (!ctx) return;

        const data = currentChartData === 'revenue' ? revenueData : orderData;
        const label = currentChartData === 'revenue' ? 'Revenue (UGX)' : 'Number of Orders';
        const backgroundColor = currentChartData === 'revenue' ? '#10b981' : '#3b82f6';
        const borderColor = currentChartData === 'revenue' ? '#059669' : '#2563eb';

        if (dayChart) {
            dayChart.destroy();
        }

        dayChart = new Chart(ctx, {
            type: currentChartType,
            data: {
                labels: daysOfWeek,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: currentChartType === 'line'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (currentChartData === 'revenue') {
                                    return 'UGX ' + value.toLocaleString();
                                }
                                return value;
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                if (currentChartData === 'revenue') {
                                    return 'Revenue: UGX ' + value.toLocaleString();
                                }
                                return 'Orders: ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // Chart toggle buttons
    const toggleButtons = document.querySelectorAll('.chart-toggle-btn');
    if (toggleButtons.length) {
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const chartData = this.dataset.chart;
                const chartType = this.dataset.type;

                currentChartData = chartData;
                currentChartType = chartType;

                // Update active state
                toggleButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                initDayChart();
            });
        });
    }

    // Initialize day chart
    initDayChart();

    // ============================================
    // DATE RANGE TOGGLE
    // ============================================
    const periodSelect = document.getElementById('periodSelect');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            const customDiv = document.getElementById('customDateRange');
            if (customDiv) {
                customDiv.style.display = this.value === 'custom' ? 'flex' : 'none';
            }
        });
    }
</script>
@endsection
