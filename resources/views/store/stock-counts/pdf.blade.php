<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Count - {{ $stockCount->count_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #f97316;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0;
            font-size: 12px;
        }
        .info-section {
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 8px;
        }
        .info-section h3 {
            margin: 0 0 10px;
            color: #374151;
            font-size: 14px;
            border-left: 3px solid #f97316;
            padding-left: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            color: #6b7280;
            padding: 5px 0;
        }
        .info-value {
            display: table-cell;
            color: #374151;
            padding: 5px 0;
        }
        .summary-grid {
            display: flex;
            margin-bottom: 20px;
        }
        .summary-card {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-right: 10px;
        }
        .summary-card:last-child {
            margin-right: 0;
        }
        .summary-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #374151;
        }
        .variance-negative {
            color: #dc2626;
        }
        .variance-positive {
            color: #059669;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table th {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #6b7280;
        }
        .table td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            color: #9ca3af;
            font-size: 10px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #374151;
            margin-top: 40px;
            padding-top: 8px;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Stock Count Report</h1>
        <p>{{ $stockCount->count_number }}</p>
    </div>

    <div class="info-section">
        <h3>Count Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Count Number:</div>
                <div class="info-value">{{ $stockCount->count_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Count Date:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($stockCount->count_date)->format('F d, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $stockCount->location_type === 'store' ? 'Main Store' : ($stockCount->location->name ?? 'Department') }}</div>
            </div>
            @if($stockCount->notes)
            <div class="info-row">
                <div class="info-label">Notes:</div>
                <div class="info-value">{{ $stockCount->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="info-section">
        <h3>Staff Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Count Conducted By:</div>
                <div class="info-value">{{ $stockCount->creator->first_name ?? 'Unknown' }} {{ $stockCount->creator->last_name ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Count Date/Time:</div>
                <div class="info-value">{{ $stockCount->created_at->format('F d, Y h:i A') }}</div>
            </div>
            @if($stockCount->completed_by)
            <div class="info-row">
                <div class="info-label">Approved By:</div>
                <div class="info-value">{{ $stockCount->completer->first_name ?? 'Manager' }} {{ $stockCount->completer->last_name ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Approved Date/Time:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($stockCount->completed_at)->format('F d, Y h:i A') }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Items Counted</div>
            <div class="summary-value">{{ $stockCount->items->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">System Quantity</div>
            <div class="summary-value">{{ number_format($stockCount->getTotalSystemQuantityAttribute(), 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Physical Counted</div>
            <div class="summary-value">{{ number_format($stockCount->getTotalNetQuantityAttribute(), 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Variance</div>
            <div class="summary-value {{ $totalVariance < 0 ? 'variance-negative' : ($totalVariance > 0 ? 'variance-positive' : '') }}">
                {{ $totalVariance >= 0 ? '+' : '' }}{{ number_format($totalVariance, 2) }}
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>System Qty</th>
                <th>Physical Qty</th>
                <th>Net Qty</th>
                <th>Variance</th>
                <th>Notes</th>
             </tr>
        </thead>
        <tbody>
            @foreach($stockCount->items as $item)
            @php
                $netQty = $item->net_quantity;
                $variance = $item->variance;
                $varianceClass = $variance < 0 ? 'variance-negative' : ($variance > 0 ? 'variance-positive' : '');
            @endphp
            <tr>
                <td>{{ $item->inventoryItem->name ?? 'N/A' }}<br><small style="color:#9ca3af;">{{ $item->inventoryItem->item_code ?? '' }}</small></td>
                <td>{{ $item->inventoryItem->base_unit ?? 'units' }}</td>
                <td>{{ number_format($item->system_quantity, 2) }}</td>
                <td>{{ number_format($item->physical_quantity, 2) }}</td>
                <td>{{ number_format($netQty, 2) }}</td>
                <td class="{{ $varianceClass }}">{{ $variance >= 0 ? '+' : '' }}{{ number_format($variance, 2) }}</td>
                <td>{{ $item->reason_notes ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Totals</strong></td>
                <td><strong>{{ number_format($stockCount->getTotalSystemQuantityAttribute(), 2) }}</strong></td>
                <td></td>
                <td><strong>{{ number_format($stockCount->getTotalNetQuantityAttribute(), 2) }}</strong></td>
                <td><strong class="{{ $totalVariance < 0 ? 'variance-negative' : ($totalVariance > 0 ? 'variance-positive' : '') }}">{{ $totalVariance >= 0 ? '+' : '' }}{{ number_format($totalVariance, 2) }}</strong></td>
                <td></td>
             </tr>
        </tfoot>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Counted By: {{ $stockCount->creator->first_name ?? '' }} {{ $stockCount->creator->last_name ?? '' }}
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Approved By: {{ $stockCount->completer->first_name ?? '_________________' }} {{ $stockCount->completer->last_name ?? '' }}
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y h:i A') }} | Patio Bella Inventory System</p>
    </div>

</body>
</html>
