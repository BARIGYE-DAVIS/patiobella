@extends('layouts.app')

@section('title', 'Role Details: ' . $role->name)

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Role Details</h2>
            <p class="text-gray-500 text-sm mt-1">View role information and permissions</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-800 px-3 py-2">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Roles
            </a>
            <a href="{{ route('roles.edit', $role->id) }}" class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Role
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - Role Information --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Basic Information --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Basic Information</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Role Name</label>
                        <p class="text-gray-800 font-medium">{{ $role->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Role Code</label>
                        <p class="font-mono text-sm text-indigo-600 bg-indigo-50 px-2 py-1 rounded inline-block">{{ $role->code }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Description</label>
                        <p class="text-gray-600">{{ $role->description ?? 'No description provided.' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Status</label>
                        @if($role->is_active)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                        @if($role->is_system_role)
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">System Role</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Users with this Role --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Users with this Role</h3>
                </div>
                <div class="p-4">
                    @php $usersCount = $role->users()->count(); @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ $usersCount }}</p>
                    <p class="text-sm text-gray-500">users assigned to this role</p>
                    @if($usersCount > 0)
                        <a href="{{ route('users.index', ['role_id' => $role->id]) }}" class="text-blue-600 text-sm hover:underline mt-2 inline-block">
                            View all users →
                        </a>
                    @endif
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Audit Information</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Created By</label>
                        <p class="text-gray-700">{{ $role->creator ? $role->creator->first_name . ' ' . $role->creator->last_name : 'System' }}</p>
                        <p class="text-xs text-gray-400">{{ $role->created_at ? $role->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Last Updated By</label>
                        <p class="text-gray-700">{{ $role->updater ? $role->updater->first_name . ' ' . $role->updater->last_name : 'Never updated' }}</p>
                        @if($role->updated_at)
                            <p class="text-xs text-gray-400">{{ $role->updated_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Permissions --}}
        <div class="lg:col-span-2">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Assigned Permissions</h3>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $role->permissions->count() }} permissions</p>
                </div>
                <div class="p-4">
                    @if($role->permissions->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <p>No permissions assigned to this role.</p>
                            <a href="{{ route('roles.edit', $role->id) }}" class="text-blue-600 hover:underline mt-2 inline-block">
                                Assign permissions now →
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($permissionsGrouped as $group => $groupPermissions)
                                <div>
                                    <h4 class="font-semibold text-gray-700 uppercase text-sm border-b border-gray-200 pb-2 mb-3">
                                        {{ ucfirst(str_replace('_', ' ', $group)) }}
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        @foreach($groupPermissions as $permission)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Danger Zone - Delete (Only for non-system roles with no users) --}}
    @if(!$role->is_system_role && $role->users()->count() == 0)
    <div class="mt-6 border-t border-red-200 pt-6">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-red-800">Delete Role</h3>
                    <p class="text-sm text-red-600">This action cannot be undone. The role will be permanently deleted.</p>
                </div>
                <button type="button" onclick="deleteRole({{ $role->id }})" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Delete Role
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<form id="delete-form" action="{{ route('roles.destroy', $role->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function deleteRole(id) {
        if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection