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
            <strong>Note:</strong> For pack items (carton, box, etc.) — select the pack type and enter how many pieces are in each pack. The total pieces will be calculated automatically. For direct units (kg, litres, pcs) — just enter the quantity directly.
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
                            <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Unit</th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Requested</th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Prev. Issued</th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Remaining</th>

                            {{-- Issue group --}}
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-green-600 border-l border-gray-200 bg-green-50">
                                Qty to Issue
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-green-600 bg-green-50">
                                Pack Type
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-green-600 bg-green-50">
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
                            $remaining    = $item->quantity_requested - $item->quantity_issued;
                            $baseUnit     = $item->inventoryItem->base_unit ?? 'units';
                            $reqPackType  = $item->requested_pack_type ?? null;
                            $reqPackSize  = $item->requested_pack_size ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors" id="row_{{ $loop->index }}">

                            {{-- Item --}}
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800 text-sm">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $loop->index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                            </td>

                            {{-- Unit --}}
                            <td class="px-4 py-3 text-gray-500 text-sm">{{ $baseUnit }}</td>

                            {{-- Requested --}}
                            <td class="px-4 py-3 text-center tabular-nums font-semibold text-gray-800">
                                {{ number_format($item->quantity_requested, 2) }}
                                @if($reqPackType)
                                    <div class="text-xs text-gray-400 font-normal">{{ ucfirst($reqPackType) }}{{ $reqPackSize ? ' × '.$reqPackSize.' '.$baseUnit : '' }}</div>
                                @else
                                    <div class="text-xs text-gray-400 font-normal">{{ $baseUnit }}</div>
                                @endif
                            </td>

                            {{-- Previously Issued --}}
                            <td class="px-4 py-3 text-center tabular-nums text-orange-600 font-semibold">
                                {{ number_format($item->quantity_issued, 2) }}
                            </td>

                            {{-- Remaining --}}
                            <td class="px-4 py-3 text-center tabular-nums font-semibold text-blue-600">
                                {{ number_format($remaining, 2) }}
                            </td>

                            {{-- Qty to Issue --}}
                            <td class="px-4 py-3 text-center border-l border-gray-100">
                                <input type="number"
                                       name="items[{{ $loop->index }}][quantity_issued]"
                                       id="qty_{{ $loop->index }}"
                                       class="quantity-issued w-24 px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                                       value="0" min="0" max="{{ $remaining }}" step="0.01"
                                       data-max="{{ $remaining }}"
                                       data-index="{{ $loop->index }}"
                                       oninput="recalculate({{ $loop->index }})">
                            </td>

                            {{-- Pack Type --}}
                            <td class="px-4 py-3 text-center">
                                <select name="items[{{ $loop->index }}][pack_type]"
                                        id="pack_type_{{ $loop->index }}"
                                        class="pack-type w-28 px-2 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
                                        data-index="{{ $loop->index }}"
                                        onchange="recalculate({{ $loop->index }})">
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

                            {{-- Pack Info (pcs per pack input + live label like show view) --}}
                            <td class="px-4 py-3 text-center" id="pack_info_cell_{{ $loop->index }}">
                                <input type="number"
                                       name="items[{{ $loop->index }}][pack_size]"
                                       id="pack_size_{{ $loop->index }}"
                                       class="pack-size w-20 px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
                                       placeholder="e.g. 24"
                                       step="1" min="1"
                                       data-index="{{ $loop->index }}"
                                       oninput="recalculate({{ $loop->index }})">
                                {{-- Live pack info badge — shown when pack type + size are filled --}}
                                <div id="pack_badge_{{ $loop->index }}" class="mt-1.5 hidden">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded bg-green-50 text-green-700 border border-green-100 font-mono" id="pack_badge_text_{{ $loop->index }}"></span>
                                </div>
                                {{-- Direct label shown when no pack type --}}
                                <div id="direct_badge_{{ $loop->index }}" class="mt-1.5">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded bg-gray-50 text-gray-500 border border-gray-200">
                                        Direct {{ $baseUnit }}
                                    </span>
                                </div>
                            </td>

                            {{-- Total Pieces --}}
                            <td class="px-4 py-3 text-center tabular-nums font-semibold" id="total_cell_{{ $loop->index }}">
                                <span id="total_display_{{ $loop->index }}" class="text-gray-300">—</span>
                                <input type="hidden"
                                       id="total_pieces_{{ $loop->index }}"
                                       data-baseunit="{{ $baseUnit }}"
                                       value="0">
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
                            <td colspan="2" class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Totals</td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-gray-800">
                                {{ number_format($requisition->items->sum('quantity_requested'), 2) }}
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-orange-600">
                                {{ number_format($requisition->items->sum('quantity_issued'), 2) }}
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-blue-600">
                                {{ number_format($requisition->items->sum(fn($i) => $i->quantity_requested - $i->quantity_issued), 2) }}
                            </td>
                            <td class="px-4 py-3 border-l border-gray-200"></td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3"></td>
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
    function recalculate(index) {
        const qtyInput  = document.getElementById(`qty_${index}`);
        const qty       = parseFloat(qtyInput.value) || 0;
        const packType  = document.getElementById(`pack_type_${index}`).value;
        const packSize  = parseFloat(document.getElementById(`pack_size_${index}`).value) || 0;
        const baseUnit  = document.getElementById(`total_pieces_${index}`).getAttribute('data-baseunit');
        const maxQty    = parseFloat(qtyInput.getAttribute('data-max'));

        // Clamp qty to max
        if (qty > maxQty) qtyInput.value = maxQty;

        const packBadge     = document.getElementById(`pack_badge_${index}`);
        const packBadgeText = document.getElementById(`pack_badge_text_${index}`);
        const directBadge   = document.getElementById(`direct_badge_${index}`);
        const totalDisplay  = document.getElementById(`total_display_${index}`);
        const totalInput    = document.getElementById(`total_pieces_${index}`);
        const packSizeInput = document.getElementById(`pack_size_${index}`);

        let totalPieces = 0;

        if (packType) {
            // Pack mode
            packSizeInput.classList.remove('hidden');
            directBadge.classList.add('hidden');

            if (packSize > 0) {
                totalPieces = qty * packSize;

                // Show badge like show view: "Carton × 12 bottles"
                packBadgeText.textContent = `${ucfirst(packType)} × ${packSize} ${baseUnit}`;
                packBadge.classList.remove('hidden');

                // Total display — green like show view
                totalDisplay.innerHTML = `<span class="text-green-600">${totalPieces.toFixed(2)} <span class="text-xs text-gray-400 font-normal">${baseUnit}</span></span>`;
            } else {
                packBadge.classList.add('hidden');
                totalDisplay.innerHTML = `<span class="text-red-400 text-xs">Enter pcs/${packType}</span>`;
                totalPieces = 0;
            }
        } else {
            // Direct mode
            packSizeInput.value = '';
            packBadge.classList.add('hidden');
            directBadge.classList.remove('hidden');

            totalPieces = qty;

            if (qty > 0) {
                totalDisplay.innerHTML = `<span class="text-green-600">${qty.toFixed(2)} <span class="text-xs text-gray-400 font-normal">${baseUnit}</span></span>`;
            } else {
                totalDisplay.innerHTML = `<span class="text-gray-300">—</span>`;
            }
        }

        totalInput.value = totalPieces;
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('[id^="total_pieces_"]').forEach(input => {
            grand += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').textContent = grand.toFixed(2);
    }

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
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
        document.querySelectorAll('.quantity-issued').forEach(input => {
            if (parseFloat(input.value) > 0) hasQty = true;
        });
        if (!hasQty) {
            e.preventDefault();
            alert('Please enter at least one item quantity to issue.');
            return false;
        }

        let packError = false;
        document.querySelectorAll('.pack-type').forEach(select => {
            if (select.value) {
                const index = select.getAttribute('data-index');
                const packSize = parseFloat(document.getElementById(`pack_size_${index}`).value) || 0;
                if (packSize < 1) packError = true;
            }
        });
        if (packError) {
            e.preventDefault();
            alert('Please enter the number of pieces per pack for all selected pack types.');
            return false;
        }
    });

    // Init
    document.addEventListener('DOMContentLoaded', function () {
        @foreach($requisition->items as $item)
        recalculate({{ $loop->index }});
        @endforeach
    });
</script>

@endsection
