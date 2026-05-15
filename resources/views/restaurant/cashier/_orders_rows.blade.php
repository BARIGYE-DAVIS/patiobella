{{-- resources/views/restaurant/cashier/_orders_rows.blade.php --}}

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
        No orders found
    </td>
</tr>
@endforelse
