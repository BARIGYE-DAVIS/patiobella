{{-- resources/views/procurement/cost-prices/index.blade.php --}}

@extends('layouts.procurement')

@section('title', 'Batch Management')
@section('page-title', 'Batch Management')

@section('content')
<style>
    .batch-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .batch-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    .batch-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .batch-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }
    .batch-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    .status-depleted {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-partially {
        background: #fed7aa;
        color: #9a3412;
    }
    .status-expired {
        background: #fef3c7;
        color: #92400e;
    }
    .expiry-warning {
        color: #dc2626;
        font-weight: 600;
    }
    .expiry-soon {
        color: #f59e0b;
        font-weight: 600;
    }
    .filter-bar {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
</style>

<div class="space-y-4">

    {{-- Filters --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('procurement.cost-prices.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Batch # or Item name..."
                       class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Batch Status</label>
                <select name="batch_status" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('batch_status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="partially_used" {{ request('batch_status') == 'partially_used' ? 'selected' : '' }}>Partially Used</option>
                    <option value="depleted" {{ request('batch_status') == 'depleted' ? 'selected' : '' }}>Depleted</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Status</label>
                <select name="expiry_status" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">All</option>
                    <option value="expiring_soon" {{ request('expiry_status') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (30 days)</option>
                    <option value="expired" {{ request('expiry_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('procurement.cost-prices.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Batches Table --}}
    <div class="batch-card">
        <div class="batch-header">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-layer-group mr-2 text-emerald-600"></i>
                Stock Batches
            </h3>
            <p class="text-xs text-gray-500 mt-1">Manage batch costs, expiry dates, and quantities</p>
        </div>

        <div class="overflow-x-auto">
            @if($batches->count() > 0)
                <table class="batch-table">
                    <thead>
                        <tr>
                            <th>Batch Number</th>
                            <th>Item Name</th>
                            <th>Item Code</th>
                            <th>Remaining Qty</th>
                            <th>Unit Cost</th>
                            <th>Total Value</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $batch)
                            @php
                                $isExpired = $batch->expiry_date && $batch->expiry_date < now();
                                $isExpiringSoon = $batch->expiry_date && $batch->expiry_date <= now()->addDays(30) && $batch->expiry_date >= now();

                                if ($isExpired) {
                                    $expiryClass = 'expiry-warning';
                                    $expiryText = 'EXPIRED';
                                } elseif ($isExpiringSoon) {
                                    $expiryClass = 'expiry-soon';
                                    $expiryText = 'Expires soon';
                                } else {
                                    $expiryClass = '';
                                    $expiryText = $batch->expiry_date ? $batch->expiry_date->format('d M Y') : 'No expiry';
                                }

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
                            <tr>
                                <td class="font-mono text-xs">{{ $batch->batch_number }}</td>
                                <td class="font-medium">{{ $batch->inventoryItem->name ?? 'N/A' }}</td>
                                <td>{{ $batch->inventoryItem->item_code ?? 'N/A' }}</td>
                                <td>{{ number_format($batch->remaining_quantity, 2) }} {{ $batch->base_unit }}</td>
                                <td class="font-semibold text-emerald-600">UGX {{ number_format($batch->unit_cost, 2) }}</td>
                                <td>UGX {{ number_format($batch->total_cost, 2) }}</td>
                                <td class="{{ $expiryClass }}">
                                    @if($isExpired)
                                        <span class="status-badge status-depleted">{{ $expiryText }}</span>
                                    @elseif($isExpiringSoon)
                                        <span class="status-badge status-partially">{{ $expiryText }}</span>
                                    @else
                                        {{ $expiryText }}
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('procurement.cost-prices.edit', $batch->id) }}"
                                       class="text-blue-600 hover:text-blue-800 transition" title="Edit Batch">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-layer-group text-4xl mb-2 block"></i>
                    No batches found.
                </div>
            @endif
        </div>

        @if($batches->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $batches->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
