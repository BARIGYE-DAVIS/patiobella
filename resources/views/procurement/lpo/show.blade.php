@extends('layouts.procurement')

@section('title', 'LPO Details')

@section('page-title', 'Local Purchase Order Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">LPO #{{ $lpo->lpo_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $lpo->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('procurement.lpo.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="p-6">
        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    'pending_director' => 'bg-yellow-100 text-yellow-800',
                    'director_approved' => 'bg-green-100 text-green-800',
                    'director_rejected' => 'bg-red-100 text-red-800',
                    'issued' => 'bg-blue-100 text-blue-800',
                ];
                $statusText = [
                    'pending_director' => 'Pending Director Approval',
                    'director_approved' => 'Approved by Director',
                    'director_rejected' => 'Rejected by Director',
                    'issued' => 'Issued to Vendor',
                ];
            @endphp
            <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$lpo->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusText[$lpo->status] ?? ucfirst($lpo->status) }}
            </span>
        </div>

        {{-- Rejection Reason --}}
        @if($lpo->status == 'director_rejected' && $lpo->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $lpo->rejection_reason }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- LPO Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">LPO Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">LPO Number:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $lpo->lpo_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Requisition #:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->requisition->requisition_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">LPO Date:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->lpo_date->format('F d, Y') }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Expected Delivery:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->expected_delivery_date ? date('F d, Y', strtotime($lpo->expected_delivery_date)) : 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Delivery Address:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->delivery_address ?: 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Delivery Instructions:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->delivery_instructions ?: 'Not specified' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Vendor Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Vendor Name:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Contact Person:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->contact_person ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Phone:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Email:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->email ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($lpo->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Internal Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $lpo->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table with Category --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">LPO Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Unit Cost (UGX)</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Total (UGX)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $totalQuantity = 0;
                        @endphp
                        @foreach($lpo->items as $item)
                        @php
                            $totalQuantity += $item->quantity_approved;
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br>
                                    <span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                           <td class="px-4 py-3 text-sm text-gray-500">
                             <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                             {{ $item->inventoryItem?->category?->name ?: '—' }}
                        </span>
                        </td>
                            <td class="px-4 py-3 text-sm text-gray-500 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->metrics ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold">
                                {{ number_format($item->quantity_approved, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                {{ number_format($item->unit_cost, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                                {{ number_format($item->total_cost, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm font-bold text-gray-700">TOTALS</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ number_format($totalQuantity, 2) }}</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-sm font-bold text-green-700 text-right">{{ number_format($lpo->total_amount, 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-sm text-blue-600">Total Items</p>
                <p class="text-2xl font-bold text-blue-800">{{ $lpo->items->count() }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600">Total LPO Amount</p>
                <p class="text-2xl font-bold text-green-800">UGX {{ number_format($lpo->total_amount, 2) }}</p>
            </div>
        </div>

        {{-- Show approval info if approved --}}
        @if($lpo->status == 'director_approved' && $lpo->approvedBy)
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">Approved by Director</p>
                    <p class="text-xs text-green-600">{{ $lpo->approvedBy->first_name }} {{ $lpo->approvedBy->last_name }} on {{ $lpo->approved_at ? $lpo->approved_at->format('F d, Y g:i A') : '' }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
