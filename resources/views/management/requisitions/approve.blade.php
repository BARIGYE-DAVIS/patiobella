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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
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

            {{-- Items Table with Editable Approved Quantity --}}
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Items to Approve</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-5">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Category</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Requested</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-40">Approved Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($requisition->items as $index => $item)
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
                                                   class="quantity-input w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-blue-500"
                                                   oninput="validateQuantity({{ $index }}, {{ $item->quantity_requested }}, '{{ addslashes($item->inventoryItem->name ?? 'Item') }}')"
                                                   onkeydown="return preventExceededValue(event, {{ $item->quantity_requested }}, '{{ addslashes($item->inventoryItem->name ?? 'Item') }}')"
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
                                <td colspan="7" class="px-4 py-3">
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
            </div>

            {{-- Additional Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">GM Approval Notes (Optional)</label>
                <textarea name="approval_notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Any additional comments from General Manager..."></textarea>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <a href="{{ route('management.requisitions.show', $requisition->id) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" id="submitBtn" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Confirm Approval & Send to Procurement
            </button>
        </div>
    </form>
</div>

<script>
    function showToast(message) {
        // Remove existing toast
        const existingToast = document.querySelector('.toast-message');
        if (existingToast) existingToast.remove();

        // Create new toast
        const toast = document.createElement('div');
        toast.className = 'toast-message';
        toast.innerHTML = message;
        document.body.appendChild(toast);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    function setMaxQuantity(index, maxQty, itemName) {
        const input = document.getElementById(`qty_${index}`);
        input.value = maxQty;
        validateQuantity(index, maxQty, itemName);
        showToast(`✅ ${itemName}: Approved quantity set to maximum (${maxQty})`);
    }

    function preventExceededValue(event, maxQty, itemName) {
        // Allow control keys (backspace, delete, tab, etc.)
        const controlKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'];
        if (controlKeys.includes(event.key)) {
            return true;
        }

        // Allow decimal point
        if (event.key === '.') {
            return true;
        }

        // Get current value and new key
        const input = event.target;
        const currentValue = input.value;
        const newKey = event.key;

        // If key is a number
        if (!isNaN(parseFloat(newKey)) && newKey !== ' ') {
            let newValue;

            // If current value is 0, replace it
            if (currentValue === '0' || currentValue === '0.') {
                newValue = newKey;
            } else {
                newValue = currentValue + newKey;
            }

            const numericValue = parseFloat(newValue);

            if (numericValue > maxQty) {
                event.preventDefault();
                showToast(`⚠️ Cannot exceed requested quantity (${maxQty}) for ${itemName}`);
                return false;
            }
        }

        return true;
    }

    function validateQuantity(index, maxQty, itemName) {
        const input = document.getElementById(`qty_${index}`);
        const warningDiv = document.getElementById(`warning_${index}`);
        const row = document.getElementById(`row_${index}`);
        let value = parseFloat(input.value);

        // Clear previous styling
        input.classList.remove('valid', 'invalid');
        warningDiv.innerHTML = '';
        warningDiv.classList.remove('error', 'success', 'warning');
        if (row) row.classList.remove('row-invalid');

        // Check if value is valid number
        if (isNaN(value)) {
            // Empty or invalid input
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

        // Force correct value if exceeds max (additional safety)
        if (value > maxQty) {
            input.value = maxQty;
            value = maxQty;
            showToast(`⚠️ ${itemName}: Approved quantity adjusted to maximum (${maxQty})`);
        }

        // Check for negative
        if (value < 0) {
            input.classList.add('invalid');
            warningDiv.innerHTML = '❌ Cannot be negative. Please enter zero or positive number.';
            warningDiv.classList.add('error');
            if (row) row.classList.add('row-invalid');
            return false;
        }

        // Valid - show appropriate message
        input.classList.add('valid');

        if (value === maxQty) {
            warningDiv.innerHTML = '✓ Full quantity approved';
            warningDiv.classList.add('success');
        } else if (value === 0) {
            warningDiv.innerHTML = '⚠️ Warning: Approving zero quantity. This item will be skipped.';
            warningDiv.classList.add('warning');
        } else {
            const percentage = ((value / maxQty) * 100).toFixed(1);
            warningDiv.innerHTML = `✓ Partial approval: ${value} of ${maxQty} (${percentage}%) approved. Remaining will be cancelled.`;
            warningDiv.classList.add('success');
        }

        // Update total approved
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

        // Enable/disable submit button based on validity
        const submitBtn = document.getElementById('submitBtn');
        if (allValid) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // Initialize validation for all rows
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name*="[quantity_approved]"]').forEach(function(input, idx) {
            const maxValue = parseFloat(input.getAttribute('max'));
            const row = input.closest('.item-row');
            const itemName = row.querySelector('td:nth-child(2)').innerText.trim();
            validateQuantity(idx, maxValue, itemName);
        });

        // Add paste event listeners with user feedback
        document.querySelectorAll('input[name*="[quantity_approved]"]').forEach(function(input) {
            input.addEventListener('paste', function(event) {
                const maxValue = parseFloat(this.getAttribute('max'));
                const row = this.closest('.item-row');
                const itemName = row.querySelector('td:nth-child(2)').innerText.trim();

                setTimeout(() => {
                    let value = parseFloat(this.value);
                    if (value > maxValue) {
                        this.value = maxValue;
                        const index = parseInt(this.id.split('_')[1]);
                        validateQuantity(index, maxValue, itemName);
                        showToast(`⚠️ ${itemName}: Pasted value exceeded limit. Set to maximum (${maxValue})`);
                    } else {
                        const index = parseInt(this.id.split('_')[1]);
                        validateQuantity(index, maxValue, itemName);
                    }
                }, 10);
            });
        });
    });
</script>
@endsection
