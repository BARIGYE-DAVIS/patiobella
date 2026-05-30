@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-2.5 shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Users Management</h1>
                <p class="text-sm text-gray-500">Manage system users and their access</p>
            </div>
        </div>
        <a href="{{ route('users.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all shadow-sm font-medium text-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New User
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 border-l-4 border-l-orange-500">
            <p class="text-xs text-gray-500 mb-1">Total Users</p>
            <p class="text-2xl font-bold text-gray-800">{{ $users->total() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 border-l-4 border-l-green-500">
            <p class="text-xs text-gray-500 mb-1">Active Users</p>
            <p class="text-2xl font-bold text-green-600" id="activeCount">{{ $users->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 border-l-4 border-l-purple-500">
            <p class="text-xs text-gray-500 mb-1">Departments</p>
            <p class="text-2xl font-bold text-purple-600">{{ $departments->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 border-l-4 border-l-blue-500">
            <p class="text-xs text-gray-500 mb-1">Roles</p>
            <p class="text-2xl font-bold text-blue-600">{{ $roles->count() }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-5 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            <h3 class="text-white font-semibold text-sm">Filter Users</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="liveSearch"
                        placeholder="Search by name or email..."
                        class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition-all">
                </div>
                <select id="roleFilter" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition-all bg-white">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ strtolower($role->name) }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <select id="departmentFilter" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition-all bg-white">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ strtolower($department->name) }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <select id="statusFilter" class="flex-1 px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition-all bg-white">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <button onclick="resetFilters()"
                        class="px-3 py-2.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium whitespace-nowrap">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Results summary --}}
    <div class="flex justify-between items-center text-sm text-gray-500">
        <span>Showing <span id="resultStart">0</span>–<span id="resultEnd">0</span> of <span id="resultTotal">0</span></span>
        <span id="resultCount" class="text-orange-600 font-medium"></span>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-0 table-fixed">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-[22%]">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-[22%]">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-[14%]">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-[12%]">Role</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-[10%]">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-[8%]">Can Create</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-[12%]">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody" class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        @php $roleName = $roles->firstWhere('id', $user->role)->name ?? 'No Role'; @endphp
                        <tr class="hover:bg-orange-50/50 transition-colors"
                            data-user-name="{{ strtolower($user->first_name . ' ' . $user->last_name) }}"
                            data-user-email="{{ strtolower($user->email) }}"
                            data-user-department="{{ strtolower($user->department?->name ?? '') }}"
                            data-user-role="{{ strtolower($roleName) }}"
                            data-user-active="{{ $user->is_active ? '1' : '0' }}">

                            {{-- Name --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</span>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600 truncate block">{{ $user->email }}</span>
                            </td>

                            {{-- Department --}}
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600 truncate block">{{ $user->department?->name ?? '—' }}</span>
                            </td>

                            {{-- Role --}}
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 max-w-full truncate">
                                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full flex-shrink-0"></span>
                                    {{ $roleName }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 text-center">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Can Create --}}
                            <td class="px-4 py-3 text-center">
                                @if($user->can_create_users)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Yes
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">No</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('users.show', $user->id) }}"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($user->is_active)
                                        <button type="button" onclick="deactivateUser({{ $user->id }})"
                                            class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                            title="Deactivate">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button" onclick="activateUser({{ $user->id }})"
                                            class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Activate">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                    @if(auth()->user()->is_super_admin && $user->id !== auth()->user()->id)
                                        <button type="button" onclick="deleteUser({{ $user->id }})"
                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm">No users found.</p>
                                <a href="{{ route('users.create') }}" class="text-orange-600 hover:underline text-sm mt-1 inline-block">Create your first user</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div>{{ $users->appends(request()->query())->links() }}</div>

</div>

{{-- Hidden forms --}}
<form id="activate-form"   action="" method="POST" class="hidden">@csrf @method('PATCH')</form>
<form id="deactivate-form" action="" method="POST" class="hidden">@csrf @method('PATCH')</form>
<form id="delete-form"     action="" method="POST" class="hidden">@csrf @method('DELETE')</form>

@push('scripts')
<script>
(function () {
    const searchInput      = document.getElementById('liveSearch');
    const roleFilter       = document.getElementById('roleFilter');
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter     = document.getElementById('statusFilter');
    const tbody            = document.getElementById('usersTableBody');
    const activeCountSpan  = document.getElementById('activeCount');
    const resultStart      = document.getElementById('resultStart');
    const resultEnd        = document.getElementById('resultEnd');
    const resultTotal      = document.getElementById('resultTotal');
    const resultCount      = document.getElementById('resultCount');

    function getRows() {
        return Array.from(tbody.querySelectorAll('tr[data-user-name]'));
    }

    function filterTable() {
        const search     = searchInput.value.toLowerCase();
        const role       = roleFilter.value.toLowerCase();
        const dept       = departmentFilter.value.toLowerCase();
        const status     = statusFilter.value;

        let visible = 0, active = 0;

        getRows().forEach(row => {
            const name   = row.dataset.userName   || '';
            const email  = row.dataset.userEmail  || '';
            const deptV  = row.dataset.userDepartment || '';
            const roleV  = row.dataset.userRole   || '';
            const isActive = row.dataset.userActive === '1';

            let show = true;
            if (search && !name.includes(search) && !email.includes(search)) show = false;
            if (show && role   && roleV !== role)  show = false;
            if (show && dept   && deptV !== dept)  show = false;
            if (show && status !== '' && (status === '1') !== isActive) show = false;

            row.style.display = show ? '' : 'none';
            if (show) { visible++; if (isActive) active++; }
        });

        activeCountSpan.textContent = active;
        resultTotal.textContent     = visible;
        resultStart.textContent     = visible ? 1 : 0;
        resultEnd.textContent       = visible;
        resultCount.textContent     = `${visible} user${visible !== 1 ? 's' : ''} found`;

        // Dynamic empty row
        const emptyRow = tbody.querySelector('.js-empty-row');
        if (visible === 0 && !emptyRow) {
            const tr = document.createElement('tr');
            tr.className = 'js-empty-row';
            tr.innerHTML = `<td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                No users match your filters.
                <button onclick="window.__resetFilters()" class="text-orange-600 hover:underline ml-1">Reset filters</button>
            </td>`;
            tbody.appendChild(tr);
        } else if (visible > 0 && emptyRow) {
            emptyRow.remove();
        }
    }

    window.__resetFilters = function () {
        searchInput.value = roleFilter.value = departmentFilter.value = statusFilter.value = '';
        filterTable();
    };

    // expose for inline onclick in the header button
    window.resetFilters = window.__resetFilters;

    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    departmentFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);

    // Init summary
    const total = getRows().length;
    resultTotal.textContent = total;
    resultStart.textContent = total ? 1 : 0;
    resultEnd.textContent   = total;
    resultCount.textContent = `${total} user${total !== 1 ? 's' : ''} found`;

    // Actions
    window.activateUser = function (id) {
        if (!confirm('Activate this user?')) return;
        const f = document.getElementById('activate-form');
        f.action = '/users/' + id + '/activate';
        f.submit();
    };

    window.deactivateUser = function (id) {
        if (!confirm('Deactivate this user?')) return;
        const f = document.getElementById('deactivate-form');
        f.action = '/users/' + id + '/deactivate';
        f.submit();
    };

    window.deleteUser = function (id) {
        if (!confirm('Permanently delete this user? This cannot be undone.')) return;
        const f = document.getElementById('delete-form');
        f.action = '/users/' + id;
        f.submit();
    };
})();
</script>
@endpush
@endsection
