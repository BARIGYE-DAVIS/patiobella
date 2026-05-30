@extends('layouts.director')
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
        body, p, span, td, th, div {
            font-size: 12px !important;
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
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header with Action Buttons --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center no-print">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">LPO #{{ $lpo->lpo_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $lpo->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('director.lpos.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            <button onclick="printLPO()" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button onclick="downloadPDF()" class="ml-2 bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                <i class="fas fa-file-pdf mr-1"></i> Download PDF
            </button>
        </div>
    </div>

    {{-- Printable Section --}}
    <div id="print-section" class="p-6">

        {{-- Logo and Header --}}
        <div class="flex justify-between items-start mb-6 pb-4 border-b">
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
                    $statusText = 'Pending Your Approval';
                } elseif ($lpo->status == 'director_approved') {
                    $statusClass = 'status-approved';
                    $statusText = 'Approved - Ready for External PO';
                } elseif ($lpo->status == 'director_rejected') {
                    $statusClass = 'status-rejected';
                    $statusText = 'Rejected';
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
                </div>
            </div>
        </div>
        @endif

        {{-- Director Notes --}}
        @if($lpo->director_notes)
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-yellow-800">Director Notes (for Procurement)</h4>
                    <p class="text-sm text-yellow-700 mt-1">{{ $lpo->director_notes }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- LPO Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
                        <span class="text-sm text-gray-800">{{ $lpo->lpo_date->format('F d, Y') }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Expected Delivery:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->expected_delivery_date ? \Carbon\Carbon::parse($lpo->expected_delivery_date)->format('F d, Y') : 'Not specified' }}</span>
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
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">VAT Rate:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vat_rate }}%</span>
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
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Delivery Address:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->delivery_address ?: 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Delivery Instructions:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->delivery_instructions ?: 'Not specified' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($lpo->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Procurement Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $lpo->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">LPO Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-5">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Metrics</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Unit Cost</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php $totalQuantity = 0; $subtotal = 0; @endphp
                        @foreach($lpo->items as $index => $item)
                        @php
                            $totalQuantity += $item->quantity_approved;
                            $total = $item->quantity_approved * $item->unit_cost;
                            $subtotal += $total;
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br><span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->inventoryItem?->category?->name ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->metrics ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold">{{ number_format($item->quantity_approved, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right">UGX {{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">UGX {{ number_format($total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-sm font-bold text-gray-700">TOTALS</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ number_format($totalQuantity, 2) }}</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-sm font-bold text-green-700 text-right">UGX {{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @if($lpo->vat_rate > 0)
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-right text-sm">VAT ({{ $lpo->vat_rate }}%):</td>
                            <td class="px-4 py-2 text-right text-sm">UGX {{ number_format($lpo->vat_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-right text-sm font-bold">GRAND TOTAL:</td>
                            <td class="px-4 py-2 text-right text-sm font-bold text-green-700">UGX {{ number_format($lpo->total_amount, 2) }}</td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-right text-sm font-bold">TOTAL:</td>
                            <td class="px-4 py-2 text-right text-sm font-bold text-green-700">UGX {{ number_format($lpo->total_amount, 2) }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-sm text-blue-600">Total Items</p>
                <p class="text-2xl font-bold text-blue-800">{{ $lpo->items->count() }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                <p class="text-sm text-yellow-600">VAT Rate</p>
                <p class="text-2xl font-bold text-yellow-800">{{ $lpo->vat_rate }}%</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600">Total Amount</p>
                <p class="text-2xl font-bold text-green-800">UGX {{ number_format($lpo->total_amount, 0) }}</p>
            </div>
        </div>

        {{-- Signatures Section --}}
        <div class="mt-8 pt-4 border-t">
            <div class="grid grid-cols-2 gap-8">
                {{-- Prepared By Signature (Procurement) --}}
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
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                    <p class="text-sm text-gray-600 mt-1">{{ $preparedByName ?: '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $lpo->created_at ? $lpo->created_at->format('d M Y') : '' }}</p>
                </div>

                {{-- Approved By Signature (Director) --}}
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-2">Approved By (Director):</p>
                    @php
                        $approver = $lpo->approvedBy;
                        $approverSignature = null;
                        $approverName = '';

                        if ($lpo->status == 'director_approved' && $approver) {
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
                    @if($approverSignature)
                        <img src="{{ $approverSignature }}" class="signature-img mx-auto" alt="Signature">
                    @else
                        <div class="h-12"></div>
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                    @if($lpo->status == 'director_approved' && $approver)
                        <p class="text-sm text-gray-600 mt-1">{{ $approverName }}</p>
                        <p class="text-xs text-gray-400">{{ $lpo->approved_at ? \Carbon\Carbon::parse($lpo->approved_at)->format('d M Y') : '' }}</p>
                    @else
                        <p class="text-sm text-gray-400 mt-1">Not Yet Approved</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t text-center">
            <p class="text-xs text-gray-400">Computer generated document. Valid without signature.</p>
            <p class="text-xs text-gray-400">{{ $companyName }} - All Rights Reserved</p>
        </div>
    </div>

    {{-- Action Buttons (only for pending LPOs) --}}
    @if($lpo->status == 'pending_director')
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-4 no-print">
        <button type="button" onclick="openRejectModal()"
                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Reject LPO
        </button>
        <button type="button" onclick="openApproveModal()"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Approve LPO & Send to Procurement
        </button>
    </div>
    @endif

    {{-- Show approval info if approved --}}
    @if($lpo->status == 'director_approved')
    <div class="px-6 py-4 border-t border-gray-200 bg-green-50 no-print">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-green-800">LPO Approved by Director</p>
                <p class="text-xs text-green-600">Procurement can now create External Purchase Order and send to vendor.</p>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Approve Modal with Notes --}}
<div id="approveModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden no-print">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
        <div class="bg-green-600 px-6 py-4 rounded-t-lg">
            <h3 class="text-lg font-semibold text-white">Approve LPO</h3>
        </div>
        <form action="{{ route('director.lpos.approve', $lpo->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-semibold mb-2 text-gray-700">Director Notes (Optional)</label>
                <textarea name="director_notes" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Add any notes or instructions for Procurement department..."></textarea>
                <p class="text-xs text-gray-500 mt-2">These notes will be visible to Procurement when they convert this LPO to an External Purchase Order.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Rejection Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden no-print">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="bg-red-600 px-6 py-4 rounded-t-lg">
            <h3 class="text-lg font-semibold text-white">Reject LPO</h3>
        </div>
        <form action="{{ route('director.lpos.reject', $lpo->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-semibold mb-2 text-gray-700">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Please provide a reason for rejecting this LPO..." required></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApproveModal() {
        document.getElementById('approveModal').classList.remove('hidden');
    }
    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

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
