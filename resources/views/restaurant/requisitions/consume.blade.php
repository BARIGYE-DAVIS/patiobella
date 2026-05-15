{{-- resources/views/restaurant/requisitions/consume.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'Record Consumption')

@section('page-title', 'Record Consumption')

@section('content')
<style>
    .item-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 14px;
    }
    .item-card-header {
        background: #f9fafb;
        padding: 10px 14px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-box {
        border-radius: 8px;
        padding: 8px 12px;
        text-align: center;
    }
    .stat-issued   { background: #d1fae5; }
    .stat-consumed { background: #fef3c7; }
    .stat-returned { background: #ede9fe; }
    .stat-remaining { background: #dbeafe; }
    .stat-label { font-size: 10px; color: #6b7280; margin-bottom: 2px; }
    .stat-value { font-size: 15px; font-weight: 700; }
    .consume-input {
        width: 130px;
        text-align: center;
        padding: 6px 10px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
    }
    .consume-input:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }
    .unit-badge {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 11px;
        font-weight: 600;
        color: #374151;
    }
    .remaining-warning {
        font-size: 11px;
        color: #dc2626;
        margin-top: 4px;
        display: none;
    }
    .grand-total-box {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 10px;
        padding: 12px 18px;
        margin-top: 16px;
    }
</style>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-5 py-4 border-b border-gray-200 bg-orange-50 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('restaurant.requisitions.show', $requisition->id) }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 border border-gray-200 rounded-lg px-3 py-1.5 bg-white hover:bg-gray-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <div>
                <h3 class="text-base font-semibold text-gray-800">Record Consumption</h3>
                <p class="text-xs text-gray-500">
                    {{ $requisition->requisition_number }} &middot; {{ $requisition->department->name ?? 'Restaurant' }}
                </p>
            </div>
        </div>
    </div>

    <div class="p-5">

        {{-- Info banner --}}
        <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg p-4 mb-5">
            <p class="text-sm text-blue-800">
                <strong>Note:</strong> Enter how much of each item was used/consumed.
                You can only record up to the remaining quantity (issued − already consumed − returned).
                Leave an item at <strong>0</strong> to skip it.
            </p>
        </div>

        <form method="POST" action="{{ route('restaurant.requisitions.record-consumption', $requisition->id) }}" id="consumeForm">
            @csrf

            @foreach($consumableItems as $index => $item)
            @php
                $unit      = $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units');
                $issued    = (float) ($item->issued_total_pieces   ?? 0);
                $consumed  = (float) ($item->quantity_consumed     ?? 0);
                $returned  = (float) ($item->returned_total_pieces ?? 0);
                $remaining = $issued - $consumed - $returned;

                // Pack display info
                $issuedPackType = $item->issued_pack_type;
                $issuedPackSize = $item->issued_pack_size;
                $hasPack        = $issuedPackType && $issuedPackSize;
            @endphp

            <div class="item-card">
                <div class="item-card-header">
                    <div>
                        <span class="font-semibold text-sm text-gray-800">
                            {{ $item->inventoryItem->name ?? 'N/A' }}
                        </span>
                        <span class="text-xs text-gray-400 ml-2 font-mono">
                            {{ $item->inventoryItem->item_code ?? '' }}
                        </span>
                        @if($hasPack)
                            <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                {{ $item->quantity_issued }} {{ ucfirst($issuedPackType) }}(s) × {{ $issuedPackSize }} = {{ number_format($issued, 2) }} {{ $unit }}
                            </span>
                        @endif
                    </div>
                    <span class="unit-badge">{{ $unit }}</span>
                </div>

                <div class="p-4">
                    {{-- Stats row --}}
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <div class="stat-box stat-issued">
                            <div class="stat-label">Issued</div>
                            <div class="stat-value text-green-700">{{ number_format($issued, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $unit }}</div>
                        </div>
                        <div class="stat-box stat-consumed">
                            <div class="stat-label">Already Consumed</div>
                            <div class="stat-value text-amber-700">{{ number_format($consumed, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $unit }}</div>
                        </div>
                        <div class="stat-box stat-returned">
                            <div class="stat-label">Returned</div>
                            <div class="stat-value text-purple-700">{{ number_format($returned, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $unit }}</div>
                        </div>
                        <div class="stat-box stat-remaining">
                            <div class="stat-label">Remaining</div>
                            <div class="stat-value text-blue-700" id="remaining_display_{{ $index }}">
                                {{ number_format($remaining, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $unit }}</div>
                        </div>
                    </div>

                    {{-- Hidden fields --}}
                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">

                    {{-- Consumption input --}}
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-semibold text-gray-600">Consumed now:</label>
                        <input type="number"
                               name="items[{{ $index }}][quantity_consumed]"
                               id="consume_{{ $index }}"
                               class="consume-input"
                               value="0"
                               min="0"
                               max="{{ $remaining }}"
                               step="0.01"
                               data-remaining="{{ $remaining }}"
                               data-index="{{ $index }}"
                               data-unit="{{ $unit }}"
                               oninput="validateConsumption({{ $index }})">
                        <span class="unit-badge">{{ $unit }}</span>
                        <button type="button"
                                class="text-xs text-blue-600 hover:underline"
                                onclick="setMax({{ $index }}, {{ $remaining }})">
                            Use all ({{ number_format($remaining, 2) }})
                        </button>
                    </div>
                    <div class="remaining-warning" id="warning_{{ $index }}">
                        ⚠️ Cannot exceed remaining quantity of {{ number_format($remaining, 2) }} {{ $unit }}
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Consumption notes --}}
            <div class="mt-4">
                <label class="block text-sm font-semibold text-gray-600 mb-1">
                    Consumption Notes <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea name="consumption_notes"
                          rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                          placeholder="e.g. Used for dinner service, special event, etc.">{{ old('consumption_notes') }}</textarea>
            </div>

            {{-- Grand total --}}
            <div class="grand-total-box flex justify-between items-center">
                <span class="text-sm font-semibold text-gray-700">Total Being Consumed This Entry:</span>
                <span class="text-xl font-bold text-orange-600" id="grandTotal">0.00</span>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 mt-5">
                <a href="{{ route('restaurant.requisitions.show', $requisition->id) }}"
                   class="px-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-orange-600 text-white rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
                    Save Consumption
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function validateConsumption(index) {
        const input     = document.getElementById(`consume_${index}`);
        const remaining = parseFloat(input.getAttribute('data-remaining')) || 0;
        const unit      = input.getAttribute('data-unit');
        const warning   = document.getElementById(`warning_${index}`);
        let value       = parseFloat(input.value) || 0;

        if (value < 0) {
            input.value = 0;
            value = 0;
        }

        if (value > remaining) {
            warning.style.display = 'block';
            input.style.borderColor = '#dc2626';
        } else {
            warning.style.display = 'none';
            input.style.borderColor = '#d1d5db';
        }

        updateGrandTotal();
    }

    function setMax(index, remaining) {
        const input = document.getElementById(`consume_${index}`);
        input.value = remaining;
        validateConsumption(index);
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('[id^="consume_"]').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').innerText = total.toFixed(2);
    }

    // Validate before submit
    document.getElementById('consumeForm').addEventListener('submit', function(e) {
        let hasError   = false;
        let hasQty     = false;

        document.querySelectorAll('[id^="consume_"]').forEach(input => {
            const value     = parseFloat(input.value) || 0;
            const remaining = parseFloat(input.getAttribute('data-remaining')) || 0;

            if (value > remaining) {
                hasError = true;
            }
            if (value > 0) {
                hasQty = true;
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('One or more quantities exceed the available remaining. Please correct them.');
            return false;
        }

        if (!hasQty) {
            e.preventDefault();
            alert('Please enter a consumed quantity for at least one item.');
            return false;
        }
    });
</script>
@endsection
