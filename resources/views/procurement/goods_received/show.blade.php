@extends('layouts.procurement')
@section('title', 'GRN #' . $grn->grn_number)

@section('content')
@php
    use App\Models\BusinessSetting;

    // ── Logo (base64) ──────────────────────────────────────────────────────────
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

    // ── Stamp (base64) ────────────────────────────────────────────────────────
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

    // ── Received By User Signature (base64) ───────────────────────────────────
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

    // ── Verified By User Signature (base64) ───────────────────────────────────
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

    // ── VAT Calculations ───────────────────────────────────────────────────────
    $vatRate = 18;
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

    $isVerified = in_array($grn->status, ['verified', 'inventory_updated', 'completed']);
    $canVerify  = !$isVerified && in_array($grn->status, ['draft', 'received']);
@endphp

<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl shadow-xl">
        <div class="px-6 py-6 sm:px-8 sm:py-7">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-blue-300 text-xs font-semibold tracking-widest uppercase">Goods Received Note</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase
                            @if($grn->status == 'completed') bg-green-100 text-green-700
                            @elseif($grn->status == 'inventory_updated') bg-blue-100 text-blue-700
                            @elseif($grn->status == 'verified') bg-purple-100 text-purple-700
                            @elseif($grn->status == 'draft') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-700 @endif">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ ucfirst(str_replace('_', ' ', $grn->status)) }}
                        </span>
                    </div>
                    <h1 class="font-mono text-2xl sm:text-3xl font-bold text-white tracking-tight">{{ $grn->grn_number }}</h1>
                    <p class="text-blue-200 text-sm mt-1">
                        Created {{ $grn->created_at->format('F d, Y') }} at {{ $grn->created_at->format('H:i') }}
                        &nbsp;·&nbsp;
                        by <span class="text-white font-semibold">
                            {{ $grn->createdBy->first_name ?? '' }} {{ $grn->createdBy->last_name ?? 'Procurement' }}
                        </span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">

                    {{-- ✅ VERIFY BUTTON — only shown when not yet verified --}}
                    @if($canVerify)
                    <button type="button" onclick="openVerifyModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-purple-600 text-white rounded-lg text-xs font-semibold hover:bg-purple-700 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Verify GRN
                    </button>
                    @endif

                    @if(in_array($grn->status, ['inventory_updated', 'completed']) && !$grn->isRated())
                    <button type="button" onclick="openRatingModal({{ $grn->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-600 text-white rounded-lg text-xs font-semibold hover:bg-amber-700 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Rate Delivery
                    </button>
                    @endif

                    <button type="button" onclick="openAttachPdfModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-700 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Attach Document
                    </button>

                    <button type="button" onclick="openEmailModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Share Email
                    </button>

                    <a href="{{ route('procurement.goods-received.download-pdf', $grn->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download PDF
                    </a>

<a href="{{ route('procurement.goods-received.print', $grn->id) }}"
   target="_blank"
   class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-600 text-white rounded-lg text-xs font-semibold hover:bg-slate-700 transition shadow-sm">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
    </svg>
    Print
</a>
                    <a href="{{ route('procurement.goods-received.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-300 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Rating Display (if already rated) --}}
    @if($grn->isRated())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">Delivery Rated</p>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="flex items-center gap-0.5">
                            @php
                                $rating = $grn->rating->rating;
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<svg class="w-3.5 h-3.5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                                    } else {
                                        echo '<svg class="w-3.5 h-3.5 text-yellow-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
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
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-100 rounded-full">
                <svg class="w-3.5 h-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-xs font-bold text-yellow-700">{{ number_format($grn->rating->rating, 1) }} / 5.0</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- KPI TILES --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative overflow-hidden">
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-500 rounded-b-xl"></div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total Items</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $grn->items->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Line items received</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative overflow-hidden">
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-green-500 rounded-b-xl"></div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total Accepted</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($grn->items->sum('quantity_accepted'), 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Units accepted</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative overflow-hidden">
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-red-500 rounded-b-xl"></div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total Rejected</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($grn->items->sum('quantity_rejected'), 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Units rejected</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative overflow-hidden">
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500 rounded-b-xl"></div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total Value (incl. VAT)</p>
            <p class="text-lg font-bold text-amber-600 mt-1">UGX {{ number_format($totalInclVat, 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Payable to vendor</p>
        </div>
    </div>

    {{-- Info Cards Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- GRN Details Card --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm lg:col-span-2">
            <div class="border-b border-gray-100 px-5 py-3 flex items-center gap-2 bg-gray-50/50">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-700">GRN Details</span>
            </div>
            <div class="p-5 divide-y divide-gray-100">
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">GRN Number</span>
                    <span class="text-sm font-mono font-semibold text-blue-700">{{ $grn->grn_number }}</span>
                </div>
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">PO Reference</span>
                    <span class="text-sm font-mono font-semibold">
                        <a href="{{ route('procurement.purchase-orders.show', $grn->purchaseOrder->id ?? 0) }}" class="text-blue-600 hover:underline">
                            {{ $grn->purchaseOrder->po_number ?? 'N/A' }}
                        </a>
                    </span>
                </div>
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">Vendor</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $grn->vendor->name ?? 'N/A' }}</span>
                </div>
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">Vendor Email</span>
                    <span class="text-sm text-blue-600">{{ $grn->vendor->email ?? '—' }}</span>
                </div>
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">Received Date</span>
                    <span class="text-sm text-gray-700">{{ $grn->received_date->format('F d, Y') }}</span>
                </div>
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">Received By</span>
                    <span class="text-sm text-gray-700 font-semibold">
                        {{ $grn->received_by ?? (($receivedByUser->first_name ?? '') . ' ' . ($receivedByUser->last_name ?? '')) }}
                    </span>
                </div>
                @if($grn->delivery_note_number)
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">Vendor DN #</span>
                    <span class="text-sm font-mono text-gray-700">{{ $grn->delivery_note_number }}</span>
                </div>
                @endif
                @if($grn->notes)
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">Notes</span>
                    <span class="text-sm text-gray-500 italic">{{ $grn->notes }}</span>
                </div>
                @endif
                @if($isVerified && $grn->verified_at)
                <div class="flex items-start py-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-36 shrink-0">Verified On</span>
                    <span class="text-sm text-purple-700 font-semibold">{{ \Carbon\Carbon::parse($grn->verified_at)->format('F d, Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Side Cards --}}
        <div class="space-y-4">

            {{-- Delivered By Card --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="border-b border-gray-100 px-5 py-3 flex items-center gap-2 bg-gray-50/50">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Delivered By</span>
                </div>
                <div class="p-5 space-y-2">
                    @if($grn->delivered_by_name)
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-orange-700 text-sm font-bold">{{ substr($grn->delivered_by_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">{{ $grn->delivered_by_name }}</p>
                            @if($grn->delivered_by_phone)
                                <p class="text-xs text-gray-500 mt-0.5">📞 {{ $grn->delivered_by_phone }}</p>
                            @endif
                            @if($grn->delivered_by_email)
                                <p class="text-xs text-blue-500 mt-0.5">✉️ {{ $grn->delivered_by_email }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        <p class="text-sm text-gray-400 italic">No delivery personnel recorded.</p>
                    @endif
                </div>
            </div>

            {{-- Created By Card --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="border-b border-gray-100 px-5 py-3 flex items-center gap-2 bg-gray-50/50">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Created By</span>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">
                                {{ substr($grn->createdBy->first_name ?? 'P', 0, 1) }}{{ substr($grn->createdBy->last_name ?? '', 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">
                                {{ $grn->createdBy->first_name ?? 'Procurement' }}
                                {{ $grn->createdBy->last_name ?? 'Officer' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $grn->createdBy->email ?? '' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $grn->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- VAT / Financial Summary Card --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="border-b border-gray-100 px-5 py-3 flex items-center gap-2 bg-gray-50/50">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Financial Summary</span>
                </div>
                <div class="p-5 space-y-2">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-xs text-gray-500 font-semibold">PO Total</span>
                        <span class="font-mono text-sm font-semibold text-gray-700">UGX {{ number_format($grn->po_total_amount ?? 0, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-t border-dashed border-gray-200 pt-2 mt-2">
                        <span class="text-xs text-gray-500 font-semibold">Subtotal (excl. VAT)</span>
                        <span class="font-mono text-sm font-semibold text-gray-700">UGX {{ number_format($subtotalExclVat, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-xs text-blue-600 font-semibold">VAT @ {{ $vatRate }}%</span>
                        <span class="font-mono text-sm font-semibold text-blue-600">UGX {{ number_format($totalVat, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-t border-gray-300 mt-1">
                        <span class="text-xs font-bold text-gray-800 uppercase tracking-wide">Total Payable (incl. VAT)</span>
                        <span class="font-mono text-base font-bold text-emerald-700">UGX {{ number_format($totalInclVat, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Table Card --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="border-b border-gray-100 px-5 py-3 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-700">Received Items</span>
            </div>
            <span class="text-xs bg-indigo-50 text-indigo-700 font-semibold px-2 py-1 rounded-full">
                {{ $grn->items->count() }} item{{ $grn->items->count() !== 1 ? 's' : '' }}
            </span>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-[11px] font-bold uppercase text-gray-500">#</th>
                        <th class="text-left px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Item</th>
                        <th class="text-center px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Ordered</th>
                        <th class="text-center px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Received</th>
                        <th class="text-center px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Accepted</th>
                        <th class="text-center px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Rejected</th>
                        <th class="text-right px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Unit Cost</th>
                        <th class="text-right px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Subtotal</th>
                        <th class="text-right px-4 py-3 text-[11px] font-bold uppercase text-gray-500 text-blue-600">VAT ({{ $vatRate }}%)</th>
                        <th class="text-right px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Total</th>
                        <th class="text-left px-4 py-3 text-[11px] font-bold uppercase text-gray-500">Rejection Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($grn->items as $i => $item)
                    @php
                        $lineSubtotal = $item->quantity_accepted * $item->unit_cost;
                        $lineVat      = $lineSubtotal * ($vatRate / 100);
                        $lineTotal    = $lineSubtotal + $lineVat;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-xs text-gray-400 font-semibold">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800 text-sm">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                            @if($item->inventoryItem && $item->inventoryItem->item_code)
                                <p class="text-[10px] text-gray-400 mt-0.5">Code: {{ $item->inventoryItem->item_code }}</p>
                            @endif
                            @if($item->pack_type && $item->pack_size)
                                <p class="text-[10px] text-blue-500 mt-0.5">📦 {{ $item->number_of_packs }} × {{ $item->pack_type }} ({{ $item->pack_size }} units/pack)</p>
                            @endif
                            @if($item->notes)
                                <p class="text-xs text-gray-400 mt-0.5 italic">{{ $item->notes }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-semibold">{{ number_format($item->quantity_ordered, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-1 bg-green-50 text-green-700 rounded text-xs font-semibold">{{ number_format($item->quantity_received, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-1 bg-emerald-50 text-emerald-700 rounded text-xs font-semibold">{{ number_format($item->quantity_accepted, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->quantity_rejected > 0)
                                <span class="inline-block px-2 py-1 bg-red-50 text-red-700 rounded text-xs font-semibold">{{ number_format($item->quantity_rejected, 2) }}</span>
                            @else
                                <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-gray-600 text-xs">{{ number_format($item->unit_cost, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-gray-600 text-xs">{{ number_format($lineSubtotal, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-blue-600 text-xs">{{ number_format($lineVat, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-emerald-700 text-xs">{{ number_format($lineTotal, 2) }}</td>
                        <td class="px-4 py-3">
                            @if($item->rejection_reason)
                                <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-md">{{ $item->rejection_reason }}</span>
                            @else
                                <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden p-4 space-y-3">
            @foreach($grn->items as $i => $item)
            @php
                $lineSubtotal = $item->quantity_accepted * $item->unit_cost;
                $lineVat      = $lineSubtotal * ($vatRate / 100);
                $lineTotal    = $lineSubtotal + $lineVat;
            @endphp
            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="text-xs text-gray-400 font-semibold">ITEM {{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span>
                        <p class="font-semibold text-gray-800 text-sm mt-0.5">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">incl. VAT</p>
                        <span class="font-mono text-sm font-bold text-emerald-700">UGX {{ number_format($lineTotal, 0) }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-2 text-center mb-2">
                    <div>
                        <p class="text-[10px] text-gray-400 mb-1">Ordered</p>
                        <span class="inline-block px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-semibold">{{ number_format($item->quantity_ordered, 0) }}</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-1">Received</p>
                        <span class="inline-block px-2 py-0.5 bg-green-50 text-green-700 rounded text-xs font-semibold">{{ number_format($item->quantity_received, 0) }}</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-1">Accepted</p>
                        <span class="inline-block px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-xs font-semibold">{{ number_format($item->quantity_accepted, 0) }}</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-1">Rejected</p>
                        <span class="inline-block px-2 py-0.5 bg-red-50 text-red-700 rounded text-xs font-semibold">{{ number_format($item->quantity_rejected, 0) }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center pt-2 border-t border-gray-100">
                    <div>
                        <p class="text-[10px] text-gray-400">Subtotal</p>
                        <p class="text-xs font-mono text-gray-600">{{ number_format($lineSubtotal, 0) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-500">VAT ({{ $vatRate }}%)</p>
                        <p class="text-xs font-mono text-blue-600">{{ number_format($lineVat, 0) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-emerald-600">Total</p>
                        <p class="text-xs font-mono font-bold text-emerald-700">{{ number_format($lineTotal, 0) }}</p>
                    </div>
                </div>
                @if($item->rejection_reason)
                <p class="mt-2 text-xs text-red-600 bg-red-50 px-3 py-1.5 rounded-lg">
                    <span class="font-semibold">Rejection:</span> {{ $item->rejection_reason }}
                </p>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Totals Footer --}}
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-t border-green-200 px-5 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ $grn->items->count() }} items &nbsp;·&nbsp; {{ number_format($grn->items->sum('quantity_accepted'), 2) }} units accepted</span>
                    @if($grn->items->sum('quantity_rejected') > 0)
                        <span class="text-red-500 font-medium">&nbsp;·&nbsp; {{ number_format($grn->items->sum('quantity_rejected'), 2) }} rejected</span>
                    @endif
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide">Subtotal (excl. VAT)</p>
                        <p class="font-mono text-sm font-bold text-gray-700">UGX {{ number_format($subtotalExclVat, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-blue-500 uppercase font-semibold tracking-wide">VAT ({{ $vatRate }}%)</p>
                        <p class="font-mono text-sm font-bold text-blue-600">UGX {{ number_format($totalVat, 2) }}</p>
                    </div>
                    <div class="text-right border-l border-green-300 pl-6">
                        <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide">Total Payable (incl. VAT)</p>
                        <p class="font-mono text-base font-bold text-emerald-700">UGX {{ number_format($totalInclVat, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         SIGNATURES SECTION
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="border-b border-gray-100 px-5 py-3 flex items-center gap-2 bg-gray-50/50">
            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">Authorisations & Signatures</span>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

    {{-- Received By --}}
    <div class="flex flex-col items-center text-center p-4 rounded-xl border border-gray-100 bg-gray-50/50">
        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-3">Received By</p>
        @if($receivedBySignatureB64)
            <img src="{{ $receivedBySignatureB64 }}" class="max-h-16 max-w-[180px] mb-2" alt="Signature">
        @else
            <div class="h-16 w-full max-w-[180px] flex items-end justify-center mb-2">
                <p class="text-[10px] text-gray-300 italic">No signature on file</p>
            </div>
        @endif
        <div class="w-40 border-t border-gray-400 pt-2 mt-1"></div>
        <p class="text-sm font-semibold text-gray-800 mt-1">
            {{ $grn->received_by ?? (($receivedByUser->first_name ?? '') . ' ' . ($receivedByUser->last_name ?? '')) }}
        </p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $grn->created_at ? $grn->created_at->format('d M Y') : '' }}</p>
    </div>

{{-- Delivered By --}}
<div class="flex flex-col items-center text-center p-4 rounded-xl border border-gray-100 bg-gray-50/50">
    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-3">Delivered By</p>

    <div class="w-40 border-t border-gray-400 pt-2 mt-1"></div>
    <p class="text-sm font-semibold text-gray-800 mt-1">
        {{ $grn->delivered_by_name ?? '—' }}
    </p>
</div>

    {{-- Verified By --}}
    <div class="flex flex-col items-center text-center p-4 rounded-xl border
        @if($isVerified) border-purple-200 bg-purple-50/30 @else border-dashed border-gray-200 bg-gray-50/30 @endif">
        <p class="text-[11px] font-bold uppercase tracking-widest @if($isVerified) text-purple-400 @else text-gray-400 @endif mb-3">Verified By</p>
        @if($isVerified && $verifiedByUser)
            @if($verifiedBySignatureB64)
                <img src="{{ $verifiedBySignatureB64 }}" class="max-h-16 max-w-[180px] mb-2" alt="Verified Signature">
            @else
                <div class="h-16 w-full max-w-[180px] flex items-end justify-center mb-2">
                    <p class="text-[10px] text-gray-300 italic">No signature on file</p>
                </div>
            @endif
            <div class="w-40 border-t border-purple-400 pt-2 mt-1"></div>
            <p class="text-sm font-semibold text-purple-800 mt-1">
                {{ $verifiedByUser->first_name }} {{ $verifiedByUser->last_name }}
            </p>
            <p class="text-xs text-purple-400 mt-0.5">
                {{ $grn->verified_at ? $grn->verified_at->format('d M Y') : '' }}
            </p>
            <span class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-[10px] font-semibold">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Verified
            </span>
        @else
            <div class="h-16 w-full max-w-[180px] flex items-center justify-center mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 border border-dashed border-yellow-300 rounded-lg text-[11px] text-yellow-700 font-medium">
                    ⏳ Pending Verification
                </span>
            </div>
            <div class="w-40 border-t border-dashed border-gray-300 pt-2 mt-1"></div>
            <p class="text-xs text-gray-400 mt-1">Awaiting approval</p>
        @endif
    </div>

    {{-- Company Stamp --}}
    <div class="flex flex-col items-center text-center p-4 rounded-xl border border-gray-100 bg-gray-50/50">
        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-3">Company Stamp</p>
        @if($companyStampB64)
            <img src="{{ $companyStampB64 }}" class="max-h-16 max-w-[120px] mb-2" alt="Company Stamp">
        @else
            <div class="h-16 w-full max-w-[120px] flex items-end justify-center mb-2">
                <p class="text-[10px] text-gray-300 italic">No stamp on file</p>
            </div>
        @endif
        <div class="w-40 border-t border-gray-400 pt-2 mt-1"></div>
        <p class="text-xs text-gray-400 mt-1">Authorised Signatory</p>
    </div>

</div>
            </div>
        </div>
    </div>

    {{-- Attached Documents Section --}}
    @if(isset($documents) && $documents->count() > 0)
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="border-b border-gray-100 px-5 py-3 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-700">Attached Documents</span>
            </div>
            <span class="text-xs bg-purple-50 text-purple-700 font-semibold px-2 py-1 rounded-full">{{ $documents->count() }} document(s)</span>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($documents as $doc)
                <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition group" id="doc-{{ $doc->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                @if($doc->mime_type == 'application/pdf') bg-red-100
                                @elseif(strpos($doc->mime_type, 'image') !== false) bg-blue-100
                                @else bg-gray-100 @endif">
                                @if($doc->mime_type == 'application/pdf')
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20 6.83V18a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8.17L20 6.83z"/></svg>
                                @elseif(strpos($doc->mime_type, 'image') !== false)
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20 6.83V18a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8.17L20 6.83z"/></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate" title="{{ $doc->original_name }}">{{ $doc->original_name }}</p>
                                <p class="text-xs text-gray-400">{{ number_format($doc->file_size / 1024, 2) }} KB @if($doc->document_type) · {{ $doc->document_type }} @endif</p>
                                <p class="text-xs text-gray-400">by {{ $doc->uploadedBy->first_name ?? 'User' }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                            <button type="button" onclick="previewDocument('{{ route('procurement.goods-received.preview-document', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <a href="{{ route('procurement.goods-received.download-document', $doc->id) }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded transition" title="Download">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            @if(Auth::user()->is_super_admin || Auth::user()->can('delete_documents'))
                            <button type="button" onclick="deleteDocument({{ $doc->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════════════
     VERIFY GRN MODAL
══════════════════════════════════════════════════════════════════════════════ --}}
<div id="verifyModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center" onclick="closeVerifyModal(event)">
    <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Verify Goods Received Note</h3>
                <p class="text-xs text-gray-500 mt-0.5">GRN #{{ $grn->grn_number }}</p>
            </div>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-5">
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div><span class="text-gray-500">Vendor:</span> <span class="font-semibold text-gray-800">{{ $grn->vendor->name ?? 'N/A' }}</span></div>
                <div><span class="text-gray-500">PO:</span> <span class="font-semibold text-gray-800">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</span></div>
                <div><span class="text-gray-500">Received Date:</span> <span class="font-semibold text-gray-800">{{ $grn->received_date->format('d M Y') }}</span></div>
                <div><span class="text-gray-500">Total (incl. VAT):</span> <span class="font-semibold text-emerald-700">UGX {{ number_format($totalInclVat, 0) }}</span></div>
            </div>
        </div>

        <form id="verifyForm" method="POST" action="{{ route('procurement.goods-received.verify', $grn->id) }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Verification Notes (Optional)</label>
                    <textarea name="verification_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none" placeholder="Add any remarks about this verification..."></textarea>
                </div>
                <div class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-amber-700">By verifying, you confirm that all goods have been checked and the quantities and values are correct. This action will update the GRN status.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeVerifyModal()" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirm Verification
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Rating Modal --}}
<div id="ratingModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center" onclick="closeRatingModal(event)">
    <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Rate Vendor Delivery</h3>
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
                    <div class="flex justify-center gap-1">
                        <div class="flex flex-row-reverse justify-center gap-1">
                            <input type="radio" name="rating" value="5" id="star5" class="hidden" required>
                            <label for="star5" class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition"><i class="far fa-star"></i></label>
                            <input type="radio" name="rating" value="4" id="star4" class="hidden">
                            <label for="star4" class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition"><i class="far fa-star"></i></label>
                            <input type="radio" name="rating" value="3" id="star3" class="hidden">
                            <label for="star3" class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition"><i class="far fa-star"></i></label>
                            <input type="radio" name="rating" value="2" id="star2" class="hidden">
                            <label for="star2" class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition"><i class="far fa-star"></i></label>
                            <input type="radio" name="rating" value="1" id="star1" class="hidden">
                            <label for="star1" class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition"><i class="far fa-star"></i></label>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comments (Optional)</label>
                    <textarea name="comment" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Share your experience..."></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeRatingModal()" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium transition">Submit Rating</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Email Modal --}}
<div id="emailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center" onclick="closeEmailModal(event)">
    <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Share GRN via Email</h3>
        </div>
        <form id="emailForm" action="{{ route('procurement.goods-received.send-email', $grn->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Recipient Email</label>
                    <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ $grn->vendor->email ?? '' }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Subject</label>
                    <input type="text" name="subject" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" value="Goods Received Note {{ $grn->grn_number }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Message</label>
                    <textarea name="message" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">Please find attached the Goods Received Note {{ $grn->grn_number }}.</textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEmailModal()" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition">Send & Download</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Attach Document Modal --}}
<div id="attachPdfModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center" onclick="closeAttachPdfModal(event)">
    <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Attach Document to GRN</h3>
        </div>
        <form id="attachPdfForm" method="POST" enctype="multipart/form-data" action="{{ route('procurement.goods-received.attach-document', $grn->id) }}">
            @csrf
            <div class="space-y-5">
                <div class="bg-blue-50 rounded-lg p-3 text-sm">
                    <p class="font-medium text-gray-700">PO: {{ $grn->purchaseOrder->po_number ?? 'N/A' }}</p>
                    <p class="text-gray-500 text-xs mt-1">GRN: {{ $grn->grn_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Document (PDF, JPG, PNG)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-500 transition cursor-pointer" id="dropzone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m-4-4l-4 4m6-20h2m-6 0h.01M12 40h24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>Upload a file</span>
                                    <input id="file-upload" name="document" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFile(this)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                        </div>
                    </div>
                    <div id="filePreview" class="hidden mt-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                            <span id="fileName" class="text-sm text-gray-700"></span>
                            <span id="fileSize" class="text-xs text-gray-400"></span>
                        </div>
                        <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Document Description (Optional)</label>
                    <input type="text" name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="e.g., Signed GRN">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAttachPdfModal()" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">Upload Document</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Document Preview Modal --}}
<div id="documentPreviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden items-center justify-center" onclick="closeDocumentPreview(event)">
    <div class="bg-white rounded-xl w-full max-w-5xl max-h-[90vh] overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-5 py-3 border-b bg-gray-50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <span id="previewTitle" class="font-semibold text-gray-800">Document Preview</span>
            </div>
            <button type="button" onclick="closeDocumentPreview()" class="p-2 text-gray-500 hover:bg-gray-100 rounded transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="previewContent" class="p-4 overflow-auto max-h-[calc(90vh-70px)] flex items-center justify-center min-h-[400px] bg-gray-100">
            <div class="text-center text-gray-500">
                <svg class="animate-spin w-8 h-8 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p>Loading preview...</p>
            </div>
        </div>
    </div>
</div>

<script>
// ── Verify Modal ───────────────────────────────────────────────────────────────
function openVerifyModal() {
    document.getElementById('verifyModal').classList.remove('hidden');
    document.getElementById('verifyModal').style.display = 'flex';
}
function closeVerifyModal(e) {
    if (!e || e.target === document.getElementById('verifyModal')) {
        document.getElementById('verifyModal').classList.add('hidden');
        document.getElementById('verifyModal').style.display = 'none';
    }
}

// ── Rating Modal ───────────────────────────────────────────────────────────────
function openRatingModal(grnId) {
    const form = document.getElementById('ratingForm');
    form.action = '/procurement/vendor-ratings/store/' + grnId;
    document.getElementById('ratingModal').classList.remove('hidden');
    document.getElementById('ratingModal').style.display = 'flex';
}
function closeRatingModal(e) {
    if (!e || e.target === document.getElementById('ratingModal')) {
        document.getElementById('ratingModal').classList.add('hidden');
        document.getElementById('ratingModal').style.display = 'none';
    }
}

// ── Email Modal ────────────────────────────────────────────────────────────────
function openEmailModal() {
    document.getElementById('emailModal').classList.remove('hidden');
    document.getElementById('emailModal').style.display = 'flex';
}
function closeEmailModal(e) {
    if (!e || e.target === document.getElementById('emailModal')) {
        document.getElementById('emailModal').classList.add('hidden');
        document.getElementById('emailModal').style.display = 'none';
    }
}

// ── Attach Document Modal ──────────────────────────────────────────────────────
function openAttachPdfModal() {
    document.getElementById('attachPdfModal').classList.remove('hidden');
    document.getElementById('attachPdfModal').style.display = 'flex';
}
function closeAttachPdfModal(e) {
    if (!e || e.target === document.getElementById('attachPdfModal')) {
        document.getElementById('attachPdfModal').classList.add('hidden');
        document.getElementById('attachPdfModal').style.display = 'none';
        resetFileUpload();
    }
}
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) { alert('File size must be less than 5MB'); input.value = ''; return; }
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) { alert('Only PDF, JPG, and PNG files are allowed'); input.value = ''; return; }
    document.getElementById('fileName').innerText = file.name;
    document.getElementById('fileSize').innerText = (file.size / 1024).toFixed(2) + ' KB';
    document.getElementById('filePreview').classList.remove('hidden');
}
function removeFile() {
    document.getElementById('file-upload').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}
function resetFileUpload() {
    document.getElementById('file-upload').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

// ── Document Preview ───────────────────────────────────────────────────────────
function previewDocument(url, filename, mimeType) {
    const modal = document.getElementById('documentPreviewModal');
    const previewContent = document.getElementById('previewContent');
    document.getElementById('previewTitle').innerText = filename;
    previewContent.innerHTML = '<div class="text-center text-gray-500"><svg class="animate-spin w-8 h-8 mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><p>Loading preview...</p></div>';
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    if (mimeType === 'application/pdf') {
        const container = document.createElement('div');
        container.style.cssText = 'width:100%;display:flex;justify-content:center;align-items:center;flex-direction:column;';
        const canvas = document.createElement('canvas');
        container.appendChild(canvas);
        previewContent.innerHTML = '';
        previewContent.appendChild(container);
        previewContent.style.overflow = 'auto';
        if (typeof pdfjsLib === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js';
            script.onload = function() {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
                renderPdfFirstPage(url, canvas);
            };
            document.head.appendChild(script);
        } else {
            renderPdfFirstPage(url, canvas);
        }
    } else if (mimeType.startsWith('image/')) {
        const img = new Image();
        img.onload = function() {
            previewContent.innerHTML = '';
            img.style.cssText = 'max-width:100%;max-height:80vh;border-radius:8px;';
            previewContent.appendChild(img);
        };
        img.src = url;
    } else {
        previewContent.innerHTML = '<div class="text-center text-gray-500"><p>Preview not available for this file type.</p></div>';
    }
}
function renderPdfFirstPage(url, canvas) {
    pdfjsLib.getDocument(url).promise.then(pdf => pdf.getPage(1)).then(page => {
        const viewport = page.getViewport({ scale: 1.5 });
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        page.render({ canvasContext: canvas.getContext('2d'), viewport });
    }).catch(() => {
        document.getElementById('previewContent').innerHTML = '<div class="text-center text-gray-500"><p>Could not load PDF preview.</p></div>';
    });
}
function closeDocumentPreview(e) {
    if (!e || e.target === document.getElementById('documentPreviewModal')) {
        document.getElementById('documentPreviewModal').classList.add('hidden');
        document.getElementById('documentPreviewModal').style.display = 'none';
    }
}

// ── Delete Document ────────────────────────────────────────────────────────────
function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/procurement/goods-received/delete-document/${documentId}`;
        form.style.display = 'none';
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const method = document.createElement('input');
        method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}

// ── Escape key ────────────────────────────────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVerifyModal();
        closeRatingModal();
        closeEmailModal();
        closeAttachPdfModal();
        closeDocumentPreview();
    }
});
</script>
@endsection
