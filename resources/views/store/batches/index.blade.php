{{-- resources/views/store/batches/index.blade.php --}}

@extends('layouts.store')

@section('title', 'Stock Batches')
@section('page-title', 'Stock Batches')

@section('content')
<div class="space-y-4">

    {{-- Live Search Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Live Search</label>
                <div class="relative">
                    <input type="text" id="search_input"
                           placeholder="Search batch # or item name..."
                           class="w-full rounded-lg border-gray-300 text-sm pl-9"
                           autocomplete="off">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                    <div id="search_loader" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <i class="fas fa-spinner fa-spin text-emerald-500 text-xs"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Batch Status</label>
                <select id="batch_status_filter" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="partially_used">Partially Used</option>
                    <option value="depleted">Depleted</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Status</label>
                <select id="expiry_status_filter" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All</option>
                    <option value="expiring_soon">Expiring Soon (30 days)</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button id="reset_filters_btn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition w-full">
                    <i class="fas fa-undo mr-1"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>

    {{-- Results Summary --}}
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-600" id="results_summary">
            Showing <span id="showing_start">0</span> to <span id="showing_end">0</span> of <span id="total_results">0</span> results
        </div>
    </div>

    {{-- Batches Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Item Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Batch Number</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Initial Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Remaining</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Unit Cost</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Manufacture Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Expiry Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="batches_table_body" class="divide-y divide-gray-100">
                    @include('store.batches.partials.table_rows', ['batches' => $batches])
                </tbody>
            </table>
        </div>

        <div id="pagination_container" class="px-4 py-3 border-t border-gray-200">
            @if($batches->hasPages())
                {{ $batches->appends(request()->query())->links() }}
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let debounceTimer;
    let currentPage = 1;
    let isLoading = false;

    const searchInput = document.getElementById('search_input');
    const batchStatusFilter = document.getElementById('batch_status_filter');
    const expiryStatusFilter = document.getElementById('expiry_status_filter');
    const resetBtn = document.getElementById('reset_filters_btn');
    const tableBody = document.getElementById('batches_table_body');
    const paginationContainer = document.getElementById('pagination_container');
    const resultsSummary = document.getElementById('results_summary');
    const searchLoader = document.getElementById('search_loader');
    const showingStart = document.getElementById('showing_start');
    const showingEnd = document.getElementById('showing_end');
    const totalResults = document.getElementById('total_results');

    function fetchLiveResults() {
        if (isLoading) return;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const params = new URLSearchParams();
            params.append('page', currentPage);
            params.append('ajax', '1');

            if (searchInput && searchInput.value.trim()) {
                params.append('search', searchInput.value.trim());
            }
            if (batchStatusFilter && batchStatusFilter.value) {
                params.append('batch_status', batchStatusFilter.value);
            }
            if (expiryStatusFilter && expiryStatusFilter.value) {
                params.append('expiry_status', expiryStatusFilter.value);
            }

            if (searchLoader) searchLoader.classList.remove('hidden');
            isLoading = true;

            fetch(`{{ route('store.batches.index') }}?${params.toString()}`, {
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
                    showingStart.textContent = data.summary.start;
                    showingEnd.textContent = data.summary.end;
                    totalResults.textContent = data.summary.total;
                    resultsSummary.classList.remove('hidden');
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

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    if (batchStatusFilter) {
        batchStatusFilter.addEventListener('change', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    if (expiryStatusFilter) {
        expiryStatusFilter.addEventListener('change', function() {
            currentPage = 1;
            fetchLiveResults();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (batchStatusFilter) batchStatusFilter.value = '';
            if (expiryStatusFilter) expiryStatusFilter.value = '';
            currentPage = 1;
            fetchLiveResults();
        });
    }

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
@endpush
@endsection
