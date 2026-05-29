@forelse($batches as $batch)
    @php
        $isExpired = $batch->expiry_date && $batch->expiry_date < now();
        $isExpiringSoon = $batch->expiry_date && $batch->expiry_date <= now()->addDays(30) && $batch->expiry_date >= now();

        if ($batch->batch_status == 'active') {
            $statusClass = 'bg-green-100 text-green-700';
            $statusText = 'Active';
        } elseif ($batch->batch_status == 'partially_used') {
            $statusClass = 'bg-yellow-100 text-yellow-700';
            $statusText = 'Partially Used';
        } else {
            $statusClass = 'bg-gray-100 text-gray-700';
            $statusText = 'Depleted';
        }

        $remainingPercent = $batch->initial_quantity > 0 ? ($batch->remaining_quantity / $batch->initial_quantity) * 100 : 0;
    @endphp
    <tr class="hover:bg-gray-50 transition">
        <td class="px-4 py-3">
            <div class="font-medium text-gray-800">{{ $batch->inventoryItem->name ?? 'N/A' }}</div>
            <div class="text-xs text-gray-500">{{ $batch->inventoryItem->item_code ?? 'N/A' }}</div>
        </td>
        <td class="px-4 py-3">
            <span class="font-mono font-medium text-gray-800 text-xs">{{ $batch->batch_number }}</span>
        </td>
        <td class="px-4 py-3 text-center">
            {{ number_format($batch->initial_quantity, 2) }}
        </td>
        <td class="px-4 py-3 text-center">
            <div class="flex items-center justify-center gap-2">
                <span class="font-semibold {{ $batch->remaining_quantity <= 0 ? 'text-red-600' : 'text-gray-800' }}">
                    {{ number_format($batch->remaining_quantity, 2) }}
                </span>
                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min($remainingPercent, 100) }}%"></div>
                </div>
            </div>
        </td>
        <td class="px-4 py-3 text-center">
            UGX {{ number_format($batch->unit_cost, 2) }}
        </td>
        <td class="px-4 py-3 text-center">
            {{ $batch->manufacture_date ? \Carbon\Carbon::parse($batch->manufacture_date)->format('d M Y') : '—' }}
        </td>
        <td class="px-4 py-3 text-center">
            @if($isExpired)
                <span class="text-red-600 font-semibold">{{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}</span>
                <span class="ml-1 inline-flex px-1 py-0.5 bg-red-100 text-red-700 text-xs rounded-full">Expired</span>
            @elseif($isExpiringSoon)
                <span class="text-orange-600 font-semibold">{{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}</span>
                <span class="ml-1 inline-flex px-1 py-0.5 bg-orange-100 text-orange-700 text-xs rounded-full">Soon</span>
            @else
                {{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') : '—' }}
            @endif
         </td>
        <td class="px-4 py-3 text-center">
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                {{ $statusText }}
            </span>
         </td>
        <td class="px-4 py-3 text-center">
            <div class="flex items-center justify-center gap-2">
                <a href="{{ route('store.batches.show', $batch->id) }}"
                   class="text-blue-600 hover:text-blue-800 transition" title="View Batch">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('store.batches.edit', $batch->id) }}"
                   class="text-green-600 hover:text-green-800 transition" title="Edit Batch">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
         </td>
     </tr>
@empty
    <tr>
        <td colspan="9" class="px-4 py-8 text-center text-gray-400">
            <i class="fas fa-layer-group text-3xl mb-2 block"></i>
            No batches found.
        </td>
    </tr>
@endforelse
