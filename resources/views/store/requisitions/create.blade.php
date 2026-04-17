@extends('layouts.store')

@section('title', 'Create Requisition')

@section('page-title', 'Create New Requisition')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ route('store.requisitions.store') }}" id="requisitionForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="date_needed" class="block text-sm font-medium text-gray-700 mb-1">Date Needed</label>
                <input type="date" name="date_needed" id="date_needed" value="{{ old('date_needed') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Overall Notes</label>
                <textarea name="notes" id="notes" rows="1" 
                          placeholder="Any additional information..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Items Section --}}
        <div class="border-t border-gray-200 pt-6 mt-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Items Requested</h3>

            <div class="overflow-x-auto">
                <table class="w-full" id="itemsTable">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Item Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td class="px-4 py-3">
                                <select class="item-type w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="existing">Existing Item</option>
                                    <option value="new">New Item</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 existing-item-cell">
                                <select name="items[0][item_id]" class="item-select w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->item_code ?? $item->code }})</option>
                                    @endforeach
                                </select>
                                <input type="text" name="items[0][new_item_name]" class="new-item-name hidden w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter item name">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[0][quantity]" step="0.01" 
                                       class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg" 
                                       placeholder="0.00" required>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="items[0][notes]" 
                                       class="item-notes w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="Optional">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" class="remove-item text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" id="addItemBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
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

        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('store.requisitions.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="button" id="previewBtn" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                Preview Requisition
            </button>
        </div>
    </form>
</div>

{{-- Preview Modal --}}
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4 pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Preview Requisition</h3>
            <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Date Needed</p>
                        <p class="font-medium" id="previewDateNeeded">—</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Overall Notes</p>
                        <p class="font-medium" id="previewNotes">—</p>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Items Requested</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Item Name</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 w-32">Quantity</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="previewItemsBody" class="divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
            <button type="button" onclick="closePreview()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Edit
            </button>
            <button type="button" onclick="submitForm()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Confirm & Submit
            </button>
        </div>
    </div>
</div>

<script>
    let itemIndex = 1;

    function toggleItemType(row) {
        const typeSelect = row.querySelector('.item-type');
        const existingSelect = row.querySelector('.item-select');
        const newItemName = row.querySelector('.new-item-name');
        
        if (typeSelect.value === 'existing') {
            existingSelect.classList.remove('hidden');
            newItemName.classList.add('hidden');
            existingSelect.required = true;
            if (newItemName) newItemName.required = false;
        } else {
            existingSelect.classList.add('hidden');
            newItemName.classList.remove('hidden');
            existingSelect.required = false;
            if (newItemName) newItemName.required = true;
        }
    }

    function createNewRow(index) {
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td class="px-4 py-3">
                <select class="item-type w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="existing">Existing Item</option>
                    <option value="new">New Item</option>
                </select>
            <\/td>
            <td class="px-4 py-3">
                <select name="items[${index}][item_id]" class="item-select w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->item_code ?? $item->code }})</option>
                    @endforeach
                </select>
                <input type="text" name="items[${index}][new_item_name]" class="new-item-name hidden w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter item name">
            <\/td>
            <td class="px-4 py-3">
                <input type="number" name="items[${index}][quantity]" step="0.01" 
                       class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg" 
                       placeholder="0.00" required>
            <\/td>
            <td class="px-4 py-3">
                <input type="text" name="items[${index}][notes]" 
                       class="item-notes w-full px-3 py-2 border border-gray-300 rounded-lg"
                       placeholder="Optional">
            <\/td>
            <td class="px-4 py-3 text-center">
                <button type="button" class="remove-item text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            <\/td>
        `;
        return newRow;
    }

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newRow = createNewRow(itemIndex);
        tbody.appendChild(newRow);
        
        const typeSelect = newRow.querySelector('.item-type');
        typeSelect.addEventListener('change', function() {
            toggleItemType(newRow);
        });
        toggleItemType(newRow);
        
        attachRemoveEvents();
        itemIndex++;
    });

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

    // Initialize first row
    const firstRow = document.querySelector('.item-row');
    const firstTypeSelect = firstRow.querySelector('.item-type');
    firstTypeSelect.addEventListener('change', function() {
        toggleItemType(firstRow);
    });
    toggleItemType(firstRow);
    
    attachRemoveEvents();

    // Preview functionality
    function openPreview() {
        const dateNeeded = document.getElementById('date_needed').value;
        const notes = document.getElementById('notes').value;
        
        document.getElementById('previewDateNeeded').innerText = dateNeeded || 'Not specified';
        document.getElementById('previewNotes').innerText = notes || '—';
        
        const items = [];
        const rows = document.querySelectorAll('#itemsBody .item-row');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const typeSelect = row.querySelector('.item-type');
            const itemType = typeSelect.value;
            const quantity = row.querySelector('.item-quantity').value;
            const itemNotes = row.querySelector('.item-notes').value;
            
            let itemName = '';
            if (itemType === 'existing') {
                const selectedOption = row.querySelector('.item-select option:checked');
                itemName = selectedOption ? selectedOption.textContent : '';
            } else {
                const newItemName = row.querySelector('.new-item-name');
                itemName = newItemName ? newItemName.value : '';
            }
            
            if (itemName && quantity) {
                items.push({ type: itemType, name: itemName, quantity: quantity, notes: itemNotes });
            }
        }
        
        const previewBody = document.getElementById('previewItemsBody');
        previewBody.innerHTML = '';
        
        if (items.length === 0) {
            previewBody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No items added</td></tr>';
        } else {
            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                const typeLabel = item.type === 'existing' ? 'Existing' : 'New';
                const typeClass = item.type === 'existing' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800';
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-2 text-sm"><span class="px-2 py-1 text-xs rounded-full ${typeClass}">${typeLabel}</span><\/td>
                    <td class="px-4 py-2 text-sm text-gray-800">${escapeHtml(item.name)}<\/td>
                    <td class="px-4 py-2 text-sm text-gray-800 text-right">${parseFloat(item.quantity).toFixed(2)}<\/td>
                    <td class="px-4 py-2 text-sm text-gray-500">${escapeHtml(item.notes) || '—'}<\/td>
                `;
                previewBody.appendChild(row);
            }
        }
        
        document.getElementById('previewModal').classList.remove('hidden');
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }
    
    function submitForm() {
        const rows = document.querySelectorAll('#itemsBody .item-row');
        let valid = true;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const typeSelect = row.querySelector('.item-type');
            const quantity = row.querySelector('.item-quantity').value;
            
            if (!quantity || parseFloat(quantity) <= 0) {
                alert('Please enter a valid quantity for row ' + (i + 1));
                valid = false;
                break;
            }
            
            if (typeSelect.value === 'existing') {
                const selectedItem = row.querySelector('.item-select').value;
                if (!selectedItem) {
                    alert('Please select an item for row ' + (i + 1));
                    valid = false;
                    break;
                }
            } else {
                const newItemName = row.querySelector('.new-item-name').value;
                if (!newItemName.trim()) {
                    alert('Please enter item name for row ' + (i + 1));
                    valid = false;
                    break;
                }
            }
        }
        
        if (valid) {
            document.getElementById('requisitionForm').submit();
        }
    }
    
    document.getElementById('previewBtn').addEventListener('click', openPreview);
</script>
@endsection