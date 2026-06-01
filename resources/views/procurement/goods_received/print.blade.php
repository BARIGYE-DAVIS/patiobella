<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRN {{ $grn->grn_number }} — Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #f8fafc;
        }

        /* ── Print Button Bar (hidden when printing) ── */
        .print-bar {
            background: #1e293b;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .print-bar span {
            color: #94a3b8;
            font-size: 13px;
        }
        .print-bar strong { color: #fff; }
        .btn-print {
            background: #3b82f6;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #2563eb; }
        .btn-back {
            background: #334155;
            color: #cbd5e1;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-right: 8px;
        }
        .btn-back:hover { background: #475569; }

        /* ── Document ── */
        .document {
            max-width: 900px;
            margin: 24px auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        /* ── Header ── */
        .doc-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 28px 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .doc-header-left .grn-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #93c5fd;
            margin-bottom: 4px;
        }
        .doc-header-left .grn-number {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }
        .doc-header-left .grn-meta {
            margin-top: 6px;
            font-size: 11px;
            color: #94a3b8;
        }
        .doc-header-right {
            text-align: right;
        }
        .doc-header-right img.logo {
            max-height: 60px;
            max-width: 160px;
            object-fit: contain;
        }
        .doc-header-right .company-placeholder {
            color: #64748b;
            font-size: 11px;
            font-style: italic;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }
        .status-badge .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }
        .status-verified   { background: #f3e8ff; color: #7c3aed; }
        .status-completed  { background: #dcfce7; color: #16a34a; }
        .status-inventory  { background: #dbeafe; color: #1d4ed8; }
        .status-draft      { background: #fef9c3; color: #a16207; }
        .status-default    { background: #f1f5f9; color: #475569; }

        /* ── Body Padding ── */
        .doc-body { padding: 28px 32px; }

        /* ── Section Title ── */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #e2e8f0;
        }

        /* ── Info Grid ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .info-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
        .info-cell {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
        }
        .info-cell:nth-child(2n) { border-right: none; }
        .info-grid-3 .info-cell:nth-child(2n) { border-right: 1px solid #f1f5f9; }
        .info-grid-3 .info-cell:nth-child(3n) { border-right: none; }
        .info-cell .label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 3px;
        }
        .info-cell .value {
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
        }
        .info-cell .value.mono { font-family: 'Courier New', monospace; color: #1d4ed8; }
        .info-cell .value.muted { color: #64748b; font-weight: 400; }

        /* ── KPI Row ── */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .kpi-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            position: relative;
            overflow: hidden;
        }
        .kpi-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
        }
        .kpi-blue::after   { background: #3b82f6; }
        .kpi-green::after  { background: #22c55e; }
        .kpi-red::after    { background: #ef4444; }
        .kpi-amber::after  { background: #f59e0b; }
        .kpi-card .kpi-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .kpi-card .kpi-value {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
        }
        .kpi-card .kpi-value.green { color: #16a34a; }
        .kpi-card .kpi-value.red   { color: #dc2626; }
        .kpi-card .kpi-value.amber { color: #d97706; font-size: 15px; }
        .kpi-card .kpi-sub {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 11px;
        }
        .items-table thead tr {
            background: #f8fafc;
        }
        .items-table thead th {
            padding: 9px 10px;
            text-align: left;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        .items-table thead th.right { text-align: right; }
        .items-table thead th.center { text-align: center; }
        .items-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }
        .items-table tbody tr:last-child { border-bottom: none; }
        .items-table tbody tr:hover { background: #f8fafc; }
        .items-table td {
            padding: 9px 10px;
            vertical-align: top;
        }
        .items-table td.right { text-align: right; font-family: 'Courier New', monospace; }
        .items-table td.center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-blue   { background: #eff6ff; color: #1d4ed8; }
        .badge-green  { background: #f0fdf4; color: #15803d; }
        .badge-emerald{ background: #ecfdf5; color: #065f46; }
        .badge-red    { background: #fef2f2; color: #b91c1c; }
        .badge-gray   { background: #f8fafc; color: #64748b; }
        .item-name    { font-weight: 600; color: #1e293b; }
        .item-code    { font-size: 10px; color: #94a3b8; margin-top: 2px; }
        .item-pack    { font-size: 10px; color: #3b82f6; margin-top: 2px; }
        .rejection-reason { font-size: 10px; color: #dc2626; background: #fef2f2; padding: 2px 6px; border-radius: 4px; }

        /* ── Table Container ── */
        .table-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 0;
        }

        /* ── Totals Footer ── */
        .totals-footer {
            background: linear-gradient(to right, #f0fdf4, #ecfdf5);
            border-top: 1px solid #bbf7d0;
            padding: 14px 16px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 28px;
        }
        .total-block { text-align: right; }
        .total-block .total-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            margin-bottom: 2px;
        }
        .total-block .total-label.blue { color: #3b82f6; }
        .total-block .total-value {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }
        .total-block .total-value.emerald { color: #059669; font-size: 15px; }
        .total-block .total-value.blue    { color: #2563eb; }
        .total-divider {
            width: 1px;
            height: 36px;
            background: #86efac;
        }

        /* ── Financial Summary ── */
        .fin-summary {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .fin-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .fin-row:last-child { border-bottom: none; }
        .fin-row.total-row {
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }
        .fin-row .fin-label { font-size: 11px; color: #64748b; font-weight: 600; }
        .fin-row .fin-label.blue { color: #2563eb; }
        .fin-row .fin-label.bold { color: #1e293b; font-weight: 700; font-size: 12px; text-transform: uppercase; }
        .fin-row .fin-value { font-family: 'Courier New', monospace; font-size: 12px; font-weight: 700; color: #1e293b; }
        .fin-row .fin-value.blue   { color: #2563eb; }
        .fin-row .fin-value.emerald{ color: #059669; font-size: 14px; }

        /* ── Signatures ── */
        .sig-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 0;
        }
        .sig-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 12px;
            text-align: center;
        }
        .sig-box.verified-box {
            border-color: #d8b4fe;
            background: #faf5ff;
        }
        .sig-box .sig-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        .sig-box.verified-box .sig-title { color: #a855f7; }
        .sig-img-wrap {
            height: 56px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: 8px;
        }
        .sig-img-wrap img {
            max-height: 52px;
            max-width: 140px;
            object-fit: contain;
        }
        .sig-img-wrap .no-sig {
            font-size: 9px;
            color: #cbd5e1;
            font-style: italic;
        }
        .sig-line {
            width: 100%;
            border-top: 1px solid #94a3b8;
            margin: 0 auto 6px;
        }
        .sig-line.purple { border-color: #a855f7; }
        .sig-name {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .sig-name.purple { color: #7c3aed; }
        .sig-date {
            font-size: 10px;
            color: #94a3b8;
        }
        .sig-date.purple { color: #a78bfa; }
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-top: 5px;
            background: #f3e8ff;
            color: #7c3aed;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
        }
        .pending-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            background: #fefce8;
            border: 1px dashed #fde047;
            border-radius: 6px;
            font-size: 10px;
            color: #a16207;
            font-weight: 500;
        }

        /* ── Notes ── */
        .notes-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 11px;
            color: #475569;
            font-style: italic;
        }

        /* ── Spacer ── */
        .mb-6 { margin-bottom: 24px; }
        .mb-4 { margin-bottom: 16px; }

        /* ── Print Styles ── */
        @media print {
            body { background: #fff; font-size: 11px; }
            .print-bar { display: none !important; }
            .document {
                margin: 0;
                border: none;
                border-radius: 0;
                box-shadow: none;
                max-width: 100%;
            }
            .doc-body { padding: 20px 24px; }
            .doc-header { padding: 20px 24px; }
            .kpi-row { gap: 8px; }
            .sig-grid { gap: 10px; }
            .items-table { font-size: 10px; }
            @page {
                size: A4;
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body>

@php
    use App\Models\BusinessSetting;

    // ── Logo ──────────────────────────────────────────────────────────────────
    $companyLogoB64 = null;
    $rawLogo = BusinessSetting::getLogo();
    if ($rawLogo) {
        $logoPath = public_path(parse_url($rawLogo, PHP_URL_PATH));
        if (file_exists($logoPath)) {
            $mime = mime_content_type($logoPath);
            $companyLogoB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            $companyLogoB64 = $rawLogo;
        }
    }

    // ── Stamp ─────────────────────────────────────────────────────────────────
    $companyStampB64 = null;
    $rawStamp = BusinessSetting::getStamp();
    if ($rawStamp) {
        $stampPath = public_path(parse_url($rawStamp, PHP_URL_PATH));
        if (file_exists($stampPath)) {
            $mime = mime_content_type($stampPath);
            $companyStampB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($stampPath));
        } else {
            $companyStampB64 = $rawStamp;
        }
    }

    // ── Received By Signature ─────────────────────────────────────────────────
    $receivedByUser = $grn->receivedByUser ?? null;
    $receivedBySignatureB64 = null;
    if ($receivedByUser && $receivedByUser->signature_path) {
        $sigPath = public_path(parse_url(asset($receivedByUser->signature_path), PHP_URL_PATH));
        if (file_exists($sigPath)) {
            $mime = mime_content_type($sigPath);
            $receivedBySignatureB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sigPath));
        } else {
            $sigPath2 = storage_path('app/public/' . ltrim($receivedByUser->signature_path, '/'));
            if (file_exists($sigPath2)) {
                $mime = mime_content_type($sigPath2);
                $receivedBySignatureB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sigPath2));
            }
        }
    }

    // ── Verified By Signature ─────────────────────────────────────────────────
    $verifiedByUser = $grn->verifiedBy ?? null;
    $verifiedBySignatureB64 = null;
    if ($verifiedByUser && $verifiedByUser->signature_path) {
        $sigPath = public_path(parse_url(asset($verifiedByUser->signature_path), PHP_URL_PATH));
        if (file_exists($sigPath)) {
            $mime = mime_content_type($sigPath);
            $verifiedBySignatureB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sigPath));
        } else {
            $sigPath2 = storage_path('app/public/' . ltrim($verifiedByUser->signature_path, '/'));
            if (file_exists($sigPath2)) {
                $mime = mime_content_type($sigPath2);
                $verifiedBySignatureB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sigPath2));
            }
        }
    }

    // ── VAT Calculations ──────────────────────────────────────────────────────
    $vatRate         = 18;
    $subtotalExclVat = 0;
    $totalVat        = 0;
    $totalInclVat    = 0;
    foreach ($grn->items as $item) {
        $lineSubtotal    = $item->quantity_accepted * $item->unit_cost;
        $lineVat         = $lineSubtotal * ($vatRate / 100);
        $subtotalExclVat += $lineSubtotal;
        $totalVat        += $lineVat;
        $totalInclVat    += ($lineSubtotal + $lineVat);
    }

    $isVerified = $grn->status === 'verified';

    // ── Status Badge Class ────────────────────────────────────────────────────
    $statusClass = match($grn->status) {
        'verified'          => 'status-verified',
        'completed'         => 'status-completed',
        'inventory_updated' => 'status-inventory',
        'draft'             => 'status-draft',
        default             => 'status-default',
    };
@endphp

{{-- ── Print Bar ───────────────────────────────────────────────────────────── --}}
<div class="print-bar">
    <span>Printing: <strong>GRN {{ $grn->grn_number }}</strong></span>
    <div>
        <a href="{{ route('procurement.goods-received.show', $grn->id) }}" class="btn-back">
            ← Back
        </a>
        <button class="btn-print" onclick="window.print()">
            🖨 Print / Save as PDF
        </button>
    </div>
</div>

{{-- ── Document ─────────────────────────────────────────────────────────────── --}}
<div class="document">

    {{-- Header --}}
    <div class="doc-header">
        <div class="doc-header-left">
            <div class="grn-label">Goods Received Note</div>
            <div class="grn-number">{{ $grn->grn_number }}</div>
            <div class="grn-meta">
                Created {{ $grn->created_at->format('F d, Y') }} at {{ $grn->created_at->format('H:i') }}
                &nbsp;·&nbsp;
                by {{ $grn->createdBy->first_name ?? '' }} {{ $grn->createdBy->last_name ?? 'Procurement' }}
            </div>
            <div>
                <span class="status-badge {{ $statusClass }}">
                    <span class="dot"></span>
                    {{ ucfirst(str_replace('_', ' ', $grn->status)) }}
                </span>
            </div>
        </div>
        <div class="doc-header-right">
            @if($companyLogoB64)
                <img src="{{ $companyLogoB64 }}" class="logo" alt="Company Logo">
            @else
                <span class="company-placeholder">Company Logo</span>
            @endif
        </div>
    </div>

    {{-- Body --}}
    <div class="doc-body">

        {{-- KPI Tiles --}}
        <div class="kpi-row">
            <div class="kpi-card kpi-blue">
                <div class="kpi-label">Total Items</div>
                <div class="kpi-value">{{ $grn->items->count() }}</div>
                <div class="kpi-sub">Line items received</div>
            </div>
            <div class="kpi-card kpi-green">
                <div class="kpi-label">Total Accepted</div>
                <div class="kpi-value green">{{ number_format($grn->items->sum('quantity_accepted'), 0) }}</div>
                <div class="kpi-sub">Units accepted</div>
            </div>
            <div class="kpi-card kpi-red">
                <div class="kpi-label">Total Rejected</div>
                <div class="kpi-value red">{{ number_format($grn->items->sum('quantity_rejected'), 0) }}</div>
                <div class="kpi-sub">Units rejected</div>
            </div>
            <div class="kpi-card kpi-amber">
                <div class="kpi-label">Total Value (incl. VAT)</div>
                <div class="kpi-value amber">UGX {{ number_format($totalInclVat, 0) }}</div>
                <div class="kpi-sub">Payable to vendor</div>
            </div>
        </div>

        {{-- GRN Details --}}
        <div class="section-title">GRN Details</div>
        <div class="info-grid mb-6">
            <div class="info-cell">
                <div class="label">GRN Number</div>
                <div class="value mono">{{ $grn->grn_number }}</div>
            </div>
            <div class="info-cell">
                <div class="label">PO Reference</div>
                <div class="value mono">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</div>
            </div>
            <div class="info-cell">
                <div class="label">Vendor</div>
                <div class="value">{{ $grn->vendor->name ?? 'N/A' }}</div>
            </div>
            <div class="info-cell">
                <div class="label">Vendor Email</div>
                <div class="value muted">{{ $grn->vendor->email ?? '—' }}</div>
            </div>
            <div class="info-cell">
                <div class="label">Received Date</div>
                <div class="value">{{ $grn->received_date->format('F d, Y') }}</div>
            </div>
            <div class="info-cell">
                <div class="label">Received By</div>
                <div class="value">
                    {{ $grn->received_by ?? (($receivedByUser->first_name ?? '') . ' ' . ($receivedByUser->last_name ?? '')) }}
                </div>
            </div>
            @if($grn->delivery_note_number)
            <div class="info-cell">
                <div class="label">Vendor DN #</div>
                <div class="value mono">{{ $grn->delivery_note_number }}</div>
            </div>
            @endif
            @if($grn->delivered_by_name)
            <div class="info-cell">
                <div class="label">Delivered By</div>
                <div class="value">{{ $grn->delivered_by_name }}</div>
            </div>
            @endif
            @if($isVerified && $grn->verified_at)
            <div class="info-cell">
                <div class="label">Verified On</div>
                <div class="value" style="color:#7c3aed;">{{ $grn->verified_at->format('F d, Y H:i') }}</div>
            </div>
            <div class="info-cell">
                <div class="label">Verified By</div>
                <div class="value" style="color:#7c3aed;">
                    {{ $verifiedByUser->first_name ?? '' }} {{ $verifiedByUser->last_name ?? '' }}
                </div>
            </div>
            @endif
        </div>

        @if($grn->notes)
        <div class="section-title">Notes</div>
        <div class="notes-box mb-6">"{{ $grn->notes }}"</div>
        @endif

        {{-- Items Table --}}
        <div class="section-title">Received Items ({{ $grn->items->count() }} item{{ $grn->items->count() !== 1 ? 's' : '' }})</div>
        <div class="table-wrap mb-6">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th class="center">Ordered</th>
                        <th class="center">Received</th>
                        <th class="center">Accepted</th>
                        <th class="center">Rejected</th>
                        <th class="right">Unit Cost</th>
                        <th class="right">Subtotal</th>
                        <th class="right">VAT ({{ $vatRate }}%)</th>
                        <th class="right">Total</th>
                        <th>Rejection Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grn->items as $i => $item)
                    @php
                        $lineSubtotal = $item->quantity_accepted * $item->unit_cost;
                        $lineVat      = $lineSubtotal * ($vatRate / 100);
                        $lineTotal    = $lineSubtotal + $lineVat;
                    @endphp
                    <tr>
                        <td style="color:#94a3b8;font-weight:700;">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="item-name">{{ $item->inventoryItem->name ?? 'N/A' }}</div>
                            @if($item->inventoryItem && $item->inventoryItem->item_code)
                                <div class="item-code">Code: {{ $item->inventoryItem->item_code }}</div>
                            @endif
                            @if($item->pack_type && $item->pack_size)
                                <div class="item-pack">📦 {{ $item->number_of_packs }} × {{ $item->pack_type }} ({{ $item->pack_size }} units/pack)</div>
                            @endif
                        </td>
                        <td class="center"><span class="badge badge-blue">{{ number_format($item->quantity_ordered, 2) }}</span></td>
                        <td class="center"><span class="badge badge-green">{{ number_format($item->quantity_received, 2) }}</span></td>
                        <td class="center"><span class="badge badge-emerald">{{ number_format($item->quantity_accepted, 2) }}</span></td>
                        <td class="center">
                            @if($item->quantity_rejected > 0)
                                <span class="badge badge-red">{{ number_format($item->quantity_rejected, 2) }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td class="right" style="color:#475569;">{{ number_format($item->unit_cost, 2) }}</td>
                        <td class="right" style="color:#475569;">{{ number_format($lineSubtotal, 2) }}</td>
                        <td class="right" style="color:#2563eb;">{{ number_format($lineVat, 2) }}</td>
                        <td class="right" style="color:#059669;font-weight:700;">{{ number_format($lineTotal, 2) }}</td>
                        <td>
                            @if($item->rejection_reason)
                                <span class="rejection-reason">{{ $item->rejection_reason }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals Footer --}}
            <div class="totals-footer">
                <div class="total-block">
                    <div class="total-label">Subtotal (excl. VAT)</div>
                    <div class="total-value">UGX {{ number_format($subtotalExclVat, 2) }}</div>
                </div>
                <div class="total-divider"></div>
                <div class="total-block">
                    <div class="total-label blue">VAT ({{ $vatRate }}%)</div>
                    <div class="total-value blue">UGX {{ number_format($totalVat, 2) }}</div>
                </div>
                <div class="total-divider"></div>
                <div class="total-block">
                    <div class="total-label">Total Payable (incl. VAT)</div>
                    <div class="total-value emerald">UGX {{ number_format($totalInclVat, 2) }}</div>
                </div>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="section-title">Financial Summary</div>
        <div class="fin-summary mb-6">
            <div class="fin-row">
                <span class="fin-label">PO Total</span>
                <span class="fin-value">UGX {{ number_format($grn->po_total_amount ?? 0, 0) }}</span>
            </div>
            <div class="fin-row">
                <span class="fin-label">Subtotal (excl. VAT)</span>
                <span class="fin-value">UGX {{ number_format($subtotalExclVat, 0) }}</span>
            </div>
            <div class="fin-row">
                <span class="fin-label blue">VAT @ {{ $vatRate }}%</span>
                <span class="fin-value blue">UGX {{ number_format($totalVat, 0) }}</span>
            </div>
            <div class="fin-row total-row">
                <span class="fin-label bold">Total Payable (incl. VAT)</span>
                <span class="fin-value emerald">UGX {{ number_format($totalInclVat, 0) }}</span>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="section-title">Authorisations &amp; Signatures</div>
        <div class="sig-grid">

            {{-- Received By --}}
            <div class="sig-box">
                <div class="sig-title">Received By</div>
                <div class="sig-img-wrap">
                    @if($receivedBySignatureB64)
                        <img src="{{ $receivedBySignatureB64 }}" alt="Signature">
                    @else
                        <span class="no-sig">No signature on file</span>
                    @endif
                </div>
                <div class="sig-line"></div>
                <div class="sig-name">
                    {{ $grn->received_by ?? (($receivedByUser->first_name ?? '') . ' ' . ($receivedByUser->last_name ?? '')) }}
                </div>
                <div class="sig-date">{{ $grn->created_at ? $grn->created_at->format('d M Y') : '' }}</div>
            </div>

            {{-- Delivered By --}}
            <div class="sig-box">
                <div class="sig-title">Delivered By</div>
                <div class="sig-img-wrap">
                    <span class="no-sig">No signature required</span>
                </div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $grn->delivered_by_name ?? '—' }}</div>
                <div class="sig-date">&nbsp;</div>
            </div>

            {{-- Verified By --}}
            <div class="sig-box {{ $isVerified ? 'verified-box' : '' }}">
                <div class="sig-title">Verified By</div>
                @if($isVerified && $verifiedByUser)
                    <div class="sig-img-wrap">
                        @if($verifiedBySignatureB64)
                            <img src="{{ $verifiedBySignatureB64 }}" alt="Verified Signature">
                        @else
                            <span class="no-sig">No signature on file</span>
                        @endif
                    </div>
                    <div class="sig-line purple"></div>
                    <div class="sig-name purple">{{ $verifiedByUser->first_name }} {{ $verifiedByUser->last_name }}</div>
                    <div class="sig-date purple">{{ $grn->verified_at ? $grn->verified_at->format('d M Y') : '' }}</div>
                    <div>
                        <span class="verified-badge">✓ Verified</span>
                    </div>
                @else
                    <div class="sig-img-wrap">
                        <span class="pending-badge">⏳ Pending Verification</span>
                    </div>
                    <div class="sig-line"></div>
                    <div class="sig-date">Awaiting approval</div>
                @endif
            </div>

            {{-- Company Stamp --}}
            <div class="sig-box">
                <div class="sig-title">Company Stamp</div>
                <div class="sig-img-wrap">
                    @if($companyStampB64)
                        <img src="{{ $companyStampB64 }}" alt="Company Stamp">
                    @else
                        <span class="no-sig">No stamp on file</span>
                    @endif
                </div>
                <div class="sig-line"></div>
                <div class="sig-date">Authorised Signatory</div>
            </div>

        </div>

    </div>{{-- end doc-body --}}
</div>{{-- end document --}}

<script>
    // Auto-trigger print dialog if ?autoprint=1 in URL
    const params = new URLSearchParams(window.location.search);
    if (params.get('autoprint') === '1') {
        window.onload = () => window.print();
    }
</script>

</body>
</html>
