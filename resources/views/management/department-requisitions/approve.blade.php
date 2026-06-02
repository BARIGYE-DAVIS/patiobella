{{-- resources/views/management/department-requisitions/approve.blade.php --}}

@extends('layouts.management')

@section('title', 'Approve Requisition')
@section('page-title', 'Approve Requisition')

@section('content')
<style>
    .batch-item {
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
        background: #f3f4f6;
        border-radius: 4px;
        margin: 0.1rem 0;
        display: inline-block;
    }
    .stock-level-bar {
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        overflow: hidden;
        margin-top: 5px;
    }
    .stock-level-fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.3s ease;
    }
    .stock-level-fill.good { background: #22c55e; }
    .stock-level-fill.low { background: #f97316; }
    .stock-level-fill.critical { background: #ef4444; }
    .batch-list {
        max-width: 200px;
    }
    .batch-list .batch {
        border-bottom: 1px solid #e5e7eb;
        padding: 4px 0;
    }
    .batch-list .batch:last-child {
        border-bottom: none;
    }
    .sig-img {
        max-height: 60px;
        max-width: 200px;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }
    .signature-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px dashed #e2e8f0;
    }

    /* CRITICAL FIX FOR PRINT - Horizontal alignment on same row */
    @media print {
        .signature-row {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            gap: 40px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .signature-box {
            flex: 1 !important;
            min-width: 200px !important;
            margin: 0 !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }
        .signature-section .grid {
            display: block !important;
        }
    }

    /* Screen styles - horizontal row */
    .signature-row {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
        gap: 30px;
        flex-wrap: wrap;
    }
    .signature-box {
        flex: 1;
        min-width: 250px;
        text-align: center;
        padding: 20px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .signature-line {
        border-top: 1px solid #cbd5e1;
        width: 80%;
        margin: 20px auto 10px auto;
    }
    .approver-preview {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
    }
    .approver-preview .signature-line {
        border-top-color: #f59e0b;
    }

    /* input highlight for warnings */
    .approved-qty.invalid {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.08);
    }
    .small-warning {
        font-size: 0.75rem;
        color: #b45309;
        margin-top: 6px;
    }
    .small-error {
        font-size: 0.75rem;
        color: #dc2626;
        margin-top: 6px;
    }
</style>

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
                <button onclick="window.print()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
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
            Review each requested item and enter the whole-number quantity you approve. Items with zero approved quantity will not be issued.
            <strong class="block mt-2 text-amber-700">Note: Available stock from all batches is shown below. You cannot approve more than available stock.</strong>
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
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 hidden">Batch Details</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Requested</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 bg-blue-50">Approved Qty <span class="text-red-500">*</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requisition->items as $index => $item)
                        @php
                            // Get batches for this inventory item
                            $batches = \App\Models\Batch::where('inventory_item_id', $item->inventory_item_id)
                                ->where('batch_status', 'active')
                                ->where('remaining_quantity', '>', 0)
                                ->orderBy('expiry_date', 'asc')
                                ->get();

                            $totalAvailableStock = $batches->sum('remaining_quantity');
                            $baseUnit = $item->inventoryItem->unit_of_measurement ?? 'units';
                            $currentMetrics = $item->metrics ?? $baseUnit;
                            $lowStock = $totalAvailableStock < $item->quantity_requested;
                            $stockPercentage = $item->quantity_requested > 0 ? min(100, ($totalAvailableStock / $item->quantity_requested) * 100) : 0;
                            $stockStatus = $stockPercentage >= 50 ? 'good' : ($stockPercentage >= 25 ? 'low' : 'critical');

                            // integer available cap
                            $availableInteger = (int) floor($totalAvailableStock);
                            $requestedInteger = (int) ceil($item->quantity_requested);
                            $defaultApproved = min($requestedInteger, $availableInteger);
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
                                @if($totalAvailableStock <= 0)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        Out of Stock
                                    </span>
                                @elseif($totalAvailableStock < $item->quantity_requested)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">
                                        {{ number_format($totalAvailableStock, 2) }} (Low Stock)
                                    </span>
                                @else
                                    <span class="text-green-600 font-semibold">
                                        {{ number_format($totalAvailableStock, 2) }}
                                    </span>
                                @endif
                                <div class="stock-level-bar w-24 mx-auto mt-1">
                                    <div class="stock-level-fill {{ $stockStatus }}" style="width: {{ $stockPercentage }}%"></div>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ number_format($stockPercentage, 0) }}% of requested
                                </div>
                             </td>
                            <td class="px-4 py-3 hidden">
                                @if($batches->count() > 0)
                                    <div class="batch-list">
                                        @foreach($batches as $batch)
                                            <div class="batch text-xs">
                                                <strong>{{ $batch->batch_number }}</strong><br>
                                                Qty: {{ number_format($batch->remaining_quantity, 2) }}
                                                @if($batch->expiry_date)
                                                    <br><span class="text-gray-400">Exp: {{ date('d/m/Y', strtotime($batch->expiry_date)) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No batches available</span>
                                @endif
                             </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold">{{ number_format($item->quantity_requested, 2) }}</span>
                                @if($item->requested_pack_type)
                                <div class="text-xs text-gray-400">{{ ucfirst($item->requested_pack_type) }} × {{ $item->requested_pack_size }}</div>
                                @endif
                             </td>
                            <td class="px-4 py-3 text-center">
                                <input type="text"
                                       name="items[{{ $index }}][quantity_approved]"
                                       inputmode="numeric"
                                       pattern="\d*"
                                       class="approved-qty w-32 px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ $defaultApproved }}"
                                       data-requested="{{ $requestedInteger }}"
                                       data-available="{{ $availableInteger }}"
                                       data-item-index="{{ $index }}"
                                       autocomplete="off"
                                       required>
                                <div class="text-xs text-gray-400 mt-1">Max: {{ number_format($availableInteger, 0) }}</div>
                                <div class="approval-note" id="note-{{ $index }}"></div>
                             </td>
                         </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-xs font-semibold text-gray-500">Totals</td>
                            <td class="px-4 py-3 text-center">-</td>
                            <td class="px-4 py-3 text-center font-semibold">
                                {{ number_format($requisition->items->sum('quantity_requested'), 2) }}
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-blue-600" id="totalApproved">
                                @php
                                    $totalApprovedPreview = 0;
                                    foreach($requisition->items as $item) {
                                        $batches = \App\Models\Batch::where('inventory_item_id', $item->inventory_item_id)
                                            ->where('batch_status', 'active')
                                            ->where('remaining_quantity', '>', 0)
                                            ->sum('remaining_quantity');
                                        $availableInteger = (int) floor($batches);
                                        $requestedInteger = (int) ceil($item->quantity_requested);
                                        $totalApprovedPreview += min($item->quantity_approved ?? $requestedInteger, $availableInteger);
                                    }
                                    echo number_format($totalApprovedPreview, 0);
                                @endphp
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

            {{-- SIGNATURES SECTION - HORIZONTAL ROW (SAME LINE) --}}
            <div class="signature-section">
                <div class="signature-row">
                    {{-- Requested By Signature --}}
                    <div class="signature-box">
                        <div class="mb-3">
                            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                            </svg>
                        </div>
                        <div class="font-semibold text-gray-700 mb-2">REQUESTED BY</div>
                        @if(!empty($requisition->requestedBy->signature_path))
                            <img src="{{ asset('storage/' . $requisition->requestedBy->signature_path) }}" alt="Requested by signature" class="sig-img">
                        @else
                            <div class="signature-line"></div>
                            <div class="text-xs text-gray-400 italic mt-2">No signature uploaded</div>
                        @endif
                        <div class="mt-3">
                            <div class="font-medium text-gray-800">{{ $requisition->requestedBy->first_name ?? 'N/A' }} {{ $requisition->requestedBy->last_name ?? '' }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $requisition->created_at->format('F d, Y g:i A') }}</div>
                        </div>
                    </div>

                    {{-- Approved By Signature --}}
                    <div class="signature-box approver-preview">
                        <div class="mb-3">
                            <svg class="w-8 h-8 mx-auto text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                            </svg>
                        </div>
                        <div class="font-semibold text-gray-700 mb-2">APPROVED BY</div>
                        <div class="signature-line"></div>
                        <div class="text-xs text-amber-600 italic mt-2">
                            <i class="fas fa-info-circle mr-1"></i> Signature will be applied upon approval
                        </div>
                        <div class="mt-3">
                            <div class="font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                            <div class="text-xs text-gray-500 mt-1">Pending Approval</div>
                        </div>
                        @if(Auth::user()->signature_path)
                        <div class="mt-2 pt-2 border-t border-amber-200">
                            <p class="text-xs text-gray-500">Your signature on file:</p>
                            <img src="{{ asset('storage/' . Auth::user()->signature_path) }}" alt="Your signature" class="sig-img" style="max-height: 40px;">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    // helper: sanitize to integer string (remove non-digits)
    function sanitizeToIntegerString(val) {
        if (val === undefined || val === null) return '';
        // remove non-digit characters
        let s = String(val).replace(/[^\d]/g, '');
        // strip leading zeros except keep single zero
        s = s.replace(/^0+/, '');
        return s === '' ? '0' : s;
    }

    function showWarningMessage(el, message, type = 'warning') {
        // el is the note container element
        if (!el) return;
        el.innerText = message;
        el.classList.remove('small-warning', 'small-error');
        el.classList.add(type === 'error' ? 'small-error' : 'small-warning');
    }

    function clearWarningMessage(el) {
        if (!el) return;
        el.innerText = '';
        el.classList.remove('small-warning', 'small-error');
    }

    function clampToAvailableAndInteger(input) {
        const available = parseInt(input.dataset.available || '0', 10);
        const requested = parseInt(input.dataset.requested || '0', 10);
        const idx = input.dataset.itemIndex;
        const noteEl = document.getElementById('note-' + idx);

        // sanitize current raw value to integer string
        let raw = input.value;
        let sanitized = sanitizeToIntegerString(raw);
        let val = parseInt(sanitized || '0', 10);

        // cap at available (integer)
        if (val > available) {
            val = available;
            showWarningMessage(noteEl, `Approved quantity capped to available stock (${available}).`, 'error');
            input.classList.add('invalid');
        } else {
            // clear error
            clearWarningMessage(noteEl);
            input.classList.remove('invalid');
        }

        // warn if approved exceeds requested (but allow)
        if (val > requested) {
            showWarningMessage(noteEl, `Approved quantity (${val}) exceeds requested (${requested}). Please confirm.`, 'warning');
            // keep input border normal unless also exceeding available
            if (val <= available) input.classList.remove('invalid');
        }

        // set integer value (no decimals)
        input.value = String(Math.floor(val));

        updateTotalApproved();
    }

    function updateTotalApproved() {
        let total = 0;
        document.querySelectorAll('.approved-qty').forEach(input => {
            const v = parseInt(sanitizeToIntegerString(input.value || '0'), 10) || 0;
            total += v;
        });
        const totalElement = document.getElementById('totalApproved');
        if (totalElement) {
            totalElement.innerText = total.toFixed(0);
        }
    }

    // attach behavior to inputs
    function attachApprovedQtyHandlers() {
        document.querySelectorAll('.approved-qty').forEach(input => {
            // ensure only digits allowed while typing (non-destructive)
            input.addEventListener('input', function(e) {
                // allow user typing but sanitize immediately to digits only
                const cleaned = sanitizeToIntegerString(this.value);
                // keep caret at end: set value to cleaned
                this.value = cleaned === '' ? '0' : cleaned;
                // don't yet force cap until blur/change to avoid interrupting typing too much,
                // but we will enforce on each input to keep values valid and integer.
                // enforce cap live for immediate feedback
                clampToAvailableAndInteger(this);
            });

            // on blur/change ensure clamped and integer
            input.addEventListener('change', function() {
                clampToAvailableAndInteger(this);
            });

            // on paste sanitize
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                const cleaned = sanitizeToIntegerString(text);
                this.value = cleaned === '' ? '0' : cleaned;
                clampToAvailableAndInteger(this);
            });
        });
    }

    // Set default approved quantity to min(requested, available) integer
    function setDefaultApprovedQuantities() {
        document.querySelectorAll('.approved-qty').forEach(input => {
            const available = parseInt(input.dataset.available || '0', 10);
            const requested = parseInt(input.dataset.requested || '0', 10);
            const defaultValue = Math.min(requested, available);
            input.value = String(Math.floor(defaultValue));
        });
        updateTotalApproved();
    }

    // Initialization
    document.addEventListener('DOMContentLoaded', function() {
        setDefaultApprovedQuantities();
        attachApprovedQtyHandlers();

        // form validation on submit: ensure at least one approved > 0 and none exceed available
        const form = document.getElementById('approveForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                let hasApprovedItems = false;
                let invalid = false;
                document.querySelectorAll('.approved-qty').forEach(input => {
                    const val = parseInt(sanitizeToIntegerString(input.value || '0'), 10) || 0;
                    const available = parseInt(input.dataset.available || '0', 10);
                    if (val > 0) hasApprovedItems = true;
                    if (val > available) {
                        invalid = true;
                        const noteEl = document.getElementById('note-' + input.dataset.itemIndex);
                        showWarningMessage(noteEl, `Approved quantity (${val}) exceeds available stock (${available}).`, 'error');
                        input.classList.add('invalid');
                    }
                });

                if (invalid) {
                    e.preventDefault();
                    alert('One or more approved quantities exceed available stock. Please correct them before submitting.');
                    return false;
                }

                if (!hasApprovedItems) {
                    e.preventDefault();
                    alert('Please approve at least one item by entering a whole-number quantity greater than zero.');
                    return false;
                }

                // successful: allow submit
            });
        }
    });
</script>
@endsection
