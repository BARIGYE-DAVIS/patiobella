{{-- resources/views/restaurant/cashier/my-sales-pdf.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Sales Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 10pt;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ea580c;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #ea580c;
            font-size: 18pt;
        }
        .header p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 9pt;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
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
            padding: 10px;
            background: #f9fafb;
            border-left: 3px solid;
            border-radius: 4px;
        }
        .stat-box .stat-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
        }
        .stat-box .stat-value {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8pt;
            color: #9ca3af;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
        }
        .badge-cash {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-card {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-mobile {
            background: #fef3c7;
            color: #92400e;
        }
        .filter-info {
            background: #fef3c7;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PATIO BELLA</h1>
        <p>Restaurant & Lounge</p>
        <p>Kampala Road, Kampala | Tel: +256 XXX XXX XXX</p>
        <h2 style="font-size: 14pt; margin-top: 15px;">My Sales Report</h2>
        <p>Cashier: {{ Auth::user()->first_name ?? 'N/A' }} {{ Auth::user()->last_name ?? '' }}</p>
        <p>Report Period: {{ \Carbon\Carbon::parse($filters['from'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($filters['to'])->format('d M Y') }}</p>
        @if($filters['search'])
            <p>Search Filter: "{{ $filters['search'] }}"</p>
        @endif
    </div>

    @if($filters['search'])
    <div class="filter-info">
        <strong>⚠️ Filter Applied:</strong> Showing results for "{{ $filters['search'] }}"
    </div>
    @endif

    <div class="stats">
        <div class="stat-box" style="border-left-color: #10b981;">
            <div class="stat-label">Total Sales</div>
            <div class="stat-value" style="color: #10b981;">UGX {{ number_format($stats['total_sales'], 0) }}</div>
        </div>
        <div class="stat-box" style="border-left-color: #3b82f6;">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value" style="color: #3b82f6;">{{ number_format($stats['total_orders']) }}</div>
        </div>
        <div class="stat-box" style="border-left-color: #f59e0b;">
            <div class="stat-label">Average Order</div>
            <div class="stat-value" style="color: #f59e0b;">UGX {{ number_format($stats['avg_order'], 0) }}</div>
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
                <th>Date</th>
                <th>Time</th>
                <th>Customer Type</th>
                <th>Payment</th>
                <th class="text-right">Amount (UGX)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td style="font-family: monospace;">{{ $order->order_number }}</td
                <td>{{ $order->created_at->format('d/m/Y') }}</td
                <td>{{ $order->created_at->format('h:i A') }}</td
                <td>{{ ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')) }}</td
                <td>
                    @if($order->payment_method == 'cash')
                        <span class="badge badge-cash">💵 Cash</span>
                    @elseif($order->payment_method == 'card')
                        <span class="badge badge-card">💳 Card</span>
                    @elseif($order->payment_method == 'mobile_money')
                        <span class="badge badge-mobile">📱 Mobile Money</span>
                    @else
                        {{ ucfirst($order->payment_method ?? 'N/A') }}
                    @endif
                </td
                <td class="text-right">{{ number_format($order->total_amount, 0) }}</td
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>GRAND TOTAL:</strong></td
                <td class="text-right"><strong>UGX {{ number_format($stats['total_sales'], 0) }}</strong></td
            </tr>
        </tfoot>
     </table>
    @else
        <div style="text-align: center; padding: 40px; background: #f9fafb; border-radius: 8px;">
            <p style="font-size: 12pt; color: #6b7280;">No sales found for the selected period.</p>
            <p style="font-size: 10pt; color: #9ca3af;">Try changing the date range or search criteria.</p>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y h:i A') }} | PatioBella POS System</p>
        <p>This is an official sales report. Please keep for your records.</p>
        <p>Report shows all sales by {{ Auth::user()->first_name ?? 'Cashier' }} from {{ \Carbon\Carbon::parse($filters['from'])->format('d M Y') }} to {{ \Carbon\Carbon::parse($filters['to'])->format('d M Y') }}</p>
    </div>
</body>
</html>
