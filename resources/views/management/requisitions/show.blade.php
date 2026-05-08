@extends('layouts.management')
@section('title', 'Requisition Details')
@section('page-title', 'Requisition Details')

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('management.requisitions.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="p-6">
        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'fulfilled' => 'bg-blue-100 text-blue-800',
                    'cancelled' => 'bg-gray-100 text-gray-800',
                ];
                $statusText = [
                    'pending' => 'Pending GM Approval',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'fulfilled' => 'Fulfilled',
                    'cancelled' => 'Cancelled',
                ];
            @endphp
            <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$requisition->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusText[$requisition->status] ?? ucfirst($requisition->status) }}
            </span>
        </div>

        {{-- Rejection Reason (only shown when rejected) --}}
        @if($requisition->status == 'rejected')
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $requisition->rejection_reason ?? 'No reason provided' }}</p>
                    @if($requisition->approvedBy)
                        <p class="text-xs text-red-600 mt-2">Rejected by: {{ $requisition->approvedBy->first_name }} {{ $requisition->approvedBy->last_name }} on {{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '' }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- GM Notes (if any) --}}
        @if($requisition->gm_notes)
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-yellow-800">GM Notes</h4>
                    <p class="text-sm text-yellow-700 mt-1">{{ $requisition->gm_notes }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Requisition Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Requisition Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Requisition No:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Store:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->store ? $requisition->store->name : '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Date Needed:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->date_needed ? $requisition->date_needed->format('F d, Y') : 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Requested By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->requestedBy ? $requisition->requestedBy->first_name . ' ' . $requisition->requestedBy->last_name : '—' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Response Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Responded By:</span>
                        <span class="text-sm text-gray-800">
                            @if($requisition->status == 'approved')
                                {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : 'Not yet responded' }}
                            @elseif($requisition->status == 'rejected')
                                {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : 'Not yet responded' }}
                            @else
                                Not yet responded
                            @endif
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Responded At:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Original Notes --}}
        @if($requisition->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Original Notes (from Store)</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $requisition->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table with Category --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">Requested Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Requested Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Approved Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $totalRequested = 0;
                            $totalApproved = 0;
                        @endphp
                        @foreach($requisition->items as $item)
                        @php
                            $totalRequested += $item->quantity_requested;
                            $totalApproved += $item->quantity_approved;
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br>
                                    <span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->category_name ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->metrics ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right font-semibold">
                                {{ number_format($item->quantity_requested, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                @if($requisition->status == 'approved')
                                    <span class="font-semibold text-green-600">{{ number_format($item->quantity_approved, 2) }}</span>
                                    @if($item->quantity_approved < $item->quantity_requested)
                                        <br>
                                        <span class="text-xs text-orange-500">(Partial)</span>
                                    @endif
                                @else
                                    {{ number_format($item->quantity_approved, 2) }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td class="px-4 py-3 text-sm font-bold text-gray-700" colspan="3">TOTALS</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ number_format($totalRequested, 2) }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-right">
                                @if($requisition->status == 'approved')
                                    <span class="text-green-600">{{ number_format($totalApproved, 2) }}</span>
                                    <br>
                                    <span class="text-xs {{ $totalApproved == $totalRequested ? 'text-green-500' : 'text-orange-500' }}">
                                        ({{ $totalApproved == $totalRequested ? 'Fully Approved' : number_format(($totalApproved / $totalRequested) * 100, 1) . '% Approved' }})
                                    </span>
                                @else
                                    {{ number_format($totalApproved, 2) }}
                                @endif
                            </td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-sm text-blue-600">Total Items</p>
                <p class="text-2xl font-bold text-blue-800">{{ $requisition->items->count() }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                <p class="text-sm text-yellow-600">Total Requested Quantity</p>
                <p class="text-2xl font-bold text-yellow-800">{{ number_format($totalRequested, 2) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600">Total Approved Quantity</p>
                <p class="text-2xl font-bold text-green-800">{{ number_format($totalApproved, 2) }}</p>
            </div>
        </div>

        {{-- Action Buttons (only for pending requisitions) --}}
        @if($requisition->status == 'pending')
        <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-4">
            <button type="button" onclick="openRejectModal()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Reject Requisition
            </button>
            <a href="{{ route('management.requisitions.approve-form', $requisition->id) }}"
               class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Approve Requisition
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Rejection Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 rounded-t-xl">
            <h3 class="text-lg font-semibold text-white">Reject Requisition</h3>
        </div>
        <form action="{{ route('management.requisitions.reject', $requisition->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-semibold mb-2 text-gray-700">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="4" class="form-textarea w-full border-gray-300 rounded-lg"
                          placeholder="Please provide a reason for rejecting this requisition..." required></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection
