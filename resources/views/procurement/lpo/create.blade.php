@extends('layouts.procurement')

@section('title', 'Create LPO')
@section('page-title', 'Create Local Purchase Order (LPO)')

@section('content')
<style>
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }
    .items-table th,
    .items-table td {
        padding: 12px;
        border: 1px solid #e5e7eb;
        vertical-align: top;
    }
    .items-table th {
        background-color: #f9fafb;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
    }
    .item-row:hover {
        background-color: #f9fafb;
    }
    .quantity-input {
        width: 100px;
    }
    .unit-cost-input {
        width: 120px;
    }
    .total-cell {
        font-weight: 600;
        background-color: #f0fdf4;
    }
    .preview-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Create Local Purchase Order (LPO)</h3>
        <p class="text-sm text-gray-500">From Requisition: {{ $requisition->requisition_number }}</p>
    </div>

    <form method="POST" action="{{ route('procurement.lpo.store') }}" id="lpoForm">
        @csrf
        <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">

        <div class="p-6 space-y-6">
            {{-- LPO Header --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        LPO Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="normal">Normal</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Vendor <span class="text-red-500">*</span>
                    </label>
                    <select name="vendor_id" id="vendor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
    <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        LPO Date <span class="text-red-500">*</span>
    </label>
    <input type="date" name="lpo_date" id="lpo_date" value="{{ date('Y-m-d') }}" required
           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100"
           readonly>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
    <input type="date" name="expected_delivery_date" id="expected_delivery_date"
           min="{{ date('Y-m-d') }}"
           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
</div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="cash">Cash</option>
                        <option value="credit">Credit</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">VAT Rate (%)</label>
                    <input type="number" name="vat_rate" id="vat_rate" step="0.01" min="0" max="100" value="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                    <input type="text" name="delivery_address" id="delivery_address"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                           placeholder="Enter delivery address">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Instructions</label>
                    <textarea name="delivery_instructions" id="delivery_instructions" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                              placeholder="Any special delivery instructions..."></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">LPO Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                              placeholder="Any additional notes..."></textarea>
                </div>
            </div>

            {{-- Items Section --}}
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4">Items</h4>
                <div class="overflow-x-auto">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 30%">Item Name</th>
                                <th style="width: 8%">Metrics</th>
                                <th style="width: 10%">GM Approved Qty</th>
                                <th style="width: 12%">Quantity to Order</th>
                                <th style="width: 15%">Unit Cost (UGX)</th>
                                <th style="width: 15%">Total (UGX)</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($requisition->items as $index => $item)
                            <tr class="item-row" id="row_{{ $index }}">
                                <td>
                                    {{ $item->inventoryItem->name ?? 'Item not found' }}
                                    @if($item->inventoryItem && $item->inventoryItem->item_code)
                                        <br>
                                        <span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                    @endif
                                    <input type="hidden" name="items[{{ $index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                                    <input type="hidden" name="items[{{ $index }}][requisition_item_id]" value="{{ $item->id }}">
                                 </td>
                                <td class="text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                        {{ $item->metrics ?: 'pcs' }}
                                    </span>
                                </td>
                                <td class="text-right font-semibold text-green-600">
                                    {{ number_format($item->quantity_approved, 2) }}
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][quantity]"
                                           id="qty_{{ $index }}"
                                           value="{{ $item->quantity_approved }}"
                                           step="0.01" min="0"
                                           class="w-full px-2 py-1 border border-gray-300 rounded-lg text-right quantity-input"
                                           oninput="calculateRowTotal({{ $index }})">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][unit_cost]"
                                           id="cost_{{ $index }}"
                                           step="0.01" min="0"
                                           class="w-full px-2 py-1 border border-gray-300 rounded-lg text-right unit-cost-input"
                                           oninput="calculateRowTotal({{ $index }})">
                                </td>
                                <td class="total-cell text-right" id="total_{{ $index }}">
                                    UGX 0.00
                                </td>
                                <td class="text-center">
                                    <button type="button" class="text-red-600 hover:text-red-800 remove-item" data-index="{{ $index }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                             </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="5" class="text-right font-bold">Subtotal:</td>
                                <td class="text-right font-bold" id="subtotal_display">UGX 0.00</td>
                                <td></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td colspan="5" class="text-right">VAT (<span id="vat_rate_display">0</span>%):</td>
                                <td class="text-right" id="vat_amount_display">UGX 0.00</td>
                                <td></td>
                            </tr>
                            <tr class="bg-green-50">
                                <td colspan="5" class="text-right font-bold text-green-800">TOTAL:</td>
                                <td class="text-right font-bold text-green-800" id="total_display">UGX 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 no-print">
            <a href="{{ route('procurement.requisitions.show', $requisition->id) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="button" id="previewBtn" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                Preview LPO
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
                    <h1 class="text-xl font-bold text-blue-600">LOCAL PURCHASE ORDER (LPO)</h1>
                    <p class="text-sm text-gray-500" id="previewLpoNumber">LPO-XXXXXX</p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4 no-print">
                <h3 class="text-xl font-semibold text-gray-800">Preview LPO</h3>
                <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- LPO Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg mb-6">
                <div>
                    <p class="text-xs text-gray-500">LPO Type</p>
                    <p class="font-medium" id="previewType">Normal</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Vendor</p>
                    <p class="font-medium" id="previewVendor">—</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">LPO Date</p>
                    <p class="font-medium" id="previewLpoDate">—</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Expected Delivery</p>
                    <p class="font-medium" id="previewDeliveryDate">—</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Payment Method</p>
                    <p class="font-medium" id="previewPaymentMethod">—</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Delivery Address</p>
                    <p class="font-medium" id="previewDeliveryAddress">—</p>
                </div>
            </div>

            {{-- Items Table Preview --}}
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Items</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Item</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Metrics</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Quantity</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Unit Cost (UGX)</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Total (UGX)</th>
                            </tr>
                        </thead>
                        <tbody id="previewItemsBody" class="divide-y divide-gray-200"></tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right font-bold">Subtotal:</td>
                                <td class="px-4 py-2 text-right font-bold" id="previewSubtotal">UGX 0.00</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right">VAT (<span id="previewVatRate">0</span>%):</td>
                                <td class="px-4 py-2 text-right" id="previewVatAmount">UGX 0.00</td>
                            </tr>
                            <tr class="bg-green-50">
                                <td colspan="4" class="px-4 py-2 text-right font-bold text-green-800">TOTAL:</td>
                                <td class="px-4 py-2 text-right font-bold text-green-800" id="previewTotal">UGX 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Amount in Words --}}
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500">Amount in Words:</p>
                <p class="text-sm font-medium" id="previewAmountWords">—</p>
            </div>

            {{-- Notes --}}
            <div class="mt-4">
                <p class="text-xs text-gray-500">LPO Notes:</p>
                <p class="text-sm text-gray-700" id="previewNotes">—</p>
            </div>

            {{-- Signatures Section --}}
            <div class="mt-8 pt-4 border-t">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-2">Prepared By:</p>
                        @php $user = Auth::user(); @endphp
                        @if($user && $user->signature_url)
                            @php
                                $sigUrl = $user->signature_url;
                                $sigPath = public_path(parse_url($sigUrl, PHP_URL_PATH));
                                $sigExists = file_exists($sigPath);
                                $sigMime = $sigExists ? mime_content_type($sigPath) : 'image/png';
                                $sigB64 = $sigExists ? base64_encode(file_get_contents($sigPath)) : null;
                            @endphp
                            @if($sigB64)
                                <img src="data:{{ $sigMime }};base64,{{ $sigB64 }}" class="signature-img mx-auto" alt="Signature">
                            @else
                                <img src="{{ $sigUrl }}" class="signature-img mx-auto" alt="Signature">
                            @endif
                        @else
                            <div style="height: 50px;"></div>
                        @endif
                        <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                        <p class="text-xs text-gray-600 mt-1">{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</p>
                        <p class="text-xs text-gray-400" id="previewDate">{{ now()->format('d M Y') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-2">Approved By (Director):</p>
                        <div style="height: 50px;"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                        <p class="text-xs text-gray-400 mt-1">Pending Director Approval</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs text-gray-400">This is a computer generated document. with digital signatures.</p>
                <p class="text-xs text-gray-400">{{ $companyName }} - All Rights Reserved</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t no-print">
            <button type="button" onclick="closePreview()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Edit</button>
            <button type="button" onclick="printLPO()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button type="button" onclick="submitLPO()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Submit LPO for Director Approval
            </button>
        </div>
    </div>
</div>

<script>
    // Calculate row total
    function calculateRowTotal(index) {
        const quantity = parseFloat(document.getElementById(`qty_${index}`).value) || 0;
        const cost = parseFloat(document.getElementById(`cost_${index}`).value) || 0;
        const total = quantity * cost;
        document.getElementById(`total_${index}`).innerHTML = `UGX ${total.toFixed(2)}`;
        updateTotals();
    }

    // Update all totals
    function updateTotals() {
        let subtotal = 0;
        const rows = document.querySelectorAll('#itemsBody .item-row');
        rows.forEach((row, idx) => {
            const quantity = parseFloat(document.getElementById(`qty_${idx}`).value) || 0;
            const cost = parseFloat(document.getElementById(`cost_${idx}`).value) || 0;
            subtotal += quantity * cost;
        });

        const vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;
        const vatAmount = (subtotal * vatRate) / 100;
        const total = subtotal + vatAmount;

        document.getElementById('subtotal_display').innerHTML = `UGX ${subtotal.toFixed(2)}`;
        document.getElementById('vat_rate_display').innerHTML = vatRate;
        document.getElementById('vat_amount_display').innerHTML = `UGX ${vatAmount.toFixed(2)}`;
        document.getElementById('total_display').innerHTML = `UGX ${total.toFixed(2)}`;
    }

    // Remove item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.item-row');
            row.remove();
            updateTotals();
        });
    });

    // VAT rate change
    document.getElementById('vat_rate').addEventListener('input', updateTotals);

    // Preview LPO
    function openPreview() {
        // Get form values
        const type = document.getElementById('type').value;
        const vendor = document.getElementById('vendor_id');
        const vendorName = vendor.options[vendor.selectedIndex]?.text || '—';
        const lpoDate = document.getElementById('lpo_date').value;
        const deliveryDate = document.getElementById('expected_delivery_date').value;
        const paymentMethod = document.getElementById('payment_method');
        const paymentMethodText = paymentMethod.options[paymentMethod.selectedIndex]?.text || '—';
        const deliveryAddress = document.getElementById('delivery_address').value || '—';
        const notes = document.getElementById('notes').value || '—';
        const vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;

        // Set preview values
        document.getElementById('previewType').innerHTML = `<span class="type-badge ${type === 'emergency' ? 'type-emergency' : 'type-normal'}">${type === 'emergency' ? 'EMERGENCY' : 'Normal'}</span>`;
        document.getElementById('previewVendor').innerText = vendorName;
        document.getElementById('previewLpoDate').innerText = lpoDate ? new Date(lpoDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';
        document.getElementById('previewDeliveryDate').innerText = deliveryDate ? new Date(deliveryDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';
        document.getElementById('previewPaymentMethod').innerText = paymentMethodText;
        document.getElementById('previewDeliveryAddress').innerText = deliveryAddress;
        document.getElementById('previewNotes').innerText = notes;
        document.getElementById('previewVatRate').innerText = vatRate;

        // Generate LPO number
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const random = Math.floor(Math.random() * 9999).toString().padStart(4, '0');
        document.getElementById('previewLpoNumber').innerText = `LPO-${year}${month}${day}-${random}`;

        // Build items preview
        let subtotal = 0;
        let itemsHtml = '';
        const rows = document.querySelectorAll('#itemsBody .item-row');

        rows.forEach((row, idx) => {
            const itemName = row.querySelector('td:first-child').innerText.split('\n')[0].trim();
            const metrics = row.querySelector('td:nth-child(2) span')?.innerText || '—';
            const quantity = parseFloat(document.getElementById(`qty_${idx}`).value) || 0;
            const cost = parseFloat(document.getElementById(`cost_${idx}`).value) || 0;
            const total = quantity * cost;
            subtotal += total;

            if (quantity > 0 && cost > 0) {
                itemsHtml += `
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-800">${itemName}</td>
                        <td class="px-4 py-2 text-sm text-gray-500">${metrics}</td>
                        <td class="px-4 py-2 text-sm text-right">${quantity.toFixed(2)}</td>
                        <td class="px-4 py-2 text-sm text-right">UGX ${cost.toFixed(2)}</td>
                        <td class="px-4 py-2 text-sm text-right">UGX ${total.toFixed(2)}</td>
                    </tr>
                `;
            }
        });

        document.getElementById('previewItemsBody').innerHTML = itemsHtml;

        const vatAmount = (subtotal * vatRate) / 100;
        const total = subtotal + vatAmount;

        document.getElementById('previewSubtotal').innerHTML = `UGX ${subtotal.toFixed(2)}`;
        document.getElementById('previewVatAmount').innerHTML = `UGX ${vatAmount.toFixed(2)}`;
        document.getElementById('previewTotal').innerHTML = `UGX ${total.toFixed(2)}`;

        // Amount in words (simplified)
        document.getElementById('previewAmountWords').innerHTML = `${total.toFixed(2)} Uganda Shillings Only`;

        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    function printLPO() {
        const printContents = document.getElementById('print-section').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>LPO Preview</title>
                <style>
                    body { padding: 20px; font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .company-logo, .print-logo { max-height: 40px !important; width: auto !important; }
                    .signature-img { max-height: 50px; max-width: 150px; }
                    @media print { body { margin: 0; padding: 20px; } }
                </style>
            </head>
            <body>${printContents}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    function submitLPO() {
        closePreview();
        document.getElementById('lpoForm').submit();
    }

    document.getElementById('previewBtn').addEventListener('click', openPreview);

    // Initialize totals
    document.querySelectorAll('#itemsBody .item-row').forEach((row, idx) => {
        calculateRowTotal(idx);
    });
</script>
@endsection
