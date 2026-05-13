{{-- resources/views/restaurant/menu/create.blade.php --}}
{{-- resources/views/restaurant/menu/edit.blade.php --}}

@extends('layouts.restaurant')

@section('title', isset($menuItem) ? 'Edit Menu Item' : 'Create Menu Item')

@section('page-title', isset($menuItem) ? 'Edit Menu Item' : 'Create Menu Item')

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
    .form-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: #ea580c;
        ring: 2px solid #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }
    .form-textarea {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        min-height: 80px;
    }
    .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        background-color: white;
    }
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .help-text {
        font-size: 0.65rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .checkbox-group input {
        width: 1rem;
        height: 1rem;
    }
    .btn-save {
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
    .btn-save:hover {
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
    .image-preview {
        margin-top: 0.5rem;
        max-width: 150px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 0.25rem;
    }
    .image-preview img {
        width: 100%;
        border-radius: 6px;
    }
</style>

<div class="form-card">
    <div class="form-header">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas {{ isset($menuItem) ? 'fa-edit' : 'fa-plus-circle' }} mr-2 text-orange-600"></i>
            {{ isset($menuItem) ? 'Edit Menu Item' : 'Create New Menu Item' }}
        </h3>
        <p class="text-xs text-gray-500 mt-1">Fill in the details below to {{ isset($menuItem) ? 'update' : 'create' }} a menu item</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ isset($menuItem) ? route('restaurant.menu.update', $menuItem->id) : route('restaurant.menu.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if(isset($menuItem))
                @method('PUT')
            @endif

            <div class="form-row">
                {{-- Item Name --}}
                <div class="form-group">
                    <label class="form-label">
                        Item Name <span class="required">*</span>
                    </label>
                    <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror"
                           value="{{ old('name', $menuItem->name ?? '') }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="form-group">
                    <label class="form-label">
                        Category <span class="required">*</span>
                    </label>
                    <select name="category" class="form-select @error('category') border-red-500 @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $menuItem->category ?? '') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea @error('description') border-red-500 @enderror"
                          placeholder="Describe the dish...">{{ old('description', $menuItem->description ?? '') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="help-text">Brief description of the item as it will appear on the menu</p>
            </div>

            <div class="form-row">
                {{-- Selling Price --}}
                <div class="form-group">
                    <label class="form-label">
                        Selling Price (UGX) <span class="required">*</span>
                    </label>
                    <input type="number" name="selling_price" step="0.01" class="form-input @error('selling_price') border-red-500 @enderror"
                           value="{{ old('selling_price', $menuItem->selling_price ?? '') }}" required>
                    @error('selling_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Preparation Time --}}
                <div class="form-group">
                    <label class="form-label">Preparation Time (minutes)</label>
                    <input type="number" name="preparation_time" class="form-input @error('preparation_time') border-red-500 @enderror"
                           value="{{ old('preparation_time', $menuItem->preparation_time ?? '') }}" placeholder="e.g., 15">
                    @error('preparation_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="help-text">Estimated time for kitchen to prepare</p>
                </div>
            </div>

            <div class="form-row">
                {{-- Linked Inventory Item --}}
                <div class="form-group">
                    <label class="form-label">Linked Inventory Item (Optional)</label>
                    <select name="inventory_item_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach($inventoryItems as $item)
                            <option value="{{ $item->id }}" {{ old('inventory_item_id', $menuItem->inventory_item_id ?? '') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} ({{ $item->base_unit ?? 'unit' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="help-text">Link to inventory item for automatic stock deduction when ordered</p>
                </div>

                {{-- Sort Order --}}
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-input"
                           value="{{ old('sort_order', $menuItem->sort_order ?? 0) }}" placeholder="0">
                    <p class="help-text">Lower numbers appear first in the menu</p>
                </div>
            </div>

            {{-- Allergen Information --}}
            <div class="form-group">
                <label class="form-label">Allergen Information</label>
                <input type="text" name="allergen_info" class="form-input @error('allergen_info') border-red-500 @enderror"
                       value="{{ old('allergen_info', $menuItem->allergen_info ?? '') }}"
                       placeholder="e.g., Contains: Nuts, Dairy, Gluten">
                @error('allergen_info')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="help-text">List any allergens present in this item</p>
            </div>

            {{-- Notes --}}
            <div class="form-group">
                <label class="form-label">Internal Notes</label>
                <textarea name="notes" class="form-textarea"
                          placeholder="Internal notes for kitchen staff...">{{ old('notes', $menuItem->notes ?? '') }}</textarea>
                <p class="help-text">These notes are only visible to staff, not customers</p>
            </div>

            {{-- Active Status & Image URL --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" value="1" id="is_active"
                               {{ old('is_active', $menuItem->is_active ?? true) ? 'checked' : '' }}>
                        <label for="is_active" class="text-sm text-gray-700">Active (visible on POS)</label>
                    </div>
                    <p class="help-text">Inactive items will not appear in the POS menu</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Image URL (Optional)</label>
                    <input type="text" name="image_url" class="form-input"
                           value="{{ old('image_url', $menuItem->image_url ?? '') }}"
                           placeholder="https://example.com/image.jpg">
                    <p class="help-text">URL to an image of the dish</p>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('restaurant.menu.index') }}" class="btn-cancel">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas {{ isset($menuItem) ? 'fa-save' : 'fa-plus' }} mr-1"></i>
                    {{ isset($menuItem) ? 'Update Menu Item' : 'Create Menu Item' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Preview image URL if provided
    const imageUrlInput = document.querySelector('input[name="image_url"]');
    if (imageUrlInput) {
        imageUrlInput.addEventListener('change', function() {
            const url = this.value;
            if (url) {
                let preview = document.querySelector('.image-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'image-preview';
                    this.parentNode.appendChild(preview);
                }
                preview.innerHTML = `<img src="${url}" alt="Preview" onerror="this.src='https://placehold.co/150x100?text=Invalid+URL'">`;
            }
        });
    }
</script>
@endpush
@endsection
