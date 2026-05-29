@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section with Gradient --}}
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-3 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            Create New Role
                        </h1>
                        <p class="text-gray-600 mt-1">Define a new role and configure its permissions</p>
                    </div>
                </div>
                <a href="{{ route('roles.index') }}"
                   class="group inline-flex items-center px-4 py-2 bg-white border-2 border-orange-200 rounded-lg text-orange-600 hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Roles
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
                    <h3 class="text-white font-semibold text-lg">Role Information</h3>
                </div>
                <p class="text-orange-100 text-sm mt-1 ml-9">Fill in the details below to create a new role</p>
            </div>

            <form method="POST" action="{{ route('roles.store') }}" id="roleForm" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Role Code --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Role Code <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                            </div>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                   placeholder="e.g., kitchen_supervisor"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('code') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('code')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-gray-400 text-xs flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Unique identifier (lowercase, underscores only)
                        </p>
                    </div>

                    {{-- Role Name --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">
                            Role Name <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="e.g., Kitchen Supervisor"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('name') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Description</label>
                        <div class="relative">
                            <textarea name="description" id="description" rows="4"
                                      placeholder="Brief description of this role and its responsibilities..."
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 resize-none @error('description') border-red-400 bg-red-50 @enderror">{{ old('description') }}</textarea>
                            <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                                <span id="charCount">0</span> characters
                            </div>
                        </div>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Toggle --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                       class="w-5 h-5 text-orange-500 focus:ring-orange-400 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-orange-600 transition-colors">Active</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}
                                       class="w-5 h-5 text-gray-500 focus:ring-orange-400 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-orange-600 transition-colors">Inactive</span>
                            </label>
                        </div>
                    </div>

                    {{-- System Role Toggle --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">System Role</label>
                        <div class="p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="checkbox" name="is_system_role" value="1" {{ old('is_system_role') ? 'checked' : '' }}
                                       class="w-5 h-5 text-purple-500 focus:ring-purple-400 border-gray-300 rounded">
                                <span class="ml-2 text-gray-700 group-hover:text-purple-600 transition-colors">System Role (Protected)</span>
                            </label>
                            <p class="text-xs text-gray-400 mt-2 ml-7">System roles are protected and cannot be deleted</p>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="mt-8 pt-6 border-t-2 border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('roles.index') }}"
                       class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-center font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="group px-8 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200 shadow-md hover:shadow-lg font-medium flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Role
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
                        • Role codes should be unique and descriptive (e.g., <span class="font-mono text-orange-600">admin_manager</span>)<br>
                        • You can assign permissions to roles after creation<br>
                        • System roles are marked as protected and cannot be removed
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

// Live validation for role code (only lowercase, underscores, and numbers)
const codeInput = document.getElementById('code');
if (codeInput) {
    codeInput.addEventListener('input', function(e) {
        this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
    });
}
</script>
@endsection
