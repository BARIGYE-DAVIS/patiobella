{{-- resources/views/management/department-requisitions/index.blade.php --}}

@extends('layouts.management')

@section('title', 'Department Requisitions')
@section('page-title', 'Department Requisitions')

@section('content')
<div class="space-y-4">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 cursor-pointer hover:bg-gray-50 transition" onclick="filterByStatus('all')">
            <p class="text-xs text-gray-500">Total</p>
            <p class="text-xl font-bold text-gray-800" id="totalCount">0</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-3 cursor-pointer hover:bg-yellow-50 transition" onclick="filterByStatus('pending')">
            <p class="text-xs text-yellow-600">Pending</p>
            <p class="text-xl font-bold text-yellow-700" id="pendingCount">0</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-3 cursor-pointer hover:bg-blue-50 transition" onclick="filterByStatus('approved')">
            <p class="text-xs text-blue-600">Approved</p>
            <p class="text-xl font-bold text-blue-700" id="approvedCount">0</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-green-200 p-3 cursor-pointer hover:bg-green-50 transition" onclick="filterByStatus('issued')">
            <p class="text-xs text-green-600">Issued</p>
            <p class="text-xl font-bold text-green-700" id="issuedCount">0</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-red-200 p-3 cursor-pointer hover:bg-red-50 transition" onclick="filterByStatus('rejected')">
            <p class="text-xs text-red-600">Rejected</p>
            <p class="text-xl font-bold text-red-700" id="rejectedCount">0</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 cursor-pointer hover:bg-gray-50 transition" onclick="filterByStatus('completed')">
            <p class="text-xs text-gray-500">Completed</p>
            <p class="text-xl font-bold text-gray-700" id="completedCount">0</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 bg-white rounded-t-lg px-6 pt-4">
        <nav class="-mb-px flex space-x-6 overflow-x-auto">
            <button onclick="filterByStatus('all')" class="status-tab py-3 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap" data-status="all">
                <i class="fas fa-list mr-2"></i> All Requisitions
            </button>
            <button onclick="filterByStatus('pending')" class="status-tab py-3 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap" data-status="pending">
                <i class="fas fa-clock mr-2"></i> Pending
            </button>
            <button onclick="filterByStatus('approved')" class="status-tab py-3 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap" data-status="approved">
                <i class="fas fa-check-circle mr-2"></i> Approved
            </button>
            <button onclick="filterByStatus('issued')" class="status-tab py-3 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap" data-status="issued">
                <i class="fas fa-truck mr-2"></i> Issued
            </button>
            <button onclick="filterByStatus('rejected')" class="status-tab py-3 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap" data-status="rejected">
                <i class="fas fa-times-circle mr-2"></i> Rejected
            </button>
            <button onclick="filterByStatus('cancelled')" class="status-tab py-3 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap" data-status="cancelled">
                <i class="fas fa-ban mr-2"></i> Cancelled
            </button>
            <button onclick="filterByStatus('completed')" class="status-tab py-3 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap" data-status="completed">
                <i class="fas fa-check-double mr-2"></i> Completed
            </button>
        </nav>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                <select id="departmentFilter" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" id="dateFromFilter" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" id="dateToFilter" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Live Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search by requisition #, department..."
                           class="w-full pl-9 pr-3 py-2 rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <button id="clearSearchBtn" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 hidden">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Actions --}}
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-2">
            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
            <label for="selectAll" class="text-sm text-gray-600">Select All</label>
        </div>
        <button id="bulkApproveBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition hidden">
            <i class="fas fa-check-double mr-1"></i> Bulk Approve
        </button>
    </div>

    {{-- Requisitions Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-center w-10"></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Requisition #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Items</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Total Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Requested By</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Date Needed</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody id="requisitionsTableBody">
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                            Loading requisitions...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="pagination" class="px-4 py-3 border-t border-gray-200 flex justify-between items-center">
            {{-- Pagination loaded here --}}
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.45)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Reject Requisition</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            @method('POST')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Please provide a reason for rejecting this requisition:</p>
                <textarea name="rejection_reason" rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                    required placeholder="Enter rejection reason (minimum 10 characters)..."></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentPage      = 1;
    let currentStatus    = 'all';
    let currentDepartment = '';
    let currentDateFrom  = '';
    let currentDateTo    = '';
    let currentSearch    = '';

    // Status colours / labels
    const statusColors = {
        'pending':          'bg-yellow-100 text-yellow-700',
        'approved':         'bg-blue-100 text-blue-700',
        'partially_issued': 'bg-orange-100 text-orange-700',
        'issued':           'bg-green-100 text-green-700',
        'rejected':         'bg-red-100 text-red-700',
        'cancelled':        'bg-gray-100 text-gray-500',
        'completed':        'bg-teal-100 text-teal-700',
    };

    const statusLabels = {
        'pending':          'Pending',
        'approved':         'Approved',
        'partially_issued': 'Partially Issued',
        'issued':           'Issued',
        'rejected':         'Rejected',
        'cancelled':        'Cancelled',
        'completed':        'Completed',
    };

    // ── Tabs ──────────────────────────────────────────────
    function filterByStatus(status) {
        currentStatus = status;
        currentPage   = 1;
        updateActiveTab();
        loadRequisitions();
    }

    function updateActiveTab() {
        document.querySelectorAll('.status-tab').forEach(tab => {
            const isActive = tab.dataset.status === currentStatus;
            tab.classList.toggle('border-orange-500', isActive);
            tab.classList.toggle('text-orange-600',   isActive);
            tab.classList.toggle('border-transparent', !isActive);
            tab.classList.toggle('text-gray-500',      !isActive);
        });
    }

    // ── Summary counts ────────────────────────────────────
    function updateSummaryCounts(data) {
        document.getElementById('totalCount').innerText     = data.total     ?? 0;
        document.getElementById('pendingCount').innerText   = data.pending   ?? 0;
        document.getElementById('approvedCount').innerText  = data.approved  ?? 0;
        document.getElementById('issuedCount').innerText    = data.issued    ?? 0;
        document.getElementById('rejectedCount').innerText  = data.rejected  ?? 0;
        document.getElementById('completedCount').innerText = data.completed ?? 0;
    }

    // ── Main fetch ────────────────────────────────────────
    function loadRequisitions() {
        // Show loading state
        document.getElementById('requisitionsTableBody').innerHTML = `
            <tr>
                <td colspan="10" class="px-4 py-8 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                    Loading requisitions...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page:          currentPage,
            status:        currentStatus,
            department_id: currentDepartment,
            date_from:     currentDateFrom,
            date_to:       currentDateTo,
            search:        currentSearch,
        });

        // ✅ Hitting the /json route — NOT the index route
        fetch('/management/department-requisitions/json?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server returned ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderTable(data.requisitions.data);
                renderPagination(data.requisitions);
                updateSummaryCounts(data.summary);
            } else {
                showTableError('Failed to load requisitions.');
            }
        })
        .catch(error => {
            console.error('Error loading requisitions:', error);
            showTableError('Failed to load requisitions. Please try again.');
        });
    }

    function showTableError(message) {
        document.getElementById('requisitionsTableBody').innerHTML = `
            <tr>
                <td colspan="10" class="px-4 py-8 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2 block"></i>
                    ${message}
                </td>
            </tr>
        `;
    }

    // ── Render table rows ─────────────────────────────────
    function renderTable(requisitions) {
        const tbody = document.getElementById('requisitionsTableBody');

        if (!requisitions || requisitions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2 block"></i>
                        No requisitions found.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = requisitions.map(req => {
            const statusClass = statusColors[req.status] ?? 'bg-gray-100 text-gray-500';
            const statusLabel = statusLabels[req.status] ?? req.status;
            const requestedBy = req.requested_by
                ? `${req.requested_by.first_name ?? ''} ${req.requested_by.last_name ?? ''}`.trim()
                : 'N/A';
            const createdDate  = req.created_at  ? new Date(req.created_at).toLocaleDateString()  : '';
            const neededDate   = req.date_needed  ? new Date(req.date_needed).toLocaleDateString() : '—';
            const totalQty     = parseFloat(req.total_quantity_requested || 0).toFixed(2);

            const pendingActions = req.status === 'pending' ? `
                <a href="/management/department-requisitions/${req.id}/approve"
                   class="text-green-600 hover:text-green-800" title="Approve">
                    <i class="fas fa-check-circle"></i>
                </a>
                <button onclick="showRejectModal(${req.id})"
                        class="text-red-600 hover:text-red-800" title="Reject">
                    <i class="fas fa-times-circle"></i>
                </button>
            ` : '';

            const checkbox = req.status === 'pending'
                ? `<input type="checkbox" class="req-checkbox rounded border-gray-300 text-orange-600 focus:ring-orange-500" value="${req.id}">`
                : '';

            return `
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-center">${checkbox}</td>
                    <td class="px-4 py-3">
                        <span class="font-mono font-medium text-gray-800">${req.requisition_number}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                            <i class="fas fa-building text-xs"></i>
                            ${req.department?.name ?? 'N/A'}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs text-gray-500">${req.requisition_type_label ?? '—'}</span>
                    </td>
                    <td class="px-4 py-3 text-center">${req.total_items ?? 0}</td>
                    <td class="px-4 py-3 text-center">${totalQty}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm text-gray-800">${requestedBy}</p>
                        <p class="text-xs text-gray-400">${createdDate}</p>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">${neededDate}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                            ${statusLabel}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="/management/department-requisitions/${req.id}"
                               class="text-blue-600 hover:text-blue-800" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            ${pendingActions}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Re-attach checkbox listeners
        document.querySelectorAll('.req-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkApproveButton);
        });
    }

    // ── Pagination ────────────────────────────────────────
    function renderPagination(pagination) {
        const container = document.getElementById('pagination');

        if (!pagination || pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        let buttons = '';

        if (pagination.current_page > 1) {
            buttons += `<button onclick="goToPage(${pagination.current_page - 1})"
                class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Previous</button>`;
        }

        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                buttons += `<button class="px-3 py-1 bg-orange-600 text-white rounded-lg text-sm">${i}</button>`;
            } else if (Math.abs(i - pagination.current_page) <= 2 || i === 1 || i === pagination.last_page) {
                buttons += `<button onclick="goToPage(${i})"
                    class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">${i}</button>`;
            } else if (Math.abs(i - pagination.current_page) === 3) {
                buttons += '<span class="px-2 py-1 text-gray-400">...</span>';
            }
        }

        if (pagination.current_page < pagination.last_page) {
            buttons += `<button onclick="goToPage(${pagination.current_page + 1})"
                class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Next</button>`;
        }

        container.innerHTML = `
            <div class="flex gap-2">${buttons}</div>
            <div class="text-sm text-gray-500">
                Showing ${pagination.from ?? 0} to ${pagination.to ?? 0} of ${pagination.total ?? 0} results
            </div>
        `;
    }

    function goToPage(page) {
        currentPage = page;
        loadRequisitions();
    }

    // ── Reject modal ──────────────────────────────────────
    let rejectRequisitionId = null;

    function showRejectModal(id) {
        rejectRequisitionId = id;
        document.getElementById('rejectForm').action = `/management/department-requisitions/${id}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        rejectRequisitionId = null;
    }

    // Close modal when clicking backdrop
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });

    // ── Bulk approve ──────────────────────────────────────
    function updateBulkApproveButton() {
        const checked = document.querySelectorAll('.req-checkbox:checked').length;
        document.getElementById('bulkApproveBtn').classList.toggle('hidden', checked === 0);
    }

    document.getElementById('selectAll')?.addEventListener('change', function (e) {
        document.querySelectorAll('.req-checkbox').forEach(cb => cb.checked = e.target.checked);
        updateBulkApproveButton();
    });

    document.getElementById('bulkApproveBtn')?.addEventListener('click', function () {
        const selected = Array.from(document.querySelectorAll('.req-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) return;

        if (!confirm(`Approve ${selected.length} requisition(s)?`)) return;

        fetch('/management/department-requisitions/bulk-approve', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ requisition_ids: selected })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                loadRequisitions();
                alert(data.message ?? 'Requisitions approved.');
            } else {
                alert(data.message ?? 'Failed to bulk approve.');
            }
        })
        .catch(() => alert('Error processing bulk approval.'));
    });

    // ── Filter listeners ──────────────────────────────────
    document.getElementById('departmentFilter')?.addEventListener('change', function (e) {
        currentDepartment = e.target.value;
        currentPage = 1;
        loadRequisitions();
    });

    document.getElementById('dateFromFilter')?.addEventListener('change', function (e) {
        currentDateFrom = e.target.value;
        currentPage = 1;
        loadRequisitions();
    });

    document.getElementById('dateToFilter')?.addEventListener('change', function (e) {
        currentDateTo = e.target.value;
        currentPage = 1;
        loadRequisitions();
    });

    // Enhanced search with clear button
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    let searchTimeout;
    searchInput?.addEventListener('input', function (e) {
        // Show/hide clear button
        if (e.target.value.length > 0) {
            clearSearchBtn?.classList.remove('hidden');
        } else {
            clearSearchBtn?.classList.add('hidden');
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = e.target.value;
            currentPage   = 1;
            loadRequisitions();
        }, 500);
    });

    // Clear search functionality
    clearSearchBtn?.addEventListener('click', function() {
        if (searchInput) {
            searchInput.value = '';
            currentSearch = '';
            currentPage = 1;
            clearSearchBtn.classList.add('hidden');
            loadRequisitions();
        }
    });

    // Also handle initial clear button state on page load
    if (searchInput && searchInput.value.length > 0) {
        clearSearchBtn?.classList.remove('hidden');
    }

    // ── Boot ──────────────────────────────────────────────
    updateActiveTab();
    loadRequisitions();
</script>
@endsection
