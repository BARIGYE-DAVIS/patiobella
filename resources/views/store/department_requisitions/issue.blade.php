@extends('layouts.store')

@section('title', 'Issue Items')

@section('page-title', 'Issue Items to Department')

@section('content')
<style>
    .taken-by-section {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .taken-by-label {
        font-weight: 600;
        color: #166534;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .taken-by-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #dcfce7;
        border-radius: 10px;
        background-color: #ffffff;
        transition: all 0.2s;
    }
    .taken-by-input:focus {
        outline: none;
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    .hidden-column {
        display: none;
    }
    .exceed-warning {
        color: #dc2626;
        font-size: 0.7rem;
        margin-top: 0.25rem;
    }
    .exceed-input {
        border-color: #dc2626;
        background-color: #fef2f2;
    }
</style>

<div class="space-y-4">

    {{-- ── Page Header ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('store.department-requisitions.show', $requisition->id) }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <div class="h-5 w-px bg-gray-200"></div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 leading-tight">
                    Issue Items — {{ $requisition->requisition_number }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $requisition->department->name ?? 'Department' }} &middot; {{ $requisition->created_at->format('F d, Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ── Info Note ── --}}
    <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg px-5 py-4">
        <p class="text-sm text-blue-800">
            <strong>Note:</strong> You cannot issue more than the <strong>approved quantity</strong> for each item.
            The <em>Requested</em> and <em>Approved</em> columns are shown for reference.
            Only available stock limits what you can issue.
        </p>
    </div>

    <form method="POST" action="{{ route('store.department-requisitions.issue', $requisition->id) }}" id="issueForm">
        @csrf

        {{-- ── Taken By ── --}}
        <div class="taken-by-section">
            <label for="taken_by" class="taken-by-label">
                <span class="text-red-500">*</span> Who is taking these items?
            </label>
            <input type="text"
                   name="taken_by"
                   id="taken_by"
                   value="{{ old('taken_by') }}"
                   class="taken-by-input"
                   placeholder="Enter full name of the department staff receiving the items"
                   required>
            <p class="text-xs text-gray-500 mt-2 ml-1">
                ⚠️ This is a required field. The person signing for these items will be recorded in the stock movement.
            </p>
        </div>

        {{-- ── Items Table ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Items to Issue</h3>
                <span class="text-xs text-gray-500">
                    {{ $requisition->items->count() }} item{{ $requisition->items->count() !== 1 ? 's' : '' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Item</th>
                            <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Base Unit</th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">
                                Requested
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-blue-600 bg-blue-50">
                                Approved
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Available Stock</th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-green-600 border-l border-gray-200 bg-green-50">
                                Qty to Issue
                                <div class="text-[9px] font-normal normal-case text-gray-500">(max = approved)</div>
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-green-600 bg-green-50 pack-type-col hidden-column">
                                Pack Type
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-green-600 bg-green-50 pack-info-col hidden-column">
                                Pack Info
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-green-600 bg-green-50">
                                Total Pieces
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requisition->items as $item)
                        @php
                            $baseUnit      = $item->inventoryItem->base_unit ?? 'units';
                            $requestedQty  = (float) $item->quantity_requested;
                            $approvedQty   = (float) ($item->quantity_approved ?? $requestedQty);
                            $alreadyIssued = (float) ($item->issued_total_pieces ?? 0);
                            $remainingToIssue = max(0, $approvedQty - $alreadyIssued);
                            $stockAvail    = $item->inventoryItem->current_stock ?? 0;
                            $maxIssue      = min($remainingToIssue, $stockAvail);
                            $lowStock      = $stockAvail <= 0;
                            $fullyIssued   = $remainingToIssue <= 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $lowStock ? 'bg-red-50' : '' }} {{ $fullyIssued ? 'bg-gray-50 opacity-60' : '' }}" id="row_{{ $loop->index }}">

                            {{-- Item --}}
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800 text-sm">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $loop->index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                                @if($alreadyIssued > 0)
                                    <div class="text-xs text-blue-500 mt-1">
                                        Already issued: {{ number_format($alreadyIssued, 2) }} {{ $baseUnit }}
                                    </div>
                                @endif
                                @if($fullyIssued)
                                    <div class="text-xs text-green-600 mt-1 font-medium">
                                        ✓ Fully issued
                                    </div>
                                @endif
                            </td>

                            {{-- Base Unit --}}
                            <td class="px-4 py-3 text-gray-500 text-sm">{{ $baseUnit }}</td>

                            {{-- Requested --}}
                            <td class="px-4 py-3 text-center tabular-nums">
                                <span class="font-semibold text-gray-600">{{ number_format($requestedQty, 2) }}</span>
                            </td>

                            {{-- Approved --}}
                            <td class="px-4 py-3 text-center tabular-nums bg-blue-50">
                                <span class="font-semibold text-blue-700">{{ number_format($approvedQty, 2) }}</span>
                                <div class="text-xs text-gray-500">{{ $baseUnit }}</div>
                                @if($remainingToIssue > 0 && $remainingToIssue < $approvedQty)
                                    <div class="text-xs text-orange-500">Remaining: {{ number_format($remainingToIssue, 2) }}</div>
                                @endif
                            </td>

                            {{-- Available Stock --}}
                            <td class="px-4 py-3 text-center tabular-nums">
                                @if($lowStock)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        Out of stock
                                    </span>
                                @else
                                    <span class="font-semibold {{ $stockAvail < 10 ? 'text-orange-600' : 'text-emerald-600' }}">
                                        {{ number_format($stockAvail, 2) }}
                                    </span>
                                    <div class="text-xs text-gray-400">{{ $baseUnit }}</div>
                                @endif
                            </td>

                            {{-- Qty to Issue — max = remaining approved quantity --}}
                            <td class="px-4 py-3 text-center border-l border-gray-100">
                                @if($fullyIssued)
                                    <span class="text-green-600 text-sm font-medium">Fully issued</span>
                                    <input type="hidden" name="items[{{ $loop->index }}][quantity_issued]" value="0">
                                @else
                                    <input type="number"
                                           name="items[{{ $loop->index }}][quantity_issued]"
                                           id="qty_{{ $loop->index }}"
                                           class="quantity-issued w-24 px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                                           value="0"
                                           min="0"
                                           step="0.01"
                                           max="{{ $maxIssue }}"
                                           data-max="{{ $maxIssue }}"
                                           data-approved="{{ $approvedQty }}"
                                           data-already-issued="{{ $alreadyIssued }}"
                                           data-stock="{{ $stockAvail }}"
                                           data-baseunit="{{ $baseUnit }}"
                                           data-index="{{ $loop->index }}"
                                           oninput="updateTotal({{ $loop->index }})">
                                    <div id="warning_{{ $loop->index }}" class="exceed-warning hidden"></div>
                                @endif
                            </td>

                            {{-- Pack Type (HIDDEN) --}}
                            <td class="px-4 py-3 text-center pack-type-col hidden-column">
                                <select name="items[{{ $loop->index }}][pack_type]"
                                        id="pack_type_{{ $loop->index }}"
                                        class="pack-type w-28 px-2 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
                                        disabled>
                                    <option value="">— Direct —</option>
                                    <option value="carton">Carton</option>
                                    <option value="box">Box</option>
                                    <option value="crate">Crate</option>
                                    <option value="dozen">Dozen</option>
                                    <option value="pack">Pack</option>
                                    <option value="sack">Sack</option>
                                    <option value="set">Set</option>
                                </select>
                            </td>

                            {{-- Pack Info (HIDDEN) --}}
                            <td class="px-4 py-3 text-center pack-info-col hidden-column">
                                <input type="number"
                                       name="items[{{ $loop->index }}][pack_size]"
                                       id="pack_size_{{ $loop->index }}"
                                       class="pack-size w-20 px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
                                       placeholder="e.g. 24"
                                       step="1" min="1"
                                       disabled>
                            </td>

                            {{-- Total Pieces --}}
                            <td class="px-4 py-3 text-center tabular-nums font-semibold" id="total_cell_{{ $loop->index }}">
                                @if($fullyIssued)
                                    <span class="text-green-600">0.00</span>
                                    <input type="hidden" id="total_pieces_{{ $loop->index }}" name="items[{{ $loop->index }}][issued_total_pieces]" value="0">
                                @else
                                    <span id="total_display_{{ $loop->index }}" class="text-gray-300">—</span>
                                    <input type="hidden"
                                           id="total_pieces_{{ $loop->index }}"
                                           name="items[{{ $loop->index }}][issued_total_pieces]"
                                           data-baseunit="{{ $baseUnit }}"
                                           data-max="{{ $maxIssue }}"
                                           value="0">
                                @endif
                            </td>

                            {{-- Notes --}}
                            <td class="px-4 py-3">
                                <input type="text" name="items[{{ $loop->index }}][notes]"
                                       class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                       placeholder="Optional notes...">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    {{-- Totals footer --}}
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Totals</td>
                            <td class="px-4 py-3 border-l border-gray-200"></td>
                            <td class="px-4 py-3 pack-type-col hidden-column"></td>
                            <td class="px-4 py-3 pack-info-col hidden-column"></td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-green-600">
                                <span id="grandTotal">0.00</span>
                                <div class="text-xs text-gray-400 font-normal">base units</div>
                            </td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Store Notes ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-5 mt-4">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Store Notes (Optional)</label>
            <textarea name="store_notes" rows="2"
                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Any notes for the department..."></textarea>
        </div>

        {{-- ── Actions ── --}}
        <div class="mt-4 flex justify-end gap-3">
            <a href="{{ route('store.department-requisitions.show', $requisition->id) }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-green-600 text-sm text-white rounded-lg hover:bg-green-700 transition font-medium">
                Confirm Issue
            </button>
        </div>

    </form>
</div>

<script>
    function updateTotal(index) {
        const qtyInput = document.getElementById(`qty_${index}`);
        if (!qtyInput) return;

        const qty = parseFloat(qtyInput.value) || 0;
        const maxAllowed = parseFloat(qtyInput.getAttribute('data-max')) || 0;
        const baseUnit = qtyInput.getAttribute('data-baseunit');
        const stockAvail = parseFloat(qtyInput.getAttribute('data-stock')) || 0;
        const approvedQty = parseFloat(qtyInput.getAttribute('data-approved')) || 0;
        const alreadyIssued = parseFloat(qtyInput.getAttribute('data-already-issued')) || 0;
        const remainingToIssue = maxAllowed;

        const totalDisplay = document.getElementById(`total_display_${index}`);
        const totalInput = document.getElementById(`total_pieces_${index}`);
        const warningSpan = document.getElementById(`warning_${index}`);

        let isValid = true;
        let warningMessage = '';

        // Check against approved remaining quantity
        if (qty > remainingToIssue) {
            isValid = false;
            warningMessage = `Cannot exceed remaining approved quantity (${remainingToIssue.toFixed(2)} ${baseUnit})`;
            qtyInput.classList.add('exceed-input');
        }
        // Check against stock availability
        else if (qty > stockAvail) {
            isValid = false;
            warningMessage = `Insufficient stock. Available: ${stockAvail.toFixed(2)} ${baseUnit}`;
            qtyInput.classList.add('exceed-input');
        }
        else {
            qtyInput.classList.remove('exceed-input');
        }

        // Show/hide warning
        if (warningSpan) {
            if (!isValid && qty > 0) {
                warningSpan.textContent = warningMessage;
                warningSpan.classList.remove('hidden');
            } else {
                warningSpan.textContent = '';
                warningSpan.classList.add('hidden');
            }
        }

        // Calculate total pieces (direct issue = quantity)
        let totalPieces = qty;

        if (qty > 0 && isValid) {
            totalDisplay.innerHTML = `<span class="text-green-600">${qty.toFixed(2)} <span class="text-xs text-gray-400 font-normal">${baseUnit}</span></span>`;
        } else if (qty > 0 && !isValid) {
            totalDisplay.innerHTML = `<span class="text-red-500">${qty.toFixed(2)} <span class="text-xs font-normal">${baseUnit}</span></span>
                <div class="text-[10px] text-red-400 mt-0.5">${warningMessage}</div>`;
        } else {
            totalDisplay.innerHTML = `<span class="text-gray-300">—</span>`;
        }

        if (totalInput) {
            totalInput.value = isValid ? totalPieces : 0;
        }

        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('[id^="total_pieces_"]').forEach(input => {
            grand += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').textContent = grand.toFixed(2);
    }

    // Form validation
    document.getElementById('issueForm').addEventListener('submit', function(e) {
        const takenBy = document.getElementById('taken_by').value.trim();
        if (!takenBy) {
            e.preventDefault();
            alert('Please enter the name of the person taking these items.');
            document.getElementById('taken_by').focus();
            return false;
        }

        let hasQty = false;
        let hasInvalid = false;
        let invalidMessages = [];

        document.querySelectorAll('.quantity-issued').forEach(input => {
            const qty = parseFloat(input.value) || 0;
            const maxAllowed = parseFloat(input.getAttribute('data-max')) || 0;
            const stockAvail = parseFloat(input.getAttribute('data-stock')) || 0;
            const baseUnit = input.getAttribute('data-baseunit');

            if (qty > 0) {
                hasQty = true;
                if (qty > maxAllowed) {
                    hasInvalid = true;
                    invalidMessages.push(`Cannot issue ${qty} ${baseUnit} - only ${maxAllowed.toFixed(2)} ${baseUnit} remaining from approved quantity`);
                } else if (qty > stockAvail) {
                    hasInvalid = true;
                    invalidMessages.push(`Insufficient stock: ${qty} ${baseUnit} requested, only ${stockAvail.toFixed(2)} ${baseUnit} available`);
                }
            }
        });

        if (!hasQty) {
            e.preventDefault();
            alert('Please enter at least one item quantity to issue.');
            return false;
        }

        if (hasInvalid) {
            e.preventDefault();
            alert('Invalid quantities:\n\n' + invalidMessages.join('\n'));
            return false;
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($requisition->items as $item)
            @php
                $approvedQty = (float) ($item->quantity_approved ?? $item->quantity_requested);
                $alreadyIssued = (float) ($item->issued_total_pieces ?? 0);
                $remainingToIssue = max(0, $approvedQty - $alreadyIssued);
            @endphp
            @if($remainingToIssue > 0)
                updateTotal({{ $loop->index }});
            @endif
        @endforeach
    });
</script>
@endsection
