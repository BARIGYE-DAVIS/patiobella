@forelse($stockCounts as $count)
    @php
        $totalVariance = $count->getTotalVarianceAttribute();
        $varianceClass = $totalVariance < 0 ? 'text-red-600' : ($totalVariance > 0 ? 'text-green-600' : 'text-gray-400');
    @endphp
    <tr class="hover:bg-gray-50 transition">
        <td class="px-4 py-3">
            <span class="font-mono font-medium text-gray-800">{{ $count->count_number }}</span>
        </td>
        <td class="px-4 py-3">
            @if($type === 'store')
                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                    <i class="fas fa-warehouse text-xs"></i> Main Store
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                    <i class="fas fa-building text-xs"></i> {{ $count->location->name ?? 'N/A' }}
                </span>
            @endif
        </td>
        <td class="px-4 py-3 text-center">
            {{ \Carbon\Carbon::parse($count->count_date)->format('d M Y') }}
        </td>
        <td class="px-4 py-3 text-center">
            {{ $count->items->count() }}
        </td>
        <td class="px-4 py-3 text-center">
            <span class="font-semibold {{ $varianceClass }}">
                {{ number_format($totalVariance, 2) }}
            </span>
        </td>
        <td class="px-4 py-3 text-center">
            @php
                $statusColors = [
                    'draft' => 'bg-gray-100 text-gray-700',
                    'in_progress' => 'bg-yellow-100 text-yellow-700',
                    'completed' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                ];
                $statusLabels = [
                    'draft' => 'Draft',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ];
            @endphp
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$count->status] }}">
                {{ $statusLabels[$count->status] }}
            </span>
        </td>
        <td class="px-4 py-3 text-center text-gray-600">
            {{ $count->creator->first_name ?? 'N/A' }}
        </td>
        <td class="px-4 py-3 text-center">
            <div class="flex items-center justify-center gap-2">
                <a href="{{ route('management.stock-counts.show', $count->id) }}"
                   class="text-blue-600 hover:text-blue-800 transition" title="View">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
            <i class="fas fa-clipboard-list text-3xl mb-2 block"></i>
            No stock counts found.
            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to', 'department_id']))
                <div class="mt-2">
                    <span class="text-orange-600 cursor-pointer hover:underline" onclick="document.getElementById('resetBtn')?.click()">
                        Clear all filters
                    </span>
                </div>
            @else
                <a href="{{ route('management.stock-counts.create', ['type' => $type]) }}" class="text-orange-600 hover:underline ml-1">
                    Create your first stock count
                </a>
            @endif
        </td>
    </tr>
@endforelse
