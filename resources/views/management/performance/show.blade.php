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
    .gifts-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fbbf24;
    }
    .performance-card {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1px solid #3b82f6;
    }
    .top-item-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #22c55e;
    }
    .tooltip-value {
        font-weight: bold;
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
           

            <div class="flex gap-2">
    <a href="{{ route('management.performance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <!-- EXPORT BUTTONS -->
    <a href="{{ route('management.performance.export-pdf', $report->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
    <a href="{{ route('management.performance.export-excel', $report->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
        <i class="fas fa-file-excel"></i> Excel
    </a>

    <a href="{{ route('management.performance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
        <i class="fas fa-plus"></i> New Stock Take
    </a>
    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
        <i class="fas fa-print"></i> Print
    </button>
</div>
        </div>
    </div>

    <div class="p-4">
        <!-- ============================================ -->
        <!-- WITH GIFTS INCLUDED SECTION -->
        <!-- ============================================ -->
        <div class="performance-card rounded-lg p-4 mb-6">
            <h4 class="font-semibold text-blue-800 mb-3">
                <i class="fas fa-chart-line mr-2"></i> Sales (Gifts Included)
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Total Sales</div>
                    <div class="summary-value text-emerald-600">{{ number_format($report->total_sales, 0) }} UGX</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Cost of Goods Sold</div>
                    <div class="summary-value text-red-600">{{ number_format($report->total_cogs, 0) }} UGX</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Gifts</div>
                    <div class="summary-value text-purple-600">{{ number_format($report->gifts_amount ?? 0, 0) }} UGX</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Profit</div>
                    <div class="summary-value text-blue-600">{{ number_format($report->total_profit, 0) }} UGX</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Profit Margin</div>
                    <div class="summary-value text-emerald-600">{{ number_format($report->profit_margin, 2) }}%</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-3">
                <div class="bg-white rounded-lg p-2 text-center">
                    <div class="summary-label">COGS %</div>
                    <div class="summary-value text-red-600">{{ number_format($report->total_sales > 0 ? ($report->total_cogs / $report->total_sales) * 100 : 0, 2) }}%</div>
                </div>
                <div class="bg-white rounded-lg p-2 text-center">
                    <div class="summary-label">Average Profit per Item</div>
                    <div class="summary-value text-orange-600">{{ number_format($report->total_profit / max($report->items->groupBy('menu_item_id')->count(), 1), 0) }} UGX</div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- WITHOUT GIFTS (GIFTS EXCLUDED) SECTION -->
        <!-- ============================================ -->
        @php
            $salesWithoutGifts = $report->sales_without_gifts ?? ($report->total_sales - ($report->gifts_amount ?? 0));
            $profitWithoutGifts = $report->profit_without_gifts ?? ($salesWithoutGifts - $report->total_cogs);
            $marginWithoutGifts = $report->profit_margin_without_gifts ?? ($salesWithoutGifts > 0 ? ($profitWithoutGifts / $salesWithoutGifts) * 100 : 0);
            $cogsPercentageWithoutGifts = $salesWithoutGifts > 0 ? ($report->total_cogs / $salesWithoutGifts) * 100 : 0;
        @endphp

        <div class="gifts-card rounded-lg p-4 mb-6">
            <h4 class="font-semibold text-yellow-800 mb-3">
                <i class="fas fa-gift mr-2"></i> Performance Summary: Gifts Excluded
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Sales (Gifts Removed)</div>
                    <div class="summary-value text-green-600">{{ number_format($salesWithoutGifts, 0) }} UGX</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Cost of Goods Sold</div>
                    <div class="summary-value text-red-600">{{ number_format($report->total_cogs, 0) }} UGX</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Profit</div>
                    <div class="summary-value text-blue-600">{{ number_format($profitWithoutGifts, 0) }} UGX</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="summary-label">Profit Margin</div>
                    <div class="summary-value text-emerald-600">{{ number_format($marginWithoutGifts, 2) }}%</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-3">
                <div class="bg-white rounded-lg p-2 text-center">
                    <div class="summary-label">COGS %</div>
                    <div class="summary-value text-red-600">{{ number_format($cogsPercentageWithoutGifts, 2) }}%</div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- TOP MOVING STOCK ITEMS (MOST USED INGREDIENTS) -->
        <!-- ============================================ -->
        @php
            $ingredientUsage = [];
            foreach($report->items as $item) {
                $inventoryId = $item->inventory_item_id;
                $ingredientName = $item->inventoryItem->name ?? 'N/A';
                $uom = $item->inventoryItem->unit_of_measurement ?? 'piece';
                $usedQty = $item->used_quantity;

                if (!isset($ingredientUsage[$inventoryId])) {
                    $ingredientUsage[$inventoryId] = [
                        'name' => $ingredientName,
                        'uom' => $uom,
                        'used' => 0,
                        'cogs' => 0
                    ];
                }
                $ingredientUsage[$inventoryId]['used'] += $usedQty;
                $ingredientUsage[$inventoryId]['cogs'] += $item->cogs;
            }

            // Sort by used quantity (most used first)
            usort($ingredientUsage, function($a, $b) {
                return $b['used'] <=> $a['used'];
            });
            $topMovingItems = array_slice($ingredientUsage, 0, 10);
        @endphp

        <div class="top-item-card rounded-lg p-4 mb-6">
            <h4 class="font-semibold text-green-800 mb-3">
                <i class="fas fa-fire mr-2"></i> Top Moving Stock Items (Most Used)
            </h4>
            <div class="overflow-x-auto">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ITEM NAME</th>
                            <th>UOM</th>
                            <th>QUANTITY USED</th>
                            <th>COGS (UGX)</th>
                            <th>% OF TOTAL COGS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topMovingItems as $index => $item)
                        <tr>
                            <td class="text-center font-bold">{{ $index + 1 }}</td>
                            <td class="font-medium">{{ $item['name'] }}</td>
                            <td class="text-center">{{ $item['uom'] }}</td>
                            <td class="text-center font-semibold text-orange-600">{{ number_format($item['used'], 2) }}</td>
                            <td class="text-right text-red-600">{{ number_format($item['cogs'], 0) }} UGX</td>
                            <td class="text-center">{{ $report->total_cogs > 0 ? number_format(($item['cogs'] / $report->total_cogs) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-semibold">
                        <tr>
                            <td colspan="3" class="text-right">TOTALS:</td>
                            <td class="text-center">{{ number_format(collect($topMovingItems)->sum('used'), 2) }}</td>
                            <td class="text-right">{{ number_format(collect($topMovingItems)->sum('cogs'), 0) }} UGX</td>
                            <td class="text-center">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- MENU ITEMS SECTION -->
        <!-- ============================================ -->
        @php
            $groupedItems = $report->items->groupBy('menu_item_id');
        @endphp

        <div class="section-title">MENU ITEMS</div>
        <div class="overflow-x-auto">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>MENU ITEM</th>
                        <th>QTY SOLD</th>
                        <th>SELLING PRICE</th>
                        <th>COGS</th>
                        <th>PROFIT MARGIN</th>
                        <th>PROFIT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedItems as $menuItemId => $items)
                        @php
                            $firstItem = $items->first();
                            $menuItem = $firstItem->menuItem;
                            $totalCogs = $items->sum('cogs');
                            $totalRevenue = $firstItem->quantity_sold * $firstItem->selling_price;
                            $profit = $totalRevenue - $totalCogs;
                            $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="font-semibold">{{ $menuItem->name ?? 'N/A' }}</td>
                            <td class="text-center">{{ number_format($firstItem->quantity_sold, 0) }}</td>
                            <td class="text-right">{{ number_format($firstItem->selling_price, 0) }} UGX</td>
                            <td class="text-right text-red-600">{{ number_format($totalCogs, 0) }} UGX</td>
                            <td class="text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $profitMargin >= 50 ? 'bg-green-100 text-green-700' : ($profitMargin >= 30 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ number_format($profitMargin, 1) }}%
                                </span>
                            </td>
                            <td class="text-right text-blue-600">{{ number_format($profit, 0) }} UGX</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-semibold">
                    <tr>
                        <td colspan="3" class="text-right">TOTALS:</td>
                        <td class="text-right text-red-600">{{ number_format($report->total_cogs, 0) }} UGX</td>
                        <td class="text-center">{{ number_format($report->profit_margin, 1) }}%</td>
                        <td class="text-right text-blue-600">{{ number_format($report->total_profit, 0) }} UGX</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ============================================ -->
        <!-- GENERAL STOCK SECTION -->
        <!-- ============================================ -->
        <div class="section-title">GENERAL STOCK</div>
        <div class="overflow-x-auto">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>ITEM NAME</th>
                        <th>UOM</th>
                        <th>OPENING</th>
                        <th>ADDED</th>
                        <th>USED</th>
                        <th>CLOSING</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $stockSummary = [];
                        foreach($report->items as $item) {
                            $inventoryId = $item->inventory_item_id;
                            $uom = $item->inventoryItem->unit_of_measurement ?? 'piece';
                            if (!isset($stockSummary[$inventoryId])) {
                                $stockSummary[$inventoryId] = [
                                    'name' => $item->inventoryItem->name ?? 'N/A',
                                    'uom' => $uom,
                                    'opening' => $item->opening_stock,
                                    'added' => $item->added_stock ?? 0,
                                    'used' => 0,
                                    'closing' => $item->closing_stock,
                                ];
                            }
                            $stockSummary[$inventoryId]['used'] += $item->used_quantity;
                        }
                    @endphp

                    @foreach($stockSummary as $stock)
                        <tr>
                            <td class="font-medium">{{ $stock['name'] }}</td>
                            <td class="text-center">{{ $stock['uom'] }}</td>
                            <td class="text-center">{{ number_format($stock['opening'], 2) }}</td>
                            <td class="text-center">{{ number_format($stock['added'], 2) }}</td>
                            <td class="text-center text-orange-600 font-semibold">{{ number_format($stock['used'], 2) }}</td>
                            <td class="text-center">{{ number_format($stock['closing'], 2) }}</td>
                        </tr>
                    @endforeach

                    @if(count($stockSummary) == 0)
                        <tr>
                            <td colspan="6" class="text-center text-gray-400 py-4">No stock items found</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot class="bg-gray-100 font-semibold">
                    <tr>
                        <td colspan="2" class="text-right">TOTALS:</td>
                        <td class="text-center">{{ number_format(collect($stockSummary)->sum('opening'), 2) }}</td>
                        <td class="text-center">{{ number_format(collect($stockSummary)->sum('added'), 2) }}</td>
                        <td class="text-center text-orange-600">{{ number_format(collect($stockSummary)->sum('used'), 2) }}</td>
                        <td class="text-center">{{ number_format(collect($stockSummary)->sum('closing'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ============================================ -->
        <!-- CHARTS & TRENDS SECTION -->
        <!-- ============================================ -->
        <div class="section-title">CHARTS & TRENDS</div>
        <div class="chart-container">
            <div class="chart-card">
                <h4>Sales vs COGS vs Profit (With Gifts)</h4>
                <canvas id="salesVsCogsChart" width="300" height="200"></canvas>
            </div>
            <div class="chart-card">
                <h4>Sales vs COGS vs Profit (Without Gifts)</h4>
                <canvas id="salesVsCogsWithoutGiftsChart" width="300" height="200"></canvas>
            </div>
            <div class="chart-card">
                <h4>Profit Margin by Menu Item</h4>
                <canvas id="marginByItemChart" width="300" height="200"></canvas>
            </div>
            <div class="chart-card">
                <h4>Top Selling Items (Quantity)</h4>
                <canvas id="topSellingChart" width="300" height="200"></canvas>
            </div>
        </div>

        <div class="chart-container" style="margin-top: 10px;">
            <div class="chart-card">
                <h4>Ingredient Usage Trend (Top 6 Ingredients)</h4>
                <canvas id="ingredientTrendChart" width="300" height="200"></canvas>
            </div>
            <div class="chart-card">
                <h4>Top Moving Stock Items (Most Used)</h4>
                <canvas id="topMovingChart" width="300" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart 1: Sales vs COGS vs Profit (With Gifts)
    const salesVsCogsCtx = document.getElementById('salesVsCogsChart').getContext('2d');
    new Chart(salesVsCogsCtx, {
        type: 'bar',
        data: {
            labels: ['With Gifts'],
            datasets: [
                { label: 'Total Sales', data: [{{ $report->total_sales }}], backgroundColor: '#10b981', borderColor: '#059669', borderWidth: 1 },
                { label: 'Total COGS', data: [{{ $report->total_cogs }}], backgroundColor: '#ef4444', borderColor: '#dc2626', borderWidth: 1 },
                { label: 'Total Profit', data: [{{ $report->total_profit }}], backgroundColor: '#3b82f6', borderColor: '#2563eb', borderWidth: 1 }
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
                            let amount = context.raw;
                            let percentage = '';
                            if (context.dataset.label === 'Total Profit' && {{ $report->total_sales }} > 0) {
                                percentage = ' (' + ((amount / {{ $report->total_sales }}) * 100).toFixed(1) + '% of sales)';
                            }
                            if (context.dataset.label === 'Total COGS' && {{ $report->total_sales }} > 0) {
                                percentage = ' (' + ((amount / {{ $report->total_sales }}) * 100).toFixed(1) + '% of sales)';
                            }
                            return context.dataset.label + ': ' + amount.toLocaleString() + ' UGX' + percentage;
                        }
                    }
                }
            },
            scales: { y: { ticks: { callback: function(value) { return value.toLocaleString() + ' UGX'; } } } }
        }
    });

    // Chart 2: Sales vs COGS vs Profit (Without Gifts)
    @php
        $salesWithoutGiftsChart = $report->sales_without_gifts ?? ($report->total_sales - ($report->gifts_amount ?? 0));
        $profitWithoutGiftsChart = $report->profit_without_gifts ?? ($salesWithoutGiftsChart - $report->total_cogs);
    @endphp

    const salesWithoutGiftsCtx = document.getElementById('salesVsCogsWithoutGiftsChart').getContext('2d');
    new Chart(salesWithoutGiftsCtx, {
        type: 'bar',
        data: {
            labels: ['Without Gifts'],
            datasets: [
                { label: 'Total Sales', data: [{{ $salesWithoutGiftsChart }}], backgroundColor: '#10b981', borderColor: '#059669', borderWidth: 1 },
                { label: 'Total COGS', data: [{{ $report->total_cogs }}], backgroundColor: '#ef4444', borderColor: '#dc2626', borderWidth: 1 },
                { label: 'Total Profit', data: [{{ $profitWithoutGiftsChart }}], backgroundColor: '#3b82f6', borderColor: '#2563eb', borderWidth: 1 }
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
                            let amount = context.raw;
                            let percentage = '';
                            if (context.dataset.label === 'Total Profit' && {{ $salesWithoutGiftsChart }} > 0) {
                                percentage = ' (' + ((amount / {{ $salesWithoutGiftsChart }}) * 100).toFixed(1) + '% of sales)';
                            }
                            if (context.dataset.label === 'Total COGS' && {{ $salesWithoutGiftsChart }} > 0) {
                                percentage = ' (' + ((amount / {{ $salesWithoutGiftsChart }}) * 100).toFixed(1) + '% of sales)';
                            }
                            return context.dataset.label + ': ' + amount.toLocaleString() + ' UGX' + percentage;
                        }
                    }
                }
            },
            scales: { y: { ticks: { callback: function(value) { return value.toLocaleString() + ' UGX'; } } } }
        }
    });

    // Chart 3: Profit Margin by Menu Item (Pie chart with tooltips showing both % and amount)
    @php
        $marginLabels = [];
        $marginData = [];
        $marginAmounts = [];
        foreach($groupedItems as $menuItemId => $items) {
            $firstItem = $items->first();
            $totalRevenue = $firstItem->quantity_sold * $firstItem->selling_price;
            $totalCogs = $items->sum('cogs');
            $profit = $totalRevenue - $totalCogs;
            $profitMargin = $totalRevenue > 0 ? (($totalRevenue - $totalCogs) / $totalRevenue) * 100 : 0;
            $marginLabels[] = addslashes($firstItem->menuItem->name ?? 'N/A');
            $marginData[] = round($profitMargin, 1);
            $marginAmounts[] = $profit;
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
                            let label = context.label || '';
                            let percentage = context.raw;
                            let profitAmount = {!! json_encode($marginAmounts) !!}[context.dataIndex];
                            return label + ': ' + percentage + '% margin | Profit: ' + profitAmount.toLocaleString() + ' UGX';
                        }
                    }
                }
            }
        }
    });

    // Chart 4: Top Selling Items (Quantity) - Bar chart
    @php
        $qtyLabels = [];
        $qtyData = [];
        foreach($groupedItems as $menuItemId => $items) {
            $firstItem = $items->first();
            $qtyLabels[] = addslashes($firstItem->menuItem->name ?? 'N/A');
            $qtyData[] = $firstItem->quantity_sold;
        }
        array_multisort($qtyData, SORT_DESC, $qtyLabels);
        $qtyLabels = array_slice($qtyLabels, 0, 8);
        $qtyData = array_slice($qtyData, 0, 8);
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
            scales: { y: { ticks: { stepSize: 1 } } }
        }
    });

    // Chart 5: Ingredient Usage Trend with correct UOM
    @php
        $ingredientUsageTrend = [];
        $ingredientIds = [];
        foreach($report->items as $item) {
            $ingredientId = $item->inventory_item_id;
            $ingredientName = $item->inventoryItem->name ?? 'N/A';
            $uom = $item->inventoryItem->unit_of_measurement ?? 'piece';
            $ingredientIds[$ingredientId] = ['name' => $ingredientName, 'uom' => $uom];
            if (!isset($ingredientUsageTrend[$ingredientId])) {
                $ingredientUsageTrend[$ingredientId] = 0;
            }
            $ingredientUsageTrend[$ingredientId] += $item->used_quantity;
        }
        arsort($ingredientUsageTrend);
        $topIngredientsTrend = array_slice($ingredientUsageTrend, 0, 6, true);

        $previousReports = \App\Models\PerformanceReport::where('department_id', $report->department_id)
            ->where('id', '<', $report->id)
            ->orderBy('report_date', 'desc')
            ->limit(5)
            ->get()
            ->reverse();

        $trendDates = [];
        $trendData = [];
        foreach($topIngredientsTrend as $ingredientId => $usage) {
            $trendData[$ingredientId] = [];
        }

        $allReports = clone $previousReports;
        $allReports->push($report);

        foreach($allReports as $rep) {
            $trendDates[] = date('d M', strtotime($rep->report_date));
            foreach($topIngredientsTrend as $ingredientId => $usage) {
                $totalUsage = 0;
                foreach($rep->items as $item) {
                    if($item->inventory_item_id == $ingredientId) {
                        $totalUsage += $item->used_quantity;
                    }
                }
                $trendData[$ingredientId][] = round($totalUsage, 2);
            }
        }

        $colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec489a'];
        $ingredientNamesWithUom = [];
        foreach($topIngredientsTrend as $ingredientId => $usage) {
            $ingredientNamesWithUom[$ingredientId] = $ingredientIds[$ingredientId]['name'] . ' (' . $ingredientIds[$ingredientId]['uom'] . ')';
        }
    @endphp

    const ingredientTrendCtx = document.getElementById('ingredientTrendChart').getContext('2d');
    new Chart(ingredientTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($trendDates) !!},
            datasets: [
                @foreach($topIngredientsTrend as $ingredientId => $usage)
                {
                    label: '{{ addslashes($ingredientNamesWithUom[$ingredientId]) }}',
                    data: {!! json_encode($trendData[$ingredientId]) !!},
                    borderColor: '{{ $colors[$loop->index % count($colors)] }}',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                @endforeach
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10 } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toFixed(2) + ' units';
                        }
                    }
                }
            },
            scales: {
                y: { title: { display: true, text: 'Quantity Used', font: { size: 10 } }, ticks: { font: { size: 10 } } },
                x: { title: { display: true, text: 'Report Date', font: { size: 10 } }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // Chart 6: Top Moving Stock Items (Bar chart)
    @php
        $topMovingNames = [];
        $topMovingQuantities = [];
        foreach($topMovingItems as $item) {
            $topMovingNames[] = addslashes($item['name']) . ' (' . $item['uom'] . ')';
            $topMovingQuantities[] = round($item['used'], 2);
        }
        $topMovingNames = array_slice($topMovingNames, 0, 8);
        $topMovingQuantities = array_slice($topMovingQuantities, 0, 8);
    @endphp

    const topMovingCtx = document.getElementById('topMovingChart').getContext('2d');
    new Chart(topMovingCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topMovingNames) !!},
            datasets: [{
                label: 'Quantity Used',
                data: {!! json_encode($topMovingQuantities) !!},
                backgroundColor: '#ec489a',
                borderColor: '#be185d',
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
                            return context.dataset.label + ': ' + context.raw.toFixed(2) + ' units';
                        }
                    }
                }
            },
            scales: {
                y: { title: { display: true, text: 'Quantity Used', font: { size: 10 } } },
                x: { ticks: { font: { size: 9 } } }
            }
        }
    });
});
</script>

<style media="print">
    .no-print, .bg-gray-50, .flex.gap-2, button, .badge-completed, .chart-container {
        display: none !important;
    }
    body { padding: 0; margin: 0; }
    .bg-white { border: none; box-shadow: none; }
    .summary-card { border: 1px solid #ddd; page-break-inside: avoid; }
    .report-table { page-break-inside: avoid; }
}
</style>
@endsection
