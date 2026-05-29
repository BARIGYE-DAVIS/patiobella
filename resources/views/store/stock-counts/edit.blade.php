@extends('layouts.store')

@section('title', 'Edit Stock Count')
@section('page-title', 'Edit Stock Count')

@push('styles')
<style>
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
    .status-draft { background-color: #e5e7eb; color: #374151; }
    input[type="number"]:focus {
        border-color: #f59e0b;
        ring-color: #fbbf24;
    }
    .physical-qty-input {
        transition: all 0.2s ease;
    }
    .physical-qty-input:hover {
        border-color: #f59e0b;
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
                    <span class="status-badge status-draft">
                        <i class="fas fa-pencil-alt mr-1"></i> Editing - Draft
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Count Date: {{ \Carbon\Carbon::parse($stockCount->count_date)->format('F d, Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('store.stock-counts.show', $stockCount->id) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Details
                </a>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('store.stock-counts.update-items', $stockCount->id) }}" id="editStockCountForm">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-blue-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Edit Physical Counts</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Update physical quantities. Net quantity and variance will calculate automatically.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Info Alert --}}
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200 hidden">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <strong>How it works:</strong>
                            <ul class="mt-1 list-disc list-inside space-y-1">
                                <li><strong>Empty Bottle Weight</strong> is read-only and auto-loaded from the inventory item</li>
                                <li><strong>Net Quantity</strong> = Physical Count - Empty Bottle Weight</li>
                                <li><strong>Variance</strong> = Net Quantity - Expected Quantity</li>
                                <li><strong>Reason</strong> field is only enabled when there is a variance (Variance ≠ 0)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="rounded-lg border border-gray-200 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b-2 border-gray-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">Item</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Expected Qty</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Physical Count</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Empty Wt (kg)</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Net Qty</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Variance</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">Reason / Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="itemsTableBody">
                            @foreach($stockCount->items as $index => $item)
                            @php
                                $emptyWeight = $item->inventoryItem->empty_bottle_weight ?? 0;
                                $netQty = $item->net_quantity;
                                $variance = $item->variance;
                                $expectedQty = $item->system_quantity;
                                $itemId = $item->id;
                                $varianceClass = $variance < 0 ? 'variance-negative' : ($variance > 0 ? 'variance-positive' : 'variance-zero');
                                $hasVariance = $variance != 0;
                            @endphp
                            <tr class="hover:bg-yellow-50 transition-colors duration-150" data-item-id="{{ $itemId }}">
                                <td class="px-4 py-3">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $itemId }}">
                                    <p class="font-semibold text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                    <p class="text-xs text-gray-400">Unit: {{ $item->inventoryItem->base_unit ?? 'units' }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-gray-700" id="expected-display-{{ $itemId }}">{{ number_format($expectedQty, 2) }}</span>
                                    <input type="hidden" class="expected-qty-{{ $itemId }}" value="{{ $expectedQty }}">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number"
                                           name="items[{{ $index }}][physical_quantity]"
                                           step="any"
                                           class="physical-qty-input w-28 px-2 py-1.5 border-2 border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 rounded-lg text-center text-sm transition-all duration-200"
                                           value="{{ $item->physical_quantity }}"
                                           data-item-id="{{ $itemId }}"
                                           data-empty-weight="{{ $emptyWeight }}"
                                           data-expected="{{ $expectedQty }}">
                                    @if($item->physical_quantity_is_gross)
                                        <span class="text-xs text-gray-400 block">(gross weight)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($emptyWeight > 0)
                                        <span class="text-orange-600 font-medium">{{ number_format($emptyWeight, 3) }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                    <input type="hidden" name="items[{{ $index }}][empty_bottle_weight]" value="{{ $emptyWeight }}">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="net-qty-display-{{ $itemId }} font-semibold text-gray-800">{{ number_format($netQty, 2) }}</span>
                                    <input type="hidden" class="net-qty-hidden-{{ $itemId }}" value="{{ $netQty }}">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="variance-display-{{ $itemId }} {{ $varianceClass }}">
                                        {{ $variance >= 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <select name="items[{{ $index }}][reason_notes]"
                                            class="reason-select-{{ $itemId }} w-full px-2 py-1.5 border border-gray-300 rounded-lg text-sm"
                                            data-item-id="{{ $itemId }}"
                                            {{ !$hasVariance ? 'disabled' : '' }}>
                                        <option value="">— Select reason —</option>
                                        <option value="Damaged" {{ $item->reason_notes == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                                        <option value="Theft" {{ $item->reason_notes == 'Theft' ? 'selected' : '' }}>Theft</option>
                                        <option value="Expiry / Spoilage" {{ $item->reason_notes == 'Expiry / Spoilage' ? 'selected' : '' }}>Expiry / Spoilage</option>
                                        <option value="Miscount" {{ $item->reason_notes == 'Miscount' ? 'selected' : '' }}>Miscount</option>
                                        <option value="Spillage" {{ $item->reason_notes == 'Spillage' ? 'selected' : '' }}>Spillage</option>
                                        <option value="Returned to Supplier" {{ $item->reason_notes == 'Returned to Supplier' ? 'selected' : '' }}>Returned to Supplier</option>
                                        <option value="Write-off" {{ $item->reason_notes == 'Write-off' ? 'selected' : '' }}>Write-off</option>
                                        <option value="Other" {{ $item->reason_notes == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <input type="text"
                                           name="items[{{ $index }}][reason_details]"
                                           class="hidden reason-details-{{ $itemId }} w-full mt-1 px-2 py-1.5 border border-gray-300 rounded-lg text-sm"
                                           placeholder="Additional details..."
                                           value="{{ $item->reason_notes && !in_array($item->reason_notes, ['Damaged','Theft','Expiry / Spoilage','Miscount','Spillage','Returned to Supplier','Write-off','Other']) ? $item->reason_notes : '' }}"
                                           {{ !$hasVariance ? 'disabled' : '' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Summary Row --}}
                @php
                    $totalSystem = $stockCount->getTotalSystemQuantityAttribute();
                    $totalNet = $stockCount->getTotalNetQuantityAttribute();
                    $totalVariance = $stockCount->getTotalVarianceAttribute();
                @endphp
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase">Total Expected</p>
                            <p class="text-xl font-bold text-gray-800" id="totalExpected">{{ number_format($totalSystem, 2) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase">Total Net Quantity</p>
                            <p class="text-xl font-bold text-gray-800" id="totalNet">{{ number_format($totalNet, 2) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase">Total Variance</p>
                            <p class="text-xl font-bold {{ $totalVariance < 0 ? 'text-red-600' : ($totalVariance > 0 ? 'text-green-600' : 'text-gray-800') }}" id="totalVariance">
                                {{ $totalVariance >= 0 ? '+' : '' }}{{ number_format($totalVariance, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('store.stock-counts.show', $stockCount->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-times text-gray-400"></i> Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg text-sm font-medium transition shadow-sm hover:shadow-md">
                    <i class="fas fa-save text-sm"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all physical quantity inputs
        const physicalInputs = document.querySelectorAll('.physical-qty-input');

        // Function to calculate and update all values for a specific row
        function updateRowCalculations(inputElement) {
            const itemId = inputElement.getAttribute('data-item-id');
            const physicalValue = parseFloat(inputElement.value) || 0;
            const emptyWeight = parseFloat(inputElement.getAttribute('data-empty-weight')) || 0;
            const expectedQty = parseFloat(inputElement.getAttribute('data-expected')) || 0;

            // Calculate net quantity and variance
            const netQty = Math.max(0, physicalValue - emptyWeight);
            const variance = netQty - expectedQty;

            // Update Net Quantity display
            const netDisplay = document.querySelector(`.net-qty-display-${itemId}`);
            if (netDisplay) {
                netDisplay.textContent = netQty.toFixed(2);
            }

            // Update Variance display with color coding
            const varianceDisplay = document.querySelector(`.variance-display-${itemId}`);
            if (varianceDisplay) {
                varianceDisplay.textContent = `${variance >= 0 ? '+' : ''}${variance.toFixed(2)}`;

                // Update color class
                varianceDisplay.classList.remove('variance-positive', 'variance-negative', 'variance-zero');
                if (variance > 0) {
                    varianceDisplay.classList.add('variance-positive');
                } else if (variance < 0) {
                    varianceDisplay.classList.add('variance-negative');
                } else {
                    varianceDisplay.classList.add('variance-zero');
                }
            }

            // Enable/disable reason fields based on variance
            const hasVariance = Math.abs(variance) > 0.0001;
            const reasonSelect = document.querySelector(`.reason-select-${itemId}`);
            const reasonDetails = document.querySelector(`.reason-details-${itemId}`);

            if (reasonSelect) {
                reasonSelect.disabled = !hasVariance;
                reasonSelect.style.backgroundColor = hasVariance ? '#ffffff' : '#f3f4f6';
                reasonSelect.style.cursor = hasVariance ? 'pointer' : 'not-allowed';

                if (!hasVariance && reasonSelect.value !== '') {
                    reasonSelect.value = '';
                }
            }

            if (reasonDetails) {
                reasonDetails.disabled = !hasVariance;
                reasonDetails.style.backgroundColor = hasVariance ? '#ffffff' : '#f3f4f6';
                reasonDetails.style.cursor = hasVariance ? 'pointer' : 'not-allowed';

                if (!hasVariance && reasonDetails.value !== '') {
                    reasonDetails.value = '';
                }
            }

            // Update totals after each change
            updateTotalCalculations();
        }

        // Function to update total summary
        function updateTotalCalculations() {
            let totalExpected = 0;
            let totalNet = 0;

            document.querySelectorAll('.physical-qty-input').forEach(input => {
                const expected = parseFloat(input.getAttribute('data-expected')) || 0;
                const physical = parseFloat(input.value) || 0;
                const emptyWeight = parseFloat(input.getAttribute('data-empty-weight')) || 0;
                const netQty = Math.max(0, physical - emptyWeight);

                totalExpected += expected;
                totalNet += netQty;
            });

            const totalVariance = totalNet - totalExpected;

            // Update DOM
            const totalExpectedEl = document.getElementById('totalExpected');
            const totalNetEl = document.getElementById('totalNet');
            const totalVarianceEl = document.getElementById('totalVariance');

            if (totalExpectedEl) totalExpectedEl.textContent = totalExpected.toFixed(2);
            if (totalNetEl) totalNetEl.textContent = totalNet.toFixed(2);
            if (totalVarianceEl) {
                totalVarianceEl.textContent = `${totalVariance >= 0 ? '+' : ''}${totalVariance.toFixed(2)}`;

                // Update color
                totalVarianceEl.classList.remove('text-red-600', 'text-green-600', 'text-gray-800');
                if (totalVariance < 0) {
                    totalVarianceEl.classList.add('text-red-600');
                } else if (totalVariance > 0) {
                    totalVarianceEl.classList.add('text-green-600');
                } else {
                    totalVarianceEl.classList.add('text-gray-800');
                }
            }
        }

        // Attach input event listeners to all physical quantity inputs
        physicalInputs.forEach(input => {
            input.addEventListener('input', function() {
                updateRowCalculations(this);
            });
            input.addEventListener('blur', function() {
                updateRowCalculations(this);
            });
        });

        // Initial calculation for all rows
        physicalInputs.forEach(input => {
            updateRowCalculations(input);
        });
    });
</script>
@endsection
