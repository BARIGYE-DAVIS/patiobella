{{-- resources/views/kitchen/consumption/create.blade.php --}}

@extends('layouts.kitchen')

@section('title', 'Record Consumption')

@section('page-title', 'Record Consumption')

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .form-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    .form-body {
        padding: 1.5rem;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .text-right { text-align: right; }
    .consumption-input {
        width: 80px;
        padding: 0.35rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.75rem;
        text-align: center;
    }
    .consumption-input:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 2px rgba(234, 88, 12, 0.1);
    }
    .remaining-badge {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .remaining-high { background: #d1fae5; color: #065f46; }
    .remaining-medium { background: #fef3c7; color: #92400e; }
    .remaining-low { background: #fee2e2; color: #991b1b; }
    .info-box {
        background: #f0fdf4;
        border-left: 4px solid #10b981;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 0.75rem;
    }
    .btn-submit {
        background: #ea580c;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-submit:hover {
        background: #c2410c;
    }
    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .total-footer {
        background: #fef3c7;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .calculation-preview {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
</style>

<div class="space-y-4">

    {{-- Back Button --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('kitchen.consumption.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Requisitions
        </a>
    </div>

    {{-- Form Card --}}
    <div class="form-card">
        <div class="form-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-fire mr-2 text-orange-600"></i>
                Record Consumption - {{ $requisition->requisition_number }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">Record items used in food preparation today</p>
        </div>

        <div class="form-body">
            <div class="info-box">
                <i class="fas fa-info-circle mr-1 text-green-600"></i>
                You can record consumption in <strong>packs</strong> (e.g., cartons, crates) or <strong>individual pieces</strong>, or both.
            </div>

            <form method="POST" action="{{ route('kitchen.consumption.store', $requisition->id) }}" id="consumptionForm">
                @csrf

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 25%">Item</th>
                                <th style="width: 15%">Pack Info</th>
                                <th style="width: 15%" class="text-right">Remaining</th>
                                <th style="width: 15%" class="text-right">Packs Used</th>
                                <th style="width: 15%" class="text-right">Pieces Used</th>
                                <th style="width: 15%" class="text-right">Total Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->items as $index => $item)
                            @php
                                $remainingClass = $item->remaining_pieces > 10 ? 'remaining-high' : ($item->remaining_pieces > 0 ? 'remaining-medium' : 'remaining-low');
                                $maxPacks = $item->remaining_packs ?? 0;
                                $maxPieces = $item->remaining_pieces_extra ?? $item->remaining_pieces;
                            @endphp
                            @if($item->remaining_pieces > 0)
                            <tr>
                                <td class="font-medium text-gray-800">
                                    {{ $item->inventoryItem->name ?? 'N/A' }}
                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                </td>
                                <td>
                                    @if($item->issued_pack_type)
                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                            {{ ucfirst($item->issued_pack_type) }} = {{ $item->issued_pack_size }} pieces
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Individual item</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span class="remaining-badge {{ $remainingClass }}">
                                        @if($item->issued_pack_type && $item->remaining_packs > 0)
                                            {{ $item->remaining_packs }} {{ ucfirst($item->issued_pack_type) }}(s)
                                            @if($item->remaining_pieces_extra > 0)
                                                + {{ $item->remaining_pieces_extra }} pieces
                                            @endif
                                        @else
                                            {{ number_format($item->remaining_pieces, 0) }} pieces
                                        @endif
                                    </span>
                                </td>
                                <td class="text-right">
                                    @if($item->issued_pack_type)
                                        <input type="number"
                                               name="items[{{ $index }}][packs_consumed]"
                                               class="consumption-input packs-input"
                                               value="0"
                                               min="0"
                                               max="{{ $maxPacks }}"
                                               step="1"
                                               data-pack-size="{{ $item->issued_pack_size }}"
                                               data-index="{{ $index }}"
                                               onchange="calculateTotal({{ $index }})">
                                        <div class="calculation-preview" id="packsPreview_{{ $index }}"></div>
                                    @else
                                        —
                                        <input type="hidden" name="items[{{ $index }}][packs_consumed]" value="0">
                                    @endif
                                </td>
                                <td class="text-right">
                                    <input type="number"
                                           name="items[{{ $index }}][pieces_consumed]"
                                           class="consumption-input pieces-input"
                                           value="0"
                                           min="0"
                                           max="{{ $item->remaining_pieces }}"
                                           step="1"
                                           data-index="{{ $index }}"
                                           onchange="calculateTotal({{ $index }})">
                                </td>
                                <td class="text-right">
                                    <span id="totalDisplay_{{ $index }}" class="font-semibold text-orange-600">0</span>
                                    <span class="text-xs text-gray-500"> pieces</span>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5" class="text-right font-semibold">Total Consumed:</td>
                                <td class="text-right font-bold text-orange-600" id="grandTotal">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Notes --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" rows="2" placeholder="e.g., Used for lunch service, special event, etc."></textarea>
                </div>

                {{-- Total Footer --}}
                <div class="total-footer">
                    <span class="text-sm font-medium text-gray-700">Total Items Consumed Today:</span>
                    <span class="text-xl font-bold text-orange-600" id="grandTotalDisplay">0</span>
                    <span class="text-xs text-gray-500">pieces</span>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('kitchen.consumption.index') }}" class="btn-cancel">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Save Consumption
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemCount = {{ count($requisition->items) }};

        function calculateTotal(index) {
            const packsInput = document.querySelector(`input[name="items[${index}][packs_consumed]"]`);
            const piecesInput = document.querySelector(`input[name="items[${index}][pieces_consumed]"]`);
            const totalSpan = document.getElementById(`totalDisplay_${index}`);

            let packs = 0;
            let packSize = 1;
            let pieces = 0;

            if (packsInput) {
                packs = parseFloat(packsInput.value) || 0;
                packSize = parseFloat(packsInput.getAttribute('data-pack-size')) || 1;

                const previewSpan = document.getElementById(`packsPreview_${index}`);
                if (previewSpan && packs > 0) {
                    previewSpan.innerHTML = `${packs} pack(s) × ${packSize} = ${packs * packSize} pieces`;
                } else if (previewSpan) {
                    previewSpan.innerHTML = '';
                }
            }

            if (piecesInput) {
                pieces = parseFloat(piecesInput.value) || 0;
            }

            const total = (packs * packSize) + pieces;
            totalSpan.textContent = total;

            updateGrandTotal();
        }

        function updateGrandTotal() {
            let grandTotal = 0;

            for (let i = 0; i < itemCount; i++) {
                const totalSpan = document.getElementById(`totalDisplay_${i}`);
                if (totalSpan) {
                    grandTotal += parseFloat(totalSpan.textContent) || 0;
                }
            }

            document.getElementById('grandTotal').textContent = grandTotal;
            document.getElementById('grandTotalDisplay').textContent = grandTotal;
        }

        // Attach event listeners
        for (let i = 0; i < itemCount; i++) {
            const packsInput = document.querySelector(`input[name="items[${i}][packs_consumed]"]`);
            const piecesInput = document.querySelector(`input[name="items[${i}][pieces_consumed]"]`);

            if (packsInput) {
                packsInput.addEventListener('input', () => calculateTotal(i));
            }
            if (piecesInput) {
                piecesInput.addEventListener('input', () => calculateTotal(i));
            }
        }

        // Form validation
        document.getElementById('consumptionForm').addEventListener('submit', function(e) {
            let hasConsumption = false;

            for (let i = 0; i < itemCount; i++) {
                const packsInput = document.querySelector(`input[name="items[${i}][packs_consumed]"]`);
                const piecesInput = document.querySelector(`input[name="items[${i}][pieces_consumed]"]`);

                let packs = packsInput ? (parseFloat(packsInput.value) || 0) : 0;
                let pieces = piecesInput ? (parseFloat(piecesInput.value) || 0) : 0;

                if (packs > 0 || pieces > 0) {
                    hasConsumption = true;
                    break;
                }
            }

            if (!hasConsumption) {
                e.preventDefault();
                alert('Please enter at least one item quantity to record consumption.');
            }
        });

        // Initial calculation
        for (let i = 0; i < itemCount; i++) {
            calculateTotal(i);
        }
    });
</script>
@endsection
