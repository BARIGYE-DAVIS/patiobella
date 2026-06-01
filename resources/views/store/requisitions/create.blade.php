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
        padding: 10px 8px;
        border: 1px solid #e5e7eb;
        vertical-align: top;
    }
    .items-table th {
        background-color: #f9fafb;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        color: #6b7280;
        text-align: left;
    }
    .item-row:hover {
        background-color: #f9fafb;
    }
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 10px;
        font-size: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
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
    .tab-button {
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .tab-button.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        background-color: #eff6ff;
    }
    .tab-button:not(.active):hover {
        color: #6b7280;
        border-bottom-color: #d1d5db;
    }
    .low-stock-item {
        cursor: pointer;
        transition: all 0.2s;
    }
    .low-stock-item:hover {
        background-color: #f3f4f6;
        transform: translateX(2px);
    }
    .batch-info, .total-stock {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }
    .batch-expiring-soon { background: #fef3c7; color: #d97706; }
    .batch-expired { background: #fee2e2; color: #dc2626; }
    .batch-ok { background: #dcfce7; color: #16a34a; }
    .batch-low { background: #fee2e2; color: #dc2626; }
    .total-stock {
        background: #e0e7ff;
        color: #3730a3;
        font-weight: bold;
    }
    .requisition-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
    }
    .type-normal    { background: #d1fae5; color: #065f46; }
    .type-emergency { background: #fee2e2; color: #991b1b; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .selected-item-name {
        font-size: 11px;
        font-weight: 600;
        color: #1f2937;
        background: #f3f4f6;
        padding: 4px 6px;
        border-radius: 4px;
        margin-top: 6px;
    }
    .quick-add-btn {
        cursor: pointer;
        transition: all 0.2s;
    }
    .quick-add-btn:hover {
        background-color: #dcfce7;
    }

    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px;
        }
        .no-print { display: none !important; }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
        <h3 class="text-md font-semibold text-gray-800">Create New Requisition</h3>
        <p class="text-xs text-gray-500">Select batches that need replenishment</p>
    </div>

    <form method="POST" action="{{ route('store.requisitions.store') }}" id="requisitionForm">
        @csrf

        <div class="p-5 space-y-5">

            <div id="draftBanner">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                </svg>
                <span>Draft restored from your last session.</span>
                <button type="button" id="clearDraftBtn" class="text-amber-600 underline text-xs">Clear draft</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Requisition Type <span class="text-red-500">*</span></label>
                    <select name="requisition_type" id="requisition_type" required class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm">
                        <option value="normal" {{ old('requisition_type') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="emergency" {{ old('requisition_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    <p class="text-[10px] text-gray-500 mt-0.5">Emergency requisitions will be prioritized</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date Needed <span class="text-red-500">*</span></label>
                    <input type="date" name="date_needed" id="date_needed" min="{{ date('Y-m-d') }}" value="{{ old('date_needed') }}" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Overall Notes</label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Any additional information..." class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- TABS SECTION -->
            <div class="border-t border-gray-200 pt-4">
                <div class="border-b border-gray-200">
                    <nav class="flex gap-1">
                        <button type="button" id="tabLowStock" class="tab-button active px-4 py-2 text-sm font-medium">
                            📉 Low Stock Items
                        </button>
                        <button type="button" id="tabManualSelect" class="tab-button px-4 py-2 text-sm font-medium">
                            🔍 All items Selection
                        </button>
                    </nav>
                </div>

                <!-- Low Stock Tab Content -->
                <div id="lowStockPanel" class="mt-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
                        <div class="flex items-center gap-2 text-sm text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                            </svg>
                            <span>Click on any low stock item below to add it to your requisition list.</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-96 overflow-y-auto border rounded-md">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Item Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Category</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Batch #</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Expiry Date</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Current Stock</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Unit Cost</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Action</th>
                                </tr>
                            </thead>
                            <tbody id="lowStockItemsBody" class="divide-y divide-gray-100">
                                <!-- Low stock items will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Manual Selection Tab Content -->
                <div id="manualSelectPanel" class="mt-4 hidden">
                    <div class="mb-4 flex justify-between items-center">
                        <p class="text-xs text-gray-500">Search and select specific batches to request</p>
                        <button type="button" id="addItemBtn" class="bg-green-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-green-700 transition flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Batch
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width:10%">Item / Batch</th>
                                    <th style="width:10%">Category</th>
                                    <th style="width:20%">Batch Details</th>
                                    <th style="width:15%">Available Stock</th>
                                    <th style="width:10%">Quantity</th>
                                    <th style="width:10%">Metrics</th>
                                    <th style="width:15%">Notes</th>
                                    <th style="width:5%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            @error('items')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex justify-end gap-2">
            <a href="{{ route('store.requisitions.index') }}" class="px-4 py-1.5 border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-50 transition">Cancel</a>
            <button type="button" id="previewBtn" class="px-4 py-1.5 bg-purple-600 text-white rounded-md text-xs hover:bg-purple-700 transition">Preview Requisition</button>
        </div>
    </form>
</div>

<!-- PREVIEW MODAL (same as before) -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-4 border w-full max-w-5xl shadow-lg rounded-lg bg-white">
        <div id="print-section">
            <div class="flex justify-between items-start mb-4 pb-3 border-b">
                <div>
                    @php $logo = \App\Models\BusinessSetting::getLogo(); @endphp
                    @if($logo)
                        @php
                            $logoPath = public_path(parse_url($logo, PHP_URL_PATH));
                            $logoExists = file_exists($logoPath);
                            $logoMime = $logoExists ? mime_content_type($logoPath) : 'image/png';
                            $logoB64 = $logoExists ? base64_encode(file_get_contents($logoPath)) : null;
                        @endphp
                        @if($logoB64)
                            <img src="data:{{ $logoMime }};base64,{{ $logoB64 }}" alt="Logo" class="h-12 w-auto">
                        @else
                            <img src="{{ $logo }}" alt="Logo" class="h-12 w-auto">
                        @endif
                    @else
                        <h2 class="text-xl font-bold text-gray-800">{{ \App\Models\BusinessSetting::get('company_name', 'Company Name') }}</h2>
                    @endif
                </div>
                <div class="text-right">
                    <h3 class="text-md font-semibold text-gray-800">REQUISITION FORM</h3>
                    <p class="text-xs text-gray-500" id="previewRequisitionNumber">REQ-XXXX</p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-3 no-print">
                <h3 class="text-md font-semibold text-gray-800">Preview</h3>
                <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-3">
                <div class="bg-gray-50 p-3 rounded-md">
                    <div class="grid grid-cols-3 gap-3">
                        <div><p class="text-[10px] text-gray-500">Requisition Type</p><p class="font-medium text-xs" id="previewType">—</p></div>
                        <div><p class="text-[10px] text-gray-500">Date Needed</p><p class="font-medium text-xs" id="previewDateNeeded">—</p></div>
                        <div><p class="text-[10px] text-gray-500">Requested By</p><p class="font-medium text-xs">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p></div>
                    </div>
                    <div class="mt-2"><p class="text-[10px] text-gray-500">Overall Notes</p><p class="text-xs" id="previewNotes">—</p></div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-700 text-xs mb-2">Items Requested</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full border text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 border text-left">Item</th>
                                    <th class="px-2 py-1 border text-left">Category</th>
                                    <th class="px-2 py-1 border text-left">Batch No.</th>
                                    <th class="px-2 py-1 border text-left">Expiry</th>
                                    <th class="px-2 py-1 border text-right">Stock</th>
                                    <th class="px-2 py-1 border text-right">Unit Cost</th>
                                    <th class="px-2 py-1 border text-right">Qty</th>
                                    <th class="px-2 py-1 border text-left">Metrics</th>
                                    <th class="px-2 py-1 border text-left">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="previewItemsBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-[10px] text-gray-500">Requested By:</p>
                            @if(Auth::user()->signature_url)
                                <img src="{{ Auth::user()->signature_url }}" class="h-10 w-auto mt-1">
                            @else
                                <p class="text-xs text-gray-400 mt-1">No signature</p>
                            @endif
                            <p class="text-xs font-medium mt-1">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                            <p class="text-[10px] text-gray-500">{{ now()->format('F d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500">Management Use Only</p>
                            <p class="text-xs text-gray-400 mt-2">Approved By: _______________</p>
                            <p class="text-xs text-gray-400 mt-1">Date: _______________</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 flex justify-end gap-2 pt-3 border-t no-print">
            <button type="button" onclick="closePreview()" class="px-3 py-1 border rounded-md text-xs">Edit</button>
            <button type="button" onclick="printRequisition()" class="px-3 py-1 bg-blue-600 text-white rounded-md text-xs">Print</button>
            <button type="button" onclick="submitForm()" class="px-3 py-1 bg-green-600 text-white rounded-md text-xs">Confirm & Submit</button>
        </div>
    </div>
</div>

<script>
const DRAFT_KEY = 'requisition_draft';
let itemCounter = 0;

const batchOptions = `
    <option value="">-- Select Batch --</option>
    @foreach($batches as $batch)
        @php
            $item = $batch->inventoryItem;
            $itemName = $item->name ?? 'Unknown Item';
            $categoryName = $item->category->name ?? 'Uncategorized';
            $expiryDate = $batch->expiry_date;
            $expiryWarning = '';
            if ($expiryDate) {
                $daysLeft = now()->diffInDays($expiryDate, false);
                if ($daysLeft <= 0) {
                    $expiryWarning = ' EXPIRED!';
                } elseif ($daysLeft <= 30) {
                    $expiryWarning = ' (Expires in ' . $daysLeft . ' days)';
                }
            }
            $totalStock = \App\Models\Batch::where('inventory_item_id', $batch->inventory_item_id)
                ->where('batch_status', 'active')
                ->where('remaining_quantity', '>', 0)
                ->sum('remaining_quantity');
            $unitCost = number_format($batch->unit_cost, 2);
        @endphp
        <option value="{{ $batch->id }}"
            data-item-id="{{ $batch->inventory_item_id }}"
            data-item-name="{{ addslashes($itemName) }}"
            data-category="{{ addslashes($categoryName) }}"
            data-batch-number="{{ $batch->batch_number }}"
            data-expiry-date="{{ $expiryDate ? $expiryDate->format('Y-m-d') : 'N/A' }}"
            data-batch-stock="{{ $batch->remaining_quantity }}"
            data-total-stock="{{ $totalStock }}"
            data-unit="{{ $batch->unit_of_measurement ?? $item->unit_of_measurement ?? 'piece' }}"
            data-pack-type="{{ $batch->pack_type ?? 'Direct' }}"
            data-pack-size="{{ $batch->pack_size ?? 1 }}"
            data-unit-cost="{{ $batch->unit_cost }}">
            {{ $itemName }} - Batch #{{ $batch->batch_number }} (Exp: {{ $expiryDate ? $expiryDate->format('d M Y') : 'N/A' }}{{ $expiryWarning }}, Stock: {{ number_format($batch->remaining_quantity, 2) }}, Cost: UGX {{ $unitCost }})
        </option>
    @endforeach
`;

const metricsOptions = `
    <option value="">-- Select --</option>
    <option value="kg">Kilograms (kg)</option>
    <option value="litres">Litres (L)</option>
    <option value="pcs">Pieces (pcs)</option>
    <option value="boxes">Boxes</option>
    <option value="bags">Bags</option>
    <option value="cartons">Cartons</option>
    <option value="dozen">Dozen</option>
    <option value="bottles">Bottles</option>
    <option value="packs">Packs</option>
`;

// Low stock items data from backend
const lowStockItems = @json($lowStockItems ?? []);

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Populate low stock items table
function populateLowStockItems() {
    const tbody = $('#lowStockItemsBody');
    tbody.empty();

    if (lowStockItems.length === 0) {
        tbody.html('<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No low stock items found</td></tr>');
        return;
    }

    lowStockItems.forEach(item => {
        const row = `
            <tr class="low-stock-item quick-add-btn" data-batch-id="${item.batch_id}" data-item-name="${escapeHtml(item.item_name)}" data-category="${escapeHtml(item.category)}" data-batch-number="${escapeHtml(item.batch_number)}" data-expiry-date="${item.expiry_date}" data-batch-stock="${item.batch_stock}" data-total-stock="${item.total_stock}" data-unit="${item.unit}" data-unit-cost="${item.unit_cost}" data-item-id="${item.item_id}">
                <td class="px-4 py-2 text-sm font-medium text-gray-800">${escapeHtml(item.item_name)}</td>
                <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(item.category)}</td>
                <td class="px-4 py-2 text-sm font-mono">${escapeHtml(item.batch_number)}</td>
                <td class="px-4 py-2 text-sm ${item.expiry_status === 'expired' ? 'text-red-600 font-semibold' : (item.expiry_status === 'expiring_soon' ? 'text-amber-600' : 'text-gray-600')}">${escapeHtml(item.expiry_date)}</td>
                <td class="px-4 py-2 text-sm text-right font-semibold ${item.batch_stock <= item.reorder_level ? 'text-red-600' : 'text-gray-800'}">${parseFloat(item.batch_stock).toFixed(2)} ${item.unit}</td>
                <td class="px-4 py-2 text-sm text-right">UGX ${parseFloat(item.unit_cost).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-2 text-center">
                    <button type="button" class="quick-add-lowstock bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs hover:bg-blue-200 transition">+ Add</button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Attach click events to quick add buttons
    $('.quick-add-lowstock').on('click', function(e) {
        e.stopPropagation();
        const row = $(this).closest('tr');
        addLowStockItemToRequisition(row);
    });

    // Also allow clicking on the row itself
    $('.low-stock-item').on('click', function(e) {
        if (!$(e.target).is('button')) {
            addLowStockItemToRequisition($(this));
        }
    });
}

function addLowStockItemToRequisition($row) {
    const batchId = $row.data('batch-id');
    const itemName = $row.data('item-name');
    const category = $row.data('category');
    const batchNumber = $row.data('batch-number');
    const expiryDate = $row.data('expiry-date');
    const batchStock = $row.data('batch-stock');
    const totalStock = $row.data('total-stock');
    const unit = $row.data('unit');
    const unitCost = $row.data('unit-cost');
    const itemId = $row.data('item-id');

    // Check if this batch is already added
    let existingRow = null;
    $('#itemsBody .item-row').each(function() {
        const hiddenItemId = $(this).find('.item-id-hidden').val();
        if (hiddenItemId == itemId) {
            existingRow = $(this);
            return false;
        }
    });

    if (existingRow) {
        alert(`${itemName} is already in your requisition list. Please update the quantity there.`);
        // Switch to manual tab and highlight the row
        $('#tabManualSelect').click();
        existingRow.css('background-color', '#fef3c7').animate({ backgroundColor: '#ffffff' }, 1000);
        return;
    }

    // Create a new row in the manual selection table
    const index = itemCounter++;
    const newRow = `
        <tr class="item-row" id="row-${index}" data-batch-id="${batchId}">
            <td class="item-cell" style="width:20%">
                <select name="items[${index}][batch_id]" class="batch-select w-full" required style="font-size: 12px;">
                    ${batchOptions}
                </select>
                <div class="selected-item-name text-xs font-semibold text-gray-700 mt-1">📦 ${escapeHtml(itemName)}</div>
                <input type="hidden" name="items[${index}][inventory_item_id]" class="item-id-hidden" value="${itemId}">
                <input type="hidden" name="items[${index}][unit_cost]" class="unit-cost-hidden" value="${unitCost}">
                <input type="hidden" name="items[${index}][item_name]" class="item-name-hidden" value="${escapeHtml(itemName)}">
                <input type="hidden" name="items[${index}][batch_stock]" class="batch-stock-hidden" value="${batchStock}">
                <input type="hidden" name="items[${index}][total_stock]" class="total-stock-hidden" value="${totalStock}">
             </td>
            <td class="category-cell">${escapeHtml(category)}</td>
            <td class="batch-details-cell">
                <strong class="batch-number">${escapeHtml(batchNumber)}</strong><br>
                <span class="expiry-date">Exp: ${escapeHtml(expiryDate)}</span><br>
                <span class="unit-cost-info text-gray-600 text-[10px]">💰 Unit Cost: UGX ${parseFloat(unitCost).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
            </td>
            <td class="stock-cell">
                <div class="batch-stock-info">
                    <span class="batch-info ${batchStock <= 10 ? 'batch-low' : 'batch-ok'}">📦 Batch: ${parseFloat(batchStock).toFixed(2)} ${unit}</span>
                </div>
                <div class="total-stock-info mt-1">
                    <span class="total-stock">📊 Total: ${parseFloat(totalStock).toFixed(2)} ${unit}</span>
                </div>
            </td>
            <td>
                <input type="number" name="items[${index}][quantity]" step="0.01" class="w-full px-2 py-1 border rounded-md text-sm" placeholder="0.00" required>
            </td>
            <td>
                <select name="items[${index}][metrics]" class="w-full px-2 py-1 border rounded-md text-sm">
                    ${metricsOptions}
                </select>
            </td>
            <td>
                <input type="text" name="items[${index}][notes]" class="w-full px-2 py-1 border rounded-md text-sm" placeholder="Reason...">
            </td>
            <td class="text-center">
                <button type="button" class="remove-item text-red-600 hover:text-red-800" title="Remove">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </td>
        </tr>
    `;

    $('#itemsBody').append(newRow);

    // Initialize select2 for the new row and preselect the batch
    const $newRowElement = $(`#row-${index}`);
    const $select = $newRowElement.find('.batch-select');

    if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
    $select.select2({ placeholder: '-- Search Batch --', allowClear: true, width: '100%' });
    $select.val(batchId).trigger('change');

    attachRemoveEvents();
    saveDraft();

    // Show a success notification
    const notification = $(`<div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg text-sm z-50">✓ ${itemName} added to requisition</div>`);
    $('body').append(notification);
    setTimeout(() => notification.fadeOut(300, function() { $(this).remove(); }), 2000);

    // Switch to manual selection tab to show the added item
    $('#tabManualSelect').click();

    // Scroll to the new row
    $('html, body').animate({
        scrollTop: $newRowElement.offset().top - 100
    }, 500);
}

// Tab switching
$('#tabLowStock').on('click', function() {
    $(this).addClass('active');
    $('#tabManualSelect').removeClass('active');
    $('#lowStockPanel').removeClass('hidden');
    $('#manualSelectPanel').addClass('hidden');
});

$('#tabManualSelect').on('click', function() {
    $(this).addClass('active');
    $('#tabLowStock').removeClass('active');
    $('#manualSelectPanel').removeClass('hidden');
    $('#lowStockPanel').addClass('hidden');
});

// Existing functions from previous version
function saveDraft() {
    const rows = [];
    document.querySelectorAll('#itemsBody .item-row').forEach(row => {
        rows.push({
            batch_id: row.querySelector('.batch-select').value,
            quantity: row.querySelector('input[name*="[quantity]"]').value,
            metrics: row.querySelector('select[name*="[metrics]"]').value,
            notes: row.querySelector('input[name*="[notes]"]').value,
        });
    });
    try {
        localStorage.setItem(DRAFT_KEY, JSON.stringify({
            requisition_type: document.getElementById('requisition_type').value,
            date_needed: document.getElementById('date_needed').value,
            notes: document.getElementById('notes').value,
            rows,
        }));
    } catch(e) {}
}

function loadDraft() {
    try { const r = localStorage.getItem(DRAFT_KEY); return r ? JSON.parse(r) : null; }
    catch(e) { return null; }
}

function clearDraft() { localStorage.removeItem(DRAFT_KEY); }

function createNewRow(saved) {
    const index = itemCounter++;
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.id = `row-${index}`;
    tr.innerHTML = `
        <td class="item-cell" style="width:20%">
            <select name="items[${index}][batch_id]" class="batch-select w-full" required style="font-size: 12px;">
                ${batchOptions}
            </select>
            <div class="selected-item-name text-xs font-semibold text-gray-700 mt-1 hidden"></div>
            <input type="hidden" name="items[${index}][inventory_item_id]" class="item-id-hidden">
            <input type="hidden" name="items[${index}][unit_cost]" class="unit-cost-hidden">
            <input type="hidden" name="items[${index}][item_name]" class="item-name-hidden">
            <input type="hidden" name="items[${index}][batch_stock]" class="batch-stock-hidden">
            <input type="hidden" name="items[${index}][total_stock]" class="total-stock-hidden">
         </td>
        <td class="category-cell">—</td>
        <td class="batch-details-cell">
            <strong class="batch-number">—</strong><br>
            <span class="expiry-date"></span><br>
            <span class="pack-info"></span><br>
            <span class="unit-cost-info"></span>
        </td>
        <td class="stock-cell">
            <div class="batch-stock-info"></div>
            <div class="total-stock-info mt-1"></div>
        </td>
        <td>
            <input type="number" name="items[${index}][quantity]" step="0.01" class="w-full px-2 py-1 border rounded-md text-sm" placeholder="0.00" required value="${saved && saved.quantity ? saved.quantity : ''}">
        </td>
        <td>
            <select name="items[${index}][metrics]" class="w-full px-2 py-1 border rounded-md text-sm">
                ${metricsOptions}
            </select>
        </td>
        <td>
            <input type="text" name="items[${index}][notes]" class="w-full px-2 py-1 border rounded-md text-sm" placeholder="Reason..." value="${saved && saved.notes ? saved.notes.replace(/"/g, '&quot;') : ''}">
        </td>
        <td class="text-center">
            <button type="button" class="remove-item text-red-600 hover:text-red-800" title="Remove">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </td>
    `;

    if (saved && saved.metrics) {
        setTimeout(() => {
            const metricsSelect = tr.querySelector('select[name*="[metrics]"]');
            if (metricsSelect) metricsSelect.value = saved.metrics;
        }, 50);
    }

    return tr;
}

function updateBatchDetails(row, selectedOption) {
    if (!selectedOption || !selectedOption.value) {
        row.querySelector('.category-cell').innerText = '—';
        row.querySelector('.selected-item-name').classList.add('hidden');
        row.querySelector('.selected-item-name').innerHTML = '';
        row.querySelector('.batch-details-cell').innerHTML = '<strong class="batch-number">—</strong><br><span class="expiry-date"></span><br><span class="pack-info"></span><br><span class="unit-cost-info"></span>';
        row.querySelector('.stock-cell').innerHTML = '<div class="batch-stock-info"></div><div class="total-stock-info mt-1"></div>';
        return;
    }

    const $opt = $(selectedOption);
    const itemName = $opt.data('item-name') || '';
    const category = $opt.data('category') || '—';
    const batchNumber = $opt.data('batch-number') || '';
    const expiryDate = $opt.data('expiry-date') || 'N/A';
    const batchStock = parseFloat($opt.data('batch-stock')) || 0;
    const totalStock = parseFloat($opt.data('total-stock')) || 0;
    const unit = $opt.data('unit') || 'piece';
    const packType = $opt.data('pack-type') || 'Direct';
    const packSize = $opt.data('pack-size') || 1;
    const unitCost = parseFloat($opt.data('unit-cost')) || 0;
    const itemId = $opt.data('item-id') || '';

    row.querySelector('.item-name-hidden').value = itemName;
    row.querySelector('.item-id-hidden').value = itemId;
    row.querySelector('.unit-cost-hidden').value = unitCost;
    row.querySelector('.batch-stock-hidden').value = batchStock;
    row.querySelector('.total-stock-hidden').value = totalStock;

    const itemNameDiv = row.querySelector('.selected-item-name');
    itemNameDiv.innerHTML = `📦 ${escapeHtml(itemName)}`;
    itemNameDiv.classList.remove('hidden');

    row.querySelector('.category-cell').innerHTML = `<span class="text-gray-700">${escapeHtml(category)}</span>`;

    let expiryHtml = escapeHtml(expiryDate);
    if (expiryDate !== 'N/A') {
        const expiry = new Date(expiryDate);
        const today = new Date();
        const daysLeft = Math.ceil((expiry - today) / (1000 * 60 * 60 * 24));
        if (daysLeft <= 0) {
            expiryHtml = `<span class="batch-expired">⚠️ EXPIRED: ${escapeHtml(expiryDate)}</span>`;
        } else if (daysLeft <= 30) {
            expiryHtml = `<span class="batch-expiring-soon">⚠️ Expires in ${daysLeft} days (${escapeHtml(expiryDate)})</span>`;
        }
    }

    let batchStockClass = batchStock <= 10 ? 'batch-low' : 'batch-ok';
    let batchStockText = batchStock <= 0 ? `OUT OF STOCK (${batchStock.toFixed(2)} ${unit})` :
                          (batchStock < 10 ? `⚠️ LOW: ${batchStock.toFixed(2)} ${unit}` : `${batchStock.toFixed(2)} ${unit}`);

    let totalStockText = `${totalStock.toFixed(2)} ${unit} total across all batches`;
    let packInfo = (packType !== 'Direct' && packSize > 1) ? `<span class="text-blue-500 text-xs">📦 ${escapeHtml(packType)} (${packSize} ${unit}/pack)</span><br>` : '';

    row.querySelector('.batch-details-cell').innerHTML = `
        <strong class="batch-number">${escapeHtml(batchNumber)}</strong><br>
        ${packInfo}
        <span class="expiry-date">${expiryHtml}</span><br>
        <span class="unit-cost-info text-gray-600 text-[10px]">💰 Unit Cost: UGX ${unitCost.toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
    `;

    row.querySelector('.stock-cell').innerHTML = `
        <div class="batch-stock-info">
            <span class="batch-info ${batchStockClass}">📦 Batch: ${batchStockText}</span>
        </div>
        <div class="total-stock-info mt-1">
            <span class="total-stock">📊 Total: ${totalStockText}</span>
        </div>
    `;
}

function initSelect2(element, preselectValue) {
    if ($(element).hasClass('select2-hidden-accessible')) $(element).select2('destroy');
    $(element).select2({ placeholder: '-- Search Batch --', allowClear: true, width: '100%' });
    $(element).off('change').on('change', function() {
        const row = $(this).closest('.item-row')[0];
        const selectedOption = this.options[this.selectedIndex];
        updateBatchDetails(row, selectedOption);
        saveDraft();
    });
    if (preselectValue) {
        $(element).val(preselectValue).trigger('change');
    }
}

function removeItem(e) {
    const row = $(e.currentTarget).closest('.item-row')[0];
    if ($('#itemsBody .item-row').length > 1) {
        $(row).remove();
        saveDraft();
    } else {
        alert('You must have at least one item.');
    }
}

function attachRemoveEvents() {
    $('.remove-item').off('click').on('click', removeItem);
}

// Initialize
$(document).ready(function() {
    populateLowStockItems();

    const draft = loadDraft();
    const tbody = $('#itemsBody');

    if (draft && draft.rows && draft.rows.length > 0) {
        $('#requisition_type').val(draft.requisition_type || 'normal');
        $('#date_needed').val(draft.date_needed || '');
        $('#notes').val(draft.notes || '');
        draft.rows.forEach(savedRow => {
            if (savedRow.batch_id) {
                const tr = createNewRow(savedRow);
                tbody.append(tr);
                initSelect2($(tr).find('.batch-select'), savedRow.batch_id || null);
            }
        });
        $('#draftBanner').show();
        if ($('#itemsBody .item-row').length > 0) {
            $('#tabManualSelect').click();
        }
    } else {
        const tr = createNewRow(null);
        tbody.append(tr);
        initSelect2($(tr).find('.batch-select'), null);
    }
    attachRemoveEvents();
});

$('#addItemBtn').on('click', function() {
    const tr = createNewRow(null);
    $('#itemsBody').append(tr);
    initSelect2($(tr).find('.batch-select'), null);
    attachRemoveEvents();
    saveDraft();
});

$('#requisition_type, #date_needed, #notes').on('change input', saveDraft);
$('#itemsBody').on('input change', saveDraft);

$('#clearDraftBtn').on('click', function() {
    if (confirm('Clear saved draft and start fresh?')) {
        clearDraft();
        $('#requisition_type').val('normal');
        $('#date_needed').val('');
        $('#notes').val('');
        $('#itemsBody').empty();
        itemCounter = 0;
        const tr = createNewRow(null);
        $('#itemsBody').append(tr);
        initSelect2($(tr).find('.batch-select'), null);
        attachRemoveEvents();
        $('#draftBanner').hide();
    }
});

function openPreview() {
    const reqType = $('#requisition_type').val();
    $('#previewType').html(`<span class="requisition-type-badge ${reqType === 'emergency' ? 'type-emergency' : 'type-normal'}">${reqType === 'emergency' ? 'EMERGENCY' : 'Normal'}</span>`);
    $('#previewDateNeeded').text($('#date_needed').val() || 'Not specified');
    $('#previewNotes').text($('#notes').val() || '—');

    const items = [];
    $('.item-row').each(function() {
        const select = $(this).find('.batch-select')[0];
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            const quantity = $(this).find('input[name*="[quantity]"]').val();
            if (quantity && parseFloat(quantity) > 0) {
                items.push({
                    name: $(opt).data('item-name') || '',
                    category: $(opt).data('category') || '—',
                    batchNumber: $(opt).data('batch-number') || '',
                    expiryDate: $(opt).data('expiry-date') || 'N/A',
                    currentStock: $(opt).data('total-stock') || 0,
                    unitCost: $(opt).data('unit-cost') || 0,
                    quantity: quantity,
                    metrics: $(this).find('select[name*="[metrics]"] option:checked').text() || '',
                    notes: $(this).find('input[name*="[notes]"]').val() || '',
                });
            }
        }
    });

    const previewBody = $('#previewItemsBody');
    previewBody.empty();
    if (items.length === 0) {
        previewBody.html('<tr><td colspan="9" class="px-3 py-6 text-center text-gray-500">No valid items added</td</tr>');
    } else {
        items.forEach(item => {
            previewBody.append(`
                <tr>
                    <td class="px-2 py-1 border">${escapeHtml(item.name)}</td>
                    <td class="px-2 py-1 border">${escapeHtml(item.category)}</td>
                    <td class="px-2 py-1 border">${escapeHtml(item.batchNumber)}</td>
                    <td class="px-2 py-1 border">${escapeHtml(item.expiryDate)}</td>
                    <td class="px-2 py-1 border text-right">${parseFloat(item.currentStock).toFixed(2)}</td>
                    <td class="px-2 py-1 border text-right">UGX ${parseFloat(item.unitCost).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td class="px-2 py-1 border text-right font-semibold">${parseFloat(item.quantity).toFixed(2)}</td>
                    <td class="px-2 py-1 border">${escapeHtml(item.metrics) || '—'}</td>
                    <td class="px-2 py-1 border">${escapeHtml(item.notes) || '—'}</td>
                </tr>
            `);
        });
    }

    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    $('#previewRequisitionNumber').text(`REQ-${now.getFullYear()}${pad(now.getMonth()+1)}${pad(now.getDate())}-${Math.floor(Math.random()*9999).toString().padStart(4,'0')}`);
    $('#previewModal').removeClass('hidden');
}

function closePreview() { $('#previewModal').addClass('hidden'); }

function printRequisition() {
    const content = document.getElementById('print-section').innerHTML;
    const win = window.open('', '_blank');
    win.document.write(`
        <!DOCTYPE html><html><head><title>Requisition</title>
        <style>body{padding:20px;font-family:Arial;font-size:12px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ccc;padding:6px;}th{background:#f5f5f5;}.text-right{text-align:right;}.no-print{display:none;}</style>
        </head><body>${content}</body></html>
    `);
    win.document.close();
    win.onload = () => { win.focus(); win.print(); };
}

function submitForm() {
    let valid = true;
    $('.item-row').each(function(i) {
        if (!valid) return;
        const batchId = $(this).find('.batch-select').val();
        const quantity = $(this).find('input[name*="[quantity]"]').val();
        if (!batchId) { alert(`Please select a batch for row ${i+1}`); valid = false; }
        else if (!quantity || parseFloat(quantity) <= 0) { alert(`Please enter a valid quantity for row ${i+1}`); valid = false; }
    });
    if (valid) { clearDraft(); $('#requisitionForm').submit(); }
}

$('#previewBtn').on('click', openPreview);
</script>
@endsection
