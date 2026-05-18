{{-- resources/views/bar/stock/show.blade.php --}}

@extends('layouts.bar')

@section('title', 'Stock Item Details')

@section('page-title', 'Stock Item Details')

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
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
    }
    .info-body {
        padding: 1.5rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .info-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 0.8rem;
    }
    .info-value {
        font-weight: 500;
        color: #1f2937;
        font-size: 0.85rem;
    }
    .stock-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .stock-high { background: #d1fae5; color: #065f46; }
    .stock-medium { background: #fef3c7; color: #92400e; }
    .stock-low { background: #fee2e2; color: #991b1b; }
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
    .btn-back {
        background: #6b7280;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-block;
    }
    .btn-back:hover {
        background: #4b5563;
    }
</style>

<div class="mb-4">
    <a href="{{ route('bar.stock.index') }}" class="btn-back">
        <i class="fas fa-arrow-left mr-1"></i> Back to Stock
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Item Information Card --}}
    <div class="info-card">
        <div class="info-header">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i> Item Information
        </div>
        <div class="info-body">
            <div class="info-row">
                <span class="info-label">Item Name:</span>
                <span class="info-value font-semibold">{{ $inventoryItem->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Item Code:</span>
                <span class="info-value font-mono">{{ $inventoryItem->item_code ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Barcode:</span>
                <span class="info-value font-mono">{{ $inventoryItem->barcode ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Category:</span>
                <span class="info-value">{{ $inventoryItem->category->name ?? 'Uncategorized' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Base Unit:</span>
                <span class="info-value">{{ $inventoryItem->base_unit ?? 'units' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Selling Price:</span>
                <span class="info-value">UGX {{ number_format($inventoryItem->selling_price ?? 0, 0) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    @php
                        if ($currentStock > 20) $stockStatus = 'High';
                        elseif ($currentStock > 5) $stockStatus = 'Medium';
                        else $stockStatus = 'Low';
                    @endphp
                    <span class="stock-badge stock-{{ strtolower($stockStatus) }}">{{ $stockStatus }} Stock</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Stock Summary Card --}}
    <div class="info-card">
        <div class="info-header">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i> Stock Summary
        </div>
        <div class="info-body">
            <div class="info-row">
                <span class="info-label">Total Issued to Bar:</span>
                <span class="info-value font-semibold text-green-600">{{ number_format($totalIssued, 2) }} {{ $inventoryItem->base_unit ?? 'units' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Consumed:</span>
                <span class="info-value text-amber-600">{{ number_format($totalConsumed, 2) }} {{ $inventoryItem->base_unit ?? 'units' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Sold:</span>
                <span class="info-value text-red-600">{{ number_format($totalSold, 2) }} {{ $inventoryItem->base_unit ?? 'units' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Returned:</span>
                <span class="info-value text-purple-600">{{ number_format($totalReturned, 2) }} {{ $inventoryItem->base_unit ?? 'units' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Current Stock:</span>
                <span class="info-value font-bold text-blue-600">{{ number_format($currentStock, 2) }} {{ $inventoryItem->base_unit ?? 'units' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Requisition History Table --}}
<div class="info-card">
    <div class="info-header">
        <i class="fas fa-history mr-2 text-blue-600"></i> Requisition History
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Requisition #</th>
                    <th>Date Issued</th>
                    <th>Status</th>
                    <th class="text-right">Issued</th>
                    <th class="text-right">Consumed</th>
                    <th class="text-right">Sold</th>
                    <th class="text-right">Returned</th>
                    <th class="text-right">Remaining</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisitionItems as $item)
                @php
                    $issued = (float) ($item->issued_total_pieces ?? 0);
                    $consumed = (float) ($item->quantity_consumed ?? 0);
                    $sold = (float) ($item->quantity_sold ?? 0);
                    $returned = (float) ($item->returned_total_pieces ?? 0);
                    $remaining = $issued - ($consumed + $sold + $returned);
                    $unit = $item->metrics ?? ($inventoryItem->base_unit ?? 'units');
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="font-mono text-xs">
                        <a href="{{ route('bar.requisitions.show', $item->department_requisition_id) }}" class="text-blue-600 hover:underline">
                            {{ $item->departmentRequisition->requisition_number ?? 'N/A' }}
                        </a>
                    </td
                    <td class="text-xs">{{ $item->created_at ? $item->created_at->format('d/m/Y') : 'N/A' }}</td
                    <td class="text-xs">
                        <span class="status-badge status-{{ $item->departmentRequisition->status ?? 'pending' }}">
                            {{ ucfirst($item->departmentRequisition->status ?? 'Pending') }}
                        </span>
                    </td
                    <td class="text-right">{{ number_format($issued, 2) }} {{ $unit }}</td
                    <td class="text-right">{{ number_format($consumed, 2) }} {{ $unit }}</td
                    <td class="text-right">{{ number_format($sold, 2) }} {{ $unit }}</td
                    <td class="text-right">{{ number_format($returned, 2) }} {{ $unit }}</td
                    <td class="text-right font-semibold {{ $remaining > 0 ? 'text-green-600' : 'text-gray-400' }}">
                        {{ number_format($remaining, 2) }} {{ $unit }}
                    </td
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-400">
                        <i class="fas fa-box-open text-4xl mb-2 block"></i>
                        No requisition history found for this item.
                    </td
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 font-bold">TOTALS</td
                    <td class="px-4 py-3 text-right font-bold">{{ number_format($totalIssued, 2) }}</td
                    <td class="px-4 py-3 text-right font-bold">{{ number_format($totalConsumed, 2) }}</td
                    <td class="px-4 py-3 text-right font-bold">{{ number_format($totalSold, 2) }}</td
                    <td class="px-4 py-3 text-right font-bold">{{ number_format($totalReturned, 2) }}</td
                    <td class="px-4 py-3 text-right font-bold">{{ number_format($currentStock, 2) }}</td
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
