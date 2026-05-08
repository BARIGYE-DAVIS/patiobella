@extends('layouts.store')

@section('title', 'Edit Requisition')

@section('page-title', 'Edit Requisition')

@section('content')
<style>
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }
    .items-table th,
    .items-table td {
        padding: 12px;
        border: 1px solid #e5e7eb;
        vertical-align: top;
    }
    .items-table th {
        background-color: #f9fafb;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
    }
    .item-row:hover {
        background-color: #f9fafb;
    }
    .select2-container .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">

    {{-- Page Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Edit Requisition</h3>
        <p class="text-sm text-gray-500">Requisition #{{ $requisition->requisition_number }}</p>
    </div>

    <form method="POST" action="{{ route('store.requisitions.update', $requisition->id) }}" id="requisitionForm">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-6">

            {{-- ── Header Fields ──────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date Needed <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_needed" id="date_needed"
                           value="{{ old('date_needed', $requisition->date_needed?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Overall Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              placeholder="Any additional information..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg
                                     focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $requisition->notes) }}</textarea>
                </div>
            </div>

            {{-- ── Items Table ─────────────────────────────────────────── --}}
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4">Items Requested</h4>

                <div class="overflow-x-auto">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 30%">Item Name</th>
                                <th style="width: 15%">Category</th>
                                <th style="width: 10%">Quantity</th>
                                <th style="width: 15%">Metrics</th>
                                <th style="width: 20%">Notes</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">

                            @foreach($requisition->items as $index => $item)
                            @php
                                $catName = $item->category_name
                                    ?: ($item->inventoryItem?->category?->name ?? '—');
                            @endphp
                            <tr class="item-row" id="row-{{ $index }}">
                                <td>
                                    <select name="items[{{ $index }}][item_id]"
                                            class="item-select w-full" required>
                                        <option value="">-- Search Item --</option>
                                        @foreach($items as $inventoryItem)
                                            <option value="{{ $inventoryItem->id }}"
                                                    data-category="{{ $inventoryItem->category->name ?? 'Uncategorized' }}"
                                                    @selected($item->inventory_item_id == $inventoryItem->id)>
                                                {{ $inventoryItem->name }}
                                                ({{ $inventoryItem->item_code ?? $inventoryItem->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="category-cell text-sm text-gray-500">
                                    {{ $catName }}
                                </td>
                                <td>
                                    <input type="number"
                                           name="items[{{ $index }}][quantity]"
                                           step="0.01"
                                           value="{{ old('items.'.$index.'.quantity', $item->quantity_requested) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="0.00" required>
                                </td>
                                <td>
                                    <select name="items[{{ $index }}][metrics]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="">-- Select --</option>
                                        <option value="kg"           @selected($item->metrics=='kg')>Kilograms (kg)</option>
                                        <option value="litres"       @selected($item->metrics=='litres')>Litres (L)</option>
                                        <option value="pcs"          @selected($item->metrics=='pcs')>Pieces (pcs)</option>
                                        <option value="boxes"        @selected($item->metrics=='boxes')>Boxes (box)</option>
                                        <option value="bags"         @selected($item->metrics=='bags')>Bags (bag)</option>
                                        <option value="cartons"      @selected($item->metrics=='cartons')>Cartons (ctn)</option>
                                        <option value="dozen"        @selected($item->metrics=='dozen')>Dozen (dz)</option>
                                        <option value="grams"        @selected($item->metrics=='grams')>Grams (g)</option>
                                        <option value="millilitres"  @selected($item->metrics=='millilitres')>Millilitres (ml)</option>
                                        <option value="meters"       @selected($item->metrics=='meters')>Meters (m)</option>
                                        <option value="rolls"        @selected($item->metrics=='rolls')>Rolls (roll)</option>
                                        <option value="bottles"      @selected($item->metrics=='bottles')>Bottles (btl)</option>
                                        <option value="packs"        @selected($item->metrics=='packs')>Packs (pk)</option>
                                        <option value="sets"         @selected($item->metrics=='sets')>Sets (set)</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text"
                                           name="items[{{ $index }}][notes]"
                                           value="{{ old('items.'.$index.'.notes', $item->notes) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="Optional notes...">
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            class="remove-item text-red-600 hover:text-red-800"
                                            title="Remove row">
                                        <svg class="w-5 h-5 mx-auto pointer-events-none"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                {{-- Add Item — always anchored below the table --}}
                <div class="mt-4">
                    <button type="button" id="addItemBtn"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg
                                   hover:bg-green-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Item
                    </button>
                </div>
            </div>

            @error('items')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror

        </div>

        {{-- ── Form Actions ────────────────────────────────────────────── --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <a href="{{ route('store.requisitions.show', $requisition->id) }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700
                      hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Update Requisition
            </button>
        </div>
    </form>
</div>

<script>
    // ═══════════════════════════════════════════════════════════════════
    //  STATE
    // ═══════════════════════════════════════════════════════════════════
    let itemCounter = {{ $requisition->items->count() }};

    // ═══════════════════════════════════════════════════════════════════
    //  STATIC OPTION STRINGS  (PHP renders once; JS reuses for new rows)
    // ═══════════════════════════════════════════════════════════════════
    const metricsOptions = `
        <option value="">-- Select --</option>
        <option value="kg">Kilograms (kg)</option>
        <option value="litres">Litres (L)</option>
        <option value="pcs">Pieces (pcs)</option>
        <option value="boxes">Boxes (box)</option>
        <option value="bags">Bags (bag)</option>
        <option value="cartons">Cartons (ctn)</option>
        <option value="dozen">Dozen (dz)</option>
        <option value="grams">Grams (g)</option>
        <option value="millilitres">Millilitres (ml)</option>
        <option value="meters">Meters (m)</option>
        <option value="rolls">Rolls (roll)</option>
        <option value="bottles">Bottles (btl)</option>
        <option value="packs">Packs (pk)</option>
        <option value="sets">Sets (set)</option>
    `;

    const itemOptions = `
        <option value="">-- Search Item --</option>
        @foreach($items as $inventoryItem)
            <option value="{{ $inventoryItem->id }}"
                    data-category="{{ $inventoryItem->category->name ?? 'Uncategorized' }}">
                {{ $inventoryItem->name }} ({{ $inventoryItem->item_code ?? $inventoryItem->code }})
            </option>
        @endforeach
    `;

    // ═══════════════════════════════════════════════════════════════════
    //  SELECT2
    // ═══════════════════════════════════════════════════════════════════
    function initSelect2(element) {
        $(element).select2({
            placeholder : '-- Search Item --',
            allowClear  : true,
            width       : '100%',
        });

        $(element).on('change', function () {
            const categoryName = $(this).find('option:selected').data('category') || '—';
            $(this).closest('.item-row').find('.category-cell').text(categoryName);
        });

        // Trigger to populate category cell from the pre-selected value
        $(element).trigger('change');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ROW CREATION  (new blank rows only — existing rows come from PHP)
    // ═══════════════════════════════════════════════════════════════════
    function createNewRow(index) {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.id        = `row-${index}`;
        tr.innerHTML = `
            <td>
                <select name="items[${index}][item_id]" class="item-select w-full" required>
                    ${itemOptions}
                </select>
            </td>
            <td class="category-cell text-sm text-gray-500">—</td>
            <td>
                <input type="number" name="items[${index}][quantity]" step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                       placeholder="0.00" required>
            </td>
            <td>
                <select name="items[${index}][metrics]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    ${metricsOptions}
                </select>
            </td>
            <td>
                <input type="text" name="items[${index}][notes]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                       placeholder="Optional notes...">
            </td>
            <td class="text-center">
                <button type="button" class="remove-item text-red-600 hover:text-red-800"
                        title="Remove row">
                    <svg class="w-5 h-5 mx-auto pointer-events-none" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        `;
        return tr;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  REMOVE ROW
    //  FIX: use e.currentTarget (always the <button>) not e.target
    //       (which could be the SVG or <path> child).
    //  SVG children also have pointer-events:none as a second safety net.
    // ═══════════════════════════════════════════════════════════════════
    function removeItem(e) {
        const row = e.currentTarget.closest('.item-row');
        if (!row) return;
        if (document.querySelectorAll('#itemsBody .item-row').length > 1) {
            row.remove();
        } else {
            alert('You must have at least one item.');
        }
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-item').forEach(function (btn) {
            btn.removeEventListener('click', removeItem);
            btn.addEventListener('click', removeItem);
        });
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ADD ITEM BUTTON
    // ═══════════════════════════════════════════════════════════════════
    document.getElementById('addItemBtn').addEventListener('click', function () {
        const tr = createNewRow(itemCounter++);
        document.getElementById('itemsBody').appendChild(tr);
        initSelect2($(tr).find('.item-select'));
        attachRemoveEvents();
    });

    // ═══════════════════════════════════════════════════════════════════
    //  BOOTSTRAP  —  init Select2 on all PHP-rendered rows
    // ═══════════════════════════════════════════════════════════════════
    document.querySelectorAll('#itemsBody .item-select').forEach(function (select) {
        initSelect2($(select));
    });

    attachRemoveEvents();
</script>
@endsection
