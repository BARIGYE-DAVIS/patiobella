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
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
    }
    .type-normal {
        background: #d1fae5;
        color: #065f46;
    }
    .type-emergency {
        background: #fee2e2;
        color: #991b1b;
    }
    .vat-section {
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }
    .vat-section-title {
        font-size: 14px;
        font-weight: bold;
        color: #92400e;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="input-group {{ $errors->has('type') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700 required-field">PO Type</label>
                    <select name="type" class="form-select w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white" required>
                        <option value="normal" {{ old('type') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    @error('type')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

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

                <div class="input-group {{ $errors->has('payment_method') ? 'field-error' : '' }}">
                    <label class="block font-semibold mb-2 text-gray-700 required-field">Payment Method</label>
                    <select name="payment_method" class="form-select w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white" required>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                    @error('payment_method')
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
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

            <div class="input-group {{ $errors->has('notes') ? 'field-error' : '' }}">
                <label class="block font-semibold mb-2 text-gray-700">General Order Note (Optional)</label>
                <textarea name="notes" class="form-textarea w-full border-gray-300 rounded-lg form-enhanced bg-gray-50 focus:bg-white {{ $errors->has('notes') ? 'error-border' : '' }}" rows="3"
                          placeholder="General note for the whole purchase order">{{ old('notes') }}</textarea>
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
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700">
                        <th class="p-3 border text-left">Item</th>
                        <th class="p-3 border text-center">GM Approved Qty</th>
                        <th class="p-3 border text-center w-24">Order Qty</th>
                        <th class="p-3 border text-center w-28">Unit Cost (UGX)</th>
                        <th class="p-3 border text-center w-28">Total (UGX)</th>
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
                            </td>
                            <td class="p-3 border text-center font-semibold text-green-600">
                                {{ number_format($item->quantity_approved, 2) }}
                            </td>
                            <td class="p-3 border text-center">
                                <input type="number" name="items[{{ $k }}][quantity]"
                                       class="form-input w-24 border-gray-300 rounded-lg form-enhanced text-center item-quantity"
                                       value="{{ old("items.$k.quantity", $item->quantity_approved ?? 0) }}"
                                       min="0.01" step="0.01" required>
                            </td>
                            <td class="p-3 border text-center">
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">UGX</span>
                                    <input type="number" name="items[{{ $k }}][unit_cost]"
                                           class="form-input w-36 border-gray-300 rounded-lg form-enhanced pl-12 item-cost"
                                           value="{{ old("items.$k.unit_cost") }}"
                                           min="0" step="0.01" required oninput="updateTotals()">
                                </div>
                            </td>
                            <td class="p-3 border text-center item-total font-semibold text-green-600">
                                UGX 0.00
                            </td>
                            <td class="p-3 border">
                                <textarea name="items[{{ $k }}][notes]"
                                          class="form-textarea w-48 border-gray-300 rounded-lg form-enhanced"
                                          rows="2" placeholder="Item notes...">{{ old("items.$k.notes", $item->notes ?? '') }}</textarea>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- VAT SECTION - Prominent and Clear --}}
    <div class="vat-section">
        <div class="vat-section-title">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11.172a2 2 0 011.414.586l2.828 2.828a2 2 0 01.586 1.414V19a2 2 0 01-2 2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 16h.01M12 20h.01"></path>
            </svg>
            VAT CALCULATION
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div class="input-group">
                <label class="block font-semibold mb-2 text-gray-700">VAT Rate (%)</label>
                <input type="number" name="vat_rate" id="vat_rate" step="0.01" min="0" max="100"
                       class="form-input w-full border-gray-300 rounded-lg form-enhanced bg-yellow-50 focus:bg-white text-lg font-bold"
                       value="{{ old('vat_rate', 0) }}" oninput="updateTotals()">
                @error('vat_rate')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            <div class="text-center">
                <div class="text-sm text-gray-600 mb-1">VAT Amount</div>
                <div class="text-2xl font-bold text-yellow-700" id="vat_amount_display">UGX 0.00</div>
            </div>
            <div class="text-center bg-yellow-100 rounded-lg p-3">
                <div class="text-sm text-gray-600 mb-1">Total Including VAT</div>
                <div class="text-2xl font-bold text-green-700" id="total_display">UGX 0.00</div>
            </div>
        </div>
    </div>

    {{-- Totals Summary Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-700 to-gray-600 px-6 py-3">
            <h3 class="text-md font-semibold text-white">Order Summary</h3>
        </div>
        <div class="p-4">
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-semibold" id="subtotal_display">UGX 0.00</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">VAT (<span id="vat_rate_display">0</span>%):</span>
                <span class="font-semibold" id="vat_amount_summary">UGX 0.00</span>
            </div>
            <div class="flex justify-between items-center py-2 text-lg font-bold">
                <span class="text-green-700">GRAND TOTAL:</span>
                <span class="text-green-700" id="grand_total_display">UGX 0.00</span>
            </div>
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
            Create & Send Purchase Order
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
                        <span class="font-semibold text-gray-800">PO Type:</span>
                        <span id="pv_type" class="ml-2 text-gray-700">Normal</span>
                    </div>
                    <div class="flex items-center mb-2">
                        <span class="font-semibold text-gray-800 w-32">Vendor:</span>
                        <span id="pv_vendor" class="text-gray-700"></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                        <div>
                            <span class="font-medium text-gray-600 text-xs">Delivery Date:</span>
                            <div id="pv_delivery_date" class="text-gray-700 text-sm">—</div>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 text-xs">Delivery Address:</span>
                            <div id="pv_delivery_address" class="text-gray-700 text-sm">—</div>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 text-xs">Delivery Terms:</span>
                            <div id="pv_delivery_terms" class="text-gray-700 text-sm">—</div>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 text-xs">Payment Method:</span>
                            <div id="pv_payment_method" class="text-gray-700 text-sm">—</div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <span class="font-medium text-gray-600 text-xs">General Order Note:</span>
                        <div id="pv_note" class="text-gray-700 text-sm italic">—</div>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="font-bold text-gray-800 mb-3">Order Items</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-2 border text-left text-xs">Item</th>
                                    <th class="p-2 border text-center text-xs w-20">Qty</th>
                                    <th class="p-2 border text-center text-xs w-28">Unit Cost</th>
                                    <th class="p-2 border text-center text-xs w-28">Total</th>
                                    <th class="p-2 border text-left text-xs">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="pv_items" class="text-sm"></tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="p-2 text-right font-bold">Subtotal:</td>
                                    <td class="p-2 text-right font-bold" id="pv_subtotal">UGX 0.00</td>
                                    <td class="p-2"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="p-2 text-right">VAT (<span id="pv_vat_rate">0</span>%):</td>
                                    <td class="p-2 text-right" id="pv_vat_amount">UGX 0.00</td>
                                    <td class="p-2"></td>
                                </tr>
                                <tr class="bg-green-50">
                                    <td colspan="3" class="p-2 text-right font-bold text-green-800">GRAND TOTAL:</td>
                                    <td class="p-2 text-right font-bold text-green-800" id="pv_total">UGX 0.00</td>
                                    <td class="p-2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t">
                <button onclick="hidePreview()" type="button" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Close Preview</button>
            </div>
        </div>
    </div>
</form>

<script>
function updateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.table-row-hover').forEach(function(row) {
        let qty = row.querySelector('.item-quantity')?.value;
        let cost = row.querySelector('.item-cost')?.value;
        let totalCell = row.querySelector('.item-total');
        if (totalCell && qty && cost) {
            let total = parseFloat(qty) * parseFloat(cost);
            totalCell.innerText = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            subtotal += total;
        }
    });

    const vatRate = parseFloat(document.getElementById('vat_rate')?.value) || 0;
    const vatAmount = (subtotal * vatRate) / 100;
    const total = subtotal + vatAmount;

    document.getElementById('subtotal_display').innerHTML = `UGX ${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('vat_rate_display').innerHTML = vatRate;
    document.getElementById('vat_amount_display').innerHTML = `UGX ${vatAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('vat_amount_summary').innerHTML = `UGX ${vatAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('total_display').innerHTML = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('grand_total_display').innerHTML = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
}

document.getElementById('vat_rate')?.addEventListener('input', updateTotals);
document.querySelectorAll('.item-quantity, .item-cost').forEach(el => {
    el.addEventListener('input', updateTotals);
});

function showPreview() {
    let type = document.querySelector('select[name="type"]');
    let typeText = type?.value === 'emergency' ? 'EMERGENCY' : 'Normal';
    let typeClass = type?.value === 'emergency' ? 'type-emergency' : 'type-normal';
    document.getElementById('pv_type').innerHTML = `<span class="type-badge ${typeClass}">${typeText}</span>`;

    let vendor = document.querySelector('select[name="vendor_id"]');
    document.getElementById('pv_vendor').innerText = vendor ? vendor.options[vendor.selectedIndex]?.text || '--' : '--';

    let paymentMethod = document.querySelector('select[name="payment_method"]');
    document.getElementById('pv_payment_method').innerText = paymentMethod ? paymentMethod.options[paymentMethod.selectedIndex]?.text || '--' : '--';

    document.getElementById('pv_delivery_date').innerText = document.querySelector('input[name="expected_delivery_date"]')?.value || '--';
    document.getElementById('pv_delivery_address').innerText = document.querySelector('input[name="delivery_address"]')?.value || '--';
    document.getElementById('pv_delivery_terms').innerText = document.querySelector('input[name="delivery_terms"]')?.value || '--';
    document.getElementById('pv_note').innerText = document.querySelector('textarea[name="notes"]')?.value || '--';

    let rows = document.querySelectorAll('.table-row-hover');
    let html = '';
    let subtotal = 0;

    rows.forEach(function(row) {
        let itemName = row.querySelector('td:first-child')?.innerText.trim();
        let qty = row.querySelector('.item-quantity')?.value || 0;
        let cost = row.querySelector('.item-cost')?.value || 0;
        let note = row.querySelector('textarea[name*="[notes]"]')?.value || '';
        let total = qty && cost ? (parseFloat(qty) * parseFloat(cost)).toFixed(2) : '0.00';
        subtotal += parseFloat(total);

        if (itemName && qty > 0 && cost > 0) {
            html += `<tr>
                <td class="p-2 border">${itemName}</td>
                <td class="p-2 border text-center">${qty}</td>
                <td class="p-2 border text-center">UGX ${parseFloat(cost).toLocaleString()}</td>
                <td class="p-2 border text-center">UGX ${parseFloat(total).toLocaleString()}</td>
                <td class="p-2 border">${note || '--'}</td>
            </tr>`;
        }
    });

    document.getElementById('pv_items').innerHTML = html;

    const vatRate = parseFloat(document.getElementById('vat_rate')?.value) || 0;
    const vatAmount = (subtotal * vatRate) / 100;
    const total = subtotal + vatAmount;

    document.getElementById('pv_subtotal').innerHTML = `UGX ${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('pv_vat_rate').innerHTML = vatRate;
    document.getElementById('pv_vat_amount').innerHTML = `UGX ${vatAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('pv_total').innerHTML = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

    document.getElementById('previewModal').classList.remove('hidden');
}

function hidePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
});
</script>
@endif
@endsection
