@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Users</h2>
        <a href="{{ route('users.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New User
        </a>
    </div>

    {{-- Filters --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Name or email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" id="department_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" id="is_active" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="md:col-span-4 flex justify-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Apply Filters
                </button>
                <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Can Create Users</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">
                                {{ $user->department ? $user->department->name : '—' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                // Find role name from roles collection using the role ID
                                $roleName = 'No Role';
                                foreach($roles as $role) {
                                    if($role->id == $user->role) {
                                        $roleName = $role->name;
                                        break;
                                    }
                                }
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $roleName }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_active)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->can_create_users)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Yes
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                    No
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            <a href="{{ route('users.edit', $user->id) }}" class="text-amber-600 hover:text-amber-900 mr-3">Edit</a>
                            @if($user->is_active)
                                <button type="button" onclick="deactivateUser({{ $user->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">Deactivate</button>
                            @else
                                <button type="button" onclick="activateUser({{ $user->id }})" class="text-green-600 hover:text-green-900 mr-3">Activate</button>
                            @endif
                            @if(auth()->user()->is_super_admin && $user->id !== auth()->user()->id)
                                <button type="button" onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No users found.
                            <a href="{{ route('users.create') }}" class="text-blue-600 hover:underline ml-2">Create your first user</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

{{-- Forms for actions --}}
<form id="activate-form" action="" method="POST" class="hidden">
    @csrf
    @method('PATCH')
</form>

<form id="deactivate-form" action="" method="POST" class="hidden">
    @csrf
    @method('PATCH')
</form>

<form id="delete-form" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function activateUser(id) {
        if (confirm('Are you sure you want to activate this user?')) {
            const form = document.getElementById('activate-form');
            form.action = '/users/' + id + '/activate';
            form.submit();
        }
    }

    function deactivateUser(id) {
        if (confirm('Are you sure you want to deactivate this user?')) {
            const form = document.getElementById('deactivate-form');
            form.action = '/users/' + id + '/deactivate';
            form.submit();
        }
    }

    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            const form = document.getElementById('delete-form');
            form.action = '/users/' + id;
            form.submit();
        }
    }
</script>
@endsection