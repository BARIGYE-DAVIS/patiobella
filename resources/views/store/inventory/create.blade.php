@extends('layouts.store')

@section('title', 'Add Inventory Item')

@section('page-title', 'Add New Inventory Item')

@section('content')
<style>
    .entry-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .entry-card.active {
        border-color: #059669 !important;
        background-color: #f0fdf4 !important;
    }
    .preview-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
    }
    .preview-label {
        font-weight: 600;
        color: #475569;
        width: 160px;
        display: inline-block;
    }
    .bulk-section {
        background-color: #fef3c7;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        display: none;
    }
    .bulk-section.show {
        display: block;
    }
    .simple-section {
        background-color: #e0f2fe;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        display: none;
    }
    .simple-section.show {
        display: block;
    }
    .total-box {
        background-color: #fef3c7;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        margin: 20px 0;
    }
    .total-box span {
        font-size: 28px;
        font-weight: bold;
        color: #92400e;
    }
    .help-text {
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
    }
    .grn-table {
        width: 100%;
        border-collapse: collapse;
    }
    .grn-table th, .grn-table td {
        padding: 12px;
        border: 1px solid #e5e7eb;
        text-align: left;
    }
    .grn-table th {
        background-color: #f9fafb;
        font-weight: 600;
    }
    .base-unit-box {
        background-color: #ede9fe;
        border: 1px solid #c4b5fd;
        border-radius: 8px;
        padding: 12px 15px;
        margin-top: 12px;
    }
    .conversion-summary {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1px solid #86efac;
        border-radius: 8px;
        padding: 14px;
        margin-top: 12px;
        font-size: 14px;
        color: #166534;
        display: none;
    }
    .conversion-summary.show {
        display: block;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Add New Inventory Item</h3>
        <p class="text-sm text-gray-500">Choose to receive from GRN or manually add new stock</p>
    </div>

    {{-- Server-side error display --}}
    @if(session('error'))
        <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
            <strong>Please fix the following errors:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="p-6">
        {{-- Entry Type Selection Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div id="manualCard" class="entry-card border-2 border-gray-200 rounded-lg p-4 text-center hover:border-yellow-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <p class="font-semibold text-gray-800">Manual Entry</p>
                <p class="text-xs text-gray-500">Add new stock manually</p>
            </div>
            <div id="grnCard" class="entry-card border-2 border-gray-200 rounded-lg p-4 text-center hover:border-green-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-semibold text-gray-800">From GRN</p>
                <p class="text-xs text-gray-500">Receive from Goods Received Note</p>
            </div>
        </div>

        {{-- ==================== MANUAL ENTRY SECTION ==================== --}}
        <div id="manualSection">
            <form id="manualForm" method="POST" action="{{ route('store.inventory.store') }}">
                @csrf

                {{-- Row 1: Item Name + Category --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" name="item_name" id="item_name"
                               class="w-full px-3 py-2 border rounded-lg @error('item_name') border-red-500 @enderror"
                               value="{{ old('item_name') }}" required>
                        @error('item_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id"
                                class="w-full px-3 py-2 border rounded-lg @error('category_id') border-red-500 @enderror" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Row 2: Item Code + Receiving/Pack Unit (metrics) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Item Code</label>
                        <input type="text" name="item_code" id="item_code"
                               class="w-full px-3 py-2 border rounded-lg @error('item_code') border-red-500 @enderror"
                               placeholder="Auto-generated if empty" value="{{ old('item_code') }}">
                        @error('item_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            How are you receiving this item? <span class="text-red-500">*</span>
                        </label>
                        <select name="metrics" id="metrics"
                                class="w-full px-3 py-2 border rounded-lg @error('metrics') border-red-500 @enderror" required>
                            <option value="">-- Select Receiving Unit --</option>
                            <optgroup label="Direct Units (no unpacking needed)">
                                <option value="kg"          {{ old('metrics') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                <option value="litres"      {{ old('metrics') == 'litres' ? 'selected' : '' }}>Litres (L)</option>
                                <option value="pcs"         {{ old('metrics') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                <option value="grams"       {{ old('metrics') == 'grams' ? 'selected' : '' }}>Grams (g)</option>
                                <option value="millilitres" {{ old('metrics') == 'millilitres' ? 'selected' : '' }}>Millilitres (ml)</option>
                            </optgroup>
                            <optgroup label="Pack Units (contain multiple items inside)">
                                <option value="box"    {{ old('metrics') == 'box' ? 'selected' : '' }}>Box</option>
                                <option value="carton" {{ old('metrics') == 'carton' ? 'selected' : '' }}>Carton</option>
                                <option value="crate"  {{ old('metrics') == 'crate' ? 'selected' : '' }}>Crate</option>
                                <option value="dozen"  {{ old('metrics') == 'dozen' ? 'selected' : '' }}>Dozen</option>
                                <option value="pack"   {{ old('metrics') == 'pack' ? 'selected' : '' }}>Pack</option>
                                <option value="sack"   {{ old('metrics') == 'sack' ? 'selected' : '' }}>Sack</option>
                                <option value="set"    {{ old('metrics') == 'set' ? 'selected' : '' }}>Set</option>
                            </optgroup>
                        </select>
                        @error('metrics')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- BASE UNIT — always visible once metrics is chosen --}}
                <div id="baseUnitWrapper" class="base-unit-box" style="display:none;">
                    <label class="block font-medium text-purple-800 mb-1">
                        What is the individual unit you sell / consume? <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <select name="base_unit" id="base_unit"
                                    class="w-full px-3 py-2 border border-purple-300 rounded-lg @error('base_unit') border-red-500 @enderror">
                                <option value="">-- Select Base Unit --</option>
                                <option value="bottle"  {{ old('base_unit') == 'bottle'  ? 'selected' : '' }}>Bottle</option>
                                <option value="can"     {{ old('base_unit') == 'can'     ? 'selected' : '' }}>Can</option>
                                <option value="piece"   {{ old('base_unit') == 'piece'   ? 'selected' : '' }}>Piece</option>
                                <option value="glass"   {{ old('base_unit') == 'glass'   ? 'selected' : '' }}>Glass</option>
                                <option value="plate"   {{ old('base_unit') == 'plate'   ? 'selected' : '' }}>Plate</option>
                                <option value="kg"      {{ old('base_unit') == 'kg'      ? 'selected' : '' }}>Kilogram (kg)</option>
                                <option value="gram"    {{ old('base_unit') == 'gram'    ? 'selected' : '' }}>Gram (g)</option>
                                <option value="litre"   {{ old('base_unit') == 'litre'   ? 'selected' : '' }}>Litre (L)</option>
                                <option value="ml"      {{ old('base_unit') == 'ml'      ? 'selected' : '' }}>Millilitre (ml)</option>
                                <option value="sachet"  {{ old('base_unit') == 'sachet'  ? 'selected' : '' }}>Sachet</option>
                                <option value="egg"     {{ old('base_unit') == 'egg'     ? 'selected' : '' }}>Egg</option>
                                <option value="roll"    {{ old('base_unit') == 'roll'    ? 'selected' : '' }}>Roll</option>
                                <option value="strip"   {{ old('base_unit') == 'strip'   ? 'selected' : '' }}>Strip</option>
                                <option value="unit"    {{ old('base_unit') == 'unit'    ? 'selected' : '' }}>Unit (generic)</option>
                            </select>
                            <div class="help-text">This is what stock will always be counted in</div>
                            @error('base_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        {{-- For direct units, base_unit = metrics, so we show a note --}}
                        <div id="baseUnitNote" class="text-sm text-purple-700 flex items-center" style="display:none !important;"></div>
                    </div>
                </div>

                {{-- SIMPLE SECTION: Direct units (kg, litres, pcs, etc.) --}}
                <div id="simpleSection" class="simple-section">
                    <label class="font-medium text-gray-700 mb-1">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity" id="simple_quantity"
                           step="0.01" min="0" value="{{ old('quantity') }}"
                           class="w-full px-3 py-2 border rounded-lg @error('quantity') border-red-500 @enderror">
                    <div class="help-text">Enter the total quantity you are receiving</div>
                    @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- BULK SECTION: Pack units (box, carton, crate, etc.) --}}
                <div id="bulkSection" class="bulk-section">
                    <p class="text-sm font-semibold text-yellow-800 mb-3">
                        📦 Tell us about the packs you are receiving
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="font-medium text-gray-700 mb-1">
                                How many <span id="packTypeLabel" class="text-yellow-700">packs</span> received?
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="number_of_units" id="number_of_units"
                                   step="1" min="1" value="{{ old('number_of_units') }}"
                                   class="w-full px-3 py-2 border rounded-lg">
                            <div class="help-text">e.g. 3 cartons</div>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 mb-1">
                                How many <span id="baseUnitLabel2" class="text-yellow-700">items</span> in each
                                <span id="packTypeLabel2" class="text-yellow-700">pack</span>?
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="pieces_per_unit" id="pieces_per_unit"
                                   step="1" min="1" value="{{ old('pieces_per_unit') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('pieces_per_unit') border-red-500 @enderror">
                            <div class="help-text">e.g. 24 bottles per carton</div>
                            @error('pieces_per_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-end">
                            {{-- Conversion summary shown inline --}}
                            <div id="conversionSummary" class="conversion-summary w-full">
                                <strong>📊 Conversion:</strong><br>
                                <span id="conversionText"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Stock Display --}}
                <div class="total-box">
                    📊 <strong>Total Stock to be Added:</strong>
                    <span id="total_quantity_display">0.00</span>
                    <span id="total_unit_label" class="text-lg font-semibold text-yellow-800">units</span>
                </div>

                {{-- Row: Store + Vendor --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Store Location</label>
                        <select name="store_id" class="w-full px-3 py-2 border rounded-lg">
                            <option value="1" {{ old('store_id') == 1 ? 'selected' : '' }}>Main Store</option>
                            <option value="2" {{ old('store_id') == 2 ? 'selected' : '' }}>Bar Store</option>
                            <option value="3" {{ old('store_id') == 3 ? 'selected' : '' }}>Cafe Store</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Supplier/Vendor</label>
                        <select name="vendor_id" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="w-full px-3 py-2 border rounded-lg"
                              placeholder="Batch number, expiry date, etc.">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('store.inventory.index') }}"
                       class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="button" onclick="showPreview()"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Preview</button>
                </div>
            </form>
        </div>

        {{-- ==================== GRN SECTION ==================== --}}
        <div id="grnSection" style="display: none;">
            <div class="bg-green-50 p-4 rounded-lg mb-4">
                <label class="block font-medium text-gray-700 mb-2">Select Goods Received Note (GRN)</label>
                <select id="grn_select" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">-- Select GRN --</option>
                    @foreach($grns as $grn)
                        <option value="{{ $grn->id }}">
                            {{ $grn->grn_number }} - {{ $grn->vendor->name ?? 'N/A' }} - {{ $grn->received_date->format('Y-m-d') }}
                        </option>
                    @endforeach
                </select>
                <button type="button" id="loadGrnBtn"
                        class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Load GRN Items
                </button>
            </div>

            <div id="grnErrorBox" class="hidden p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg mb-4"></div>

            <div id="grnItemsContainer" style="display: none;">
                <form method="POST" action="{{ route('store.inventory.store-from-grn') }}" id="grnForm">
                    @csrf
                    <input type="hidden" name="grn_id" id="selected_grn_id">
                    <div id="grnItemsTable"></div>
                    <div class="mt-4 flex justify-end gap-3">
                        <button type="button" id="cancelGrnBtn"
                                class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Receive into Inventory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== PREVIEW MODAL ==================== --}}
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
                    <div><span class="preview-label">Item Name:</span> <span id="preview_item" class="font-semibold">—</span></div>
                    <div><span class="preview-label">Category:</span> <span id="preview_category">—</span></div>
                    <div><span class="preview-label">Item Code:</span> <span id="preview_code">—</span></div>
                    <div><span class="preview-label">Receiving Unit:</span> <span id="preview_metrics">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Base/Sell Unit:</span> <span id="preview_base_unit" class="font-semibold text-purple-700">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Receipt Detail:</span> <span id="preview_type">—</span></div>
                    <div class="col-span-2">
                        <span class="preview-label">Total Stock Added:</span>
                        <span id="preview_quantity" class="font-bold text-green-600 text-lg">0</span>
                    </div>
                    <div class="col-span-2"><span class="preview-label">Store:</span> <span id="preview_store">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Vendor:</span> <span id="preview_vendor">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Notes:</span> <span id="preview_notes">—</span></div>
                </div>
            </div>

            <div id="previewErrorBox" class="hidden mt-4 p-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm"></div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closePreview()"
                        class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Edit</button>
                <button type="button" onclick="submitForm()"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Confirm & Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ─── Logger ─────────────────────────────────────────────────────────────────
    const AppLog = {
        _send(level, context, message, data = {}) {
            console[level === 'error' ? 'error' : level === 'warn' ? 'warn' : 'log'](
                `[${level.toUpperCase()}] [${context}]`, message, data
            );
        },
        info:  (ctx, msg, data) => AppLog._send('info',  ctx, msg, data),
        warn:  (ctx, msg, data) => AppLog._send('warn',  ctx, msg, data),
        error: (ctx, msg, data) => AppLog._send('error', ctx, msg, data),
    };

    // ─── Inline error helpers ────────────────────────────────────────────────────
    function showInlineError(boxId, message) {
        const box = document.getElementById(boxId);
        if (box) { box.textContent = '⚠️ ' + message; box.classList.remove('hidden'); }
    }
    function hideInlineError(boxId) {
        const box = document.getElementById(boxId);
        if (box) box.classList.add('hidden');
    }

    // ─── Entry toggle ────────────────────────────────────────────────────────────
    const manualCard    = document.getElementById('manualCard');
    const grnCard       = document.getElementById('grnCard');
    const manualSection = document.getElementById('manualSection');
    const grnSection    = document.getElementById('grnSection');

    manualCard.addEventListener('click', function () {
        manualCard.classList.add('active', 'border-yellow-500', 'bg-yellow-50');
        grnCard.classList.remove('active', 'border-green-500', 'bg-green-50');
        manualSection.style.display = 'block';
        grnSection.style.display = 'none';
    });

    grnCard.addEventListener('click', function () {
        grnCard.classList.add('active', 'border-green-500', 'bg-green-50');
        manualCard.classList.remove('active', 'border-yellow-500', 'bg-yellow-50');
        manualSection.style.display = 'none';
        grnSection.style.display = 'block';
    });

    // ─── DOM refs ────────────────────────────────────────────────────────────────
    const metricsSelect      = document.getElementById('metrics');
    const baseUnitSelect     = document.getElementById('base_unit');
    const baseUnitWrapper    = document.getElementById('baseUnitWrapper');
    const simpleSection      = document.getElementById('simpleSection');
    const bulkSection        = document.getElementById('bulkSection');
    const totalQuantitySpan  = document.getElementById('total_quantity_display');
    const totalUnitLabel     = document.getElementById('total_unit_label');
    const simpleQuantity     = document.getElementById('simple_quantity');
    const piecesPerUnit      = document.getElementById('pieces_per_unit');
    const numberOfUnits      = document.getElementById('number_of_units');
    const conversionSummary  = document.getElementById('conversionSummary');
    const conversionText     = document.getElementById('conversionText');
    const packTypeLabel      = document.getElementById('packTypeLabel');
    const packTypeLabel2     = document.getElementById('packTypeLabel2');
    const baseUnitLabel2     = document.getElementById('baseUnitLabel2');

    const bulkMetrics   = ['box', 'carton', 'crate', 'dozen', 'pack', 'sack', 'set'];
    const simpleMetrics = ['kg', 'litres', 'pcs', 'grams', 'millilitres'];

    // ─── Metrics change handler ──────────────────────────────────────────────────
    function updateManualForm() {
        try {
            const sel = metricsSelect.value;

            if (!sel) {
                simpleSection.classList.remove('show');
                bulkSection.classList.remove('show');
                baseUnitWrapper.style.display = 'none';
                totalUnitLabel.innerText = 'units';
                calculateManualTotal();
                return;
            }

            // Always show the base unit selector
            baseUnitWrapper.style.display = 'block';

            if (bulkMetrics.includes(sel)) {
                // Bulk: show pack entry fields
                simpleSection.classList.remove('show');
                bulkSection.classList.add('show');

                // Update dynamic labels inside bulk section
                packTypeLabel.innerText  = sel;
                packTypeLabel2.innerText = sel;

                // Base unit label in the question
                const bu = baseUnitSelect.value || 'items';
                baseUnitLabel2.innerText = bu;

                AppLog.info('ManualForm', 'Switched to bulk mode', { metrics: sel });

            } else if (simpleMetrics.includes(sel)) {
                // Simple: no pack conversion needed
                simpleSection.classList.add('show');
                bulkSection.classList.remove('show');
                conversionSummary.classList.remove('show');

                // For direct units, pre-select base_unit to match metrics
                const directMap = {
                    'kg': 'kg', 'grams': 'gram', 'litres': 'litre',
                    'millilitres': 'ml', 'pcs': 'piece'
                };
                if (directMap[sel]) {
                    baseUnitSelect.value = directMap[sel];
                }
                totalUnitLabel.innerText = sel;
                AppLog.info('ManualForm', 'Switched to simple mode', { metrics: sel });
            }

            calculateManualTotal();
        } catch (e) {
            AppLog.error('ManualForm', 'updateManualForm failed', { error: e.message });
        }
    }

    // ─── Base unit change ────────────────────────────────────────────────────────
    baseUnitSelect.addEventListener('change', function () {
        const bu = this.value || 'items';
        baseUnitLabel2.innerText = bu;
        totalUnitLabel.innerText = bu;
        calculateManualTotal();
    });

    // ─── Total calculation ───────────────────────────────────────────────────────
    function calculateManualTotal() {
        try {
            const sel = metricsSelect.value;
            const bu  = baseUnitSelect.value || 'units';
            let total = 0;

            if (bulkMetrics.includes(sel)) {
                const pieces = parseFloat(piecesPerUnit.value) || 0;
                const units  = parseFloat(numberOfUnits.value) || 0;
                total = pieces * units;

                // Show conversion summary
                if (pieces > 0 && units > 0) {
                    conversionSummary.classList.add('show');
                    conversionText.innerHTML =
                        `${units} ${sel}(s) &times; ${pieces} ${bu} each = <strong>${total} ${bu}s</strong> added to stock`;
                } else {
                    conversionSummary.classList.remove('show');
                }

                totalUnitLabel.innerText = bu + 's';
                AppLog.info('ManualForm', 'Bulk total', { pieces, units, total, bu });

            } else {
                total = parseFloat(simpleQuantity.value) || 0;
                totalUnitLabel.innerText = sel || bu;
                conversionSummary.classList.remove('show');
                AppLog.info('ManualForm', 'Simple total', { total });
            }

            totalQuantitySpan.innerText = total.toLocaleString(undefined, {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });
            return total;
        } catch (e) {
            AppLog.error('ManualForm', 'calculateManualTotal failed', { error: e.message });
            return 0;
        }
    }

    metricsSelect.addEventListener('change', updateManualForm);
    simpleQuantity.addEventListener('input', calculateManualTotal);
    piecesPerUnit.addEventListener('input', calculateManualTotal);
    numberOfUnits.addEventListener('input', calculateManualTotal);

    // ─── Preview ─────────────────────────────────────────────────────────────────
function showPreview() {
    hideInlineError('previewErrorBox');
    try {
        const itemName = document.getElementById('item_name').value.trim();
        if (!itemName) {
            alert('Please fill in the item name before previewing.');
            return; // do NOT open modal empty
        }

        const metrics = metricsSelect.value;
        if (!metrics) {
            alert('Please select a receiving unit.');
            return;
        }

        const baseUnit = baseUnitSelect.value;
        if (!baseUnit) {
            alert('Please select the base/selling unit.');
            return;
        }

        const totalQuantity = calculateManualTotal();
        if (totalQuantity <= 0) {
            alert('Total quantity must be greater than zero.');
            return;
        }

        const categorySelect = document.getElementById('category_id');
        const categoryName   = categorySelect.options[categorySelect.selectedIndex]?.text || '—';
        const itemCode       = document.getElementById('item_code').value || 'Auto-generated';
        const storeSelect    = document.querySelector('select[name="store_id"]');
        const storeName      = storeSelect.options[storeSelect.selectedIndex]?.text || '—';
        const vendorSelect   = document.querySelector('select[name="vendor_id"]');
        const vendorName     = vendorSelect.options[vendorSelect.selectedIndex]?.text || 'None';
        const notes          = document.getElementById('notes').value || '—';

        let receiptDetail = '';
        if (bulkMetrics.includes(metrics)) {
            const pieces = piecesPerUnit.value || 0;
            const units  = numberOfUnits.value || 0;
            receiptDetail = units + ' ' + metrics + '(s) × ' + pieces + ' ' + baseUnit + '(s) per ' + metrics + ' = ' + totalQuantity + ' ' + baseUnit + 's';
        } else {
            receiptDetail = totalQuantity + ' ' + metrics + ' (direct receipt)';
        }

        document.getElementById('preview_item').innerText      = itemName;
        document.getElementById('preview_category').innerText  = categoryName;
        document.getElementById('preview_code').innerText      = itemCode;
        document.getElementById('preview_metrics').innerText   = metrics;
        document.getElementById('preview_base_unit').innerText = baseUnit;
        document.getElementById('preview_type').innerText      = receiptDetail;
        document.getElementById('preview_quantity').innerText  = totalQuantity.toLocaleString() + ' ' + baseUnit + '(s)';
        document.getElementById('preview_store').innerText     = storeName;
        document.getElementById('preview_vendor').innerText    = vendorName;
        document.getElementById('preview_notes').innerText     = notes;

        // Only open modal AFTER all fields are populated
        document.getElementById('previewModal').style.display = 'flex';

    } catch (e) {
        AppLog.error('Preview', 'showPreview failed', { error: e.message, stack: e.stack });
        alert('Unexpected error: ' + e.message);
    }
}

    function closePreview() {
        document.getElementById('previewModal').style.display = 'none';
        hideInlineError('previewErrorBox');
    }

    function submitForm() {
        AppLog.info('ManualForm', 'Submitted via preview confirmation');
        document.getElementById('manualForm').submit();
    }

    // ─── GRN Load ────────────────────────────────────────────────────────────────
    document.getElementById('loadGrnBtn')?.addEventListener('click', function () {
        hideInlineError('grnErrorBox');
        const grnId = document.getElementById('grn_select').value;
        if (!grnId) {
            showInlineError('grnErrorBox', 'Please select a GRN before loading.');
            return;
        }

        const btn = this;
        btn.innerText = 'Loading...';
        btn.disabled = true;

        fetch(`/store/inventory/get-grn-items/${grnId}`)
            .then(r => {
                if (!r.ok) throw new Error(`Server returned ${r.status}`);
                return r.json();
            })
            .then(data => {
                if (data.success && data.items.length > 0) {
                    displayGrnItems(data.items, grnId);
                    document.getElementById('grnItemsContainer').style.display = 'block';
                } else {
                    showInlineError('grnErrorBox', data.message || 'No items found in this GRN.');
                }
            })
            .catch(error => {
                showInlineError('grnErrorBox', 'Error loading GRN items: ' + error.message);
            })
            .finally(() => {
                btn.innerText = 'Load GRN Items';
                btn.disabled = false;
            });
    });

    function displayGrnItems(items, grnId) {
        try {
            document.getElementById('selected_grn_id').value = grnId;

            let html = `<table class="grn-table">
                <thead><tr>
                    <th>Item</th>
                    <th>Receiving Unit</th>
                    <th class="text-center">Qty Accepted</th>
                    <th class="text-center">Base/Sell Unit</th>
                    <th class="text-center">Items per Pack</th>
                    <th class="text-center">Total Base Units</th>
                    <th class="text-center">Unit Cost</th>
                    <th class="text-center">Qty to Receive</th>
                </tr></thead><tbody>`;

            items.forEach((item, idx) => {
                const qtyAccepted = parseFloat(item.quantity_accepted).toFixed(2);
                const unitCost    = parseFloat(item.unit_cost).toFixed(2);
                const isBulk      = ['box','carton','crate','dozen','pack','sack','set'].includes(item.metrics);

                html += `<tr>
                    <td>
                        ${item.item_name}
                        <input type="hidden" name="items[${idx}][grn_item_id]" value="${item.id}">
                        <input type="hidden" name="items[${idx}][inventory_item_id]" value="${item.inventory_item_id}">
                    </td>
                    <td>${item.metrics}</td>
                    <td class="text-center">${qtyAccepted}</td>
                    <td class="text-center">
                        <select name="items[${idx}][base_unit]"
                                class="w-28 px-2 py-1 border rounded text-sm grn-base-unit"
                                data-idx="${idx}"
                                onchange="updateGrnRowTotal(${idx})">
                            <option value="bottle">Bottle</option>
                            <option value="can">Can</option>
                            <option value="piece">Piece</option>
                            <option value="kg">Kg</option>
                            <option value="litre">Litre</option>
                            <option value="ml">ml</option>
                            <option value="glass">Glass</option>
                            <option value="egg">Egg</option>
                            <option value="unit">Unit</option>
                        </select>
                        <input type="hidden" name="items[${idx}][unit_cost]" value="${item.unit_cost}">
                    </td>
                    <td class="text-center">
                        ${isBulk ? `<input type="number" name="items[${idx}][pack_size]"
                                           id="grn_pack_size_${idx}"
                                           class="w-20 px-2 py-1 border rounded text-center"
                                           min="1" step="1" placeholder="e.g. 24"
                                           oninput="updateGrnRowTotal(${idx})">` : `<span class="text-gray-400">—</span>
                                           <input type="hidden" name="items[${idx}][pack_size]" value="1">`}
                    </td>
                    <td class="text-center font-semibold text-green-700" id="grn_total_base_${idx}">
                        ${isBulk ? '—' : qtyAccepted}
                    </td>
                    <td class="text-center">UGX ${unitCost}</td>
                    <td class="text-center">
                        <input type="number" name="items[${idx}][quantity]"
                               id="grn_qty_${idx}"
                               value="${item.quantity_accepted}"
                               class="w-24 px-2 py-1 border rounded text-center"
                               min="0" max="${item.quantity_accepted}" step="0.01">
                    </td>
                </tr>`;
            });

            html += `</tbody></table>
                <p class="text-xs text-gray-500 mt-2">
                    * For pack items (carton, crate etc.) enter how many individual units are in each pack.
                    Total base units will be calculated automatically.
                </p>`;

            document.getElementById('grnItemsTable').innerHTML = html;
            AppLog.info('GRN', 'Table rendered', { grnId, count: items.length });
        } catch (e) {
            AppLog.error('GRN', 'displayGrnItems failed', { error: e.message });
            showInlineError('grnErrorBox', 'Failed to display GRN items. Please reload.');
        }
    }

    // Update total base units for a GRN row when pack_size changes
    function updateGrnRowTotal(idx) {
        const packSizeEl = document.getElementById(`grn_pack_size_${idx}`);
        const totalEl    = document.getElementById(`grn_total_base_${idx}`);
        const qtyEl      = document.getElementById(`grn_qty_${idx}`);

        if (!packSizeEl || !totalEl) return;

        const packSize = parseFloat(packSizeEl.value) || 0;
        const qty      = parseFloat(qtyEl?.value) || 0;
        const total    = packSize > 0 ? (qty * packSize) : 0;

        totalEl.innerText = total > 0 ? total.toFixed(0) : '—';
    }

    document.getElementById('cancelGrnBtn')?.addEventListener('click', function () {
        document.getElementById('grnItemsContainer').style.display = 'none';
        document.getElementById('grn_select').value = '';
        hideInlineError('grnErrorBox');
    });

    document.getElementById('grnForm')?.addEventListener('submit', function (e) {
        const inputs = this.querySelectorAll('input[name*="[quantity]"]');
        let valid = true;
        inputs.forEach(input => {
            if (!input.value || parseFloat(input.value) <= 0) valid = false;
        });
        if (!valid) {
            e.preventDefault();
            showInlineError('grnErrorBox', 'All quantities must be greater than zero.');
        }
    });

    // ─── Global error handlers ───────────────────────────────────────────────────
    window.addEventListener('error', function (event) {
        AppLog.error('Global', 'Uncaught JS error', { message: event.message });
    });
    window.addEventListener('unhandledrejection', function (event) {
        AppLog.error('Global', 'Unhandled rejection', { reason: String(event.reason) });
    });

    // ─── Init: default to manual mode ───────────────────────────────────────────
    manualCard.classList.add('active', 'border-yellow-500', 'bg-yellow-50');
    manualSection.style.display = 'block';

    // Restore state after validation failure
    const oldMetrics = '{{ old("metrics") }}';
    if (oldMetrics) {
        metricsSelect.value = oldMetrics;
        updateManualForm();
    }
    const oldBaseUnit = '{{ old("base_unit") }}';
    if (oldBaseUnit) {
        baseUnitSelect.value = oldBaseUnit;
    }

    AppLog.info('Init', 'Inventory create page initialised');
</script>
@endsection
