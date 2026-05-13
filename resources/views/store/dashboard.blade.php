@extends('layouts.store')

@section('title', 'Store Dashboard')

@section('page-title', 'Store Dashboard')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .stat-card h3 { font-size: 0.7rem; text-transform: uppercase; color: #6b7280; margin-bottom: 0.5rem; letter-spacing: 0.5px; }
    .stat-card .value { font-size: 1.5rem; font-weight: bold; }
    .stat-card .trend { font-size: 0.7rem; margin-top: 0.25rem; }

    .badge-urgent { background: #dc2626; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-pending { background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-approved { background: #10b981; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }

    .quick-action-btn {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        display: block;
        text-decoration: none;
        color: #374151;
    }
    .quick-action-btn:hover { background: #e5e7eb; transform: translateY(-2px); }

    .data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .data-table th { background: #f8fafc; padding: 0.75rem; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; }
    .data-table tr:hover { background: #f8fafc; }
    .text-right { text-align: right; }

    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }

    .stock-health { width: 100%; background: #e5e7eb; border-radius: 10px; height: 8px; overflow: hidden; }
    .stock-health-fill { height: 100%; border-radius: 10px; }
    .health-good { background: #10b981; }
    .health-warning { background: #f59e0b; }
    .health-critical { background: #ef4444; }

    .two-col { display: flex; gap: 1.5rem; margin-bottom: 1.5rem; }
    .col { flex: 1; }
    .three-col { display: flex; gap: 1rem; margin-bottom: 1.5rem; }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    .chart-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        text-align: center;
    }

    .main-tabs {
        display: flex;
        gap: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .main-tab-btn {
        padding: 0.6rem 1.2rem;
        font-size: 0.8rem;
        font-weight: 500;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        color: #6b7280;
        margin-bottom: -2px;
    }
    .main-tab-btn:hover { color: #374151; }
    .main-tab-btn.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }
    .main-tab-content { display: none; }
    .main-tab-content.active { display: block; }

    .sub-tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; flex-wrap: wrap; }
    .sub-tab-btn { padding: 0.3rem 1rem; font-size: 0.7rem; background: #f3f4f6; border: none; border-radius: 20px; cursor: pointer; transition: all 0.2s; color: #6b7280; }
    .sub-tab-btn:hover { background: #e5e7eb; }
    .sub-tab-btn.active { background: #3b82f6; color: white; }
    .sub-tab-content { display: none; }
    .sub-tab-content.active { display: block; }

    .pie-row { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .pie-card {
        flex: 1;
        min-width: 250px;
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
    }
    .pie-card h4 { font-size: 0.75rem; font-weight: 600; text-align: center; margin-bottom: 1rem; }

    .font-mono { font-family: monospace; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="space-y-6">

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold"><i class="fas fa-chart-line mr-2"></i> Welcome, {{ Auth::user()->first_name ?? 'Store Manager' }}!</h2>
                <p class="text-blue-100 mt-1">{{ now()->format('l, F d, Y') }} | {{ now()->format('h:i A') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-exchange-alt mr-1"></i> Today's Activity</p>
                <p class="text-2xl font-bold">{{ $todayIssuesCount ?? 0 }} Issues | {{ $todayReturnsCount ?? 0 }} Returns</p>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <h3><i class="fas fa-dollar-sign mr-1"></i> Stock Value</h3>
            <div class="value">UGX {{ number_format($totalStockValue ?? 0, 2) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <h3><i class="fas fa-arrow-down mr-1"></i> Stock IN Today</h3>
            <div class="value">{{ number_format($stockInToday ?? 0) }} units</div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <h3><i class="fas fa-arrow-up mr-1"></i> Stock OUT Today</h3>
            <div class="value">{{ number_format($stockOutToday ?? 0) }} units</div>
        </div>
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <h3><i class="fas fa-chart-line mr-1"></i> Net Change</h3>
            <div class="value {{ ($netChangeToday ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                {{ ($netChangeToday ?? 0) >= 0 ? '+' : '' }}{{ number_format($netChangeToday ?? 0) }} units
            </div>
        </div>
    </div>

    {{-- PIE CHARTS SECTION --}}
    <div class="pie-row">
        <div class="pie-card">
            <h4><i class="fas fa-chart-pie mr-1"></i> Stock Health</h4>
            <canvas id="stockHealthPieChart" height="200"></canvas>
            <div class="text-center mt-2 text-xs">
                <span class="text-green-600">Healthy: {{ $healthyStockPercent ?? 0 }}%</span> |
                <span class="text-orange-600">Low: {{ $lowStockPercent ?? 0 }}%</span> |
                <span class="text-red-600">Out: {{ $outOfStockPercent ?? 0 }}%</span>
            </div>
        </div>
        <div class="pie-card">
            <h4><i class="fas fa-calendar-day mr-1"></i> Today's Activity</h4>
            <canvas id="todayActivityPieChart" height="200"></canvas>
        </div>
        <div class="pie-card">
            <h4><i class="fas fa-building mr-1"></i> Dept Consumption</h4>
            <canvas id="deptConsumptionPieChart" height="200"></canvas>
        </div>
    </div>

    <div class="pie-row">
        <div class="pie-card">
            <h4><i class="fas fa-truck mr-1"></i> Stock IN Source</h4>
            <canvas id="inventorySourcePieChart" height="200"></canvas>
        </div>
        <div class="pie-card">
            <h4><i class="fas fa-clock mr-1"></i> Pending Requisitions by Dept</h4>
            <canvas id="pendingRequisitionPieChart" height="200"></canvas>
        </div>
        <div class="pie-card">
            <h4><i class="fas fa-chart-simple mr-1"></i> Issues vs Returns</h4>
            <canvas id="issuesReturnsPieChart" height="200"></canvas>
        </div>
    </div>

    {{-- MAIN TABS --}}
    <div class="main-tabs">
        <button class="main-tab-btn active" data-tab="trends-tab"><i class="fas fa-chart-line mr-1"></i> Stock Trends</button>
        <button class="main-tab-btn" data-tab="alerts-tab"><i class="fas fa-bell mr-1"></i> Alerts & Requests</button>
        <button class="main-tab-btn" data-tab="activity-tab"><i class="fas fa-history mr-1"></i> Today's Activity</button>
    </div>

    {{-- TAB 1: STOCK TRENDS --}}
    <div id="trends-tab" class="main-tab-content active">
        <div class="sub-tabs">
            <button class="sub-tab-btn active" data-subtab="daily-trend">📅 Daily (30 Days)</button>
            <button class="sub-tab-btn" data-subtab="weekly-trend">📆 Weekly (12 Weeks)</button>
            <button class="sub-tab-btn" data-subtab="monthly-trend">📊 Monthly (6 Months)</button>
        </div>

        {{-- DAILY TRENDS --}}
        <div id="daily-trend" class="sub-tab-content active">
            <div class="two-col">
                <div class="chart-container">
                    <div class="chart-title"><i class="fas fa-boxes mr-1"></i> Inventory Updates - Daily Value</div>
                    <canvas id="dailyStockChart" height="250"></canvas>
                    <div class="text-center mt-2 text-xs">
                        <span class="trend-up"><i class="fas fa-arrow-down mr-1"></i> Avg IN: {{ number_format($avgDailyStockIn ?? 0, 0) }} units</span>
                        <span class="trend-down ml-3"><i class="fas fa-arrow-up mr-1"></i> Avg OUT: {{ number_format($avgDailyStockOut ?? 0, 0) }} units</span>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="chart-title"><i class="fas fa-exchange-alt mr-1"></i> Department Movements - Daily Value</div>
                    <canvas id="dailyDeptChart" height="250"></canvas>
                    <div class="text-center mt-2 text-xs">
                        <span class="trend-up"><i class="fas fa-arrow-down mr-1"></i> Avg Issues: {{ number_format($avgDailyIssues ?? 0, 0) }} units</span>
                        <span class="trend-down ml-3"><i class="fas fa-arrow-up mr-1"></i> Avg Returns: {{ number_format($avgDailyReturns ?? 0, 0) }} units</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- WEEKLY TRENDS --}}
        <div id="weekly-trend" class="sub-tab-content">
            <div class="two-col">
                <div class="chart-container">
                    <div class="chart-title"><i class="fas fa-boxes mr-1"></i> Inventory Updates - Weekly Value</div>
                    <canvas id="weeklyStockChart" height="250"></canvas>
                    <div class="text-center mt-2 text-xs">
                        <span class="trend-up"><i class="fas fa-arrow-down mr-1"></i> Avg IN: {{ number_format($avgWeeklyStockIn ?? 0, 0) }} units</span>
                        <span class="trend-down ml-3"><i class="fas fa-arrow-up mr-1"></i> Avg OUT: {{ number_format($avgWeeklyStockOut ?? 0, 0) }} units</span>
                        <div class="mt-1">
                            <span class="trend-up">WoW IN: {{ ($wowStockInChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($wowStockInChange ?? 0, 1) }}%</span>
                            <span class="trend-down ml-3">WoW OUT: {{ ($wowStockOutChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($wowStockOutChange ?? 0, 1) }}%</span>
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="chart-title"><i class="fas fa-exchange-alt mr-1"></i> Department Movements - Weekly Value</div>
                    <canvas id="weeklyDeptChart" height="250"></canvas>
                    <div class="text-center mt-2 text-xs">
                        <span class="trend-up">WoW Issues: {{ ($wowIssuesChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($wowIssuesChange ?? 0, 1) }}%</span>
                        <span class="trend-down ml-3">WoW Returns: {{ ($wowReturnsChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($wowReturnsChange ?? 0, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- MONTHLY TRENDS --}}
        <div id="monthly-trend" class="sub-tab-content">
            <div class="two-col">
                <div class="chart-container">
                    <div class="chart-title"><i class="fas fa-boxes mr-1"></i> Inventory Updates - Monthly Value</div>
                    <canvas id="monthlyStockChart" height="250"></canvas>
                    <div class="text-center mt-2 text-xs">
                        <span class="trend-up"><i class="fas fa-arrow-down mr-1"></i> Avg IN: {{ number_format($avgMonthlyStockIn ?? 0, 0) }} units</span>
                        <span class="trend-down ml-3"><i class="fas fa-arrow-up mr-1"></i> Avg OUT: {{ number_format($avgMonthlyStockOut ?? 0, 0) }} units</span>
                        <div class="mt-1">
                            <span class="trend-up">MoM IN: {{ ($momStockInChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($momStockInChange ?? 0, 1) }}%</span>
                            <span class="trend-down ml-3">MoM OUT: {{ ($momStockOutChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($momStockOutChange ?? 0, 1) }}%</span>
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="chart-title"><i class="fas fa-exchange-alt mr-1"></i> Department Movements - Monthly Value</div>
                    <canvas id="monthlyDeptChart" height="250"></canvas>
                    <div class="text-center mt-2 text-xs">
                        <span class="trend-up">MoM Issues: {{ ($momIssuesChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($momIssuesChange ?? 0, 1) }}%</span>
                        <span class="trend-down ml-3">MoM Returns: {{ ($momReturnsChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($momReturnsChange ?? 0, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: ALERTS & REQUESTS --}}
    <div id="alerts-tab" class="main-tab-content">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- LOW STOCK ALERTS --}}
            @if(($lowStockItems ?? collect())->count() > 0 || ($outOfStockItems ?? collect())->count() > 0)
            <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                    <h3 class="font-semibold text-red-700"><i class="fas fa-exclamation-triangle mr-2"></i> Stock Alerts</h3>
                </div>
                <div class="p-4">
                    @if($lowStockItems->count() > 0)
                    <h4 class="text-sm font-semibold text-orange-600 mb-2"><i class="fas fa-hourglass-half mr-1"></i> Low Stock ({{ $lowStockItems->count() }})</h4>
                    @foreach($lowStockItems as $item)
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span>{{ $item->name }}</span>
                        <span class="text-orange-600 font-bold">{{ number_format($item->current_stock) }} / {{ number_format($item->minimum_stock) }}</span>
                    </div>
                    <div class="stock-health mb-2"><div class="stock-health-fill health-warning" style="width: {{ ($item->current_stock / $item->minimum_stock) * 100 }}%"></div></div>
                    @endforeach
                    @endif
                    @if($outOfStockItems->count() > 0)
                    <h4 class="text-sm font-semibold text-red-600 mb-2 mt-3"><i class="fas fa-ban mr-1"></i> Out of Stock ({{ $outOfStockItems->count() }})</h4>
                    @foreach($outOfStockItems as $item)
                    <div class="flex justify-between items-center text-sm mb-1"><span>{{ $item->name }}</span><span class="text-red-600 font-bold">0 units</span></div>
                    @endforeach
                    @endif
                </div>
            </div>
            @endif

            {{-- PENDING REQUISITIONS --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-700"><i class="fas fa-clock mr-2"></i> Pending Requisitions</h3>
                    <a href="{{ route('store.department-requisitions.index') }}" class="text-xs text-blue-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Date</th><th>Requisition #</th><th>Dept</th><th>Date Needed</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($pendingRequisitions ?? [] as $req)
                            <tr>
                                <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                <td class="font-mono">{{ $req->requisition_number }}</td>
                                <td><span class="badge-pending">{{ $req->department->name ?? 'N/A' }}</span></td>
                                <td class="{{ $req->date_needed && $req->date_needed <= now() ? 'text-red-600 font-bold' : '' }}">{{ $req->date_needed ? $req->date_needed->format('Y-m-d') : 'Not set' }}</td>
                                <td><span class="badge-pending">Pending</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-gray-500 py-4">No pending requisitions</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: TODAY'S ACTIVITY --}}
    <div id="activity-tab" class="main-tab-content">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h3 class="font-semibold text-gray-700"><i class="fas fa-history mr-2"></i> Today's Activity Log</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Time</th><th>Type</th><th>Item</th><th>Qty</th><th>Department</th><th>By</th></tr></thead>
                    <tbody>
                        @forelse($todayActivities ?? [] as $activity)
                        <tr>
                            <td>{{ $activity->created_at->format('h:i A') }}</td>
                            <td><span class="badge-approved">{{ $activity->movementType->name ?? 'N/A' }}</span></td>
                            <td>{{ $activity->inventoryItem->name ?? 'N/A' }}</td>
                            <td>{{ number_format($activity->quantity_in_base_unit ?? 0, 2) }}</td>
                            <td>{{ $activity->department->name ?? 'N/A' }}</td>
                            <td>{{ $activity->taken_by ?? $activity->returned_by ?? $activity->createdBy->name ?? 'System' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-gray-500 py-4">No activities today</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== MAIN TAB SWITCHING ==========
    const mainTabBtns = document.querySelectorAll('.main-tab-btn');
    const mainTabContents = document.querySelectorAll('.main-tab-content');

    mainTabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');
            mainTabBtns.forEach(b => b.classList.remove('active'));
            mainTabContents.forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // ========== SUB TAB SWITCHING ==========
    const subTabBtns = document.querySelectorAll('.sub-tab-btn');
    const subTabContents = document.querySelectorAll('.sub-tab-content');

    subTabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-subtab');
            subTabBtns.forEach(b => b.classList.remove('active'));
            subTabContents.forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');

            setTimeout(() => {
                if (window.dailyStockChart) window.dailyStockChart.resize();
                if (window.dailyDeptChart) window.dailyDeptChart.resize();
                if (window.weeklyStockChart) window.weeklyStockChart.resize();
                if (window.weeklyDeptChart) window.weeklyDeptChart.resize();
                if (window.monthlyStockChart) window.monthlyStockChart.resize();
                if (window.monthlyDeptChart) window.monthlyDeptChart.resize();
            }, 100);
        });
    });

    // ========== PIE CHARTS ==========
    new Chart(document.getElementById('stockHealthPieChart'), {
        type: 'pie',
        data: { labels: {!! json_encode($stockHealthData['labels'] ?? []) !!}, datasets: [{ data: {!! json_encode($stockHealthData['data'] ?? []) !!}, backgroundColor: {!! json_encode($stockHealthData['colors'] ?? []) !!}, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    new Chart(document.getElementById('todayActivityPieChart'), {
        type: 'pie',
        data: { labels: {!! json_encode($todayActivityData['labels'] ?? []) !!}, datasets: [{ data: {!! json_encode($todayActivityData['data'] ?? []) !!}, backgroundColor: {!! json_encode($todayActivityData['colors'] ?? []) !!}, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    new Chart(document.getElementById('deptConsumptionPieChart'), {
        type: 'pie',
        data: { labels: {!! json_encode($deptConsumptionData['labels'] ?? []) !!}, datasets: [{ data: {!! json_encode($deptConsumptionData['data'] ?? []) !!}, backgroundColor: {!! json_encode($deptConsumptionData['colors'] ?? []) !!}, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    new Chart(document.getElementById('inventorySourcePieChart'), {
        type: 'pie',
        data: { labels: {!! json_encode($inventorySourceData['labels'] ?? []) !!}, datasets: [{ data: {!! json_encode($inventorySourceData['data'] ?? []) !!}, backgroundColor: {!! json_encode($inventorySourceData['colors'] ?? []) !!}, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    new Chart(document.getElementById('pendingRequisitionPieChart'), {
        type: 'pie',
        data: { labels: {!! json_encode($pendingRequisitionData['labels'] ?? []) !!}, datasets: [{ data: {!! json_encode($pendingRequisitionData['data'] ?? []) !!}, backgroundColor: {!! json_encode($pendingRequisitionData['colors'] ?? []) !!}, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    new Chart(document.getElementById('issuesReturnsPieChart'), {
        type: 'pie',
        data: { labels: ['Issues to Departments', 'Returns from Departments'], datasets: [{ data: [{{ array_sum($dailyIssuesValues ?? [0]) }}, {{ array_sum($dailyReturnsValues ?? [0]) }}], backgroundColor: ['#f59e0b', '#10b981'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    // ========== DAILY CHARTS ==========
    @if(isset($dailyLabels) && count($dailyLabels) > 0)
    window.dailyStockChart = new Chart(document.getElementById('dailyStockChart'), {
        type: 'line',
        data: { labels: {!! json_encode($dailyLabels) !!}, datasets: [
            { label: 'Stock IN', data: {!! json_encode($dailyStockInValues) !!}, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.4 },
            { label: 'Stock OUT', data: {!! json_encode($dailyStockOutValues) !!}, borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, 0.1)', fill: true, tension: 0.4 }
        ] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
    });

    window.dailyDeptChart = new Chart(document.getElementById('dailyDeptChart'), {
        type: 'bar',
        data: { labels: {!! json_encode($dailyLabels) !!}, datasets: [
            { label: 'Issues to Departments', data: {!! json_encode($dailyIssuesValues) !!}, backgroundColor: '#f59e0b', borderRadius: 4 },
            { label: 'Returns from Departments', data: {!! json_encode($dailyReturnsValues) !!}, backgroundColor: '#10b981', borderRadius: 4 }
        ] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
    });
    @endif

    // ========== WEEKLY CHARTS ==========
    @if(isset($weeklyLabels) && count($weeklyLabels) > 0)
    window.weeklyStockChart = new Chart(document.getElementById('weeklyStockChart'), {
        type: 'line',
        data: { labels: {!! json_encode($weeklyLabels) !!}, datasets: [
            { label: 'Stock IN', data: {!! json_encode($weeklyStockInValues) !!}, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.4 },
            { label: 'Stock OUT', data: {!! json_encode($weeklyStockOutValues) !!}, borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, 0.1)', fill: true, tension: 0.4 }
        ] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
    });

    window.weeklyDeptChart = new Chart(document.getElementById('weeklyDeptChart'), {
        type: 'bar',
        data: { labels: {!! json_encode($weeklyLabels) !!}, datasets: [
            { label: 'Issues', data: {!! json_encode($weeklyIssuesValues) !!}, backgroundColor: '#f59e0b', borderRadius: 4 },
            { label: 'Returns', data: {!! json_encode($weeklyReturnsValues) !!}, backgroundColor: '#10b981', borderRadius: 4 }
        ] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
    });
    @endif

    // ========== MONTHLY CHARTS ==========
    @if(isset($monthlyLabels) && count($monthlyLabels) > 0)
    window.monthlyStockChart = new Chart(document.getElementById('monthlyStockChart'), {
        type: 'line',
        data: { labels: {!! json_encode($monthlyLabels) !!}, datasets: [
            { label: 'Stock IN', data: {!! json_encode($monthlyStockInValues) !!}, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.4 },
            { label: 'Stock OUT', data: {!! json_encode($monthlyStockOutValues) !!}, borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, 0.1)', fill: true, tension: 0.4 }
        ] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
    });

    window.monthlyDeptChart = new Chart(document.getElementById('monthlyDeptChart'), {
        type: 'bar',
        data: { labels: {!! json_encode($monthlyLabels) !!}, datasets: [
            { label: 'Issues', data: {!! json_encode($monthlyIssuesValues) !!}, backgroundColor: '#f59e0b', borderRadius: 4 },
            { label: 'Returns', data: {!! json_encode($monthlyReturnsValues) !!}, backgroundColor: '#10b981', borderRadius: 4 }
        ] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
    });
    @endif
});
</script>
@endsection
