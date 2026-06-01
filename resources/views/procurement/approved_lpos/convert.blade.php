@extends('layouts.procurement')

@section('title', 'Convert LPO to External PO')

@section('page-title', 'Convert LPO to External Purchase Order')

@section('content')
<style>
    .info-box {
        background-color: #eff6ff;
        border-left: 4px solid #3b82f6;
    }
    .director-notes-box {
        background-color: #fef3c7;
        border-left: 4px solid #f59e0b;
    }
    .editable-input {
        width: 120px;
        text-align: right;
        appearance: textfield;
        -moz-appearance: textfield;
    }
    .editable-input:focus {
        border-color: #3b82f6;
        outline: none;
        ring: 2px solid #3b82f6;
    }
    .item-quantity, .item-cost {
        transition: all 0.2s ease;
    }
    .item-quantity:hover, .item-cost:hover {
        background-color: #fef3c7;
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
    .vat-section {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    /* A4 Print Styles */
    #print-section {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 15mm 15mm 20mm 15mm;
        box-sizing: border-box;
        font-size: 10pt;
        line-height: 1.4;
        background: white;
    }
    .company-logo { max-height: 60px; width: auto; }
    .signature-img { max-height: 50px; max-width: 150px; }

    /* Preview Modal A4 Container */
    .modal-a4-container {
        background: white;
        margin: 0 auto;
        overflow-y: auto;
        max-height: 90vh;
    }
    .modal-a4-container #print-section {
        margin: 0 auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 15mm 20mm 15mm;
            box-sizing: border-box;
            font-size: 10pt;
            max-width: none;
        }
        .no-print { display: none !important; }
        button, .btn, .action-buttons { display: none !important; }
        .company-logo, .print-logo { max-height: 40px !important; width: auto !important; }
        .signature-img { max-height: 50px !important; max-width: 150px !important; }
        table { page-break-inside: avoid; }
        thead { display: table-header-group; }
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center no-print">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Convert LPO #{{ $lpo->lpo_number }} to External PO</h3>
            <p class="text-sm text-gray-500">Review and edit items based on Director's notes, then convert to External PO</p>
        </div>
        <div class="flex gap-2">
            <button onclick="printPO()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button onclick="downloadPDF()" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </button>
        </div>
    </div>

    {{-- Director Notes --}}
    @if($lpo->director_notes)
    <div class="director-notes-box p-4 mx-6 mt-4 rounded-lg">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <div>
                <h4 class="text-sm font-semibold text-yellow-800">Director Notes</h4>
                <p class="text-sm text-yellow-700 mt-1">{{ $lpo->director_notes }}</p>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('procurement.approved-lpos.store-epo', $lpo->id) }}" id="epoForm">
        @csrf
        <input type="hidden" name="vat_rate" value="{{ $lpo->vat_rate ?? 0 }}">

        <div class="p-6 space-y-6">
            {{-- Info Box --}}
            <div class="info-box p-4 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Note:</strong> You can edit quantities and unit costs based on Director's notes.
                    Click Preview to review before sending to vendor.
                </p>
            </div>

            {{-- LPO Information (Read Only) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LPO Number</label>
                    <input type="text" value="{{ $lpo->lpo_number }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PO Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="normal" {{ $lpo->type == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="emergency" {{ $lpo->type == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="cash" {{ $lpo->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="credit" {{ $lpo->payment_method == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="bank_transfer" {{ $lpo->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="mobile_money" {{ $lpo->payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="cheque" {{ $lpo->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
            </div>

            {{-- External PO Details --}}
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-md font-semibold text-gray-800 mb-4">External Purchase Order Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Date <span class="text-red-500">*</span></label>
                        <input type="date" name="po_date" id="po_date" value="{{ date('Y-m-d') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                        <input type="date" name="delivery_date" id="delivery_date" value="{{ $lpo->expected_delivery_date ? date('Y-m-d', strtotime($lpo->expected_delivery_date)) : '' }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                        <input type="text" name="delivery_address" id="delivery_address" value="{{ $lpo->delivery_address }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter delivery address">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Terms</label>
                        <input type="text" name="delivery_terms" id="delivery_terms" value="{{ $lpo->delivery_instructions }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., FOB, CIF, Ex-Works">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (for vendor)</label>
                    <textarea name="notes" id="po_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Any additional notes to include in the purchase order...">{{ $lpo->notes }}</textarea>
                </div>
            </div>

            {{-- Editable Items Table --}}
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-md font-semibold text-gray-800 mb-3">Items <span class="text-xs text-gray-500">(Editable - type directly)</span></h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Quantity</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Unit Cost (UGX)</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Total (UGX)</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-gray-200">
                            @foreach($lpo->items as $index => $item)
                            @php
                                $originalQty = $item->quantity_approved;
                                $originalCost = $item->unit_cost;
                                $total = $originalQty * $originalCost;
                            @endphp
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                    <input type="hidden" name="items[{{ $index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                                    @if($item->inventoryItem && $item->inventoryItem->item_code)
                                        <br><span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                        {{ $item->inventoryItem?->category?->name ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                        {{ $item->metrics ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <input type="number" name="items[{{ $index }}][quantity]"
                                           class="item-quantity editable-input w-24 px-2 py-1 border border-gray-300 rounded-lg text-right"
                                           value="{{ $originalQty == floor($originalQty) ? number_format($originalQty, 0) : rtrim(rtrim(number_format($originalQty, 2), '0'), '.') }}"
                                           step="any"
                                           min="0"
                                           oninput="updateTotals()"
                                           style="appearance: textfield; -moz-appearance: textfield;">
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <input type="number" name="items[{{ $index }}][unit_cost]"
                                           class="item-cost editable-input w-28 px-2 py-1 border border-gray-300 rounded-lg text-right"
                                           value="{{ $originalCost }}"
                                           step="any"
                                           min="0"
                                           oninput="updateTotals()"
                                           style="appearance: textfield; -moz-appearance: textfield;">
                                </td>
                                <td class="px-4 py-3 text-right font-semibold item-total text-green-600">
                                    UGX {{ $total % 1 === 0 ? number_format($total, 0) : number_format($total, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- VAT Section --}}
            <div class="vat-section">
                <h4 class="text-md font-semibold text-yellow-800 mb-3">VAT Calculation</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (UGX)</label>
                        <input type="text" id="subtotal_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">VAT Rate (%)</label>
                        <input type="number" id="vat_rate" step="any" min="0" max="100"
                               value="{{ $lpo->vat_rate ?? 0 }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">VAT Amount (UGX)</label>
                        <input type="text" id="vat_amount_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <span class="text-lg font-bold">Grand Total: </span>
                    <span id="grand_total_display" class="text-xl font-bold text-green-600">UGX 0</span>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 no-print">
            <a href="{{ route('procurement.approved-lpos.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="button" onclick="showPreview()" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                Preview External PO
            </button>
        </div>
    </form>
</div>

{{-- Preview Modal - A4 Sized --}}
<div id="previewModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden overflow-y-auto">
    <div class="relative top-10 mx-auto p-4 w-full max-w-[210mm] mb-10">
        {{-- Modal top bar (no-print) --}}
        <div class="flex justify-between items-center mb-4 bg-white p-3 rounded-t-lg shadow no-print">
            <h3 class="text-xl font-semibold text-gray-800">Preview External Purchase Order</h3>
            <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- A4 print section --}}
        <div id="print-section" class="bg-white shadow-lg">

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
                    <h1 class="text-xl font-bold text-blue-600">PURCHASE ORDER</h1>
                    <p class="text-sm text-gray-500" id="previewPONumber">PO-XXXXXX</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">PO Number</p>
                            <p class="font-medium" id="previewPONumberText">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">PO Date</p>
                            <p class="font-medium" id="previewPODate">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">PO Type</p>
                            <p class="font-medium" id="previewType">Normal</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Payment Method</p>
                            <p class="font-medium" id="previewPaymentMethod">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Vendor</p>
                            <p class="font-medium" id="previewVendor">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Delivery Date</p>
                            <p class="font-medium" id="previewDeliveryDate">—</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Delivery Address</p>
                            <p class="font-medium" id="previewDeliveryAddress">—</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Delivery Terms</p>
                            <p class="font-medium" id="previewDeliveryTerms">—</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Items</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 rounded-lg" style="table-layout: fixed;">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500" style="width: 40%;">Item</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500" style="width: 15%;">Quantity</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500" style="width: 22%;">Unit Cost</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500" style="width: 23%;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="previewItemsBody"></tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right font-bold">Subtotal:</td>
                                    <td class="px-4 py-2 text-right font-bold" id="previewSubtotal">UGX 0</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right">VAT (<span id="previewVatRate">0</span>%):</td>
                                    <td class="px-4 py-2 text-right" id="previewVatAmount">UGX 0</td>
                                </tr>
                                <tr class="bg-green-50">
                                    <td colspan="3" class="px-4 py-2 text-right font-bold text-green-800">GRAND TOTAL:</td>
                                    <td class="px-4 py-2 text-right font-bold text-green-800" id="previewGrandTotal">UGX 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Additional Notes</p>
                    <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded" id="previewNotes">—</p>
                </div>

                {{-- Signatures Section --}}
                <div class="mt-6 pt-4 border-t">
                    <div class="grid grid-cols-2 gap-8">
                        {{-- Prepared By Signature (Procurement) --}}
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-2">Prepared By (Procurement):</p>
                            @php $preparedBy = Auth::user(); @endphp
                            @if($preparedBy && $preparedBy->signature_path)
                                @php
                                    $sigPath = storage_path('app/public/' . $preparedBy->signature_path);
                                    if (file_exists($sigPath)) {
                                        $sigMime = mime_content_type($sigPath);
                                        $sigData = base64_encode(file_get_contents($sigPath));
                                        $sigBase64 = 'data:' . $sigMime . ';base64,' . $sigData;
                                        echo '<img src="' . $sigBase64 . '" class="signature-img mx-auto" alt="Signature">';
                                    } else {
                                        echo '<img src="' . asset($preparedBy->signature_path) . '" class="signature-img mx-auto" alt="Signature">';
                                    }
                                @endphp
                            @else
                                <div class="h-12"></div>
                                <p class="text-xs text-gray-400">No signature on file</p>
                            @endif
                            <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                            <p class="text-xs text-gray-600 mt-1">{{ $preparedBy->first_name ?? '' }} {{ $preparedBy->last_name ?? '' }}</p>
                            <p class="text-xs text-gray-400">{{ now()->format('d M Y') }}</p>
                        </div>

                        {{-- Approved By Signature (Director) --}}
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-2">Approved By (Director):</p>
                            @php
                                $wasApproved = $lpo->approved_by && $lpo->approved_at;
                                $approver = $wasApproved ? $lpo->approvedBy : null;
                            @endphp
                            @if($wasApproved && $approver && $approver->signature_path)
                                @php
                                    $sigPath = storage_path('app/public/' . $approver->signature_path);
                                    if (file_exists($sigPath)) {
                                        $sigMime = mime_content_type($sigPath);
                                        $sigData = base64_encode(file_get_contents($sigPath));
                                        $sigBase64 = 'data:' . $sigMime . ';base64,' . $sigData;
                                        echo '<img src="' . $sigBase64 . '" class="signature-img mx-auto" alt="Signature">';
                                    } else {
                                        echo '<img src="' . asset($approver->signature_path) . '" class="signature-img mx-auto" alt="Signature">';
                                    }
                                @endphp
                                <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                                <p class="text-xs text-gray-600 mt-1">{{ $approver->first_name ?? '' }} {{ $approver->last_name ?? '' }}</p>
                                <p class="text-xs text-gray-400">{{ $lpo->approved_at ? \Carbon\Carbon::parse($lpo->approved_at)->format('d M Y') : '' }}</p>
                            @elseif($lpo->status == 'director_rejected')
                                <div class="h-12"></div>
                                <div class="border-t border-red-300 mt-2 pt-1 w-40 mx-auto"></div>
                                <p class="text-sm text-red-600 mt-1">REJECTED</p>
                            @else
                                <div class="h-12"></div>
                                <div class="border-t border-gray-300 mt-2 pt-1 w-40 mx-auto"></div>
                                <p class="text-xs text-gray-400 mt-1">Pending Director Approval</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- end #print-section --}}

        <div class="mt-4 flex justify-end gap-3 pt-4 border-t bg-white p-3 rounded-b-lg no-print">
            <button type="button" onclick="closePreview()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Edit
            </button>
            <button type="button" onclick="printPO()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button type="button" onclick="submitForm()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Confirm &amp; Send to Vendor
            </button>
        </div>
    </div>
</div>

<script>
    // Set min dates on page load - prevent past dates
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];

        const poDateInput = document.getElementById('po_date');
        const deliveryDateInput = document.getElementById('delivery_date');

        if (poDateInput) {
            poDateInput.setAttribute('min', today);
            // If current value is in the past, reset to today
            if (poDateInput.value < today) {
                poDateInput.value = today;
            }
        }

        if (deliveryDateInput) {
            deliveryDateInput.setAttribute('min', today);
            // If current value is in the past, reset to today
            if (deliveryDateInput.value && deliveryDateInput.value < today) {
                deliveryDateInput.value = today;
            }
        }
    });

    function formatNumber(num) {
        if (num % 1 === 0) {
            return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
        return parseFloat(num.toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('#itemsBody tr').forEach(row => {
            const quantity = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
            const unitCost = parseFloat(row.querySelector('.item-cost')?.value) || 0;
            const total = quantity * unitCost;
            subtotal += total;

            const totalCell = row.querySelector('.item-total');
            if (totalCell) {
                totalCell.innerText = `UGX ${formatNumber(total)}`;
            }
        });

        const vatRate = parseFloat(document.getElementById('vat_rate')?.value) || 0;
        const vatAmount = (subtotal * vatRate) / 100;
        const grandTotal = subtotal + vatAmount;

        document.getElementById('subtotal_display').value = `UGX ${formatNumber(subtotal)}`;
        document.getElementById('vat_amount_display').value = `UGX ${formatNumber(vatAmount)}`;
        document.getElementById('grand_total_display').innerHTML = `UGX ${formatNumber(grandTotal)}`;

        if (document.querySelector('input[name="vat_rate"]')) {
            document.querySelector('input[name="vat_rate"]').value = vatRate;
        }
    }

    function showPreview() {
        const today = new Date();
        const poNumber = `PO-${today.getFullYear()}${String(today.getMonth()+1).padStart(2,'0')}${String(today.getDate()).padStart(2,'0')}-${Math.floor(Math.random() * 10000)}`;

        document.getElementById('previewPONumber').innerText = poNumber;
        document.getElementById('previewPONumberText').innerText = poNumber;
        document.getElementById('previewPODate').innerText = document.getElementById('po_date').value || 'Not specified';
        document.getElementById('previewVendor').innerText = '{{ $lpo->vendor->name ?? 'N/A' }}';
        document.getElementById('previewDeliveryDate').innerText = document.getElementById('delivery_date').value || 'Not specified';
        document.getElementById('previewDeliveryAddress').innerText = document.getElementById('delivery_address').value || 'Not specified';
        document.getElementById('previewDeliveryTerms').innerText = document.getElementById('delivery_terms').value || 'Not specified';
        document.getElementById('previewNotes').innerText = document.getElementById('po_notes').value || '—';

        const poType = document.querySelector('select[name="type"]').value;
        const typeText = poType === 'emergency' ? 'EMERGENCY' : 'Normal';
        const typeClass = poType === 'emergency' ? 'type-emergency' : 'type-normal';
        document.getElementById('previewType').innerHTML = `<span class="type-badge ${typeClass}">${typeText}</span>`;

        const paymentMethod = document.querySelector('select[name="payment_method"]').value;
        const paymentText = paymentMethod ? paymentMethod.toUpperCase().replace(/_/g, ' ') : '—';
        document.getElementById('previewPaymentMethod').innerText = paymentText;

        const previewBody = document.getElementById('previewItemsBody');
        previewBody.innerHTML = '';
        let subtotal = 0;

        document.querySelectorAll('#itemsBody tr').forEach(row => {
            const itemName = row.querySelector('td:first-child')?.innerText.split('\n')[0] || 'Item';
            const quantity = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
            const unitCost = parseFloat(row.querySelector('.item-cost')?.value) || 0;
            const total = quantity * unitCost;
            subtotal += total;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2 text-sm" style="word-break: break-word;">${itemName}</td>
                <td class="px-4 py-2 text-sm text-right">${formatNumber(quantity)}</td>
                <td class="px-4 py-2 text-sm text-right">UGX ${formatNumber(unitCost)}</td>
                <td class="px-4 py-2 text-sm text-right">UGX ${formatNumber(total)}</td>
            `;
            previewBody.appendChild(tr);
        });

        const vatRate = parseFloat(document.getElementById('vat_rate')?.value) || 0;
        const vatAmount = (subtotal * vatRate) / 100;
        const grandTotal = subtotal + vatAmount;

        document.getElementById('previewSubtotal').innerHTML = `UGX ${formatNumber(subtotal)}`;
        document.getElementById('previewVatRate').innerHTML = vatRate;
        document.getElementById('previewVatAmount').innerHTML = `UGX ${formatNumber(vatAmount)}`;
        document.getElementById('previewGrandTotal').innerHTML = `UGX ${formatNumber(grandTotal)}`;

        document.getElementById('previewModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function submitForm() {
        closePreview();
        document.getElementById('epoForm').submit();
    }

    function printPO() {
        const printContents = document.getElementById('print-section').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Purchase Order</title>
                <meta charset="UTF-8">
                <style>
                    @page {
                        size: A4 portrait;
                        margin: 15mm 15mm 20mm 15mm;
                    }
                    * { box-sizing: border-box; margin: 0; padding: 0; }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 10pt;
                        line-height: 1.4;
                        color: #111;
                        width: 210mm;
                    }
                    h1 { font-size: 14pt; font-weight: bold; color: #2563eb; }
                    h2 { font-size: 13pt; font-weight: bold; }
                    h4 { font-size: 10pt; font-weight: bold; margin-bottom: 6px; }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        page-break-inside: avoid;
                        font-size: 9pt;
                        table-layout: fixed;
                    }
                    thead { display: table-header-group; }
                    th, td {
                        border: 0.5pt solid #ccc;
                        padding: 5px 8px;
                        text-align: left;
                        word-break: break-word;
                    }
                    th { background-color: #f2f2f2; font-size: 8pt; text-transform: uppercase; }
                    .text-right { text-align: right; }
                    .text-center { text-align: center; }
                    .font-bold { font-weight: bold; }
                    .font-medium { font-weight: 500; }
                    .font-semibold { font-weight: 600; }
                    .company-logo { max-height: 40px; width: auto; }
                    .print-logo { max-height: 40px; width: auto; }
                    .signature-img { max-height: 50px; max-width: 120px; display: block; margin: 0 auto; }
                    .type-badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 8pt; font-weight: 600; }
                    .type-normal { background: #d1fae5; color: #065f46; }
                    .type-emergency { background: #fee2e2; color: #991b1b; }

                    /* Layout helpers */
                    .flex { display: flex; }
                    .justify-between { justify-content: space-between; }
                    .items-start { align-items: flex-start; }
                    .items-center { align-items: center; }
                    .text-right-block { text-align: right; }

                    .grid { display: grid; }
                    .grid-cols-2 { grid-template-columns: 1fr 1fr; gap: 16px; }
                    .grid-cols-4 { grid-template-columns: repeat(4, 1fr); gap: 10px; }
                    .col-span-2 { grid-column: span 2; }

                    .gap-4 { gap: 10px; }
                    .gap-8 { gap: 20px; }

                    .p-4 { padding: 10px; }
                    .p-2 { padding: 6px; }
                    .px-4 { padding-left: 10px; padding-right: 10px; }
                    .py-2 { padding-top: 5px; padding-bottom: 5px; }

                    .mb-2 { margin-bottom: 6px; }
                    .mb-4 { margin-bottom: 12px; }
                    .mb-6 { margin-bottom: 18px; }
                    .mt-2 { margin-top: 6px; }
                    .mt-6 { margin-top: 18px; }
                    .pt-1 { padding-top: 3px; }
                    .pt-4 { padding-top: 12px; }
                    .pb-4 { padding-bottom: 12px; }

                    .border-b { border-bottom: 0.5pt solid #e5e7eb; }
                    .border-t { border-top: 0.5pt solid #e5e7eb; }
                    .border { border: 0.5pt solid #e5e7eb; }

                    .rounded-lg { border-radius: 6px; }
                    .bg-gray-50 { background-color: #f9fafb; }
                    .bg-green-50 { background-color: #f0fdf4; }
                    .bg-gray-100 { background-color: #f3f4f6; }

                    .text-xs { font-size: 8pt; }
                    .text-sm { font-size: 9pt; }
                    .text-xl { font-size: 14pt; }

                    .text-blue-600 { color: #2563eb; }
                    .text-gray-400 { color: #9ca3af; }
                    .text-gray-500 { color: #6b7280; }
                    .text-gray-600 { color: #4b5563; }
                    .text-gray-700 { color: #374151; }
                    .text-gray-800 { color: #1f2937; }
                    .text-green-800 { color: #166534; }
                    .text-red-600 { color: #dc2626; }

                    .w-40 { width: 120px; }
                    .mx-auto { margin-left: auto; margin-right: auto; display: block; }
                    .h-12 { height: 40px; }

                    .space-y-4 > * + * { margin-top: 12px; }
                    .overflow-x-auto { overflow: visible; }

                    .no-print { display: none !important; }

                    @media print {
                        body { width: auto; }
                        .no-print { display: none !important; }
                    }
                </style>
            </head>
            <body>${printContents}</body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 600);
    }

    function downloadPDF() {
        const element = document.getElementById('print-section');
        const opt = {
            margin:      [15, 15, 20, 15],
            filename:    'Purchase-Order-{{ $lpo->lpo_number }}.pdf',
            image:       { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: true,
                windowWidth: 794
            },
            jsPDF: {
                unit:        'mm',
                format:      'a4',
                orientation: 'portrait'
            },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        };
        html2pdf().set(opt).from(element).save();
    }

    document.querySelectorAll('.item-quantity, .item-cost').forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    updateTotals();
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
@endsection
