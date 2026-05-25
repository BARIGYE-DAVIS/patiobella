{{-- resources/views/users/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create New User</h2>
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800 transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('first_name') border-red-500 @enderror">
                @error('first_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200">
            </div>

            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-gray-700 font-medium mb-2">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Minimum 8 characters</p>
            </div>

            <div>
                <label for="department_id" class="block text-gray-700 font-medium mb-2">Department</label>
                <select name="department_id" id="department_id"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition duration-200 @error('department_id') border-red-500 @enderror">
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                <div class="flex items-center gap-6 mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500">
                        <span class="ml-2">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }} class="text-red-600 focus:ring-red-500">
                        <span class="ml-2">Inactive</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">User Creation Permission</label>
                <div class="flex items-center mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="can_create_users" value="1" {{ old('can_create_users') ? 'checked' : '' }} class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-5 h-5">
                        <span class="ml-2">Allow this user to create other users</span>
                    </label>
                </div>
                <p class="text-gray-500 text-xs mt-1">Only Super Admin can grant this permission</p>
            </div>
        </div>

        {{-- Multiple Roles Section --}}
        <div class="mt-6">
            <label class="block text-gray-700 font-medium mb-2">Roles <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3 p-4 border-2 border-gray-300 rounded-lg bg-gray-50">
                @foreach($roles as $role)
                    <label class="inline-flex items-center p-2 rounded transition duration-200 hover:bg-gray-100">
                        <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                            {{ in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-gray-700">{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('role_ids')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-xs mt-1">Select one or more roles for this user.</p>
        </div>

        {{-- Extra Permissions Section --}}
        <div class="mt-6">
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
                                <div class="grid grid-cols-12 gap-4 px-4 py-2 hover:bg-gray-50 transition duration-200 border-b border-gray-100">
                                    <div class="col-span-11 flex items-center">
                                        <span class="text-gray-700">{{ $permission->name }}</span>
                                        <span class="text-gray-400 text-xs ml-2">({{ $permission->code }})</span>
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <input type="checkbox" name="extra_permissions[]" value="{{ $permission->id }}"
                                            {{ in_array($permission->id, old('extra_permissions', [])) ? 'checked' : '' }}
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

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition duration-200">
                Cancel
            </a>
            <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition duration-200">
                <i class="fas fa-save mr-2"></i> Create User
            </button>
        </div>
    </form>
</div>
@endsection
