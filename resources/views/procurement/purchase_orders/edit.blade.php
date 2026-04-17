@extends('layouts.procurement')
@section('title', 'Edit Purchase Order')
@section('page-title', 'Edit Purchase Order')

@section('content')
<style>
    .form-enhanced {
        transition: all 0.2s ease;
    }
    .form-enhanced:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    .input-group {
        transition: transform 0.2s ease;
    }
    .input-group:hover {
        transform: translateY(-1px);
    }
    .card-hover {
        transition: box-shadow 0.3s ease;
    }
    .card-hover:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }
    .table-row-hover:hover {
        background-color: #f8fafc;
        transition: background-color 0.2s ease;
    }
    .required-field::after {
        content: '*';
        color: #ef4444;
        margin-left: 4px;
    }
    .error-border {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
    }
    .error-text {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }
    .field-error {
        border-left: 3px solid #ef4444;
        padding-left: 0.75rem;
    }
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-draft {
        background-color: #fef3c7;
        color: #92400e;
    }
</style>

@if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    </div>
@endif

@if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm overflow-hidden">
        <div class="p-4">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <h3 class="font-semibold text-red-800">Please fix the following errors:</h3>
            </div>
            <ul class="list-disc list-inside space-y-1 text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="mb-4">
    <a href="{{ route('procurement.purchase-orders.show', $purchaseOrder->id) }}" 
       class="text-blue-600 hover:text-blue-800 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Purchase Order
    </a>
</div>

<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-yellow-800">You are editing a <strong>DRAFT</strong> purchase order. Make your changes and save.</span>
    </div>
</div>

<form method="POST" action="{{ route('procurement.purchase-orders.update', $purchaseOrder->id) }}" class="space-y-8" id="poForm">
    @csrf
    @method('PUT')

    {{-- Order Details Card --}}
    <div class="bg-white rounded-xl shadow-lg card-hover overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Edit Purchase Order: {{ $purchaseOrder->po_number }}
                </h2>
                <span class="status-badge status-draft">DRAFT</span>
            </div>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="input-group {{ $errors->has('vendor_id') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700 required-field">Vendor</label>
                    <select name="vendor_id" class="form-select w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('vendor_id') ? 'error-border' : '' }}" required>
                        <option value="">Select Vendor...</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $purchaseOrder->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }} - {{ $vendor->contact_person }} ({{ $vendor->phone }})
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-group {{ $errors->has('expected_delivery_date') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700">Expected Delivery Date</label>
                    <input type="date" name="expected_delivery_date"
                           class="form-input w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('expected_delivery_date') ? 'error-border' : '' }}"
                           value="{{ old('expected_delivery_date', $purchaseOrder->expected_delivery_date ? date('Y-m-d', strtotime($purchaseOrder->expected_delivery_date)) : '') }}">
                    @error('expected_delivery_date')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-group {{ $errors->has('delivery_address') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700">Delivery Address</label>
                    <input type="text" name="delivery_address"
                           class="form-input w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('delivery_address') ? 'error-border' : '' }}"
                           placeholder="Enter delivery address"
                           value="{{ old('delivery_address', $purchaseOrder->delivery_address) }}">
                    @error('delivery_address')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-group {{ $errors->has('delivery_terms') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700">Delivery Terms</label>
                    <input type="text" name="delivery_terms"
                           class="form-input w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('delivery_terms') ? 'error-border' : '' }}"
                           placeholder="e.g. Delivery within 5 days"
                           value="{{ old('delivery_terms', $purchaseOrder->delivery_terms) }}">
                    @error('delivery_terms')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="input-group {{ $errors->has('notes') ? 'field-error' : '' }}">
                <label class="block font-semibold mb-2 text-gray-700">General Order Note (Optional)</label>
                <textarea name="notes" class="form-textarea w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('notes') ? 'error-border' : '' }}" rows="3"
                          placeholder="General note for the whole purchase order">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                @error('notes')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Items Card --}}
    <div class="bg-white rounded-xl shadow-lg card-hover overflow-hidden">
        <div class="bg-gradient-to-r from-green-800 to-green-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Order Items
            </h2>
        </div>
        <div class="p-6 overflow-x-auto">
            @if($errors->has('items') || $errors->has('items.*'))
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded">
                    <p class="text-red-700 text-sm">Please check the items below for errors.</p>
                </div>
            @endif
            
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700">
                        <th class="p-3 border text-left">Item</th>
                        <th class="p-3 border text-center">Quantity</th>
                        <th class="p-3 border text-center">Unit Cost (UGX)</th>
                        <th class="p-3 border text-center">Total (UGX)</th>
                        <th class="p-3 border text-left">Item Notes</th>
                        <th class="p-3 border text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="items-table-body">
                    @foreach($purchaseOrder->items as $index => $item)
                        <tr class="table-row-hover border-b item-row" data-index="{{ $index }}">
                            <td class="p-3 border font-semibold text-gray-800">
                                {{ $item->inventoryItem->name ?? 'Unknown Item' }}
                                <input type="hidden" name="items[{{ $index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                                <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $item->unit_id }}">
                            </td>
                            <td class="p-3 border text-center">
                                <input type="number" name="items[{{ $index }}][quantity]" 
                                       class="form-input w-28 border-gray-300 rounded-lg form-enhanced text-center item-quantity"
                                       value="{{ old("items.$index.quantity", $item->quantity_ordered) }}" 
                                       min="0.01" step="0.01" required>
                                @error("items.$index.quantity")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-3 border text-center">
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">UGX</span>
                                    <input type="number" name="items[{{ $index }}][unit_cost]" 
                                           class="form-input w-36 border-gray-300 rounded-lg form-enhanced pl-12 item-cost"
                                           value="{{ old("items.$index.unit_cost", $item->unit_cost) }}" 
                                           min="0" step="0.01" required>
                                </div>
                                @error("items.$index.unit_cost")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-3 border text-center item-total font-semibold text-green-600">
                                UGX {{ number_format($item->total_cost, 2) }}
                            </td>
                            <td class="p-3 border">
                                <textarea name="items[{{ $index }}][notes]" 
                                          class="form-textarea w-48 border-gray-300 rounded-lg form-enhanced"
                                          rows="2" placeholder="Item notes...">{{ old("items.$index.notes", $item->notes) }}</textarea>
                                @error("items.$index.notes")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-3 border text-center">
                                <button type="button" class="remove-item text-red-600 hover:text-red-800" title="Remove Item">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="p-3">
                            <button type="button" id="add-item-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Item
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end space-x-4 sticky bottom-4 bg-white p-4 rounded-lg shadow-lg">
        <a href="{{ route('procurement.purchase-orders.show', $purchaseOrder->id) }}" 
           class="px-8 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-md flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Cancel
        </a>
        <button type="button" onclick="showPreview()"
            class="px-8 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Preview Changes
        </button>
        <button type="submit" class="px-8 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Update Purchase Order
        </button>
    </div>

    {{-- Preview Modal --}}
    <div id="previewModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden transition-all duration-300">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-screen overflow-y-auto transform transition-all">
            <div class="sticky top-0 bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Purchase Order Preview (After Update)</h2>
                <button type="button" onclick="hidePreview()"
                    class="text-gray-300 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="font-semibold text-gray-800">Vendor:</span>
                        <span id="pv_vendor" class="ml-2 text-gray-700"></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-medium text-gray-600">Delivery Date:</span>
                            <span id="pv_delivery_date" class="ml-2 text-gray-700"></span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium text-gray-600">Delivery Address:</span>
                            <span id="pv_delivery_address" class="ml-2 text-gray-700"></span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="font-medium text-gray-600">Delivery Terms:</span>
                            <span id="pv_delivery_terms" class="ml-2 text-gray-700"></span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-gray-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span class="font-medium text-gray-600">General Order Note:</span>
                        </div>
                        <span id="pv_note" class="block mt-1 ml-6 text-gray-700 italic"></span>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Order Items
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-3 border text-left">Item</th>
                                    <th class="p-3 border text-center">Quantity</th>
                                    <th class="p-3 border text-center">Unit Cost</th>
                                    <th class="p-3 border text-center">Total</th>
                                    <th class="p-3 border text-left">Item Notes</th>
                                </tr>
                            </thead>
                            <tbody id="pv_items" class="text-sm"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t">
                <button onclick="hidePreview()" type="button" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-all duration-200">
                    Close Preview
                </button>
            </div>
        </div>
    </div>
</form>

<script>
// Calculate and update totals
function updateTotals() {
    document.querySelectorAll('.item-row').forEach(function(row) {
        let qty = row.querySelector('.item-quantity')?.value;
        let cost = row.querySelector('.item-cost')?.value;
        let totalCell = row.querySelector('.item-total');
        if (totalCell && qty && cost) {
            let total = parseFloat(qty) * parseFloat(cost);
            totalCell.innerText = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        } else if (totalCell) {
            totalCell.innerText = 'UGX 0.00';
        }
    });
}

// Add new item row
let itemCounter = {{ count($purchaseOrder->items) }};

document.getElementById('add-item-btn')?.addEventListener('click', function() {
    const tbody = document.getElementById('items-table-body');
    const newIndex = itemCounter++;
    
    const newRow = `
        <tr class="table-row-hover border-b item-row" data-index="${newIndex}">
            <td class="p-3 border">
                <input type="text" name="items[${newIndex}][item_name]" 
                       class="form-input w-full border-gray-300 rounded-lg form-enhanced"
                       placeholder="Enter item name" required>
                <input type="hidden" name="items[${newIndex}][inventory_item_id]" value="">
            </td>
            <td class="p-3 border text-center">
                <input type="number" name="items[${newIndex}][quantity]" 
                       class="form-input w-28 border-gray-300 rounded-lg form-enhanced text-center item-quantity"
                       value="1" min="0.01" step="0.01" required>
            </td>
            <td class="p-3 border text-center">
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">UGX</span>
                    <input type="number" name="items[${newIndex}][unit_cost]" 
                           class="form-input w-36 border-gray-300 rounded-lg form-enhanced pl-12 item-cost"
                           value="0" min="0" step="0.01" required>
                </div>
            </td>
            <td class="p-3 border text-center item-total font-semibold text-green-600">
                UGX 0.00
            </td>
            <td class="p-3 border">
                <textarea name="items[${newIndex}][notes]" 
                          class="form-textarea w-48 border-gray-300 rounded-lg form-enhanced"
                          rows="2" placeholder="Item notes..."></textarea>
            </td>
            <td class="p-3 border text-center">
                <button type="button" class="remove-item text-red-600 hover:text-red-800" title="Remove Item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', newRow);
    
    // Add event listeners to new inputs
    const newRowElement = tbody.lastElementChild;
    newRowElement.querySelectorAll('.item-quantity, .item-cost').forEach(input => {
        input.addEventListener('input', updateTotals);
    });
    newRowElement.querySelector('.remove-item')?.addEventListener('click', function() {
        this.closest('.item-row').remove();
        updateTotals();
    });
    
    updateTotals();
});

// Remove item
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        e.target.closest('.item-row').remove();
        updateTotals();
    }
});

// Preview function
function showPreview() {
    // Get vendor
    let vendor = document.querySelector('select[name="vendor_id"]');
    let vendorText = vendor ? vendor.options[vendor.selectedIndex]?.text || '--' : '--';
    document.getElementById('pv_vendor').innerText = vendorText;

    // Delivery details
    document.getElementById('pv_delivery_date').innerText = document.querySelector('input[name="expected_delivery_date"]')?.value || '--';
    document.getElementById('pv_delivery_address').innerText = document.querySelector('input[name="delivery_address"]')?.value || '--';
    document.getElementById('pv_delivery_terms').innerText = document.querySelector('input[name="delivery_terms"]')?.value || '--';
    document.getElementById('pv_note').innerText = document.querySelector('textarea[name="notes"]')?.value || '--';

    // Items
    let rows = document.querySelectorAll('.item-row');
    let html = '';
    rows.forEach(function(row) {
        let itemName = row.querySelector('input[name*="[item_name]"], td:first-child')?.innerText.trim() || 
                      row.querySelector('input[name*="[item_name]"]')?.value || 'New Item';
        let qty = row.querySelector('.item-quantity')?.value || 0;
        let cost = row.querySelector('.item-cost')?.value || 0;
        let note = row.querySelector('textarea[name*="[notes]"]')?.value || '';
        let total = qty && cost ? (parseFloat(qty) * parseFloat(cost)).toFixed(2) : '0.00';
        
        html += `<tr class="border-b hover:bg-gray-50">
            <td class="p-3 border">${itemName}</td>
            <td class="p-3 border text-center">${qty}</td>
            <td class="p-3 border text-center">UGX ${parseFloat(cost).toLocaleString()}</td>
            <td class="p-3 border text-center font-semibold text-green-600">UGX ${parseFloat(total).toLocaleString()}</td>
            <td class="p-3 border">${note || '--'}</td>
        </tr>`;
    });
    document.getElementById('pv_items').innerHTML = html;

    document.getElementById('previewModal').classList.remove('hidden');
}

function hidePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
    document.querySelectorAll('.item-quantity, .item-cost').forEach(el => {
        el.addEventListener('input', updateTotals);
    });
});
</script>
@endsection