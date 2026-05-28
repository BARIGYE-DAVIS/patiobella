<style>
    .amount-to-pay {
        font-weight: 600;
        color: #dc2626;
        font-size: 12px;
    }
    .summary-box {
        background-color: #fefce8;
        border: 1px solid #fde68a;
        border-radius: 6px;
        padding: 10px;
    }
    .payment-summary {
        background-color: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 6px;
        padding: 10px;
    }
    .back-btn:hover {
        background-color: #f3f4f6;
    }
</style>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-green-700 to-green-600 px-4 py-2 flex justify-between items-center">
        <h2 class="text-sm font-semibold text-white">
            <i class="fa fa-truck mr-2 text-xs"></i>Step 2: Receive Goods for PO #{{ $po->po_number }}
        </h2>
        <button type="button" onclick="backToPoList()" class="back-btn px-2 py-1 bg-white text-gray-700 rounded text-xs hover:bg-gray-100">
            <i class="fa fa-arrow-left mr-1 text-xs"></i> Back to PO List
        </button>
    </div>

    <div class="p-3">
        <form method="POST" action="{{ route('procurement.goods-received.store') }}" id="grnForm">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <div>
                    <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Vendor</label>
                    <input type="text" value="{{ $po->vendor->name }}" class="w-full px-2 py-1 border border-gray-200 rounded bg-gray-50 text-xs" readonly>
                </div>
                <div>
                    <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">PO Number</label>
                    <input type="text" value="{{ $po->po_number }}" class="w-full px-2 py-1 border border-gray-200 rounded bg-gray-50 text-xs" readonly>
                </div>
                <div>
                    <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Received Date</label>
                    <input type="date" name="received_date" value="{{ date('Y-m-d') }}" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" readonly required>
                </div>
                <div>
                    <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Delivery Note #</label>
                    <input type="text" name="delivery_note_number" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" placeholder="Optional">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Notes</label>
                    <textarea name="notes" rows="1" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" placeholder="General notes..."></textarea>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border-b p-1.5 text-left">Item</th>
                            <th class="border-b p-1.5 text-center w-16">Ordered</th>
                            <th class="border-b p-1.5 text-right w-24">Unit Cost</th>
                            <th class="border-b p-1.5 text-right w-28">Ordered Amt</th>
                            <th class="border-b p-1.5 text-center w-24">Received</th>
                            <th class="border-b p-1.5 text-center w-20">Rejected</th>
                            <th class="border-b p-1.5 text-right w-28">Amount to Pay</th>
                            <th class="border-b p-1.5 text-left w-32">Rejection Reason</th>
                        </tr>
                    </thead>
                    <tbody id="items-table-body">
                        @foreach($po->items as $index => $item)
                            @php
                                $remainingToReceive = $item->quantity_ordered - ($item->quantity_received ?? 0);
                            @endphp
                            <tr class="item-row" data-index="{{ $index }}">
                                <td class="p-1.5 border-b">
                                    {{ $item->inventoryItem->name ?? 'Unknown Item' }}
                                    <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[{{ $index }}][unit_cost]" value="{{ $item->unit_cost }}">
                                    <input type="hidden" name="items[{{ $index }}][quantity_ordered]" value="{{ $item->quantity_ordered }}">
                                </td>
                                <td class="p-1.5 border-b text-center">{{ $item->quantity_ordered }}</td>
                                <td class="p-1.5 border-b text-right">{{ number_format($item->unit_cost, 2) }}</td>
                                <td class="p-1.5 border-b text-right">UGX {{ number_format($item->quantity_ordered * $item->unit_cost, 2) }}</td>
                                <td class="p-1.5 border-b text-center">
                                    <input type="number" class="received-input w-20 px-1 py-0.5 border border-gray-300 rounded text-center text-xs"
                                           data-index="{{ $index }}"
                                           value="0" min="0" max="{{ $remainingToReceive }}" step="0.01">
                                </td>
                                <td class="p-1.5 border-b text-center">
                                    <input type="number" class="rejected-input w-16 px-1 py-0.5 border border-gray-300 rounded text-center text-xs"
                                           data-index="{{ $index }}"
                                           value="0" min="0" max="{{ $remainingToReceive }}" step="0.01">
                                </td>
                                <td class="p-1.5 border-b text-right amount-to-pay" data-index="{{ $index }}">UGX 0.00</td>
                                <td class="p-1.5 border-b">
                                    <input type="text" name="items[{{ $index }}][rejection_reason]" class="w-full px-1 py-0.5 border border-gray-300 rounded text-xs" placeholder="Reason if rejected">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="6" class="p-1.5 text-right font-semibold">Total to Pay:</td>
                            <td class="p-1.5 text-right font-bold text-green-700" id="total_amount_to_pay">UGX 0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                <div class="summary-box">
                    <h3 class="font-semibold text-gray-700 mb-1 text-xs">Purchase Order Summary</h3>
                    <div class="flex justify-between text-xs">
                        <span>PO Total Amount:</span>
                        <span class="font-bold">UGX {{ number_format($po->total_amount, 2) }}</span>
                    </div>
                </div>
                <div class="payment-summary">
                    <h3 class="font-semibold text-gray-700 mb-1 text-xs">Payment Summary</h3>
                    <div class="flex justify-between text-xs">
                        <span>Total to Pay Vendor:</span>
                        <span class="font-bold text-red-600" id="summary_total_pay">UGX 0.00</span>
                    </div>
                    <div class="flex justify-between text-xs mt-0.5">
                        <span>Total Received:</span>
                        <span class="font-bold text-green-600" id="summary_total_received">0</span>
                    </div>
                    <div class="flex justify-between text-xs mt-0.5">
                        <span>Total Rejected:</span>
                        <span class="font-bold text-red-600" id="summary_total_rejected">0</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-xs flex items-center gap-1">
                    <i class="fa fa-save text-xs"></i> Create GRN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    // Function to calculate single row amount
    function calculateRowAmount(index) {
        const receivedInput = document.querySelector(`.received-input[data-index="${index}"]`);
        const rejectedInput = document.querySelector(`.rejected-input[data-index="${index}"]`);
        const unitCostInput = document.querySelector(`input[name="items[${index}][unit_cost]"]`);

        if (!receivedInput || !rejectedInput || !unitCostInput) return;

        let received = parseFloat(receivedInput.value) || 0;
        let rejected = parseFloat(rejectedInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;

        const maxReceive = parseFloat(receivedInput.getAttribute('max')) || 0;
        if (received > maxReceive) {
            received = maxReceive;
            receivedInput.value = maxReceive;
        }

        if (rejected > received) {
            rejected = received;
            rejectedInput.value = received;
        }

        const accepted = received - rejected;
        const amountToPay = accepted * unitCost;

        const amountCell = document.querySelector(`.amount-to-pay[data-index="${index}"]`);
        if (amountCell) {
            amountCell.innerText = `UGX ${amountToPay.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        }

        // Create hidden input for accepted quantity
        let hiddenAccepted = document.querySelector(`input[name="items[${index}][quantity_accepted]"]`);
        if (!hiddenAccepted) {
            hiddenAccepted = document.createElement('input');
            hiddenAccepted.type = 'hidden';
            hiddenAccepted.name = `items[${index}][quantity_accepted]`;
            receivedInput.closest('td').appendChild(hiddenAccepted);
        }
        hiddenAccepted.value = accepted;

        // Create hidden inputs for received and rejected
        let hiddenReceived = document.querySelector(`input[name="items[${index}][quantity_received]"]`);
        if (!hiddenReceived) {
            hiddenReceived = document.createElement('input');
            hiddenReceived.type = 'hidden';
            hiddenReceived.name = `items[${index}][quantity_received]`;
            receivedInput.closest('td').appendChild(hiddenReceived);
        }
        hiddenReceived.value = received;

        let hiddenRejected = document.querySelector(`input[name="items[${index}][quantity_rejected]"]`);
        if (!hiddenRejected) {
            hiddenRejected = document.createElement('input');
            hiddenRejected.type = 'hidden';
            hiddenRejected.name = `items[${index}][quantity_rejected]`;
            rejectedInput.closest('td').appendChild(hiddenRejected);
        }
        hiddenRejected.value = rejected;

        updateTotals();
    }

    // Function to update all totals
    function updateTotals() {
        let totalAmount = 0;
        let totalReceived = 0;
        let totalRejected = 0;

        document.querySelectorAll('.item-row').forEach((row, idx) => {
            const receivedInput = row.querySelector(`.received-input[data-index="${idx}"]`);
            const rejectedInput = row.querySelector(`.rejected-input[data-index="${idx}"]`);
            const unitCostInput = document.querySelector(`input[name="items[${idx}][unit_cost]"]`);

            if (receivedInput && rejectedInput && unitCostInput) {
                const received = parseFloat(receivedInput.value) || 0;
                const rejected = parseFloat(rejectedInput.value) || 0;
                const unitCost = parseFloat(unitCostInput.value) || 0;
                const accepted = Math.max(0, received - rejected);

                totalAmount += accepted * unitCost;
                totalReceived += received;
                totalRejected += rejected;
            }
        });

        const totalAmountCell = document.getElementById('total_amount_to_pay');
        const summaryTotalPay = document.getElementById('summary_total_pay');
        const summaryReceived = document.getElementById('summary_total_received');
        const summaryRejected = document.getElementById('summary_total_rejected');

        if (totalAmountCell) totalAmountCell.innerHTML = `UGX ${totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        if (summaryTotalPay) summaryTotalPay.innerHTML = `UGX ${totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        if (summaryReceived) summaryReceived.innerHTML = totalReceived;
        if (summaryRejected) summaryRejected.innerHTML = totalRejected;
    }

    // Attach event listeners to all inputs
    function attachEventListeners() {
        document.querySelectorAll('.received-input, .rejected-input').forEach(input => {
            input.removeEventListener('input', input._handler);
            input._handler = function() {
                const index = this.getAttribute('data-index');
                calculateRowAmount(parseInt(index));
            };
            input.addEventListener('input', input._handler);
        });
    }

    // Initialize
    function init() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((_, idx) => {
            calculateRowAmount(idx);
        });
        attachEventListeners();
    }

    // Run init when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Make back function global
    window.backToPoList = function() {
        const container = document.getElementById('grnFormContainer');
        if (container) {
            container.innerHTML = '';
            container.classList.add('hidden');
        }
        const poContainer = document.getElementById('poTableContainer');
        if (poContainer) {
            poContainer.classList.remove('hidden');
        }
        if (typeof window.loadPurchaseOrders === 'function' && typeof window.currentPage !== 'undefined') {
            window.loadPurchaseOrders(window.currentPage);
        }
    };
})();
</script>
