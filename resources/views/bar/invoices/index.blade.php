{{-- resources/views/bar/invoices/index.blade.php --}}

@extends('layouts.bar')

@section('title', 'Bar Invoices')

@section('page-title', 'Invoice Management')

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
    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .status-unpaid {
        background: #fef3c7;
        color: #92400e;
    }
    .status-paid {
        background: #d1fae5;
        color: #065f46;
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
    .filter-bar {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .search-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 250px;
    }
    .btn-view {
        background: #3b82f6;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
    }
    .btn-view:hover {
        background: #2563eb;
    }
    .item-badge {
        display: inline-block;
        background: #e5e7eb;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.65rem;
        margin: 0.1rem;
    }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-receipt mr-2"></i>
                    Bar Invoices
                </h2>
                <p class="text-blue-100 mt-1">Manage all bar invoices and payments</p>
            </div>
            <div>
                <p class="text-sm"><i class="fas fa-chart-line mr-1"></i> Total Invoices</p>
                <p class="text-2xl font-bold">{{ $stats['total_invoices'] }}</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-label">Unpaid Invoices</div>
            <div class="stat-value text-orange-600">{{ $stats['total_unpaid'] }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-label">Paid Invoices</div>
            <div class="stat-value text-green-600">{{ $stats['total_paid'] }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value text-blue-600">UGX {{ number_format($stats['total_revenue'], 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value text-red-600">{{ $stats['total_invoices'] }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-bar">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <div class="flex gap-3">
                <a href="{{ route('bar.invoices.index') }}" class="px-4 py-2 rounded-lg text-sm {{ $status == 'all' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    All
                </a>
                <a href="{{ route('bar.invoices.index', ['status' => 'unpaid']) }}" class="px-4 py-2 rounded-lg text-sm {{ $status == 'unpaid' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Unpaid
                </a>
                <a href="{{ route('bar.invoices.index', ['status' => 'paid']) }}" class="px-4 py-2 rounded-lg text-sm {{ $status == 'paid' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Paid
                </a>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text"
                       id="searchInput"
                       value="{{ request('search', '') }}"
                       placeholder="Search by invoice # or item..."
                       class="pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date & Time</th>
                        <th>Cashier</th>
                        <th>Items</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Amount Paid</th>
                        <th class="text-right">Change</th>
                        <th>Payment</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="invoicesTableBody">
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="font-mono text-xs font-bold">{{ $invoice->order_number }}</td>
                        <td class="text-xs">{{ $invoice->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="text-xs">{{ $invoice->cashier->first_name ?? 'N/A' }}</td>
                        <td class="text-xs">
                            @foreach($invoice->items->take(2) as $item)
                                <span class="item-badge">{{ \Str::limit($item->item_name, 15) }} ({{ $item->quantity }})</span>
                            @endforeach
                            @if($invoice->items->count() > 2)
                                <span class="item-badge">+{{ $invoice->items->count() - 2 }}</span>
                            @endif
                        </td>
                        <td class="text-right font-semibold">UGX {{ number_format($invoice->total_amount, 0) }}</td>
                        <td class="text-right">
                            @if($invoice->amount_paid)
                                UGX {{ number_format($invoice->amount_paid, 0) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">
                            @if($invoice->change_amount && $invoice->change_amount > 0)
                                UGX {{ number_format($invoice->change_amount, 0) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-xs">
                            @if($invoice->payment_method)
                                @if($invoice->payment_method == 'cash') 💵 Cash
                                @elseif($invoice->payment_method == 'card') 💳 Card
                                @elseif($invoice->payment_method == 'mobile_money') 📱 Mobile Money
                                @else {{ ucfirst($invoice->payment_method) }}
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-center">
                            @if($invoice->payment_status === 'unpaid')
                                <span class="status-badge status-unpaid">Unpaid</span>
                            @else
                                <span class="status-badge status-paid">Paid</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('bar.invoices.show', $invoice->id) }}" class="btn-view">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-8 text-gray-400">
                            <i class="fas fa-receipt text-4xl mb-2 block"></i>
                            No invoices found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $invoices->appends(['status' => $status, 'search' => request('search')])->links() }}
        </div>
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');

    function performSearch() {
        const searchTerm = searchInput.value;
        const currentUrl = new URL(window.location.href);
        if (searchTerm) {
            currentUrl.searchParams.set('search', searchTerm);
        } else {
            currentUrl.searchParams.delete('search');
        }
        window.location.href = currentUrl.toString();
    }

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
</script>
@endsection
