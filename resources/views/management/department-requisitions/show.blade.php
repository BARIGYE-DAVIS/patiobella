{{-- resources/views/management/department-requisitions/show.blade.php --}}

@extends('layouts.management')

@section('title', 'Requisition Details')
@section('page-title', 'Requisition Details')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $requisition->requisition_number }}</h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'approved' => 'bg-blue-100 text-blue-700',
                            'partially_issued' => 'bg-orange-100 text-orange-700',
                            'issued' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'cancelled' => 'bg-gray-100 text-gray-500',
                            'completed' => 'bg-teal-100 text-teal-700'
                        ];
                        $statusLabels = [
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'partially_issued' => 'Partially Issued',
                            'issued' => 'Issued',
                            'rejected' => 'Rejected',
                            'cancelled' => 'Cancelled',
                            'completed' => 'Completed'
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$requisition->status] }}">
                        {{ $statusLabels[$requisition->status] }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Created: {{ $requisition->created_at->format('F d, Y h:i A') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('management.department-requisitions.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                @if($requisition->status === 'pending')
                <a href="{{ route('management.department-requisitions.approve-form', $requisition->id) }}"
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-check mr-1"></i> Approve
                </a>
                <button type="button" onclick="openRejectModal()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-times mr-1"></i> Reject
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Left Column - Requisition Info --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Requisition Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                    <i class="fas fa-info-circle mr-2"></i> Requisition Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Requisition Number:</span>
                        <span class="text-sm font-mono font-medium text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Department:</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">
                            <i class="fas fa-building text-xs"></i> {{ $requisition->department->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Requisition Type:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->requisition_type_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Date Needed:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->date_needed ? date('F d, Y', strtotime($requisition->date_needed)) : 'Not specified' }}</span>
                    </div>
                </div>
            </div>

            {{-- Requestor Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                    <i class="fas fa-user mr-2"></i> Requested By
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Name:</span>
                        <span class="text-sm text-gray-800 font-medium">{{ $requisition->requestedBy->first_name ?? 'N/A' }} {{ $requisition->requestedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Email:</span>
                        <span class="text-sm text-gray-600">{{ $requisition->requestedBy->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Requested On:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Approval Information --}}
            @if($requisition->approved_at)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                    <i class="fas fa-check-circle mr-2 text-green-500"></i> Approval Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Approved By:</span>
                        <span class="text-sm text-gray-800 font-medium">{{ $requisition->approvedBy->first_name ?? 'Management' }} {{ $requisition->approvedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Approved On:</span>
                        <span class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($requisition->approved_at)->format('F d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Rejection Information --}}
            @if($requisition->status === 'rejected' && $requisition->rejection_reason)
            <div class="bg-red-50 border border-red-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-red-700 uppercase tracking-wider mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Rejection Reason
                </h3>
                <p class="text-sm text-red-600">{{ $requisition->rejection_reason }}</p>
            </div>
            @endif

            {{-- Department Notes --}}
            @if($requisition->department_notes)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-blue-700 uppercase tracking-wider mb-3">
                    <i class="fas fa-sticky-note mr-2"></i> Department Notes
                </h3>
                <p class="text-sm text-blue-600">{{ $requisition->department_notes }}</p>
            </div>
            @endif

            {{-- Store Notes --}}
            @if($requisition->store_notes)
            <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wider mb-3">
                    <i class="fas fa-store mr-2"></i> Store Notes
                </h3>
                <p class="text-sm text-green-600">{{ $requisition->store_notes }}</p>
            </div>
            @endif
        </div>

        {{-- Right Column - Items Table --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
                    <p class="text-xs text-gray-500">Total Items</p>
                    <p class="text-xl font-bold text-gray-800">{{ $requisition->total_items }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
                    <p class="text-xs text-gray-500">Requested Qty</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($requisition->total_quantity_requested, 2) }}</p>
                </div>
                @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'completed', 'partially_returned']))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
                    <p class="text-xs text-gray-500">Issued Qty</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($requisition->total_quantity_issued, 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
                    <p class="text-xs text-gray-500">Completion</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $requisition->issued_percentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $requisition->issued_percentage }}%</p>
                </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">
                        <i class="fas fa-boxes mr-2"></i> Requested Items
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Metrics</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Requested</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Approved Qty</th>

                                @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'completed', 'partially_returned']))
                                <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 border-l border-gray-200 bg-green-50">Issued</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-orange-600 bg-orange-50">Used</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600 bg-purple-50">Returned</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 bg-gray-50">In Dept</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($requisition->items as $item)
                            @php
                                $used = ($item->quantity_consumed ?? 0) + ($item->quantity_sold ?? 0);
                                $approvedQty = $item->quantity_approved ?? $item->quantity_requested;
                                $remainingInDept = max(0, ($item->issued_total_pieces ?? 0) - $used - ($item->quantity_returned ?? 0));
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs text-gray-500">{{ $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units') }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold">{{ number_format($item->quantity_requested, 2) }}</span>
                                    @if($item->requested_pack_type)
                                    <div class="text-xs text-gray-400">{{ ucfirst($item->requested_pack_type) }} × {{ $item->requested_pack_size }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold text-blue-600">{{ number_format($approvedQty, 2) }}</span>
                                    @if($item->approved_pack_type)
                                    <div class="text-xs text-gray-400">{{ ucfirst($item->approved_pack_type) }} × {{ $item->approved_pack_size }}</div>
                                    @endif
                                    @if($item->approval_notes)
                                    <div class="text-xs text-gray-400 italic">{{ $item->approval_notes }}</div>
                                    @endif
                                </td>

                                @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'completed', 'partially_returned']))
                                <td class="px-4 py-3 text-center border-l border-gray-100">
                                    <span class="text-green-600 font-semibold">{{ number_format($item->quantity_issued ?? 0, 2) }}</span>
                                    @if($item->issued_pack_type)
                                    <div class="text-xs text-gray-400">{{ ucfirst($item->issued_pack_type) }} × {{ $item->issued_pack_size }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-orange-600">{{ number_format($used, 2) }}</span>
                                    <div class="text-xs text-gray-400">(consumed + sold)</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-purple-600">{{ number_format($item->quantity_returned ?? 0, 2) }}</span>
                                    @if($item->returned_pack_type)
                                    <div class="text-xs text-gray-400">{{ ucfirst($item->returned_pack_type) }} × {{ $item->returned_pack_size }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold {{ $remainingInDept > 0 ? 'text-gray-800' : 'text-gray-400' }}">
                                        {{ number_format($remainingInDept, 2) }}
                                    </span>
                                </td>
                                @endif
                            </td>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-xs font-semibold text-gray-500">Totals</td>
                                <td class="px-4 py-3 text-center font-semibold">{{ number_format($requisition->total_quantity_requested, 2) }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-blue-600">
                                    @php
                                        $totalApproved = $requisition->items->sum(function($item) {
                                            return $item->quantity_approved ?? $item->quantity_requested;
                                        });
                                    @endphp
                                    {{ number_format($totalApproved, 2) }}
                                </td>
                                @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'completed', 'partially_returned']))
                                <td class="px-4 py-3 text-center font-semibold text-green-600 border-l border-gray-200">{{ number_format($requisition->total_quantity_issued, 2) }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-orange-600">
                                    {{ number_format($requisition->total_quantity_consumed + $requisition->total_quantity_sold, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-purple-600">{{ number_format($requisition->total_quantity_returned, 2) }}</td>
                                <td class="px-4 py-3 text-center font-semibold">
                                    {{ number_format($requisition->total_quantity_issued - ($requisition->total_quantity_consumed + $requisition->total_quantity_sold + $requisition->total_quantity_returned), 2) }}
                                </td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.45)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Reject Requisition</h3>
            </div>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="rejectForm" method="POST" action="{{ route('management.department-requisitions.reject', $requisition->id) }}">
            @csrf

            <div class="p-6 space-y-5">
                {{-- Requisition Info Summary --}}
                <div class="bg-gray-50 rounded-lg p-3 text-sm">
                    <p class="font-medium text-gray-700">Requisition: {{ $requisition->requisition_number }}</p>
                    <p class="text-gray-500 text-xs mt-1">Department: {{ $requisition->department->name ?? 'N/A' }}</p>
                </div>

                {{-- Rejection Reason --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500"
                              placeholder="Please provide a detailed reason for rejecting this requisition..." required></textarea>
                    <p class="text-xs text-gray-500 mt-1">This reason will be visible to the department.</p>
                </div>

                {{-- Additional Internal Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Internal Notes (Optional)
                    </label>
                    <textarea name="store_notes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500"
                              placeholder="Any internal notes for management/staff..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Only visible to management and store staff.</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-check mr-1"></i> Confirm Rejection
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

    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });

    // Form validation
    document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
        const reason = document.querySelector('#rejectForm textarea[name="rejection_reason"]').value.trim();

        if (!reason) {
            e.preventDefault();
            alert('Please provide a reason for rejecting this requisition.');
            return false;
        }

        if (reason.length < 10) {
            e.preventDefault();
            alert('Please provide a detailed reason (at least 10 characters).');
            return false;
        }
    });
</script>
@endsection
