@extends('layouts.procurement')

@section('title', 'Create LPO')
@section('page-title', 'Create Local Purchase Order')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     EXTERNAL DEPENDENCIES
═══════════════════════════════════════════════════════ --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- ═══════════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════════ --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

:root {
    --primary:       #1e40af;
    --primary-light: #dbeafe;
    --primary-dark:  #1e3a8a;
    --success:       #15803d;
    --success-light: #dcfce7;
    --danger:        #dc2626;
    --danger-light:  #fef2f2;
    --warn:          #b45309;
    --warn-light:    #fef3c7;
    --neutral-50:    #f8fafc;
    --neutral-100:   #f1f5f9;
    --neutral-200:   #e2e8f0;
    --neutral-400:   #94a3b8;
    --neutral-600:   #475569;
    --neutral-700:   #334155;
    --neutral-800:   #1e293b;
    --radius:        10px;
    --shadow-sm:     0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:     0 4px 16px rgba(0,0,0,.08);
    --shadow-lg:     0 16px 48px rgba(0,0,0,.14);
}

*, *::before, *::after { box-sizing: border-box; }
body { font-family: 'Sora', sans-serif; color: var(--neutral-800); }

/* ── Page card ─────────────────────────────────────── */
.lpo-card {
    background: #fff;
    border: 1px solid var(--neutral-200);
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
}
.lpo-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 28px;
    background:  #b33e07;
    color: #fff;
}
.lpo-card__header h3  { font-size: 1.05rem; font-weight: 700; margin: 0; letter-spacing: -.01em; }
.lpo-card__header p   { font-size: .75rem; opacity: .65; margin: 3px 0 0; }
.lpo-ref-badge {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    padding: 4px 14px;
    border-radius: 99px;
    font-family: 'JetBrains Mono', monospace;
    font-size: .75rem;
    letter-spacing: .04em;
}

/* ── Form body ─────────────────────────────────────── */
.form-body  { padding: 28px; }
.form-grid  { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
.col-span-2 { grid-column: span 2; }
@media (max-width: 640px) {
    .form-grid  { grid-template-columns: 1fr; }
    .col-span-2 { grid-column: span 1; }
}

/* ── Form fields ───────────────────────────────────── */
.field-label {
    display: block;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--neutral-600);
    margin-bottom: 6px;
}
.field-required { color: var(--danger); }

.field-control {
    width: 100%;
    padding: 9px 13px;
    border: 1.5px solid var(--neutral-200);
    border-radius: 8px;
    font-size: .85rem;
    font-family: 'Sora', sans-serif;
    color: var(--neutral-800);
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.field-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(30,64,175,.12);
}
.field-control[readonly] { background: var(--neutral-50); cursor: not-allowed; }
.field-control.select2-hidden-accessible + .select2-container .select2-selection { min-height: 40px; }

/* ── Section divider ───────────────────────────────── */
.section-divider {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0;
    margin: 8px 0 18px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .09em;
    color: var(--neutral-600);
}
.section-divider::after { content: ''; flex: 1; height: 1px; background: var(--neutral-200); }

/* ── Items table ───────────────────────────────────── */
.items-wrap { overflow-x: auto; margin-top: 4px; }
.items-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid var(--neutral-200);
    border-radius: var(--radius);
    overflow: hidden;
    font-size: .81rem;
}
.items-table thead tr { background: var(--neutral-100); }
.items-table th {
    padding: 10px 12px;
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--neutral-400);
    border-bottom: 1px solid var(--neutral-200);
    white-space: nowrap;
}
.items-table th:not(:first-child) { border-left: 1px solid var(--neutral-200); }
.items-table td {
    padding: 10px 12px;
    vertical-align: middle;
    border-bottom: 1px solid var(--neutral-100);
}
.items-table td:not(:first-child) { border-left: 1px solid var(--neutral-100); }
.items-table tbody tr:last-child td { border-bottom: none; }
.items-table tbody tr:hover { background: #eff6ff; }
.items-table tfoot tr { background: var(--neutral-50); }
.items-table tfoot td {
    padding: 10px 14px;
    font-size: .82rem;
    border-top: 1px solid var(--neutral-200);
}
.items-table tfoot td:not(:first-child) { border-left: 1px solid var(--neutral-200); }

/* ── Total rows ────────────────────────────────────── */
.total-row-grand { background: #eff6ff !important; }
.total-row-grand td { color: var(--primary-dark); font-weight: 700; }

/* ── Quantity / cost inputs ────────────────────────── */
.qty-wrap  { display: flex; flex-direction: column; gap: 4px; min-width: 110px; }
.qty-input,
.cost-input {
    padding: 7px 9px;
    border: 1.5px solid var(--neutral-200);
    border-radius: 7px;
    text-align: right;
    font-size: .82rem;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 500;
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s, background .2s;
    width: 100%;
}
.qty-input:focus,
.cost-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,64,175,.12); }
.qty-input.valid   { border-color: var(--success); background: var(--success-light); }
.qty-input.invalid { border-color: var(--danger);  background: var(--danger-light); }
.cost-input { min-width: 120px; }

.qty-warning {
    font-size: .68rem;
    line-height: 1.4;
    min-height: 14px;
}
.qty-warning.error   { color: var(--danger); }
.qty-warning.success { color: var(--success); }
.qty-warning.warning { color: var(--warn); }

/* ── Total cell ────────────────────────────────────── */
.total-cell {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 600;
    font-size: .82rem;
    text-align: right;
    color: var(--success);
    background: #f0fdf4;
    white-space: nowrap;
}

/* ── Badges ────────────────────────────────────────── */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 99px;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.badge--normal    { background: var(--success-light); color: #065f46; }
.badge--emergency { background: var(--danger-light);  color: #991b1b; }
.badge--metrics   { background: var(--neutral-100);   color: var(--neutral-600); font-size: .68rem; }
.badge--approved  { background: var(--success-light); color: var(--success); }

/* ── Batch info ────────────────────────────────────── */
.batch-mono   { font-family: 'JetBrains Mono', monospace; font-size: .75rem; font-weight: 600; color: var(--neutral-700); }
.expiry-ok    { font-size: .72rem; color: var(--neutral-600); }
.expiry-warn  { font-size: .72rem; color: var(--warn); }
.expiry-error { font-size: .72rem; color: var(--danger); font-weight: 700; }
.pack-note    { font-size: .68rem; color: #2563eb; margin-top: 2px; }

/* ── Remove button ─────────────────────────────────── */
.btn-remove {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px; height: 28px;
    border: none;
    border-radius: 6px;
    background: var(--danger-light);
    color: var(--danger);
    cursor: pointer;
    transition: background .15s;
}
.btn-remove:hover { background: #fecaca; }

/* ── Action bar ────────────────────────────────────── */
.action-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    padding: 16px 28px;
    background: var(--neutral-50);
    border-top: 1px solid var(--neutral-200);
}
.btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 20px;
    border-radius: 8px;
    font-size: .84rem;
    font-weight: 600;
    font-family: 'Sora', sans-serif;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all .18s;
}
.btn--ghost  { background: #fff; border: 1.5px solid var(--neutral-200); color: var(--neutral-600); }
.btn--ghost:hover  { background: var(--neutral-100); }
.btn--purple { background: #7c3aed; color: #fff; }
.btn--purple:hover { background: #6d28d9; }
.btn--green  { background: #16a34a; color: #fff; }
.btn--green:hover  { background: var(--success); }
.btn--blue   { background: var(--primary); color: #fff; }
.btn--blue:hover   { background: var(--primary-dark); }

/* ── Modal ─────────────────────────────────────────── */
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
    max-width: 980px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    animation: modalIn .25s cubic-bezier(.34,1.56,.64,1);
    position: relative;
}
@keyframes modalIn {
    from { transform: translateY(-20px) scale(.97); opacity: 0; }
    to   { transform: translateY(0) scale(1); opacity: 1; }
}
.modal-close {
    position: absolute; top: 14px; right: 14px;
    width: 30px; height: 30px;
    border: none; border-radius: 50%; background: rgba(0,0,0,.08);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--neutral-600); transition: background .15s;
}
.modal-close:hover { background: rgba(0,0,0,.16); }
.modal-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 16px 28px;
    border-top: 1px solid var(--neutral-200);
    background: var(--neutral-50);
}

/* ── Print section ─────────────────────────────────── */
#print-section { padding: 32px 36px; }
.print-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 18px;
    border-bottom: 2px solid var(--neutral-200);
    margin-bottom: 22px;
}
.print-header__title { font-size: 1.2rem; font-weight: 800; color: var(--primary); }
.print-header__sub   { font-family: 'JetBrains Mono', monospace; font-size: .75rem; color: var(--neutral-400); margin-top: 3px; }
.company-logo { max-height: 52px; width: auto; }

/* ── Info grid ─────────────────────────────────────── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 14px;
    padding: 16px 20px;
    background: var(--neutral-50);
    border: 1px solid var(--neutral-200);
    border-radius: var(--radius);
    margin-bottom: 22px;
}
.info-block__label { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--neutral-400); margin-bottom: 3px; }
.info-block__value { font-size: .85rem; font-weight: 600; color: var(--neutral-800); }

/* ── Signature block ───────────────────────────────── */
.sig-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 32px;
    padding: 20px 0 0;
    margin-top: 28px;
    border-top: 1px solid var(--neutral-200);
}
.sig-block { display: flex; flex-direction: column; align-items: center; text-align: center; }
.sig-block__label { font-size: .66rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--neutral-400); margin-bottom: 10px; }
.sig-block__img-wrap { height: 54px; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 7px; }
.sig-img { max-height: 50px; max-width: 170px; object-fit: contain; }
.sig-line { border-top: 1.5px solid var(--neutral-400); width: 190px; padding-top: 6px; }
.sig-block__name { font-size: .84rem; font-weight: 600; margin-top: 4px; }
.sig-block__date { font-size: .7rem; color: var(--neutral-400); margin-top: 2px; }
.sig-pending {
    display: inline-flex; align-items: center;
    padding: 4px 12px;
    background: var(--warn-light);
    color: var(--warn);
    border-radius: 99px;
    font-size: .72rem; font-weight: 600;
}

/* ── Amount in words ───────────────────────────────── */
.amount-words-box {
    padding: 12px 16px;
    background: var(--neutral-50);
    border: 1px solid var(--neutral-200);
    border-radius: 8px;
    margin: 16px 0;
}
.amount-words-box__label { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--neutral-400); margin-bottom: 4px; }
.amount-words-box__value { font-size: .88rem; font-weight: 600; color: var(--neutral-800); }

/* ══════════════════════
   PRINT MEDIA QUERY
══════════════════════ */
@media print {
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    body * { visibility: hidden; }
    #print-section, #print-section * { visibility: visible; }
    #print-section {
        position: absolute; top: 0; left: 0; width: 100%;
        padding: 20px 28px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
    }
    .no-print, .modal-close, .modal-footer, .action-bar { display: none !important; }
    .company-logo { max-height: 40px !important; }
    .sig-img { max-height: 40px !important; }
    .items-table th, .items-table td { border: 1px solid #ccc !important; padding: 6px 8px !important; }
    .items-table thead tr { background: #f1f5f9 !important; }
    .sig-grid { display: grid !important; grid-template-columns: 1fr 1fr !important; }
    .print-header { display: flex !important; }
    .info-grid { display: grid !important; grid-template-columns: repeat(3,1fr) !important; }
}
</style>

{{-- ═══════════════════════════════════════════════════════
     MAIN FORM CARD
═══════════════════════════════════════════════════════ --}}
<div class="lpo-card">

    {{-- Header --}}
    <div class="lpo-card__header">
        <div>
            <h3>Create Local Purchase Order (LPO)</h3>
            <p>From Requisition: {{ $requisition->requisition_number }}</p>
        </div>
        <span class="lpo-ref-badge">{{ $requisition->requisition_number }}</span>
    </div>

    <form method="POST" action="{{ route('procurement.lpo.store') }}" id="lpoForm">
        @csrf
        <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">

        <div class="form-body">

            {{-- ── LPO Details ─────────────────────────────── --}}
            <div class="section-divider">LPO Details</div>
            <div class="form-grid">

                {{-- LPO Type --}}
                <div>
                    <label class="field-label" for="type">
                        LPO Type <span class="field-required">*</span>
                    </label>
                    <select name="type" id="type" required class="field-control">
                        <option value="normal">Normal</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>

                {{-- Vendor --}}
                <div>
                    <label class="field-label" for="vendor_id">
                        Vendor <span class="field-required">*</span>
                    </label>
                    <select name="vendor_id" id="vendor_id" required class="field-control">
                        <option value="">— Select Vendor —</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- LPO Date --}}
                <div>
                    <label class="field-label" for="lpo_date">
                        LPO Date <span class="field-required">*</span>
                    </label>
                    <input type="date" name="lpo_date" id="lpo_date"
                           value="{{ date('Y-m-d') }}" required readonly
                           class="field-control">
                </div>

                {{-- Expected Delivery --}}
                <div>
                    <label class="field-label" for="expected_delivery_date">
                        Expected Delivery Date
                    </label>
                    <input type="date" name="expected_delivery_date" id="expected_delivery_date"
                           min="{{ date('Y-m-d') }}"
                           class="field-control">
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="field-label" for="payment_method">
                        Payment Method <span class="field-required">*</span>
                    </label>
                    <select name="payment_method" id="payment_method" required class="field-control">
                        <option value="cash">Cash</option>
                        <option value="credit">Credit</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                {{-- VAT Rate --}}
                <div>
                    <label class="field-label" for="vat_rate">
                        VAT Rate (%)
                    </label>
                    <input type="number" name="vat_rate" id="vat_rate"
                           step="0.01" min="0" max="100" value="18"
                           class="field-control">
                </div>

                {{-- Delivery Address --}}
                <div class="col-span-2">
                    <label class="field-label" for="delivery_address">Delivery Address</label>
                    <input type="text" name="delivery_address" id="delivery_address"
                           class="field-control"
                           placeholder="Enter delivery address…">
                </div>

                {{-- Delivery Instructions --}}
                <div class="col-span-2">
                    <label class="field-label" for="delivery_instructions">Delivery Instructions</label>
                    <textarea name="delivery_instructions" id="delivery_instructions" rows="2"
                              class="field-control"
                              placeholder="Any special delivery instructions…"></textarea>
                </div>

                {{-- LPO Notes --}}
                <div class="col-span-2">
                    <label class="field-label" for="notes">LPO Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="field-control"
                              placeholder="Any additional notes…"></textarea>
                </div>
            </div>

            {{-- ── Items ────────────────────────────────────── --}}
            <div class="section-divider" style="margin-top:24px;">Items</div>
            <div class="items-wrap">
                <table class="items-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th style="width:22%;">Item</th>
                            <th style="width:12%;">Batch Details</th>
                            <th style="width:8%;">Unit</th>
                            <th style="width:9%; text-align:right;">Approved Qty</th>
                            <th style="width:13%;">Order Qty</th>
                            <th style="width:13%;">Unit Cost (UGX)</th>
                            <th style="width:13%; text-align:right;">Total (UGX)</th>
                            <th style="width:6%; text-align:center;">Del</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">

                        @foreach($requisition->items as $index => $item)
                        @php
                            $batch       = $item->batch;
                            $batchNumber = $batch?->batch_number ?? 'N/A';
                            $expiryDate  = $batch?->expiry_date ?? null;
                            $daysLeft    = $expiryDate ? now()->diffInDays($expiryDate, false) : null;
                            $expiryClass = '';
                            if ($daysLeft !== null) {
                                $expiryClass = $daysLeft <= 0 ? 'expiry-error' : ($daysLeft <= 30 ? 'expiry-warn' : 'expiry-ok');
                            }
                            $packInfo    = '';
                            if ($batch?->pack_type && $batch->pack_type !== 'Direct' && $batch->pack_size > 1) {
                                $packInfo = $batch->pack_type . ' (' . $batch->pack_size . '/pack)';
                            }
                            $unitCost    = $item->unit_cost ?? ($batch?->unit_cost ?? 0);
                            $itemName    = $item->item_name ?: ($item->inventoryItem?->name ?? 'Item not found');
                            $metrics     = $item->metrics ?: 'pcs';
                        @endphp
                        <tr class="item-row" id="row_{{ $index }}">

                            {{-- Item name + hidden fields --}}
                            <td>
                                <div style="font-weight:600; font-size:.83rem;">{{ $itemName }}</div>
                                @if($item->inventoryItem?->item_code)
                                    <div style="font-size:.68rem; color:var(--neutral-400); font-family:'JetBrains Mono',monospace;">
                                        {{ $item->inventoryItem->item_code }}
                                    </div>
                                @endif
                                {{-- Hidden inputs --}}
                                <input type="hidden" name="items[{{ $index }}][inventory_item_id]"   value="{{ $item->inventory_item_id }}">
                                <input type="hidden" name="items[{{ $index }}][requisition_item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $index }}][max_quantity]"        value="{{ $item->quantity_approved }}" class="max-quantity">
                                <input type="hidden" name="items[{{ $index }}][batch_id]"            value="{{ $batch?->id ?? '' }}">
                                <input type="hidden" name="items[{{ $index }}][batch_number]"        value="{{ $batchNumber }}">
                            </td>

                            {{-- Batch details --}}
                            <td>
                                <div class="batch-mono">{{ $batchNumber }}</div>
                                @if($expiryDate)
                                    <div class="{{ $expiryClass }}">
                                        Exp: {{ $expiryDate->format('d M Y') }}
                                        @if($daysLeft <= 0) (EXPIRED)
                                        @elseif($daysLeft <= 30) ({{ $daysLeft }}d)
                                        @endif
                                    </div>
                                @endif
                                @if($packInfo)
                                    <div class="pack-note">📦 {{ $packInfo }}</div>
                                @endif
                            </td>

                            {{-- Metrics --}}
                            <td><span class="badge badge--metrics">{{ $metrics }}</span></td>

                            {{-- Approved Qty --}}
                            <td style="text-align:right;">
                                <span class="badge badge--approved" data-approved="{{ $item->quantity_approved }}">
                                    {{ number_format($item->quantity_approved, 2) }}
                                </span>
                            </td>

                            {{-- Order Qty --}}
                            <td>
                                <div class="qty-wrap">
                                    <input type="number"
                                           class="qty-input"
                                           id="qty_{{ $index }}"
                                           name="items[{{ $index }}][quantity]"
                                           value="{{ $item->quantity_approved }}"
                                           step="0.01" min="0"
                                           max="{{ $item->quantity_approved }}"
                                           oninput="validateQty({{ $index }}, {{ $item->quantity_approved }})">
                                    <span class="qty-warning" id="warning_{{ $index }}"></span>
                                </div>
                            </td>

                            {{-- Unit Cost --}}
                            <td>
                                <input type="number"
                                       class="cost-input"
                                       id="cost_{{ $index }}"
                                       name="items[{{ $index }}][unit_cost_input]"
                                       value="{{ $unitCost }}"
                                       step="0.01" min="0"
                                       oninput="calcRowTotal({{ $index }})">
                                <input type="hidden" name="items[{{ $index }}][unit_cost]" id="cost_hidden_{{ $index }}" value="{{ $unitCost }}">
                            </td>

                            {{-- Row Total --}}
                            <td class="total-cell" id="total_{{ $index }}">0.00</td>

                            {{-- Delete --}}
                            <td style="text-align:center;">
                                <button type="button" class="btn-remove remove-item" data-index="{{ $index }}" title="Remove item">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" style="text-align:right; font-weight:700;">Subtotal</td>
                            <td style="text-align:right; font-family:'JetBrains Mono',monospace; font-weight:700;" id="subtotal_display">0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align:right;">VAT (<span id="vat_rate_display">18</span>%)</td>
                            <td style="text-align:right; font-family:'JetBrains Mono',monospace;" id="vat_amount_display">0.00</td>
                            <td></td>
                        </tr>
                        <tr class="total-row-grand">
                            <td colspan="6" style="text-align:right; font-weight:800; font-size:.9rem; letter-spacing:.02em;">TOTAL</td>
                            <td style="text-align:right; font-family:'JetBrains Mono',monospace; font-size:.92rem; font-weight:800;" id="total_display">0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>{{-- /form-body --}}

        {{-- Action bar --}}
        <div class="action-bar no-print">
            <a href="{{ route('procurement.requisitions.show', $requisition->id) }}" class="btn btn--ghost">Cancel</a>
            <button type="button" id="previewBtn" class="btn btn--purple">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                Preview LPO
            </button>
        </div>
    </form>
</div>

{{-- ═══════════════════════════════════════════════════════
     PREVIEW MODAL
═══════════════════════════════════════════════════════ --}}
@php
    $logo        = \App\Models\BusinessSetting::getLogo();
    $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
    $logoB64 = null; $logoMime = 'image/png';
    if ($logo) {
        $lp = public_path(parse_url($logo, PHP_URL_PATH));
        if (file_exists($lp)) { $logoMime = mime_content_type($lp); $logoB64 = base64_encode(file_get_contents($lp)); }
    }

    /* Prepared-by signature */
    $cu     = Auth::user();
    $sigB64 = null; $sigMime = 'image/png';
    if ($cu && $cu->signature_url) {
        $sp = public_path(parse_url($cu->signature_url, PHP_URL_PATH));
        if (file_exists($sp)) { $sigMime = mime_content_type($sp); $sigB64 = base64_encode(file_get_contents($sp)); }
    }
@endphp

<div id="previewModal" class="modal-overlay hidden no-print">
    <div class="modal-box">
        <button class="modal-close no-print" onclick="closePreview()" title="Close">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>

        <div id="print-section">

            {{-- Print header --}}
            <div class="print-header">
                <div>
                    @if($logoB64)
                        <img src="data:{{ $logoMime }};base64,{{ $logoB64 }}" class="company-logo" alt="{{ $companyName }}">
                    @elseif($logo)
                        <img src="{{ $logo }}" class="company-logo" alt="{{ $companyName }}">
                    @else
                        <div style="font-size:1.1rem;font-weight:800;color:var(--neutral-800);">{{ $companyName }}</div>
                    @endif
                </div>
                <div style="text-align:right;">
                    <div class="print-header__title">LOCAL PURCHASE ORDER</div>
                    <div class="print-header__sub" id="previewLpoNumber">LPO-PENDING</div>
                    <div style="font-size:.7rem;color:var(--neutral-400);margin-top:2px;" id="previewLpoDateDisplay">—</div>
                </div>
            </div>

            {{-- Info grid --}}
            <div class="info-grid">
                <div class="info-block">
                    <div class="info-block__label">LPO Type</div>
                    <div class="info-block__value" id="previewType">—</div>
                </div>
                <div class="info-block">
                    <div class="info-block__label">Vendor</div>
                    <div class="info-block__value" id="previewVendor">—</div>
                </div>
                <div class="info-block">
                    <div class="info-block__label">Payment Method</div>
                    <div class="info-block__value" id="previewPaymentMethod">—</div>
                </div>
                <div class="info-block">
                    <div class="info-block__label">Expected Delivery</div>
                    <div class="info-block__value" id="previewDeliveryDate">—</div>
                </div>
                <div class="info-block">
                    <div class="info-block__label">Delivery Address</div>
                    <div class="info-block__value" id="previewDeliveryAddress">—</div>
                </div>
                <div class="info-block">
                    <div class="info-block__label">Requisition Ref</div>
                    <div class="info-block__value">{{ $requisition->requisition_number }}</div>
                </div>
            </div>

            {{-- Items preview table --}}
            <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--neutral-400);margin-bottom:8px;">
                Order Items
            </div>
            <div style="overflow-x:auto; margin-bottom:16px;">
                <table class="items-table" style="font-size:.8rem;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Batch No.</th>
                            <th>Expiry</th>
                            <th>Unit</th>
                            <th style="text-align:right;">Qty</th>
                            <th style="text-align:right;">Unit Cost</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody id="previewItemsBody"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" style="text-align:right;font-weight:700;">Subtotal</td>
                            <td style="text-align:right;font-weight:700;font-family:'JetBrains Mono',monospace;" id="previewSubtotal">0.00</td>
                        </tr>
                        <tr>
                            <td colspan="7" style="text-align:right;">VAT (<span id="previewVatRate">18</span>%)</td>
                            <td style="text-align:right;font-family:'JetBrains Mono',monospace;" id="previewVatAmount">0.00</td>
                        </tr>
                        <tr class="total-row-grand">
                            <td colspan="7" style="text-align:right;font-weight:800;">TOTAL (UGX)</td>
                            <td style="text-align:right;font-weight:800;font-family:'JetBrains Mono',monospace;" id="previewTotal">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Amount in words --}}
            <div class="amount-words-box">
                <div class="amount-words-box__label">Amount in Words</div>
                <div class="amount-words-box__value" id="previewAmountWords">—</div>
            </div>

            {{-- Notes --}}
            <div style="padding:10px 14px;background:var(--neutral-50);border:1px solid var(--neutral-200);border-radius:8px;margin-bottom:24px;">
                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--neutral-400);margin-bottom:3px;">LPO Notes</div>
                <div style="font-size:.85rem;" id="previewNotes">—</div>
            </div>

            {{-- Delivery instructions --}}
            <div style="padding:10px 14px;background:var(--neutral-50);border:1px solid var(--neutral-200);border-radius:8px;margin-bottom:24px;" id="previewDeliveryInstructionsWrap">
                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--neutral-400);margin-bottom:3px;">Delivery Instructions</div>
                <div style="font-size:.85rem;" id="previewDeliveryInstructions">—</div>
            </div>

            {{-- Signatures --}}
            <div class="sig-grid">
                {{-- Prepared by --}}
                <div class="sig-block">
                    <div class="sig-block__label">Prepared By</div>
                    <div class="sig-block__img-wrap">
                        @if($sigB64)
                            <img src="data:{{ $sigMime }};base64,{{ $sigB64 }}" class="sig-img" alt="Signature">
                        @elseif($cu && $cu->signature_url)
                            <img src="{{ $cu->signature_url }}" class="sig-img" alt="Signature">
                        @else
                            <span style="font-size:.72rem;color:var(--neutral-400);">No signature on file</span>
                        @endif
                    </div>
                    <div class="sig-line"></div>
                    <div class="sig-block__name">{{ ($cu->first_name ?? '').' '.($cu->last_name ?? '') }}</div>
                    <div class="sig-block__date">{{ now()->format('d M Y') }}</div>
                </div>

                {{-- Director approval --}}
                <div class="sig-block">
                    <div class="sig-block__label">Approved By (Director)</div>
                    <div class="sig-block__img-wrap">
                        <span class="sig-pending">⏳ Pending Director Approval</span>
                    </div>
                    <div class="sig-line"></div>
                    <div class="sig-block__name" style="color:var(--neutral-400);">Awaiting Authorization</div>
                    <div class="sig-block__date">—</div>
                </div>
            </div>

            {{-- Footer --}}
            <div style="text-align:center;margin-top:22px;padding-top:14px;border-top:1px solid var(--neutral-200);">
                <div style="font-size:.68rem;color:var(--neutral-400);">This is a computer-generated document.</div>
                <div style="font-size:.68rem;color:var(--neutral-400);">{{ $companyName }} — All Rights Reserved</div>
            </div>

        </div>{{-- /print-section --}}

        <div class="modal-footer no-print">
            <button type="button" class="btn btn--ghost" onclick="closePreview()">← Edit</button>
            <button type="button" class="btn btn--blue" onclick="printLPO()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print
            </button>
            <button type="button" class="btn btn--green" onclick="submitLPO()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 2 11 13M22 2 15 22l-4-9-9-4 20-7z"/>
                </svg>
                Submit for Director Approval
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════════ --}}
<script>
/* ═══════════════════════════════════
   UTILITIES
═══════════════════════════════════ */
function fmtQty(val) {
    if (isNaN(val)) return '0';
    return val % 1 === 0 ? val.toFixed(0) : parseFloat(val.toFixed(2)).toString();
}

function fmtCurrency(val) {
    return 'UGX ' + Number(val).toLocaleString('en-UG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function esc(text) {
    if (!text) return '';
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}

/* Simple number-to-words for UGX (handles up to billions) */
function numberToWords(n) {
    if (n === 0) return 'Zero';
    const ones  = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                   'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
                   'Seventeen','Eighteen','Nineteen'];
    const tens  = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
    function chunk(num) {
        if (num < 20)  return ones[num];
        if (num < 100) return tens[Math.floor(num/10)] + (num%10 ? ' '+ones[num%10] : '');
        return ones[Math.floor(num/100)] + ' Hundred' + (num%100 ? ' '+chunk(num%100) : '');
    }
    let result = '';
    const b = Math.floor(n / 1e9);  if (b) { result += chunk(b) + ' Billion '; n %= 1e9; }
    const m = Math.floor(n / 1e6);  if (m) { result += chunk(m) + ' Million '; n %= 1e6; }
    const k = Math.floor(n / 1e3);  if (k) { result += chunk(k) + ' Thousand '; n %= 1e3; }
    if (n) result += chunk(n);
    return result.trim();
}

function amountInWords(total) {
    const whole = Math.floor(total);
    const cents = Math.round((total - whole) * 100);
    let words = numberToWords(whole) + ' Uganda Shillings';
    if (cents > 0) words += ' and ' + numberToWords(cents) + ' Cents';
    return words + ' Only';
}

/* ═══════════════════════════════════
   ROW-LEVEL LOGIC
═══════════════════════════════════ */
function validateQty(index, maxQty) {
    const inp  = document.getElementById('qty_' + index);
    const warn = document.getElementById('warning_' + index);
    let val    = parseFloat(inp.value);

    inp.classList.remove('valid', 'invalid');
    warn.className = 'qty-warning';
    warn.textContent = '';

    if (isNaN(val)) {
        inp.classList.add('invalid');
        warn.textContent = '✗ Enter a valid quantity';
        warn.classList.add('error');
        calcRowTotal(index);
        return false;
    }

    if (val > maxQty) {
        inp.value = maxQty;
        val = maxQty;
        warn.textContent = '⚠ Adjusted to max approved (' + fmtQty(maxQty) + ')';
        warn.classList.add('warning');
    } else if (val < 0) {
        inp.value = 0;
        val = 0;
        warn.textContent = '⚠ Cannot be negative';
        warn.classList.add('warning');
    } else if (val === 0) {
        warn.textContent = '⚠ Zero — this item will be skipped';
        warn.classList.add('warning');
    } else if (val === maxQty) {
        warn.textContent = '✓ Full approved quantity';
        warn.classList.add('success');
        inp.classList.add('valid');
    } else {
        const pct = ((val / maxQty) * 100).toFixed(1);
        warn.textContent = '✓ ' + fmtQty(val) + ' of ' + fmtQty(maxQty) + ' (' + pct + '%)';
        warn.classList.add('success');
        inp.classList.add('valid');
    }

    calcRowTotal(index);
    return true;
}

function calcRowTotal(index) {
    const qty  = parseFloat(document.getElementById('qty_'  + index).value) || 0;
    const cost = parseFloat(document.getElementById('cost_' + index).value) || 0;
    const total = qty * cost;

    // Sync hidden unit_cost field
    const hidden = document.getElementById('cost_hidden_' + index);
    if (hidden) hidden.value = cost;

    const cell = document.getElementById('total_' + index);
    if (cell) cell.textContent = total.toLocaleString('en-UG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    updateTotals();
}

function updateTotals() {
    let subtotal = 0;

    document.querySelectorAll('#itemsBody .item-row').forEach(function(row) {
        const idx  = row.id.replace('row_', '');
        const qty  = parseFloat(document.getElementById('qty_'  + idx)?.value) || 0;
        const cost = parseFloat(document.getElementById('cost_' + idx)?.value) || 0;
        subtotal  += qty * cost;
    });

    const vatRate  = parseFloat(document.getElementById('vat_rate').value) || 0;
    const vatAmt   = (subtotal * vatRate) / 100;
    const grandTotal = subtotal + vatAmt;

    document.getElementById('subtotal_display').textContent  = subtotal.toLocaleString('en-UG',   {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('vat_rate_display').textContent  = vatRate;
    document.getElementById('vat_amount_display').textContent= vatAmt.toLocaleString('en-UG',     {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('total_display').textContent     = grandTotal.toLocaleString('en-UG', {minimumFractionDigits:2, maximumFractionDigits:2});
}

/* ═══════════════════════════════════
   REMOVE ITEM
═══════════════════════════════════ */
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.remove-item');
    if (!btn) return;
    if (!confirm('Remove this item from the LPO?')) return;
    const row = btn.closest('.item-row');
    if (row) { row.remove(); updateTotals(); }
});

/* ═══════════════════════════════════
   VAT CHANGE
═══════════════════════════════════ */
document.getElementById('vat_rate').addEventListener('input', updateTotals);

/* ═══════════════════════════════════
   PREVIEW
═══════════════════════════════════ */
document.getElementById('previewBtn').addEventListener('click', openPreview);

function openPreview() {
    /* ── Collect form values ── */
    const typeEl     = document.getElementById('type');
    const vendorEl   = document.getElementById('vendor_id');
    const pmEl       = document.getElementById('payment_method');
    const lpoDateVal = document.getElementById('lpo_date').value;
    const delDateVal = document.getElementById('expected_delivery_date').value;
    const vatRate    = parseFloat(document.getElementById('vat_rate').value) || 0;

    const typeVal      = typeEl.value;
    const vendorName   = vendorEl.options[vendorEl.selectedIndex]?.text || '—';
    const pmText       = pmEl.options[pmEl.selectedIndex]?.text || '—';
    const deliveryAddr = document.getElementById('delivery_address').value.trim() || '—';
    const deliveryInst = document.getElementById('delivery_instructions').value.trim() || '—';
    const notes        = document.getElementById('notes').value.trim() || '—';

    const fmtDate = (d) => d ? new Date(d + 'T00:00:00').toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'}) : '—';

    /* ── Generate LPO number ── */
    const now   = new Date();
    const rand  = String(Math.floor(Math.random() * 9999)).padStart(4, '0');
    const lpoNo = 'LPO-' + now.getFullYear()
                + String(now.getMonth()+1).padStart(2,'0')
                + String(now.getDate()).padStart(2,'0')
                + '-' + rand;

    /* ── Set header fields ── */
    document.getElementById('previewLpoNumber').textContent     = lpoNo;
    document.getElementById('previewLpoDateDisplay').textContent= fmtDate(lpoDateVal);
    document.getElementById('previewVendor').textContent        = vendorName;
    document.getElementById('previewPaymentMethod').textContent = pmText;
    document.getElementById('previewDeliveryDate').textContent  = fmtDate(delDateVal);
    document.getElementById('previewDeliveryAddress').textContent = deliveryAddr;
    document.getElementById('previewNotes').textContent         = notes;
    document.getElementById('previewDeliveryInstructions').textContent = deliveryInst;
    document.getElementById('previewVatRate').textContent       = vatRate;

    document.getElementById('previewType').innerHTML = typeVal === 'emergency'
        ? '<span class="badge badge--emergency">🔴 Emergency</span>'
        : '<span class="badge badge--normal">✅ Normal</span>';

    /* ── Build items rows ── */
    let subtotal = 0;
    let rows = '';
    let rowNum = 0;

    document.querySelectorAll('#itemsBody .item-row').forEach(function(row) {
        const idx = row.id.replace('row_', '');

        const nameEl  = row.querySelector('td:first-child');
        const nameLines = nameEl ? nameEl.innerText.trim().split('\n') : [];
        const itemName  = nameLines[0] ? nameLines[0].trim() : '—';

        const batchEl  = row.querySelector('.batch-mono');
        const batchNo  = batchEl ? batchEl.textContent.trim() : '—';

        const expiryEl = row.querySelector('.expiry-ok, .expiry-warn, .expiry-error');
        const expiry   = expiryEl ? expiryEl.textContent.trim() : '—';

        const metricsEl = row.querySelector('.badge--metrics');
        const metrics   = metricsEl ? metricsEl.textContent.trim() : '—';

        const qty  = parseFloat(document.getElementById('qty_'  + idx)?.value) || 0;
        const cost = parseFloat(document.getElementById('cost_' + idx)?.value) || 0;
        const rowTotal = qty * cost;
        subtotal += rowTotal;

        if (qty <= 0) return; // skip zero-qty items in preview

        rowNum++;
        const totalColor = rowTotal > 0 ? 'color:var(--success);font-weight:700;' : '';

        rows += `<tr>
            <td style="text-align:center;color:var(--neutral-400);font-size:.72rem;">${rowNum}</td>
            <td style="font-weight:600;">${esc(itemName)}</td>
            <td style="font-family:'JetBrains Mono',monospace;font-size:.75rem;">${esc(batchNo)}</td>
            <td style="font-size:.75rem;">${esc(expiry)}</td>
            <td><span class="badge badge--metrics">${esc(metrics)}</span></td>
            <td style="text-align:right;font-family:'JetBrains Mono',monospace;">${fmtQty(qty)}</td>
            <td style="text-align:right;font-family:'JetBrains Mono',monospace;">${cost.toLocaleString('en-UG',{minimumFractionDigits:2,maximumFractionDigits:2})}</td>
            <td style="text-align:right;font-family:'JetBrains Mono',monospace;${totalColor}">${rowTotal.toLocaleString('en-UG',{minimumFractionDigits:2,maximumFractionDigits:2})}</td>
        </tr>`;
    });

    if (rows === '') {
        rows = `<tr><td colspan="8" style="text-align:center;color:var(--neutral-400);padding:16px;">No items with quantity > 0</td></tr>`;
    }

    document.getElementById('previewItemsBody').innerHTML = rows;

    const vatAmt   = (subtotal * vatRate) / 100;
    const grandTotal = subtotal + vatAmt;

    const fmt2 = (v) => v.toLocaleString('en-UG', {minimumFractionDigits:2, maximumFractionDigits:2});

    document.getElementById('previewSubtotal').textContent  = fmt2(subtotal);
    document.getElementById('previewVatAmount').textContent = fmt2(vatAmt);
    document.getElementById('previewTotal').textContent     = fmt2(grandTotal);
    document.getElementById('previewAmountWords').textContent = amountInWords(grandTotal);

    /* ── Show modal ── */
    document.getElementById('previewModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
    document.body.style.overflow = '';
}

/* ═══════════════════════════════════
   PRINT
═══════════════════════════════════ */
function printLPO() {
    const content = document.getElementById('print-section').innerHTML;
    const win = window.open('', '_blank', 'width=960,height=720');
    win.document.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>${document.getElementById('previewLpoNumber').textContent}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Sora', Arial, sans-serif; font-size: 11px; padding: 24px 32px; color: #1e293b; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #e2e8f0; padding: 6px 9px; vertical-align: middle; }
  thead th { background: #f1f5f9; font-size: 9px; text-transform: uppercase; letter-spacing: .07em; color: #64748b; font-weight: 700; }
  tfoot td { background: #f8fafc; }
  .total-row-grand td { background: #eff6ff; color: #1e3a8a; font-weight: 800; }
  .print-header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }
  .print-header__title { font-size: 15px; font-weight: 800; color: #1e40af; }
  .print-header__sub   { font-family: 'JetBrains Mono', monospace; font-size: 9px; color: #94a3b8; margin-top: 3px; }
  .company-logo { max-height: 40px; width: auto; }
  .info-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; }
  .info-block__label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; margin-bottom: 3px; }
  .info-block__value { font-size: 11px; font-weight: 600; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
  .badge--normal    { background: #dcfce7; color: #065f46; }
  .badge--emergency { background: #fee2e2; color: #991b1b; }
  .badge--metrics   { background: #f1f5f9; color: #475569; }
  .amount-words-box { padding: 10px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; margin: 14px 0; }
  .amount-words-box__label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; margin-bottom: 3px; }
  .amount-words-box__value { font-size: 11px; font-weight: 600; }
  .sig-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; border-top: 1px solid #e2e8f0; padding-top: 18px; margin-top: 20px; }
  .sig-block { display: flex; flex-direction: column; align-items: center; text-align: center; }
  .sig-block__label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; margin-bottom: 10px; }
  .sig-block__img-wrap { height: 46px; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 6px; }
  .sig-img { max-height: 42px; max-width: 160px; object-fit: contain; }
  .sig-line { border-top: 1.5px solid #94a3b8; width: 180px; padding-top: 5px; }
  .sig-block__name { font-size: 11px; font-weight: 600; margin-top: 4px; }
  .sig-block__date { font-size: 9px; color: #94a3b8; margin-top: 2px; }
  .sig-pending { display: inline-block; padding: 4px 12px; background: #fef3c7; color: #b45309; border-radius: 99px; font-size: 9px; font-weight: 600; }
  .no-print, .modal-close, .modal-footer { display: none !important; }
</style>
</head>
<body>${content}</body>
</html>`);
    win.document.close();
    win.focus();
    setTimeout(() => { win.print(); }, 600);
}

/* ═══════════════════════════════════
   SUBMIT
═══════════════════════════════════ */
function submitLPO() {
    let valid = true;
    let firstError = null;

    document.querySelectorAll('#itemsBody .item-row').forEach(function(row) {
        const idx    = row.id.replace('row_', '');
        const qty    = parseFloat(document.getElementById('qty_' + idx)?.value) || 0;
        const maxEl  = row.querySelector('.max-quantity');
        const maxQty = maxEl ? parseFloat(maxEl.value) || 0 : 0;

        if (qty > maxQty) {
            valid = false;
            if (!firstError) firstError = 'Row ' + (parseInt(idx)+1) + ': quantity (' + fmtQty(qty) + ') exceeds approved (' + fmtQty(maxQty) + ')';
        }
    });

    if (!valid) {
        alert('Validation error — ' + firstError);
        return;
    }

    // Check vendor selected
    const vendorId = document.getElementById('vendor_id').value;
    if (!vendorId) {
        alert('Please select a vendor before submitting.');
        closePreview();
        document.getElementById('vendor_id').focus();
        return;
    }

    closePreview();
    document.getElementById('lpoForm').submit();
}

/* ═══════════════════════════════════
   CLOSE ON BACKDROP / ESCAPE
═══════════════════════════════════ */
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreview();
});

/* ═══════════════════════════════════
   SELECT2 INIT
═══════════════════════════════════ */
$(document).ready(function() {
    $('#vendor_id').select2({ placeholder: '— Select Vendor —', allowClear: true });
});

/* ═══════════════════════════════════
   INIT ALL ROWS ON PAGE LOAD
═══════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#itemsBody .item-row').forEach(function(row) {
        const idx    = row.id.replace('row_', '');
        const maxEl  = row.querySelector('.max-quantity');
        const maxQty = maxEl ? parseFloat(maxEl.value) || 0 : 0;
        validateQty(parseInt(idx), maxQty);
        calcRowTotal(parseInt(idx));
    });
});
</script>

@endsection
