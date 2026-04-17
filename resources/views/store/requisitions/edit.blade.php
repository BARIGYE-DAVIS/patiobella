@extends('layouts.store')

@section('title', 'Edit Requisition')

@section('page-title', 'Edit Requisition')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ route('store.requisitions.update', $requisition->id) }}" id="requisitionForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="date_needed" class="block text-sm font-medium text-gray-700 mb-1">Date Needed</label>
                <input type="date" name="date_needed" id="date_needed" value="{{ old('date_needed', $requisition->date_needed ? $requisition->date_needed->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Overall Notes</label>
                <textarea name="notes" id="notes" rows="1" 
                          placeholder="Any additional information..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $requisition->notes) }}</textarea>
            </div>
        </div>

        {{-- Items Section --}}
        <div class="border-t border-gray-200 pt-6 mt-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Items Requested</h3>

            <div class="overflow-x-auto">
                <table class="w-full" id="itemsTable">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach($requisition->items as $index => $item)
                        <tr class="item-row">
                            <td class="px-4 py-3">
                                <input type="text" name="items[{{ $index }}][name]" 
                                       value="{{ old('items.' . $index . '.name', $item->inventory_item_id ? ($item->inventoryItem->name ?? '') : $item->item_name) }}"
                                       class="item-name w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="Item name" required>
                                @if($item->inventory_item_id)
                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->inventory_item_id }}">
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[{{ $index }}][quantity]" step="0.01" 
                                       value="{{ old('items.' . $index . '.quantity', $item->quantity_requested) }}"
                                       class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg" 
                                       placeholder="0.00" required>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="items[{{ $index }}][notes]" 
                                       value="{{ old('items.' . $index . '.notes', $item->notes) }}"
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="button" id="addExistingItemBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Existing Item
                </button>
                <button type="button" id="addNewItemBtn" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Item
                </button>
            </div>
        </div>

        @error('items')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror

        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('store.requisitions.show', $requisition->id) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Update Requisition
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = {{ $requisition->items->count() }};

    function createExistingItemRow(index) {
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td class="px-4 py-3">
                <select name="items[${index}][item_id]" class="item-select w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">Select Item</option>
                    @foreach($items as $inventoryItem)
                        <option value="{{ $inventoryItem->id }}">{{ $inventoryItem->name }} ({{ $inventoryItem->item_code ?? $inventoryItem->code }})</option>
                    @endforeach
                </select>
                <input type="hidden" name="items[${index}][name]" value="">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${index}][quantity]" step="0.01" 
                       class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg" 
                       placeholder="0.00" required>
            </td>
            <td class="px-4 py-3">
                <input type="text" name="items[${index}][notes]" 
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
        `;
        return newRow;
    }

    function createNewItemRow(index) {
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td class="px-4 py-3">
                <input type="text" name="items[${index}][name]" 
                       class="item-name w-full px-3 py-2 border border-gray-300 rounded-lg"
                       placeholder="Enter new item name" required>
                <input type="hidden" name="items[${index}][item_id]" value="">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${index}][quantity]" step="0.01" 
                       class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg" 
                       placeholder="0.00" required>
            </td>
            <td class="px-4 py-3">
                <input type="text" name="items[${index}][notes]" 
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
        `;
        return newRow;
    }

    document.getElementById('addExistingItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newRow = createExistingItemRow(itemIndex);
        tbody.appendChild(newRow);
        attachRemoveEvents();
        itemIndex++;
    });

    document.getElementById('addNewItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newRow = createNewItemRow(itemIndex);
        tbody.appendChild(newRow);
        attachRemoveEvents();
        itemIndex++;
    });

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.removeEventListener('click', removeItem);
            btn.addEventListener('click', removeItem);
        });
    }

    function removeItem(e) {
        const row = e.target.closest('.item-row');
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
        } else {
            alert('You must have at least one item.');
        }
    }

    attachRemoveEvents();
</script>
@endsection