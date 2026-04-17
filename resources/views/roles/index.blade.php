@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Roles & Permissions</h2>
            <p class="text-gray-500 text-sm mt-1">Manage user roles and their access permissions</p>
        </div>
        <a href="{{ route('roles.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Role
        </a>
    </div>

    {{-- Filters --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <form method="GET" action="{{ route('roles.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name, code or description..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <select name="is_active" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>
            </div>
            
            <div>
                <a href="{{ route('roles.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Roles Table --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Permissions</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Users</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $role->name }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $role->code }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $role->description ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions->take(4) as $permission)
                                    <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                                @if($role->permissions->count() > 4)
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                        +{{ $role->permissions->count() - 4 }} more
                                    </span>
                                @endif
                                @if($role->permissions->isEmpty())
                                    <span class="text-xs text-gray-400 italic">No permissions</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            {{ $role->users_count }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($role->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                            @if($role->is_system_role)
                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('roles.show', $role->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                            <a href="{{ route('roles.edit', $role->id) }}" class="text-amber-600 hover:text-amber-800 mr-3">Edit</a>
                            @if(!$role->is_system_role)
                                @if($role->is_active)
                                    <button type="button" onclick="deactivateRole({{ $role->id }})" class="text-yellow-600 hover:text-yellow-800 mr-3">Deactivate</button>
                                @else
                                    <button type="button" onclick="activateRole({{ $role->id }})" class="text-green-600 hover:text-green-800 mr-3">Activate</button>
                                @endif
                                <button type="button" onclick="deleteRole({{ $role->id }})" class="text-red-600 hover:text-red-800">Delete</button>
                            @else
                                <span class="text-gray-400 text-sm">System Role</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No roles found.
                            <a href="{{ route('roles.create') }}" class="text-blue-600 hover:underline ml-2">Create your first role</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $roles->appends(request()->query())->links() }}
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
    function activateRole(id) {
        if (confirm('Are you sure you want to activate this role?')) {
            const form = document.getElementById('activate-form');
            form.action = '/roles/' + id + '/activate';
            form.submit();
        }
    }

    function deactivateRole(id) {
        if (confirm('Are you sure you want to deactivate this role?')) {
            const form = document.getElementById('deactivate-form');
            form.action = '/roles/' + id + '/deactivate';
            form.submit();
        }
    }

    function deleteRole(id) {
        if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
            const form = document.getElementById('delete-form');
            form.action = '/roles/' + id;
            form.submit();
        }
    }
</script>
@endsection