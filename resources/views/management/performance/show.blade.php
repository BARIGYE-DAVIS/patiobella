@extends('layouts.management')

@section('title', 'Performance Report Details')
@section('page-title', 'Performance Report Details')

@section('content')
<style>
    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    .summary-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 5px;
    }
    .summary-value {
        font-size: 22px;
        font-weight: bold;
    }
    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .report-table th, .report-table td {
        border: 1px solid #e5e7eb;
        padding: 8px 6px;
        vertical-align: middle;
    }
    .report-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-align: center;
    }
    .section-title {
        font-size: 14px;
        font-weight: bold;
        margin: 20px 0 10px 0;
        padding: 8px;
        background-color: #e5e7eb;
        border-radius: 6px;
    }
    .badge-completed {
        background-color: #d1fae5;
        color: #065f46;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }
    .chart-card {
        flex: 1;
        min-width: 250px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
    }
    .chart-card h4 {
        font-size: 14px;
        margin-bottom: 15px;
        color: #374151;
    }
    canvas {
        max-height: 200px;
        width: 100%;
    }
</style>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 py-4 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                {{ $report->report_number }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">
                {{ $report->department->name ?? 'N/A' }} | {{ date('d M Y', strtotime($report->report_date)) }}
                | Created by {{ $report->createdBy->first_name ?? 'N/A' }} {{ $report->createdBy->last_name ?? '' }}
                | Status: <span class="badge-completed">Completed</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('management.performance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('management.performance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
                <i class="fas fa-plus"></i> New Stock Take
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <div class="p-4">
        <!-- SUMMARY CARDS -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
            <div class="summary-card">
                <div class="summary-label">TOTAL SALES</div>
                <div class="summary-value text-emerald-600">{{ number_format($report->total_sales, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">TOTAL COGS</div>
                <div class="summary-value text-red-600">{{ number_format($report->total_cogs, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">TOTAL PROFIT</div>
                <div class="summary-value text-blue-600">{{ number_format($report->total_profit, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">PROFIT MARGIN</div>
                <div class="summary-value text-purple-600">{{ number_format($report->profit_margin, 1) }}%</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">AVERAGE PROFIT</div>
                <div class="summary-value text-orange-600">{{ number_format($report->total_profit / max($report->items->groupBy('menu_item_id')->count(), 1), 0) }} UGX</div>
            </div>
        </div>

        <!-- MENU ITEMS & INGREDIENTS SECTION with Totals -->
        @php
            $groupedItems = $report->items->groupBy('menu_item_id');
        @endphp

        <div class="section-title">MENU ITEMS & INGREDIENTS</div>
        <div class="overflow-x-auto">
            <table class="report-table">
                <thead>
                    <tr>
                        <th rowspan="2">MENU ITEM</th>
                        <th rowspan="2">QTY<br>SOLD</th>
                        <th rowspan="2">SELLING<br>PRICE</th>
                        <th colspan="4">INGREDIENTS</th>
                        <th rowspan="2">COGS</th>
                        <th rowspan="2">PROFIT<br>MARGIN</th>
                        <th rowspan="2">PROFIT/<br>MARK UP</th>
                    </tr>
                    <tr>
                        <th>NAME</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>COST</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedItems as $menuItemId => $items)
                        @php
                            $firstItem = $items->first();
                            $menuItem = $firstItem->menuItem;
                            $numIngredients = $items->count();
                            $totalCogs = $items->sum('cogs');
                            $totalRevenue = $firstItem->quantity_sold * $firstItem->selling_price;
                            $profit = $totalRevenue - $totalCogs;
                            $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;
                        @endphp

                        @foreach($items as $index => $item)
                            <tr>
                                @if($index == 0)
                                    <td rowspan="{{ $numIngredients }}" class="font-semibold">{{ $menuItem->name ?? 'N/A' }}</td>
                                    <td rowspan="{{ $numIngredients }}" class="text-center">{{ number_format($item->quantity_sold, 0) }}</td>
                                    <td rowspan="{{ $numIngredients }}" class="text-right">{{ number_format($item->selling_price, 0) }} UGX</td>
                                @endif
                                <td>{{ $item->inventoryItem->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ number_format($item->quantity_required, 3) }}</td>
                                <td class="text-center">{{ $item->inventoryItem->unit_of_measurement ?? 'piece' }}</td>
                                <td class="text-right">{{ number_format($item->unit_cost, 0) }} UGX</td>
                                @if($index == 0)
                                    <td rowspan="{{ $numIngredients }}" class="text-right text-red-600">{{ number_format($totalCogs, 0) }} UGX</td>
                                    <td rowspan="{{ $numIngredients }}" class="text-center">{{ number_format($profitMargin, 1) }}%</td>
                                    <td rowspan="{{ $numIngredients }}" class="text-right text-blue-600">{{ number_format($profit, 0) }} UGX</td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-semibold">
                    <tr>
                        <td colspan="6" class="text-right">TOTALS:</td>
                        <td class="text-right text-red-600">{{ number_format($report->total_cogs, 0) }} UGX</td>
                        <td class="text-center">{{ number_format($report->profit_margin, 1) }}%</td>
                        <td class="text-right text-blue-600">{{ number_format($report->total_profit, 0) }} UGX</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- GENERAL STOCK SECTION -->
        <div class="section-title">GENERAL STOCK</div>
        <div class="overflow-x-auto">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>ITEM NAME</th>
                        <th>UOM</th>
                        <th>OPENING</th>
                        <th>USED</th>
                        <th>CLOSING</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $stockSummary = [];
                        foreach($report->items as $item) {
                            $inventoryId = $item->inventory_item_id;
                            if (!isset($stockSummary[$inventoryId])) {
                                $stockSummary[$inventoryId] = [
                                    'name' => $item->inventoryItem->name ?? 'N/A',
                                    'uom' => $item->inventoryItem->unit_of_measurement ?? 'piece',
                                    'opening' => $item->opening_stock,
                                    'used' => 0,
                                    'closing' => 0,
                                ];
                            }
                            $stockSummary[$inventoryId]['used'] += $item->used_quantity;
                            $stockSummary[$inventoryId]['closing'] = $stockSummary[$inventoryId]['opening'] - $stockSummary[$inventoryId]['used'];
                        }
                    @endphp

                    @foreach($stockSummary as $stock)
                        <tr>
                            <td>{{ $stock['name'] }}</td>
                            <td class="text-center">{{ $stock['uom'] }}</td>
                            <td class="text-center">{{ number_format($stock['opening'], 2) }}</td>
                            <td class="text-center">{{ number_format($stock['used'], 2) }}</td>
                            <td class="text-center">{{ number_format($stock['closing'], 2) }}</td>
                        </tr>
                    @endforeach

                    @if(count($stockSummary) == 0)
                        <tr>
                            <td colspan="5" class="text-center text-gray-400 py-4">No stock items found</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot class="bg-gray-100 font-semibold">
                    <tr>
                        <td colspan="2" class="text-right">TOTALS:</td>
                        <td class="text-center">{{ number_format(collect($stockSummary)->sum('opening'), 2) }}</td>
                        <td class="text-center">{{ number_format(collect($stockSummary)->sum('used'), 2) }}</td>
                        <td class="text-center">{{ number_format(collect($stockSummary)->sum('closing'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- CHARTS SECTION -->
        <div class="section-title">CHARTS & TRENDS</div>
        <div class="chart-container">
            <div class="chart-card">
                <h4>Sales vs COGS vs Profit</h4>
                <canvas id="salesVsCogsChart" width="300" height="200"></canvas>
            </div>
            <div class="chart-card">
                <h4>Profit Margin by Menu Item</h4>
                <canvas id="marginByItemChart" width="300" height="200"></canvas>
            </div>
            <div class="chart-card">
                <h4>Top Selling Items</h4>
                <canvas id="topSellingChart" width="300" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart 1: Sales vs COGS vs Profit
    const salesVsCogsCtx = document.getElementById('salesVsCogsChart').getContext('2d');
    new Chart(salesVsCogsCtx, {
        type: 'bar',
        data: {
            labels: ['Financials'],
            datasets: [
                {
                    label: 'Total Sales',
                    data: [{{ $report->total_sales }}],
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1
                },
                {
                    label: 'Total COGS',
                    data: [{{ $report->total_cogs }}],
                    backgroundColor: '#ef4444',
                    borderColor: '#dc2626',
                    borderWidth: 1
                },
                {
                    label: 'Total Profit',
                    data: [{{ $report->total_profit }}],
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toLocaleString() + ' UGX';
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' UGX';
                        }
                    }
                }
            }
        }
    });

    // Chart 2: Profit Margin by Menu Item
    @php
        $marginLabels = [];
        $marginData = [];
        foreach($groupedItems as $menuItemId => $items) {
            $firstItem = $items->first();
            $totalRevenue = $firstItem->quantity_sold * $firstItem->selling_price;
            $totalCogs = $items->sum('cogs');
            $profitMargin = $totalRevenue > 0 ? (($totalRevenue - $totalCogs) / $totalRevenue) * 100 : 0;
            $marginLabels[] = addslashes($firstItem->menuItem->name ?? 'N/A');
            $marginData[] = round($profitMargin, 1);
        }
    @endphp

    const marginCtx = document.getElementById('marginByItemChart').getContext('2d');
    new Chart(marginCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($marginLabels) !!},
            datasets: [{
                data: {!! json_encode($marginData) !!},
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec489a', '#06b6d4', '#84cc16', '#f97316', '#6366f1'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'right', labels: { font: { size: 10 } } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });

    // Chart 3: Top Selling Items (Quantity Sold)
    @php
        $qtyLabels = [];
        $qtyData = [];
        foreach($groupedItems as $menuItemId => $items) {
            $firstItem = $items->first();
            $qtyLabels[] = addslashes($firstItem->menuItem->name ?? 'N/A');
            $qtyData[] = $firstItem->quantity_sold;
        }

        // Sort by quantity sold descending and take top 5
        array_multisort($qtyData, SORT_DESC, $qtyLabels);
        $qtyLabels = array_slice($qtyLabels, 0, 5);
        $qtyData = array_slice($qtyData, 0, 5);
    @endphp

    const topSellingCtx = document.getElementById('topSellingChart').getContext('2d');
    new Chart(topSellingCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($qtyLabels) !!},
            datasets: [{
                label: 'Quantity Sold',
                data: {!! json_encode($qtyData) !!},
                backgroundColor: '#f59e0b',
                borderColor: '#d97706',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw + ' units';
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

<style media="print">
    .no-print, .bg-gray-50, .flex.gap-2, button, .badge-completed, .chart-container {
        display: none !important;
    }
    body {
        padding: 0;
        margin: 0;
    }
    .bg-white {
        border: none;
        box-shadow: none;
    }
    .summary-card {
        border: 1px solid #ddd;
        page-break-inside: avoid;
    }
    .report-table {
        page-break-inside: avoid;
    }
}
</style>
@endsection
