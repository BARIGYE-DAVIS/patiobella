@extends('layouts.store')

@section('title', 'Stock Movement Details')

@section('page-title', 'Stock Movement Details')

@section('content')
<style>
    /* Modern Font System */
    .detail-card {
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(226, 232, 240, 0.6);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }
    .detail-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
    }
    .detail-label {
        font-weight: 600;
        color: #475569;
        width: 160px;
        display: inline-block;
        font-size: 0.875rem;
        letter-spacing: 0.01em;
    }
    .pack-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        color: #b45309;
        padding: 0.25rem 0.875rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        border: 1px solid #fed7aa;
    }
    .movement-in {
        color: #059669;
        background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
        padding: 0.25rem 1rem;
        border-radius: 40px;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-weight: 600;
        font-size: 0.75rem;
        border: 1px solid #a7f3d0;
    }
    .movement-out {
        color: #dc2626;
        background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
        padding: 0.25rem 1rem;
        border-radius: 40px;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-weight: 600;
        font-size: 0.75rem;
        border: 1px solid #fecaca;
    }
    .info-value {
        color: #1e293b;
        font-size: 0.9rem;
        font-weight: 500;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 1rem 1.25rem;
        border: 1px solid #eef2ff;
        transition: all 0.2s;
    }
    .stat-card:hover {
        border-color: #c7d2fe;
        background: #fefefe;
    }
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 1rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    .badge-approved {
        background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .badge-pending {
        background: linear-gradient(135deg, #fef9c3 0%, #fefce8 100%);
        color: #854d0e;
        border: 1px solid #fde047;
    }
    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #334155;
        letter-spacing: -0.01em;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .value-large {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .value-medium {
        font-size: 1.125rem;
        font-weight: 700;
    }
    .info-row {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #eef2ff;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .meta-text {
        font-size: 0.7rem;
        color: #94a3b8;
        letter-spacing: 0.02em;
    }
</style>

<div class="max-w-6xl mx-auto">
    {{-- Header Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex justify-between items-center flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Movement #{{ $movement->movement_number }}</h3>
                </div>
                <p class="text-sm text-slate-500 ml-13">Recorded on {{ $movement->created_at->format('F d, Y g:i A') }}</p>
            </div>
            <a href="{{ route('store.stock-movements.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>

        <div class="p-6">
            {{-- Movement Type & Status Row --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                <div class="stat-card">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Movement Direction</div>
                    @if($movement->movementType && $movement->movementType->sign == '+')
                        <div class="movement-in text-base">📥 Stock In</div>
                    @else
                        <div class="movement-out text-base">📤 Stock Out</div>
                    @endif
                </div>
                <div class="stat-card">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Current Status</div>
                    @if($movement->approved_at)
                        <div class="badge-status badge-approved">✓ Approved</div>
                    @else
                        <div class="badge-status badge-pending">⏳ Pending Approval</div>
                    @endif
                </div>
                <div class="stat-card">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Movement Date</div>
                    <div class="text-slate-800 font-semibold">{{ $movement->movement_date->format('F d, Y') }}</div>
                </div>
            </div>

            {{-- Two Column Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Item Information --}}
                <div class="detail-card">
                    <div class="section-title">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Item Information
                    </div>
                    <div class="space-y-2">
                        <div class="info-row">
                            <span class="detail-label">Item Name:</span>
                            <span class="font-bold text-slate-800">{{ $movement->inventoryItem->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="detail-label">Item Code:</span>
                            <span class="info-value">{{ $movement->inventoryItem->item_code ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="detail-label">Category:</span>
                            <span class="info-value">{{ $movement->inventoryItem->category->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="detail-label">Base Unit:</span>
                            <span class="pack-badge">{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="detail-label">Current Stock:</span>
                            <span class="text-indigo-600 font-bold text-lg">{{ number_format($movement->inventoryItem->current_stock ?? 0, 2) }} <span class="text-sm font-normal text-slate-500">{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units' }}</span></span>
                        </div>
                    </div>
                </div>

                {{-- Movement Details --}}
                <div class="detail-card">
                    <div class="section-title">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Movement Details
                    </div>
                    <div class="space-y-2">
                        <div class="info-row">
                            <span class="detail-label">Movement Number:</span>
                            <span class="font-mono text-sm bg-slate-100 px-2 py-0.5 rounded-md">{{ $movement->movement_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="detail-label">Movement Type:</span>
                            <span class="info-value">{{ $movement->movementType->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="detail-label">Approved By:</span>
                            <span class="info-value">{{ $movement->approvedBy->name ?? 'System' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="detail-label">Approved At:</span>
                            <span class="info-value">{{ $movement->approved_at ? date('F d, Y g:i A', strtotime($movement->approved_at)) : '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quantity Details --}}
            <div class="detail-card mb-6">
                <div class="section-title">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Quantity Details
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-gradient-to-br from-amber-50 to-white rounded-xl p-5 border border-amber-100">
                        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-2">Quantity Received/Issued</p>
                        @if($movement->pack_type)
                            <div class="value-large text-amber-700">
                                {{ number_format($movement->number_of_packs) }} <span class="text-base font-medium">{{ ucfirst($movement->pack_type) }}(s)</span>
                            </div>
                            <div class="text-sm text-slate-500 mt-2 flex items-center gap-1">
                                <span class="font-medium">× {{ number_format($movement->pack_size) }}</span>
                                <span>{{ $movement->base_unit ?? 'pieces' }} per {{ $movement->pack_type }}</span>
                            </div>
                        @else
                            <div class="value-large text-indigo-600">
                                {{ number_format($movement->quantity, 2) }}
                                <span class="text-sm font-medium text-slate-500 ml-1">{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units' }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-5 border border-blue-100">
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Total (Base Unit)</p>
                        <div class="value-large text-slate-800">
                            {{ number_format($movement->quantity_in_base_unit, 2) }}
                            <span class="text-sm font-medium text-slate-500 ml-1">{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'pieces' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Financial Details --}}
            <div class="detail-card mb-6">
                <div class="section-title">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Financial Details
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Unit Cost</p>
                        <div class="value-medium text-slate-800">
                            UGX {{ number_format($movement->unit_cost ?? 0, 2) }}
                            <span class="text-xs text-slate-400 ml-1">/ {{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'unit' }}</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-white rounded-xl p-5 border border-green-100">
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Total Value</p>
                        <div class="value-large text-green-700">
                            UGX {{ number_format($movement->total_value ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reason / Notes --}}
            @if($movement->reason)
            <div class="detail-card mb-6">
                <div class="section-title">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Reason / Notes
                </div>
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <p class="text-slate-700 text-sm leading-relaxed">{{ $movement->reason }}</p>
                </div>
            </div>
            @endif

            {{-- Reference Documents --}}
            <div class="detail-card mb-6">
                <div class="section-title">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reference Documents
                </div>
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                    @if($movement->purchase_order_id)
                        <p class="mb-2 text-sm"><span class="font-semibold text-slate-600">Purchase Order ID:</span> <span class="text-slate-800">{{ $movement->purchase_order_id }}</span></p>
                    @endif
                    @if($movement->goods_received_note_id)
                        <p class="mb-2 text-sm"><span class="font-semibold text-slate-600">GRN ID:</span> <span class="text-slate-800">{{ $movement->goods_received_note_id }}</span></p>
                    @endif
                    @if($movement->reversed_by_movement_id)
                        <p class="mb-2 text-sm"><span class="font-semibold text-slate-600">Reversed by Movement:</span> <span class="text-slate-800">{{ $movement->reversed_by_movement_id }}</span></p>
                    @endif
                    @if(!$movement->purchase_order_id && !$movement->goods_received_note_id && !$movement->reversed_by_movement_id)
                        <p class="text-slate-500 text-sm">No reference documents attached</p>
                    @endif
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="detail-card">
                <div class="section-title">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Audit Information
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Created By</div>
                        <div class="font-semibold text-slate-800">{{ $movement->createdBy->name ?? 'System' }}</div>
                        <div class="meta-text mt-1">{{ $movement->created_at->format('F d, Y g:i A') }}</div>
                    </div>
                    @if($movement->updated_at != $movement->created_at)
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Last Updated By</div>
                        <div class="font-semibold text-slate-800">{{ $movement->updatedBy->name ?? 'System' }}</div>
                        <div class="meta-text mt-1">{{ $movement->updated_at->format('F d, Y g:i A') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
