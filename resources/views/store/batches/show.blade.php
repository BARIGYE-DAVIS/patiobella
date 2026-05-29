{{-- resources/views/store/batches/show.blade.php --}}

@extends('layouts.store')

@section('title', 'Batch Details')
@section('page-title', 'Batch Details')

@section('content')
<style>
    .info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .info-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    .info-body {
        padding: 1.5rem;
    }
    .info-label {
        font-size: 0.7rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-top: 0.25rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    .status-partially {
        background: #fed7aa;
        color: #9a3412;
    }
    .status-depleted {
        background: #fee2e2;
        color: #991b1b;
    }
    .expiry-warning {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
    }
    .expiry-danger {
        background: #fee2e2;
        border-left: 4px solid #dc2626;
    }
    .progress-bar {
        background: #e5e7eb;
        border-radius: 9999px;
        height: 0.5rem;
        overflow: hidden;
    }
    .progress-fill {
        background: #10b981;
        height: 0.5rem;
        border-radius: 9999px;
        transition: width 0.3s ease;
    }
</style>

<div class="space-y-4">

    {{-- Back Button --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('batches.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Batches
        </a>
        <a href="{{ route('batches.edit', $batch->id) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-edit mr-1"></i> Edit Batch
        </a>
    </div>

    {{-- Batch Information Card --}}
    <div class="info-card">
        <div class="info-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-layer-group mr-2 text-emerald-600"></i>
                Batch Information
            </h3>
            <p class="text-xs text-gray-500 mt-1">Batch Number: {{ $batch->batch_number }}</p>
        </div>
        <div class="info-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <div class="info-label">Item Name</div>
                    <div class="info-value">{{ $item->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="info-label">Item Code</div>
                    <div class="info-value">{{ $item->item_code ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="info-label">Category</div>
                    <div class="info-value">{{ $item->category->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="info-label">Base Unit</div>
                    <div class="info-value">{{ $batch->base_unit ?? $item->base_unit ?? 'piece' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock Information Card --}}
    <div class="info-card">
        <div class="info-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                Stock Information
            </h3>
        </div>
        <div class="info-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <div class="info-label">Initial Quantity</div>
                    <div class="info-value">{{ number_format($batch->initial_quantity, 2) }} {{ $batch->base_unit ?? $item->base_unit ?? 'pcs' }}</div>
                </div>
                <div>
                    <div class="info-label">Remaining Quantity</div>
                    <div class="info-value {{ $batch->remaining_quantity <= 0 ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ number_format($batch->remaining_quantity, 2) }} {{ $batch->base_unit ?? $item->base_unit ?? 'pcs' }}
                    </div>
                </div>
                <div>
                    <div class="info-label">Usage Percentage</div>
                    <div class="info-value">
                        @php
                            $usagePercent = $batch->initial_quantity > 0 ? (($batch->initial_quantity - $batch->remaining_quantity) / $batch->initial_quantity) * 100 : 0;
                        @endphp
                        {{ number_format($usagePercent, 1) }}%
                        <div class="progress-bar mt-2">
                            <div class="progress-fill" style="width: {{ min($usagePercent, 100) }}%"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="info-label">Batch Status</div>
                    <div class="info-value">
                        @php
                            if ($batch->batch_status == 'active') {
                                $statusClass = 'status-active';
                                $statusText = 'Active';
                            } elseif ($batch->batch_status == 'partially_used') {
                                $statusClass = 'status-partially';
                                $statusText = 'Partially Used';
                            } else {
                                $statusClass = 'status-depleted';
                                $statusText = 'Depleted';
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cost Information Card --}}
    <div class="info-card">
        <div class="info-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-dollar-sign mr-2 text-yellow-600"></i>
                Cost Information
            </h3>
        </div>
        <div class="info-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <div class="info-label">Unit Cost</div>
                    <div class="info-value text-emerald-600">UGX {{ number_format($batch->unit_cost, 2) }}</div>
                </div>
                <div>
                    <div class="info-label">Total Cost</div>
                    <div class="info-value">UGX {{ number_format($batch->total_cost, 2) }}</div>
                </div>
                <div>
                    <div class="info-label">Remaining Value</div>
                    <div class="info-value">UGX {{ number_format($batch->remaining_quantity * $batch->unit_cost, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dates Information Card --}}
    <div class="info-card">
        <div class="info-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                Date Information
            </h3>
        </div>
        <div class="info-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <div class="info-label">Manufacture Date</div>
                    <div class="info-value">{{ $batch->manufacture_date ? \Carbon\Carbon::parse($batch->manufacture_date)->format('d M Y') : '—' }}</div>
                </div>
                <div>
                    <div class="info-label">Expiry Date</div>
                    <div class="info-value {{ $batch->expiry_date && $batch->expiry_date < now() ? 'text-red-600' : '' }}">
                        {{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') : '—' }}
                        @if($batch->expiry_date && $batch->expiry_date < now())
                            <span class="ml-2 text-xs text-red-600">(EXPIRED)</span>
                        @elseif($batch->expiry_date && $batch->expiry_date <= now()->addDays(30))
                            <span class="ml-2 text-xs text-orange-600">(Expiring soon)</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="info-label">Created Date</div>
                    <div class="info-value">{{ $batch->created_at ? \Carbon\Carbon::parse($batch->created_at)->format('d M Y H:i') : '—' }}</div>
                </div>
                <div>
                    <div class="info-label">Last Updated</div>
                    <div class="info-value">{{ $batch->updated_at ? \Carbon\Carbon::parse($batch->updated_at)->format('d M Y H:i') : '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Supplier Information Card --}}
    <div class="info-card">
        <div class="info-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-truck mr-2 text-indigo-600"></i>
                Supplier Information
            </h3>
        </div>
        <div class="info-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="info-label">Supplier</div>
                    <div class="info-value">{{ $batch->supplier->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="info-label">Supplier Batch Number</div>
                    <div class="info-value">{{ $batch->supplier_batch_number ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes Card --}}
    @if($batch->notes)
    <div class="info-card">
        <div class="info-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-sticky-note mr-2 text-gray-600"></i>
                Notes
            </h3>
        </div>
        <div class="info-body">
            <p class="text-gray-600 text-sm">{{ $batch->notes }}</p>
        </div>
    </div>
    @endif
</div>
@endsection
