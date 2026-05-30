@extends('layouts.procurement')

@section('title', 'Purchase Order Details')
@section('page-title', 'Purchase Order Details')

@section('content')
<style>
    /* Professional Document Styling */
    @media print {
        body * {
            visibility: hidden;
        }
        #print-section, #print-section * {
            visibility: visible;
        }
        #print-section {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: white;
        }
        .no-print {
            display: none !important;
        }
        button, .btn, .action-buttons, .share-buttons {
            display: none !important;
        }
        .company-logo {
            max-height: 50px !important;
            width: auto !important;
        }
        .signature-img {
            max-height: 60px !important;
            max-width: 180px !important;
        }
        body, p, span, td, th, div {
            font-size: 11px !important;
        }
        h1, h2, h3, h4 {
            font-size: 14px !important;
        }
    }

    /* Document Layout */
    .po-container {
        max-width: 1100px;
        margin: 0 auto;
        background: white;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        border-radius: 12px;
    }

    .company-logo {
        max-height: 60px;
        width: auto;
    }

    .signature-img {
        max-height: 60px;
        max-width: 180px;
        object-fit: contain;
    }

    .document-header {
        border-bottom: 2px solid #1e40af;
    }

    .po-title {
        color: #1e40af;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .info-label {
        font-weight: 600;
        color: #4b5563;
        background: #f3f4f6;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        display: inline-block;
    }

    .status-badge {
        display: inline-flex;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-draft { background: #fef3c7; color: #d97706; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-sent { background: #dbeafe; color: #1d4ed8; }
    .status-received { background: #e0e7ff; color: #3730a3; }
    .status-cancelled { background: #fee2e2; color: #dc2626; }

    .signature-box {
        border-top: 1px solid #e5e7eb;
        padding-top: 20px;
        margin-top: 20px;
    }

    .share-btn {
        transition: all 0.2s ease;
    }

    .share-btn:hover {
        transform: translateY(-2px);
    }

    .footer-note {
        font-size: 10px;
        color: #9ca3af;
        text-align: center;
        border-top: 1px solid #e5e7eb;
        padding-top: 16px;
        margin-top: 24px;
    }
</style>

@php
    /**
     * Helper: resolve a user's signature to a base64 data URI.
     * Handles paths that may or may not be prefixed with "public/".
     */
    function resolveSignatureBase64($user): ?string {
        if (!$user || !$user->signature_path) {
            return null;
        }
        // Strip any leading slashes and a leading "public/" segment
        $clean = ltrim($user->signature_path, '/');
        $clean = preg_replace('#^public/#', '', $clean);
        $sigPath = storage_path('app/public/' . $clean);
        if (file_exists($sigPath)) {
            $mime = mime_content_type($sigPath);
            $data = base64_encode(file_get_contents($sigPath));
            return 'data:' . $mime . ';base64,' . $data;
        }
        return null;
    }
@endphp

<div class="po-container">
    {{-- Header with Action Buttons --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center no-print rounded-t-xl">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">PO #{{ $purchaseOrder->po_number }}</h3>
            <p class="text-xs text-gray-500">Created on {{ $purchaseOrder->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('procurement.purchase-orders.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-1 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>

            {{-- Share Dropdown --}}
            <div class="relative inline-block text-left ml-2">
                <button type="button" onclick="toggleShareMenu()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 share-btn flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Share
                </button>
                <div id="shareMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="py-1">
                        <button onclick="shareViaEmail()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Email via Gmail
                        </button>
                        <button onclick="shareViaWhatsApp()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            WhatsApp
                        </button>
                        <button onclick="downloadPDF()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download PDF
                        </button>
                        <button onclick="printPO()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Printable Document Section --}}
    <div id="print-section" class="p-8">

        {{-- Header with Logo and Title --}}
        <div class="document-header flex justify-between items-start pb-4 mb-6">
            <div>
                @php
                    $logo = \App\Models\BusinessSetting::getLogo();
                    $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
                    $companyAddress = \App\Models\BusinessSetting::get('address', '');
                    $companyCity = \App\Models\BusinessSetting::get('city', '');
                    $companyCountry = \App\Models\BusinessSetting::get('country', '');
                    $companyPhone = \App\Models\BusinessSetting::get('phone', '');
                    $companyEmail = \App\Models\BusinessSetting::get('email', '');

                    $logoBase64 = null;
                    if ($logo) {
                        $logoPath = public_path(parse_url($logo, PHP_URL_PATH));
                        if (file_exists($logoPath)) {
                            $logoMime = mime_content_type($logoPath);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoBase64 = 'data:' . $logoMime . ';base64,' . $logoData;
                        }
                    }
                @endphp
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="company-logo" alt="Logo">
                @elseif($logo)
                    <img src="{{ $logo }}" class="company-logo" alt="Logo">
                @else
                    <h2 class="text-2xl font-bold text-gray-800">{{ $companyName }}</h2>
                @endif
                <div class="text-xs text-gray-500 mt-2">
                    @if($companyAddress)<p>{{ $companyAddress }}</p>@endif
                    @if($companyCity || $companyCountry)<p>{{ $companyCity }}{{ $companyCity && $companyCountry ? ', ' : '' }}{{ $companyCountry }}</p>@endif
                    @if($companyPhone)<p>Tel: {{ $companyPhone }}</p>@endif
                    @if($companyEmail)<p>Email: {{ $companyEmail }}</p>@endif
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-2xl font-bold po-title">PURCHASE ORDER</h1>
                <p class="text-sm font-mono text-gray-600 mt-1">{{ $purchaseOrder->po_number }}</p>
                <div class="mt-2">
                    @php
                        $statusClass = 'status-draft';
                        $statusText = ucfirst($purchaseOrder->status);
                        if ($purchaseOrder->status == 'approved') { $statusClass = 'status-approved'; $statusText = 'APPROVED'; }
                        elseif ($purchaseOrder->status == 'sent') { $statusClass = 'status-sent'; $statusText = 'SENT TO VENDOR'; }
                        elseif ($purchaseOrder->status == 'fully_received') { $statusClass = 'status-received'; $statusText = 'FULLY RECEIVED'; }
                        elseif ($purchaseOrder->status == 'partially_received') { $statusClass = 'status-received'; $statusText = 'PARTIALLY RECEIVED'; }
                        elseif ($purchaseOrder->status == 'cancelled') { $statusClass = 'status-cancelled'; $statusText = 'CANCELLED'; }
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                </div>
            </div>
        </div>

        {{-- Vendor & PO Information --}}
        <div class="grid grid-cols-2 gap-8 mb-6">
            <div>
                <div class="info-label mb-2">VENDOR INFORMATION</div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="font-semibold text-gray-800">{{ $purchaseOrder->vendor->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">{{ $purchaseOrder->vendor->contact_person ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $purchaseOrder->vendor->phone ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $purchaseOrder->vendor->email ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $purchaseOrder->vendor->address ?? '' }}</p>
                </div>
            </div>
            <div>
                <div class="info-label mb-2">ORDER DETAILS</div>
                <div class="bg-gray-50 p-3 rounded-lg space-y-1">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">PO Date:</span>
                        <span class="text-sm font-medium">{{ $purchaseOrder->po_date ? \Carbon\Carbon::parse($purchaseOrder->po_date)->format('d M Y') : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Expected Delivery:</span>
                        <span class="text-sm font-medium">{{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('d M Y') : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Payment Terms:</span>
                        <span class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ', $purchaseOrder->payment_method ?? 'Cash')) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">PO Type:</span>
                        <span class="text-sm font-medium">
                            <span class="px-2 py-0.5 rounded text-xs {{ $purchaseOrder->type == 'emergency' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $purchaseOrder->type == 'emergency' ? 'EMERGENCY' : 'NORMAL' }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery Address --}}
        @if($purchaseOrder->delivery_address)
        <div class="mb-6">
            <div class="info-label mb-2">DELIVERY ADDRESS</div>
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-sm text-gray-700">{{ $purchaseOrder->delivery_address }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        <div class="mb-6">
            <div class="info-label mb-2">ORDER ITEMS</div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Item Description</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Unit</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Unit Price (UGX)</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Total (UGX)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php $subtotal = 0; $counter = 1; @endphp
                        @foreach($purchaseOrder->items as $item)
                        @php
                            $total = $item->quantity_ordered * $item->unit_cost;
                            $subtotal += $total;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $counter++ }}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br><span class="text-xs text-gray-400">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                                @if($item->notes)
                                    <br><span class="text-xs text-gray-400">Note: {{ $item->notes }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ $item->inventoryItem->base_unit ?? 'pcs' }}</td>
                            <td class="px-4 py-2 text-sm text-right">{{ number_format($item->quantity_ordered, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right">{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right font-semibold">{{ number_format($total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t">
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-right text-sm font-semibold">Subtotal:</td>
                            <td class="px-4 py-2 text-right text-sm">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @if($purchaseOrder->vat_rate > 0)
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-right text-sm">VAT ({{ $purchaseOrder->vat_rate }}%):</td>
                            <td class="px-4 py-2 text-right text-sm">{{ number_format($purchaseOrder->vat_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="bg-blue-50">
                            <td colspan="5" class="px-4 py-2 text-right text-sm font-bold">TOTAL:</td>
                            <td class="px-4 py-2 text-right text-sm font-bold text-blue-700">UGX {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Terms & Notes --}}
        @if($purchaseOrder->notes || $purchaseOrder->delivery_terms)
        <div class="mb-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    @if($purchaseOrder->notes)
                        <p class="text-sm text-yellow-800"><strong>Notes:</strong> {{ $purchaseOrder->notes }}</p>
                    @endif
                    @if($purchaseOrder->delivery_terms)
                        <p class="text-sm text-yellow-800 mt-1"><strong>Delivery Terms:</strong> {{ $purchaseOrder->delivery_terms }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Signatures Section --}}
        <div class="mt-6 pt-4 border-t">
            <div class="grid grid-cols-2 gap-8">

                {{-- Prepared By --}}
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-2">Prepared By:</p>
                    @php
                        $preparedBy          = $purchaseOrder->creator;
                        $preparedByName      = $preparedBy
                            ? trim(($preparedBy->first_name ?? '') . ' ' . ($preparedBy->last_name ?? ''))
                            : '';
                        $preparedBySignature = resolveSignatureBase64($preparedBy);
                        $preparedBySigUrl    = $preparedBy?->signature_url ?? null;
                    @endphp

                    @if($preparedBySignature)
                        <img src="{{ $preparedBySignature }}" class="signature-img mx-auto" alt="Signature">
                    @elseif($preparedBySigUrl)
                        <img src="{{ $preparedBySigUrl }}" class="signature-img mx-auto" alt="Signature">
                    @else
                        <div class="h-12"></div>
                        <p class="text-xs text-gray-400 mt-2">No signature on file</p>
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                    <p class="text-sm text-gray-600 mt-1">{{ $preparedByName ?: '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $purchaseOrder->created_at ? $purchaseOrder->created_at->format('d M Y') : '' }}</p>
                </div>

                {{-- Approved By (Director) --}}
                {{--
                    The PO is converted from an LPO. The director who approved
                    the LPO is the real approver. We resolve in this priority:
                    1. purchaseOrder->approved_by  (set if PO itself was approved)
                    2. purchaseOrder->lpo->approved_by  (director who approved the source LPO)
                --}}
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-2">Approved By (Director):</p>
                    @php
                        // Priority 1: PO has its own approved_by
                        if ($purchaseOrder->approved_by && $purchaseOrder->approvedBy) {
                            $approver    = $purchaseOrder->approvedBy;
                            $approvedAt  = $purchaseOrder->approved_at;
                        }
                        // Priority 2: Pull approver from the source LPO
                        elseif ($purchaseOrder->lpo && $purchaseOrder->lpo->approved_by && $purchaseOrder->lpo->approvedBy) {
                            $approver    = $purchaseOrder->lpo->approvedBy;
                            $approvedAt  = $purchaseOrder->lpo->approved_at;
                        }
                        else {
                            $approver    = null;
                            $approvedAt  = null;
                        }

                        $approverName  = $approver
                            ? trim(($approver->first_name ?? '') . ' ' . ($approver->last_name ?? ''))
                            : '';
                        $approverSig   = resolveSignatureBase64($approver);
                        $approverUrl   = $approver?->signature_url ?? null;
                    @endphp

                    @if($purchaseOrder->status === 'cancelled')
                        <div class="h-12"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-red-600 mt-1">CANCELLED</p>
                    @elseif($approver)
                        @if($approverSig)
                            <img src="{{ $approverSig }}" class="signature-img mx-auto" alt="Signature">
                        @elseif($approverUrl)
                            <img src="{{ $approverUrl }}" class="signature-img mx-auto" alt="Signature">
                        @else
                            <div class="h-12"></div>
                            <p class="text-xs text-gray-400 mt-2">No signature on file</p>
                        @endif
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-gray-600 mt-1">{{ $approverName }}</p>
                        <p class="text-xs text-gray-400">{{ $approvedAt ? \Carbon\Carbon::parse($approvedAt)->format('d M Y') : '' }}</p>
                    @else
                        <div class="h-12"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-gray-400 mt-1">Not Approved</p>
                    @endif
                </div>

            </div>
        </div>
        {{-- ═══════════════════════════════════════════════════ --}}

        {{-- Footer --}}
        <div class="footer-note">
            <p>Computer generated document. Valid without signature.</p>
            <p class="mt-1">{{ $companyName }}{{ $companyPhone ? ' — Tel: ' . $companyPhone : '' }}</p>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    // Toggle Share Menu
    function toggleShareMenu() {
        const menu = document.getElementById('shareMenu');
        menu.classList.toggle('hidden');
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('shareMenu');
        const button = event.target.closest('button');
        if (menu && !menu.contains(event.target) && (!button || !button.innerHTML.includes('Share'))) {
            menu.classList.add('hidden');
        }
    });

    // Generate PDF Blob for sharing
    async function generatePDFBlob() {
        const element = document.getElementById('print-section');
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: 'PO-{{ $purchaseOrder->po_number }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        const worker = html2pdf().set(opt).from(element);
        const pdfBlob = await worker.outputPdf('blob');
        return pdfBlob;
    }

    // Share via Email (Gmail)
    async function shareViaEmail() {
        const pdfBlob = await generatePDFBlob();
        const pdfUrl = URL.createObjectURL(pdfBlob);
        const subject = encodeURIComponent('Purchase Order {{ $purchaseOrder->po_number }}');
        const body = encodeURIComponent(`Dear Sir/Madam,\n\nPlease find attached Purchase Order {{ $purchaseOrder->po_number }}.\n\nBest regards,\n{{ $companyName }}`);
        window.open(`https://mail.google.com/mail/?view=cm&fs=1&su=${subject}&body=${body}`, '_blank');
        setTimeout(() => {
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = 'PO-{{ $purchaseOrder->po_number }}.pdf';
            link.click();
            URL.revokeObjectURL(pdfUrl);
        }, 500);
        alert('PDF downloaded. Please attach it to your email.');
    }

    // Share via WhatsApp
    async function shareViaWhatsApp() {
        const pdfBlob = await generatePDFBlob();
        const pdfUrl = URL.createObjectURL(pdfBlob);
        const text = encodeURIComponent(`Purchase Order {{ $purchaseOrder->po_number }}\nTotal: UGX {{ number_format($purchaseOrder->total_amount, 0) }}\nVendor: {{ $purchaseOrder->vendor->name ?? 'N/A' }}\n\nPDF attached below.`);
        window.open(`https://wa.me/?text=${text}`, '_blank');
        setTimeout(() => {
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = 'PO-{{ $purchaseOrder->po_number }}.pdf';
            link.click();
            URL.revokeObjectURL(pdfUrl);
        }, 500);
        alert('PDF downloaded. Please attach it to your WhatsApp message.');
    }

    // Download PDF
    function downloadPDF() {
        const element = document.getElementById('print-section');
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: 'PO-{{ $purchaseOrder->po_number }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    // Print Function
    function printPO() {
        const printContents = document.getElementById('print-section').innerHTML;
        const originalTitle = document.title;
        document.title = 'PO {{ $purchaseOrder->po_number }}';
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>PO {{ $purchaseOrder->po_number }}</title>
                <style>
                    body { padding: 20px; font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .company-logo { max-height: 50px; width: auto; }
                    .signature-img { max-height: 60px; max-width: 180px; }
                    .status-badge { padding: 2px 8px; border-radius: 20px; font-size: 10px; }
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
</script>

@endsection
