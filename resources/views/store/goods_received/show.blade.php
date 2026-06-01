@extends('layouts.store')

@section('title', 'GRN #' . $grn->grn_number)
@section('page-title', 'Goods Received Note Details')

@section('content')
@php
    use App\Models\BusinessSetting;

    $companyName = BusinessSetting::get('company_name', 'Company Name');
    if ($companyName && (str_starts_with($companyName, '"') || str_starts_with($companyName, "'"))) {
        $companyName = trim($companyName, '"\'');
    }

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
    $receivedByUser = $grn->receivedByUser;
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
    // FIXED: Use verifiedBy relationship, not verifiedByUser
    $verifiedByUser = $grn->verifiedBy;
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

    // ── Financial calculations ─────────────────────────────────────────────────
    $vatRate = 18;
    $subtotalExclVat = 0;
    $totalVat = 0;
    $totalInclVat = 0;
    foreach ($grn->items as $item) {
        $lineSubtotal = $item->quantity_accepted * $item->unit_cost;
        $lineVat      = $lineSubtotal * ($vatRate / 100);
        $subtotalExclVat += $lineSubtotal;
        $totalVat        += $lineVat;
        $totalInclVat    += ($lineSubtotal + $lineVat);
    }

    // ── Status helpers ────────────────────────────────────────────────────────
    $statusClass = match($grn->status) {
        'draft'              => 'status-draft',
        'inventory_updated'  => 'status-inventory_updated',
        'verified'           => 'status-verified',
        'rejected'           => 'status-rejected',
        default              => 'status-draft',
    };
    $statusText = match($grn->status) {
        'draft'             => 'Draft',
        'inventory_updated' => 'Inventory Updated',
        'verified'          => 'Verified',
        'rejected'          => 'Rejected',
        default             => ucfirst($grn->status),
    };

    $isVerified = $grn->status === 'verified';
@endphp

<style>
    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            padding: 20px;
        }
        .no-print { display: none !important; }
        button, .btn, .action-buttons { display: none !important; }
        .company-logo, .print-logo { max-height: 50px !important; width: auto !important; }
        .signature-img { max-height: 60px !important; max-width: 180px !important; }
        .stamp-img { max-height: 70px !important; max-width: 120px !important; }
    }
    .company-logo { max-height: 60px; width: auto; }
    .signature-img { max-height: 60px; max-width: 180px; }
    .stamp-img { max-height: 70px; max-width: 120px; }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .status-draft             { background: #fef3c7; color: #92400e; }
    .status-inventory_updated { background: #d1fae5; color: #065f46; }
    .status-verified          { background: #dbeafe; color: #1e40af; }
    .status-rejected          { background: #fee2e2; color: #991b1b; }
    .info-label {
        font-weight: 600;
        color: #64748b;
        width: 140px;
        display: inline-block;
        font-size: 11px;
    }
    .vat-summary-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 12px 16px;
    }
    .vat-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        padding: 3px 0;
    }
    .vat-row.total-row {
        font-weight: 700;
        font-size: 13px;
        border-top: 1px solid #86efac;
        margin-top: 6px;
        padding-top: 6px;
        color: #065f46;
    }
    .sig-block {
        text-align: center;
        flex: 1;
        padding: 0 12px;
    }
    .sig-block-title {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }
    .sig-line {
        border-top: 1px solid #9ca3af;
        margin-top: 8px;
        padding-top: 4px;
        width: 80%;
        margin-left: auto;
        margin-right: auto;
    }
    .sig-name { font-size: 11px; color: #374151; margin-top: 4px; }
    .sig-date { font-size: 10px; color: #9ca3af; }
</style>

<div class="space-y-4">

    {{-- Header with Action Buttons --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-orange-700 to-orange-600 px-4 py-3 flex justify-between items-center no-print">
            <div>
                <h2 class="text-sm font-bold text-white">GRN #{{ $grn->grn_number }}</h2>
                <p class="text-orange-100 text-[11px] mt-0.5">Created on {{ $grn->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="printGRN()" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
                <button onclick="downloadPDF()" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </button>
                <a href="{{ route('store.goods-received.index') }}" class="bg-gray-600 text-white px-3 py-1 rounded text-xs hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        {{-- Printable Section --}}
        <div id="print-section" class="p-4">

            {{-- Logo and Header --}}
            <div class="flex justify-between items-start mb-4 pb-3 border-b">
                <div>
                    @if($companyLogoB64)
                        <img src="{{ $companyLogoB64 }}" class="company-logo print-logo" alt="Logo">
                    @else
                        <h2 class="text-xl font-bold text-gray-800">{{ $companyName }}</h2>
                    @endif
                </div>
                <div class="text-right">
                    <h1 class="text-xl font-bold text-blue-600">GOODS RECEIVED NOTE</h1>
                    <p class="text-sm text-gray-500">{{ $grn->grn_number }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">Created: {{ $grn->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            {{-- Status Badge --}}
            <div class="mb-4">
                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
            </div>

            {{-- GRN Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">GRN Information</h4>
                    <div class="space-y-1">
                        <div><span class="info-label">GRN Number:</span> <span class="text-xs font-mono">{{ $grn->grn_number }}</span></div>
                        <div><span class="info-label">PO Reference:</span> <span class="text-xs">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</span></div>
                        <div><span class="info-label">Received Date:</span> <span class="text-xs">{{ $grn->received_date->format('d M Y') }}</span></div>
                        <div><span class="info-label">Delivery Note #:</span> <span class="text-xs">{{ $grn->delivery_note_number ?? '—' }}</span></div>
                    </div>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Delivery Information</h4>
                    <div class="space-y-1">
                        <div><span class="info-label">Delivered By:</span> <span class="text-xs">{{ $grn->delivered_by_name ?? '—' }}</span></div>
                        <div><span class="info-label">Phone:</span> <span class="text-xs">{{ $grn->delivered_by_phone ?? '—' }}</span></div>
                        <div><span class="info-label">Email:</span> <span class="text-xs">{{ $grn->delivered_by_email ?? '—' }}</span></div>
                        @if($isVerified && $grn->verified_at)
                        <div><span class="info-label">Verified On:</span> <span class="text-xs">{{ \Carbon\Carbon::parse($grn->verified_at)->format('d M Y, H:i') }}</span></div>
                        @endif
                        <div><span class="info-label">Received By:</span> <span class="text-xs">{{ $grn->received_by ?? '—' }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Vendor Information --}}
            <div class="mb-4">
                <h4 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Vendor Information</h4>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div><span class="info-label">Vendor Name:</span> <span class="text-xs">{{ $grn->vendor->name ?? 'N/A' }}</span></div>
                        <div><span class="info-label">Contact Person:</span> <span class="text-xs">{{ $grn->vendor->contact_person ?? 'N/A' }}</span></div>
                        <div><span class="info-label">Phone:</span> <span class="text-xs">{{ $grn->vendor->phone ?? 'N/A' }}</span></div>
                        <div><span class="info-label">Email:</span> <span class="text-xs">{{ $grn->vendor->email ?? 'N/A' }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($grn->notes)
            <div class="mb-4">
                <h4 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Notes</h4>
                <div class="bg-gray-50 rounded-lg p-2">
                    <p class="text-xs text-gray-600">{{ $grn->notes }}</p>
                </div>
            </div>
            @endif

            {{-- Items Table --}}
            <div class="mb-4">
                <h4 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Received Items</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 border text-left">Item</th>
                                <th class="p-2 border text-center w-16">Ordered</th>
                                <th class="p-2 border text-center w-16">Received</th>
                                <th class="p-2 border text-center w-16">Accepted</th>
                                <th class="p-2 border text-center w-16">Rejected</th>
                                <th class="p-2 border text-right w-24">Unit Cost</th>
                                <th class="p-2 border text-right w-24">Subtotal (excl VAT)</th>
                                <th class="p-2 border text-right w-20">VAT ({{ $vatRate }}%)</th>
                                <th class="p-2 border text-right w-24">Total (incl VAT)</th>
                                <th class="p-2 border text-left w-32">Rejection Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grn->items as $item)
                            @php
                                $lineSubtotal = $item->quantity_accepted * $item->unit_cost;
                                $lineVat      = $lineSubtotal * ($vatRate / 100);
                                $lineTotal    = $lineSubtotal + $lineVat;
                            @endphp
                            <tr class="border-b">
                                <td class="p-2 border">
                                    {{ $item->inventoryItem->name ?? 'N/A' }}
                                    @if($item->inventoryItem && $item->inventoryItem->item_code)
                                        <br><span class="text-[9px] text-gray-400">Code: {{ $item->inventoryItem->item_code }}</span>
                                    @endif
                                    @if($item->pack_type && $item->pack_size)
                                        <br><span class="text-[9px] text-blue-500">
                                            📦 {{ $item->number_of_packs ?? 1 }} × {{ $item->pack_type }} ({{ $item->pack_size }} units/pack)
                                        </span>
                                    @endif
                                </td>
                                <td class="p-2 border text-center">{{ number_format($item->quantity_ordered, 2) }}</td>
                                <td class="p-2 border text-center">{{ number_format($item->quantity_received, 2) }}</td>
                                <td class="p-2 border text-center font-semibold text-green-600">{{ number_format($item->quantity_accepted, 2) }}</td>
                                <td class="p-2 border text-center">
                                    @if($item->quantity_rejected > 0)
                                        <span class="text-red-600">{{ number_format($item->quantity_rejected, 2) }}</span>
                                    @else
                                        —
                                    @endif
 <!-- #region -->                   </td>
                                <td class="p-2 border text-right">UGX {{ number_format($item->unit_cost, 2) }}</td>
                                <td class="p-2 border text-right">UGX {{ number_format($lineSubtotal, 2) }}</td>
                                <td class="p-2 border text-right text-blue-600">UGX {{ number_format($lineVat, 2) }}</td>
                                <td class="p-2 border text-right font-semibold">UGX {{ number_format($lineTotal, 2) }}</td>
                                <td class="p-2 border text-xs text-gray-600">{{ $item->rejection_reason ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="6" class="p-2 text-right font-bold text-xs">Subtotal (excl VAT):</td>
                                <td class="p-2 text-right font-bold text-xs">UGX {{ number_format($subtotalExclVat, 2) }}</td>
                                <td class="p-2 text-right font-bold text-blue-600 text-xs">UGX {{ number_format($totalVat, 2) }}</td>
                                <td class="p-2 text-right font-bold text-green-700 text-xs">UGX {{ number_format($totalInclVat, 2) }}</td>
                                <td class="p-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Financial Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-blue-50 rounded p-2 text-center">
                        <p class="text-[9px] text-blue-600 uppercase font-semibold">Total Items</p>
                        <p class="text-lg font-bold text-blue-800">{{ $grn->items->count() }}</p>
                    </div>
                    <div class="bg-green-50 rounded p-2 text-center">
                        <p class="text-[9px] text-green-600 uppercase font-semibold">Total Accepted</p>
                        <p class="text-lg font-bold text-green-800">{{ number_format($grn->items->sum('quantity_accepted'), 2) }}</p>
                    </div>
                    <div class="bg-red-50 rounded p-2 text-center">
                        <p class="text-[9px] text-red-600 uppercase font-semibold">Total Rejected</p>
                        <p class="text-lg font-bold text-red-800">{{ number_format($grn->items->sum('quantity_rejected'), 2) }}</p>
                    </div>
                </div>

                <div class="vat-summary-box">
                    <h4 class="text-[10px] font-bold text-gray-600 uppercase tracking-wide mb-2">Financial Summary</h4>
                    <div class="vat-row subtotal-row">
                        <span>Subtotal (excl. VAT {{ $vatRate }}%):</span>
                        <span>UGX {{ number_format($subtotalExclVat, 2) }}</span>
                    </div>
                    <div class="vat-row vat-amount-row">
                        <span>VAT @ {{ $vatRate }}%:</span>
                        <span>UGX {{ number_format($totalVat, 2) }}</span>
                    </div>
                    <div class="vat-row total-row">
                        <span>Total Payable (incl. VAT):</span>
                        <span>UGX {{ number_format($totalInclVat, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Signatures Section --}}
            <div class="mt-6 pt-4 border-t">
                <div class="flex justify-between gap-4">

                    {{-- Received By --}}
                    <div class="sig-block">
                        <p class="sig-block-title">Received By</p>
                        @if($receivedBySignatureB64)
                            <img src="{{ $receivedBySignatureB64 }}" class="signature-img mx-auto" alt="Signature">
                        @else
                            <div class="h-12"></div>
                        @endif
                        <div class="sig-line"></div>
                        <p class="sig-name">
                            {{ $grn->received_by ?? (($receivedByUser->first_name ?? '') . ' ' . ($receivedByUser->last_name ?? '')) }}
                        </p>
                        <p class="sig-date">{{ $grn->created_at ? $grn->created_at->format('d M Y') : '' }}</p>
                    </div>

                    {{-- Verified By --}}
                    <div class="sig-block">
                        <p class="sig-block-title">Verified By</p>
                        @if($isVerified && $verifiedBySignatureB64)
                            <img src="{{ $verifiedBySignatureB64 }}" class="signature-img mx-auto" alt="Signature">
                        @elseif($isVerified && !$verifiedBySignatureB64)
                            <div class="h-12 flex items-center justify-center">
                                <span class="text-xs text-gray-400">No signature on file</span>
                            </div>
                        @else
                            <div class="h-12 flex items-center justify-center">
                                <span class="text-xs text-yellow-600">Pending Verification</span>
                            </div>
                        @endif
                        <div class="sig-line"></div>
                        @if($isVerified && $verifiedByUser)
                            <p class="sig-name">{{ $verifiedByUser->first_name ?? '' }} {{ $verifiedByUser->last_name ?? '' }}</p>
                            <p class="sig-date">{{ $grn->verified_at ? \Carbon\Carbon::parse($grn->verified_at)->format('d M Y') : '' }}</p>
                        @else
                            <p class="sig-name text-gray-400">Not yet verified</p>
                            <p class="sig-date"></p>
                        @endif
                    </div>

                    {{-- Company Stamp --}}
                    <div class="sig-block">
                        <p class="sig-block-title">Company Stamp</p>
                        @if($companyStampB64)
                            <img src="{{ $companyStampB64 }}" class="stamp-img mx-auto" alt="Company Stamp">
                        @else
                            <div class="h-12"></div>
                        @endif
                        <div class="sig-line"></div>
                        <p class="sig-date">Authorised Signatory</p>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-6 pt-3 border-t text-center">
                <p class="text-[9px] text-gray-400">This is a computer generated document. Valid without signature.</p>
                <p class="text-[9px] text-gray-400">{{ $companyName }} – All Rights Reserved</p>
            </div>

        </div>{{-- /print-section --}}
    </div>

</div>

<script>
    function printGRN() {
        const printContents = document.getElementById('print-section').innerHTML;
        const originalTitle = document.title;
        document.title = 'GRN {{ $grn->grn_number }}';

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>GRN {{ $grn->grn_number }}</title>
                <style>
                    body { padding: 20px; font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 6px 8px; font-size: 11px; }
                    th { background-color: #f2f2f2; }
                    .company-logo { max-height: 50px !important; width: auto !important; }
                    .signature-img { max-height: 60px !important; max-width: 180px !important; }
                    .stamp-img { max-height: 70px !important; max-width: 120px !important; }
                    @media print { body { margin: 0; padding: 20px; } }
                </style>
            </head>
            <body>${printContents}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
        document.title = originalTitle;
    }

    function downloadPDF() {
        const element = document.getElementById('print-section');
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: 'GRN-{{ $grn->grn_number }}.pdf',
            image: { type: 'jpeg', quality: 0.95 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

@endsection
