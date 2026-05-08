@extends('layouts.store')

@section('title', 'Create Requisition')

@section('page-title', 'Create New Requisition')

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
    #draftBanner {
        display: none;
        align-items: center;
        gap: 10px;
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 13px;
        color: #92400e;
        margin-bottom: 16px;
    }
    #draftBanner svg { flex-shrink: 0; }
    #clearDraftBtn {
        margin-left: auto;
        font-size: 12px;
        color: #b45309;
        text-decoration: underline;
        background: none;
        border: none;
        cursor: pointer;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Create New Requisition</h3>
        <p class="text-sm text-gray-500">Fill in the details below to request items from the store</p>
    </div>

    <form method="POST" action="{{ route('store.requisitions.store') }}" id="requisitionForm">
        @csrf

        <div class="p-6 space-y-6">

            {{-- Draft restored banner --}}
            <div id="draftBanner">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                </svg>
                <span>Draft restored from your last session.</span>
                <button type="button" id="clearDraftBtn">Clear draft</button>
            </div>

            {{-- Requisition Header --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date Needed <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_needed" id="date_needed"
                           value="{{ old('date_needed') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Overall Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              placeholder="Any additional information for the procurement team..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Items Section --}}
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
                            {{-- Rows are injected by JS on page load --}}
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="button" id="addItemBtn"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Item
                    </button>
                </div>
            </div>

            @error('items')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror

        </div>

        {{-- Form Actions --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <a href="{{ route('store.requisitions.index') }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="button" id="previewBtn"
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                Preview Requisition
            </button>
        </div>
    </form>
</div>

{{-- Preview Modal --}}
<div id="previewModal"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-lg bg-white">

        <div class="flex justify-between items-center mb-4 pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Preview Requisition</h3>
            <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Date Needed</p>
                        <p class="font-medium" id="previewDateNeeded">—</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Overall Notes</p>
                        <p class="font-medium" id="previewNotes">—</p>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Items Requested</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500" style="width:30%">Item Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500" style="width:15%">Category</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500" style="width:10%">Quantity</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500" style="width:15%">Metrics</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500" style="width:30%">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="previewItemsBody" class="divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
            <button type="button" onclick="closePreview()"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Edit
            </button>
            <button type="button" onclick="submitForm()"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Confirm & Submit
            </button>
        </div>
    </div>
</div>

<script>
    // ═══════════════════════════════════════════════════════════════════
    //  CONFIG
    // ═══════════════════════════════════════════════════════════════════
    const DRAFT_KEY   = 'requisition_draft';
    let   itemCounter = 0;   // always incrementing; never reused

    // ═══════════════════════════════════════════════════════════════════
    //  STATIC OPTION STRINGS  (PHP renders these once; JS reuses them)
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
        @foreach($items as $item)
            <option value="{{ $item->id }}"
                    data-category="{{ $item->category->name ?? 'Uncategorized' }}">
                {{ $item->name }} ({{ $item->item_code ?? $item->code }})
            </option>
        @endforeach
    `;

    // ═══════════════════════════════════════════════════════════════════
    //  DRAFT  —  save / load / clear
    // ═══════════════════════════════════════════════════════════════════

    function saveDraft() {
        const rows = [];
        document.querySelectorAll('#itemsBody .item-row').forEach(function (row) {
            rows.push({
                item_id  : row.querySelector('.item-select').value,
                category : row.querySelector('.category-cell').innerText,
                quantity : row.querySelector('input[name*="[quantity]"]').value,
                metrics  : row.querySelector('select[name*="[metrics]"]').value,
                notes    : row.querySelector('input[name*="[notes]"]').value,
            });
        });

        try {
            localStorage.setItem(DRAFT_KEY, JSON.stringify({
                date_needed : document.getElementById('date_needed').value,
                notes       : document.getElementById('notes').value,
                rows,
            }));
        } catch (e) { /* storage full / unavailable — fail silently */ }
    }

    function loadDraft() {
        try {
            const raw = localStorage.getItem(DRAFT_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) { return null; }
    }

    function clearDraft() {
        localStorage.removeItem(DRAFT_KEY);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ROW CREATION
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Build and return a <tr>.
     * @param {object|null} saved  — pre-fill data, or null for a blank row
     */
    function createNewRow(saved) {
        const index = itemCounter++;
        const tr    = document.createElement('tr');
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
                <button type="button" class="remove-item text-red-600 hover:text-red-800" title="Remove row">
                    <svg class="w-5 h-5 mx-auto pointer-events-none" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        `;

        // Pre-fill simple fields now; item_id is set after Select2 init
        if (saved) {
            tr.querySelector('input[name*="[quantity]"]').value = saved.quantity || '';
            tr.querySelector('select[name*="[metrics]"]').value = saved.metrics  || '';
            tr.querySelector('input[name*="[notes]"]').value    = saved.notes    || '';
            tr.querySelector('.category-cell').innerText        = saved.category || '—';
        }

        return tr;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  SELECT2  —  init + category auto-fill
    // ═══════════════════════════════════════════════════════════════════

    function initSelect2(element, preselectValue) {
        $(element).select2({ placeholder: '-- Search Item --', allowClear: true, width: '100%' });

        $(element).on('change', function () {
            const categoryName = $(this).find('option:selected').data('category') || '—';
            $(this).closest('.item-row').find('.category-cell').text(categoryName);
            saveDraft();
        });

        // Restore saved selection (triggers the change handler above)
        if (preselectValue) {
            $(element).val(preselectValue).trigger('change');
        } else {
            $(element).trigger('change');
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    //  REMOVE ROW
    // ═══════════════════════════════════════════════════════════════════

    function removeItem(e) {
        const row = e.currentTarget.closest('.item-row');
        if (!row) return;
        if (document.querySelectorAll('#itemsBody .item-row').length > 1) {
            row.remove();
            saveDraft();
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
    //  ADD ROW BUTTON
    // ═══════════════════════════════════════════════════════════════════

    document.getElementById('addItemBtn').addEventListener('click', function () {
        const tr = createNewRow(null);
        document.getElementById('itemsBody').appendChild(tr);
        initSelect2($(tr).find('.item-select'), null);
        attachRemoveEvents();
        saveDraft();
    });

    // ═══════════════════════════════════════════════════════════════════
    //  AUTO-SAVE on every change across the form
    // ═══════════════════════════════════════════════════════════════════

    document.getElementById('date_needed').addEventListener('change', saveDraft);
    document.getElementById('notes').addEventListener('input',  saveDraft);
    // Delegated listeners cover quantity / metrics / notes in all rows
    document.getElementById('itemsBody').addEventListener('input',  saveDraft);
    document.getElementById('itemsBody').addEventListener('change', saveDraft);

    // ═══════════════════════════════════════════════════════════════════
    //  BOOTSTRAP  —  restore draft or start with one blank row
    // ═══════════════════════════════════════════════════════════════════

    (function init() {
        const draft = loadDraft();
        const tbody = document.getElementById('itemsBody');

        if (draft && Array.isArray(draft.rows) && draft.rows.length > 0) {
            // ── Restore header fields ──────────────────────────────────
            document.getElementById('date_needed').value = draft.date_needed || '';
            document.getElementById('notes').value       = draft.notes       || '';

            // ── Restore rows ───────────────────────────────────────────
            draft.rows.forEach(function (savedRow) {
                const tr = createNewRow(savedRow);
                tbody.appendChild(tr);
                initSelect2($(tr).find('.item-select'), savedRow.item_id || null);
            });

            // ── Show the banner ────────────────────────────────────────
            document.getElementById('draftBanner').style.display = 'flex';
        } else {
            // No draft — one blank row
            const tr = createNewRow(null);
            tbody.appendChild(tr);
            initSelect2($(tr).find('.item-select'), null);
        }

        attachRemoveEvents();
    })();

    // ─── Clear draft button ───────────────────────────────────────────
    document.getElementById('clearDraftBtn').addEventListener('click', function () {
        if (!confirm('Clear the saved draft and start fresh?')) return;

        clearDraft();
        document.getElementById('date_needed').value = '';
        document.getElementById('notes').value       = '';

        const tbody = document.getElementById('itemsBody');
        tbody.innerHTML = '';
        itemCounter = 0;

        const tr = createNewRow(null);
        tbody.appendChild(tr);
        initSelect2($(tr).find('.item-select'), null);
        attachRemoveEvents();

        document.getElementById('draftBanner').style.display = 'none';
    });

    // ═══════════════════════════════════════════════════════════════════
    //  PREVIEW MODAL
    // ═══════════════════════════════════════════════════════════════════

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function openPreview() {
        document.getElementById('previewDateNeeded').innerText =
            document.getElementById('date_needed').value || 'Not specified';
        document.getElementById('previewNotes').innerText =
            document.getElementById('notes').value || '—';

        const items = [];
        document.querySelectorAll('#itemsBody .item-row').forEach(function (row) {
            const select   = row.querySelector('.item-select');
            const selOpt   = select.options[select.selectedIndex];
            const itemName = selOpt && selOpt.value ? selOpt.textContent.trim() : '';
            const quantity = row.querySelector('input[name*="[quantity]"]').value;

            if (itemName && quantity && parseFloat(quantity) > 0) {
                items.push({
                    name     : itemName,
                    category : row.querySelector('.category-cell')?.innerText || '—',
                    quantity,
                    metrics  : row.querySelector('select[name*="[metrics]"] option:checked')?.text || '',
                    notes    : row.querySelector('input[name*="[notes]"]').value,
                });
            }
        });

        const previewBody = document.getElementById('previewItemsBody');
        previewBody.innerHTML = '';

        if (items.length === 0) {
            previewBody.innerHTML =
                '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No valid items added</td></tr>';
        } else {
            items.forEach(function (item) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-2 text-sm text-gray-800">${escapeHtml(item.name)}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(item.category)}</td>
                    <td class="px-4 py-2 text-sm text-gray-800 text-right">${parseFloat(item.quantity).toFixed(2)}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(item.metrics) || '—'}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(item.notes)   || '—'}</td>
                `;
                previewBody.appendChild(tr);
            });
        }

        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    function submitForm() {
        const rows  = document.querySelectorAll('#itemsBody .item-row');
        let   valid = true;

        rows.forEach(function (row, i) {
            if (!valid) return;
            const itemId   = row.querySelector('.item-select').value;
            const quantity = row.querySelector('input[name*="[quantity]"]').value;

            if (!itemId) {
                alert(`Please select an item for row ${i + 1}`);
                valid = false;
            } else if (!quantity || parseFloat(quantity) <= 0) {
                alert(`Please enter a valid quantity for row ${i + 1}`);
                valid = false;
            }
        });

        if (valid) {
            clearDraft();   // wipe the draft on successful submit
            document.getElementById('requisitionForm').submit();
        }
    }

    document.getElementById('previewBtn').addEventListener('click', openPreview);
</script>
@endsection
