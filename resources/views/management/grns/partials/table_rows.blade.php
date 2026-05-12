{{-- resources/views/management/grns/partials/table_rows.blade.php --}}

@forelse($grns as $grn)
<tr>
    <td>
        <a href="{{ route('management.grns.show', $grn->id) }}" class="text-blue-600 hover:underline font-mono">
            {{ $grn->grn_number }}
        </a>
    </td>
    <td>{{ $grn->received_date ? $grn->received_date->format('Y-m-d') : 'N/A' }}</td>
    <td>{{ $grn->vendor->name ?? 'N/A' }}</td>
    <td>{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</td>
    <td>{{ $grn->delivery_note_number ?? '—' }}</td>
    <td class="text-right">UGX {{ number_format($grn->grn_total_amount, 2) }}</td>
    <td>
        <span class="badge-status status-{{ $grn->status }}">
            @if($grn->status == 'draft')
                Draft
            @elseif($grn->status == 'completed')
                Completed
            @elseif($grn->status == 'inventory_updated')
                Inventory Updated
            @else
                {{ ucfirst($grn->status) }}
            @endif
        </span>
    </td>
    <td>
        <a href="{{ route('management.grns.show', $grn->id) }}" class="text-blue-500 hover:text-blue-700">
            View Details
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-gray-500 py-4">No goods received notes found</td>
</tr>
@endforelse
