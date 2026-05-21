{{-- resources/views/management/menus/edit.blade.php --}}

@extends('layouts.management')

@section('title', 'Edit Menu')

@section('page-title', 'Edit Menu: ' . $menu->name)

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
    .btn-primary {
        background: #ea580c;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: #c2410c;
    }
    .btn-secondary {
        background: #6b7280;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
    }
    .btn-secondary:hover {
        background: #4b5563;
    }
    .btn-success {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
    }
    .btn-success:hover {
        background: #059669;
    }
    .btn-danger {
        background: #ef4444;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
    }
    .btn-danger:hover {
        background: #dc2626;
    }
    .btn-warning {
        background: #f59e0b;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
    }
    .btn-warning:hover {
        background: #d97706;
    }
    .menu-item-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .menu-item-header {
        padding: 1rem;
        background: white;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .menu-item-body {
        padding: 1rem;
    }
    .ingredient-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }
    .ingredient-name {
        flex: 2;
        min-width: 150px;
    }
    .ingredient-qty {
        flex: 1;
        min-width: 80px;
    }
    .ingredient-unit {
        flex: 1;
        min-width: 80px;
    }
    .ingredient-cost {
        flex: 1;
        min-width: 100px;
        font-size: 0.875rem;
    }
    .ingredient-total {
        flex: 1;
        min-width: 100px;
        font-weight: bold;
        font-size: 0.875rem;
    }
    .ingredient-action {
        flex: 0.5;
    }
    .add-ingredient-form {
        background: #f3f4f6;
        padding: 0.75rem;
        border-radius: 8px;
        margin-top: 0.75rem;
    }
    .cost-summary {
        background: #fef3c7;
        padding: 0.75rem;
        border-radius: 8px;
        margin-top: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .badge-success {
        background: #d1fae5;
        color: #065f46;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-warning {
        background: #fed7aa;
        color: #92400e;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1.5rem;
    }
    @media (min-width: 768px) {
        .grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .md\:col-span-2 {
            grid-column: span 2;
        }
    }
    .flex {
        display: flex;
    }
    .items-center {
        align-items: center;
    }
    .justify-between {
        justify-content: space-between;
    }
    .justify-end {
        justify-content: flex-end;
    }
    .gap-2 {
        gap: 0.5rem;
    }
    .gap-3 {
        gap: 0.75rem;
    }
    .gap-6 {
        gap: 1.5rem;
    }
    .mt-1 {
        margin-top: 0.25rem;
    }
    .mt-2 {
        margin-top: 0.5rem;
    }
    .mt-6 {
        margin-top: 1.5rem;
    }
    .mb-2 {
        margin-bottom: 0.5rem;
    }
    .mb-3 {
        margin-bottom: 0.75rem;
    }
    .mr-1 {
        margin-right: 0.25rem;
    }
    .mr-2 {
        margin-right: 0.5rem;
    }
    .mr-6 {
        margin-right: 1.5rem;
    }
    .ml-2 {
        margin-left: 0.5rem;
    }
    .pt-4 {
        padding-top: 1rem;
    }
    .border-t {
        border-top-width: 1px;
    }
    .border-gray-200 {
        border-color: #e5e7eb;
    }
    .text-lg {
        font-size: 1.125rem;
    }
    .text-xs {
        font-size: 0.75rem;
    }
    .text-sm {
        font-size: 0.875rem;
    }
    .font-semibold {
        font-weight: 600;
    }
    .text-gray-800 {
        color: #1f2937;
    }
    .text-gray-500 {
        color: #6b7280;
    }
    .text-gray-400 {
        color: #9ca3af;
    }
    .text-red-500 {
        color: #ef4444;
    }
    .text-emerald-600 {
        color: #059669;
    }
    .inline-flex {
        display: inline-flex;
    }
    .select2-container {
        width: 100% !important;
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
            <h1 class="text-2xl font-bold text-gray-900">Edit Menu: {{ $menu->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Edit menu details, add dishes, and set their recipe ingredients</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('management.menus.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Menus
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('management.menus.update', $menu->id) }}" id="mainForm" novalidate>
        @csrf
        @method('PUT')

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
                    <input type="text" name="name" id="field_name" value="{{ old('name', $menu->name) }}"
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
                                {{ old('department_id', $menu->department_id) == $department->id ? 'selected' : '' }}>
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
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none">{{ old('description', $menu->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" id="field_sort_order"
                           value="{{ old('sort_order', $menu->sort_order) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                    <p class="text-gray-400 text-xs mt-1">Lower numbers appear first</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                    <div class="flex items-center gap-6 mt-2">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="radio" name="is_active" value="1"
                                   {{ old('is_active', $menu->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                                   class="accent-orange-500">
                            <span>Active</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="radio" name="is_active" value="0"
                                   {{ old('is_active', $menu->is_active ? '1' : '0') == '0' ? 'checked' : '' }}
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
                <i class="fas fa-save"></i> Update Menu &amp; Items
            </button>
        </div>

    </form>
</div>

{{-- ═══════════════════════════ TEMPLATES ═══════════════════════════ --}}

{{-- Menu Item Card Template --}}
<template id="menuItemTemplate">
    <div class="menu-item-card bg-white border border-gray-200 rounded-xl overflow-hidden" data-item-index="__INDEX__" data-item-id="__ITEM_ID__">

        {{-- Item Header --}}
        <div class="flex items-start gap-3 px-4 py-3 bg-gray-50 border-b border-gray-200">
            <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center item-number">–</span>
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="sm:col-span-1">
                    <label class="block text-xs text-gray-500 mb-0.5">Item Name <span class="text-red-500">*</span></label>
                    <input type="text" name="items[__INDEX__][name]"
                           placeholder="e.g., Margherita Pizza"
                           class="item-name-field w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                           value="__NAME__"
                           required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Category</label>
                    <select name="items[__INDEX__][menu_item_category_id]"
                            class="item-category-field w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                        <option value="">No Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" __CATEGORY_SELECTED_{{ $category->id }}__>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Active</label>
                    <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer mt-1.5">
                        <input type="checkbox" name="items[__INDEX__][is_active]" __ACTIVE_CHECKED__ class="accent-orange-500 item-active-field">
                        Active
                    </label>
                </div>
            </div>
            <input type="hidden" name="items[__INDEX__][id]" value="__ITEM_ID__">
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
                      class="item-description-field w-full px-2.5 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none mb-3 text-gray-600">__DESCRIPTION__</textarea>

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
                        <button type="button" class="pricing-mode-toggle text-xs px-3 py-1 rounded-md transition-all" data-mode="price">
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
                               placeholder="Enter selling price" step="0.01" min="0" value="__PRICE__">
                        <p class="text-xs text-gray-400 mt-1">Margin will be calculated automatically</p>
                    </div>

                    {{-- Margin mode --}}
                    <div class="margin-mode-field hidden">
                        <label class="block text-xs text-gray-500 mb-0.5">Desired Margin (%)</label>
                        <input type="number" name="items[__INDEX__][desired_margin]"
                               class="item-desired-margin w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 transition"
                               placeholder="Enter desired margin %" step="0.1" min="0" max="99.9" value="__DESIRED_MARGIN__">
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

            {{-- Glovo Section --}}
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
                               placeholder="e.g., 20" step="0.1" min="0" value="__COMMISSION_PCT__">
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
    <div class="ingredient-row flex items-center gap-2 px-3 py-2 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors" data-ingredient-idx="__IDX__" data-recipe-id="__RECIPE_ID__">
        <input type="hidden" class="ingredient-inventory-id"  name="items[__ITEM_INDEX__][ingredients][__IDX__][inventory_item_id]" value="__INVENTORY_ID__">
        <input type="hidden" class="ingredient-base-unit"     name="items[__ITEM_INDEX__][ingredients][__IDX__][base_unit]" value="__BASE_UNIT__">
        <input type="hidden" class="ingredient-unit-cost"     name="items[__ITEM_INDEX__][ingredients][__IDX__][unit_cost]" value="__UNIT_COST__">
        <input type="hidden" class="ingredient-unit-selected" name="items[__ITEM_INDEX__][ingredients][__IDX__][unit]" value="__UNIT__">
        <input type="hidden" class="ingredient-wastage-val"   name="items[__ITEM_INDEX__][ingredients][__IDX__][wastage_percentage]" value="__WASTAGE__">
        <input type="hidden" class="ingredient-recipe-id"     name="items[__ITEM_INDEX__][ingredients][__IDX__][recipe_id]" value="__RECIPE_ID__">

        <div class="flex-1 min-w-0">
            <p class="ingredient-name-display text-xs font-medium text-gray-800 truncate">__INGREDIENT_NAME__</p>
            <p class="ingredient-unit-display text-xs text-gray-400">__UNIT_DISPLAY__</p>
        </div>
        <div class="w-20 flex-shrink-0">
            <input type="number" step="0.001" min="0.001"
                   class="ingredient-qty-value w-full px-2 py-1 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 text-right"
                   name="items[__ITEM_INDEX__][ingredients][__IDX__][quantity]" value="__QUANTITY__">
        </div>
        <div class="w-24 flex-shrink-0 text-right">
            <p class="text-xs text-gray-400">Unit cost</p>
            <p class="ingredient-unit-cost-display text-xs font-medium text-gray-700">__COST_DISPLAY__</p>
        </div>
        <div class="w-24 flex-shrink-0 text-right">
            <p class="text-xs text-gray-400">Total</p>
            <p class="ingredient-total-display text-xs font-semibold text-gray-900">__TOTAL_DISPLAY__</p>
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

    let menuItemCounter = 0;
    let ingredientCounter = {};

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

    function calculateMaterialCost(menuItemDiv) {
        let total = 0;
        menuItemDiv.find('.ingredient-row').each(function () {
            let txt = $(this).find('.ingredient-total-display').text().replace(' UGX','').replace(/,/g,'');
            let c   = parseFloat(txt);
            if (!isNaN(c)) total += c;
        });
        return total;
    }

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

        let commissionPct = parseFloat(menuItemDiv.find('.item-glovo-commission-rate').val()) || 0;
        let glovoPrice    = sellingPrice * (1 + commissionPct / 100);
        let commission    = glovoPrice * (commissionPct / 100);
        let finalMargin   = glovoPrice - materialCost - commission;

        menuItemDiv.find('.glovo-price-display').text(fmtUGX(glovoPrice));
        menuItemDiv.find('.glovo-commission-display').text(fmtUGX(commission));
        menuItemDiv.find('.final-margin-display').text(fmtUGX(finalMargin));

        menuItemDiv.find('.material-cost-hidden').val(materialCost.toFixed(2));
        menuItemDiv.find('.markup-hidden').val(markUp.toFixed(2));
        menuItemDiv.find('.age-margins-hidden').val(margin.toFixed(2));
        menuItemDiv.find('.age-cost-hidden').val(costPercent.toFixed(2));
        menuItemDiv.find('.glovo-selling-price-hidden').val(glovoPrice.toFixed(2));
        menuItemDiv.find('.glovo-commission-hidden').val(commission.toFixed(2));
        menuItemDiv.find('.final-margin-hidden').val(finalMargin.toFixed(2));
    }

    function updateIngredientTotals(row, qty, unit, baseUnit, unitCost, wastage) {
        let qtyBase = convertToBaseUnit(qty, unit, baseUnit);
        let total   = qtyBase * unitCost * (1 + wastage / 100);
        row.find('.ingredient-unit-cost-display').text(Number(unitCost).toLocaleString() + ' / ' + baseUnit);
        row.find('.ingredient-total-display').text(fmtUGX(total));
        return total;
    }

    function updateItemCount() {
        let count = $('#menuItemsContainer .menu-item-card').length;
        $('#itemCountBadge').text(count);
        $('#emptyState').toggleClass('hidden', count > 0);
        $('#menuItemsContainer .menu-item-card').each(function (i) {
            $(this).find('.item-number').text(i + 1);
        });
    }

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

    function initSelect2(selectEl, parentEl) {
        if (selectEl.hasClass('select2-hidden-accessible')) selectEl.select2('destroy');
        selectEl.select2({ dropdownParent: parentEl, placeholder: 'Search ingredient...', allowClear: true, width: '100%' });
    }

    function switchPricingMode(menuItemDiv, mode) {
        menuItemDiv.data('pricing-mode', mode);
        menuItemDiv.find('.pricing-mode-toggle').removeClass('active');
        menuItemDiv.find('.pricing-mode-toggle[data-mode="' + mode + '"]').addClass('active');

        let marginInput = menuItemDiv.find('.item-desired-margin');
        let priceInput = menuItemDiv.find('.item-selling-price');

        if (mode === 'price') {
            menuItemDiv.find('.price-mode-field').removeClass('hidden');
            menuItemDiv.find('.margin-mode-field').addClass('hidden');
            marginInput.removeAttr('required');
            priceInput.attr('required', 'required');
        } else {
            menuItemDiv.find('.price-mode-field').addClass('hidden');
            menuItemDiv.find('.margin-mode-field').removeClass('hidden');
            priceInput.removeAttr('required');
            marginInput.attr('required', 'required');
        }
        updateAllPricing(menuItemDiv);
    }

    $('#addMenuItemBtn').on('click', function () {
        let idx  = menuItemCounter;
        let tmpl = $('#menuItemTemplate').html();
        tmpl = tmpl.replace(/__INDEX__/g, idx);
        tmpl = tmpl.replace(/__ITEM_ID__/g, '');
        tmpl = tmpl.replace(/__NAME__/g, '');
        tmpl = tmpl.replace(/__PRICE__/g, '');
        tmpl = tmpl.replace(/__DESIRED_MARGIN__/g, '');
        tmpl = tmpl.replace(/__DESCRIPTION__/g, '');
        tmpl = tmpl.replace(/__COMMISSION_PCT__/g, '20');
        tmpl = tmpl.replace(/__ACTIVE_CHECKED__/g, 'checked');
        tmpl = tmpl.replace(/__CATEGORY_SELECTED_\d+__/g, '');

        $('#menuItemsContainer').append(tmpl);
        ingredientCounter[idx] = 0;

        let div = $('#menuItemsContainer .menu-item-card').last();
        div.data('item-index', idx);
        div.data('pricing-mode', 'price');

        div.find('.pricing-mode-toggle[data-mode="price"]').addClass('active');
        div.find('.price-mode-field').removeClass('hidden');
        div.find('.margin-mode-field').addClass('hidden');
        div.find('.item-selling-price').attr('required', 'required');
        div.find('.item-desired-margin').removeAttr('required');

        let ingForm = div.find('.add-ingredient-form');
        setTimeout(function() {
            initSelect2(div.find('.ingredient-select'), ingForm);
        }, 10);

        bindMenuItemEvents(div);
        updateItemCount();
        menuItemCounter++;
        div[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    function bindMenuItemEvents(menuItemDiv) {

        menuItemDiv.find('.delete-menu-item').off('click').on('click', function () {
            if (confirm('Delete this menu item and all its ingredients?')) {
                let itemId = menuItemDiv.data('item-id');
                if (itemId) {
                    if ($('#deleteItems').length === 0) {
                        $('<input>').attr({type: 'hidden', id: 'deleteItems', name: 'delete_items'}).val('').appendTo('#mainForm');
                    }
                    let currentValue = $('#deleteItems').val();
                    $('#deleteItems').val(currentValue + (currentValue ? ',' : '') + itemId);
                }
                menuItemDiv.find('.ingredient-select').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
                menuItemDiv.remove();
                updateItemCount();
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
            .off('change keyup').on('change keyup', function () { });

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

            let rowTmpl = $('#ingredientRowTemplate').html();
            rowTmpl = rowTmpl.replace(/__IDX__/g, ingIdx);
            rowTmpl = rowTmpl.replace(/__ITEM_INDEX__/g, itemIdx);
            rowTmpl = rowTmpl.replace(/__RECIPE_ID__/g, '');
            rowTmpl = rowTmpl.replace(/__INVENTORY_ID__/g, inventoryItemId);
            rowTmpl = rowTmpl.replace(/__INGREDIENT_NAME__/g, ingredientName);
            rowTmpl = rowTmpl.replace(/__BASE_UNIT__/g, baseUnit);
            rowTmpl = rowTmpl.replace(/__UNIT_COST__/g, unitCost);
            rowTmpl = rowTmpl.replace(/__QUANTITY__/g, qty);
            rowTmpl = rowTmpl.replace(/__UNIT__/g, unit);
            rowTmpl = rowTmpl.replace(/__WASTAGE__/g, wastage);
            rowTmpl = rowTmpl.replace(/__UNIT_DISPLAY__/g, qty + ' ' + unit);
            rowTmpl = rowTmpl.replace(/__COST_DISPLAY__/g, Number(unitCost).toLocaleString() + ' / ' + baseUnit);

            let totalCost = convertToBaseUnit(qty, unit, baseUnit) * unitCost * (1 + wastage / 100);
            rowTmpl = rowTmpl.replace(/__TOTAL_DISPLAY__/g, fmtUGX(totalCost));

            let ingList = menuItemDiv.find('.ingredients-list');
            ingList.find('.ingredients-empty').addClass('hidden');
            ingList.append(rowTmpl);

            let newRow = ingList.find('.ingredient-row').last();

            newRow.find('.ingredient-qty-value').off('change keyup').on('change keyup', function () {
                let newQty = $(this).val();
                newRow.find('.ingredient-unit-display').text(newQty + ' ' + unit);
                let newTotal = convertToBaseUnit(newQty, unit, baseUnit) * unitCost * (1 + wastage / 100);
                newRow.find('.ingredient-total-display').text(fmtUGX(newTotal));
                updateAllPricing(menuItemDiv);
            });

            updateAllPricing(menuItemDiv);
            menuItemDiv.find('.add-ingredient-form').addClass('hidden');
            ingSelect.val('').trigger('change');
        });

        menuItemDiv.off('click', '.remove-ingredient').on('click', '.remove-ingredient', function () {
            if (confirm('Remove this ingredient from the recipe?')) {
                let row = $(this).closest('.ingredient-row');
                let recipeId = row.data('recipe-id');
                if (recipeId) {
                    if ($('#deleteRecipeItems').length === 0) {
                        $('<input>').attr({type: 'hidden', id: 'deleteRecipeItems', name: 'delete_recipe_items'}).val('').appendTo('#mainForm');
                    }
                    let currentValue = $('#deleteRecipeItems').val();
                    $('#deleteRecipeItems').val(currentValue + (currentValue ? ',' : '') + recipeId);
                }
                row.remove();
                if (menuItemDiv.find('.ingredient-row').length === 0) {
                    menuItemDiv.find('.ingredients-empty').removeClass('hidden');
                }
                updateAllPricing(menuItemDiv);
            }
        });
    }

    function loadExistingMenuItems() {
        let menuItems = @json($menu->items);

        if (menuItems && menuItems.length > 0) {
            $.each(menuItems, function(index, item) {
                let tmpl = $('#menuItemTemplate').html();

                let pricingMode = 'price';
                let desiredMargin = '';
                let sellingPrice = item.selling_price || 0;

                if (item.age_margins && item.age_margins > 0 && sellingPrice > 0) {
                    let calculatedMargin = ((sellingPrice - (item.m_cost || 0)) / sellingPrice) * 100;
                    if (calculatedMargin > 0) {
                        desiredMargin = calculatedMargin.toFixed(2);
                        pricingMode = 'margin';
                    }
                }

                tmpl = tmpl.replace(/__INDEX__/g, menuItemCounter);
                tmpl = tmpl.replace(/__ITEM_ID__/g, item.id);
                tmpl = tmpl.replace(/__NAME__/g, escapeHtml(item.name));
                tmpl = tmpl.replace(/__PRICE__/g, sellingPrice);
                tmpl = tmpl.replace(/__DESIRED_MARGIN__/g, desiredMargin);
                tmpl = tmpl.replace(/__DESCRIPTION__/g, escapeHtml(item.description || ''));
                tmpl = tmpl.replace(/__COMMISSION_PCT__/g, '20');
                tmpl = tmpl.replace(/__ACTIVE_CHECKED__/g, item.is_active ? 'checked' : '');
                tmpl = tmpl.replace(new RegExp('__CATEGORY_SELECTED_' + (item.menu_item_category_id || '') + '__', 'g'), 'selected');
                tmpl = tmpl.replace(/__CATEGORY_SELECTED_\d+__/g, '');

                $('#menuItemsContainer').append(tmpl);

                let newItemDiv = $('#menuItemsContainer .menu-item-card').last();
                newItemDiv.data('item-index', menuItemCounter);
                newItemDiv.data('pricing-mode', pricingMode);
                newItemDiv.data('item-id', item.id);

                if (pricingMode === 'margin') {
                    newItemDiv.find('.pricing-mode-toggle[data-mode="margin"]').addClass('active');
                    newItemDiv.find('.price-mode-field').addClass('hidden');
                    newItemDiv.find('.margin-mode-field').removeClass('hidden');
                    newItemDiv.find('.item-selling-price').removeAttr('required');
                    newItemDiv.find('.item-desired-margin').attr('required', 'required');
                } else {
                    newItemDiv.find('.pricing-mode-toggle[data-mode="price"]').addClass('active');
                    newItemDiv.find('.price-mode-field').removeClass('hidden');
                    newItemDiv.find('.margin-mode-field').addClass('hidden');
                    newItemDiv.find('.item-selling-price').attr('required', 'required');
                    newItemDiv.find('.item-desired-margin').removeAttr('required');
                }

                ingredientCounter[menuItemCounter] = 0;

                if (item.recipe_items && item.recipe_items.length > 0) {
                    $.each(item.recipe_items, function(ingIdx, recipe) {
                        let inventoryItem = recipe.inventory_item;
                        if (inventoryItem) {
                            let quantity = parseFloat(recipe.quantity_required) || 0;
                            let unit = inventoryItem.base_unit || 'piece';
                            let displayQty = quantity;
                            let displayUnit = unit;

                            if (unit === 'kg' && quantity < 1 && quantity > 0) {
                                displayUnit = 'g';
                                displayQty = quantity * 1000;
                            } else if (unit === 'litre' && quantity < 1 && quantity > 0) {
                                displayUnit = 'ml';
                                displayQty = quantity * 1000;
                            }

                            let wastage = parseFloat(recipe.wastage_percentage) || 0;
                            let unitCost = parseFloat(inventoryItem.unit_cost) || 0;
                            let qtyInBaseUnit = quantity;
                            let ingredientCost = qtyInBaseUnit * unitCost;
                            let totalCost = ingredientCost * (1 + (wastage / 100));

                            let rowTmpl = $('#ingredientRowTemplate').html();
                            rowTmpl = rowTmpl.replace(/__IDX__/g, ingIdx);
                            rowTmpl = rowTmpl.replace(/__ITEM_INDEX__/g, menuItemCounter);
                            rowTmpl = rowTmpl.replace(/__RECIPE_ID__/g, recipe.id);
                            rowTmpl = rowTmpl.replace(/__INVENTORY_ID__/g, inventoryItem.id);
                            rowTmpl = rowTmpl.replace(/__INGREDIENT_NAME__/g, escapeHtml(inventoryItem.name));
                            rowTmpl = rowTmpl.replace(/__BASE_UNIT__/g, unit);
                            rowTmpl = rowTmpl.replace(/__UNIT_COST__/g, unitCost);
                            rowTmpl = rowTmpl.replace(/__QUANTITY__/g, displayQty.toFixed(3));
                            rowTmpl = rowTmpl.replace(/__UNIT__/g, displayUnit);
                            rowTmpl = rowTmpl.replace(/__WASTAGE__/g, wastage);
                            rowTmpl = rowTmpl.replace(/__UNIT_DISPLAY__/g, displayQty.toFixed(3) + ' ' + displayUnit);
                            rowTmpl = rowTmpl.replace(/__COST_DISPLAY__/g, unitCost.toLocaleString() + ' / ' + unit);
                            rowTmpl = rowTmpl.replace(/__TOTAL_DISPLAY__/g, fmtUGX(totalCost));

                            newItemDiv.find('.ingredients-list').append(rowTmpl);

                            let newRow = newItemDiv.find('.ingredients-list .ingredient-row').last();
                            newRow.find('.ingredient-qty-value').off('change keyup').on('change keyup', function() {
                                let newQty = $(this).val();
                                let newTotal = convertToBaseUnit(newQty, displayUnit, unit) * unitCost * (1 + wastage / 100);
                                newRow.find('.ingredient-total-display').text(fmtUGX(newTotal));
                                updateAllPricing(newItemDiv);
                            });
                        }
                    });
                    newItemDiv.find('.ingredients-empty').addClass('hidden');
                }

                newItemDiv.find('.ingredient-select').select2({
                    dropdownParent: newItemDiv,
                    placeholder: 'Search ingredient...',
                    allowClear: true
                });

                bindMenuItemEvents(newItemDiv);
                updateAllPricing(newItemDiv);

                menuItemCounter++;
                updateItemCount();
            });
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;')
                   .replace(/</g, '&lt;')
                   .replace(/>/g, '&gt;')
                   .replace(/"/g, '&quot;')
                   .replace(/'/g, '&#39;');
    }

    $('#mainForm').on('submit', function (e) {
        let hasItems = $('#menuItemsContainer .menu-item-card').length > 0;

        if (!hasItems) {
            if (!confirm('No menu items added. Update the menu without any items?')) {
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
        return true;
    });

    loadExistingMenuItems();

});
</script>
@endpush
