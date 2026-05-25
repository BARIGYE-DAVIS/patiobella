@extends('layouts.store')

@section('content')
<div class="container-fluid px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h4 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="bg-orange-500 w-2 h-8 rounded-full inline-block"></span>
                Edit Inventory Item
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
            <!-- Card Header with Orange Gradient -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                <h5 class="text-white font-semibold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editing: {{ $item->name }}
                </h5>
            </div>

            <div class="p-6">
                <form action="{{ route('store.inventory.update', $item->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Item Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Item Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-200 rounded-lg transition-all duration-200 @error('name') border-red-500 bg-red-50 @enderror"
                                   value="{{ old('name', $item->name) }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category - Searchable Dropdown -->
                        <div class="relative">
                            <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" class="searchable-dropdown w-full" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Item Code -->
                        <div>
                            <label for="item_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                Item Code <span class="text-xs text-gray-400">(Optional)</span>
                            </label>
                            <input type="text" name="item_code" id="item_code"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-200 rounded-lg transition-all duration-200 @error('item_code') border-red-500 bg-red-50 @enderror"
                                   value="{{ old('item_code', $item->item_code) }}">
                            @error('item_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Barcode -->
                        <div>
                            <label for="barcode" class="block text-sm font-semibold text-gray-700 mb-2">
                                Barcode <span class="text-xs text-gray-400">(Optional)</span>
                            </label>
                            <input type="text" name="barcode" id="barcode"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-200 rounded-lg transition-all duration-200 @error('barcode') border-red-500 bg-red-50 @enderror"
                                   value="{{ old('barcode', $item->barcode) }}">
                            @error('barcode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit of Measure - Searchable Dropdown -->
                        <div class="relative">
                            <label for="default_unit_of_measure_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Unit of Measure / Metrics
                                <span class="text-xs text-gray-400">(How you receive/purchase)</span>
                            </label>
                            <select name="default_unit_of_measure_id" id="default_unit_of_measure_id" class="searchable-dropdown w-full">
                                <option value="">Select Unit</option>
                                @foreach($unitsOfMeasure as $unit)
                                    <option value="{{ $unit->id }}" {{ old('default_unit_of_measure_id', $item->default_unit_of_measure_id) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} @if($unit->symbol) ({{ $unit->symbol }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('default_unit_of_measure_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Base Unit - Searchable Dropdown -->
                        <div class="relative">
                            <label for="base_unit" class="block text-sm font-semibold text-gray-700 mb-2">
                                Base Unit <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-400">(Smallest unit for counting)</span>
                            </label>
                            <select name="base_unit" id="base_unit" class="searchable-dropdown w-full" required>
                                <option value="">Select Base Unit</option>
                                @foreach($existingBaseUnits as $baseUnitValue)
                                    <option value="{{ $baseUnitValue }}" {{ old('base_unit', $item->base_unit) == $baseUnitValue ? 'selected' : '' }}>
                                        {{ ucfirst($baseUnitValue) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('base_unit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Empty Bottle Weight -->
                        <div>
                            <label for="empty_bottle_weight" class="block text-sm font-semibold text-gray-700 mb-2">
                                Empty Container Weight (kg) <span class="text-xs text-gray-400">- Optional</span>
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

                        <!-- Unit Cost -->
                        <div>
                            <label for="unit_cost" class="block text-sm font-semibold text-gray-700 mb-2">
                                Unit Cost
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">UGX</span>
                                <input type="number" name="unit_cost" id="unit_cost"
                                       class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-200 rounded-lg transition-all duration-200 @error('unit_cost') border-red-500 bg-red-50 @enderror"
                                       value="{{ old('unit_cost', $item->unit_cost) }}" step="0.01" min="0">
                            </div>
                            @error('unit_cost')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="selling_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                Selling Price
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

                    <!-- Notes Section -->
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
                            Cancel Changes
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center items-center px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition font-medium shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Item
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

    /* CRITICAL FIX: Dropdown width control */
    .select2-container {
        width: 100% !important;
        min-width: 180px !important;
        max-width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        border: 2px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.375rem 0;
        height: auto !important;
        transition: all 0.2s ease;
        background-color: white;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2rem !important;
        padding-left: 1rem !important;
        padding-right: 2rem !important;
        color: #374151 !important;
        font-size: 0.95rem;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.75rem !important;
        right: 0.5rem !important;
        width: 2rem;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #f97316 !important;
        outline: none !important;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1) !important;
    }

    /* CRITICAL FIX: Dropdown menu width - PREVENT WIDE DROPDOWN */
    .select2-dropdown {
        border-color: #e5e7eb !important;
        border-radius: 0.5rem !important;
        overflow: hidden !important;
        width: auto !important;
        min-width: 200px !important;
        max-width: 350px !important;
        position: absolute !important;
    }

    /* Make dropdown exactly match the select element width */
    .select2-container--open .select2-dropdown--below {
        width: auto !important;
        min-width: 220px !important;
        max-width: 350px !important;
    }

    /* Dropdown results container - add scroll for many items, no horizontal scroll */
    .select2-results__options {
        max-height: 250px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    /* Ensure text doesn't overflow in dropdown items */
    .select2-results__option {
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: #f97316 !important;
    }

    .select2-results__option[aria-selected="true"] {
        background-color: #fff7ed !important;
        color: #f97316 !important;
        font-weight: 500;
    }

    /* Search box styling */
    .select2-search--dropdown {
        padding: 0.5rem !important;
    }

    .select2-search__field {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding: 0.5rem !important;
        width: 100% !important;
        font-size: 0.9rem;
        box-sizing: border-box !important;
    }

    .select2-search__field:focus {
        border-color: #f97316 !important;
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.1) !important;
    }

    /* Remove number input spinners */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 0.5;
    }

    input[type="number"]:hover::-webkit-inner-spin-button,
    input[type="number"]:hover::-webkit-outer-spin-button {
        opacity: 1;
    }

    /* Focus ring for all inputs */
    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
    }

    /* Ensure selects don't overflow their container */
    .relative {
        position: relative;
    }

    /* Hide any horizontal overflow */
    body {
        overflow-x: hidden !important;
    }

    .container-fluid {
        overflow-x: hidden !important;
    }
</style>

<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 with fixed width constraints
        $('.searchable-dropdown').each(function() {
            $(this).select2({
                placeholder: $(this).find('option:first').text() || 'Search and select...',
                allowClear: true,
                width: 'resolve',
                dropdownAutoWidth: false,
                minimumResultsForSearch: 3,
                containerCssClass: 'select2-container--fixed-width',
                dropdownCssClass: 'select2-dropdown--fixed-width'
            });
        });

        // Prevent dropdown from becoming too wide
        $(document).on('select2:open', () => {
            setTimeout(() => {
                const dropdown = document.querySelector('.select2-dropdown');
                if (dropdown) {
                    const selectContainer = document.querySelector('.select2-container');
                    if (selectContainer) {
                        const containerWidth = selectContainer.offsetWidth;
                        dropdown.style.width = `${Math.min(containerWidth + 20, 350)}px`;
                        dropdown.style.maxWidth = '350px';
                    }
                }
            }, 10);
        });
    });
</script>
@endsection
