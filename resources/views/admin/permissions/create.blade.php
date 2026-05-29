@extends('layouts.app')

@section('title', 'Create Permission')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Create New Permission</h2>
            <p class="text-gray-500 text-sm mt-1">Add a new permission to the system</p>
        </div>
        <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-800">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Permissions
        </a>
    </div>

    <form method="POST" action="{{ route('permissions.store') }}" id="permissionForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Permission Name --}}
            <div class="space-y-2">
                <label class="block text-gray-700 font-semibold mb-2">
                    Permission Name <span class="text-orange-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           placeholder="e.g., Manage Users"
                           class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('name') border-red-500 @enderror">
                </div>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs">The display name of the permission</p>
            </div>

            {{-- Permission Code --}}
            <div class="space-y-2">
                <label class="block text-gray-700 font-semibold mb-2">
                    Permission Code <span class="text-orange-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                           placeholder="e.g., manage_users"
                           class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('code') border-red-500 @enderror">
                </div>
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs">Unique identifier (lowercase, underscores only)</p>
            </div>

            {{-- Group --}}
            <div class="space-y-2">
                <label class="block text-gray-700 font-semibold mb-2">
                    Group <span class="text-orange-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <select name="group" id="group" required
                            class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('group') border-red-500 @enderror">
                        <option value="">Select Group</option>
                        <option value="users" {{ old('group') == 'users' ? 'selected' : '' }}>Users</option>
                        <option value="roles" {{ old('group') == 'roles' ? 'selected' : '' }}>Roles</option>
                        <option value="permissions" {{ old('group') == 'permissions' ? 'selected' : '' }}>Permissions</option>
                        <option value="departments" {{ old('group') == 'departments' ? 'selected' : '' }}>Departments</option>
                        <option value="inventory" {{ old('group') == 'inventory' ? 'selected' : '' }}>Inventory</option>
                        <option value="purchasing" {{ old('group') == 'purchasing' ? 'selected' : '' }}>Purchasing</option>
                        <option value="vendors" {{ old('group') == 'vendors' ? 'selected' : '' }}>Vendors</option>
                        <option value="requisitions" {{ old('group') == 'requisitions' ? 'selected' : '' }}>Requisitions</option>
                        <option value="stock" {{ old('group') == 'stock' ? 'selected' : '' }}>Stock</option>
                        <option value="menu" {{ old('group') == 'menu' ? 'selected' : '' }}>Menu</option>
                        <option value="sales" {{ old('group') == 'sales' ? 'selected' : '' }}>Sales</option>
                        <option value="reports" {{ old('group') == 'reports' ? 'selected' : '' }}>Reports</option>
                        <option value="settings" {{ old('group') == 'settings' ? 'selected' : '' }}>Settings</option>
                        <option value="other" {{ old('group') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                @error('group')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs">Group this permission belongs to (for organization)</p>
            </div>

            {{-- Status --}}
            <div class="space-y-2">
                <label class="block text-gray-700 font-semibold mb-2">Status</label>
                <div class="flex gap-4 p-3 bg-gray-50 rounded-lg border-2 border-gray-200">
                    <label class="inline-flex items-center cursor-pointer group">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-gray-700 group-hover:text-green-600 transition-colors">Active</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer group">
                        <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}
                               class="w-5 h-5 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-gray-700 group-hover:text-red-600 transition-colors">Inactive</span>
                    </label>
                </div>
                <p class="text-gray-400 text-xs">Inactive permissions will not be available for assignment</p>
            </div>

            {{-- Description --}}
            <div class="md:col-span-2 space-y-2">
                <label class="block text-gray-700 font-semibold mb-2">Description</label>
                <div class="relative">
                    <textarea name="description" id="description" rows="3"
                              placeholder="Brief description of what this permission allows..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 resize-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs">Optional description explaining the purpose of this permission</p>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('permissions.index') }}"
               class="px-6 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Permission
            </button>
        </div>
    </form>
</div>

<script>
    // Auto-generate code from name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');

    function generateCodeFromName(name) {
        return name
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .trim()
            .replace(/\s+/g, '_');
    }

    if (nameInput && codeInput) {
        let manualEdit = false;

        codeInput.addEventListener('focus', function() {
            manualEdit = true;
        });

        nameInput.addEventListener('input', function() {
            if (!manualEdit) {
                codeInput.value = generateCodeFromName(this.value);
            }
        });
    }
</script>
@endsection
