@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section with Gradient --}}
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-3 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            Edit Role
                        </h1>
                        <p class="text-gray-600 mt-1">Update role details and permissions</p>
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
                <p class="text-orange-100 text-sm mt-1 ml-9">Edit the role details below</p>
            </div>

            <form method="POST" action="{{ route('roles.update', $role->id) }}" id="roleForm" class="p-6">
                @csrf
                @method('PUT')

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
                            <input type="text" name="code" id="code" value="{{ old('code', $role->code) }}" required
                                   placeholder="e.g., kitchen_supervisor"
                                   {{ $role->is_system_role ? 'readonly' : '' }}
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 @error('code') border-red-400 bg-red-50 @enderror @if($role->is_system_role) bg-gray-100 cursor-not-allowed @endif">
                        </div>
                        @error('code')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        @if($role->is_system_role)
                            <p class="text-amber-600 text-xs mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                System role codes cannot be changed
                            </p>
                        @else
                            <p class="text-gray-400 text-xs flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Unique identifier (lowercase, underscores only)
                            </p>
                        @endif
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
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
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
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200 resize-none @error('description') border-red-400 bg-red-50 @enderror">{{ old('description', $role->description) }}</textarea>
                            <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                                <span id="charCount">0</span> characters
                            </div>
                        </div>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $role->is_active) == '1' ? 'checked' : '' }}
                                       class="w-5 h-5 text-orange-500 focus:ring-orange-400 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-orange-600 transition-colors">Active</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $role->is_active) == '0' ? 'checked' : '' }}
                                       class="w-5 h-5 text-gray-500 focus:ring-orange-400 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-orange-600 transition-colors">Inactive</span>
                            </label>
                        </div>
                    </div>

                    {{-- System Role --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-semibold mb-2">System Role</label>
                        <div class="p-3 rounded-lg border-2 {{ $role->is_system_role ? 'bg-amber-50 border-amber-200' : 'bg-gray-50 border-gray-200' }}">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="checkbox" name="is_system_role" value="1" {{ old('is_system_role', $role->is_system_role) ? 'checked' : '' }}
                                       class="w-5 h-5 rounded text-purple-500 focus:ring-purple-400 border-gray-300">
                                <span class="ml-2 text-gray-700 group-hover:text-purple-600 transition-colors">
                                    System Role (Protected)
                                </span>
                            </label>
                            @if($role->is_system_role)
                                <p class="text-xs text-amber-600 mt-2 ml-7 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Uncheck to remove system role protection
                                </p>
                            @else
                                <p class="text-xs text-gray-400 mt-2 ml-7">System roles are protected and cannot be deleted</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stats Summary --}}
                <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $role->users()->count() }}</div>
                        <div class="text-xs text-gray-600">Users Assigned</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $role->permissions()->count() }}</div>
                        <div class="text-xs text-gray-600">Permissions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $role->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-600">Created Date</div>
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
                        Update Role
                    </button>
                </div>
            </form>
        </div>

        {{-- Danger Zone - Delete (Only for non-system roles with no users) --}}
        @if(!$role->is_system_role && $role->users()->count() == 0)
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
                            <p class="font-semibold text-red-800">Delete this role</p>
                            <p class="text-sm text-red-600 mt-1">This action cannot be undone. The role will be permanently deleted.</p>
                        </div>
                        <button type="button" onclick="deleteRole({{ $role->id }})"
                                class="group bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium flex items-center space-x-2">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Delete Role</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @elseif($role->is_system_role)
        <div class="mt-6">
            <div class="bg-gradient-to-r from-amber-50 to-amber-100 border-2 border-amber-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-amber-800">System Role Protected</h3>
                        <p class="text-sm text-amber-700 mt-1">This is a system role. Uncheck the "System Role" checkbox above to remove protection and enable deletion.</p>
                    </div>
                </div>
            </div>
        </div>
        @elseif($role->users()->count() > 0)
        <div class="mt-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-blue-800">Cannot Delete Role</h3>
                        <p class="text-sm text-blue-700 mt-1">This role is currently assigned to {{ $role->users()->count() }} user(s). Please reassign or remove these users before deleting.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<form id="delete-form" action="{{ route('roles.destroy', $role->id) }}" method="POST" class="hidden">
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

    // Live validation for role code (only lowercase, underscores, and numbers)
    const codeInput = document.getElementById('code');
    if (codeInput && !codeInput.readOnly) {
        codeInput.addEventListener('input', function(e) {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
        });
    }

    function deleteRole(id) {
        if (confirm('⚠️ Warning: Are you sure you want to delete this role?\n\nThis action cannot be undone and will permanently remove the role from the system.')) {
            document.getElementById('delete-form').submit();
        }
    }

    // Warn before leaving if changes are unsaved
    let formModified = false;
    const form = document.getElementById('roleForm');
    const formInputs = form.querySelectorAll('input, textarea, select');

    formInputs.forEach(input => {
        input.addEventListener('change', () => {
            formModified = true;
        });
        input.addEventListener('input', () => {
            formModified = true;
        });
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
