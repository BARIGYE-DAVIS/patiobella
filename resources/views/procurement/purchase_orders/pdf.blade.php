<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $po->po_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2a5298;
        }
        .header h1 {
            color: #2a5298;
            margin: 0;
            font-size: 32px;
        }
        .header h2 {
            color: #666;
            margin: 5px 0 0;
            font-size: 18px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }
        .po-title {
            text-align: center;
            margin: 20px 0;
        }
        .po-title h3 {
            background: #2a5298;
            color: white;
            display: inline-block;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 16px;
            margin: 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #e8f4f8;
            padding: 8px 12px;
            margin-bottom: 15px;
            border-left: 4px solid #2a5298;
            font-weight: bold;
            color: #1e3c72;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-grid td {
            padding: 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 140px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #2a5298;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .item-notes {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
            font-style: italic;
        }
        .totals {
            margin-top: 20px;
            width: 300px;
            float: right;
        }
        .totals-table {
            width: 100%;
        }
        .totals-table td {
            padding: 5px;
            border: none;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
            margin-top: 5px;
        }
        .notes-box {
            background-color: #fff3cd;
            padding: 12px;
            border-left: 4px solid #ffc107;
            margin-top: 20px;
            clear: both;
            font-size: 11px;
        }
        .delivery-box {
            background-color: #d1ecf1;
            padding: 12px;
            border-left: 4px solid #17a2b8;
            margin-top: 20px;
            clear: both;
            font-size: 11px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
            padding: 10px;
            border-top: 1px solid #eee;
        }
        .clearfix {
            overflow: auto;
        }
        .signature {
            margin-top: 50px;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 5px;
            font-size: 11px;
            text-align: center;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PURCHASE ORDER</h1>
        <h2>{{ $po->po_number }}</h2>
        <div class="company-info">
            <strong>PaitoBella Restaurant</strong><br>
            Kampala, Uganda<br>
            Phone: +256 XXX XXX XXX | Email: procurement@patiobella.com
        </div>
    </div>

    <div class="po-title">
        <h3>OFFICIAL PURCHASE ORDER</h3>
    </div>

    {{-- Order Information --}}
    <div class="section">
        <div class="section-title">ORDER INFORMATION</div>
        <table class="info-grid">
            <tr>
                <td class="info-label">PO Number:</td>
                <td class="info-value">{{ $po->po_number }}</td>
                <td class="info-label">Order Date:</td>
                <td class="info-value">{{ $po->po_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Status:</td>
                <td class="info-value">{{ ucfirst($po->status) }}</td>
                <td class="info-label">Expected Delivery:</td>
                <td class="info-value">{{ $po->expected_delivery_date ? date('F d, Y', strtotime($po->expected_delivery_date)) : 'Not specified' }}</td>
            </tr>
            <tr>
                <td class="info-label">Payment Terms:</td>
                <td class="info-value">Upon Delivery</td>
                <td class="info-label">Currency:</td>
                <td class="info-value">Ugandan Shilling (UGX)</td>
            </tr>
        </table>
    </div>

    {{-- Vendor Information --}}
    <div class="section">
        <div class="section-title">VENDOR INFORMATION</div>
        <table class="info-grid">
            <tr>
                <td class="info-label">Company Name:</td>
                <td class="info-value">{{ $po->vendor->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Contact Person:</td>
                <td class="info-value">{{ $po->vendor->contact_person ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Phone:</td>
                <td class="info-value">{{ $po->vendor->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Email:</td>
                <td class="info-value">{{ $po->vendor->email }}</td>
            </tr>
            @if($po->vendor->address)
            <tr>
                <td class="info-label">Address:</td>
                <td class="info-value">{{ $po->vendor->address }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Delivery Information --}}
    @if($po->delivery_address || $po->delivery_terms)
    <div class="delivery-box">
        <strong>🚚 DELIVERY INFORMATION</strong><br>
        @if($po->delivery_address)
        <strong>Delivery Address:</strong> {{ $po->delivery_address }}<br>
        @endif
        @if($po->delivery_terms)
        <strong>Delivery Terms:</strong> {{ $po->delivery_terms }}
        @endif
    </div>
    @endif

    {{-- Order Items --}}
    <div class="section">
        <div class="section-title">ORDER ITEMS</div>
        <table>
            <thead>
                <tr>
                    <th width="40%">Item Description</th>
                    <th width="15%" class="text-center">Quantity</th>
                    <th width="20%" class="text-right">Unit Cost (UGX)</th>
                    <th width="25%" class="text-right">Total (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @php $itemCounter = 1; @endphp
                @foreach($po->items as $item)
                <tr>
                    <td>
                        <strong>{{ $itemCounter++ }}.</strong> {{ $item->inventoryItem->name ?? 'N/A' }}
                        @if($item->notes)
                            <div class="item-notes">📝 Note: {{ $item->notes }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity_ordered, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right"><strong>{{ number_format($po->subtotal, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">Tax (0%):</td>
                    <td class="text-right">{{ number_format($po->tax_amount, 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #333;">
                    <td colspan="3" class="text-right"><strong>GRAND TOTAL:</strong></td>
                    <td class="text-right"><strong style="color: #2a5298;">UGX {{ number_format($po->total_amount, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- General Order Notes --}}
    @if($po->notes)
    <div class="notes-box">
        <strong>📋 GENERAL ORDER NOTES</strong><br>
        {{ $po->notes }}
    </div>
    @endif

    {{-- Terms and Conditions --}}
    <div class="section">
        <div class="section-title">TERMS & CONDITIONS</div>
        <ul style="font-size: 11px; color: #555; margin: 0; padding-left: 20px;">
            <li>Please deliver the items as per the specified delivery date and address.</li>
            <li>All items must meet the quality standards and specifications as requested.</li>
            <li>Invoice must reference this Purchase Order Number for payment processing.</li>
            <li>Payment will be processed within 30 days of receipt of correct invoice and delivery.</li>
            <li>Any discrepancies must be reported within 3 days of order receipt.</li>
            <li>This order is subject to our standard terms and conditions of purchase.</li>
        </ul>
    </div>

    {{-- Signature Section --}}
    <div class="signature">
        <table style="width: 100%; margin-top: 40px;">
            <tr>
                <td style="text-align: center;">
                    <div class="signature-line">
                        Authorized Signature
                    </div>
                    <div style="margin-top: 10px; font-size: 11px;">(Procurement Department)</div>
                </td>
                <td style="text-align: center;">
                    <div class="signature-line">
                        Company Stamp
                    </div>
                </td>
                <td style="text-align: center;">
                    <div class="signature-line">
                        Date: {{ date('Y-m-d') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated document and requires no signature. For any queries, please contact procurement department.</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>