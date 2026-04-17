@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">User Details</h2>
            <p class="text-gray-500 text-sm mt-1">View user information</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
            <a href="{{ route('users.edit', $user->id) }}" class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition">
                <i class="fas fa-edit mr-2"></i> Edit User
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - User Information --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic Information --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Basic Information</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Full Name</div>
                        <div class="col-span-2 text-sm text-gray-800">{{ $user->first_name }} {{ $user->last_name }}</div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Email Address</div>
                        <div class="col-span-2 text-sm text-gray-800">{{ $user->email }}</div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Role</div>
                        <div class="col-span-2">
                            @php
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
                        </div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Department</div>
                        <div class="col-span-2 text-sm text-gray-800">{{ $user->department ? $user->department->name : '—' }}</div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Status</div>
                        <div class="col-span-2">
                            @if($user->is_active)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Can Create Users</div>
                        <div class="col-span-2">
                            @if($user->can_create_users)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Yes</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">No</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Audit Information --}}
        <div class="space-y-6">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Audit Information</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Created By</label>
                        <p class="text-gray-700">{{ $user->creator ? $user->creator->first_name . ' ' . $user->creator->last_name : 'System' }}</p>
                        <p class="text-xs text-gray-400">{{ $user->created_at ? $user->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Last Updated By</label>
                        <p class="text-gray-700">{{ $user->updater ? $user->updater->first_name . ' ' . $user->updater->last_name : 'Never updated' }}</p>
                        @if($user->updated_at)
                            <p class="text-xs text-gray-400">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                    @if($user->last_login_at)
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Last Login</label>
                        <p class="text-gray-700">{{ $user->last_login_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Danger Zone --}}
            @if(auth()->user()->is_super_admin && $user->id !== auth()->user()->id)
            <div class="border border-red-200 rounded-lg overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                    <h3 class="font-semibold text-red-800">Danger Zone</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-red-600 mb-4">Once deleted, this user can be restored by an administrator.</p>
                    <button type="button" onclick="deleteUser({{ $user->id }})" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        Delete User
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
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