@extends('layouts.procurement')
@section('title', 'Create Goods Received Note')
@section('page-title', 'Create Goods Received Note')

@section('content')
<style>
    .item-row { transition: background-color 0.2s ease; }
    .item-row:hover { background-color: #f8fafc; }
    .quantity-input { width: 100px; }
    .rejected-input { width: 100px; }
    .amount-display { font-weight: 600; color: #059669; }
    .amount-to-pay { font-weight: 700; color: #dc2626; font-size: 14px; }
    .summary-box { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; }
    .payment-summary { background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 16px; }
    input[readonly] { background-color: #f3f4f6; cursor: not-allowed; color: #6b7280; }
    .restore-banner { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
</style>

@if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
        {{ session('error') }}
    </div>
@endif

{{-- Restore banner (shown when saved data exists) --}}
<div id="restoreBanner" class="restore-banner hidden">
    <div class="flex items-center gap-2 text-blue-800 text-sm">
        <i class="fa fa-rotate-left text-blue-500"></i>
        <span>You have unsaved data from a previous session. It has been restored.</span>
    </div>
    <button type="button" onclick="clearSavedData()" class="text-xs text-red-500 hover:text-red-700 underline ml-4 whitespace-nowrap">
        <i class="fa fa-trash mr-1"></i>Clear saved data
    </button>
</div>

<div class="space-y-6">
    {{-- Step 1: Select Purchase Order --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">
                <i class="fa fa-search mr-2"></i>Step 1: Select Sent Purchase Order
            </h2>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block font-semibold mb-2 text-gray-700">Purchase Order</label>
                    <select id="purchase_order_id" class="form-select w-full border-gray-300 rounded-lg"
                            onchange="saveField('po_select', this.value)">
                        <option value="">-- Select PO --</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}">
                                {{ $po->po_number }} - {{ $po->vendor->name }} ({{ $po->created_at->format('Y-m-d') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" onclick="loadPoItems()"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <i class="fa fa-download"></i> Load Items
                </button>
            </div>
        </div>
    </div>

    {{-- Step 2: GRN Form --}}
    <form method="POST" action="{{ route('procurement.goods-received.store') }}" id="grnForm">
        @csrf
        <input type="hidden" name="purchase_order_id" id="po_id">

        <div id="poDetails" style="display: none;">
            {{-- Vendor and Delivery Info --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-800 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fa fa-truck mr-2"></i>Step 2: Receive Goods
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">
                                <i class="fa fa-building text-gray-400 mr-1"></i>Vendor
                            </label>
                            <input type="text" id="vendor_name" class="form-input w-full bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">
                                <i class="fa fa-hashtag text-gray-400 mr-1"></i>PO Number
                            </label>
                            <input type="text" id="po_number" class="form-input w-full bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">
                                <i class="fa fa-calendar-day text-gray-400 mr-1"></i>Received Date
                                <span class="text-xs text-gray-400 font-normal ml-1">(auto-set to today)</span>
                            </label>
                            <input type="date" name="received_date" id="received_date"
                                   class="form-input w-full border-gray-300 rounded-lg"
                                   readonly required>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">
                                <i class="fa fa-file-alt text-gray-400 mr-1"></i>Vendor Delivery Note Number
                            </label>
                            <input type="text" name="delivery_note_number" id="delivery_note_number"
                                   class="form-input w-full border-gray-300 rounded-lg"
                                   placeholder="Optional"
                                   oninput="saveField('delivery_note_number', this.value)">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold mb-2 text-gray-700">
                            <i class="fa fa-comment-dots text-gray-400 mr-1"></i>General Notes
                        </label>
                        <textarea name="notes" id="general_notes"
                                  class="form-textarea w-full border-gray-300 rounded-lg" rows="2"
                                  placeholder="Any additional notes..."
                                  oninput="saveField('general_notes', this.value)"></textarea>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-800 to-indigo-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fa fa-list-check mr-2"></i>Step 3: Enter Received Quantities
                    </h2>
                </div>

                {{-- Mobile card view --}}
                <div class="block lg:hidden p-4 space-y-4" id="mobileItemsBody">
                    <p class="text-center text-gray-500 py-4">Select a PO to load items</p>
                </div>

                {{-- Desktop table view --}}
                <div class="hidden lg:block p-6 overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-3 border text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="p-3 border text-center text-xs font-medium text-gray-500 uppercase">Ordered Qty</th>
                                <th class="p-3 border text-center text-xs font-medium text-gray-500 uppercase">Unit Cost (UGX)</th>
                                <th class="p-3 border text-center text-xs font-medium text-gray-500 uppercase">Ordered Amt (UGX)</th>
                                <th class="p-3 border text-center text-xs font-medium text-gray-500 uppercase">Qty Received</th>
                                <th class="p-3 border text-center text-xs font-medium text-gray-500 uppercase">Qty Rejected</th>
                                <th class="p-3 border text-center text-xs font-medium text-gray-500 uppercase">Amt to Pay (UGX)</th>
                                <th class="p-3 border text-left text-xs font-medium text-gray-500 uppercase">Rejection Reason</th>
                                <th class="p-3 border text-left text-xs font-medium text-gray-500 uppercase">Item Notes</th>
                            </tr>
                        </thead>
                        <tbody id="items-table-body">
                            <tr>
                                <td colspan="9" class="p-8 text-center text-gray-500">Select a PO to load items</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary Section --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6" id="summarySection" style="display: none;">
                <div class="bg-gradient-to-r from-purple-800 to-purple-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fa fa-chart-pie mr-2"></i>Summary
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="summary-box">
                            <h3 class="font-semibold text-gray-700 mb-3">
                                <i class="fa fa-file-invoice text-green-600 mr-1"></i>Purchase Order Summary
                            </h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">PO Total Amount:</span>
                                    <span class="font-bold text-gray-800" id="po_total_amount">UGX 0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Ordered Quantity:</span>
                                    <span class="font-bold text-gray-800" id="po_total_qty">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="payment-summary">
                            <h3 class="font-semibold text-gray-700 mb-3">
                                <i class="fa fa-coins text-yellow-600 mr-1"></i>Payment Summary (To be paid to Vendor)
                            </h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Amount to Pay:</span>
                                    <span class="font-bold text-red-600 text-xl" id="total_amount_to_pay">UGX 0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Received Quantity:</span>
                                    <span class="font-bold text-green-600" id="total_received_qty">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Rejected Quantity:</span>
                                    <span class="font-bold text-red-600" id="total_rejected_qty">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <button type="button" onclick="clearSavedData(true)"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center justify-center gap-2">
                    <i class="fa fa-xmark"></i> Cancel
                </button>
                <button type="button" onclick="showPreview()"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center gap-2">
                    <i class="fa fa-magnifying-glass"></i> Preview GRN
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center gap-2">
                    <i class="fa fa-check-circle"></i> Create Goods Received Note
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Preview Modal --}}
<div id="previewModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden overflow-y-auto">
    <div class="min-h-screen flex items-start justify-center py-6 px-4">
        <div class="w-full max-w-5xl bg-white rounded-lg shadow-xl">
            <div class="flex justify-between items-center px-5 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fa fa-file-circle-check text-green-600"></i>
                    Preview Goods Received Note
                </h3>
                <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="p-4 sm:p-5 space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 flex items-center gap-1"><i class="fa fa-hashtag"></i> PO Number</p>
                            <p class="font-medium text-sm" id="previewPONumber">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 flex items-center gap-1"><i class="fa fa-building"></i> Vendor</p>
                            <p class="font-medium text-sm" id="previewVendor">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 flex items-center gap-1"><i class="fa fa-calendar-day"></i> Received Date</p>
                            <p class="font-medium text-sm" id="previewReceivedDate">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 flex items-center gap-1"><i class="fa fa-file-alt"></i> Delivery Note #</p>
                            <p class="font-medium text-sm" id="previewDeliveryNote">—</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fa fa-boxes-stacked text-indigo-500"></i> Received Items
                    </h4>

                    {{-- Mobile preview cards --}}
                    <div class="block sm:hidden space-y-2" id="previewItemsMobile"></div>

                    {{-- Desktop preview table --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full border border-gray-200 rounded-lg text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-20">Ordered</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-20">Received</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-20">Rejected</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">Amt to Pay</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rejection Reason</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item Notes</th>
                                </tr>
                            </thead>
                            <tbody id="previewItemsBody"></tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-3 py-2 text-right font-bold text-sm">Total to Pay:</td>
                                    <td class="px-3 py-2 text-right font-bold text-green-600 text-sm whitespace-nowrap" id="previewTotalPay">UGX 0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Mobile grand total --}}
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200 sm:hidden">
                        <span class="text-sm font-bold text-gray-700">Total to Pay</span>
                        <span class="text-sm font-bold text-green-600" id="previewTotalPayMobile">UGX 0.00</span>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 flex items-center gap-1 mb-1">
                        <i class="fa fa-comment-dots"></i> General Notes
                    </p>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded" id="previewNotes">—</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 px-4 sm:px-5 py-4 border-t bg-gray-50 rounded-b-lg">
                <button type="button" onclick="closePreview()"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm flex items-center justify-center gap-2">
                    <i class="fa fa-pen-to-square"></i> Edit
                </button>
                <button type="button" onclick="submitForm()"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center justify-center gap-2">
                    <i class="fa fa-check-circle"></i> Confirm &amp; Create GRN
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ─────────────────────────────────────────────
// STORAGE HELPERS
// ─────────────────────────────────────────────
const STORAGE_KEY = 'grn_form_data';

function getSaved() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; } catch { return {}; }
}

function setSaved(data) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(data)); } catch(e) {}
}

function saveField(field, value) {
    const data = getSaved();
    data[field] = value;
    setSaved(data);
}

function saveItemField(index, field, value) {
    const data = getSaved();
    if (!data.items) data.items = {};
    if (!data.items[index]) data.items[index] = {};
    data.items[index][field] = value;
    setSaved(data);
}

function clearSavedData(redirect) {
    localStorage.removeItem(STORAGE_KEY);
    if (redirect) {
        window.location.href = "{{ route('procurement.goods-received.index') }}";
    } else {
        document.getElementById('restoreBanner').classList.add('hidden');
        // Reset the form UI
        document.getElementById('poDetails').style.display = 'none';
        document.getElementById('summarySection').style.display = 'none';
        document.getElementById('purchase_order_id').value = '';
        document.getElementById('items-table-body').innerHTML =
            '<tr><td colspan="9" class="p-8 text-center text-gray-500">Select a PO to load items</td></tr>';
        document.getElementById('mobileItemsBody').innerHTML =
            '<p class="text-center text-gray-500 py-4">Select a PO to load items</p>';
    }
}

// ─────────────────────────────────────────────
// DATE — always today, readonly
// ─────────────────────────────────────────────
function setTodayDate() {
    const today = new Date();
    const yyyy  = today.getFullYear();
    const mm    = String(today.getMonth() + 1).padStart(2, '0');
    const dd    = String(today.getDate()).padStart(2, '0');
    document.getElementById('received_date').value = `${yyyy}-${mm}-${dd}`;
}

// ─────────────────────────────────────────────
// LOAD PO ITEMS (from server + restore saved values)
// ─────────────────────────────────────────────
let poTotalAmount = 0;
let poTotalQty    = 0;

function loadPoItems(savedItemData) {
    const poId = document.getElementById('purchase_order_id').value;
    if (!poId) { alert('Please select a Purchase Order'); return; }

    fetch(`/procurement/goods-received/get-po-items/${poId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('poDetails').style.display = 'block';
            document.getElementById('summarySection').style.display = 'block';
            document.getElementById('po_id').value       = poId;
            document.getElementById('vendor_name').value = data.vendor_name;
            document.getElementById('po_number').value   = data.po_number;

            poTotalAmount = data.items.reduce((s, i) => s + i.quantity_ordered * i.unit_cost, 0);
            poTotalQty    = data.items.reduce((s, i) => s + parseFloat(i.quantity_ordered), 0);

            document.getElementById('po_total_amount').innerText =
                `UGX ${poTotalAmount.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('po_total_qty').innerText = poTotalQty.toLocaleString();

            let desktopHtml = '';
            let mobileHtml  = '';

            data.items.forEach((item, index) => {
                const orderedAmount  = item.quantity_ordered * item.unit_cost;
                // Restore saved item values if available
                const saved          = (savedItemData && savedItemData[index]) ? savedItemData[index] : {};
                const savedReceived  = saved.quantity_received  ?? 0;
                const savedRejected  = saved.quantity_rejected  ?? 0;
                const savedReason    = saved.rejection_reason   ?? '';
                const savedNotes     = saved.notes              ?? '';

                // Desktop row
                desktopHtml += `
                <tr class="item-row border-b" data-index="${index}">
                    <td class="p-3 border font-semibold text-sm">
                        ${item.item_name}
                        <input type="hidden" name="items[${index}][po_item_id]"       value="${item.id}">
                        <input type="hidden" name="items[${index}][unit_cost]"        value="${item.unit_cost}">
                        <input type="hidden" name="items[${index}][quantity_ordered]" value="${item.quantity_ordered}">
                    </td>
                    <td class="p-3 border text-center text-sm">${item.quantity_ordered}</td>
                    <td class="p-3 border text-center text-sm">UGX ${parseFloat(item.unit_cost).toLocaleString()}</td>
                    <td class="p-3 border text-center amount-display">UGX ${orderedAmount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td class="p-3 border text-center">
                        <input type="number" name="items[${index}][quantity_received]"
                               class="quantity-input form-input border-gray-300 rounded-lg text-center"
                               value="${savedReceived}" min="0" max="${item.quantity_to_receive}" step="0.01"
                               oninput="syncAndCalculate(${index}, ${item.unit_cost}, 'desktop')">
                    </td>
                    <td class="p-3 border text-center">
                        <input type="number" name="items[${index}][quantity_rejected]"
                               class="rejected-input form-input border-gray-300 rounded-lg text-center"
                               value="${savedRejected}" min="0" max="${item.quantity_to_receive}" step="0.01"
                               oninput="syncAndCalculate(${index}, ${item.unit_cost}, 'desktop')"
                               id="rejected_${index}">
                    </td>
                    <td class="p-3 border text-center amount-to-pay" id="amount_to_pay_${index}">UGX 0.00</td>
                    <td class="p-3 border">
                        <input type="text" name="items[${index}][rejection_reason]"
                               id="desktop_rejection_${index}"
                               class="form-input w-full border-gray-300 rounded-lg text-sm"
                               value="${escHtml(savedReason)}"
                               placeholder="Reason if rejected"
                               oninput="syncText(${index},'rejection','desktop'); saveItemField(${index},'rejection_reason',this.value)">
                    </td>
                    <td class="p-3 border">
                        <textarea name="items[${index}][notes]"
                                  id="desktop_notes_${index}"
                                  class="form-textarea w-full border-gray-300 rounded-lg text-sm"
                                  rows="2" placeholder="Item notes..."
                                  oninput="syncText(${index},'notes','desktop'); saveItemField(${index},'notes',this.value)">${escHtml(savedNotes)}</textarea>
                    </td>
                </tr>`;

                // Mobile card
                mobileHtml += `
                <div class="border border-gray-200 rounded-lg p-4 bg-white item-row" data-index="${index}">
                    <div class="mb-2">
                        <p class="text-sm font-semibold text-gray-800">${item.item_name}</p>
                        <div class="flex gap-2 text-xs text-gray-500 mt-0.5">
                            <span>Ordered: <strong>${item.quantity_ordered}</strong></span>
                            <span>•</span>
                            <span>Unit: <strong>UGX ${parseFloat(item.unit_cost).toLocaleString()}</strong></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">
                                <i class="fa fa-box-open mr-0.5"></i> Qty Received
                            </label>
                            <input type="number" id="mobile_received_${index}"
                                   class="mobile-received w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm"
                                   value="${savedReceived}" min="0" max="${item.quantity_to_receive}" step="0.01"
                                   oninput="syncAndCalculate(${index}, ${item.unit_cost}, 'mobile')">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">
                                <i class="fa fa-ban mr-0.5"></i> Qty Rejected
                            </label>
                            <input type="number" id="mobile_rejected_${index}"
                                   class="mobile-rejected w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm"
                                   value="${savedRejected}" min="0" max="${item.quantity_to_receive}" step="0.01"
                                   oninput="syncAndCalculate(${index}, ${item.unit_cost}, 'mobile')">
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-xs text-gray-500">Amount to Pay</span>
                        <span class="text-sm font-bold text-red-600" id="mobile_amount_to_pay_${index}">UGX 0.00</span>
                    </div>
                    <div class="mt-3 space-y-2">
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">
                                <i class="fa fa-triangle-exclamation mr-0.5"></i> Rejection Reason
                            </label>
                            <input type="text" id="mobile_rejection_${index}"
                                   class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-sm"
                                   value="${escHtml(savedReason)}"
                                   placeholder="Reason if rejected"
                                   oninput="syncText(${index},'rejection','mobile'); saveItemField(${index},'rejection_reason',this.value)">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">
                                <i class="fa fa-comment-dots mr-0.5"></i> Item Notes
                            </label>
                            <textarea id="mobile_notes_${index}"
                                      class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-sm"
                                      rows="2" placeholder="Item notes..."
                                      oninput="syncText(${index},'notes','mobile'); saveItemField(${index},'notes',this.value)">${escHtml(savedNotes)}</textarea>
                        </div>
                    </div>
                </div>`;
            });

            document.getElementById('items-table-body').innerHTML = desktopHtml;
            document.getElementById('mobileItemsBody').innerHTML  = mobileHtml;

            // Recalculate all rows after rendering
            data.items.forEach((item, index) => {
                calculateAmountToPay(index, item.unit_cost);
            });

            updatePaymentTotals();
        })
        .catch(err => { console.error(err); alert('Error loading PO items'); });
}

// ─────────────────────────────────────────────
// SYNC DESKTOP <-> MOBILE
// ─────────────────────────────────────────────
function syncAndCalculate(index, unitCost, source) {
    const dR = document.querySelector(`input[name="items[${index}][quantity_received]"]`);
    const dJ = document.querySelector(`input[name="items[${index}][quantity_rejected]"]`);
    const mR = document.getElementById(`mobile_received_${index}`);
    const mJ = document.getElementById(`mobile_rejected_${index}`);

    if (source === 'desktop') {
        if (mR) mR.value = dR?.value ?? 0;
        if (mJ) mJ.value = dJ?.value ?? 0;
    } else {
        if (dR) dR.value = mR?.value ?? 0;
        if (dJ) dJ.value = mJ?.value ?? 0;
    }

    // Persist
    saveItemField(index, 'quantity_received', (source === 'desktop' ? dR : mR)?.value ?? 0);
    saveItemField(index, 'quantity_rejected',  (source === 'desktop' ? dJ : mJ)?.value ?? 0);

    calculateAmountToPay(index, unitCost);
}

function syncText(index, field, source) {
    if (field === 'rejection') {
        const d = document.getElementById(`desktop_rejection_${index}`);
        const m = document.getElementById(`mobile_rejection_${index}`);
        if (source === 'desktop' && m) m.value = d?.value ?? '';
        if (source === 'mobile'  && d) d.value = m?.value ?? '';
    }
    if (field === 'notes') {
        const d = document.getElementById(`desktop_notes_${index}`);
        const m = document.getElementById(`mobile_notes_${index}`);
        if (source === 'desktop' && m) m.value = d?.value ?? '';
        if (source === 'mobile'  && d) d.value = m?.value ?? '';
    }
}

// ─────────────────────────────────────────────
// CALCULATIONS
// ─────────────────────────────────────────────
function calculateAmountToPay(index, unitCost) {
    const received     = parseFloat(document.querySelector(`input[name="items[${index}][quantity_received]"]`)?.value) || 0;
    const rejected     = parseFloat(document.querySelector(`input[name="items[${index}][quantity_rejected]"]`)?.value) || 0;
    const maxToReceive = parseFloat(document.querySelector(`input[name="items[${index}][quantity_received]"]`)?.max)   || 0;

    let accepted = received - rejected;
    if (accepted < 0)            accepted = 0;
    if (accepted > maxToReceive) accepted = maxToReceive;

    const amountToPay = accepted * unitCost;
    const formatted   = `UGX ${amountToPay.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

    const dCell = document.getElementById(`amount_to_pay_${index}`);
    const mCell = document.getElementById(`mobile_amount_to_pay_${index}`);
    if (dCell) dCell.innerText = formatted;
    if (mCell) mCell.innerText = formatted;

    // Hidden accepted qty
    let hiddenAccepted = document.querySelector(`input[name="items[${index}][quantity_accepted]"]`);
    if (!hiddenAccepted) {
        const container = document.querySelector(`input[name="items[${index}][quantity_received]"]`)?.closest('td');
        if (container) {
            hiddenAccepted      = document.createElement('input');
            hiddenAccepted.type = 'hidden';
            hiddenAccepted.name = `items[${index}][quantity_accepted]`;
            container.appendChild(hiddenAccepted);
        }
    }
    if (hiddenAccepted) hiddenAccepted.value = accepted;

    updatePaymentTotals();
}

function updatePaymentTotals() {
    let totalAmountToPay = 0;
    let totalReceivedQty = 0;
    let totalRejectedQty = 0;

    // Scope ONLY to desktop table rows — single source of truth
    const rows = document.querySelectorAll('#items-table-body .item-row');
    rows.forEach((row, idx) => {
        const received = parseFloat(row.querySelector(`input[name="items[${idx}][quantity_received]"]`)?.value || 0);
        const rejected = parseFloat(row.querySelector(`input[name="items[${idx}][quantity_rejected]"]`)?.value || 0);
        const unitCost = parseFloat(row.querySelector(`input[name="items[${idx}][unit_cost]"]`)?.value || 0);
        let accepted = received - rejected;
        if (accepted < 0) accepted = 0;
        totalAmountToPay += accepted * unitCost;
        totalReceivedQty += received;
        totalRejectedQty += rejected;
    });

    const fmt = n => `UGX ${n.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('total_amount_to_pay').innerText = fmt(totalAmountToPay);
    document.getElementById('total_received_qty').innerText  = totalReceivedQty.toLocaleString();
    document.getElementById('total_rejected_qty').innerText  = totalRejectedQty.toLocaleString();
}

// ─────────────────────────────────────────────
// PREVIEW
// ─────────────────────────────────────────────
function showPreview() {
    document.getElementById('previewPONumber').innerText     = document.getElementById('po_number').value || '—';
    document.getElementById('previewVendor').innerText       = document.getElementById('vendor_name').value || '—';
    document.getElementById('previewReceivedDate').innerText = document.getElementById('received_date').value || '—';
    document.getElementById('previewDeliveryNote').innerText = document.getElementById('delivery_note_number').value || '—';
    document.getElementById('previewNotes').innerText        = document.getElementById('general_notes').value || '—';

    const previewBody   = document.getElementById('previewItemsBody');
    const previewMobile = document.getElementById('previewItemsMobile');
    previewBody.innerHTML   = '';
    previewMobile.innerHTML = '';
    let totalToPay = 0;

    const rows = document.querySelectorAll('#items-table-body .item-row');
    rows.forEach((row, idx) => {
        const itemName        = row.querySelector('td:first-child')?.childNodes[0]?.textContent?.trim() || 'Item';
        const orderedQty      = parseFloat(row.querySelector(`input[name="items[${idx}][quantity_ordered]"]`)?.value || 0);
        const receivedQty     = parseFloat(row.querySelector(`input[name="items[${idx}][quantity_received]"]`)?.value || 0);
        const rejectedQty     = parseFloat(row.querySelector(`input[name="items[${idx}][quantity_rejected]"]`)?.value || 0);
        const unitCost        = parseFloat(row.querySelector(`input[name="items[${idx}][unit_cost]"]`)?.value || 0);
        const rejectionReason = document.getElementById(`desktop_rejection_${idx}`)?.value?.trim() || '—';
        const itemNotes       = document.getElementById(`desktop_notes_${idx}`)?.value?.trim() || '—';
        const accepted        = Math.max(0, receivedQty - rejectedQty);
        const amountToPay     = accepted * unitCost;
        totalToPay           += amountToPay;

        const tr = document.createElement('tr');
        tr.className = 'border-b';
        tr.innerHTML = `
            <td class="px-3 py-2 text-sm font-medium">${itemName}</td>
            <td class="px-3 py-2 text-sm text-right">${orderedQty.toFixed(2)}</td>
            <td class="px-3 py-2 text-sm text-right">${receivedQty.toFixed(2)}</td>
            <td class="px-3 py-2 text-sm text-right">${rejectedQty.toFixed(2)}</td>
            <td class="px-3 py-2 text-sm text-right whitespace-nowrap text-green-700 font-semibold">UGX ${amountToPay.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            <td class="px-3 py-2 text-sm text-gray-600">${rejectionReason}</td>
            <td class="px-3 py-2 text-sm text-gray-600">${itemNotes}</td>
        `;
        previewBody.appendChild(tr);

        const card = document.createElement('div');
        card.className = 'border border-gray-200 rounded-lg p-3 bg-white';
        card.innerHTML = `
            <p class="text-sm font-semibold text-gray-800 mb-1">${itemName}</p>
            <div class="grid grid-cols-3 gap-1 text-xs text-gray-600 mb-2">
                <div><span class="text-gray-400">Ordered</span><br><strong>${orderedQty.toFixed(2)}</strong></div>
                <div><span class="text-gray-400">Received</span><br><strong>${receivedQty.toFixed(2)}</strong></div>
                <div><span class="text-gray-400">Rejected</span><br><strong>${rejectedQty.toFixed(2)}</strong></div>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-gray-100 mb-1">
                <span class="text-xs text-gray-500">Amount to Pay</span>
                <span class="text-sm font-bold text-green-700">UGX ${amountToPay.toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
            </div>
            ${rejectionReason !== '—' ? `<p class="text-xs text-red-600 mt-1"><i class="fa fa-triangle-exclamation mr-0.5"></i> ${rejectionReason}</p>` : ''}
            ${itemNotes !== '—' ? `<p class="text-xs text-gray-500 mt-1"><i class="fa fa-comment-dots mr-0.5"></i> ${itemNotes}</p>` : ''}
        `;
        previewMobile.appendChild(card);
    });

    const formatted = `UGX ${totalToPay.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
    document.getElementById('previewTotalPay').innerText       = formatted;
    document.getElementById('previewTotalPayMobile').innerText = formatted;
    document.getElementById('previewModal').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}

function submitForm() {
    // Clear storage on successful submit so data doesn't restore on next visit
    localStorage.removeItem(STORAGE_KEY);
    document.getElementById('grnForm').submit();
}

// ─────────────────────────────────────────────
// UTILITY
// ─────────────────────────────────────────────
function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/"/g,'&quot;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;');
}

// ─────────────────────────────────────────────
// ON PAGE LOAD — restore saved data
// ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    setTodayDate();

    const saved = getSaved();

    // Restore PO select
    if (saved.po_select) {
        const sel = document.getElementById('purchase_order_id');
        sel.value = saved.po_select;
        if (sel.value) {
            // Show restore banner
            document.getElementById('restoreBanner').classList.remove('hidden');
            // Re-load PO items and restore item-level data
            loadPoItems(saved.items || {});
        }
    }

    // Restore top-level fields (after a tick so they're not overwritten)
    setTimeout(() => {
        if (saved.delivery_note_number) {
            document.getElementById('delivery_note_number').value = saved.delivery_note_number;
        }
        if (saved.general_notes) {
            document.getElementById('general_notes').value = saved.general_notes;
        }
        // Always keep received_date as today regardless of saved value
        setTodayDate();
    }, 600);
});
</script>
@endsection
