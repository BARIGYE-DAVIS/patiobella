@extends('layouts.store')

@section('title', 'Issue Items')

@section('page-title', 'Issue Items to Department')

@section('content')
<style>
    .info-box {
        background-color: #eff6ff;
        border-left: 4px solid #3b82f6;
    }
    .item-row {
        transition: background-color 0.2s ease;
    }
    .item-row:hover {
        background-color: #f9fafb;
    }
    .taken-by-section {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .taken-by-label {
        font-weight: 600;
        color: #166534;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .taken-by-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #dcfce7;
        border-radius: 10px;
        background-color: #ffffff;
        transition: all 0.2s;
    }
    .taken-by-input:focus {
        outline: none;
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    .total-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #1d4ed8;
    }
    .direct-unit-tag {
        display: inline-block;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #166534;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Issue Items to {{ $requisition->department->name ?? 'Department' }}</h3>
        <p class="text-sm text-gray-500">Requisition #{{ $requisition->requisition_number }}</p>
    </div>

    <div class="p-6">
        <div class="info-box p-4 rounded-lg mb-6">
            <p class="text-sm text-blue-800">
                <strong>Note:</strong> For pack items (carton, box, etc.) — select the pack type and enter how many pieces are in each pack. The total pieces will be calculated automatically. For direct units (kg, litres, pcs) — just enter the quantity directly.
            </p>
        </div>

        <form method="POST" action="{{ route('store.department-requisitions.issue', $requisition->id) }}" id="issueForm">
            @csrf

            {{-- TAKEN BY SECTION --}}
            <div class="taken-by-section">
                <label for="taken_by" class="taken-by-label">
                    <span class="text-red-500">*</span> Who is taking these items?
                </label>
                <input type="text"
                       name="taken_by"
                       id="taken_by"
                       value="{{ old('taken_by') }}"
                       class="taken-by-input"
                       placeholder="Enter full name of the department staff receiving the items"
                       required>
                <p class="text-xs text-gray-500 mt-2 ml-1">
                    ⚠️ This is a required field. The person signing for these items will be recorded in the stock movement.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Requested</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Previously Issued</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Remaining</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty to Issue</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pack Type</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pcs / Pack</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Pieces</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($requisition->items as $item)
                        @php
                            $remaining = $item->quantity_requested - $item->quantity_issued;
                            $baseUnit  = $item->inventoryItem->base_unit ?? 'units';
                            $recvUnit  = $item->inventoryItem->default_unit_of_measure_id ?? $baseUnit;
                        @endphp
                        <tr class="item-row border-b" id="row_{{ $loop->index }}">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <p class="font-medium">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $loop->index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="direct-unit-tag">{{ $baseUnit }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-semibold">
                                {{ number_format($item->quantity_requested, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-orange-600">
                                {{ number_format($item->quantity_issued, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-blue-600">
                                {{ number_format($remaining, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number"
                                       name="items[{{ $loop->index }}][quantity_issued]"
                                       id="qty_{{ $loop->index }}"
                                       class="quantity-issued w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                                       value="0" min="0" max="{{ $remaining }}" step="0.01"
                                       data-max="{{ $remaining }}"
                                       data-index="{{ $loop->index }}"
                                       oninput="recalculate({{ $loop->index }})">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <select name="items[{{ $loop->index }}][pack_type]"
                                        id="pack_type_{{ $loop->index }}"
                                        class="pack-type w-28 px-2 py-1 border border-gray-300 rounded-lg text-sm"
                                        data-index="{{ $loop->index }}"
                                        onchange="recalculate({{ $loop->index }})">
                                    <option value="">— Direct —</option>
                                    <option value="carton">Carton</option>
                                    <option value="box">Box</option>
                                    <option value="crate">Crate</option>
                                    <option value="dozen">Dozen</option>
                                    <option value="pack">Pack</option>
                                    <option value="sack">Sack</option>
                                    <option value="set">Set</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number"
                                       name="items[{{ $loop->index }}][pack_size]"
                                       id="pack_size_{{ $loop->index }}"
                                       class="pack-size w-20 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                                       placeholder="e.g. 24"
                                       step="1" min="1"
                                       data-index="{{ $loop->index }}"
                                       oninput="recalculate({{ $loop->index }})">
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{-- Shows calculated total or direct qty --}}
                                <span id="total_display_{{ $loop->index }}" class="total-pill">
                                    0 {{ $baseUnit }}
                                </span>
                                <input type="hidden"
                                       id="total_pieces_{{ $loop->index }}"
                                       data-baseunit="{{ $baseUnit }}"
                                       value="0">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="items[{{ $loop->index }}][notes]"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-lg text-sm"
                                       placeholder="Optional notes...">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="10" class="px-4 py-3">
                                <div class="flex justify-end items-center gap-4">
                                    <span class="text-sm text-gray-600">Total Requested:</span>
                                    <span class="text-sm font-semibold">{{ number_format($requisition->items->sum('quantity_requested'), 2) }}</span>
                                    <span class="text-sm text-gray-600 ml-4">Total Pieces to Issue:</span>
                                    <span class="text-sm font-semibold text-green-600" id="grandTotal">0</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Notes (Optional)</label>
                <textarea name="store_notes" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Any notes for the department..."></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('store.department-requisitions.show', $requisition->id) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Confirm Issue
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function recalculate(index) {
        const qty       = parseFloat(document.getElementById(`qty_${index}`).value) || 0;
        const packType  = document.getElementById(`pack_type_${index}`).value;
        const packSize  = parseFloat(document.getElementById(`pack_size_${index}`).value) || 0;
        const baseUnit  = document.getElementById(`total_pieces_${index}`).getAttribute('data-baseunit');
        const maxQty    = parseFloat(document.getElementById(`qty_${index}`).getAttribute('data-max'));

        // Validate quantity
        if (qty > maxQty) {
            document.getElementById(`qty_${index}`).value = maxQty;
        }

        let totalPieces = 0;
        let displayText = '';

        if (packType) {
            // Pack mode — qty = number of packs, total = qty × pack_size
            if (packSize > 0) {
                totalPieces = qty * packSize;
                displayText = `${qty} ${packType}(s) × ${packSize} = <strong>${totalPieces} ${baseUnit}</strong>`;
            } else {
                totalPieces = 0;
                displayText = `<span class="text-red-500">Enter pcs/${packType}</span>`;
            }
        } else {
            // Direct mode — qty is already in base units
            totalPieces = qty;
            displayText = `<strong>${qty} ${baseUnit}</strong>`;
        }

        document.getElementById(`total_display_${index}`).innerHTML = displayText;
        document.getElementById(`total_pieces_${index}`).value = totalPieces;

        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('[id^="total_pieces_"]').forEach(input => {
            grand += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').innerText = grand.toFixed(2);
    }

    // Form validation before submit
    document.getElementById('issueForm').addEventListener('submit', function(e) {
        const takenBy = document.getElementById('taken_by').value.trim();
        if (!takenBy) {
            e.preventDefault();
            alert('Please enter the name of the person taking these items.');
            document.getElementById('taken_by').focus();
            return false;
        }

        let hasQty = false;
        document.querySelectorAll('.quantity-issued').forEach(input => {
            if (parseFloat(input.value) > 0) hasQty = true;
        });

        if (!hasQty) {
            e.preventDefault();
            alert('Please enter at least one item quantity to issue.');
            return false;
        }

        // Validate: if pack type selected, pack size must be filled
        let packError = false;
        document.querySelectorAll('.pack-type').forEach(select => {
            if (select.value) {
                const index = select.getAttribute('data-index');
                const packSize = parseFloat(document.getElementById(`pack_size_${index}`).value) || 0;
                if (packSize < 1) {
                    packError = true;
                }
            }
        });

        if (packError) {
            e.preventDefault();
            alert('Please enter the number of pieces per pack for all selected pack types.');
            return false;
        }
    });

    // Initialize all rows
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($requisition->items as $item)
        recalculate({{ $loop->index }});
        @endforeach
    });
</script>
@endsection
