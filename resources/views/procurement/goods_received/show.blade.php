@extends('layouts.procurement')
@section('title', 'GRN #' . $grn->grn_number)

@section('content')

{{-- ── PAGE STYLES ── --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .grn-page { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Hero banner */
    .grn-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #1e40af 100%);
        position: relative;
        overflow: hidden;
    }
    .grn-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 80% 20%, rgba(59,130,246,0.18) 0%, transparent 60%),
                          radial-gradient(circle at 10% 90%, rgba(16,185,129,0.10) 0%, transparent 50%);
    }
    .grn-hero-content { position: relative; z-index: 1; }

    /* Mono number style */
    .mono { font-family: 'IBM Plex Mono', monospace; }

    /* Status pill */
    .pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .pill-completed { background: #d1fae5; color: #065f46; }
    .pill-draft     { background: #fef3c7; color: #92400e; }
    .pill-cancelled { background: #fee2e2; color: #991b1b; }
    .pill-inventory_updated { background: #dbeafe; color: #1e40af; }
    .pill-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

    /* Info cards */
    .info-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .info-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .header-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .card-title { font-size: 0.875rem; font-weight: 700; color: #1e293b; letter-spacing: 0.01em; }

    /* KPI tiles */
    .kpi-tile {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    .kpi-tile::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 0 0 14px 14px;
    }
    .kpi-blue::after  { background: #3b82f6; }
    .kpi-green::after { background: #10b981; }
    .kpi-amber::after { background: #f59e0b; }
    .kpi-red::after   { background: #ef4444; }

    .kpi-label { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #94a3b8; margin-bottom: 6px; }
    .kpi-value { font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1; }
    .kpi-sub   { font-size: 0.72rem; color: #64748b; margin-top: 4px; }

    /* Row details */
    .detail-row { display: flex; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f1f5f9; gap: 12px; }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { font-size: 0.75rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; width: 140px; flex-shrink: 0; padding-top: 2px; }
    .detail-value { font-size: 0.875rem; color: #1e293b; font-weight: 500; flex: 1; }

    /* Action buttons */
    .btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; border-radius: 10px;
        font-size: 0.825rem; font-weight: 600;
        cursor: pointer; transition: all 0.15s ease;
        border: none; text-decoration: none;
    }
    .btn-primary   { background: #1d4ed8; color: #fff; }
    .btn-primary:hover { background: #1e40af; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(29,78,216,0.3); }
    .btn-success   { background: #059669; color: #fff; }
    .btn-success:hover { background: #047857; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(5,150,105,0.3); }
    .btn-warning   { background: #d97706; color: #fff; }
    .btn-warning:hover { background: #b45309; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(217,119,6,0.3); }
    .btn-ghost     { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .btn-ghost:hover { background: #e2e8f0; }

    /* Items table */
    .items-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .items-table thead tr th {
        background: #f8fafc;
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;
        padding: 11px 14px; border-bottom: 2px solid #e2e8f0;
    }
    .items-table thead tr th:first-child { border-radius: 8px 0 0 0; }
    .items-table thead tr th:last-child  { border-radius: 0 8px 0 0; }
    .items-table tbody tr td { padding: 13px 14px; border-bottom: 1px solid #f1f5f9; font-size: 0.845rem; color: #334155; vertical-align: middle; }
    .items-table tbody tr:last-child td { border-bottom: none; }
    .items-table tbody tr:hover td { background: #f8fafc; }

    .qty-chip {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 52px; padding: 3px 10px; border-radius: 6px;
        font-size: 0.8rem; font-weight: 600; font-family: 'IBM Plex Mono', monospace;
    }
    .qty-ordered   { background: #eff6ff; color: #1d4ed8; }
    .qty-received  { background: #f0fdf4; color: #15803d; }
    .qty-accepted  { background: #ecfdf5; color: #059669; }
    .qty-rejected  { background: #fff1f2; color: #e11d48; }

    .amount-cell { font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 0.82rem; }

    /* Star rating display */
    .star-rating-display {
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }
    .rating-badge {
        background: #fef3c7;
        color: #d97706;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Modal styles */
    .modal-backdrop {
        position: fixed; inset: 0; background: rgba(15,23,42,0.55);
        backdrop-filter: blur(3px); z-index: 1000;
        display: none; align-items: center; justify-content: center;
    }
    .modal-backdrop.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 18px;
        padding: 28px 30px; width: 100%; max-width: 500px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.18);
        animation: modalIn 0.2s ease;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-title {
        font-size: 1.05rem; font-weight: 800; color: #0f172a;
        margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
    }
    .rating-stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 8px;
    }
    .rating-stars label {
        cursor: pointer;
        transition: transform 0.2s;
        font-size: 28px;
    }
    .rating-stars label:hover {
        transform: scale(1.1);
    }
    .rating-stars label:hover ~ label {
        transform: scale(1.1);
    }

    /* Email modal */
    .email-modal-backdrop {
        position: fixed; inset: 0; background: rgba(15,23,42,0.55);
        backdrop-filter: blur(3px); z-index: 1000;
        display: none; align-items: center; justify-content: center;
    }
    .email-modal-backdrop.open { display: flex; }
    .email-modal-box {
        background: #fff; border-radius: 18px;
        padding: 28px 30px; width: 100%; max-width: 440px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.18);
        animation: modalIn 0.2s ease;
    }
    .email-modal-input {
        width: 100%; border: 1.5px solid #e2e8f0; border-radius: 9px;
        padding: 10px 14px; font-size: 0.875rem; color: #1e293b;
        outline: none; transition: border-color 0.15s;
    }
    .email-modal-input:focus { border-color: #3b82f6; }

    /* Totals footer */
    .totals-footer {
        background: linear-gradient(to right, #f0fdf4, #ecfdf5);
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 12px;
        margin-top: 16px;
    }
</style>

<div class="grn-page space-y-6">

    {{-- ── HERO HEADER ── --}}
    <div class="grn-hero rounded-2xl shadow-xl">
        <div class="grn-hero-content px-6 py-6 sm:px-8 sm:py-7">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-blue-300 text-xs font-semibold tracking-widest uppercase">Goods Received Note</span>
                        <span class="pill pill-{{ $grn->status }}">
                            <span class="pill-dot"></span>{{ ucfirst(str_replace('_', ' ', $grn->status)) }}
                        </span>
                    </div>
                    <h1 class="mono text-2xl sm:text-3xl font-bold text-white tracking-tight">{{ $grn->grn_number }}</h1>
                    <p class="text-blue-200 text-sm mt-1">
                        Created {{ $grn->created_at->format('F d, Y') }} at {{ $grn->created_at->format('H:i') }}
                        &nbsp;·&nbsp;
                        by <span class="text-white font-semibold">
                            {{ $grn->createdBy->first_name ?? '' }} {{ $grn->createdBy->last_name ?? 'Procurement' }}
                        </span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if(in_array($grn->status, ['inventory_updated', 'completed']) && !$grn->isRated())
                    <button type="button" onclick="openRatingModal({{ $grn->id }})"
                            class="btn btn-warning">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Rate This Delivery
                    </button>
                    @endif

                    <button type="button" onclick="openEmailModal()"
                            class="btn btn-success">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Share via Email
                    </button>

                    <a href="{{ route('procurement.goods-received.download-pdf', $grn->id) }}"
                       class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download PDF
                    </a>

                    <a href="{{ route('procurement.goods-received.index') }}" class="btn btn-ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RATING DISPLAY (if already rated) ── --}}
    @if($grn->isRated())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-yellow-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">Delivery Rated</p>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="star-rating-display">
                            @php
                                $rating = $grn->rating->rating;
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star text-yellow-400 text-sm"></i>';
                                    } else {
                                        echo '<i class="far fa-star text-yellow-400 text-sm"></i>';
                                    }
                                }
                            @endphp
                        </div>
                        <span class="text-xs text-gray-500">by {{ $grn->rating->ratedBy->first_name ?? 'User' }}</span>
                    </div>
                    @if($grn->rating->comment)
                        <p class="text-sm text-gray-600 mt-2 italic">"{{ $grn->rating->comment }}"</p>
                    @endif
                </div>
            </div>
            <div class="rating-badge">
                <i class="fas fa-star text-yellow-500"></i>
                <span>{{ number_format($grn->rating->rating, 1) }} / 5.0</span>
            </div>
        </div>
    </div>
    @endif

    {{-- ── SUCCESS / ERROR FLASH ── --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium text-sm">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── KPI TILES ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="kpi-tile kpi-blue">
            <p class="kpi-label">Total Items</p>
            <p class="kpi-value">{{ $grn->items->count() }}</p>
            <p class="kpi-sub">Line items received</p>
        </div>
        <div class="kpi-tile kpi-green">
            <p class="kpi-label">Total Accepted</p>
            <p class="kpi-value mono">{{ number_format($grn->items->sum('quantity_accepted'), 0) }}</p>
            <p class="kpi-sub">Units accepted</p>
        </div>
        <div class="kpi-tile kpi-red">
            <p class="kpi-label">Total Rejected</p>
            <p class="kpi-value mono">{{ number_format($grn->items->sum('quantity_rejected'), 0) }}</p>
            <p class="kpi-sub">Units rejected</p>
        </div>
        <div class="kpi-tile kpi-amber">
            <p class="kpi-label">Total Value</p>
            <p class="kpi-value" style="font-size:1.15rem">UGX {{ number_format($grn->items->sum('total_cost'), 0) }}</p>
            <p class="kpi-sub">Payable to vendor</p>
        </div>
    </div>

    {{-- ── INFO CARDS ROW ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="info-card lg:col-span-2">
            <div class="info-card-header">
                <div class="header-icon" style="background:#eff6ff">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="card-title">GRN Details</span>
            </div>
            <div class="px-6 py-4">
                <div class="detail-row">
                    <span class="detail-label">GRN Number</span>
                    <span class="detail-value mono font-semibold text-blue-700">{{ $grn->grn_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">PO Reference</span>
                    <span class="detail-value">
                        <a href="{{ route('procurement.purchase-orders.show', $grn->purchaseOrder->id ?? 0) }}"
                           class="mono font-semibold text-blue-600 hover:underline">
                            {{ $grn->purchaseOrder->po_number ?? 'N/A' }}
                        </a>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Vendor</span>
                    <span class="detail-value font-semibold">{{ $grn->vendor->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Vendor Email</span>
                    <span class="detail-value text-blue-600">{{ $grn->vendor->email ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Received Date</span>
                    <span class="detail-value">{{ $grn->received_date->format('F d, Y') }}</span>
                </div>
                @if($grn->delivery_note_number)
                <div class="detail-row">
                    <span class="detail-label">Vendor DN #</span>
                    <span class="detail-value mono">{{ $grn->delivery_note_number }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="pill pill-{{ $grn->status }}">
                            <span class="pill-dot"></span>{{ ucfirst(str_replace('_', ' ', $grn->status)) }}
                        </span>
                    </span>
                </div>
                @if($grn->notes)
                <div class="detail-row">
                    <span class="detail-label">Notes</span>
                    <span class="detail-value text-slate-500 italic">{{ $grn->notes }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="info-card">
                <div class="info-card-header">
                    <div class="header-icon" style="background:#f0fdf4">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="card-title">Created By</span>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">
                                {{ substr($grn->createdBy->first_name ?? 'P', 0, 1) }}{{ substr($grn->createdBy->last_name ?? '', 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-slate-800">
                                {{ $grn->createdBy->first_name ?? 'Procurement' }}
                                {{ $grn->createdBy->last_name ?? 'Officer' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $grn->createdBy->email ?? '' }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $grn->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <div class="header-icon" style="background:#fef3c7">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="card-title">Financial Summary</span>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500 uppercase font-semibold tracking-wide">PO Total</span>
                        <span class="mono text-sm font-semibold text-slate-700">UGX {{ number_format($grn->po_total_amount, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500 uppercase font-semibold tracking-wide">Received Value</span>
                        <span class="mono text-sm font-semibold text-emerald-600">UGX {{ number_format($grn->grn_total_amount, 0) }}</span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 pt-3 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Amount to Pay</span>
                        <span class="mono text-base font-bold text-blue-700">UGX {{ number_format($grn->items->sum('total_cost'), 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ITEMS TABLE ── --}}
    <div class="info-card">
        <div class="info-card-header">
            <div class="header-icon" style="background:#eef2ff">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="card-title">Received Items</span>
            <span class="ml-auto text-xs bg-indigo-50 text-indigo-700 font-semibold px-3 py-1 rounded-full">
                {{ $grn->items->count() }} item{{ $grn->items->count() !== 1 ? 's' : '' }}
            </span>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="text-left" style="padding-left:20px">#</th>
                        <th class="text-left">Item</th>
                        <th class="text-center">Ordered</th>
                        <th class="text-center">Received</th>
                        <th class="text-center">Accepted</th>
                        <th class="text-center">Rejected</th>
                        <th class="text-right">Unit Cost</th>
                        <th class="text-right" style="padding-right:20px">Total</th>
                        <th class="text-left">Rejection Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grn->items as $i => $item)
                    <tr>
                        <td style="padding-left:20px; color:#94a3b8; font-size:0.75rem; font-weight:600;">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <p class="font-semibold text-slate-800 text-sm">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                            @if($item->notes)
                                <p class="text-xs text-slate-400 mt-0.5 italic">{{ $item->notes }}</p>
                            @endif
                        </td>
                        <td class="text-center"><span class="qty-chip qty-ordered">{{ number_format($item->quantity_ordered, 2) }}</span></td>
                        <td class="text-center"><span class="qty-chip qty-received">{{ number_format($item->quantity_received, 2) }}</span></td>
                        <td class="text-center"><span class="qty-chip qty-accepted">{{ number_format($item->quantity_accepted, 2) }}</span></td>
                        <td class="text-center">
                            @if($item->quantity_rejected > 0)
                                <span class="qty-chip qty-rejected">{{ number_format($item->quantity_rejected, 2) }}</span>
                            @else
                                <span class="text-slate-300 text-sm">—</span>
                            @endif
                        </td>
                        <td class="text-right amount-cell text-slate-600">{{ number_format($item->unit_cost, 2) }}</td>
                        <td class="text-right amount-cell text-emerald-700" style="padding-right:20px">{{ number_format($item->total_cost, 2) }}</td>
                        <td>
                            @if($item->rejection_reason)
                                <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-md">{{ $item->rejection_reason }}</span>
                            @else
                                <span class="text-slate-300 text-sm">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-5 pb-5">
                <div class="totals-footer">
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ $grn->items->count() }} items received</span>
                        <span class="text-slate-400">·</span>
                        <span>{{ number_format($grn->items->sum('quantity_accepted'), 2) }} units accepted</span>
                        @if($grn->items->sum('quantity_rejected') > 0)
                            <span class="text-slate-400">·</span>
                            <span class="text-red-600">{{ number_format($grn->items->sum('quantity_rejected'), 2) }} units rejected</span>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Total Payable</p>
                        <p class="mono text-lg font-bold text-emerald-700">UGX {{ number_format($grn->items->sum('total_cost'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:hidden p-4 space-y-3">
            @foreach($grn->items as $i => $item)
            <div class="border border-slate-200 rounded-xl p-4 bg-white">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="text-xs text-slate-400 font-semibold">ITEM {{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span>
                        <p class="font-semibold text-slate-800 text-sm mt-0.5">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                    </div>
                    <span class="mono text-sm font-bold text-emerald-700">UGX {{ number_format($item->total_cost, 0) }}</span>
                </div>
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Ordered</p>
                        <span class="qty-chip qty-ordered text-xs">{{ number_format($item->quantity_ordered, 0) }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Received</p>
                        <span class="qty-chip qty-received text-xs">{{ number_format($item->quantity_received, 0) }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Accepted</p>
                        <span class="qty-chip qty-accepted text-xs">{{ number_format($item->quantity_accepted, 0) }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Rejected</p>
                        <span class="qty-chip qty-rejected text-xs">{{ number_format($item->quantity_rejected, 0) }}</span>
                    </div>
                </div>
                @if($item->rejection_reason)
                <p class="mt-2 text-xs text-red-600 bg-red-50 px-3 py-1.5 rounded-lg">
                    <span class="font-semibold">Rejection:</span> {{ $item->rejection_reason }}
                </p>
                @endif
            </div>
            @endforeach

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex justify-between items-center">
                <span class="text-sm font-bold text-slate-700">Total Payable</span>
                <span class="mono text-base font-bold text-emerald-700">UGX {{ number_format($grn->items->sum('total_cost'), 2) }}</span>
            </div>
        </div>
    </div>

</div>

{{-- ── RATING MODAL ── --}}
<div id="ratingModal" class="modal-backdrop" onclick="closeRatingModal(event)">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            Rate Vendor Delivery
        </div>

        <form id="ratingForm" method="POST">
            @csrf
            <div class="space-y-5">
                <div class="bg-gray-50 rounded-lg p-3 text-sm">
                    <p class="font-medium text-gray-700">Vendor: <span id="ratingVendorName">{{ $grn->vendor->name ?? 'N/A' }}</span></p>
                    <p class="text-gray-500 text-xs mt-1">GRN: {{ $grn->grn_number }}</p>
                    <p class="text-gray-500 text-xs">Delivery Date: {{ $grn->received_date->format('F d, Y') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3 text-center">How would you rate this delivery?</label>
                    <div class="flex justify-center gap-2 mb-3">
                        <div class="rating-stars">
                            <input type="radio" name="rating" value="5" id="modalStar5" class="hidden" required>
                            <label for="modalStar5" class="cursor-pointer text-3xl text-gray-300 hover:text-yellow-400 transition-colors">
                                <i class="far fa-star"></i>
                            </label>

                            <input type="radio" name="rating" value="4" id="modalStar4" class="hidden">
                            <label for="modalStar4" class="cursor-pointer text-3xl text-gray-300 hover:text-yellow-400 transition-colors">
                                <i class="far fa-star"></i>
                            </label>

                            <input type="radio" name="rating" value="3" id="modalStar3" class="hidden">
                            <label for="modalStar3" class="cursor-pointer text-3xl text-gray-300 hover:text-yellow-400 transition-colors">
                                <i class="far fa-star"></i>
                            </label>

                            <input type="radio" name="rating" value="2" id="modalStar2" class="hidden">
                            <label for="modalStar2" class="cursor-pointer text-3xl text-gray-300 hover:text-yellow-400 transition-colors">
                                <i class="far fa-star"></i>
                            </label>

                            <input type="radio" name="rating" value="1" id="modalStar1" class="hidden">
                            <label for="modalStar1" class="cursor-pointer text-3xl text-gray-300 hover:text-yellow-400 transition-colors">
                                <i class="far fa-star"></i>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-center gap-4 text-xs text-gray-500">
                        <span>Poor</span>
                        <span>Fair</span>
                        <span>Good</span>
                        <span>Very Good</span>
                        <span>Excellent</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comments (Optional)</label>
                    <textarea name="comment" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Share your experience with this vendor..."></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeRatingModal()"
                            class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-paper-plane mr-1"></i> Submit Rating
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── EMAIL MODAL ── --}}
<div id="emailModal" class="email-modal-backdrop" onclick="closeEmailModal(event)">
    <div class="email-modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            Share GRN via Email
        </div>

        <form id="emailForm" action="{{ route('procurement.goods-received.send-email', $grn->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Recipient Email</label>
                    <input type="email" name="email" class="email-modal-input" value="{{ $grn->vendor->email ?? '' }}" placeholder="vendor@example.com" required>
                    @if($grn->vendor->email ?? false)
                        <p class="text-xs text-slate-400 mt-1">Pre-filled with vendor email</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Subject</label>
                    <input type="text" name="subject" class="email-modal-input" value="Goods Received Note {{ $grn->grn_number }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Message (optional)</label>
                    <textarea name="message" rows="3" class="email-modal-input resize-none" placeholder="Add a personal message...">Please find attached the Goods Received Note {{ $grn->grn_number }} for PO {{ $grn->purchaseOrder->po_number ?? '' }}.</textarea>
                </div>

                <div id="sendingIndicator" class="hidden flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                    <svg class="animate-spin w-4 h-4 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-sm text-blue-700 font-medium">Sending email &amp; downloading PDF...</span>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEmailModal()" class="btn btn-ghost flex-1 justify-center">Cancel</button>
                    <button type="button" id="sendEmailBtn" onclick="sendEmailAndDownload()" class="btn btn-success flex-1 justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send &amp; Download PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Star rating for modal
    const modalRatingInputs = document.querySelectorAll('#ratingModal input[name="rating"]');
    const modalStarLabels = document.querySelectorAll('#ratingModal .rating-stars label');

    function updateModalStars(ratingValue) {
        for (let i = 0; i < modalStarLabels.length; i++) {
            const label = modalStarLabels[i];
            const starValue = 5 - i;

            if (starValue <= ratingValue) {
                label.innerHTML = '<i class="fas fa-star text-yellow-400"></i>';
            } else {
                label.innerHTML = '<i class="far fa-star text-gray-300"></i>';
            }
        }
    }

    modalRatingInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateModalStars(parseInt(this.value));
        });
    });

    modalStarLabels.forEach((label, index) => {
        label.addEventListener('mouseenter', function() {
            const hoverValue = 5 - index;
            for (let i = 0; i < modalStarLabels.length; i++) {
                const starLabel = modalStarLabels[i];
                const starValue = 5 - i;

                if (starValue <= hoverValue) {
                    starLabel.innerHTML = '<i class="fas fa-star text-yellow-400"></i>';
                } else {
                    starLabel.innerHTML = '<i class="far fa-star text-gray-300"></i>';
                }
            }
        });

        label.addEventListener('mouseleave', function() {
            const checkedInput = document.querySelector('#ratingModal input[name="rating"]:checked');
            if (checkedInput) {
                updateModalStars(parseInt(checkedInput.value));
            } else {
                for (let i = 0; i < modalStarLabels.length; i++) {
                    modalStarLabels[i].innerHTML = '<i class="far fa-star text-gray-300"></i>';
                }
            }
        });
    });

    function openRatingModal(grnId) {
        const form = document.getElementById('ratingForm');
        form.action = '/procurement/vendor-ratings/store/' + grnId;
        document.getElementById('ratingModal').classList.add('open');
    }

    function closeRatingModal() {
        document.getElementById('ratingModal').classList.remove('open');
    }

    function openEmailModal() {
        document.getElementById('emailModal').classList.add('open');
    }

    function closeEmailModal(e) {
        if (!e || e.target === document.getElementById('emailModal')) {
            document.getElementById('emailModal').classList.remove('open');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRatingModal();
            closeEmailModal();
        }
    });

    function sendEmailAndDownload() {
        const form = document.getElementById('emailForm');
        const email = form.querySelector('input[name="email"]').value.trim();
        const subject = form.querySelector('input[name="subject"]').value.trim();

        if (!email || !subject) {
            alert('Please fill in the recipient email and subject.');
            return;
        }

        document.getElementById('sendingIndicator').classList.remove('hidden');
        const btn = document.getElementById('sendEmailBtn');
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');

        const pdfUrl = "{{ route('procurement.goods-received.download-pdf', $grn->id) }}";
        const pdfLink = document.createElement('a');
        pdfLink.href = pdfUrl;
        pdfLink.download = 'GRN_{{ $grn->grn_number }}.pdf';
        pdfLink.target = '_blank';
        document.body.appendChild(pdfLink);
        pdfLink.click();
        document.body.removeChild(pdfLink);

        setTimeout(() => {
            form.submit();
        }, 800);
    }
</script>
@endsection
