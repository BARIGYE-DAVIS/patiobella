{{-- resources/views/users/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800 transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>

    <form method="POST" action="{{ route('users.update', $user->id) }}" id="userForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('first_name') border-red-500 @enderror">
                @error('first_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200">
            </div>

            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="department_id" class="block text-gray-700 font-medium mb-2">Department</label>
                <select name="department_id" id="department_id"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('department_id') border-red-500 @enderror">
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Assign user to a specific department (optional)</p>
            </div>

            {{-- Multiple Roles Section --}}
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-medium mb-2">Roles <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3 p-4 border-2 border-gray-300 rounded-lg bg-gray-50">
                    @foreach($roles as $role)
                        @php
                            $isChecked = in_array($role->id, old('role_ids', $userRoleIds));
                        @endphp
                        <label class="inline-flex items-center p-2 rounded transition duration-200 {{ $isChecked ? 'bg-green-50 border-l-4 border-green-500' : 'hover:bg-gray-100' }}">
                            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                {{ $isChecked ? 'checked' : '' }}
                                class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 {{ $isChecked ? 'text-gray-900 font-medium' : 'text-gray-600' }}">
                                {{ $role->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('role_ids')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Select one or more roles for this user.</p>
            </div>

            {{-- User Specific Permissions Section - Single Checkbox --}}
            <div class="md:col-span-2 mt-4">
                <label class="block text-gray-700 font-medium mb-2">Extra Permissions (Add to Role)</label>
                <div class="border-2 border-gray-300 rounded-lg overflow-hidden">
                    <div class="bg-orange-50 px-4 py-2 border-b border-gray-300">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-11">
                                <span class="text-sm font-medium text-gray-700">Permission</span>
                            </div>
                            <div class="col-span-1 text-center">
                                <span class="text-sm font-medium text-green-600">Allow</span>
                            </div>
                        </div>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @php
                            $groupedPermissions = $permissions->groupBy('group');
                        @endphp
                        @foreach($groupedPermissions as $groupName => $groupPermissions)
                            <div class="border-b border-gray-200">
                                <div class="bg-gray-100 px-4 py-2">
                                    <span class="text-sm font-semibold text-gray-700 uppercase">{{ $groupName ?: 'General' }}</span>
                                </div>
                                @foreach($groupPermissions as $permission)
                                    @php
                                        $isAllowed = in_array($permission->id, $extraPermissionIds);
                                    @endphp
                                    <div class="grid grid-cols-12 gap-4 px-4 py-2 hover:bg-gray-50 transition duration-200 border-b border-gray-100">
                                        <div class="col-span-11 flex items-center">
                                            <span class="text-gray-700">{{ $permission->name }}</span>
                                            <span class="text-gray-400 text-xs ml-2">({{ $permission->code }})</span>
                                        </div>
                                        <div class="col-span-1 text-center">
                                            <input type="checkbox" name="extra_permissions[]" value="{{ $permission->id }}"
                                                {{ $isAllowed ? 'checked' : '' }}
                                                class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <p class="text-gray-500 text-xs mt-2">
                    <span class="inline-block w-3 h-3 bg-green-100 border border-green-500 mr-1"></span>
                    Checked = User gets this permission IN ADDITION to their role permissions.
                </p>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Status</label>
                <div class="flex items-center gap-6 mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-green-600 focus:ring-green-500">
                        <span class="ml-2">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <span class="ml-2">Inactive</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">User Creation Permission</label>
                <div class="flex items-center mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="can_create_users" value="1" {{ old('can_create_users', $user->can_create_users) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-5 h-5">
                        <span class="ml-2">Allow this user to create other users</span>
                    </label>
                </div>
                <p class="text-gray-500 text-xs mt-1">Only Super Admin can grant this permission</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition duration-200">
                Cancel
            </a>
            <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition duration-200">
                <i class="fas fa-save mr-2"></i> Update User
            </button>
        </div>
    </form>

    {{-- Separate Password Change Form --}}
    <div class="mt-8 border-t border-gray-200 pt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
        <form method="POST" action="{{ route('users.update-password', $user->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            @method('PUT')
            <div>
                <label for="password" class="block text-gray-700 font-medium mb-2">New Password</label>
                <input type="password" name="password" id="password"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Minimum 8 characters. Leave blank to keep current password.</p>
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition duration-200">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- Danger Zone (Only for Super Admin) --}}
    @if(auth()->user()->is_super_admin && $user->id !== auth()->user()->id)
    <div class="mt-8 border-t-2 border-red-200 pt-6">
        <h3 class="text-lg font-semibold text-red-600 mb-4">Danger Zone</h3>
        <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <p class="font-medium text-red-800">Delete this user</p>
                    <p class="text-sm text-red-600">Once deleted, this user can be restored by an administrator.</p>
                </div>
                <button type="button" onclick="confirmDelete()"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                    <i class="fas fa-trash mr-2"></i> Delete User
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 text-center mb-2">Confirm Delete</h3>
            <p class="text-gray-600 text-center mb-6">Are you sure you want to delete user <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>? This action can be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-200">
                    Cancel
                </button>
                <button type="button" onclick="submitDelete()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('users.destroy', $user->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete() {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    function submitDelete() {
        document.getElementById('delete-form').submit();
    }

    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection
