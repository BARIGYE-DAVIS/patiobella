@extends('layouts.director')

@section('title', 'LPO Details')
@section('page-title', 'Local Purchase Order Details')

@section('content')
<style>
    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px;
        }
        .no-print { display: none !important; }
        button, .btn, .action-buttons, .edit-mode { display: none !important; }
        .company-logo, .print-logo { max-height: 50px !important; width: auto !important; }
        .signature-img { max-height: 60px !important; max-width: 180px !important; }
        .stamp-img { max-height: 60px !important; max-width: 120px !important; }
        body, p, span, td, th, div, .text-sm, .text-xs { font-size: 12px !important; }
        h1, h2, h3, h4, .text-lg, .text-xl, .text-md { font-size: 14px !important; }
        table { font-size: 11px !important; }
        th, td { padding: 6px 8px !important; }
        .flex-signatures { display: flex !important; flex-direction: row !important; justify-content: space-between !important; gap: 20px !important; }
        .sig-block { flex: 1 !important; text-align: center !important; }
    }
    .company-logo { max-height: 60px; width: auto; }
    .signature-img { max-height: 60px; max-width: 180px; }
    .stamp-img { max-height: 60px; max-width: 120px; }
    .type-badge, .status-badge { display: inline-flex; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
    .type-normal { background: #d1fae5; color: #065f46; }
    .type-emergency { background: #fee2e2; color: #991b1b; }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #dc2626; }
    .status-converted { background: #dbeafe; color: #1d4ed8; }
    .batch-info { font-size: 10px; padding: 2px 4px; border-radius: 4px; display: inline-block; }
    .batch-low { background: #fee2e2; color: #dc2626; }
    .batch-ok { background: #dcfce7; color: #16a34a; }
    .batch-warning { background: #fef3c7; color: #d97706; }
    .batch-expired { background: #fee2e2; color: #dc2626; }
    .batch-expiring-soon { background: #fef3c7; color: #d97706; }
    .flex-signatures { display: flex; flex-direction: row; justify-content: space-between; gap: 20px; }
    .sig-block { flex: 1; text-align: center; }
    .quantity-input {
        width: 100px;
        padding: 6px 8px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        text-align: right;
        font-size: 13px;
        transition: all 0.2s;
    }
    .quantity-input:focus {
        outline: none;
        border-color: #3b82f6;
        ring: 2px solid #3b82f6;
    }
    .quantity-input.valid {
        border-color: #10b981;
        background-color: #f0fdf4;
    }
    .quantity-input.changed {
        border-color: #f59e0b;
        background-color: #fffbeb;
    }
    .original-qty {
        font-size: 10px;
        color: #6b7280;
        display: block;
        margin-top: 2px;
    }
    .edit-badge {
        background: #fef3c7;
        color: #d97706;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
        margin-left: 8px;
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
            <a href="{{ route('director.lpos.index') }}" class="text-gray-600 hover:text-gray-800 text-xs">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
            <button onclick="printLPO()" class="ml-2 bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button onclick="downloadPDF()" class="ml-2 bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </button>
        </div>
    </div>

    {{-- Editable Form for Pending LPOs --}}
    @if($lpo->status == 'pending_director')
    <form id="lpoEditForm" method="POST" action="{{ route('director.lpos.update', $lpo->id) }}">
        @csrf
        @method('PUT')
    @endif

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
                    $statusText = 'Pending Your Approval';
                } elseif ($lpo->status == 'director_approved') {
                    $statusClass = 'status-approved';
                    $statusText = 'Approved - Ready for External PO';
                } elseif ($lpo->status == 'converted_to_epo') {
                    $statusClass = 'status-converted';
                    $statusText = 'Converted to External PO';
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
            @if($lpo->status == 'pending_director')
            <span class="edit-badge">
                <i class="fas fa-edit mr-1"></i> Editable Mode
            </span>
            @endif
        </div>

        {{-- Rejection Reason --}}
        @if($lpo->status == 'director_rejected' && $lpo->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle text-red-600 text-sm mt-0.5"></i>
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

        {{-- Director Notes --}}
        @if($lpo->director_notes)
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-sm font-semibold text-yellow-800">Director Notes:</p>
            <p class="text-sm text-yellow-700">{{ $lpo->director_notes }}</p>
        </div>
        @endif

        {{-- Internal Notes --}}
        @if($lpo->notes)
        <div class="mb-4">
            <p class="text-sm font-medium text-gray-500">Internal Notes:</p>
            <div class="bg-gray-50 rounded p-3">
                <p class="text-sm text-gray-700">{{ $lpo->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table with Editable Quantities --}}
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-3">
                LPO Items
                @if($lpo->status == 'pending_director')
                <span class="text-xs text-orange-600 ml-2">(Click on quantity to edit)</span>
                @endif
            </h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">#</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Item</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Category</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Batch No.</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Expiry Date</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Pack Info</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Metrics</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Unit Cost</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Batch Stock</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total Stock</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Requested</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">LPO Qty</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $subtotal = 0;
                            $totalQuantity = 0;
                            $originalSubtotal = 0;

                            function fmtQty($val) {
                                if ($val == floor($val)) {
                                    return number_format($val, 0);
                                }
                                return rtrim(rtrim(number_format($val, 2), '0'), '.');
                            }
                        @endphp
                        @foreach($lpo->items as $index => $item)
                        @php
                            $originalQuantity = $item->quantity_approved;
                            $totalQuantity += $item->quantity_approved;
                            $total = $item->quantity_approved * $item->unit_cost;
                            $subtotal += $total;
                            $originalSubtotal += $originalQuantity * $item->unit_cost;

                            // Get batch information from the requisition item
                            $batch = null;
                            $batchNumber = 'N/A';
                            $expiryDate = null;
                            $expiryClass = '';
                            $daysLeft = 0;
                            $packInfo = 'Direct';
                            $batchStock = 0;
                            $totalStock = 0;
                            $requestedQty = 0;

                            if ($lpo->requisition && isset($lpo->requisition->items[$index])) {
                                $reqItem = $lpo->requisition->items[$index];
                                $requestedQty = $reqItem->quantity_requested;
                                $batch = $reqItem->batch;
                                if ($batch) {
                                    $batchNumber = $batch->batch_number;
                                    $expiryDate = $batch->expiry_date;
                                    if ($expiryDate) {
                                        $daysLeft = now()->diffInDays($expiryDate, false);
                                        if ($daysLeft <= 0) {
                                            $expiryClass = 'batch-expired';
                                        } elseif ($daysLeft <= 30) {
                                            $expiryClass = 'batch-expiring-soon';
                                        }
                                    }
                                    if ($batch->pack_type && $batch->pack_type != 'Direct' && $batch->pack_size > 1) {
                                        $packInfo = $batch->pack_type . ' (' . $batch->pack_size . '/pack)';
                                    }
                                    $batchStock = $batch->remaining_quantity;

                                    if ($reqItem->inventory_item_id) {
                                        $totalStock = \App\Models\Batch::where('inventory_item_id', $reqItem->inventory_item_id)
                                            ->where('batch_status', 'active')
                                            ->where('remaining_quantity', '>', 0)
                                            ->sum('remaining_quantity');
                                    }
                                }
                            }

                            $batchStockClass = $batchStock <= 0 ? 'batch-low' : ($batchStock < 10 ? 'batch-warning' : 'batch-ok');
                            $totalStockClass = $totalStock <= 0 ? 'batch-low' : ($totalStock < 10 ? 'batch-warning' : 'batch-ok');
                            $isChanged = ($item->quantity_approved != $originalQuantity);
                        @endphp
                        <tr class="border-b hover:bg-gray-50" data-item-id="{{ $item->id }}" data-original-qty="{{ $originalQuantity }}">
                            <td class="px-3 py-2 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br><span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $item->inventoryItem?->category?->name ?: '—' }}</td>
                            <td class="px-3 py-2 text-sm font-mono text-gray-600">{{ $batchNumber }}</td>
                            <td class="px-3 py-2 text-sm">
                                @if($expiryDate)
                                    <span class="batch-info {{ $expiryClass }}">
                                        {{ $expiryDate->format('d M Y') }}
                                        @if($daysLeft <= 0)
                                            (EXPIRED)
                                        @elseif($daysLeft <= 30)
                                            ({{ $daysLeft }} days left)
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $packInfo }}</td>
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $item->metrics ?: '—' }}</td>
                            <td class="px-3 py-2 text-sm text-right">UGX {{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-3 py-2 text-sm text-right">
                                <div class="font-semibold {{ $batchStockClass }}">
                                    {{ fmtQty($batchStock) }}
                                </div>
                                <div class="text-xs {{ $batchStockClass }}">
                                    @if($batchStock <= 0) Out @elseif($batchStock < 10) Low @else In @endif
                                </div>
                            </td>
                            <td class="px-3 py-2 text-sm text-right">
                                <div class="font-semibold {{ $totalStockClass }}">
                                    {{ fmtQty($totalStock) }}
                                </div>
                                <div class="text-xs {{ $totalStockClass }}">
                                    @if($totalStock <= 0) Out @elseif($totalStock < 10) Low @else In @endif
                                </div>
                            </td>
                            <td class="px-3 py-2 text-sm text-right">
                                <span class="text-gray-500">{{ fmtQty($requestedQty) }}</span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if($lpo->status == 'pending_director')
                                    <div class="flex flex-col items-end">
                                        <input type="number"
                                               name="items[{{ $index }}][quantity]"
                                               value="{{ $item->quantity_approved }}"
                                               step="0.01"
                                               min="0"
                                               max="{{ $requestedQty }}"
                                               class="quantity-input {{ $isChanged ? 'changed' : 'valid' }}"
                                               data-original="{{ $originalQuantity }}"
                                               data-unit-cost="{{ $item->unit_cost }}"
                                               data-index="{{ $index }}"
                                               oninput="updateItemTotal(this, {{ $index }}, {{ $item->unit_cost }})">
                                        <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                        <span class="original-qty">Original: {{ fmtQty($originalQuantity) }}</span>
                                    </div>
                                @else
                                    <span class="font-semibold">{{ fmtQty($item->quantity_approved) }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-right font-semibold item-total" id="total_{{ $index }}">
                                UGX {{ number_format($total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="11" class="px-3 py-2 text-right text-sm font-semibold">Subtotal:</td>
                            <td class="px-3 py-2 text-right text-sm font-semibold" id="subtotal_display">UGX {{ number_format($subtotal, 2) }}</td>
                            <td class="px-3 py-2"></td>
                        </tr>
                        @if($lpo->vat_rate > 0)
                        <tr>
                            <td colspan="11" class="px-3 py-2 text-right text-sm">VAT ({{ $lpo->vat_rate }}%):</td>
                            <td class="px-3 py-2 text-right text-sm" id="vat_display">UGX {{ number_format($lpo->vat_amount, 2) }}</td>
                            <td class="px-3 py-2"></td>
                        </tr>
                        @endif
                        <tr class="bg-green-50">
                            <td colspan="11" class="px-3 py-2 text-right text-sm font-bold">TOTAL:</td>
                            <td class="px-3 py-2 text-right text-sm font-bold text-green-600" id="total_display">UGX {{ number_format($lpo->total_amount, 2) }}</td>
                            <td class="px-3 py-2"></td>
                        </tr>
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
                <p class="text-xl font-bold text-green-800" id="summary_total">UGX {{ number_format($lpo->total_amount, 0) }}</p>
            </div>
        </div>

        {{-- SIGNATURES SECTION - SAME ROW (FLEX) --}}
        <div class="mt-6 pt-4 border-t">
            <div class="flex-signatures">
                {{-- Prepared By Signature --}}
                <div class="sig-block">
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

                {{-- Company Stamp --}}
                <div class="sig-block">
                    <p class="text-sm text-gray-500 mb-2">Company Stamp:</p>
                    @php
                        $stamp = \App\Models\BusinessSetting::getStamp();
                        $stampBase64 = null;
                        if ($stamp) {
                            $stampPath = public_path(parse_url($stamp, PHP_URL_PATH));
                            if (file_exists($stampPath)) {
                                $stampMime = mime_content_type($stampPath);
                                $stampData = base64_encode(file_get_contents($stampPath));
                                $stampBase64 = 'data:' . $stampMime . ';base64,' . $stampData;
                            }
                        }
                    @endphp
                    @if($stampBase64)
                        <img src="{{ $stampBase64 }}" class="stamp-img mx-auto" alt="Stamp">
                    @elseif($stamp)
                        <img src="{{ $stamp }}" class="stamp-img mx-auto" alt="Stamp">
                    @else
                        <div class="h-12"></div>
                        <p class="text-xs text-gray-400 mt-2">No stamp</p>
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                    <p class="text-sm text-gray-400 mt-1">Authorized Signature</p>
                </div>

                {{-- Approved By Signature --}}
                <div class="sig-block">
                    <p class="text-sm text-gray-500 mb-2">Approved By (Director):</p>
                    @php
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
                        <p class="text-sm text-gray-400 mt-1">Pending Your Approval</p>
                    @elseif($lpo->status == 'director_rejected')
                        <div class="h-12"></div>
                        <div class="border-t border-red-300 mt-2 pt-1 w-40 mx-auto"></div>
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
            <p class="text-xs text-gray-400">Computer generated document. Valid digital signature.</p>
            <p class="text-xs text-gray-400">{{ $companyName }} - All Rights Reserved</p>
        </div>
    </div>

    @if($lpo->status == 'pending_director')
    </form>
    @endif

    {{-- Action Buttons (only for pending LPOs) --}}
    @if($lpo->status == 'pending_director')
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-4 no-print">
        <button type="button" onclick="openRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
            <i class="fas fa-times mr-1"></i> Reject LPO
        </button>
        <button type="button" onclick="saveAndOpenApproveModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
            <i class="fas fa-save mr-1"></i> Save Changes & Approve
        </button>
    </div>
    @endif
</div>

{{-- Approve Modal with Notes --}}
<div id="approveModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden no-print">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
        <div class="bg-green-600 px-6 py-4 rounded-t-lg">
            <h3 class="text-lg font-semibold text-white">Approve LPO</h3>
            <p class="text-sm text-green-100 mt-1">{{ $lpo->lpo_number }}</p>
        </div>
        <form id="approveForm" action="{{ route('director.lpos.approve', $lpo->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-semibold mb-2 text-gray-700">Director Notes (Optional)</label>
                <textarea name="director_notes" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Add any notes or instructions for Procurement department..."></textarea>
                <p class="text-xs text-gray-500 mt-2">These notes will be visible to Procurement.</p>
                <input type="hidden" name="updated_items" id="updated_items">
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-check mr-1"></i> Confirm Approval
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
            <p class="text-sm text-red-100 mt-1">{{ $lpo->lpo_number }}</p>
        </div>
        <form action="{{ route('director.lpos.reject', $lpo->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-semibold mb-2 text-gray-700">Reason for Rejection <span class="text-red-500">*</span></label>
                <textarea name="rejection_reason" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Please provide a reason for rejecting this LPO..." required></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-times mr-1"></i> Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Store original values for comparison
    let originalQuantities = {};

    // Initialize tracking on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.quantity-input').forEach(input => {
            const index = input.getAttribute('data-index');
            originalQuantities[index] = parseFloat(input.value);
            updateItemTotal(input, index, parseFloat(input.getAttribute('data-unit-cost')));
        });
    });

    function updateItemTotal(input, index, unitCost) {
        const quantity = parseFloat(input.value) || 0;
        const total = quantity * unitCost;
        const totalElement = document.getElementById(`total_${index}`);

        if (totalElement) {
            totalElement.innerHTML = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }

        // Mark as changed if different from original
        if (originalQuantities[index] !== quantity) {
            input.classList.add('changed');
            input.classList.remove('valid');
        } else {
            input.classList.add('valid');
            input.classList.remove('changed');
        }

        // Recalculate overall totals LIVE
        recalculateTotals();
    }

    function recalculateTotals() {
        let subtotal = 0;

        document.querySelectorAll('.quantity-input').forEach(input => {
            const quantity = parseFloat(input.value) || 0;
            const unitCost = parseFloat(input.getAttribute('data-unit-cost'));
            subtotal += quantity * unitCost;
        });

        const vatRate = {{ $lpo->vat_rate ?? 0 }};
        const vatAmount = subtotal * (vatRate / 100);
        const totalAmount = subtotal + vatAmount;

        // Update all displays
        const subtotalDisplay = document.getElementById('subtotal_display');
        const vatDisplay = document.getElementById('vat_display');
        const totalDisplay = document.getElementById('total_display');
        const summaryTotal = document.getElementById('summary_total');

        if (subtotalDisplay) {
            subtotalDisplay.innerHTML = `UGX ${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }
        if (vatDisplay) {
            vatDisplay.innerHTML = `UGX ${vatAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }
        if (totalDisplay) {
            totalDisplay.innerHTML = `UGX ${totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }
        if (summaryTotal) {
            summaryTotal.innerHTML = `UGX ${totalAmount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}`;
        }
    }

    function saveAndOpenApproveModal() {
        // Collect all updated quantities
        let hasChanges = false;
        const updates = [];

        document.querySelectorAll('.quantity-input').forEach(input => {
            const newQuantity = parseFloat(input.value);
            const originalQty = parseFloat(input.getAttribute('data-original'));
            const itemId = input.closest('tr').querySelector('input[name*="[item_id]"]')?.value;

            if (newQuantity !== originalQty) {
                hasChanges = true;
                updates.push({
                    item_id: itemId,
                    quantity: newQuantity,
                    original: originalQty
                });
            }
        });

        if (hasChanges) {
            // Submit the edit form first, then approve
            const editForm = document.getElementById('lpoEditForm');
            const formData = new FormData(editForm);

            fetch(editForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      openApproveModal();
                  } else {
                      alert('Error saving changes: ' + (data.message || 'Unknown error'));
                  }
              }).catch(error => {
                  console.error('Error:', error);
                  alert('Error saving changes. Please try again.');
              });
        } else {
            // No changes, just open approve modal
            openApproveModal();
        }
    }

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
                    .stamp-img { max-height: 60px !important; max-width: 120px !important; }
                    .type-badge, .status-badge { padding: 3px 8px; font-size: 10px; border-radius: 999px; }
                    .flex-signatures { display: flex !important; flex-direction: row !important; justify-content: space-between !important; gap: 20px !important; }
                    .sig-block { flex: 1 !important; text-align: center !important; }
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
@endsection
