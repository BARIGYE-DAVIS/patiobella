@extends('layouts.management')

@section('title', 'Restaurant Tables')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-3 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                            Restaurant Tables
                        </h1>
                        <p class="text-gray-600 mt-1">Manage restaurant tables and their status</p>
                    </div>
                </div>
                <a href="{{ route('management.tables.create') }}"
                   class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Table
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Tables</p>
                        <p class="text-2xl font-bold text-gray-800" id="totalTables">{{ $tables->total() }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Available</p>
                        <p class="text-2xl font-bold text-green-600" id="availableCount">0</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Reserved</p>
                        <p class="text-2xl font-bold text-yellow-600" id="reservedCount">0</p>
                    </div>
                    <div class="bg-yellow-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Occupied</p>
                        <p class="text-2xl font-bold text-orange-600" id="occupiedCount">0</p>
                    </div>
                    <div class="bg-orange-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Awaiting Payment</p>
                        <p class="text-2xl font-bold text-red-600" id="paymentCount">0</p>
                    </div>
                    <div class="bg-red-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Search Filters --}}
        <div class="mb-6 bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <h3 class="text-white font-semibold">Filter Tables</h3>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="gridViewBtn" onclick="setView('grid')"
                                class="px-3 py-1 rounded-lg bg-white/20 text-white hover:bg-white/30 transition-all">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Grid
                        </button>
                        <button id="listViewBtn" onclick="setView('list')"
                                class="px-3 py-1 rounded-lg bg-white/20 text-white hover:bg-white/30 transition-all">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            List
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="liveSearch"
                               placeholder="Live search by table number..."
                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                    </div>
                    <div class="md:col-span-2">
                        <select id="locationFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select id="capacityFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                            <option value="">All Capacities</option>
                            @foreach($capacities as $capacity)
                                <option value="{{ $capacity }}">{{ $capacity }} Seats</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <select id="statusFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100 transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="occupied">Occupied (Bill Not Printed)</option>
                            <option value="payment">Awaiting Payment (Bill Printed)</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button onclick="resetFilters()" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                            Reset Filters
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

        {{-- Tables Grid/List View Container --}}
        <div id="tablesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        </div>

        {{-- List View Container --}}
        <div id="listViewContainer" class="hidden bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Table #</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Location</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Capacity</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Size</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>

        {{-- No Results --}}
        <div id="noResults" class="hidden text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M6 14h12m-6-4v8m-8 0h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500">No tables match your filters.</p>
            <button onclick="resetFilters()" class="text-orange-600 hover:underline mt-2 inline-block">Reset filters</button>
        </div>
    </div>
</div>

<script>
    let currentView = localStorage.getItem('tableView') || 'grid';
    let allTables = @json($tables->items());
    let ordersData = @json($orders ?? []);

    function initView() {
        if (currentView === 'list') {
            document.getElementById('gridViewBtn').classList.remove('bg-white/30');
            document.getElementById('listViewBtn').classList.add('bg-white/30');
            showListView();
        } else {
            document.getElementById('gridViewBtn').classList.add('bg-white/30');
            document.getElementById('listViewBtn').classList.remove('bg-white/30');
            showGridView();
        }
    }

    function setView(view) {
        currentView = view;
        localStorage.setItem('tableView', view);

        if (view === 'list') {
            document.getElementById('gridViewBtn').classList.remove('bg-white/30');
            document.getElementById('listViewBtn').classList.add('bg-white/30');
            showListView();
        } else {
            document.getElementById('gridViewBtn').classList.add('bg-white/30');
            document.getElementById('listViewBtn').classList.remove('bg-white/30');
            showGridView();
        }

        filterTables();
    }

    function showGridView() {
        document.getElementById('tablesContainer').classList.remove('hidden');
        document.getElementById('listViewContainer').classList.add('hidden');
    }

    function showListView() {
        document.getElementById('tablesContainer').classList.add('hidden');
        document.getElementById('listViewContainer').classList.remove('hidden');
    }

    function getTableStatus(table) {
        if (!table.is_active) {
            return { status: 'inactive', label: 'Inactive', color: 'gray', badgeClass: 'bg-gray-100 text-gray-600', icon: '⭕' };
        }
        if (table.is_occupied) {
            const order = ordersData[table.id];
            if (order && order.is_printed == 1 && order.payment_status === 'unpaid') {
                return { status: 'payment', label: 'Awaiting Payment', color: 'red', badgeClass: 'bg-red-100 text-red-800', icon: '💰' };
            }
            return { status: 'occupied', label: 'Occupied', color: 'orange', badgeClass: 'bg-orange-100 text-orange-800', icon: '🪑' };
        }
        if (table.is_reserved) {
            return { status: 'reserved', label: 'Reserved', color: 'yellow', badgeClass: 'bg-yellow-100 text-yellow-800', icon: '🕒' };
        }
        return { status: 'available', label: 'Available', color: 'green', badgeClass: 'bg-green-100 text-green-800', icon: '✅' };
    }

    function isEditLocked(status) {
        return ['occupied', 'payment'].includes(status.status);
    }

    function filterTables() {
        const searchTerm = document.getElementById('liveSearch').value.toLowerCase();
        const locationValue = document.getElementById('locationFilter').value;
        const capacityValue = document.getElementById('capacityFilter').value;
        const statusValue = document.getElementById('statusFilter').value;

        let filteredTables = allTables.filter(table => {
            let show = true;
            const status = getTableStatus(table);

            if (searchTerm && !table.table_number.toLowerCase().includes(searchTerm)) {
                show = false;
            }
            if (show && locationValue && table.location !== locationValue) {
                show = false;
            }
            if (show && capacityValue && table.capacity != capacityValue) {
                show = false;
            }
            if (show && statusValue && status.status !== statusValue) {
                show = false;
            }
            return show;
        });

        updateStats(filteredTables);
        updateResultSummary(filteredTables);

        if (currentView === 'list') {
            renderListView(filteredTables);
        } else {
            renderGridView(filteredTables);
        }
    }

    function updateStats(tables) {
        let available = 0, reserved = 0, occupied = 0, payment = 0;

        tables.forEach(table => {
            const status = getTableStatus(table);
            if (status.status === 'available') available++;
            else if (status.status === 'reserved') reserved++;
            else if (status.status === 'occupied') occupied++;
            else if (status.status === 'payment') payment++;
        });

        document.getElementById('availableCount').textContent = available;
        document.getElementById('reservedCount').textContent = reserved;
        document.getElementById('occupiedCount').textContent = occupied;
        document.getElementById('paymentCount').textContent = payment;
    }

    function updateResultSummary(tables) {
        const total = tables.length;
        document.getElementById('resultTotal').textContent = total;
        document.getElementById('resultCount').textContent = `Found ${total} table${total !== 1 ? 's' : ''}`;

        if (total > 0) {
            document.getElementById('resultStart').textContent = 1;
            document.getElementById('resultEnd').textContent = total;
            document.getElementById('noResults').classList.add('hidden');
        } else {
            document.getElementById('resultStart').textContent = 0;
            document.getElementById('resultEnd').textContent = 0;
            document.getElementById('noResults').classList.remove('hidden');
        }
    }

    function renderGridView(tables) {
        const container = document.getElementById('tablesContainer');

        if (tables.length === 0) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = tables.map(table => {
            const status = getTableStatus(table);
            const locked = isEditLocked(status);

            const borderColor = {
                green: 'border-green-300',
                red: 'border-red-300',
                orange: 'border-orange-300',
                yellow: 'border-yellow-300',
                gray: 'border-gray-300',
            }[status.color] ?? 'border-gray-300';

            const editBtn = locked
                ? `<span
                       class="flex-1 text-center bg-gray-100 text-gray-400 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed select-none flex items-center justify-center gap-1"
                       title="Cannot edit while table is ${status.label}">
                       <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                       </svg>
                       Locked
                   </span>`
                : `<a href="/management/tables/${table.id}/edit"
                       class="flex-1 text-center bg-orange-600 text-white px-3 py-2 rounded-lg hover:bg-orange-700 transition text-sm font-medium">
                       Edit
                   </a>`;

            return `
            <div class="border-2 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-xl transform hover:-translate-y-1 bg-white ${borderColor}">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Table ${escapeHtml(table.table_number)}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                ${escapeHtml(table.location || 'No location set')}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${status.badgeClass}">
                                ${status.icon} ${status.label}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="text-gray-700">Capacity: <strong>${table.capacity} ${table.capacity === 1 ? 'seat' : 'seats'}</strong></span>
                        </div>
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <span class="text-gray-700">Size: <strong>${getSizeLabel(table.size)}</strong></span>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4 pt-3 border-t border-gray-200">
                        <a href="/management/tables/${table.id}"
                           class="flex-1 text-center bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                            View Details
                        </a>
                        ${editBtn}
                    </div>
                </div>
            </div>
        `}).join('');
    }

    function renderListView(tables) {
        const tbody = document.getElementById('listViewBody');

        if (tables.length === 0) {
            tbody.innerHTML = '';
            return;
        }

        tbody.innerHTML = tables.map(table => {
            const status = getTableStatus(table);
            const locked = isEditLocked(status);

            const editAction = locked
                ? `<span
                       class="inline-flex items-center text-gray-400 cursor-not-allowed select-none"
                       title="Cannot edit while table is ${status.label}">
                       <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                       </svg>
                       Locked
                   </span>`
                : `<a href="/management/tables/${table.id}/edit"
                       class="inline-flex items-center text-amber-600 hover:text-amber-800 transition-colors">
                       <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                       </svg>
                       Edit
                   </a>`;

            return `
            <tr class="hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent transition-all duration-200">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900 text-lg">Table ${escapeHtml(table.table_number)}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        ${escapeHtml(table.location || '—')}
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        🪑 ${table.capacity} ${table.capacity === 1 ? 'seat' : 'seats'}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        ${getSizeLabel(table.size)}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${status.badgeClass}">
                        ${status.icon} ${status.label}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="/management/tables/${table.id}"
                       class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View
                    </a>
                    ${editAction}
                </td>
            </tr>
        `}).join('');
    }

    function getSizeLabel(size) {
        const sizeMap = {
            'Small': '📏 Small',
            'Medium': '📐 Medium',
            'Large': '📏 Large',
            'Extra Large': '📐 Extra Large',
            'VIP': '⭐ VIP'
        };
        return sizeMap[size] || size || '—';
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function resetFilters() {
        document.getElementById('liveSearch').value = '';
        document.getElementById('locationFilter').value = '';
        document.getElementById('capacityFilter').value = '';
        document.getElementById('statusFilter').value = '';
        filterTables();
    }

    document.getElementById('liveSearch').addEventListener('input', filterTables);
    document.getElementById('locationFilter').addEventListener('change', filterTables);
    document.getElementById('capacityFilter').addEventListener('change', filterTables);
    document.getElementById('statusFilter').addEventListener('change', filterTables);

    initView();
    filterTables();
</script>
@endsection
