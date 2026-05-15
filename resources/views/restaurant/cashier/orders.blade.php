@extends('layouts.cashier')

@section('title', 'Orders')

@section('page-title', 'All Orders')

@section('content')
@php
    $currentStatus = $status ?? request()->get('status', 'unpaid');
    $searchTerm = request()->get('search', '');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <div class="flex gap-3">
                <a href="#" onclick="changeStatus('unpaid')"
                   class="px-4 py-2 rounded-lg text-sm {{ $currentStatus === 'unpaid' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    <i class="fas fa-clock mr-1"></i> Unpaid
                    (<span id="unpaidCount">{{ \App\Models\SalesOrder::where('payment_status', 'unpaid')->count() }}</span>)
                </a>
                <a href="#" onclick="changeStatus('paid')"
                   class="px-4 py-2 rounded-lg text-sm {{ $currentStatus === 'paid' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    <i class="fas fa-check-circle mr-1"></i> Paid
                    (<span id="paidCount">{{ \App\Models\SalesOrder::where('payment_status', 'paid')->count() }}</span>)
                </a>
            </div>

            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text"
                       id="searchInput"
                       value="{{ $searchTerm }}"
                       placeholder="Search invoice #..."
                       class="pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left p-3">Invoice #</th>
                    <th class="text-left p-3">Date</th>
                    <th class="text-left p-3">Cashier</th>
                    <th class="text-right p-3">Total</th>
                    <th class="text-center p-3">Status</th>
                    <th class="text-center p-3">Action</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                @forelse($orders as $order)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="p-3 font-mono text-xs font-bold">{{ $order->order_number }}</td>
                    <td class="p-3 text-xs">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                    <td class="p-3 text-xs">{{ $order->cashier->first_name ?? 'N/A' }}</td>
                    <td class="p-3 text-right font-semibold">UGX {{ number_format($order->total_amount, 0) }}</td>
                    <td class="p-3 text-center">
                        @if($order->payment_status === 'unpaid')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Unpaid</span>
                        @else
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Paid</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <a href="{{ route('restaurant.cashier.orders.show', $order->id) }}"
                           class="text-orange-600 hover:text-orange-800 text-sm">
                            <i class="fas fa-eye mr-1"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400">
                        <i class="fas fa-receipt text-4xl mb-2 block"></i>
                        @if($searchTerm)
                            No orders found for invoice # "{{ $searchTerm }}"
                        @else
                            No orders found
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-200" id="paginationLinks">
        {{ $orders->appends(['status' => $currentStatus, 'search' => $searchTerm])->links() }}
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    let currentStatus = '{{ $currentStatus }}';

    function performSearch() {
        const searchTerm = searchInput.value;

        fetch(`{{ route('restaurant.cashier.orders') }}?status=${currentStatus}&search=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
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

        searchInput.value = '';

        fetch(`{{ route('restaurant.cashier.orders') }}?status=${status}`, {
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

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });
</script>
@endsection
