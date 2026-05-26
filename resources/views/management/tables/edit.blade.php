@extends('layouts.management')

@section('title', 'Edit Table')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-3 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            Edit Table
                        </h1>
                        <p class="text-gray-600 mt-1">Update table information</p>
                    </div>
                </div>
                <a href="{{ route('management.tables.index') }}"
                   class="group inline-flex items-center px-4 py-2 bg-white border-2 border-orange-200 rounded-lg text-orange-600 hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Tables
                </a>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-orange-100">
            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <h3 class="text-white font-semibold text-lg">Table Information</h3>
                </div>
                <p class="text-orange-100 text-sm mt-1 ml-9">Update the table details below</p>
            </div>

            <form method="POST" action="{{ route('management.tables.update', $table->id) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Table Number (Read-only) --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Table Number <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                            </div>
                            <input type="text" name="table_number" id="table_number" value="{{ old('table_number', $table->table_number) }}" required
                                   placeholder="e.g., T01, 1, VIP01"
                                   readonly
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed focus:outline-none @error('table_number') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('table_number')
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-gray-400 text-xs flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Table number cannot be changed (read-only)
                        </p>
                    </div>

                    {{-- Capacity --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Capacity <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <select name="capacity" id="capacity" required
                                    class="w-full pl-10 pr-4 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('capacity') border-red-400 bg-red-50 @enderror">
                                <option value="">Select Capacity</option>
                                <option value="1" {{ old('capacity', $table->capacity) == '1' ? 'selected' : '' }}>🍽️ 1 Seat</option>
                                <option value="2" {{ old('capacity', $table->capacity) == '2' ? 'selected' : '' }}>🍽️ 2 Seats</option>
                                <option value="4" {{ old('capacity', $table->capacity) == '4' ? 'selected' : '' }}>🍽️ 4 Seats</option>
                                <option value="6" {{ old('capacity', $table->capacity) == '6' ? 'selected' : '' }}>🍽️ 6 Seats</option>
                                <option value="8" {{ old('capacity', $table->capacity) == '8' ? 'selected' : '' }}>🍽️ 8 Seats</option>
                                <option value="10" {{ old('capacity', $table->capacity) == '10' ? 'selected' : '' }}>🍽️ 10 Seats</option>
                                <option value="12" {{ old('capacity', $table->capacity) == '12' ? 'selected' : '' }}>🍽️ 12 Seats</option>
                            </select>
                        </div>
                        @error('capacity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Size --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Size</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </div>
                            <select name="size" id="size"
                                    class="w-full pl-10 pr-4 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                                <option value="">Select Size</option>
                                <option value="Small" {{ old('size', $table->size) == 'Small' ? 'selected' : '' }}>📏 Small</option>
                                <option value="Medium" {{ old('size', $table->size) == 'Medium' ? 'selected' : '' }}>📐 Medium</option>
                                <option value="Large" {{ old('size', $table->size) == 'Large' ? 'selected' : '' }}>📏 Large</option>
                                <option value="Extra Large" {{ old('size', $table->size) == 'Extra Large' ? 'selected' : '' }}>📐 Extra Large</option>
                                <option value="VIP" {{ old('size', $table->size) == 'VIP' ? 'selected' : '' }}>⭐ VIP</option>
                            </select>
                        </div>
                        @error('size')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Location --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Location</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <select name="location" id="location"
                                    class="w-full pl-10 pr-4 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                                <option value="">Select Location</option>
                                <option value="Indoor" {{ old('location', $table->location) == 'Indoor' ? 'selected' : '' }}>🏠 Indoor</option>
                                <option value="Outdoor" {{ old('location', $table->location) == 'Outdoor' ? 'selected' : '' }}>🌿 Outdoor</option>
                                <option value="Terrace" {{ old('location', $table->location) == 'Terrace' ? 'selected' : '' }}>🏞️ Terrace</option>
                                <option value="VIP Room" {{ old('location', $table->location) == 'VIP Room' ? 'selected' : '' }}>👑 VIP Room</option>
                                <option value="Bar Area" {{ old('location', $table->location) == 'Bar Area' ? 'selected' : '' }}>🍸 Bar Area</option>
                                <option value="Garden" {{ old('location', $table->location) == 'Garden' ? 'selected' : '' }}>🌻 Garden</option>
                                <option value="Smoking Area" {{ old('location', $table->location) == 'Smoking Area' ? 'selected' : '' }}>🚬 Smoking Area</option>
                            </select>
                        </div>
                        @error('location')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Description</label>
                        <div class="relative">
                            <textarea name="description" id="description" rows="3"
                                      placeholder="Additional notes about this table (e.g., near window, has power outlet, etc.)"
                                      class="w-full px-4 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 resize-none @error('description') border-red-400 bg-red-50 @enderror">{{ old('description', $table->description) }}</textarea>
                            <div class="absolute bottom-2 right-3 text-xs text-gray-400">
                                <span id="charCount">0</span> characters
                            </div>
                        </div>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Fields --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Table Status</label>
                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_reserved" value="0" {{ old('is_reserved', $table->is_reserved) == '0' ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Available
                                </span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_reserved" value="1" {{ old('is_reserved', $table->is_reserved) == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 focus:ring-orange-500">
                                <span class="ml-2 text-sm text-gray-700 group-hover:text-orange-600 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Reserved
                                </span>
                            </label>
                        </div>
                        <p class="text-gray-400 text-xs">Current reservation status of the table</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Active Status</label>
                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $table->is_active) == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Active
                                </span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $table->is_active) == '0' ? 'checked' : '' }}
                                       class="w-4 h-4 text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-700 group-hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Inactive
                                </span>
                            </label>
                        </div>
                        <p class="text-gray-400 text-xs">Inactive tables will not appear in availability lists</p>
                    </div>

                    {{-- Sort Order --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Sort Order</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $table->sort_order) }}"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                        </div>
                        <p class="text-gray-400 text-xs">Order in which tables are displayed (lower numbers first)</p>
                    </div>
                </div>

                {{-- Stats Summary --}}
                <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $table->reservations()->count() }}</div>
                        <div class="text-xs text-gray-600">Total Reservations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $table->upcomingReservations()->count() }}</div>
                        <div class="text-xs text-gray-600">Upcoming Reservations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $table->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-600">Created Date</div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="mt-8 pt-6 border-t-2 border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('management.tables.index') }}"
                       class="px-6 py-2.5 text-sm border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-center font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="group px-8 py-2.5 text-sm bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200 shadow-md hover:shadow-lg font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Table
                    </button>
                </div>
            </form>
        </div>

        {{-- Danger Zone - Delete --}}
        @if($table->upcomingReservations()->count() == 0)
        <div class="mt-6">
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-2 border-red-200 rounded-xl overflow-hidden shadow-sm">
                <div class="bg-red-500 px-6 py-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="text-white font-semibold">Danger Zone</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <p class="font-semibold text-red-800">Delete this table</p>
                            <p class="text-sm text-red-600 mt-1">This action cannot be undone. The table will be permanently deleted.</p>
                        </div>
                        <button type="button" onclick="deleteTable()"
                                class="group bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium flex items-center space-x-2">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Delete Table</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="mt-6">
            <div class="bg-gradient-to-r from-amber-50 to-amber-100 border-2 border-amber-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-amber-800">Cannot Delete Table</h3>
                        <p class="text-sm text-amber-700 mt-1">This table has {{ $table->upcomingReservations()->count() }} upcoming reservation(s). Cancel or complete them before deleting.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<form id="delete-form" action="{{ route('management.tables.destroy', $table->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    // Character counter for description
    const description = document.getElementById('description');
    const charCount = document.getElementById('charCount');

    if (description && charCount) {
        const updateCharCount = () => {
            charCount.textContent = description.value.length;
        };
        description.addEventListener('input', updateCharCount);
        updateCharCount();
    }

    function deleteTable() {
        if (confirm('⚠️ Warning: Are you sure you want to delete this table?\n\nThis action cannot be undone and will permanently remove the table from the system.')) {
            document.getElementById('delete-form').submit();
        }
    }

    // Warn before leaving if changes are unsaved
    let formModified = false;
    const form = document.querySelector('form');
    const formInputs = form.querySelectorAll('input, textarea, select');

    formInputs.forEach(input => {
        if (!input.readOnly) {
            input.addEventListener('change', () => {
                formModified = true;
            });
            input.addEventListener('input', () => {
                formModified = true;
            });
        }
    });

    window.addEventListener('beforeunload', (e) => {
        if (formModified) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });

    form.addEventListener('submit', () => {
        formModified = false;
    });
</script>
@endsection
