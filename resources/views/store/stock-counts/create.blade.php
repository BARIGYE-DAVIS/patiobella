{{-- resources/views/store/stock-counts/create.blade.php --}}

@extends('layouts.store')

@section('title', 'Create Stock Count')
@section('page-title', 'Create Stock Count')

{{-- Tom Select CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Override Tom Select to match app theme */
    .ts-wrapper.single .ts-control {
        border-radius: 0.5rem;
        border-color: #d1d5db;
        font-size: 0.875rem;
        padding: 6px 10px;
        background-color: white;
        cursor: pointer;
    }
    .ts-wrapper.single .ts-control:focus,
    .ts-wrapper.focus .ts-control {
        border-color: #f97316 !important;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.15) !important;
    }
    .ts-dropdown {
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        font-size: 0.875rem;
    }
    .ts-dropdown .active {
        background-color: #fff7ed !important;
        color: #92400e !important;
    }
    .ts-dropdown .option:hover {
        background-color: #fff7ed;
    }
    .option-code {
        font-size: 0.7rem;
        color: #6b7280;
        display: block;
        margin-top: 1px;
    }
    /* Disabled state */
    .ts-wrapper.disabled .ts-control {
        background-color: #f3f4f6;
        cursor: not-allowed;
        opacity: 0.7;
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { opacity: 0.5; }
    #itemsBody tr:hover { background-color: #fefce8; transition: background-color 0.2s ease; }
    .variance-positive { color: #059669; font-weight: 600; }
    .variance-negative { color: #dc2626; font-weight: 600; }
    .variance-zero { color: #6b7280; }
    .remove-item {
        transition: all 0.2s ease; padding: 6px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        background: none; border: none; cursor: pointer;
    }
    .remove-item:hover { background-color: #fee2e2; }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Tabs --}}
    <div class="border-b border-gray-200 bg-white rounded-t-xl shadow-sm px-6 pt-4">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('store.stock-counts.create', ['type' => 'store']) }}"
               class="{{ $type === 'store' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} pb-4 px-2 border-b-2 font-medium text-sm transition-all duration-200">
                <i class="fas fa-warehouse mr-2"></i> Store Count
            </a>
            <a href="{{ route('store.stock-counts.create', ['type' => 'department']) }}"
               class="{{ $type === 'department' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} pb-4 px-2 border-b-2 font-medium text-sm transition-all duration-200">
                <i class="fas fa-building mr-2"></i> Department Count
            </a>
        </nav>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('store.stock-counts.store') }}" id="stockCountForm">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b border-orange-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-orange-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $type === 'store' ? 'Store Stock Count' : 'Department Stock Count' }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $type === 'store' ? 'Count and verify store inventory levels' : 'Count department stock and track variances' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Basic Info --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-info-circle text-orange-500 text-sm"></i>
                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Basic Information</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @if($type === 'department')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-building mr-1 text-gray-400"></i> Department <span class="text-red-500">*</span>
                            </label>
                            <select name="department_id" id="department_id" class="w-full rounded-lg border-gray-300 bg-white focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition-all duration-200" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Select department to load inventory items</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1 text-gray-400"></i> Count Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" value="{{ old('count_date', date('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-600 cursor-not-allowed focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition-all duration-200"
                                   readonly disabled>
                            <input type="hidden" name="count_date" value="{{ old('count_date', date('Y-m-d')) }}">
                            <p class="text-xs text-gray-400 mt-1">Count date is automatically set to today</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sticky-note mr-1 text-gray-400"></i> Notes
                            </label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition-all duration-200"
                                      placeholder="Optional notes about this stock count...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Items Section --}}
                <div class="border-t border-gray-200 pt-2">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">
                                <i class="fas fa-boxes mr-2 text-orange-500"></i> Items to Count
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Enter physical counts and track variances</p>
                        </div>

                    </div>

                    <div class="rounded-lg border border-gray-200 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-2/5">Item <span class="text-red-500">*</span></th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Expected Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Physical Count</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Empty Bottle Wt (kg)</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Net Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">Variance</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/5">Reason</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-12"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" class="divide-y divide-gray-100">
                                @if($type === 'store')
                                <tr id="noItemsRow">
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
                                            <p class="text-sm">No items added yet</p>
                                            <p class="text-xs mt-1">Click "Add Item" to start counting</p>
                                        </div>
                                    </td>
                                </tr>
                                @else
                                <tr id="loadingRow">
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                                            <p class="text-sm">Select a department to load items...</p>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                        @if($type === 'store')
                        <button type="button" id="addItemBtn"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-plus-circle text-sm"></i>
                            <span>Add Item</span>
                        </button>
                        @endif
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('store.stock-counts.index', ['type' => $type]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200">
                    <i class="fas fa-times text-gray-400"></i> Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-save text-sm"></i> Create Stock Count
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@php
    $allItemsJson = $type === 'store'
        ? json_encode(
            collect($storeItems)->map(function($i) {
                return [
                    'id'                  => $i->id,
                    'name'                => $i->name,
                    'code'                => $i->item_code ?? 'N/A',
                    'base_unit'           => $i->base_unit ?? 'units',
                    'empty_bottle_weight' => $i->empty_bottle_weight ?? 0,
                ];
            })->values()->all()
          )
        : '[]';
@endphp
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    let rowCounter = 0;
    const isStore = {{ $type === 'store' ? 'true' : 'false' }};
    const allItems = {!! $allItemsJson !!};

    // ─── Helper: safe HTML escape ────────────────────────────────────────────
    function esc(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }

    // ─── Calculation logic (shared by store & department rows) ───────────────
    function setupCalculation(index, getExpected) {
        const physicalInput   = document.querySelector(`.physical-qty-${index}`);
        const emptyWeightInput= document.querySelector(`.empty-weight-${index}`);

        function recalculate() {
            const physical    = parseFloat(physicalInput?.value)    || 0;
            const emptyWeight = parseFloat(emptyWeightInput?.value) || 0;
            const netQty      = Math.max(0, physical - emptyWeight);
            const expected    = getExpected();
            const variance    = netQty - expected;

            // Net qty
            const netHidden  = document.querySelector(`.net-qty-${index}`);
            const netDisplay = document.querySelector(`.net-qty-display-${index}`);
            if (netHidden)  netHidden.value = netQty;
            if (netDisplay) netDisplay.innerHTML =
                `<span class="font-semibold text-gray-800">${netQty.toFixed(2)}</span>`;

            // Variance
            const varDisplay = document.querySelector(`.variance-display-${index}`);
            if (varDisplay) {
                const cls = variance < 0 ? 'variance-negative' : (variance > 0 ? 'variance-positive' : 'variance-zero');
                varDisplay.innerHTML = `<span class="${cls}">${variance >= 0 ? '+' : ''}${variance.toFixed(2)}</span>`;
            }

            // Reason field
            const reasonInput = document.querySelector(`.reason-notes-${index}`);
            if (reasonInput) {
                if (variance !== 0) {
                    reasonInput.disabled = false;
                    reasonInput.classList.remove('bg-gray-100','cursor-not-allowed');
                    reasonInput.style.borderColor = '#f97316';
                } else {
                    reasonInput.disabled = true;
                    reasonInput.value = '';
                    reasonInput.classList.add('bg-gray-100','cursor-not-allowed');
                    reasonInput.style.borderColor = '#d1d5db';
                }
            }
        }

        physicalInput?.addEventListener('input', recalculate);
        emptyWeightInput?.addEventListener('input', recalculate);
    }

    // ─── STORE: add a new row with a Tom Select dropdown ────────────────────
    function addStoreItemRow() {
        const index = rowCounter++;
        const tbody = document.getElementById('itemsBody');

        const noItemsRow = document.getElementById('noItemsRow');
        if (noItemsRow) noItemsRow.remove();

        const row = document.createElement('tr');
        row.className = 'hover:bg-amber-50 transition-colors duration-150';
        row.id = `row-${index}`;

        row.innerHTML = `
            <td class="px-4 py-3" style="min-width:240px;">
                <select id="item-select-${index}" name="items[${index}][inventory_item_id]"
                        placeholder="Search item by name or code..." class="w-full" required>
                    <option value="">Search item by name or code...</option>
                    ${allItems.map(item =>
                        `<option value="${item.id}"
                            data-unit="${esc(item.base_unit)}"
                            data-empty="${item.empty_bottle_weight}"
                            data-code="${esc(item.code)}">
                            ${esc(item.name)}
                         </option>`
                    ).join('')}
                </select>
                <span class="loading-${index} hidden ml-2">
                    <i class="fas fa-spinner fa-spin text-orange-500 text-xs"></i>
                </span>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="expected-qty-display-${index} text-gray-500">—</span>
                <input type="hidden" name="items[${index}][expected_quantity]"
                       class="expected-qty-${index}" value="0">
            </td>
            <td class="px-4 py-3 text-center">
                <input type="number" name="items[${index}][physical_quantity]" step="any"
                       class="physical-qty-${index} w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm bg-gray-100 cursor-not-allowed"
                       placeholder="0.00" disabled>
            </td>
            <td class="px-4 py-3 text-center">
                <input type="number" name="items[${index}][empty_bottle_weight]" step="any"
                       class="empty-weight-${index} w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm bg-gray-100 cursor-not-allowed"
                       placeholder="0.000" readonly disabled>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="net-qty-display-${index} text-gray-500">—</span>
                <input type="hidden" class="net-qty-${index}" value="0">
            </td>
            <td class="px-4 py-3 text-center">
                <span class="variance-display-${index} text-gray-500">—</span>
            </td>
            <td class="px-4 py-3">
                <select name="items[${index}][reason_notes]"
                        class="reason-notes-${index} w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-gray-100 cursor-not-allowed"
                        disabled>
                    <option value="">— Select reason —</option>
                    <option value="Damaged">Damaged</option>
                    <option value="Theft">Theft</option>
                    <option value="Expiry">Expiry / Spoilage</option>
                    <option value="Miscount">Miscount</option>
                    <option value="Spillage">Spillage</option>
                    <option value="Returned to Supplier">Returned to Supplier</option>
                    <option value="Write-off">Write-off</option>
                    <option value="Other">Other</option>
                </select>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" class="remove-item text-red-500 hover:text-red-700" data-index="${index}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);

        // Initialise Tom Select on the <select>
        const ts = new TomSelect(`#item-select-${index}`, {
            placeholder: 'Search item by name or code...',
            searchField: ['text', 'code'],
            maxOptions: 200,
            render: {
                option: function(data, escape) {
                    return `<div>
                        <span class="font-semibold">${escape(data.text)}</span>
                        <span class="option-code">Code: ${escape(data.$option?.dataset?.code ?? '')}</span>
                    </div>`;
                },
                item: function(data, escape) {
                    const code = data.$option?.dataset?.code ?? '';
                    return `<div>${escape(data.text)}${code ? ` <span style="font-size:0.7rem;color:#6b7280;">(${escape(code)})</span>` : ''}</div>`;
                },
            },
            onChange: function(value) {
                if (!value) {
                    // Clear dependent fields
                    const pInput = document.querySelector(`.physical-qty-${index}`);
                    const eInput = document.querySelector(`.empty-weight-${index}`);
                    const rInput = document.querySelector(`.reason-notes-${index}`);
                    const eDisp  = document.querySelector(`.expected-qty-display-${index}`);
                    const eHid   = document.querySelector(`.expected-qty-${index}`);
                    [pInput, eInput].forEach(el => { if(el){ el.disabled=true; el.value=''; el.classList.add('bg-gray-100','cursor-not-allowed'); }});
                    if(rInput){ rInput.disabled=true; rInput.value=''; rInput.classList.add('bg-gray-100','cursor-not-allowed'); }
                    if(eDisp) eDisp.innerHTML = '—';
                    if(eHid)  eHid.value = '0';
                    return;
                }

                // Enable physical count field
                const pInput = document.querySelector(`.physical-qty-${index}`);
                const eInput = document.querySelector(`.empty-weight-${index}`);
                if(pInput){ pInput.disabled=false; pInput.classList.remove('bg-gray-100','cursor-not-allowed'); }

                // Set empty bottle weight from option dataset (READ-ONLY)
                const optEl = this.options[value]?.$option;
                const emptyWt = parseFloat(optEl?.dataset?.empty ?? 0) || 0;
                if(eInput) {
                    eInput.value = emptyWt;
                    eInput.readOnly = true;
                    eInput.disabled = false;
                    eInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                }

                // Fetch current stock from store
                fetchItemStock(index, value);
            }
        });

        // Setup variance calculation – reads expected from hidden input
        setupCalculation(index, () => parseFloat(document.querySelector(`.expected-qty-${index}`)?.value) || 0);
    }

    // ─── STORE: fetch expected stock via AJAX ────────────────────────────────
    function fetchItemStock(index, itemId) {
        const loadingEl = document.querySelector(`.loading-${index}`);
        if (loadingEl) loadingEl.classList.remove('hidden');

        fetch(`{{ url('store/stock-counts/get-item-stock') }}/${itemId}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const disp = document.querySelector(`.expected-qty-display-${index}`);
                    const hid  = document.querySelector(`.expected-qty-${index}`);
                    if (disp) disp.innerHTML =
                        `<span class="font-semibold text-gray-800">${parseFloat(data.current_stock).toFixed(2)}</span>
                         <span class="text-xs text-gray-400 ml-1">${data.base_unit}</span>`;
                    if (hid) hid.value = data.current_stock;
                }
            })
            .catch(err => console.error('Stock fetch error:', err))
            .finally(() => { if (loadingEl) loadingEl.classList.add('hidden'); });
    }

    // ─── DEPARTMENT: add a pre-populated row (no dropdown needed) ────────────
    function addDepartmentItemRow(itemData) {
        const index = rowCounter++;
        const tbody = document.getElementById('itemsBody');
        const expectedQty = parseFloat(itemData.expected_quantity) || 0;

        const row = document.createElement('tr');
        row.className = 'hover:bg-amber-50 transition-colors duration-150';
        row.id = `row-${index}`;

        row.innerHTML = `
            <td class="px-4 py-3">
                <input type="hidden" name="items[${index}][inventory_item_id]" value="${itemData.inventory_item_id}">
                <input type="hidden" name="items[${index}][expected_quantity]"  value="${expectedQty}">
                <input type="hidden" name="items[${index}][unit_cost]"          value="${itemData.unit_cost || 0}">
                <p class="font-semibold text-gray-800">${esc(itemData.item_name)}</p>
                <p class="text-xs text-gray-400">${esc(itemData.item_code)}</p>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="font-bold text-gray-700">${expectedQty.toFixed(2)}</span>
                <span class="text-xs text-gray-400 ml-1">${esc(itemData.base_unit || 'units')}</span>
            </td>
            <td class="px-4 py-3 text-center">
                <input type="number" name="items[${index}][physical_quantity]" step="any"
                       class="physical-qty-${index} w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                       placeholder="0.00">
            </td>
            <td class="px-4 py-3 text-center">
                <input type="number" name="items[${index}][empty_bottle_weight]" step="any"
                       class="empty-weight-${index} w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm bg-gray-100 cursor-not-allowed"
                       value="${itemData.empty_bottle_weight || 0}" placeholder="0.000" readonly disabled>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="net-qty-display-${index} text-gray-500">—</span>
                <input type="hidden" class="net-qty-${index}" value="0">
            </td>
            <td class="px-4 py-3 text-center">
                <span class="variance-display-${index} text-gray-500">—</span>
            </td>
            <td class="px-4 py-3">
                <select name="items[${index}][reason_notes]"
                        class="reason-notes-${index} w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-gray-100 cursor-not-allowed"
                        disabled>
                    <option value="">— Select reason —</option>
                    <option value="Damaged">Damaged</option>
                    <option value="Theft">Theft</option>
                    <option value="Expiry">Expiry / Spoilage</option>
                    <option value="Miscount">Miscount</option>
                    <option value="Spillage">Spillage</option>
                    <option value="Returned to Supplier">Returned to Supplier</option>
                    <option value="Write-off">Write-off</option>
                    <option value="Other">Other</option>
                </select>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" class="remove-item text-red-500 hover:text-red-700" data-index="${index}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);
        setupCalculation(index, () => expectedQty);
    }

    // ─── Row removal ─────────────────────────────────────────────────────────
    document.getElementById('itemsBody').addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-item');
        if (!btn) return;
        const row = document.getElementById(`row-${btn.dataset.index}`);
        if (row) row.remove();
        if (isStore && document.querySelectorAll('#itemsBody tr').length === 0) {
            document.getElementById('itemsBody').innerHTML =
                `<tr id="noItemsRow"><td colspan="8" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
                        <p class="text-sm">No items added yet</p>
                        <p class="text-xs mt-1">Click "Add Item" to start counting</p>
                    </div></td></tr>`;
        }
    });

    // ─── Add Item button (Store only) ─────────────────────────────────────────
    @if($type === 'store')
    document.getElementById('addItemBtn').addEventListener('click', addStoreItemRow);
    @endif

    // ─── Department AJAX load ─────────────────────────────────────────────────
    @if($type === 'department')
    function loadDepartmentItems(departmentId) {
        if (!departmentId) return;
        rowCounter = 0;
        const tbody = document.getElementById('itemsBody');
        tbody.innerHTML = `<tr id="loadingRow"><td colspan="8" class="px-4 py-12 text-center">
            <div class="flex flex-col items-center justify-center text-gray-400">
                <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                <p class="text-sm">Loading items...</p>
            </div></td></tr>`;

        fetch(`{{ url('store/stock-counts/get-department-items') }}/${departmentId}`)
            .then(r => r.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.success && data.items && data.items.length > 0) {
                    data.items.forEach(item => addDepartmentItemRow(item));
                } else {
                    tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
                            <p class="text-sm">No items found for this department</p>
                            <p class="text-xs text-red-400 mt-1">${data.message ?? ''}</p>
                        </div></td></tr>`;
                }
            })
            .catch(err => {
                console.error('Department items error:', err);
                tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center justify-center text-red-400">
                        <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                        <p class="text-sm">Failed to load items. Please try again.</p>
                    </div></td></tr>`;
            });
    }

    const deptSelect = document.getElementById('department_id');
    if (deptSelect) {
        if (deptSelect.value) loadDepartmentItems(deptSelect.value);
        deptSelect.addEventListener('change', () => loadDepartmentItems(deptSelect.value));
    }
    @endif

document.getElementById('stockCountForm').addEventListener('submit', function(e) {
    // Disable rows where physical_quantity is blank or empty string (not counted)
    document.querySelectorAll('#itemsBody tr[id^="row-"]').forEach(row => {
        const physicalInput = row.querySelector('[name*="physical_quantity"]');

        // If field is empty/blank, disable ALL inputs in this row so they don't get submitted
        if (!physicalInput || physicalInput.value === '' || physicalInput.value === null) {
            row.querySelectorAll('input, select').forEach(el => el.disabled = true);
        }
    });

    // Check at least one item was counted
    const countedItems = [...document.querySelectorAll('[name*="physical_quantity"]')]
        .filter(el => !el.disabled && el.value !== '');

    if (countedItems.length === 0) {
        e.preventDefault();
        // Re-enable all so page doesn't break if they dismiss and try again
        document.querySelectorAll('#itemsBody input, #itemsBody select').forEach(el => el.disabled = false);
        alert('Please count at least one item before submitting.');
    }
});
</script>
@endpush
