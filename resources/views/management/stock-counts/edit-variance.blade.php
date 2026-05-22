{{-- resources/views/management/stock-counts/show.blade.php --}}

@extends('layouts.management')

@section('title', 'Stock Count Details')
@section('page-title', 'Stock Count Details')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $stockCount->count_number }}</h2>
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
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$stockCount->status] }}">
                        {{ $statusLabels[$stockCount->status] }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Count Date: {{ \Carbon\Carbon::parse($stockCount->count_date)->format('F d, Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('management.stock-counts.index', ['type' => $type]) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1">Total Items</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stockCount->items->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1">System Quantity</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stockCount->getTotalSystemQuantityAttribute(), 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1">Physical Net Quantity</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stockCount->getTotalNetQuantityAttribute(), 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1">Total Variance</p>
            @php
                $totalVariance = $stockCount->getTotalVarianceAttribute();
                $varianceColor = $totalVariance < 0 ? 'text-red-600' : ($totalVariance > 0 ? 'text-green-600' : 'text-gray-800');
            @endphp
            <p class="text-2xl font-bold {{ $varianceColor }}">{{ number_format($totalVariance, 2) }}</p>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-800">Counted Items</h3>
            @if($stockCount->status === 'draft')
            <button type="button" onclick="document.getElementById('editModal').classList.remove('hidden')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm transition">
                <i class="fas fa-edit mr-1"></i> Edit Quantities
            </button>
            @endif
            @if($stockCount->status === 'in_progress' && $stockCount->hasVariances())
            <a href="{{ route('management.stock-counts.approve-variances', $stockCount->id) }}"
               class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-sm transition">
                <i class="fas fa-check-circle mr-1"></i> Approve Variances
            </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Unit</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">System Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Physical (Gross)</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Empty Weight</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Net Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Variance</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Reason Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Reason Notes</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($stockCount->items as $item)
                    @php
                        $netQty = $item->net_quantity;
                        $variance = $item->variance;
                        $varianceClass = $variance < 0 ? 'text-red-600' : ($variance > 0 ? 'text-green-600' : 'text-gray-500');
                        $hasVariance = $variance != 0;
                        $isApproved = $item->isApproved();
                        $emptyWeight = $item->inventoryItem->empty_bottle_weight ?? 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->inventoryItem->base_unit ?? 'units' }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->system_quantity, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            {{ number_format($item->physical_quantity, 2) }}
                            @if($item->physical_quantity_is_gross)
                            <span class="text-xs text-gray-400 block">(gross weight)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($emptyWeight > 0)
                                <span class="text-orange-600">{{ number_format($emptyWeight, 3) }} kg</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-semibold">{{ number_format($netQty, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold {{ $varianceClass }}">
                                {{ $variance >= 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($item->reason_code)
                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                    {{ $item->reason_code }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-500">{{ $item->reason_notes ?: '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($hasVariance)
                                @if($isApproved)
                                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                        <i class="fas fa-check mr-1 text-xs"></i> Approved
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                        <i class="fas fa-clock mr-1 text-xs"></i> Pending
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">
                                    <i class="fas fa-check mr-1 text-xs"></i> OK
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    @if($stockCount->status === 'draft')
    <div class="flex justify-end gap-3">
        <form action="{{ route('management.stock-counts.cancel', $stockCount->id) }}" method="POST" onsubmit="return confirm('Cancel this stock count?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                <i class="fas fa-times mr-1"></i> Cancel Count
            </button>
        </form>
        <form action="{{ route('management.stock-counts.submit', $stockCount->id) }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                <i class="fas fa-paper-plane mr-1"></i> Submit for Review
            </button>
        </form>
    </div>
    @endif

    @if($stockCount->status === 'in_progress')
    <div class="flex justify-end gap-3">
        <form action="{{ route('management.stock-counts.cancel', $stockCount->id) }}" method="POST" onsubmit="return confirm('Cancel this stock count?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                <i class="fas fa-times mr-1"></i> Cancel Count
            </button>
        </form>
        @if(!$stockCount->hasUnapprovedVariances())
        <form action="{{ route('management.stock-counts.complete', $stockCount->id) }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                <i class="fas fa-check-circle mr-1"></i> Complete Count
            </button>
        </form>
        @endif
    </div>
    @endif

</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.45)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Edit Physical Quantities</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('management.stock-counts.update-items', $stockCount->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Item</th>
                            <th class="px-3 py-2 text-center">System Qty</th>
                            <th class="px-3 py-2 text-center">Count Method</th>
                            <th class="px-3 py-2 text-center">Physical Qty/Weight</th>
                            <th class="px-3 py-2 text-center">Empty Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockCount->items as $item)
                        @php
                            $emptyWeight = $item->inventoryItem->empty_bottle_weight ?? 0;
                        @endphp
                        <tr class="border-b">
                            <td class="px-3 py-2">
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                <p class="font-medium">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-3 py-2 text-center">{{ number_format($item->system_quantity, 2) }}</td>
                            <td class="px-3 py-2 text-center">
                                <select name="items[{{ $loop->index }}][is_gross_weight]" class="count-method-{{ $loop->index }} rounded-lg border-gray-300 text-sm" data-empty="{{ $emptyWeight }}">
                                    <option value="0" {{ !$item->physical_quantity_is_gross ? 'selected' : '' }}>Net Quantity</option>
                                    <option value="1" {{ $item->physical_quantity_is_gross ? 'selected' : '' }}>Gross Weight</option>
                                </select>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <input type="number" name="items[{{ $loop->index }}][physical_quantity]" step="any"
                                       class="physical-qty-{{ $loop->index }} w-28 px-2 py-1 border border-gray-300 rounded-lg text-center"
                                       value="{{ $item->physical_quantity }}">
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($emptyWeight > 0)
                                    <span class="text-orange-600">{{ number_format($emptyWeight, 3) }} kg</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('editModal')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endsection
