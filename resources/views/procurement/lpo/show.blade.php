@extends('layouts.procurement')

@section('title', 'LPO Details')

@section('page-title', 'Local Purchase Order Details')

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
        }
        .no-print {
            display: none !important;
        }
        button, .btn, .action-buttons {
            display: none !important;
        }
        .company-logo, .print-logo {
            max-height: 50px !important;
            width: auto !important;
        }
        .signature-img {
            max-height: 60px !important;
            max-width: 180px !important;
        }
        body, p, span, td, th, div, .text-sm, .text-xs {
            font-size: 12px !important;
        }
        h1, h2, h3, h4, .text-lg, .text-xl, .text-md {
            font-size: 14px !important;
        }
        table {
            font-size: 11px !important;
        }
        th, td {
            padding: 6px 8px !important;
        }
    }
    .company-logo {
        max-height: 60px;
        width: auto;
    }
    .signature-img {
        max-height: 60px;
        max-width: 180px;
    }
    .type-badge, .status-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
    }
    .type-normal {
        background: #d1fae5;
        color: #065f46;
    }
    .type-emergency {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-pending {
        background: #fef3c7;
        color: #d97706;
    }
    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }
    .status-rejected {
        background: #fee2e2;
        color: #dc2626;
    }
    .status-converted {
        background: #dbeafe;
        color: #1d4ed8;
    }
    body, p, span, td, th, div, label, .text-sm {
        font-size: 13px;
    }
    h1, h2, h3, h4, .text-lg, .text-xl, .text-md {
        font-size: 15px;
    }
    table {
        font-size: 12px;
    }
    th, td {
        padding: 8px 10px;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header with Action Buttons --}}
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center no-print">
        <div>
            <h3 class="text-md font-semibold text-gray-800">LPO #{{ $lpo->lpo_number }}</h3>
            <p class="text-xs text-gray-500">Created on {{ $lpo->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('procurement.lpo.index') }}" class="text-gray-600 hover:text-gray-800 text-xs">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <button onclick="printLPO()" class="ml-2 bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button onclick="downloadPDF()" class="ml-2 bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </button>
        </div>
    </div>

    {{-- Printable Section --}}
    <div id="print-section" class="p-6">

        {{-- Logo and Header --}}
        <div class="flex justify-between items-start mb-6 pb-3 border-b">
            <div>
                @php
                    $logo = \App\Models\BusinessSetting::getLogo();
                    $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
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
                    <img src="{{ $logoBase64 }}" class="company-logo print-logo" alt="Logo">
                @elseif($logo)
                    <img src="{{ $logo }}" class="company-logo print-logo" alt="Logo">
                @else
                    <h2 class="text-xl font-bold text-gray-800">{{ $companyName }}</h2>
                @endif
            </div>
            <div class="text-right">
                <h1 class="text-xl font-bold text-blue-600">LOCAL PURCHASE ORDER</h1>
                <p class="text-sm text-gray-500">{{ $lpo->lpo_number }}</p>
            </div>
        </div>

        {{-- Status and Type Badges --}}
        <div class="mb-6 flex gap-2">
            @php
                if ($lpo->status == 'pending_director') {
                    $statusClass = 'status-pending';
                    $statusText = 'Pending Director Approval';
                } elseif ($lpo->status == 'director_approved') {
                    $statusClass = 'status-approved';
                    $statusText = 'Approved by Director';
                } elseif ($lpo->status == 'converted_to_epo') {
                    $statusClass = 'status-converted';
                    $statusText = 'Converted to External PO';
                } elseif ($lpo->status == 'director_rejected') {
                    $statusClass = 'status-rejected';
                    $statusText = 'Rejected by Director';
                } else {
                    $statusClass = 'status-pending';
                    $statusText = ucfirst(str_replace('_', ' ', $lpo->status));
                }
            @endphp
            <span class="status-badge {{ $statusClass }}">
                {{ $statusText }}
            </span>
            <span class="type-badge {{ $lpo->type == 'emergency' ? 'type-emergency' : 'type-normal' }}">
                {{ $lpo->type == 'emergency' ? 'EMERGENCY' : 'Normal' }}
            </span>
        </div>

        {{-- Rejection Reason --}}
        @if($lpo->status == 'director_rejected' && $lpo->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $lpo->rejection_reason }}</p>
                    @if($lpo->approvedBy)
                        <p class="text-xs text-red-600 mt-1">Rejected by: {{ $lpo->approvedBy->first_name }} {{ $lpo->approvedBy->last_name }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- LPO Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">LPO Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">LPO Number:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $lpo->lpo_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">LPO Type:</span>
                        <span class="text-sm">{{ $lpo->type == 'emergency' ? 'EMERGENCY' : 'Normal' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Requisition #:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->requisition->requisition_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">LPO Date:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->lpo_date ? $lpo->lpo_date->format('d M Y') : '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Expected Delivery:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->expected_delivery_date ? \Carbon\Carbon::parse($lpo->expected_delivery_date)->format('d M Y') : '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Payment Method:</span>
                        <span class="text-sm text-gray-800">
                            @if($lpo->payment_method == 'cash') Cash
                            @elseif($lpo->payment_method == 'credit') Credit
                            @elseif($lpo->payment_method == 'bank_transfer') Bank Transfer
                            @elseif($lpo->payment_method == 'mobile_money') Mobile Money
                            @elseif($lpo->payment_method == 'cheque') Cheque
                            @else {{ ucfirst(str_replace('_', ' ', $lpo->payment_method)) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Vendor Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Vendor Name:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Contact Person:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->contact_person ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Phone:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Email:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->email ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery Info --}}
        <div class="grid grid-cols-1 gap-2 mb-6">
            <div class="flex">
                <span class="w-36 text-sm text-gray-500">Delivery Address:</span>
                <span class="text-sm text-gray-800">{{ $lpo->delivery_address ?: '—' }}</span>
            </div>
            <div class="flex">
                <span class="w-36 text-sm text-gray-500">Delivery Instructions:</span>
                <span class="text-sm text-gray-800">{{ $lpo->delivery_instructions ?: '—' }}</span>
            </div>
        </div>

        {{-- Notes --}}
        @if($lpo->director_notes)
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-sm font-semibold text-yellow-800">Director Notes:</p>
            <p class="text-sm text-yellow-700">{{ $lpo->director_notes }}</p>
        </div>
        @endif

        @if($lpo->notes)
        <div class="mb-4">
            <p class="text-sm font-medium text-gray-500">Internal Notes:</p>
            <div class="bg-gray-50 rounded p-3">
                <p class="text-sm text-gray-700">{{ $lpo->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-3">LPO Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Item</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Category</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Metrics</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Qty</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Unit Cost</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php $subtotal = 0; @endphp
                        @foreach($lpo->items as $item)
                        @php $total = $item->quantity_approved * $item->unit_cost; $subtotal += $total; @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br><span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $item->inventoryItem?->category?->name ?: '—' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $item->metrics ?: '—' }}</td>
                            <td class="px-3 py-2 text-sm text-right">{{ number_format($item->quantity_approved, 2) }}</td>
                            <td class="px-3 py-2 text-sm text-right">UGX {{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-3 py-2 text-sm text-right font-semibold">UGX {{ number_format($total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr><td colspan="5" class="px-3 py-2 text-right text-sm font-semibold">Subtotal:</td><td class="px-3 py-2 text-right text-sm font-semibold">UGX {{ number_format($subtotal, 2) }}</td></tr>
                        @if($lpo->vat_rate > 0)
                        <tr><td colspan="5" class="px-3 py-2 text-right text-sm">VAT ({{ $lpo->vat_rate }}%):</td><td class="px-3 py-2 text-right text-sm">UGX {{ number_format($lpo->vat_amount, 2) }}</td></tr>
                        @endif
                        <tr class="bg-green-50"><td colspan="5" class="px-3 py-2 text-right text-sm font-bold">TOTAL:</td><td class="px-3 py-2 text-right text-sm font-bold text-green-600">UGX {{ number_format($lpo->total_amount, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-blue-50 rounded p-3 text-center">
                <p class="text-xs text-blue-600">Total Items</p>
                <p class="text-xl font-bold text-blue-800">{{ $lpo->items->count() }}</p>
            </div>
            <div class="bg-yellow-50 rounded p-3 text-center">
                <p class="text-xs text-yellow-600">VAT Rate</p>
                <p class="text-xl font-bold text-yellow-800">{{ $lpo->vat_rate }}%</p>
            </div>
            <div class="bg-green-50 rounded p-3 text-center">
                <p class="text-xs text-green-600">Total Amount</p>
                <p class="text-xl font-bold text-green-800">UGX {{ number_format($lpo->total_amount, 0) }}</p>
            </div>
        </div>

        {{-- Signatures Section --}}
        <div class="mt-6 pt-4 border-t">
            <div class="grid grid-cols-2 gap-8">
                {{-- Prepared By Signature --}}
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-2">Prepared By:</p>
                    @php
                        $preparedBy = $lpo->createdBy;
                        $preparedBySignature = null;
                        $preparedByName = '';

                        if ($preparedBy) {
                            $preparedByName = trim(($preparedBy->first_name ?? '') . ' ' . ($preparedBy->last_name ?? ''));
                            if ($preparedBy->signature_path) {
                                $sigPath = storage_path('app/public/' . $preparedBy->signature_path);
                                if (file_exists($sigPath)) {
                                    $sigMime = mime_content_type($sigPath);
                                    $sigData = base64_encode(file_get_contents($sigPath));
                                    $preparedBySignature = 'data:' . $sigMime . ';base64,' . $sigData;
                                }
                            }
                        }
                    @endphp
                    @if($preparedBySignature)
                        <img src="{{ $preparedBySignature }}" class="signature-img mx-auto" alt="Signature">
                    @else
                        <div class="h-12"></div>
                        <p class="text-xs text-gray-400 mt-2">No signature on file</p>
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                    <p class="text-sm text-gray-600 mt-1">{{ $preparedByName ?: '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $lpo->created_at ? $lpo->created_at->format('d M Y') : '' }}</p>
                </div>

                {{-- Approved By Signature - FIXED: Show if approval happened (approved_by and approved_at exist) --}}
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-2">Approved By (Director):</p>
                    @php
                        // CRITICAL FIX: Check if approval actually happened (approved_by AND approved_at exist)
                        // NOT based on status! Because when converted_to_epo, status changes but approval already happened
                        $wasApproved = $lpo->approved_by && $lpo->approved_at;
                        $approver = $wasApproved ? $lpo->approvedBy : null;
                        $approverSignature = null;
                        $approverName = '';

                        if ($wasApproved && $approver) {
                            $approverName = trim(($approver->first_name ?? '') . ' ' . ($approver->last_name ?? ''));
                            if ($approver->signature_path) {
                                $sigPath = storage_path('app/public/' . $approver->signature_path);
                                if (file_exists($sigPath)) {
                                    $sigMime = mime_content_type($sigPath);
                                    $sigData = base64_encode(file_get_contents($sigPath));
                                    $approverSignature = 'data:' . $sigMime . ';base64,' . $sigData;
                                }
                            }
                        }
                    @endphp

                    {{-- Show signature if approval occurred (regardless of current status) --}}
                    @if($wasApproved)
                        @if($approverSignature)
                            <img src="{{ $approverSignature }}" class="signature-img mx-auto" alt="Signature">
                        @else
                            <div class="h-12"></div>
                            <p class="text-xs text-gray-400 mt-2">No signature on file</p>
                        @endif
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-gray-600 mt-1">{{ $approverName ?: 'Unknown Approver' }}</p>
                        <p class="text-xs text-gray-400">{{ $lpo->approved_at ? \Carbon\Carbon::parse($lpo->approved_at)->format('d M Y') : '' }}</p>
                    @elseif($lpo->status == 'pending_director')
                        <div class="h-12"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-gray-400 mt-1">Pending Approval</p>
                    @elseif($lpo->status == 'director_rejected')
                        <div class="h-12"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-red-600 mt-1">REJECTED</p>
                        @if($lpo->approvedBy)
                            <p class="text-xs text-gray-500">By: {{ $lpo->approvedBy->first_name }} {{ $lpo->approvedBy->last_name }}</p>
                        @endif
                    @else
                        <div class="h-12"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                        <p class="text-sm text-gray-400 mt-1">Not Yet Approved</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-6 pt-3 border-t text-center">
            <p class="text-xs text-gray-400">Computer generated document. Valid without signature.</p>
            <p class="text-xs text-gray-400">{{ $companyName }} - All Rights Reserved</p>
        </div>
    </div>
</div>

<script>
    function printLPO() {
        const printContents = document.getElementById('print-section').innerHTML;
        const originalTitle = document.title;
        document.title = 'LPO {{ $lpo->lpo_number }}';

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>LPO {{ $lpo->lpo_number }}</title>
                <style>
                    body { padding: 20px; font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 6px 8px; font-size: 11px; }
                    th { background-color: #f2f2f2; }
                    .company-logo, .print-logo { max-height: 50px !important; width: auto !important; }
                    .signature-img { max-height: 60px !important; max-width: 180px !important; }
                    .type-badge, .status-badge { padding: 3px 8px; font-size: 10px; border-radius: 999px; }
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
            filename: 'LPO-{{ $lpo->lpo_number }}.pdf',
            image: { type: 'jpeg', quality: 0.95 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

@endsection
