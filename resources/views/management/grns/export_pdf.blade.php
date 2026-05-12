<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Goods Received Notes Report</title>
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
        .card-completed { border-left-color: #10b981; }
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
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-inventory_updated { background: #dbeafe; color: #1e40af; }

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
    <h1>GOODS RECEIVED NOTES REPORT</h1>
    <div class="sub">Generated: {{ $export_date }} | Total GRNs: {{ $total_grns }} | Total Value: UGX {{ number_format($total_value, 2) }}</div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 1: SUMMARY STATISTICS --}}
{{-- ============================================================ --}}
@php
    $grnCount = $grns->count();
    $totalVal = $grns->sum('grn_total_amount');
    $draftCount = $grns->where('status', 'draft')->count();
    $completedCount = $grns->where('status', 'completed')->count();
    $inventoryUpdatedCount = $grns->where('status', 'inventory_updated')->count();
@endphp

<div class="stat-grid">
    <div class="stat-card card-total">
        <div class="label">TOTAL GRNs</div>
        <div class="value text-blue">{{ $grnCount }}</div>
        <div class="sub">Goods received notes</div>
    </div>
    <div class="stat-card card-value">
        <div class="label">TOTAL VALUE</div>
        <div class="value text-purple">UGX {{ number_format($totalVal, 2) }}</div>
        <div class="sub">All received goods</div>
    </div>
    <div class="stat-card card-pending">
        <div class="label">DRAFT</div>
        <div class="value text-orange">{{ $draftCount }}</div>
        <div class="sub">Pending completion</div>
    </div>
    <div class="stat-card card-completed">
        <div class="label">COMPLETED</div>
        <div class="value text-green">{{ $completedCount }}</div>
        <div class="sub">Ready for inventory</div>
    </div>
    <div class="stat-card card-total">
        <div class="label">INVENTORY UPDATED</div>
        <div class="value text-blue">{{ $inventoryUpdatedCount }}</div>
        <div class="sub">Stock added</div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SECTION 2: STATUS DISTRIBUTION PIE CHART --}}
{{-- ============================================================ --}}
@php
    $statusLabels = ['Draft', 'Completed', 'Inventory Updated'];
    $statusData = [$draftCount, $completedCount, $inventoryUpdatedCount];
    $statusColors = ['#f59e0b', '#10b981', '#3b82f6'];

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
    <div class="chart-title">GRN Status Distribution</div>
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
{{-- SECTION 3: GRNs TABLE --}}
{{-- ============================================================ --}}
<div class="section-title">📋 GOODS RECEIVED NOTES LIST</div>

<table class="data-table">
    <thead>
        <tr>
            <th>GRN Number</th>
            <th>Received Date</th>
            <th>Vendor</th>
            <th>PO Number</th>
            <th>Delivery Note #</th>
            <th class="text-right">Total Amount</th>
            <th>Status</th>
            <th>Created By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($grns as $grn)
        <tr>
            <td>{{ $grn->grn_number }}</td>
            <td>{{ $grn->received_date ? $grn->received_date->format('Y-m-d') : 'N/A' }}</td>
            <td>{{ $grn->vendor->name ?? 'N/A' }}</td>
            <td>{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</td>
            <td>{{ $grn->delivery_note_number ?? '—' }}</td>
            <td class="text-right">UGX {{ number_format($grn->grn_total_amount, 2) }}</td>
            <td class="text-center">
                <span class="badge-status status-{{ $grn->status }}">
                    @if($grn->status == 'draft') Draft
                    @elseif($grn->status == 'completed') Completed
                    @elseif($grn->status == 'inventory_updated') Inventory Updated
                    @else {{ ucfirst($grn->status) }}
                    @endif
                </span>
            </td>
            <td>{{ $grn->createdBy->name ?? 'N/A' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center">No goods received notes found</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right"><strong>GRAND TOTAL:</strong></td>
            <td class="text-right"><strong>UGX {{ number_format($totalVal, 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

{{-- ============================================================ --}}
{{-- DETAILED SECTION (New Page) --}}
{{-- ============================================================ --}}
@if($grns->count() > 0)
<div class="page-break"></div>

<div class="header" style="border-bottom: 1px solid #e5e7eb;">
    <h1 style="font-size: 12px;">DETAILED GRN ITEMS</h1>
    <div class="sub">{{ $export_date }}</div>
</div>

@foreach($grns as $grn)
<div class="section-title">📄 GRN: {{ $grn->grn_number }} - {{ $grn->vendor->name ?? 'N/A' }}</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Item Code</th>
            <th class="text-right">Ordered</th>
            <th class="text-right">Received</th>
            <th class="text-right">Accepted</th>
            <th class="text-right">Rejected</th>
            <th>Pack Type</th>
            <th class="text-right">Pack Size</th>
            <th>Base Unit</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Total Cost</th>
        </tr>
    </thead>
    <tbody>
        @php $grnTotal = 0; @endphp
        @foreach($grn->items as $item)
        @php $grnTotal += $item->total_cost; @endphp
        <tr>
            <td>{{ $item->inventoryItem->name ?? 'N/A' }}</td>
            <td>{{ $item->inventoryItem->item_code ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($item->quantity_ordered, 2) }}</td>
            <td class="text-right">{{ number_format($item->quantity_received, 2) }}</td>
            <td class="text-right">{{ number_format($item->quantity_accepted, 2) }}</td>
            <td class="text-right">{{ number_format($item->quantity_rejected, 2) }}</td>
            <td class="text-center">{{ $item->pack_type ?? '—' }}</td>
            <td class="text-right">{{ number_format($item->pack_size ?? 0) }}</td>
            <td class="text-center">{{ $item->base_unit ?? $item->inventoryItem->base_unit ?? 'units' }}</td>
            <td class="text-right">UGX {{ number_format($item->unit_cost, 2) }}</td>
            <td class="text-right">UGX {{ number_format($item->total_cost, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10" class="text-right"><strong>GRN TOTAL:</strong></td>
            <td class="text-right"><strong>UGX {{ number_format($grnTotal, 2) }}</strong></td>
        </tr>
    </tfoot>
</table>

@if($grn->notes)
<div style="margin: -10px 0 10px 0; padding: 5px 8px; background: #fef3c7; border-left: 3px solid #f59e0b; font-size: 7px;">
    <strong>Notes:</strong> {{ $grn->notes }}
</div>
@endif

@endforeach
@endif

{{-- FOOTER --}}
<div class="footer">
    <p>This report was automatically generated by the Inventory Management System | Confidential – For internal use only</p>
    <p>For any discrepancies, please contact the Store Manager immediately.</p>
</div>

</body>
</html>
