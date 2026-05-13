{{-- resources/views/restaurant/requisitions/create.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'Create Requisition')

@section('page-title', 'Create New Requisition')

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .form-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    .form-body {
        padding: 1.5rem;
    }
    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .form-label .required {
        color: #ef4444;
        margin-left: 0.25rem;
    }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }
    .form-textarea {
        min-height: 80px;
    }
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .items-table-container {
        overflow-x: auto;
        margin-bottom: 1rem;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .items-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }
    .items-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .btn-add {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        width: 100%;
    }
    .btn-add:hover {
        background: #059669;
    }
    .btn-remove {
        background: #ef4444;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        border: none;
        cursor: pointer;
    }
    .btn-remove:hover {
        background: #dc2626;
    }
    .btn-submit {
        background: #ea580c;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-submit:hover {
        background: #c2410c;
    }
    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-cancel:hover {
        background: #e5e7eb;
    }

    /* Live Search Styles */
    .item-search-container {
        position: relative;
        width: 100%;
    }
    .search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 10;
        display: none;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .search-dropdown-item {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.8rem;
    }
    .search-dropdown-item:hover {
        background: #fef3c7;
    }
    .search-dropdown-item .item-code {
        font-size: 0.65rem;
        color: #6b7280;
    }
    .selected-item-display {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        background: #fef3c7;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    .selected-item-name {
        font-weight: 600;
    }
    .clear-item {
        color: #ef4444;
        cursor: pointer;
    }
</style>

<div class="form-card">
    <div class="form-header">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clipboard-list mr-2 text-orange-600"></i>
            Create New Requisition
        </h3>
        <p class="text-xs text-gray-500 mt-1">Request items from the store for restaurant operations</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('restaurant.requisitions.store') }}" id="requisitionForm">
            @csrf

            <div class="form-row">
                <div>
                    <label class="form-label">Date Needed</label>
                    <input type="date" name="date_needed" class="form-input" value="{{ old('date_needed') }}">
                </div>
                <div>
                    <label class="form-label">Notes (for Store)</label>
                    <textarea name="department_notes" class="form-textarea" placeholder="Any special instructions for the store...">{{ old('department_notes') }}</textarea>
                </div>
            </div>

            <div class="mt-4 mb-3">
                <h4 class="font-semibold text-gray-700">Items Requested</h4>
            </div>

            <div class="items-table-container">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 30%">Item</th>
                            <th style="width: 10%">Quantity</th>
                            <th style="width: 12%">Pack Type</th>
                            <th style="width: 10%">Pcs/Pack</th>
                            <th style="width: 12%">Metrics</th>
                            <th style="width: 20%">Notes</th>
                            <th style="width: 6%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Rows will be added dynamically -->
                    </tbody>
                </table>
            </div>

            <div class="mt-3 mb-4">
                <button type="button" id="addItemBtn" class="btn-add">
                    <i class="fas fa-plus mr-1"></i> Add Item
                </button>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('restaurant.requisitions.index') }}" class="btn-cancel">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane mr-1"></i> Submit Requisition
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let itemCounter = 0;
    let itemsList = @json($items->map(function($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'code' => $item->item_code ?? 'N/A'
        ];
    }));

    // Create search dropdown HTML
    function createSearchDropdown(index) {
        const dropdownId = `search-dropdown-${index}`;
        const searchInputId = `item-search-${index}`;

        return `
            <div class="item-search-container">
                <input type="text" id="${searchInputId}" class="form-input item-search" placeholder="Search item by name or code..." autocomplete="off">
                <div id="${dropdownId}" class="search-dropdown"></div>
                <input type="hidden" name="items[${index}][inventory_item_id]" class="selected-item-id" value="">
                <div id="selected-display-${index}" class="selected-item-display" style="display: none;">
                    <span class="selected-item-name"></span>
                    <span class="clear-item" onclick="clearSelectedItem(${index})">✕ Remove</span>
                </div>
            </div>
        `;
    }

    // Create a new row
    function createNewRow(index) {
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.id = `row-${index}`;
        newRow.dataset.index = index;
        newRow.innerHTML = `
            <td class="item-cell">
                ${createSearchDropdown(index)}
            </td>
            <td>
                <input type="number" name="items[${index}][quantity]" step="0.01" class="form-input item-quantity" placeholder="0.00" disabled>
            </td>
            <td>
                <select name="items[${index}][pack_type]" class="form-select item-pack-type" disabled>
                    <option value="">-- None --</option>
                    <option value="carton">Carton</option>
                    <option value="box">Box</option>
                    <option value="crate">Crate</option>
                    <option value="dozen">Dozen</option>
                    <option value="pack">Pack</option>
                    <option value="bag">Bag</option>
                    <option value="sack">Sack</option>
                    <option value="bottle">Bottle</option>
                </select>
            </td>
            <td>
                <input type="number" name="items[${index}][pack_size]" step="1" class="form-input item-pack-size" placeholder="e.g., 12" disabled>
            </td>
            <td>
                <select name="items[${index}][metrics]" class="form-select item-metrics" disabled>
                    <option value="">-- Select --</option>
                    <option value="kg">Kilograms (kg)</option>
                    <option value="litres">Litres (L)</option>
                    <option value="pcs">Pieces (pcs)</option>
                    <option value="grams">Grams (g)</option>
                    <option value="millilitres">Millilitres (ml)</option>
                    <option value="bottles">Bottles</option>
                </select>
            </td>
            <td>
                <input type="text" name="items[${index}][notes]" class="form-input item-notes" placeholder="Optional notes" disabled>
            </td>
            <td class="text-center">
                <button type="button" class="btn-remove remove-item" data-index="${index}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        return newRow;
    }

    // Setup live search for a specific row
    function setupLiveSearch(index) {
        const searchInput = document.getElementById(`item-search-${index}`);
        const dropdown = document.getElementById(`search-dropdown-${index}`);
        const hiddenInput = document.querySelector(`#row-${index} .selected-item-id`);
        const selectedDisplay = document.getElementById(`selected-display-${index}`);
        const selectedNameSpan = selectedDisplay?.querySelector('.selected-item-name');

        // Enable/disable form fields based on selection
        const enableFields = (enabled) => {
            const row = document.getElementById(`row-${index}`);
            const quantityInput = row.querySelector('.item-quantity');
            const packTypeSelect = row.querySelector('.item-pack-type');
            const packSizeInput = row.querySelector('.item-pack-size');
            const metricsSelect = row.querySelector('.item-metrics');
            const notesInput = row.querySelector('.item-notes');

            quantityInput.disabled = !enabled;
            packTypeSelect.disabled = !enabled;
            packSizeInput.disabled = !enabled;
            metricsSelect.disabled = !enabled;
            notesInput.disabled = !enabled;

            if (enabled) {
                quantityInput.required = true;
            } else {
                quantityInput.required = false;
            }
        };

        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filteredItems = itemsList.filter(item =>
                item.name.toLowerCase().includes(searchTerm) ||
                item.code.toLowerCase().includes(searchTerm)
            );

            if (filteredItems.length > 0 && searchTerm.length > 0) {
                dropdown.style.display = 'block';
                dropdown.innerHTML = filteredItems.map(item => `
                    <div class="search-dropdown-item" data-id="${item.id}" data-name="${item.name}" data-code="${item.code}">
                        <div class="font-medium">${item.name}</div>
                        <div class="item-code">Code: ${item.code}</div>
                    </div>
                `).join('');

                // Add click handlers
                dropdown.querySelectorAll('.search-dropdown-item').forEach(el => {
                    el.addEventListener('click', function() {
                        const itemId = this.dataset.id;
                        const itemName = this.dataset.name;
                        const itemCode = this.dataset.code;

                        hiddenInput.value = itemId;
                        searchInput.value = itemName;
                        searchInput.style.display = 'none';
                        selectedDisplay.style.display = 'flex';
                        selectedNameSpan.innerHTML = `${itemName} <span class="text-xs text-gray-500">(${itemCode})</span>`;
                        dropdown.style.display = 'none';
                        enableFields(true);
                    });
                });
            } else {
                dropdown.style.display = 'none';
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    // Clear selected item
    window.clearSelectedItem = function(index) {
        const searchInput = document.getElementById(`item-search-${index}`);
        const hiddenInput = document.querySelector(`#row-${index} .selected-item-id`);
        const selectedDisplay = document.getElementById(`selected-display-${index}`);

        hiddenInput.value = '';
        searchInput.value = '';
        searchInput.style.display = 'block';
        selectedDisplay.style.display = 'none';

        // Clear all field values
        const row = document.getElementById(`row-${index}`);
        row.querySelector('.item-quantity').value = '';
        row.querySelector('.item-pack-type').value = '';
        row.querySelector('.item-pack-size').value = '';
        row.querySelector('.item-metrics').value = '';
        row.querySelector('.item-notes').value = '';

        // Disable fields again
        const quantityInput = row.querySelector('.item-quantity');
        const packTypeSelect = row.querySelector('.item-pack-type');
        const packSizeInput = row.querySelector('.item-pack-size');
        const metricsSelect = row.querySelector('.item-metrics');
        const notesInput = row.querySelector('.item-notes');

        quantityInput.disabled = true;
        packTypeSelect.disabled = true;
        packSizeInput.disabled = true;
        metricsSelect.disabled = true;
        notesInput.disabled = true;
        quantityInput.required = false;
    };

    // Initialize all rows
    function initializeRows() {
        document.querySelectorAll('.item-row').forEach(row => {
            const index = row.dataset.index;
            if (index !== undefined) {
                setupLiveSearch(parseInt(index));
            }
        });
    }

    // Remove item
    function removeItem(e) {
        const button = e.target.closest('.remove-item');
        if (!button) return;
        const index = button.dataset.index;
        const row = document.getElementById(`row-${index}`);
        if (row) {
            row.remove();
        }
        updateRowIndices();
    }

    // Update row indices after removal
    function updateRowIndices() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, newIndex) => {
            row.id = `row-${newIndex}`;
            row.dataset.index = newIndex;

            // Update all name attributes in the row
            row.querySelectorAll('[name]').forEach(el => {
                const name = el.getAttribute('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${newIndex}]`);
                    el.setAttribute('name', newName);
                }
            });

            // Update IDs of elements
            const searchInput = row.querySelector('.item-search');
            if (searchInput) searchInput.id = `item-search-${newIndex}`;

            const dropdown = row.querySelector('.search-dropdown');
            if (dropdown) dropdown.id = `search-dropdown-${newIndex}`;

            const selectedDisplay = row.querySelector('.selected-item-display');
            if (selectedDisplay) selectedDisplay.id = `selected-display-${newIndex}`;

            const removeBtn = row.querySelector('.remove-item');
            if (removeBtn) removeBtn.dataset.index = newIndex;
        });

        // Reinitialize live search for all rows
        initializeRows();
    }

    // Add new item button
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newIndex = document.querySelectorAll('.item-row').length;
        const newRow = createNewRow(newIndex);
        tbody.appendChild(newRow);
        setupLiveSearch(newIndex);

        // Add remove event listener
        const removeBtn = newRow.querySelector('.remove-item');
        if (removeBtn) {
            removeBtn.addEventListener('click', removeItem);
        }
    });

    // Add remove event listeners to existing rows
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', removeItem);
    });

    // Form validation
    document.getElementById('requisitionForm').addEventListener('submit', function(e) {
        let hasValidItem = false;
        const rows = document.querySelectorAll('.item-row');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const itemId = row.querySelector('.selected-item-id').value;
            const quantity = row.querySelector('.item-quantity').value;

            if (itemId && parseFloat(quantity) > 0) {
                hasValidItem = true;
                break;
            }
        }

        if (!hasValidItem) {
            e.preventDefault();
            alert('Please add at least one item with a valid quantity.');
        }
    });

    // Add initial empty row if no rows exist
    if (document.querySelectorAll('.item-row').length === 0) {
        document.getElementById('addItemBtn').click();
    }
</script>
@endsection
