@extends('layouts.manager')

@section('title', 'Stock Movement Details')

@section('page-title', 'Stock Movement Details')

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
        font-size: 1rem;
        font-weight: 500;
        color: #1f2937;
        margin-top: 0.25rem;
    }
    .info-value-large {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .badge-in {
        background: #d1fae5;
        color: #065f46;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
    .badge-out {
        background: #fee2e2;
        color: #991b1b;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
    .section-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .quantity-box {
        background: #f0f9ff;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        border-left: 4px solid #3b82f6;
    }
    .value-box {
        background: #f0fdf4;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        border-left: 4px solid #10b981;
    }
    .trace-card {
        background: #fefce8;
        border: 1px solid #fde047;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .btn-back {
        padding: 0.5rem 1rem;
        background: #6b7280;
        color: white;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .btn-back:hover {
        background: #4b5563;
    }
    .btn-print {
        padding: 0.5rem 1rem;
        background: #8b5cf6;
        color: white;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .btn-print:hover {
        background: #7c3aed;
    }
</style>

<div class="space-y-4">
    {{-- Back Button --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('manager.stock-movements.index', request()->query()) }}" class="btn-back">
            ← Back to Movements
        </a>
        <button onclick="window.print()" class="btn-print">
            🖨️ Print Details
        </button>
    </div>

    {{-- Main Movement Card --}}
    <div class="detail-card">
        <div class="detail-header">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $movement->movement_number }}</h2>
                <p class="text-xs text-gray-500">
                    Recorded on {{ $movement->created_at ? $movement->created_at->format('F d, Y g:i A') : 'N/A' }}
                </p>
            </div>
            <div>
                @if($movement->movementType && $movement->movementType->sign === '+')
                    <span class="badge-in">📥 STOCK IN (+)</span>
                @else
                    <span class="badge-out">📤 STOCK OUT (-)</span>
                @endif

                @if($movement->approved_at)
                    <span class="badge-in ml-2">✓ Approved</span>
                @else
                    <span class="badge-pending ml-2">⏳ Pending Approval</span>
                @endif
            </div>
        </div>

        <div class="detail-body">
            <div class="info-grid">
                {{-- Item Information --}}
                <div>
                    <h4 class="section-title">Item Information</h4>
                    <div class="info-item">
                        <div class="info-label">Item Name</div>
                        <div class="info-value">{{ $movement->inventoryItem->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Item Code</div>
                        <div class="info-value">{{ $movement->inventoryItem->item_code ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Category</div>
                        <div class="info-value">{{ $movement->inventoryItem->category->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Base Unit</div>
                        <div class="info-value">{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units' }}</div>
                    </div>
                </div>

                {{-- Movement Details --}}
                <div>
                    <h4 class="section-title">Movement Details</h4>
                    <div class="info-item">
                        <div class="info-label">Movement Type</div>
                        <div class="info-value">{{ $movement->movementType->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Movement Date</div>
                        <div class="info-value">{{ $movement->movement_date ? $movement->movement_date->format('F d, Y') : 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Approved By</div>
                        <div class="info-value">{{ $movement->approvedBy->name ?? 'System' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Approved At</div>
                        <div class="info-value">{{ $movement->approved_at ? date('F d, Y g:i A', strtotime($movement->approved_at)) : '—' }}</div>
                    </div>
                </div>

                {{-- Quantity Details --}}
                <div>
                    <h4 class="section-title">Quantity Details</h4>
                    @if($movement->pack_type)
                        <div class="quantity-box">
                            <div class="info-label">Received/Issued</div>
                            <div class="info-value-large">{{ number_format($movement->number_of_packs ?? $movement->quantity) }}</div>
                            <div class="info-value">{{ ucfirst($movement->pack_type) }}(s)</div>
                            <div class="text-xs text-gray-500 mt-1">
                                × {{ number_format($movement->pack_size) }} {{ $movement->base_unit ?? 'pieces' }} per {{ $movement->pack_type }}
                            </div>
                        </div>
                    @else
                        <div class="quantity-box">
                            <div class="info-label">Quantity</div>
                            <div class="info-value-large">{{ number_format($movement->quantity, 2) }}</div>
                            <div class="info-value">{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units' }}</div>
                        </div>
                    @endif

                    <div class="mt-3">
                        <div class="info-item">
                            <div class="info-label">Total (Base Unit)</div>
                            <div class="info-value">{{ number_format($movement->quantity_in_base_unit, 2) }} {{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'pieces' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Financial Details --}}
                <div>
                    <h4 class="section-title">Financial Details</h4>
                    <div class="value-box">
                        <div class="info-label">Unit Cost</div>
                        <div class="info-value">UGX {{ number_format($movement->unit_cost ?? 0, 2) }}</div>
                        <div class="info-label mt-2">Total Value</div>
                        <div class="info-value-large">UGX {{ number_format($movement->total_value ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- Stock Impact --}}
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="info-label">Stock BEFORE Movement</div>
                    <div class="text-xl font-bold text-blue-600">{{ number_format($movement->stock_before ?? 0, 2) }}</div>
                    <div class="text-xs text-gray-500">{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="info-label">Stock AFTER Movement</div>
                    <div class="text-xl font-bold text-green-600">{{ number_format($movement->stock_after ?? 0, 2) }}</div>
                    <div class="text-xs text-gray-500">{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units' }}</div>
                </div>
            </div>

            {{-- Reason / Notes --}}
            @if($movement->reason)
            <div class="mt-4">
                <h4 class="section-title">Reason / Notes</h4>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-700">{{ $movement->reason }}</p>
                </div>
            </div>
            @endif

            {{-- Person Responsible --}}
            <div class="grid grid-cols-2 gap-4 mt-4">
                @if($movement->taken_by)
                <div class="trace-card">
                    <div class="info-label">📋 TAKEN BY (Department Staff)</div>
                    <div class="text-base font-semibold text-gray-800">{{ $movement->taken_by }}</div>
                    <div class="text-xs text-gray-500 mt-1">This person received the items from store</div>
                </div>
                @endif

                @if($movement->returned_by)
                <div class="trace-card">
                    <div class="info-label">🔄 RETURNED BY (Department Staff)</div>
                    <div class="text-base font-semibold text-gray-800">{{ $movement->returned_by }}</div>
                    <div class="text-xs text-gray-500 mt-1">This person returned the items to store</div>
                </div>
                @endif

                @if(!$movement->taken_by && !$movement->returned_by && $movement->createdBy)
                <div class="trace-card">
                    <div class="info-label">👤 CREATED BY (Store Staff)</div>
                    <div class="text-base font-semibold text-gray-800">{{ $movement->createdBy->name ?? 'System' }}</div>
                    <div class="text-xs text-gray-500 mt-1">This person recorded the movement</div>
                </div>
                @endif
            </div>

            {{-- Related Distribution (if this is an issue) --}}
            @if($relatedDistribution)
            <div class="mt-4">
                <h4 class="section-title">📦 Related Department Requisition</h4>
                <div class="bg-blue-50 rounded-lg p-3">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <div class="info-label">Requisition #</div>
                            <div class="font-mono text-sm">{{ $relatedDistribution->departmentRequisition->requisition_number ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Department</div>
                            <div class="font-semibold">{{ $relatedDistribution->departmentRequisition->department->name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Taken By</div>
                            <div class="font-semibold">{{ $relatedDistribution->departmentRequisition->taken_by ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Issue Date</div>
                            <div>{{ $relatedDistribution->created_at ? $relatedDistribution->created_at->format('Y-m-d') : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Related Return (if this is a return) --}}
            @if($relatedReturn)
            <div class="mt-4">
                <h4 class="section-title">🔄 Related Return Transaction</h4>
                <div class="bg-purple-50 rounded-lg p-3">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <div class="info-label">Requisition #</div>
                            <div class="font-mono text-sm">{{ $relatedReturn->departmentRequisition->requisition_number ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Department</div>
                            <div class="font-semibold">{{ $relatedReturn->departmentRequisition->department->name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Returned By</div>
                            <div class="font-semibold">{{ $relatedReturn->departmentRequisition->returned_by ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Return Date</div>
                            <div>{{ $relatedReturn->returned_at ? $relatedReturn->returned_at->format('Y-m-d') : $relatedReturn->created_at->format('Y-m-d') }}</div>
                        </div>
                        @if($relatedReturn->return_reason)
                        <div class="col-span-4">
                            <div class="info-label">Return Reason</div>
                            <div class="text-sm">{{ $relatedReturn->return_reason }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Audit Information --}}
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="info-label">Created By</div>
                        <div class="text-sm">{{ $movement->createdBy->name ?? 'System' }}</div>
                        <div class="text-xs text-gray-400">{{ $movement->created_at ? $movement->created_at->format('F d, Y g:i A') : 'N/A' }}</div>
                    </div>
                    @if($movement->updated_at && $movement->updated_at != $movement->created_at)
                    <div>
                        <div class="info-label">Last Updated By</div>
                        <div class="text-sm">{{ $movement->updatedBy->name ?? 'System' }}</div>
                        <div class="text-xs text-gray-400">{{ $movement->updated_at ? $movement->updated_at->format('F d, Y g:i A') : 'N/A' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
