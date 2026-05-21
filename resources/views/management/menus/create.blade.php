{{-- resources/views/management/menus/create.blade.php --}}

@extends('layouts.management')

@section('title', 'Create Menu')

@section('page-title', 'Create New Menu')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        color: #111827;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 6px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234,88,12,0.1);
        outline: none;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #ea580c;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        font-size: 0.875rem;
    }
    .select2-container { width: 100% !important; }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.375rem 0.625rem;
        font-size: 0.875rem;
    }
    .menu-item-card { animation: slideIn 0.25s ease-out; }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .ingredient-row { animation: fadeIn 0.2s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { opacity: 1; }
    .pricing-mode-toggle {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .pricing-mode-toggle.active {
        background-color: #ea580c !important;
        border-color: #ea580c !important;
        color: white !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 pb-16">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Menu</h1>
            <p class="text-sm text-gray-500 mt-0.5">Define menu details, add dishes, and set their recipe ingredients</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" id="clearDraftBtn"
                    class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-red-500 transition-colors"
                    title="Clear saved draft">
                <i class="fas fa-trash-alt"></i> Clear draft
            </button>
            <a href="{{ route('management.menus.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Menus
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('management.menus.store') }}" id="mainForm" novalidate>
        @csrf

        {{-- Section 1: Menu Details --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50">
                <span class="flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-600 text-xs font-bold">1</span>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Menu Details</h2>
                    <p class="text-xs text-gray-400">Basic information about this menu</p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Menu Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="field_name" value="{{ old('name') }}"
                           placeholder="e.g., Breakfast Menu, Wine List"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Department <span class="text-red-500">*</span>
                    </label>
                    <select name="department_id" id="field_department_id"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                            required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" id="field_description" rows="2"
                              placeholder="Brief description of this menu..."
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" id="field_sort_order"
                           value="{{ old('sort_order', 0) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                    <p class="text-gray-400 text-xs mt-1">Lower numbers appear first</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                    <div class="flex items-center gap-6 mt-2">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="radio" name="is_active" value="1"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                   class="accent-orange-500">
                            <span>Active</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="radio" name="is_active" value="0"
                                   {{ old('is_active') == '0' ? 'checked' : '' }}
                                   class="accent-orange-500">
                            <span>Inactive</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Menu Items --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-600 text-xs font-bold">2</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">
                            Menu Items
                            <span id="itemCountBadge"
                                  class="ml-1 px-1.5 py-0.5 text-xs bg-orange-100 text-orange-700 rounded-full font-bold">0</span>
                        </h2>
                        <p class="text-xs text-gray-400">Add dishes and their recipe ingredients</p>
                    </div>
                </div>
                <button type="button" id="addMenuItemBtn"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-plus"></i> Add Menu Item
                </button>
            </div>

            <div class="p-6">
                <div id="emptyState" class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-3">
                        <i class="fas fa-utensils text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">No items yet</p>
                    <p class="text-xs text-gray-400 mt-1">Click "Add Menu Item" to start building your menu</p>
                </div>
                <div id="menuItemsContainer" class="space-y-4"></div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('management.menus.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-times text-xs"></i> Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <i class="fas fa-save"></i> Create Menu &amp; Items
            </button>
        </div>

    </form>
</div>

{{-- ═══════════════════════════ TEMPLATES ═══════════════════════════ --}}

{{-- Menu Item Card Template --}}
<template id="menuItemTemplate">
    <div class="menu-item-card bg-white border border-gray-200 rounded-xl overflow-hidden" data-item-index="__INDEX__">

        {{-- Item Header --}}
        <div class="flex items-start gap-3 px-4 py-3 bg-gray-50 border-b border-gray-200">
            <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center item-number">–</span>
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="sm:col-span-1">
                    <label class="block text-xs text-gray-500 mb-0.5">Item Name <span class="text-red-500">*</span></label>
                    <input type="text" name="items[__INDEX__][name]"
                           placeholder="e.g., Margherita Pizza"
                           class="item-name-field w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Category</label>
                    <select name="items[__INDEX__][menu_item_category_id]"
                            class="item-category-field w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                        <option value="">No Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Active</label>
                    <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer mt-1.5">
                        <input type="checkbox" name="items[__INDEX__][is_active]" checked class="accent-orange-500 item-active-field">
                        Active
                    </label>
                </div>
            </div>
            <button type="button"
                    class="delete-menu-item flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors"
                    title="Delete item">
                <i class="fas fa-trash text-xs"></i>
            </button>
        </div>

        {{-- Item Body --}}
        <div class="px-4 py-3">
            <textarea name="items[__INDEX__][description]" rows="1"
                      placeholder="Optional: short description of this dish..."
                      class="item-description-field w-full px-2.5 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none mb-3 text-gray-600"></textarea>

            {{-- Ingredients --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden mb-3">
                <div class="flex items-center justify-between px-3 py-2 bg-gray-50 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-600 flex items-center gap-1.5">
                        <i class="fas fa-list-ul text-gray-400"></i> Recipe Ingredients
                    </span>
                    <button type="button" class="show-add-ingredient inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus"></i> Add Ingredient
                    </button>
                </div>

                <div class="ingredients-list" data-item-index="__INDEX__">
                    <div class="ingredients-empty px-3 py-4 text-center text-xs text-gray-400">
                        <i class="fas fa-pepper-hot mr-1 text-gray-300"></i> No ingredients added yet
                    </div>
                </div>

                {{-- Add Ingredient Form --}}
                <div class="add-ingredient-form hidden border-t border-dashed border-amber-200 bg-amber-50 px-3 py-3">
                    <p class="text-xs font-semibold text-amber-700 mb-2"><i class="fas fa-plus-circle mr-1"></i>Add Ingredient</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <div class="col-span-2">
                            <label class="block text-xs text-gray-500 mb-0.5">Ingredient</label>
                            <select class="ingredient-select w-full text-sm border border-gray-300 rounded-lg">
                                <option value="">Search ingredient...</option>
                                @foreach($inventoryItems as $item)
                                    <option value="{{ $item->id }}"
                                            data-name="{{ $item->name }}"
                                            data-base-unit="{{ $item->base_unit }}"
                                            data-unit-cost="{{ $item->unit_cost }}">
                                        {{ $item->name }} ({{ $item->base_unit }} — {{ number_format($item->unit_cost, 2) }} UGX)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-0.5">Quantity</label>
                            <input type="number" step="0.001" min="0.001"
                                   class="ingredient-qty-input w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-0.5">Unit</label>
                            <select class="ingredient-unit-select w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg">
                                <option value="g">g (grams)</option>
                                <option value="ml">ml (millilitres)</option>
                                <option value="kg">kg (kilograms)</option>
                                <option value="litre">litre</option>
                                <option value="piece">piece</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-0.5">Wastage %</label>
                            <input type="number" step="0.1" min="0" max="100"
                                   class="ingredient-wastage w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg" value="0">
                        </div>
                        <div class="col-span-2 sm:col-span-3 flex items-end">
                            <div class="ingredient-cost-preview text-xs text-gray-500 italic">
                                Select an ingredient and quantity to preview cost
                            </div>
                        </div>
                        <div class="flex items-end gap-2 justify-end">
                            <button type="button" class="cancel-add-ingredient px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="button" class="confirm-add-ingredient px-3 py-1.5 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
                                Add
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Cost Summary under ingredients --}}
                <div class="flex items-center justify-between px-3 py-2 bg-yellow-50 border-t border-yellow-100">
                    <span class="text-xs text-gray-600">
                        Material Cost: <strong class="material-cost-display text-gray-900">0 UGX</strong>
                    </span>
                    <span class="text-xs text-gray-400">Auto-calculated from ingredients</span>
                </div>
            </div>

            {{-- Pricing Section --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden mb-3">
                <div class="flex items-center justify-between px-3 py-2 bg-gray-50 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-600 flex items-center gap-1.5">
                        <i class="fas fa-tag text-gray-400"></i> Pricing
                    </span>
                    <div class="flex items-center gap-1 bg-white rounded-lg p-0.5 border border-gray-200">
                        <button type="button" class="pricing-mode-toggle text-xs px-3 py-1 rounded-md transition-all active" data-mode="price">
                            Set Price
                        </button>
                        <button type="button" class="pricing-mode-toggle text-xs px-3 py-1 rounded-md transition-all" data-mode="margin">
                            Set Margin %
                        </button>
                    </div>
                </div>

                <div class="p-3 space-y-3">
                    {{-- Price mode --}}
                    <div class="price-mode-field">
                        <label class="block text-xs text-gray-500 mb-0.5">Selling Price (UGX)</label>
                        <input type="number" name="items[__INDEX__][selling_price]"
                               class="item-selling-price w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 transition"
                               placeholder="Enter selling price" step="0.01" min="0">
                        <p class="text-xs text-gray-400 mt-1">Margin will be calculated automatically</p>
                    </div>

                    {{-- Margin mode --}}
                    <div class="margin-mode-field hidden">
                        <label class="block text-xs text-gray-500 mb-0.5">Desired Margin (%)</label>
                        <input type="number" name="items[__INDEX__][desired_margin]"
                               class="item-desired-margin w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 transition"
                               placeholder="Enter desired margin %" step="0.1" min="0" max="99.9">
                        <p class="text-xs text-gray-400 mt-1">Selling price will be calculated automatically</p>
                    </div>

                    {{-- Calculated summary --}}
                    <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 rounded-lg px-3 py-2">
                        <span class="text-gray-600">Material Cost: <strong class="material-cost-display text-gray-900">0 UGX</strong></span>
                        <span class="text-gray-600">Margin: <strong class="calc-margin-display text-gray-900">0%</strong></span>
                        <span class="text-gray-600">Mark Up: <strong class="markup-display text-gray-900">0 UGX</strong></span>
                        <span class="text-gray-600">Cost %: <strong class="cost-percent-display text-gray-900">0%</strong></span>
                    </div>
                </div>
            </div>

            {{-- Glovo Section (Commission Rate Only) --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="flex items-center gap-1.5 px-3 py-2 bg-gray-50 border-b border-gray-100">
                    <i class="fas fa-motorcycle text-orange-500 text-xs"></i>
                    <span class="text-xs font-semibold text-gray-600">Glovo / Delivery Platform</span>
                </div>
                <div class="p-3 space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-0.5">
                            Commission Rate (%)
                            <span class="text-gray-400 font-normal">— charged by Glovo</span>
                        </label>
                        <input type="number" name="items[__INDEX__][glovo_commission_percentage]"
                               class="item-glovo-commission-rate w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 transition"
                               placeholder="e.g., 20" step="0.1" min="0" value="20">
                    </div>

                    <div class="grid grid-cols-3 gap-3 bg-orange-50 rounded-lg px-3 py-2">
                        <div>
                            <span class="text-xs text-gray-500">Glovo Selling Price</span>
                            <p class="text-sm font-semibold text-orange-700 glovo-price-display">0 UGX</p>
                            <p class="text-xs text-gray-400">Selling price + commission%</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Glovo Commission</span>
                            <p class="text-sm font-semibold text-red-600 glovo-commission-display">0 UGX</p>
                            <p class="text-xs text-gray-400">Commission% of Glovo price</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Final Margin</span>
                            <p class="text-sm font-semibold text-emerald-600 final-margin-display">0 UGX</p>
                            <p class="text-xs text-gray-400">Glovo price − cost − commission</p>
                        </div>
                    </div>

                    {{-- Hidden fields for storage --}}
                    <input type="hidden" name="items[__INDEX__][glovo_selling_price]" class="glovo-selling-price-hidden">
                    <input type="hidden" name="items[__INDEX__][glovo_commission]" class="glovo-commission-hidden">
                    <input type="hidden" name="items[__INDEX__][final_margin]" class="final-margin-hidden">
                    <input type="hidden" name="items[__INDEX__][mark_up]" class="markup-hidden">
                    <input type="hidden" name="items[__INDEX__][age_margins]" class="age-margins-hidden">
                    <input type="hidden" name="items[__INDEX__][age_cost]" class="age-cost-hidden">
                    <input type="hidden" name="items[__INDEX__][material_cost]" class="material-cost-hidden" value="0">
                </div>
            </div>
        </div>
    </div>
</template>

{{-- Ingredient Row Template --}}
<template id="ingredientRowTemplate">
    <div class="ingredient-row flex items-center gap-2 px-3 py-2 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors" data-ingredient-idx="__IDX__">
        <input type="hidden" class="ingredient-inventory-id"  name="items[__ITEM_INDEX__][ingredients][__IDX__][inventory_item_id]">
        <input type="hidden" class="ingredient-base-unit"     name="items[__ITEM_INDEX__][ingredients][__IDX__][base_unit]">
        <input type="hidden" class="ingredient-unit-cost"     name="items[__ITEM_INDEX__][ingredients][__IDX__][unit_cost]">
        <input type="hidden" class="ingredient-unit-selected" name="items[__ITEM_INDEX__][ingredients][__IDX__][unit]">
        <input type="hidden" class="ingredient-wastage-val"   name="items[__ITEM_INDEX__][ingredients][__IDX__][wastage_percentage]">

        <div class="flex-1 min-w-0">
            <p class="ingredient-name-display text-xs font-medium text-gray-800 truncate"></p>
            <p class="ingredient-unit-display text-xs text-gray-400"></p>
        </div>
        <div class="w-20 flex-shrink-0">
            <input type="number" step="0.001" min="0.001"
                   class="ingredient-qty-value w-full px-2 py-1 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 text-right"
                   name="items[__ITEM_INDEX__][ingredients][__IDX__][quantity]">
        </div>
        <div class="w-24 flex-shrink-0 text-right">
            <p class="text-xs text-gray-400">Unit cost</p>
            <p class="ingredient-unit-cost-display text-xs font-medium text-gray-700"></p>
        </div>
        <div class="w-24 flex-shrink-0 text-right">
            <p class="text-xs text-gray-400">Total</p>
            <p class="ingredient-total-display text-xs font-semibold text-gray-900"></p>
        </div>
        <button type="button"
                class="remove-ingredient flex-shrink-0 w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 transition-colors flex items-center justify-center">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
</template>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {

    const DRAFT_KEY = 'menu_create_draft_v1';
    let menuItemCounter  = 0;
    let ingredientCounter = {};

    // ── Helpers ───────────────────────────────────────────────────────
    function fmtUGX(n) {
        return Number(n || 0).toLocaleString('en-UG', { maximumFractionDigits: 0 }) + ' UGX';
    }

    function convertToBaseUnit(qty, fromUnit, toBaseUnit) {
        let v = parseFloat(qty);
        if (isNaN(v)) return 0;
        if (toBaseUnit === 'kg')    { if (fromUnit === 'g') return v / 1000;  if (fromUnit === 'kg')    return v; }
        if (toBaseUnit === 'litre') { if (fromUnit === 'ml') return v / 1000; if (fromUnit === 'litre') return v; }
        if (toBaseUnit === 'piece') { if (fromUnit === 'piece') return v; }
        return v;
    }

    // ── Material cost ─────────────────────────────────────────────────
    function calculateMaterialCost(menuItemDiv) {
        let total = 0;
        menuItemDiv.find('.ingredient-row').each(function () {
            let txt = $(this).find('.ingredient-total-display').text().replace(' UGX','').replace(/,/g,'');
            let c   = parseFloat(txt);
            if (!isNaN(c)) total += c;
        });
        return total;
    }

    // ── All pricing + Glovo ───────────────────────────────────────────
    function updateAllPricing(menuItemDiv) {
        let materialCost    = calculateMaterialCost(menuItemDiv);
        let pricingMode     = menuItemDiv.data('pricing-mode') || 'price';
        let sellingPriceIn  = menuItemDiv.find('.item-selling-price');
        let desiredMarginIn = menuItemDiv.find('.item-desired-margin');
        let sellingPrice    = parseFloat(sellingPriceIn.val())  || 0;
        let desiredMargin   = parseFloat(desiredMarginIn.val()) || 0;

        if (materialCost > 0) {
            if (pricingMode === 'margin' && desiredMargin > 0 && desiredMargin < 100) {
                sellingPrice = materialCost / (1 - desiredMargin / 100);
                sellingPriceIn.val(sellingPrice.toFixed(2));
            } else if (pricingMode === 'price' && sellingPrice > 0) {
                desiredMargin = ((sellingPrice - materialCost) / sellingPrice) * 100;
                desiredMarginIn.val(desiredMargin.toFixed(2));
            }
        }

        let margin      = (sellingPrice > 0 && materialCost > 0) ? ((sellingPrice - materialCost) / sellingPrice) * 100 : 0;
        let markUp      = sellingPrice > materialCost ? sellingPrice - materialCost : 0;
        let costPercent = (sellingPrice > 0 && materialCost > 0) ? (materialCost / sellingPrice) * 100 : 0;

        menuItemDiv.find('.material-cost-display').text(fmtUGX(materialCost));
        menuItemDiv.find('.calc-margin-display').text(margin.toFixed(1) + '%');
        menuItemDiv.find('.markup-display').text(fmtUGX(markUp));
        menuItemDiv.find('.cost-percent-display').text(costPercent.toFixed(1) + '%');

        // Glovo calculations
        let commissionPct = parseFloat(menuItemDiv.find('.item-glovo-commission-rate').val()) || 0;
        let glovoPrice    = sellingPrice * (1 + commissionPct / 100);
        let commission    = glovoPrice * (commissionPct / 100);
        let finalMargin   = glovoPrice - materialCost - commission;

        menuItemDiv.find('.glovo-price-display').text(fmtUGX(glovoPrice));
        menuItemDiv.find('.glovo-commission-display').text(fmtUGX(commission));
        menuItemDiv.find('.final-margin-display').text(fmtUGX(finalMargin));

        // Store hidden values
        menuItemDiv.find('.material-cost-hidden').val(materialCost.toFixed(2));
        menuItemDiv.find('.markup-hidden').val(markUp.toFixed(2));
        menuItemDiv.find('.age-margins-hidden').val(margin.toFixed(2));
        menuItemDiv.find('.age-cost-hidden').val(costPercent.toFixed(2));
        menuItemDiv.find('.glovo-selling-price-hidden').val(glovoPrice.toFixed(2));
        menuItemDiv.find('.glovo-commission-hidden').val(commission.toFixed(2));
        menuItemDiv.find('.final-margin-hidden').val(finalMargin.toFixed(2));

        saveDraft();
    }

    // ── Ingredient row totals ─────────────────────────────────────────
    function updateIngredientTotals(row, qty, unit, baseUnit, unitCost, wastage) {
        let qtyBase = convertToBaseUnit(qty, unit, baseUnit);
        let total   = qtyBase * unitCost * (1 + wastage / 100);
        row.find('.ingredient-unit-cost-display').text(Number(unitCost).toLocaleString() + ' / ' + baseUnit);
        row.find('.ingredient-total-display').text(fmtUGX(total));
        return total;
    }

    // ── Item count badge ──────────────────────────────────────────────
    function updateItemCount() {
        let count = $('#menuItemsContainer .menu-item-card').length;
        $('#itemCountBadge').text(count);
        $('#emptyState').toggleClass('hidden', count > 0);
        $('#menuItemsContainer .menu-item-card').each(function (i) {
            $(this).find('.item-number').text(i + 1);
        });
    }

    // ── Add-ingredient cost preview ───────────────────────────────────
    function updateCostPreview(menuItemDiv) {
        let ingSelect = menuItemDiv.find('.ingredient-select');
        let selOpt    = ingSelect.find('option:selected');
        let baseUnit  = selOpt.data('base-unit');
        let unitCost  = parseFloat(selOpt.data('unit-cost')) || 0;
        let qty       = parseFloat(menuItemDiv.find('.ingredient-qty-input').val()) || 0;
        let unit      = menuItemDiv.find('.ingredient-unit-select').val();
        let wastage   = parseFloat(menuItemDiv.find('.ingredient-wastage').val()) || 0;
        let preview   = menuItemDiv.find('.ingredient-cost-preview');

        if (ingSelect.val() && qty > 0) {
            let total = convertToBaseUnit(qty, unit, baseUnit) * unitCost * (1 + wastage / 100);
            preview.text('Estimated cost: ' + fmtUGX(total));
        } else {
            preview.text('Select an ingredient and quantity to preview cost');
        }
    }

    // ── Select2 ──────────────────────────────────────────────────────
    function initSelect2(selectEl, parentEl) {
        if (selectEl.hasClass('select2-hidden-accessible')) selectEl.select2('destroy');
        selectEl.select2({ dropdownParent: parentEl, placeholder: 'Search ingredient...', allowClear: true, width: '100%' });
    }

    // ── Pricing mode switch ───────────────────────────────────────────
    function switchPricingMode(menuItemDiv, mode) {
        menuItemDiv.data('pricing-mode', mode);
        menuItemDiv.find('.pricing-mode-toggle').removeClass('active');
        menuItemDiv.find('.pricing-mode-toggle[data-mode="' + mode + '"]').addClass('active');

        // CRITICAL FIX: Remove 'required' attribute from the hidden margin input
        // to prevent the browser error: "An invalid form control with name='items[0][desired_margin]' is not focusable"
        let marginInput = menuItemDiv.find('.item-desired-margin');
        let priceInput = menuItemDiv.find('.item-selling-price');

        if (mode === 'price') {
            menuItemDiv.find('.price-mode-field').removeClass('hidden');
            menuItemDiv.find('.margin-mode-field').addClass('hidden');
            // Remove required from margin field when hidden
            marginInput.removeAttr('required');
            // Optionally add required to price field
            priceInput.attr('required', 'required');
        } else {
            menuItemDiv.find('.price-mode-field').addClass('hidden');
            menuItemDiv.find('.margin-mode-field').removeClass('hidden');
            // Remove required from price field when hidden
            priceInput.removeAttr('required');
            // Optionally add required to margin field
            marginInput.attr('required', 'required');
        }
        updateAllPricing(menuItemDiv);
    }

    // ══════════════════════════════════════════════════════════════════
    // DRAFT — localStorage persistence
    // ══════════════════════════════════════════════════════════════════
    function saveDraft() {
        try {
            let draft = {
                name:          $('#field_name').val(),
                department_id: $('#field_department_id').val(),
                description:   $('#field_description').val(),
                sort_order:    $('#field_sort_order').val(),
                is_active:     $('input[name="is_active"]:checked').val() || '1',
                items: []
            };

            $('#menuItemsContainer .menu-item-card').each(function () {
                let div  = $(this);
                let idx  = div.data('item-index');
                let item = {
                    pricing_mode:     div.data('pricing-mode') || 'price',
                    name:             div.find('.item-name-field').val(),
                    category_id:      div.find('.item-category-field').val(),
                    description:      div.find('.item-description-field').val(),
                    is_active:        div.find('.item-active-field').is(':checked'),
                    selling_price:    div.find('.item-selling-price').val(),
                    desired_margin:   div.find('.item-desired-margin').val(),
                    commission_pct:   div.find('.item-glovo-commission-rate').val(),
                    ingredients:      []
                };

                div.find('.ingredient-row').each(function () {
                    let row = $(this);
                    item.ingredients.push({
                        inventory_item_id: row.find('.ingredient-inventory-id').val(),
                        name:              row.find('.ingredient-name-display').text(),
                        base_unit:         row.find('.ingredient-base-unit').val(),
                        unit_cost:         row.find('.ingredient-unit-cost').val(),
                        unit:              row.find('.ingredient-unit-selected').val(),
                        qty:               row.find('.ingredient-qty-value').val(),
                        wastage:           row.find('.ingredient-wastage-val').val(),
                        unit_display:      row.find('.ingredient-unit-display').text(),
                        total_display:     row.find('.ingredient-total-display').text()
                    });
                });

                draft.items.push(item);
            });

            localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
        } catch(e) { /* storage unavailable – silent */ }
    }

    function loadDraft() {
        try {
            let raw = localStorage.getItem(DRAFT_KEY);
            if (!raw) return;
            let draft = JSON.parse(raw);

            if (!$('#field_name').val() && draft.name) $('#field_name').val(draft.name);
            if (!$('#field_department_id').val() && draft.department_id) $('#field_department_id').val(draft.department_id);
            if (!$('#field_description').val() && draft.description) $('#field_description').val(draft.description);
            if (draft.sort_order !== undefined) $('#field_sort_order').val(draft.sort_order);
            if (draft.is_active !== undefined) {
                $('input[name="is_active"][value="' + draft.is_active + '"]').prop('checked', true);
            }

            if (draft.items && draft.items.length) {
                draft.items.forEach(function(itemDraft) {
                    restoreItemFromDraft(itemDraft);
                });
            }
        } catch(e) {
            try { localStorage.removeItem(DRAFT_KEY); } catch(e2){}
        }
    }

    function clearDraft() {
        try { localStorage.removeItem(DRAFT_KEY); } catch(e){}
    }

    function restoreItemFromDraft(itemDraft) {
        let idx  = menuItemCounter;
        let tmpl = $('#menuItemTemplate').html().replace(/__INDEX__/g, idx);
        $('#menuItemsContainer').append($(tmpl));
        ingredientCounter[idx] = 0;

        let div = $('#menuItemsContainer .menu-item-card').last();
        div.data('item-index', idx);
        let mode = itemDraft.pricing_mode || 'price';
        div.data('pricing-mode', mode);

        div.find('.item-name-field').val(itemDraft.name || '');
        div.find('.item-category-field').val(itemDraft.category_id || '');
        div.find('.item-description-field').val(itemDraft.description || '');
        div.find('.item-active-field').prop('checked', itemDraft.is_active !== false);
        div.find('.item-glovo-commission-rate').val(itemDraft.commission_pct !== undefined ? itemDraft.commission_pct : 20);

        // Apply correct pricing mode UI and handle required attributes
        if (mode === 'price') {
            div.find('.price-mode-field').removeClass('hidden');
            div.find('.margin-mode-field').addClass('hidden');
            div.find('.item-selling-price').val(itemDraft.selling_price || '');
            div.find('.item-desired-margin').removeAttr('required');
            div.find('.item-selling-price').attr('required', 'required');
        } else {
            div.find('.price-mode-field').addClass('hidden');
            div.find('.margin-mode-field').removeClass('hidden');
            div.find('.item-desired-margin').val(itemDraft.desired_margin || '');
            div.find('.item-selling-price').removeAttr('required');
            div.find('.item-desired-margin').attr('required', 'required');
        }

        // Set active toggle button
        div.find('.pricing-mode-toggle').removeClass('active');
        div.find('.pricing-mode-toggle[data-mode="' + mode + '"]').addClass('active');

        if (itemDraft.ingredients && itemDraft.ingredients.length) {
            itemDraft.ingredients.forEach(function(ing) {
                addIngredientRowFromDraft(div, idx, ing);
            });
            div.find('.ingredients-empty').addClass('hidden');
        }

        let ingForm = div.find('.add-ingredient-form');
        setTimeout(function() {
            initSelect2(div.find('.ingredient-select'), ingForm);
        }, 10);

        bindMenuItemEvents(div);
        updateAllPricing(div);
        menuItemCounter++;
        updateItemCount();
    }

    function addIngredientRowFromDraft(menuItemDiv, itemIdx, ing) {
        let ingIdx  = ingredientCounter[itemIdx] || 0;
        let rowTmpl = $('#ingredientRowTemplate').html()
            .replace(/__IDX__/g, ingIdx)
            .replace(/__ITEM_INDEX__/g, itemIdx);

        let ingList = menuItemDiv.find('.ingredients-list');
        ingList.find('.ingredients-empty').addClass('hidden');
        ingList.append(rowTmpl);

        let row      = ingList.find('.ingredient-row').last();
        let qty      = ing.qty      || 0;
        let unit     = ing.unit     || 'g';
        let baseUnit = ing.base_unit|| 'kg';
        let unitCost = parseFloat(ing.unit_cost) || 0;
        let wastage  = parseFloat(ing.wastage)   || 0;

        row.find('.ingredient-inventory-id').val(ing.inventory_item_id);
        row.find('.ingredient-base-unit').val(baseUnit);
        row.find('.ingredient-unit-cost').val(unitCost);
        row.find('.ingredient-unit-selected').val(unit);
        row.find('.ingredient-wastage-val').val(wastage);
        row.find('.ingredient-name-display').text(ing.name || '');
        row.find('.ingredient-unit-display').text(ing.unit_display || qty + ' ' + unit);
        row.find('.ingredient-qty-value').val(qty);

        updateIngredientTotals(row, qty, unit, baseUnit, unitCost, wastage);

        row.find('.ingredient-qty-value').on('change keyup', function () {
            let newQty = $(this).val();
            row.find('.ingredient-unit-display').text(newQty + ' ' + unit);
            updateIngredientTotals(row, newQty, unit, baseUnit, unitCost, wastage);
            updateAllPricing(menuItemDiv);
        });

        ingredientCounter[itemIdx]++;
    }

    // ══════════════════════════════════════════════════════════════════
    // ADD NEW MENU ITEM
    // ══════════════════════════════════════════════════════════════════
    $('#addMenuItemBtn').on('click', function () {
        let idx  = menuItemCounter;
        let tmpl = $('#menuItemTemplate').html().replace(/__INDEX__/g, idx);
        $('#menuItemsContainer').append($(tmpl));
        ingredientCounter[idx] = 0;

        let div = $('#menuItemsContainer .menu-item-card').last();
        div.data('item-index', idx);
        div.data('pricing-mode', 'price');

        div.find('.pricing-mode-toggle[data-mode="price"]').addClass('active');
        div.find('.price-mode-field').removeClass('hidden');
        div.find('.margin-mode-field').addClass('hidden');

        // Set required attribute for price field, remove from margin
        div.find('.item-selling-price').attr('required', 'required');
        div.find('.item-desired-margin').removeAttr('required');

        let ingForm = div.find('.add-ingredient-form');
        setTimeout(function() {
            initSelect2(div.find('.ingredient-select'), ingForm);
        }, 10);

        bindMenuItemEvents(div);
        updateItemCount();
        menuItemCounter++;
        saveDraft();
        div[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // ══════════════════════════════════════════════════════════════════
    // BIND EVENTS FOR A MENU ITEM CARD
    // ══════════════════════════════════════════════════════════════════
    function bindMenuItemEvents(menuItemDiv) {

        menuItemDiv.find('.delete-menu-item').off('click').on('click', function () {
            if (confirm('Delete this menu item and all its ingredients?')) {
                menuItemDiv.find('.ingredient-select').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
                menuItemDiv.remove();
                updateItemCount();
                saveDraft();
            }
        });

        menuItemDiv.find('.pricing-mode-toggle').off('click').on('click', function (e) {
            e.preventDefault();
            let mode = $(this).data('mode');
            if (mode === menuItemDiv.data('pricing-mode')) return;
            switchPricingMode(menuItemDiv, mode);
        });

        menuItemDiv.find('.item-selling-price').off('change keyup').on('change keyup', function () {
            if (menuItemDiv.data('pricing-mode') === 'price') updateAllPricing(menuItemDiv);
        });

        menuItemDiv.find('.item-desired-margin').off('change keyup').on('change keyup', function () {
            let v = parseFloat($(this).val()) || 0;
            if (v >= 100) {
                alert('Margin percentage must be less than 100%.');
                $(this).val('');
                return;
            }
            if (menuItemDiv.data('pricing-mode') === 'margin') updateAllPricing(menuItemDiv);
        });

        menuItemDiv.find('.item-glovo-commission-rate').off('change keyup').on('change keyup', function () {
            updateAllPricing(menuItemDiv);
        });

        menuItemDiv.find('.item-name-field, .item-description-field, .item-category-field, .item-active-field')
            .off('change keyup').on('change keyup', function () { saveDraft(); });

        menuItemDiv.find('.show-add-ingredient').off('click').on('click', function () {
            let form = menuItemDiv.find('.add-ingredient-form');
            if (form.hasClass('hidden')) {
                form.removeClass('hidden');
                initSelect2(menuItemDiv.find('.ingredient-select'), form);
                menuItemDiv.find('.ingredient-qty-input').val('');
                menuItemDiv.find('.ingredient-unit-select').val('g');
                menuItemDiv.find('.ingredient-wastage').val('0');
                menuItemDiv.find('.ingredient-cost-preview').text('Select an ingredient and quantity to preview cost');
            } else {
                form.addClass('hidden');
            }
        });

        menuItemDiv.find('.cancel-add-ingredient').off('click').on('click', function () {
            menuItemDiv.find('.add-ingredient-form').addClass('hidden');
        });

        menuItemDiv.find('.ingredient-select, .ingredient-qty-input, .ingredient-unit-select, .ingredient-wastage')
            .off('change keyup').on('change keyup', function () { updateCostPreview(menuItemDiv); });

        menuItemDiv.find('.confirm-add-ingredient').off('click').on('click', function () {
            let itemIdx  = menuItemDiv.data('item-index');
            let ingSelect= menuItemDiv.find('.ingredient-select');
            let selOpt   = ingSelect.find('option:selected');

            let inventoryItemId = ingSelect.val();
            let ingredientName  = selOpt.data('name');
            let baseUnit        = selOpt.data('base-unit');
            let unitCost        = parseFloat(selOpt.data('unit-cost')) || 0;
            let qty             = menuItemDiv.find('.ingredient-qty-input').val();
            let unit            = menuItemDiv.find('.ingredient-unit-select').val();
            let wastage         = parseFloat(menuItemDiv.find('.ingredient-wastage').val()) || 0;

            if (!inventoryItemId)              { alert('Please select an ingredient.'); return; }
            if (!qty || parseFloat(qty) <= 0)  { alert('Please enter a valid quantity.'); return; }

            let ingIdx = ingredientCounter[itemIdx] || 0;
            ingredientCounter[itemIdx]++;

            let rowTmpl = $('#ingredientRowTemplate').html()
                .replace(/__IDX__/g, ingIdx)
                .replace(/__ITEM_INDEX__/g, itemIdx);

            let ingList = menuItemDiv.find('.ingredients-list');
            ingList.find('.ingredients-empty').addClass('hidden');
            ingList.append(rowTmpl);

            let newRow = ingList.find('.ingredient-row').last();
            newRow.find('.ingredient-inventory-id').val(inventoryItemId);
            newRow.find('.ingredient-base-unit').val(baseUnit);
            newRow.find('.ingredient-unit-cost').val(unitCost);
            newRow.find('.ingredient-unit-selected').val(unit);
            newRow.find('.ingredient-wastage-val').val(wastage);
            newRow.find('.ingredient-name-display').text(ingredientName);
            newRow.find('.ingredient-unit-display').text(qty + ' ' + unit);
            newRow.find('.ingredient-qty-value').val(qty);

            updateIngredientTotals(newRow, qty, unit, baseUnit, unitCost, wastage);
            updateAllPricing(menuItemDiv);

            newRow.find('.ingredient-qty-value').off('change keyup').on('change keyup', function () {
                let newQty = $(this).val();
                newRow.find('.ingredient-unit-display').text(newQty + ' ' + unit);
                updateIngredientTotals(newRow, newQty, unit, baseUnit, unitCost, wastage);
                updateAllPricing(menuItemDiv);
            });

            menuItemDiv.find('.add-ingredient-form').addClass('hidden');
            ingSelect.val('').trigger('change');
        });

        menuItemDiv.off('click', '.remove-ingredient').on('click', '.remove-ingredient', function () {
            if (confirm('Remove this ingredient from the recipe?')) {
                $(this).closest('.ingredient-row').remove();
                if (menuItemDiv.find('.ingredient-row').length === 0) {
                    menuItemDiv.find('.ingredients-empty').removeClass('hidden');
                }
                updateAllPricing(menuItemDiv);
            }
        });
    }

    // ══════════════════════════════════════════════════════════════════
    // FORM SUBMISSION
    // ══════════════════════════════════════════════════════════════════
    $('#mainForm').on('submit', function (e) {
        let hasItems = $('#menuItemsContainer .menu-item-card').length > 0;

        if (!hasItems) {
            if (!confirm('No menu items added. Create the menu without any items?')) {
                e.preventDefault();
                return false;
            }
        }

        let valid = true;
        $('.menu-item-card').each(function () {
            let div  = $(this);
            let name = div.find('.item-name-field').val();

            if (!name) {
                alert('Every menu item must have a name.');
                valid = false;
                return false;
            }

            let hasIngredients = div.find('.ingredient-row').length > 0;
            if (hasIngredients) {
                let pricingMode = div.data('pricing-mode');
                if (pricingMode === 'price') {
                    let price = parseFloat(div.find('.item-selling-price').val()) || 0;
                    if (price <= 0) {
                        alert('Menu item "' + name + '" has ingredients but no selling price.');
                        valid = false;
                        return false;
                    }
                } else {
                    let margin = parseFloat(div.find('.item-desired-margin').val()) || 0;
                    if (margin <= 0) {
                        alert('Menu item "' + name + '" has ingredients but no desired margin.');
                        valid = false;
                        return false;
                    }
                }
            }
        });

        if (!valid) { e.preventDefault(); return false; }

        clearDraft();
        return true;
    });

    // ══════════════════════════════════════════════════════════════════
    // CLEAR DRAFT BUTTON
    // ══════════════════════════════════════════════════════════════════
    $('#clearDraftBtn').on('click', function () {
        if (confirm('Clear all saved draft data and start fresh?')) {
            clearDraft();
            location.reload();
        }
    });

    // ══════════════════════════════════════════════════════════════════
    // SAVE DRAFT ON MENU DETAIL FIELD CHANGES
    // ══════════════════════════════════════════════════════════════════
    $('#field_name, #field_description, #field_sort_order, #field_department_id')
        .on('change keyup', saveDraft);
    $('input[name="is_active"]').on('change', saveDraft);

    // ══════════════════════════════════════════════════════════════════
    // BOOT — load draft on page ready
    // ══════════════════════════════════════════════════════════════════
    loadDraft();

});
</script>
@endpush
