@extends('layouts.procurement')
@section('title', 'Create Goods Received Note')
@section('page-title', 'Create Goods Received Note')

@section('content')
<style>
    .item-row {
        transition: background-color 0.2s ease;
    }
    .item-row:hover {
        background-color: #f8fafc;
    }
    .quantity-input {
        width: 100px;
    }
    .rejected-input {
        width: 100px;
    }
    .amount-display {
        font-weight: 600;
        color: #059669;
    }
    .amount-to-pay {
        font-weight: 700;
        color: #dc2626;
        font-size: 14px;
    }
    .summary-box {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 16px;
    }
    .payment-summary {
        background-color: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 16px;
    }
</style>

@if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
        {{ session('error') }}
    </div>
@endif

<div class="space-y-6">
    {{-- Step 1: Select Purchase Order --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Step 1: Select Sent Purchase Order</h2>
        </div>
        <div class="p-6">
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block font-semibold mb-2 text-gray-700">Purchase Order</label>
                    <select id="purchase_order_id" class="form-select w-full border-gray-300 rounded-lg">
                        <option value="">-- Select PO --</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}">
                                {{ $po->po_number }} - {{ $po->vendor->name }} ({{ $po->created_at->format('Y-m-d') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" onclick="loadPoItems()" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Load Items
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
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-800 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Step 2: Receive Goods</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">Vendor</label>
                            <input type="text" id="vendor_name" class="form-input w-full bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">PO Number</label>
                            <input type="text" id="po_number" class="form-input w-full bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">Received Date</label>
                            <input type="date" name="received_date" class="form-input w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">Vendor Delivery Note Number</label>
                            <input type="text" name="delivery_note_number" class="form-input w-full border-gray-300 rounded-lg" 
                                   placeholder="Optional">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block font-semibold mb-2 text-gray-700">General Notes</label>
                        <textarea name="notes" class="form-textarea w-full border-gray-300 rounded-lg" rows="2" 
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-800 to-indigo-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Step 3: Enter Received Quantities</h2>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-3 border text-left">Item</th>
                                <th class="p-3 border text-center">Ordered Qty</th>
                                <th class="p-3 border text-center">Unit Cost (UGX)</th>
                                <th class="p-3 border text-center">Ordered Amount (UGX)</th>
                                <th class="p-3 border text-center">Previously Received</th>
                                <th class="p-3 border text-center">To Receive</th>
                                <th class="p-3 border text-center">Qty Accepted</th>
                                <th class="p-3 border text-center">Qty Rejected</th>
                                <th class="p-3 border text-center">Amount to Pay (UGX)</th>
                                <th class="p-3 border text-left">Rejection Reason</th>
                                <th class="p-3 border text-left">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="items-table-body">
                            <tr>
                                <td colspan="11" class="p-8 text-center text-gray-500">Select a PO to load items</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary Section --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden" id="summarySection" style="display: none;">
                <div class="bg-gradient-to-r from-purple-800 to-purple-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Summary</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="summary-box">
                            <h3 class="font-semibold text-gray-700 mb-3">Purchase Order Summary</h3>
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
                            <h3 class="font-semibold text-gray-700 mb-3">💰 Payment Summary (To be paid to Vendor)</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Amount to Pay:</span>
                                    <span class="font-bold text-red-600 text-xl" id="total_amount_to_pay">UGX 0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Accepted Quantity:</span>
                                    <span class="font-bold text-green-600" id="total_accepted_qty">0</span>
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

            {{-- Submit Button --}}
            <div class="flex justify-end space-x-4">
                <a href="{{ route('procurement.goods-received.index') }}" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Create Goods Received Note
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let poTotalAmount = 0;
let poTotalQty = 0;

function loadPoItems() {
    const poId = document.getElementById('purchase_order_id').value;
    if (!poId) {
        alert('Please select a Purchase Order');
        return;
    }

    fetch(`/procurement/goods-received/get-po-items/${poId}`)
        .then(response => response.json())
        .then(data => {
            // Show the form
            document.getElementById('poDetails').style.display = 'block';
            document.getElementById('summarySection').style.display = 'block';
            document.getElementById('po_id').value = poId;
            document.getElementById('vendor_name').value = data.vendor_name;
            document.getElementById('po_number').value = data.po_number;

            // Calculate PO totals
            poTotalAmount = data.items.reduce((sum, item) => sum + (item.quantity_ordered * item.unit_cost), 0);
            poTotalQty = data.items.reduce((sum, item) => sum + parseFloat(item.quantity_ordered), 0);
            
            document.getElementById('po_total_amount').innerText = `UGX ${poTotalAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('po_total_qty').innerText = poTotalQty.toLocaleString();

            // Reset payment totals
            updatePaymentTotals();

            // Build items table
            let html = '';
            data.items.forEach((item, index) => {
                const orderedAmount = item.quantity_ordered * item.unit_cost;
                html += `
                    <tr class="item-row border-b" data-index="${index}">
                        <td class="p-3 border font-semibold">
                            ${item.item_name}
                            <input type="hidden" name="items[${index}][po_item_id]" value="${item.id}">
                            <input type="hidden" name="items[${index}][unit_cost]" value="${item.unit_cost}">
                            <input type="hidden" name="items[${index}][quantity_ordered]" value="${item.quantity_ordered}">
                        </td>
                        <td class="p-3 border text-center">${item.quantity_ordered}</td>
                        <td class="p-3 border text-center">UGX ${parseFloat(item.unit_cost).toLocaleString()}</td>
                        <td class="p-3 border text-center amount-display">UGX ${orderedAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="p-3 border text-center">${item.quantity_received_so_far}</td>
                        <td class="p-3 border text-center font-semibold text-blue-600">${item.quantity_to_receive}</td>
                        <td class="p-3 border text-center">
                            <input type="number" name="items[${index}][quantity_received]" 
                                   class="quantity-input form-input border-gray-300 rounded-lg text-center"
                                   value="${item.quantity_to_receive}" min="0" max="${item.quantity_to_receive}" step="0.01"
                                   oninput="calculateAmountToPay(${index}, ${item.unit_cost})">
                        </td>
                        <td class="p-3 border text-center">
                            <input type="number" name="items[${index}][quantity_rejected]" 
                                   class="rejected-input form-input border-gray-300 rounded-lg text-center"
                                   value="0" min="0" max="${item.quantity_to_receive}" step="0.01"
                                   oninput="calculateAmountToPay(${index}, ${item.unit_cost})"
                                   id="rejected_${index}">
                        </td>
                        <td class="p-3 border text-center amount-to-pay" id="amount_to_pay_${index}">
                            UGX 0.00
                        </td>
                        <td class="p-3 border">
                            <input type="text" name="items[${index}][rejection_reason]" 
                                   class="form-input w-full border-gray-300 rounded-lg"
                                   placeholder="Reason if rejected">
                        </td>
                        <td class="p-3 border">
                            <textarea name="items[${index}][notes]" 
                                      class="form-textarea w-full border-gray-300 rounded-lg"
                                      rows="2" placeholder="Item notes..."></textarea>
                        </td>
                    </tr>
                `;
            });
            document.getElementById('items-table-body').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading PO items');
        });
}

function calculateAmountToPay(index, unitCost) {
    const received = parseFloat(document.querySelector(`input[name="items[${index}][quantity_received]"]`).value) || 0;
    const rejected = parseFloat(document.querySelector(`input[name="items[${index}][quantity_rejected]"]`).value) || 0;
    const maxToReceive = parseFloat(document.querySelector(`input[name="items[${index}][quantity_received]"]`).max) || 0;
    
    // Calculate accepted quantity (received - rejected)
    let accepted = received - rejected;
    if (accepted < 0) accepted = 0;
    if (accepted > maxToReceive) accepted = maxToReceive;
    
    // Calculate amount to pay (accepted × unit cost)
    const amountToPay = accepted * unitCost;
    
    // Update display
    document.getElementById(`amount_to_pay_${index}`).innerText = `UGX ${amountToPay.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    
    // Update hidden accepted quantity field if needed
    if (!document.querySelector(`input[name="items[${index}][quantity_accepted]"]`)) {
        const container = document.querySelector(`input[name="items[${index}][quantity_received]"]`).closest('td');
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `items[${index}][quantity_accepted]`;
        hiddenInput.value = accepted;
        container.appendChild(hiddenInput);
    } else {
        document.querySelector(`input[name="items[${index}][quantity_accepted]"]`).value = accepted;
    }
    
    // Update payment totals
    updatePaymentTotals();
}

function updatePaymentTotals() {
    let totalAmountToPay = 0;
    let totalAcceptedQty = 0;
    let totalRejectedQty = 0;
    
    const rows = document.querySelectorAll('.item-row');
    rows.forEach((row, idx) => {
        const received = parseFloat(row.querySelector(`input[name="items[${idx}][quantity_received]"]`)?.value || 0);
        const rejected = parseFloat(row.querySelector(`input[name="items[${idx}][quantity_rejected]"]`)?.value || 0);
        const unitCost = parseFloat(row.querySelector(`input[name="items[${idx}][unit_cost]"]`)?.value || 0);
        
        let accepted = received - rejected;
        if (accepted < 0) accepted = 0;
        
        totalAmountToPay += accepted * unitCost;
        totalAcceptedQty += accepted;
        totalRejectedQty += rejected;
    });
    
    document.getElementById('total_amount_to_pay').innerText = `UGX ${totalAmountToPay.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('total_accepted_qty').innerText = totalAcceptedQty.toLocaleString();
    document.getElementById('total_rejected_qty').innerText = totalRejectedQty.toLocaleString();
}
</script>
@endsection