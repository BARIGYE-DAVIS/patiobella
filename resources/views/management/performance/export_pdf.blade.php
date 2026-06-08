<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Performance Report - {{ $report->report_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2563eb;
        }
        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 9px;
            color: #6b7280;
        }
        .report-info {
            background: #f3f4f6;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 9px;
        }
        .report-info table {
            width: 100%;
        }
        .report-info td {
            padding: 3px 5px;
        }
        .section-title {
            background: #2563eb;
            color: white;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            border-radius: 4px;
        }
        .gifts-section {
            background: #dbeafe;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
        }
        .no-gifts-section {
            background: #fef3c7;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #f59e0b;
        }
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }
        .summary-card {
            flex: 1;
            min-width: 100px;
            background: white;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .summary-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #6b7280;
        }
        .summary-value {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background: #e5e7eb;
            padding: 6px 4px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }
        td {
            padding: 5px 4px;
            text-align: left;
            border: 1px solid #d1d5db;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-red {
            color: #dc2626;
        }
        .text-green {
            color: #10b981;
        }
        .text-blue {
            color: #2563eb;
        }
        .text-orange {
            color: #f59e0b;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fed7aa;
            color: #9a3412;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    @if($businessSettings['company_logo'] ?? false)
        <img src="{{ public_path('storage/' . $businessSettings['company_logo']) }}" style="max-height: 60px; margin-bottom: 10px;">
    @endif
    <h1>{{ $businessSettings['company_name'] ?? 'PATIO BELLA' }}</h1>
    <h2>Performance Report</h2>
    <p>{{ $businessSettings['address'] ?? '' }} {{ $businessSettings['city'] ?? '' }} {{ $businessSettings['country'] ?? '' }} | Tel: {{ $businessSettings['phone'] ?? '' }} | Email: {{ $businessSettings['email'] ?? '' }}</p>
</div>

    <!-- REPORT INFORMATION -->
    <div class="report-info">
        <table>
            <tr>
                <td><strong>Report #:</strong> {{ $report->report_number }}</td>
                <td><strong>Department:</strong> {{ $report->department->name ?? 'N/A' }}</td>
                <td><strong>Report Date:</strong> {{ $report->report_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Generated By:</strong> {{ $generated_by }}</td>
                <td><strong>Generated On:</strong> {{ $generated_date }}</td>
                <td><strong>Status:</strong> <span class="badge badge-success">Completed</span></td>
            </tr>
        </table>
    </div>

    <!-- SECTION 1: WITH GIFTS INCLUDED -->
    <div class="section-title">📊 SALES (GIFTS INCLUDED)</div>
    <div class="gifts-section">
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Sales</div>
                <div class="summary-value text-green">{{ number_format($report->total_sales, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">COGS</div>
                <div class="summary-value text-red">{{ number_format($report->total_cogs, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Gifts</div>
                <div class="summary-value text-orange">{{ number_format($report->gifts_amount ?? 0, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Profit</div>
                <div class="summary-value text-blue">{{ number_format($report->total_profit, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Profit Margin</div>
                <div class="summary-value text-green">{{ number_format($report->profit_margin, 2) }}%</div>
            </div>
        </div>
        <div style="margin-top: 8px;">
            <span class="badge badge-warning">COGS %: {{ number_format($report->total_sales > 0 ? ($report->total_cogs / $report->total_sales) * 100 : 0, 2) }}%</span>
            <span class="badge badge-success" style="margin-left: 10px;">Avg Profit/Item: {{ number_format($report->total_profit / max($report->items->groupBy('menu_item_id')->count(), 1), 0) }} UGX</span>
        </div>
    </div>

    <!-- SECTION 2: WITHOUT GIFTS -->
    <div class="section-title">🎁 PERFORMANCE SUMMARY: GIFTS EXCLUDED</div>
    <div class="no-gifts-section">
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Sales (Gifts Removed)</div>
                <div class="summary-value text-green">{{ number_format($salesWithoutGifts, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">COGS</div>
                <div class="summary-value text-red">{{ number_format($report->total_cogs, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Profit</div>
                <div class="summary-value text-blue">{{ number_format($profitWithoutGifts, 0) }} UGX</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Profit Margin</div>
                <div class="summary-value text-green">{{ number_format($marginWithoutGifts, 2) }}%</div>
            </div>
        </div>
        <div>
            <span class="badge badge-warning">COGS %: {{ number_format($salesWithoutGifts > 0 ? ($report->total_cogs / $salesWithoutGifts) * 100 : 0, 2) }}%</span>
        </div>
    </div>

    <!-- SECTION 3: MENU ITEMS -->
    <div class="section-title">🍽️ MENU ITEMS BREAKDOWN</div>
    <table>
        <thead>
            <tr>
                <th>MENU ITEM</th>
                <th>QTY SOLD</th>
                <th>SELLING PRICE (UGX)</th>
                <th>COGS (UGX)</th>
                <th>PROFIT MARGIN</th>
                <th>PROFIT (UGX)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedItems as $menuItemId => $items)
                @php
                    $firstItem = $items->first();
                    $totalCogs = $items->sum('cogs');
                    $totalRevenue = $firstItem->quantity_sold * $firstItem->selling_price;
                    $profit = $totalRevenue - $totalCogs;
                    $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;
                    $marginClass = $profitMargin >= 50 ? 'badge-success' : ($profitMargin >= 30 ? 'badge-warning' : 'badge-danger');
                @endphp
                <tr>
                    <td class="font-bold">{{ $firstItem->menuItem->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($firstItem->quantity_sold, 0) }}</td>
                    <td class="text-right">{{ number_format($firstItem->selling_price, 0) }}</td>
                    <td class="text-right text-red">{{ number_format($totalCogs, 0) }}</td>
                    <td class="text-center"><span class="badge {{ $marginClass }}">{{ number_format($profitMargin, 1) }}%</span></td>
                    <td class="text-right text-blue">{{ number_format($profit, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="background: #f3f4f6;">
            <tr>
                <td colspan="3" class="text-right font-bold">TOTALS:</td>
                <td class="text-right font-bold text-red">{{ number_format($report->total_cogs, 0) }}</td>
                <td class="text-center font-bold">{{ number_format($report->profit_margin, 1) }}%</td>
                <td class="text-right font-bold text-blue">{{ number_format($report->total_profit, 0) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- SECTION 4: TOP MOVING STOCK ITEMS -->
    <div class="section-title">🔥 TOP MOVING STOCK ITEMS (MOST USED)</div>
    <table>
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
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="text-center">{{ $item['uom'] }}</td>
                <td class="text-right text-orange font-bold">{{ number_format($item['used'], 2) }}</td>
                <td class="text-right text-red">{{ number_format($item['cogs'], 0) }}</td>
                <td class="text-center">{{ $report->total_cogs > 0 ? number_format(($item['cogs'] / $report->total_cogs) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot style="background: #f3f4f6;">
            <tr>
                <td colspan="3" class="text-right font-bold">TOTALS:</td>
                <td class="text-right font-bold">{{ number_format(collect($topMovingItems)->sum('used'), 2) }}</td>
                <td class="text-right font-bold">{{ number_format(collect($topMovingItems)->sum('cogs'), 0) }}</td>
                <td class="text-center font-bold">100%</td>
            </tr>
        </tfoot>
    </table>

    <!-- SECTION 5: GENERAL STOCK SUMMARY -->
    <div class="section-title">📦 GENERAL STOCK SUMMARY</div>
    <table>
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
                    if (!isset($stockSummary[$inventoryId])) {
                        $stockSummary[$inventoryId] = [
                            'name' => $item->inventoryItem->name ?? 'N/A',
                            'uom' => $item->inventoryItem->unit_of_measurement ?? 'piece',
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
                <td>{{ $stock['name'] }}</td>
                <td class="text-center">{{ $stock['uom'] }}</td>
                <td class="text-right">{{ number_format($stock['opening'], 2) }}</td>
                <td class="text-right">{{ number_format($stock['added'], 2) }}</td>
                <td class="text-right text-orange font-bold">{{ number_format($stock['used'], 2) }}</td>
                <td class="text-right">{{ number_format($stock['closing'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot style="background: #f3f4f6;">
            <tr>
                <td colspan="2" class="text-right font-bold">TOTALS:</td>
                <td class="text-right font-bold">{{ number_format(collect($stockSummary)->sum('opening'), 2) }}</td>
                <td class="text-right font-bold">{{ number_format(collect($stockSummary)->sum('added'), 2) }}</td>
                <td class="text-right font-bold">{{ number_format(collect($stockSummary)->sum('used'), 2) }}</td>
                <td class="text-right font-bold">{{ number_format(collect($stockSummary)->sum('closing'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- FOOTER -->
<div class="footer">
    <p>This report was generated automatically by {{ $businessSettings['company_name'] ?? 'Patiobella' }} Management System</p>
    <p>© {{ date('Y') }} {{ $businessSettings['company_name'] ?? 'Patiobella' }} - All Rights Reserved</p>
    <p>{{ $businessSettings['address'] ?? '' }} {{ $businessSettings['city'] ?? '' }} {{ $businessSettings['country'] ?? '' }} | Tel: {{ $businessSettings['phone'] ?? '' }} | Email: {{ $businessSettings['email'] ?? '' }}</p>
</div>

</body>
</html>
