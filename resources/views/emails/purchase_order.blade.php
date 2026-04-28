<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $po->po_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            margin-bottom: 20px;
            font-size: 16px;
        }
        .po-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2a5298;
        }
        .po-info h3 {
            margin: 0 0 10px;
            color: #2a5298;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 130px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .vendor-section {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .vendor-section h3 {
            margin: 0 0 10px;
            color: #1e3c72;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #2a5298;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }
        .item-notes {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }
        .totals {
            text-align: right;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }
        .totals-row {
            margin-bottom: 8px;
        }
        .totals-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #2a5298;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .notes-box {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #ffc107;
        }
        .delivery-box {
            background-color: #d1ecf1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #17a2b8;
        }
        .attachment-note {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            border-left: 4px solid #2a5298;
        }
        @media (max-width: 600px) {
            .content {
                padding: 15px;
            }
            th, td {
                padding: 8px;
                font-size: 12px;
            }
            .info-label {
                width: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PURCHASE ORDER</h1>
            <p>{{ $po->po_number }}</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $po->vendor->contact_person ?: $po->vendor->name }},</strong>
            </div>
            
            <p>Please find below your purchase order details. A PDF version of this order is attached to this email.</p>
            
            <div class="po-info">
                <h3>Order Information</h3>
                <div class="info-row">
                    <span class="info-label">PO Number:</span>
                    <span class="info-value">{{ $po->po_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value">{{ $po->po_date->format('F d, Y') }}</span>
                </div>
                @if($po->expected_delivery_date)
                <div class="info-row">
                    <span class="info-label">Expected Delivery:</span>
                    <span class="info-value">{{ date('F d, Y', strtotime($po->expected_delivery_date)) }}</span>
                </div>
                @endif
            </div>
            
            <div class="vendor-section">
                <h3>Vendor Information</h3>
                <div class="info-row">
                    <span class="info-label">Company:</span>
                    <span class="info-value">{{ $po->vendor->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contact Person:</span>
                    <span class="info-value">{{ $po->vendor->contact_person ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $po->vendor->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $po->vendor->email }}</span>
                </div>
                @if($po->vendor->address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $po->vendor->address }}</span>
                </div>
                @endif
            </div>
            
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th width="40%">Item Description</th>
                        <th width="15%" class="text-center">Quantity</th>
                        <th width="20%" class="text-center">Unit Cost (UGX)</th>
                        <th width="25%" class="text-center">Total (UGX)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $item)
                    <tr>
                        <td>
                            {{ $item->inventoryItem->name ?? 'N/A' }}
                            @if($item->notes)
                                <div class="item-notes">📝 Note: {{ $item->notes }}</div>
                            @endif
                         </td>
                        <td style="text-align: center;">{{ number_format($item->quantity_ordered, 2) }}</td>
                        <td style="text-align: center;">{{ number_format($item->unit_cost, 2) }}</td>
                        <td style="text-align: center;">{{ number_format($item->total_cost, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="totals">
                <div class="totals-row">
                    <span class="totals-label">Subtotal:</span>
                    <span>UGX {{ number_format($po->subtotal, 2) }}</span>
                </div>
                <div class="totals-row">
                    <span class="totals-label">Tax (0%):</span>
                    <span>UGX {{ number_format($po->tax_amount, 2) }}</span>
                </div>
                <div class="grand-total">
                    <span class="totals-label">Grand Total:</span>
                    <span>UGX {{ number_format($po->total_amount, 2) }}</span>
                </div>
            </div>
            
            @if($po->delivery_address || $po->delivery_terms)
            <div class="delivery-box">
                <h4 style="margin-top: 0; color: #0c5460;">🚚 Delivery Information</h4>
                @if($po->delivery_address)
                <p><strong>Delivery Address:</strong> {{ $po->delivery_address }}</p>
                @endif
                @if($po->delivery_terms)
                <p><strong>Delivery Terms:</strong> {{ $po->delivery_terms }}</p>
                @endif
            </div>
            @endif
            
            @if($po->notes)
            <div class="notes-box">
                <h4 style="margin-top: 0; color: #856404;">📋 General Order Notes</h4>
                <p>{{ $po->notes }}</p>
            </div>
            @endif
            
            <div class="attachment-note">
                <strong>📎 Attachment:</strong> A PDF copy of this purchase order is attached to this email. Please print and keep for your records.
            </div>
            
            <p style="margin-top: 30px;">
                Thank you for your prompt attention to this order. Should you have any questions, please contact our procurement department.
            </p>
            
            <p>
                Sincerely,<br>
                <strong>Procurement Department</strong><br>
                PaitoBella Restaurant
            </p>
        </div>
        
        <div class="footer">
            <p>This is a system-generated purchase order. Please contact the procurement department for any queries.</p>
            <p>&copy; {{ date('Y') }} PaitoBella Restaurant. All rights reserved.</p>
            <p><strong>Note:</strong> This email and its attachments may contain confidential information.</p>
        </div>
    </div>
</body>
</html>