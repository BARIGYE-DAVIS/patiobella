{{-- resources/views/management/menus/show.blade.php --}}

@extends('layouts.management')

@section('title', 'Menu Details')

@section('page-title', 'Menu Details')

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
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
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
    .data-table tr:hover {
        background: #fef3c7;
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
    .btn-manage-items {
        background: #3b82f6;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-block;
    }
    .btn-manage-items:hover {
        background: #2563eb;
    }
    .badge-category {
        background: #e5e7eb;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.65rem;
        display: inline-block;
    }
</style>

<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('management.menus.index') }}" class="btn-back">
        <i class="fas fa-arrow-left mr-1"></i> Back to Menus
    </a>
    <a href="{{ route('management.menus.items', $menu->id) }}" class="btn-manage-items">
        <i class="fas fa-utensils mr-1"></i> Manage Menu Items
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Menu Information --}}
    <div class="info-card">
        <div class="info-header">
            <i class="fas fa-info-circle mr-2 text-emerald-600"></i> Menu Information
        </div>
        <div class="info-body">
            <div class="info-row">
                <span class="info-label">Menu Name:</span>
                <span class="info-value font-semibold">{{ $menu->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Department:</span>
                <span class="info-value">{{ $menu->department->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Description:</span>
                <span class="info-value">{{ $menu->description ?? 'No description' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sort Order:</span>
                <span class="info-value">{{ $menu->sort_order }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    @if($menu->is_active)
                        <span class="status-badge status-active">Active</span>
                    @else
                        <span class="status-badge status-inactive">Inactive</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="info-card">
        <div class="info-header">
            <i class="fas fa-chart-line mr-2 text-emerald-600"></i> Statistics
        </div>
        <div class="info-body">
            <div class="info-row">
                <span class="info-label">Total Menu Items:</span>
                <span class="info-value font-semibold">{{ $menu->items_count }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Active Items:</span>
                <span class="info-value text-green-600">{{ $menu->active_items_count }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Inactive Items:</span>
                <span class="info-value text-red-600">{{ $menu->items_count - $menu->active_items_count }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Created By:</span>
                <span class="info-value">{{ $menu->creator->first_name ?? 'N/A' }} {{ $menu->creator->last_name ?? '' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Created At:</span>
                <span class="info-value">{{ $menu->created_at->format('d/m/Y h:i A') }}</span>
            </div>
            @if($menu->updated_by)
            <div class="info-row">
                <span class="info-label">Last Updated By:</span>
                <span class="info-value">{{ $menu->updater->first_name ?? 'N/A' }} {{ $menu->updater->last_name ?? '' }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Menu Items Table --}}
<div class="info-card">
    <div class="info-header">
        <i class="fas fa-boxes mr-2 text-emerald-600"></i> Menu Items
        <span class="text-xs text-gray-500 ml-2">({{ $menu->items_count }} items)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th class="text-right">Price (UGX)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @forelse($menu->items as $item)
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td class="font-medium">{{ $item->name }}</td>
                    <td>
                        @if($item->category)
                            <span class="badge-category">{{ $item->category->name }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="text-right">UGX {{ number_format($item->selling_price, 0) }}</td>
                    <td class="text-center">
                        @if($item->is_active)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">
                        <i class="fas fa-utensils text-4xl mb-2 block"></i>
                        No menu items added yet.
                        <div class="mt-2">
                            <a href="{{ route('management.menus.items', $menu->id) }}" class="text-emerald-600 hover:underline">
                                Click here to add items
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
