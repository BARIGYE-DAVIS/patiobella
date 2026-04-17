@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Create New Role</h2>
            <p class="text-gray-500 text-sm mt-1">Create a role and assign permissions</p>
        </div>
        <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-800">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Roles
        </a>
    </div>

    <form method="POST" action="{{ route('roles.store') }}" id="roleForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Role Code --}}
            <div>
                <label for="code" class="block text-gray-700 font-medium mb-2">Role Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" required
                       placeholder="e.g., kitchen_supervisor"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-1">Unique identifier (use underscores, no spaces)</p>
            </div>

            {{-- Role Name --}}
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">Role Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       placeholder="e.g., Kitchen Supervisor"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="md:col-span-2">
                <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" id="description" rows="2"
                          placeholder="Brief description of this role and its responsibilities"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-gray-700 font-medium mb-2">Status</label>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="text-blue-600">
                        <span class="ml-2">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }} class="text-blue-600">
                        <span class="ml-2">Inactive</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Permissions Section --}}
        <div class="border-t border-gray-200 pt-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Assign Permissions</h3>
                    <p class="text-gray-500 text-sm">Select the permissions this role should have. You can also create new permissions.</p>
                </div>
            </div>

            <div class="space-y-6" id="permissions-container">
                @foreach($permissions as $group => $groupPermissions)
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-group="{{ $group }}">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="group-checkbox text-blue-600 rounded" data-group="{{ $group }}">
                                <span class="ml-2 font-semibold text-gray-700 uppercase text-sm">{{ ucfirst(str_replace('_', ' ', $group)) }}</span>
                            </label>
                            <button type="button" class="text-blue-600 text-sm hover:text-blue-800 flex items-center gap-1 add-permission-btn" data-group="{{ $group }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add New Permission
                            </button>
                        </div>
                        <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 permission-list" data-group="{{ $group }}">
                            @foreach($groupPermissions as $permission)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                           class="permission-checkbox text-blue-600 rounded" data-group="{{ $group }}"
                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @error('permissions')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
            @error('new_permissions')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Form Actions --}}
        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('roles.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Role
            </button>
        </div>
    </form>
</div>

{{-- Modal for creating new permission --}}
<div id="permissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Create New Permission</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Permission Name <span class="text-red-500">*</span></label>
            <input type="text" id="new_perm_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   placeholder="e.g., Manage Special Orders">
            <p class="text-gray-400 text-xs mt-1">The permission code will be generated automatically</p>
        </div>
        <input type="hidden" id="current_group">
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="button" onclick="createPermission()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Permission</button>
        </div>
    </div>
</div>

<script>
    // Get the base URL for AJAX requests
    const permissionsStoreUrl = '{{ url("/permissions/store") }}';
    let currentGroup = '';

    // Function to generate code from name (convert to snake_case)
    function generateCodeFromName(name) {
        return name
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')  // Remove special characters
            .trim()
            .replace(/\s+/g, '_');         // Replace spaces with underscores
    }

    // Open modal when Add New Permission is clicked
    document.querySelectorAll('.add-permission-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentGroup = this.dataset.group;
            document.getElementById('current_group').value = currentGroup;
            document.getElementById('new_perm_name').value = '';
            document.getElementById('permissionModal').classList.remove('hidden');
        });
    });

    function closeModal() {
        document.getElementById('permissionModal').classList.add('hidden');
    }

    function createPermission() {
        const name = document.getElementById('new_perm_name').value.trim();
        const group = currentGroup;

        if (!name) {
            alert('Please enter permission name.');
            return;
        }

        // Auto-generate code from name
        const code = generateCodeFromName(name);

        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Send AJAX request to create permission
        fetch(permissionsStoreUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                code: code,
                name: name,
                group: group
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add the new permission to the UI
                const permissionList = document.querySelector(`.permission-list[data-group="${group}"]`);
                const newCheckbox = document.createElement('label');
                newCheckbox.className = 'flex items-center cursor-pointer';
                newCheckbox.innerHTML = `
                    <input type="checkbox" name="permissions[]" value="${data.permission.id}" 
                           class="permission-checkbox text-blue-600 rounded" data-group="${group}" checked>
                    <span class="ml-2 text-sm text-gray-700">${data.permission.name}</span>
                `;
                permissionList.appendChild(newCheckbox);

                // Add event listener to the new checkbox
                const newCheckboxInput = newCheckbox.querySelector('input');
                newCheckboxInput.addEventListener('change', function() {
                    updateGroupCheckbox(group);
                });

                // Update group checkbox state
                updateGroupCheckbox(group);

                // Close modal
                closeModal();

                // Show success message
                alert('Permission created successfully!');
            } else {
                alert(data.message || 'Error creating permission');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the permission.');
        });
    }

    // Select/Deselect all permissions in a group
    document.querySelectorAll('.group-checkbox').forEach(groupCheckbox => {
        groupCheckbox.addEventListener('change', function() {
            const group = this.dataset.group;
            const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });

    // Update group checkbox when individual permissions are changed
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const group = this.dataset.group;
            updateGroupCheckbox(group);
        });
    });

    function updateGroupCheckbox(group) {
        const groupCheckbox = document.querySelector(`.group-checkbox[data-group="${group}"]`);
        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const someChecked = Array.from(checkboxes).some(cb => cb.checked);
        
        if (allChecked) {
            groupCheckbox.checked = true;
            groupCheckbox.indeterminate = false;
        } else if (someChecked) {
            groupCheckbox.checked = false;
            groupCheckbox.indeterminate = true;
        } else {
            groupCheckbox.checked = false;
            groupCheckbox.indeterminate = false;
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('permissionModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
@endsection