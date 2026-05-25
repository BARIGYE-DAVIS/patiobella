@extends('layouts.management')

@section('title', 'Approve Stock Count')
@section('page-title', 'Approve Stock Count')

@push('styles')
<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-review { background-color: #fef3c7; color: #d97706; }
    .variance-positive { color: #059669; font-weight: 600; }
    .variance-negative { color: #dc2626; font-weight: 600; }
    .variance-zero { color: #6b7280; }
    .approved-badge {
        background-color: #d1fae5;
        color: #065f46;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .pending-badge {
        background-color: #fed7aa;
        color: #9a3412;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $stockCount->count_number }}</h2>
                    <span class="status-badge status-review">
                        <i class="fas fa-clock mr-1"></i> Ready for Approval
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Count Date: {{ \Carbon\Carbon::parse($stockCount->count_date)->format('F d, Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('management.stock-counts.show', $stockCount->id) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('management.stock-counts.approve-count-submit', $stockCount->id) }}" id="approveForm">
            @csrf

            {{-- Header --}}
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Final Approval
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Review the stock count summary and approve to finalize
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Basic Info --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Location</label>
                            <p class="text-gray-800 font-medium">
                                {{ $type === 'store' ? 'Main Store' : ($stockCount->location->name ?? 'Department') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Counted By</label>
                            <p class="text-gray-800 font-medium">
                                {{ $stockCount->creator->first_name ?? 'Unknown' }} {{ $stockCount->creator->last_name ?? '' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Count Date</label>
                            <p class="text-gray-800 font-medium">
                                {{ \Carbon\Carbon::parse($stockCount->count_date)->format('F d, Y') }}
                            </p>
                        </div>
                        @if($stockCount->notes)
                        <div class="md:col-span-3">
                            <label class="block text-xs text-gray-500 uppercase mb-1">Notes</label>
                            <p class="text-gray-600 text-sm bg-white p-2 rounded border border-gray-200">
                                {{ $stockCount->notes }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-xl p-4 text-center border border-blue-100">
                        <p class="text-xs text-blue-600 uppercase mb-1">Total Items</p>
                        <p class="text-2xl font-bold text-blue-800">{{ $stockCount->items->count() }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-4 text-center border border-orange-100">
                        <p class="text-xs text-orange-600 uppercase mb-1">Items with Variance</p>
                        <p class="text-2xl font-bold text-orange-800">
                            {{ $stockCount->items->filter(fn($i) => $i->variance != 0)->count() }}
                        </p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 text-center border border-green-100">
                        <p class="text-xs text-green-600 uppercase mb-1">Total System Qty</p>
                        <p class="text-2xl font-bold text-green-800">{{ number_format($stockCount->getTotalSystemQuantityAttribute(), 2) }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4 text-center border border-purple-100">
                        <p class="text-xs text-purple-600 uppercase mb-1">Total Net Qty</p>
                        <p class="text-2xl font-bold text-purple-800">{{ number_format($stockCount->getTotalNetQuantityAttribute(), 2) }}</p>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="border-t border-gray-200 pt-2">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">
                        <i class="fas fa-list mr-2 text-green-500"></i> Counted Items Summary
                    </h3>

                    <div class="rounded-lg border border-gray-200 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Item</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Expected</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Physical (Gross)</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Empty Wt (kg)</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Net Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Variance</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Reason</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($stockCount->items as $item)
                                @php
                                    $emptyWeight = $item->inventoryItem->empty_bottle_weight ?? 0;
                                    $netQty = $item->net_quantity;
                                    $variance = $item->variance;
                                    $varianceClass = $variance < 0 ? 'variance-negative' : ($variance > 0 ? 'variance-positive' : 'variance-zero');
                                    $isApproved = $item->isApproved();
                                    $hasVariance = $variance != 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ number_format($item->system_quantity, 2) }}
                                        <span class="text-xs text-gray-400 ml-1">{{ $item->inventoryItem->base_unit ?? 'units' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ number_format($item->physical_quantity, 2) }}
                                        @if($item->physical_quantity_is_gross)
                                        <span class="text-xs text-gray-400 block">(gross)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-orange-600">{{ number_format($emptyWeight, 3) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold">
                                        {{ number_format($netQty, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="{{ $varianceClass }}">
                                            {{ $variance >= 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($hasVariance)
                                            <p class="text-sm font-medium text-gray-700">{{ $item->reason_code ?? '—' }}</p>
                                            <p class="text-xs text-gray-400">{{ $item->reason_notes ?? '' }}</p>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($isApproved)
                                            <span class="approved-badge">
                                                <i class="fas fa-check-circle mr-1"></i> Approved
                                            </span>
                                        @elseif(!$hasVariance)
                                            <span class="approved-badge">
                                                <i class="fas fa-check-circle mr-1"></i> No Variance
                                            </span>
                                        @else
                                            <span class="pending-badge">
                                                <i class="fas fa-clock mr-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Variance Summary --}}
                @php
                    $approvedCount = $stockCount->items->filter(fn($i) => $i->isApproved())->count();
                    $pendingCount = $stockCount->items->filter(fn($i) => $i->variance != 0 && !$i->isApproved())->count();
                    $totalVariance = $stockCount->getTotalVarianceAttribute();
                @endphp

                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">
                                <i class="fas fa-chart-line mr-1"></i> Variance Summary
                            </p>
                            <p class="text-xs text-yellow-600 mt-1">
                                Total variance value:
                                <span class="font-bold {{ $totalVariance < 0 ? 'text-red-600' : ($totalVariance > 0 ? 'text-green-600' : 'text-gray-600') }}">
                                    {{ $totalVariance >= 0 ? '+' : '' }}{{ number_format($totalVariance, 2) }} units
                                </span>
                            </p>
                        </div>
                        <div class="flex gap-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Approved</p>
                                <p class="text-xl font-bold text-green-600">{{ $approvedCount }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Pending</p>
                                <p class="text-xl font-bold text-orange-600">{{ $pendingCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Approval Notes --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-pen mr-1 text-gray-400"></i> Approval Notes (Optional)
                    </label>
                    <textarea name="approval_notes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 transition-all duration-200"
                              placeholder="Add any approval notes or comments..."></textarea>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('management.stock-counts.show', $stockCount->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-times text-gray-400"></i> Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg text-sm font-medium transition shadow-sm hover:shadow-md"
                        onclick="return confirmApprove()">
                    <i class="fas fa-check-circle"></i> Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmApprove() {
        const pendingCount = {{ $pendingCount }};
        if (pendingCount > 0) {
            return confirm(`There ${pendingCount === 1 ? 'is' : 'are'} ${pendingCount} item(s) with pending approval.\n\nAre you sure you want to approve this stock count?`);
        }
        return confirm('Are you sure you want to approve and complete this stock count?\n\nThis action cannot be undone.');
    }

    document.getElementById('approveForm').addEventListener('submit', function(e) {
        const pendingCount = {{ $pendingCount }};
        if (pendingCount > 0) {
            if (!confirm(`WARNING: ${pendingCount} item(s) still have pending approval.\n\nApprove anyway?`)) {
                e.preventDefault();
            }
        }
    });
</script>
@endpush
