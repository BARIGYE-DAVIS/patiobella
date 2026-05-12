@extends('layouts.management')

@section('title', 'Goods Received Note Details')

@section('page-title', 'Goods Received Note Details')

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
    .status-completed { background: #d1fae5; color: #065f46; }
    .status-inventory_updated { background: #dbeafe; color: #1e40af; }

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
        <a href="{{ route('management.grns.index', request()->query()) }}" class="btn-back">
            ← Back to GRNs
        </a>
        <button onclick="window.print()" class="btn-print">
            🖨️ Print Details
        </button>
    </div>

    {{-- Main GRN Card --}}
    <div class="detail-card">
        <div class="detail-header">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $grn->grn_number }}</h2>
                <p class="text-xs text-gray-500">
                    Created on {{ $grn->created_at ? $grn->created_at->format('F d, Y g:i A') : 'N/A' }}
                </p>
            </div>
            <div>
                <span class="badge-status status-{{ $grn->status }}">
                    @if($grn->status == 'draft')
                        Draft
                    @elseif($grn->status == 'completed')
                        Completed
                    @elseif($grn->status == 'inventory_updated')
                        Inventory Updated
                    @else
                        {{ ucfirst($grn->status) }}
                    @endif
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
                        <div class="info-value">{{ $grn->vendor->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Contact Person</div>
                        <div class="info-value">{{ $grn->vendor->contact_person ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email / Phone</div>
                        <div class="info-value">
                            {{ $grn->vendor->email ?? 'N/A' }} / {{ $grn->vendor->phone ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                {{-- Receipt Details --}}
                <div>
                    <h4 class="section-title">Receipt Details</h4>
                    <div class="info-item">
                        <div class="info-label">Received Date</div>
                        <div class="info-value">{{ $grn->received_date ? $grn->received_date->format('F d, Y') : 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Delivery Note Number</div>
                        <div class="info-value">{{ $grn->delivery_note_number ?? 'Not provided' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Purchase Order</div>
                        <div class="info-value">
                            <a href="#" class="text-blue-600 hover:underline">
                                {{ $grn->purchaseOrder->po_number ?? 'N/A' }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Financial Summary --}}
                <div>
                    <h4 class="section-title">Financial Summary</h4>
                    <div class="info-item">
                        <div class="info-label">Subtotal</div>
                        <div class="info-value">UGX {{ number_format($grn->subtotal, 2) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tax Amount</div>
                        <div class="info-value">UGX {{ number_format($grn->tax_amount, 2) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Amount</div>
                        <div class="info-value-large text-green-600">UGX {{ number_format($grn->grn_total_amount, 2) }}</div>
                    </div>
                </div>

                {{-- Reference Information --}}
                <div>
                    <h4 class="section-title">Reference Information</h4>
                    <div class="info-item">
                        <div class="info-label">Created By</div>
                        <div class="info-value">{{ $grn->createdBy->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Updated By</div>
                        <div class="info-value">{{ $grn->updatedBy->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Updated At</div>
                        <div class="info-value">{{ $grn->updated_at ? $grn->updated_at->format('F d, Y g:i A') : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($grn->notes)
            <div class="mt-4">
                <h4 class="section-title">Notes</h4>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-700">{{ $grn->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div class="detail-card">
        <div class="detail-header">
            <h4 class="font-semibold text-gray-800">Received Items</h4>
        </div>
        <div class="detail-body">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Item Code</th>
                            <th class="text-right">Ordered Qty</th>
                            <th class="text-right">Received Qty</th>
                            <th class="text-right">Accepted Qty</th>
                            <th class="text-right">Rejected Qty</th>
                            <th>Pack Type</th>
                            <th class="text-right">Pack Size</th>
                            <th>Base Unit</th>
                            <th class="text-right">Unit Cost</th>
                            <th class="text-right">Total Cost</th>
                            <th>Rejection Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalOrdered = 0;
                            $totalReceived = 0;
                            $totalAccepted = 0;
                            $totalRejected = 0;
                            $totalValue = 0;
                        @endphp
                        @foreach($grn->items as $item)
                        @php
                            $totalOrdered += $item->quantity_ordered;
                            $totalReceived += $item->quantity_received;
                            $totalAccepted += $item->quantity_accepted;
                            $totalRejected += $item->quantity_rejected;
                            $totalValue += $item->total_cost;
                        @endphp
                        <tr>
                            <td>{{ $item->inventoryItem->name ?? 'N/A' }}</td>
                            <td>{{ $item->inventoryItem->item_code ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($item->quantity_ordered, 2) }}</td>
                            <td class="text-right">{{ number_format($item->quantity_received, 2) }}</td>
                            <td class="text-right">{{ number_format($item->quantity_accepted, 2) }}</td>
                            <td class="text-right">{{ number_format($item->quantity_rejected, 2) }}</td>
                            <td class="text-center">{{ $item->pack_type ?? '—' }}</td>
                            <td class="text-right">{{ number_format($item->pack_size ?? 0) }}</td>
                            <td class="text-center">{{ $item->base_unit ?? $item->inventoryItem->base_unit ?? 'units' }}</td>
                            <td class="text-right">UGX {{ number_format($item->unit_cost, 2) }}</td>
                            <td class="text-right">UGX {{ number_format($item->total_cost, 2) }}</td>
                            <td>{{ $item->rejection_reason ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="font-semibold">TOTALS</td>
                            <td class="text-right font-semibold">{{ number_format($totalOrdered, 2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($totalReceived, 2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($totalAccepted, 2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($totalRejected, 2) }}</td>
                            <td colspan="3"></td>
                            <td class="text-right font-semibold">UGX {{ number_format($totalValue, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Related Purchase Order Info --}}
    @if($grn->purchaseOrder)
    <div class="detail-card">
        <div class="detail-header">
            <h4 class="font-semibold text-gray-800">Related Purchase Order</h4>
        </div>
        <div class="detail-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="info-item">
                    <div class="info-label">PO Number</div>
                    <div class="info-value">
                        <a href="#" class="text-blue-600 hover:underline">
                            {{ $grn->purchaseOrder->po_number }}
                        </a>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">PO Date</div>
                    <div class="info-value">{{ $grn->purchaseOrder->po_date ? $grn->purchaseOrder->po_date->format('Y-m-d') : 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">PO Status</div>
                    <div class="info-value">
                        <span class="badge-status status-{{ $grn->purchaseOrder->status }}">
                            {{ ucfirst(str_replace('_', ' ', $grn->purchaseOrder->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Timeline / GRN Progress --}}
    <div class="detail-card">
        <div class="detail-header">
            <h4 class="font-semibold text-gray-800">GRN Progress Timeline</h4>
        </div>
        <div class="detail-body">
            <div class="timeline">
                {{-- Created --}}
                <div class="timeline-item">
                    <div class="timeline-dot completed"></div>
                    <div class="timeline-title">GRN Created</div>
                    <div class="timeline-date">{{ $grn->created_at ? $grn->created_at->format('F d, Y g:i A') : 'N/A' }}</div>
                    <div class="text-sm text-gray-600">By: {{ $grn->createdBy->name ?? 'System' }}</div>
                </div>

                {{-- Goods Received --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ $grn->status == 'completed' || $grn->status == 'inventory_updated' ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Goods Received & Verified</div>
                    <div class="timeline-date">{{ $grn->received_date ? $grn->received_date->format('F d, Y') : 'Pending' }}</div>
                    <div class="text-sm text-gray-600">Delivery Note: {{ $grn->delivery_note_number ?? '—' }}</div>
                </div>

                {{-- Inventory Updated --}}
                <div class="timeline-item">
                    <div class="timeline-dot {{ $grn->status == 'inventory_updated' ? 'completed' : 'pending' }}"></div>
                    <div class="timeline-title">Stock Added to Inventory</div>
                    <div class="timeline-date">
                        @if($grn->status == 'inventory_updated')
                            Inventory updated
                        @else
                            Pending stock update
                        @endif
                    </div>
                    <div class="text-sm text-gray-600">
                        @if($grn->status == 'inventory_updated')
                            Stock has been added to inventory system
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
