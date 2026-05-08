@extends('layouts.store')

@section('title', 'Requisition Details')

@section('page-title', 'Requisition Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('store.requisitions.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            @if($requisition->status == 'pending')
            <a href="{{ route('store.requisitions.edit', $requisition->id) }}" class="text-amber-600 hover:text-amber-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <form method="POST" action="{{ route('store.requisitions.cancel', $requisition->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Cancel this requisition?')">
                    Cancel
                </button>
            </form>
            @endif
            @if($requisition->status == 'rejected')
            <a href="{{ route('store.requisitions.edit', $requisition->id) }}" class="text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l3-3m0 0l3 3m-3-3v10m9-10l3-3m0 0l3 3m-3-3v10"/>
                </svg>
                Respond
            </a>
            @endif
        </div>
    </div>

    <div class="p-6">
        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'fulfilled' => 'bg-blue-100 text-blue-800',
                    'cancelled' => 'bg-gray-100 text-gray-800',
                ];
                $statusText = [
                    'pending' => 'Pending GM Approval',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'fulfilled' => 'Fulfilled',
                    'cancelled' => 'Cancelled',
                ];
            @endphp
            <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$requisition->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusText[$requisition->status] ?? ucfirst($requisition->status) }}
            </span>
        </div>

        {{-- Rejection Reason (only shown when rejected) --}}
        @if($requisition->status == 'rejected')
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $requisition->rejection_reason ?? 'No reason provided' }}</p>
                    @if($requisition->approvedBy)
                        <p class="text-xs text-red-600 mt-2">Rejected by: {{ $requisition->approvedBy->first_name }} {{ $requisition->approvedBy->last_name }} on {{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '' }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Approval Info (only shown when approved) --}}
        @if($requisition->status == 'approved')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-green-800">Approval Confirmation</h4>
                    <p class="text-sm text-green-700 mt-1">This requisition has been approved by General Manager.</p>
                    @if($requisition->approvedBy)
                        <p class="text-xs text-green-600 mt-2">Approved by: {{ $requisition->approvedBy->first_name }} {{ $requisition->approvedBy->last_name }} on {{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '' }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Requisition Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Requisition Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Requisition No:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Date Needed:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->date_needed ? $requisition->date_needed->format('F d, Y') : 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Requested By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->requestedBy ? $requisition->requestedBy->first_name . ' ' . $requisition->requestedBy->last_name : '—' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Response Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Responded By:</span>
                        <span class="text-sm text-gray-800">
                            @if($requisition->status == 'approved')
                                {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : 'Not yet responded' }}
                            @elseif($requisition->status == 'rejected')
                                {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : 'Not yet responded' }}
                            @else
                                Not yet responded
                            @endif
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Responded At:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($requisition->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $requisition->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        {{-- Items Table --}}
<div>
    <h4 class="text-sm font-medium text-gray-500 mb-3">Requested Items</h4>
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Requested Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Approved Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @php
                    $totalRequested = 0;
                    $totalApproved = 0;
                @endphp
                @foreach($requisition->items as $item)
                @php
                    $totalRequested += $item->quantity_requested;
                    $totalApproved += $item->quantity_approved;
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
                            {{ $item->category_name ?: '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $item->metrics ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 text-right font-semibold">
                        {{ number_format($item->quantity_requested, 2) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 text-right">
                        {{ number_format($item->quantity_approved, 2) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-800" colspan="2">Total Items: {{ $requisition->items->count() }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-800"></td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-800 text-right">Total Qty: {{ number_format($totalRequested, 2) }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-800 text-right">{{ number_format($totalApproved, 2) }}</td>
                    <td class="px-4 py-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-sm text-blue-600">Total Items</p>
                <p class="text-2xl font-bold text-blue-800">{{ $requisition->items->count() }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                <p class="text-sm text-yellow-600">Total Requested Quantity</p>
                <p class="text-2xl font-bold text-yellow-800">{{ number_format($totalRequested, 2) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600">Total Approved Quantity</p>
                <p class="text-2xl font-bold text-green-800">{{ number_format($totalApproved, 2) }}</p>
                @if($requisition->status == 'approved' && $totalApproved < $totalRequested)
                    <p class="text-xs text-orange-600 mt-1">Partial approval - Some items reduced</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
