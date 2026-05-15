@extends('layouts.store')

@section('title', 'Department Requisitions')
@section('page-title', 'Department Requisitions')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-900">Department Requisitions</h3>
            <p class="text-sm text-gray-500 mt-0.5">Manage requests from Kitchen, Bar, and other departments</p>
        </div>
        <span id="count-badge" class="text-xs font-medium px-3 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
            {{ $requisitions->total() }} requisitions
        </span>
    </div>

    {{-- Toolbar --}}
    <div class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">

        {{-- Live Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input
                type="text"
                id="live-search"
                placeholder="Search requisition #, department, person…"
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
        </div>

        {{-- Status Filter --}}
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Status</label>
            <select id="filter-status" class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[160px]">
                <option value="">All statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="partially_issued">Partially Issued</option>
                <option value="issued">Issued</option>
                <option value="partially_consumed">Partially Consumed</option>
                <option value="fully_consumed">Fully Consumed</option>
                <option value="completed">Completed</option>
                <option value="partially_returned">Partially Returned</option>
                <option value="returned">Returned</option>
                <option value="rejected">Rejected</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        {{-- Department Filter --}}
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Department</label>
            <select id="filter-dept" class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[140px]">
                <option value="">All departments</option>
                @foreach($departments as $dept)
                    <option value="{{ strtolower($dept->name) }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Reset --}}
        <button id="btn-reset" class="flex items-center gap-1.5 text-sm text-gray-500 border border-gray-300 rounded-lg px-3 py-2 bg-white hover:bg-gray-100 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
            Reset
        </button>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    @php
                        $cols = [
                            'num'    => ['label' => 'Requisition #', 'align' => 'left'],
                            'dept'   => ['label' => 'Department',    'align' => 'left'],
                            'person' => ['label' => 'Requested By',  'align' => 'left'],
                            'date'   => ['label' => 'Date',          'align' => 'center'],
                            'items'  => ['label' => 'Items',         'align' => 'right'],
                            'qty'    => ['label' => 'Total Qty',     'align' => 'right'],
                            'status' => ['label' => 'Status',        'align' => 'center'],
                        ];
                    @endphp
                    @foreach($cols as $key => $col)
                    <th
                        data-col="{{ $key }}"
                        class="sort-header px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500 cursor-pointer select-none hover:text-gray-800 transition whitespace-nowrap
                            {{ $col['align'] === 'right' ? 'text-right' : ($col['align'] === 'center' ? 'text-center' : 'text-left') }}"
                    >
                        {{ $col['label'] }}
                        <span class="sort-icon ml-1 opacity-30 text-[10px]">↕</span>
                    </th>
                    @endforeach
                    <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500 text-center">Action</th>
                </tr>
            </thead>
            <tbody id="req-tbody" class="divide-y divide-gray-100">
                @forelse($requisitions as $req)
                @php
                    $statusConfig = [
                        'pending'           => ['class' => 'bg-amber-50 text-amber-700 border-amber-200',  'label' => 'Pending'],
                        'approved'          => ['class' => 'bg-blue-50 text-blue-700 border-blue-200',    'label' => 'Approved'],
                        'issued'            => ['class' => 'bg-green-50 text-green-700 border-green-200', 'label' => 'Issued'],
                        'partially_issued'  => ['class' => 'bg-orange-50 text-orange-700 border-orange-200', 'label' => 'Partially Issued'],
                        'partially_consumed'=> ['class' => 'bg-yellow-50 text-yellow-700 border-yellow-200', 'label' => 'Partially Consumed'],
                        'fully_consumed'    => ['class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Fully Consumed'],
                        'completed'         => ['class' => 'bg-teal-50 text-teal-700 border-teal-200',     'label' => 'Completed'],
                        'partially_returned'=> ['class' => 'bg-purple-50 text-purple-700 border-purple-200', 'label' => 'Partially Returned'],
                        'returned'          => ['class' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'label' => 'Returned'],
                        'rejected'          => ['class' => 'bg-red-50 text-red-700 border-red-200',       'label' => 'Rejected'],
                        'cancelled'         => ['class' => 'bg-gray-100 text-gray-500 border-gray-200',   'label' => 'Cancelled'],
                    ];
                    $sc = $statusConfig[$req->status] ?? ['class' => 'bg-gray-100 text-gray-500 border-gray-200', 'label' => ucfirst($req->status)];
                    $deptName   = $req->department->name ?? 'N/A';
                    $personName = trim(($req->requestedBy->first_name ?? '') . ' ' . ($req->requestedBy->last_name ?? ''));
                @endphp
                <tr
                    class="req-row hover:bg-gray-50 transition-colors"
                    data-num="{{ strtolower($req->requisition_number) }}"
                    data-dept="{{ strtolower($deptName) }}"
                    data-person="{{ strtolower($personName) }}"
                    data-date="{{ $req->created_at->format('Y-m-d') }}"
                    data-items="{{ $req->items->count() }}"
                    data-qty="{{ $req->items->sum('quantity_requested') }}"
                    data-status="{{ $req->status }}"
                >
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-500 searchable">
                        {{ $req->requisition_number }}
                    </td>
                    <td class="px-4 py-3 searchable">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                            {{ $deptName }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700 searchable">{{ $personName ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-center">{{ $req->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ $req->items->count() }}</td>
                    <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($req->items->sum('quantity_requested'), 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full border {{ $sc['class'] }}">
                            {{ $sc['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('store.department-requisitions.show', $req->id) }}"
                           class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-gray-200 rounded-lg px-2.5 py-1.5 hover:bg-blue-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr id="no-data-row">
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                        </svg>
                        No requisitions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Empty state for filtered results --}}
        <div id="empty-filtered" class="hidden px-4 py-12 text-center text-gray-400">
            <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <p class="text-sm">No requisitions match your search.</p>
        </div>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between flex-wrap gap-2">
        <p class="text-xs text-gray-500" id="footer-info">
            Showing {{ $requisitions->count() }} of {{ $requisitions->total() }} requisitions
        </p>
        <div>{{ $requisitions->links() }}</div>
    </div>
</div>

<script>
(function () {
    const rows        = Array.from(document.querySelectorAll('.req-row'));
    const searchInput = document.getElementById('live-search');
    const statusSel   = document.getElementById('filter-status');
    const deptSel     = document.getElementById('filter-dept');
    const resetBtn    = document.getElementById('btn-reset');
    const emptyDiv    = document.getElementById('empty-filtered');
    const footerInfo  = document.getElementById('footer-info');
    const countBadge  = document.getElementById('count-badge');
    const total       = rows.length;

    let sortCol = null, sortDir = 1;

    function applyFilters() {
        const q    = searchInput.value.trim().toLowerCase();
        const st   = statusSel.value;
        const dept = deptSel.value;
        let visible = 0;

        rows.forEach(row => {
            const matchSearch = !q || (
                row.dataset.num.includes(q) ||
                row.dataset.dept.includes(q) ||
                row.dataset.person.includes(q)
            );
            const matchStatus = !st   || row.dataset.status === st;
            const matchDept   = !dept || row.dataset.dept   === dept;

            const show = matchSearch && matchStatus && matchDept;
            row.style.display = show ? '' : 'none';
            if (show) visible++;

            // Highlight matching text in searchable cells
            row.querySelectorAll('.searchable').forEach(cell => {
                const original = cell.textContent.trim();
                if (!q) {
                    cell.innerHTML = cell.textContent;
                    return;
                }
                const idx = original.toLowerCase().indexOf(q);
                if (idx >= 0) {
                    cell.innerHTML =
                        escapeHtml(original.slice(0, idx)) +
                        '<mark class="bg-amber-100 text-amber-900 rounded px-0.5">' +
                        escapeHtml(original.slice(idx, idx + q.length)) +
                        '</mark>' +
                        escapeHtml(original.slice(idx + q.length));
                } else {
                    cell.innerHTML = escapeHtml(original);
                }
            });
        });

        emptyDiv.classList.toggle('hidden', visible > 0 || total === 0);
        const label = visible + ' requisition' + (visible !== 1 ? 's' : '');
        countBadge.textContent = label;
        footerInfo.textContent = visible === total
            ? 'Showing all ' + total + ' requisitions'
            : 'Showing ' + visible + ' of ' + total + ' requisitions';
    }

    function escapeHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function sortRows(col) {
        const tbody = document.getElementById('req-tbody');
        const numericCols = ['items','qty'];
        const sorted = [...rows].sort((a, b) => {
            let av = a.dataset[col], bv = b.dataset[col];
            if (numericCols.includes(col)) {
                return (parseFloat(av) - parseFloat(bv)) * sortDir;
            }
            return av.localeCompare(bv) * sortDir;
        });
        sorted.forEach(r => tbody.appendChild(r));
        applyFilters();
    }

    document.querySelectorAll('.sort-header').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.col;
            sortDir = (sortCol === col) ? sortDir * -1 : 1;
            sortCol = col;
            document.querySelectorAll('.sort-header').forEach(t => {
                t.querySelector('.sort-icon').textContent = '↕';
                t.querySelector('.sort-icon').classList.add('opacity-30');
            });
            const icon = th.querySelector('.sort-icon');
            icon.textContent = sortDir === 1 ? '↑' : '↓';
            icon.classList.remove('opacity-30');
            sortRows(col);
        });
    });

    searchInput.addEventListener('input', applyFilters);
    statusSel.addEventListener('change', applyFilters);
    deptSel.addEventListener('change', applyFilters);

    resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        statusSel.value   = '';
        deptSel.value     = '';
        applyFilters();
    });
})();
</script>
@endsection
