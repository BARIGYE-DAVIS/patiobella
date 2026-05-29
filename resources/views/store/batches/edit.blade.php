{{-- resources/views/store/batches/edit.blade.php --}}

@extends('layouts.store')

@section('title', 'Edit Batch')
@section('page-title', 'Edit Batch')

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
    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .form-label .required {
        color: #ef4444;
        margin-left: 0.25rem;
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
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .info-box {
        background: #f0fdf4;
        border-left: 4px solid #10b981;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    .info-label {
        font-size: 0.7rem;
        color: #6b7280;
    }
    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }
    .btn-save {
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
    .btn-save:hover {
        background: #059669;
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
    .btn-cancel:hover {
        background: #e5e7eb;
    }
    .expiry-warning {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
    }
    .expiry-danger {
        background: #fee2e2;
        border-left: 4px solid #dc2626;
    }
    .quantity-display {
        font-size: 1.5rem;
        font-weight: bold;
        color: #065f46;
    }
</style>

<div class="space-y-4">

    {{-- Back Button --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('batches.show', $batch->id) }}" class="text-gray-600 hover:text-gray-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Batch Details
        </a>
    </div>

    {{-- Main Form Card --}}
    <div class="form-card">
        <div class="form-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-edit mr-2 text-emerald-600"></i>
                Edit Batch: {{ $batch->batch_number }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">Update batch expiry date, manufacture date, or quantity</p>
        </div>

        <div class="form-body">
            {{-- Current Info --}}
            <div class="info-box">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="info-label">Item Name</div>
                        <div class="info-value">{{ $item->name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Item Code</div>
                        <div class="info-value">{{ $item->item_code ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Base Unit</div>
                        <div class="info-value">{{ $batch->base_unit ?? $item->base_unit ?? 'piece' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Remaining Quantity</div>
                        <div class="quantity-display">{{ number_format($batch->remaining_quantity, 2) }} {{ $batch->base_unit ?? $item->base_unit ?? 'pcs' }}</div>
                    </div>
                </div>
            </div>

            {{-- Expiry Warning --}}
            @if($batch->expiry_date && $batch->expiry_date < now())
                <div class="expiry-danger p-3 rounded-lg mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>EXPIRED!</strong> This batch expired on {{ $batch->expiry_date->format('d M Y') }}.
                    Please dispose or mark as depleted.
                </div>
            @elseif($batch->expiry_date && $batch->expiry_date <= now()->addDays(30))
                <div class="expiry-warning p-3 rounded-lg mb-4">
                    <i class="fas fa-clock mr-2"></i>
                    <strong>Expiring Soon!</strong> This batch expires on {{ $batch->expiry_date->format('d M Y') }}.
                    Consider using it first (FIFO).
                </div>
            @endif

            <form id="batchForm">
                @csrf
                @method('PUT')

                {{-- Section 1: Update Manufacture Date --}}
                <div class="form-group mb-4">
                    <label class="form-label">Manufacture Date</label>
                    <input type="date" id="manufacture_date" class="form-input" value="{{ $batch->manufacture_date ? $batch->manufacture_date->format('Y-m-d') : '' }}">
                    <div class="text-xs text-gray-500 mt-1">When was this batch manufactured?</div>
                </div>

                {{-- Section 2: Update Expiry Date --}}
                <div class="form-group mb-4">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" id="expiry_date" class="form-input" value="{{ $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '' }}">
                    <div class="text-xs text-gray-500 mt-1">When does this batch expire?</div>
                </div>

                {{-- Section 3: Adjust Remaining Quantity --}}
                <div class="form-group mb-4 pt-4 border-t border-gray-200">
                    <label class="form-label">Adjust Remaining Quantity</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <select id="adjustment_type" class="form-select">
                                <option value="set">Set to specific quantity</option>
                                <option value="add">Add quantity</option>
                                <option value="subtract">Subtract quantity</option>
                            </select>
                        </div>
                        <div>
                            <input type="number" id="adjustment_quantity" class="form-input" step="0.01" min="0" placeholder="Quantity">
                        </div>
                        <div>
                            <button type="button" id="adjustQuantityBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm w-full">
                                Apply Adjustment
                            </button>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Current quantity: <strong id="current_quantity">{{ number_format($batch->remaining_quantity, 2) }}</strong> {{ $batch->base_unit ?? $item->base_unit ?? 'pcs' }}
                    </div>
                </div>

                {{-- Reason --}}
                <div class="form-group mt-4">
                    <label class="form-label">Reason for Change</label>
                    <textarea id="reason" class="form-textarea" rows="2" placeholder="e.g., Expiry date correction, damaged stock, recount adjustment..."></textarea>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('batches.show', $batch->id) }}" class="btn-cancel">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="button" id="updateBatchBtn" class="btn-save">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const batchId = {{ $batch->id }};
        const baseUnit = '{{ $batch->base_unit ?? $item->base_unit ?? 'piece' }}';

        // ─────────────────────────────────────────────────────────
        // Update Manufacture Date
        // ─────────────────────────────────────────────────────────
        const manufactureDateInput = document.getElementById('manufacture_date');
        const expiryDateInput = document.getElementById('expiry_date');
        const updateBatchBtn = document.getElementById('updateBatchBtn');

        updateBatchBtn.addEventListener('click', async function() {
            const manufactureDate = manufactureDateInput.value;
            const expiryDate = expiryDateInput.value;
            const reason = document.getElementById('reason').value;

            if (!manufactureDate && !expiryDate) {
                alert('Please enter at least one date to update.');
                return;
            }

            updateBatchBtn.disabled = true;
            updateBatchBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';

            try {
                // Update manufacture date if changed
                if (manufactureDate) {
                    const manufactureResponse = await fetch(`/store/batches/${batchId}/manufacture-date`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            manufacture_date: manufactureDate,
                            reason: reason || 'Manufacture date updated'
                        })
                    });
                    const manufactureResult = await manufactureResponse.json();
                    if (!manufactureResult.success) {
                        alert('Error updating manufacture date: ' + manufactureResult.message);
                        updateBatchBtn.disabled = false;
                        updateBatchBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Save Changes';
                        return;
                    }
                }

                // Update expiry date if changed
                if (expiryDate) {
                    const expiryResponse = await fetch(`/store/batches/${batchId}/expiry`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            expiry_date: expiryDate,
                            reason: reason || 'Expiry date updated'
                        })
                    });
                    const expiryResult = await expiryResponse.json();
                    if (!expiryResult.success) {
                        alert('Error updating expiry date: ' + expiryResult.message);
                        updateBatchBtn.disabled = false;
                        updateBatchBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Save Changes';
                        return;
                    }
                }

                alert('Batch updated successfully!');
                window.location.href = '{{ route("batches.show", $batch->id) }}';
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                updateBatchBtn.disabled = false;
                updateBatchBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Save Changes';
            }
        });

        // ─────────────────────────────────────────────────────────
        // Adjust Quantity
        // ─────────────────────────────────────────────────────────
        const adjustmentType = document.getElementById('adjustment_type');
        const adjustmentQuantity = document.getElementById('adjustment_quantity');
        const adjustQuantityBtn = document.getElementById('adjustQuantityBtn');

        adjustQuantityBtn.addEventListener('click', async function() {
            const type = adjustmentType.value;
            const quantity = parseFloat(adjustmentQuantity.value);

            if (isNaN(quantity) || quantity <= 0) {
                alert('Please enter a valid quantity.');
                return;
            }

            const reason = document.getElementById('reason').value;

            let confirmMessage = '';
            if (type === 'set') {
                confirmMessage = `Are you sure you want to SET the quantity to ${quantity} ${baseUnit}?`;
            } else if (type === 'add') {
                confirmMessage = `Are you sure you want to ADD ${quantity} ${baseUnit} to this batch?`;
            } else {
                confirmMessage = `Are you sure you want to SUBTRACT ${quantity} ${baseUnit} from this batch?`;
            }

            if (!confirm(confirmMessage)) {
                return;
            }

            adjustQuantityBtn.disabled = true;
            adjustQuantityBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';

            try {
                const response = await fetch(`/store/batches/${batchId}/quantity`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        adjustment_type: type,
                        quantity: quantity,
                        reason: reason || `Manual quantity adjustment: ${type} ${quantity} ${baseUnit}`
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                adjustQuantityBtn.disabled = false;
                adjustQuantityBtn.innerHTML = 'Apply Adjustment';
            }
        });
    });
</script>
@endsection
