@forelse($requisitions as $req)
<tr class="hover:bg-gray-50">
    <td class="px-4 py-3 text-sm font-mono font-semibold">{{ $req->requisition_number }}</td>
    <td class="px-4 py-3 text-sm">
        <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
            {{ $req->department->name ?? 'N/A' }}
        </span>
    </td>
    <td class="px-4 py-3 text-sm">{{ $req->requestedBy->first_name ?? '' }} {{ $req->requestedBy->last_name ?? '' }}</td>
    <td class="px-4 py-3 text-sm text-center">{{ $req->created_at->format('Y-m-d') }}</td>
    <td class="px-4 py-3 text-sm text-right">{{ $req->items->count() }}</td>
    <td class="px-4 py-3 text-sm text-right">{{ number_format($req->items->sum('quantity_requested'), 2) }}</td>
    <td class="px-4 py-3 text-sm text-center text-green-600">{{ number_format($req->items->sum('quantity_issued'), 2) }}</td>
    <td class="px-4 py-3 text-sm text-center text-orange-600">{{ number_format($req->items->sum('quantity_returned'), 2) }}</td>
    <td class="px-4 py-3 text-center">
        <span class="status-badge status-{{ str_replace('_', '-', $req->status) }}">
            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
        </span>
    </td>
    <td class="px-4 py-3 text-center">
        <div class="flex justify-center gap-2">
            <a href="{{ route('store.department-requisitions.show', $req->id) }}"
               class="text-blue-600 hover:text-blue-800 text-sm">View</a>

            @if(in_array($req->status, ['issued', 'partially_returned']))
            <a href="{{ route('store.department-requisitions.return-form', $req->id) }}"
               class="btn-return inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Return
            </a>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="px-4 py-8 text-center text-gray-500">No requisitions found.</td>
</tr>
@endforelse
