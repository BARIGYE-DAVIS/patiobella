@extends('layouts.management')

@section('title', 'Purchase Order Details')

@section('page-title', 'Purchase Order Details')

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
    .status-draft { background: #fef3c7; color: #92400e; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-sent { background: #dbeafe; color: #1e40af; }
    .status-partially_received { background: #fed7aa; color: #9c4221; }
    .status-fully_received { background: #a7f3d0; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }

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

    .btn-back {
        padding: 0.5rem 1rem;
        background: #6b7280;
        color: white;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .btn-back:hover { background: #4b5563; }
    .btn-print {
        padding: 0.5rem 1rem;
        background: #8b5cf6;
        color: white;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .btn-print:hover { background: #7c3aed; }
</style>

<div class="space-y-4">
    {{-- Back Button --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('management.purchase-orders.index', request()->query()) }}" class="btn-back">
            ← Back to Purchase Orders
        </a>
        <button onclick="window.print()" class="btn-print">
            🖨️ Print Details
        </button>
    </div>

    {{-- Main Purchase Order Card --}}
    <div class="detail-card">
        <div class="detail-header">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $purchaseOrder->po_number }}</h2>
                <p class="text-xs text-gray-500">
                    Created on {{ $purchaseOrder->created_at ? $purchaseOrder->created_at->format('F d, Y g:i A') : 'N/A' }}
                </p>
            </div>
            <div>
                <span class="badge-status status-{{ $purchaseOrder->status }}">
                    {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}
                </span>
            </div>
        </div>

        <div class="detail-body">
            <div class="info-grid">
                {{-- Vendor Information --}}
                <div>
                    <h4 class="section-title">Vendor Information</h4>
                    <div class="info-item">
                        <div class="info-label">Vendor Name</div>
                        <div class="info-value">{{ $purchaseOrder->vendor->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Contact Person</div>
                        <div class="info-value">{{ $purchaseOrder->vendor->contact_person ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email / Phone</div>
                        <div class="info-value">
                            {{ $purchaseOrder->vendor->email ?? 'N/A' }} / {{ $purchaseOrder->vendor->phone ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                {{-- Order Details --}}
                <div>
                    <h4 class="section-title">Order Details</h4>
                    <div class="info-item">
                        <div class="info-label">PO Date</div>
                        <div class="info-value">{{ $purchaseOrder->po_date ? $purchaseOrder->po_date->format('F d, Y') : 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Expected Delivery Date</div>
                        <div class="info-value">{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('F d, Y') : 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Delivery Address</div>
                        <div class="info-value">{{ $purchaseOrder->delivery_address ?? 'Not specified' }}</div>
                    </div>
                </div>

                {{-- Financial Summary --}}
                <div>
                    <h4 class="section-title">Financial Summary</h4>
                    <div class="info-item">
                        <div class="info-label">Subtotal</div>
                        <div class="info-value">UGX {{ number_format($purchaseOrder->subtotal, 2) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tax Amount</div>
                        <div class="info-value">UGX {{ number_format($purchaseOrder->tax_amount, 2) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Amount</div>
                        <div class="info-value-large text-green-600">UGX {{ number_format($purchaseOrder->total_amount, 2) }}</div>
                    </div>
                </div>

                {{-- Approval Information --}}
                <div>
                    <h4 class="section-title">Approval Information</h4>
                    <div class="info-item">
                        <div class="info-label">Ordered By</div>
                        <div class="info-value">{{ $purchaseOrder->orderedBy->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Approved By</div>
                        <div class="info-value">{{ $purchaseOrder->approvedBy->name ?? 'Not approved yet' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Approved At</div>
                        <div class="info-value">{{ $purchaseOrder->approved_at ? date('F d, Y g:i A', strtotime($purchaseOrder->approved_at)) : '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($purchaseOrder->notes)
            <div class="mt-4">
                <h4 class="section-title">Notes</h4>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-700">{{ $purchaseOrder->notes }}</p>
                </div>
            </div>
            @endif

            {{-- Delivery Terms --}}
            @if($purchaseOrder->delivery_terms)
            <div class="mt-4">
                <h4 class="section-title">Delivery Terms</h4>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-700">{{ $purchaseOrder->delivery_terms }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div class="detail-card">
        <div class="detail-header">
            <h4 class="font-semibold text-gray-800">Order Items</h4>
        </div>
        <div class="detail-body">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th class="text-right">Quantity Ordered</th>
                            <th class="text-right">Quantity Received</th>
                            <th>Unit</th>
                            <th class="text-right">Unit Cost (UGX)</th>
                            <th class="text-right">Total Cost (UGX)</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalOrderedQty = 0;
                            $totalReceivedQty = 0;
                        @endphp
                        @foreach($purchaseOrder->items as $item)
                        @php
                            $totalOrderedQty += $item->quantity_ordered;
                            $totalReceivedQty += $item->quantity_received;
                        @endphp
                        <tr>
                            <td>{{ $item->inventoryItem->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($item->quantity_ordered, 2) }}</td>
                            <td class="text-right">
                                {{ number_format($item->quantity_received, 2) }}
                                @if($item->quantity_received >= $item->quantity_ordered)
                                    <span class="text-green-500 text-xs ml-1">✓</span>
                                @endif
                            </td>
                            <td>{{ $item->inventoryItem->base_unit ?? 'units' }}</td>
                            <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="text-right">{{ number_format($item->total_cost, 2) }}</td>
                            <td>{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="font-semibold">TOTALS</td>
                            <td class="text-right font-semibold">{{ number_format($totalOrderedQty, 2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($totalReceivedQty, 2) }}</td>
                            <td></td>
                            <td></td>
                            <td class="text-right font-semibold">UGX {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Goods Received Notes (if any) --}}
    @if($purchaseOrder->goodsReceivedNotes && $purchaseOrder->goodsReceivedNotes->count() > 0)
    <div class="detail-card">
        <div class="detail-header">
            <h4 class="font-semibold text-gray-800">Goods Received Notes</h4>
        </div>
        <div class="detail-body">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>GRN Number</th>
                            <th>Received Date</th>
                            <th>Delivery Note #</th>
                            <th class="text-right">Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->goodsReceivedNotes as $grn)
                        <tr>
                            <td>{{ $grn->grn_number }}</td>
                            <td>{{ $grn->received_date ? $grn->received_date->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $grn->delivery_note_number ?? '—' }}</td>
                            <td class="text-right">UGX {{ number_format($grn->grn_total_amount, 2) }}</td>
                            <td>
                                @if($grn->status == 'completed')
                                    <span class="badge-status status-fully_received">Completed</span>
                                @elseif($grn->status == 'inventory_updated')
                                    <span class="badge-status status-approved">Inventory Updated</span>
                                @else
                                    <span class="badge-status status-draft">Draft</span>
                                @endif
                             </td>
                            <td>
                                <a href="#" class="text-blue-500 hover:underline text-sm">View GRN</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Timeline / Order Progress --}}
    <div class="detail-card">
        <div class="detail-header">
            <h4 class="font-semibold text-gray-800">Order Progress Timeline</h4>
        </div>
        <div class="detail-body">
            <div class="timeline">
                {{-- Created --}}
                <div class="timeline-item">
                    <div class="timeline-dot completed"></div>
                    <div class="timeline-title">Purchase Order Created</div>
                    <div class="timeline-date">{{ $purchaseOrder->created_at ? $purchaseOrder->created_at->format('F d, Y g:i A') : 'N/A' }}</div>
                    <div class="text-sm text-gray-600">By: {{ $purchaseOrder->orderedBy->name ?? 'System' }}</div>
                </div>

                {{-- Approved --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ $purchaseOrder->approved_at ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Approved</div>
                    <div class="timeline-date">{{ $purchaseOrder->approved_at ? date('F d, Y g:i A', strtotime($purchaseOrder->approved_at)) : 'Pending approval' }}</div>
                    <div class="text-sm text-gray-600">By: {{ $purchaseOrder->approvedBy->name ?? '—' }}</div>
                </div>

                {{-- Sent to Vendor --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ $purchaseOrder->status == 'sent' || $purchaseOrder->status == 'partially_received' || $purchaseOrder->status == 'fully_received' ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Sent to Vendor</div>
                    <div class="timeline-date">
                        @if(in_array($purchaseOrder->status, ['sent', 'partially_received', 'fully_received']))
                            Order transmitted
                        @else
                            Not yet sent
                        @endif
                    </div>
                </div>

                {{-- Partially Received --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ $purchaseOrder->status == 'partially_received' || $purchaseOrder->status == 'fully_received' ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Partially Received</div>
                    <div class="timeline-date">
                        @if($purchaseOrder->status == 'partially_received' || $purchaseOrder->status == 'fully_received')
                            Some items received
                        @else
                            Awaiting delivery
                        @endif
                    </div>
                </div>

                {{-- Fully Received --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ $purchaseOrder->status == 'fully_received' ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Fully Received</div>
                    <div class="timeline-date">
                        @if($purchaseOrder->status == 'fully_received')
                            Order complete
                        @else
                            Pending completion
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
