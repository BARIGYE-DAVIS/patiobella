@extends('layouts.director')
@section('title', 'Local Purchase Orders')
@section('page-title', 'Local Purchase Orders')

@section('content')
<style>
    .status-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-pending {
        background: #fef3c7;
        color: #d97706;
    }
    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }
    .status-rejected {
        background: #fee2e2;
        color: #dc2626;
    }
    .search-loading {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        border: 2px solid #e5e7eb;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin {
        to { transform: translateY(-50%) rotate(360deg); }
    }
    .filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .filter-input {
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        background: white;
    }
    .filter-input:focus {
        outline: none;
        border-color: #3b82f6;
        ring: 2px solid #93c5fd;
    }
    .btn-filter {
        background-color: #3b82f6;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        border: none;
    }
    .btn-filter:hover {
        background-color: #2563eb;
    }
    .btn-reset {
        background-color: #6b7280;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        border: none;
    }
    .btn-reset:hover {
        background-color: #4b5563;
    }
    .search-container {
        position: relative;
        flex: 1;
        min-width: 250px;
    }
    .search-container input {
        width: 100%;
        padding: 8px 35px 8px 12px;
    }
    .table-container {
        overflow-x: auto;
        min-height: 400px;
    }
    .pagination-container {
        margin-top: 16px;
    }
</style>

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Local Purchase Orders</h2>
            <p class="text-xs text-gray-500 mt-0.5">Review, approve, or reject LPOs submitted for director sign-off</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium px-2.5 py-1 rounded-full border bg-gray-50 text-gray-600 border-gray-200" id="totalCountDisplay">
                Loading...
            </span>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="filter-group">
            {{-- Search Box with Live Search --}}
            <div class="search-container">
                <input type="text"
                       id="searchInput"
                       placeholder="Search by LPO #, Vendor, Requisition..."
                       class="filter-input w-full"
                       autocomplete="off">
                <div id="searchLoader" class="search-loading" style="display: none;"></div>
            </div>

            {{-- Status Filter --}}
            <select id="statusFilter" class="filter-input" style="min-width: 130px;">
                <option value="all">All Status</option>
                <option value="pending_director">Pending</option>
                <option value="director_approved">Approved</option>
                <option value="director_rejected">Rejected</option>
            </select>

            {{-- Type Filter --}}
<select id="typeFilter" class="filter-input" style="min-width: 120px;">
    <option value="all">All Types</option>
    <option value="normal">Normal</option>
    <option value="emergency">Emergency</option>
</select>
            {{-- Date From --}}
            <input type="date" id="dateFrom" class="filter-input" style="min-width: 140px;" placeholder="Date From">

            {{-- Date To --}}
            <input type="date" id="dateTo" class="filter-input" style="min-width: 140px;" placeholder="Date To">

            {{-- Buttons --}}
            <button id="applyFiltersBtn" class="btn-filter">
                <i class="fas fa-search mr-1"></i> Apply
            </button>
            <button id="resetFiltersBtn" class="btn-reset">
                <i class="fas fa-undo-alt mr-1"></i> Reset
            </button>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="table-container">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">LPO #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Requisition #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Vendor</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">LPO Date</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-500">Total Amount</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody id="lpoTableBody">
                    @include('director.lpos.partials.table_rows', ['lpos' => $lpos])
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div id="paginationContainer" class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between flex-wrap gap-2">
            @if($lpos->hasPages())
                <p class="text-xs text-gray-500">
                    Showing {{ $lpos->firstItem() }}–{{ $lpos->lastItem() }} of {{ $lpos->total() }} LPOs
                </p>
                <div>{{ $lpos->appends(request()->query())->links() }}</div>
            @else
                <p class="text-xs text-gray-500">
                    Showing {{ $lpos->firstItem() }}–{{ $lpos->lastItem() }} of {{ $lpos->total() }} LPOs
                </p>
            @endif
        </div>
    </div>
</div>

<script>
    let searchTimeout;
    let isAjaxLoading = false;

    function fetchLpos() {
        if (isAjaxLoading) return;

        isAjaxLoading = true;
        document.getElementById('searchLoader').style.display = 'block';

        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const type = document.getElementById('typeFilter').value;
// then add to URL: url.searchParams.set('type', type);
        const url = new URL('{{ route("director.lpos.index") }}', window.location.origin);
        url.searchParams.set('search', search);
        url.searchParams.set('status', status);
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);
        url.searchParams.set('type', type);
        url.searchParams.set('ajax', '1');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('lpoTableBody').innerHTML = data.html;
                document.getElementById('paginationContainer').innerHTML = data.pagination;
                document.getElementById('totalCountDisplay').innerHTML = `${data.total} total`;
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            isAjaxLoading = false;
            document.getElementById('searchLoader').style.display = 'none';
        });
    }

    // Live search with debounce
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchLpos();
        }, 500);
    });

    // Apply filters button
    document.getElementById('applyFiltersBtn').addEventListener('click', function() {
        fetchLpos();
    });

    // Reset filters button
    document.getElementById('resetFiltersBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = 'all';
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        fetchLpos();
    });

    // Enter key in search triggers search immediately
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            fetchLpos();
        }
    });

    // Initial load - update total count
    document.getElementById('totalCountDisplay').innerHTML = '{{ $lpos->total() }} total';
</script>
@endsection
