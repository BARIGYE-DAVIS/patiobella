@forelse($items as $item)
<tr class="hover:bg-gray-50">
    <td class="px-4 py-3 text-sm font-mono">{{ $item->item_code ?? '—' }}</td>
    <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $item->name }}</td>
    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->category->name ?? '—' }}</td>
    <td class="px-4 py-3 text-sm text-right font-semibold">
        @if($item->current_stock > 0)
            <span class="text-green-600">{{ number_format($item->current_stock, 2) }}</span>
        @else
            <span class="text-red-600">{{ number_format($item->current_stock, 2) }}</span>
        @endif
    </td>
    <td class="px-4 py-3 text-sm text-right">UGX {{ number_format($item->unit_cost ?? 0, 2) }}</td>
    <td class="px-4 py-3 text-center">
        @if($item->is_active)
            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
        @else
            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
        @endif
    </td>
    <td class="px-4 py-3 text-center">
        <div class="flex justify-center gap-2">
            <button type="button" onclick="viewItem({{ $item->id }})"
                    class="text-blue-600 hover:text-blue-800" title="View Details">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
            <a href="{{ route('store.inventory.edit', $item->id) }}"
               class="text-amber-600 hover:text-amber-800" title="Edit Item">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <form action="{{ route('store.inventory.destroy', $item->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete Item"
                        onclick="return confirm('Delete this item?')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
        No inventory items found. Click "Add New Item" to create one.
    </td>
</tr>
@endforelse
