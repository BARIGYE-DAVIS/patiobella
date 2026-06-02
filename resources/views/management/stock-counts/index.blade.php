{{-- resources/views/management/stock-counts/index.blade.php --}}

@extends('layouts.management')

@section('title', 'Stock Counts')
@section('page-title', 'Stock Counts')

@section('content')
<div class="space-y-4">

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('management.stock-counts.index', ['type' => 'store']) }}"
               class="{{ $type === 'store' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-warehouse mr-2"></i> Store Counts
            </a>
            <a href="{{ route('management.stock-counts.index', ['type' => 'department']) }}"
               class="{{ $type === 'department' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-building mr-2"></i> Department Counts
            </a>
        </nav>
    </div>

    {{-- Filters with Live Search --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <input type="hidden" name="type" id="typeHidden" value="{{ $type }}">

            {{-- Live Search Field --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Live Search</label>
                <div class="relative">
                    <input type="text"
                           id="searchInput"
                           placeholder="Type to search count #, location, creator..."
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500 pl-9"
                           autocomplete="off">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                    <div id="searchLoader" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <i class="fas fa-spinner fa-spin text-orange-500 text-xs"></i>
                    </div>
                </div>
            </div>

            @if($type === 'department')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" id="departmentSelect" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="statusSelect" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" id="dateFrom" value="{{ request('date_from') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" id="dateTo" value="{{ request('date_to') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div class="flex items-end gap-2 md:col-span-1">
                <button type="button" id="resetBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition w-full">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Results Summary & Create Button --}}
    <div class="flex justify-between items-center hidden">
        <div class="text-sm text-gray-600" id="resultsSummary">
            Showing {{ $stockCounts->firstItem() ?? 0 }} to {{ $stockCounts->lastItem() ?? 0 }} of {{ $stockCounts->total() }} results
        </div>
        <a href="{{ route('management.stock-counts.create', ['type' => $type]) }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-plus mr-1"></i> New Stock Count
        </a>
    </div>

    {{-- Stock Counts Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Count #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Count Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Items</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Variance</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Created By</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="stockCountsTableBody" class="divide-y divide-gray-100">
                    @include('management.stock-counts.partials.table-rows', ['stockCounts' => $stockCounts, 'type' => $type])
                </tbody>
            </table>
        </div>

        <div id="paginationContainer" class="px-4 py-3 border-t border-gray-200">
            @if($stockCounts->hasPages())
                {{ $stockCounts->appends(request()->query())->links() }}
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let debounceTimer;
    let currentPage = 1;
    let isLoading = false;

    const searchInput = document.getElementById('searchInput');
    const departmentSelect = document.getElementById('departmentSelect');
    const statusSelect = document.getElementById('statusSelect');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const resetBtn = document.getElementById('resetBtn');
    const tableBody = document.getElementById('stockCountsTableBody');
    const paginationContainer = document.getElementById('paginationContainer');
    const resultsSummary = document.getElementById('resultsSummary');
    const searchLoader = document.getElementById('searchLoader');
    const typeHidden = document.getElementById('typeHidden');

    function fetchLiveResults() {
        if (isLoading) return;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const params = new URLSearchParams();
            params.append('type', typeHidden ? typeHidden.value : '{{ $type }}');
            params.append('page', currentPage);
            params.append('ajax', '1');

            if (searchInput && searchInput.value.trim()) {
                params.append('search', searchInput.value.trim());
            }
            if (departmentSelect && departmentSelect.value) {
                params.append('department_id', departmentSelect.value);
            }
            if (statusSelect && statusSelect.value) {
                params.append('status', statusSelect.value);
            }
            if (dateFrom && dateFrom.value) {
                params.append('date_from', dateFrom.value);
            }
            if (dateTo && dateTo.value) {
                params.append('date_to', dateTo.value);
            }

            if (searchLoader) searchLoader.classList.remove('hidden');
            isLoading = true;

            fetch(window.location.pathname + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    tableBody.innerHTML = data.html;
                }
                if (data.pagination !== undefined) {
                    paginationContainer.innerHTML = data.pagination;
                }
                if (data.summary) {
                    resultsSummary.innerHTML = `Showing ${data.summary.start} to ${data.summary.end} of ${data.summary.total} results`;
                }
                isLoading = false;
                if (searchLoader) searchLoader.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                isLoading = false;
                if (searchLoader) searchLoader.classList.add('hidden');
            });
        }, 300);
    }

    // Live search - as user types
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    // Live filter on select changes
    if (departmentSelect) {
        departmentSelect.addEventListener('change', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    if (dateFrom) {
        dateFrom.addEventListener('change', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    if (dateTo) {
        dateTo.addEventListener('change', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    // Reset all filters
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (departmentSelect) departmentSelect.value = '';
            if (statusSelect) statusSelect.value = '';
            if (dateFrom) dateFrom.value = '';
            if (dateTo) dateTo.value = '';
            currentPage = 1;
            fetchLiveResults();
        });
    }

    // Handle pagination clicks
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.getAttribute('href')) {
                e.preventDefault();
                const url = new URL(link.href);
                const page = url.searchParams.get('page');
                if (page) {
                    currentPage = parseInt(page);
                    fetchLiveResults();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        });
    }
});
</script>
@endsection
