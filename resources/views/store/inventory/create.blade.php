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
        width: 140px;
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" name="item_name" id="item_name" class="w-full px-3 py-2 border rounded-lg @error('item_name') border-red-500 @enderror" value="{{ old('item_name') }}" required>
                        @error('item_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" class="w-full px-3 py-2 border rounded-lg @error('category_id') border-red-500 @enderror" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Item Code</label>
                        <input type="text" name="item_code" id="item_code" class="w-full px-3 py-2 border rounded-lg @error('item_code') border-red-500 @enderror" placeholder="Auto-generated if empty" value="{{ old('item_code') }}">
                        @error('item_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Unit of Measurement (Metrics) <span class="text-red-500">*</span></label>
                        <select name="metrics" id="metrics" class="w-full px-3 py-2 border rounded-lg @error('metrics') border-red-500 @enderror" required>
                            <option value="">-- Choose Metrics --</option>
                            <option value="kg"          {{ old('metrics') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                            <option value="litres"      {{ old('metrics') == 'litres' ? 'selected' : '' }}>Litres (L)</option>
                            <option value="pcs"         {{ old('metrics') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                            <option value="grams"       {{ old('metrics') == 'grams' ? 'selected' : '' }}>Grams (g)</option>
                            <option value="millilitres" {{ old('metrics') == 'millilitres' ? 'selected' : '' }}>Millilitres (ml)</option>
                            <option value="box"         {{ old('metrics') == 'box' ? 'selected' : '' }}>Box (contains multiple pieces)</option>
                            <option value="carton"      {{ old('metrics') == 'carton' ? 'selected' : '' }}>Carton (contains multiple pieces)</option>
                            <option value="crate"       {{ old('metrics') == 'crate' ? 'selected' : '' }}>Crate (contains multiple pieces)</option>
                            <option value="dozen"       {{ old('metrics') == 'dozen' ? 'selected' : '' }}>Dozen (12 pieces)</option>
                            <option value="pack"        {{ old('metrics') == 'pack' ? 'selected' : '' }}>Pack (contains multiple pieces)</option>
                        </select>
                        @error('metrics')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Simple Section --}}
                <div id="simpleSection" class="simple-section">
                    <label class="font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="simple_quantity" step="0.01" min="0" value="{{ old('quantity') }}" class="w-full px-3 py-2 border rounded-lg @error('quantity') border-red-500 @enderror">
                    @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Bulk Section --}}
                <div id="bulkSection" class="bulk-section">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-700 mb-1">Pieces per Unit <span class="text-red-500">*</span></label>
                            <input type="number" name="pieces_per_unit" id="pieces_per_unit" step="1" min="1" value="{{ old('pieces_per_unit') }}" class="w-full px-3 py-2 border rounded-lg @error('pieces_per_unit') border-red-500 @enderror">
                            <div class="help-text">How many pieces in one box/carton?</div>
                            @error('pieces_per_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 mb-1">Number of Units <span class="text-red-500">*</span></label>
                            <input type="number" name="number_of_units" id="number_of_units" step="1" min="1" value="{{ old('number_of_units') }}" class="w-full px-3 py-2 border rounded-lg">
                            <div class="help-text">How many boxes/cartons received?</div>
                        </div>
                    </div>
                </div>

                {{-- Total Display --}}
                <div class="total-box">
                    📊 <strong>Total Stock:</strong> <span id="total_quantity_display">0.00</span> <span id="total_unit_label">units</span>
                </div>

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
                                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full px-3 py-2 border rounded-lg" placeholder="Batch number, expiry date, etc.">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('store.inventory.index') }}" class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="button" onclick="showPreview()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Preview</button>
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
                <button type="button" id="loadGrnBtn" class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Load GRN Items</button>
            </div>

            {{-- GRN inline error display --}}
            <div id="grnErrorBox" class="hidden p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg mb-4"></div>

            <div id="grnItemsContainer" style="display: none;">
                <form method="POST" action="{{ route('store.inventory.store-from-grn') }}" id="grnForm">
                    @csrf
                    <input type="hidden" name="grn_id" id="selected_grn_id">
                    <div id="grnItemsTable"></div>
                    <div class="mt-4 flex justify-end gap-3">
                        <button type="button" id="cancelGrnBtn" class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Receive into Inventory</button>
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
                    <div><span class="preview-label">Metrics:</span> <span id="preview_metrics">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Receipt Type:</span> <span id="preview_type">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Total Quantity:</span> <span id="preview_quantity" class="font-bold text-green-600">0</span></div>
                    <div class="col-span-2"><span class="preview-label">Store:</span> <span id="preview_store">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Vendor:</span> <span id="preview_vendor">—</span></div>
                    <div class="col-span-2"><span class="preview-label">Notes:</span> <span id="preview_notes">—</span></div>
                </div>
            </div>

            {{-- Modal validation error --}}
            <div id="previewErrorBox" class="hidden mt-4 p-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm"></div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closePreview()" class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Edit</button>
                <button type="button" onclick="submitForm()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Confirm & Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ─── Utility: centralized frontend logger ───────────────────────────────────
    const AppLog = {
        _send(level, context, message, data = {}) {
            const entry = {
                level,
                context,
                message,
                data,
                timestamp: new Date().toISOString(),
                url: window.location.href,
            };
            console[level === 'error' ? 'error' : level === 'warn' ? 'warn' : 'log'](
                `[${level.toUpperCase()}] [${context}]`, message, data
            );
            // Uncomment to send logs to your backend endpoint:
            // fetch('/api/client-log', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            //     body: JSON.stringify(entry)
            // }).catch(() => {});
        },
        info:  (ctx, msg, data) => AppLog._send('info',  ctx, msg, data),
        warn:  (ctx, msg, data) => AppLog._send('warn',  ctx, msg, data),
        error: (ctx, msg, data) => AppLog._send('error', ctx, msg, data),
    };

    // ─── Helper: show inline error boxes ────────────────────────────────────────
    function showInlineError(boxId, message) {
        const box = document.getElementById(boxId);
        if (box) {
            box.textContent = '⚠️ ' + message;
            box.classList.remove('hidden');
        }
    }
    function hideInlineError(boxId) {
        const box = document.getElementById(boxId);
        if (box) box.classList.add('hidden');
    }

    // ─── Toggle between Manual and GRN ──────────────────────────────────────────
    const manualCard   = document.getElementById('manualCard');
    const grnCard      = document.getElementById('grnCard');
    const manualSection = document.getElementById('manualSection');
    const grnSection   = document.getElementById('grnSection');

    manualCard.addEventListener('click', function () {
        try {
            manualCard.classList.add('active', 'border-yellow-500', 'bg-yellow-50');
            grnCard.classList.remove('active', 'border-green-500', 'bg-green-50');
            manualSection.style.display = 'block';
            grnSection.style.display = 'none';
            AppLog.info('EntryToggle', 'Switched to manual entry mode');
        } catch (e) {
            AppLog.error('EntryToggle', 'Failed to switch to manual mode', { error: e.message });
        }
    });

    grnCard.addEventListener('click', function () {
        try {
            grnCard.classList.add('active', 'border-green-500', 'bg-green-50');
            manualCard.classList.remove('active', 'border-yellow-500', 'bg-yellow-50');
            manualSection.style.display = 'none';
            grnSection.style.display = 'block';
            AppLog.info('EntryToggle', 'Switched to GRN entry mode');
        } catch (e) {
            AppLog.error('EntryToggle', 'Failed to switch to GRN mode', { error: e.message });
        }
    });

    // ─── Manual form dynamic calculations ───────────────────────────────────────
    const metricsSelect        = document.getElementById('metrics');
    const simpleSection        = document.getElementById('simpleSection');
    const bulkSection          = document.getElementById('bulkSection');
    const totalQuantitySpan    = document.getElementById('total_quantity_display');
    const totalUnitLabel       = document.getElementById('total_unit_label');
    const simpleQuantity       = document.getElementById('simple_quantity');
    const piecesPerUnit        = document.getElementById('pieces_per_unit');
    const numberOfUnits        = document.getElementById('number_of_units');

    const bulkMetrics   = ['box', 'carton', 'crate', 'dozen', 'pack'];
    const singleMetrics = ['kg', 'litres', 'pcs', 'grams', 'millilitres'];

    function updateManualForm() {
        try {
            const sel = metricsSelect.value;
            if (bulkMetrics.includes(sel)) {
                simpleSection.classList.remove('show');
                bulkSection.classList.add('show');
                totalUnitLabel.innerText = 'pieces';
            } else if (singleMetrics.includes(sel)) {
                simpleSection.classList.add('show');
                bulkSection.classList.remove('show');
                totalUnitLabel.innerText = sel;
            } else {
                simpleSection.classList.remove('show');
                bulkSection.classList.remove('show');
            }
            AppLog.info('ManualForm', 'Metrics changed', { selected: sel });
            calculateManualTotal();
        } catch (e) {
            AppLog.error('ManualForm', 'updateManualForm failed', { error: e.message });
        }
    }

    function calculateManualTotal() {
        try {
            const sel = metricsSelect.value;
            let total = 0;
            if (bulkMetrics.includes(sel)) {
                const pieces = parseFloat(piecesPerUnit.value) || 0;
                const units  = parseFloat(numberOfUnits.value) || 0;
                total = pieces * units;
                AppLog.info('ManualForm', 'Bulk total calculated', { pieces, units, total });
            } else {
                total = parseFloat(simpleQuantity.value) || 0;
                AppLog.info('ManualForm', 'Simple total calculated', { total });
            }
            totalQuantitySpan.innerText = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

    // ─── Preview function ────────────────────────────────────────────────────────
    function showPreview() {
        hideInlineError('previewErrorBox');
        try {
            const itemName = document.getElementById('item_name').value.trim();
            if (!itemName) {
                AppLog.warn('Preview', 'Blocked: item name missing');
                showInlineError('previewErrorBox', 'Please fill in the item name before previewing.');
                document.getElementById('previewModal').style.display = 'flex';
                return;
            }

            const metrics = metricsSelect.value;
            if (!metrics) {
                AppLog.warn('Preview', 'Blocked: metrics not selected');
                alert('Please select a unit of measurement.');
                return;
            }

            const totalQuantity = calculateManualTotal();
            if (totalQuantity <= 0) {
                AppLog.warn('Preview', 'Blocked: zero or negative quantity', { totalQuantity });
                alert('Total quantity must be greater than zero.');
                return;
            }

            const categorySelect = document.getElementById('category_id');
            const categoryName   = categorySelect.options[categorySelect.selectedIndex]?.text || '—';
            const itemCode       = document.getElementById('item_code').value || 'Auto-generated';
            const metricsText    = metricsSelect.options[metricsSelect.selectedIndex]?.text.split(' -')[0] || '—';
            const storeSelect    = document.querySelector('select[name="store_id"]');
            const storeName      = storeSelect.options[storeSelect.selectedIndex]?.text || '—';
            const vendorSelect   = document.querySelector('select[name="vendor_id"]');
            const vendorName     = vendorSelect.options[vendorSelect.selectedIndex]?.text || '—';
            const notes          = document.getElementById('notes').value || '—';

            let receiptType = '';
            if (bulkMetrics.includes(metrics)) {
                const pieces = piecesPerUnit.value || 0;
                const units  = numberOfUnits.value || 0;
                receiptType = `📦 Bulk: ${units} ${metrics}(s) × ${pieces} pieces = ${totalQuantity} pieces`;
            } else {
                const qty = simpleQuantity.value || 0;
                receiptType = `📏 Direct: ${qty} ${metricsText}`;
            }

            document.getElementById('preview_item').innerText     = itemName;
            document.getElementById('preview_category').innerText = categoryName;
            document.getElementById('preview_code').innerText     = itemCode;
            document.getElementById('preview_metrics').innerText  = metricsText;
            document.getElementById('preview_type').innerHTML     = receiptType;
            document.getElementById('preview_quantity').innerHTML = `${totalQuantity.toLocaleString()} ${totalUnitLabel.innerText}`;
            document.getElementById('preview_store').innerText    = storeName;
            document.getElementById('preview_vendor').innerText   = vendorName;
            document.getElementById('preview_notes').innerText    = notes;

            document.getElementById('previewModal').style.display = 'flex';

            AppLog.info('Preview', 'Preview shown', {
                itemName, category: categoryName, metrics, totalQuantity, store: storeName
            });
        } catch (e) {
            AppLog.error('Preview', 'showPreview failed', { error: e.message, stack: e.stack });
            alert('An unexpected error occurred while loading the preview. Please try again.');
        }
    }

    function closePreview() {
        try {
            document.getElementById('previewModal').style.display = 'none';
            hideInlineError('previewErrorBox');
            AppLog.info('Preview', 'Preview closed');
        } catch (e) {
            AppLog.error('Preview', 'closePreview failed', { error: e.message });
        }
    }

    function submitForm() {
        try {
            AppLog.info('ManualForm', 'Form submitted via preview confirmation');
            document.getElementById('manualForm').submit();
        } catch (e) {
            AppLog.error('ManualForm', 'submitForm failed', { error: e.message });
            alert('Failed to submit the form. Please try again.');
        }
    }

    // ─── GRN Load functionality ──────────────────────────────────────────────────
    document.getElementById('loadGrnBtn')?.addEventListener('click', function () {
        hideInlineError('grnErrorBox');
        const grnId = document.getElementById('grn_select').value;

        if (!grnId) {
            AppLog.warn('GRN', 'Load blocked: no GRN selected');
            showInlineError('grnErrorBox', 'Please select a GRN before loading.');
            return;
        }

        const btn = this;
        btn.innerText = 'Loading...';
        btn.disabled = true;

        AppLog.info('GRN', 'Fetching GRN items', { grnId });

        fetch(`/store/inventory/get-grn-items/${grnId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.items.length > 0) {
                    AppLog.info('GRN', 'GRN items loaded', { grnId, count: data.items.length });
                    displayGrnItems(data.items, grnId);
                    document.getElementById('grnItemsContainer').style.display = 'block';
                } else {
                    const msg = data.message || 'No items found in this GRN.';
                    AppLog.warn('GRN', 'No items returned', { grnId, response: data });
                    showInlineError('grnErrorBox', msg);
                }
            })
            .catch(error => {
                AppLog.error('GRN', 'Failed to fetch GRN items', { grnId, error: error.message });
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
                    <th class="text-center">Quantity Accepted</th>
                    <th class="text-center">Unit Cost</th>
                    <th class="text-center">Metrics</th>
                    <th class="text-center">Quantity to Receive</th>
                </tr></thead><tbody>`;

            items.forEach((item, idx) => {
                const qtyAccepted = parseFloat(item.quantity_accepted).toFixed(2);
                const unitCost    = parseFloat(item.unit_cost).toFixed(2);
                html += `<tr>
                    <td>${item.item_name}
                        <input type="hidden" name="items[${idx}][grn_item_id]" value="${item.id}">
                        <input type="hidden" name="items[${idx}][inventory_item_id]" value="${item.inventory_item_id}">
                    </td>
                    <td class="text-center">${qtyAccepted}</td>
                    <td class="text-center">UGX ${unitCost}</td>
                    <td class="text-center">${item.metrics}</td>
                    <td class="text-center">
                        <input type="number" name="items[${idx}][quantity]" value="${item.quantity_accepted}"
                               class="w-24 px-2 py-1 border rounded text-center"
                               min="0" max="${item.quantity_accepted}" step="0.01"
                               oninput="AppLog.info('GRN', 'Quantity changed', { itemId: '${item.inventory_item_id}', value: this.value })">
                        <input type="hidden" name="items[${idx}][unit_cost]" value="${item.unit_cost}">
                    </td>
                </tr>`;
            });

            html += `</tbody></table>`;
            document.getElementById('grnItemsTable').innerHTML = html;
            AppLog.info('GRN', 'GRN items table rendered', { grnId, count: items.length });
        } catch (e) {
            AppLog.error('GRN', 'displayGrnItems failed', { error: e.message, stack: e.stack });
            showInlineError('grnErrorBox', 'Failed to display GRN items. Please reload and try again.');
        }
    }

    document.getElementById('cancelGrnBtn')?.addEventListener('click', function () {
        try {
            document.getElementById('grnItemsContainer').style.display = 'none';
            document.getElementById('grn_select').value = '';
            hideInlineError('grnErrorBox');
            AppLog.info('GRN', 'GRN selection cancelled');
        } catch (e) {
            AppLog.error('GRN', 'cancelGrnBtn handler failed', { error: e.message });
        }
    });

    // ─── GRN form submission guard ───────────────────────────────────────────────
    document.getElementById('grnForm')?.addEventListener('submit', function (e) {
        try {
            const inputs = this.querySelectorAll('input[name*="[quantity]"]');
            let valid = true;
            inputs.forEach(input => {
                if (!input.value || parseFloat(input.value) <= 0) {
                    valid = false;
                }
            });
            if (!valid) {
                e.preventDefault();
                AppLog.warn('GRN', 'Form submit blocked: invalid quantities');
                showInlineError('grnErrorBox', 'All quantities must be greater than zero.');
                return;
            }
            AppLog.info('GRN', 'GRN form submitted');
        } catch (err) {
            AppLog.error('GRN', 'GRN form submit handler failed', { error: err.message });
        }
    });

    // ─── Global JS error catcher ─────────────────────────────────────────────────
    window.addEventListener('error', function (event) {
        AppLog.error('Global', 'Uncaught JS error', {
            message: event.message,
            source: event.filename,
            line: event.lineno,
            col: event.colno,
        });
    });

    window.addEventListener('unhandledrejection', function (event) {
        AppLog.error('Global', 'Unhandled promise rejection', {
            reason: event.reason?.message || String(event.reason),
        });
    });

    // ─── Default: Manual mode active ────────────────────────────────────────────
    manualCard.classList.add('active', 'border-yellow-500', 'bg-yellow-50');
    manualSection.style.display = 'block';
    AppLog.info('Init', 'Inventory create page initialised');

    // Restore bulk section state if old input exists (after validation failure redirect)
    const oldMetrics = '{{ old("metrics") }}';
    if (oldMetrics) {
        metricsSelect.value = oldMetrics;
        updateManualForm();
    }
</script>
@endsection
