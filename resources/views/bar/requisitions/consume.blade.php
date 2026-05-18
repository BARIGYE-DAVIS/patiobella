{{-- resources/views/bar/requisitions/consume.blade.php --}}

@extends('layouts.bar')

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
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
    }
    .form-body {
        padding: 1.5rem;
    }
    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }
    .form-textarea {
        min-height: 80px;
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
    .btn-submit {
        background: #10b981;
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
        background: #059669;
    }
    .btn-cancel {
        background: #6b7280;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-cancel:hover {
        background: #4b5563;
    }
    .info-box {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
    }
    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .status-issued { background: #d1fae5; color: #065f46; }
    .status-partially_issued { background: #fef3c7; color: #92400e; }
    .status-partially_consumed { background: #fed7aa; color: #9a3412; }
</style>

<div class="form-card">
    <div class="form-header">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-utensils mr-2 text-green-600"></i>
            Record Consumption - {{ $requisition->requisition_number }}
        </h3>
        <p class="text-xs text-gray-500 mt-1">Record items that have been used/consumed from this requisition</p>
    </div>

    <div class="form-body">
        {{-- Info Box --}}
        <div class="info-box">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-amber-600 mt-0.5 mr-2"></i>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Important</p>
                    <p class="text-xs text-amber-700">Record consumption only for items that have been physically used. Consumed quantities will be deducted from available stock.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('bar.requisitions.consume.store', $requisition->id) }}" id="consumeForm">
            @csrf

            <div class="mb-4">
                <label class="form-label">Consumption Notes (Optional)</label>
                <textarea name="consumption_notes" class="form-textarea" placeholder="Add any notes about consumption...">{{ old('consumption_notes') }}</textarea>
            </div>

            <div class="mt-4 mb-3">
                <h4 class="font-semibold text-gray-700">Items Available for Consumption</h4>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 35%">Item</th>
                            <th style="width: 15%" class="text-center">Issued</th>
                            <th style="width: 15%" class="text-center">Already Consumed</th>
                            <th style="width: 15%" class="text-center">Already Returned</th>
                            <th style="width: 20%" class="text-center">Available to Consume</th>
                            <th style="width: 20%" class="text-center">Quantity to Consume Now</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consumableItems as $item)
                        @php
                            $issued = (float) ($item->issued_total_pieces ?? 0);
                            $consumed = (float) ($item->quantity_consumed ?? 0);
                            $returned = (float) ($item->returned_total_pieces ?? 0);
                            $available = $issued - $consumed - $returned;
                            $unit = $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units');
                        @endphp
                        <tr>
                            <td class="font-medium">
                                {{ $item->inventoryItem->name ?? 'N/A' }}
                                @if($item->issued_pack_type)
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-cubes"></i> Issued as: {{ ucfirst($item->issued_pack_type) }}
                                    @if($item->issued_pack_size) ({{ $item->issued_pack_size }} pcs/pack) @endif
                                </div>
                                @endif
                            </td
                            <td class="text-center">{{ number_format($issued, 2) }} {{ $unit }}</td
                            <td class="text-center">{{ number_format($consumed, 2) }} {{ $unit }}</td
                            <td class="text-center">{{ number_format($returned, 2) }} {{ $unit }}</td
                            <td class="text-center">
                                <span class="font-semibold text-green-600">{{ number_format($available, 2) }} {{ $unit }}</span>
                            </td
                            <td class="text-center">
                                <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                                <input type="number" name="items[{{ $loop->index }}][quantity_consumed]"
                                       class="form-input consume-quantity text-center"
                                       step="0.01" min="0" max="{{ $available }}"
                                       value="{{ old("items.{$loop->index}.quantity_consumed", 0) }}"
                                       style="width: 100px; margin: 0 auto;">
                                <div class="text-xs text-gray-400 mt-1">Max: {{ number_format($available, 2) }}</div>
                            </td
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('bar.requisitions.show', $requisition->id) }}" class="btn-cancel">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save mr-1"></i> Record Consumption
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Validate quantities before submission
    document.getElementById('consumeForm').addEventListener('submit', function(e) {
        let hasValidConsumption = false;
        const quantities = document.querySelectorAll('.consume-quantity');

        for (let i = 0; i < quantities.length; i++) {
            const qty = parseFloat(quantities[i].value);
            if (!isNaN(qty) && qty > 0) {
                hasValidConsumption = true;
                break;
            }
        }

        if (!hasValidConsumption) {
            e.preventDefault();
            alert('Please enter at least one item quantity to record consumption.');
        }
    });
</script>
@endsection
