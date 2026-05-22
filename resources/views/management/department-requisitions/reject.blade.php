{{-- resources/views/management/department-requisitions/reject.blade.php --}}

@extends('layouts.management')

@section('title', 'Reject Requisition')
@section('page-title', 'Reject Requisition')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $requisition->requisition_number }}</h2>
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                        Pending Rejection
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Created: {{ $requisition->created_at->format('F d, Y h:i A') }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-building mr-1"></i> {{ $requisition->department->name ?? 'N/A' }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('management.department-requisitions.show', $requisition->id) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="bg-red-50 border-l-4 border-red-400 rounded-lg px-5 py-4">
        <p class="text-sm text-red-800">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Rejection Instructions:</strong>
            Please provide a clear reason for rejecting this requisition. The department will be notified of your decision.
        </p>
    </div>

    {{-- Requisition Summary --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-700">Requisition Summary</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-500">Department</p>
                    <p class="text-sm font-medium text-gray-800">{{ $requisition->department->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Requested By</p>
                    <p class="text-sm font-medium text-gray-800">{{ $requisition->requestedBy->first_name ?? 'N/A' }} {{ $requisition->requestedBy->last_name ?? '' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Items Requested</p>
                    <p class="text-sm font-medium text-gray-800">{{ $requisition->items->count() }} items</p>
                </div>
            </div>

            {{-- Items List --}}
            <div class="border-t border-gray-200 pt-4">
                <p class="text-xs text-gray-500 mb-2">Requested Items:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($requisition->items as $item)
                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                        {{ $item->inventoryItem->name ?? 'N/A' }} ({{ number_format($item->quantity_requested, 2) }})
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Rejection Form --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('management.department-requisitions.reject', $requisition->id) }}" id="rejectForm">
            @csrf

            <div class="p-6 space-y-6">
                {{-- Rejection Reason --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="5"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500"
                              placeholder="Please provide a detailed reason for rejecting this requisition..."></textarea>
                    <p class="text-xs text-gray-500 mt-2">This reason will be visible to the department that submitted the requisition.</p>
                </div>

                {{-- Additional Notes (Optional) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes (Optional)
                    </label>
                    <textarea name="store_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500"
                              placeholder="Any internal notes for management reference..."></textarea>
                    <p class="text-xs text-gray-500 mt-2">These notes will only be visible to management and store staff.</p>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('management.department-requisitions.show', $requisition->id) }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-times mr-1"></i> Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Form validation
    document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
        const reason = document.querySelector('textarea[name="rejection_reason"]').value.trim();

        if (!reason) {
            e.preventDefault();
            alert('Please provide a reason for rejecting this requisition.');
            document.querySelector('textarea[name="rejection_reason"]').focus();
            return false;
        }

        if (reason.length < 10) {
            e.preventDefault();
            alert('Please provide a detailed reason (at least 10 characters).');
            document.querySelector('textarea[name="rejection_reason"]').focus();
            return false;
        }
    });
</script>
@endsection
