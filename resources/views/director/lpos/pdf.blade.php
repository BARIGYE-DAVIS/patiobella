<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Local Purchase Order - {{ $lpo->lpo_number }}</title>
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
            border-bottom: 3px solid #1e3c72;
        }
        .header h1 {
            color: #1e3c72;
            margin: 0;
            font-size: 28px;
        }
        .header h2 {
            color: #666;
            margin: 5px 0 0;
            font-size: 16px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }
        .title-box {
            text-align: center;
            margin: 20px 0;
        }
        .title-box h3 {
            background: #1e3c72;
            color: white;
            display: inline-block;
            padding: 8px 25px;
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
            border-left: 4px solid #1e3c72;
            font-weight: bold;
            font-size: 14px;
            color: #1e3c72;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
            font-size: 12px;
        }
        .info-label {
            font-weight: bold;
            width: 140px;
            color: #555;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #1e3c72;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
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
            font-size: 12px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
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
        .clearfix {
            overflow: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LOCAL PURCHASE ORDER</h1>
        <h2>{{ $lpo->lpo_number }}</h2>
        <div class="company-info">
            <strong>PaitoBella Restaurant</strong><br>
            Kampala, Uganda<br>
            Phone: +256 XXX XXX XXX | Email: procurement@patiobella.com
        </div>
    </div>

    <div class="title-box">
        <h3>OFFICIAL LPO DOCUMENT</h3>
    </div>

    {{-- LPO Information --}}
    <div class="section">
        <div class="section-title">LPO INFORMATION</div>
        <table class="info-table">
            <tr>
                <td class="info-label">LPO Number:</td>
                <td>{{ $lpo->lpo_number }}</td>
                <td class="info-label">LPO Date:</td>
                <td>{{ $lpo->lpo_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Requisition #:</td>
                <td>{{ $lpo->requisition->requisition_number ?? 'N/A' }}</td>
                <td class="info-label">Expected Delivery:</td>
                <td>{{ $lpo->expected_delivery_date ? date('F d, Y', strtotime($lpo->expected_delivery_date)) : 'Not specified' }}</td>
            </tr>
            <tr>
                <td class="info-label">Delivery Address:</td>
                <td colspan="3">{{ $lpo->delivery_address ?: 'Not specified' }}</td>
            </tr>
            <tr>
                <td class="info-label">Delivery Instructions:</td>
                <td colspan="3">{{ $lpo->delivery_instructions ?: 'Not specified' }}</td>
            </tr>
        </table>
    </div>

    {{-- Vendor Information --}}
    <div class="section">
        <div class="section-title">VENDOR INFORMATION</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Vendor Name:</td>
                <td>{{ $lpo->vendor->name ?? 'N/A' }}</td>
                <td class="info-label">Contact Person:</td>
                <td>{{ $lpo->vendor->contact_person ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Phone:</td>
                <td>{{ $lpo->vendor->phone ?? 'N/A' }}</td>
                <td class="info-label">Email:</td>
                <td>{{ $lpo->vendor->email ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- Items Table --}}
    <div class="section">
        <div class="section-title">ORDER ITEMS</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="45%">Item Description</th>
                    <th width="15%" class="text-center">Metrics</th>
                    <th width="15%" class="text-right">Quantity</th>
                    <th width="25%" class="text-right">Unit Cost (UGX)</th>
                    <th width="25%" class="text-right">Total (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @foreach($lpo->items as $item)
                <tr>
                    <td>
                        <strong>{{ $counter++ }}.</strong> {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                        @if($item->inventoryItem && $item->inventoryItem->item_code)
                            <br><span style="font-size: 10px; color: #666;">Code: {{ $item->inventoryItem->item_code }}</span>
                        @endif
                        @if($item->notes)
                            <br><span style="font-size: 10px; color: #666;">Note: {{ $item->notes }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->metrics ?: '—' }}</td>
                    <td class="text-right">{{ number_format($item->quantity_approved, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="totals">
        <table class="totals-table">
            <tr>
                <td class="text-right">Subtotal:</td>
                <td class="text-right">UGX {{ number_format($lpo->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right">Tax (0%):</td>
                <td class="text-right">UGX {{ number_format($lpo->tax_amount, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="text-right"><strong>GRAND TOTAL:</strong></td>
                <td class="text-right"><strong>UGX {{ number_format($lpo->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="clearfix"></div>

    {{-- Notes --}}
    @if($lpo->notes)
    <div style="margin-top: 20px; padding: 12px; background-color: #fef3c7; border-left: 4px solid #f59e0b;">
        <strong style="font-size: 12px;">📝 NOTES:</strong>
        <p style="font-size: 11px; margin: 5px 0 0;">{{ $lpo->notes }}</p>
    </div>
    @endif

    {{-- Terms --}}
    <div style="margin-top: 20px;">
        <div class="section-title">TERMS & CONDITIONS</div>
        <ul style="font-size: 10px; color: #555; margin: 0; padding-left: 20px;">
            <li>Please deliver the items as per the specified delivery date and address.</li>
            <li>All items must meet the quality standards and specifications as requested.</li>
            <li>Invoice must reference this LPO Number for payment processing.</li>
            <li>Payment will be processed within 30 days of receipt of correct invoice and delivery.</li>
            <li>Any discrepancies must be reported within 3 days of order receipt.</li>
        </ul>
    </div>

    {{-- Signatures --}}
    <div class="signature">
        <table style="width: 100%; margin-top: 40px;">
            <tr>
                <td style="text-align: center;">
                    <div class="signature-line">
                        Authorized Signature
                    </div>
                    <div style="margin-top: 10px; font-size: 10px;">(Procurement Department)</div>
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
        <p>This is a computer-generated Local Purchase Order. Please contact procurement department for any queries.</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>