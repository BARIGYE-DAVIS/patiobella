@extends('layouts.management')

@section('title', 'Management Dashboard')

@section('page-title', 'Management Dashboard')

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
    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }

    .stat-stock { border-left-color: #3b82f6; }
    .stat-po { border-left-color: #8b5cf6; }
    .stat-grn { border-left-color: #10b981; }
    .stat-value { border-left-color: #f59e0b; }

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
    .two-col { display: flex; gap: 1.5rem; margin-bottom: 1.5rem; }
    .three-col { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
    .col { flex: 1; }

    .badge-status { padding: 2px 8px; border-radius: 20px; font-size: 0.65rem; font-weight: 500; display: inline-block; }
    .badge-in { background: #d1fae5; color: #065f46; }
    .badge-out { background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-approved { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #a7f3d0; color: #065f46; }

    .data-table { width: 100%; border-collapse: collapse; font-size: 0.7rem; }
    .data-table th { background: #f8fafc; padding: 0.5rem; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.5rem; border-bottom: 1px solid #e2e8f0; }
    .data-table tr:hover { background: #f8fafc; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    .tabs-container { margin-bottom: 1.5rem; }
    .tabs { display: flex; gap: 0.5rem; border-bottom: 2px solid #e5e7eb; flex-wrap: wrap; }
    .tab-btn { padding: 0.6rem 1.2rem; font-size: 0.8rem; font-weight: 500; background: transparent; border: none; border-bottom: 2px solid transparent; cursor: pointer; transition: all 0.2s; color: #6b7280; margin-bottom: -2px; }
    .tab-btn:hover { color: #374151; }
    .tab-btn.active { color: #3b82f6; border-bottom-color: #3b82f6; }
    .tab-content { display: none; padding: 1.5rem 0; }
    .tab-content.active { display: block; }

    .sub-tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; padding-left: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; }
    .sub-tab-btn { padding: 0.3rem 1rem; font-size: 0.7rem; background: #f3f4f6; border: none; border-radius: 20px; cursor: pointer; transition: all 0.2s; color: #6b7280; }
    .sub-tab-btn:hover { background: #e5e7eb; }
    .sub-tab-btn.active { background: #3b82f6; color: white; }
    .sub-tab-content { display: none; }
    .sub-tab-content.active { display: block; }

    .kpi-row { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .kpi-card { flex: 1; min-width: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 1rem; }
    .kpi-card h4 { font-size: 0.7rem; opacity: 0.9; margin-bottom: 0.5rem; }
    .kpi-card .kpi-value { font-size: 1.5rem; font-weight: bold; }
    .kpi-card .kpi-label { font-size: 0.65rem; opacity: 0.8; margin-top: 0.25rem; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="space-y-6">

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <h2 class="text-xl font-bold">Welcome back, {{ Auth::user()->first_name ?? 'Manager' }}!</h2>
        <p class="text-blue-100 mt-1">Here's your complete inventory performance dashboard</p>
    </div>

    {{-- Main KPIs --}}
    <div class="kpi-row">
        <div class="kpi-card" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
            <h4>Total Stock Value</h4>
            <div class="kpi-value">UGX {{ number_format($totalStockValue ?? 0, 2) }}</div>
            <div class="kpi-label">Current inventory value</div>
        </div>
        <div class="kpi-card" style="background: linear-gradient(135deg, #10b981, #059669);">
            <h4>Stock IN (This Month)</h4>
            <div class="kpi-value">{{ number_format($monthlyStockIn ?? 0) }} units</div>
            <div class="kpi-label">+{{ number_format($stockInGrowth ?? 0) }}% from last month</div>
        </div>
        <div class="kpi-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <h4>Stock OUT (This Month)</h4>
            <div class="kpi-value">{{ number_format($monthlyStockOut ?? 0) }} units</div>
            <div class="kpi-label">{{ number_format($stockOutGrowth ?? 0) }}% change</div>
        </div>
        <div class="kpi-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <h4>Pending Approvals</h4>
            <div class="kpi-value">{{ number_format($pendingApprovals ?? 0) }}</div>
            <div class="kpi-label">Require your attention</div>
        </div>
    </div>

    {{-- MAIN TABS --}}
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-btn active" data-tab="tab-stock">📦 Stock Movements</button>
            <button class="tab-btn" data-tab="tab-po">📄 Purchase Orders</button>
            <button class="tab-btn" data-tab="tab-grn">📥 Goods Received</button>
            <button class="tab-btn" data-tab="tab-distribution">🏢 Department Distribution</button>
        </div>

        {{-- ============================================================ --}}
        {{-- TAB 1: STOCK MOVEMENTS with Daily/Weekly/Monthly Trends --}}
        {{-- ============================================================ --}}
        <div id="tab-stock" class="tab-content active">
            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card stat-stock"><h3>Total Stock IN</h3><div class="value">{{ number_format($totalStockIn ?? 0, 2) }} units</div></div>
                <div class="stat-card stat-stock"><h3>Total Stock OUT</h3><div class="value">{{ number_format($totalStockOut ?? 0, 2) }} units</div></div>
                <div class="stat-card stat-stock"><h3>Net Change</h3><div class="value {{ ($netChange ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">{{ ($netChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($netChange ?? 0, 2) }} units</div></div>
                <div class="stat-card stat-value"><h3>Total Value Moved</h3><div class="value">UGX {{ number_format($totalValueMoved ?? 0, 2) }}</div></div>
            </div>

            {{-- SUB TABS for Stock Movements Trends --}}
            <div class="sub-tabs">
                <button class="sub-tab-btn active" data-subtab="stock-daily">📅 Daily Trend</button>
                <button class="sub-tab-btn" data-subtab="stock-weekly">📆 Weekly Trend</button>
                <button class="sub-tab-btn" data-subtab="stock-monthly">📊 Monthly Trend</button>
            </div>

            {{-- STOCK DAILY TREND --}}
            <div id="stock-daily" class="sub-tab-content active">
                <div class="two-col">
                    <div class="chart-container"><div class="chart-title">Daily Stock IN Value</div><canvas id="stockDailyInChart"></canvas></div>
                    <div class="chart-container"><div class="chart-title">Daily Stock OUT Value</div><canvas id="stockDailyOutChart"></canvas></div>
                </div>
                <div class="two-col">
                    <div class="chart-container"><div class="chart-title">Daily Stock IN Count</div><canvas id="stockDailyInCountChart"></canvas></div>
                    <div class="chart-container"><div class="chart-title">Daily Stock OUT Count</div><canvas id="stockDailyOutCountChart"></canvas></div>
                </div>
            </div>

            {{-- STOCK WEEKLY TREND --}}
            <div id="stock-weekly" class="sub-tab-content">
                <div class="two-col">
                    <div class="chart-container"><div class="chart-title">Weekly Stock IN vs OUT Value</div><canvas id="stockWeeklyValueChart"></canvas></div>
                    <div class="chart-container"><div class="chart-title">Weekly Stock IN vs OUT Count</div><canvas id="stockWeeklyCountChart"></canvas></div>
                </div>
            </div>

            {{-- STOCK MONTHLY TREND --}}
            <div id="stock-monthly" class="sub-tab-content">
                <div class="two-col">
                    <div class="chart-container"><div class="chart-title">Monthly Stock IN vs OUT Value</div><canvas id="stockMonthlyValueChart"></canvas></div>
                    <div class="chart-container"><div class="chart-title">Monthly Stock IN vs OUT Count</div><canvas id="stockMonthlyCountChart"></canvas></div>
                </div>
            </div>

            {{-- Pie Charts Section --}}
            <div class="two-col">
                <div class="chart-container"><div class="chart-title">Stock IN vs OUT (Overall)</div><canvas id="stockInOutPieChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Stock IN Source Breakdown</div><canvas id="stockSourcePieChart"></canvas></div>
            </div>

            {{-- Top Items & Recent Movements --}}
            <div class="two-col">
                <div class="chart-container"><div class="chart-title">Top 10 Moving Items</div><canvas id="stockTopItemsChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Recent Stock Movements</div>
                    <table class="data-table"><thead><tr><th>Date</th><th>Item</th><th>Type</th><th class="text-right">Qty</th></tr></thead>
                    <tbody>@forelse($recentMovements ?? [] as $m)<tr><td>{{ $m->movement_date?->format('Y-m-d') }}</td><td>{{ $m->inventoryItem->name ?? 'N/A' }}</td><td><span class="badge-status {{ $m->movementType && $m->movementType->sign == '+' ? 'badge-in' : 'badge-out' }}">{{ $m->movementType->name ?? 'N/A' }}</span></td><td class="text-right">{{ number_format($m->quantity_in_base_unit ?? 0, 2) }}</td></tr>@empty<tr><td colspan="4" class="text-center">No data</td></tr>@endforelse</tbody></table>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TAB 2: PURCHASE ORDERS with Daily/Weekly/Monthly Trends --}}
        {{-- ============================================================ --}}
        <div id="tab-po" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card stat-po"><h3>Total POs</h3><div class="value">{{ number_format($totalPOs ?? 0) }}</div></div>
                <div class="stat-card stat-po"><h3>Total PO Value</h3><div class="value">UGX {{ number_format($totalPOValue ?? 0, 2) }}</div></div>
                <div class="stat-card stat-po"><h3>Pending POs</h3><div class="value">{{ number_format($pendingPOs ?? 0) }}</div></div>
                <div class="stat-card stat-po"><h3>Completed POs</h3><div class="value">{{ number_format($completedPOs ?? 0) }}</div></div>
            </div>

            <div class="sub-tabs">
                <button class="sub-tab-btn active" data-subtab="po-daily">📅 Daily Trend</button>
                <button class="sub-tab-btn" data-subtab="po-weekly">📆 Weekly Trend</button>
                <button class="sub-tab-btn" data-subtab="po-monthly">📊 Monthly Trend</button>
            </div>

            <div id="po-daily" class="sub-tab-content active">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Daily PO Value</div><canvas id="poDailyValueChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Daily PO Count</div><canvas id="poDailyCountChart"></canvas></div></div>
            </div>
            <div id="po-weekly" class="sub-tab-content">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Weekly PO Value</div><canvas id="poWeeklyValueChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Weekly PO Count</div><canvas id="poWeeklyCountChart"></canvas></div></div>
            </div>
            <div id="po-monthly" class="sub-tab-content">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Monthly PO Value</div><canvas id="poMonthlyValueChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Monthly PO Count</div><canvas id="poMonthlyCountChart"></canvas></div></div>
            </div>

            <div class="two-col">
                <div class="chart-container"><div class="chart-title">PO Status Distribution</div><canvas id="poStatusPieChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Recent Purchase Orders</div>
                    <table class="data-table"><thead><tr><th>PO #</th><th>Date</th><th>Vendor</th><th class="text-right">Amount</th><th>Status</th></tr></thead>
                    <tbody>@forelse($recentPOs ?? [] as $po)<tr><td class="font-mono">{{ $po->po_number }}</td><td>{{ $po->po_date->format('Y-m-d') }}</td><td>{{ $po->vendor->name ?? 'N/A' }}</td><td class="text-right">UGX {{ number_format($po->total_amount, 2) }}</td><td><span class="badge-status status-{{ $po->status }}">{{ ucfirst(str_replace('_', ' ', $po->status)) }}</span></td></tr>@empty<tr><td colspan="5" class="text-center">No data</td></tr>@endforelse</tbody></table>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TAB 3: GOODS RECEIVED NOTES with Daily/Weekly/Monthly Trends --}}
        {{-- ============================================================ --}}
        <div id="tab-grn" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card stat-grn"><h3>Total GRNs</h3><div class="value">{{ number_format($totalGRNs ?? 0) }}</div></div>
                <div class="stat-card stat-grn"><h3>Total GRN Value</h3><div class="value">UGX {{ number_format($totalGRNValue ?? 0, 2) }}</div></div>
                <div class="stat-card stat-grn"><h3>Pending Inventory</h3><div class="value">{{ number_format($grnCompletedCount ?? 0) }}</div></div>
                <div class="stat-card stat-grn"><h3>Inventory Updated</h3><div class="value">{{ number_format($grnInventoryUpdatedCount ?? 0) }}</div></div>
            </div>

            <div class="sub-tabs">
                <button class="sub-tab-btn active" data-subtab="grn-daily">📅 Daily Trend</button>
                <button class="sub-tab-btn" data-subtab="grn-weekly">📆 Weekly Trend</button>
                <button class="sub-tab-btn" data-subtab="grn-monthly">📊 Monthly Trend</button>
            </div>

            <div id="grn-daily" class="sub-tab-content active">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Daily GRN Value</div><canvas id="grnDailyValueChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Daily GRN Count</div><canvas id="grnDailyCountChart"></canvas></div></div>
            </div>
            <div id="grn-weekly" class="sub-tab-content">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Weekly GRN Value</div><canvas id="grnWeeklyValueChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Weekly GRN Count</div><canvas id="grnWeeklyCountChart"></canvas></div></div>
            </div>
            <div id="grn-monthly" class="sub-tab-content">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Monthly GRN Value</div><canvas id="grnMonthlyValueChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Monthly GRN Count</div><canvas id="grnMonthlyCountChart"></canvas></div></div>
            </div>

            <div class="two-col">
                <div class="chart-container"><div class="chart-title">GRN Status Distribution</div><canvas id="grnStatusPieChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Recent GRNs</div>
                    <table class="data-table"><thead><tr><th>GRN #</th><th>Date</th><th>Vendor</th><th class="text-right">Amount</th><th>Status</th></tr></thead>
                    <tbody>@forelse($recentGRNs ?? [] as $grn)<tr><td class="font-mono">{{ $grn->grn_number }}</td><td>{{ $grn->received_date->format('Y-m-d') }}</td><td>{{ $grn->vendor->name ?? 'N/A' }}</td><td class="text-right">UGX {{ number_format($grn->grn_total_amount, 2) }}</td><td><span class="badge-status status-{{ $grn->status }}">{{ ucfirst(str_replace('_', ' ', $grn->status)) }}</span></td></tr>@empty<tr><td colspan="5" class="text-center">No data</td></tr>@endforelse</tbody></table>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TAB 4: DEPARTMENT DISTRIBUTION with Daily/Weekly/Monthly Trends --}}
        {{-- ============================================================ --}}
        <div id="tab-distribution" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="stat-card"><h3>Total Issued</h3><div class="value">{{ number_format($totalIssued ?? 0, 2) }} units</div></div>
                <div class="stat-card"><h3>Total Returned</h3><div class="value">{{ number_format($totalReturned ?? 0, 2) }} units</div></div>
                <div class="stat-card"><h3>Total Consumed</h3><div class="value">{{ number_format($totalConsumed ?? 0, 2) }} units</div></div>
            </div>

            <div class="sub-tabs">
                <button class="sub-tab-btn active" data-subtab="dept-daily">📅 Daily Trend</button>
                <button class="sub-tab-btn" data-subtab="dept-weekly">📆 Weekly Trend</button>
                <button class="sub-tab-btn" data-subtab="dept-monthly">📊 Monthly Trend</button>
            </div>

            <div id="dept-daily" class="sub-tab-content active">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Daily Issued to Departments</div><canvas id="deptDailyIssuedChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Daily Returned from Departments</div><canvas id="deptDailyReturnedChart"></canvas></div></div>
            </div>
            <div id="dept-weekly" class="sub-tab-content">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Weekly Issued vs Returned</div><canvas id="deptWeeklyChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Weekly Consumption by Department</div><canvas id="deptWeeklyConsumptionChart"></canvas></div></div>
            </div>
            <div id="dept-monthly" class="sub-tab-content">
                <div class="two-col"><div class="chart-container"><div class="chart-title">Monthly Issued vs Returned</div><canvas id="deptMonthlyChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Monthly Consumption by Department</div><canvas id="deptMonthlyConsumptionChart"></canvas></div></div>
            </div>

            <div class="two-col">
                <div class="chart-container"><div class="chart-title">Department Consumption Share</div><canvas id="deptConsumptionPieChart"></canvas></div>
                <div class="chart-container"><div class="chart-title">Issued vs Returned by Department</div><canvas id="deptIssuedReturnedChart"></canvas></div>
            </div>

            <div class="chart-container">
                <div class="chart-title">Detailed Department Transactions</div>
                <table class="data-table"><thead><tr><th>Date</th><th>Requisition #</th><th>Department</th><th>Item</th><th class="text-right">Issued</th><th>Taken By</th><th class="text-right">Returned</th><th>Returned By</th></tr></thead>
                <tbody>@forelse($distributions ?? [] as $d)<tr><td>{{ $d->created_at?->format('Y-m-d') }}</td><td class="font-mono">{{ $d->departmentRequisition->requisition_number ?? 'N/A' }}</td><td>{{ $d->departmentRequisition->department->name ?? 'N/A' }}</td><td>{{ $d->inventoryItem->name ?? 'N/A' }}</td><td class="text-right">{{ number_format($d->quantity_issued, 2) }}</td><td>{{ $d->departmentRequisition->taken_by ?? 'N/A' }}</td><td class="text-right">{{ number_format($d->quantity_returned ?? 0, 2) }}</td><td>{{ $d->departmentRequisition->returned_by ?? 'N/A' }}</td></tr>@empty<tr><td colspan="8" class="text-center">No data</td></tr>@endforelse</tbody></table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // MAIN TAB SWITCHING
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
    function initSubTabs(prefix) {
        const btns = document.querySelectorAll(`.sub-tab-btn[data-subtab^="${prefix}"]`);
        const contents = document.querySelectorAll(`.sub-tab-content[id^="${prefix}"]`);
        btns.forEach(btn => {
            btn.addEventListener('click', () => {
                const subTabId = btn.getAttribute('data-subtab');
                btns.forEach(b => b.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(subTabId).classList.add('active');
            });
        });
    }
    initSubTabs('stock'); initSubTabs('po'); initSubTabs('grn'); initSubTabs('dept');

    // Helper to get data from PHP
    const dailyLabels = {!! json_encode($dailyLabels ?? []) !!};
    const weeklyLabels = {!! json_encode($weeklyLabels ?? []) !!};
    const monthlyLabels = {!! json_encode($monthlyLabels ?? []) !!};

    // ========== STOCK CHARTS ==========
    if(document.getElementById('stockDailyInChart')) {
        new Chart(document.getElementById('stockDailyInChart'), { type: 'bar', data: { labels: dailyLabels, datasets: [{ label: 'Stock IN Value', data: {!! json_encode($dailyStockInValues ?? []) !!}, backgroundColor: '#10b981' }] } });
        new Chart(document.getElementById('stockDailyOutChart'), { type: 'bar', data: { labels: dailyLabels, datasets: [{ label: 'Stock OUT Value', data: {!! json_encode($dailyStockOutValues ?? []) !!}, backgroundColor: '#ef4444' }] } });
        new Chart(document.getElementById('stockDailyInCountChart'), { type: 'line', data: { labels: dailyLabels, datasets: [{ label: 'Stock IN Count', data: {!! json_encode($dailyStockInCounts ?? []) !!}, borderColor: '#10b981' }] } });
        new Chart(document.getElementById('stockDailyOutCountChart'), { type: 'line', data: { labels: dailyLabels, datasets: [{ label: 'Stock OUT Count', data: {!! json_encode($dailyStockOutCounts ?? []) !!}, borderColor: '#ef4444' }] } });
        new Chart(document.getElementById('stockWeeklyValueChart'), { type: 'bar', data: { labels: weeklyLabels, datasets: [{ label: 'Stock IN', data: {!! json_encode($weeklyStockInValues ?? []) !!}, backgroundColor: '#10b981' }, { label: 'Stock OUT', data: {!! json_encode($weeklyStockOutValues ?? []) !!}, backgroundColor: '#ef4444' }] } });
        new Chart(document.getElementById('stockWeeklyCountChart'), { type: 'line', data: { labels: weeklyLabels, datasets: [{ label: 'Stock IN Count', data: {!! json_encode($weeklyStockInCounts ?? []) !!}, borderColor: '#10b981' }, { label: 'Stock OUT Count', data: {!! json_encode($weeklyStockOutCounts ?? []) !!}, borderColor: '#ef4444' }] } });
        new Chart(document.getElementById('stockMonthlyValueChart'), { type: 'bar', data: { labels: monthlyLabels, datasets: [{ label: 'Stock IN', data: {!! json_encode($monthlyStockInValues ?? []) !!}, backgroundColor: '#10b981' }, { label: 'Stock OUT', data: {!! json_encode($monthlyStockOutValues ?? []) !!}, backgroundColor: '#ef4444' }] } });
        new Chart(document.getElementById('stockMonthlyCountChart'), { type: 'line', data: { labels: monthlyLabels, datasets: [{ label: 'Stock IN Count', data: {!! json_encode($monthlyStockInCounts ?? []) !!}, borderColor: '#10b981' }, { label: 'Stock OUT Count', data: {!! json_encode($monthlyStockOutCounts ?? []) !!}, borderColor: '#ef4444' }] } });
        new Chart(document.getElementById('stockInOutPieChart'), { type: 'pie', data: { labels: ['Stock IN', 'Stock OUT'], datasets: [{ data: [{{ $totalStockIn ?? 0 }}, {{ $totalStockOut ?? 0 }}], backgroundColor: ['#10b981', '#ef4444'] }] } });
        new Chart(document.getElementById('stockSourcePieChart'), { type: 'pie', data: { labels: {!! json_encode($sourceLabels ?? []) !!}, datasets: [{ data: {!! json_encode($sourceData ?? []) !!}, backgroundColor: ['#3b82f6', '#8b5cf6', '#ec489a', '#f59e0b'] }] } });
        new Chart(document.getElementById('stockTopItemsChart'), { type: 'bar', data: { labels: {!! json_encode($topItemsLabels ?? []) !!}, datasets: [{ label: 'Units Moved', data: {!! json_encode($topItemsData ?? []) !!}, backgroundColor: '#f59e0b' }] } });
    }

    // ========== PO CHARTS ==========
    if(document.getElementById('poDailyValueChart')) {
        new Chart(document.getElementById('poDailyValueChart'), { type: 'bar', data: { labels: dailyLabels, datasets: [{ label: 'PO Value', data: {!! json_encode($poDailyValues ?? []) !!}, backgroundColor: '#8b5cf6' }] } });
        new Chart(document.getElementById('poDailyCountChart'), { type: 'line', data: { labels: dailyLabels, datasets: [{ label: 'PO Count', data: {!! json_encode($poDailyCounts ?? []) !!}, borderColor: '#8b5cf6' }] } });
        new Chart(document.getElementById('poWeeklyValueChart'), { type: 'bar', data: { labels: weeklyLabels, datasets: [{ label: 'PO Value', data: {!! json_encode($poWeeklyValues ?? []) !!}, backgroundColor: '#8b5cf6' }] } });
        new Chart(document.getElementById('poWeeklyCountChart'), { type: 'line', data: { labels: weeklyLabels, datasets: [{ label: 'PO Count', data: {!! json_encode($poWeeklyCounts ?? []) !!}, borderColor: '#8b5cf6' }] } });
        new Chart(document.getElementById('poMonthlyValueChart'), { type: 'line', data: { labels: monthlyLabels, datasets: [{ label: 'PO Value', data: {!! json_encode($poMonthlyValues ?? []) !!}, borderColor: '#8b5cf6', fill: true }] } });
        new Chart(document.getElementById('poMonthlyCountChart'), { type: 'bar', data: { labels: monthlyLabels, datasets: [{ label: 'PO Count', data: {!! json_encode($poMonthlyCounts ?? []) !!}, backgroundColor: '#8b5cf6' }] } });
        new Chart(document.getElementById('poStatusPieChart'), { type: 'pie', data: { labels: {!! json_encode($poStatusLabels ?? []) !!}, datasets: [{ data: {!! json_encode($poStatusData ?? []) !!}, backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#f97316', '#06b6d4', '#ef4444'] }] } });
    }

    // ========== GRN CHARTS ==========
    if(document.getElementById('grnDailyValueChart')) {
        new Chart(document.getElementById('grnDailyValueChart'), { type: 'bar', data: { labels: dailyLabels, datasets: [{ label: 'GRN Value', data: {!! json_encode($dailyValues ?? []) !!}, backgroundColor: '#10b981' }] } });
        new Chart(document.getElementById('grnDailyCountChart'), { type: 'line', data: { labels: dailyLabels, datasets: [{ label: 'GRN Count', data: {!! json_encode($dailyCounts ?? []) !!}, borderColor: '#10b981' }] } });
        new Chart(document.getElementById('grnWeeklyValueChart'), { type: 'bar', data: { labels: weeklyLabels, datasets: [{ label: 'GRN Value', data: {!! json_encode($weeklyGRNValues ?? []) !!}, backgroundColor: '#10b981' }] } });
        new Chart(document.getElementById('grnWeeklyCountChart'), { type: 'line', data: { labels: weeklyLabels, datasets: [{ label: 'GRN Count', data: {!! json_encode($weeklyGRNCounts ?? []) !!}, borderColor: '#10b981' }] } });
        new Chart(document.getElementById('grnMonthlyValueChart'), { type: 'line', data: { labels: monthlyLabels, datasets: [{ label: 'GRN Value', data: {!! json_encode($monthlyValues ?? []) !!}, borderColor: '#10b981', fill: true }] } });
        new Chart(document.getElementById('grnMonthlyCountChart'), { type: 'bar', data: { labels: monthlyLabels, datasets: [{ label: 'GRN Count', data: {!! json_encode($monthlyCounts ?? []) !!}, backgroundColor: '#10b981' }] } });
        new Chart(document.getElementById('grnStatusPieChart'), { type: 'pie', data: { labels: ['Draft', 'Completed', 'Inventory Updated'], datasets: [{ data: [{{ $grnDraftCount ?? 0 }}, {{ $grnCompletedCount ?? 0 }}, {{ $grnInventoryUpdatedCount ?? 0 }}], backgroundColor: ['#f59e0b', '#10b981', '#3b82f6'] }] } });
    }

    // ========== DEPARTMENT CHARTS ==========
    if(document.getElementById('deptDailyIssuedChart')) {
        new Chart(document.getElementById('deptDailyIssuedChart'), { type: 'bar', data: { labels: dailyLabels, datasets: [{ label: 'Issued', data: {!! json_encode($deptDailyIssued ?? []) !!}, backgroundColor: '#ef4444' }, { label: 'Returned', data: {!! json_encode($deptDailyReturned ?? []) !!}, backgroundColor: '#10b981' }] } });
        new Chart(document.getElementById('deptDailyReturnedChart'), { type: 'line', data: { labels: dailyLabels, datasets: [{ label: 'Net Consumption', data: {!! json_encode($deptDailyNet ?? []) !!}, borderColor: '#3b82f6', fill: true }] } });
        new Chart(document.getElementById('deptWeeklyChart'), { type: 'bar', data: { labels: weeklyLabels, datasets: [{ label: 'Issued', data: {!! json_encode($deptWeeklyIssued ?? []) !!}, backgroundColor: '#ef4444' }, { label: 'Returned', data: {!! json_encode($deptWeeklyReturned ?? []) !!}, backgroundColor: '#10b981' }] } });
        new Chart(document.getElementById('deptWeeklyConsumptionChart'), { type: 'line', data: { labels: weeklyLabels, datasets: [{ label: 'Kitchen', data: {!! json_encode($deptWeeklyKitchen ?? []) !!}, borderColor: '#3b82f6' }, { label: 'Bar', data: {!! json_encode($deptWeeklyBar ?? []) !!}, borderColor: '#8b5cf6' }, { label: 'Cafe', data: {!! json_encode($deptWeeklyCafe ?? []) !!}, borderColor: '#ec489a' }] } });
        new Chart(document.getElementById('deptMonthlyChart'), { type: 'bar', data: { labels: monthlyLabels, datasets: [{ label: 'Issued', data: {!! json_encode($deptMonthlyIssued ?? []) !!}, backgroundColor: '#ef4444' }, { label: 'Returned', data: {!! json_encode($deptMonthlyReturned ?? []) !!}, backgroundColor: '#10b981' }] } });
        new Chart(document.getElementById('deptMonthlyConsumptionChart'), { type: 'line', data: { labels: monthlyLabels, datasets: [{ label: 'Kitchen', data: {!! json_encode($deptMonthlyKitchen ?? []) !!}, borderColor: '#3b82f6' }, { label: 'Bar', data: {!! json_encode($deptMonthlyBar ?? []) !!}, borderColor: '#8b5cf6' }, { label: 'Cafe', data: {!! json_encode($deptMonthlyCafe ?? []) !!}, borderColor: '#ec489a' }] } });
        new Chart(document.getElementById('deptConsumptionPieChart'), { type: 'pie', data: { labels: {!! json_encode($deptLabels ?? []) !!}, datasets: [{ data: {!! json_encode($deptConsumedData ?? []) !!}, backgroundColor: ['#3b82f6', '#8b5cf6', '#ec489a'] }] } });
        new Chart(document.getElementById('deptIssuedReturnedChart'), { type: 'bar', data: { labels: {!! json_encode($deptLabels ?? []) !!}, datasets: [{ label: 'Issued', data: {!! json_encode($deptIssuedData ?? []) !!}, backgroundColor: '#ef4444' }, { label: 'Returned', data: {!! json_encode($deptReturnedData ?? []) !!}, backgroundColor: '#10b981' }] } });
    }
});
</script>
@endsection
