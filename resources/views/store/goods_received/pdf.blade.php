<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GRN {{ $grn->grn_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1e3c72;
        }
        .company-logo {
            max-height: 50px;
            width: auto;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #1e3c72;
            font-size: 18px;
            margin: 5px 0;
        }
        .header h2 {
            color: #666;
            font-size: 14px;
        }
        .company-info {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .status-draft { background: #fef3c7; color: #92400e; }
        .status-inventory_updated { background: #d1fae5; color: #065f46; }
        .status-verified { background: #dbeafe; color: #1e40af; }

        /* Section Title */
        .section-title {
            background-color: #e8f4f8;
            padding: 6px 10px;
            margin: 15px 0 10px 0;
            border-left: 4px solid #1e3c72;
            font-weight: bold;
            font-size: 11px;
            color: #1e3c72;
        }

        /* Info Grid */
        .info-grid {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-grid td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 130px;
            color: #555;
            font-size: 10px;
        }
        .info-value {
            color: #333;
            font-size: 10px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #1e3c72;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
            vertical-align: top;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Totals */
        .totals-box {
            margin-top: 15px;
            width: 280px;
            float: right;
        }
        .totals-table {
            width: 100%;
        }
        .totals-table td {
            padding: 4px 0;
            font-size: 10px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #333;
            margin-top: 5px;
        }

        /* Notes Box */
        .notes-box {
            background-color: #fff3cd;
            padding: 8px;
            border-left: 3px solid #ffc107;
            margin: 15px 0;
            font-size: 9px;
            clear: both;
        }

        /* Signatures */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 5px;
            width: 180px;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-img {
            max-height: 50px;
            max-width: 150px;
            margin-bottom: 5px;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #999;
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .clearfix { overflow: auto; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        @php
            $logo = \App\Models\BusinessSetting::getLogo();
            $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
            $companyPhone = \App\Models\BusinessSetting::get('phone', '');
            $companyEmail = \App\Models\BusinessSetting::get('email', '');
            $companyAddress = \App\Models\BusinessSetting::get('address', '');
            $companyStamp = \App\Models\BusinessSetting::getStamp();

            $logoBase64 = null;
            if ($logo) {
                $logoPath = public_path(parse_url($logo, PHP_URL_PATH));
                if (file_exists($logoPath)) {
                    $logoMime = mime_content_type($logoPath);
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoBase64 = 'data:' . $logoMime . ';base64,' . $logoData;
                }
            }
        @endphp

        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="company-logo" alt="Logo">
        @endif
        <h1>GOODS RECEIVED NOTE</h1>
        <h2>{{ $grn->grn_number }}</h2>
        <div class="company-info">
            {{ $companyName }}<br>
            {{ $companyAddress }}<br>
            @if($companyPhone) Phone: {{ $companyPhone }} @endif
            @if($companyEmail) | Email: {{ $companyEmail }} @endif
        </div>
    </div>

    {{-- Status --}}
    @php
        $statusClass = match($grn->status) {
            'draft' => 'status-draft',
            'inventory_updated' => 'status-inventory_updated',
            'verified' => 'status-verified',
            default => 'status-draft',
        };
        $statusText = match($grn->status) {
            'draft' => 'DRAFT',
            'inventory_updated' => 'INVENTORY UPDATED',
            'verified' => 'VERIFIED',
            default => ucfirst($grn->status),
        };
    @endphp
    <div class="status-badge {{ $statusClass }}">{{ $statusText }}</div>

    {{-- GRN Information --}}
    <table class="info-grid">
        <tr>
            <td class="info-label">GRN Number:</td><td class="info-value">{{ $grn->grn_number }}</td>
            <td class="info-label">PO Reference:</td><td class="info-value">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Received Date:</td><td class="info-value">{{ $grn->received_date->format('d M Y') }}</td>
            <td class="info-label">Delivery Note #:</td><td class="info-value">{{ $grn->delivery_note_number ?? '—' }}</td>
        </tr>
        <tr>
            <td class="info-label">Received By:</td><td class="info-value">{{ $grn->received_by ?? '—' }}</td>
            <td class="info-label">Delivered By:</td><td class="info-value">{{ $grn->delivered_by_name ?? '—' }}</td>
        </tr>
    </table>

    {{-- Vendor Information --}}
    <div class="section-title">VENDOR INFORMATION</div>
    <table class="info-grid">
        <tr>
            <td class="info-label">Vendor Name:</td><td class="info-value">{{ $grn->vendor->name ?? 'N/A' }}</td>
            <td class="info-label">Contact Person:</td><td class="info-value">{{ $grn->vendor->contact_person ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Phone:</td><td class="info-value">{{ $grn->vendor->phone ?? 'N/A' }}</td>
            <td class="info-label">Email:</td><td class="info-value">{{ $grn->vendor->email ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Notes --}}
    @if($grn->notes)
    <div class="notes-box">
        <strong>📝 NOTES:</strong><br>
        {{ $grn->notes }}
    </div>
    @endif

    {{-- Items Table --}}
    <div class="section-title">RECEIVED ITEMS</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:35%">Item</th>
                <th style="width:10%" class="text-center">Ordered</th>
                <th style="width:10%" class="text-center">Received</th>
                <th style="width:10%" class="text-center">Accepted</th>
                <th style="width:10%" class="text-center">Rejected</th>
                <th style="width:12%" class="text-right">Unit Cost</th>
                <th style="width:13%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @foreach($grn->items as $item)
            @php
                $total = $item->quantity_accepted * $item->unit_cost;
                $subtotal += $total;
            @endphp
            <tr>
                <td>
                    {{ $item->inventoryItem->name ?? 'N/A' }}
                    @if($item->inventoryItem && $item->inventoryItem->item_code)
                        <br><span style="font-size:8px;color:#666;">Code: {{ $item->inventoryItem->item_code }}</span>
                    @endif
                </td>
                <td class="text-center">{{ number_format($item->quantity_ordered, 2) }}</td>
                <td class="text-center">{{ number_format($item->quantity_received, 2) }}</td>
                <td class="text-center"><strong>{{ number_format($item->quantity_accepted, 2) }}</strong></td>
                <td class="text-center">
                    @if($item->quantity_rejected > 0)
                        {{ number_format($item->quantity_rejected, 2) }}
                    @else
                        —
                    @endif
                </td>
                <td class="text-right">UGX {{ number_format($item->unit_cost, 2) }}</td>
                <td class="text-right">UGX {{ number_format($total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-box">
        <table class="totals-table">
            <tr>
                <td style="text-align:right">Subtotal:</td>
                <td style="text-align:right">UGX {{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($grn->vat_rate > 0)
            <tr>
                <td style="text-align:right">VAT ({{ $grn->vat_rate }}%):</td>
                <td style="text-align:right">UGX {{ number_format($grn->vat_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td style="text-align:right"><strong>TOTAL PAYABLE:</strong></td>
                <td style="text-align:right"><strong>UGX {{ number_format($subtotal + ($grn->vat_amount ?? 0), 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="clearfix"></div>

    {{-- Signatures --}}
    <div class="signature-section">
        <div class="signature-box">
            @php $receivedBy = $grn->receivedByUser; @endphp
            @if($receivedBy && $receivedBy->signature_url)
                @php
                    $sigPath = storage_path('app/public/' . $receivedBy->signature_path);
                    if (file_exists($sigPath)) {
                        $sigMime = mime_content_type($sigPath);
                        $sigData = base64_encode(file_get_contents($sigPath));
                        $sigBase64 = 'data:' . $sigMime . ';base64,' . $sigData;
                    } else {
                        $sigBase64 = $receivedBy->signature_url;
                    }
                @endphp
                @if(isset($sigBase64))
                    <img src="{{ $sigBase64 }}" class="signature-img" alt="Signature">
                @endif
            @else
                <div style="height: 50px;"></div>
            @endif
            <div class="signature-line"></div>
            <div>Received By</div>
            <div style="font-size:9px; margin-top:4px;">{{ $grn->received_by ?? '' }}</div>
            <div style="font-size:8px; color:#666;">{{ $grn->received_date->format('d M Y') }}</div>
        </div>
        <div class="signature-box">
            @if($companyStamp)
                @php
                    $stampPath = public_path(parse_url($companyStamp, PHP_URL_PATH));
                    if (file_exists($stampPath)) {
                        $stampMime = mime_content_type($stampPath);
                        $stampData = base64_encode(file_get_contents($stampPath));
                        $stampBase64 = 'data:' . $stampMime . ';base64,' . $stampData;
                    } else {
                        $stampBase64 = $companyStamp;
                    }
                @endphp
                @if(isset($stampBase64))
                    <img src="{{ $stampBase64 }}" class="signature-img" alt="Stamp">
                @endif
            @else
                <div style="height: 50px;"></div>
            @endif
            <div class="signature-line"></div>
            <div>Company Stamp</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>This is a computer generated document. Valid without signature.</p>
        <p>{{ $companyName }} - Procurement Department</p>
    </div>

</body>
</html>
