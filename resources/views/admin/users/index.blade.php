@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-3 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            Users Management
                        </h1>
                        <p class="text-gray-600 mt-1">Manage system users and their access</p>
                    </div>
                </div>
                <a href="{{ route('users.create') }}"
                   class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New User
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Users</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $users->total() }}</p>
                    </div>
                    <div class="bg-orange-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Active Users</p>
                        <p class="text-2xl font-bold text-green-600" id="activeCount">{{ $users->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Departments</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $departments->count() }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Roles</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $roles->count() }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Search Filters --}}
        <div class="mb-6 bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-3">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <h3 class="text-white font-semibold">Filter Users</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="liveSearch"
                               placeholder="Live search by name or email..."
                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                    </div>
                    <div class="md:col-span-3">
                        <select id="roleFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ strtolower($role->name) }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select id="departmentFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ strtolower($department->name) }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select id="statusFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="1">Active Only</option>
                            <option value="0">Inactive Only</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <button onclick="resetFilters()" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Results Summary --}}
        <div class="mb-4 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Showing <span id="resultStart">0</span> to <span id="resultEnd">0</span> of <span id="resultTotal">0</span> results
            </div>
            <div class="text-sm text-orange-600">
                <span id="resultCount"></span>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Can Create Users</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody" class="divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent transition-all duration-200 group"
                                data-user-name="{{ strtolower($user->first_name . ' ' . $user->last_name) }}"
                                data-user-email="{{ strtolower($user->email) }}"
                                data-user-department="{{ strtolower($user->department ? $user->department->name : '—') }}"
                                data-user-role="{{ strtolower($roleName = $roles->firstWhere('id', $user->role)->name ?? 'no role') }}"
                                data-user-active="{{ $user->is_active }}"
                                data-user-can-create="{{ $user->can_create_users }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-orange-100 to-amber-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </div>
                                        </div>
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
                                        $roleName = $roles->firstWhere('id', $user->role)->name ?? 'No Role';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-1"></span>
                                        {{ $roleName }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($user->can_create_users)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            No
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    <a href="{{ route('users.show', $user->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center text-amber-600 hover:text-amber-800 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    @if($user->is_active)
                                        <button type="button" onclick="deactivateUser({{ $user->id }})" class="inline-flex items-center text-yellow-600 hover:text-yellow-800 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Deactivate
                                        </button>
                                    @else
                                        <button type="button" onclick="activateUser({{ $user->id }})" class="inline-flex items-center text-green-600 hover:text-green-800 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Activate
                                        </button>
                                    @endif
                                    @if(auth()->user()->is_super_admin && $user->id !== auth()->user()->id)
                                        <button type="button" onclick="deleteUser({{ $user->id }})" class="inline-flex items-center text-red-600 hover:text-red-800 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    No users found.
                                    <a href="{{ route('users.create') }}" class="text-orange-600 hover:underline ml-2">Create your first user</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $users->appends(request()->query())->links() }}
        </div>
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
    // Live search functionality - filters table rows without page reload
    const searchInput = document.getElementById('liveSearch');
    const roleFilter = document.getElementById('roleFilter');
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#usersTableBody tr');
    const resultStart = document.getElementById('resultStart');
    const resultEnd = document.getElementById('resultEnd');
    const resultTotal = document.getElementById('resultTotal');
    const resultCount = document.getElementById('resultCount');
    const activeCountSpan = document.getElementById('activeCount');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const departmentValue = departmentFilter.value.toLowerCase();
        const statusValue = statusFilter.value;

        let visibleCount = 0;
        let activeVisibleCount = 0;

        tableRows.forEach(row => {
            // Skip if it's the "no results" row
            if (row.querySelector('td[colspan="7"]')) {
                return;
            }

            const name = row.getAttribute('data-user-name') || '';
            const email = row.getAttribute('data-user-email') || '';
            const department = row.getAttribute('data-user-department') || '';
            const role = row.getAttribute('data-user-role') || '';
            const isActive = row.getAttribute('data-user-active') === '1';

            let show = true;

            // Search filter (search in name and email)
            if (searchTerm && !name.includes(searchTerm) && !email.includes(searchTerm)) {
                show = false;
            }

            // Role filter
            if (show && roleValue && role !== roleValue) {
                show = false;
            }

            // Department filter
            if (show && departmentValue && department !== departmentValue) {
                show = false;
            }

            // Status filter
            if (show && statusValue !== '') {
                const filterActive = statusValue === '1';
                if (isActive !== filterActive) {
                    show = false;
                }
            }

            row.style.display = show ? '' : 'none';

            if (show) {
                visibleCount++;
                if (isActive) activeVisibleCount++;
            }
        });

        // Update stats
        activeCountSpan.textContent = activeVisibleCount;

        // Update result summary
        resultTotal.textContent = visibleCount;
        resultCount.textContent = `Found ${visibleCount} user${visibleCount !== 1 ? 's' : ''}`;

        // Update showing range
        const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan="7"]'));
        if (visibleRows.length > 0) {
            resultStart.textContent = 1;
            resultEnd.textContent = visibleRows.length;
        } else {
            resultStart.textContent = 0;
            resultEnd.textContent = 0;
        }

        // Show/hide no results message
        const existingNoResults = document.querySelector('#usersTableBody tr td[colspan="7"]')?.parentElement;

        if (visibleCount === 0 && !existingNoResults) {
            const tbody = document.getElementById('usersTableBody');
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    No users match your filters.
                    <button onclick="resetFilters()" class="text-orange-600 hover:underline ml-2">Reset filters</button>
                </td>
            `;
            tbody.appendChild(emptyRow);
        } else if (visibleCount > 0 && existingNoResults) {
            existingNoResults.remove();
        }
    }

    function resetFilters() {
        searchInput.value = '';
        roleFilter.value = '';
        departmentFilter.value = '';
        statusFilter.value = '';
        filterTable();
    }

    // Add event listeners for live filtering
    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    departmentFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);

    // Initialize result summary on page load
    function initResultSummary() {
        const totalRows = tableRows.length;
        const nonEmptyRows = Array.from(tableRows).filter(row => !row.querySelector('td[colspan="7"]'));
        const totalCount = nonEmptyRows.length;
        resultTotal.textContent = totalCount;
        resultCount.textContent = `Found ${totalCount} user${totalCount !== 1 ? 's' : ''}`;
        if (totalCount > 0) {
            resultStart.textContent = 1;
            resultEnd.textContent = totalCount;
        }
    }

    initResultSummary();

    function activateUser(id) {
        if (confirm('✓ Are you sure you want to activate this user?')) {
            const form = document.getElementById('activate-form');
            form.action = '/users/' + id + '/activate';
            form.submit();
        }
    }

    function deactivateUser(id) {
        if (confirm('⚠️ Are you sure you want to deactivate this user?')) {
            const form = document.getElementById('deactivate-form');
            form.action = '/users/' + id + '/deactivate';
            form.submit();
        }
    }

    function deleteUser(id) {
        if (confirm('🗑️ Warning: Are you sure you want to delete this user?\n\nThis action cannot be undone and will permanently remove the user from the system.')) {
            const form = document.getElementById('delete-form');
            form.action = '/users/' + id;
            form.submit();
        }
    }
</script>
@endsection
