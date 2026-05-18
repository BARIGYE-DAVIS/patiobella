{{-- resources/views/bar/requisitions/show.blade.php --}}

@extends('layouts.bar')

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
    .status-partially-issued { background-color: #fed7aa; color: #9c4221; }
    .status-partially-consumed { background-color: #fef3c7; color: #92400e; }
    .status-fully-consumed { background-color: #d1fae5; color: #065f46; }
    .status-partially-returned { background-color: #e9d5ff; color: #6b21a5; }
    .status-returned { background-color: #e5e7eb; color: #374151; }
    .status-rejected { background-color: #fee2e2; color: #991b1b; }
    .status-cancelled { background-color: #f3f4f6; color: #6b7280; }

    .info-label {
        font-weight: 600;
        color: #4b5563;
        width: 140px;
        display: inline-block;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .text-center { text-align: center; }

    .badge-consumed {
        background: #fef3c7;
        color: #92400e;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
    .badge-sold {
        background: #d1fae5;
        color: #065f46;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
    .badge-returned {
        background: #e9d5ff;
        color: #6b21a5;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
    .badge-remaining {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
</style>

@php
    $stockMovement = \App\Models\StockMovement::where('reason', 'LIKE', '%' . $requisition->requisition_number . '%')->first();
@endphp

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('bar.requisitions.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            @if($requisition->status == 'pending')
            <form method="POST" action="{{ route('bar.requisitions.cancel', $requisition->id) }}" class="inline" onsubmit="return confirm('Cancel this requisition?')">
                @csrf
                @method('PUT')
                <button type="submit" class="text-red-600 hover:text-red-800">
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

        {{-- Summary Statistics --}}
        @php
            $totalIssued    = $requisition->items->sum('issued_total_pieces');
            $totalConsumed  = $requisition->items->sum('quantity_consumed');
            $totalSold      = $requisition->items->sum('quantity_sold');
            $totalReturned  = $requisition->items->sum('returned_total_pieces');
            $totalRemaining = $totalIssued - ($totalConsumed + $totalSold + $totalReturned);
        @endphp

        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-5 gap-4 text-center">
                <div>
                    <p class="text-xs text-gray-500">Total Issued</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($totalIssued, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Consumed</p>
                    <p class="text-xl font-bold text-amber-600">{{ number_format($totalConsumed, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Sold</p>
                    <p class="text-xl font-bold text-red-600">{{ number_format($totalSold, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Returned</p>
                    <p class="text-xl font-bold text-purple-600">{{ number_format($totalReturned, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Remaining</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($totalRemaining, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Issuance Information --}}
        @if($stockMovement)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Issuance Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="info-label">Taken By:</span>
                        <span class="text-sm font-semibold text-blue-600">{{ $stockMovement->taken_by ?? 'Not recorded' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Issued Date:</span>
                        <span class="text-sm text-gray-800">{{ $stockMovement->created_at ? $stockMovement->created_at->format('F d, Y g:i A') : '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Issued By (Store):</span>
                        <span class="text-sm text-gray-800">{{ $stockMovement->createdBy->name ?? 'System' }}</span>
                    </div>
                </div>
            </div>

            @if(in_array($requisition->status, ['returned', 'partially_returned']))
            @php
                $returnMovement = \App\Models\StockMovement::where('reason', 'LIKE', '%RETURN%' . $requisition->requisition_number . '%')->first();
            @endphp
            @if($returnMovement)
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Return Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="info-label">Returned By:</span>
                        <span class="text-sm font-semibold text-purple-600">{{ $returnMovement->returned_by ?? 'Not recorded' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Return Date:</span>
                        <span class="text-sm text-gray-800">{{ $returnMovement->created_at ? $returnMovement->created_at->format('F d, Y g:i A') : '—' }}</span>
                    </div>
                </div>
            </div>
            @endif
            @endif
        </div>
        @elseif(in_array($requisition->status, ['issued', 'partially_issued']))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Issuance Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="info-label">Taken By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->taken_by ?? 'Pending store update' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Issued Date:</span>
                        <span class="text-sm text-gray-800">Awaiting store confirmation</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Department Notes --}}
        @if($requisition->department_notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Bar Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $requisition->department_notes }}</p>
            </div>
        </div>
        @endif

        {{-- Store Notes --}}
        @if($requisition->store_notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Store Notes</h4>
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-sm text-blue-700">{{ $requisition->store_notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table with Consumed & Sold Columns --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">Requested Items</h4>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-left" style="width: 18%">Item</th>
                            <th class="text-left" style="width: 8%">Unit</th>
                            <th class="text-center" style="width: 10%">Requested</th>
                            <th class="text-center" style="width: 10%">Issued</th>
                            <th class="text-center" style="width: 10%">Consumed</th>
                            <th class="text-center" style="width: 10%">Sold</th>
                            <th class="text-center" style="width: 10%">Returned</th>
                            <th class="text-center" style="width: 10%">Remaining</th>
                            <th class="text-left" style="width: 14%">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisition->items as $item)
                        @php
                            $unit      = $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units');
                            $requested = (float) ($item->quantity_requested ?? 0);
                            $issued    = (float) ($item->issued_total_pieces ?? $item->quantity_issued ?? 0);
                            $consumed  = (float) ($item->quantity_consumed ?? 0);
                            $sold      = (float) ($item->quantity_sold ?? 0);
                            $returned  = (float) ($item->returned_total_pieces ?? $item->quantity_returned ?? 0);
                            $remaining = $issued - ($consumed + $sold + $returned);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $item->inventoryItem->item_code ?? '' }}</div>
                                @if($item->issued_pack_type && $item->issued_pack_size && $item->quantity_issued)
                                    <div class="text-xs text-blue-500 mt-0.5">
                                        {{ $item->quantity_issued }} {{ $item->issued_pack_type }}(s) × {{ $item->issued_pack_size }} = {{ number_format($issued, 2) }} {{ $unit }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $unit }}</td>
                            <td class="px-4 py-3 text-center">
                                {{ number_format($requested, 2) }}
                                @if($item->requested_pack_type)
                                    <div class="text-xs text-gray-400">{{ $item->requested_pack_type }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-green-600 font-semibold">
                                {{ number_format($issued, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($consumed > 0)
                                    <span class="badge-consumed">{{ number_format($consumed, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($sold > 0)
                                    <span class="badge-sold">{{ number_format($sold, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($returned > 0)
                                    <span class="badge-returned">{{ number_format($returned, 2) }}</span>
                                    @if($item->returned_pack_type && $item->quantity_returned > 0)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $item->quantity_returned }} {{ $item->returned_pack_type }}(s)</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge-remaining">{{ number_format($remaining, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $item->notes ?? '—' }} </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-4 py-3 font-bold">TOTALS</td>
                            <td class="px-4 py-3 text-center font-bold">{{ number_format($requisition->items->sum('quantity_requested'), 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ number_format($totalIssued, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-amber-600">{{ number_format($totalConsumed, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-red-600">{{ number_format($totalSold, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-purple-600">{{ number_format($totalReturned, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600">{{ number_format($totalRemaining, 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Record Consumption Button --}}
    @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']))
    <div class="flex justify-end mt-6 p-6 pt-0">
        <a href="{{ route('bar.requisitions.consume', $requisition->id) }}" class="bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
            Record Consumption
        </a>
    </div>
    @endif
</div>
@endsection
