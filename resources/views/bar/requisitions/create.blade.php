{{-- resources/views/bar/requisitions/create.blade.php --}}

@extends('layouts.bar')

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

    /* Enhanced Search Dropdown Styles */
    .item-search-wrapper {
        position: relative;
        width: 100%;
    }
    .item-search-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    .item-search-input:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        max-height: 280px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        display: none;
    }
    .search-results-dropdown.show {
        display: block;
    }
    .search-result-item {
        padding: 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .search-result-item:hover {
        background: #fef3c7;
    }
    .search-result-item .item-name {
        font-weight: 600;
        font-size: 0.875rem;
        color: #1f2937;
    }
    .search-result-item .item-code {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 0.125rem;
    }
    .selected-item-badge {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fef3c7;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border: 1px solid #fed7aa;
    }
    .selected-item-badge .item-info {
        font-weight: 600;
        font-size: 0.875rem;
        color: #92400e;
    }
    .selected-item-badge .clear-item-btn {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        transition: background 0.2s;
    }
    .selected-item-badge .clear-item-btn:hover {
        background: #fee2e2;
    }
    .field-disabled {
        background-color: #f9fafb;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Dropdown arrow indicator */
    .item-search-wrapper::after {
        content: '▾';
        position: absolute;
        right: 0.75rem;
        top: 0.55rem;
        color: #9ca3af;
        pointer-events: none;
        font-size: 0.85rem;
    }
    .item-search-wrapper.has-selection::after {
        display: none;
    }
</style>

<div class="form-card">
    <div class="form-header">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
            Create New Bar Requisition
        </h3>
        <p class="text-xs text-gray-500 mt-1">Request items from the store for bar operations</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('bar.requisitions.store') }}" id="requisitionForm">
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
                <a href="{{ route('bar.requisitions.index') }}" class="btn-cancel">
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
    // Items data from backend
    let itemsList = @json($items->map(function($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'code' => $item->item_code ?? 'N/A'
        ];
    }));

    let rowCounter = 0;

    // Render dropdown with items (filtered or all)
    function renderDropdown(dropdownElement, items) {
        if (!dropdownElement) return;

        if (items.length === 0) {
            dropdownElement.innerHTML = '<div class="search-result-item" style="color:#6b7280;">No items found</div>';
            dropdownElement.classList.add('show');
            return;
        }

        dropdownElement.innerHTML = items.map(item => `
            <div class="search-result-item" data-id="${item.id}" data-name="${escapeHtml(item.name)}" data-code="${escapeHtml(item.code)}">
                <div class="item-name">${escapeHtml(item.name)}</div>
                <div class="item-code">Code: ${escapeHtml(item.code)}</div>
            </div>
        `).join('');
        dropdownElement.classList.add('show');

        // Add click handlers to each result
        dropdownElement.querySelectorAll('.search-result-item').forEach(el => {
            el.addEventListener('click', (e) => {
                e.stopPropagation();
                const wrapper = dropdownElement.closest('.item-search-wrapper');
                const rowElement = wrapper.closest('.item-row');
                const searchInputElem = wrapper.querySelector('.item-search-input');
                const selectedBadge = wrapper.querySelector('.selected-item-badge');
                const selectedInfoSpan = selectedBadge.querySelector('.item-info');
                const hiddenId = wrapper.querySelector('.selected-item-id');

                const itemId = el.dataset.id;
                const itemName = el.dataset.name;
                const itemCode = el.dataset.code;

                // Update UI with selected item
                hiddenId.value = itemId;
                searchInputElem.value = itemName;
                searchInputElem.style.display = 'none';
                selectedInfoSpan.innerHTML = `${escapeHtml(itemName)} <span style="font-size:0.7rem; color:#6b7280;">(${escapeHtml(itemCode)})</span>`;
                selectedBadge.style.display = 'flex';
                dropdownElement.classList.remove('show');

                // Mark wrapper as having a selection (hides the arrow)
                wrapper.classList.add('has-selection');

                // Enable fields
                toggleFieldsForRow(rowElement, true);
            });
        });
    }

    // Create a new row with enhanced search
    function createNewRow() {
        const index = rowCounter++;
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.dataset.index = index;
        newRow.id = `row-${index}`;

        newRow.innerHTML = `
            <td class="item-cell">
                <div class="item-search-wrapper" data-row-index="${index}">
                    <input type="text" class="item-search-input" placeholder="Click to select an item..." autocomplete="off" readonly>
                    <div class="search-results-dropdown"></div>
                    <div class="selected-item-badge" style="display: none;">
                        <span class="item-info"></span>
                        <button type="button" class="clear-item-btn">✕ Remove</button>
                    </div>
                    <input type="hidden" name="items[${index}][inventory_item_id]" class="selected-item-id" value="">
                </div>
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

    // Enable/disable fields based on selection state
    function toggleFieldsForRow(rowElement, enabled) {
        const quantity = rowElement.querySelector('.item-quantity');
        const packType = rowElement.querySelector('.item-pack-type');
        const packSize = rowElement.querySelector('.item-pack-size');
        const metrics = rowElement.querySelector('.item-metrics');
        const notes = rowElement.querySelector('.item-notes');

        quantity.disabled = !enabled;
        packType.disabled = !enabled;
        packSize.disabled = !enabled;
        metrics.disabled = !enabled;
        notes.disabled = !enabled;

        if (enabled) {
            quantity.required = true;
        } else {
            quantity.required = false;
            quantity.value = '';
            packType.value = '';
            packSize.value = '';
            metrics.value = '';
            notes.value = '';
        }
    }

    // Clear selected item in a row
    function clearSelectedItem(wrapper, rowElement) {
        const searchInput = wrapper.querySelector('.item-search-input');
        const dropdown = wrapper.querySelector('.search-results-dropdown');
        const selectedBadge = wrapper.querySelector('.selected-item-badge');
        const hiddenId = wrapper.querySelector('.selected-item-id');

        // Reset UI
        searchInput.value = '';
        searchInput.style.display = 'block';
        selectedBadge.style.display = 'none';
        hiddenId.value = '';
        dropdown.classList.remove('show');
        dropdown.innerHTML = '';

        // Remove has-selection class to restore arrow
        wrapper.classList.remove('has-selection');

        // Disable and clear fields
        toggleFieldsForRow(rowElement, false);
    }

    // Show all items in dropdown
    function showAllItems(dropdownElement) {
        renderDropdown(dropdownElement, itemsList);
    }

    // Filter items based on search term
    function filterAndShowItems(dropdownElement, searchTerm) {
        if (searchTerm.trim() === '') {
            renderDropdown(dropdownElement, itemsList);
        } else {
            const filtered = itemsList.filter(item =>
                item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                item.code.toLowerCase().includes(searchTerm.toLowerCase())
            );
            renderDropdown(dropdownElement, filtered);
        }
    }

    // Setup search functionality for a specific row
    function setupRowSearch(rowElement) {
        const wrapper = rowElement.querySelector('.item-search-wrapper');
        if (!wrapper) return;

        const searchInput = wrapper.querySelector('.item-search-input');
        const dropdown = wrapper.querySelector('.search-results-dropdown');
        const selectedBadge = wrapper.querySelector('.selected-item-badge');
        const clearBtn = selectedBadge.querySelector('.clear-item-btn');

        let typingTimeout;

        // Clear button handler
        clearBtn.addEventListener('click', () => {
            clearSelectedItem(wrapper, rowElement);
        });

        // Open dropdown immediately on mousedown (feels instant)
        searchInput.addEventListener('mousedown', function(e) {
            const hiddenId = wrapper.querySelector('.selected-item-id');
            if (!hiddenId.value) {
                e.preventDefault();
                searchInput.focus();
                showAllItems(dropdown);
            }
        });

        // Also open on focus (keyboard navigation / tab)
        searchInput.addEventListener('focus', function() {
            const hiddenId = wrapper.querySelector('.selected-item-id');
            if (!hiddenId.value) {
                showAllItems(dropdown);
            }
        });

        // Once dropdown is open, allow typing to filter
        searchInput.addEventListener('keydown', function() {
            searchInput.removeAttribute('readonly');
        });

        // Search/filter input handler with debounce
        searchInput.addEventListener('input', function() {
            const hiddenId = wrapper.querySelector('.selected-item-id');
            if (hiddenId.value) return;

            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                filterAndShowItems(dropdown, this.value);
            }, 200);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                dropdown.classList.remove('show');
                searchInput.setAttribute('readonly', true);
                const hiddenId = wrapper.querySelector('.selected-item-id');
                if (!hiddenId.value) {
                    searchInput.value = '';
                }
            }
        });

        // Prevent dropdown from closing when clicking inside it
        dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // Helper to escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Remove item row
    function removeItemRow(button) {
        const row = button.closest('.item-row');
        if (row) {
            row.remove();
            reindexRows();
        }
    }

    // Reindex rows after removal
    function reindexRows() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, newIndex) => {
            row.dataset.index = newIndex;
            row.id = `row-${newIndex}`;

            const inputs = row.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${newIndex}]`);
                    input.setAttribute('name', newName);
                }
            });

            const removeBtn = row.querySelector('.remove-item');
            if (removeBtn) removeBtn.dataset.index = newIndex;

            const wrapper = row.querySelector('.item-search-wrapper');
            if (wrapper) wrapper.dataset.rowIndex = newIndex;
        });
    }

    // Add new item button handler
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newRow = createNewRow();
        tbody.appendChild(newRow);

        setupRowSearch(newRow);

        const removeBtn = newRow.querySelector('.remove-item');
        removeBtn.addEventListener('click', () => removeItemRow(removeBtn));
    });

    // Initialize existing rows (if any from validation errors)
    function initializeExistingRows() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            setupRowSearch(row);
            const removeBtn = row.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => removeItemRow(removeBtn));
            }

            const hiddenId = row.querySelector('.selected-item-id');
            if (hiddenId && hiddenId.value) {
                const wrapper = row.querySelector('.item-search-wrapper');
                const searchInput = wrapper.querySelector('.item-search-input');
                const selectedBadge = wrapper.querySelector('.selected-item-badge');
                const selectedInfoSpan = selectedBadge.querySelector('.item-info');

                const selectedItem = itemsList.find(item => item.id == hiddenId.value);
                if (selectedItem) {
                    searchInput.style.display = 'none';
                    selectedInfoSpan.innerHTML = `${escapeHtml(selectedItem.name)} <span style="font-size:0.7rem; color:#6b7280;">(${escapeHtml(selectedItem.code)})</span>`;
                    selectedBadge.style.display = 'flex';
                    wrapper.classList.add('has-selection');
                    toggleFieldsForRow(row, true);
                }
            }
        });
    }

    // Form validation before submit
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
    } else {
        initializeExistingRows();
    }
</script>
@endsection
