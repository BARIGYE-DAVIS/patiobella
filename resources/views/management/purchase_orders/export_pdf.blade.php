<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Orders Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #1f2937;
            padding: 15px 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #1e40af;
        }
        .header h1 { font-size: 16px; color: #1e40af; margin-bottom: 3px; }
        .header .sub { font-size: 8px; color: #6b7280; }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            background: #1e40af;
            color: white;
            padding: 5px 10px;
            margin: 15px 0 10px 0;
            border-radius: 3px;
        }

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
        .stat-card .value { font-size: 12px; font-weight: bold; margin-top: 3px; }
        .stat-card .sub { font-size: 6px; color: #9ca3af; margin-top: 2px; }
        .card-total { border-left-color: #3b82f6; }
        .card-value { border-left-color: #8b5cf6; }
        .card-pending { border-left-color: #f59e0b; }
        .card-approved { border-left-color: #10b981; }
        .text-blue { color: #1d4ed8; }
        .text-purple { color: #5b21b6; }
        .text-orange { color: #9c4221; }
        .text-green { color: #065f46; }

        .two-col {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .col { flex: 1; }

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
        .chart-item { margin-bottom: 10px; }
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
        .data-table tfoot td { background: #e2e8f0; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .badge-status {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 6px;
            font-weight: bold;
            display: inline-block;
        }
        .status-draft { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-sent { background: #dbeafe; color: #1e40af; }
        .status-partially_received { background: #fed7aa; color: #9c4221; }
        .status-fully_received { background: #a7f3d0; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .pie-wrapper { text-align: center; margin-bottom: 10px; }
        .pie-chart {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 10px;
        }
        .pie-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-top: 5px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 6.5px;
        }
        .legend-color {
            width: 8px;
            height: 8px;
            border-radius: 2px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 6px;
            color: #9ca3af;
        }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <h1>PURCHASE ORDERS REPORT</h1>
    <div class="sub">Generated: {{ $export_date }} | Total Orders: {{ $total_orders }} | Total Value: UGX {{ number_format($total_value, 2) }}</div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 1: SUMMARY STATISTICS (Calculated from data) --}}
{{-- ============================================================ --}}
@php
    $poCount = $purchaseOrders->count();
    $totalVal = $purchaseOrders->sum('total_amount');
    $draftCount = $purchaseOrders->where('status', 'draft')->count();
    $approvedCount = $purchaseOrders->where('status', 'approved')->count();
    $sentCount = $purchaseOrders->where('status', 'sent')->count();
    $partialReceivedCount = $purchaseOrders->where('status', 'partially_received')->count();
    $fullyReceivedCount = $purchaseOrders->where('status', 'fully_received')->count();
    $cancelledCount = $purchaseOrders->where('status', 'cancelled')->count();
@endphp

<div class="stat-grid">
    <div class="stat-card card-total">
        <div class="label">TOTAL POs</div>
        <div class="value text-blue">{{ $poCount }}</div>
        <div class="sub">Purchase orders</div>
    </div>
    <div class="stat-card card-value">
        <div class="label">TOTAL VALUE</div>
        <div class="value text-purple">UGX {{ number_format($totalVal, 2) }}</div>
        <div class="sub">All orders combined</div>
    </div>
    <div class="stat-card card-pending">
        <div class="label">PENDING</div>
        <div class="value text-orange">{{ $draftCount }}</div>
        <div class="sub">Draft / Awaiting approval</div>
    </div>
    <div class="stat-card card-approved">
        <div class="label">APPROVED & SENT</div>
        <div class="value text-green">{{ $approvedCount + $sentCount }}</div>
        <div class="sub">In progress</div>
    </div>
    <div class="stat-card card-total">
        <div class="label">COMPLETED</div>
        <div class="value text-blue">{{ $fullyReceivedCount }}</div>
        <div class="sub">Fully received</div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 2: STATUS DISTRIBUTION PIE CHART --}}
{{-- ============================================================ --}}
@php
    $statusLabels = ['Draft', 'Approved', 'Sent', 'Partially Received', 'Fully Received', 'Cancelled'];
    $statusData = [$draftCount, $approvedCount, $sentCount, $partialReceivedCount, $fullyReceivedCount, $cancelledCount];
    $statusColors = ['#f59e0b', '#10b981', '#3b82f6', '#f97316', '#06b6d4', '#ef4444'];

    $pieStops = [];
    $cumulative = 0;
    $totalForPie = array_sum($statusData);
    if ($totalForPie > 0) {
        foreach($statusData as $idx => $val) {
            $pct = ($val / $totalForPie) * 100;
            $color = $statusColors[$idx % count($statusColors)];
            $pieStops[] = $color . ' ' . $cumulative . '% ' . ($cumulative + $pct) . '%';
            $cumulative += $pct;
        }
    }
    $conicGradient = !empty($pieStops) ? 'conic-gradient(' . implode(', ', $pieStops) . ')' : '#e5e7eb';
@endphp

<div class="chart-container">
    <div class="chart-title">Purchase Order Status Distribution</div>
    <div class="pie-wrapper">
        <div class="pie-chart" style="background: {{ $conicGradient }};"></div>
        <div class="pie-legend">
            @foreach($statusData as $idx => $val)
            @if($val > 0)
            <div class="legend-item">
                <div class="legend-color" style="background: {{ $statusColors[$idx % count($statusColors)] }};"></div>
                <span>{{ $statusLabels[$idx] }} ({{ $val }})</span>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 3: PURCHASE ORDERS TABLE --}}
{{-- ============================================================ --}}
<div class="section-title">📋 PURCHASE ORDERS LIST</div>

<table class="data-table">
    <thead>
        <tr>
            <th>PO Number</th>
            <th>PO Date</th>
            <th>Vendor</th>
            <th class="text-right">Total Amount</th>
            <th>Status</th>
            <th>Ordered By</th>
            <th>Approved By</th>
            <th>Expected Delivery</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchaseOrders as $po)
        <tr>
            <td>{{ $po->po_number }}</td>
            <td>{{ $po->po_date->format('Y-m-d') }}</td>
            <td>{{ $po->vendor->name ?? 'N/A' }}</td>
            <td class="text-right">UGX {{ number_format($po->total_amount, 2) }}</td>
            <td class="text-center">
                <span class="badge-status status-{{ $po->status }}">
                    {{ ucfirst(str_replace('_', ' ', $po->status)) }}
                </span>
            </td>
            <td>{{ $po->orderedBy->name ?? 'N/A' }}</td>
            <td>{{ $po->approvedBy->name ?? 'Not approved' }}</td>
            <td>{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('Y-m-d') : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center">No purchase orders found</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
            <td class="text-right"><strong>UGX {{ number_format($totalVal, 2) }}</strong></td>
            <td colspan="4"></td>
        </tr>
    </tfoot>
</table>

{{-- ============================================================ --}}
{{-- DETAILED SECTION (New Page) --}}
{{-- ============================================================ --}}
@if($purchaseOrders->count() > 0)
<div class="page-break"></div>

<div class="header" style="border-bottom: 1px solid #e5e7eb;">
    <h1 style="font-size: 12px;">DETAILED PURCHASE ORDER ITEMS</h1>
    <div class="sub">{{ $export_date }}</div>
</div>

@foreach($purchaseOrders as $po)
<div class="section-title">📄 PO: {{ $po->po_number }} - {{ $po->vendor->name ?? 'N/A' }}</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Item Name</th>
            <th class="text-right">Quantity Ordered</th>
            <th class="text-right">Quantity Received</th>
            <th>Unit</th>
            <th class="text-right">Unit Cost (UGX)</th>
            <th class="text-right">Total Cost (UGX)</th>
        </tr>
    </thead>
    <tbody>
        @php $poTotal = 0; @endphp
        @foreach($po->items as $item)
        @php $poTotal += $item->total_cost; @endphp
        <tr>
            <td>{{ $item->inventoryItem->name ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($item->quantity_ordered, 2) }}</td>
            <td class="text-right">{{ number_format($item->quantity_received, 2) }}</td>
            <td>{{ $item->inventoryItem->base_unit ?? 'units' }}</td>
            <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
            <td class="text-right">{{ number_format($item->total_cost, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right"><strong>PO TOTAL:</strong></td>
            <td class="text-right"><strong>UGX {{ number_format($poTotal, 2) }}</strong></td>
        </tr>
    </tfoot>
</table>

@if($po->notes)
<div style="margin: -10px 0 10px 0; padding: 5px 8px; background: #fef3c7; border-left: 3px solid #f59e0b; font-size: 7px;">
    <strong>Notes:</strong> {{ $po->notes }}
</div>
@endif

@endforeach
@endif

{{-- FOOTER --}}
<div class="footer">
    <p>This report was automatically generated by the Inventory Management System | Confidential – For internal use only</p>
    <p>For any discrepancies, please contact the Procurement department immediately.</p>
</div>

</body>
</html>
