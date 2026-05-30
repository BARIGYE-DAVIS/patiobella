<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Requisition {{ $requisition->requisition_number }}</title>
    <style>
        /*
         * DomPDF A4 rules:
         *  - @page sets the canvas to exactly A4 portrait
         *  - body width is set to the printable area (A4 = 210mm, minus 14mm margins each side = 182mm)
         *  - NO border-radius (DomPDF ignores it and can cause layout shifts)
         *  - NO box-sizing: border-box on tables (breaks percentage width calc in DomPDF)
         *  - All tables use table-layout: fixed so columns never overflow
         *  - Padding/margin kept tight so nothing pushes past 182mm
         */
        @page {
            size: A4 portrait;
            margin: 14mm 14mm 14mm 14mm;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1f2937;
            width: 182mm;
        }

        /* ─── HEADER ─── */
        .header-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0 0 10px 0;
        }
        .logo {
            max-height: 50px;
            max-width: 160px;
        }
        .doc-title {
            font-size: 15px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 2px;
        }
        .ref-no {
            font-size: 10px;
            color: #6b7280;
        }

        /* ─── INFO GRID ─── */
        .info-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 6px 8px;
            vertical-align: top;
            border: 1px solid #e5e7eb;
        }
        .info-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 10px;
            font-weight: 500;
            color: #1f2937;
        }

        /* ─── ITEMS TABLE ─── */
        .items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .items-table th {
            background: #f3f4f6;
            padding: 6px 8px;
            text-align: left;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            word-wrap: break-word;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            color: #1f2937;
            word-wrap: break-word;
        }

        /* ─── SIGNATURE ─── */
        .sig-divider {
            border-top: 1px solid #e5e7eb;
            margin-top: 20px;
            margin-bottom: 12px;
        }
        .sig-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .sig-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
            text-align: center;
        }

        /* ─── FOOTER ─── */
        .footer {
            margin-top: 16px;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

@php
    /* ── Logo ── */
    $logo        = \App\Models\BusinessSetting::getLogo();
    $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
    $logoB64 = null; $logoMime = 'image/png';
    if ($logo) {
        $lp = public_path(parse_url($logo, PHP_URL_PATH));
        if (file_exists($lp)) { $logoMime = mime_content_type($lp); $logoB64 = base64_encode(file_get_contents($lp)); }
    }

    /* ── Status colours ── */
    $sc = [
        'pending'     => ['#fef3c7','#92400e'],
        'approved'    => ['#d1fae5','#065f46'],
        'rejected'    => ['#fee2e2','#991b1b'],
        'ordered'     => ['#dbeafe','#1e40af'],
        'fulfilled'   => ['#ede9fe','#5b21b6'],
        'lpo_created' => ['#cffafe','#0e7490'],
        'cancelled'   => ['#f3f4f6','#6b7280'],
    ][$requisition->status] ?? ['#f3f4f6','#6b7280'];

    /* ── Signature encoder ── */
    $sig = function(?string $url): array {
        if (!$url) return [null, 'image/png'];
        $p = public_path(parse_url($url, PHP_URL_PATH));
        if (!file_exists($p)) return [null, 'image/png'];
        return [base64_encode(file_get_contents($p)), mime_content_type($p)];
    };

    $requester   = $requisition->requestedBy;
    $approver    = $requisition->approvedBy;
    $hasApproval = $approver && $requisition->status === 'approved';

    [$reqB64,  $reqMime]  = $sig($requester->signature_url ?? null);
    [$appB64,  $appMime]  = $hasApproval ? $sig($approver->signature_url ?? null) : [null, 'image/png'];
@endphp

{{-- ═══ HEADER ═══ --}}
<table class="header-table">
    <tr>
        <td style="width:55%;">
            @if($logoB64)
                <img src="data:{{ $logoMime }};base64,{{ $logoB64 }}" class="logo" alt="Logo">
            @elseif($logo)
                <img src="{{ $logo }}" class="logo" alt="Logo">
            @else
                <span style="font-size:15px;font-weight:700;color:#059669;">{{ $companyName }}</span>
            @endif
        </td>
        <td style="width:45%;text-align:right;">
            <div class="doc-title">REQUISITION FORM</div>
            <div class="ref-no">{{ $requisition->requisition_number }}</div>
        </td>
    </tr>
</table>

{{-- ═══ INFO GRID ═══ --}}
<table class="info-table">
    <tr>
        <td style="width:20%;">
            <div class="info-label">Type</div>
            <div class="info-value">
                @if($requisition->requisition_type === 'emergency')
                    <span style="background:#fee2e2;color:#991b1b;padding:1px 6px;font-size:9px;font-weight:700;">EMERGENCY</span>
                @else
                    <span style="background:#d1fae5;color:#065f46;padding:1px 6px;font-size:9px;font-weight:700;">Normal</span>
                @endif
            </div>
        </td>
        <td style="width:20%;">
            <div class="info-label">Date Needed</div>
            <div class="info-value">{{ $requisition->date_needed ? \Carbon\Carbon::parse($requisition->date_needed)->format('d M Y') : '—' }}</div>
        </td>
        <td style="width:20%;">
            <div class="info-label">Requested By</div>
            <div class="info-value">{{ ($requester->first_name ?? '') . ' ' . ($requester->last_name ?? '') }}</div>
        </td>
        <td style="width:20%;">
            <div class="info-label">Request Date</div>
            <div class="info-value">{{ $requisition->created_at ? \Carbon\Carbon::parse($requisition->created_at)->format('d M Y') : '—' }}</div>
        </td>
        <td style="width:20%;">
            <div class="info-label">Status</div>
            <div class="info-value">
                <span style="background:{{ $sc[0] }};color:{{ $sc[1] }};padding:1px 6px;font-size:9px;font-weight:700;">
                    {{ ucfirst(str_replace('_', ' ', $requisition->status)) }}
                </span>
            </div>
        </td>
    </tr>
    @if($requisition->notes)
    <tr>
        <td colspan="5" style="border-top:1px solid #e5e7eb;">
            <div class="info-label">Notes</div>
            <div class="info-value">{{ $requisition->notes }}</div>
        </td>
    </tr>
    @endif
</table>

{{-- ═══ ITEMS ═══ --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width:35%;">Item Name</th>
            <th style="width:17%;">Category</th>
            <th style="width:10%;text-align:right;">Qty</th>
            <th style="width:11%;">Metrics</th>
            <th style="width:27%;">Notes</th>
        </tr>
    </thead>
    <tbody>
        @forelse($requisition->items as $item)
        <tr>
            <td>{{ $item->inventoryItem->name ?? $item->item_name ?? 'N/A' }}</td>
            <td>{{ $item->category_name ?? '—' }}</td>
            <td style="text-align:right;">{{ number_format($item->quantity_requested, 2) }}</td>
            <td>{{ $item->metrics ?? '—' }}</td>
            <td>{{ $item->notes ?? '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;padding:20px;color:#9ca3af;">No items found</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- ═══ SIGNATURES ═══ --}}
<div class="sig-divider"></div>
<table class="sig-table">
    <tr>

        {{-- Requester --}}
        <td style="padding-right:20px;">
            <div style="font-size:9px;color:#6b7280;margin-bottom:6px;">REQUESTED BY</div>

            @if($reqB64)
                <img src="data:{{ $reqMime }};base64,{{ $reqB64 }}"
                     style="max-height:40px;max-width:150px;display:block;margin:0 auto 4px;" alt="Signature">
            @elseif($requester && $requester->signature_url)
                <img src="{{ $requester->signature_url }}"
                     style="max-height:40px;max-width:150px;display:block;margin:0 auto 4px;" alt="Signature">
            @else
                <div style="height:40px;"></div>
            @endif

            <div style="border-top:1px solid #6b7280;margin:4px 10px 5px;"></div>

            <div style="font-size:10px;font-weight:600;color:#1f2937;">
                {{ trim(($requester->first_name ?? '') . ' ' . ($requester->last_name ?? '')) ?: 'N/A' }}
            </div>
            <div style="font-size:9px;color:#6b7280;margin-top:1px;">
                {{ $requisition->created_at ? \Carbon\Carbon::parse($requisition->created_at)->format('d M Y') : \Carbon\Carbon::now()->format('d M Y') }}
            </div>
        </td>

        {{-- Approver --}}
        <td style="padding-left:20px;">
            <div style="font-size:9px;color:#6b7280;margin-bottom:6px;">MANAGEMENT USE</div>

            @if($appB64)
                <img src="data:{{ $appMime }};base64,{{ $appB64 }}"
                     style="max-height:40px;max-width:150px;display:block;margin:0 auto 4px;" alt="Signature">
            @elseif($hasApproval && $approver->signature_url)
                <img src="{{ $approver->signature_url }}"
                     style="max-height:40px;max-width:150px;display:block;margin:0 auto 4px;" alt="Signature">
            @else
                <div style="height:40px;"></div>
            @endif

            <div style="border-top:1px solid #6b7280;margin:4px 10px 5px;"></div>

            <div style="font-size:10px;font-weight:600;color:#1f2937;">
                @if($hasApproval)
                    {{ trim(($approver->first_name ?? '') . ' ' . ($approver->last_name ?? '')) ?: 'Approved' }}
                @else
                    Not Approved Yet
                @endif
            </div>
            <div style="font-size:9px;color:#6b7280;margin-top:1px;">
                @if($hasApproval && $requisition->approved_at)
                    {{ \Carbon\Carbon::parse($requisition->approved_at)->format('d M Y') }}
                @else
                    Date: _______________
                @endif
            </div>
        </td>

    </tr>
</table>

{{-- ═══ FOOTER ═══ --}}
<div class="footer">
    <p>This is a computer generated document. &nbsp;|&nbsp; {{ $companyName }} &mdash; All Rights Reserved</p>
</div>

</body>
</html>
