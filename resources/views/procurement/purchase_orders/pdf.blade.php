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

        /* SIGNATURE SECTION - SINGLE ROW */
        .signature-section {
            margin-top: 50px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            gap: 30px;
            page-break-inside: avoid;
        }
        .signature-box {
            flex: 1;
            text-align: center;
            width: 33%;
        }
        .signature-label {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            color: #2a5298;
        }
        .signature-line {
            margin-top: 30px;
            padding-top: 5px;
            border-top: 1px solid #333;
            font-size: 11px;
            text-align: center;
        }
        .signature-img {
            max-height: 60px;
            max-width: 160px;
            margin-bottom: 10px;
        }
        .stamp-img {
            max-height: 70px;
            max-width: 130px;
            margin-bottom: 10px;
        }
        .company-logo {
            max-height: 60px;
            max-width: 200px;
        }
        .type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .type-normal {
            background: #d1fae5;
            color: #065f46;
        }
        .type-emergency {
            background: #fee2e2;
            color: #991b1b;
        }
        .sig-name {
            font-size: 11px;
            font-weight: 600;
            margin-top: 5px;
            color: #1e3c72;
        }
        .sig-date {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }

.signature-section {
    margin-top: 50px;
    display: flex;           /* THIS makes them flex horizontally */
    flex-direction: row;     /* Ensures row direction (left to right) */
    justify-content: space-between;
    gap: 30px;
    page-break-inside: avoid;
}
.signature-box {
    flex: 1;                 /* Each takes equal width */
    text-align: center;
    width: 33%;
}

    </style>
</head>
<body>
    <div class="header">
        @php
            // Helper function to get signature base64 (same as show blade)
            function getSignatureBase64Pdf($user) {
                if (!$user || !$user->signature_path) return null;
                $path = $user->signature_path;
                $clean = ltrim($path, '/');
                $clean = preg_replace('#^public/#', '', $clean);
                $fullPath = storage_path('app/public/' . $clean);
                if (file_exists($fullPath)) {
                    return 'data:' . mime_content_type($fullPath) . ';base64,' . base64_encode(file_get_contents($fullPath));
                }
                $publicPath = public_path($clean);
                if (file_exists($publicPath)) {
                    return 'data:' . mime_content_type($publicPath) . ';base64,' . base64_encode(file_get_contents($publicPath));
                }
                return null;
            }

            $logo = \App\Models\BusinessSetting::getLogo();
            $companyName = \App\Models\BusinessSetting::get('company_name', 'PaitoBella Restaurant');
            $companyPhone = \App\Models\BusinessSetting::get('phone', '+256 XXX XXX XXX');
            $companyEmail = \App\Models\BusinessSetting::get('email', 'procurement@patiobella.com');
            $companyAddress = \App\Models\BusinessSetting::get('address', 'Kampala, Uganda');
            $companyStamp = \App\Models\BusinessSetting::getStamp();

            // Logo base64
            $logoBase64 = null;
            if ($logo) {
                $logoPath = public_path(parse_url($logo, PHP_URL_PATH));
                if (file_exists($logoPath)) {
                    $logoMime = mime_content_type($logoPath);
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoBase64 = 'data:' . $logoMime . ';base64,' . $logoData;
                }
            }

            // Stamp base64
            $stampBase64 = null;
            if ($companyStamp) {
                $stampPath = public_path(parse_url($companyStamp, PHP_URL_PATH));
                if (file_exists($stampPath)) {
                    $stampMime = mime_content_type($stampPath);
                    $stampData = base64_encode(file_get_contents($stampPath));
                    $stampBase64 = 'data:' . $stampMime . ';base64,' . $stampData;
                }
            }

            // 1. PREPARED BY (Creator)
            $creator = $po->creator;
            $preparedSignature = null;
            $preparedName = '';
            if ($creator) {
                $preparedName = trim(($creator->first_name ?? '') . ' ' . ($creator->last_name ?? ''));
                $preparedSignature = getSignatureBase64Pdf($creator);
            }
            if (empty($preparedName)) {
                $preparedName = $po->created_by_name ?? 'Procurement Officer';
            }
            $preparedDate = $po->created_at ? $po->created_at->format('d M Y') : date('d M Y');

            // 2. APPROVED BY (Director from LPO)
            $lpo = $po->lpo ?? null;
            $directorApprover = null;
            $directorApprovedAt = null;
            $approverSignature = null;
            $approverName = '';
            $approvalStatus = '';

            if ($lpo && $lpo->approved_by && $lpo->approved_at && $lpo->approvedBy) {
                $directorApprover = $lpo->approvedBy;
                $directorApprovedAt = $lpo->approved_at;
                $approverName = trim(($directorApprover->first_name ?? '') . ' ' . ($directorApprover->last_name ?? ''));
                if (empty($approverName)) $approverName = $directorApprover->name ?? 'Director';
                $approverSignature = getSignatureBase64Pdf($directorApprover);
                $approverDate = \Carbon\Carbon::parse($directorApprovedAt)->format('d M Y');
            } else {
                if ($lpo && $lpo->status == 'pending_director') {
                    $approvalStatus = 'Pending Director Approval';
                } elseif ($lpo && $lpo->status == 'director_rejected') {
                    $approvalStatus = 'REJECTED by Director';
                } else {
                    $approvalStatus = 'Awaiting Director Approval';
                }
                $approverName = 'Director Approval';
                $approverDate = '';
            }
        @endphp

        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="company-logo" alt="Logo">
        @else
            <h1>PURCHASE ORDER</h1>
        @endif
        <h2>{{ $po->po_number }}</h2>
        <div class="company-info">
            <strong>{{ $companyName }}</strong><br>
            {{ $companyAddress }}<br>
            Phone: {{ $companyPhone }} | Email: {{ $companyEmail }}
        </div>
    </div>

    <div class="po-title">
        <h3>OFFICIAL PURCHASE ORDER</h3>
    </div>

    {{-- Order Information --}}
    <div class="section">
        <div class="section-title">ORDER INFORMATION</div>
        <table class="info-grid">
            <tr><td class="info-label">PO Number:</td><td class="info-value">{{ $po->po_number }}</td>
                <td class="info-label">PO Type:</td><td class="info-value"><span class="type-badge {{ $po->type == 'emergency' ? 'type-emergency' : 'type-normal' }}">{{ strtoupper($po->type) }}</span></td>
            </tr>
            <tr><td class="info-label">Order Date:</td><td class="info-value">{{ $po->po_date->format('F d, Y') }}</td>
                <td class="info-label">Payment Method:</td><td class="info-value">{{ ucfirst(str_replace('_', ' ', $po->payment_method ?? 'credit')) }}</td>
            </tr>
            <tr><td class="info-label">Status:</td><td class="info-value">{{ ucfirst($po->status) }}</td>
                <td class="info-label">Expected Delivery:</td><td class="info-value">{{ $po->expected_delivery_date ? date('F d, Y', strtotime($po->expected_delivery_date)) : 'Not specified' }}</td>
            </tr>
        </table>
    </div>

    {{-- Vendor Information --}}
    <div class="section">
        <div class="section-title">VENDOR INFORMATION</div>
        <table class="info-grid">
            <tr><td class="info-label">Company Name:</td><td class="info-value">{{ $po->vendor->name }}</td></tr>
            <tr><td class="info-label">Contact Person:</td><td class="info-value">{{ $po->vendor->contact_person ?? 'N/A' }}</td></tr>
            <tr><td class="info-label">Phone:</td><td class="info-value">{{ $po->vendor->phone ?? 'N/A' }}</td></tr>
            <tr><td class="info-label">Email:</td><td class="info-value">{{ $po->vendor->email }}</td></tr>
            @if($po->vendor->address)<tr><td class="info-label">Address:</td><td class="info-value">{{ $po->vendor->address }}</td></tr>@endif
        </table>
    </div>

    {{-- Delivery Information --}}
    @if($po->delivery_address || $po->delivery_terms)
    <div class="delivery-box">
        <strong>🚚 DELIVERY INFORMATION</strong><br>
        @if($po->delivery_address)<strong>Delivery Address:</strong> {{ $po->delivery_address }}<br>@endif
        @if($po->delivery_terms)<strong>Delivery Terms:</strong> {{ $po->delivery_terms }}@endif
    </div>
    @endif

    {{-- Order Items --}}
    <div class="section">
        <div class="section-title">ORDER ITEMS</div>
        <table>
            <thead>
                <tr><th width="40%">Item Description</th><th width="15%" class="text-center">Quantity</th><th width="20%" class="text-right">Unit Cost (UGX)</th><th width="25%" class="text-right">Total (UGX)</th></tr>
            </thead>
            <tbody>
                @php $itemCounter = 1; $subtotal = 0; @endphp
                @foreach($po->items as $item)
                @php $total = $item->quantity_ordered * $item->unit_cost; $subtotal += $total; @endphp
                <tr>
                    <td><strong>{{ $itemCounter++ }}.</strong> {{ $item->inventoryItem ? $item->inventoryItem->name : 'N/A' }}
                        @if($item->inventoryItem && $item->inventoryItem->item_code)<br><span style="font-size: 10px; color: #666;">Code: {{ $item->inventoryItem->item_code }}</span>@endif
                        @if($item->notes)<div class="item-notes">📝 Note: {{ $item->notes }}</div>@endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity_ordered, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="text-right"><strong>Subtotal:</strong></td><td class="text-right"><strong>{{ number_format($subtotal, 2) }}</strong></td></tr>
                @if($po->vat_rate > 0)<tr><td colspan="3" class="text-right">VAT ({{ $po->vat_rate }}%):</td><td class="text-right">{{ number_format($po->vat_amount, 2) }}</td></tr>@endif
                <tr style="border-top: 2px solid #333;"><td colspan="3" class="text-right"><strong>GRAND TOTAL:</strong></td><td class="text-right"><strong style="color: #2a5298;">UGX {{ number_format($po->total_amount, 2) }}</strong></td></tr>
            </tfoot>
        </table>
    </div>

    {{-- General Order Notes --}}
    @if($po->notes)
    <div class="notes-box">
        <strong>📌 GENERAL ORDER NOTES</strong><br>
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

{{-- ============================================== --}}
{{--   SIGNATURE SECTION - SINGLE HORIZONTAL ROW   --}}
{{--   RECEIVED BY | VERIFIED BY | COMPANY STAMP   --}}
{{-- ============================================== --}}
<div style="display: flex; flex-direction: row; justify-content: space-between; width: 100%; margin-top: 40px; gap: 20px;">
    {{-- 1. RECEIVED BY (PREPARED BY) --}}
    <div style="flex: 1; text-align: center;">
        <div style="font-weight: bold; font-size: 12px; margin-bottom: 10px; color: #2a5298;">RECEIVED BY</div>
        @if($preparedSignature)
            <img src="{{ $preparedSignature }}" style="max-height: 50px; max-width: 140px; margin: 0 auto;">
        @else
            <div style="height: 50px;"></div>
        @endif
        <div style="border-top: 1px solid #333; margin-top: 15px; padding-top: 5px;"></div>
        <div style="font-size: 11px; font-weight: 600; margin-top: 5px;">{{ $preparedName }}</div>
        <div style="font-size: 9px; color: #666;">{{ $preparedDate }}</div>
    </div>

    {{-- 2. VERIFIED BY (APPROVED BY DIRECTOR) --}}
    <div style="flex: 1; text-align: center;">
        <div style="font-weight: bold; font-size: 12px; margin-bottom: 10px; color: #2a5298;">VERIFIED BY</div>
        @if($approverSignature)
            <img src="{{ $approverSignature }}" style="max-height: 50px; max-width: 140px; margin: 0 auto;">
        @else
            <div style="height: 50px;"></div>
        @endif
        <div style="border-top: 1px solid #333; margin-top: 15px; padding-top: 5px;"></div>
        <div style="font-size: 11px; font-weight: 600; margin-top: 5px;">{{ $approverName }}</div>
        <div style="font-size: 9px; color: #666;">
            @if($approverDate)
                {{ $approverDate }}
            @elseif($approvalStatus)
                {{ $approvalStatus }}
            @else
                Pending
            @endif
        </div>
    </div>

    {{-- 3. COMPANY STAMP --}}
    <div style="flex: 1; text-align: center;">
        <div style="font-weight: bold; font-size: 12px; margin-bottom: 10px; color: #2a5298;">COMPANY STAMP</div>
        @if($stampBase64)
            <img src="{{ $stampBase64 }}" style="max-height: 60px; max-width: 120px; margin: 0 auto;">
        @else
            <div style="height: 50px;"></div>
        @endif
        <div style="border-top: 1px solid #333; margin-top: 15px; padding-top: 5px;"></div>
        <div style="font-size: 11px; font-weight: 600; margin-top: 5px;">Authorised Signatory</div>
        <div style="font-size: 9px; color: #666;">{{ $preparedDate }}</div>
    </div>
</div>
    <div class="footer">
        <p>This is a computer-generated document. For any queries, please contact procurement department.</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
