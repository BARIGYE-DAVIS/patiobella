@extends('layouts.management')

@section('title', 'Management Reports')
@section('page-title', 'Management Reports Dashboard')

@section('content')
<style>
    .report-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        text-align: center;
    }
    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        border-color: #3b82f6;
    }
    .report-card.active {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .report-icon {
        font-size: 28px;
        margin-bottom: 8px;
    }
    .filter-bar {
        background: #f9fafb;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .data-table th, .data-table td {
        border: 1px solid #e5e7eb;
        padding: 10px 8px;
        vertical-align: middle;
    }
    .data-table th {
        background: #f3f4f6;
        font-weight: 600;
    }
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .summary-card {
        background: white;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        border: 1px solid #e5e7eb;
    }
    .summary-label {
        font-size: 11px;
        color: #6b7280;
        text-transform: uppercase;
    }
    .summary-value {
        font-size: 22px;
        font-weight: bold;
        margin-top: 5px;
    }
    .export-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        justify-content: flex-end;
    }
    .badge-critical {
        background: #fee2e2;
        color: #dc2626;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
    }
    .badge-warning {
        background: #fed7aa;
        color: #ea580c;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
    }
    .badge-normal {
        background: #d1fae5;
        color: #059669;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
    }
    .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin: 20px 0;
    }
    .chart-box {
        flex: 1;
        min-width: 300px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 15px;
    }
    .chart-box h4 {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 15px;
        text-align: center;
    }
    .progress-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin: 8px 0;
    }
    .progress-fill {
        height: 100%;
        border-radius: 4px;
    }
    .trend-line {
        display: flex;
        align-items: flex-end;
        height: 150px;
        gap: 8px;
        margin-top: 20px;
    }
    .trend-bar {
        flex: 1;
        background: #3b82f6;
        border-radius: 4px 4px 0 0;
        min-width: 30px;
        position: relative;
    }
    .trend-label {
        text-align: center;
        font-size: 9px;
        margin-top: 5px;
        transform: rotate(-45deg);
    }
    .department-pie {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }
    .pie-slice {
        display: flex;
        align-items: center;
        margin: 5px 10px;
    }
    .pie-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 5px;
    }
</style>

<div class="space-y-6">
    <!-- Report Type Selector -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="report-card {{ $reportType == 'dashboard' ? 'active' : '' }}" onclick="selectReport('dashboard')">
            <div class="report-icon">📊</div>
            <div class="text-sm font-semibold">Executive Dashboard</div>
        </div>
        <div class="report-card {{ $reportType == 'top_moving_items' ? 'active' : '' }}" onclick="selectReport('top_moving_items')">
            <div class="report-icon">🔥</div>
            <div class="text-sm font-semibold">Top Moving Items</div>
        </div>
        <div class="report-card {{ $reportType == 'low_stock' ? 'active' : '' }}" onclick="selectReport('low_stock')">
            <div class="report-icon">⚠️</div>
            <div class="text-sm font-semibold">Low Stock Alert</div>
        </div>
        <div class="report-card {{ $reportType == 'cogs_vs_sales' ? 'active' : '' }}" onclick="selectReport('cogs_vs_sales')">
            <div class="report-icon">💰</div>
            <div class="text-sm font-semibold">COGS vs Sales</div>
        </div>
        <div class="report-card {{ $reportType == 'performance' ? 'active' : '' }}" onclick="selectReport('performance')">
            <div class="report-icon">📈</div>
            <div class="text-sm font-semibold">Dept Performance</div>
        </div>
        <div class="report-card {{ $reportType == 'stock_trends' ? 'active' : '' }}" onclick="selectReport('stock_trends')">
            <div class="report-icon">📉</div>
            <div class="text-sm font-semibold">Stock Trends</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Date From</label>
                <input type="date" id="date_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ $dateFrom }}">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Date To</label>
                <input type="date" id="date_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ $dateTo }}">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Department</label>
                <select id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button onclick="loadReport()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
                    <i class="fas fa-search"></i> Load Report
                </button>
                <button onclick="exportPDF()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button onclick="exportExcel()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loading" class="text-center py-8 hidden">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
        <p class="mt-2 text-gray-500">Loading report data...</p>
    </div>

    <!-- Report Content -->
    <div id="reportContent" class="bg-white rounded-xl border border-gray-200 overflow-hidden p-4">
        <div class="text-center py-8 text-gray-400">
            <i class="fas fa-chart-line text-3xl mb-2 block"></i>
            <p>Select a report and click "Load Report"</p>
        </div>
    </div>
</div>

<script>
let currentReportType = '{{ $reportType }}';
let currentData = null;

function selectReport(type) {
    currentReportType = type;
    document.querySelectorAll('.report-card').forEach(card => {
        card.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    loadReport();
}

function loadReport() {
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    const departmentId = document.getElementById('department_id').value;

    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('reportContent').innerHTML = '';

    fetch(`/management/reports/data?report_type=${currentReportType}&date_from=${dateFrom}&date_to=${dateTo}&department_id=${departmentId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                currentData = result.data;
                renderReport(result.data);
            } else {
                showError(result.message || 'Failed to load report');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while loading the report');
        })
        .finally(() => {
            document.getElementById('loading').classList.add('hidden');
        });
}

function renderReport(data) {
    const container = document.getElementById('reportContent');

    switch (currentReportType) {
        case 'dashboard':
            renderDashboard(container, data);
            break;
        case 'top_moving_items':
            renderTopMovingItems(container, data);
            break;
        case 'low_stock':
            renderLowStock(container, data);
            break;
        case 'cogs_vs_sales':
            renderCogsVsSales(container, data);
            break;
        case 'performance':
            renderPerformance(container, data);
            break;
        case 'stock_trends':
            renderStockTrends(container, data);
            break;
        default:
            renderDashboard(container, data);
    }
}

function renderDashboard(container, data) {
    let html = `
        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-label">Total Sales</div>
                <div class="summary-value text-green-600">UGX ${(data.summary.total_sales || 0).toLocaleString()}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total COGS</div>
                <div class="summary-value text-red-600">UGX ${(data.summary.total_cogs || 0).toLocaleString()}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Profit</div>
                <div class="summary-value text-blue-600">UGX ${(data.summary.total_profit || 0).toLocaleString()}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Profit Margin</div>
                <div class="summary-value text-purple-600">${(data.summary.profit_margin || 0).toFixed(2)}%</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Stock Value</div>
                <div class="summary-value text-orange-600">UGX ${(data.summary.stock_value || 0).toLocaleString()}</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="chart-container">
            <div class="chart-box">
                <h4>COGS vs Sales vs Profit</h4>
                <div style="margin: 15px 0;">
                    <div class="text-sm">Sales: UGX ${(data.cogs_vs_sales.total_sales || 0).toLocaleString()}</div>
                    <div class="progress-bar"><div class="progress-fill bg-green-500" style="width: ${data.cogs_vs_sales.total_sales > 0 ? 100 : 0}%"></div></div>
                    <div class="text-sm mt-2">COGS: UGX ${(data.cogs_vs_sales.total_cogs || 0).toLocaleString()}</div>
                    <div class="progress-bar"><div class="progress-fill bg-red-500" style="width: ${data.cogs_vs_sales.total_sales > 0 ? (data.cogs_vs_sales.total_cogs / data.cogs_vs_sales.total_sales) * 100 : 0}%"></div></div>
                    <div class="text-sm mt-2">Profit: UGX ${(data.cogs_vs_sales.total_profit || 0).toLocaleString()}</div>
                    <div class="progress-bar"><div class="progress-fill bg-blue-500" style="width: ${data.cogs_vs_sales.total_sales > 0 ? (data.cogs_vs_sales.total_profit / data.cogs_vs_sales.total_sales) * 100 : 0}%"></div></div>
                </div>
            </div>
            <div class="chart-box">
                <h4>Department Performance (Profit)</h4>
                <div class="department-pie">
                    ${(data.performance_by_department || []).map(dept => `
                        <div class="pie-slice">
                            <div class="pie-color" style="background: ${dept.name === 'KITCHEN' ? '#10b981' : (dept.name === 'BAR' ? '#3b82f6' : '#f59e0b')}"></div>
                            <div class="pie-label">${dept.name}</div>
                            <div class="pie-percent">UGX ${dept.profit.toLocaleString()}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>

        <!-- Top Moving Items -->
        <h4 class="font-semibold text-gray-800 mt-4 mb-2">🔥 Top Moving Items</h4>
        <table class="data-table">
            <thead><tr><th>Item</th><th>UOM</th><th class="text-right">Quantity Used</th></tr></thead>
            <tbody>
                ${(data.top_moving_items || []).slice(0, 10).map(item => `
                    <tr><td>${item.name}</td><td class="text-center">${item.unit_of_measurement}</td><td class="text-right font-bold">${Number(item.total_used).toLocaleString()}</td></tr>
                `).join('')}
                ${(data.top_moving_items || []).length === 0 ? '<tr><td colspan="3" class="text-center text-gray-400">No data available</td></tr>' : ''}
            </tbody>
        </table>

        <!-- Low Stock Items -->
        <h4 class="font-semibold text-gray-800 mt-4 mb-2">⚠️ Low Stock Items</h4>
        <table class="data-table">
            <thead><tr><th>Item</th><th>Current Stock</th><th>Min Stock</th><th>Status</th></tr></thead>
            <tbody>
                ${(data.low_stock_items || []).slice(0, 10).map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td class="text-right ${item.current_stock <= 0 ? 'text-red-600 font-bold' : 'text-orange-600'}">${Number(item.current_stock).toLocaleString()}</td>
                        <td class="text-right">${Number(item.minimum_stock).toLocaleString()}</td>
                        <td><span class="${item.current_stock <= 0 ? 'badge-critical' : 'badge-warning'}">${item.current_stock <= 0 ? 'Out of Stock' : 'Low Stock'}</span></td>
                    </tr>
                `).join('')}
                ${(data.low_stock_items || []).length === 0 ? '<tr><td colspan="4" class="text-center text-green-600">All items at adequate levels</td></tr>' : ''}
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

function renderTopMovingItems(container, data) {
    let html = `
        <h4 class="font-semibold text-gray-800 mb-3">🔥 Top Moving Items (Most Used)</h4>
        <table class="data-table">
            <thead>
                <tr><th>Item Name</th><th>Item Code</th><th>UOM</th><th class="text-right">Quantity Used</th></tr>
            </thead>
            <tbody>
    `;

    (data.items || []).forEach(item => {
        html += `<tr><td class="font-semibold">${item.name}</td><td>${item.item_code || 'N/A'}</td><td class="text-center">${item.unit_of_measurement}</td><td class="text-right font-bold">${Number(item.total_used).toLocaleString()}</td></tr>`;
    });

    html += `</tbody></table>`;
    if ((data.items || []).length === 0) html = '<div class="text-center py-8 text-gray-400">No consumption data found for this period</div>';

    container.innerHTML = html;
}

function renderLowStock(container, data) {
    if ((data.items || []).length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-green-600"><i class="fas fa-check-circle text-3xl mb-2 block"></i><p>No low stock items found</p></div>';
        return;
    }

    let html = `<table class="data-table"><thead><tr><th>Item</th><th>Category</th><th>UOM</th><th class="text-right">Current Stock</th><th class="text-right">Min Stock</th><th>Status</th></tr></thead><tbody>`;

    data.items.forEach(item => {
        const status = item.current_stock <= 0 ? 'Out of Stock' : 'Low Stock';
        html += `<tr><td class="font-semibold">${item.name}</td><td>${item.category || 'N/A'}</td><td class="text-center">${item.unit_of_measurement}</td><td class="text-right text-red-600 font-bold">${Number(item.current_stock).toLocaleString()}</td><td class="text-right">${Number(item.minimum_stock).toLocaleString()}</td><td><span class="${item.current_stock <= 0 ? 'badge-critical' : 'badge-warning'}">${status}</span></td></tr>`;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;
}

function renderCogsVsSales(container, data) {
    let html = `
        <div class="summary-cards">
            <div class="summary-card"><div class="summary-label">Total Sales</div><div class="summary-value text-green-600">UGX ${(data.total_sales || 0).toLocaleString()}</div></div>
            <div class="summary-card"><div class="summary-label">Total COGS</div><div class="summary-value text-red-600">UGX ${(data.total_cogs || 0).toLocaleString()}</div></div>
            <div class="summary-card"><div class="summary-label">Total Profit</div><div class="summary-value text-blue-600">UGX ${(data.total_profit || 0).toLocaleString()}</div></div>
            <div class="summary-card"><div class="summary-label">Profit Margin</div><div class="summary-value text-purple-600">${(data.profit_margin || 0).toFixed(2)}%</div></div>
        </div>

        <div class="chart-container">
            <div class="chart-box">
                <h4>Sales vs COGS vs Profit</h4>
                <div style="margin: 20px 0;">
                    <div class="text-sm">Sales: UGX ${(data.total_sales || 0).toLocaleString()}</div>
                    <div class="progress-bar"><div class="progress-fill bg-green-500" style="width: 100%"></div></div>
                    <div class="text-sm mt-2">COGS: UGX ${(data.total_cogs || 0).toLocaleString()}</div>
                    <div class="progress-bar"><div class="progress-fill bg-red-500" style="width: ${data.total_sales > 0 ? (data.total_cogs / data.total_sales) * 100 : 0}%"></div></div>
                    <div class="text-sm mt-2">Profit: UGX ${(data.total_profit || 0).toLocaleString()}</div>
                    <div class="progress-bar"><div class="progress-fill bg-blue-500" style="width: ${data.total_sales > 0 ? (data.total_profit / data.total_sales) * 100 : 0}%"></div></div>
                </div>
            </div>
        </div>

        <h4 class="font-semibold text-gray-800 mt-3 mb-2">Weekly Trend</h4>
        <table class="data-table">
            <thead><tr><th>Week</th><th class="text-right">Sales (UGX)</th><th class="text-right">COGS (UGX)</th><th class="text-right">Profit (UGX)</th></tr></thead>
            <tbody>
                ${(data.weekly_trend || []).map(week => `<tr><td>${week.week}</td><td class="text-right">${week.sales.toLocaleString()}</td><td class="text-right">${week.cogs.toLocaleString()}</td><td class="text-right text-green-600">${week.profit.toLocaleString()}</td></tr>`).join('')}
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

function renderPerformance(container, data) {
    let html = `
        <div class="summary-cards">
            <div class="summary-card"><div class="summary-label">Total Sales</div><div class="summary-value text-green-600">UGX ${(data.total_sales || 0).toLocaleString()}</div></div>
            <div class="summary-card"><div class="summary-label">Total COGS</div><div class="summary-value text-red-600">UGX ${(data.total_cogs || 0).toLocaleString()}</div></div>
            <div class="summary-card"><div class="summary-label">Total Profit</div><div class="summary-value text-blue-600">UGX ${(data.total_profit || 0).toLocaleString()}</div></div>
        </div>

        <div class="chart-container">
            <div class="chart-box">
                <h4>Department Performance</h4>
                <div class="department-pie">
                    ${(data.departments || []).map(dept => `
                        <div class="pie-slice">
                            <div class="pie-color" style="background: ${dept.name === 'KITCHEN' ? '#10b981' : (dept.name === 'BAR' ? '#3b82f6' : '#f59e0b')}"></div>
                            <div class="pie-label">${dept.name}</div>
                            <div class="pie-percent">UGX ${dept.profit.toLocaleString()}</div>
                            <div class="pie-percent ml-2 text-xs text-gray-500">(${dept.margin.toFixed(1)}%)</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>

        <table class="data-table">
            <thead><tr><th>Department</th><th class="text-right">Sales (UGX)</th><th class="text-right">COGS (UGX)</th><th class="text-right">Profit (UGX)</th><th class="text-right">Margin</th><th>Reports</th></tr></thead>
            <tbody>
                ${(data.departments || []).map(dept => `
                    <tr>
                        <td class="font-semibold">${dept.name}</td>
                        <td class="text-right">${dept.sales.toLocaleString()}</td>
                        <td class="text-right">${dept.cogs.toLocaleString()}</td>
                        <td class="text-right text-green-600">${dept.profit.toLocaleString()}</td>
                        <td class="text-right">${dept.margin.toFixed(2)}%</td>
                        <td class="text-center">${dept.report_count}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

function renderStockTrends(container, data) {
    let html = `
        <div class="chart-container">
            <div class="chart-box">
                <h4>Stock IN vs Stock OUT (Last 30 Days)</h4>
                <div class="trend-line" style="height: auto; flex-wrap: wrap;">
                    ${(data.daily_trend || []).slice(-14).map(day => `
                        <div style="flex: 1; min-width: 40px; text-align: center;">
                            <div style="height: 60px; display: flex; flex-direction: column; gap: 2px;">
                                <div style="height: ${day.stock_in > 0 ? Math.min((day.stock_in / 1000), 60) : 2}px; background: #10b981; border-radius: 2px 2px 0 0;"></div>
                                <div style="height: ${day.stock_out > 0 ? Math.min((day.stock_out / 1000), 60) : 2}px; background: #ef4444; border-radius: 2px 2px 0 0;"></div>
                            </div>
                            <div class="trend-label" style="font-size: 8px; transform: none;">${day.date.substring(5)}</div>
                        </div>
                    `).join('')}
                </div>
                <div class="text-center text-xs mt-2"><span class="text-green-600">■ Stock IN</span> <span class="text-red-600 ml-2">■ Stock OUT</span></div>
            </div>
        </div>

        <h4 class="font-semibold text-gray-800 mt-4 mb-2">Monthly Trend</h4>
        <table class="data-table">
            <thead><tr><th>Month</th><th class="text-right">Stock IN</th><th class="text-right">Stock OUT</th><th class="text-right">Net Change</th></tr></thead>
            <tbody>
                ${(data.monthly_trend || []).map(month => `
                    <tr>
                        <td>${month.month}</td>
                        <td class="text-right text-green-600">${Number(month.stock_in).toLocaleString()}</td>
                        <td class="text-right text-red-600">${Number(month.stock_out).toLocaleString()}</td>
                        <td class="text-right ${(month.stock_in - month.stock_out) >= 0 ? 'text-green-600' : 'text-red-600'}">${(month.stock_in - month.stock_out).toLocaleString()}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

function showError(message) {
    const container = document.getElementById('reportContent');
    container.innerHTML = `<div class="text-center py-8 text-red-600"><i class="fas fa-exclamation-triangle text-3xl mb-2 block"></i><p>${message}</p></div>`;
}

function exportPDF() {
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    const departmentId = document.getElementById('department_id').value;
    window.location.href = `/management/reports/export-pdf?report_type=${currentReportType}&date_from=${dateFrom}&date_to=${dateTo}&department_id=${departmentId}`;
}

function exportExcel() {
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    const departmentId = document.getElementById('department_id').value;
    window.location.href = `/management/reports/export-excel?report_type=${currentReportType}&date_from=${dateFrom}&date_to=${dateTo}&department_id=${departmentId}`;
}

// Load default report on page load
document.addEventListener('DOMContentLoaded', function() {
    loadReport();
});
</script>
@endsection
