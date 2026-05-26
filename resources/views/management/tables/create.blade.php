@extends('layouts.management')

@section('title', 'Add New Table')

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
                            Add New Table
                        </h1>
                        <p class="text-gray-600 mt-1">Add a new table to the restaurant</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <h3 class="text-white font-semibold text-lg">Table Information</h3>
                </div>
                <p class="text-orange-100 text-sm mt-1 ml-9">Fill in the details below to add a new table</p>
            </div>

            <form method="POST" action="{{ route('management.tables.store') }}" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Table Number (Auto-generated, read-only) --}}
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
                            <input type="text" name="table_number" id="table_number" value="{{ $nextTableNumber }}" readonly
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed focus:outline-none">
                        </div>
                        <p class="text-gray-400 text-xs flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Automatically generated. Next available: {{ $nextTableNumber }}
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
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('capacity') border-red-400 bg-red-50 @enderror">
                                <option value="">Select Capacity</option>
                                <option value="1" {{ old('capacity') == '1' ? 'selected' : '' }}>🍽️ 1 Seat</option>
                                <option value="2" {{ old('capacity') == '2' ? 'selected' : '' }}>🍽️ 2 Seats</option>
                                <option value="4" {{ old('capacity') == '4' ? 'selected' : '' }}>🍽️ 4 Seats</option>
                                <option value="6" {{ old('capacity') == '6' ? 'selected' : '' }}>🍽️ 6 Seats</option>
                                <option value="8" {{ old('capacity') == '8' ? 'selected' : '' }}>🍽️ 8 Seats</option>
                                <option value="10" {{ old('capacity') == '10' ? 'selected' : '' }}>🍽️ 10 Seats</option>
                                <option value="12" {{ old('capacity') == '12' ? 'selected' : '' }}>🍽️ 12 Seats</option>
                            </select>
                        </div>
                        @error('capacity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
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
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                                <option value="">Select Size</option>
                                <option value="Small" {{ old('size') == 'Small' ? 'selected' : '' }}>📏 Small</option>
                                <option value="Medium" {{ old('size') == 'Medium' ? 'selected' : '' }}>📏 Medium</option>
                                <option value="Large" {{ old('size') == 'Large' ? 'selected' : '' }}>📏 Large</option>
                                <option value="Extra Large" {{ old('size') == 'Extra Large' ? 'selected' : '' }}>📏 Extra Large</option>
                                <option value="VIP" {{ old('size') == 'VIP' ? 'selected' : '' }}>⭐ VIP</option>
                            </select>
                        </div>
                        @error('size')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
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
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                                <option value="">Select Location</option>
                                <option value="Indoor" {{ old('location') == 'Indoor' ? 'selected' : '' }}>🏠 Indoor</option>
                                <option value="Outdoor" {{ old('location') == 'Outdoor' ? 'selected' : '' }}>🌿 Outdoor</option>
                                <option value="Terrace" {{ old('location') == 'Terrace' ? 'selected' : '' }}>🏞️ Terrace</option>
                                <option value="VIP Room" {{ old('location') == 'VIP Room' ? 'selected' : '' }}>👑 VIP Room</option>
                                <option value="Bar Area" {{ old('location') == 'Bar Area' ? 'selected' : '' }}>🍸 Bar Area</option>
                                <option value="Garden" {{ old('location') == 'Garden' ? 'selected' : '' }}>🌻 Garden</option>
                                <option value="Smoking Area" {{ old('location') == 'Smoking Area' ? 'selected' : '' }}>🚬 Smoking Area</option>
                            </select>
                        </div>
                        @error('location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Description</label>
                        <div class="relative">
                            <textarea name="description" id="description" rows="3"
                                      placeholder="Additional notes about this table (e.g., near window, has power outlet, etc.)"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 resize-none @error('description') border-red-400 bg-red-50 @enderror">{{ old('description') }}</textarea>
                            <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                                <span id="charCount">0</span> characters
                            </div>
                        </div>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Table Status --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Table Status</label>
                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_reserved" value="0" {{ old('is_reserved', '0') == '0' ? 'checked' : '' }}
                                       class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-green-600 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Available
                                </span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_reserved" value="1" {{ old('is_reserved') == '1' ? 'checked' : '' }}
                                       class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-orange-600 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Reserved
                                </span>
                            </label>
                        </div>
                        <p class="text-gray-400 text-xs">Current reservation status of the table</p>
                    </div>

                    {{-- Active Status --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Active Status</label>
                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                       class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-green-600 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Active
                                </span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}
                                       class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-red-600 transition-colors">
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
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                        </div>
                        <p class="text-gray-400 text-xs">Order in which tables are displayed (lower numbers first)</p>
                    </div>
                </div>

                {{-- Preview Card --}}
                <div class="mt-8 p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Table Preview</p>
                                <p class="text-sm font-semibold text-gray-800" id="previewText">Table {{ $nextTableNumber }}</p>
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
                    <a href="{{ route('management.tables.index') }}"
                       class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-center font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="group px-8 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200 shadow-md hover:shadow-lg font-medium flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Table
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
                        • Table numbers are automatically generated as TB001, TB002, etc.<br>
                        • Sort order determines display sequence (lower numbers appear first)<br>
                        • Inactive tables won't be shown in availability lists or reservation forms
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

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
</script>
@endsection
