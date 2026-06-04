@extends('layouts.procurement')

@section('title', 'Create Vendor')
@section('page-title', 'Add New Vendor')

@section('content')
<style>
    .categories-section {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }
    .categories-header {
        background-color: #f9fafb;
        padding: 12px 15px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .categories-search {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        width: 250px;
        font-size: 14px;
    }
    .categories-search:focus {
        outline: none;
        border-color: #3b82f6;
        ring: 2px solid #3b82f6;
    }
    .categories-body {
        max-height: 250px;
        overflow-y: auto;
        padding: 10px;
    }
    .category-checkbox {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        margin: 4px 0;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .category-checkbox:hover {
        background-color: #f3f4f6;
    }
    .category-checkbox input {
        width: 18px;
        height: 18px;
        margin-right: 12px;
        cursor: pointer;
        accent-color: #3b82f6;
    }
    .category-checkbox label {
        flex: 1;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }
    .select-all-btn {
        background-color: #3b82f6;
        color: white;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        border: none;
        cursor: pointer;
    }
    .select-all-btn:hover {
        background-color: #2563eb;
    }
    .clear-all-btn {
        background-color: #ef4444;
        color: white;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        border: none;
        cursor: pointer;
        margin-left: 8px;
    }
    .clear-all-btn:hover {
        background-color: #dc2626;
    }
    .selected-count {
        font-size: 13px;
        color: #6b7280;
    }
</style>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ route('procurement.vendors.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Vendor Code --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Code</label>
                <input type="text" name="vendor_code" id="vendor_code" value="{{ $nextVendorCode }}" readonly
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                <p class="text-gray-400 text-xs mt-1">Auto-generated. Cannot be edited.</p>
            </div>

            {{-- Vendor Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g., Mukwano Industries"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contact Person --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                       placeholder="e.g., John Doe"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       placeholder="e.g., +256 712 345678"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Alternative Phone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alternative Phone</label>
                <input type="text" name="alternative_phone" value="{{ old('alternative_phone') }}"
                       placeholder="e.g., +256 782 345678"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="e.g., info@mukwano.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Address --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2" placeholder="Physical address"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
            </div>

            {{-- City --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input type="text" name="city" value="{{ old('city') }}"
                       placeholder="e.g., Kampala"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Country --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                <input type="text" name="country" value="{{ old('country', 'Uganda') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Tax ID --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tax ID (TIN)</label>
                <input type="text" name="tax_id" value="{{ old('tax_id') }}"
                       placeholder="e.g., 1234567890"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="mobile" {{ old('payment_method') == 'mobile' ? 'selected' : '' }}>Mobile Money</option>
                </select>
                @error('payment_method')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Credit Limit --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Credit Limit</label>
                <input type="number" name="credit_limit" value="{{ old('credit_limit') }}"
                       step="0.01" placeholder="0.00"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="blacklisted" {{ old('status') == 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                </select>
            </div>

            {{-- Categories Section --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Categories Supplied by this Vendor</label>

                <div class="categories-section">
                    <div class="categories-header">
                        <div>
                            <button type="button" class="select-all-btn" onclick="selectAll(true)">Select All</button>
                            <button type="button" class="clear-all-btn" onclick="selectAll(false)">Clear All</button>
                        </div>
                        <div>
                            <input type="text" id="searchCategories" class="categories-search" placeholder="Search categories..." onkeyup="filterCategories()">
                        </div>
                    </div>
                    <div class="categories-body" id="categoriesList">
                        @foreach($categories as $category)
                            <div class="category-checkbox" data-category-name="{{ strtolower($category->name) }}">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" id="cat_{{ $category->id }}"
                                       {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                <label for="cat_{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="categories-header">
                        <span class="selected-count" id="selectedCount">0 categories selected</span>
                    </div>
                </div>
                <p class="text-gray-400 text-xs mt-1">Check the categories this vendor supplies</p>
                @error('categories')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3" placeholder="Additional information about this vendor"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('procurement.vendors.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Create Vendor
            </button>
        </div>
    </form>
</div>

<script>
    function updateSelectedCount() {
        const checked = document.querySelectorAll('input[name="categories[]"]:checked');
        document.getElementById('selectedCount').innerText = checked.length + ' category(ies) selected';
    }

    function selectAll(select) {
        const checkboxes = document.querySelectorAll('input[name="categories[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = select;
        });
        updateSelectedCount();
    }

    function filterCategories() {
        const searchTerm = document.getElementById('searchCategories').value.toLowerCase();
        const items = document.querySelectorAll('.category-checkbox');

        items.forEach(item => {
            const categoryName = item.getAttribute('data-category-name');
            if (categoryName.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="categories[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        updateSelectedCount();
    });
</script>
@endsection
