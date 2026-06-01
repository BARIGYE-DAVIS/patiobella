@extends('layouts.management')

@section('title', 'Approve Requisition')
@section('page-title', 'Approve Requisition')

@section('content')

{{-- ════════════════════════════════════════════
     STYLES
════════════════════════════════════════════ --}}
<style>
    /* ── Google Fonts ── */
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    :root {
        --brand:       #166534;
        --brand-light: #dcfce7;
        --brand-mid:   #16a34a;
        --accent:      #0ea5e9;
        --danger:      #dc2626;
        --danger-light:#fef2f2;
        --warn:        #d97706;
        --warn-light:  #fffbeb;
        --neutral-50:  #f8fafc;
        --neutral-100: #f1f5f9;
        --neutral-200: #e2e8f0;
        --neutral-400: #94a3b8;
        --neutral-600: #475569;
        --neutral-800: #1e293b;
        --radius:      10px;
        --shadow-sm:   0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.05);
        --shadow-md:   0 4px 16px rgba(0,0,0,.09);
        --shadow-lg:   0 16px 48px rgba(0,0,0,.14);
    }

    body { font-family: 'DM Sans', sans-serif; color: var(--neutral-800); }

    /* ── Page card ── */
    .req-card {
        background: #fff;
        border: 1px solid var(--neutral-200);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .req-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 28px;
        background: linear-gradient(135deg, #052e16 0%, #14532d 100%);
        color: #fff;
    }
    .req-card__header h3 { font-size: 1.1rem; font-weight: 700; letter-spacing: -.01em; margin: 0; }
    .req-card__header p  { font-size: .78rem; opacity: .65; margin: 2px 0 0; }
    .req-num-badge {
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        padding: 4px 14px;
        border-radius: 99px;
        font-size: .78rem;
        font-family: 'DM Mono', monospace;
        letter-spacing: .04em;
    }

    /* ── Info grid ── */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        padding: 18px 24px;
        background: var(--neutral-50);
        border-bottom: 1px solid var(--neutral-200);
    }
    .info-block__label { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--neutral-400); margin-bottom: 4px; }
    .info-block__value { font-size: .88rem; font-weight: 600; color: var(--neutral-800); }

    /* ── Type badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 99px;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .badge--normal    { background: #d1fae5; color: #065f46; }
    .badge--emergency { background: #fee2e2; color: #991b1b; }
    .badge--cat       { background: var(--neutral-100); color: var(--neutral-600); font-size: .68rem; }

    /* ── Notes banner ── */
    .notes-banner {
        margin: 0 24px 0;
        padding: 12px 16px;
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        border-radius: 0 var(--radius) var(--radius) 0;
        font-size: .84rem;
        color: #78350f;
    }

    /* ── Section heading ── */
    .section-heading {
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--neutral-600);
        padding: 0 24px;
        margin: 20px 0 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-heading::after { content: ''; flex: 1; height: 1px; background: var(--neutral-200); }

    /* ── Items table ── */
    .items-wrap { padding: 0 24px 24px; overflow-x: auto; }
    .items-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--neutral-200);
        border-radius: var(--radius);
        overflow: hidden;
        font-size: .82rem;
    }
    .items-table thead tr { background: var(--neutral-100); }
    .items-table th {
        padding: 10px 12px;
        font-size: .67rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--neutral-400);
        white-space: nowrap;
        border-bottom: 1px solid var(--neutral-200);
    }
    .items-table th:not(:first-child)  { border-left: 1px solid var(--neutral-200); }
    .items-table td {
        padding: 10px 12px;
        vertical-align: middle;
        border-bottom: 1px solid var(--neutral-100);
    }
    .items-table td:not(:first-child)  { border-left: 1px solid var(--neutral-100); }
    .items-table tbody tr:last-child td { border-bottom: none; }
    .items-table tbody tr:hover { background: #f0fdf4; }
    .items-table tbody tr.row-invalid  { background: var(--danger-light) !important; }
    .items-table tfoot tr { background: var(--neutral-50); }
    .items-table tfoot td { padding: 10px 12px; border-top: 1px solid var(--neutral-200); }

    /* ── Stock pills ── */
    .stock-pill {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: .72rem;
        font-weight: 600;
        line-height: 1.4;
        white-space: nowrap;
    }
    .stock-pill__label { font-size: .6rem; font-weight: 500; opacity: .75; }
    .sp-ok   { background: #dcfce7; color: #166534; }
    .sp-warn { background: #fef3c7; color: #92400e; }
    .sp-low  { background: #fee2e2; color: #991b1b; }

    /* ── Qty input ── */
    .qty-wrap { display: flex; flex-direction: column; gap: 4px; min-width: 140px; }
    .qty-row  { display: flex; align-items: center; gap: 6px; }
    .qty-max-btn {
        padding: 4px 8px;
        font-size: .7rem;
        font-weight: 600;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s;
    }
    .qty-max-btn:hover { background: #dbeafe; }
    .qty-input {
        width: 90px;
        padding: 7px 10px;
        border: 1.5px solid var(--neutral-200);
        border-radius: 7px;
        text-align: right;
        font-size: .84rem;
        font-family: 'DM Mono', monospace;
        font-weight: 500;
        background: #fff;
        transition: border-color .2s, box-shadow .2s, background .2s;
        outline: none;
    }
    .qty-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(14,165,233,.12); }
    .qty-input.valid   { border-color: var(--brand-mid); background: #f0fdf4; }
    .qty-input.invalid { border-color: var(--danger);    background: var(--danger-light); }

    .qty-warning {
        font-size: .7rem;
        line-height: 1.4;
        display: block;
        min-height: 14px;
    }
    .qty-warning.error   { color: var(--danger); }
    .qty-warning.success { color: var(--brand-mid); }
    .qty-warning.warning { color: var(--warn); }

    /* ── Notes input ── */
    .notes-input {
        width: 100%;
        min-width: 140px;
        padding: 7px 10px;
        border: 1.5px solid var(--neutral-200);
        border-radius: 7px;
        font-size: .8rem;
        outline: none;
        transition: border-color .2s;
    }
    .notes-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(14,165,233,.12); }

    /* ── Totals row ── */
    .totals-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .legend-wrap { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .legend-item { font-size: .72rem; }
    .totals-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .total-chip {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: .8rem;
        font-weight: 600;
    }
    .total-chip--req { background: var(--neutral-100); color: var(--neutral-600); }
    .total-chip--app { background: var(--brand-light);  color: var(--brand); }
    .total-chip__label { font-weight: 500; font-size: .72rem; }

    /* ── Expiry ── */
    .expiry-expired { color: var(--danger); font-weight: 700; }
    .expiry-near    { color: var(--warn); }
    .expiry-ok      { color: var(--neutral-600); }
    .pack-note      { font-size: .68rem; color: #2563eb; margin-top: 3px; }

    /* ── GM notes textarea ── */
    .gm-notes-area {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--neutral-200);
        border-radius: var(--radius);
        font-size: .85rem;
        font-family: 'DM Sans', sans-serif;
        resize: vertical;
        outline: none;
        transition: border-color .2s;
    }
    .gm-notes-area:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(14,165,233,.12); }

    /* ── Signature blocks ── */
    .sig-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 32px;
        padding: 24px 28px;
        border-top: 1px solid var(--neutral-200);
    }
    .sig-block { display: flex; flex-direction: column; align-items: center; text-align: center; }
    .sig-block__label {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--neutral-400);
        margin-bottom: 12px;
    }
    .sig-block__image-wrap { height: 56px; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 8px; }
    .sig-img { max-height: 52px; max-width: 180px; object-fit: contain; }
    .sig-line { border-top: 1.5px solid var(--neutral-400); width: 200px; padding-top: 6px; }
    .sig-block__name { font-size: .85rem; font-weight: 600; color: var(--neutral-800); margin-top: 4px; }
    .sig-block__date { font-size: .72rem; color: var(--neutral-400); margin-top: 2px; }

    /* ── Action bar ── */
    .action-bar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding: 16px 24px;
        background: var(--neutral-50);
        border-top: 1px solid var(--neutral-200);
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 20px;
        border-radius: 8px;
        font-size: .84rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .18s;
        border: none;
        text-decoration: none;
    }
    .btn--ghost  { background: #fff; border: 1.5px solid var(--neutral-200); color: var(--neutral-600); }
    .btn--ghost:hover  { background: var(--neutral-100); }
    .btn--purple { background: #7c3aed; color: #fff; }
    .btn--purple:hover { background: #6d28d9; }
    .btn--green  { background: var(--brand-mid); color: #fff; }
    .btn--green:hover  { background: var(--brand); }
    .btn--blue   { background: #2563eb; color: #fff; }
    .btn--blue:hover   { background: #1d4ed8; }

    /* ── Toast ── */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: .84rem;
        font-weight: 600;
        box-shadow: var(--shadow-lg);
        animation: toastIn .3s cubic-bezier(.34,1.56,.64,1);
        max-width: 340px;
    }
    .toast--error   { background: var(--danger);    color: #fff; }
    .toast--success { background: var(--brand-mid); color: #fff; }
    .toast--warn    { background: var(--warn);       color: #fff; }
    @keyframes toastIn {
        from { transform: translateX(120%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }

    /* ═══════════════════════════════
       PREVIEW MODAL
    ═══════════════════════════════ */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(15,23,42,.55);
        backdrop-filter: blur(3px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 32px 16px 60px;
        z-index: 9000;
        overflow-y: auto;
    }
    .modal-overlay.hidden { display: none; }
    .modal-box {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 960px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: modalIn .25s cubic-bezier(.34,1.56,.64,1);
        position: relative;
    }
    @keyframes modalIn {
        from { transform: translateY(-24px) scale(.97); opacity: 0; }
        to   { transform: translateY(0) scale(1); opacity: 1; }
    }
    .modal-close {
        position: absolute;
        top: 16px; right: 16px;
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        background: rgba(0,0,0,.08);
        cursor: pointer;
        border: none;
        color: var(--neutral-600);
        transition: background .15s;
    }
    .modal-close:hover { background: rgba(0,0,0,.16); }

    /* Print section inside modal */
    #print-section { padding: 32px 36px; }
    .print-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--neutral-200);
        margin-bottom: 24px;
    }
    .print-header__right { text-align: right; }
    .print-header__title { font-size: 1.25rem; font-weight: 800; color: var(--brand); }
    .print-header__sub   { font-family: 'DM Mono', monospace; font-size: .78rem; color: var(--neutral-400); margin-top: 3px; }
    .company-logo { max-height: 52px; width: auto; }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 28px;
        border-top: 1px solid var(--neutral-200);
        background: var(--neutral-50);
    }

    /* ═══════════════════════════
       PRINT MEDIA
    ═══════════════════════════ */
    @media print {
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            padding: 20px 28px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }

        .no-print, .modal-close, .modal-footer, .action-bar { display: none !important; }

        .print-header { display: flex !important; }
        .company-logo, .sig-img { max-height: 40px !important; }

        .items-table th,
        .items-table td { border: 1px solid #ccc !important; padding: 6px 8px !important; }
        .items-table thead tr { background: #f1f5f9 !important; }

        .sig-grid { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 24px !important; }
        .sig-block { display: flex !important; flex-direction: column !important; align-items: center !important; }
        .sig-block__image-wrap { height: 50px !important; }
    }
</style>

{{-- ════════════════════════════════════════════
     MAIN FORM
════════════════════════════════════════════ --}}
<div class="req-card">

    {{-- Header --}}
    <div class="req-card__header">
        <div>
            <h3>Approve Requisition</h3>
            <p>Review quantities and confirm stock availability before approving.</p>
        </div>
        <span class="req-num-badge">{{ $requisition->requisition_number }}</span>
    </div>

    <form method="POST" action="{{ route('management.requisitions.approve', $requisition->id) }}" id="approveForm">
        @csrf

        {{-- Info grid --}}
        <div class="info-grid">
            <div class="info-block">
                <div class="info-block__label">Type</div>
                <div class="info-block__value">
                    <span class="badge {{ $requisition->requisition_type === 'emergency' ? 'badge--emergency' : 'badge--normal' }}">
                        {{ $requisition->requisition_type === 'emergency' ? '🔴 EMERGENCY' : '✅ Normal' }}
                    </span>
                </div>
            </div>
            <div class="info-block">
                <div class="info-block__label">Store</div>
                <div class="info-block__value">{{ $requisition->store->name ?? 'N/A' }}</div>
            </div>
            <div class="info-block">
                <div class="info-block__label">Requested By</div>
                <div class="info-block__value">
                    {{ $requisition->requestedBy
                        ? $requisition->requestedBy->first_name.' '.$requisition->requestedBy->last_name
                        : 'N/A' }}
                </div>
            </div>
            <div class="info-block">
                <div class="info-block__label">Date Needed</div>
                <div class="info-block__value">
                    {{ $requisition->date_needed ? $requisition->date_needed->format('d M Y') : '—' }}
                </div>
            </div>
            <div class="info-block">
                <div class="info-block__label">Submitted</div>
                <div class="info-block__value">{{ $requisition->created_at->format('d M Y') }}</div>
            </div>
        </div>

        {{-- Notes --}}
        @if($requisition->notes)
        <div style="padding: 12px 24px 0;">
            <div class="notes-banner">
                <strong>Requester Note:</strong> {{ $requisition->notes }}
            </div>
        </div>
        @endif

        {{-- Items heading --}}
        <div class="section-heading">Items to Approve</div>

        {{-- Items table --}}
        <div class="items-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:28px; text-align:center;">#</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Batch No.</th>
                        <th>Expiry</th>
                        <th style="text-align:right;">Requested</th>
                        <th style="text-align:center;">Batch Stock</th>
                        <th style="text-align:center;">Total Stock</th>
                        <th>Unit</th>
                        <th style="min-width:165px;">Approved Qty</th>
                        <th style="min-width:160px;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        function fmtQty($val) {
                            if ($val == floor($val)) return number_format($val, 0);
                            return rtrim(rtrim(number_format($val, 2), '0'), '.');
                        }
                    @endphp

                    @foreach($requisition->items as $index => $item)
                    @php
                        $batch      = $item->batch;
                        $batchStock = $batch ? (float)$batch->remaining_quantity : 0;

                        $totalStock = 0;
                        if ($item->inventory_item_id) {
                            $totalStock = \App\Models\Batch::where('inventory_item_id', $item->inventory_item_id)
                                ->where('batch_status', 'active')
                                ->where('remaining_quantity', '>', 0)
                                ->sum('remaining_quantity');
                        }

                        $bSpClass = $batchStock <= 0 ? 'sp-low' : ($batchStock < 10 ? 'sp-warn' : 'sp-ok');
                        $bSpText  = $batchStock <= 0 ? 'Out of Stock' : ($batchStock < 10 ? 'Low' : 'In Stock');
                        $tSpClass = $totalStock <= 0 ? 'sp-low' : ($totalStock < 10 ? 'sp-warn' : 'sp-ok');
                        $tSpText  = $totalStock <= 0 ? 'Out of Stock' : ($totalStock < 10 ? 'Low' : 'In Stock');

                        $itemName   = $item->item_name ?: ($item->inventoryItem->name ?? 'Unknown Item');
                        $unitOfMeas = $batch->unit_of_measurement ?? ($item->inventoryItem->unit_of_measurement ?? 'pcs');
                        $catName    = $item->category_name ?: ($item->inventoryItem?->category?->name ?? '—');

                        $expiryClass = 'expiry-ok';
                        $expiryExtra = '';
                        if ($batch && $batch->expiry_date) {
                            $daysLeft = now()->diffInDays($batch->expiry_date, false);
                            if ($daysLeft <= 0)  { $expiryClass = 'expiry-expired'; $expiryExtra = ' (EXPIRED)'; }
                            elseif ($daysLeft <= 30) { $expiryClass = 'expiry-near'; $expiryExtra = " ({$daysLeft}d)"; }
                        }
                    @endphp
                    <tr class="item-row" id="row_{{ $index }}">
                        <td style="text-align:center; color:var(--neutral-400); font-size:.75rem;">{{ $index + 1 }}</td>

                        <td>
                            <div style="font-weight:600; font-size:.83rem;">{{ $itemName }}</div>
                            @if($item->inventoryItem?->item_code)
                                <div style="font-size:.68rem; color:var(--neutral-400); font-family:'DM Mono',monospace;">
                                    {{ $item->inventoryItem->item_code }}
                                </div>
                            @endif
                        </td>

                        <td><span class="badge badge--cat">{{ $catName }}</span></td>

                        <td style="font-family:'DM Mono',monospace; font-size:.8rem; color:var(--neutral-600);">
                            {{ $batch->batch_number ?? '—' }}
                        </td>

                        <td>
                            @if($batch && $batch->expiry_date)
                                <span class="{{ $expiryClass }}" style="font-size:.8rem;">
                                    {{ $batch->expiry_date->format('d M Y') }}{{ $expiryExtra }}
                                </span>
                            @else
                                <span style="color:var(--neutral-400);">—</span>
                            @endif
                            @if($batch && $batch->pack_type && $batch->pack_type !== 'Direct')
                                <div class="pack-note">📦 {{ $batch->pack_type }} ({{ $batch->pack_size }}/pack)</div>
                            @endif
                        </td>

                        <td style="text-align:right;">
                            <strong style="font-size:.88rem; font-family:'DM Mono',monospace;">
                                {{ fmtQty($item->quantity_requested) }}
                            </strong>
                        </td>

                        <td style="text-align:center;">
                            <span class="stock-pill {{ $bSpClass }}">
                                <span>{{ fmtQty($batchStock) }}</span>
                                <span class="stock-pill__label">{{ $bSpText }}</span>
                            </span>
                        </td>

                        <td style="text-align:center;">
                            <span class="stock-pill {{ $tSpClass }}">
                                <span>{{ fmtQty($totalStock) }}</span>
                                <span class="stock-pill__label">{{ $tSpText }}</span>
                            </span>
                        </td>

                        <td>
                            <span class="badge badge--cat">{{ $unitOfMeas }}</span>
                        </td>

                        <td>
                            <div class="qty-wrap">
                                <div class="qty-row">
                                    <button type="button" class="qty-max-btn"
                                        onclick="setMaxQty({{ $index }}, {{ $item->quantity_requested }}, '{{ addslashes($itemName) }}', {{ $batchStock }}, '{{ addslashes($unitOfMeas) }}')">
                                        Max
                                    </button>
                                    <input type="number"
                                        class="qty-input"
                                        id="qty_{{ $index }}"
                                        name="items[{{ $index }}][quantity_approved]"
                                        value="{{ $item->quantity_requested }}"
                                        step="0.01"
                                        min="0"
                                        max="{{ $item->quantity_requested }}"
                                        oninput="validateQty({{ $index }}, {{ $item->quantity_requested }}, {{ $batchStock }}, '{{ addslashes($itemName) }}', '{{ addslashes($unitOfMeas) }}')"
                                        required>
                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                </div>
                                <span class="qty-warning" id="warning_{{ $index }}"></span>
                            </div>
                        </td>

                        <td>
                            <input type="text"
                                class="notes-input"
                                name="items[{{ $index }}][notes]"
                                value="{{ $item->notes }}"
                                placeholder="Notes…">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <div class="legend-wrap">
                                <span class="legend-item" style="color:var(--brand-mid);">✓ Full approval</span>
                                <span class="legend-item" style="color:var(--warn);">⚠ Partial / warning</span>
                                <span class="legend-item" style="color:var(--danger);">✗ Invalid</span>
                            </div>
                        </td>
                        <td colspan="6">
                            <div class="totals-row" style="justify-content:flex-end;">
                                <div class="total-chip total-chip--req">
                                    <span class="total-chip__label">Requested:</span>
                                    <span id="totalRequested">{{ number_format($requisition->items->sum('quantity_requested'), 2) }}</span>
                                </div>
                                <div class="total-chip total-chip--app">
                                    <span class="total-chip__label">Approved:</span>
                                    <span id="totalApproved">0.00</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- GM Notes --}}
        <div class="section-heading">GM Approval Notes</div>
        <div style="padding: 0 24px 24px;">
            <textarea name="approval_notes" id="approval_notes" rows="3"
                class="gm-notes-area"
                placeholder="Any additional comments from the General Manager…"></textarea>
        </div>

        {{-- Signatures --}}
        <div class="section-heading">Signatures</div>
        <div class="sig-grid">
            {{-- Requester --}}
            @php $requester = $requisition->requestedBy; @endphp
            <div class="sig-block">
                <div class="sig-block__label">Requested By</div>
                <div class="sig-block__image-wrap">
                    @if($requester && $requester->signature_url)
                        @php
                            $sp = public_path(parse_url($requester->signature_url, PHP_URL_PATH));
                            $se = file_exists($sp);
                            $sm = $se ? mime_content_type($sp) : 'image/png';
                            $sb = $se ? base64_encode(file_get_contents($sp)) : null;
                        @endphp
                        @if($sb)
                            <img src="data:{{ $sm }};base64,{{ $sb }}" class="sig-img" alt="Signature">
                        @else
                            <img src="{{ $requester->signature_url }}" class="sig-img" alt="Signature">
                        @endif
                    @endif
                </div>
                <div class="sig-line"></div>
                <div class="sig-block__name">{{ ($requester->first_name ?? '').' '.($requester->last_name ?? '') }}</div>
                <div class="sig-block__date">{{ $requisition->created_at->format('d M Y') }}</div>
            </div>

            {{-- Approver --}}
            @php
                $cu = Auth::user();
                $ap = public_path(parse_url($cu->signature_url ?? '', PHP_URL_PATH));
                $ae = $cu->signature_url && file_exists($ap);
                $am = $ae ? mime_content_type($ap) : 'image/png';
                $ab = $ae ? base64_encode(file_get_contents($ap)) : null;
            @endphp
            <div class="sig-block">
                <div class="sig-block__label">Approved By (General Manager)</div>
                <div class="sig-block__image-wrap">
                    @if($ab)
                        <img src="data:{{ $am }};base64,{{ $ab }}" class="sig-img" alt="Signature">
                    @elseif($cu->signature_url)
                        <img src="{{ $cu->signature_url }}" class="sig-img" alt="Signature">
                    @else
                        <span style="font-size:.75rem; color:var(--neutral-400);">No signature on file</span>
                    @endif
                </div>
                <div class="sig-line"></div>
                <div class="sig-block__name">{{ $cu->first_name }} {{ $cu->last_name }}</div>
                <div class="sig-block__date">Will be signed upon approval</div>
            </div>
        </div>

        {{-- Action bar --}}
        <div class="action-bar no-print">
            <a href="{{ route('management.requisitions.show', $requisition->id) }}" class="btn btn--ghost">Cancel</a>
            <button type="button" class="btn btn--purple" onclick="openPreview()">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                Preview &amp; Submit
            </button>
        </div>
    </form>
</div>

{{-- ════════════════════════════════════════════
     PREVIEW MODAL
════════════════════════════════════════════ --}}
@php
    $logo        = \App\Models\BusinessSetting::getLogo();
    $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
    $logoB64     = null; $logoMime = 'image/png';
    if ($logo) {
        $lp = public_path(parse_url($logo, PHP_URL_PATH));
        if (file_exists($lp)) { $logoMime = mime_content_type($lp); $logoB64 = base64_encode(file_get_contents($lp)); }
    }
@endphp

<div id="previewModal" class="modal-overlay hidden no-print">
    <div class="modal-box">
        <button class="modal-close no-print" onclick="closePreview()" title="Close">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>

        {{-- Everything inside #print-section is printed --}}
        <div id="print-section">

            {{-- Print Header --}}
            <div class="print-header">
                <div>
                    @if($logoB64)
                        <img src="data:{{ $logoMime }};base64,{{ $logoB64 }}" class="company-logo" alt="{{ $companyName }}">
                    @elseif($logo)
                        <img src="{{ $logo }}" class="company-logo" alt="{{ $companyName }}">
                    @else
                        <div style="font-size:1.1rem; font-weight:800; color:var(--neutral-800);">{{ $companyName }}</div>
                    @endif
                </div>
                <div class="print-header__right">
                    <div class="print-header__title">REQUISITION APPROVAL</div>
                    <div class="print-header__sub">{{ $requisition->requisition_number }}</div>
                    <div style="font-size:.72rem; color:var(--neutral-400); margin-top:2px;">{{ now()->format('d M Y') }}</div>
                </div>
            </div>

            {{-- Modal info grid --}}
            <div class="info-grid" style="border-radius:10px; margin-bottom:20px; border:1px solid var(--neutral-200);">
                <div class="info-block">
                    <div class="info-block__label">Type</div>
                    <div class="info-block__value" id="previewType"></div>
                </div>
                <div class="info-block">
                    <div class="info-block__label">Store</div>
                    <div class="info-block__value">{{ $requisition->store->name ?? 'N/A' }}</div>
                </div>
                <div class="info-block">
                    <div class="info-block__label">Requested By</div>
                    <div class="info-block__value">
                        {{ $requisition->requestedBy
                            ? $requisition->requestedBy->first_name.' '.$requisition->requestedBy->last_name
                            : 'N/A' }}
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block__label">Date Needed</div>
                    <div class="info-block__value">
                        {{ $requisition->date_needed ? $requisition->date_needed->format('d M Y') : '—' }}
                    </div>
                </div>
            </div>

            {{-- Preview items table --}}
            <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--neutral-400); margin-bottom:8px;">
                Approved Items
            </div>
            <div style="overflow-x:auto; margin-bottom:24px;">
                <table class="items-table" style="font-size:.8rem;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Batch No.</th>
                            <th style="text-align:right;">Requested</th>
                            <th style="text-align:right;">Approved</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="previewItemsBody"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align:right; font-weight:700; font-size:.8rem;">TOTAL</td>
                            <td style="text-align:right; font-weight:700; font-family:'DM Mono',monospace;" id="pTotalReq">—</td>
                            <td style="text-align:right; font-weight:700; color:var(--brand-mid); font-family:'DM Mono',monospace;" id="pTotalApp">—</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- GM Notes in preview --}}
            <div style="padding:12px 14px; background:var(--neutral-50); border-radius:8px; border:1px solid var(--neutral-200); margin-bottom:24px;">
                <div style="font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--neutral-400); margin-bottom:4px;">GM Approval Notes</div>
                <div style="font-size:.85rem; color:var(--neutral-800);" id="previewApprovalNotes">—</div>
            </div>

            {{-- Signatures in preview --}}
            <div class="sig-grid" style="border-top:1px solid var(--neutral-200); padding-top:24px;">
                {{-- Requester --}}
                @php $requester = $requisition->requestedBy; @endphp
                <div class="sig-block">
                    <div class="sig-block__label">Requested By</div>
                    <div class="sig-block__image-wrap">
                        @if($requester && $requester->signature_url)
                            @php
                                $sp2 = public_path(parse_url($requester->signature_url, PHP_URL_PATH));
                                $se2 = file_exists($sp2);
                                $sm2 = $se2 ? mime_content_type($sp2) : 'image/png';
                                $sb2 = $se2 ? base64_encode(file_get_contents($sp2)) : null;
                            @endphp
                            @if($sb2)
                                <img src="data:{{ $sm2 }};base64,{{ $sb2 }}" class="sig-img" alt="Signature">
                            @else
                                <img src="{{ $requester->signature_url }}" class="sig-img" alt="Signature">
                            @endif
                        @endif
                    </div>
                    <div class="sig-line"></div>
                    <div class="sig-block__name">{{ ($requester->first_name ?? '').' '.($requester->last_name ?? '') }}</div>
                    <div class="sig-block__date">{{ $requisition->created_at->format('d M Y') }}</div>
                </div>

                {{-- Approver --}}
                <div class="sig-block">
                    <div class="sig-block__label">Approved By (General Manager)</div>
                    <div class="sig-block__image-wrap">
                        @if($ab)
                            <img src="data:{{ $am }};base64,{{ $ab }}" class="sig-img" alt="Signature">
                        @elseif($cu->signature_url)
                            <img src="{{ $cu->signature_url }}" class="sig-img" alt="Signature">
                        @else
                            <span style="font-size:.75rem; color:var(--neutral-400);">No signature on file</span>
                        @endif
                    </div>
                    <div class="sig-line"></div>
                    <div class="sig-block__name">{{ $cu->first_name }} {{ $cu->last_name }}</div>
                    <div class="sig-block__date">{{ now()->format('d M Y') }}</div>
                </div>
            </div>

            {{-- Footer --}}
            <div style="text-align:center; margin-top:24px; padding-top:16px; border-top:1px solid var(--neutral-200);">
                <div style="font-size:.68rem; color:var(--neutral-400);">This is a computer-generated approval document.</div>
                <div style="font-size:.68rem; color:var(--neutral-400);">{{ $companyName }} — All Rights Reserved</div>
            </div>
        </div>{{-- /print-section --}}

        {{-- Modal footer --}}
        <div class="modal-footer no-print">
            <button type="button" class="btn btn--ghost" onclick="closePreview()">← Edit</button>
            <button type="button" class="btn btn--blue" onclick="printApproval()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print
            </button>
            <button type="button" class="btn btn--green" onclick="submitApproval()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
                Confirm &amp; Submit
            </button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════ --}}
<script>
    /* ── Helpers ──────────────────────────────── */
    function fmtQty(val) {
        if (isNaN(val)) return '0';
        return val % 1 === 0 ? val.toFixed(0) : parseFloat(val.toFixed(2)).toString();
    }

    function esc(text) {
        const d = document.createElement('div');
        d.textContent = text || '';
        return d.innerHTML;
    }

    /* ── Toast ────────────────────────────────── */
    function showToast(msg, type = 'error') {
        document.querySelectorAll('.toast').forEach(t => t.remove());
        const t = document.createElement('div');
        t.className = `toast toast--${type}`;
        t.innerHTML = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3200);
    }

    /* ── Quantity helpers ─────────────────────── */
    function setMaxQty(idx, maxQty, name, batchStock, unit) {
        const inp = document.getElementById(`qty_${idx}`);
        inp.value = maxQty;
        validateQty(idx, maxQty, batchStock, name, unit);
        showToast(`✅ <strong>${esc(name)}</strong>: set to max (${fmtQty(maxQty)})`, 'success');
    }

    function validateQty(idx, maxQty, batchStock, name, unit) {
        const inp  = document.getElementById(`qty_${idx}`);
        const warn = document.getElementById(`warning_${idx}`);
        const row  = document.getElementById(`row_${idx}`);
        let val    = parseFloat(inp.value);

        inp.classList.remove('valid', 'invalid');
        warn.className = 'qty-warning';
        warn.textContent = '';
        row.classList.remove('row-invalid');

        if (inp.value === '' || isNaN(val)) {
            warn.textContent = '⚠ Enter approved quantity';
            warn.classList.add('warning');
            updateTotals();
            return false;
        }

        if (val > maxQty) {
            inp.value = maxQty;
            val = maxQty;
            showToast(`⚠ <strong>${esc(name)}</strong>: capped at ${fmtQty(maxQty)}`, 'warn');
        }

        if (val < 0) {
            inp.classList.add('invalid');
            row.classList.add('row-invalid');
            warn.textContent = '✗ Quantity cannot be negative';
            warn.classList.add('error');
            updateTotals();
            return false;
        }

        inp.classList.add('valid');

        if (val === 0) {
            warn.textContent = '⚠ Zero — this item will be skipped';
            warn.classList.add('warning');
        } else if (val > batchStock && batchStock > 0) {
            warn.textContent = `⚠ Exceeds batch stock (${fmtQty(batchStock)} ${unit})`;
            warn.classList.add('warning');
        } else if (val === maxQty) {
            warn.textContent = '✓ Full quantity approved';
            warn.classList.add('success');
        } else {
            const pct = ((val / maxQty) * 100).toFixed(1);
            warn.textContent = `✓ Partial: ${fmtQty(val)} / ${fmtQty(maxQty)} (${pct}%)`;
            warn.classList.add('success');
        }

        updateTotals();
        return true;
    }

    function updateTotals() {
        let total = 0;
        document.querySelectorAll('input[name*="[quantity_approved]"]').forEach(inp => {
            const v = parseFloat(inp.value);
            const m = parseFloat(inp.getAttribute('max'));
            if (!isNaN(v) && v >= 0 && v <= m) total += v;
        });
        document.getElementById('totalApproved').textContent = total.toFixed(2);
    }

    /* ── Preview ──────────────────────────────── */
    function openPreview() {
        let rows = '', tReq = 0, tApp = 0;

        document.querySelectorAll('.item-row').forEach((row, i) => {
            const name     = row.querySelector('td:nth-child(2)').innerText.trim();
            const cat      = row.querySelector('td:nth-child(3) .badge').innerText.trim();
            const batch    = row.querySelector('td:nth-child(4)').innerText.trim();
            const reqVal   = parseFloat(row.querySelector('td:nth-child(6) strong').innerText.replace(/,/g,'')) || 0;
            const appInp   = row.querySelector('input[name*="[quantity_approved]"]');
            const appVal   = parseFloat(appInp.value) || 0;
            const noteInp  = row.querySelector('input[name*="[notes]"]');
            const note     = noteInp ? noteInp.value : '';

            tReq += reqVal;
            tApp += appVal;

            const appColor = appVal > 0 ? 'var(--brand-mid)' : 'var(--danger)';
            rows += `
                <tr>
                    <td style="text-align:center; color:var(--neutral-400); font-size:.72rem;">${i+1}</td>
                    <td style="font-weight:600;">${esc(name)}</td>
                    <td><span class="badge badge--cat">${esc(cat)}</span></td>
                    <td style="font-family:'DM Mono',monospace; font-size:.78rem;">${esc(batch)}</td>
                    <td style="text-align:right; font-family:'DM Mono',monospace;">${fmtQty(reqVal)}</td>
                    <td style="text-align:right; font-family:'DM Mono',monospace; font-weight:700; color:${appColor};">${fmtQty(appVal)}</td>
                    <td style="color:var(--neutral-600); font-size:.78rem;">${esc(note) || '—'}</td>
                </tr>`;
        });

        document.getElementById('previewItemsBody').innerHTML = rows;
        document.getElementById('pTotalReq').textContent = fmtQty(tReq);
        document.getElementById('pTotalApp').textContent = fmtQty(tApp);
        document.getElementById('previewApprovalNotes').textContent =
            document.getElementById('approval_notes').value.trim() || '—';

        const isEmergency = {{ $requisition->requisition_type === 'emergency' ? 'true' : 'false' }};
        document.getElementById('previewType').innerHTML = isEmergency
            ? '<span class="badge badge--emergency">🔴 EMERGENCY</span>'
            : '<span class="badge badge--normal">✅ Normal</span>';

        document.getElementById('previewModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    /* ── Print ────────────────────────────────── */
    function printApproval() {
        const content = document.getElementById('print-section').innerHTML;
        const win = window.open('', '_blank', 'width=900,height=700');
        win.document.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Approval — {{ $requisition->requisition_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DM Sans', Arial, sans-serif; font-size: 11px; padding: 24px 32px; color: #1e293b; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
  th, td { border: 1px solid #e2e8f0; padding: 6px 9px; text-align: left; vertical-align: middle; }
  thead th { background: #f1f5f9; font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #64748b; font-weight: 700; }
  tfoot td { background: #f8fafc; font-weight: 700; }
  .print-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 20px; }
  .print-header__title { font-size: 15px; font-weight: 800; color: #166534; }
  .print-header__sub   { font-family: 'DM Mono', monospace; font-size: 9px; color: #94a3b8; margin-top: 3px; }
  .company-logo { max-height: 40px; width: auto; }
  .sig-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 20px; }
  .sig-block { display: flex; flex-direction: column; align-items: center; text-align: center; }
  .sig-block__label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; margin-bottom: 10px; }
  .sig-block__image-wrap { height: 48px; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 6px; }
  .sig-img { max-height: 44px; max-width: 160px; object-fit: contain; }
  .sig-line { border-top: 1.5px solid #94a3b8; width: 180px; padding-top: 5px; }
  .sig-block__name { font-size: 11px; font-weight: 600; margin-top: 4px; }
  .sig-block__date { font-size: 9px; color: #94a3b8; margin-top: 2px; }
  .info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; margin-bottom: 18px; }
  .info-block__label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 3px; }
  .info-block__value { font-size: 11px; font-weight: 600; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
  .badge--normal    { background: #d1fae5; color: #065f46; }
  .badge--emergency { background: #fee2e2; color: #991b1b; }
  .badge--cat       { background: #f1f5f9; color: #475569; }
  .no-print, .modal-close, .modal-footer { display: none !important; }
  .gm-notes-box { padding: 10px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 18px; }
  .gm-notes-box .lbl { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; margin-bottom: 3px; }
  .page-footer { text-align: center; margin-top: 20px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; }
</style>
</head>
<body>${content}</body>
</html>`);
        win.document.close();
        win.focus();
        setTimeout(() => { win.print(); }, 600);
    }

    /* ── Submit ───────────────────────────────── */
    function submitApproval() {
        closePreview();
        document.getElementById('approveForm').submit();
    }

    /* ── Close modal on backdrop click ── */
    document.getElementById('previewModal').addEventListener('click', function(e) {
        if (e.target === this) closePreview();
    });

    /* ── Keyboard: Escape closes modal ── */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePreview();
    });

    /* ── Init validation on page load ── */
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[name*="[quantity_approved]"]').forEach(function (inp, idx) {
            const maxQty     = parseFloat(inp.getAttribute('max')) || 0;
            const row        = inp.closest('.item-row');
            const bsCell     = row.querySelector('td:nth-child(7) .stock-pill');
            const batchStock = bsCell ? parseFloat(bsCell.querySelector('span:first-child').textContent.replace(/,/g, '')) || 0 : 0;
            const itemName   = row.querySelector('td:nth-child(2)').innerText.trim();
            const unitBadge  = row.querySelector('td:nth-child(9) .badge');
            const unit       = unitBadge ? unitBadge.textContent.trim() : 'pcs';
            validateQty(idx, maxQty, batchStock, itemName, unit);
        });
        updateTotals();
    });
</script>

@endsection
