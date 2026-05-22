{{-- resources/views/management/department-requisitions/approve.blade.php --}}

@extends('layouts.management')

@section('title', 'Approve Requisition')
@section('page-title', 'Approve Requisition')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $requisition->requisition_number }}</h2>
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                        Pending Approval
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Created: {{ $requisition->created_at->format('F d, Y h:i A') }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-building mr-1"></i> {{ $requisition->department->name ?? 'N/A' }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('management.department-requisitions.show', $requisition->id) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg px-5 py-4">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Approval Instructions:</strong>
            Review each requested item and enter the quantity you approve. Items with zero approved quantity will not be issued to the department.
            <strong class="block mt-2 text-amber-700">Note: Available stock is shown for reference. Only approve quantities that can be fulfilled from current stock.</strong>
        </p>
    </div>

    {{-- Approval Form --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('management.department-requisitions.approve', $requisition->id) }}" id="approveForm">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Metrics</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Available Stock</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Requested</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 bg-blue-50">Approved Qty <span class="text-red-500">*</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requisition->items as $index => $item)
                        @php
                            $baseUnit = $item->inventoryItem->base_unit ?? 'units';
                            $currentMetrics = $item->metrics ?? $baseUnit;
                            $availableStock = $item->inventoryItem->current_stock ?? 0;
                            $lowStock = $availableStock < $item->quantity_requested;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $lowStock ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3">
                                <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $index }}][metrics]" value="{{ $currentMetrics }}">
                                <input type="hidden" name="items[{{ $index }}][pack_type]" value="{{ $item->requested_pack_type ?? '' }}">
                                <input type="hidden" name="items[{{ $index }}][pack_size]" value="{{ $item->requested_pack_size ?? '' }}">
                                <p class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600">{{ $currentMetrics }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($availableStock <= 0)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        Out of Stock
                                    </span>
                                @elseif($availableStock < $item->quantity_requested)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">
                                        {{ number_format($availableStock, 2) }} (Low Stock)
                                    </span>
                                @else
                                    <span class="text-green-600 font-semibold">
                                        {{ number_format($availableStock, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold">{{ number_format($item->quantity_requested, 2) }}</span>
                                @if($item->requested_pack_type)
                                <div class="text-xs text-gray-400">{{ ucfirst($item->requested_pack_type) }} × {{ $item->requested_pack_size }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" name="items[{{ $index }}][quantity_approved]"
                                       class="approved-qty w-32 px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ min($item->quantity_approved ?? $item->quantity_requested, $availableStock) }}"
                                       step="0.01" min="0" required
                                       data-requested="{{ $item->quantity_requested }}"
                                       data-available="{{ $availableStock }}">
                                <div class="text-xs text-gray-400 mt-1">Max: {{ number_format($availableStock, 2) }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-xs font-semibold text-gray-500">Totals</td>
                            <td class="px-4 py-3 text-center font-semibold">
                                {{ number_format($requisition->items->sum('quantity_requested'), 2) }}
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-blue-600" id="totalApproved">
                                {{ number_format($requisition->items->sum(function($item) {
                                    return min($item->quantity_approved ?? $item->quantity_requested, $item->inventoryItem->current_stock ?? 0);
                                }), 2) }}
                            </td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Store Notes --}}
            <div class="px-6 py-5 border-t border-gray-200 bg-gray-50">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Store Notes (Optional)
                </label>
                <textarea name="store_notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Add any notes for the store keeper...">{{ old('store_notes') }}</textarea>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('management.department-requisitions.show', $requisition->id) }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-check mr-1"></i> Approve Requisition
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Update total approved dynamically
    function updateTotalApproved() {
        let total = 0;
        document.querySelectorAll('.approved-qty').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('totalApproved').innerText = total.toFixed(2);
    }

    // Warn if approved quantity exceeds requested quantity or available stock
    document.querySelectorAll('.approved-qty').forEach(input => {
        input.addEventListener('change', function() {
            const approved = parseFloat(this.value) || 0;
            const requested = parseFloat(this.dataset.requested) || 0;
            const available = parseFloat(this.dataset.available) || 0;

            if (approved > requested) {
                alert(`Warning: Approved quantity (${approved}) exceeds requested quantity (${requested}). This will be allowed but please confirm.`);
            }

            if (approved > available) {
                alert(`Warning: Approved quantity (${approved}) exceeds available stock (${available}). The store may not be able to fulfill this quantity.`);
            }

            updateTotalApproved();
        });

        input.addEventListener('input', updateTotalApproved);
    });

    // Set default approved quantity to min(requested, available)
    document.querySelectorAll('.approved-qty').forEach(input => {
        const available = parseFloat(input.dataset.available) || 0;
        const requested = parseFloat(input.dataset.requested) || 0;
        const defaultValue = Math.min(requested, available);
        if (parseFloat(input.value) > defaultValue) {
            input.value = defaultValue;
        }
    });

    // Form validation
    document.getElementById('approveForm')?.addEventListener('submit', function(e) {
        let hasApprovedItems = false;
        const approvedInputs = document.querySelectorAll('.approved-qty');

        for (let i = 0; i < approvedInputs.length; i++) {
            if (parseFloat(approvedInputs[i].value) > 0) {
                hasApprovedItems = true;
                break;
            }
        }

        if (!hasApprovedItems) {
            e.preventDefault();
            alert('Please approve at least one item by entering a quantity greater than zero.');
        }
    });

    // Initial total calculation
    updateTotalApproved();
</script>
@endsection
