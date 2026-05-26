@extends('layouts.app')

@section('title', 'Permission Details')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Permission Details</h2>
            <p class="text-gray-500 text-sm mt-1">View permission information</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-800 transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Permissions
            </a>
            <a href="{{ route('permissions.edit', $permission->id) }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Permission
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column - Permission Information --}}
        <div class="space-y-6">
            {{-- Basic Information --}}
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Permission Information</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Permission Name</div>
                        <div class="col-span-2 text-sm text-gray-800 font-medium">{{ $permission->name }}</div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Permission Code</div>
                        <div class="col-span-2">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $permission->code }}</code>
                        </div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Group</div>
                        <div class="col-span-2">
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">{{ ucfirst($permission->group) }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Status</div>
                        <div class="col-span-2">
                            @if($permission->is_active)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Description</div>
                        <div class="col-span-2 text-sm text-gray-800">{{ $permission->description ?? 'No description provided.' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Audit Information --}}
        <div class="space-y-6">
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Audit Information</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Created At</label>
                        <p class="text-gray-700">{{ $permission->created_at ? $permission->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Last Updated</label>
                        <p class="text-gray-700">{{ $permission->updated_at ? $permission->updated_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Sort Order</label>
                        <p class="text-gray-700">{{ $permission->sort_order ?? '0' }}</p>
                    </div>
                </div>
            </div>

            {{-- Roles using this permission --}}
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
                    <h3 class="font-semibold text-gray-800">
                        <svg class="w-5 h-5 inline mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Roles with this Permission
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Roles that have this permission assigned</p>
                </div>
                <div class="p-4">
                    @php
                        $roles = $permission->roles()->get();
                    @endphp
                    @if($roles->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($roles as $role)
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-3">Total: {{ $roles->count() }} role(s)</p>
                    @else
                        <p class="text-gray-500 text-sm">No roles have this permission assigned.</p>
                    @endif
                </div>
            </div>

            {{-- Users with this as extra permission --}}
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-green-50 px-4 py-3 border-b border-green-200">
                    <h3 class="font-semibold text-gray-800">
                        <svg class="w-5 h-5 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Users with Extra Permission
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Users who have this as an extra permission (added individually)</p>
                </div>
                <div class="p-4">
                    @php
                        $users = $permission->users()->wherePivot('is_allowed', true)->get();
                    @endphp
                    @if($users->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($users as $user)
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-3">Total: {{ $users->count() }} user(s)</p>
                    @else
                        <p class="text-gray-500 text-sm">No users have this as an extra permission.</p>
                    @endif
                </div>
            </div>

            {{-- Danger Zone - Delete --}}
            @if($permission->roles()->count() == 0 && $permission->users()->count() == 0)
            <div class="border-2 border-red-200 rounded-lg overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                    <h3 class="font-semibold text-red-800">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Danger Zone
                    </h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-red-600 mb-4">Once deleted, this permission cannot be recovered.</p>
                    <button type="button" onclick="deletePermission({{ $permission->id }})"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Permission
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function deletePermission(id) {
        if (confirm('Are you sure you want to delete this permission? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection
