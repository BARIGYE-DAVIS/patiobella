@extends('layouts.management')

@section('title', 'Reservation Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-3 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            Reservation Details
                        </h1>
                        <p class="text-gray-600 mt-1">View and manage reservation information</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('management.reservations.index') }}"
                       class="group inline-flex items-center px-4 py-2 bg-white border-2 border-orange-200 rounded-lg text-orange-600 hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 shadow-sm hover:shadow">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back
                    </a>
                    <a href="{{ route('management.reservations.edit', $reservation->id) }}"
                       class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Reservation
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Customer Information Card --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-orange-100">
                    <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                        <h3 class="text-white font-semibold text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Customer Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Customer Name</label>
                                <p class="text-gray-800 font-medium">{{ $reservation->customer_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Phone Number</label>
                                <p class="text-gray-800">{{ $reservation->customer_phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Email Address</label>
                                <p class="text-gray-800">{{ $reservation->customer_email ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reservation Details Card --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-orange-100">
                    <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                        <h3 class="text-white font-semibold text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Reservation Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Table</label>
                                <p class="text-gray-800 font-medium">Table {{ $reservation->table->table_number ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $reservation->table->location ?? '' }} ({{ $reservation->table->capacity ?? 0 }} seats)</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Date</label>
                                <p class="text-gray-800">{{ $reservation->formatted_date }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Time</label>
                                <p class="text-gray-800">{{ $reservation->formatted_time }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Duration</label>
                                <p class="text-gray-800">{{ $reservation->duration_hours }} hour(s)</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Number of Guests</label>
                                <p class="text-gray-800">{{ $reservation->number_of_guests }} guests</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Status</label>
                                <div>{!! $reservation->status_badge !!}</div>
                            </div>
                        </div>
                        @if($reservation->notes)
                        <div class="mt-4">
                            <label class="text-xs text-gray-500 uppercase">Special Notes</label>
                            <p class="text-gray-700 mt-1 p-3 bg-gray-50 rounded-lg">{{ $reservation->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status Update Card --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border hidden border-orange-100">
                    <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                        <h3 class="text-white font-semibold text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Update Status
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('management.reservations.update-status', $reservation->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <select name="status" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                                    <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>✅ Confirmed</option>
                                    <option value="seated" {{ $reservation->status == 'seated' ? 'selected' : '' }}>🪑 Seated</option>
                                    <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>🏁 Completed</option>
                                    <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                    <option value="no_show" {{ $reservation->status == 'no_show' ? 'selected' : '' }}>🚫 No Show</option>
                                </select>
                                <div id="cancellation_reason_div" style="display: none;">
                                    <textarea name="cancellation_reason" rows="2" placeholder="Cancellation reason..." class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200"></textarea>
                                </div>
                                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-500 text-white py-2 rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Audit Information Card --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-orange-100">
                    <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                        <h3 class="text-white font-semibold text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Audit Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Created By</label>
                            <p class="text-gray-800">{{ $reservation->creator ? $reservation->creator->first_name . ' ' . $reservation->creator->last_name : 'System' }}</p>
                            <p class="text-xs text-gray-400">{{ $reservation->created_at ? $reservation->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Last Updated By</label>
                            <p class="text-gray-800">{{ $reservation->updater ? $reservation->updater->first_name . ' ' . $reservation->updater->last_name : 'Never updated' }}</p>
                            @if($reservation->updated_at)
                                <p class="text-xs text-gray-400">{{ $reservation->updated_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                        @if($reservation->cancelled_by)
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Cancelled By</label>
                            <p class="text-gray-800">{{ $reservation->cancelledBy ? $reservation->cancelledBy->first_name . ' ' . $reservation->cancelledBy->last_name : 'System' }}</p>
                            <p class="text-xs text-gray-400">{{ $reservation->cancelled_at ? $reservation->cancelled_at->format('M d, Y H:i') : 'N/A' }}</p>
                            @if($reservation->cancellation_reason)
                                <p class="text-sm text-gray-600 mt-2">Reason: {{ $reservation->cancellation_reason }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Danger Zone (Delete) --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-red-200">
                    <div class="bg-red-500 px-6 py-4">
                        <h3 class="text-white font-semibold text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Danger Zone
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-red-600 mb-4">Once deleted, this reservation cannot be recovered.</p>
                        <button type="button" onclick="deleteReservation()" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-all duration-200">
                            Delete Reservation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('management.reservations.destroy', $reservation->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    // Show cancellation reason field when status is cancelled
    const statusSelect = document.querySelector('select[name="status"]');
    const cancellationDiv = document.getElementById('cancellation_reason_div');

    if (statusSelect) {
        function toggleCancellationReason() {
            if (statusSelect.value === 'cancelled') {
                cancellationDiv.style.display = 'block';
            } else {
                cancellationDiv.style.display = 'none';
            }
        }

        statusSelect.addEventListener('change', toggleCancellationReason);
        toggleCancellationReason();
    }

    function deleteReservation() {
        if (confirm('Are you sure you want to delete this reservation? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection
