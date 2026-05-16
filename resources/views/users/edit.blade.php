{{-- resources/views/users/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>

    <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-500 @enderror">
                @error('first_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role_id" class="block text-gray-700 font-medium mb-2">Role <span class="text-red-500">*</span></label>
                <select name="role_id" id="role_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role_id') border-red-500 @enderror">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="department_id" class="block text-gray-700 font-medium mb-2">Department</label>
                <select name="department_id" id="department_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department_id') border-red-500 @enderror">
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

            <div>
                <label class="block text-gray-700 font-medium mb-2">Status</label>
                <div class="flex items-center mt-2">
                    <label class="inline-flex items-center mr-6">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }} class="text-blue-600">
                        <span class="ml-2">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }} class="text-blue-600">
                        <span class="ml-2">Inactive</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">User Creation Permission</label>
                <div class="flex items-center mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="can_create_users" value="1" {{ old('can_create_users', $user->can_create_users) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2">Allow this user to create other users</span>
                    </label>
                </div>
                <p class="text-gray-500 text-xs mt-1">Only Super Admin can grant this permission</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
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
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Minimum 8 characters. Leave blank to keep current password.</p>
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- Danger Zone (Only for Super Admin) --}}
    @if(auth()->user()->is_super_admin && $user->id !== auth()->user()->id)
    <div class="mt-8 border-t border-red-200 pt-6">
        <h3 class="text-lg font-semibold text-red-600 mb-4">Danger Zone</h3>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-medium text-red-800">Delete this user</p>
                    <p class="text-sm text-red-600">Once deleted, this user can be restored by an administrator.</p>
                </div>
                <button type="button" onclick="deleteUser({{ $user->id }})"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Delete User
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<form id="delete-form" action="{{ route('users.destroy', $user->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user? This action can be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection
