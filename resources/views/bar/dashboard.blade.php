{{-- resources/views/bar/dashboard.blade.php --}}

@extends('layouts.bar')

@section('title', 'Bar Dashboard')

@section('page-title', 'Bar Dashboard')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        transition: all 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: bold;
        margin-top: 0.5rem;
    }
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #6b7280;
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
    .stock-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .stock-high { background: #d1fae5; color: #065f46; }
    .stock-low { background: #fee2e2; color: #991b1b; }
    .btn-quick {
        display: block;
        background: white;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
        text-decoration: none;
    }
    .btn-quick:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-color: #3b82f6;
    }
</style>

<div class="space-y-6">

    {{-- Header Welcome --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-wine-bottle mr-2"></i>
                    Welcome back, {{ Auth::user()->first_name ?? 'Bar Staff' }}!
                </h2>
                <p class="text-blue-100 mt-1">{{ now()->format('l, F d, Y') }} | {{ now()->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-sm"><i class="fas fa-chart-line mr-1"></i> Today's Sales</p>
                <p class="text-2xl font-bold">UGX {{ number_format($todaySales, 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-label"><i class="fas fa-dollar-sign mr-1"></i> Today's Sales</div>
            <div class="stat-value text-green-600">UGX {{ number_format($todaySales, 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-label"><i class="fas fa-receipt mr-1"></i> Today's Orders</div>
            <div class="stat-value text-blue-600">{{ number_format($todayOrders) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-label"><i class="fas fa-boxes mr-1"></i> Stock Value</div>
            <div class="stat-value text-orange-600">UGX {{ number_format($totalStockValue, 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-label"><i class="fas fa-exclamation-triangle mr-1"></i> Low Stock Items</div>
            <div class="stat-value text-red-600">{{ number_format($lowStockCount) }}</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="{{ route('bar.pos') }}" class="btn-quick">
            <i class="fas fa-cash-register text-2xl text-blue-600 mb-2 block"></i>
            <span class="font-semibold text-gray-800">Point of Sale</span>
            <p class="text-xs text-gray-500 mt-1">Process customer orders</p>
        </a>
        <a href="{{ route('bar.stock.index') }}" class="btn-quick">
            <i class="fas fa-boxes text-2xl text-green-600 mb-2 block"></i>
            <span class="font-semibold text-gray-800">My Stock</span>
            <p class="text-xs text-gray-500 mt-1">View current inventory</p>
        </a>
        <a href="{{ route('bar.requisitions.create') }}" class="btn-quick">
            <i class="fas fa-clipboard-list text-2xl text-purple-600 mb-2 block"></i>
            <span class="font-semibold text-gray-800">Request Stock</span>
            <p class="text-xs text-gray-500 mt-1">Create new requisition</p>
        </a>
        <a href="{{ route('bar.sales.index') }}" class="btn-quick">
            <i class="fas fa-chart-line text-2xl text-orange-600 mb-2 block"></i>
            <span class="font-semibold text-gray-800">Sales Report</span>
            <p class="text-xs text-gray-500 mt-1">View sales analytics</p>
        </a>
    </div>

    {{-- Low Stock Alert --}}
    @if($lowStockCount > 0)
    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-3 text-xl"></i>
            <div>
                <h3 class="font-semibold text-red-800">Low Stock Alert</h3>
                <p class="text-sm text-red-600">{{ $lowStockCount }} item(s) are running low. Please request more stock from the store.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Stock Overview --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-boxes mr-2 text-blue-600"></i> Stock Overview
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Current Stock</th>
                        <th>Unit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockList as $item)
                    <tr>
                        <td class="font-medium">{{ $item['name'] }}</td>
                        <td>
                            <span class="stock-badge {{ $item['stock'] > 10 ? 'stock-high' : 'stock-low' }}">
                                {{ number_format($item['stock'], 2) }}
                            </span>
                        </td>
                        <td>{{ $item['unit'] }}</td>
                        <td>
                            @if($item['stock'] > 10)
                                <span class="text-green-600 text-xs">✓ In Stock</span>
                            @elseif($item['stock'] > 0)
                                <span class="text-orange-600 text-xs">⚠️ Low Stock</span>
                            @else
                                <span class="text-red-600 text-xs">✗ Out of Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400">
                            <i class="fas fa-box-open text-4xl mb-2 block"></i>
                            No stock items found. Request items from the store.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-history mr-2 text-blue-600"></i> Recent Orders
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date & Time</th>
                        <th>Items</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="font-mono text-sm">{{ $order->order_number }}</td>
                        <td class="text-sm">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="text-sm">{{ $order->items->count() }} items</td>
                        <td class="text-right font-semibold">UGX {{ number_format($order->total_amount, 0) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400">
                            <i class="fas fa-receipt text-4xl mb-2 block"></i>
                            No orders found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pending Requisitions Alert --}}
    @if($pendingRequisitions > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-yellow-800">Pending Requisitions</h3>
                <p class="text-sm text-yellow-600">You have {{ $pendingRequisitions }} requisition(s) waiting for approval.</p>
            </div>
            <a href="{{ route('bar.requisitions.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700">
                View Requisitions
            </a>
        </div>
    </div>
    @endif

</div>
@endsection
