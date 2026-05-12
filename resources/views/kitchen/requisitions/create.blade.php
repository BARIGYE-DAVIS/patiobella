@extends('layouts.kitchen')

@section('title', 'Create Requisition')

@section('page-title', 'Create New Requisition')

@section('content')
<style>
    .item-row {
        transition: background-color 0.2s ease;
    }
    .item-row:hover {
        background-color: #f9fafb;
    }
    .select2-container .select2-selection--single {
        height: 36px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        padding-left: 10px;
        font-size: 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }
    .select2-dropdown {
        font-size: 0.75rem;
    }
    .help-text {
        font-size: 0.65rem;
        color: #6b7280;
        margin-top: 2px;
    }
    .compact-input {
        padding: 6px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }
    .btn-sm {
        padding: 4px 10px;
        font-size: 0.7rem;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
        <h3 class="text-md font-semibold text-gray-800">Create New Requisition</h3>
        <p class="text-xs text-gray-500">Request items from the store for kitchen operations</p>
    </div>

    <form method="POST" action="{{ route('kitchen.requisitions.store') }}" id="requisitionForm">
        @csrf

        <div class="p-5 space-y-5">
            {{-- Requisition Header --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Date Needed</label>
                    <input type="date" name="date_needed" value="{{ old('date_needed') }}"
                           class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="date_needed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Notes (for Store)</label>
                    <textarea name="department_notes" rows="2" id="department_notes"
                              placeholder="Any notes for the store manager..."
                              class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('department_notes') }}</textarea>
                </div>
            </div>

            {{-- Items Section --}}
            <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-800">Items Requested</h4>
                    <button type="button" id="addItemBtn"
                            class="bg-green-600 text-white px-3 py-1 rounded-md hover:bg-green-700 transition flex items-center gap-1 text-xs">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-md text-xs">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-2 py-2 text-left font-semibold text-gray-600 w-32">Item</th>
                                <th class="px-2 py-2 text-center font-semibold text-gray-600 w-20">Quantity</th>
                                <th class="px-2 py-2 text-center font-semibold text-gray-600 w-24">Pack Type</th>
                                <th class="px-2 py-2 text-center font-semibold text-gray-600 w-20">Pcs/Pack</th>
                                <th class="px-2 py-2 text-center font-semibold text-gray-600 w-20">Metrics</th>
                                <th class="px-2 py-2 text-left font-semibold text-gray-600">Notes</th>
                                <th class="px-2 py-2 text-center w-8">Action</th>
                            </td>
                        </thead>
                        <tbody id="itemsBody">
                            <tr class="item-row border-b" id="row-0">
                                <td class="px-2 py-2">
                                    <select name="items[0][inventory_item_id]" class="item-select w-full text-xs" required>
                                        <option value="">-- Search Item --</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-name="{{ $item->name }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-red-500 text-xs item-error hidden">Please select an item</div>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" name="items[0][quantity]" step="0.01"
                                           class="item-quantity w-full px-1 py-1 text-xs border border-gray-300 rounded text-center"
                                           placeholder="0.00" required>
                                    <div class="text-red-500 text-xs quantity-error hidden">Quantity must be > 0</div>
                                </td>
                                <td class="px-2 py-2">
                                    <select name="items[0][pack_type]" class="item-pack-type w-full px-1 py-1 text-xs border border-gray-300 rounded">
                                        <option value="">-- None --</option>
                                        <option value="carton">Carton</option>
                                        <option value="box">Box</option>
                                        <option value="crate">Crate</option>
                                        <option value="dozen">Dozen</option>
                                        <option value="pack">Pack</option>
                                        <option value="bag">Bag</option>
                                        <option value="sack">Sack</option>
                                        <option value="bottle">Bottle</option>
                                        <option value="can">Can</option>
                                        <option value="jar">Jar</option>
                                        <option value="tray">Tray</option>
                                        <option value="pallet">Pallet</option>
                                        <option value="case">Case</option>
                                        <option value="bundle">Bundle</option>
                                        <option value="roll">Roll</option>
                                        <option value="sheet">Sheet</option>
                                        <option value="liter">Liter</option>
                                        <option value="gallon">Gallon</option>
                                    </select>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" name="items[0][pack_size]" step="1"
                                           class="item-pack-size w-full px-1 py-1 text-xs border border-gray-300 rounded text-center"
                                           placeholder="e.g., 12">
                                </td>
                                <td class="px-2 py-2">
                                    <select name="items[0][metrics]" class="item-metrics w-full px-1 py-1 text-xs border border-gray-300 rounded">
                                        <option value="">-- Select --</option>
                                        <option value="kg">Kilograms (kg)</option>
                                        <option value="grams">Grams (g)</option>
                                        <option value="lbs">Pounds (lbs)</option>
                                        <option value="oz">Ounces (oz)</option>
                                        <option value="litres">Litres (L)</option>
                                        <option value="millilitres">Millilitres (ml)</option>
                                        <option value="gallons">Gallons (gal)</option>
                                        <option value="quarts">Quarts (qt)</option>
                                        <option value="pcs">Pieces (pcs)</option>
                                        <option value="dozen">Dozen (doz)</option>
                                        <option value="bottles">Bottles</option>
                                        <option value="cans">Cans</option>
                                        <option value="packets">Packets</option>
                                        <option value="meters">Meters (m)</option>
                                        <option value="centimeters">Centimeters (cm)</option>
                                        <option value="inches">Inches (in)</option>
                                        <option value="sq_meters">Square Meters (m²)</option>
                                    </select>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" name="items[0][notes]"
                                           class="item-notes w-full px-1 py-1 text-xs border border-gray-300 rounded"
                                           placeholder="Notes...">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @error('items')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Form Actions --}}
        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex justify-end gap-2">
            <a href="{{ route('kitchen.requisitions.index') }}" class="px-3 py-1.5 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Submit Requisition
            </button>
        </div>
    </form>
</div>

<script>
    let itemCounter = 1;

    function initSelect2(element) {
        $(element).select2({
            placeholder: "-- Search Item --",
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: true
        });
    }

    function createNewRow(index) {
        const newRow = document.createElement('tr');
        newRow.className = 'item-row border-b';
        newRow.id = `row-${index}`;
        newRow.innerHTML = `
            <td class="px-2 py-2">
                <select name="items[${index}][inventory_item_id]" class="item-select w-full text-xs" required>
                    <option value="">-- Search Item --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-name="{{ $item->name }}">{{ $item->name }}</option>
                    @endforeach
                </select>
                <div class="text-red-500 text-xs item-error hidden">Please select an item</div>
            </td>
            <td class="px-2 py-2">
                <input type="number" name="items[${index}][quantity]" step="0.01"
                       class="item-quantity w-full px-1 py-1 text-xs border border-gray-300 rounded text-center"
                       placeholder="0.00" required>
                <div class="text-red-500 text-xs quantity-error hidden">Quantity must be > 0</div>
            </td>
            <td class="px-2 py-2">
                <select name="items[${index}][pack_type]" class="item-pack-type w-full px-1 py-1 text-xs border border-gray-300 rounded">
                    <option value="">-- None --</option>
                    <option value="carton">Carton</option>
                    <option value="box">Box</option>
                    <option value="crate">Crate</option>
                    <option value="dozen">Dozen</option>
                    <option value="pack">Pack</option>
                    <option value="bag">Bag</option>
                    <option value="sack">Sack</option>
                    <option value="bottle">Bottle</option>
                    <option value="can">Can</option>
                    <option value="jar">Jar</option>
                    <option value="tray">Tray</option>
                    <option value="pallet">Pallet</option>
                    <option value="case">Case</option>
                    <option value="bundle">Bundle</option>
                    <option value="roll">Roll</option>
                    <option value="sheet">Sheet</option>
                    <option value="liter">Liter</option>
                    <option value="gallon">Gallon</option>
                </select>
            </td>
            <td class="px-2 py-2">
                <input type="number" name="items[${index}][pack_size]" step="1"
                       class="item-pack-size w-full px-1 py-1 text-xs border border-gray-300 rounded text-center"
                       placeholder="e.g., 12">
            </td>
            <td class="px-2 py-2">
                <select name="items[${index}][metrics]" class="item-metrics w-full px-1 py-1 text-xs border border-gray-300 rounded">
                    <option value="">-- Select --</option>
                    <option value="kg">Kilograms (kg)</option>
                    <option value="grams">Grams (g)</option>
                    <option value="lbs">Pounds (lbs)</option>
                    <option value="oz">Ounces (oz)</option>
                    <option value="litres">Litres (L)</option>
                    <option value="millilitres">Millilitres (ml)</option>
                    <option value="gallons">Gallons (gal)</option>
                    <option value="quarts">Quarts (qt)</option>
                    <option value="pcs">Pieces (pcs)</option>
                    <option value="dozen">Dozen (doz)</option>
                    <option value="bottles">Bottles</option>
                    <option value="cans">Cans</option>
                    <option value="packets">Packets</option>
                    <option value="meters">Meters (m)</option>
                    <option value="centimeters">Centimeters (cm)</option>
                    <option value="inches">Inches (in)</option>
                    <option value="sq_meters">Square Meters (m²)</option>
                </select>
            </td>
            <td class="px-2 py-2">
                <input type="text" name="items[${index}][notes]"
                       class="item-notes w-full px-1 py-1 text-xs border border-gray-300 rounded"
                       placeholder="Notes...">
            </td>
            <td class="px-2 py-2 text-center">
                <button type="button" class="remove-item text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        `;
        return newRow;
    }

    function validateForm() {
        let isValid = true;
        const rows = document.querySelectorAll('#itemsBody .item-row');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const itemSelect = row.querySelector('.item-select');
            const quantity = row.querySelector('.item-quantity');
            const itemError = row.querySelector('.item-error');
            const quantityError = row.querySelector('.quantity-error');

            // Reset errors
            itemError.classList.add('hidden');
            quantityError.classList.add('hidden');

            let hasError = false;

            // Check if item is selected
            if (!itemSelect.value) {
                itemError.classList.remove('hidden');
                isValid = false;
                hasError = true;
            }

            // Check if quantity is valid
            if (!quantity.value || parseFloat(quantity.value) <= 0) {
                quantityError.classList.remove('hidden');
                isValid = false;
                hasError = true;
            }
        }

        // Check if at least one item has valid quantity
        let hasValidItem = false;
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const itemSelect = row.querySelector('.item-select');
            const quantity = row.querySelector('.item-quantity').value;

            if (itemSelect.value && parseFloat(quantity) > 0) {
                hasValidItem = true;
                break;
            }
        }

        if (!hasValidItem) {
            alert('Please add at least one item with a valid quantity.');
            return false;
        }

        return isValid;
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-item').forEach(function(btn) {
            btn.removeEventListener('click', removeItem);
            btn.addEventListener('click', removeItem);
        });
    }

    function removeItem(e) {
        const row = e.target.closest('.item-row');
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            row.remove();
        } else {
            alert('You must have at least one item.');
        }
    }

    // Form submit validation
    document.getElementById('requisitionForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            alert('Please fix the errors above before submitting.');
        }
    });

    // Add item button
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newRow = createNewRow(itemCounter);
        tbody.appendChild(newRow);

        const newSelect = newRow.querySelector('.item-select');
        initSelect2(newSelect);

        attachRemoveEvents();
        itemCounter++;
    });

    // Initialize first row
    const firstSelect = document.querySelector('.item-select');
    if (firstSelect) {
        initSelect2(firstSelect);
    }
    attachRemoveEvents();
</script>
@endsection
