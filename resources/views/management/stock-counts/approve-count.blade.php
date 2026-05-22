{{-- resources/views/management/stock-counts/approve-count.blade.php --}}

@extends('layouts.management')

@section('title', 'Approve Stock Count')
@section('page-title', 'Approve Stock Count')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $stockCount->count_number }}</h2>
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                        Pending Approval
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Count Date: {{ \Carbon\Carbon::parse($stockCount->count_date)->format('F d, Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('management.stock-counts.show', $stockCount->id) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Details
                </a>
            </div>
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
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Count Date</p>
                <p class="text-sm font-semibold text-gray-800">
                    <i class="fas fa-calendar-check mr-1 text-gray-400"></i>
                    {{ \Carbon\Carbon::parse($stockCount->count_date)->format('F d, Y') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Location</p>
                <p class="text-sm font-semibold text-gray-800">
                    <i class="fas fa-location-dot mr-1 text-gray-400"></i>
                    {{ $stockCount->location_type === 'store' ? 'Main Store' : ($stockCount->location->name ?? 'Department') }}
                </p>
            </div>
        </div>
        @if($stockCount->notes)
        <div class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-xs text-gray-500 uppercase mb-1">Reason for Variance</p>
            <p class="text-sm text-gray-600">{{ $stockCount->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1">Total Items Counted</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stockCount->items->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1">System Quantity</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stockCount->getTotalSystemQuantityAttribute(), 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1">Physical Counted</p>
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

    {{-- Items Table (Read Only) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Counted Items - Review</h3>
            <p class="text-xs text-gray-400 mt-1">Review all counted items before approving</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Unit</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">System Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Physical Counted</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Variance</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Reason for Variance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($stockCount->items as $item)
                    @php
                        $netQty = $item->net_quantity;
                        $variance = $item->variance;
                        $varianceClass = $variance < 0 ? 'text-red-600' : ($variance > 0 ? 'text-green-600' : 'text-gray-500');
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->inventoryItem->base_unit ?? 'units' }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->system_quantity, 2) }}</td>
                        <td class="px-4 py-3 text-center font-semibold">{{ number_format($netQty, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold {{ $varianceClass }}">
                                {{ $variance >= 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-left">
                            <span class="text-sm text-gray-500">{{ $item->reason_notes ?: '—' }}</span>
                        </td>
                    </td>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-xs font-semibold text-gray-500">Totals</td>
                        <td class="px-4 py-3 text-center font-semibold">{{ number_format($stockCount->getTotalSystemQuantityAttribute(), 2) }}</td>
                        <td class="px-4 py-3 text-center font-semibold">{{ number_format($stockCount->getTotalNetQuantityAttribute(), 2) }}</td>
                        <td class="px-4 py-3 text-center font-semibold {{ $totalVariance < 0 ? 'text-red-600' : ($totalVariance > 0 ? 'text-green-600' : 'text-gray-800') }}">
                            {{ number_format($totalVariance, 2) }}
                        </td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Approval Form --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('management.stock-counts.approve-count-submit', $stockCount->id) }}" id="approveForm">
            @csrf

            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Approval Notes (Optional)
                    </label>
                    <textarea name="approval_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500" placeholder="Add any approval notes..."></textarea>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800">Confirm Approval</p>
                            <p class="text-xs text-green-700 mt-1">
                                By approving this stock count, you confirm that the physical count has been verified and is accurate.
                                This will mark the stock count as completed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('management.stock-counts.show', $stockCount->id) }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-check-circle mr-1"></i> Approve Stock Count
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('approveForm')?.addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to approve this stock count? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
</script>
@endsection
