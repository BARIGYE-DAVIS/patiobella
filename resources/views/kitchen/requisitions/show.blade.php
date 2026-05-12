@extends('layouts.kitchen')

@section('title', 'Requisition Details')

@section('page-title', 'Requisition Details')

@section('content')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-approved { background-color: #dbeafe; color: #1e40af; }
    .status-issued { background-color: #d1fae5; color: #065f46; }
    .status-partially_issued { background-color: #fed7aa; color: #9c4221; }
    .status-partially_returned { background-color: #e9d5ff; color: #6b21a5; }
    .status-returned { background-color: #e5e7eb; color: #374151; }
    .status-rejected { background-color: #fee2e2; color: #991b1b; }
    .status-cancelled { background-color: #f3f4f6; color: #6b7280; }
    .info-label {
        font-weight: 600;
        color: #4b5563;
        width: 140px;
        display: inline-block;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('kitchen.requisitions.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            @if($requisition->status == 'pending')
            <form method="POST" action="{{ route('kitchen.requisitions.cancel', $requisition->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Cancel this requisition?')">
                    Cancel
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="p-6">
        {{-- Status Badge --}}
        <div class="mb-6">
            <span class="status-badge status-{{ str_replace('_', '-', $requisition->status) }}">
                {{ ucfirst(str_replace('_', ' ', $requisition->status)) }}
            </span>
        </div>

        {{-- Rejection Reason --}}
        @if($requisition->status == 'rejected' && $requisition->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $requisition->rejection_reason }}</p>
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
                        <span class="info-label">Requisition No:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Date Needed:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->date_needed ? date('F d, Y', strtotime($requisition->date_needed)) : 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Requested By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->requestedBy->first_name ?? '' }} {{ $requisition->requestedBy->last_name ?? '' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Store Response</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="info-label">Approved By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approvedBy->first_name ?? '' }} {{ $requisition->approvedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Approved At:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approved_at ? date('F d, Y g:i A', strtotime($requisition->approved_at)) : 'Not yet' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($requisition->department_notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Kitchen Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $requisition->department_notes }}</p>
            </div>
        </div>
        @endif

        @if($requisition->store_notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Store Notes</h4>
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-sm text-blue-700">{{ $requisition->store_notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">Requested Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metrics</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Requested Qty</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pack Type</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pieces/Pack</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Pieces</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Issued</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Returned</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($requisition->items as $item)
                        @php
                            $totalPieces = $item->requested_pack_type && $item->requested_pack_size
                                ? $item->quantity_requested * $item->requested_pack_size
                                : $item->quantity_requested;
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $item->inventoryItem->name ?? 'N/A' }}
                                <br>
                                <span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $item->metrics ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-semibold">
                                {{ number_format($item->quantity_requested, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm">
                                {{ $item->requested_pack_type ? ucfirst($item->requested_pack_type) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm">
                                {{ $item->requested_pack_size ? number_format($item->requested_pack_size) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-blue-600">
                                {{ number_format($totalPieces, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-green-600">
                                {{ number_format($item->quantity_issued, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-orange-600">
                                {{ number_format($item->quantity_returned, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-700">TOTALS</td>
                            <td class="px-4 py-3 text-center text-sm font-bold">{{ number_format($requisition->items->sum('quantity_requested'), 2) }}</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-right text-sm font-bold">{{ number_format($requisition->total_issued_pieces, 2) }}</td>
                            <td class="px-4 py-3 text-center text-sm font-bold">{{ number_format($requisition->items->sum('quantity_issued'), 2) }}</td>
                            <td class="px-4 py-3 text-center text-sm font-bold">{{ number_format($requisition->items->sum('quantity_returned'), 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
