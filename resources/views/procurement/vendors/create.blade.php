@extends('layouts.procurement')

@section('title', 'Create Vendor')

@section('page-title', 'Add New Vendor')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ route('procurement.vendors.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Vendor Code --}}
            <div>
                <label for="vendor_code" class="block text-sm font-medium text-gray-700 mb-1">Vendor Code <span class="text-red-500">*</span></label>
                <input type="text" name="vendor_code" id="vendor_code" value="{{ old('vendor_code') }}" required
                       placeholder="e.g., VEND-001"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('vendor_code') border-red-500 @enderror">
                @error('vendor_code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-1">Unique identifier for this vendor</p>
            </div>

            {{-- Vendor Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Vendor Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       placeholder="e.g., Mukwano Industries"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contact Person --}}
            <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}"
                       placeholder="e.g., John Doe"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                       placeholder="e.g., +256 712 345678"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Alternative Phone --}}
            <div>
                <label for="alternative_phone" class="block text-sm font-medium text-gray-700 mb-1">Alternative Phone</label>
                <input type="text" name="alternative_phone" id="alternative_phone" value="{{ old('alternative_phone') }}"
                       placeholder="e.g., +256 782 345678"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       placeholder="e.g., info@mukwano.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Address --}}
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" id="address" rows="2"
                          placeholder="Physical address"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
            </div>

            {{-- City --}}
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input type="text" name="city" id="city" value="{{ old('city') }}"
                       placeholder="e.g., Kampala"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Country --}}
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country', 'Uganda') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Tax ID / TIN --}}
            <div>
                <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID (TIN)</label>
                <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}"
                       placeholder="e.g., 1234567890"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Payment Method --}}
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select name="payment_method" id="payment_method"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="mobile" {{ old('payment_method') == 'mobile' ? 'selected' : '' }}>Mobile Money</option>
                </select>
            </div>

            {{-- Credit Limit --}}
            <div>
                <label for="credit_limit" class="block text-sm font-medium text-gray-700 mb-1">Credit Limit</label>
                <input type="number" name="credit_limit" id="credit_limit" value="{{ old('credit_limit') }}"
                       step="0.01" placeholder="0.00"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="blacklisted" {{ old('status') == 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                </select>
            </div>

            {{-- Notes --}}
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="Additional information about this vendor"
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
@endsection