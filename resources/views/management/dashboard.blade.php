@extends('layouts.management')

@section('title', 'Management Dashboard')
@section('page-title', 'Management Dashboard')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
*, *::before, *::after { box-sizing: border-box; }

body, .dashboard-root { font-family: 'DM Sans', sans-serif; }

.dashboard-root {
    --blue:    #2563eb;
    --green:   #059669;
    --amber:   #d97706;
    --violet:  #7c3aed;
    --rose:    #e11d48;
    --slate:   #475569;
    --surface: #ffffff;
    --bg:      #f1f5f9;
    --border:  #e2e8f0;
    --text:    #0f172a;
    --muted:   #64748b;
    background: var(--bg);
    padding: 0 0 2rem;
}

/* ── Header ─────────────────────────────────────────────────────────── */
.dash-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #1e40af 100%);
    border-radius: 16px;
    padding: 1.75rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}
.dash-header::before {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
}
.dash-header::after {
    content: '';
    position: absolute;
    right: 80px; bottom: -60px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,0.03);
}
.dash-header-left h2 {
    font-size: 1.35rem;
    font-weight: 600;
    color: #fff;
    margin: 0 0 0.25rem;
    letter-spacing: -0.01em;
}
.dash-header-left p {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.55);
    margin: 0;
}
.dash-header-date {
    font-family: 'DM Mono', monospace;
    font-size: 0.7rem;
    color: rgba(255,255,255,0.45);
    white-space: nowrap;
}

/* ── KPI Grid ────────────────────────────────────────────────────────── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media(max-width: 900px) { .kpi-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width: 540px) { .kpi-grid { grid-template-columns: 1fr; } }

.kpi-card {
    background: var(--surface);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    transition: transform 0.18s, box-shadow 0.18s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
.kpi-card .kpi-accent {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    border-radius: 14px 0 0 14px;
}
.kpi-card .kpi-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    margin-bottom: 0.85rem;
}
.kpi-card .kpi-label {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--muted);
    margin-bottom: 0.35rem;
}
.kpi-card .kpi-value {
    font-size: 1.4rem;
    font-weight: 600;
    color: var(--text);
    letter-spacing: -0.02em;
    line-height: 1;
    font-family: 'DM Mono', monospace;
}
.kpi-card .kpi-sub {
    font-size: 0.68rem;
    color: var(--muted);
    margin-top: 0.4rem;
}
.kpi-blue   .kpi-accent { background: var(--blue); }
.kpi-blue   .kpi-icon   { background: #eff6ff; color: var(--blue); }
.kpi-green  .kpi-accent { background: var(--green); }
.kpi-green  .kpi-icon   { background: #ecfdf5; color: var(--green); }
.kpi-amber  .kpi-accent { background: var(--amber); }
.kpi-amber  .kpi-icon   { background: #fffbeb; color: var(--amber); }
.kpi-violet .kpi-accent { background: var(--violet); }
.kpi-violet .kpi-icon   { background: #f5f3ff; color: var(--violet); }

/* ── Tab Bar ─────────────────────────────────────────────────────────── */
.tab-bar {
    display: flex;
    gap: 0.25rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0.35rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}
.tab-btn {
    flex: 1;
    min-width: 130px;
    padding: 0.55rem 1rem;
    font-size: 0.75rem;
    font-weight: 500;
    font-family: 'DM Sans', sans-serif;
    background: transparent;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    color: var(--muted);
    transition: all 0.15s;
    white-space: nowrap;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}
.tab-btn:hover { background: var(--bg); color: var(--text); }
.tab-btn.active { background: #0f172a; color: #fff; box-shadow: 0 2px 8px rgba(15,23,42,0.2); }

.tab-content { display: none; }
.tab-content.active { display: block; }

/* ── Sub Tab Bar ─────────────────────────────────────────────────────── */
.sub-tab-bar {
    display: flex;
    gap: 0.4rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}
.sub-tab-btn {
    padding: 0.35rem 1rem;
    font-size: 0.7rem;
    font-weight: 500;
    font-family: 'DM Sans', sans-serif;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    cursor: pointer;
    color: var(--muted);
    transition: all 0.15s;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.sub-tab-btn:hover { border-color: #94a3b8; color: var(--text); }
.sub-tab-btn.active { background: var(--blue); border-color: var(--blue); color: #fff; }
.sub-tab-content { display: none; }
.sub-tab-content.active { display: block; }

/* ── Summary Stat Cards ──────────────────────────────────────────────── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}
@media(max-width:800px) { .stat-grid { grid-template-columns: repeat(2,1fr); } }
.stat-card {
    background: var(--surface);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    border: 1px solid var(--border);
}
.stat-card .stat-label {
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--muted);
    margin-bottom: 0.4rem;
}
.stat-card .stat-value {
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--text);
    font-family: 'DM Mono', monospace;
    letter-spacing: -0.02em;
}
.stat-card.three-col-stat { }
.stat-grid-3 { grid-template-columns: repeat(3,1fr); }

/* ── Chart Panels ────────────────────────────────────────────────────── */
.chart-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}
@media(max-width:768px) { .chart-grid-2 { grid-template-columns: 1fr; } }

.panel {
    background: var(--surface);
    border-radius: 12px;
    border: 1px solid var(--border);
    overflow: hidden;
}
.panel-header {
    padding: 0.9rem 1.25rem 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.panel-title {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--slate);
}
.panel-body {
    padding: 0.75rem 1.25rem 1.25rem;
}
.panel-body canvas {
    max-height: 200px;
}

/* ── Data Table ──────────────────────────────────────────────────────── */
.data-wrap { overflow-x: auto; }
.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.72rem;
    font-family: 'DM Sans', sans-serif;
}
.data-table thead tr {
    background: #f8fafc;
    border-bottom: 2px solid var(--border);
}
.data-table th {
    padding: 0.6rem 0.75rem;
    text-align: left;
    font-weight: 600;
    color: var(--slate);
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}
.data-table td {
    padding: 0.55rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--text);
    vertical-align: middle;
}
.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover td { background: #f8fafc; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.font-mono { font-family: 'DM Mono', monospace; font-size: 0.68rem; }

/* ── Badges ──────────────────────────────────────────────────────────── */
.badge {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 0.02em;
}
.badge-in      { background: #d1fae5; color: #065f46; }
.badge-out     { background: #fee2e2; color: #991b1b; }
.badge-pending { background: #fef3c7; color: #92400e; }
.badge-approved{ background: #dbeafe; color: #1e40af; }
.badge-completed { background: #d1fae5; color: #065f46; }

/* ── Trend chip ──────────────────────────────────────────────────────── */
.trend-up   { color: #059669; font-size: 0.68rem; font-weight: 600; }
.trend-down { color: #e11d48; font-size: 0.68rem; font-weight: 600; }
</style>

<div class="dashboard-root">

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="dash-header">
        <div class="dash-header-left">
            <h2>Welcome back, {{ Auth::user()->first_name ?? 'Manager' }}</h2>
            <p>Inventory performance overview — all data is live</p>
        </div>
        <div class="dash-header-date">{{ now()->format('l, F j Y') }}</div>
    </div>

    {{-- ── KPI Row ──────────────────────────────────────────────────────── --}}
    <div class="kpi-grid">
        <div class="kpi-card kpi-blue">
            <div class="kpi-accent"></div>
            <div class="kpi-icon"><i class="fa fa-money text-green-500" aria-hidden="true"></i></div>
            <div class="kpi-label">Total Stock Value</div>
            <div class="kpi-value">{{ number_format($totalStockValue ?? 0, 0) }}</div>
            <div class="kpi-sub">UGX — current inventory</div>
        </div>
        <div class="kpi-card kpi-green">
            <div class="kpi-accent"></div>
            <div class="kpi-icon"><i class="fa fa-arrow-down" aria-hidden="true"></i></div>
            <div class="kpi-label">Stock IN this month</div>
            <div class="kpi-value">{{ number_format($monthlyStockIn ?? 0) }}</div>
            <div class="kpi-sub">
                <span class="trend-up">+{{ number_format($stockInGrowth ?? 0) }}%</span> vs last month
            </div>
        </div>
        <div class="kpi-card kpi-amber">
            <div class="kpi-accent"></div>
            <div class="kpi-icon"><i class="fa fa-arrow-up" aria-hidden="true"></i></div>
            <div class="kpi-label">Stock OUT this month</div>
            <div class="kpi-value">{{ number_format($monthlyStockOut ?? 0) }}</div>
            <div class="kpi-sub">
                @php $chg = $stockOutGrowth ?? 0; @endphp
                <span class="{{ $chg >= 0 ? 'trend-up' : 'trend-down' }}">
                    {{ $chg >= 0 ? '+' : '' }}{{ number_format($chg) }}%
                </span> vs last month
            </div>
        </div>
        <div class="kpi-card kpi-violet">
            <div class="kpi-accent"></div>
            <div class="kpi-icon"><i class="fa fa-bell" aria-hidden="true"></i></div>
            <div class="kpi-label">Pending Approvals</div>
            <div class="kpi-value">{{ number_format($pendingApprovals ?? 0) }}</div>
            <div class="kpi-sub">Require your attention</div>
        </div>
    </div>

    {{-- ── Main Tab Bar ─────────────────────────────────────────────────── --}}
    <div class="tab-bar">
        <button class="tab-btn active" data-tab="tab-stock">
            <span>📦</span> Stock Movements
        </button>
        <button class="tab-btn" data-tab="tab-po">
            <span>📄</span> Purchase Orders
        </button>
        <button class="tab-btn" data-tab="tab-grn">
            <span>📥</span> Goods Received
        </button>
        <button class="tab-btn" data-tab="tab-distribution">
            <span>🏢</span> Dept. Distribution
        </button>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: STOCK MOVEMENTS                                          --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div id="tab-stock" class="tab-content active">

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">Total Stock IN</div>
                <div class="stat-value">{{ number_format($totalStockIn ?? 0, 2) }} <span style="font-size:0.6rem;color:var(--muted)">units</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Stock OUT</div>
                <div class="stat-value">{{ number_format($totalStockOut ?? 0, 2) }} <span style="font-size:0.6rem;color:var(--muted)">units</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Net Change</div>
                <div class="stat-value {{ ($netChange ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                    {{ ($netChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($netChange ?? 0, 2) }}
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Value Moved</div>
                <div class="stat-value" style="font-size:1rem;">UGX {{ number_format($totalValueMoved ?? 0, 0) }}</div>
            </div>
        </div>

        <div class="sub-tab-bar">
            <button class="sub-tab-btn active" data-subtab="stock-daily">📅 Daily</button>
            <button class="sub-tab-btn" data-subtab="stock-weekly">📆 Weekly</button>
            <button class="sub-tab-btn" data-subtab="stock-monthly">📊 Monthly</button>
        </div>

        <div id="stock-daily" class="sub-tab-content active">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily Stock IN Value</span></div><div class="panel-body"><canvas id="stockDailyInChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily Stock OUT Value</span></div><div class="panel-body"><canvas id="stockDailyOutChart"></canvas></div></div>
            </div>
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily IN Count</span></div><div class="panel-body"><canvas id="stockDailyInCountChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily OUT Count</span></div><div class="panel-body"><canvas id="stockDailyOutCountChart"></canvas></div></div>
            </div>
        </div>

        <div id="stock-weekly" class="sub-tab-content">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Weekly IN vs OUT Value</span></div><div class="panel-body"><canvas id="stockWeeklyValueChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Weekly IN vs OUT Count</span></div><div class="panel-body"><canvas id="stockWeeklyCountChart"></canvas></div></div>
            </div>
        </div>

        <div id="stock-monthly" class="sub-tab-content">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Monthly IN vs OUT Value</span></div><div class="panel-body"><canvas id="stockMonthlyValueChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Monthly IN vs OUT Count</span></div><div class="panel-body"><canvas id="stockMonthlyCountChart"></canvas></div></div>
            </div>
        </div>

        {{-- Pie + Top Items --}}
        <div class="chart-grid-2" style="margin-top:1rem;">
            <div class="panel"><div class="panel-header"><span class="panel-title">IN vs OUT Overview</span></div><div class="panel-body"><canvas id="stockInOutPieChart"></canvas></div></div>
            <div class="panel"><div class="panel-header"><span class="panel-title">IN Source Breakdown</span></div><div class="panel-body"><canvas id="stockSourcePieChart"></canvas></div></div>
        </div>
        <div class="chart-grid-2">
            <div class="panel"><div class="panel-header"><span class="panel-title">Top 10 Moving Items</span></div><div class="panel-body"><canvas id="stockTopItemsChart"></canvas></div></div>
            <div class="panel">
                <div class="panel-header"><span class="panel-title">Recent Movements</span></div>
                <div class="panel-body" style="padding-top:0.5rem;">
                    <div class="data-wrap">
                        <table class="data-table">
                            <thead><tr><th>Date</th><th>Item</th><th>Type</th><th class="text-right">Qty</th></tr></thead>
                            <tbody>
                                @forelse($recentMovements ?? [] as $m)
                                <tr>
                                    <td class="font-mono">{{ $m->movement_date?->format('Y-m-d') }}</td>
                                    <td>{{ $m->inventoryItem->name ?? 'N/A' }}</td>
                                    <td><span class="badge {{ $m->movementType && $m->movementType->sign == '+' ? 'badge-in' : 'badge-out' }}">{{ $m->movementType->name ?? 'N/A' }}</span></td>
                                    <td class="text-right font-mono">{{ number_format($m->quantity_in_base_unit ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center" style="color:var(--muted);padding:1.5rem 0">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: PURCHASE ORDERS                                          --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div id="tab-po" class="tab-content">
        <div class="stat-grid">
            <div class="stat-card"><div class="stat-label">Total POs</div><div class="stat-value">{{ number_format($totalPOs ?? 0) }}</div></div>
            <div class="stat-card"><div class="stat-label">Total PO Value</div><div class="stat-value" style="font-size:1rem;">UGX {{ number_format($totalPOValue ?? 0, 0) }}</div></div>
            <div class="stat-card"><div class="stat-label">Pending POs</div><div class="stat-value" style="color:var(--amber)">{{ number_format($pendingPOs ?? 0) }}</div></div>
            <div class="stat-card"><div class="stat-label">Completed POs</div><div class="stat-value" style="color:var(--green)">{{ number_format($completedPOs ?? 0) }}</div></div>
        </div>

        <div class="sub-tab-bar">
            <button class="sub-tab-btn active" data-subtab="po-daily">📅 Daily</button>
            <button class="sub-tab-btn" data-subtab="po-weekly">📆 Weekly</button>
            <button class="sub-tab-btn" data-subtab="po-monthly">📊 Monthly</button>
        </div>

        <div id="po-daily" class="sub-tab-content active">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily PO Value</span></div><div class="panel-body"><canvas id="poDailyValueChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily PO Count</span></div><div class="panel-body"><canvas id="poDailyCountChart"></canvas></div></div>
            </div>
        </div>
        <div id="po-weekly" class="sub-tab-content">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Weekly PO Value</span></div><div class="panel-body"><canvas id="poWeeklyValueChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Weekly PO Count</span></div><div class="panel-body"><canvas id="poWeeklyCountChart"></canvas></div></div>
            </div>
        </div>
        <div id="po-monthly" class="sub-tab-content">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Monthly PO Value</span></div><div class="panel-body"><canvas id="poMonthlyValueChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Monthly PO Count</span></div><div class="panel-body"><canvas id="poMonthlyCountChart"></canvas></div></div>
            </div>
        </div>

        <div class="chart-grid-2" style="margin-top:1rem;">
            <div class="panel"><div class="panel-header"><span class="panel-title">PO Status Distribution</span></div><div class="panel-body"><canvas id="poStatusPieChart"></canvas></div></div>
            <div class="panel">
                <div class="panel-header"><span class="panel-title">Recent Purchase Orders</span></div>
                <div class="panel-body" style="padding-top:0.5rem;">
                    <div class="data-wrap">
                        <table class="data-table">
                            <thead><tr><th>PO #</th><th>Date</th><th>Vendor</th><th class="text-right">Amount</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentPOs ?? [] as $po)
                                <tr>
                                    <td class="font-mono">{{ $po->po_number }}</td>
                                    <td class="font-mono">{{ $po->po_date->format('Y-m-d') }}</td>
                                    <td>{{ $po->vendor->name ?? 'N/A' }}</td>
                                    <td class="text-right font-mono">{{ number_format($po->total_amount, 0) }}</td>
                                    <td><span class="badge badge-{{ $po->status }}">{{ ucfirst(str_replace('_', ' ', $po->status)) }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center" style="color:var(--muted);padding:1.5rem 0">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: GOODS RECEIVED NOTES                                     --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div id="tab-grn" class="tab-content">
        <div class="stat-grid">
            <div class="stat-card"><div class="stat-label">Total GRNs</div><div class="stat-value">{{ number_format($totalGRNs ?? 0) }}</div></div>
            <div class="stat-card"><div class="stat-label">Total GRN Value</div><div class="stat-value" style="font-size:1rem;">UGX {{ number_format($totalGRNValue ?? 0, 0) }}</div></div>
            <div class="stat-card"><div class="stat-label">Pending Inventory</div><div class="stat-value" style="color:var(--amber)">{{ number_format($grnCompletedCount ?? 0) }}</div></div>
            <div class="stat-card"><div class="stat-label">Inventory Updated</div><div class="stat-value" style="color:var(--green)">{{ number_format($grnInventoryUpdatedCount ?? 0) }}</div></div>
        </div>

        <div class="sub-tab-bar">
            <button class="sub-tab-btn active" data-subtab="grn-daily">📅 Daily</button>
            <button class="sub-tab-btn" data-subtab="grn-weekly">📆 Weekly</button>
            <button class="sub-tab-btn" data-subtab="grn-monthly">📊 Monthly</button>
        </div>

        <div id="grn-daily" class="sub-tab-content active">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily GRN Value</span></div><div class="panel-body"><canvas id="grnDailyValueChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily GRN Count</span></div><div class="panel-body"><canvas id="grnDailyCountChart"></canvas></div></div>
            </div>
        </div>
        <div id="grn-weekly" class="sub-tab-content">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Weekly GRN Value</span></div><div class="panel-body"><canvas id="grnWeeklyValueChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Weekly GRN Count</span></div><div class="panel-body"><canvas id="grnWeeklyCountChart"></canvas></div></div>
            </div>
        </div>
        <div id="grn-monthly" class="sub-tab-content">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Monthly GRN Value</span></div><div class="panel-body"><canvas id="grnMonthlyValueChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Monthly GRN Count</span></div><div class="panel-body"><canvas id="grnMonthlyCountChart"></canvas></div></div>
            </div>
        </div>

        <div class="chart-grid-2" style="margin-top:1rem;">
            <div class="panel"><div class="panel-header"><span class="panel-title">GRN Status Distribution</span></div><div class="panel-body"><canvas id="grnStatusPieChart"></canvas></div></div>
            <div class="panel">
                <div class="panel-header"><span class="panel-title">Recent GRNs</span></div>
                <div class="panel-body" style="padding-top:0.5rem;">
                    <div class="data-wrap">
                        <table class="data-table">
                            <thead><tr><th>GRN #</th><th>Date</th><th>Vendor</th><th class="text-right">Amount</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentGRNs ?? [] as $grn)
                                <tr>
                                    <td class="font-mono">{{ $grn->grn_number }}</td>
                                    <td class="font-mono">{{ $grn->received_date->format('Y-m-d') }}</td>
                                    <td>{{ $grn->vendor->name ?? 'N/A' }}</td>
                                    <td class="text-right font-mono">{{ number_format($grn->grn_total_amount, 0) }}</td>
                                    <td><span class="badge badge-{{ $grn->status }}">{{ ucfirst(str_replace('_', ' ', $grn->status)) }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center" style="color:var(--muted);padding:1.5rem 0">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 4: DEPARTMENT DISTRIBUTION                                  --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div id="tab-distribution" class="tab-content">
        <div class="stat-grid stat-grid-3" style="grid-template-columns:repeat(3,1fr)">
            <div class="stat-card"><div class="stat-label">Total Issued</div><div class="stat-value" style="color:var(--rose)">{{ number_format($totalIssued ?? 0, 2) }} <span style="font-size:0.6rem;color:var(--muted)">units</span></div></div>
            <div class="stat-card"><div class="stat-label">Total Returned</div><div class="stat-value" style="color:var(--green)">{{ number_format($totalReturned ?? 0, 2) }} <span style="font-size:0.6rem;color:var(--muted)">units</span></div></div>
            <div class="stat-card"><div class="stat-label">Total Consumed</div><div class="stat-value" style="color:var(--amber)">{{ number_format($totalConsumed ?? 0, 2) }} <span style="font-size:0.6rem;color:var(--muted)">units</span></div></div>
        </div>

        <div class="sub-tab-bar">
            <button class="sub-tab-btn active" data-subtab="dept-daily">📅 Daily</button>
            <button class="sub-tab-btn" data-subtab="dept-weekly">📆 Weekly</button>
            <button class="sub-tab-btn" data-subtab="dept-monthly">📊 Monthly</button>
        </div>

        <div id="dept-daily" class="sub-tab-content active">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily Issued to Departments</span></div><div class="panel-body"><canvas id="deptDailyIssuedChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Daily Net Consumption</span></div><div class="panel-body"><canvas id="deptDailyReturnedChart"></canvas></div></div>
            </div>
        </div>
        <div id="dept-weekly" class="sub-tab-content">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Weekly Issued vs Returned</span></div><div class="panel-body"><canvas id="deptWeeklyChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Weekly Consumption by Dept</span></div><div class="panel-body"><canvas id="deptWeeklyConsumptionChart"></canvas></div></div>
            </div>
        </div>
        <div id="dept-monthly" class="sub-tab-content">
            <div class="chart-grid-2">
                <div class="panel"><div class="panel-header"><span class="panel-title">Monthly Issued vs Returned</span></div><div class="panel-body"><canvas id="deptMonthlyChart"></canvas></div></div>
                <div class="panel"><div class="panel-header"><span class="panel-title">Monthly Consumption by Dept</span></div><div class="panel-body"><canvas id="deptMonthlyConsumptionChart"></canvas></div></div>
            </div>
        </div>

        <div class="chart-grid-2" style="margin-top:1rem;">
            <div class="panel"><div class="panel-header"><span class="panel-title">Consumption Share by Dept</span></div><div class="panel-body"><canvas id="deptConsumptionPieChart"></canvas></div></div>
            <div class="panel"><div class="panel-header"><span class="panel-title">Issued vs Returned by Dept</span></div><div class="panel-body"><canvas id="deptIssuedReturnedChart"></canvas></div></div>
        </div>

        {{-- Full-width transactions table --}}
        <div class="panel" style="margin-top:1rem;">
            <div class="panel-header" style="padding-bottom:0.75rem; border-bottom:1px solid var(--border);">
                <span class="panel-title">Detailed Department Transactions</span>
            </div>
            <div class="panel-body" style="padding-top:0.5rem;">
                <div class="data-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Requisition #</th>
                                <th>Department</th>
                                <th>Item</th>
                                <th class="text-right">Issued</th>
                                <th>Taken By</th>
                                <th class="text-right">Returned</th>
                                <th>Returned By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($distributions ?? [] as $d)
                            <tr>
                                <td class="font-mono">{{ $d->created_at?->format('Y-m-d') }}</td>
                                <td class="font-mono">{{ $d->departmentRequisition->requisition_number ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-approved">{{ $d->departmentRequisition->department->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $d->inventoryItem->name ?? 'N/A' }}</td>
                                <td class="text-right font-mono" style="color:var(--rose)">{{ number_format($d->quantity_issued, 2) }}</td>
                                <td>{{ $d->departmentRequisition->taken_by ?? '—' }}</td>
                                <td class="text-right font-mono" style="color:var(--green)">{{ number_format($d->quantity_returned ?? 0, 2) }}</td>
                                <td>{{ $d->departmentRequisition->returned_by ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center" style="color:var(--muted);padding:1.5rem 0">No distribution data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /tab-distribution --}}

</div>{{-- /dashboard-root --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab switching ────────────────────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        });
    });

    // ── Sub-tab switching (scoped by prefix) ─────────────────────────
    ['stock', 'po', 'grn', 'dept'].forEach(prefix => {
        const btns     = document.querySelectorAll(`.sub-tab-btn[data-subtab^="${prefix}"]`);
        const contents = document.querySelectorAll(`.sub-tab-content[id^="${prefix}"]`);
        btns.forEach(btn => {
            btn.addEventListener('click', () => {
                btns.forEach(b => b.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(btn.dataset.subtab).classList.add('active');
            });
        });
    });

    // ── Chart defaults ───────────────────────────────────────────────
    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#64748b';
    Chart.defaults.plugins.legend.labels.boxWidth = 10;
    Chart.defaults.plugins.legend.labels.padding  = 12;

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' } },
        scales: {
            x: { grid: { display: false }, ticks: { maxRotation: 30 } },
            y: { grid: { color: '#f1f5f9' }, border: { dash: [4,4] } }
        }
    };
    const pieOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' } }
    };

    function mkChart(id, type, labels, datasets, opts) {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el, {
            type,
            data: { labels, datasets },
            options: type === 'pie' || type === 'doughnut' ? pieOptions : { ...defaultOptions, ...opts }
        });
    }

    // ── Data from PHP ────────────────────────────────────────────────
    const dL = {!! json_encode($dailyLabels ?? []) !!};
    const wL = {!! json_encode($weeklyLabels ?? []) !!};
    const mL = {!! json_encode($monthlyLabels ?? []) !!};

    // ── STOCK ────────────────────────────────────────────────────────
    mkChart('stockDailyInChart',      'bar',  dL, [{ label:'IN Value',  data:{!! json_encode($dailyStockInValues ?? []) !!},  backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 }]);
    mkChart('stockDailyOutChart',     'bar',  dL, [{ label:'OUT Value', data:{!! json_encode($dailyStockOutValues ?? []) !!}, backgroundColor:'rgba(225,29,72,0.8)',  borderRadius:4 }]);
    mkChart('stockDailyInCountChart', 'line', dL, [{ label:'IN Count',  data:{!! json_encode($dailyStockInCounts ?? []) !!},  borderColor:'#059669', tension:0.4, fill:false, pointRadius:3 }]);
    mkChart('stockDailyOutCountChart','line', dL, [{ label:'OUT Count', data:{!! json_encode($dailyStockOutCounts ?? []) !!}, borderColor:'#e11d48', tension:0.4, fill:false, pointRadius:3 }]);
    mkChart('stockWeeklyValueChart',  'bar',  wL, [{ label:'IN',  data:{!! json_encode($weeklyStockInValues ?? []) !!},  backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 },
                                                    { label:'OUT', data:{!! json_encode($weeklyStockOutValues ?? []) !!}, backgroundColor:'rgba(225,29,72,0.8)', borderRadius:4 }]);
    mkChart('stockWeeklyCountChart',  'line', wL, [{ label:'IN Count',  data:{!! json_encode($weeklyStockInCounts ?? []) !!},  borderColor:'#059669', tension:0.4 },
                                                    { label:'OUT Count', data:{!! json_encode($weeklyStockOutCounts ?? []) !!}, borderColor:'#e11d48', tension:0.4 }]);
    mkChart('stockMonthlyValueChart', 'bar',  mL, [{ label:'IN',  data:{!! json_encode($monthlyStockInValues ?? []) !!},  backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 },
                                                    { label:'OUT', data:{!! json_encode($monthlyStockOutValues ?? []) !!}, backgroundColor:'rgba(225,29,72,0.8)', borderRadius:4 }]);
    mkChart('stockMonthlyCountChart', 'line', mL, [{ label:'IN Count',  data:{!! json_encode($monthlyStockInCounts ?? []) !!},  borderColor:'#059669', tension:0.4 },
                                                    { label:'OUT Count', data:{!! json_encode($monthlyStockOutCounts ?? []) !!}, borderColor:'#e11d48', tension:0.4 }]);
    mkChart('stockInOutPieChart',     'doughnut', ['Stock IN','Stock OUT'],
            [{ data:[{{ $totalStockIn ?? 0 }},{{ $totalStockOut ?? 0 }}], backgroundColor:['#059669','#e11d48'], hoverOffset:6 }]);
    mkChart('stockSourcePieChart',    'doughnut', {!! json_encode($sourceLabels ?? []) !!},
            [{ data:{!! json_encode($sourceData ?? []) !!}, backgroundColor:['#2563eb','#7c3aed','#ec4899','#f59e0b'], hoverOffset:6 }]);
    mkChart('stockTopItemsChart',     'bar', {!! json_encode($topItemsLabels ?? []) !!},
            [{ label:'Units Moved', data:{!! json_encode($topItemsData ?? []) !!}, backgroundColor:'rgba(245,158,11,0.8)', borderRadius:4 }],
            { indexAxis:'y' });

    // ── PO ───────────────────────────────────────────────────────────
    mkChart('poDailyValueChart',   'bar',  dL, [{ label:'PO Value', data:{!! json_encode($poDailyValues ?? []) !!},  backgroundColor:'rgba(124,58,237,0.8)', borderRadius:4 }]);
    mkChart('poDailyCountChart',   'line', dL, [{ label:'PO Count', data:{!! json_encode($poDailyCounts ?? []) !!},  borderColor:'#7c3aed', tension:0.4 }]);
    mkChart('poWeeklyValueChart',  'bar',  wL, [{ label:'PO Value', data:{!! json_encode($poWeeklyValues ?? []) !!}, backgroundColor:'rgba(124,58,237,0.8)', borderRadius:4 }]);
    mkChart('poWeeklyCountChart',  'line', wL, [{ label:'PO Count', data:{!! json_encode($poWeeklyCounts ?? []) !!}, borderColor:'#7c3aed', tension:0.4 }]);
    mkChart('poMonthlyValueChart', 'bar',  mL, [{ label:'PO Value', data:{!! json_encode($poMonthlyValues ?? []) !!}, backgroundColor:'rgba(124,58,237,0.8)', borderRadius:4 }]);
    mkChart('poMonthlyCountChart', 'line', mL, [{ label:'PO Count', data:{!! json_encode($poMonthlyCounts ?? []) !!}, borderColor:'#7c3aed', tension:0.4 }]);
    mkChart('poStatusPieChart',    'doughnut', {!! json_encode($poStatusLabels ?? []) !!},
            [{ data:{!! json_encode($poStatusData ?? []) !!}, backgroundColor:['#f59e0b','#059669','#2563eb','#f97316','#06b6d4','#e11d48'], hoverOffset:6 }]);

    // ── GRN ──────────────────────────────────────────────────────────
    mkChart('grnDailyValueChart',   'bar',  dL, [{ label:'GRN Value', data:{!! json_encode($dailyValues ?? []) !!},       backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 }]);
    mkChart('grnDailyCountChart',   'line', dL, [{ label:'GRN Count', data:{!! json_encode($dailyCounts ?? []) !!},       borderColor:'#059669', tension:0.4 }]);
    mkChart('grnWeeklyValueChart',  'bar',  wL, [{ label:'GRN Value', data:{!! json_encode($weeklyGRNValues ?? []) !!},   backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 }]);
    mkChart('grnWeeklyCountChart',  'line', wL, [{ label:'GRN Count', data:{!! json_encode($weeklyGRNCounts ?? []) !!},   borderColor:'#059669', tension:0.4 }]);
    mkChart('grnMonthlyValueChart', 'bar',  mL, [{ label:'GRN Value', data:{!! json_encode($monthlyValues ?? []) !!},     backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 }]);
    mkChart('grnMonthlyCountChart', 'line', mL, [{ label:'GRN Count', data:{!! json_encode($monthlyCounts ?? []) !!},     borderColor:'#059669', tension:0.4 }]);
    mkChart('grnStatusPieChart',    'doughnut', ['Draft','Completed','Inventory Updated'],
            [{ data:[{{ $grnDraftCount ?? 0 }},{{ $grnCompletedCount ?? 0 }},{{ $grnInventoryUpdatedCount ?? 0 }}],
               backgroundColor:['#f59e0b','#059669','#2563eb'], hoverOffset:6 }]);

    // ── DEPARTMENT ───────────────────────────────────────────────────
    mkChart('deptDailyIssuedChart',       'bar',  dL, [{ label:'Issued',   data:{!! json_encode($deptDailyIssued ?? []) !!},   backgroundColor:'rgba(225,29,72,0.8)',  borderRadius:4 },
                                                         { label:'Returned', data:{!! json_encode($deptDailyReturned ?? []) !!}, backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 }]);
    mkChart('deptDailyReturnedChart',     'line', dL, [{ label:'Net Consumption', data:{!! json_encode($deptDailyNet ?? []) !!}, borderColor:'#2563eb', tension:0.4, fill:true, backgroundColor:'rgba(37,99,235,0.05)' }]);
    mkChart('deptWeeklyChart',            'bar',  wL, [{ label:'Issued',   data:{!! json_encode($deptWeeklyIssued ?? []) !!},   backgroundColor:'rgba(225,29,72,0.8)',  borderRadius:4 },
                                                         { label:'Returned', data:{!! json_encode($deptWeeklyReturned ?? []) !!}, backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 }]);
    mkChart('deptWeeklyConsumptionChart', 'line', wL, [{ label:'Kitchen', data:{!! json_encode($deptWeeklyKitchen ?? []) !!}, borderColor:'#2563eb', tension:0.4 },
                                                         { label:'Bar',     data:{!! json_encode($deptWeeklyBar ?? []) !!},     borderColor:'#7c3aed', tension:0.4 },
                                                         { label:'Cafe',    data:{!! json_encode($deptWeeklyCafe ?? []) !!},    borderColor:'#ec4899', tension:0.4 }]);
    mkChart('deptMonthlyChart',           'bar',  mL, [{ label:'Issued',   data:{!! json_encode($deptMonthlyIssued ?? []) !!},   backgroundColor:'rgba(225,29,72,0.8)',  borderRadius:4 },
                                                         { label:'Returned', data:{!! json_encode($deptMonthlyReturned ?? []) !!}, backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 }]);
    mkChart('deptMonthlyConsumptionChart','line', mL, [{ label:'Kitchen', data:{!! json_encode($deptMonthlyKitchen ?? []) !!}, borderColor:'#2563eb', tension:0.4 },
                                                         { label:'Bar',     data:{!! json_encode($deptMonthlyBar ?? []) !!},     borderColor:'#7c3aed', tension:0.4 },
                                                         { label:'Cafe',    data:{!! json_encode($deptMonthlyCafe ?? []) !!},    borderColor:'#ec4899', tension:0.4 }]);
    mkChart('deptConsumptionPieChart',    'doughnut', {!! json_encode($deptLabels ?? []) !!},
            [{ data:{!! json_encode($deptConsumedData ?? []) !!}, backgroundColor:['#2563eb','#7c3aed','#ec4899'], hoverOffset:6 }]);
    mkChart('deptIssuedReturnedChart',    'bar', {!! json_encode($deptLabels ?? []) !!},
            [{ label:'Issued',   data:{!! json_encode($deptIssuedData ?? []) !!},   backgroundColor:'rgba(225,29,72,0.8)',  borderRadius:4 },
             { label:'Returned', data:{!! json_encode($deptReturnedData ?? []) !!}, backgroundColor:'rgba(5,150,105,0.8)', borderRadius:4 }]);
});
</script>

@endsection
