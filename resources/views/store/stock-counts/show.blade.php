@extends('layouts.store')

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
                <a href="{{ route('store.stock-counts.index', ['type' => $type]) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                @if($stockCount->status === 'draft')
                <a href="{{ route('store.stock-counts.edit-count', $stockCount->id) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-edit mr-1"></i> Edit Count
                </a>
                @endif
                @if($stockCount->status === 'in_progress')
                <a href="{{ route('store.stock-counts.review', $stockCount->id) }}"
                   class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-clipboard-list mr-1"></i> Review Stock Count
                </a>
                <a href="{{ route('store.stock-counts.approve-count', $stockCount->id) }}"
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-check-circle mr-1"></i> Approve Stock Count
                </a>
                @endif
                @if($stockCount->status === 'completed')
                <a href="{{ route('store.stock-counts.download-pdf', $stockCount->id) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-download mr-1"></i> Download PDF
                </a>
                @endif
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

    {{-- Count Information Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Count Conducted By</p>
                <p class="text-sm font-semibold text-gray-800">
                    <i class="fas fa-user mr-1 text-gray-400"></i>
                    {{ $stockCount->creator->first_name ?? 'Unknown' }} {{ $stockCount->creator->last_name ?? '' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $stockCount->created_at->format('F d, Y h:i A') }}</p>
            </div>
            @if($stockCount->completed_by)
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Approved By</p>
                <p class="text-sm font-semibold text-gray-800">
                    <i class="fas fa-check-circle mr-1 text-green-500"></i>
                    {{ $stockCount->completer->first_name ?? 'Manager' }} {{ $stockCount->completer->last_name ?? '' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $stockCount->completed_at ? \Carbon\Carbon::parse($stockCount->completed_at)->format('F d, Y h:i A') : '' }}</p>
            </div>
            @endif
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Notes</p>
                <p class="text-sm text-gray-600">{{ $stockCount->notes ?: 'No notes provided' }}</p>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-800">Counted Items</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Unit</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Expected Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Physical Count</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Empty Weight (kg)</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Net Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Variance</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Reason</th>
                        @if(in_array($stockCount->status, ['in_progress', 'completed']))
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Status</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($stockCount->items as $item)
                    @php
                        $netQty = $item->net_quantity;
                        $variance = $item->variance;
                        $varianceClass = $variance < 0 ? 'text-red-600' : ($variance > 0 ? 'text-green-600' : 'text-gray-500');
                        $emptyWeight = $item->inventoryItem->empty_bottle_weight ?? 0;
                        $isApproved = $item->isApproved();
                        $hasVariance = $variance != 0;
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
                                <span class="text-orange-600">{{ number_format($emptyWeight, 3) }}</span>
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
                        <td class="px-4 py-3 text-left">
                            @if($item->reason_notes)
                                <p class="text-sm font-medium text-gray-700">{{ $item->reason_notes }}</p>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        @if(in_array($stockCount->status, ['in_progress', 'completed']))
                        <td class="px-4 py-3 text-center">
                            @if($isApproved)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i> Approved
                                </span>
                            @elseif(!$hasVariance)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i> No Variance
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Action Buttons for Draft --}}
    @if($stockCount->status === 'draft')
    <div class="flex justify-end gap-3">
        <form action="{{ route('store.stock-counts.cancel', $stockCount->id) }}" method="POST" onsubmit="return confirm('Cancel this stock count?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                <i class="fas fa-times mr-1"></i> Cancel Count
            </button>
        </form>
        <form action="{{ route('store.stock-counts.submit', $stockCount->id) }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                <i class="fas fa-paper-plane mr-1"></i> Submit for Review
            </button>
        </form>
    </div>
    @endif

    {{-- Action Buttons for In Progress --}}
    @if($stockCount->status === 'in_progress')
    <div class="flex justify-end gap-3">
        <form action="{{ route('store.stock-counts.cancel', $stockCount->id) }}" method="POST" onsubmit="return confirm('Cancel this stock count? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                <i class="fas fa-times mr-1"></i> Cancel Count
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
