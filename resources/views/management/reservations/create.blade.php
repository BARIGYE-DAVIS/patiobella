@extends('layouts.management')

@section('title', 'Create Reservation')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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
                            Create Reservation
                        </h1>
                        <p class="text-gray-600 mt-1">Book a table for a customer</p>
                    </div>
                </div>
                <a href="{{ route('management.reservations.index') }}"
                   class="group inline-flex items-center px-4 py-2 bg-white border-2 border-orange-200 rounded-lg text-orange-600 hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Reservations
                </a>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-orange-100">
            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <h3 class="text-white font-semibold text-lg">Reservation Details</h3>
                </div>
                <p class="text-orange-100 text-sm mt-1 ml-9">Fill in the customer and reservation information</p>
            </div>

            <form method="POST" action="{{ route('management.reservations.store') }}" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Customer Name --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Customer Name <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                                   placeholder="Enter customer name"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('customer_name') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('customer_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Customer Phone --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Customer Phone</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                                   placeholder="e.g., 0777 123456"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                        </div>
                        @error('customer_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Customer Email --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Customer Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                                   placeholder="customer@example.com"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                        </div>
                        @error('customer_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Table Selection --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Select Table <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <select name="restaurant_table_id" id="restaurant_table_id" required
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('restaurant_table_id') border-red-400 bg-red-50 @enderror">
                                <option value="">Select a table</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}" {{ old('restaurant_table_id') == $table->id ? 'selected' : '' }}>
                                        Table {{ $table->table_number }} - {{ $table->capacity }} seats - {{ $table->location ?? 'No location' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('restaurant_table_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Reservation Date --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Reservation Date <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="date" name="reservation_date" id="reservation_date" value="{{ old('reservation_date', date('Y-m-d')) }}" required
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('reservation_date') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('reservation_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Reservation Time --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Reservation Time <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input type="time" name="reservation_time" id="reservation_time" value="{{ old('reservation_time', '19:00') }}" required
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('reservation_time') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('reservation_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Duration Hours --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Duration (Hours) <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <select name="duration_hours" id="duration_hours" required
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                                <option value="1" {{ old('duration_hours') == '1' ? 'selected' : '' }}>1 Hour</option>
                                <option value="2" {{ old('duration_hours') == '2' ? 'selected' : '' }}>2 Hours</option>
                                <option value="3" {{ old('duration_hours') == '3' ? 'selected' : '' }}>3 Hours</option>
                                <option value="4" {{ old('duration_hours') == '4' ? 'selected' : '' }}>4 Hours</option>
                            </select>
                        </div>
                        @error('duration_hours')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Number of Guests --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Number of Guests <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <input type="number" name="number_of_guests" id="number_of_guests" value="{{ old('number_of_guests', 2) }}" required
                                   min="1" max="50"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('number_of_guests') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('number_of_guests')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="status" value="pending" {{ old('status', 'pending') == 'pending' ? 'checked' : '' }}
                                       class="w-5 h-5 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-yellow-600 transition-colors">⏳ Pending</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="status" value="confirmed" {{ old('status') == 'confirmed' ? 'checked' : '' }}
                                       class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-green-600 transition-colors">✅ Confirmed</span>
                            </label>
                        </div>
                        <p class="text-gray-400 text-xs">Pending = Awaiting confirmation, Confirmed = Reservation is locked</p>
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Special Notes</label>
                        <div class="relative">
                            <textarea name="notes" id="notes" rows="3"
                                      placeholder="Any special requests or notes (e.g., wheelchair access, high chair, celebration, etc.)"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 resize-none @error('notes') border-red-400 bg-red-50 @enderror">{{ old('notes') }}</textarea>
                        </div>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Preview Card --}}
                <div class="mt-8 p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Reservation Preview</p>
                                <p class="text-sm font-semibold text-gray-800" id="previewText">Fill in the form above</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="text-sm font-semibold text-green-600" id="previewStatus">Ready to create</p>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="mt-8 pt-6 border-t-2 border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('management.reservations.index') }}"
                       class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-center font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="group px-8 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200 shadow-md hover:shadow-lg font-medium flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Reservation
                    </button>
                </div>
            </form>
        </div>

        {{-- Help Section --}}
        <div class="mt-6 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-4 border border-orange-200">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="font-semibold text-gray-800">Quick Tips</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        • All reservations require customer name and contact information<br>
                        • Tables are automatically marked as reserved when a reservation is created<br>
                        • Status "Pending" requires confirmation before the table is locked<br>
                        • You can update reservation status later from the reservation details page
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live preview
    const customerName = document.getElementById('customer_name');
    const tableSelect = document.getElementById('restaurant_table_id');
    const dateSelect = document.getElementById('reservation_date');
    const previewText = document.getElementById('previewText');

    function updatePreview() {
        const name = customerName ? customerName.value : '';
        const table = tableSelect ? tableSelect.options[tableSelect.selectedIndex]?.text : '';
        const date = dateSelect ? dateSelect.value : '';

        if (name) {
            previewText.textContent = `${name} - ${table} on ${date}`;
        } else {
            previewText.textContent = 'Fill in the form above';
        }
    }

    if (customerName) customerName.addEventListener('input', updatePreview);
    if (tableSelect) tableSelect.addEventListener('change', updatePreview);
    if (dateSelect) dateSelect.addEventListener('change', updatePreview);
</script>
@endsection
