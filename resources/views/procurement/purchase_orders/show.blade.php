@extends('layouts.procurement')

@section('title', 'Purchase Order Details')
@section('page-title', 'Purchase Order Details')

@section('content')
<style>
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
        .flex-signatures {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            gap: 20px !important;
        }
        .sig-block {
            flex: 1 !important;
            text-align: center !important;
        }
    }
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
    .stamp-img {
        max-height: 70px;
        max-width: 120px;
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
    .flex-signatures {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        gap: 20px;
    }
    .sig-block {
        flex: 1;
        text-align: center;
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
    function resolveSignatureBase64($user): ?string {
        if (!$user || !$user->signature_path) return null;
        $clean = ltrim($user->signature_path, '/');
        $clean = preg_replace('#^public/#', '', $clean);
        $sigPath = storage_path('app/public/' . $clean);
        if (file_exists($sigPath)) {
            $mime = mime_content_type($sigPath);
            $data = base64_encode(file_get_contents($sigPath));
            return 'data:' . $mime . ';base64,' . $data;
        }
        // Try as asset URL
        $assetPath = asset($user->signature_path);
        return $assetPath;
    }

    // Get LPO and director approver
    $lpo = $purchaseOrder->lpo ?? null;
    $directorApprover = null;
    $directorApprovedAt = null;

    if ($lpo && $lpo->approved_by && $lpo->approved_at && $lpo->approvedBy) {
        $directorApprover = $lpo->approvedBy;
        $directorApprovedAt = $lpo->approved_at;
    }

    $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
    $companyAddress = \App\Models\BusinessSetting::get('address', '');
    $companyCity = \App\Models\BusinessSetting::get('city', '');
    $companyCountry = \App\Models\BusinessSetting::get('country', '');
    $companyPhone = \App\Models\BusinessSetting::get('phone', '');
    $companyEmail = \App\Models\BusinessSetting::get('email', '');
    $companyStamp = \App\Models\BusinessSetting::getStamp();

    $logoBase64 = null;
    $logo = \App\Models\BusinessSetting::getLogo();
    if ($logo) {
        $logoPath = public_path(parse_url($logo, PHP_URL_PATH));
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:' . mime_content_type($logoPath) . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }

    $stampBase64 = null;
    if ($companyStamp) {
        $stampPath = public_path(parse_url($companyStamp, PHP_URL_PATH));
        if (file_exists($stampPath)) {
            $stampBase64 = 'data:' . mime_content_type($stampPath) . ';base64,' . base64_encode(file_get_contents($stampPath));
        }
    }
@endphp

<div class="po-container">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center no-print rounded-t-xl">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">PO #{{ $purchaseOrder->po_number }}</h3>
            <p class="text-xs text-gray-500">Created on {{ $purchaseOrder->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('procurement.purchase-orders.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-1 text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
            <div class="relative inline-block text-left ml-2">
                <button type="button" onclick="toggleShareMenu()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 share-btn flex items-center gap-2">
                    <i class="fas fa-share-alt mr-1"></i> Share
                </button>
                <div id="shareMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="py-1">
                        <button onclick="shareViaEmail()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="fas fa-envelope text-red-500 w-4"></i> Email via Gmail
                        </button>
                        <button onclick="shareViaWhatsApp()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="fab fa-whatsapp text-green-500 w-4"></i> WhatsApp
                        </button>
                        <button onclick="downloadPDF()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="fas fa-download text-red-600 w-4"></i> Download PDF
                        </button>
                        <button onclick="printPO()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="fas fa-print text-blue-500 w-4"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="print-section" class="p-8">
        <div class="document-header flex justify-between items-start pb-4 mb-6">
            <div>
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

        @if($purchaseOrder->delivery_address)
        <div class="mb-6">
            <div class="info-label mb-2">DELIVERY ADDRESS</div>
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-sm text-gray-700">{{ $purchaseOrder->delivery_address }}</p>
            </div>
        </div>
        @endif

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
                        @php $total = $item->quantity_ordered * $item->unit_cost; $subtotal += $total; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $counter++ }}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)<br><span class="text-xs text-gray-400">Code: {{ $item->inventoryItem->item_code }}</span>@endif
                                @if($item->notes)<br><span class="text-xs text-gray-400">Note: {{ $item->notes }}</span>@endif
                             </td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ $item->inventoryItem->base_unit ?? 'pcs' }}</td>
                            <td class="px-4 py-2 text-sm text-right">{{ number_format($item->quantity_ordered, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right">{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right font-semibold">{{ number_format($total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t">
                        <tr><td colspan="5" class="px-4 py-2 text-right text-sm font-semibold">Subtotal:</td><td class="px-4 py-2 text-right text-sm">{{ number_format($subtotal, 2) }}</td></tr>
                        @if($purchaseOrder->vat_rate > 0)
                        <tr><td colspan="5" class="px-4 py-2 text-right text-sm">VAT ({{ $purchaseOrder->vat_rate }}%):</td><td class="px-4 py-2 text-right text-sm">{{ number_format($purchaseOrder->vat_amount, 2) }}</td></tr>
                        @endif
                        <tr class="bg-blue-50"><td colspan="5" class="px-4 py-2 text-right text-sm font-bold">TOTAL:</td><td class="px-4 py-2 text-right text-sm font-bold text-blue-700">UGX {{ number_format($purchaseOrder->total_amount, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($purchaseOrder->notes || $purchaseOrder->delivery_terms)
        <div class="mb-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-yellow-600 text-sm mt-0.5"></i>
                <div>
                    @if($purchaseOrder->notes)<p class="text-sm text-yellow-800"><strong>Notes:</strong> {{ $purchaseOrder->notes }}</p>@endif
                    @if($purchaseOrder->delivery_terms)<p class="text-sm text-yellow-800 mt-1"><strong>Delivery Terms:</strong> {{ $purchaseOrder->delivery_terms }}</p>@endif
                </div>
            </div>
        </div>
        @endif

        {{-- SIGNATURES SECTION - SAME ROW --}}
        <div class="mt-6 pt-4 border-t">
            <div class="flex-signatures">
                {{-- Prepared By Signature --}}
                <div class="sig-block">
                    <p class="text-sm text-gray-500 mb-2">Prepared By:</p>
                    @php $preparedBy = $purchaseOrder->creator; @endphp
                    @if($preparedBy && ($sig = resolveSignatureBase64($preparedBy)))
                        @if(str_starts_with($sig, 'data:'))
                            <img src="{{ $sig }}" class="signature-img mx-auto" alt="Signature">
                        @else
                            <img src="{{ $sig }}" class="signature-img mx-auto" alt="Signature">
                        @endif
                    @else
                        <div class="h-12"></div>
                        <p class="text-xs text-gray-400 mt-2">No signature on file</p>
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                    <p class="text-sm text-gray-600 mt-1">{{ $preparedBy->first_name ?? '' }} {{ $preparedBy->last_name ?? '' }}</p>
                    <p class="text-xs text-gray-400">{{ $purchaseOrder->created_at ? $purchaseOrder->created_at->format('d M Y') : '' }}</p>
                </div>

                {{-- Company Stamp --}}
                <div class="sig-block">
                    <p class="text-sm text-gray-500 mb-2">Company Stamp:</p>
                    @if($stampBase64)
                        <img src="{{ $stampBase64 }}" class="stamp-img mx-auto" alt="Stamp">
                    @elseif($companyStamp)
                        <img src="{{ $companyStamp }}" class="stamp-img mx-auto" alt="Stamp">
                    @else
                        <div class="h-12"></div>
                        <p class="text-xs text-gray-400 mt-2">No stamp</p>
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                    <p class="text-sm text-gray-400 mt-1">Authorized Signature</p>
                </div>

                {{-- Approved By (Director) - From LPO Approval --}}
                <div class="sig-block">
                    <p class="text-sm text-gray-500 mb-2">Approved By (Director):</p>
                    @if($directorApprover && ($sig = resolveSignatureBase64($directorApprover)))
                        @if(str_starts_with($sig, 'data:'))
                            <img src="{{ $sig }}" class="signature-img mx-auto" alt="Signature">
                        @else
                            <img src="{{ $sig }}" class="signature-img mx-auto" alt="Signature">
                        @endif
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-gray-600 mt-1">{{ $directorApprover->first_name ?? '' }} {{ $directorApprover->last_name ?? '' }}</p>
                        <p class="text-xs text-gray-400">{{ $directorApprovedAt ? \Carbon\Carbon::parse($directorApprovedAt)->format('d M Y') : '' }}</p>
                    @else
                        <div class="h-12"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-gray-400 mt-1">
                            @if($lpo && $lpo->status == 'pending_director')
                                Pending Director Approval
                            @elseif($lpo && $lpo->status == 'director_rejected')
                                REJECTED
                            @else
                                No approval record found
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="footer-note">
            <p>Computer generated document. Valid Digital signature.</p>
            <p class="mt-1">{{ $companyName }}{{ $companyPhone ? ' — Tel: ' . $companyPhone : '' }}</p>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
    let pdfBlobCache = null;

    function toggleShareMenu() {
        const menu = document.getElementById('shareMenu');
        if (menu) menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('shareMenu');
        if (menu && !menu.contains(e.target) && !e.target.closest('button')?.innerHTML?.includes('Share')) {
            menu.classList.add('hidden');
        }
    });

    async function generatePDFBlob() {
        if (pdfBlobCache) return pdfBlobCache;
        const element = document.getElementById('print-section');
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        const worker = html2pdf().set(opt).from(element);
        const blob = await worker.outputPdf('blob');
        pdfBlobCache = blob;
        return blob;
    }

    async function downloadPDF() {
        const blob = await generatePDFBlob();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'PO-{{ $purchaseOrder->po_number }}.pdf';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(() => URL.revokeObjectURL(url), 100);
    }

    async function shareViaEmail() {
        const blob = await generatePDFBlob();
        const url = URL.createObjectURL(blob);
        const subject = encodeURIComponent('Purchase Order {{ $purchaseOrder->po_number }}');
        const body = encodeURIComponent(`Dear Sir/Madam,\n\nPlease find attached Purchase Order {{ $purchaseOrder->po_number }}.\n\nBest regards,\n{{ $companyName }}`);
        window.open(`https://mail.google.com/mail/?view=cm&fs=1&su=${subject}&body=${body}`, '_blank');
        setTimeout(() => {
            const a = document.createElement('a');
            a.href = url;
            a.download = 'PO-{{ $purchaseOrder->po_number }}.pdf';
            a.click();
            setTimeout(() => URL.revokeObjectURL(url), 500);
        }, 500);
    }

    async function shareViaWhatsApp() {
        const blob = await generatePDFBlob();
        const url = URL.createObjectURL(blob);
        const text = encodeURIComponent(`Purchase Order {{ $purchaseOrder->po_number }}\nTotal: UGX {{ number_format($purchaseOrder->total_amount, 0) }}\nVendor: {{ $purchaseOrder->vendor->name ?? 'N/A' }}`);
        window.open(`https://wa.me/?text=${text}`, '_blank');
        setTimeout(() => {
            const a = document.createElement('a');
            a.href = url;
            a.download = 'PO-{{ $purchaseOrder->po_number }}.pdf';
            a.click();
            setTimeout(() => URL.revokeObjectURL(url), 500);
        }, 500);
    }

    function printPO() {
        const printContents = document.getElementById('print-section').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`
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
                    .flex-signatures { display: flex; flex-direction: row; justify-content: space-between; gap: 20px; }
                    .sig-block { flex: 1; text-align: center; }
                    @media print { body { margin: 0; padding: 20px; } }
                </style>
            </head>
            <body>${printContents}</body>
            </html>
        `);
        win.document.close();
        win.print();
    }
</script>
@endsection
