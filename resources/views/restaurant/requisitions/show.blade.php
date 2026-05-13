{{-- resources/views/restaurant/requisitions/show.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'Requisition Details')

@section('page-title', 'Requisition Details')

@section('content')
<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .detail-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .detail-body {
        padding: 1.5rem;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    .info-item {
        margin-bottom: 0.75rem;
    }
    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #6b7280;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-size: 0.9rem;
        font-weight: 500;
        color: #1f2937;
        margin-top: 0.25rem;
    }
    .info-value-large {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .badge-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-issued { background: #dbeafe; color: #1e40af; }
    .status-partially_issued { background: #fed7aa; color: #9c4221; }
    .status-returned { background: #f3e8ff; color: #6b21a5; }
    .status-partially_returned { background: #f3e8ff; color: #6b21a5; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .status-cancelled { background: #f3f4f6; color: #374151; }

    .section-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
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
    .text-right { text-align: right; }

    .btn-back {
        background: #6b7280;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .btn-back:hover {
        background: #4b5563;
        color: white;
    }
    .btn-print {
        background: #8b5cf6;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .btn-print:hover {
        background: #7c3aed;
        color: white;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        border-left: 2px solid #e5e7eb;
        padding-left: 20px;
        margin-left: 10px;
    }
    .timeline-item:last-child {
        border-left: none;
    }
    .timeline-dot {
        position: absolute;
        left: -8px;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: white;
        border: 2px solid;
    }
    .timeline-dot.completed { border-color: #10b981; background: #d1fae5; }
    .timeline-dot.current { border-color: #3b82f6; background: #dbeafe; }
    .timeline-dot.pending { border-color: #9ca3af; background: #f3f4f6; }
    .timeline-title { font-weight: 600; font-size: 0.8rem; margin-bottom: 4px; }
    .timeline-date { font-size: 0.65rem; color: #6b7280; }
</style>

<div class="space-y-4">

    {{-- Header with Back Button --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('restaurant.requisitions.index') }}" class="btn-back">
            <i class="fas fa-arrow-left mr-1"></i> Back to Requisitions
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print mr-1"></i> Print
        </button>
    </div>

    {{-- Main Requisition Card --}}
    <div class="detail-card">
        <div class="detail-header">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $requisition->requisition_number }}</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Created on {{ $requisition->created_at->format('F d, Y g:i A') }}
                </p>
            </div>
            <div>
                @php
                    $statusClass = 'status-' . str_replace('_', '', $requisition->status);
                @endphp
                <span class="badge-status {{ $statusClass }}">
                    @if($requisition->status == 'pending')
                        <i class="fas fa-clock mr-1"></i> Pending Approval
                    @elseif($requisition->status == 'approved')
                        <i class="fas fa-check-circle mr-1"></i> Approved
                    @elseif($requisition->status == 'issued')
                        <i class="fas fa-boxes mr-1"></i> Issued
                    @elseif($requisition->status == 'partially_issued')
                        <i class="fas fa-boxes mr-1"></i> Partially Issued
                    @elseif($requisition->status == 'returned')
                        <i class="fas fa-undo-alt mr-1"></i> Returned
                    @elseif($requisition->status == 'partially_returned')
                        <i class="fas fa-undo-alt mr-1"></i> Partially Returned
                    @elseif($requisition->status == 'rejected')
                        <i class="fas fa-times-circle mr-1"></i> Rejected
                    @elseif($requisition->status == 'cancelled')
                        <i class="fas fa-ban mr-1"></i> Cancelled
                    @else
                        {{ ucfirst($requisition->status) }}
                    @endif
                </span>
            </div>
        </div>

        <div class="detail-body">
            <div class="info-grid">
                {{-- Requisition Information --}}
                <div>
                    <h4 class="section-title">Requisition Information</h4>
                    <div class="info-item">
                        <div class="info-label">Requisition Number</div>
                        <div class="info-value font-mono">{{ $requisition->requisition_number }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date Needed</div>
                        <div class="info-value">{{ $requisition->date_needed ? $requisition->date_needed->format('F d, Y') : 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Requested By</div>
                        <div class="info-value">{{ $requisition->requestedBy->name ?? 'N/A' }}</div>
                    </div>
                </div>

                {{-- Response Information --}}
                <div>
                    <h4 class="section-title">Response Information</h4>
                    <div class="info-item">
                        <div class="info-label">Responded By</div>
                        <div class="info-value">{{ $requisition->approvedBy->name ?? 'Not yet responded' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Responded At</div>
                        <div class="info-value">{{ $requisition->approved_at ? date('F d, Y g:i A', strtotime($requisition->approved_at)) : '—' }}</div>
                    </div>
                </div>

                {{-- Issuance Information (if issued) --}}
                @if(in_array($requisition->status, ['issued', 'partially_issued', 'returned', 'partially_returned']))
                <div>
                    <h4 class="section-title">Issuance Information</h4>
                    <div class="info-item">
                        <div class="info-label">Taken By</div>
                        <div class="info-value">{{ $requisition->taken_by ?? 'Not yet issued' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Issued Date</div>
                        <div class="info-value">—</div>
                    </div>
                </div>
                @endif

                {{-- Return Information (if returned) --}}
                @if(in_array($requisition->status, ['returned', 'partially_returned']))
                <div>
                    <h4 class="section-title">Return Information</h4>
                    <div class="info-item">
                        <div class="info-label">Returned By</div>
                        <div class="info-value">{{ $requisition->returned_by ?? 'Not recorded' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Return Date</div>
                        <div class="info-value">—</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Department Notes --}}
            @if($requisition->department_notes)
            <div class="mt-4">
                <h4 class="section-title">Notes to Store</h4>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-700">{{ $requisition->department_notes }}</p>
                </div>
            </div>
            @endif

            {{-- Store Notes --}}
            @if($requisition->store_notes)
            <div class="mt-4">
                <h4 class="section-title">Store Notes</h4>
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-sm text-blue-700">{{ $requisition->store_notes }}</p>
                </div>
            </div>
            @endif

            {{-- Rejection Reason --}}
            @if($requisition->status == 'rejected' && $requisition->rejection_reason)
            <div class="mt-4">
                <h4 class="section-title">Rejection Reason</h4>
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="text-sm text-red-700">{{ $requisition->rejection_reason }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div class="detail-card">
        <div class="detail-header">
            <h4 class="font-semibold text-gray-800">Requested Items</h4>
        </div>
        <div class="detail-body">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Metrics</th>
                            <th class="text-right">Requested Qty</th>
                            <th class="text-right">Issued Qty</th>
                            <th class="text-right">Returned Qty</th>
                            <th class="text-right">Consumed</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalRequested = 0;
                            $totalIssued = 0;
                            $totalReturned = 0;
                            $totalConsumed = 0;
                        @endphp
                        @foreach($requisition->items as $item)
                        @php
                            $totalRequested += $item->quantity_requested;
                            $totalIssued += $item->quantity_issued;
                            $totalReturned += $item->quantity_returned;
                            $totalConsumed += $item->quantity_consumed;
                        @endphp
                        <tr>
                            <td>
                                {{ $item->inventoryItem->name ?? 'N/A' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br><span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                            <td>{{ $item->metrics ?: '—' }}</td>
                            <td class="text-right font-semibold">{{ number_format($item->quantity_requested, 2) }}</td>
                            <td class="text-right text-green-600">{{ number_format($item->quantity_issued, 2) }}</td>
                            <td class="text-right text-orange-600">{{ number_format($item->quantity_returned, 2) }}</td>
                            <td class="text-right text-blue-600">{{ number_format($item->quantity_consumed, 2) }}</td>
                            <td>{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="font-semibold">TOTALS</td>
                            <td class="text-right font-semibold">{{ number_format($totalRequested, 2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($totalIssued, 2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($totalReturned, 2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($totalConsumed, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Timeline / Order Progress --}}
    <div class="detail-card">
        <div class="detail-header">
            <h4 class="font-semibold text-gray-800">Requisition Progress Timeline</h4>
        </div>
        <div class="detail-body">
            <div class="timeline">
                {{-- Created --}}
                <div class="timeline-item">
                    <div class="timeline-dot completed"></div>
                    <div class="timeline-title">Requisition Created</div>
                    <div class="timeline-date">{{ $requisition->created_at->format('F d, Y g:i A') }}</div>
                    <div class="text-sm text-gray-600">By: {{ $requisition->requestedBy->name ?? 'System' }}</div>
                </div>

                {{-- Approved --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ $requisition->approved_at ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Approved by Store Manager</div>
                    <div class="timeline-date">{{ $requisition->approved_at ? date('F d, Y g:i A', strtotime($requisition->approved_at)) : 'Pending approval' }}</div>
                    <div class="text-sm text-gray-600">By: {{ $requisition->approvedBy->name ?? '—' }}</div>
                </div>

                {{-- Issued --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ in_array($requisition->status, ['issued', 'partially_issued', 'returned', 'partially_returned']) ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Items Issued</div>
                    <div class="timeline-date">
                        @if(in_array($requisition->status, ['issued', 'partially_issued', 'returned', 'partially_returned']))
                            Items have been issued
                        @else
                            Not yet issued
                        @endif
                    </div>
                </div>

                {{-- Returned --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ in_array($requisition->status, ['returned', 'partially_returned']) ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Items Returned (if any)</div>
                    <div class="timeline-date">
                        @if(in_array($requisition->status, ['returned', 'partially_returned']))
                            Items returned to store
                        @else
                            No returns recorded
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Button for Pending Requisitions --}}
    @if($requisition->status == 'pending')
    <div class="flex justify-end">
        <form method="POST" action="{{ route('restaurant.requisitions.cancel', $requisition->id) }}" onsubmit="return confirm('Are you sure you want to cancel this requisition? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                <i class="fas fa-ban mr-1"></i> Cancel Requisition
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
