@extends('layouts.waiter')

@section('title', 'My Bills')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-receipt text-orange-500 mr-2"></i>
                        My Bills
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">View and print past orders</p>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchInput" placeholder="Search by order # or table..."
                           class="pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 w-64">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Table</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="billsTableBody">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition bill-row"
                        data-order-number="{{ $order->order_number }}"
                        data-table-number="{{ $order->table_number }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            Table {{ $order->table_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $order->customer_name ?? 'Walk-in' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $order->items->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                            UGX {{ number_format($order->total_amount, 0) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_status === 'paid')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1 text-xs"></i> Paid
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1 text-xs"></i> Unpaid
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="printBill({{ $order->id }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium rounded-lg transition">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-receipt text-4xl mb-3 block opacity-30"></i>
                            <p class="text-sm">No bills found</p>
                            <p class="text-xs mt-1">Orders you take will appear here</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.bill-row');

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();

            rows.forEach(row => {
                const orderNumber = row.getAttribute('data-order-number') || '';
                const tableNumber = row.getAttribute('data-table-number') || '';

                if (orderNumber.toLowerCase().includes(searchTerm) || tableNumber.toLowerCase().includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    function printBill(orderId) {
        window.open('/waiter/bills/' + orderId + '/print', '_blank', 'width=400,height=600');
    }
</script>
@endsection
