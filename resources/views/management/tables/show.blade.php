@extends('layouts.management')

@section('title', 'Table Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 py-8">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-3 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            Table Details
                        </h1>
                        <p class="text-gray-600 mt-1 hidden">View complete table information</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('management.tables.index') }}"
                       class="group inline-flex items-center px-4 py-2 bg-white border-2 border-orange-200 rounded-lg text-orange-600 hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 shadow-sm hover:shadow">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Tables
                    </a>
                    <a href="{{ route('management.tables.edit', $table->id) }}"
                       class="group inline-flex items-center px-5 py-2 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Table
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Table Number</p>
                        <p class="text-2xl font-bold text-gray-800">#{{ $table->table_number }}</p>
                    </div>
                    <div class="bg-orange-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Capacity</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $table->capacity }} seats</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Size</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $table->size_label }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Status</p>
                        <div class="mt-1">{!! $table->status_label !!}</div>
                    </div>
                    <div class="bg-green-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 {{ $table->is_active ? 'border-green-500' : 'border-red-500' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Active Status</p>
                        <p class="text-2xl font-bold {{ $table->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $table->is_active ? 'Active' : 'Inactive' }}</p>
                    </div>
                    <div class="{{ $table->is_active ? 'bg-green-100' : 'bg-red-100' }} rounded-lg p-2">
                        <svg class="w-6 h-6 {{ $table->is_active ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($table->is_active)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            @endif
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Table Information --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information Card --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-orange-100">
                    <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-white font-semibold text-lg">Table Information</h3>
                        </div>
                        <p class="text-orange-100 text-sm mt-1 ml-9">Detailed table specifications</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="border-b border-gray-100 pb-3">
                                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Table Number</label>
                                    <p class="text-lg font-bold text-gray-800 mt-1">Table {{ $table->table_number }}</p>
                                </div>
                                <div class="border-b border-gray-100 pb-3">
                                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Capacity</label>
                                    <p class="text-gray-800 mt-1 flex items-center">
                                        <span class="text-lg font-semibold">{{ $table->capacity }}</span>
                                        <span class="text-gray-500 ml-1">{{ $table->capacity == 1 ? 'seat' : 'seats' }}</span>
                                    </p>
                                </div>
                                <div class="border-b border-gray-100 pb-3">
                                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Size</label>
                                    <p class="text-gray-800 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $table->size_label }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="border-b border-gray-100 pb-3">
                                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Location</label>
                                    <p class="text-gray-800 mt-1 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $table->location ?? 'Not specified' }}
                                    </p>
                                </div>
                                <div class="border-b border-gray-100 pb-3">
                                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Sort Order</label>
                                    <p class="text-gray-800 mt-1">{{ $table->sort_order }}</p>
                                </div>
                                <div class="pb-3">
                                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Status</label>
                                    <div class="mt-2">{!! $table->status_label !!}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Description</label>
                            <p class="text-gray-700 mt-2 leading-relaxed">{{ $table->description ?? 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Upcoming Reservations Card --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-orange-100">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="text-white font-semibold text-lg">Upcoming Reservations</h3>
                        </div>
                        <p class="text-purple-100 text-sm mt-1 ml-9">Future reservations for this table</p>
                    </div>
                    <div class="p-6">
                        @if($upcomingReservations->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50 border-b-2 border-gray-200">
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Guests</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($upcomingReservations as $reservation)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-800">{{ $reservation->customer_name }}</div>
                                                    <div class="text-xs text-gray-500 mt-0.5">{{ $reservation->customer_phone }}</div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-sm font-medium text-gray-700">{{ $reservation->formatted_date }}</div>
                                                    <div class="text-xs text-gray-500 mt-0.5">{{ $reservation->formatted_time }} ({{ $reservation->duration_hours }} hrs)</div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        👥 {{ $reservation->number_of_guests }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">{!! $reservation->status_badge !!}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <a href="#" class="text-blue-600 hover:text-blue-800 transition-colors inline-flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-500">No upcoming reservations for this table.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                {{-- Quick Actions Card --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-orange-100">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <h3 class="text-white font-semibold text-lg">Quick Actions</h3>
                        </div>
                        <p class="text-blue-100 text-sm mt-1 ml-9">Manage table status quickly</p>
                    </div>
                    <div class="p-5 space-y-3">
                        <form action="{{ route('management.tables.toggle-reserved', $table->id) }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit"
                                    class="w-full {{ $table->is_reserved ? 'bg-green-600 hover:bg-green-700' : 'bg-orange-600 hover:bg-orange-700' }} text-white px-4 py-2.5 rounded-lg transition-all duration-200 font-medium flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($table->is_reserved)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endif
                                </svg>
                                {{ $table->is_reserved ? 'Mark as Available' : 'Mark as Reserved' }}
                            </button>
                        </form>

                        <form action="{{ route('management.tables.toggle-active', $table->id) }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit"
                                    class="w-full {{ $table->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2.5 rounded-lg transition-all duration-200 font-medium flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($table->is_active)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endif
                                </svg>
                                {{ $table->is_active ? 'Deactivate Table' : 'Activate Table' }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Audit Information Card --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-orange-100">
                    <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-white font-semibold text-lg">Audit Information</h3>
                        </div>
                        <p class="text-gray-300 text-sm mt-1 ml-9">Creation and update history</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="border-b border-gray-100 pb-3">
                            <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Created By
                            </label>
                            <p class="text-gray-800 font-medium mt-1">{{ $table->creator ? $table->creator->first_name . ' ' . $table->creator->last_name : 'System' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $table->created_at ? $table->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                        </div>
                        <div class="pb-2">
                            <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Last Updated By
                            </label>
                            <p class="text-gray-800 font-medium mt-1">{{ $table->updater ? $table->updater->first_name . ' ' . $table->updater->last_name : 'Never updated' }}</p>
                            @if($table->updated_at)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $table->updated_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Additional Info Card --}}
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-5 border border-orange-200">
                    <div class="flex items-start space-x-3">
                        <div class="bg-orange-100 rounded-lg p-2">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Quick Tips</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                • Use the Quick Actions to toggle table status<br>
                                • Edit button allows you to modify all details<br>
                                • Upcoming reservations show future bookings
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh the page every 30 seconds to update reservation status
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
@endpush
@endsection
