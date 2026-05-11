<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Movements Export</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 20px;
            font-size: 9px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #f0f0f0;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            font-size: 7px;
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 7px;
            color: #999;
        }
        .breakdown {
            font-size: 7px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Stock Movements Report</h1>
        <p>Generated on {{ $export_date }}</p>
        <p>Total Movements: {{ $total_movements }} | Total Value: UGX {{ number_format($total_value, 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Movement #</th>
                <th>Item</th>
                <th>Code</th>
                <th>Type</th>
                <th class="text-center">Qty</th>
                <th>Breakdown</th>
                <th class="text-right">Total Units</th>
                <th class="text-right">Stock Before</th>
                <th class="text-right">Stock After</th>
                <th class="text-right">Unit Cost</th>
                <th class="text-right">Total Value</th>
                <th>Date</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
            <tr>
                <td>{{ $movement->movement_number }}</td>
                <td>{{ $movement->item_name }}</td>
                <td>{{ $movement->item_code }}</td>
                <td>{{ $movement->type }} ({{ $movement->direction }})</td>
                <td class="text-center">{{ number_format($movement->quantity, 2) }}</td>
                <td class="breakdown">{{ $movement->breakdown }}</td>
                <td class="text-right">{{ number_format($movement->total_units, 2) }} {{ $movement->unit }}</td>
                <td class="text-right">{{ number_format($movement->stock_before, 2) }}</td>
                <td class="text-right">{{ number_format($movement->stock_after, 2) }}</td>
                <td class="text-right">UGX {{ number_format($movement->unit_cost, 2) }}</td>
                <td class="text-right">UGX {{ number_format($movement->total_value, 2) }}</td>
                <td>{{ $movement->movement_date }}</td>
                <td>{{ Str::limit($movement->reason, 50) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the inventory management system.</p>
    </div>
</body>
</html>
