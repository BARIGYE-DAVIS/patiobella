{{-- resources/views/restaurant/cashier/dashboard.blade.php --}}

@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')

@section('page-title', 'Cashier Dashboard')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        transition: all 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-card h3 {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }
    .stat-card .value {
        font-size: 1.5rem;
        font-weight: bold;
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
        background: #f8fafc;
    }
    .text-right {
        text-align: right;
    }
    .badge-completed {
        background: #d1fae5;
        color: #065f46;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .btn-start-order {
        background: #ea580c;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-start-order:hover {
        background: #c2410c;
        color: white;
    }
</style>

<div class="space-y-6">

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-cash-register mr-2"></i>
                    Welcome, {{ Auth::user()->first_name ?? 'Cashier' }}!
                </h2>
                <p class="text-orange-100 mt-1">{{ now()->format('l, F d, Y') }} | {{ now()->format('h:i A') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-chart-line mr-1"></i> Today's Sales</p>
                <p class="text-2xl font-bold">UGX {{ number_format($todaySales, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #10b981;">
            <h3><i class="fas fa-chart-line mr-1"></i> Today's Sales</h3>
            <div class="value text-green-600">UGX {{ number_format($todaySales, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Total revenue today</p>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <h3><i class="fas fa-receipt mr-1"></i> Today's Orders</h3>
            <div class="value text-blue-600">{{ $todayOrders }}</div>
            <p class="text-xs text-gray-500 mt-1">Orders completed</p>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <h3><i class="fas fa-hourglass-half mr-1"></i> Pending Orders</h3>
            <div class="value text-orange-600">{{ $pendingOrders }}</div>
            <p class="text-xs text-gray-500 mt-1">Awaiting completion</p>
        </div>
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <h3><i class="fas fa-user mr-1"></i> Average Order</h3>
            <div class="value text-purple-600">
                UGX {{ $todayOrders > 0 ? number_format($todaySales / $todayOrders, 2) : '0.00' }}
            </div>
            <p class="text-xs text-gray-500 mt-1">Average value today</p>
        </div>
    </div>

    {{-- Recent Orders Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-history mr-2"></i> Recent Orders
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Time</th>
                        <th>Customer Type</th>
                        <th class="text-right">Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="font-mono">{{ $order->order_number }}</td>
                        <td>{{ $order->created_at->format('H:i A') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')) }}</td>
                        <td class="text-right">UGX {{ number_format($order->total_amount, 2) }}</td>
                        <td><span class="badge-completed">Completed</span></td>
                        <td>
                            <a href="{{ route('restaurant.cashier.receipt', $order->id) }}" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-print mr-1"></i> Receipt
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-8">
                            <i class="fas fa-receipt text-4xl mb-2 block"></i>
                            No orders yet today. Start a new order!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Start New Order Button --}}
    <div class="text-center">
        <a href="{{ route('restaurant.cashier.pos') }}" class="btn-start-order">
            <i class="fas fa-plus-circle text-lg"></i>
            Start New Order
        </a>
    </div>
</div>
@endsection
