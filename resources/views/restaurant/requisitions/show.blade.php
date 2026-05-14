{{-- resources/views/restaurant/requisitions/show.blade.php --}}

@extends('layouts.restaurant')

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
            <a href="{{ route('restaurant.requisitions.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            @if($requisition->status == 'pending')
            <form method="POST" action="{{ route('restaurant.requisitions.cancel', $requisition->id) }}" class="inline">
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
            <h4 class="text-sm font-medium text-gray-500 mb-2">Restaurant Notes</h4>
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

        {{-- Items Table --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">Requested Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Requested</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Issued</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Returned</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($requisition->items as $item)
                        @php
                            $unit = $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units');

                            // ── REQUESTED ────────────────────────────────────────────────
                            // Requested is always stored as entered by the restaurant:
                            // quantity_requested = number of packs (if pack used) or direct qty
                            $reqPackType = $item->requested_pack_type;
                            $reqPackSize = $item->requested_pack_size;
                            $hasPack     = $reqPackType && $reqPackSize;

                            if ($hasPack) {
                                $requestedDisplay = number_format($item->quantity_requested, 2) . ' ' . ucfirst($reqPackType) . '(s)';
                                $requestedSub     = number_format($reqPackSize) . ' ' . $unit . '/pack';
                            } else {
                                $requestedDisplay = number_format($item->quantity_requested, 2) . ' ' . $unit;
                                $requestedSub     = null;
                            }

                            // ── ISSUED ───────────────────────────────────────────────────
                            // quantity_issued  = packs issued (e.g. 5 cartons) — already in pack units
                            // issued_pack_type = carton / box / etc  (null if direct)
                            // issued_pack_size = pieces per pack      (null if direct)
                            $issuedQty      = (float) ($item->quantity_issued ?? 0);
                            $issuedPackType = $item->issued_pack_type;
                            $issuedPackSize = $item->issued_pack_size;
                            $issuedHasPack  = $issuedPackType && $issuedPackSize;

                            if ($issuedQty <= 0) {
                                $issuedDisplay = '—';
                                $issuedSub     = null;
                            } elseif ($issuedHasPack) {
                                $issuedDisplay = number_format($issuedQty, 2) . ' ' . ucfirst($issuedPackType) . '(s)';
                                $issuedSub     = number_format($item->issued_total_pieces ?? ($issuedQty * $issuedPackSize)) . ' ' . $unit . ' total';
                            } else {
                                $issuedDisplay = number_format($issuedQty, 2) . ' ' . $unit;
                                $issuedSub     = null;
                            }

                            // ── RETURNED ─────────────────────────────────────────────────
                            // quantity_returned  = packs returned (e.g. 2 cartons) — already in pack units
                            // returned_pack_type = carton / box / etc  (null if direct)
                            // returned_pack_size = pieces per pack      (null if direct)
                            $returnedQty      = (float) ($item->quantity_returned ?? 0);
                            $returnedPackType = $item->returned_pack_type;
                            $returnedPackSize = $item->returned_pack_size;
                            $returnedHasPack  = $returnedPackType && $returnedPackSize;

                            // Also account for individual pieces returned (no pack)
                            $returnedPiecesOnly = (float) ($item->returned_total_pieces ?? 0) - ($returnedHasPack ? $returnedQty * $returnedPackSize : $returnedQty);

                            if ((float)($item->returned_total_pieces ?? 0) <= 0) {
                                $returnedDisplay = '—';
                                $returnedSub     = null;
                            } elseif ($returnedHasPack) {
                                $returnedDisplay = number_format($returnedQty, 2) . ' ' . ucfirst($returnedPackType) . '(s)';
                                if ($returnedPiecesOnly > 0) {
                                    $returnedDisplay .= ' + ' . number_format($returnedPiecesOnly, 2) . ' ' . $unit;
                                }
                                $returnedSub = number_format($item->returned_total_pieces) . ' ' . $unit . ' total';
                            } else {
                                $returnedDisplay = number_format($item->returned_total_pieces, 2) . ' ' . $unit;
                                $returnedSub     = null;
                            }
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $item->inventoryItem->name ?? 'N/A' }}
                                <br>
                                <span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $unit }}</td>

                            {{-- REQUESTED --}}
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-800">
                                {{ $requestedDisplay }}
                                @if($requestedSub)
                                    <div class="text-xs text-gray-400 font-normal">{{ $requestedSub }}</div>
                                @endif
                            </td>

                            {{-- ISSUED --}}
                            <td class="px-4 py-3 text-center text-sm font-semibold text-green-700">
                                {{ $issuedDisplay }}
                                @if($issuedSub)
                                    <div class="text-xs text-gray-400 font-normal">{{ $issuedSub }}</div>
                                @endif
                            </td>

                            {{-- RETURNED --}}
                            <td class="px-4 py-3 text-center text-sm font-semibold text-orange-600">
                                {{ $returnedDisplay }}
                                @if($returnedSub)
                                    <div class="text-xs text-gray-400 font-normal">{{ $returnedSub }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr class="border-t border-gray-200">
                            <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-700">TOTALS</td>
                            {{-- Requested total: sum of base units across all items --}}
                            <td class="px-4 py-3 text-center text-sm font-bold text-gray-800">
                                {{ number_format($requisition->items->sum(function($item) {
                                    $packSize = $item->requested_pack_size;
                                    $packType = $item->requested_pack_type;
                                    return ($packType && $packSize)
                                        ? $item->quantity_requested * $packSize
                                        : $item->quantity_requested;
                                }), 2) }} base units
                            </td>
                            {{-- Issued total: sum of issued base units --}}
                            <td class="px-4 py-3 text-center text-sm font-bold text-green-700">
                                {{ number_format($requisition->items->sum(function($item) {
                                    return (float) ($item->issued_total_pieces ?? 0);
                                }), 2) }} base units
                            </td>
                            {{-- Returned total: sum of returned base units --}}
                            <td class="px-4 py-3 text-center text-sm font-bold text-orange-600">
                                {{ number_format($requisition->items->sum(function($item) {
                                    return (float) ($item->returned_total_pieces ?? 0);
                                }), 2) }} base units
                            </td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
