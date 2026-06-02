@extends('layouts.store')

@section('title', 'Create Stock Count')
@section('page-title', 'Create Stock Count')

{{-- Tom Select CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
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
    .number-input {
        -moz-appearance: textfield;
    }
    .number-input::-webkit-inner-spin-button,
    .number-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="space-y-6" id="printArea">

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
                <div class="flex items-center justify-between flex-wrap gap-3">
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
                    <div class="flex gap-2 no-print">
                        <button type="button" id="previewBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                            <i class="fas fa-eye"></i> Preview
                        </button>
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

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user-check mr-1 text-gray-400"></i> Counted By <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="counted_by" id="counted_by"
                                   class="w-full rounded-lg border-gray-300 focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition-all duration-200"
                                   placeholder="Enter name of person performing the count"
                                   value="{{ old('counted_by', Auth::user()->first_name . ' ' . Auth::user()->last_name) }}" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sticky-note mr-1 text-gray-400"></i> Notes
                            </label>
                            <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition-all duration-200"
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
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-2/5">Item</th>
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
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 no-print">
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

{{-- Preview Modal --}}
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
    <div class="min-h-screen p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-600"></i> Stock Count Preview
                </h3>
                <div class="flex gap-2">
                    <button onclick="printPreview()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button onclick="closePreviewModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            <div id="previewContent" class="p-6"></div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
const allItems = @json($storeItemsJson);
let rowCounter = 0;
const isStore = {{ $type === 'store' ? 'true' : 'false' }};

function esc(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

function formatNumber(value) {
    // Return whole number representation (integer)
    const v = parseFloat(value) || 0;
    return String(Math.round(v));
}

function setupCalculation(index, getExpected) {
    const physicalInput   = document.querySelector(`.physical-qty-${index}`);
    const emptyWeightInput= document.querySelector(`.empty-weight-${index}`);

    function recalculate(showOnlyWhenPhysical=false) {
        const physicalRaw = physicalInput?.value;
        if (showOnlyWhenPhysical && (!physicalRaw || physicalRaw === '')) {
            // hide displays until user enters a physical count
            const netDisplay = document.querySelector(`.net-qty-display-${index}`);
            const varDisplay = document.querySelector(`.variance-display-${index}`);
            if (netDisplay) netDisplay.innerHTML = '—';
            if (varDisplay) varDisplay.innerHTML = '—';
            const netHidden = document.querySelector(`.net-qty-${index}`);
            if (netHidden) netHidden.value = '0';
            const reasonInput = document.querySelector(`.reason-notes-${index}`);
            if (reasonInput) {
                reasonInput.disabled = true;
                reasonInput.value = '';
                reasonInput.classList.add('bg-gray-100','cursor-not-allowed');
                reasonInput.style.borderColor = '#d1d5db';
            }
            return;
        }

        const physical    = Math.round(parseFloat(physicalInput?.value)    || 0);
        const emptyWeight = Math.round(parseFloat(emptyWeightInput?.value) || 0);
        let netQty      = physical - emptyWeight;
        if (netQty < 0) netQty = 0;
        const expected    = Math.round(getExpected());
        const variance    = netQty - expected;

        const netHidden  = document.querySelector(`.net-qty-${index}`);
        const netDisplay = document.querySelector(`.net-qty-display-${index}`);
        if (netHidden)  netHidden.value = formatNumber(netQty);
        if (netDisplay) netDisplay.innerHTML = `<span class="font-semibold text-gray-800">${formatNumber(netQty)}</span>`;

        const varDisplay = document.querySelector(`.variance-display-${index}`);
        if (varDisplay) {
            const cls = variance < 0 ? 'variance-negative' : (variance > 0 ? 'variance-positive' : 'variance-zero');
            varDisplay.innerHTML = `<span class="${cls}">${variance >= 0 ? '+' : ''}${formatNumber(variance)}</span>`;
        }

        const reasonInput = document.querySelector(`.reason-notes-${index}`);
        if (reasonInput) {
            if (Math.abs(variance) > 0) {
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

    // Only compute after user begins typing physical count. Also update on blur to show final integer.
    physicalInput?.addEventListener('input', function() { recalculate(true); });
    physicalInput?.addEventListener('blur', function() { recalculate(true); });
    // Empty weight rarely changes; but keep event just in case. Only recalc if physical already entered.
    emptyWeightInput?.addEventListener('input', function() { recalculate(true); });

    // Start with hidden values (don't auto-populate net/variance until the user types)
    recalculate(true);
}

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
            <input type="number" name="items[${index}][physical_quantity]" step="1" min="0"
                   class="physical-qty-${index} number-input w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm bg-gray-100 cursor-not-allowed"
                   placeholder="0" disabled>
         </td>
        <td class="px-4 py-3 text-center">
            <input type="number" name="items[${index}][empty_bottle_weight]" step="1" min="0"
                   class="empty-weight-${index} number-input w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm bg-gray-100 cursor-not-allowed"
                   placeholder="0" readonly disabled>
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

            const pInput = document.querySelector(`.physical-qty-${index}`);
            const eInput = document.querySelector(`.empty-weight-${index}`);
            if(pInput){ pInput.disabled=false; pInput.classList.remove('bg-gray-100','cursor-not-allowed'); }

            const optEl = this.options[value]?.$option;
            const emptyWt = Math.round(parseFloat(optEl?.dataset?.empty ?? 0) || 0);
            if(eInput) {
                eInput.value = formatNumber(emptyWt);
                eInput.readOnly = true;
                eInput.disabled = false;
                eInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            }

            fetchItemStock(index, value);
        }
    });

    setupCalculation(index, () => parseInt(document.querySelector(`.expected-qty-${index}`)?.value || 0));
}

function fetchItemStock(index, itemId) {
    const loadingEl = document.querySelector(`.loading-${index}`);
    if (loadingEl) loadingEl.classList.remove('hidden');

    fetch(`{{ url('store/stock-counts/get-item-stock') }}/${itemId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const disp = document.querySelector(`.expected-qty-display-${index}`);
                const hid  = document.querySelector(`.expected-qty-${index}`);
                if (disp) disp.innerHTML = `<span class="font-semibold text-gray-800">${formatNumber(data.current_stock)}</span><span class="text-xs text-gray-400 ml-1">${data.base_unit}</span>`;
                if (hid) hid.value = Math.round(parseFloat(data.current_stock) || 0);
            }
        })
        .catch(err => console.error('Stock fetch error:', err))
        .finally(() => { if (loadingEl) loadingEl.classList.add('hidden'); });
}

function addDepartmentItemRow(itemData) {
    const index = rowCounter++;
    const tbody = document.getElementById('itemsBody');
    const expectedQty = Math.round(parseFloat(itemData.expected_quantity) || 0);

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
            <span class="font-bold text-gray-700">${formatNumber(expectedQty)}</span>
            <span class="text-xs text-gray-400 ml-1">${esc(itemData.base_unit || 'units')}</span>
         </td>
        <td class="px-4 py-3 text-center">
            <input type="number" name="items[${index}][physical_quantity]" step="1" min="0"
                   class="physical-qty-${index} number-input w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                   placeholder="0">
         </td>
        <td class="px-4 py-3 text-center">
            <input type="number" name="items[${index}][empty_bottle_weight]" step="1" min="0"
                   class="empty-weight-${index} number-input w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm bg-gray-100 cursor-not-allowed"
                   value="${formatNumber(itemData.empty_bottle_weight || 0)}" placeholder="0" readonly disabled>
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

document.getElementById('itemsBody').addEventListener('click', function(e) {
    const btn = e.target.closest('.remove-item');
    if (!btn) return;
    const row = document.getElementById(`row-${btn.dataset.index}`);
    if (row) row.remove();
    if (isStore && document.querySelectorAll('#itemsBody tr').length === 0) {
        document.getElementById('itemsBody').innerHTML = `<tr id="noItemsRow"><td colspan="8" class="px-4 py-12 text-center"><div class="flex flex-col items-center justify-center text-gray-400"><i class="fas fa-box-open text-4xl mb-3 opacity-50"></i><p class="text-sm">No items added yet</p><p class="text-xs mt-1">Click "Add Item" to start counting</p></div></td></tr>`;
    }
});

@if($type === 'store')
document.getElementById('addItemBtn').addEventListener('click', addStoreItemRow);
@endif

@if($type === 'department')
function loadDepartmentItems(departmentId) {
    if (!departmentId) return;
    rowCounter = 0;
    const tbody = document.getElementById('itemsBody');
    tbody.innerHTML = `<tr id="loadingRow"><td colspan="8" class="px-4 py-12 text-center"><div class="flex flex-col items-center justify-center text-gray-400"><i class="fas fa-spinner fa-spin text-3xl mb-3"></i><p class="text-sm">Loading items...</p></div></td></tr>`;

    fetch(`{{ url('store/stock-counts/get-department-items') }}/${departmentId}`)
        .then(r => r.json())
        .then(data => {
            tbody.innerHTML = '';
            if (data.success && data.items && data.items.length > 0) {
                data.items.forEach(item => addDepartmentItemRow(item));
            } else {
                tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-12 text-center"><div class="flex flex-col items-center justify-center text-gray-400"><i class="fas fa-box-open text-4xl mb-3 opacity-50"></i><p class="text-sm">No items found for this department</p></div></td></tr>`;
            }
        })
        .catch(err => {
            console.error('Department items error:', err);
            tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-12 text-center"><div class="flex flex-col items-center justify-center text-red-400"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p class="text-sm">Failed to load items. Please try again.</p></div></td></tr>`;
        });
}

const deptSelect = document.getElementById('department_id');
if (deptSelect) {
    if (deptSelect.value) loadDepartmentItems(deptSelect.value);
    deptSelect.addEventListener('change', () => loadDepartmentItems(deptSelect.value));
}
@endif

document.getElementById('stockCountForm').addEventListener('submit', function(e) {
    // disable rows without physical counts (or with empty inputs)
    document.querySelectorAll('#itemsBody tr[id^="row-"]').forEach(row => {
        const physicalInput = row.querySelector('[name*="physical_quantity"]');
        if (!physicalInput || physicalInput.value === '' || physicalInput.value === null || parseInt(physicalInput.value) === 0) {
            row.querySelectorAll('input, select').forEach(el => el.disabled = true);
        }
    });

    const countedItems = [...document.querySelectorAll('[name*="physical_quantity"]')]
        .filter(el => !el.disabled && el.value !== '' && parseInt(el.value) > 0);

    if (countedItems.length === 0) {
        e.preventDefault();
        document.querySelectorAll('#itemsBody input, #itemsBody select').forEach(el => el.disabled = false);
        alert('Please count at least one item before submitting.');
    }
});

// PREVIEW FUNCTIONALITY
function generatePreviewHTML() {
    const countedBy = document.getElementById('counted_by')?.value || 'Not specified';
    const notes = document.getElementById('notes')?.value || '';
    const countDate = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    const currentUser = @json(Auth::user());
    const departmentName = @json($type === 'department' ? ($selectedDepartment->name ?? 'N/A') : 'Main Store');

    const items = [];
    document.querySelectorAll('#itemsBody tr[id^="row-"]').forEach(row => {
        const itemName = row.querySelector('.font-semibold')?.innerText || row.querySelector('.item-info')?.innerText || 'N/A';
        const unit = row.querySelector('.text-xs.text-gray-400')?.innerText?.split(' ')[0] || 'units';
        const expected = parseInt(row.querySelector('[name*="expected_quantity"]')?.value || row.querySelector('[name*="expected_quantity"]')?.closest('td')?.previousElementSibling?.innerText || 0);
        const physical = parseInt(row.querySelector('[name*="physical_quantity"]')?.value || 0);
        const netQty = Math.max(0, physical);
        const variance = netQty - expected;

        if (physical > 0) {
            items.push({ name: itemName, unit: unit, expected: expected, physical: physical, net: netQty, variance: variance });
        }
    });

    let countedBySignature = '';
    if (currentUser && currentUser.signature_path) {
        countedBySignature = `<img src="{{ asset('storage') }}/${currentUser.signature_path}" style="max-height:50px; max-width:150px;">`;
    }

    return `
        <div style="font-family: Arial, sans-serif; max-width: 100%; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px;">
                <h2 style="margin: 0; color: #111827;">STOCK COUNT SHEET</h2>
                <p style="margin: 4px 0 0; color: #6b7280;">${departmentName} - ${countDate}</p>
                <p style="margin: 4px 0 0; font-size: 12px;">Count #: ${new Date().getTime()}</p>
            </div>

            <div style="margin-bottom: 18px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tr><td style="padding: 4px;"><strong>Counted By:</strong> ${countedBy}</td></td>
                    <tr><td style="padding: 4px;"><strong>Count Date:</strong> ${countDate}</td></tr>
                    <tr><td style="padding: 4px;"><strong>Notes:</strong> ${notes || 'None'}</td></tr>
                </table>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="border:1px solid #e5e7eb; padding:8px; background:#f97316; color:#fff;">#</th>
                        <th style="border:1px solid #e5e7eb; padding:8px; background:#f97316; color:#fff;">Item</th>
                        <th style="border:1px solid #e5e7eb; padding:8px; background:#f97316; color:#fff;">Expected</th>
                        <th style="border:1px solid #e5e7eb; padding:8px; background:#f97316; color:#fff;">Physical</th>
                        <th style="border:1px solid #e5e7eb; padding:8px; background:#f97316; color:#fff;">Net Qty</th>
                        <th style="border:1px solid #e5e7eb; padding:8px; background:#f97316; color:#fff;">Variance</th>
                        <th style="border:1px solid #e5e7eb; padding:8px; background:#f97316; color:#fff;">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    ${items.map((item, idx) => `
                        <tr>
                            <td style="border:1px solid #e5e7eb; padding:8px;">${idx+1}</td>
                            <td style="border:1px solid #e5e7eb; padding:8px;">${esc(item.name)}</td>
                            <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;">${item.expected.toFixed(0)}</td>
                            <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;">${item.physical.toFixed(0)}</td>
                            <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;"><strong>${item.net.toFixed(0)}</strong></td>
                            <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;">${item.variance >= 0 ? '+' : ''}${item.variance.toFixed(0)}</td>
                            <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;">${item.unit}</td>
                        </tr>
                    `).join('')}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="border:1px solid #e5e7eb; padding:8px; text-align:right;"><strong>Total Items Counted:</strong></td>
                        <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;"><strong>${items.length}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 30px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%; text-align: center;">
                            <div style="border-top: 1px solid #111827; width: 80%; margin: 0 auto 10px auto;"></div>
                            ${countedBySignature}
                            <div><strong>${countedBy}</strong></div>
                            <div style="font-size: 11px;">Counted By (Store Staff)</div>
                        </td>
                        <td style="width: 50%; text-align: center;">
                            <div style="border-top: 1px solid #111827; width: 80%; margin: 0 auto 10px auto;"></div>
                            <div class="border-b border-gray-400 w-32 mx-auto mb-2" style="border-bottom: 1px solid #9ca3af; min-height: 30px;"></div>
                            <div style="font-size: 11px; color:#6b7280;">(Sign here)</div>
                            <div><strong>_________________________</strong></div>
                            <div style="font-size: 11px;">Verified By (Supervisor)</div>
                         </td>
                    </tr>
                </table>
            </div>

            <div style="margin-top: 20px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #eef2f7; padding-top: 12px;">
                <p>This is a system-generated stock count sheet. Please verify all counts before signing.</p>
            </div>
        </div>
    `;
}

function showPreviewModal() {
    const countedBy = document.getElementById('counted_by')?.value.trim();
    if (!countedBy) {
        alert('Please enter the name of the person performing the count before previewing.');
        document.getElementById('counted_by').focus();
        return;
    }

    let hasItems = false;
    document.querySelectorAll('#itemsBody tr[id^="row-"]').forEach(row => {
        const physical = row.querySelector('[name*="physical_quantity"]')?.value;
        if (physical && parseInt(physical) > 0) hasItems = true;
    });

    if (!hasItems) {
        alert('Please add and count at least one item before previewing.');
        return;
    }

    const previewContent = generatePreviewHTML();
    document.getElementById('previewContent').innerHTML = previewContent;
    document.getElementById('previewModal').classList.remove('hidden');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}

function printPreview() {
    const printContent = document.getElementById('previewContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Stock Count Preview</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    @media print { body { margin: 0; padding: 0; } }
                </style>
            </head>
            <body>${printContent}
                <script>window.onload = function() { window.print(); window.close(); };<\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}

document.getElementById('previewBtn')?.addEventListener('click', showPreviewModal);
</script>
@endsection
