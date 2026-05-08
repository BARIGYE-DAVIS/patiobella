@extends('layouts.director')
@section('title', 'LPO Details')
@section('page-title', 'Local Purchase Order Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">LPO #{{ $lpo->lpo_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $lpo->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('director.lpos.index') }}" class="text-gray-600 hover:text-gray-800">
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
                    'pending_director' => 'bg-yellow-100 text-yellow-800',
                    'director_approved' => 'bg-green-100 text-green-800',
                    'director_rejected' => 'bg-red-100 text-red-800',
                ];
                $statusText = [
                    'pending_director' => 'Pending Your Approval',
                    'director_approved' => 'Approved - Ready for External PO',
                    'director_rejected' => 'Rejected',
                ];
            @endphp
            <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$lpo->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusText[$lpo->status] ?? ucfirst($lpo->status) }}
            </span>
        </div>

        {{-- Director Notes (if any) --}}
        @if($lpo->director_notes)
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-yellow-800">Director Notes (for Procurement)</h4>
                    <p class="text-sm text-yellow-700 mt-1">{{ $lpo->director_notes }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Rejection Reason --}}
        @if($lpo->status == 'director_rejected' && $lpo->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $lpo->rejection_reason }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- LPO Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">LPO Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">LPO Number:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $lpo->lpo_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Requisition #:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->requisition->requisition_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">LPO Date:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->lpo_date->format('F d, Y') }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Expected Delivery:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->expected_delivery_date ? date('F d, Y', strtotime($lpo->expected_delivery_date)) : 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Delivery Address:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->delivery_address ?: 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Delivery Instructions:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->delivery_instructions ?: 'Not specified' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Vendor Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Vendor Name:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Contact Person:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->contact_person ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Phone:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-sm text-gray-500">Email:</span>
                        <span class="text-sm text-gray-800">{{ $lpo->vendor->email ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($lpo->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Procurement Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $lpo->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table with Category --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">LPO Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-5">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Unit Cost (UGX)</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Total (UGX)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php $totalQuantity = 0; @endphp
                        @foreach($lpo->items as $index => $item)
                        @php $totalQuantity += $item->quantity_approved; @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br><span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
              <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
            {{ $item->inventoryItem?->category?->name ?: '—' }}
         </span>
         </td>
                            <td class="px-4 py-3 text-sm text-gray-500 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->metrics ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold">{{ number_format($item->quantity_approved, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">{{ number_format($item->total_cost, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-sm font-bold text-gray-700">TOTALS</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ number_format($totalQuantity, 2) }}</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-sm font-bold text-green-700 text-right">{{ number_format($lpo->total_amount, 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-sm text-blue-600">Total Items</p>
                <p class="text-2xl font-bold text-blue-800">{{ $lpo->items->count() }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600">Total LPO Amount</p>
                <p class="text-2xl font-bold text-green-800">UGX {{ number_format($lpo->total_amount, 2) }}</p>
            </div>
        </div>

        {{-- Action Buttons (only for pending LPOs) --}}
        @if($lpo->status == 'pending_director')
        <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-4">
            <button type="button" onclick="openRejectModal()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Reject LPO
            </button>
            <button type="button" onclick="openApproveModal()"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Approve LPO & Send to Procurement
            </button>
        </div>
        @endif

        {{-- Show approval info if approved --}}
        @if($lpo->status == 'director_approved')
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">LPO Approved by Director</p>
                    <p class="text-xs text-green-600">Procurement can now create External Purchase Order and send to vendor.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Approve Modal with Notes --}}
<div id="approveModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
        <div class="bg-green-600 px-6 py-4 rounded-t-lg">
            <h3 class="text-lg font-semibold text-white">Approve LPO</h3>
        </div>
        <form action="{{ route('director.lpos.approve', $lpo->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-semibold mb-2 text-gray-700">Director Notes (Optional)</label>
                <textarea name="director_notes" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Add any notes or instructions for Procurement department..."></textarea>
                <p class="text-xs text-gray-500 mt-2">These notes will be visible to Procurement when they convert this LPO to an External Purchase Order.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Rejection Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="bg-red-600 px-6 py-4 rounded-t-lg">
            <h3 class="text-lg font-semibold text-white">Reject LPO</h3>
        </div>
        <form action="{{ route('director.lpos.reject', $lpo->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-semibold mb-2 text-gray-700">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Please provide a reason for rejecting this LPO..." required></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
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
    function openApproveModal() {
        document.getElementById('approveModal').classList.remove('hidden');
    }
    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection
