{{-- resources/views/bar/sales/export-pdf.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bar Sales Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 9pt;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #3b82f6;
            font-size: 18pt;
        }
        .header p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 9pt;
        }
        .stats {
            margin-bottom: 20px;
            overflow: hidden;
        }
        .stat-box {
            float: left;
            width: 23%;
            margin-right: 2%;
            padding: 8px;
            background: #f9fafb;
            border-left: 3px solid;
            border-radius: 4px;
        }
        .stat-box .stat-label {
            font-size: 7pt;
            color: #6b7280;
            text-transform: uppercase;
        }
        .stat-box .stat-value {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8pt;
        }
        th {
            background: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 7pt;
            color: #9ca3af;
        }
        .invoice-number {
            font-family: monospace;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PATIO BELLA - BAR</h1>
        <p>Sales Report</p>
        @if(isset($exportType) && $exportType === 'all')
            <p>Period: ALL TIME (No Date Filter)</p>
        @else
            <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        @endif
        <p>Generated on {{ now()->format('d/m/Y h:i A') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box" style="border-left-color: #10b981;">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value" style="color: #10b981;">UGX {{ number_format($stats['total_sales'], 0) }}</div>
        </div>
        <div class="stat-box" style="border-left-color: #3b82f6;">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value" style="color: #3b82f6;">{{ number_format($stats['total_orders']) }}</div>
        </div>
        <div class="stat-box" style="border-left-color: #f59e0b;">
            <div class="stat-label">Average Order</div>
            <div class="stat-value" style="color: #f59e0b;">UGX {{ number_format($stats['avg_order_value'], 0) }}</div>
        </div>
        <div class="stat-box" style="border-left-color: #ef4444;">
            <div class="stat-label">Items Sold</div>
            <div class="stat-value" style="color: #ef4444;">{{ number_format($stats['total_items']) }}</div>
        </div>
    </div>

    <div style="clear: both;"></div>

    @if($orders->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date & Time</th>
                <th>Cashier</th>
                <th>Customer Type</th>
                <th>Payment Method</th>
                <th>Item Name</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Item Total</th>
                <th class="text-right">Amount Paid</th>
                <th class="text-right">Change</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotalItems = 0;
                $grandTotalRevenue = 0;
            @endphp
            @foreach($orders as $order)
                @php
                    $firstItem = true;
                    $itemCount = $order->items->count();
                    $invoiceTotal = 0;
                @endphp
                @foreach($order->items as $item)
                    @php
                        $invoiceTotal += $item->total_price;
                        $grandTotalItems += $item->quantity;
                    @endphp
                    <tr>
                        @if($firstItem)
                            <td class="invoice-number" rowspan="{{ $itemCount }}">{{ $order->order_number }}</td>
                            <td rowspan="{{ $itemCount }}">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                            <td rowspan="{{ $itemCount }}">{{ $order->cashier->first_name ?? 'N/A' }}</td>
                            <td rowspan="{{ $itemCount }}">{{ ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')) }}</td>
                            <td rowspan="{{ $itemCount }}">{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
                        @endif
                        <td>{{ $item->item_name }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">UGX {{ number_format($item->unit_price, 0) }}</td>
                        <td class="text-right">UGX {{ number_format($item->total_price, 0) }}</td>
                        @if($firstItem)
                            <td rowspan="{{ $itemCount }}" class="text-right">UGX {{ number_format($order->amount_paid ?? $order->total_amount, 0) }}</td>
                            <td rowspan="{{ $itemCount }}" class="text-right">UGX {{ number_format($order->change_amount ?? 0, 0) }}</td>
                        @endif
                    </tr>
                    @php $firstItem = false; @endphp
                @endforeach
                @php $grandTotalRevenue += $invoiceTotal; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>GRAND TOTALS:</strong></td>
                <td class="text-center"><strong>{{ number_format($grandTotalItems, 0) }}</strong></td>
                <td colspan="2"></td>
                <td class="text-right"><strong>UGX {{ number_format($grandTotalRevenue, 0) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align: center; padding: 40px;">
        <p>No sales data found for the selected period.</p>
    </div>
    @endif

    <div class="footer">
        <p>This is an official sales report. Please keep for your records.</p>
        <p>Powered by PatioBella POS System</p>
    </div>
</body>
</html>
