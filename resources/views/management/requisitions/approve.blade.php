@extends('layouts.management')
@section('title', 'Approve Requisition')
@section('page-title', 'Approve Requisition')

@section('content')
<style>
    .quantity-input {
        transition: all 0.2s ease;
    }
    .quantity-input.valid {
        border-color: #10b981;
        background-color: #f0fdf4;
    }
    .quantity-input.invalid {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
    .warning-text {
        font-size: 11px;
        margin-top: 4px;
        display: block;
    }
    .warning-text.error {
        color: #ef4444;
    }
    .warning-text.success {
        color: #10b981;
    }
    .warning-text.warning {
        color: #f59e0b;
    }
    .row-invalid {
        background-color: #fef2f2;
    }
    .toast-message {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #ef4444;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .stock-info {
        font-size: 10px;
        padding: 2px 4px;
        border-radius: 4px;
        display: inline-block;
    }
    .stock-low {
        background: #fee2e2;
        color: #dc2626;
    }
    .stock-ok {
        background: #dcfce7;
        color: #16a34a;
    }
    .stock-warning {
        background: #fef3c7;
        color: #d97706;
    }
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
            max-height: 40px !important;
            width: auto !important;
        }
    }
    .company-logo {
        max-height: 60px;
        width: auto;
    }
    .signature-img {
        max-height: 50px;
        max-width: 150px;
    }
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 12px;
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
</style>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Approve Requisition</h3>
        <p class="text-sm text-gray-500">Requisition #{{ $requisition->requisition_number }}</p>
    </div>

    <form method="POST" action="{{ route('management.requisitions.approve', $requisition->id) }}" id="approveForm">
        @csrf

        <div class="p-6 space-y-6">
            {{-- Requisition Info --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-500">Requisition Type</p>
                    <p class="font-medium">
                        <span class="type-badge {{ $requisition->requisition_type == 'emergency' ? 'type-emergency' : 'type-normal' }}">
                            {{ $requisition->requisition_type == 'emergency' ? 'EMERGENCY' : 'Normal' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Store</p>
                    <p class="font-medium">{{ $requisition->store->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Requested By</p>
                    <p class="font-medium">{{ $requisition->requestedBy ? $requisition->requestedBy->first_name . ' ' . $requisition->requestedBy->last_name : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date Needed</p>
                    <p class="font-medium">{{ $requisition->date_needed ? $requisition->date_needed->format('F d, Y') : 'Not specified' }}</p>
                </div>
            </div>



            {{-- Notes --}}
            @if($requisition->notes)
            <div class="p-4 bg-yellow-50 rounded-lg">
                <p class="text-sm text-gray-600">{{ $requisition->notes }}</p>
            </div>
            @endif

            {{-- Items Table with Editable Approved Quantity and Stock Info --}}
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Items to Approve</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-5">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Category</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Requested</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Available Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Metrics</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Approved Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($requisition->items as $index => $item)
                            @php
                                $currentStock = $item->inventoryItem ? $item->inventoryItem->current_stock : 0;
                                $stockClass = $currentStock <= 0 ? 'stock-low' : ($currentStock < 10 ? 'stock-warning' : 'stock-ok');
                                $stockText = $currentStock <= 0 ? 'Out of Stock' : ($currentStock < 10 ? 'Low Stock' : 'In Stock');
                            @endphp
                            <tr class="item-row" id="row_{{ $index }}">
                                <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                    @if($item->inventoryItem && $item->inventoryItem->item_code)
                                        <br>
                                        <span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                        {{ $item->category_name ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                    <strong>{{ number_format($item->quantity_requested, 2) }}</strong>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="stock-info {{ $stockClass }}">
                                        {{ number_format($currentStock, 2) }} {{ $item->inventoryItem->base_unit ?? 'pcs' }}
                                        <br>
                                        <span class="text-xs">{{ $stockText }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                        {{ $item->metrics ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="text-xs text-blue-600 hover:text-blue-800 px-2 py-1 bg-blue-50 rounded"
                                                    onclick="setMaxQuantity({{ $index }}, {{ $item->quantity_requested }}, '{{ addslashes($item->inventoryItem->name ?? 'Item') }}')">
                                                Max
                                            </button>
                                            <input type="number"
                                                   name="items[{{ $index }}][quantity_approved]"
                                                   id="qty_{{ $index }}"
                                                   value="{{ $item->quantity_requested }}"
                                                   step="0.01"
                                                   min="0"
                                                   max="{{ $item->quantity_requested }}"
                                                   class="quantity-input w-28 px-3 py-2 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-blue-500"
                                                   oninput="validateQuantity({{ $index }}, {{ $item->quantity_requested }}, {{ $currentStock }}, '{{ addslashes($item->inventoryItem->name ?? 'Item') }}', '{{ $item->inventoryItem->base_unit ?? 'pcs' }}')"
                                                   required>
                                            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                        </div>
                                        <div id="warning_{{ $index }}" class="warning-text"></div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <input type="text"
                                           name="items[{{ $index }}][notes]"
                                           value="{{ $item->notes }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                           placeholder="Add approval notes...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="8" class="px-4 py-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-sm text-gray-600">Legend:</span>
                                            <span class="text-xs text-green-600 ml-2">✓ Valid quantity</span>
                                            <span class="text-xs text-red-600 ml-2">✗ Invalid quantity</span>
                                            <span class="text-xs text-orange-600 ml-2">⚠️ Partial approval</span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-sm text-gray-600">Total Requested:</span>
                                            <span class="text-sm font-semibold text-gray-800" id="totalRequested">
                                                {{ number_format($requisition->items->sum('quantity_requested'), 2) }}
                                            </span>
                                            <span class="text-sm text-gray-600 ml-4">Total Approved:</span>
                                            <span class="text-sm font-semibold text-green-600" id="totalApproved">
                                                0.00
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                            {{-- Requester Signature --}}
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-2">Requested by</p>
                @php $requester = $requisition->requestedBy; @endphp
                @if($requester && $requester->signature_url)
                    @php
                        $sigUrl = $requester->signature_url;
                        $sigPath = public_path(parse_url($sigUrl, PHP_URL_PATH));
                        $sigExists = file_exists($sigPath);
                        $sigMime = $sigExists ? mime_content_type($sigPath) : 'image/png';
                        $sigB64 = $sigExists ? base64_encode(file_get_contents($sigPath)) : null;
                    @endphp
                    @if($sigB64)
                        <img src="data:{{ $sigMime }};base64,{{ $sigB64 }}" class="signature-img" alt="Signature">
                    @else
                        <img src="{{ $sigUrl }}" class="signature-img" alt="Signature">
                    @endif
                @else
                    <p class="text-sm text-gray-400">No signature on file</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">{{ $requester->first_name ?? '' }} {{ $requester->last_name ?? '' }}</p>
            </div>
            </div>

            {{-- Additional Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">GM Approval Notes (Optional)</label>
                <textarea name="approval_notes" id="approval_notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Any additional comments from General Manager..."></textarea>
            </div>
        </div>

        {{-- Preview Button --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <a href="{{ route('management.requisitions.show', $requisition->id) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="button" onclick="openPreview()" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                Preview & Submit Approval
            </button>
        </div>
    </form>
</div>

{{-- Preview Modal --}}
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-6xl shadow-lg rounded-lg bg-white">
        <div id="print-section">
            {{-- Logo and Header --}}
            <div class="flex justify-between items-start mb-6 pb-4 border-b">
                <div>
                    @php
                        $logo = \App\Models\BusinessSetting::getLogo();
                        $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
                    @endphp
                    @if($logo)
                        @php
                            $logoPath = public_path(parse_url($logo, PHP_URL_PATH));
                            $logoExists = file_exists($logoPath);
                            $logoMime = $logoExists ? mime_content_type($logoPath) : 'image/png';
                            $logoB64 = $logoExists ? base64_encode(file_get_contents($logoPath)) : null;
                        @endphp
                        @if($logoB64)
                            <img src="data:{{ $logoMime }};base64,{{ $logoB64 }}" class="company-logo print-logo" alt="Logo">
                        @else
                            <img src="{{ $logo }}" class="company-logo print-logo" alt="Logo">
                        @endif
                    @else
                        <h2 class="text-xl font-bold text-gray-800">{{ $companyName }}</h2>
                    @endif
                </div>
                <div class="text-right">
                    <h1 class="text-xl font-bold text-green-600">APPROVAL FORM</h1>
                    <p class="text-sm text-gray-500">{{ $requisition->requisition_number }}</p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Preview Approval</h3>
                <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Requisition Info --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg mb-6">
                <div>
                    <p class="text-xs text-gray-500">Requisition Type</p>
                    <p class="font-medium" id="previewType">Normal</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Store</p>
                    <p class="font-medium">{{ $requisition->store->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Requested By</p>
                    <p class="font-medium">{{ $requisition->requestedBy ? $requisition->requestedBy->first_name . ' ' . $requisition->requestedBy->last_name : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Date Needed</p>
                    <p class="font-medium">{{ $requisition->date_needed ? $requisition->date_needed->format('F d, Y') : 'Not specified' }}</p>
                </div>
            </div>



            {{-- Items Table Preview --}}
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Approved Items</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Item</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Category</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Requested</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Approved</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="previewItemsBody" class="divide-y divide-gray-200"></tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-right font-bold">TOTAL:</td>
                                <td class="px-4 py-2 text-right font-bold" id="previewTotalRequested">0.00</td>
                                <td class="px-4 py-2 text-right font-bold text-green-600" id="previewTotalApproved">0.00</td>
                                <td class="px-4 py-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                           {{-- Requester Signature Preview --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-2">Requested by </p>
                @if($requester && $requester->signature_url)
                    @if($sigB64)
                        <img src="data:{{ $sigMime }};base64,{{ $sigB64 }}" class="signature-img" alt="Signature">
                    @else
                        <img src="{{ $sigUrl }}" class="signature-img" alt="Signature">
                    @endif
                @else
                    <p class="text-sm text-gray-400">No signature on file</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">{{ $requester->first_name ?? '' }} {{ $requester->last_name ?? '' }}</p>
            </div>
            </div>

            {{-- Approval Notes --}}
            <div class="mt-6">
                <p class="text-xs text-gray-500">GM Approval Notes:</p>
                <p class="text-sm text-gray-700" id="previewApprovalNotes">—</p>
            </div>

            {{-- Signatures Section --}}
            <div class="mt-8 pt-4 border-t">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-2">Approved By (Management):</p>
                    <div class="mt-2">
                        <p class="text-sm font-medium">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                        <p class="text-xs text-gray-400">{{ now()->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs text-gray-400">This is a computer generated approval document. Valid without signature.</p>
                <p class="text-xs text-gray-400">{{ $companyName }} - All Rights Reserved</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
            <button type="button" onclick="closePreview()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Edit</button>
            <button type="button" onclick="printApproval()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button type="button" onclick="submitApproval()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Confirm & Submit Approval
            </button>
        </div>
    </div>
</div>

<script>
    let approvedItemsData = [];

    function showToast(message) {
        const existingToast = document.querySelector('.toast-message');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-message';
        toast.innerHTML = message;
        document.body.appendChild(toast);

        setTimeout(() => { toast.remove(); }, 3000);
    }

    function setMaxQuantity(index, maxQty, itemName) {
        const input = document.getElementById(`qty_${index}`);
        input.value = maxQty;
        const row = document.getElementById(`row_${index}`);
        const stockCell = row.querySelector('td:nth-child(5) .stock-info');
        let currentStock = 0;
        if (stockCell) {
            const stockText = stockCell.innerText;
            const match = stockText.match(/(\d+(?:\.\d+)?)/);
            if (match) currentStock = parseFloat(match[1]);
        }
        const baseUnit = row.querySelector('td:nth-child(5) .stock-info')?.innerText?.match(/[a-z]+$/)?.[0] || 'pcs';
        validateQuantity(index, maxQty, currentStock, itemName, baseUnit);
        showToast(`✅ ${itemName}: Approved quantity set to maximum (${maxQty})`);
    }

    function validateQuantity(index, maxQty, currentStock, itemName, baseUnit) {
        const input = document.getElementById(`qty_${index}`);
        const warningDiv = document.getElementById(`warning_${index}`);
        const row = document.getElementById(`row_${index}`);
        let value = parseFloat(input.value);

        input.classList.remove('valid', 'invalid');
        warningDiv.innerHTML = '';
        warningDiv.classList.remove('error', 'success', 'warning');
        if (row) row.classList.remove('row-invalid');

        if (isNaN(value)) {
            if (input.value === '') {
                warningDiv.innerHTML = '⚠️ Please enter approved quantity';
                warningDiv.classList.add('warning');
            } else {
                input.classList.add('invalid');
                warningDiv.innerHTML = '❌ Please enter a valid number';
                warningDiv.classList.add('error');
                if (row) row.classList.add('row-invalid');
            }
            return false;
        }

        if (value > maxQty) {
            input.value = maxQty;
            value = maxQty;
            showToast(`⚠️ ${itemName}: Approved quantity adjusted to maximum (${maxQty})`);
        }

        if (value < 0) {
            input.classList.add('invalid');
            warningDiv.innerHTML = '❌ Cannot be negative. Please enter zero or positive number.';
            warningDiv.classList.add('error');
            if (row) row.classList.add('row-invalid');
            return false;
        }

        if (value > currentStock && currentStock > 0) {
            warningDiv.innerHTML = `⚠️ Warning: Approved quantity (${value}) exceeds available stock (${currentStock} ${baseUnit})!`;
            warningDiv.classList.add('warning');
        } else if (value === maxQty) {
            warningDiv.innerHTML = '✓ Full quantity approved';
            warningDiv.classList.add('success');
        } else if (value === 0) {
            warningDiv.innerHTML = '⚠️ Warning: Approving zero quantity. This item will be skipped.';
            warningDiv.classList.add('warning');
        } else {
            const percentage = ((value / maxQty) * 100).toFixed(1);
            warningDiv.innerHTML = `✓ Partial approval: ${value} of ${maxQty} (${percentage}%) approved.`;
            warningDiv.classList.add('success');
        }

        input.classList.add('valid');
        updateTotalApproved();
        return true;
    }

    function updateTotalApproved() {
        let totalApproved = 0;
        let allValid = true;

        document.querySelectorAll('input[name*="[quantity_approved]"]').forEach(function(input) {
            const value = parseFloat(input.value);
            const maxValue = parseFloat(input.getAttribute('max'));

            if (!isNaN(value) && value >= 0 && value <= maxValue) {
                totalApproved += value;
            } else {
                allValid = false;
            }
        });

        document.getElementById('totalApproved').innerText = totalApproved.toFixed(2);

        const submitBtn = document.getElementById('submitBtn');
        if (allValid) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function openPreview() {
        approvedItemsData = [];
        let totalRequested = 0;
        let totalApproved = 0;
        let itemsHtml = '';

        document.querySelectorAll('.item-row').forEach(function(row, idx) {
            const itemName = row.querySelector('td:nth-child(2)').innerText.trim();
            const category = row.querySelector('td:nth-child(3) .px-2').innerText.trim();
            const requested = parseFloat(row.querySelector('td:nth-child(4) strong').innerText.replace(/,/g, ''));
            const approvedInput = row.querySelector(`input[name*="[quantity_approved]"]`);
            const approved = parseFloat(approvedInput.value);
            const notes = row.querySelector(`input[name*="[notes]"]`).value || '—';

            totalRequested += requested;
            totalApproved += approved;

            itemsHtml += `
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-800">${escapeHtml(itemName)}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(category)}</td>
                    <td class="px-4 py-2 text-sm text-right">${requested.toFixed(2)}</td>
                    <td class="px-4 py-2 text-sm text-right font-semibold ${approved > 0 ? 'text-green-600' : 'text-red-600'}">${approved.toFixed(2)}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(notes)}</td>
                </table>
            `;

            approvedItemsData.push({
                item_name: itemName,
                requested: requested,
                approved: approved,
                notes: notes
            });
        });

        document.getElementById('previewItemsBody').innerHTML = itemsHtml;
        document.getElementById('previewTotalRequested').innerHTML = totalRequested.toFixed(2);
        document.getElementById('previewTotalApproved').innerHTML = totalApproved.toFixed(2);
        document.getElementById('previewApprovalNotes').innerHTML = document.getElementById('approval_notes').value || '—';
        document.getElementById('previewType').innerHTML = `<span class="type-badge {{ $requisition->requisition_type == 'emergency' ? 'type-emergency' : 'type-normal' }}">{{ $requisition->requisition_type == 'emergency' ? 'EMERGENCY' : 'Normal' }}</span>`;

        document.getElementById('previewModal').classList.remove('hidden');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    function printApproval() {
        const printContents = document.getElementById('print-section').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Approval Form - {{ $requisition->requisition_number }}</title>
                <style>
                    body { padding: 20px; font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .company-logo, .print-logo { max-height: 40px !important; width: auto !important; }
                    .signature-img { max-height: 50px; max-width: 150px; }
                    @media print {
                        body { margin: 0; padding: 20px; }
                    }
                </style>
            </head>
            <body>${printContents}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    function submitApproval() {
        closePreview();
        document.getElementById('approveForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name*="[quantity_approved]"]').forEach(function(input, idx) {
            const maxValue = parseFloat(input.getAttribute('max'));
            const row = input.closest('.item-row');
            const itemName = row.querySelector('td:nth-child(2)').innerText.trim();
            const stockCell = row.querySelector('td:nth-child(5) .stock-info');
            let currentStock = 0;
            if (stockCell) {
                const stockText = stockCell.innerText;
                const match = stockText.match(/(\d+(?:\.\d+)?)/);
                if (match) currentStock = parseFloat(match[1]);
            }
            const baseUnit = row.querySelector('td:nth-child(5) .stock-info')?.innerText?.match(/[a-z]+$/)?.[0] || 'pcs';
            validateQuantity(idx, maxValue, currentStock, itemName, baseUnit);
        });
    });
</script>
@endsection
