@extends('layouts.store')

@section('title', 'Issue Items')

@section('page-title', 'Issue Items to Department')

@section('content')
<style>
    .quantity-input {
        width: 100px;
    }
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
        ring: 2px solid #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
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
                <strong>Note:</strong> Enter the quantities you are issuing to the department.
                You can issue partially or fully. Stock will be automatically deducted.
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metrics</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Requested</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Previously Issued</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Remaining</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity to Issue</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pack Type</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pieces/Pack</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($requisition->items as $item)
                        @php
                            $remaining = $item->quantity_requested - $item->quantity_issued;
                            $maxIssue = $remaining;
                        @endphp
                        <tr class="item-row border-b">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $item->inventoryItem->name ?? 'N/A' }}
                                <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $loop->index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $item->metrics ?: '—' }}
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
                                <input type="number" name="items[{{ $loop->index }}][quantity_issued]"
                                       class="quantity-issued w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                                       value="0" min="0" max="{{ $maxIssue }}" step="0.01"
                                       data-max="{{ $maxIssue }}"
                                       onchange="validateQuantity(this)">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <select name="items[{{ $loop->index }}][pack_type]" class="pack-type w-28 px-2 py-1 border border-gray-300 rounded-lg text-sm">
                                    <option value="">-- None --</option>
                                    <option value="carton">Carton</option>
                                    <option value="box">Box</option>
                                    <option value="crate">Crate</option>
                                    <option value="dozen">Dozen</option>
                                    <option value="pack">Pack</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" name="items[{{ $loop->index }}][pack_size]"
                                       class="pack-size w-20 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                                       placeholder="e.g., 24" step="1" min="1">
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
                            <td colspan="9" class="px-4 py-3">
                                <div class="flex justify-end items-center gap-4">
                                    <span class="text-sm text-gray-600">Total Requested:</span>
                                    <span class="text-sm font-semibold" id="totalRequested">
                                        {{ number_format($requisition->items->sum('quantity_requested'), 2) }}
                                    </span>
                                    <span class="text-sm text-gray-600 ml-4">Total to Issue:</span>
                                    <span class="text-sm font-semibold text-green-600" id="totalToIssue">0.00</span>
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
    function validateQuantity(input) {
        const max = parseFloat(input.getAttribute('data-max'));
        let value = parseFloat(input.value);

        if (isNaN(value)) {
            input.value = 0;
            value = 0;
        }

        if (value > max) {
            alert(`Cannot issue more than remaining quantity (${max})`);
            input.value = max;
            value = max;
        }

        if (value < 0) {
            input.value = 0;
            value = 0;
        }

        updateTotalToIssue();
    }

    function updateTotalToIssue() {
        let total = 0;
        document.querySelectorAll('.quantity-issued').forEach(input => {
            let value = parseFloat(input.value);
            if (!isNaN(value) && value > 0) {
                total += value;
            }
        });
        document.getElementById('totalToIssue').innerText = total.toFixed(2);
    }

    // Add validation before form submit
    document.getElementById('issueForm').addEventListener('submit', function(e) {
        let takenBy = document.getElementById('taken_by').value.trim();
        if (takenBy === '') {
            e.preventDefault();
            alert('Please enter the name of the person taking these items.');
            document.getElementById('taken_by').focus();
            return false;
        }

        let hasQuantity = false;
        document.querySelectorAll('.quantity-issued').forEach(input => {
            let value = parseFloat(input.value);
            if (!isNaN(value) && value > 0) {
                hasQuantity = true;
            }
        });

        if (!hasQuantity) {
            e.preventDefault();
            alert('Please enter at least one item quantity to issue.');
            return false;
        }
    });

    // Initialize
    document.querySelectorAll('.quantity-issued').forEach(input => {
        input.addEventListener('input', updateTotalToIssue);
    });
    updateTotalToIssue();
</script>
@endsection
