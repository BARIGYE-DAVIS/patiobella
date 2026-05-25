@extends('layouts.management')

@section('title', 'Review Stock Count')
@section('page-title', 'Review Stock Count')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper.single .ts-control {
        border-radius: 0.5rem;
        border-color: #d1d5db;
        font-size: 0.875rem;
        padding: 6px 10px;
        background-color: #f3f4f6;
        cursor: not-allowed;
    }
    .ts-dropdown {
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { opacity: 0.5; }
    #itemsBody tr:hover { background-color: #fefce8; transition: background-color 0.2s ease; }
    .variance-positive { color: #059669; font-weight: 600; }
    .variance-negative { color: #dc2626; font-weight: 600; }
    .variance-zero { color: #6b7280; }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-review { background-color: #fef3c7; color: #d97706; }
    .approved-badge {
        background-color: #d1fae5;
        color: #065f46;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $stockCount->count_number }}</h2>
                    <span class="status-badge status-review">
                        <i class="fas fa-clock mr-1"></i> Under Review
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Count Date: {{ \Carbon\Carbon::parse($stockCount->count_date)->format('F d, Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('management.stock-counts.show', $stockCount->id) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('management.stock-counts.review-approve', $stockCount->id) }}" id="reviewForm">
            @csrf

            {{-- Header --}}
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-6 py-4 border-b border-yellow-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-yellow-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $type === 'store' ? 'Store Stock Count Review' : 'Department Stock Count Review' }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Review physical counts, verify variances, and approve or request changes
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Basic Info (Read-only) --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-info-circle text-yellow-500 text-sm"></i>
                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Count Information</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-building mr-1 text-gray-400"></i> Location
                            </label>
                            <p class="text-gray-800">
                                {{ $type === 'store' ? 'Main Store' : ($stockCount->location->name ?? 'Department') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user mr-1 text-gray-400"></i> Counted By
                            </label>
                            <p class="text-gray-800">
                                {{ $stockCount->creator->first_name ?? 'Unknown' }} {{ $stockCount->creator->last_name ?? '' }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sticky-note mr-1 text-gray-400"></i> Notes
                            </label>
                            <p class="text-gray-600 text-sm bg-white p-3 rounded-lg border border-gray-200">
                                {{ $stockCount->notes ?: 'No notes provided' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Items Section --}}
                <div class="border-t border-gray-200 pt-2">
                    <div class="mb-4">
                        <h3 class="text-base font-semibold text-gray-800">
                            <i class="fas fa-boxes mr-2 text-yellow-500"></i> Items to Review
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Review physical counts and approve/reject variances</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Expected Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Physical Count</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Empty Weight (kg)</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Net Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Variance</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason / Notes</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" class="divide-y divide-gray-100">
                                @foreach($stockCount->items as $index => $item)
                                @php
                                    $emptyWeight = $item->inventoryItem->empty_bottle_weight ?? 0;
                                    $netQty = $item->net_quantity;
                                    $variance = $item->variance;
                                    $varianceClass = $variance < 0 ? 'variance-negative' : ($variance > 0 ? 'variance-positive' : 'variance-zero');
                                    $hasVariance = $variance != 0;
                                    $isApproved = $item->isApproved();
                                @endphp
                                <tr class="hover:bg-yellow-50 transition-colors duration-150" data-item-id="{{ $item->id }}" data-has-variance="{{ $hasVariance ? 'true' : 'false' }}">
                                    <td class="px-4 py-3">
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        <p class="font-semibold text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                        <input type="hidden" name="items[{{ $index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-bold text-gray-700">{{ number_format($item->system_quantity, 2) }}</span>
                                        <span class="text-xs text-gray-400 ml-1">{{ $item->inventoryItem->base_unit ?? 'units' }}</span>
                                        <input type="hidden" class="expected-qty-{{ $item->id }}" value="{{ $item->system_quantity }}">
                                     </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number"
                                               name="items[{{ $index }}][physical_quantity]"
                                               step="any"
                                               class="physical-qty-{{ $item->id }} w-28 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                                               value="{{ $item->physical_quantity }}"
                                               data-item-id="{{ $item->id }}">
                                        @if($item->physical_quantity_is_gross)
                                        <span class="text-xs text-gray-400 block">(gross weight)</span>
                                        @endif
                                     </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-orange-600 font-medium">{{ number_format($emptyWeight, 3) }}</span>
                                        <input type="hidden" class="empty-weight-{{ $item->id }}" value="{{ $emptyWeight }}">
                                     </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="net-qty-display-{{ $item->id }} font-semibold text-gray-800">{{ number_format($netQty, 2) }}</span>
                                        <input type="hidden" class="net-qty-{{ $item->id }}" value="{{ $netQty }}">
                                     </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="variance-display-{{ $item->id }} {{ $varianceClass }}">
                                            {{ $variance >= 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                                        </span>
                                     </td>
                                    <td class="px-4 py-3">
                                        <select name="items[{{ $index }}][reason_code]"
                                                class="reason-code-{{ $item->id }} w-full px-2 py-1 border border-gray-300 rounded-lg text-sm"
                                                {{ !$hasVariance || $isApproved ? 'disabled' : '' }}>
                                            <option value="">— Select reason —</option>
                                            @foreach($reasons as $reason)
                                                <option value="{{ $reason->code }}" {{ $item->reason_code == $reason->code ? 'selected' : '' }}>
                                                    {{ $reason->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text"
                                               name="items[{{ $index }}][reason_notes]"
                                               class="reason-notes-{{ $item->id }} w-full mt-1 px-2 py-1 border border-gray-300 rounded-lg text-sm"
                                               placeholder="Additional notes..."
                                               value="{{ $item->reason_notes }}"
                                               {{ !$hasVariance || $isApproved ? 'disabled' : '' }}>
                                     </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($isApproved)
                                            <span class="approved-badge">
                                                <i class="fas fa-check-circle mr-1"></i> Approved
                                            </span>
                                        @elseif(!$hasVariance)
                                            <span class="approved-badge">
                                                <i class="fas fa-check-circle mr-1"></i> No Variance
                                            </span>
                                        @else
                                            <div class="flex items-center justify-center">
                                                <input type="checkbox"
                                                       name="items[{{ $index }}][approved]"
                                                       class="approve-checkbox-{{ $item->id }} w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                                       value="1"
                                                       data-item-id="{{ $item->id }}">
                                            </div>
                                        @endif
                                     </td>
                                </tr>
                                @endforeach
                            </tbody>
                        赶
                    </div>
                </div>
            </div>

            {{-- Review Notes & Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-comment mr-1 text-gray-400"></i> Review Notes (Optional)
                    </label>
                    <textarea name="review_notes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 transition-all duration-200"
                              placeholder="Add any notes about this review..."></textarea>
                </div>

                {{-- Summary Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Total Items</p>
                        <p class="text-lg font-bold text-gray-800">{{ $stockCount->items->count() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Items with Variance</p>
                        <p class="text-lg font-bold text-orange-600">
                            {{ $stockCount->items->filter(fn($i) => $i->variance != 0)->count() }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Approved</p>
                        <p class="text-lg font-bold text-green-600">
                            {{ $stockCount->items->filter(fn($i) => $i->isApproved())->count() }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Pending Approval</p>
                        <p class="text-lg font-bold text-red-600">
                            {{ $stockCount->items->filter(fn($i) => $i->variance != 0 && !$i->isApproved())->count() }}
                        </p>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="window.history.back()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-times text-gray-400"></i> Cancel
                    </button>
                    <button type="submit" name="action" value="reject"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-sm"
                            onclick="return confirmReject()">
                        <i class="fas fa-times-circle"></i> Reject Count
                    </button>
                    <button type="submit" name="action" value="approve"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg text-sm font-medium transition shadow-sm hover:shadow-md">
                        <i class="fas fa-check-circle"></i> Approve & Complete
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Recalculate net quantity and variance when physical quantity changes
    function setupCalculation(itemId, expectedQty, emptyWeight) {
        const physicalInput = document.querySelector(`.physical-qty-${itemId}`);

        function recalculate() {
            const physical = parseFloat(physicalInput?.value) || 0;
            const netQty = Math.max(0, physical - emptyWeight);
            const variance = netQty - expectedQty;

            // Update net quantity display
            const netDisplay = document.querySelector(`.net-qty-display-${itemId}`);
            const netHidden = document.querySelector(`.net-qty-${itemId}`);
            if (netDisplay) netDisplay.textContent = netQty.toFixed(2);
            if (netHidden) netHidden.value = netQty;

            // Update variance display
            const varDisplay = document.querySelector(`.variance-display-${itemId}`);
            if (varDisplay) {
                const cls = variance < 0 ? 'variance-negative' : (variance > 0 ? 'variance-positive' : 'variance-zero');
                varDisplay.className = cls;
                varDisplay.textContent = `${variance >= 0 ? '+' : ''}${variance.toFixed(2)}`;
            }

            // Enable/disable reason fields based on variance
            const hasVariance = variance !== 0;
            const reasonCode = document.querySelector(`.reason-code-${itemId}`);
            const reasonNotes = document.querySelector(`.reason-notes-${itemId}`);
            const approveCheckbox = document.querySelector(`.approve-checkbox-${itemId}`);
            const statusCell = document.querySelector(`tr[data-item-id="${itemId}"] td:last-child`);

            if (reasonCode) {
                reasonCode.disabled = !hasVariance;
                if (!hasVariance) reasonCode.value = '';
            }
            if (reasonNotes) {
                reasonNotes.disabled = !hasVariance;
                if (!hasVariance) reasonNotes.value = '';
            }

            // Update status cell based on variance
            if (statusCell && !hasVariance) {
                statusCell.innerHTML = `<span class="approved-badge"><i class="fas fa-check-circle mr-1"></i> No Variance</span>`;
            } else if (statusCell && hasVariance && approveCheckbox) {
                if (!approveCheckbox.closest('td')) {
                    statusCell.innerHTML = `<div class="flex items-center justify-center">
                        <input type="checkbox" name="items[${itemId}][approved]"
                               class="approve-checkbox-${itemId} w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                               value="1" data-item-id="${itemId}">
                    </div>`;
                    // Re-attach event listener to the new checkbox
                    const newCheckbox = document.querySelector(`.approve-checkbox-${itemId}`);
                    if (newCheckbox) {
                        newCheckbox.addEventListener('change', function() {
                            if (this.checked) {
                                const reasonCodeField = document.querySelector(`.reason-code-${itemId}`);
                                if (reasonCodeField && !reasonCodeField.value) {
                                    alert('Please select a reason code before approving.');
                                    this.checked = false;
                                }
                            }
                        });
                    }
                }
            }

            // Update row data attribute
            const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
            if (row) {
                row.setAttribute('data-has-variance', hasVariance ? 'true' : 'false');
            }
        }

        physicalInput?.addEventListener('input', recalculate);
    }

    // Initialize calculations for all items
    @foreach($stockCount->items as $item)
    @php
        $emptyWeight = $item->inventoryItem->empty_bottle_weight ?? 0;
    @endphp
    setupCalculation({{ $item->id }}, {{ $item->system_quantity }}, {{ $emptyWeight }});
    @endforeach

    // Attach change event to approve checkboxes
    document.querySelectorAll('[class*="approve-checkbox-"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                const itemId = this.getAttribute('data-item-id');
                const reasonCode = document.querySelector(`.reason-code-${itemId}`);
                if (reasonCode && !reasonCode.value) {
                    alert('Please select a reason code before approving this variance.');
                    this.checked = false;
                }
            }
        });
    });

    // Form validation before submit
    function confirmReject() {
        return confirm('Are you sure you want to REJECT this stock count?\n\nThe count will be cancelled and will need to be redone.');
    }

    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        const action = e.submitter?.value;

        if (action === 'approve') {
            // Check if all variances have been approved
            const unapprovedItems = [];
            document.querySelectorAll('tr[data-has-variance="true"]').forEach(row => {
                const itemId = row.getAttribute('data-item-id');
                const approveCheckbox = document.querySelector(`.approve-checkbox-${itemId}`);
                const reasonCode = document.querySelector(`.reason-code-${itemId}`);

                if (approveCheckbox && !approveCheckbox.checked) {
                    const itemName = row.querySelector('.font-semibold')?.textContent || 'Unknown';
                    unapprovedItems.push(itemName);
                }

                if (reasonCode && approveCheckbox?.checked && !reasonCode.value) {
                    const itemName = row.querySelector('.font-semibold')?.textContent || 'Unknown';
                    alert(`Please select a reason for variance on item: ${itemName}`);
                    e.preventDefault();
                    return false;
                }
            });

            if (unapprovedItems.length > 0) {
                alert(`Please approve variances for the following items:\n- ${unapprovedItems.join('\n- ')}`);
                e.preventDefault();
                return false;
            }
            
            if (!confirm('All variances have been reviewed. Approve and complete this stock count?')) {
                e.preventDefault();
            }
        }
    });
</script>
@endpush
