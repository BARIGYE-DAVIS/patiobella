{{-- resources/views/management/menu-items/create.blade.php --}}

@extends('layouts.management')

@section('title', 'Create Menu Item')

@section('page-title', 'Create New Menu Item')

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
    .hidden {
        display: none;
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
            <h1 class="text-2xl font-bold text-gray-900">Create New Menu Item</h1>
            <p class="text-sm text-gray-500 mt-0.5">Add a new dish to a menu with its recipe ingredients</p>
        </div>
        <div>
            <a href="{{ route('management.menu-items.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Menu Items
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('management.menu-items.store') }}" id="mainForm" novalidate>
        @csrf
        <input type="hidden" name="pricing_mode" id="pricing_mode_hidden" value="{{ old('pricing_mode', 'price') }}">

        {{-- Section 1: Basic Information --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50">
                <span class="flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-600 text-xs font-bold">1</span>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Basic Information</h2>
                    <p class="text-xs text-gray-400">Select menu and basic item details</p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Select Menu <span class="text-red-500">*</span>
                    </label>
                    <select name="menu_id" id="menu_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition" required>
                        <option value="">Select Menu</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" {{ old('menu_id', request()->menu_id) == $menu->id ? 'selected' : '' }}>
                                {{ $menu->name }} ({{ $menu->department->name ?? 'No Department' }})
                            </option>
                        @endforeach
                    </select>
                    @error('menu_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Item Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="item_name"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           placeholder="e.g., Margherita Pizza" value="{{ old('name') }}" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Category</label>
                    <select name="menu_item_category_id" id="category_id"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                        <option value="">No Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('menu_item_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('menu_item_category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none"
                              placeholder="Brief description of this dish...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Allergen Information</label>
                    <textarea name="allergen_info" id="allergen_info" rows="1"
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none"
                              placeholder="e.g., Contains dairy, gluten, nuts">{{ old('allergen_info') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                    <div class="flex items-center gap-6 mt-2">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="accent-orange-500">
                            <span>Active (visible on menu)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Recipe Ingredients --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-600 text-xs font-bold">2</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">Recipe Ingredients</h2>
                        <p class="text-xs text-gray-400">Add ingredients and their quantities</p>
                    </div>
                </div>
                <button type="button" id="addIngredientBtn"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-plus"></i> Add Ingredient
                </button>
            </div>

            <div class="p-6">
                <div id="ingredientsEmptyState" class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-3">
                        <i class="fas fa-pepper-hot text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">No ingredients yet</p>
                    <p class="text-xs text-gray-400 mt-1">Click "Add Ingredient" to start building your recipe</p>
                </div>
                <div id="ingredientsContainer" class="space-y-3"></div>

                {{-- Cost Summary --}}
                <div class="mt-4 flex items-center justify-between px-3 py-2 bg-yellow-50 rounded-lg border border-yellow-100">
                    <span class="text-xs text-gray-600">
                        Material Cost: <strong id="materialCostDisplay" class="text-gray-900">0 UGX</strong>
                    </span>
                    <span class="text-xs text-gray-400">Auto-calculated from ingredients</span>
                </div>
            </div>
        </div>

        {{-- Section 3: Pricing --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-600 text-xs font-bold">3</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">Pricing</h2>
                        <p class="text-xs text-gray-400">Set selling price or desired margin</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 bg-white rounded-lg p-0.5 border border-gray-200">
                    <button type="button" class="pricing-mode-toggle text-xs px-3 py-1 rounded-md transition-all {{ old('pricing_mode', 'price') == 'price' ? 'active' : '' }}" data-mode="price">
                        Set Price
                    </button>
                    <button type="button" class="pricing-mode-toggle text-xs px-3 py-1 rounded-md transition-all {{ old('pricing_mode') == 'margin' ? 'active' : '' }}" data-mode="margin">
                        Set Margin %
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                {{-- Price mode --}}
                <div class="price-mode-field {{ old('pricing_mode') == 'margin' ? 'hidden' : '' }}">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Selling Price (UGX)</label>
                    <input type="number" name="selling_price" id="selling_price"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           placeholder="Enter selling price" step="0.01" min="0" value="{{ old('selling_price') }}">
                    <p class="text-xs text-gray-400 mt-1">Margin will be calculated automatically</p>
                </div>

                {{-- Margin mode --}}
                <div class="margin-mode-field {{ old('pricing_mode') == 'margin' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Desired Margin (%)</label>
                    <input type="number" name="desired_margin" id="desired_margin"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           placeholder="Enter desired margin %" step="0.1" min="0" max="99.9" value="{{ old('desired_margin') }}">
                    <p class="text-xs text-gray-400 mt-1">Selling price will be calculated automatically</p>
                </div>

                {{-- Calculated summary --}}
                <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 rounded-lg px-3 py-2">
                    <span class="text-gray-600">Material Cost: <strong id="summaryMaterialCost" class="text-gray-900">0 UGX</strong></span>
                    <span class="text-gray-600">Margin: <strong id="summaryMargin" class="text-gray-900">0%</strong></span>
                    <span class="text-gray-600">Mark Up: <strong id="summaryMarkUp" class="text-gray-900">0 UGX</strong></span>
                    <span class="text-gray-600">Cost %: <strong id="summaryCostPercent" class="text-gray-900">0%</strong></span>
                </div>
            </div>
        </div>

        {{-- Section 4: Glovo / Delivery Platform --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50">
                <span class="flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-600 text-xs font-bold">4</span>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Glovo / Delivery Platform</h2>
                    <p class="text-xs text-gray-400">Delivery platform pricing</p>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Commission Rate (%) <span class="text-gray-400 font-normal">— charged by Glovo</span>
                    </label>
                    <input type="number" name="glovo_commission_percentage" id="glovo_commission_percentage"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           placeholder="e.g., 20" step="0.1" min="0" value="{{ old('glovo_commission_percentage', 20) }}">
                </div>

                <div class="grid grid-cols-3 gap-3 bg-orange-50 rounded-lg px-3 py-2">
                    <div>
                        <span class="text-xs text-gray-500">Glovo Selling Price</span>
                        <p class="text-sm font-semibold text-orange-700" id="glovoPriceDisplay">0 UGX</p>
                        <p class="text-xs text-gray-400">Selling price + commission%</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Glovo Commission</span>
                        <p class="text-sm font-semibold text-red-600" id="glovoCommissionDisplay">0 UGX</p>
                        <p class="text-xs text-gray-400">Commission% of Glovo price</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Final Margin</span>
                        <p class="text-sm font-semibold text-emerald-600" id="finalMarginDisplay">0 UGX</p>
                        <p class="text-xs text-gray-400">Glovo price − cost − commission</p>
                    </div>
                </div>

                {{-- Hidden fields for storage --}}
                <input type="hidden" name="glovo_selling_price" id="glovo_selling_price_hidden">
                <input type="hidden" name="glovo_commission" id="glovo_commission_hidden">
                <input type="hidden" name="final_margin" id="final_margin_hidden">
                <input type="hidden" name="mark_up" id="mark_up_hidden">
                <input type="hidden" name="age_margins" id="age_margins_hidden">
                <input type="hidden" name="age_cost" id="age_cost_hidden">
                <input type="hidden" name="material_cost" id="material_cost_hidden" value="0">
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('management.menu-items.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-times text-xs"></i> Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <i class="fas fa-save"></i> Create Menu Item
            </button>
        </div>

    </form>
</div>

{{-- Add Ingredient Modal --}}
<div id="ingredientModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center">
                    <i class="fas fa-plus text-white text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Add Ingredient</h3>
                    <p class="text-sm text-gray-500">Select an ingredient and quantity</p>
                </div>
            </div>
            <button type="button" class="closeModal text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ingredient <span class="text-red-500">*</span></label>
                    <select id="ingredient_select" class="w-full text-sm border border-gray-300 rounded-lg">
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
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" step="0.001" min="0.001" id="ingredient_quantity"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           placeholder="e.g., 50, 0.5, 2">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Unit</label>
                    <select id="ingredient_unit" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                        <option value="g">g (grams)</option>
                        <option value="ml">ml (millilitres)</option>
                        <option value="kg">kg (kilograms)</option>
                        <option value="litre">litre</option>
                        <option value="piece">piece</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Wastage %</label>
                    <input type="number" step="0.1" min="0" max="100" id="ingredient_wastage"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           value="0">
                </div>
                <div class="col-span-2">
                    <div class="ingredient-cost-preview text-xs text-gray-500 italic bg-gray-50 p-2 rounded-lg">
                        Select an ingredient and quantity to preview cost
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" class="closeModal px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                    Cancel
                </button>
                <button type="button" id="confirmAddIngredient" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                    Add Ingredient
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Ingredient Row Template --}}
<template id="ingredientRowTemplate">
    <div class="ingredient-row flex items-center gap-3 px-3 py-2 bg-gray-50 rounded-lg border border-gray-200" data-ingredient-idx="__IDX__">
        <input type="hidden" class="ingredient-inventory-id" name="ingredients[__IDX__][inventory_item_id]" value="__INVENTORY_ID__">
        <input type="hidden" class="ingredient-base-unit" name="ingredients[__IDX__][base_unit]" value="__BASE_UNIT__">
        <input type="hidden" class="ingredient-unit-cost" name="ingredients[__IDX__][unit_cost]" value="__UNIT_COST__">
        <input type="hidden" class="ingredient-unit-selected" name="ingredients[__IDX__][unit]" value="__UNIT__">
        <input type="hidden" class="ingredient-wastage-val" name="ingredients[__IDX__][wastage_percentage]" value="__WASTAGE__">
        <input type="hidden" class="ingredient-quantity" name="ingredients[__IDX__][quantity]" value="__QUANTITY__">

        <div class="flex-1">
            <p class="text-sm font-medium text-gray-800 ingredient-name-display">__INGREDIENT_NAME__</p>
            <p class="text-xs text-gray-400 ingredient-unit-display">__QUANTITY__ __UNIT__</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400">Unit cost</p>
            <p class="text-sm font-medium text-gray-700 ingredient-unit-cost-display">__COST_DISPLAY__</p>
        </div>
        <div class="text-right min-w-[100px]">
            <p class="text-xs text-gray-400">Total</p>
            <p class="text-sm font-semibold text-gray-900 ingredient-total-display">__TOTAL_DISPLAY__</p>
        </div>
        <button type="button" class="remove-ingredient w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors flex items-center justify-center">
            <i class="fas fa-trash text-xs"></i>
        </button>
    </div>
</template>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {

    const DRAFT_KEY = 'menu_item_create_draft_v2';
    let ingredientCounter = 0;
    let currentMaterialCost = 0;

    // =====================================================
    // DRAFT - localStorage persistence
    // =====================================================
    function saveDraft() {
        try {
            let draft = {
                menu_id: $('#menu_id').val(),
                name: $('#item_name').val(),
                description: $('#description').val(),
                category_id: $('#category_id').val(),
                allergen_info: $('#allergen_info').val(),
                is_active: $('input[name="is_active"]').is(':checked') ? '1' : '',
                selling_price: $('#selling_price').val(),
                desired_margin: $('#desired_margin').val(),
                glovo_commission: $('#glovo_commission_percentage').val(),
                pricing_mode: $('.pricing-mode-toggle.active').data('mode'),
                ingredients: []
            };

            $('.ingredient-row').each(function(idx) {
                draft.ingredients.push({
                    inventory_item_id: $(this).find('.ingredient-inventory-id').val(),
                    name: $(this).find('.ingredient-name-display').text(),
                    base_unit: $(this).find('.ingredient-base-unit').val(),
                    unit_cost: $(this).find('.ingredient-unit-cost').val(),
                    quantity: $(this).find('.ingredient-quantity').val(),
                    unit: $(this).find('.ingredient-unit-selected').val(),
                    wastage: $(this).find('.ingredient-wastage-val').val()
                });
            });

            localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
        } catch(e) { console.log('Draft save failed'); }
    }

    function loadDraft() {
        try {
            let draft = localStorage.getItem(DRAFT_KEY);
            if (!draft) return;
            let data = JSON.parse(draft);

            if (!$('#menu_id').val() && data.menu_id) $('#menu_id').val(data.menu_id);
            if (!$('#item_name').val() && data.name) $('#item_name').val(data.name);
            if (!$('#description').val() && data.description) $('#description').val(data.description);
            if (!$('#category_id').val() && data.category_id) $('#category_id').val(data.category_id);
            if (!$('#allergen_info').val() && data.allergen_info) $('#allergen_info').val(data.allergen_info);
            if (data.is_active) $('input[name="is_active"]').prop('checked', data.is_active === '1');
            if (data.selling_price) $('#selling_price').val(data.selling_price);
            if (data.desired_margin) $('#desired_margin').val(data.desired_margin);
            if (data.glovo_commission) $('#glovo_commission_percentage').val(data.glovo_commission);

            // Set pricing mode
            if (data.pricing_mode === 'margin') {
                $('.pricing-mode-toggle').removeClass('active');
                $('.pricing-mode-toggle[data-mode="margin"]').addClass('active');
                $('.price-mode-field').addClass('hidden');
                $('.margin-mode-field').removeClass('hidden');
                $('#pricing_mode_hidden').val('margin');
                $('#selling_price').removeAttr('required');
                $('#desired_margin').attr('required', 'required');
            } else {
                $('.pricing-mode-toggle').removeClass('active');
                $('.pricing-mode-toggle[data-mode="price"]').addClass('active');
                $('.price-mode-field').removeClass('hidden');
                $('.margin-mode-field').addClass('hidden');
                $('#pricing_mode_hidden').val('price');
                $('#desired_margin').removeAttr('required');
                $('#selling_price').attr('required', 'required');
            }

            // Restore ingredients
            if (data.ingredients && data.ingredients.length) {
                $.each(data.ingredients, function(idx, ing) {
                    let totalCost = convertToBaseUnit(ing.quantity, ing.unit, ing.base_unit) * ing.unit_cost * (1 + (ing.wastage || 0) / 100);

                    let rowTmpl = $('#ingredientRowTemplate').html();
                    rowTmpl = rowTmpl.replace(/__IDX__/g, ingredientCounter);
                    rowTmpl = rowTmpl.replace(/__INVENTORY_ID__/g, ing.inventory_item_id);
                    rowTmpl = rowTmpl.replace(/__INGREDIENT_NAME__/g, escapeHtml(ing.name));
                    rowTmpl = rowTmpl.replace(/__BASE_UNIT__/g, ing.base_unit);
                    rowTmpl = rowTmpl.replace(/__UNIT_COST__/g, ing.unit_cost);
                    rowTmpl = rowTmpl.replace(/__QUANTITY__/g, ing.quantity);
                    rowTmpl = rowTmpl.replace(/__UNIT__/g, ing.unit);
                    rowTmpl = rowTmpl.replace(/__WASTAGE__/g, ing.wastage || 0);
                    rowTmpl = rowTmpl.replace(/__COST_DISPLAY__/g, Number(ing.unit_cost).toLocaleString() + ' / ' + ing.base_unit);
                    rowTmpl = rowTmpl.replace(/__TOTAL_DISPLAY__/g, fmtUGX(totalCost));

                    $('#ingredientsContainer').append(rowTmpl);
                    $('#ingredientsEmptyState').addClass('hidden');
                    ingredientCounter++;
                });
                calculateTotalMaterialCost();
            }
        } catch(e) {
            localStorage.removeItem(DRAFT_KEY);
        }
    }

    function clearDraft() {
        localStorage.removeItem(DRAFT_KEY);
    }

    // Helper functions
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

    function calculateTotalMaterialCost() {
        let total = 0;
        $('.ingredient-row').each(function() {
            let totalText = $(this).find('.ingredient-total-display').text();
            let cost = parseFloat(totalText.replace(' UGX', '').replace(/,/g, ''));
            if (!isNaN(cost)) total += cost;
        });
        currentMaterialCost = total;
        $('#materialCostDisplay, #summaryMaterialCost').text(fmtUGX(total));
        $('#material_cost_hidden').val(total.toFixed(2));
        updatePricing();
        saveDraft();
        return total;
    }

    // FRONTEND REAL-TIME CALCULATIONS - BOTH DIRECTIONS
    function updatePricing() {
        let pricingMode = $('.pricing-mode-toggle.active').data('mode');
        let sellingPrice = parseFloat($('#selling_price').val()) || 0;
        let desiredMargin = parseFloat($('#desired_margin').val()) || 0;

        if (currentMaterialCost > 0) {
            if (pricingMode === 'margin' && desiredMargin > 0 && desiredMargin < 100) {
                // Calculate selling price from desired margin
                sellingPrice = currentMaterialCost / (1 - desiredMargin / 100);
                $('#selling_price').val(sellingPrice.toFixed(2));
            } else if (pricingMode === 'price' && sellingPrice > 0) {
                // Calculate margin from selling price
                desiredMargin = ((sellingPrice - currentMaterialCost) / sellingPrice) * 100;
                $('#desired_margin').val(desiredMargin.toFixed(2));
            }
        }

        let margin = (sellingPrice > 0 && currentMaterialCost > 0) ? ((sellingPrice - currentMaterialCost) / sellingPrice) * 100 : 0;
        let markUp = sellingPrice > currentMaterialCost ? sellingPrice - currentMaterialCost : 0;
        let costPercent = (sellingPrice > 0 && currentMaterialCost > 0) ? (currentMaterialCost / sellingPrice) * 100 : 0;

        $('#summaryMargin').text(margin.toFixed(1) + '%');
        $('#summaryMarkUp').text(fmtUGX(markUp));
        $('#summaryCostPercent').text(costPercent.toFixed(1) + '%');
        $('#mark_up_hidden').val(markUp.toFixed(2));
        $('#age_margins_hidden').val(margin.toFixed(2));
        $('#age_cost_hidden').val(costPercent.toFixed(2));

        // Glovo calculations
        let commissionPct = parseFloat($('#glovo_commission_percentage').val()) || 0;
        let glovoPrice = sellingPrice * (1 + commissionPct / 100);
        let commission = glovoPrice * (commissionPct / 100);
        let finalMargin = glovoPrice - currentMaterialCost - commission;

        $('#glovoPriceDisplay').text(fmtUGX(glovoPrice));
        $('#glovoCommissionDisplay').text(fmtUGX(commission));
        $('#finalMarginDisplay').text(fmtUGX(finalMargin));
        $('#glovo_selling_price_hidden').val(glovoPrice.toFixed(2));
        $('#glovo_commission_hidden').val(commission.toFixed(2));
        $('#final_margin_hidden').val(finalMargin.toFixed(2));
    }

    // Add ingredient modal handlers
    $('#addIngredientBtn').on('click', function() {
        $('#ingredient_select').val('').trigger('change');
        $('#ingredient_quantity').val('');
        $('#ingredient_unit').val('g');
        $('#ingredient_wastage').val('0');
        $('.ingredient-cost-preview').text('Select an ingredient and quantity to preview cost');
        $('#ingredientModal').removeClass('hidden').addClass('flex');

        $('#ingredient_select').select2({
            dropdownParent: $('#ingredientModal'),
            placeholder: 'Search ingredient...',
            allowClear: true,
            width: '100%'
        });
    });

    $('#ingredient_select, #ingredient_quantity, #ingredient_unit, #ingredient_wastage').on('change keyup', function() {
        let selOpt = $('#ingredient_select').find('option:selected');
        let baseUnit = selOpt.data('base-unit');
        let unitCost = parseFloat(selOpt.data('unit-cost')) || 0;
        let qty = parseFloat($('#ingredient_quantity').val()) || 0;
        let unit = $('#ingredient_unit').val();
        let wastage = parseFloat($('#ingredient_wastage').val()) || 0;

        if ($('#ingredient_select').val() && qty > 0) {
            let total = convertToBaseUnit(qty, unit, baseUnit) * unitCost * (1 + wastage / 100);
            $('.ingredient-cost-preview').text('Estimated cost: ' + fmtUGX(total));
        } else {
            $('.ingredient-cost-preview').text('Select an ingredient and quantity to preview cost');
        }
    });

    $('#confirmAddIngredient').on('click', function() {
        let ingSelect = $('#ingredient_select');
        let selOpt = ingSelect.find('option:selected');
        let inventoryItemId = ingSelect.val();
        let ingredientName = selOpt.data('name');
        let baseUnit = selOpt.data('base-unit');
        let unitCost = parseFloat(selOpt.data('unit-cost')) || 0;
        let qty = parseFloat($('#ingredient_quantity').val()) || 0;
        let unit = $('#ingredient_unit').val();
        let wastage = parseFloat($('#ingredient_wastage').val()) || 0;

        if (!inventoryItemId) { alert('Please select an ingredient.'); return; }
        if (!qty || qty <= 0) { alert('Please enter a valid quantity.'); return; }

        let totalCost = convertToBaseUnit(qty, unit, baseUnit) * unitCost * (1 + wastage / 100);

        let rowTmpl = $('#ingredientRowTemplate').html();
        rowTmpl = rowTmpl.replace(/__IDX__/g, ingredientCounter);
        rowTmpl = rowTmpl.replace(/__INVENTORY_ID__/g, inventoryItemId);
        rowTmpl = rowTmpl.replace(/__INGREDIENT_NAME__/g, escapeHtml(ingredientName));
        rowTmpl = rowTmpl.replace(/__BASE_UNIT__/g, baseUnit);
        rowTmpl = rowTmpl.replace(/__UNIT_COST__/g, unitCost);
        rowTmpl = rowTmpl.replace(/__QUANTITY__/g, qty);
        rowTmpl = rowTmpl.replace(/__UNIT__/g, unit);
        rowTmpl = rowTmpl.replace(/__WASTAGE__/g, wastage);
        rowTmpl = rowTmpl.replace(/__COST_DISPLAY__/g, unitCost.toLocaleString() + ' / ' + baseUnit);
        rowTmpl = rowTmpl.replace(/__TOTAL_DISPLAY__/g, fmtUGX(totalCost));

        $('#ingredientsContainer').append(rowTmpl);
        $('#ingredientsEmptyState').addClass('hidden');
        ingredientCounter++;

        calculateTotalMaterialCost();
        $('#ingredientModal').addClass('hidden').removeClass('flex');
    });

    // Remove ingredient
    $(document).on('click', '.remove-ingredient', function() {
        if (confirm('Remove this ingredient from the recipe?')) {
            $(this).closest('.ingredient-row').remove();
            if ($('.ingredient-row').length === 0) {
                $('#ingredientsEmptyState').removeClass('hidden');
            }
            calculateTotalMaterialCost();
        }
    });

    // Pricing mode toggle - Update both frontend and hidden field
    $('.pricing-mode-toggle').on('click', function() {
        let mode = $(this).data('mode');
        if ($(this).hasClass('active')) return;

        $('.pricing-mode-toggle').removeClass('active');
        $(this).addClass('active');

        // Update hidden field for backend
        $('#pricing_mode_hidden').val(mode);

        if (mode === 'price') {
            $('.price-mode-field').removeClass('hidden');
            $('.margin-mode-field').addClass('hidden');
            // Remove required from margin, add required to price
            $('#desired_margin').removeAttr('required');
            $('#selling_price').attr('required', 'required');
            // If there's a selling price already, calculate margin
            if ($('#selling_price').val()) {
                updatePricing();
            }
        } else {
            $('.price-mode-field').addClass('hidden');
            $('.margin-mode-field').removeClass('hidden');
            // Remove required from price, add required to margin
            $('#selling_price').removeAttr('required');
            $('#desired_margin').attr('required', 'required');
            // If there's a desired margin already, calculate price
            if ($('#desired_margin').val()) {
                updatePricing();
            }
        }
        saveDraft();
    });

    // Input event handlers with real-time calculation
    $('#selling_price').on('change keyup', function() {
        let pricingMode = $('.pricing-mode-toggle.active').data('mode');
        if (pricingMode === 'price') {
            updatePricing();
        }
        saveDraft();
    });

    $('#desired_margin').on('change keyup', function() {
        let v = parseFloat($(this).val()) || 0;
        if (v >= 100) {
            alert('Margin percentage must be less than 100%.');
            $(this).val('');
            return;
        }
        let pricingMode = $('.pricing-mode-toggle.active').data('mode');
        if (pricingMode === 'margin') {
            updatePricing();
        }
        saveDraft();
    });

    $('#glovo_commission_percentage').on('change keyup', function() {
        updatePricing();
        saveDraft();
    });

    // Other fields draft save
    $('#menu_id, #item_name, #description, #category_id, #allergen_info, input[name="is_active"]').on('change keyup click', function() {
        saveDraft();
    });

    // Close modal
    $('.closeModal').on('click', function() {
        $('#ingredientModal').addClass('hidden').removeClass('flex');
    });

    $(window).on('click', function(e) {
        if ($(e.target).is('#ingredientModal')) {
            $('#ingredientModal').addClass('hidden').removeClass('flex');
        }
    });

    // Form submission - ONLY validate active pricing mode
    $('#mainForm').on('submit', function(e) {
        let menuId = $('#menu_id').val();
        let itemName = $('#item_name').val();

        if (!menuId) {
            alert('Please select a menu.');
            e.preventDefault();
            return false;
        }
        if (!itemName) {
            alert('Please enter an item name.');
            e.preventDefault();
            return false;
        }

        let hasIngredients = $('.ingredient-row').length > 0;
        let sellingPrice = parseFloat($('#selling_price').val()) || 0;
        let desiredMargin = parseFloat($('#desired_margin').val()) || 0;
        let pricingMode = $('.pricing-mode-toggle.active').data('mode');

        if (hasIngredients) {
            if (pricingMode === 'price' && sellingPrice <= 0) {
                alert('This item has ingredients. Please enter a selling price.');
                e.preventDefault();
                return false;
            }
            if (pricingMode === 'margin' && desiredMargin <= 0) {
                alert('This item has ingredients. Please enter a desired margin percentage.');
                e.preventDefault();
                return false;
            }
        }

        clearDraft();
        return true;
    });

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;')
                   .replace(/</g, '&lt;')
                   .replace(/>/g, '&gt;')
                   .replace(/"/g, '&quot;')
                   .replace(/'/g, '&#39;');
    }

    // Load draft on page load (after old input fallback from Laravel)
    loadDraft();

    // Also update pricing after draft load
    setTimeout(function() {
        calculateTotalMaterialCost();
    }, 100);

});
</script>
@endpush
