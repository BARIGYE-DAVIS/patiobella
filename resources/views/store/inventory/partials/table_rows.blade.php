@forelse($items as $item)
@php
    // Get stock from batches
    $batches = \App\Models\Batch::where('inventory_item_id', $item->id)
        ->where('batch_status', 'active')
        ->get();
    $totalStock = $batches->sum('remaining_quantity');
    $activeBatchCount = $batches->count();

    // Determine stock status
    if ($totalStock <= 0) {
        $stockStatus = 'out_of_stock';
        $statusClass = 'bg-red-100 text-red-700';
        $statusText = 'Out of Stock';
    } elseif ($item->minimum_stock > 0 && $totalStock <= $item->minimum_stock) {
        $stockStatus = 'low_stock';
        $statusClass = 'bg-yellow-100 text-yellow-700';
        $statusText = 'Low Stock';
    } else {
        $stockStatus = 'in_stock';
        $statusClass = 'bg-green-100 text-green-700';
        $statusText = 'In Stock';
    }

    // Calculate progress bar percentage
    $maxRef = $item->maximum_stock > 0 ? $item->maximum_stock : ($item->minimum_stock > 0 ? $item->minimum_stock * 3 : 100);
    $pct = $maxRef > 0 ? min(100, ($totalStock / $maxRef) * 100) : 0;
    $barClass = match($stockStatus) {
        'out_of_stock' => 'bg-red-500',
        'low_stock'    => 'bg-yellow-400',
        default        => 'bg-green-500',
    };
@endphp
<tr class="hover:bg-gray-50 transition-colors">
    {{-- Item name + code --}}
    <td class="px-4 py-3">
        <p class="font-semibold text-gray-800 leading-tight">{{ $item->name }}</p>
        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $item->item_code }}</p>
        @if($item->barcode)
            <p class="text-[10px] text-gray-300 font-mono mt-0.5">barcode: {{ $item->barcode }}</p>
        @endif
    </td>

    {{-- Category --}}
    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
        {{ $item->category->name ?? '—' }}
    </td>

    {{-- Unit of Measurement --}}
    <td class="px-4 py-3">
        <span class="bg-orange-50 text-orange-700 text-xs font-medium px-2 py-1 rounded-md">
            {{ ucfirst($item->unit_of_measurement ?? $item->base_unit ?? 'piece') }}
        </span>
    </td>

    {{-- Current stock + mini progress bar --}}
    <td class="px-4 py-3 text-right">
        <span class="font-semibold text-gray-800 tabular-nums">
            {{ number_format($totalStock, 2) }}
        </span>
        <span class="text-xs text-gray-400 ml-0.5">{{ $item->unit_of_measurement ?? 'pcs' }}</span>

        @if($item->minimum_stock > 0)
            <div class="w-20 h-1.5 bg-gray-100 rounded-full mt-1.5 ml-auto overflow-hidden">
                <div class="{{ $barClass }} h-full rounded-full" style="width: {{ $pct }}%"></div>
            </div>
        @endif
    </td>

    {{-- Status badge --}}
    <td class="px-4 py-3 whitespace-nowrap">
        <span class="inline-block {{ $statusClass }} text-xs font-semibold px-2.5 py-1 rounded-full">
            {{ $statusText }}
        </span>
    </td>

    {{-- Active indicator --}}
    <td class="px-4 py-3 text-center">
        @if($item->is_active)
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500" title="Active"></span>
        @else
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-gray-300" title="Inactive"></span>
        @endif
    </td>

    {{-- Active Batches Count --}}
    <td class="px-4 py-3">
        @if($activeBatchCount > 0)
            <span class="inline-flex items-center gap-1 text-xs text-blue-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                {{ $activeBatchCount }} batch(es)
            </span>
        @else
            <span class="text-xs text-gray-400">No active batches</span>
        @endif
    </td>

    {{-- Actions --}}
    <td class="px-4 py-3">
        <div class="flex items-center gap-1.5">
            <a href="{{ route('store.inventory.show', $item->id) }}"
               class="inline-flex items-center gap-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View
            </a>
            <a href="{{ route('store.inventory.edit', $item->id) }}"
               class="inline-flex items-center border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="px-4 py-16">
        <div class="flex flex-col items-center gap-3 text-gray-400">
            <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">No inventory items found.</p>
            <a href="{{ route('store.inventory.create') }}"
               class="text-sm text-blue-700 hover:underline font-medium">
                Add your first item →
            </a>
        </div>
    </td>
</tr>
@endforelse
