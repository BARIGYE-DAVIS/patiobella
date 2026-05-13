{{-- resources/views/restaurant/dashboard.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'Restaurant Dashboard')

@section('page-title', 'Restaurant Dashboard')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .stat-card h3 { font-size: 0.7rem; text-transform: uppercase; color: #6b7280; margin-bottom: 0.5rem; letter-spacing: 0.5px; }
    .stat-card .value { font-size: 1.5rem; font-weight: bold; }

    .badge-pending { background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-approved { background: #10b981; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-issued { background: #3b82f6; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-returned { background: #8b5cf6; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }

    .data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .data-table th { background: #f8fafc; padding: 0.75rem; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; }
    .data-table tr:hover { background: #f8fafc; }
    .text-right { text-align: right; }

    .quick-action-btn {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        display: block;
        text-decoration: none;
        color: #374151;
    }
    .quick-action-btn:hover { background: #e5e7eb; transform: translateY(-2px); }

    .two-col { display: flex; gap: 1.5rem; margin-bottom: 1.5rem; }
    .col { flex: 1; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="space-y-6">

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold"><i class="fas fa-utensils mr-2"></i> Welcome, {{ Auth::user()->first_name ?? 'Restaurant Staff' }}!</h2>
                <p class="text-orange-100 mt-1">{{ now()->format('l, F d, Y') }} | {{ now()->format('h:i A') }}</p>
                <p class="text-orange-100 text-sm mt-1"><i class="fas fa-building mr-1"></i> {{ $department->name ?? 'Restaurant' }} Department</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-clipboard-list mr-1"></i> Pending Requisitions</p>
                <p class="text-2xl font-bold">{{ $pendingRequisitions ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <h3><i class="fas fa-clock mr-1"></i> Pending Requisitions</h3>
            <div class="value">{{ number_format($pendingRequisitions ?? 0) }}</div>
            <p class="text-xs text-gray-500 mt-1">Awaiting store approval</p>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <h3><i class="fas fa-check-circle mr-1"></i> Approved Requisitions</h3>
            <div class="value">{{ number_format($approvedRequisitions ?? 0) }}</div>
            <p class="text-xs text-gray-500 mt-1">Ready for pickup</p>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <h3><i class="fas fa-boxes mr-1"></i> Issued This Month</h3>
            <div class="value">{{ number_format($issuedThisMonth ?? 0) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Items received from store</p>
        </div>
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <h3><i class="fas fa-undo-alt mr-1"></i> Returned This Month</h3>
            <div class="value">{{ number_format($returnedThisMonth ?? 0) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Items returned to store</p>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN: Quick Actions & Recent Requisitions --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3"><i class="fas fa-bolt mr-2"></i> Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <a href="{{ route('restaurant.requisitions.create') }}" class="quick-action-btn">
                        <i class="fas fa-plus-circle text-2xl mb-1 d-block"></i>
                        <div class="text-xs font-medium">New Requisition</div>
                    </a>
                    <a href="{{ route('restaurant.requisitions.index') }}" class="quick-action-btn">
                        <i class="fas fa-list text-2xl mb-1 d-block"></i>
                        <div class="text-xs font-medium">My Requisitions</div>
                    </a>
                    <a href="{{ route('restaurant.menu.index') }}" class="quick-action-btn">
                        <i class="fas fa-utensils text-2xl mb-1 d-block"></i>
                        <div class="text-xs font-medium">View Menu</div>
                    </a>
                    <a href="{{ route('restaurant.sales.index') }}" class="quick-action-btn">
                        <i class="fas fa-chart-line text-2xl mb-1 d-block"></i>
                        <div class="text-xs font-medium">Sales Report</div>
                    </a>
                </div>
            </div>

            {{-- Recent Requisitions --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-700"><i class="fas fa-clipboard-list mr-2"></i> Recent Requisitions</h3>
                    <a href="{{ route('restaurant.requisitions.index') }}" class="text-xs text-blue-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Requisition #</th>
                                <th>Items</th>
                                <th class="text-right">Total Qty</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequisitions ?? [] as $req)
                            <tr>
                                <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                <td class="font-mono">{{ $req->requisition_number }}</td>
                                <td>{{ $req->items->count() }} items</td>
                                <td class="text-right">{{ number_format($req->items->sum('quantity_requested'), 2) }}</td>
                                <td>
                                    @if($req->status == 'pending')
                                        <span class="badge-pending">Pending</span>
                                    @elseif($req->status == 'approved')
                                        <span class="badge-approved">Approved</span>
                                    @elseif($req->status == 'issued')
                                        <span class="badge-issued">Issued</span>
                                    @elseif($req->status == 'returned')
                                        <span class="badge-returned">Returned</span>
                                    @else
                                        <span class="badge-pending">{{ ucfirst($req->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('restaurant.requisitions.show', $req->id) }}" class="text-blue-600 text-xs hover:underline">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-gray-500 py-4">No requisitions found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Recent Stock Movements --}}
        <div class="space-y-6">

            {{-- Recent Stock Movements --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-700"><i class="fas fa-exchange-alt mr-2"></i> Recent Stock Movements</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Type</th>
                                <th class="text-right">Qty</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements ?? [] as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('Y-m-d') }}</td>
                                <td>{{ $movement->inventoryItem->name ?? 'N/A' }}</td>
                                <td>
                                    @if($movement->movementType && $movement->movementType->sign == '-')
                                        <span class="badge-issued">Issue</span>
                                    @else
                                        <span class="badge-returned">Return</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($movement->quantity_in_base_unit ?? 0, 2) }}</td>
                                <td>{{ $movement->taken_by ?? $movement->returned_by ?? 'System' }}</td>
                            </tr>
                            @empty
                            <td><td colspan="5" class="text-center text-gray-500 py-4">No stock movements found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Helpful Information --}}
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                <h3 class="font-semibold text-blue-800 mb-2"><i class="fas fa-info-circle mr-1"></i> Quick Tips</h3>
                <ul class="text-xs text-blue-700 space-y-2">
                    <li><i class="fas fa-check-circle text-green-600 mr-1"></i> Create requisitions for items you need from the store</li>
                    <li><i class="fas fa-clock mr-1"></i> Approved requisitions are ready for pickup</li>
                    <li><i class="fas fa-undo-alt mr-1"></i> Return any unused or damaged items to the store</li>
                    <li><i class="fas fa-chart-line mr-1"></i> Check sales reports to track performance</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
