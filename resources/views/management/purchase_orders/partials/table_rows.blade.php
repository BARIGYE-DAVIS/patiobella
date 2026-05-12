@forelse($purchaseOrders as $po)
<tr>
    <td>
        <a href="{{ route('management.purchase-orders.show', $po->id) }}" class="text-blue-600 hover:underline font-mono">
            {{ $po->po_number }}
        </a>
    </td>
    <td>{{ $po->po_date->format('Y-m-d') }}</td>
    <td>{{ $po->vendor->name ?? 'N/A' }}</td>
    <td class="text-right">UGX {{ number_format($po->total_amount, 2) }}</td>
    <td>
        <span class="badge-status status-{{ $po->status }}">
            {{ ucfirst(str_replace('_', ' ', $po->status)) }}
        </span>
    </td>
    <td>{{ $po->orderedBy->name ?? 'N/A' }}</td>
    <td>{{ $po->approvedBy->name ?? 'Not approved' }}</td>
    <td>
        <a href="{{ route('management.purchase-orders.show', $po->id) }}" class="text-blue-500 hover:text-blue-700">
            View
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-gray-500 py-4">No purchase orders found</td>
</tr>
@endforelse
