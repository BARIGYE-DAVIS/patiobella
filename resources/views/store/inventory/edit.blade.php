@extends('layouts.store')

@section('content')
<div class="container-fluid px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h4 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="bg-orange-500 w-2 h-8 rounded-full inline-block"></span>
                Inventory Item Details
            </h4>
            <a href="{{ route('store.inventory.show', $item->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-150 ease-in-out text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Item
            </a>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                <h5 class="text-white font-semibold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    {{ $item->name }}
                </h5>
                <p class="text-orange-100 text-xs mt-1">Stock costs are managed per batch | Item master data is read-only</p>
            </div>

            <div class="p-6">
                <form action="{{ route('store.inventory.update', $item->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Read-only Item Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Item Name - Read Only -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Item Name
                            </label>
                            <input type="text" value="{{ $item->name }}"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                                   readonly disabled>
                        </div>

                        <!-- Item Code - Read Only -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Item Code
                            </label>
                            <input type="text" value="{{ $item->item_code }}"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                                   readonly disabled>
                        </div>

                        <!-- Barcode - Read Only -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Barcode
                            </label>
                            <input type="text" value="{{ $item->barcode ?? '—' }}"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                                   readonly disabled>
                        </div>

                        <!-- Category - Read Only -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Category
                            </label>
                            <input type="text" value="{{ $item->category->name ?? '—' }}"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                                   readonly disabled>
                        </div>

                        <!-- Unit of Measurement - Read Only -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Unit of Measurement
                            </label>
                            <input type="text" value="{{ ucfirst($item->unit_of_measurement ?? $item->base_unit ?? 'piece') }}"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                                   readonly disabled>
                        </div>
                    </div>

                    <!-- Editable Fields Section -->
                    <div class="border-t border-gray-200 pt-4 mt-2">
                        <h6 class="text-sm font-semibold text-orange-600 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Editable Fields
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Empty Bottle Weight - EDITABLE -->
                            <div>
                                <label for="empty_bottle_weight" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Empty Container Weight (kg)
                                </label>
                                <div class="relative">
                                    <input type="number" name="empty_bottle_weight" id="empty_bottle_weight"
                                           class="w-full px-4 py-2.5 border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-200 rounded-lg transition-all duration-200 @error('empty_bottle_weight') border-red-500 bg-red-50 @enderror"
                                           value="{{ old('empty_bottle_weight', $item->empty_bottle_weight) }}"
                                           step="0.000001" min="0" max="999999.999999" placeholder="e.g., 0.450">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">kg</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">For bottle/container items: weight of empty container</p>
                                @error('empty_bottle_weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Selling Price - EDITABLE (optional) -->
                            <div class="hidden">
                                <label for="selling_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Selling Price <span class="text-xs text-gray-400">(Optional)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">UGX</span>
                                    <input type="number" name="selling_price" id="selling_price"
                                           class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-200 rounded-lg transition-all duration-200 @error('selling_price') border-red-500 bg-red-50 @enderror"
                                           value="{{ old('selling_price', $item->selling_price) }}" step="0.01" min="0">
                                </div>
                                @error('selling_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notes - EDITABLE -->
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            Notes <span class="text-xs text-gray-400">(Optional)</span>
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full px-4 py-2.5 border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-200 rounded-lg transition-all duration-200 @error('notes') border-red-500 bg-red-50 @enderror"
                                  placeholder="Additional information about this item...">{{ old('notes', $item->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Information Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">Important Notes</p>
                                <ul class="text-xs text-blue-600 mt-1 space-y-1 list-disc list-inside">
                                    <li>Unit cost is managed at the <strong>batch level</strong> - each batch can have its own cost</li>
                                    <li>Stock quantities are managed through <strong>Goods Received Notes (GRN)</strong> and batch adjustments</li>
                                    <li>To update item name, code, or category, please contact system administrator</li>
                                    <li>View batch details and costs in the <strong>Stock Batches</strong> section on the item details page</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Active Status Toggle -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                <input type="checkbox" name="is_active" id="is_active"
                                       class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                       value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                            </div>
                            <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                                Item is <span class="font-bold {{ $item->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-400">Inactive items won't appear in POS selections</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-between pt-4 border-t border-gray-200">
                        <a href="{{ route('store.inventory.show', $item->id) }}"
                           class="inline-flex justify-center items-center px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center items-center px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition font-medium shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Toggle Switch Styling */
    .toggle-checkbox {
        left: -2px;
        top: -2px;
        transition: all 0.2s ease-in-out;
    }
    .toggle-checkbox:checked {
        left: 20px;
        border-color: #f97316;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #f97316;
    }
    .toggle-label {
        transition: background-color 0.2s ease;
    }

    /* Disabled input styling */
    input:disabled, input[readonly] {
        cursor: not-allowed;
        background-color: #f9fafb;
    }
</style>

<script>
    // Simple toggle script
    const toggleCheckbox = document.getElementById('is_active');
    const toggleLabel = document.querySelector('label[for="is_active"]');
    if (toggleCheckbox) {
        toggleCheckbox.addEventListener('change', function() {
            // Visual feedback handled by CSS
        });
    }
</script>

@endsectiona
