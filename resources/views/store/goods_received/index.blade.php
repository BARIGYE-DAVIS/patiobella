@extends('layouts.store')

@section('title', 'Goods Received Notes')
@section('page-title', 'Goods Received Notes')

@section('content')
<style>
    .grn-card {
        transition: all 0.3s ease;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .status-draft {
        background: #fef3c7;
        color: #92400e;
    }
    .status-draft::before {
        content: "●";
        font-size: 0.5rem;
        color: #f59e0b;
    }
    .status-inventory_updated {
        background: #d1fae5;
        color: #065f46;
    }
    .status-inventory_updated::before {
        content: "●";
        font-size: 0.5rem;
        color: #10b981;
    }
    .status-verified {
        background: #dbeafe;
        color: #1e40af;
    }
    .status-verified::before {
        content: "●";
        font-size: 0.5rem;
        color: #3b82f6;
    }
    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-rejected::before {
        content: "●";
        font-size: 0.5rem;
        color: #ef4444;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.875rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.8rem;
    }
    .data-table tbody tr {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .data-table tbody tr:hover {
        background: #f0fdf4;
    }
    .btn-view {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-view:hover {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .grn-number {
        font-family: monospace;
        font-weight: 700;
        color: #059669;
        letter-spacing: 0.5px;
    }
</style>

<div class="space-y-5">

    {{-- Header Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Total GRNs</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $grns->total() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-file-alt text-emerald-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Draft</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $grns->where('status', 'draft')->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-pencil-alt text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Inventory Updated</p>
                    <p class="text-2xl font-bold text-green-600">{{ $grns->where('status', 'inventory_updated')->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">This Month</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $grns->filter(function($grn) { return $grn->received_date->format('Y-m') == now()->format('Y-m'); })->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-orange-700 to-orange-600 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">Goods Received Notes</h2>
                    <p class="text-xs text-orange-100 mt-0.5">Manage and track all received goods</p>
                </div>
            </div>
            <a href="{{ route('store.goods-received.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-orange-700 rounded-lg hover:bg-orange-50 transition shadow-sm text-sm font-semibold">
                <i class="fas fa-plus-circle"></i>
                New GRN
            </a>
        </div>

        {{-- Search and Filter Bar --}}
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchInput"
                           placeholder="Search by GRN number, PO number, or vendor..."
                           class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                </div>
                <div class="flex gap-3">
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <select id="statusFilter" class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="inventory_updated">Inventory Updated</option>
                            <option value="verified">Verified</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <button id="clearFilters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                        <i class="fas fa-eraser mr-1"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="data-table" id="grnsTable">
                <thead>
                    <tr>
                        <th>GRN Number</th>
                        <th>PO Number</th>
                        <th>Vendor</th>
                        <th class="text-center">Received Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($grns as $grn)
                    <tr class="grn-row"
                        data-grn-number="{{ strtolower($grn->grn_number) }}"
                        data-po-number="{{ strtolower($grn->purchaseOrder->po_number ?? '') }}"
                        data-vendor="{{ strtolower($grn->vendor->name ?? '') }}"
                        data-status="{{ $grn->status }}">
                        <td class="font-semibold">
                            <span class="grn-number">{{ $grn->grn_number }}</span>
                        </td>
                        <td>{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</td>
                        <td>{{ $grn->vendor->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $grn->received_date->format('Y-m-d') }}</td>
                        <td class="text-center">
                            @php
                                $statusClass = match($grn->status) {
                                    'draft' => 'status-draft',
                                    'inventory_updated' => 'status-inventory_updated',
                                    'verified' => 'status-verified',
                                    'rejected' => 'status-rejected',
                                    default => 'status-draft',
                                };
                                $statusText = match($grn->status) {
                                    'draft' => 'Draft',
                                    'inventory_updated' => 'Inventory Updated',
                                    'verified' => 'Verified',
                                    'rejected' => 'Rejected',
                                    default => ucfirst($grn->status),
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('store.goods-received.show', $grn->id) }}"
                               class="btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr id="noDataRow">
                        <td colspan="6" class="text-center py-12">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <i class="fas fa-box-open text-4xl"></i>
                                <p class="text-sm font-medium text-gray-500">No Goods Received Notes found</p>
                                <a href="{{ route('store.goods-received.create') }}" class="text-orange-600 hover:underline text-sm">
                                    Create your first GRN →
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($grns->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $grns->links() }}
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const rows = document.querySelectorAll('.grn-row');
    const tableBody = document.getElementById('tableBody');
    let noDataRow = document.getElementById('noDataRow');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        let visibleCount = 0;

        rows.forEach(row => {
            const grnNumber = row.getAttribute('data-grn-number') || '';
            const poNumber = row.getAttribute('data-po-number') || '';
            const vendor = row.getAttribute('data-vendor') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchesSearch = searchTerm === '' ||
                                 grnNumber.includes(searchTerm) ||
                                 poNumber.includes(searchTerm) ||
                                 vendor.includes(searchTerm);
            const matchesStatus = statusValue === '' || rowStatus === statusValue;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0 && rows.length > 0) {
            if (!noDataRow) {
                const newNoDataRow = document.createElement('tr');
                newNoDataRow.id = 'noDataRow';
                newNoDataRow.innerHTML = `
                    <td colspan="6" class="text-center py-12">
                        <div class="flex flex-col items-center gap-3 text-gray-400">
                            <i class="fas fa-search text-4xl"></i>
                            <p class="text-sm font-medium text-gray-500">No results match your filters</p>
                            <button id="clearFiltersEmpty" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700 transition">Clear Filters</button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(newNoDataRow);
                noDataRow = newNoDataRow;

                document.getElementById('clearFiltersEmpty')?.addEventListener('click', function() {
                    searchInput.value = '';
                    statusFilter.value = '';
                    filterTable();
                });
            }
        } else if (noDataRow && visibleCount > 0) {
            noDataRow.remove();
            noDataRow = null;
        }
    }

    // Debounced search
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterTable, 300);
    });

    statusFilter.addEventListener('change', filterTable);

    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        filterTable();
    });

    // Row click to view details
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-view')) {
                const viewLink = this.querySelector('.btn-view');
                if (viewLink) {
                    window.location.href = viewLink.getAttribute('href');
                }
            }
        });
    });
});
</script>
@endsection
