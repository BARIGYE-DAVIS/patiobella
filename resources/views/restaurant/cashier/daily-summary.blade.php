{{-- resources/views/restaurant/cashier/daily-summary.blade.php --}}

@extends('layouts.cashier')

@section('title', 'Daily Sales Summary')

@section('page-title', 'Daily Sales Summary')

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
    .text-right { text-align: right; }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Daily Sales Summary
                </h2>
                <p class="text-orange-100 mt-1">View sales reports by date</p>
            </div>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('restaurant.cashier.daily-summary') }}" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Select Date</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700">
                    <i class="fas fa-search mr-1"></i> View
                </button>
            </div>
            <div>
                <a href="{{ route('restaurant.cashier.daily-summary', ['date' => date('Y-m-d')]) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400 inline-block">
                    <i class="fas fa-calendar-day mr-1"></i> Today
                </a>
            </div>
        </form>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card" style="border-left-color: #10b981;">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <i class="fas fa-chart-line mr-1"></i> Total Sales
            </h3>
            <div class="text-2xl font-bold text-green-600 mt-1">UGX {{ number_format($totalSales, 2) }}</div>
            <p class="text-xs text-gray-400 mt-1">Revenue for {{ date('F d, Y', strtotime($date)) }}</p>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <i class="fas fa-receipt mr-1"></i> Total Orders
            </h3>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $totalOrders }}</div>
            <p class="text-xs text-gray-400 mt-1">Orders completed</p>
        </div>
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <i class="fas fa-chart-simple mr-1"></i> Average Order
            </h3>
            <div class="text-2xl font-bold text-purple-600 mt-1">UGX {{ number_format($averageOrder, 2) }}</div>
            <p class="text-xs text-gray-400 mt-1">Average per transaction</p>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-list mr-2"></i> Sales Transactions
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Order #</th>
                        <th>Customer Type</th>
                        <th>Payment Method</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Tax</th>
                        <th class="text-right">Total</th>
                        <th>Cashier</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td class="text-sm">{{ $sale->created_at->format('h:i A') }}</td>
                        <td class="font-mono text-sm">{{ $sale->order_number }}</td>
                        <td class="text-sm">{{ ucfirst(str_replace('_', ' ', $sale->customer_type ?? 'dine_in')) }}</td>
                        <td class="text-sm">{{ ucfirst($sale->payment_method ?? 'cash') }}</td>
                        <td class="text-right text-sm">UGX {{ number_format($sale->subtotal, 2) }}</td>
                        <td class="text-right text-sm">UGX {{ number_format($sale->tax_amount, 2) }}</td>
                        <td class="text-right text-sm font-semibold">UGX {{ number_format($sale->total_amount, 2) }}</td>
                        <td class="text-sm">{{ $sale->cashier->first_name ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-8">
                            <i class="fas fa-receipt text-4xl mb-2 block"></i>
                            No sales recorded for {{ date('F d, Y', strtotime($date)) }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-sm font-bold">TOTALS</td>
                        <td class="px-4 py-3 text-right font-bold">UGX {{ number_format($sales->sum('subtotal'), 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold">UGX {{ number_format($sales->sum('tax_amount'), 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">UGX {{ number_format($totalSales, 2) }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-center gap-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 inline-flex items-center gap-2">
            <i class="fas fa-print"></i> Print Report
        </button>
        <a href="{{ route('restaurant.cashier.pos') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700 inline-flex items-center gap-2">
            <i class="fas fa-cash-register"></i> New Sale
        </a>
        <a href="{{ route('restaurant.cashier.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 inline-flex items-center gap-2">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .space-y-6, .space-y-6 * {
            visibility: visible;
        }
        .space-y-6 {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0;
            padding: 20px;
        }
        button, .bg-gradient-to-r, .flex.justify-center, form, .filter-card {
            display: none !important;
        }
    }
</style>
@endsection
