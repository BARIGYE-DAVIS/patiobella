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
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Convert LPO #{{ $lpo->lpo_number }} to External PO</h3>
        <p class="text-sm text-gray-500">Review and edit items based on Director's notes, then convert to External PO</p>
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

        <div class="p-6 space-y-6">
            {{-- Info Box --}}
            <div class="info-box p-4 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Note:</strong> You can edit quantities and unit costs based on Director's notes.
                    Click Preview to review before sending to vendor.
                </p>
            </div>

            {{-- LPO Information (Read Only) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LPO Number</label>
                    <input type="text" value="{{ $lpo->lpo_number }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                    <input type="text" value="{{ $lpo->vendor->name ?? 'N/A' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Email</label>
                    <input type="text" value="{{ $lpo->vendor->email ?? 'N/A' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total LPO Amount</label>
                    <input type="text" id="lpoTotalAmount" value="UGX {{ number_format($lpo->total_amount, 2) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
            </div>

            {{-- External PO Details --}}
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-md font-semibold text-gray-800 mb-4">External Purchase Order Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Date <span class="text-red-500">*</span></label>
                        <input type="date" name="po_date" id="po_date" value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                        <input type="date" name="delivery_date" id="delivery_date" value="{{ $lpo->expected_delivery_date ? date('Y-m-d', strtotime($lpo->expected_delivery_date)) : '' }}"
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
                        <input type="text" name="delivery_terms" id="delivery_terms"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., FOB, CIF, Ex-Works">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (for vendor)</label>
                    <textarea name="notes" id="po_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Any additional notes to include in the purchase order..."></textarea>
                </div>
            </div>

            {{-- Editable Items Table --}}
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-md font-semibold text-gray-800 mb-3">Items <span class="text-xs text-gray-500">(Editable - click to modify)</span></h4>
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
                                           value="{{ $item->quantity_approved }}" step="0.01" min="0"
                                           onchange="updateTotals()">
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <input type="number" name="items[{{ $index }}][unit_cost]"
                                           class="item-cost editable-input w-28 px-2 py-1 border border-gray-300 rounded-lg text-right"
                                           value="{{ $item->unit_cost }}" step="0.01" min="0"
                                           onchange="updateTotals()">
                                </td>
                                <td class="px-4 py-3 text-right font-semibold item-total text-green-600">
                                    UGX {{ number_format($item->quantity_approved * $item->unit_cost, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm font-bold text-gray-700">TOTALS</td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right" id="totalQuantity">
                                    {{ number_format($lpo->items->sum('quantity_approved'), 2) }}
                                </td>
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-sm font-bold text-green-700 text-right" id="grandTotal">
                                    UGX {{ number_format($lpo->total_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <a href="{{ route('procurement.approved-lpos.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="button" onclick="showPreview()" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                Preview External PO
            </button>
        </div>
    </form>
</div>

{{-- Preview Modal --}}
<div id="previewModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4 pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Preview External Purchase Order</h3>
            <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">PO Number</p>
                        <p class="font-medium" id="previewPONumber">PO-XXXXXX</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">PO Date</p>
                        <p class="font-medium" id="previewPODate">—</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Vendor</p>
                        <p class="font-medium" id="previewVendor">—</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Delivery Date</p>
                        <p class="font-medium" id="previewDeliveryDate">—</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Delivery Address</p>
                        <p class="font-medium" id="previewDeliveryAddress">—</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Delivery Terms</p>
                        <p class="font-medium" id="previewDeliveryTerms">—</p>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Items</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Item</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 w-24">Quantity</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 w-28">Unit Cost (UGX)</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 w-32">Total (UGX)</th>
                            </tr>
                        </thead>
                        <tbody id="previewItemsBody"></tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-bold">Grand Total:</td>
                                <td class="px-4 py-2 text-right font-bold text-green-600" id="previewGrandTotal">UGX 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-500">Additional Notes</p>
                <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded" id="previewNotes">—</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
            <button type="button" onclick="closePreview()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Edit
            </button>
            <button type="button" onclick="submitForm()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Confirm & Send to Vendor
            </button>
        </div>
    </div>
</div>

<script>
    function updateTotals() {
        let totalQuantity = 0;
        let grandTotal = 0;

        document.querySelectorAll('#itemsBody tr').forEach(row => {
            const quantity = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
            const unitCost = parseFloat(row.querySelector('.item-cost')?.value) || 0;
            const total = quantity * unitCost;

            totalQuantity += quantity;
            grandTotal += total;

            const totalCell = row.querySelector('.item-total');
            if (totalCell) {
                totalCell.innerText = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
        });

        document.getElementById('totalQuantity').innerText = totalQuantity.toFixed(2);
        document.getElementById('grandTotal').innerText = `UGX ${grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    function showPreview() {
        // Generate preview PO number
        const today = new Date();
        const poNumber = `PO-${today.getFullYear()}${String(today.getMonth()+1).padStart(2,'0')}${String(today.getDate()).padStart(2,'0')}-${Math.floor(Math.random() * 10000)}`;

        document.getElementById('previewPONumber').innerText = poNumber;
        document.getElementById('previewPODate').innerText = document.getElementById('po_date').value || 'Not specified';
        document.getElementById('previewVendor').innerText = '{{ $lpo->vendor->name ?? 'N/A' }}';
        document.getElementById('previewDeliveryDate').innerText = document.getElementById('delivery_date').value || 'Not specified';
        document.getElementById('previewDeliveryAddress').innerText = document.getElementById('delivery_address').value || 'Not specified';
        document.getElementById('previewDeliveryTerms').innerText = document.getElementById('delivery_terms').value || 'Not specified';
        document.getElementById('previewNotes').innerText = document.getElementById('po_notes').value || '—';

        // Build items preview
        const previewBody = document.getElementById('previewItemsBody');
        previewBody.innerHTML = '';
        let grandTotal = 0;

        document.querySelectorAll('#itemsBody tr').forEach(row => {
            const itemName = row.querySelector('td:first-child')?.innerText.split('\n')[0] || 'Item';
            const quantity = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
            const unitCost = parseFloat(row.querySelector('.item-cost')?.value) || 0;
            const total = quantity * unitCost;
            grandTotal += total;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2 text-sm">${itemName}</td>
                <td class="px-4 py-2 text-sm text-right">${quantity.toFixed(2)}</td>
                <td class="px-4 py-2 text-sm text-right">UGX ${unitCost.toLocaleString()}</td>
                <td class="px-4 py-2 text-sm text-right">UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            `;
            previewBody.appendChild(tr);
        });

        document.getElementById('previewGrandTotal').innerText = `UGX ${grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    function submitForm() {
        document.getElementById('epoForm').submit();
    }

    // Attach event listeners
    document.querySelectorAll('.item-quantity, .item-cost').forEach(input => {
        input.addEventListener('input', updateTotals);
    });
    updateTotals();
</script>
@endsection
