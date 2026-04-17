@extends('layouts.procurement')
@section('title', 'Purchase Order Details')
@section('page-title', 'Purchase Order Details')

@section('content')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-draft {
        background-color: #fef3c7;
        color: #92400e;
    }
    .status-approved {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-sent {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .status-partially_received {
        background-color: #fed7aa;
        color: #9c4221;
    }
    .status-received {
        background-color: #c6f6d5;
        color: #22543d;
    }
    .status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .info-row {
        @apply py-3 border-b border-gray-100;
    }
    .info-label {
        @apply font-semibold text-gray-600 w-32;
    }
    .info-value {
        @apply text-gray-800;
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

<div class="space-y-6">
    {{-- Header with Actions --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white">PO #{{ $purchaseOrder->po_number }}</h2>
                <p class="text-gray-300 text-sm mt-1">Created: {{ $purchaseOrder->created_at->format('F d, Y H:i') }}</p>
            </div>
            <div class="flex space-x-3">
                @if($purchaseOrder->status == 'draft')
                    <a href="{{ route('procurement.purchase-orders.edit', $purchaseOrder->id) }}" 
                       class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit PO
                    </a>
                    <form action="{{ route('procurement.purchase-orders.send', $purchaseOrder->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send to Vendor
                        </button>
                    </form>
                @endif
                
                @if($purchaseOrder->status == 'sent')
                    <form action="{{ route('procurement.purchase-orders.approve', $purchaseOrder->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approve PO
                        </button>
                    </form>
                @endif
                
                @if(in_array($purchaseOrder->status, ['draft', 'sent']))
                    <form action="{{ route('procurement.purchase-orders.destroy', $purchaseOrder->id) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to delete this PO? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete PO
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('procurement.purchase-orders.index') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Status Banner --}}
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-gray-600 font-semibold">Current Status:</span>
                <span class="status-badge status-{{ str_replace('_', '-', $purchaseOrder->status) }}">
                    {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}
                </span>
            </div>
            <div class="text-right">
                <span class="text-sm text-gray-500">Last Updated: {{ $purchaseOrder->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Vendor Information --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden lg:col-span-2">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Vendor Information
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex">
                        <span class="info-label">Vendor Name:</span>
                        <span class="info-value">{{ $purchaseOrder->vendor->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Contact Person:</span>
                        <span class="info-value">{{ $purchaseOrder->vendor->contact_person ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $purchaseOrder->vendor->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $purchaseOrder->vendor->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Address:</span>
                        <span class="info-value">{{ $purchaseOrder->vendor->address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-3">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Order Summary
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between pb-2 border-b">
                        <span class="font-semibold text-gray-600">Subtotal:</span>
                        <span class="text-gray-800">UGX {{ number_format($purchaseOrder->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b">
                        <span class="font-semibold text-gray-600">Tax Amount:</span>
                        <span class="text-gray-800">UGX {{ number_format($purchaseOrder->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="font-bold text-gray-800 text-lg">Total Amount:</span>
                        <span class="font-bold text-green-600 text-lg">UGX {{ number_format($purchaseOrder->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delivery Details --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-3">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Delivery Details
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="font-semibold text-gray-600 block mb-1">Expected Delivery Date</span>
                    <span class="text-gray-800">{{ $purchaseOrder->expected_delivery_date ? date('F d, Y', strtotime($purchaseOrder->expected_delivery_date)) : 'Not specified' }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600 block mb-1">Delivery Address</span>
                    <span class="text-gray-800">{{ $purchaseOrder->delivery_address ?: 'Not specified' }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600 block mb-1">Delivery Terms</span>
                    <span class="text-gray-800">{{ $purchaseOrder->delivery_terms ?: 'Not specified' }}</span>
                </div>
            </div>
            @if($purchaseOrder->notes)
                <div class="mt-4 pt-4 border-t">
                    <span class="font-semibold text-gray-600 block mb-1">Order Notes</span>
                    <span class="text-gray-800">{{ $purchaseOrder->notes }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-3">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Order Items
            </h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border text-left">Item</th>
                        <th class="p-3 border text-center">Quantity Ordered</th>
                        <th class="p-3 border text-center">Quantity Received</th>
                        <th class="p-3 border text-center">Unit Cost (UGX)</th>
                        <th class="p-3 border text-center">Total Cost (UGX)</th>
                        <th class="p-3 border text-left">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrder->items as $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 border font-semibold text-gray-800">
                                {{ $item->inventoryItem->name ?? 'N/A' }}
                            </td>
                            <td class="p-3 border text-center">
                                {{ number_format($item->quantity_ordered, 2) }}
                            </td>
                            <td class="p-3 border text-center">
                                <span class="font-medium {{ $item->quantity_received >= $item->quantity_ordered ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ number_format($item->quantity_received, 2) }}
                                </span>
                                @if($item->quantity_received < $item->quantity_ordered)
                                    <span class="text-xs text-gray-500 block">
                                        ({{ number_format(($item->quantity_received / $item->quantity_ordered) * 100, 1) }}% received)
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 border text-center">
                                UGX {{ number_format($item->unit_cost, 2) }}
                            </td>
                            <td class="p-3 border text-center font-semibold">
                                UGX {{ number_format($item->total_cost, 2) }}
                            </td>
                            <td class="p-3 border">
                                {{ $item->notes ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                No items found for this purchase order.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="4" class="p-3 border text-right">Grand Total:</td>
                        <td class="p-3 border text-center text-green-600 text-lg">
                            UGX {{ number_format($purchaseOrder->total_amount, 2) }}
                        </td>
                        <td class="p-3 border"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Additional Information --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-3">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Additional Information
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="font-semibold text-gray-600 block mb-1">Created By</span>
                    <span class="text-gray-800">{{ $purchaseOrder->createdBy->name ?? 'System' }}</span>
                    <span class="text-xs text-gray-500 block">{{ $purchaseOrder->created_at->format('F d, Y H:i') }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600 block mb-1">Last Updated By</span>
                    <span class="text-gray-800">{{ $purchaseOrder->updatedBy->name ?? 'Not updated' }}</span>
                    @if($purchaseOrder->updated_at != $purchaseOrder->created_at)
                        <span class="text-xs text-gray-500 block">{{ $purchaseOrder->updated_at->format('F d, Y H:i') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection