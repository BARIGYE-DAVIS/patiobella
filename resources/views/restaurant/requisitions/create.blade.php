{{-- resources/views/restaurant/requisitions/create.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'Create Requisition')

@section('page-title', 'Create New Requisition')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clipboard-list mr-2 text-orange-600"></i>
            Create New Requisition
        </h3>
        <p class="text-xs text-gray-500 mt-1">Request items from the store for restaurant operations</p>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('restaurant.requisitions.store') }}" id="requisitionForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Requisition Type <span class="text-red-500">*</span>
                    </label>
                    <select name="requisition_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-600 focus:ring-1 focus:ring-orange-600" required>
                        <option value="">Select Type</option>
                        @foreach($requisitionTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('requisition_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Date Needed</label>
                    <input type="date" name="date_needed" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-600 focus:ring-1 focus:ring-orange-600" value="{{ old('date_needed') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Notes (for Store)</label>
                    <textarea name="department_notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-600 focus:ring-1 focus:ring-orange-600" placeholder="Any special instructions for the store..." rows="1">{{ old('department_notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 mb-3">
                <h4 class="font-semibold text-gray-700">Items Requested</h4>
            </div>

            <div class="overflow-x-auto mb-4">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="w-[45%] px-3 py-3 text-left font-semibold text-gray-600 border-b-2 border-gray-200">Item <span class="text-red-500">*</span></th>
                            <th class="w-[25%] px-3 py-3 text-left font-semibold text-gray-600 border-b-2 border-gray-200">Quantity <span class="text-red-500">*</span></th>
                            <th class="w-[20%] px-3 py-3 text-left font-semibold text-gray-600 border-b-2 border-gray-200">Metrics</th>
                            <th class="w-[10%] px-3 py-3 text-center font-semibold text-gray-600 border-b-2 border-gray-200">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody"></tbody>
                </table>
            </div>

            <div class="mt-3 mb-4">
                <button type="button" id="addItemBtn" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    <i class="fas fa-plus mr-1"></i> Add Item
                </button>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('restaurant.requisitions.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
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
    let searchTimeout = null;

    // Fetch item details from API
    async function fetchItemDetails(itemId, rowElement) {
        if (!itemId) return;

        const loadingSpinner = rowElement.querySelector('.loading-spinner');
        if (loadingSpinner) loadingSpinner.classList.remove('hidden');

        try {
            const response = await fetch(`/restaurant/requisitions/item-details/${itemId}`);
            const result = await response.json();

            if (result.success) {
                updateRowWithItemData(rowElement, result.data);
            }
        } catch (error) {
            console.error('Error fetching item details:', error);
        } finally {
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
        }
    }

    // Update row with fetched item data
    function updateRowWithItemData(rowElement, data) {
        const metricsInput = rowElement.querySelector('.item-metrics');

        // Update metrics field (READ-ONLY)
        if (metricsInput && data.metrics) {
            metricsInput.value = data.metrics;
        }
    }

    // Filter items based on search term (live search)
    function filterItems(searchTerm) {
        if (!searchTerm.trim()) {
            return itemsList;
        }
        return itemsList.filter(item =>
            item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            item.code.toLowerCase().includes(searchTerm.toLowerCase())
        );
    }

    // Render search results dropdown
    function renderDropdown(dropdownElement, items, searchInput, rowElement) {
        if (!dropdownElement) return;

        if (items.length === 0) {
            dropdownElement.innerHTML = '<div class="px-3 py-2 text-gray-500 text-sm">No items found</div>';
            dropdownElement.classList.remove('hidden');
            return;
        }

        dropdownElement.innerHTML = items.map(item => `
            <div class="search-result-item px-3 py-2 cursor-pointer hover:bg-orange-50 border-b border-gray-100 last:border-0 transition-colors" data-id="${item.id}" data-name="${escapeHtml(item.name)}" data-code="${escapeHtml(item.code)}">
                <div class="font-semibold text-sm text-gray-800">${escapeHtml(item.name)}</div>
                <div class="text-xs text-gray-500">Code: ${escapeHtml(item.code)}</div>
            </div>
        `).join('');
        dropdownElement.classList.remove('hidden');

        // Add click handlers
        dropdownElement.querySelectorAll('.search-result-item').forEach(el => {
            el.addEventListener('click', async (e) => {
                e.stopPropagation();
                const itemId = el.dataset.id;
                const itemName = el.dataset.name;
                const itemCode = el.dataset.code;
                const wrapper = searchInput.closest('.item-search-wrapper');
                const selectedBadge = wrapper.querySelector('.selected-item-badge');
                const selectedInfoSpan = selectedBadge.querySelector('.item-info');
                const hiddenId = wrapper.querySelector('.selected-item-id');

                // Update UI
                hiddenId.value = itemId;
                searchInput.value = itemName;
                searchInput.classList.add('hidden');
                selectedInfoSpan.innerHTML = `${escapeHtml(itemName)} <span class="text-xs text-gray-500">(${escapeHtml(itemCode)})</span>`;
                selectedBadge.classList.remove('hidden');
                dropdownElement.classList.add('hidden');

                // Fetch and update item details
                await fetchItemDetails(itemId, rowElement);

                // Enable quantity field
                const quantityInput = rowElement.querySelector('.item-quantity');
                quantityInput.disabled = false;
                quantityInput.required = true;
            });
        });
    }

    // Perform live search
    function performLiveSearch(searchInput, dropdownElement, rowElement) {
        const searchTerm = searchInput.value;
        const filteredItems = filterItems(searchTerm);
        renderDropdown(dropdownElement, filteredItems, searchInput, rowElement);
    }

    // Clear selected item
    function clearSelectedItem(wrapper, rowElement) {
        const searchInput = wrapper.querySelector('.item-search-input');
        const dropdown = wrapper.querySelector('.search-results-dropdown');
        const selectedBadge = wrapper.querySelector('.selected-item-badge');
        const hiddenId = wrapper.querySelector('.selected-item-id');
        const quantityInput = rowElement.querySelector('.item-quantity');
        const metricsInput = rowElement.querySelector('.item-metrics');

        // Reset UI
        searchInput.value = '';
        searchInput.classList.remove('hidden');
        selectedBadge.classList.add('hidden');
        hiddenId.value = '';
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';

        // Reset and disable fields
        quantityInput.disabled = true;
        quantityInput.required = false;
        quantityInput.value = '';
        metricsInput.value = '';
    }

    // Setup search for a row
    function setupRowSearch(rowElement) {
        const wrapper = rowElement.querySelector('.item-search-wrapper');
        if (!wrapper) return;

        const searchInput = wrapper.querySelector('.item-search-input');
        const dropdown = wrapper.querySelector('.search-results-dropdown');
        const clearBtn = wrapper.querySelector('.clear-item-btn');

        // Live search on input with debounce
        searchInput.addEventListener('input', function() {
            const hiddenId = wrapper.querySelector('.selected-item-id');
            if (hiddenId.value) return;

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performLiveSearch(searchInput, dropdown, rowElement);
            }, 300);
        });

        // Show dropdown on focus if no item selected
        searchInput.addEventListener('focus', function() {
            const hiddenId = wrapper.querySelector('.selected-item-id');
            if (!hiddenId.value) {
                performLiveSearch(searchInput, dropdown, rowElement);
            }
        });

        // Clear button handler
        clearBtn.addEventListener('click', () => {
            clearSelectedItem(wrapper, rowElement);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Prevent dropdown from closing when clicking inside
        dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // Escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Create new row
    function createNewRow() {
        const index = rowCounter++;
        const newRow = document.createElement('tr');
        newRow.className = 'item-row border-b border-gray-100';
        newRow.dataset.index = index;

        newRow.innerHTML = `
            <td class="px-3 py-2">
                <div class="relative item-search-wrapper">
                    <input type="text" class="item-search-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-600 focus:ring-1 focus:ring-orange-600" placeholder="Type to search items..." autocomplete="off">
                    <div class="search-results-dropdown absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto hidden"></div>
                    <div class="selected-item-badge hidden flex justify-between items-center bg-orange-50 px-3 py-2 rounded-lg border border-orange-200">
                        <span class="item-info font-semibold text-sm text-orange-800"></span>
                        <button type="button" class="clear-item-btn text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition-colors">✕ Remove</button>
                    </div>
                    <input type="hidden" name="items[${index}][inventory_item_id]" class="selected-item-id" value="">
                    <div class="loading-spinner hidden absolute right-3 top-2">
                        <div class="w-4 h-4 border-2 border-orange-600 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </td>
            <td class="px-3 py-2">
                <input type="number" name="items[${index}][quantity]" step="0.01" class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-600 focus:ring-1 focus:ring-orange-600 disabled:bg-gray-100 disabled:cursor-not-allowed" placeholder="0.00" disabled required>
            </td>
            <td class="px-3 py-2">
                <input type="text" name="items[${index}][metrics]" class="item-metrics w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 cursor-not-allowed" placeholder="Auto-filled" readonly disabled>
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="remove-item bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        return newRow;
    }

    // Remove row
    function removeItemRow(button) {
        const row = button.closest('.item-row');
        if (row) {
            row.remove();
            reindexRows();
        }
    }

    // Reindex rows
    function reindexRows() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, newIndex) => {
            row.dataset.index = newIndex;
            const inputs = row.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${newIndex}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
    }

    // Form validation
    document.getElementById('requisitionForm').addEventListener('submit', function(e) {
        let hasValidItem = false;
        const rows = document.querySelectorAll('.item-row');
        const requisitionType = document.querySelector('select[name="requisition_type"]').value;

        if (!requisitionType) {
            e.preventDefault();
            alert('Please select a requisition type (Daily, Weekly, or Monthly).');
            return false;
        }

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

    // Add item button
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newRow = createNewRow();
        tbody.appendChild(newRow);
        setupRowSearch(newRow);
        newRow.querySelector('.remove-item').addEventListener('click', () => removeItemRow(newRow.querySelector('.remove-item')));
    });

    // Initial row
    if (document.querySelectorAll('.item-row').length === 0) {
        document.getElementById('addItemBtn').click();
    }
</script>

<style>
    .hidden {
        display: none !important;
    }
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endsection
