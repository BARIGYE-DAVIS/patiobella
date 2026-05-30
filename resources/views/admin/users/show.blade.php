@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">User Details</h2>
            <p class="text-gray-500 text-sm mt-1">View user information and specific permissions</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
            <a href="{{ route('users.edit', $user->id) }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition duration-200">
                <i class="fas fa-edit mr-2"></i> Edit User
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column - User Information --}}
        <div class="space-y-6">
            {{-- Basic Information --}}
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Basic Information</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Full Name</div>
                        <div class="col-span-2 text-sm text-gray-800 font-medium">{{ $user->first_name }} {{ $user->last_name }}</div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Email Address</div>
                        <div class="col-span-2 text-sm text-gray-800">{{ $user->email }}</div>
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
                    @if($user->is_super_admin)
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-500">Super Admin</div>
                        <div class="col-span-2">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Yes - Full Access</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Digital Signature --}}
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-indigo-50 px-4 py-3 border-b border-indigo-200">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-signature mr-2 text-indigo-600"></i> Digital Signature
                    </h3>
                </div>
                <div class="p-4 text-center">
                    @if($user->signature_url)
                        <div class="inline-block p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <img src="{{ $user->signature_url }}?v={{ time() }}" alt="Signature" class="max-w-md max-h-24">
                        </div>
                        <div class="mt-3">
                            <p class="text-xs text-gray-500">
                                Last updated: {{ $user->signature_updated_at ? \Carbon\Carbon::parse($user->signature_updated_at)->format('d M Y H:i') : 'Never' }}
                            </p>
                        </div>
                    @else
                        <div class="text-gray-400 py-4">
                            <i class="fas fa-signature text-4xl mb-2 block"></i>
                            <p>No signature uploaded yet</p>
                            <p class="text-xs mt-1">User can add signature from edit page</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Roles Information (Display only, not used for permissions) --}}
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-green-50 px-4 py-3 border-b border-green-200">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-users mr-2 text-green-600"></i> Assigned Roles (Informational Only)
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Roles do NOT grant permissions. Permissions are assigned individually below.</p>
                </div>
                <div class="p-4">
                    @if($user->roles && $user->roles->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-3">
                            Total: {{ $user->roles->count() }} role(s) assigned (informational only)
                        </p>
                    @else
                        <p class="text-gray-500 text-sm">No roles assigned to this user.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- Specific Permissions (Extra Permissions only - NO role permissions) --}}
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-orange-50 px-4 py-3 border-b border-orange-200">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-key mr-2 text-orange-600"></i> Specific Permissions
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Permissions specifically assigned to this user </p>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    @php
                        // Get ONLY extra permissions (user-specific)
                        $extraPermissions = $user->userPermissions()
                            ->wherePivot('is_allowed', true)
                            ->get();
                        $groupedPermissions = $extraPermissions->groupBy('group');
                    @endphp

                    @if($extraPermissions->count() > 0)
                        @foreach($groupedPermissions as $groupName => $groupPermissions)
                            <div class="mb-4">
                                <div class="bg-gray-100 px-3 py-1 rounded mb-2">
                                    <span class="text-xs font-semibold text-gray-600 uppercase">{{ $groupName ?: 'General' }}</span>
                                </div>
                                <div class="grid grid-cols-1 gap-1">
                                    @foreach($groupPermissions as $permission)
                                        <div class="flex items-center text-sm text-gray-700 py-1">
                                            <i class="fas fa-check-circle text-green-500 text-xs mr-2"></i>
                                            {{ $permission->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <p class="text-xs text-gray-500 mt-2 border-t pt-2">
                            Total: {{ $extraPermissions->count() }} specific permission(s)
                        </p>
                    @else
                        <p class="text-gray-500 text-sm">No specific permissions assigned to this user.</p>
                    @endif
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-history mr-2"></i> Audit Information
                    </h3>
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
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Signature Updated By</label>
                        <p class="text-gray-700">
                            @if($user->signature_updated_by)
                                @php
                                    $signatureUpdater = \App\Models\User::find($user->signature_updated_by);
                                @endphp
                                {{ $signatureUpdater ? $signatureUpdater->first_name . ' ' . $signatureUpdater->last_name : 'Unknown' }}
                            @else
                                Never updated
                            @endif
                        </p>
                        @if($user->signature_updated_at)
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($user->signature_updated_at)->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                    @if($user->last_login_at)
                    <div>
                        <label class="text-xs text-gray-500 uppercase block">Last Login</label>
                        <p class="text-gray-700">{{ \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Danger Zone --}}
            @if(auth()->user()->is_super_admin && $user->id !== auth()->user()->id)
            <div class="border-2 border-red-200 rounded-lg overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                    <h3 class="font-semibold text-red-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Danger Zone
                    </h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-red-600 mb-4">Once deleted, this user can be restored by an administrator.</p>
                    <button type="button" onclick="deleteUser({{ $user->id }})"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                        <i class="fas fa-trash mr-2"></i> Delete User
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
