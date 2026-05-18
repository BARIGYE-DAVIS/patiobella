@if($orders->count() > 0)
<div class="overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Time</th>
                <th>Items</th>
                <th>Payment</th>
                <th class="text-right">Amount</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td class="font-mono text-xs font-bold">{{ $order->order_number }}</td
                <td class="text-xs">{{ $order->created_at->format('d/m/Y') }}</td
                <td class="text-xs">{{ $order->created_at->format('h:i A') }}</td
                <td class="text-xs">{{ $order->items->count() }} items</td
                <td class="text-xs">
                    @if($order->payment_method == 'cash') 💵 Cash
                    @elseif($order->payment_method == 'card') 💳 Card
                    @elseif($order->payment_method == 'mobile_money') 📱 Mobile Money
                    @else {{ ucfirst($order->payment_method ?? 'N/A') }}
                    @endif
                </td
                <td class="text-right font-semibold">UGX {{ number_format($order->total_amount, 0) }}</td
                <td class="text-center">
                    <a href="{{ route('bar.cashier.receipt', $order->id) }}" class="btn-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td
            </tr
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="5" class="text-right font-bold">TOTAL:</td
                <td class="text-right font-bold">UGX {{ number_format($totalSales, 0) }}</td
                <td></td
            </tr>
        </tfoot>
    </table>
</div>
<div class="mt-4 grid grid-cols-4 gap-4">
    <div class="bg-gray-50 p-3 rounded text-center">
        <p class="text-xs text-gray-500">Total Orders</p>
        <p class="text-xl font-bold text-blue-600">{{ $totalOrders }}</p>
    </div>
    <div class="bg-gray-50 p-3 rounded text-center">
        <p class="text-xs text-gray-500">Total Items</p>
        <p class="text-xl font-bold text-red-600">{{ $totalItems }}</p>
    </div>
    <div class="bg-gray-50 p-3 rounded text-center">
        <p class="text-xs text-gray-500">Average Order</p>
        <p class="text-xl font-bold text-orange-600">UGX {{ number_format($avgOrder, 0) }}</p>
    </div>
    <div class="bg-gray-50 p-3 rounded text-center">
        <p class="text-xs text-gray-500">Total Revenue</p>
        <p class="text-xl font-bold text-green-600">UGX {{ number_format($totalSales, 0) }}</p>
    </div>
</div>
@else
<div class="text-center py-8 text-gray-400">
    <i class="fas fa-chart-line text-4xl mb-2 block"></i>
    No sales found for {{ date('F Y', strtotime($month . '-01')) }}
</div>
@endif
