<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Movements Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #1f2937;
            padding: 15px 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #1e40af;
        }
        .header h1 { font-size: 16px; color: #1e40af; margin-bottom: 3px; }
        .header .sub { font-size: 8px; color: #6b7280; }

        /* Section Titles */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            background: #1e40af;
            color: white;
            padding: 5px 10px;
            margin: 15px 0 10px 0;
            border-radius: 3px;
        }

        /* Statistics Cards */
        .stat-grid {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .stat-card {
            flex: 1;
            padding: 8px;
            border-radius: 6px;
            border-left: 3px solid;
            background: #f8fafc;
        }
        .stat-card .label { font-size: 7px; text-transform: uppercase; color: #6b7280; }
        .stat-card .value { font-size: 14px; font-weight: bold; margin-top: 3px; }
        .stat-card .sub { font-size: 6px; color: #9ca3af; margin-top: 2px; }
        .card-in { border-left-color: #10b981; }
        .card-out { border-left-color: #ef4444; }
        .card-net { border-left-color: #3b82f6; }
        .card-val { border-left-color: #8b5cf6; }
        .text-green { color: #065f46; }
        .text-red { color: #991b1b; }
        .text-blue { color: #1d4ed8; }

        /* Two Column Layout */
        .two-col {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .col {
            flex: 1;
        }

        /* Bar Chart */
        .chart-container {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }
        .chart-title {
            font-size: 9px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            text-align: center;
        }
        .chart-item {
            margin-bottom: 10px;
        }
        .chart-label {
            font-size: 7px;
            font-weight: 500;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }
        .chart-bar-bg {
            background: #e5e7eb;
            border-radius: 4px;
            height: 14px;
            overflow: hidden;
        }
        .chart-bar-fill {
            display: block;
            height: 100%;
            border-radius: 4px;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 7px;
        }
        .data-table th {
            background: #f1f5f9;
            padding: 6px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #e2e8f0;
            font-size: 6.5px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 5px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) { background: #f9fafb; }
        .data-table tfoot td {
            background: #e2e8f0;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Badges */
        .badge-in {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 6px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-out {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 6px;
            font-weight: bold;
            display: inline-block;
        }

        /* Trend Bars in Table */
        .trend-bar {
            background: #e5e7eb;
            border-radius: 2px;
            height: 6px;
            width: 100%;
        }
        .trend-fill-in {
            display: block;
            height: 100%;
            background: #10b981;
            border-radius: 2px;
        }
        .trend-fill-out {
            display: block;
            height: 100%;
            background: #ef4444;
            border-radius: 2px;
        }

        /* Pie Chart CSS (conic-gradient) */
        .pie-wrapper {
            text-align: center;
            margin-bottom: 15px;
        }
        .pie-chart {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            margin: 0 auto 10px;
        }
        .pie-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 6.5px;
        }
        .legend-color {
            width: 10px;
            height: 10px;
            border-radius: 2px;
        }

        /* Mini box for small charts */
        .inline-stats {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }
        .stat-small {
            flex: 1;
            text-align: center;
            padding: 5px;
            background: #f1f5f9;
            border-radius: 4px;
        }

        .page-break {
            page-break-before: always;
        }

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 6px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <h1>STOCK MOVEMENTS & DISTRIBUTION REPORT</h1>
    <div class="sub">Generated: {{ $export_date }} | Total Movements: {{ $total_movements }} | Total Value: UGX {{ number_format($chartData['totalValue'], 2) }}</div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 1: SUMMARY STATISTICS CARDS --}}
{{-- ============================================================ --}}
<div class="stat-grid">
    <div class="stat-card card-in">
        <div class="label">TOTAL STOCK IN</div>
        <div class="value text-green">{{ number_format($chartData['totalStockIn'], 2) }}</div>
        <div class="sub">units | Purchases + GRN + Returns</div>
    </div>
    <div class="stat-card card-out">
        <div class="label">TOTAL STOCK OUT</div>
        <div class="value text-red">{{ number_format($chartData['totalStockOut'], 2) }}</div>
        <div class="sub">units | Issues + Manual Out</div>
    </div>
    <div class="stat-card card-net">
        <div class="label">NET CHANGE</div>
        <div class="value text-blue">{{ $chartData['netChange'] >= 0 ? '+' : '' }}{{ number_format($chartData['netChange'], 2) }}</div>
        <div class="sub">units | IN minus OUT</div>
    </div>
    <div class="stat-card card-val">
        <div class="label">TOTAL VALUE MOVED</div>
        <div class="value">UGX {{ number_format($chartData['totalValue'], 2) }}</div>
        <div class="sub">All transactions combined</div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 2: STOCK FLOW & DEPARTMENT CONSUMPTION (Side by Side) --}}
{{-- ============================================================ --}}
<div class="section-title">📊 STOCK FLOW & DEPARTMENT CONSUMPTION</div>

@php
    $totalFlow = $chartData['totalStockIn'] + $chartData['totalStockOut'];
    $inPct = $totalFlow > 0 ? round(($chartData['totalStockIn'] / $totalFlow) * 100, 1) : 0;
    $outPct = $totalFlow > 0 ? round(($chartData['totalStockOut'] / $totalFlow) * 100, 1) : 0;
    $deptColors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#14b8a6', '#ef4444'];
    $deptMax = !empty($chartData['deptConsumption']['data']) ? max($chartData['deptConsumption']['data']) : 1;
@endphp

<div class="two-col">
    {{-- LEFT: IN vs OUT Bar Chart --}}
    <div class="col">
        <div class="chart-container">
            <div class="chart-title">Stock IN vs Stock OUT</div>
            <div class="chart-item">
                <div class="chart-label">
                    <span>📥 Stock IN</span>
                    <span>{{ number_format($chartData['totalStockIn'], 0) }} ({{ $inPct }}%)</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" style="width: {{ $inPct }}%; background: #10b981;"></div>
                </div>
            </div>
            <div class="chart-item">
                <div class="chart-label">
                    <span>📤 Stock OUT</span>
                    <span>{{ number_format($chartData['totalStockOut'], 0) }} ({{ $outPct }}%)</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" style="width: {{ $outPct }}%; background: #ef4444;"></div>
                </div>
            </div>
            <div class="inline-stats">
                <div class="stat-small"><strong>IN</strong><br>{{ number_format($chartData['totalStockIn'], 0) }}</div>
                <div class="stat-small"><strong>OUT</strong><br>{{ number_format($chartData['totalStockOut'], 0) }}</div>
                <div class="stat-small"><strong>NET</strong><br>{{ $chartData['netChange'] >= 0 ? '+' : '' }}{{ number_format($chartData['netChange'], 0) }}</div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Department Consumption Bar Chart --}}
    <div class="col">
        <div class="chart-container">
            <div class="chart-title">Department Consumption</div>
            @foreach($chartData['deptConsumption']['labels'] as $idx => $label)
            @php
                $val = $chartData['deptConsumption']['data'][$idx] ?? 0;
                $barPct = $deptMax > 0 ? round(($val / $deptMax) * 100, 1) : 0;
                $color = $deptColors[$idx % count($deptColors)];
            @endphp
            <div class="chart-item">
                <div class="chart-label">
                    <span>{{ $label }}</span>
                    <span>{{ number_format($val, 0) }} units</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" style="width: {{ $barPct }}%; background: {{ $color }};"></div>
                </div>
            </div>
            @endforeach
            @if(empty($chartData['deptConsumption']['labels']))
            <div class="chart-item">No department consumption data available</div>
            @endif
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 3: WEEKLY TREND --}}
{{-- ============================================================ --}}
<div class="section-title">📅 WEEKLY STOCK MOVEMENT TREND (Last 7 Days)</div>

@php
    $wLabels = $chartData['weeklyTrend']['labels'] ?? [];
    $wIn = $chartData['weeklyTrend']['in'] ?? [];
    $wOut = $chartData['weeklyTrend']['out'] ?? [];
    $wMax = !empty($wIn) ? max(array_merge($wIn, $wOut)) : 1;
@endphp

<table class="data-table">
    <thead>
        <tr>
            <th style="width:15%">Date</th>
            <th style="width:35%">Stock IN</th>
            <th style="width:15%" class="text-right">Units IN</th>
            <th style="width:35%">Stock OUT</th>
            <th style="width:15%" class="text-right">Units OUT</th>
            <th style="width:10%" class="text-right">Net</th>
        </tr>
    </thead>
    <tbody>
        @foreach($wLabels as $idx => $label)
        @php
            $inVal = $wIn[$idx] ?? 0;
            $outVal = $wOut[$idx] ?? 0;
            $inPctBar = $wMax > 0 ? round(($inVal / $wMax) * 100, 1) : 0;
            $outPctBar = $wMax > 0 ? round(($outVal / $wMax) * 100, 1) : 0;
            $net = $inVal - $outVal;
        @endphp
        <tr>
            <td>{{ $label }}</td>
            <td>
                <div class="trend-bar">
                    <div class="trend-fill-in" style="width: {{ $inPctBar }}%;"></div>
                </div>
            </td>
            <td class="text-right">{{ number_format($inVal, 2) }}</td>
            <td>
                <div class="trend-bar">
                    <div class="trend-fill-out" style="width: {{ $outPctBar }}%;"></div>
                </div>
            </td>
            <td class="text-right">{{ number_format($outVal, 2) }}</td>
            <td class="text-right {{ $net >= 0 ? 'text-green' : 'text-red' }}">
                {{ $net >= 0 ? '+' : '' }}{{ number_format($net, 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ============================================================ --}}
{{-- SECTION 4: TOP 5 ITEMS & STOCK IN SOURCE (Side by Side) --}}
{{-- ============================================================ --}}
<div class="section-title">🏆 TOP 5 MOVING ITEMS & STOCK IN SOURCE</div>

@php
    $topColors = ['#f59e0b', '#ef4444', '#10b981', '#3b82f6', '#8b5cf6'];
    $topMax = !empty($chartData['topItems']) ? max(array_column($chartData['topItems'], 'total')) : 1;
    $sourceColors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b'];
    $sourceTotal = array_sum(array_column($chartData['stockInBySource'], 'total'));
@endphp

<div class="two-col">
    {{-- LEFT: Top 5 Moving Items --}}
    <div class="col">
        <div class="chart-container">
            <div class="chart-title">Top 5 Moving Items</div>
            @foreach($chartData['topItems'] as $idx => $item)
            @php
                $barPct = $topMax > 0 ? round(($item['total'] / $topMax) * 100, 1) : 0;
                $color = $topColors[$idx % count($topColors)];
            @endphp
            <div class="chart-item">
                <div class="chart-label">
                    <span>#{{ $idx+1 }} {{ \Illuminate\Support\Str::limit($item['name'], 20) }}</span>
                    <span>{{ number_format($item['total'], 0) }} units</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" style="width: {{ $barPct }}%; background: {{ $color }};"></div>
                </div>
            </div>
            @endforeach
            @if(empty($chartData['topItems']))
            <div class="chart-item">No data available</div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Stock IN Source Breakdown --}}
    <div class="col">
        <div class="chart-container">
            <div class="chart-title">Stock IN Source Breakdown</div>

            {{-- Pie Chart using CSS Conic Gradient --}}
            @php
                $pieColors = [];
                $pieStops = [];
                $cumulative = 0;
                foreach($chartData['stockInBySource'] as $idx => $src) {
                    $pct = $sourceTotal > 0 ? ($src['total'] / $sourceTotal) * 100 : 0;
                    $color = $sourceColors[$idx % count($sourceColors)];
                    $pieColors[] = $color;
                    $pieStops[] = $color . ' ' . $cumulative . '% ' . ($cumulative + $pct) . '%';
                    $cumulative += $pct;
                }
                $conicGradient = !empty($pieStops) ? 'conic-gradient(' . implode(', ', $pieStops) . ')' : '#e5e7eb';
            @endphp

            <div class="pie-wrapper">
                <div class="pie-chart" style="background: {{ $conicGradient }};"></div>
                <div class="pie-legend">
                    @foreach($chartData['stockInBySource'] as $idx => $src)
                    @php $pct = $sourceTotal > 0 ? round(($src['total'] / $sourceTotal) * 100, 1) : 0; @endphp
                    <div class="legend-item">
                        <div class="legend-color" style="background: {{ $sourceColors[$idx % count($sourceColors)] }};"></div>
                        <span>{{ $src['name'] }} ({{ $pct }}%)</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <table class="data-table">
                <thead><tr><th>Source</th><th class="text-right">Units</th><th class="text-right">% of IN</th></tr></thead>
                <tbody>
                    @foreach($chartData['stockInBySource'] as $src)
                    @php $pct = $chartData['totalStockIn'] > 0 ? round(($src['total'] / $chartData['totalStockIn']) * 100, 1) : 0; @endphp
                    <tr>
                        <td>{{ $src['name'] }}</td>
                        <td class="text-right">{{ number_format($src['total'], 2) }}</td>
                        <td class="text-right">{{ $pct }}%</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL IN</strong></td>
                        <td class="text-right"><strong>{{ number_format($chartData['totalStockIn'], 2) }}</strong></td>
                        <td class="text-right"><strong>100%</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 5: DEPARTMENT CONSUMPTION DETAIL TABLE --}}
{{-- ============================================================ --}}
<div class="section-title">🏢 DEPARTMENT CONSUMPTION DETAIL</div>

@php
    $totalConsumed = $chartData['departmentSummaries']->sum('total_consumed');
    $maxConsumed = $totalConsumed > 0 ? $totalConsumed : 1;
@endphp

<table class="data-table">
    <thead>
        <tr>
            <th>Department</th>
            <th class="text-right">Issued (units)</th>
            <th class="text-right">Returned (units)</th>
            <th class="text-right">Consumed (units)</th>
            <th class="text-right">Consumption %</th>
            <th style="width:20%">Visual</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chartData['departmentSummaries'] as $idx => $dept)
        @php
            $pct = $totalConsumed > 0 ? round(($dept->total_consumed / $totalConsumed) * 100, 1) : 0;
            $color = $deptColors[$idx % count($deptColors)];
        @endphp
        <tr>
            <td><strong>{{ $dept->department_name }}</strong></td>
            <td class="text-right">{{ number_format($dept->total_issued, 2) }}</td>
            <td class="text-right">{{ number_format($dept->total_returned, 2) }}</td>
            <td class="text-right">{{ number_format($dept->total_consumed, 2) }}</td>
            <td class="text-right">{{ $pct }}%</td>
            <td>
                <div class="trend-bar">
                    <div class="trend-fill-in" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                </div>
            </td>
        </tr>
        @endforeach
        @if($chartData['departmentSummaries']->isEmpty())
        <tr><td colspan="6" class="text-center">No department data available</td></tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="text-right"><strong>{{ number_format($chartData['departmentSummaries']->sum('total_issued'), 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($chartData['departmentSummaries']->sum('total_returned'), 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalConsumed, 2) }}</strong></td>
            <td class="text-right"><strong>100%</strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>

{{-- ============================================================ --}}
{{-- PAGE BREAK - DETAILED TABLES --}}
{{-- ============================================================ --}}
<div class="page-break"></div>

{{-- HEADER FOR SECOND PAGE --}}
<div class="header" style="border-bottom: 1px solid #e5e7eb;">
    <h1 style="font-size: 12px;">DETAILED TRANSACTIONS</h1>
    <div class="sub">{{ $export_date }}</div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 6: DISTRIBUTION DETAIL --}}
{{-- ============================================================ --}}
<div class="section-title">📦 DEPARTMENT DISTRIBUTION (ISSUES TO DEPARTMENTS)</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Requisition #</th>
            <th>Department</th>
            <th>Item</th>
            <th class="text-right">Qty</th>
            <th>Pack Type</th>
            <th class="text-right">Pack Size</th>
            <th class="text-right">Total Pieces</th>
            <th>Taken By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($chartData['distributions'] as $dist)
        <tr>
            <td>{{ $dist->created_at ? $dist->created_at->format('Y-m-d') : 'N/A' }}</td>
            <td>{{ $dist->departmentRequisition->requisition_number ?? 'N/A' }}</td>
            <td><span class="badge-out">{{ $dist->departmentRequisition->department->name ?? 'N/A' }}</span></td>
            <td>{{ $dist->inventoryItem->name ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($dist->quantity_issued, 2) }}</td>
            <td>{{ $dist->issued_pack_type ?? '-' }}</td>
            <td class="text-right">{{ number_format($dist->issued_pack_size ?? 0) }}</td>
            <td class="text-right"><strong>{{ number_format($dist->issued_total_pieces ?? 0, 2) }}</strong></td>
            <td>{{ $dist->departmentRequisition->taken_by ?? 'N/A' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center">No distribution records found</td></tr>
        @endforelse
    </tbody>
</table>

{{-- ============================================================ --}}
{{-- SECTION 7: RETURNS DETAIL --}}
{{-- ============================================================ --}}
<div class="section-title">🔄 RETURNS FROM DEPARTMENTS</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Requisition #</th>
            <th>Department</th>
            <th>Item</th>
            <th class="text-right">Qty Returned</th>
            <th>Reason</th>
            <th>Returned By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($chartData['returns'] as $ret)
        @php $retDate = $ret->returned_at ? $ret->returned_at->format('Y-m-d') : ($ret->created_at ? $ret->created_at->format('Y-m-d') : 'N/A'); @endphp
        <tr>
            <td>{{ $retDate }}</td>
            <td>{{ $ret->departmentRequisition->requisition_number ?? 'N/A' }}</td>
            <td><span class="badge-in">{{ $ret->departmentRequisition->department->name ?? 'N/A' }}</span></td>
            <td>{{ $ret->inventoryItem->name ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($ret->quantity_returned, 2) }}</td>
            <td>{{ \Illuminate\Support\Str::limit($ret->return_reason ?? '-', 30) }}</td>
            <td>{{ $ret->departmentRequisition->returned_by ?? 'N/A' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No return records found</td></tr>
        @endforelse
    </tbody>
</table>

{{-- ============================================================ --}}
{{-- SECTION 8: STOCK ADDITIONS DETAIL --}}
{{-- ============================================================ --}}
<div class="section-title">📥 STOCK ADDITIONS (IN MOVEMENTS)</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Movement #</th>
            <th>Item</th>
            <th class="text-right">Quantity</th>
            <th>Unit</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Total Value</th>
            <th>Source</th>
            <th>Received By</th>
        </tr>
    </thead>
    <tbody>
        @php $addTotalValue = 0; @endphp
        @forelse($chartData['stockAdditions'] as $addition)
        @php $addTotalValue += $addition->total_value ?? 0; @endphp
        <tr>
            <td>{{ $addition->movement_date ? $addition->movement_date->format('Y-m-d') : 'N/A' }}</td>
            <td>{{ $addition->movement_number }}</td>
            <td>{{ $addition->inventoryItem->name ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($addition->quantity_in_base_unit ?? $addition->quantity, 2) }}</td>
            <td>{{ $addition->base_unit ?? $addition->inventoryItem->base_unit ?? 'units' }}</td>
            <td class="text-right">{{ number_format($addition->unit_cost ?? 0, 2) }}</td>
            <td class="text-right">{{ number_format($addition->total_value ?? 0, 2) }}</td>
            <td>{{ $addition->movementType->name ?? 'N/A' }}</td>
            <td>{{ $addition->createdBy->name ?? 'System' }}</td>
        </tr>
        @empty
        <td><td colspan="9" class="text-center">No stock addition records found</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="text-right"><strong>Total Value Added:</strong></td>
            <td class="text-right"><strong>UGX {{ number_format($addTotalValue, 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

{{-- ============================================================ --}}
{{-- SECTION 9: ALL MOVEMENTS LOG --}}
{{-- ============================================================ --}}
<div class="section-title">📋 ALL STOCK MOVEMENTS LOG</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Movement #</th>
            <th>Item</th>
            <th>Type</th>
            <th class="text-right">Qty</th>
            <th>Unit</th>
            <th class="text-right">Before</th>
            <th class="text-right">After</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Value</th>
            <th>Reason</th>
            <th>Responsible</th>
        </tr>
    </thead>
    <tbody>
        @php $totalQty = 0; $totalValueSum = 0; @endphp
        @forelse($movements as $movement)
        @php
            $totalQty += $movement->quantity;
            $totalValueSum += $movement->total_value;
        @endphp
        <tr>
            <td>{{ $movement->date }}</td>
            <td>{{ $movement->movement_number }}</td>
            <td>{{ $movement->item_name }}</td>
            <td class="text-center">
                @if($movement->direction == 'IN')
                    <span class="badge-in">{{ $movement->type }}</span>
                @else
                    <span class="badge-out">{{ $movement->type }}</span>
                @endif
            </td>
            <td class="text-right">{{ number_format($movement->quantity, 2) }}</td>
            <td>{{ $movement->unit }}</td>
            <td class="text-right">{{ number_format($movement->stock_before, 2) }}</td>
            <td class="text-right">{{ number_format($movement->stock_after, 2) }}</td>
            <td class="text-right">{{ number_format($movement->unit_cost, 2) }}</td>
            <td class="text-right">{{ number_format($movement->total_value, 2) }}</td>
            <td>{{ \Illuminate\Support\Str::limit($movement->reason, 25) }}</td>
            <td>
                @if($movement->taken_by != 'N/A')
                    Taken: {{ $movement->taken_by }}
                @elseif($movement->returned_by != 'N/A')
                    Returned: {{ $movement->returned_by }}
                @else
                    —
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="12" class="text-center">No movements found</td><tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="text-right"><strong>TOTALS:</strong></td>
            <td class="text-right"><strong>{{ number_format($totalQty, 2) }}</strong></td>
            <td colspan="4"></td>
            <td class="text-right"><strong>UGX {{ number_format($totalValueSum, 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

{{-- FOOTER --}}
<div class="footer">
    <p>This report was automatically generated by the Inventory Management System | Confidential – For internal use only</p>
    <p>For any discrepancies, please contact the Store Manager immediately.</p>
</div>

</body>
</html>
