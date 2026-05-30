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
    .stock-info {
        font-size: 11px;
        margin-top: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .stock-low     { background: #fee2e2; color: #dc2626; }
    .stock-ok      { background: #dcfce7; color: #16a34a; }
    .stock-warning { background: #fef3c7; color: #d97706; }
    .requisition-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
    }
    .type-normal    { background: #d1fae5; color: #065f46; }
    .type-emergency { background: #fee2e2; color: #991b1b; }

    /* ── Print styles ── */
    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            padding: 20px;
        }
        .no-print { display: none !important; }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Requisition Type <span class="text-red-500">*</span>
                    </label>
                    <select name="requisition_type" id="requisition_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="normal"     {{ old('requisition_type') == 'normal'    ? 'selected' : '' }}>Normal</option>
                        <option value="emergency"  {{ old('requisition_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Emergency requisitions will be prioritized</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date Needed <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_needed" id="date_needed"
                           min="{{ date('Y-m-d') }}"
                           value="{{ old('date_needed') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Overall Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              placeholder="Any additional information..."
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
                                <th style="width:30%">Item Name</th>
                                <th style="width:10%">Category</th>
                                <th style="width:10%">Stock</th>
                                <th style="width:10%">Quantity</th>
                                <th style="width:12%">Metrics</th>
                                <th style="width:18%">Notes</th>
                                <th style="width:10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody"></tbody>
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

{{-- ═══════════════════════════════════════════════════════
     PREVIEW MODAL
     ═══════════════════════════════════════════════════════ --}}
<div id="previewModal"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-5xl shadow-lg rounded-lg bg-white">

        {{-- print-section: everything inside here is what gets printed / PDF'd --}}
        <div id="print-section">

            {{-- Logo / header --}}
            <div class="flex justify-between items-start mb-6 pb-4 border-b">
                <div id="logoContainer">
                    @php $logo = \App\Models\BusinessSetting::getLogo(); @endphp
                    @if($logo)
                        {{--
                            FIX (print/PDF): Convert the logo to a base64 data-URI so
                            html2canvas can render it without cross-origin restrictions.
                            We embed it server-side so it is always available.
                        --}}
                        @php
                            $logoPath    = public_path(parse_url($logo, PHP_URL_PATH));
                            $logoExists  = file_exists($logoPath);
                            $logoMime    = $logoExists ? mime_content_type($logoPath) : 'image/png';
                            $logoB64     = $logoExists ? base64_encode(file_get_contents($logoPath)) : null;
                        @endphp
                        @if($logoB64)
                            <img src="data:{{ $logoMime }};base64,{{ $logoB64 }}"
                                 alt="Company Logo" class="h-16 w-auto">
                        @else
                            <img src="{{ $logo }}" alt="Company Logo" class="h-16 w-auto">
                        @endif
                    @else
                        <h2 class="text-2xl font-bold text-gray-800">
                            {{ \App\Models\BusinessSetting::get('company_name', 'Company Name') }}
                        </h2>
                    @endif
                </div>
                <div class="text-right">
                    <h3 class="text-lg font-semibold text-gray-800">REQUISITION FORM</h3>
                    <p class="text-sm text-gray-500" id="previewRequisitionNumber">REQ-XXXX</p>
                </div>
            </div>

            {{-- Modal title + close (hidden on print) --}}
            <div class="flex justify-between items-center mb-4 no-print">
                <h3 class="text-xl font-semibold text-gray-800">Preview Requisition</h3>
                <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                {{-- Meta info --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Requisition Type</p>
                            <p class="font-medium" id="previewType">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Date Needed</p>
                            <p class="font-medium" id="previewDateNeeded">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Requested By</p>
                            <p class="font-medium">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-xs text-gray-500">Overall Notes</p>
                        <p class="font-medium" id="previewNotes">—</p>
                    </div>
                </div>

                {{-- Items table --}}
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Items Requested</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Item Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Category</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Quantity</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Metrics</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="previewItemsBody" class="divide-y divide-gray-200"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Signature section --}}
                <div class="mt-6 pt-4 border-t">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-gray-500">Requested By:</p>
                            <div class="mt-2">
                                @if(Auth::user()->signature_url)
                                    @php
                                        /*
                                         * FIX (print/PDF): Embed the signature as base64 just like the logo.
                                         * This guarantees it renders in html2canvas and the print window.
                                         */
                                        $sigUrl    = Auth::user()->signature_url;
                                        $sigPath   = public_path(parse_url($sigUrl, PHP_URL_PATH));
                                        $sigExists = file_exists($sigPath);
                                        $sigMime   = $sigExists ? mime_content_type($sigPath) : 'image/png';
                                        $sigB64    = $sigExists ? base64_encode(file_get_contents($sigPath)) : null;
                                    @endphp
                                    @if($sigB64)
                                        <img src="data:{{ $sigMime }};base64,{{ $sigB64 }}"
                                             alt="Signature" class="h-12 w-auto">
                                    @else
                                        <img src="{{ $sigUrl }}?v={{ time() }}" alt="Signature" class="h-12 w-auto">
                                    @endif
                                @else
                                    <p class="text-sm text-gray-400">No signature on file</p>
                                @endif
                            </div>
                            <p class="text-sm font-medium mt-2">
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Management Use Only</p>
                            <div class="mt-2">
                                <p class="text-sm text-gray-400">Approved By: _______________</p>
                                <p class="text-sm text-gray-400 mt-2">Date: _______________</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- /#print-section --}}

        {{-- Modal action buttons (hidden on print) --}}
        <div class="mt-6 flex justify-end gap-3 pt-4 border-t no-print">
            <button type="button" onclick="closePreview()"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Edit
            </button>
            <button type="button" onclick="printRequisition()"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button type="button" onclick="downloadPDF()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hidden transition">
                <i class="fas fa-file-pdf mr-1"></i> Download PDF
            </button>
            <button type="button" onclick="submitForm()"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
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
let   itemCounter = 0;
const stockCache  = {};

/*
 * FIX (stock route 404):
 * The route is registered under the "store" prefix, so the correct URL is
 * /store/requisitions/item-stock/{id}.  We use a named-route URL generated
 * server-side to avoid hard-coding any prefix.
 */
const STOCK_BASE_URL = '{{ rtrim(url("/"), "/") }}/store/requisitions/item-stock';
// If your named route is different, replace with:
// const STOCK_BASE_URL = '{{ route("store.requisitions.item-stock", ["id" => "__ID__"]) }}'.replace("/__ID__", "");

// ═══════════════════════════════════════════════════════════════════
//  STATIC OPTION STRINGS
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

/*
 * Each option stores:
 *   data-name     = clean item name (no stock suffix)   ← FIX for preview
 *   data-category = category name
 *   data-stock    = current stock number
 *   data-unit     = base unit
 *
 * The visible option text still shows the stock hint for usability.
 */
const itemOptions = `
    <option value="">-- Search Item --</option>
    @foreach($items as $item)
        <option value="{{ $item->id }}"
                data-name="{{ addslashes($item->name) }}"
                data-category="{{ addslashes($item->category->name ?? 'Uncategorized') }}"
                data-stock="{{ $item->current_stock }}"
                data-unit="{{ $item->base_unit ?? 'pcs' }}">
            {{ $item->name }} ({{ number_format($item->current_stock, 2) }} {{ $item->base_unit ?? 'pcs' }} available)
        </option>
    @endforeach
`;

// ═══════════════════════════════════════════════════════════════════
//  DRAFT HELPERS
// ═══════════════════════════════════════════════════════════════════
function saveDraft() {
    const rows = [];
    document.querySelectorAll('#itemsBody .item-row').forEach(row => {
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
            requisition_type : document.getElementById('requisition_type').value,
            date_needed      : document.getElementById('date_needed').value,
            notes            : document.getElementById('notes').value,
            rows,
        }));
    } catch (e) { /* quota exceeded – fail silently */ }
}

function loadDraft() {
    try { const r = localStorage.getItem(DRAFT_KEY); return r ? JSON.parse(r) : null; }
    catch (e) { return null; }
}

function clearDraft() { localStorage.removeItem(DRAFT_KEY); }

// ═══════════════════════════════════════════════════════════════════
//  STOCK FETCH
//  FIX: use STOCK_BASE_URL so the prefix is always correct
// ═══════════════════════════════════════════════════════════════════
async function fetchStock(itemId, row) {
    if (stockCache[itemId]) { updateStockDisplay(row, stockCache[itemId]); return; }

    try {
        const res  = await fetch(`${STOCK_BASE_URL}/${itemId}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });

        // If the server returned non-JSON (e.g. an HTML error page), surface a useful message
        const contentType = res.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            console.error(`Stock endpoint returned non-JSON (status ${res.status}). Check your route.`);
            row.querySelector('.stock-cell').innerHTML =
                '<span class="stock-info stock-low">Route not found – check STOCK_BASE_URL</span>';
            return;
        }

        const data = await res.json();
        if (data.success) {
            stockCache[itemId] = data;
            updateStockDisplay(row, data);
        } else {
            row.querySelector('.stock-cell').innerHTML =
                '<span class="stock-info stock-low">Stock info unavailable</span>';
        }
    } catch (err) {
        console.error('Error fetching stock:', err);
        row.querySelector('.stock-cell').innerHTML =
            '<span class="stock-info stock-low">Error loading stock</span>';
    }
}

function updateStockDisplay(row, stockData) {
    const stockCell = row.querySelector('.stock-cell');
    const stock = parseFloat(stockData.current_stock);
    const unit  = stockData.base_unit || 'pcs';
    let cls  = 'stock-ok';
    let text = `${stock.toFixed(2)} ${unit} available`;
    if      (stock <= 0)  { cls = 'stock-low';     text = `OUT OF STOCK (${stock.toFixed(2)} ${unit})`; }
    else if (stock < 10)  { cls = 'stock-warning';  text = `Low stock: ${stock.toFixed(2)} ${unit} left`; }
    stockCell.innerHTML = `<span class="stock-info ${cls}">${text}</span>`;
}

// ═══════════════════════════════════════════════════════════════════
//  ROW CREATION
// ═══════════════════════════════════════════════════════════════════
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
        <td class="stock-cell text-sm">—</td>
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
    if (saved) {
        tr.querySelector('input[name*="[quantity]"]').value = saved.quantity || '';
        tr.querySelector('select[name*="[metrics]"]').value = saved.metrics  || '';
        tr.querySelector('input[name*="[notes]"]').value    = saved.notes    || '';
        tr.querySelector('.category-cell').innerText        = saved.category || '—';
    }
    return tr;
}

// ═══════════════════════════════════════════════════════════════════
//  SELECT2
// ═══════════════════════════════════════════════════════════════════
function initSelect2(element, preselectValue) {
    if ($(element).hasClass('select2-hidden-accessible')) $(element).select2('destroy');

    $(element).select2({ placeholder: '-- Search Item --', allowClear: true, width: '100%' });
    $(element).off('change.requisition');

    $(element).on('change.requisition', async function () {
        const opt          = this.options[this.selectedIndex];
        const categoryName = opt ? ($(opt).data('category') || '—') : '—';

        // FIX: unwrap jQuery to DOM element before using querySelector
        const row = $(this).closest('.item-row')[0];
        row.querySelector('.category-cell').innerText = categoryName;

        const itemId = this.value;
        if (itemId) {
            row.querySelector('.stock-cell').innerHTML =
                '<span class="stock-info stock-warning">Loading stock...</span>';
            await fetchStock(itemId, row);
        } else {
            row.querySelector('.stock-cell').innerHTML = '—';
        }
        saveDraft();
    });

    if (preselectValue) $(element).val(preselectValue).trigger('change.requisition');
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
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.removeEventListener('click', removeItem);
        btn.addEventListener('click', removeItem);
    });
}

// ═══════════════════════════════════════════════════════════════════
//  ADD ITEM
// ═══════════════════════════════════════════════════════════════════
document.getElementById('addItemBtn').addEventListener('click', () => {
    const tr = createNewRow(null);
    document.getElementById('itemsBody').appendChild(tr);
    initSelect2($(tr).find('.item-select'), null);
    attachRemoveEvents();
    saveDraft();
});

// ═══════════════════════════════════════════════════════════════════
//  AUTO-SAVE
// ═══════════════════════════════════════════════════════════════════
document.getElementById('requisition_type').addEventListener('change', saveDraft);
document.getElementById('date_needed').addEventListener('change', saveDraft);
document.getElementById('notes').addEventListener('input', saveDraft);
document.getElementById('itemsBody').addEventListener('input', saveDraft);
document.getElementById('itemsBody').addEventListener('change', saveDraft);

// ═══════════════════════════════════════════════════════════════════
//  BOOTSTRAP (on page load)
// ═══════════════════════════════════════════════════════════════════
(function init() {
    const draft = loadDraft();
    const tbody = document.getElementById('itemsBody');

    if (draft && Array.isArray(draft.rows) && draft.rows.length > 0) {
        document.getElementById('requisition_type').value = draft.requisition_type || 'normal';
        document.getElementById('date_needed').value      = draft.date_needed || '';
        document.getElementById('notes').value            = draft.notes || '';
        draft.rows.forEach(savedRow => {
            const tr = createNewRow(savedRow);
            tbody.appendChild(tr);
            initSelect2($(tr).find('.item-select'), savedRow.item_id || null);
        });
        document.getElementById('draftBanner').style.display = 'flex';
    } else {
        const tr = createNewRow(null);
        tbody.appendChild(tr);
        initSelect2($(tr).find('.item-select'), null);
    }
    attachRemoveEvents();
})();

document.getElementById('clearDraftBtn').addEventListener('click', () => {
    if (!confirm('Clear the saved draft and start fresh?')) return;
    clearDraft();
    document.getElementById('requisition_type').value = 'normal';
    document.getElementById('date_needed').value      = '';
    document.getElementById('notes').value            = '';
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
//  HELPERS
// ═══════════════════════════════════════════════════════════════════
function escapeHtml(text) {
    if (!text) return '';
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}

// ═══════════════════════════════════════════════════════════════════
//  PREVIEW MODAL
// ═══════════════════════════════════════════════════════════════════
function openPreview() {
    const reqType      = document.getElementById('requisition_type').value;
    const reqTypeText  = reqType === 'emergency' ? 'EMERGENCY' : 'Normal';
    const reqTypeClass = reqType === 'emergency' ? 'type-emergency' : 'type-normal';

    document.getElementById('previewType').innerHTML =
        `<span class="requisition-type-badge ${reqTypeClass}">${reqTypeText}</span>`;
    document.getElementById('previewDateNeeded').innerText =
        document.getElementById('date_needed').value || 'Not specified';
    document.getElementById('previewNotes').innerText =
        document.getElementById('notes').value || '—';

    const items = [];
    document.querySelectorAll('#itemsBody .item-row').forEach(row => {
        const select   = row.querySelector('.item-select');
        const selOpt   = select.options[select.selectedIndex];

        /*
         * FIX (preview item name):
         * Read the clean name from data-name instead of textContent,
         * which previously included " (500.00 glass available)".
         */
        const itemName = (selOpt && selOpt.value)
            ? ($(selOpt).data('name') || selOpt.textContent.split('(')[0].trim())
            : '';

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
        items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2 text-sm text-gray-800">${escapeHtml(item.name)}</td>
                <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(item.category)}</td>
                <td class="px-4 py-2 text-sm text-gray-800 text-right">${parseFloat(item.quantity).toFixed(2)}</td>
                <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(item.metrics) || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(item.notes) || '—'}</td>
            `;
            previewBody.appendChild(tr);
        });
    }

    // Temp requisition number
    const now    = new Date();
    const pad    = n => String(n).padStart(2, '0');
    const random = Math.floor(Math.random() * 9999).toString().padStart(4, '0');
    document.getElementById('previewRequisitionNumber').innerText =
        `REQ-${now.getFullYear()}${pad(now.getMonth()+1)}${pad(now.getDate())}-${random}`;

    document.getElementById('previewModal').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}

// ═══════════════════════════════════════════════════════════════════
//  PRINT
//  FIX: open a new window and write the print-section HTML into it.
//  Because logo + signature are already base64 data-URIs (embedded
//  server-side), they render correctly with no cross-origin issues.
// ═══════════════════════════════════════════════════════════════════
function printRequisition() {
    const content = document.getElementById('print-section').innerHTML;
    const win = window.open('', '_blank');
    win.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Requisition Form</title>
            <style>
                body { padding: 24px; font-family: Arial, sans-serif; font-size: 13px; }
                table { width: 100%; border-collapse: collapse; margin-top: 8px; }
                th, td { border: 1px solid #d1d5db; padding: 8px 10px; text-align: left; }
                th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; }
                img { max-height: 64px; }
                .no-print { display: none !important; }
                .requisition-type-badge { padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
                .type-normal    { background: #d1fae5; color: #065f46; }
                .type-emergency { background: #fee2e2; color: #991b1b; }
            </style>
        </head>
        <body>${content}</body>
        </html>
    `);
    win.document.close();
    // Small delay so images are painted before the print dialog opens
    win.onload = () => { win.focus(); win.print(); };
}

// ═══════════════════════════════════════════════════════════════════
//  PDF DOWNLOAD
//  FIX: useCORS + allowTaint to handle any remaining external images.
//  Logo/signature are base64 so they never hit CORS, but this is a
//  safe fallback for anything else.
// ═══════════════════════════════════════════════════════════════════
function downloadPDF() {
    const element = document.getElementById('print-section');
    const opt = {
        margin      : [0.5, 0.5, 0.5, 0.5],
        filename    : `requisition-${new Date().toISOString().slice(0,10)}.pdf`,
        image       : { type: 'jpeg', quality: 0.98 },
        html2canvas : { scale: 2, useCORS: true, allowTaint: true, letterRendering: true },
        jsPDF       : { unit: 'in', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}

// ═══════════════════════════════════════════════════════════════════
//  SUBMIT
// ═══════════════════════════════════════════════════════════════════
function submitForm() {
    const rows = document.querySelectorAll('#itemsBody .item-row');
    let valid  = true;
    rows.forEach((row, i) => {
        if (!valid) return;
        const itemId   = row.querySelector('.item-select').value;
        const quantity = row.querySelector('input[name*="[quantity]"]').value;
        if (!itemId)                           { alert(`Please select an item for row ${i+1}`);          valid = false; }
        else if (!quantity || parseFloat(quantity) <= 0) { alert(`Please enter a valid quantity for row ${i+1}`); valid = false; }
    });
    if (valid) { clearDraft(); document.getElementById('requisitionForm').submit(); }
}

document.getElementById('previewBtn').addEventListener('click', openPreview);
</script>
@endsection
