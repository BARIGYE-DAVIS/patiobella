@extends('layouts.procurement')
@section('title', 'Create Purchase Order')
@section('page-title', 'Create Purchase Order')

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
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-info {
        background-color: #dbeafe;
        color: #1e40af;
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
</style>

{{-- Display Validation Errors --}}
@if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm overflow-hidden">
        <div class="p-4">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
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

{{-- Session Messages --}}
@if(session('error'))
    <div class="alert alert-danger mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
@endif

{{-- Step 1: Choose Requisition --}}
<div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl shadow-md mb-8 card-hover">
    <div class="flex items-center mb-4">
        <div class="bg-blue-500 rounded-lg p-2 mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Step 1: Select Approved Requisition</h3>
    </div>
    <form method="GET" action="{{ route('procurement.purchase-orders.create') }}" class="flex flex-col md:flex-row gap-6 items-end">
        <div class="flex-1">
            <label class="block font-semibold mb-2 text-gray-700 required-field">Choose Approved Requisition</label>
            <select name="requisition_id" class="form-select w-full border-gray-300 rounded-lg form-enhanced bg-white" required>
                <option value="">-- Choose Requisition --</option>
                @foreach($approvedRequisitions as $req)
                    <option value="{{ $req->id }}" {{ request('requisition_id') == $req->id ? 'selected' : '' }}>
                        {{ $req->requisition_number }} | {{ $req->store->name ?? '-' }} | {{ $req->created_at->format('Y-m-d') }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Load Requisition
        </button>
    </form>
</div>

@if($requisition)
<form method="POST" action="{{ route('procurement.purchase-orders.store') }}" class="space-y-8" id="poForm">
    @csrf
    <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">

    {{-- Order Details Card --}}
    <div class="bg-white rounded-xl shadow-lg card-hover overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Purchase Order Details
            </h2>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="input-group {{ $errors->has('vendor_id') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700 required-field">Vendor</label>
                    <select name="vendor_id" class="form-select w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('vendor_id') ? 'error-border' : '' }}" required>
                        <option value="">Select Vendor...</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}"
                                {{ (old('vendor_id', $selectedVendor->id ?? $requisition->suggested_vendor_id ?? '') == $vendor->id) ? "selected" : "" }}>
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
                           value="{{ old('expected_delivery_date') }}">
                    @error('expected_delivery_date')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-group {{ $errors->has('delivery_address') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700">Delivery Address</label>
                    <input type="text" name="delivery_address"
                           class="form-input w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('delivery_address') ? 'error-border' : '' }}"
                           placeholder="Enter delivery address"
                           value="{{ old('delivery_address') }}">
                    @error('delivery_address')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-group {{ $errors->has('delivery_terms') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700">Delivery Terms</label>
                    <input type="text" name="delivery_terms"
                           class="form-input w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('delivery_terms') ? 'error-border' : '' }}"
                           placeholder="e.g. Delivery within 5 days"
                           value="{{ old('delivery_terms') }}">
                    @error('delivery_terms')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="input-group {{ $errors->has('note') ? 'field-error' : '' }}">
                <label class="block font-semibold mb-2 text-gray-700">General Order Note (Optional)</label>
                <textarea name="note" class="form-textarea w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('note') ? 'error-border' : '' }}" rows="3"
                          placeholder="General note for the whole purchase order">{{ old('note') }}</textarea>
                @error('note')
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
                    </tr>
                </thead>
                <tbody>
                    @foreach($requisition->items as $k => $item)
                        @if($item->quantity_approved > 0)
                        <tr class="table-row-hover border-b">
                            <td class="p-3 border font-semibold text-gray-800">
                                {{ $item->inventoryItem->name ?? $item->item_name }}
                                <input type="hidden" name="items[{{ $k }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                                @error("items.$k.inventory_item_id")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-3 border text-center">
                                <input type="number" name="items[{{ $k }}][quantity]" 
                                       class="form-input w-28 border-gray-300 rounded-lg form-enhanced text-center {{ $errors->has("items.$k.quantity") ? 'error-border' : '' }}"
                                       value="{{ old("items.$k.quantity", $item->quantity_approved ?? 0) }}" 
                                       min="0.01" step="0.01" required>
                                @error("items.$k.quantity")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-3 border text-center">
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">UGX</span>
                                    <input type="number" name="items[{{ $k }}][unit_cost]" 
                                           class="form-input w-36 border-gray-300 rounded-lg form-enhanced pl-12 {{ $errors->has("items.$k.unit_cost") ? 'error-border' : '' }}"
                                           value="{{ old("items.$k.unit_cost") }}" 
                                           min="0" step="0.01" required oninput="updateTotals()">
                                </div>
                                @error("items.$k.unit_cost")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-3 border text-center js-total font-semibold text-green-600">
                                UGX 0.00
                            </td>
                            <td class="p-3 border">
                                <textarea name="items[{{ $k }}][notes]" 
                                          class="form-textarea w-48 border-gray-300 rounded-lg form-enhanced"
                                          rows="2" placeholder="Item notes...">{{ old("items.$k.notes", $item->notes ?? '') }}</textarea>
                                @error("items.$k.notes")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end space-x-4 sticky bottom-4 bg-white p-4 rounded-lg shadow-lg">
        <button type="button" onclick="showPreview()"
            class="px-8 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Preview Order
        </button>
        <button type="submit" class="px-8 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            Save Purchase Order
        </button>
    </div>

    {{-- Professional Modal Preview --}}
    <div id="previewModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden transition-all duration-300">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-screen overflow-y-auto transform transition-all">
            <div class="sticky top-0 bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Purchase Order Preview</h2>
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

{{-- JavaScript for localStorage persistence --}}
<script>
// Save form data to localStorage on input
function saveFormData() {
    const form = document.getElementById('poForm');
    if (!form) return;
    
    const formData = {};
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (input.name && input.type !== 'submit' && input.type !== 'button') {
            if (input.type === 'checkbox' || input.type === 'radio') {
                formData[input.name] = input.checked;
            } else {
                formData[input.name] = input.value;
            }
        }
    });
    
    localStorage.setItem('po_form_data', JSON.stringify(formData));
    localStorage.setItem('po_form_timestamp', new Date().getTime());
}

// Load form data from localStorage
function loadFormData() {
    const savedData = localStorage.getItem('po_form_data');
    if (!savedData) return;
    
    const formData = JSON.parse(savedData);
    const form = document.getElementById('poForm');
    if (!form) return;
    
    // Check if data is less than 1 hour old
    const timestamp = localStorage.getItem('po_form_timestamp');
    if (timestamp && (new Date().getTime() - parseInt(timestamp)) > 3600000) {
        localStorage.removeItem('po_form_data');
        localStorage.removeItem('po_form_timestamp');
        return;
    }
    
    // Only load if there are no old values (fresh form)
    const hasOldValues = form.querySelector('input[name="vendor_id"]')?.value;
    if (!hasOldValues || hasOldValues === '') {
        for (const [name, value] of Object.entries(formData)) {
            const input = form.querySelector(`[name="${name}"]`);
            if (input && value) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = value;
                } else {
                    input.value = value;
                }
            }
        }
        updateTotals();
    }
}

// Clear saved form data after successful submission
function clearSavedFormData() {
    localStorage.removeItem('po_form_data');
    localStorage.removeItem('po_form_timestamp');
}

// Auto-save on input
document.addEventListener('DOMContentLoaded', function() {
    loadFormData();
    updateTotals();
    
    const form = document.getElementById('poForm');
    if (form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                saveFormData();
                updateTotals();
            });
            input.addEventListener('change', saveFormData);
        });
    }
    
    // Check if we have a success message and clear storage
    if(session('success'))
        clearSavedFormData();
    endif
});

function showPreview() {
    // Get vendor
    let vendor = document.querySelector('select[name="vendor_id"]');
    let vendorText = vendor ? vendor.options[vendor.selectedIndex]?.text || '--' : '--';
    document.getElementById('pv_vendor').innerText = vendorText;

    // Delivery date, address, terms, note
    document.getElementById('pv_delivery_date').innerText = document.querySelector('input[name="expected_delivery_date"]')?.value || '--';
    document.getElementById('pv_delivery_address').innerText = document.querySelector('input[name="delivery_address"]')?.value || '--';
    document.getElementById('pv_delivery_terms').innerText = document.querySelector('input[name="delivery_terms"]')?.value || '--';
    document.getElementById('pv_note').innerText = document.querySelector('textarea[name="note"]')?.value || '--';

    // Items
    let rows = document.querySelectorAll('table tbody tr');
    let html = '';
    rows.forEach(function(row) {
        let cells = row.querySelectorAll('td');
        let itemName = cells[0]?.innerText.trim();
        let qty = cells[1]?.querySelector('input')?.value || 0;
        let unitCost = cells[2]?.querySelector('input')?.value || 0;
        let note = cells[4]?.querySelector('textarea')?.value || '';
        let total = qty && unitCost ? (parseFloat(qty) * parseFloat(unitCost)).toFixed(2) : '0.00';
        html += `<tr class="border-b hover:bg-gray-50">
            <td class="p-3 border">${itemName || '--'}</td>
            <td class="p-3 border text-center">${qty}</td>
            <td class="p-3 border text-center">UGX ${parseFloat(unitCost).toLocaleString()}</td>
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

function updateTotals() {
    document.querySelectorAll('table tbody tr').forEach(function(row) {
        let qty = row.querySelector('input[name*="[quantity]"]')?.value;
        let unitCost = row.querySelector('input[name*="[unit_cost]"]')?.value;
        let cell = row.querySelector('.js-total');
        if (cell && qty && unitCost) {
            let total = (parseFloat(qty) * parseFloat(unitCost)).toFixed(2);
            cell.innerText = `UGX ${parseFloat(total).toLocaleString()}`;
        } else if (cell) {
            cell.innerText = 'UGX 0.00';
        }
    });
}
</script>
@endif
@endsection