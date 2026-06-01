<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>GRN {{ $grn->grn_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
        }

        /* ── Header ── */
        .header {
            background: #0f172a;
            padding: 22px 30px;
        }
        .header-table { width: 100%; }
        .header-left  { width: 60%; vertical-align: middle; }
        .header-right { width: 40%; vertical-align: middle; text-align: right; }

        .doc-label  { font-size: 9px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #93c5fd; margin-bottom: 4px; }
        .grn-number { font-size: 20px; font-weight: 700; color: #fff; font-family: 'DejaVu Sans Mono', monospace; letter-spacing: 1px; }
        .grn-meta   { font-size: 9.5px; color: #94a3b8; margin-top: 5px; }
        .grn-meta strong { color: #e2e8f0; }

        .logo-img { max-height: 55px; max-width: 150px; }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 6px;
        }
        .badge-verified  { background: #f3e8ff; color: #7c3aed; }
        .badge-completed { background: #dcfce7; color: #16a34a; }
        .badge-inventory { background: #dbeafe; color: #1d4ed8; }
        .badge-draft     { background: #fef9c3; color: #a16207; }
        .badge-default   { background: #f1f5f9; color: #475569; }

        /* ── Info Strip ── */
        .info-strip {
            background: #1e3a8a;
            padding: 8px 30px;
        }
        .strip-table { width: 100%; }
        .strip-cell  { vertical-align: top; padding-right: 18px; }
        .strip-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #bfdbfe; }
        .strip-value { font-size: 10px; font-weight: 700; color: #fff; margin-top: 2px; }

        /* ── Body ── */
        .body { padding: 20px 30px; }

        /* ── Section Title ── */
        .section-title {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
            margin-top: 16px;
        }
        .section-title-first { margin-top: 0; }

        /* ── KPI Row ── */
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 14px; }
        .kpi-cell  {
            border: 1.5px solid #e2e8f0;
            border-radius: 5px;
            padding: 9px 10px;
            text-align: center;
            width: 25%;
        }
        .kpi-cell.blue  { border-color: #bfdbfe; }
        .kpi-cell.green { border-color: #bbf7d0; }
        .kpi-cell.red   { border-color: #fecaca; }
        .kpi-cell.amber { border-color: #fde68a; }
        .kpi-label { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; }
        .kpi-value { font-size: 16px; font-weight: 800; font-family: 'DejaVu Sans Mono', monospace; margin-top: 2px; }
        .kpi-value.blue  { color: #1d4ed8; }
        .kpi-value.green { color: #059669; }
        .kpi-value.red   { color: #dc2626; }
        .kpi-value.amber { color: #d97706; font-size: 11px; }

        /* ── Two Col ── */
        .two-col-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .col-l { width: 50%; padding-right: 14px; vertical-align: top; }
        .col-r { width: 50%; padding-left: 14px; vertical-align: top; border-left: 1px solid #f1f5f9; }

        /* ── Info Table ── */
        .info-tbl { width: 100%; border-collapse: collapse; }
        .info-tbl td { padding: 4px 0; vertical-align: top; }
        .i-label { width: 110px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #94a3b8; }
        .i-value { font-size: 10.5px; font-weight: 600; color: #0f172a; }
        .i-value.mono   { font-family: 'DejaVu Sans Mono', monospace; color: #1d4ed8; }
        .i-value.purple { color: #7c3aed; }
        .i-value.muted  { color: #64748b; font-weight: 400; }

        /* ── Items Table ── */
        .items-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 6px;
        }
        .items-tbl { width: 100%; border-collapse: collapse; }
        .items-tbl thead tr { background: #1e3a8a; }
        .items-tbl thead th {
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 7px 8px;
            text-align: left;
        }
        .items-tbl thead th.r { text-align: right; }
        .items-tbl thead th.c { text-align: center; }
        .items-tbl tbody tr { border-bottom: 1px solid #f1f5f9; }
        .items-tbl tbody tr.alt { background: #f8fafc; }
        .items-tbl tbody td { padding: 8px 8px; font-size: 10px; color: #334155; vertical-align: middle; }
        .items-tbl tbody td.r { text-align: right; }
        .items-tbl tbody td.c { text-align: center; }

        .item-name { font-weight: 700; color: #0f172a; font-size: 10.5px; }
        .item-code { font-size: 8.5px; color: #94a3b8; margin-top: 1px; }
        .item-pack { font-size: 8.5px; color: #3b82f6; margin-top: 1px; }

        .qty { font-family: 'DejaVu Sans Mono', monospace; font-weight: 700; font-size: 9.5px; }
        .qty-blue    { color: #1d4ed8; }
        .qty-green   { color: #15803d; }
        .qty-emerald { color: #059669; }
        .qty-red     { color: #dc2626; }

        .rej-reason { font-size: 8.5px; color: #dc2626; background: #fff1f2; padding: 1px 5px; border-radius: 3px; }

        /* ── Totals Footer ── */
        .totals-tbl { width: 100%; border-collapse: collapse; }
        .totals-tbl td { padding: 5px 8px; font-size: 10.5px; }
        .t-label { text-align: right; color: #64748b; font-weight: 600; width: 72%; }
        .t-label.blue  { color: #2563eb; }
        .t-label.bold  { color: #0f172a; font-weight: 800; font-size: 11px; text-transform: uppercase; }
        .t-value { text-align: right; font-family: 'DejaVu Sans Mono', monospace; font-weight: 700; width: 28%; color: #0f172a; }
        .t-value.blue    { color: #2563eb; }
        .t-value.emerald { color: #059669; font-size: 12.5px; }
        .grand-row td { background: #0f172a; border-top: 2px solid #1e3a8a; }
        .grand-row .t-label { color: #e2e8f0; }
        .grand-row .t-value { color: #60a5fa; font-size: 13px; }
        .sep-row td { border-top: 1.5px solid #e2e8f0; padding-top: 7px; }

        /* ── Financial Summary ── */
        .fin-tbl { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 5px; }
        .fin-tbl td { padding: 7px 12px; font-size: 10.5px; border-bottom: 1px solid #f1f5f9; }
        .fin-tbl tr:last-child td { border-bottom: none; background: #f8fafc; }
        .fin-label { color: #64748b; font-weight: 600; width: 60%; }
        .fin-label.blue { color: #2563eb; }
        .fin-label.bold { color: #0f172a; font-weight: 800; text-transform: uppercase; }
        .fin-value { text-align: right; font-family: 'DejaVu Sans Mono', monospace; font-weight: 700; color: #0f172a; }
        .fin-value.blue    { color: #2563eb; }
        .fin-value.emerald { color: #059669; font-size: 12px; }

        /* ── Notes ── */
        .notes-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #3b82f6;
            border-radius: 4px;
            padding: 9px 12px;
            font-size: 10px;
            color: #475569;
            font-style: italic;
            margin-top: 4px;
        }

        /* ── Signatures ── */
        .sig-outer { margin-top: 16px; }
        .sig-table { width: 100%; border-collapse: separate; border-spacing: 10px; }
        .sig-cell {
            width: 25%;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 10px;
            text-align: center;
            vertical-align: bottom;
        }
        .sig-cell.verified-cell {
            border-color: #d8b4fe;
            background: #faf5ff;
        }
        .sig-cell-title {
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        .sig-cell.verified-cell .sig-cell-title { color: #a855f7; }

        .sig-img-area { height: 50px; text-align: center; margin-bottom: 6px; }
        .sig-img-area img { max-height: 48px; max-width: 130px; }
        .sig-no-img { font-size: 8px; color: #cbd5e1; font-style: italic; line-height: 50px; }

        .sig-line-div { border-top: 1px solid #94a3b8; margin-bottom: 5px; }
        .sig-line-div.purple { border-color: #a855f7; }

        .sig-name { font-size: 10px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
        .sig-name.purple { color: #7c3aed; }
        .sig-date { font-size: 8.5px; color: #94a3b8; }
        .sig-date.purple { color: #a78bfa; }

        .verified-pill {
            display: inline-block;
            margin-top: 4px;
            background: #f3e8ff;
            color: #7c3aed;
            font-size: 8px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
        }
        .pending-pill {
            display: inline-block;
            margin-top: 4px;
            background: #fefce8;
            border: 1px solid #fde047;
            color: #a16207;
            font-size: 8px;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 4px;
        }

        /* ── Footer ── */
        .doc-footer {
            margin-top: 20px;
            padding: 12px 30px;
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }
        .footer-tbl { width: 100%; }
        .footer-l { vertical-align: bottom; width: 60%; }
        .footer-r { vertical-align: bottom; text-align: right; width: 40%; }
        .footer-text { font-size: 8.5px; color: #94a3b8; line-height: 1.5; }
        .footer-brand { font-size: 10px; font-weight: 700; color: #1e3a8a; }
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

    $statusBadgeClass = match($grn->status) {
        'verified'          => 'badge-verified',
        'completed'         => 'badge-completed',
        'inventory_updated' => 'badge-inventory',
        'draft'             => 'badge-draft',
        default             => 'badge-default',
    };
@endphp

{{-- ══ HEADER ══ --}}
<div class="header">
    <table class="header-table">
        <tr>
            <td class="header-left">
                <div class="doc-label">Goods Received Note</div>
                <div class="grn-number">{{ $grn->grn_number }}</div>
                <div class="grn-meta">
                    Received: <strong>{{ $grn->received_date->format('d F Y') }}</strong>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    Created: <strong>{{ $grn->created_at->format('d M Y H:i') }}</strong>
                </div>
                <div class="grn-meta" style="margin-top:3px;">
                    By: <strong>{{ ($grn->createdBy->first_name ?? '') . ' ' . ($grn->createdBy->last_name ?? 'Procurement') }}</strong>
                </div>
                <div>
                    <span class="status-badge {{ $statusBadgeClass }}">
                        {{ ucfirst(str_replace('_', ' ', $grn->status)) }}
                    </span>
                </div>
            </td>
            <td class="header-right">
                @if($companyLogoB64)
                    <img src="{{ $companyLogoB64 }}" class="logo-img" alt="Logo">
                @else
                    <div style="color:#475569;font-size:10px;font-style:italic;">Company Logo</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- ══ INFO STRIP ══ --}}
<div class="info-strip">
    <table class="strip-table">
        <tr>
            <td class="strip-cell">
                <div class="strip-label">PO Reference</div>
                <div class="strip-value">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</div>
            </td>
            <td class="strip-cell">
                <div class="strip-label">Vendor</div>
                <div class="strip-value">{{ $grn->vendor->name ?? 'N/A' }}</div>
            </td>
            <td class="strip-cell">
                <div class="strip-label">Received By</div>
                <div class="strip-value">
                    {{ $grn->received_by ?? (($receivedByUser->first_name ?? '') . ' ' . ($receivedByUser->last_name ?? '—')) }}
                </div>
            </td>
            <td class="strip-cell">
                <div class="strip-label">Delivered By</div>
                <div class="strip-value">{{ $grn->delivered_by_name ?? '—' }}</div>
            </td>
            @if($isVerified)
            <td class="strip-cell">
                <div class="strip-label">Verified By</div>
                <div class="strip-value">{{ ($verifiedByUser->first_name ?? '') . ' ' . ($verifiedByUser->last_name ?? '—') }}</div>
            </td>
            @endif
            @if($grn->delivery_note_number)
            <td class="strip-cell">
                <div class="strip-label">Vendor DN #</div>
                <div class="strip-value">{{ $grn->delivery_note_number }}</div>
            </td>
            @endif
        </tr>
    </table>
</div>

{{-- ══ BODY ══ --}}
<div class="body">

    {{-- KPI Row --}}
    <table class="kpi-table">
        <tr>
            <td class="kpi-cell blue">
                <div class="kpi-label">Total Items</div>
                <div class="kpi-value blue">{{ $grn->items->count() }}</div>
            </td>
            <td class="kpi-cell green">
                <div class="kpi-label">Total Accepted</div>
                <div class="kpi-value green">{{ number_format($grn->items->sum('quantity_accepted'), 0) }}</div>
            </td>
            <td class="kpi-cell red">
                <div class="kpi-label">Total Rejected</div>
                <div class="kpi-value red">{{ number_format($grn->items->sum('quantity_rejected'), 0) }}</div>
            </td>
            <td class="kpi-cell amber">
                <div class="kpi-label">Total (incl. VAT)</div>
                <div class="kpi-value amber">UGX {{ number_format($totalInclVat, 0) }}</div>
            </td>
        </tr>
    </table>

    {{-- GRN Details + Vendor Info --}}
    <div class="section-title section-title-first">GRN &amp; Vendor Details</div>
    <table class="two-col-table">
        <tr>
            <td class="col-l">
                <table class="info-tbl">
                    <tr>
                        <td class="i-label">GRN Number</td>
                        <td class="i-value mono">{{ $grn->grn_number }}</td>
                    </tr>
                    <tr>
                        <td class="i-label">PO Reference</td>
                        <td class="i-value mono">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="i-label">Received Date</td>
                        <td class="i-value">{{ $grn->received_date->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="i-label">Received By</td>
                        <td class="i-value">
                            {{ $grn->received_by ?? (($receivedByUser->first_name ?? '') . ' ' . ($receivedByUser->last_name ?? '')) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="i-label">Delivered By</td>
                        <td class="i-value">{{ $grn->delivered_by_name ?? '—' }}</td>
                    </tr>
                    @if($grn->delivery_note_number)
                    <tr>
                        <td class="i-label">Vendor DN #</td>
                        <td class="i-value mono">{{ $grn->delivery_note_number }}</td>
                    </tr>
                    @endif
                    @if($isVerified && $grn->verified_at)
                    <tr>
                        <td class="i-label">Verified By</td>
                        <td class="i-value purple">
                            {{ ($verifiedByUser->first_name ?? '') . ' ' . ($verifiedByUser->last_name ?? '') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="i-label">Verified On</td>
                        <td class="i-value purple">{{ $grn->verified_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td class="col-r">
                <table class="info-tbl">
                    <tr>
                        <td class="i-label">Vendor Name</td>
                        <td class="i-value">{{ $grn->vendor->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="i-label">Vendor Email</td>
                        <td class="i-value muted">{{ $grn->vendor->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="i-label">Vendor Phone</td>
                        <td class="i-value muted">{{ $grn->vendor->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="i-label">Vendor Address</td>
                        <td class="i-value muted">{{ $grn->vendor->address ?? '—' }}</td>
                    </tr>
                </table>

                <div class="section-title" style="margin-top:14px;">Financial Summary</div>
                <table class="fin-tbl">
                    <tr>
                        <td class="fin-label">PO Total</td>
                        <td class="fin-value">UGX {{ number_format($grn->po_total_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="fin-label">Subtotal (excl. VAT)</td>
                        <td class="fin-value">UGX {{ number_format($subtotalExclVat, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="fin-label blue">VAT @ {{ $vatRate }}%</td>
                        <td class="fin-value blue">UGX {{ number_format($totalVat, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="fin-label bold">Total Payable (incl. VAT)</td>
                        <td class="fin-value emerald">UGX {{ number_format($totalInclVat, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($grn->notes)
    <div class="section-title">Notes</div>
    <div class="notes-box">{{ $grn->notes }}</div>
    @endif

    {{-- Items Table --}}
    <div class="section-title">
        Received Items — {{ $grn->items->count() }} Line Item(s)
    </div>
    <div class="items-wrap">
        <table class="items-tbl">
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Item</th>
                    <th class="c" style="width:56px;">Ordered</th>
                    <th class="c" style="width:56px;">Received</th>
                    <th class="c" style="width:56px;">Accepted</th>
                    <th class="c" style="width:56px;">Rejected</th>
                    <th class="r" style="width:72px;">Unit Cost</th>
                    <th class="r" style="width:72px;">Subtotal</th>
                    <th class="r" style="width:70px;">VAT ({{ $vatRate }}%)</th>
                    <th class="r" style="width:78px;">Total</th>
                    <th style="width:88px;">Rejection</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grn->items as $i => $item)
                @php
                    $lineSubtotal = $item->quantity_accepted * $item->unit_cost;
                    $lineVat      = $lineSubtotal * ($vatRate / 100);
                    $lineTotal    = $lineSubtotal + $lineVat;
                @endphp
                <tr class="{{ $i % 2 === 1 ? 'alt' : '' }}">
                    <td style="color:#94a3b8;font-size:8.5px;font-weight:700;">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="item-name">{{ $item->inventoryItem->name ?? 'N/A' }}</div>
                        @if($item->inventoryItem && $item->inventoryItem->item_code)
                            <div class="item-code">Code: {{ $item->inventoryItem->item_code }}</div>
                        @endif
                        @if($item->pack_type && $item->pack_size)
                            <div class="item-pack">{{ $item->number_of_packs }} x {{ $item->pack_type }} ({{ $item->pack_size }}/pack)</div>
                        @endif
                    </td>
                    <td class="c"><span class="qty qty-blue">{{ number_format($item->quantity_ordered, 2) }}</span></td>
                    <td class="c"><span class="qty qty-green">{{ number_format($item->quantity_received, 2) }}</span></td>
                    <td class="c"><span class="qty qty-emerald">{{ number_format($item->quantity_accepted, 2) }}</span></td>
                    <td class="c">
                        @if($item->quantity_rejected > 0)
                            <span class="qty qty-red">{{ number_format($item->quantity_rejected, 2) }}</span>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td class="r" style="font-family:'DejaVu Sans Mono',monospace;color:#475569;">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="r" style="font-family:'DejaVu Sans Mono',monospace;color:#475569;">{{ number_format($lineSubtotal, 2) }}</td>
                    <td class="r" style="font-family:'DejaVu Sans Mono',monospace;color:#2563eb;">{{ number_format($lineVat, 2) }}</td>
                    <td class="r" style="font-family:'DejaVu Sans Mono',monospace;font-weight:700;color:#059669;">{{ number_format($lineTotal, 2) }}</td>
                    <td>
                        @if($item->rejection_reason)
                            <span class="rej-reason">{{ $item->rejection_reason }}</span>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals footer inside table wrap --}}
        <table class="totals-tbl" style="border-top:1.5px solid #e2e8f0;background:#f8fafc;">
            <tr>
                <td class="t-label">Subtotal (excl. VAT):</td>
                <td class="t-value">UGX {{ number_format($subtotalExclVat, 2) }}</td>
            </tr>
            <tr>
                <td class="t-label blue">VAT @ {{ $vatRate }}%:</td>
                <td class="t-value blue">UGX {{ number_format($totalVat, 2) }}</td>
            </tr>
            <tr class="grand-row">
                <td class="t-label bold" style="color:#e2e8f0;">TOTAL PAYABLE (incl. VAT):</td>
                <td class="t-value" style="color:#60a5fa;font-size:12.5px;">UGX {{ number_format($totalInclVat, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- Signatures --}}
    <div class="section-title" style="margin-top:22px;">Authorisations &amp; Signatures</div>
    <div class="sig-outer">
        <table class="sig-table">
            <tr>

                {{-- Received By --}}
                <td class="sig-cell">
                    <div class="sig-cell-title">Received By</div>
                    <div class="sig-img-area">
                        @if($receivedBySignatureB64)
                            <img src="{{ $receivedBySignatureB64 }}" alt="Signature">
                        @else
                            <div class="sig-no-img">No signature on file</div>
                        @endif
                    </div>
                    <div class="sig-line-div"></div>
                    <div class="sig-name">
                        {{ $grn->received_by ?? (($receivedByUser->first_name ?? '') . ' ' . ($receivedByUser->last_name ?? '')) }}
                    </div>
                    <div class="sig-date">{{ $grn->created_at ? $grn->created_at->format('d M Y') : '' }}</div>
                </td>

                {{-- Delivered By --}}
                <td class="sig-cell">
                    <div class="sig-cell-title">Delivered By</div>
                    <div class="sig-img-area">
                        <div class="sig-no-img">No signature required</div>
                    </div>
                    <div class="sig-line-div"></div>
                    <div class="sig-name">{{ $grn->delivered_by_name ?? '—' }}</div>
                    <div class="sig-date">&nbsp;</div>
                </td>

                {{-- Verified By --}}
                <td class="sig-cell {{ $isVerified ? 'verified-cell' : '' }}">
                    <div class="sig-cell-title">Verified By</div>
                    @if($isVerified && $verifiedByUser)
                        <div class="sig-img-area">
                            @if($verifiedBySignatureB64)
                                <img src="{{ $verifiedBySignatureB64 }}" alt="Verified Signature">
                            @else
                                <div class="sig-no-img">No signature on file</div>
                            @endif
                        </div>
                        <div class="sig-line-div purple"></div>
                        <div class="sig-name purple">{{ $verifiedByUser->first_name }} {{ $verifiedByUser->last_name }}</div>
                        <div class="sig-date purple">{{ $grn->verified_at ? $grn->verified_at->format('d M Y') : '' }}</div>
                        <div><span class="verified-pill">✓ Verified</span></div>
                    @else
                        <div class="sig-img-area">
                            <div style="line-height:50px;">
                                <span class="pending-pill">⏳ Pending</span>
                            </div>
                        </div>
                        <div class="sig-line-div"></div>
                        <div class="sig-date">Awaiting approval</div>
                    @endif
                </td>

                {{-- Company Stamp --}}
                <td class="sig-cell">
                    <div class="sig-cell-title">Company Stamp</div>
                    <div class="sig-img-area">
                        @if($companyStampB64)
                            <img src="{{ $companyStampB64 }}" alt="Company Stamp">
                        @else
                            <div class="sig-no-img">No stamp on file</div>
                        @endif
                    </div>
                    <div class="sig-line-div"></div>
                    <div class="sig-date">Authorised Signatory</div>
                </td>

            </tr>
        </table>
    </div>

</div>{{-- end .body --}}

{{-- ══ FOOTER ══ --}}
<div class="doc-footer">
    <table class="footer-tbl">
        <tr>
            <td class="footer-l">
                <div class="footer-text">{{ $grn->grn_number }} &nbsp;·&nbsp; Generated {{ now()->format('d M Y H:i') }}</div>
                <div class="footer-text">This document is computer generated. VAT @ {{ $vatRate }}% applied on accepted quantities.</div>
            </td>
            <td class="footer-r">
                <div class="footer-brand">PROCUREMENT DEPARTMENT</div>
                <div class="footer-text">Confidential — For internal use only</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
