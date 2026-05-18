{{-- resources/views/bar/cashier/my-sales.blade.php --}}

@extends('layouts.bar-cashier')

@section('title', 'My Sales')

@section('page-title', 'My Sales Performance')

@section('content')
<style>
    /* ============================================
       STAT CARDS
    ============================================ */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        transition: all 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        margin-top: 0.5rem;
    }
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #6b7280;
    }

    /* ============================================
       FILTER BAR
    ============================================ */
    .filter-bar {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }

    /* ============================================
       TABLES
    ============================================ */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
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
        background: #fef3c7;
    }

    /* ============================================
       TEXT ALIGNMENT
    ============================================ */
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    /* ============================================
       FORM ELEMENTS
    ============================================ */
    .search-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 250px;
    }

    /* ============================================
       BUTTONS
    ============================================ */
    .btn-export {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-export:hover {
        background: #059669;
    }

    /* ============================================
       BADGES
    ============================================ */
    .badge-cash {
        background: #d1fae5;
        color: #065f46;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
    .badge-card {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
    .badge-mobile {
        background: #fef3c7;
        color: #92400e;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
</style>

<div class="space-y-6">

    {{-- ============================================
         HEADER SECTION
    ============================================ --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-chart-line mr-2"></i>
                    My Sales Report
                </h2>
                <p class="text-blue-100 mt-1">Track your sales performance</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-user mr-1"></i> Cashier: {{ Auth::user()->first_name ?? 'N/A' }}</p>
                <p class="text-lg font-bold">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================
         STATISTICS CARDS
    ============================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-label"><i class="fas fa-dollar-sign mr-1"></i> Total Sales</div>
            <div class="stat-value text-green-600">UGX {{ number_format($stats['total_sales'], 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-label"><i class="fas fa-receipt mr-1"></i> Orders</div>
            <div class="stat-value text-blue-600">{{ number_format($stats['total_orders']) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-label"><i class="fas fa-chart-line mr-1"></i> Average Order</div>
            <div class="stat-value text-orange-600">UGX {{ number_format($stats['avg_order'], 0) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-label"><i class="fas fa-boxes mr-1"></i> Items Sold</div>
            <div class="stat-value text-red-600">{{ number_format($stats['total_items']) }}</div>
        </div>
    </div>

    {{-- ============================================
         FILTERS SECTION
    ============================================ --}}
    <div class="filter-bar">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <form method="GET" action="{{ route('bar.cashier.my-sales') }}" class="flex gap-3 flex-wrap">
                <select name="period" id="periodSelect" class="p-2 border rounded-lg text-sm">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $period == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="this_week" {{ $period == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom</option>
                </select>
                <div id="customDateRange" style="display: {{ request('start_date') ? 'flex' : 'none' }}; gap: 8px;">
                    <input type="date" name="start_date" class="p-2 border rounded-lg text-sm" value="{{ request('start_date', $from) }}">
                    <input type="date" name="end_date" class="p-2 border rounded-lg text-sm" value="{{ request('end_date', $to) }}">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-sync-alt mr-1"></i> Apply
                </button>
                <a href="{{ route('bar.cashier.my-sales') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            </form>

            <div class="flex gap-2">
                <input type="text" id="liveSearch" class="search-input" placeholder="🔍 Search by invoice or item..." value="{{ $search ?? '' }}">
                <button onclick="openExportModal()" class="btn-export">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================
         SALES TABLE
    ============================================ --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Invoice #</th>
                        <th class="text-left">Date & Time</th>
                        <th class="text-left">Customer Type</th>
                        <th class="text-left">Items</th>
                        <th class="text-left">Payment Method</th>
                        <th class="text-right">Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="salesTableBody">
                    @forelse($orders as $order)
                    <tr class="sale-row">
                        <td class="text-left font-mono text-xs font-bold">{{ $order->order_number }}</td>
                        <td class="text-left text-xs">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="text-left text-xs">{{ ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')) }}</td>
                        <td class="text-left text-xs">
                            @foreach($order->items->take(3) as $item)
                                <span class="inline-block bg-gray-100 rounded px-1 py-0.5 text-xs mr-1 mb-1">{{ \Str::limit($item->item_name, 20) }}</span>
                            @endforeach
                            @if($order->items->count() > 3)
                                <span class="text-gray-400 text-xs">+{{ $order->items->count() - 3 }} more</span>
                            @endif
                        </td>
                        <td class="text-left text-xs">
                            @if($order->payment_method == 'cash')
                                <span class="badge-cash">💵 Cash</span>
                            @elseif($order->payment_method == 'card')
                                <span class="badge-card">💳 Card</span>
                            @elseif($order->payment_method == 'mobile_money')
                                <span class="badge-mobile">📱 Mobile Money</span>
                            @else
                                {{ ucfirst($order->payment_method ?? 'N/A') }}
                            @endif
                        </td>
                        <td class="text-right font-semibold">UGX {{ number_format($order->total_amount, 0) }}</td>
                        <td class="text-center">
                            <a href="{{ route('bar.cashier.receipt', $order->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-eye mr-1"></i> View Receipt
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr class="no-results-row">
                        <td colspan="7" class="text-center py-8 text-gray-400">
                            <i class="fas fa-receipt text-4xl mb-2 block"></i>
                            No sales found for this period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="p-4 border-t border-gray-200" id="paginationLinks">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ============================================
     EXPORT MODAL
============================================ --}}
<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl w-96 max-w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">
                <i class="fas fa-download text-green-600 mr-2"></i> Export My Sales
            </h3>
            <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Export Type</label>
                <select id="exportType" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="all">All My Sales (No Date Filter)</option>
                    <option value="current">Current Filtered Period</option>
                    <option value="custom">Custom Date Range</option>
                </select>
            </div>

            <div id="customDateDiv" style="display: none;">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Start Date</label>
                        <input type="date" id="exportStartDate" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">End Date</label>
                        <input type="date" id="exportEndDate" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="exportMySalesExcel()" class="bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i> Excel
                </button>
                <button onclick="exportMySalesPdf()" class="bg-red-600 text-white py-2 rounded-lg font-semibold hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     SCRIPTS
============================================ --}}
<script>
    // ============================================
    // EXPORT MODAL FUNCTIONS
    // ============================================
    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    const exportTypeSelect = document.getElementById('exportType');
    if (exportTypeSelect) {
        exportTypeSelect.addEventListener('change', function() {
            const customDiv = document.getElementById('customDateDiv');
            if (customDiv) {
                customDiv.style.display = this.value === 'custom' ? 'block' : 'none';
            }
        });
    }

    function exportMySalesExcel() {
        const exportType = document.getElementById('exportType').value;
        let url = '{{ route("bar.cashier.my-sales.export.excel") }}?export_type=' + exportType;

        if (exportType === 'custom') {
            url += '&start_date=' + document.getElementById('exportStartDate').value;
            url += '&end_date=' + document.getElementById('exportEndDate').value;
        } else if (exportType === 'current') {
            url += '&start_date={{ $from }}&end_date={{ $to }}';
            @if(request('search'))
                url += '&search={{ request("search") }}';
            @endif
        }

        window.location.href = url;
        closeExportModal();
    }

    function exportMySalesPdf() {
        const exportType = document.getElementById('exportType').value;
        let url = '{{ route("bar.cashier.my-sales.export.pdf") }}?export_type=' + exportType;

        if (exportType === 'custom') {
            url += '&start_date=' + document.getElementById('exportStartDate').value;
            url += '&end_date=' + document.getElementById('exportEndDate').value;
        } else if (exportType === 'current') {
            url += '&start_date={{ $from }}&end_date={{ $to }}';
            @if(request('search'))
                url += '&search={{ request("search") }}';
            @endif
        }

        window.location.href = url;
        closeExportModal();
    }

    // ============================================
    // SEARCH FUNCTIONALITY
    // ============================================
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const tbody = document.getElementById('salesTableBody');
    const paginationDiv = document.getElementById('paginationLinks');
    let currentPage = 1;

    function performSearch() {
        if (!tbody) return;

        const searchTerm = searchInput ? searchInput.value : '';

        let url = '{{ route("bar.cashier.my-sales") }}';
        let params = new URLSearchParams();

        params.append('search', searchTerm);
        params.append('period', '{{ $period }}');

        @if(request('start_date'))
            params.append('start_date', '{{ request("start_date") }}');
        @endif
        @if(request('end_date'))
            params.append('end_date', '{{ request("end_date") }}');
        @endif

        params.append('page', currentPage);

        fetch(url + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                tbody.innerHTML = data.html;
            }
            if (data.pagination && paginationDiv) {
                paginationDiv.innerHTML = data.pagination;
            }
            if (data.stats) {
                const statCards = document.querySelectorAll('.stat-value');
                if (statCards.length >= 4) {
                    statCards[0].innerText = 'UGX ' + data.stats.total_sales.toLocaleString();
                    statCards[1].innerText = data.stats.total_orders.toLocaleString();
                    statCards[2].innerText = 'UGX ' + Math.round(data.stats.avg_order).toLocaleString();
                    statCards[3].innerText = data.stats.total_items.toLocaleString();
                }
            }

            const url = new URL(window.location.href);
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            } else {
                url.searchParams.delete('search');
            }
            window.history.pushState({}, '', url);
        })
        .catch(error => console.error('Search error:', error));
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
    }

    // ============================================
    // PERIOD SELECT TOGGLE
    // ============================================
    const periodSelect = document.getElementById('periodSelect');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            const customDiv = document.getElementById('customDateRange');
            if (customDiv) {
                customDiv.style.display = this.value === 'custom' ? 'flex' : 'none';
            }
        });
    }

    // ============================================
    // PAGINATION HANDLER
    // ============================================
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            const pageUrl = paginationLink.getAttribute('href');
            if (pageUrl) {
                const pageMatch = pageUrl.match(/[?&]page=(\d+)/);
                if (pageMatch) {
                    currentPage = pageMatch[1];
                    performSearch();
                }
            }
        }
    });
</script>
@endsection
