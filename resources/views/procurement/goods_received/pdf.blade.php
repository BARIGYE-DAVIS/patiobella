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
            padding: 0;
        }

        /* ── Header ── */
        .header {
            background: #0f172a;
            color: #fff;
            padding: 24px 32px;
            margin-bottom: 0;
        }
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .header-left  { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; }

        .company-name  { font-size: 20px; font-weight: 700; color: #fff; letter-spacing: 1px; }
        .doc-title     { font-size: 13px; color: #94a3b8; margin-top: 3px; letter-spacing: 2px; text-transform: uppercase; }
        .grn-number    { font-size: 22px; font-weight: 700; color: #60a5fa; font-family: 'DejaVu Sans Mono', monospace; }
        .grn-date      { font-size: 10px; color: #94a3b8; margin-top: 4px; }

        /* Status strip */
        .status-strip {
            background: #1e3a8a;
            padding: 8px 32px;
            display: table;
            width: 100%;
        }
        .status-item {
            display: table-cell;
            color: #bfdbfe;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding-right: 20px;
        }
        .status-item strong { color: #fff; display: block; font-size: 10.5px; margin-top: 2px; }

        /* Body */
        .body { padding: 24px 32px; }

        /* Section title */
        .section-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
            margin-top: 18px;
        }
        .section-title:first-child { margin-top: 0; }

        /* Info grid */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .info-table td { padding: 5px 0; vertical-align: top; }
        .info-label { width: 130px; color: #64748b; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .info-value { color: #0f172a; font-size: 11px; font-weight: 500; }
        .info-value.mono { font-family: 'DejaVu Sans Mono', monospace; color: #1d4ed8; }

        /* Two column layout */
        .two-col { display: table; width: 100%; }
        .col-left  { display: table-cell; width: 50%; padding-right: 16px; vertical-align: top; }
        .col-right { display: table-cell; width: 50%; padding-left: 16px; vertical-align: top; border-left: 1px solid #f1f5f9; }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .items-table thead tr {
            background: #1e3a8a;
        }
        .items-table thead th {
            color: #fff;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table thead th.text-right  { text-align: right; }
        .items-table thead th.text-center { text-align: center; }

        .items-table tbody tr { border-bottom: 1px solid #f1f5f9; }
        .items-table tbody tr.alt { background: #f8fafc; }
        .items-table tbody td {
            padding: 9px 10px;
            font-size: 10.5px;
            color: #334155;
            vertical-align: middle;
        }
        .items-table tbody td.text-right  { text-align: right; }
        .items-table tbody td.text-center { text-align: center; }

        .item-name   { font-weight: 600; color: #0f172a; }
        .item-notes  { font-size: 9px; color: #94a3b8; margin-top: 2px; font-style: italic; }

        /* Qty badges */
        .qty { font-family: 'DejaVu Sans Mono', monospace; font-weight: 700; font-size: 10px; }
        .qty-accepted { color: #059669; }
        .qty-rejected { color: #dc2626; }
        .qty-ordered  { color: #1d4ed8; }

        /* Rejection reason */
        .rejection-reason {
            font-size: 9px;
            color: #dc2626;
            background: #fff1f2;
            padding: 2px 6px;
            border-radius: 3px;
        }

        /* Totals */
        .totals-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .totals-table td { padding: 6px 10px; font-size: 11px; }
        .totals-table .total-label { text-align: right; color: #64748b; font-weight: 600; width: 70%; }
        .totals-table .total-value { text-align: right; font-family: 'DejaVu Sans Mono', monospace; font-weight: 700; color: #0f172a; width: 30%; }
        .totals-table .grand-row td { background: #0f172a; color: #fff; font-size: 12px; }
        .totals-table .grand-row .total-value { color: #60a5fa; font-size: 13px; }
        .totals-table .separator td { border-top: 1.5px solid #e2e8f0; padding-top: 8px; }

        /* KPI boxes */
        .kpi-row { display: table; width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 8px; }
        .kpi-box {
            display: table-cell;
            width: 25%;
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
            vertical-align: middle;
        }
        .kpi-box-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; font-weight: 700; }
        .kpi-box-value { font-size: 15px; font-weight: 800; color: #0f172a; margin-top: 3px; font-family: 'DejaVu Sans Mono', monospace; }
        .kpi-box.blue  { border-color: #bfdbfe; }
        .kpi-box.blue  .kpi-box-value { color: #1d4ed8; }
        .kpi-box.green { border-color: #bbf7d0; }
        .kpi-box.green .kpi-box-value { color: #059669; }
        .kpi-box.red   { border-color: #fecaca; }
        .kpi-box.red   .kpi-box-value { color: #dc2626; }
        .kpi-box.amber { border-color: #fde68a; }
        .kpi-box.amber .kpi-box-value { color: #d97706; font-size: 12px; }

        /* Notes box */
        .notes-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #3b82f6;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 10.5px;
            color: #475569;
            margin-top: 4px;
        }

        /* Status pill */
        .pill {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .pill-completed { background: #d1fae5; color: #065f46; }
        .pill-draft     { background: #fef3c7; color: #92400e; }
        .pill-cancelled { background: #fee2e2; color: #991b1b; }

        /* Footer */
        .footer {
            margin-top: 28px;
            padding: 14px 32px;
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; vertical-align: bottom; width: 50%; }
        .footer-right { display: table-cell; vertical-align: bottom; text-align: right; width: 50%; }
        .footer-text  { font-size: 9px; color: #94a3b8; }

        /* Signature */
        .sig-box { margin-top: 24px; display: table; width: 100%; }
        .sig-col  { display: table-cell; width: 33%; padding-right: 20px; vertical-align: bottom; }
        .sig-line { border-top: 1px solid #cbd5e1; padding-top: 6px; }
        .sig-label { font-size: 9px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .sig-name  { font-size: 10px; color: #0f172a; font-weight: 600; margin-top: 2px; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-top">
            <div class="header-left">
                <div class="company-name">PATIO BELLA</div>
                <div class="doc-title">Goods Received Note</div>
            </div>
            <div class="header-right">
                <div class="grn-number">{{ $grn->grn_number }}</div>
                <div class="grn-date">Received: {{ $grn->received_date->format('d M Y') }}</div>
                <div class="grn-date" style="margin-top:4px;">
                    <span class="pill pill-{{ $grn->status }}">{{ ucfirst($grn->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── STATUS STRIP ── --}}
    <div class="status-strip">
        <div class="status-item">
            PO Reference
            <strong>{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</strong>
        </div>
        <div class="status-item">
            Vendor
            <strong>{{ $grn->vendor->name ?? 'N/A' }}</strong>
        </div>
        <div class="status-item">
            Received Date
            <strong>{{ $grn->received_date->format('d M Y') }}</strong>
        </div>
        <div class="status-item">
            Created By
            <strong>{{ ($grn->createdBy->first_name ?? '') . ' ' . ($grn->createdBy->last_name ?? 'Procurement') }}</strong>
        </div>
        @if($grn->delivery_note_number)
        <div class="status-item">
            Vendor DN #
            <strong>{{ $grn->delivery_note_number }}</strong>
        </div>
        @endif
    </div>

    {{-- ── BODY ── --}}
    <div class="body">

        {{-- KPI Summary --}}
        <div class="kpi-row">
            <div class="kpi-box blue">
                <div class="kpi-box-label">Total Items</div>
                <div class="kpi-box-value">{{ $grn->items->count() }}</div>
            </div>
            <div class="kpi-box green">
                <div class="kpi-box-label">Total Accepted</div>
                <div class="kpi-box-value">{{ number_format($grn->items->sum('quantity_accepted'), 0) }}</div>
            </div>
            <div class="kpi-box red">
                <div class="kpi-box-label">Total Rejected</div>
                <div class="kpi-box-value">{{ number_format($grn->items->sum('quantity_rejected'), 0) }}</div>
            </div>
            <div class="kpi-box amber">
                <div class="kpi-box-label">Total Payable</div>
                <div class="kpi-box-value">{{ number_format($grn->items->sum('total_cost'), 0) }}</div>
            </div>
        </div>

        {{-- GRN & Vendor Details --}}
        <div class="two-col">
            <div class="col-left">
                <div class="section-title">GRN Information</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">GRN Number</td>
                        <td class="info-value mono">{{ $grn->grn_number }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">PO Reference</td>
                        <td class="info-value mono">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Received Date</td>
                        <td class="info-value">{{ $grn->received_date->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Status</td>
                        <td class="info-value">
                            <span class="pill pill-{{ $grn->status }}">{{ ucfirst($grn->status) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Created By</td>
                        <td class="info-value">{{ ($grn->createdBy->first_name ?? '') . ' ' . ($grn->createdBy->last_name ?? 'Procurement') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Created At</td>
                        <td class="info-value">{{ $grn->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @if($grn->delivery_note_number)
                    <tr>
                        <td class="info-label">Vendor DN #</td>
                        <td class="info-value mono">{{ $grn->delivery_note_number }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="col-right">
                <div class="section-title">Vendor Information</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Vendor Name</td>
                        <td class="info-value">{{ $grn->vendor->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Email</td>
                        <td class="info-value">{{ $grn->vendor->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Phone</td>
                        <td class="info-value">{{ $grn->vendor->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Address</td>
                        <td class="info-value">{{ $grn->vendor->address ?? '—' }}</td>
                    </tr>
                </table>

                {{-- Financial Summary --}}
                <div class="section-title" style="margin-top:16px;">Financial Summary</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">PO Total</td>
                        <td class="info-value" style="font-family:'DejaVu Sans Mono',monospace;">UGX {{ number_format($grn->po_total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">GRN Total</td>
                        <td class="info-value" style="font-family:'DejaVu Sans Mono',monospace; color:#059669;">UGX {{ number_format($grn->grn_total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Amount Payable</td>
                        <td class="info-value" style="font-family:'DejaVu Sans Mono',monospace; color:#1d4ed8; font-weight:700;">UGX {{ number_format($grn->items->sum('total_cost'), 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($grn->notes)
        <div class="section-title">General Notes</div>
        <div class="notes-box">{{ $grn->notes }}</div>
        @endif

        {{-- Items Table --}}
        <div class="section-title" style="margin-top:20px;">
            Received Items &mdash; {{ $grn->items->count() }} Line Item(s)
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:22px;">#</th>
                    <th>Item Description</th>
                    <th class="text-center" style="width:62px;">Ordered</th>
                    <th class="text-center" style="width:62px;">Received</th>
                    <th class="text-center" style="width:62px;">Accepted</th>
                    <th class="text-center" style="width:62px;">Rejected</th>
                    <th class="text-right" style="width:80px;">Unit Cost</th>
                    <th class="text-right" style="width:88px;">Total (UGX)</th>
                    <th style="width:100px;">Rejection Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grn->items as $i => $item)
                <tr class="{{ $i % 2 === 1 ? 'alt' : '' }}">
                    <td style="color:#94a3b8; font-size:9px; font-weight:700;">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="item-name">{{ $item->inventoryItem->name ?? 'N/A' }}</div>
                        @if($item->notes)
                            <div class="item-notes">{{ $item->notes }}</div>
                        @endif
                    </td>
                    <td class="text-center"><span class="qty qty-ordered">{{ number_format($item->quantity_ordered, 2) }}</span></td>
                    <td class="text-center"><span class="qty">{{ number_format($item->quantity_received, 2) }}</span></td>
                    <td class="text-center"><span class="qty qty-accepted">{{ number_format($item->quantity_accepted, 2) }}</span></td>
                    <td class="text-center">
                        @if($item->quantity_rejected > 0)
                            <span class="qty qty-rejected">{{ number_format($item->quantity_rejected, 2) }}</span>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-family:'DejaVu Sans Mono',monospace; color:#475569;">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right" style="font-family:'DejaVu Sans Mono',monospace; font-weight:700; color:#059669;">{{ number_format($item->total_cost, 2) }}</td>
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

        {{-- Totals --}}
        <table class="totals-table">
            <tr>
                <td class="total-label">Total Ordered Quantity:</td>
                <td class="total-value">{{ number_format($grn->items->sum('quantity_ordered'), 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">Total Accepted Quantity:</td>
                <td class="total-value" style="color:#059669;">{{ number_format($grn->items->sum('quantity_accepted'), 2) }}</td>
            </tr>
            @if($grn->items->sum('quantity_rejected') > 0)
            <tr>
                <td class="total-label">Total Rejected Quantity:</td>
                <td class="total-value" style="color:#dc2626;">{{ number_format($grn->items->sum('quantity_rejected'), 2) }}</td>
            </tr>
            @endif
            <tr class="separator">
                <td class="total-label" style="font-size:12px; font-weight:700; color:#0f172a;">TOTAL PAYABLE TO VENDOR (UGX):</td>
                <td class="total-value" style="font-size:13px; color:#1d4ed8;">{{ number_format($grn->items->sum('total_cost'), 2) }}</td>
            </tr>
        </table>

        {{-- Signature Section --}}
        <div class="section-title" style="margin-top:32px;">Authorisation &amp; Signatures</div>
        <div class="sig-box">
            <div class="sig-col">
                <div style="height:36px;"></div>
                <div class="sig-line">
                    <div class="sig-label">Prepared By</div>
                    <div class="sig-name">{{ ($grn->createdBy->first_name ?? '') . ' ' . ($grn->createdBy->last_name ?? 'Procurement Officer') }}</div>
                </div>
            </div>
            <div class="sig-col">
                <div style="height:36px;"></div>
                <div class="sig-line">
                    <div class="sig-label">Verified By</div>
                    <div class="sig-name">&nbsp;</div>
                </div>
            </div>
            <div class="sig-col">
                <div style="height:36px;"></div>
                <div class="sig-line">
                    <div class="sig-label">Approved By</div>
                    <div class="sig-name">&nbsp;</div>
                </div>
            </div>
        </div>

    </div>{{-- end .body --}}

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-text">{{ $grn->grn_number }} &nbsp;·&nbsp; Generated {{ now()->format('d M Y H:i') }}</div>
            <div class="footer-text" style="margin-top:2px;">This document is computer generated and valid without a physical signature unless required.</div>
        </div>
        <div class="footer-right">
            <div class="footer-text" style="font-weight:700; color:#1e3a8a; font-size:10px;">PATIO BELLA</div>
            <div class="footer-text">Procurement Department</div>
        </div>
    </div>

</body>
</html>
