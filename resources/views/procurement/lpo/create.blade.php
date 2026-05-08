@extends('layouts.procurement')

@section('title', 'Create Local Purchase Order')

@section('page-title', 'Create Local Purchase Order (LPO)')

@section('content')
<style>
    .item-row:hover {
        background-color: #f9fafb;
    }
    .total-row {
        background-color: #f0fdf4;
    }
    .info-box {
        background-color: #eff6ff;
        border-left: 4px solid #3b82f6;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Create Local Purchase Order (LPO)</h3>
        <p class="text-sm text-gray-500">Requisition #{{ $requisition->requisition_number }} | Store: {{ $requisition->store->name ?? 'N/A' }}</p>
    </div>

    <div class="p-4 mx-6 mt-4 rounded-lg info-box">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-blue-800">This LPO will be sent to Director for approval before being issued to the vendor.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('procurement.lpo.store') }}" id="lpoForm">
        @csrf
        <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">

        <div class="p-6 space-y-6">
            {{-- LPO Header Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LPO Date <span class="text-red-500">*</span></label>
                    <input type="date" name="lpo_date" value="{{ date('Y-m-d') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <select name="vendor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Select Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }} - {{ $vendor->contact_person ?? '' }} ({{ $vendor->phone ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                    <input type="date" name="expected_delivery_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                    <input type="text" name="delivery_address" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter delivery address">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Instructions</label>
                    <input type="text" name="delivery_instructions" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Deliver to store during working hours">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Internal Notes (for Director approval)</label>
                <textarea name="notes" rows="2" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Any notes for Director review..."></textarea>
            </div>

            {{-- Items Table --}}
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-md font-semibold text-gray-800 mb-3">Items (GM Approved Quantities)</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">GM Approved Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Unit Cost (UGX)</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Total (UGX)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($requisition->items as $index => $item)
                            <tr class="item-row border-b">
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                    <input type="hidden" name="items[{{ $index }}][requisition_item_id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[{{ $index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                                    @if($item->inventoryItem && $item->inventoryItem->item_code)
                                        <br>
                                        <span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                        {{ $item->metrics ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                                    {{ number_format($item->quantity_approved, 2) }}
                                    <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item->quantity_approved }}" class="item-quantity">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $index }}][unit_cost]" step="0.01" 
                                           class="item-cost w-32 px-3 py-2 border border-gray-300 rounded-lg text-right"
                                           value="0" min="0" required>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold item-total text-green-600">
                                    UGX 0.00
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="items[{{ $index }}][notes]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                           placeholder="Item notes...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 total-row">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-700">GRAND TOTAL:</td>
                                <td class="px-4 py-3 text-right font-bold text-green-700 text-lg" id="grandTotal">UGX 0.00</td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <a href="{{ route('procurement.requisitions.show', $requisition->id) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Submit LPO for Director Approval
            </button>
        </div>
    </form>
</div>

<script>
    function calculateTotals() {
        let grandTotal = 0;
        const rows = document.querySelectorAll('#itemsBody .item-row');
        
        rows.forEach(function(row) {
            const quantity = parseFloat(row.querySelector('.item-quantity')?.value || 0);
            const unitCost = parseFloat(row.querySelector('.item-cost')?.value || 0);
            const total = quantity * unitCost;
            const totalCell = row.querySelector('.item-total');
            
            if (totalCell) {
                totalCell.innerText = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
            
            grandTotal += total;
        });
        
        document.getElementById('grandTotal').innerText = `UGX ${grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    document.querySelectorAll('.item-cost').forEach(function(input) {
        input.addEventListener('input', calculateTotals);
        input.addEventListener('change', calculateTotals);
    });

    calculateTotals();
</script>
@endsection