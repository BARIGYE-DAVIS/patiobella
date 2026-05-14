{{-- resources/views/procurement/cost-prices/edit.blade.php --}}

@extends('layouts.procurement')

@section('title', 'Edit Cost Price')

@section('page-title', 'Edit Cost Price')

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
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
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
    .history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .history-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }
    .history-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .calculation-preview {
        background: #fef3c7;
        padding: 0.75rem;
        border-radius: 8px;
        margin-top: 0.5rem;
        font-size: 0.8rem;
    }
    .radio-group {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }
    .radio-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .bulk-section, .simple-section {
        transition: all 0.3s;
    }
</style>

<div class="space-y-4">

    {{-- Back Button --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('procurement.cost-prices.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Cost Prices
        </a>
    </div>

    {{-- Main Form Card --}}
    <div class="form-card">
        <div class="form-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-edit mr-2 text-emerald-600"></i>
                Update Cost Price: {{ $item->name }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">Update the purchase cost for this inventory item</p>
        </div>

        <div class="form-body">
            {{-- Current Info --}}
            <div class="info-box">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="info-label">Item Code</div>
                        <div class="info-value">{{ $item->item_code ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Category</div>
                        <div class="info-value">{{ $item->category->name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Base Unit</div>
                        <div class="info-value">{{ $item->base_unit ?? 'piece' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Current Unit Cost</div>
                        <div class="info-value text-emerald-600">UGX {{ number_format($item->unit_cost ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>

            <form id="costPriceForm">
                @csrf
                @method('PUT')

                {{-- Update Method Selection --}}
                <div class="form-group mb-4">
                    <label class="form-label">Update Method <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="update_method" value="simple" checked>
                            <span>Per Unit (Simple)</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="update_method" value="bulk">
                            <span>Per Pack (Bulk)</span>
                        </label>
                    </div>
                </div>

                {{-- Simple Update Section --}}
                <div id="simpleSection" class="simple-section">
                    <div class="form-row">
                        <div>
                            <label class="form-label">New Unit Cost (UGX) <span class="required">*</span></label>
                            <input type="number" id="simple_unit_cost" class="form-input" step="0.01" min="0" placeholder="Enter new unit cost" value="{{ $item->unit_cost ?? 0 }}">
                        </div>
                    </div>
                    <div class="calculation-preview" id="simplePreview">
                        <i class="fas fa-info-circle mr-1"></i> New unit cost will be: <strong>UGX <span id="simplePreviewAmount">{{ number_format($item->unit_cost ?? 0, 2) }}</span></strong>
                    </div>
                </div>

                {{-- Bulk Update Section --}}
                <div id="bulkSection" class="bulk-section" style="display: none;">
                    <div class="form-row">
                        <div>
                            <label class="form-label">Pack Type <span class="required">*</span></label>
                            <select id="pack_type" class="form-select">
                                <option value="">Select Pack Type</option>
                                <option value="carton">Carton</option>
                                <option value="crate">Crate</option>
                                <option value="box">Box</option>
                                <option value="dozen">Dozen</option>
                                <option value="pack">Pack</option>
                                <option value="sack">Sack</option>
                                <option value="bottle">Bottle (Pack)</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Pack Size (pieces per pack) <span class="required">*</span></label>
                            <input type="number" id="pack_size" class="form-input" step="1" min="1" placeholder="e.g., 12">
                        </div>
                        <div>
                            <label class="form-label">Number of Packs <span class="required">*</span></label>
                            <input type="number" id="number_of_packs" class="form-input" step="1" min="1" placeholder="e.g., 1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div>
                            <label class="form-label">Total Pack Cost (UGX) <span class="required">*</span></label>
                            <input type="number" id="pack_cost" class="form-input" step="0.01" min="0" placeholder="Total cost for the packs">
                        </div>
                    </div>
                    <div class="calculation-preview" id="bulkPreview">
                        <i class="fas fa-calculator mr-1"></i> Calculation:
                        <span id="bulkCalculation">—</span><br>
                        <i class="fas fa-chart-line mr-1"></i> New unit cost will be: <strong>UGX <span id="bulkPreviewAmount">0.00</span></strong>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="form-group mt-4">
                    <label class="form-label">Reason for Change</label>
                    <textarea id="reason" class="form-textarea" rows="2" placeholder="e.g., Supplier price increase, bulk discount, new vendor..."></textarea>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('procurement.cost-prices.index') }}" class="btn-cancel">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="button" id="submitBtn" class="btn-save">
                        <i class="fas fa-save mr-1"></i> Update Cost Price
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Price History Card --}}
    <div class="form-card">
        <div class="form-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history mr-2 text-gray-500"></i>
                Price Change History
            </h3>
        </div>
        <div class="form-body">
            @if($priceHistory->count() > 0)
                <div class="overflow-x-auto">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Old Cost</th>
                                <th>New Cost</th>
                                <th>Changed By</th>
                                <th>Pack Details</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceHistory as $history)
                            <tr>
                                <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                                <td>UGX {{ number_format($history->old_unit_cost, 2) }}</td>
                                <td class="font-semibold text-emerald-600">UGX {{ number_format($history->new_unit_cost, 2) }}</td>
                                <td>{{ $history->changedBy->name ?? 'System' }}</td>
                                <td>
                                    @if($history->pack_type)
                                        {{ $history->number_of_packs }} {{ ucfirst($history->pack_type) }}(s) × {{ $history->pack_size }} = {{ number_format($history->total_base_units, 2) }} units
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-gray-500">{{ Str::limit($history->reason, 50) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $priceHistory->links() }}
                </div>
            @else
                <div class="text-center text-gray-500 py-4">
                    <i class="fas fa-history text-4xl mb-2 block"></i>
                    No price history available for this item.
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateMethodRadios = document.querySelectorAll('input[name="update_method"]');
        const simpleSection = document.getElementById('simpleSection');
        const bulkSection = document.getElementById('bulkSection');
        const simpleUnitCost = document.getElementById('simple_unit_cost');
        const simplePreviewAmount = document.getElementById('simplePreviewAmount');
        const packType = document.getElementById('pack_type');
        const packSize = document.getElementById('pack_size');
        const numberOfPacks = document.getElementById('number_of_packs');
        const packCost = document.getElementById('pack_cost');
        const bulkPreviewAmount = document.getElementById('bulkPreviewAmount');
        const bulkCalculation = document.getElementById('bulkCalculation');
        const submitBtn = document.getElementById('submitBtn');

        // Toggle between simple and bulk sections
        updateMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'simple') {
                    simpleSection.style.display = 'block';
                    bulkSection.style.display = 'none';
                } else {
                    simpleSection.style.display = 'none';
                    bulkSection.style.display = 'block';
                }
            });
        });

        // Simple price preview
        simpleUnitCost.addEventListener('input', function() {
            const value = parseFloat(this.value) || 0;
            simplePreviewAmount.textContent = value.toFixed(2);
        });

        // Bulk price calculation
        function calculateBulk() {
            const packs = parseFloat(numberOfPacks.value) || 0;
            const size = parseFloat(packSize.value) || 0;
            const cost = parseFloat(packCost.value) || 0;

            if (packs > 0 && size > 0 && cost > 0) {
                const totalUnits = packs * size;
                const unitCost = cost / totalUnits;
                bulkPreviewAmount.textContent = unitCost.toFixed(2);
                bulkCalculation.innerHTML = `${packs} ${packType.value || 'pack'}(s) × ${size} = ${totalUnits} units @ UGX ${cost.toFixed(2)} = UGX ${unitCost.toFixed(2)} per unit`;
            } else {
                bulkPreviewAmount.textContent = '0.00';
                bulkCalculation.innerHTML = '—';
            }
        }

        packType.addEventListener('change', calculateBulk);
        packSize.addEventListener('input', calculateBulk);
        numberOfPacks.addEventListener('input', calculateBulk);
        packCost.addEventListener('input', calculateBulk);

        // Submit form
        submitBtn.addEventListener('click', async function() {
            const updateMethod = document.querySelector('input[name="update_method"]:checked').value;
            const reason = document.getElementById('reason').value;
            const itemId = {{ $item->id }};

            let url, data;

            if (updateMethod === 'simple') {
                const unitCost = parseFloat(simpleUnitCost.value);
                if (isNaN(unitCost) || unitCost < 0) {
                    alert('Please enter a valid unit cost.');
                    return;
                }

                url = `/procurement/cost-prices/${itemId}/simple`;
                data = {
                    unit_cost: unitCost,
                    reason: reason
                };
            } else {
                const pack_type = packType.value;
                const pack_size = parseFloat(packSize.value);
                const number_of_packs = parseFloat(numberOfPacks.value);
                const pack_cost = parseFloat(packCost.value);

                if (!pack_type) {
                    alert('Please select a pack type.');
                    return;
                }
                if (isNaN(pack_size) || pack_size < 1) {
                    alert('Please enter a valid pack size.');
                    return;
                }
                if (isNaN(number_of_packs) || number_of_packs < 1) {
                    alert('Please enter a valid number of packs.');
                    return;
                }
                if (isNaN(pack_cost) || pack_cost < 0) {
                    alert('Please enter a valid pack cost.');
                    return;
                }

                url = `/procurement/cost-prices/${itemId}/bulk`;
                data = {
                    pack_type: pack_type,
                    pack_size: pack_size,
                    number_of_packs: number_of_packs,
                    pack_cost: pack_cost,
                    reason: reason
                };
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';

            try {
                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
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
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Update Cost Price';
            }
        });
    });
</script>
@endsection
