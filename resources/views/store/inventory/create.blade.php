@extends('layouts.store')

@section('title', 'Add Inventory Item')
@section('page-title', 'Add New Inventory Item')

@section('content')
<style>
    /* ── Card toggle ────────────────────────────────────────── */
    .entry-card { cursor: pointer; transition: all 0.3s ease; }
    .entry-card.active { border-color: #059669 !important; background-color: #f0fdf4 !important; }

    /* ── Manual form helpers ────────────────────────────────── */
    .preview-box       { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; }
    .preview-label     { font-weight:600; color:#475569; width:160px; display:inline-block; }
    .bulk-section      { background:#fef3c7; border-radius:8px; padding:15px; margin-top:15px; display:none; }
    .bulk-section.show { display:block; }
    .simple-section      { background:#e0f2fe; border-radius:8px; padding:15px; margin-top:15px; display:none; }
    .simple-section.show { display:block; }
    .total-box    { background:#fef3c7; border-radius:8px; padding:15px; text-align:center; margin:20px 0; }
    .total-box span { font-size:28px; font-weight:bold; color:#92400e; }
    .help-text    { font-size:11px; color:#6b7280; margin-top:4px; }
    .base-unit-box { background:#ede9fe; border:1px solid #c4b5fd; border-radius:8px; padding:12px 15px; margin-top:12px; }
    .conversion-summary      { background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1px solid #86efac; border-radius:8px;
                                padding:14px; margin-top:12px; font-size:14px; color:#166534; display:none; }
    .conversion-summary.show { display:block; }

    /* ── GRN table ──────────────────────────────────────────── */
    .grn-table { width:100%; border-collapse:collapse; font-size:13px; }
    .grn-table th, .grn-table td { padding:10px 12px; border:1px solid #e5e7eb; vertical-align:top; }
    .grn-table th { background:#f9fafb; font-weight:600; white-space:nowrap; }
    .grn-row-bulk    { background:#fffbeb; }   /* warm yellow tint for pack rows */
    .grn-row-simple  { background:#eff6ff; }   /* light blue tint for direct rows */

    /* ── Per-row receiving panel (like manual form) ─────────── */
    .grn-recv-panel  { margin-top:8px; border-radius:6px; padding:10px 12px; font-size:12px; }
    .grn-recv-bulk   { background:#fef3c7; border:1px solid #fcd34d; }
    .grn-recv-simple { background:#e0f2fe; border:1px solid #7dd3fc; }
    .grn-conv-badge  { display:inline-block; background:#dcfce7; border:1px solid #86efac;
                       border-radius:6px; padding:4px 10px; font-size:12px; color:#166534; margin-top:6px; }
    .grn-stock-badge { display:inline-block; background:#f3f4f6; border:1px solid #d1d5db;
                       border-radius:6px; padding:3px 8px; font-size:11px; color:#374151; }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Add New Inventory Item</h3>
        <p class="text-sm text-gray-500">Choose to receive from GRN or manually add new stock</p>
    </div>

    @if(session('error'))
        <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">⚠️ {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
            <strong>Please fix the following errors:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="p-6">

        {{-- ── Entry Type Cards ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div id="manualCard" class="entry-card border-2 border-gray-200 rounded-lg p-4 text-center hover:border-yellow-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <p class="font-semibold text-gray-800">Manual Entry</p>
                <p class="text-xs text-gray-500">Add new stock manually</p>
            </div>
            <div id="grnCard" class="entry-card border-2 border-gray-200 rounded-lg p-4 text-center hover:border-green-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-semibold text-gray-800">From GRN</p>
                <p class="text-xs text-gray-500">Receive from Goods Received Note</p>
            </div>
        </div>

        {{-- ══════════════════ MANUAL SECTION ══════════════════ --}}
        <div id="manualSection">
            <form id="manualForm" method="POST" action="{{ route('store.inventory.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" name="item_name" id="item_name" required
                               class="w-full px-3 py-2 border rounded-lg @error('item_name') border-red-500 @enderror"
                               value="{{ old('item_name') }}">
                        @error('item_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required
                                class="w-full px-3 py-2 border rounded-lg @error('category_id') border-red-500 @enderror">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Item Code</label>
                        <input type="text" name="item_code" id="item_code" placeholder="Auto-generated if empty"
                               class="w-full px-3 py-2 border rounded-lg @error('item_code') border-red-500 @enderror"
                               value="{{ old('item_code') }}">
                        @error('item_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">How are you receiving this item? <span class="text-red-500">*</span></label>
                        <select name="metrics" id="metrics" required
                                class="w-full px-3 py-2 border rounded-lg @error('metrics') border-red-500 @enderror">
                            <option value="">-- Select Receiving Unit --</option>
                            <optgroup label="Direct Units (no unpacking needed)">
                                <option value="kg"          {{ old('metrics')=='kg'          ? 'selected':'' }}>Kilograms (kg)</option>
                                <option value="litres"      {{ old('metrics')=='litres'      ? 'selected':'' }}>Litres (L)</option>
                                <option value="pcs"         {{ old('metrics')=='pcs'         ? 'selected':'' }}>Pieces (pcs)</option>
                                <option value="grams"       {{ old('metrics')=='grams'       ? 'selected':'' }}>Grams (g)</option>
                                <option value="millilitres" {{ old('metrics')=='millilitres' ? 'selected':'' }}>Millilitres (ml)</option>
                            </optgroup>
                            <optgroup label="Pack Units (contain multiple items inside)">
                                <option value="box"    {{ old('metrics')=='box'    ? 'selected':'' }}>Box</option>
                                <option value="carton" {{ old('metrics')=='carton' ? 'selected':'' }}>Carton</option>
                                <option value="crate"  {{ old('metrics')=='crate'  ? 'selected':'' }}>Crate</option>
                                <option value="dozen"  {{ old('metrics')=='dozen'  ? 'selected':'' }}>Dozen</option>
                                <option value="pack"   {{ old('metrics')=='pack'   ? 'selected':'' }}>Pack</option>
                                <option value="sack"   {{ old('metrics')=='sack'   ? 'selected':'' }}>Sack</option>
                                <option value="set"    {{ old('metrics')=='set'    ? 'selected':'' }}>Set</option>
                            </optgroup>
                        </select>
                        @error('metrics')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Base unit --}}
                <div id="baseUnitWrapper" class="base-unit-box" style="display:none;">
                    <label class="block font-medium text-purple-800 mb-1">
                        What is the individual unit you sell / consume? <span class="text-red-500">*</span>
                    </label>
                    <select name="base_unit" id="base_unit"
                            class="w-full md:w-1/2 px-3 py-2 border border-purple-300 rounded-lg @error('base_unit') border-red-500 @enderror">
                        <option value="">-- Select Base Unit --</option>
                        @foreach(['bottle','can','piece','glass','plate','kg','gram','litre','ml','sachet','egg','roll','strip','unit'] as $u)
                            <option value="{{ $u }}" {{ old('base_unit')==$u ? 'selected':'' }}>{{ ucfirst($u) }}</option>
                        @endforeach
                    </select>
                    <div class="help-text">This is what stock will always be counted in</div>
                    @error('base_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div id="simpleSection" class="simple-section">
                    <label class="font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="simple_quantity" step="0.01" min="0"
                           value="{{ old('quantity') }}"
                           class="w-full px-3 py-2 border rounded-lg @error('quantity') border-red-500 @enderror">
                    <div class="help-text">Enter the total quantity you are receiving</div>
                    @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div id="bulkSection" class="bulk-section">
                    <p class="text-sm font-semibold text-yellow-800 mb-3">📦 Tell us about the packs you are receiving</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="font-medium text-gray-700 mb-1">
                                How many <span id="packTypeLabel" class="text-yellow-700">packs</span> received? <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="number_of_units" id="number_of_units" step="1" min="1"
                                   value="{{ old('number_of_units') }}" class="w-full px-3 py-2 border rounded-lg">
                            <div class="help-text">e.g. 3 cartons</div>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 mb-1">
                                How many <span id="baseUnitLabel2" class="text-yellow-700">items</span> in each
                                <span id="packTypeLabel2" class="text-yellow-700">pack</span>? <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="pieces_per_unit" id="pieces_per_unit" step="1" min="1"
                                   value="{{ old('pieces_per_unit') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('pieces_per_unit') border-red-500 @enderror">
                            <div class="help-text">e.g. 24 bottles per carton</div>
                            @error('pieces_per_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-end">
                            <div id="conversionSummary" class="conversion-summary w-full">
                                <strong>📊 Conversion:</strong><br>
                                <span id="conversionText"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="total-box">
                    📊 <strong>Total Stock to be Added:</strong>
                    <span id="total_quantity_display">0.00</span>
                    <span id="total_unit_label" class="text-lg font-semibold text-yellow-800">units</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Store Location</label>
                        <select name="store_id" class="w-full px-3 py-2 border rounded-lg">
                            <option value="1" {{ old('store_id')==1 ? 'selected':'' }}>Main Store</option>
                            <option value="2" {{ old('store_id')==2 ? 'selected':'' }}>Bar Store</option>
                            <option value="3" {{ old('store_id')==3 ? 'selected':'' }}>Cafe Store</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Supplier/Vendor</label>
                        <select name="vendor_id" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ old('vendor_id')==$v->id ? 'selected':'' }}>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full px-3 py-2 border rounded-lg"
                              placeholder="Batch number, expiry date, etc.">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('store.inventory.index') }}" class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="button" onclick="showPreview()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Preview</button>
                </div>
            </form>
        </div>

        {{-- ══════════════════ GRN SECTION ══════════════════ --}}
        <div id="grnSection" style="display:none;">
            <div class="bg-green-50 p-4 rounded-lg mb-4">
                <label class="block font-medium text-gray-700 mb-2">Select Goods Received Note (GRN)</label>
                <select id="grn_select" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">-- Select GRN --</option>
                    @foreach($grns as $grn)
                        <option value="{{ $grn->id }}">
                            {{ $grn->grn_number }} — {{ $grn->vendor->name ?? 'N/A' }} — {{ \Carbon\Carbon::parse($grn->received_date)->format('Y-m-d') }}
                        </option>
                    @endforeach
                </select>
                <button type="button" id="loadGrnBtn"
                        class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Load GRN Items
                </button>
            </div>

            <div id="grnErrorBox" class="hidden p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg mb-4"></div>

            <div id="grnItemsContainer" style="display:none;">
                <form method="POST" action="{{ route('store.inventory.store-from-grn') }}" id="grnForm">
                    @csrf
                    <input type="hidden" name="grn_id" id="selected_grn_id">

                    {{-- Summary banner --}}
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                        <strong>ℹ️ Instructions:</strong> For each item below, choose <em>how you are receiving it</em>
                        (direct unit or pack unit) and fill in the quantities. The system will calculate total stock
                        and top it up to what is already in inventory.
                    </div>

                    <div id="grnItemsTable"></div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" id="cancelGrnBtn"
                                class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            ✅ Receive All Into Inventory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════ PREVIEW MODAL (manual) ══════════════════ --}}
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-xl flex justify-between items-center">
            <h3 class="text-xl font-semibold text-white">Preview Inventory Entry</h3>
            <button type="button" onclick="closePreview()" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="preview-box">
                <div class="grid grid-cols-2 gap-3">
                    <div><span class="preview-label">Item Name:</span>    <span id="preview_item" class="font-semibold">—</span></div>
                    <div><span class="preview-label">Category:</span>     <span id="preview_category">—</span></div>
                    <div><span class="preview-label">Item Code:</span>    <span id="preview_code">—</span></div>
                    <div><span class="preview-label">Receiving Unit:</span><span id="preview_metrics">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Base/Sell Unit:</span>   <span id="preview_base_unit" class="font-semibold text-purple-700">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Receipt Detail:</span>   <span id="preview_type">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Total Stock Added:</span><span id="preview_quantity" class="font-bold text-green-600 text-lg">0</span></div>
                    <div class="col-span-2"><span class="preview-label">Store:</span>            <span id="preview_store">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Vendor:</span>           <span id="preview_vendor">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Notes:</span>            <span id="preview_notes">—</span></div>
                </div>
            </div>
            <div id="previewErrorBox" class="hidden mt-4 p-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm"></div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closePreview()" class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Edit</button>
                <button type="button" onclick="submitForm()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Confirm & Save</button>
            </div>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════
// Shared helpers
// ═══════════════════════════════════════════════════════════════════
const BULK_METRICS   = ['box','carton','crate','dozen','pack','sack','set'];
const SIMPLE_METRICS = ['kg','litres','pcs','grams','millilitres'];

const BASE_UNIT_OPTIONS = [
    { value:'bottle', label:'Bottle'  },
    { value:'can',    label:'Can'     },
    { value:'piece',  label:'Piece'   },
    { value:'glass',  label:'Glass'   },
    { value:'plate',  label:'Plate'   },
    { value:'kg',     label:'Kg'      },
    { value:'gram',   label:'Gram'    },
    { value:'litre',  label:'Litre'   },
    { value:'ml',     label:'ml'      },
    { value:'sachet', label:'Sachet'  },
    { value:'egg',    label:'Egg'     },
    { value:'roll',   label:'Roll'    },
    { value:'strip',  label:'Strip'   },
    { value:'unit',   label:'Unit'    },
];

const RECEIVING_UNIT_OPTIONS = [
    { value:'kg',          label:'Kilograms (kg)',    group:'direct' },
    { value:'litres',      label:'Litres (L)',         group:'direct' },
    { value:'pcs',         label:'Pieces (pcs)',       group:'direct' },
    { value:'grams',       label:'Grams (g)',          group:'direct' },
    { value:'millilitres', label:'Millilitres (ml)',   group:'direct' },
    { value:'box',         label:'Box',                group:'pack'   },
    { value:'carton',      label:'Carton',             group:'pack'   },
    { value:'crate',       label:'Crate',              group:'pack'   },
    { value:'dozen',       label:'Dozen',              group:'pack'   },
    { value:'pack',        label:'Pack',               group:'pack'   },
    { value:'sack',        label:'Sack',               group:'pack'   },
    { value:'set',         label:'Set',                group:'pack'   },
];

const DIRECT_BASE_MAP = { kg:'kg', grams:'gram', litres:'litre', millilitres:'ml', pcs:'piece' };

function buildBaseUnitSelect(name, selectedValue = '') {
    let opts = `<option value="">-- Select --</option>`;
    BASE_UNIT_OPTIONS.forEach(o => {
        const sel = o.value === selectedValue ? 'selected' : '';
        opts += `<option value="${o.value}" ${sel}>${o.label}</option>`;
    });
    return `<select name="${name}" class="w-full px-2 py-1 border rounded text-sm">${opts}</select>`;
}

function buildReceivingUnitSelect(name, selectedValue = '') {
    const directOpts = RECEIVING_UNIT_OPTIONS.filter(o => o.group === 'direct');
    const packOpts   = RECEIVING_UNIT_OPTIONS.filter(o => o.group === 'pack');
    let html = `<select name="${name}" class="w-full px-2 py-1 border rounded text-sm">
        <option value="">-- Select --</option>
        <optgroup label="Direct Units">`;
    directOpts.forEach(o => {
        html += `<option value="${o.value}" ${o.value===selectedValue?'selected':''}>${o.label}</option>`;
    });
    html += `</optgroup><optgroup label="Pack Units">`;
    packOpts.forEach(o => {
        html += `<option value="${o.value}" ${o.value===selectedValue?'selected':''}>${o.label}</option>`;
    });
    html += `</optgroup></select>`;
    return html;
}

function showInlineError(id, msg) {
    const el = document.getElementById(id);
    if (el) { el.textContent = '⚠️ ' + msg; el.classList.remove('hidden'); }
}
function hideInlineError(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

// ═══════════════════════════════════════════════════════════════════
// Entry card toggle
// ═══════════════════════════════════════════════════════════════════
document.getElementById('manualCard').addEventListener('click', function () {
    this.classList.add('active','border-yellow-500','bg-yellow-50');
    document.getElementById('grnCard').classList.remove('active','border-green-500','bg-green-50');
    document.getElementById('manualSection').style.display = 'block';
    document.getElementById('grnSection').style.display   = 'none';
});
document.getElementById('grnCard').addEventListener('click', function () {
    this.classList.add('active','border-green-500','bg-green-50');
    document.getElementById('manualCard').classList.remove('active','border-yellow-500','bg-yellow-50');
    document.getElementById('manualSection').style.display = 'none';
    document.getElementById('grnSection').style.display   = 'block';
});

// ═══════════════════════════════════════════════════════════════════
// MANUAL FORM logic (unchanged from original)
// ═══════════════════════════════════════════════════════════════════
const metricsSelect     = document.getElementById('metrics');
const baseUnitSelect    = document.getElementById('base_unit');
const baseUnitWrapper   = document.getElementById('baseUnitWrapper');
const simpleSection     = document.getElementById('simpleSection');
const bulkSection       = document.getElementById('bulkSection');
const totalQuantitySpan = document.getElementById('total_quantity_display');
const totalUnitLabel    = document.getElementById('total_unit_label');
const simpleQuantity    = document.getElementById('simple_quantity');
const piecesPerUnit     = document.getElementById('pieces_per_unit');
const numberOfUnits     = document.getElementById('number_of_units');
const conversionSummary = document.getElementById('conversionSummary');
const conversionText    = document.getElementById('conversionText');

function updateManualForm() {
    const sel = metricsSelect.value;
    if (!sel) {
        simpleSection.classList.remove('show');
        bulkSection.classList.remove('show');
        baseUnitWrapper.style.display = 'none';
        calculateManualTotal();
        return;
    }
    baseUnitWrapper.style.display = 'block';
    if (BULK_METRICS.includes(sel)) {
        simpleSection.classList.remove('show');
        bulkSection.classList.add('show');
        document.getElementById('packTypeLabel').innerText  = sel;
        document.getElementById('packTypeLabel2').innerText = sel;
        document.getElementById('baseUnitLabel2').innerText = baseUnitSelect.value || 'items';
    } else {
        simpleSection.classList.add('show');
        bulkSection.classList.remove('show');
        conversionSummary.classList.remove('show');
        if (DIRECT_BASE_MAP[sel]) baseUnitSelect.value = DIRECT_BASE_MAP[sel];
        totalUnitLabel.innerText = sel;
    }
    calculateManualTotal();
}

baseUnitSelect.addEventListener('change', function () {
    document.getElementById('baseUnitLabel2').innerText = this.value || 'items';
    totalUnitLabel.innerText = this.value || 'units';
    calculateManualTotal();
});

function calculateManualTotal() {
    const sel = metricsSelect.value;
    const bu  = baseUnitSelect.value || 'units';
    let total = 0;
    if (BULK_METRICS.includes(sel)) {
        const pieces = parseFloat(piecesPerUnit.value) || 0;
        const units  = parseFloat(numberOfUnits.value) || 0;
        total = pieces * units;
        if (pieces > 0 && units > 0) {
            conversionSummary.classList.add('show');
            conversionText.innerHTML = `${units} ${sel}(s) &times; ${pieces} ${bu} each = <strong>${total} ${bu}s</strong>`;
        } else {
            conversionSummary.classList.remove('show');
        }
        totalUnitLabel.innerText = bu + 's';
    } else {
        total = parseFloat(simpleQuantity.value) || 0;
        totalUnitLabel.innerText = sel || bu;
        conversionSummary.classList.remove('show');
    }
    totalQuantitySpan.innerText = total.toLocaleString(undefined, { minimumFractionDigits:2, maximumFractionDigits:2 });
    return total;
}

metricsSelect.addEventListener('change', updateManualForm);
simpleQuantity.addEventListener('input', calculateManualTotal);
piecesPerUnit.addEventListener('input',  calculateManualTotal);
numberOfUnits.addEventListener('input',  calculateManualTotal);

function showPreview() {
    hideInlineError('previewErrorBox');
    const itemName = document.getElementById('item_name').value.trim();
    if (!itemName)                        { alert('Please fill in the item name.'); return; }
    if (!metricsSelect.value)             { alert('Please select a receiving unit.'); return; }
    if (!baseUnitSelect.value)            { alert('Please select the base/selling unit.'); return; }
    const totalQuantity = calculateManualTotal();
    if (totalQuantity <= 0)               { alert('Total quantity must be greater than zero.'); return; }

    const catSel     = document.getElementById('category_id');
    const storeSel   = document.querySelector('select[name="store_id"]');
    const vendorSel  = document.querySelector('select[name="vendor_id"]');
    const metrics    = metricsSelect.value;
    const bu         = baseUnitSelect.value;
    let detail       = '';
    if (BULK_METRICS.includes(metrics)) {
        detail = `${numberOfUnits.value} ${metrics}(s) × ${piecesPerUnit.value} ${bu} = ${totalQuantity} ${bu}s`;
    } else {
        detail = `${totalQuantity} ${metrics} (direct receipt)`;
    }

    document.getElementById('preview_item').innerText      = itemName;
    document.getElementById('preview_category').innerText  = catSel.options[catSel.selectedIndex]?.text || '—';
    document.getElementById('preview_code').innerText      = document.getElementById('item_code').value || 'Auto-generated';
    document.getElementById('preview_metrics').innerText   = metrics;
    document.getElementById('preview_base_unit').innerText = bu;
    document.getElementById('preview_type').innerText      = detail;
    document.getElementById('preview_quantity').innerText  = totalQuantity.toLocaleString() + ' ' + bu + '(s)';
    document.getElementById('preview_store').innerText     = storeSel.options[storeSel.selectedIndex]?.text || '—';
    document.getElementById('preview_vendor').innerText    = vendorSel.options[vendorSel.selectedIndex]?.text || 'None';
    document.getElementById('preview_notes').innerText     = document.getElementById('notes').value || '—';
    document.getElementById('previewModal').style.display  = 'flex';
}
function closePreview() { document.getElementById('previewModal').style.display = 'none'; }
function submitForm()   { document.getElementById('manualForm').submit(); }

// ═══════════════════════════════════════════════════════════════════
// GRN SECTION — Load & render
// ═══════════════════════════════════════════════════════════════════
document.getElementById('loadGrnBtn').addEventListener('click', function () {
    hideInlineError('grnErrorBox');
    const grnId = document.getElementById('grn_select').value;
    if (!grnId) { showInlineError('grnErrorBox', 'Please select a GRN first.'); return; }

    const btn = this;
    btn.textContent = 'Loading…';
    btn.disabled    = true;

    fetch(`/store/inventory/get-grn-items/${grnId}`)
        .then(r => { if (!r.ok) throw new Error(`Server error ${r.status}`); return r.json(); })
        .then(data => {
            if (data.success && data.items && data.items.length > 0) {
                renderGrnItems(data.items, grnId);
                document.getElementById('grnItemsContainer').style.display = 'block';
            } else {
                showInlineError('grnErrorBox', data.message || 'No items found in this GRN.');
            }
        })
        .catch(err => showInlineError('grnErrorBox', 'Failed to load GRN: ' + err.message))
        .finally(() => { btn.textContent = 'Load GRN Items'; btn.disabled = false; });
});

// ── Render GRN items ────────────────────────────────────────────────
function renderGrnItems(items, grnId) {
    document.getElementById('selected_grn_id').value = grnId;
    let html = '';

    items.forEach((item, idx) => {
        const defaultMetrics  = item.metrics  || 'pcs';
        const defaultBaseUnit = item.base_unit || 'piece';
        const isBulkDefault   = BULK_METRICS.includes(defaultMetrics);
        const currentStock    = parseFloat(item.current_stock || 0).toFixed(2);
        const unitCost        = parseFloat(item.unit_cost || 0).toFixed(2);
        const qtyAccepted     = parseFloat(item.quantity_accepted || 0).toFixed(2);

        html += `
        <div class="border border-gray-200 rounded-lg mb-4 overflow-hidden" id="grnRow_${idx}">
            <!-- Item header -->
            <div class="flex items-start justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                <div>
                    <span class="font-semibold text-gray-800">${item.item_name}</span>
                    ${item.item_code ? `<span class="ml-2 text-xs text-gray-500">${item.item_code}</span>` : ''}
                    ${item.category  ? `<span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">${item.category}</span>` : ''}
                </div>
                <span class="grn-stock-badge">Current stock: ${currentStock} ${defaultBaseUnit}(s)</span>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" name="items[${idx}][grn_item_id]"       value="${item.id}">
            <input type="hidden" name="items[${idx}][inventory_item_id]" value="${item.inventory_item_id}">
            <input type="hidden" name="items[${idx}][unit_cost]"         value="${item.unit_cost}">

            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- LEFT: Receiving unit + pack details -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        How are you receiving this item? <span class="text-red-500">*</span>
                    </label>
                    <select name="items[${idx}][receiving_metrics]"
                            id="grnMetrics_${idx}"
                            class="w-full px-3 py-2 border rounded-lg text-sm"
                            onchange="onGrnMetricsChange(${idx})">
                        <option value="">-- Select Receiving Unit --</option>
                        <optgroup label="Direct Units (no unpacking)">
                            ${RECEIVING_UNIT_OPTIONS.filter(o=>o.group==='direct').map(o =>
                                `<option value="${o.value}" ${o.value===defaultMetrics?'selected':''}>${o.label}</option>`
                            ).join('')}
                        </optgroup>
                        <optgroup label="Pack Units (contains multiple items)">
                            ${RECEIVING_UNIT_OPTIONS.filter(o=>o.group==='pack').map(o =>
                                `<option value="${o.value}" ${o.value===defaultMetrics?'selected':''}>${o.label}</option>`
                            ).join('')}
                        </optgroup>
                    </select>

                    <!-- Base unit selector -->
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-purple-800 mb-1">
                            Individual unit you sell/consume <span class="text-red-500">*</span>
                        </label>
                        <select name="items[${idx}][base_unit]"
                                id="grnBaseUnit_${idx}"
                                class="w-full px-3 py-2 border border-purple-300 rounded-lg text-sm"
                                onchange="onGrnBaseUnitChange(${idx})">
                            <option value="">-- Select Base Unit --</option>
                            ${BASE_UNIT_OPTIONS.map(o =>
                                `<option value="${o.value}" ${o.value===defaultBaseUnit?'selected':''}>${o.label}</option>`
                            ).join('')}
                        </select>
                    </div>

                    <!-- Dynamic pack / qty panel -->
                    <div id="grnPanel_${idx}" class="grn-recv-panel ${isBulkDefault ? 'grn-recv-bulk' : 'grn-recv-simple'} mt-3">
                        <!-- filled by onGrnMetricsChange -->
                    </div>
                </div>

                <!-- RIGHT: Quantity to receive + unit cost + total summary -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Quantity to Receive <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500">(max accepted: ${qtyAccepted})</span>
                    </label>
                    <input type="number"
                           name="items[${idx}][quantity]"
                           id="grnQty_${idx}"
                           value="${qtyAccepted}"
                           min="0.01"
                           max="${qtyAccepted}"
                           step="0.01"
                           class="w-full px-3 py-2 border rounded-lg text-sm"
                           oninput="recalcGrnRow(${idx})">

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost (UGX)</label>
                        <input type="number" value="${unitCost}" readonly
                               class="w-full px-3 py-2 border bg-gray-50 rounded-lg text-sm text-gray-600">
                    </div>

                    <!-- Total base units badge -->
                    <div id="grnTotal_${idx}" class="grn-conv-badge mt-3 hidden">
                        Will add <strong id="grnTotalVal_${idx}">0</strong>
                        <span id="grnTotalUnit_${idx}">${defaultBaseUnit}</span>(s) to stock
                        <span class="text-gray-500">(was ${currentStock})</span>
                    </div>
                </div>
            </div>
        </div>`;
    });

    document.getElementById('grnItemsTable').innerHTML = html;

    // Trigger initial render for each row
    items.forEach((item, idx) => onGrnMetricsChange(idx));
}

// ── Called when receiving_metrics changes for a GRN row ─────────────
function onGrnMetricsChange(idx) {
    const metrics   = document.getElementById(`grnMetrics_${idx}`)?.value || '';
    const panelEl   = document.getElementById(`grnPanel_${idx}`);
    const isBulk    = BULK_METRICS.includes(metrics);

    panelEl.className = `grn-recv-panel mt-3 ${isBulk ? 'grn-recv-bulk' : 'grn-recv-simple'}`;

    // Auto-fill base unit for direct units
    const baseUnitSel = document.getElementById(`grnBaseUnit_${idx}`);
    if (!isBulk && DIRECT_BASE_MAP[metrics] && baseUnitSel) {
        baseUnitSel.value = DIRECT_BASE_MAP[metrics];
    }

    if (isBulk) {
        panelEl.innerHTML = `
            <p class="text-xs font-semibold text-yellow-800 mb-2">📦 Pack details</p>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs text-gray-600">Items per ${metrics}</label>
                    <input type="number" name="items[${idx}][pack_size]" id="grnPackSize_${idx}"
                           min="1" step="1" placeholder="e.g. 24"
                           class="w-full px-2 py-1 border rounded text-sm"
                           oninput="recalcGrnRow(${idx})">
                </div>
                <div class="flex items-end">
                    <div class="text-xs text-yellow-700 mt-1" id="grnPackNote_${idx}">Enter items per ${metrics}</div>
                </div>
            </div>`;
    } else {
        // For direct units hide pack_size (send 1 so validation passes)
        panelEl.innerHTML = `
            <input type="hidden" name="items[${idx}][pack_size]" value="1">
            <p class="text-xs text-blue-700">✔ Receiving directly — no unpacking needed.</p>`;
    }

    recalcGrnRow(idx);
}

function onGrnBaseUnitChange(idx) {
    recalcGrnRow(idx);
}

// ── Recalculate total base units for a GRN row ───────────────────────
function recalcGrnRow(idx) {
    const metrics    = document.getElementById(`grnMetrics_${idx}`)?.value || '';
    const baseUnit   = document.getElementById(`grnBaseUnit_${idx}`)?.value || 'unit';
    const qty        = parseFloat(document.getElementById(`grnQty_${idx}`)?.value) || 0;
    const isBulk     = BULK_METRICS.includes(metrics);
    const packSizeEl = document.getElementById(`grnPackSize_${idx}`);
    const packSize   = packSizeEl ? (parseFloat(packSizeEl.value) || 0) : 1;
    const totalEl    = document.getElementById(`grnTotal_${idx}`);
    const totalValEl = document.getElementById(`grnTotalVal_${idx}`);
    const totalUnitEl= document.getElementById(`grnTotalUnit_${idx}`);
    const noteEl     = document.getElementById(`grnPackNote_${idx}`);

    let total = isBulk ? qty * packSize : qty;

    if (totalEl && totalValEl && totalUnitEl) {
        if (total > 0 && (!isBulk || packSize > 0)) {
            totalEl.classList.remove('hidden');
            totalValEl.textContent  = total % 1 === 0 ? total : total.toFixed(2);
            totalUnitEl.textContent = baseUnit;
        } else {
            totalEl.classList.add('hidden');
        }
    }

    if (noteEl && isBulk && packSize > 0 && qty > 0) {
        noteEl.textContent = `${qty} ${metrics} × ${packSize} = ${total} ${baseUnit}s`;
    }
}

// ── Cancel GRN ──────────────────────────────────────────────────────
document.getElementById('cancelGrnBtn').addEventListener('click', function () {
    document.getElementById('grnItemsContainer').style.display = 'none';
    document.getElementById('grn_select').value = '';
    document.getElementById('grnItemsTable').innerHTML = '';
    hideInlineError('grnErrorBox');
});

// ── GRN form submit validation ───────────────────────────────────────
document.getElementById('grnForm').addEventListener('submit', function (e) {
    const rows = document.querySelectorAll('[id^="grnMetrics_"]');
    let errors = [];

    rows.forEach((sel, i) => {
        const idx      = sel.id.replace('grnMetrics_', '');
        const metrics  = sel.value;
        const baseUnit = document.getElementById(`grnBaseUnit_${idx}`)?.value;
        const qty      = parseFloat(document.getElementById(`grnQty_${idx}`)?.value) || 0;
        const isBulk   = BULK_METRICS.includes(metrics);
        const packSize = isBulk ? (parseFloat(document.getElementById(`grnPackSize_${idx}`)?.value) || 0) : 1;
        const name     = sel.closest('[id^="grnRow_"]')?.querySelector('.font-semibold')?.textContent?.trim() || `Row ${+idx+1}`;

        if (!metrics)  errors.push(`"${name}": please select a receiving unit.`);
        if (!baseUnit) errors.push(`"${name}": please select a base unit.`);
        if (qty <= 0)  errors.push(`"${name}": quantity must be greater than zero.`);
        if (isBulk && packSize < 1) errors.push(`"${name}": enter how many items are in each ${metrics}.`);
    });

    if (errors.length > 0) {
        e.preventDefault();
        showInlineError('grnErrorBox', errors.join(' | '));
        document.getElementById('grnErrorBox').scrollIntoView({ behavior:'smooth' });
    }
});

// ═══════════════════════════════════════════════════════════════════
// Init
// ═══════════════════════════════════════════════════════════════════
// Default to manual card active
document.getElementById('manualCard').classList.add('active','border-yellow-500','bg-yellow-50');
document.getElementById('manualSection').style.display = 'block';

// Restore after validation failure
const oldMetrics = '{{ old("metrics") }}';
if (oldMetrics) { metricsSelect.value = oldMetrics; updateManualForm(); }
const oldBase = '{{ old("base_unit") }}';
if (oldBase) { baseUnitSelect.value = oldBase; }
</script>
@endsection
