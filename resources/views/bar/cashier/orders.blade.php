{{-- resources/views/bar/cashier/orders.blade.php --}}

@extends('layouts.bar-cashier')

@section('title', 'Bar Orders')

@section('page-title', 'My Orders')

@section('content')
<style>
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
    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
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
        display: inline-block;
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
    .items-cell {
        max-width: 200px;
    }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-receipt mr-2"></i>
                    My Orders
                </h2>
                <p class="text-blue-100 mt-1">View and manage your orders</p>
            </div>
            <div>
                <p class="text-sm"><i class="fas fa-user mr-1"></i> Cashier: {{ Auth::user()->first_name ?? 'N/A' }}</p>
                <p class="text-lg font-bold" id="totalOrdersCount">{{ $orders->total() }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-bar">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <div class="flex gap-3">
                <a href="#" onclick="changeStatus('unpaid')"
                   class="px-4 py-2 rounded-lg text-sm {{ $status === 'unpaid' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    <i class="fas fa-clock mr-1"></i> Unpaid
                    (<span id="unpaidCount">{{ \App\Models\SalesOrder::where('payment_status', 'unpaid')->where('department_id', Auth::user()->department_id)->where('cashier_id', Auth::id())->count() }}</span>)
                </a>
                <a href="#" onclick="changeStatus('paid')"
                   class="px-4 py-2 rounded-lg text-sm {{ $status === 'paid' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    <i class="fas fa-check-circle mr-1"></i> Paid
                    (<span id="paidCount">{{ \App\Models\SalesOrder::where('payment_status', 'paid')->where('department_id', Auth::user()->department_id)->where('cashier_id', Auth::id())->count() }}</span>)
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

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Invoice #</th>
                        <th class="text-left">Date & Time</th>
                        <th class="text-left">Items</th>
                        <th class="text-right">Total Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @forelse($orders as $order)
                    <tr class="order-row">
                        <td class="text-left font-mono text-xs font-bold">{{ $order->order_number }}</td>
                        <td class="text-left text-xs">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="text-left items-cell text-xs">
                            @foreach($order->items->take(3) as $item)
                                <span class="item-badge">{{ \Str::limit($item->item_name, 20) }} ({{ $item->quantity }})</span>
                            @endforeach
                            @if($order->items->count() > 3)
                                <span class="item-badge">+{{ $order->items->count() - 3 }} more</span>
                            @endif
                        </td>
                        <td class="text-right font-semibold">UGX {{ number_format($order->total_amount, 0) }}</td>
                        <td class="text-center">
                            @if($order->payment_status === 'unpaid')
                                <span class="status-badge status-unpaid">Unpaid</span>
                            @else
                                <span class="status-badge status-paid">Paid</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('bar.cashier.orders.show', $order->id) }}" class="btn-view">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="6" class="text-center py-8 text-gray-400">
                            <i class="fas fa-receipt text-4xl mb-2 block"></i>
                            No orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200" id="paginationLinks">
            {{ $orders->appends(['status' => $status, 'search' => request('search')])->links() }}
        </div>
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    let currentStatus = '{{ $status }}';

    function performSearch() {
        const searchTerm = searchInput.value;
        const url = `{{ route('bar.cashier.orders') }}?status=${currentStatus}&search=${encodeURIComponent(searchTerm)}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.tableBody) {
                document.getElementById('ordersTableBody').innerHTML = data.tableBody;
            }
            if (data.pagination) {
                document.getElementById('paginationLinks').innerHTML = data.pagination;
            }
            if (data.unpaidCount !== undefined) {
                document.getElementById('unpaidCount').innerText = data.unpaidCount;
            }
            if (data.paidCount !== undefined) {
                document.getElementById('paidCount').innerText = data.paidCount;
            }

            const url = new URL(window.location.href);
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            } else {
                url.searchParams.delete('search');
            }
            window.history.pushState({}, '', url);
        })
        .catch(error => console.error('Search error:', error));
    }

    function changeStatus(status) {
        currentStatus = status;

        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        url.searchParams.delete('search');
        window.history.pushState({}, '', url);

        if (searchInput) {
            searchInput.value = '';
        }

        fetch(`{{ route('bar.cashier.orders') }}?status=${status}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.tableBody) {
                document.getElementById('ordersTableBody').innerHTML = data.tableBody;
            }
            if (data.pagination) {
                document.getElementById('paginationLinks').innerHTML = data.pagination;
            }
            if (data.unpaidCount !== undefined) {
                document.getElementById('unpaidCount').innerText = data.unpaidCount;
            }
            if (data.paidCount !== undefined) {
                document.getElementById('paidCount').innerText = data.paidCount;
            }

            // Update button styles
            document.querySelectorAll('.px-4.py-2.rounded-lg.text-sm').forEach(btn => {
                if (btn.innerText.toLowerCase().includes(status)) {
                    btn.classList.add('bg-orange-600', 'text-white');
                    btn.classList.remove('bg-gray-200', 'text-gray-700');
                } else {
                    btn.classList.add('bg-gray-200', 'text-gray-700');
                    btn.classList.remove('bg-orange-600', 'text-white');
                }
            });
        })
        .catch(error => console.error('Error:', error));
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
    }
</script>
@endsection
