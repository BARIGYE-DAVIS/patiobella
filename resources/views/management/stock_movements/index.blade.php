@extends('layouts.management')

@section('title', 'Stock Movements & Distribution')

@section('page-title', 'Stock Movements & Distribution')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        margin-bottom: 1rem;
    }
    .stat-card h3 {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    .stat-card .value {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .stat-in { border-left-color: #10b981; }
    .stat-out { border-left-color: #ef4444; }
    .stat-net { border-left-color: #3b82f6; }
    .stat-value { border-left-color: #8b5cf6; }

    .filter-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge-in {
        background: #d1fae5;
        color: #065f46;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.7rem;
    }

    .badge-out {
        background: #fee2e2;
        color: #991b1b;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.7rem;
    }

    .table-container {
        overflow-x: auto;
        margin-bottom: 1.5rem;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }

    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }

    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }

    .data-table tr:hover {
        background: #f8fafc;
    }

    .export-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-export {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-excel {
        background: #10b981;
        color: white;
    }

    .btn-excel:hover {
        background: #059669;
    }

    .btn-pdf {
        background: #ef4444;
        color: white;
    }

    .btn-pdf:hover {
        background: #dc2626;
    }

    .btn-print {
        background: #6b7280;
        color: white;
    }

    .btn-print:hover {
        background: #4b5563;
    }

    .filter-input {
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
    }

    /* Tabs Styles */
    .tabs-container {
        margin-bottom: 1.5rem;
    }
    .tabs {
        display: flex;
        gap: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 0;
        flex-wrap: wrap;
    }
    .tab-btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        color: #6b7280;
        margin-bottom: -2px;
    }
    .tab-btn:hover {
        color: #374151;
    }
    .tab-btn.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }
    .tab-content {
        display: none;
        padding: 1.5rem 0;
    }
    .tab-content.active {
        display: block;
    }

    /* Chart Containers */
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    .chart-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        text-align: center;
    }
    canvas {
        max-height: 300px;
        width: 100%;
    }
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    /* ── Live Search ─────────────────────────────────────────────── */
    .live-search-wrapper {
        position: relative;
        margin-bottom: 1rem;
        max-width: 360px;
    }
    .live-search-wrapper::before {
        content: '🔍';
        position: absolute;
        left: 0.6rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.75rem;
        pointer-events: none;
        line-height: 1;
    }
    .live-search-input {
        width: 100%;
        padding: 0.5rem 0.75rem 0.5rem 2rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.8rem;
        background: white;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
    }
    .live-search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .live-search-count {
        font-size: 0.7rem;
        color: #9ca3af;
        margin-top: 0.3rem;
        min-height: 1rem;
    }
</style>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="space-y-6">

    {{-- Statistics Cards --}}
    <div class="stats-row">
        <div class="stat-card stat-in">
            <h3>Total Stock IN</h3>
            <div class="value">{{ number_format($totalStockIn, 2) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Purchases + GRN + Returns + Manual</p>
        </div>
        <div class="stat-card stat-out">
            <h3>Total Stock OUT</h3>
            <div class="value">{{ number_format($totalStockOut, 2) }} units</div>
            <p class="text-xs text-gray-500 mt-1">Issues + Manual Out</p>
        </div>
        <div class="stat-card stat-net">
            <h3>Net Change</h3>
            <div class="value {{ $netChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange, 2) }} units
            </div>
            <p class="text-xs text-gray-500 mt-1">IN - OUT</p>
        </div>
        <div class="stat-card stat-value">
            <h3>Total Value Moved</h3>
            <div class="value">UGX {{ number_format($movements->sum('total_value'), 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">All transactions combined</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('management.stock-movements.index') }}" class="grid grid-cols-2 md:grid-cols-6 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" class="filter-input w-full" value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" class="filter-input w-full" value="{{ request('date_to') }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Item</label>
                <select name="item_id" class="filter-input w-full">
                    <option value="">All Items</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Movement Type</label>
                <select name="movement_type_id" class="filter-input w-full">
                    <option value="">All Types</option>
                    @foreach($movementTypes as $type)
                        <option value="{{ $type->id }}" {{ request('movement_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" class="filter-input w-full">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-blue-700">
                        🔍 Filter
                    </button>
                    <a href="{{ route('management.stock-movements.index') }}" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-xs hover:bg-gray-400">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Export Buttons --}}
    <div class="flex justify-end gap-2">
        <a href="{{ route('management.stock-movements.export.excel', request()->query()) }}" class="btn-export btn-excel">
            📊 Export to Excel
        </a>
        <a href="{{ route('management.stock-movements.export.pdf', request()->query()) }}" class="btn-export btn-pdf">
            📄 Export to PDF
        </a>
        <button onclick="window.print()" class="btn-export btn-print">
            🖨️ Print
        </button>
    </div>

    {{-- TABS --}}
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-btn active" data-tab="tab-overview">📊 Overview</button>
            <button class="tab-btn" data-tab="tab-distribution">📦 Distribution</button>
            <button class="tab-btn" data-tab="tab-returns">🔄 Returns</button>
            <button class="tab-btn" data-tab="tab-stockin">📥 Stock IN</button>
            <button class="tab-btn" data-tab="tab-all">📋 All Movements</button>
        </div>

        {{-- TAB 1: OVERVIEW (charts only) --}}
        <div id="tab-overview" class="tab-content active">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="chart-container">
                    <div class="chart-title">Stock IN vs Stock OUT</div>
                    <canvas id="inOutChart" width="400" height="300"></canvas>
                </div>
                <div class="chart-container">
                    <div class="chart-title">Department Consumption</div>
                    <canvas id="deptChart" width="400" height="300"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="chart-container">
                    <div class="chart-title">Weekly Stock Movement Trend</div>
                    <canvas id="weeklyChart" width="400" height="300"></canvas>
                </div>
                <div class="chart-container">
                    <div class="chart-title">Top 5 Moving Items</div>
                    <canvas id="topItemsChart" width="400" height="300"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-title">Stock IN Source Breakdown</div>
                <canvas id="sourceChart" width="400" height="300"></canvas>
            </div>
        </div>

        {{-- TAB 2: DISTRIBUTION --}}
        <div id="tab-distribution" class="tab-content">
            <div class="section-title">
                <span>📦 Department Distribution (Issues to Departments)</span>
                <span class="text-xs text-gray-500">{{ $distributions->count() }} transactions</span>
            </div>
            <div class="live-search-wrapper">
                <input type="text" class="live-search-input"
                    id="search-distribution"
                    placeholder="Search by date, department, item, requisition…"
                    autocomplete="off">
                <div class="live-search-count" id="count-distribution"></div>
            </div>
            <div class="table-container">
                <table class="data-table" id="table-distribution">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Requisition #</th>
                            <th>Department</th>
                            <th>Item</th>
                            <th class="text-right">Qty (Packs)</th>
                            <th>Pack Type</th>
                            <th class="text-right">Pack Size</th>
                            <th class="text-right">Total Pieces</th>
                            <th>Taken By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distributions as $dist)
                        <tr>
                            <td>{{ $dist->created_at ? $dist->created_at->format('Y-m-d') : 'N/A' }}</td>
                            <td class="font-mono">{{ $dist->departmentRequisition->requisition_number ?? 'N/A' }}</td>
                            <td><span class="badge-out">{{ $dist->departmentRequisition->department->name ?? 'N/A' }}</span></td>
                            <td>{{ $dist->inventoryItem->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($dist->quantity_issued, 2) }}</td>
                            <td>{{ $dist->issued_pack_type ?? '-' }}</td>
                            <td class="text-right">{{ number_format($dist->issued_pack_size ?? 0) }}</td>
                            <td class="text-right font-semibold">{{ number_format($dist->issued_total_pieces ?? 0, 2) }}</td>
                            <td>{{ $dist->departmentRequisition->taken_by ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr id="empty-distribution">
                            <td colspan="9" class="text-center text-gray-500 py-4">No distribution records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr id="noresults-distribution" style="display:none;">
                            <td colspan="9" class="text-center text-gray-400 py-4 italic">No rows match your search.</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- TAB 3: RETURNS --}}
        <div id="tab-returns" class="tab-content">
            <div class="section-title">
                <span>🔄 Returns from Departments</span>
                <span class="text-xs text-gray-500">{{ $returns->count() }} transactions</span>
            </div>
            <div class="live-search-wrapper">
                <input type="text" class="live-search-input"
                    id="search-returns"
                    placeholder="Search by date, department, item, reason…"
                    autocomplete="off">
                <div class="live-search-count" id="count-returns"></div>
            </div>
            <div class="table-container">
                <table class="data-table" id="table-returns">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Requisition #</th>
                            <th>Department</th>
                            <th>Item</th>
                            <th class="text-right">Qty Returned</th>
                            <th>Reason</th>
                            <th>Returned By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $ret)
                        <tr>
                            <td>{{ $ret->returned_at ? $ret->returned_at->format('Y-m-d') : $ret->created_at->format('Y-m-d') }}</td>
                            <td class="font-mono">{{ $ret->departmentRequisition->requisition_number ?? 'N/A' }}</td>
                            <td><span class="badge-in">{{ $ret->departmentRequisition->department->name ?? 'N/A' }}</span></td>
                            <td>{{ $ret->inventoryItem->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($ret->quantity_returned, 2) }}</td>
                            <td>{{ Str::limit($ret->return_reason ?? '-', 30) }}</td>
                            <td>{{ $ret->departmentRequisition->returned_by ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr id="empty-returns">
                            <td colspan="7" class="text-center text-gray-500 py-4">No return records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr id="noresults-returns" style="display:none;">
                            <td colspan="7" class="text-center text-gray-400 py-4 italic">No rows match your search.</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- TAB 4: STOCK IN --}}
        <div id="tab-stockin" class="tab-content">
            <div class="section-title">
                <span>📥 Stock Additions (IN Movements)</span>
                <span class="text-xs text-gray-500">{{ $stockAdditions->count() }} transactions</span>
            </div>
            <div class="live-search-wrapper">
                <input type="text" class="live-search-input"
                    id="search-stockin"
                    placeholder="Search by date, item, source, movement #…"
                    autocomplete="off">
                <div class="live-search-count" id="count-stockin"></div>
            </div>
            <div class="table-container">
                <table class="data-table" id="table-stockin">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Movement #</th>
                            <th>Item</th>
                            <th class="text-right">Quantity</th>
                            <th>Unit</th>
                            <th class="text-right">Unit Cost</th>
                            <th class="text-right">Total Value</th>
                            <th>Source</th>
                            <th>Received By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockAdditions as $addition)
                        <tr>
                            <td>{{ $addition->movement_date ? $addition->movement_date->format('Y-m-d') : 'N/A' }}</td>
                            <td class="font-mono">{{ $addition->movement_number }}</td>
                            <td>{{ $addition->inventoryItem->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($addition->quantity_in_base_unit ?? $addition->quantity, 2) }}</td>
                            <td>{{ $addition->base_unit ?? $addition->inventoryItem->base_unit ?? 'units' }}</td>
                            <td class="text-right">UGX {{ number_format($addition->unit_cost ?? 0, 2) }}</td>
                            <td class="text-right">UGX {{ number_format($addition->total_value ?? 0, 2) }}</td>
                            <td>{{ $addition->movementType->name ?? 'N/A' }}</td>
                            <td>{{ $addition->createdBy->name ?? 'System' }}</td>
                        </tr>
                        @empty
                        <tr id="empty-stockin">
                            <td colspan="9" class="text-center text-gray-500 py-4">No stock addition records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr id="noresults-stockin" style="display:none;">
                            <td colspan="9" class="text-center text-gray-400 py-4 italic">No rows match your search.</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- TAB 5: ALL MOVEMENTS --}}
        <div id="tab-all" class="tab-content">
            <div class="section-title">
                <span>📋 All Stock Movements</span>
                <a href="#" class="text-xs text-blue-600 hover:underline">View All →</a>
            </div>
            <div class="live-search-wrapper">
                <input type="text" class="live-search-input"
                    id="search-all"
                    placeholder="Search by date, item, type, reason…"
                    autocomplete="off">
                <div class="live-search-count" id="count-all"></div>
            </div>
            <div class="table-container">
                <table class="data-table" id="table-all">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Movement #</th>
                            <th>Item</th>
                            <th>Type</th>
                            <th class="text-right">Quantity</th>
                            <th>Unit</th>
                            <th class="text-right">Stock Before</th>
                            <th class="text-right">Stock After</th>
                            <th>Reason</th>
                            <th>Responsible</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movement->movement_date ? $movement->movement_date->format('Y-m-d') : 'N/A' }}</td>
                            <td class="font-mono">
                                <a href="{{ route('management.stock-movements.show', $movement->id) }}" class="text-blue-600 hover:underline">
                                    {{ $movement->movement_number }}
                                </a>
                            </td>
                            <td>{{ $movement->inventoryItem->name ?? 'N/A' }}</td>
                            <td>
                                @if($movement->movementType && $movement->movementType->sign === '+')
                                    <span class="badge-in">{{ $movement->movementType->name ?? 'N/A' }}</span>
                                @else
                                    <span class="badge-out">{{ $movement->movementType->name ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($movement->quantity_in_base_unit ?? $movement->quantity, 2) }}</td>
                            <td>{{ $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units' }}</td>
                            <td class="text-right">{{ number_format($movement->stock_before ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($movement->stock_after ?? 0, 2) }}</td>
                            <td>{{ Str::limit($movement->reason ?? '-', 40) }}</td>
                            <td>
                                @if($movement->taken_by)
                                    Taken: {{ $movement->taken_by }}
                                @elseif($movement->returned_by)
                                    Returned: {{ $movement->returned_by }}
                                @else
                                    {{ $movement->createdBy->name ?? 'System' }}
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-all">
                            <td colspan="10" class="text-center text-gray-500 py-4">No stock movements found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr id="noresults-all" style="display:none;">
                            <td colspan="10" class="text-center text-gray-400 py-4 italic">No rows match your search.</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-4">
                {{ $movements->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ================================================================
       TAB SWITCHING
    ================================================================ */
    var tabBtns     = document.querySelectorAll('.tab-btn');
    var tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tabId = btn.getAttribute('data-tab');

            tabBtns.forEach(function (b) { b.classList.remove('active'); });
            tabContents.forEach(function (c) { c.classList.remove('active'); });

            btn.classList.add('active');
            var target = document.getElementById(tabId);
            if (target) { target.classList.add('active'); }

            // Redraw all charts when the overview tab becomes visible
            setTimeout(function () {
                if (window.inOutChart)    { window.inOutChart.update(); }
                if (window.deptChart)     { window.deptChart.update(); }
                if (window.weeklyChart)   { window.weeklyChart.update(); }
                if (window.topItemsChart) { window.topItemsChart.update(); }
                if (window.sourceChart)   { window.sourceChart.update(); }
            }, 100);
        });
    });

    /* ================================================================
       LIVE SEARCH  –  pure vanilla JS, no TypeScript non-null (!) used
    ================================================================ */

    /**
     * Wire up a live-search input to a table.
     *
     * @param {string} inputId       - id of the <input> element
     * @param {string} tableId       - id of the <table> element
     * @param {string} noResultsId   - id of the <tfoot> placeholder row
     * @param {string} countId       - id of the results-count label
     */
    function initLiveSearch(inputId, tableId, noResultsId, countId) {
        var input      = document.getElementById(inputId);
        var table      = document.getElementById(tableId);
        var noResults  = document.getElementById(noResultsId);
        var countLabel = document.getElementById(countId);

        // Bail silently if any element is missing (e.g. table has no rows yet)
        if (!input || !table) { return; }

        input.addEventListener('input', function () {
            var query  = input.value.trim().toLowerCase();
            var tbody  = table.querySelector('tbody');
            if (!tbody) { return; }

            var rows    = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
            var visible = 0;

            rows.forEach(function (row) {
                var text = row.textContent.toLowerCase();
                if (query === '' || text.indexOf(query) !== -1) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show / hide the "no results" placeholder
            if (noResults) {
                noResults.style.display = (visible === 0 && query !== '') ? '' : 'none';
            }

            // Update the count label
            if (countLabel) {
                if (query !== '') {
                    countLabel.textContent =
                        visible + ' row' + (visible !== 1 ? 's' : '') + ' found';
                } else {
                    countLabel.textContent = '';
                }
            }
        });
    }

    // Attach to each tab
    initLiveSearch('search-distribution', 'table-distribution', 'noresults-distribution', 'count-distribution');
    initLiveSearch('search-returns',      'table-returns',       'noresults-returns',       'count-returns');
    initLiveSearch('search-stockin',      'table-stockin',       'noresults-stockin',       'count-stockin');
    initLiveSearch('search-all',          'table-all',           'noresults-all',           'count-all');

    /* ================================================================
       CHARTS  –  getContext('2d') called only after null-guard
    ================================================================ */

    // Chart 1 – IN vs OUT Pie
    var inOutCanvas = document.getElementById('inOutChart');
    if (inOutCanvas) {
        window.inOutChart = new Chart(inOutCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($inOutChart['labels']) !!},
                datasets: [{
                    data: {!! json_encode($inOutChart['data']) !!},
                    backgroundColor: {!! json_encode($inOutChart['colors']) !!},
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Chart 2 – Department Consumption Pie
    var deptCanvas = document.getElementById('deptChart');
    if (deptCanvas) {
        window.deptChart = new Chart(deptCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($deptConsumption['labels']) !!},
                datasets: [{
                    data: {!! json_encode($deptConsumption['data']) !!},
                    backgroundColor: {!! json_encode($deptConsumption['colors']) !!},
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Chart 3 – Weekly Trend Line
    var weeklyCanvas = document.getElementById('weeklyChart');
    if (weeklyCanvas) {
        window.weeklyChart = new Chart(weeklyCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($weeklyTrend['labels']) !!},
                datasets: [
                    {
                        label: 'Stock IN',
                        data: {!! json_encode($weeklyTrend['in']) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Stock OUT',
                        data: {!! json_encode($weeklyTrend['out']) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top' } }
            }
        });
    }

    // Chart 4 – Top 5 Items Bar
    var topItemsCanvas = document.getElementById('topItemsChart');
    if (topItemsCanvas) {
        window.topItemsChart = new Chart(topItemsCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($topItemsChart['labels']) !!},
                datasets: [{
                    label: 'Units Moved',
                    data: {!! json_encode($topItemsChart['data']) !!},
                    backgroundColor: {!! json_encode($topItemsChart['colors']) !!},
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } }
            }
        });
    }

    // Chart 5 – Stock IN Source Breakdown Pie
    var sourceCanvas = document.getElementById('sourceChart');
    if (sourceCanvas) {
        window.sourceChart = new Chart(sourceCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($sourceChart['labels']) !!},
                datasets: [{
                    data: {!! json_encode($sourceChart['data']) !!},
                    backgroundColor: {!! json_encode($sourceChart['colors']) !!},
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

});
</script>
@endsection
