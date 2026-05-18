{{-- resources/views/bar/cashier/dashboard.blade.php --}}

@extends('layouts.bar-cashier')

@section('title', 'Bar Cashier Dashboard')

@section('page-title', 'Bar Cashier Dashboard')

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
    .stat-value {
        font-size: 1.5rem;
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
    .text-right { text-align: right; }
</style>

<div class="space-y-6">

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-wine-bottle mr-2"></i>
                    Welcome, {{ Auth::user()->first_name ?? 'Bar Cashier' }}!
                </h2>
                <p class="text-blue-100 mt-1">{{ now()->format('l, F d, Y') }} | {{ now()->format('h:i A') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-chart-line mr-1"></i> Today's Sales</p>
                <p class="text-2xl font-bold">UGX {{ number_format($todaySales, 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-label"><i class="fas fa-dollar-sign mr-1"></i> Today's Sales</div>
            <div class="stat-value text-green-600">UGX {{ number_format($todaySales, 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-label"><i class="fas fa-receipt mr-1"></i> Today's Orders</div>
            <div class="stat-value text-blue-600">{{ number_format($todayOrders) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-label"><i class="fas fa-clock mr-1"></i> Unpaid Orders</div>
            <div class="stat-value text-orange-600">{{ number_format($unpaidOrders) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-label"><i class="fas fa-exclamation-triangle mr-1"></i> Low Stock Items</div>
            <div class="stat-value text-red-600">{{ number_format($lowStockCount) }}</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('bar.cashier.pos') }}" class="bg-blue-600 text-white rounded-xl p-4 text-center hover:bg-blue-700 transition">
            <i class="fas fa-cash-register text-2xl mb-2 block"></i>
            <span class="font-semibold">Start New Sale</span>
            <p class="text-xs text-blue-200 mt-1">Process customer orders</p>
        </a>
        <a href="{{ route('bar.cashier.orders') }}" class="bg-green-600 text-white rounded-xl p-4 text-center hover:bg-green-700 transition">
            <i class="fas fa-receipt text-2xl mb-2 block"></i>
            <span class="font-semibold">View Orders</span>
            <p class="text-xs text-green-200 mt-1">Manage pending orders</p>
        </a>
        <a href="{{ route('bar.cashier.my-sales') }}" class="bg-purple-600 text-white rounded-xl p-4 text-center hover:bg-purple-700 transition">
            <i class="fas fa-chart-line text-2xl mb-2 block"></i>
            <span class="font-semibold">My Sales</span>
            <p class="text-xs text-purple-200 mt-1">View your performance</p>
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

    {{-- Recent Orders Table --}}
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
                        <th>Payment Method</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="font-mono text-xs font-bold">{{ $order->order_number }}</td>
                        <td class="text-xs">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="text-xs">{{ $order->items->count() }} items</td>
                        <td class="text-xs">
                            @if($order->payment_method == 'cash') 💵 Cash
                            @elseif($order->payment_method == 'card') 💳 Card
                            @elseif($order->payment_method == 'mobile_money') 📱 Mobile Money
                            @else {{ ucfirst($order->payment_method ?? 'N/A') }}
                            @endif
                        </td>
                        <td class="text-right font-semibold">UGX {{ number_format($order->total_amount, 0) }}</td>
                        <td class="text-center">
                            @if($order->payment_status === 'unpaid')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Unpaid</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Paid</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">
                            <i class="fas fa-receipt text-4xl mb-2 block"></i>
                            No orders found. Start a new sale!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
