{{-- resources/views/restaurant/cashier/my-sales.blade.php --}}

@extends('layouts.cashier')

@section('title', 'My Sales')

@section('page-title', 'My Sales Performance')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    * { font-family: 'DM Sans', sans-serif; }

    /* ── Hero Banner ── */
    .hero-banner {
        background: linear-gradient(135deg, #c2410c 0%, #ea580c 50%, #f97316 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .hero-banner::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .hero-banner::after {
        content: '';
        position: absolute;
        bottom: -60px; left: 30%;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .hero-title {
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: -0.3px;
        margin: 0 0 0.25rem;
    }
    .hero-sub {
        font-size: 0.82rem;
        opacity: 0.8;
        margin: 0;
    }
    .hero-meta {
        text-align: right;
    }
    .hero-meta .cashier-name {
        font-size: 0.8rem;
        opacity: 0.85;
        margin-bottom: 0.25rem;
    }
    .hero-meta .date-range {
        font-size: 1rem;
        font-weight: 600;
    }

    /* ── Stat Cards ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 500px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        border-top: 3px solid transparent;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    .stat-card.green  { border-top-color: #10b981; }
    .stat-card.blue   { border-top-color: #3b82f6; }
    .stat-card.orange { border-top-color: #f59e0b; }
    .stat-card.red    { border-top-color: #ef4444; }

    .stat-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
        margin-bottom: 0.1rem;
    }
    .stat-card.green  .stat-icon { background: #d1fae5; color: #059669; }
    .stat-card.blue   .stat-icon { background: #dbeafe; color: #2563eb; }
    .stat-card.orange .stat-icon { background: #fef3c7; color: #d97706; }
    .stat-card.red    .stat-icon { background: #fee2e2; color: #dc2626; }

    .stat-label {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #9ca3af;
    }
    .stat-value {
        font-size: 1.45rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    .stat-card.green  .stat-value { color: #059669; }
    .stat-card.blue   .stat-value { color: #2563eb; }
    .stat-card.orange .stat-value { color: #d97706; }
    .stat-card.red    .stat-value { color: #dc2626; }

    /* ── Filter Bar ── */
    .filter-bar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 1rem 1.25rem;
    }
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        justify-content: space-between;
    }
    .filter-left, .filter-right {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        align-items: center;
    }
    .filter-select, .filter-date {
        padding: 0.45rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.78rem;
        color: #374151;
        background: #f9fafb;
        outline: none;
        transition: border-color 0.15s;
    }
    .filter-select:focus, .filter-date:focus { border-color: #f97316; }

    .btn { border: none; cursor: pointer; border-radius: 8px; font-size: 0.78rem; font-weight: 600; padding: 0.45rem 1rem; transition: background 0.15s; }
    .btn-primary   { background: #ea580c; color: #fff; }
    .btn-primary:hover { background: #c2410c; }
    .btn-secondary { background: #f3f4f6; color: #374151; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-export { background: #10b981; color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
    .btn-export:hover { background: #059669; color: #fff; }
    .btn-export.pdf { background: #ef4444; }
    .btn-export.pdf:hover { background: #dc2626; }

    .search-wrap { position: relative; }
    .search-wrap .search-icon {
        position: absolute; left: 0.65rem; top: 50%; transform: translateY(-50%);
        color: #9ca3af; font-size: 0.75rem; pointer-events: none;
    }
    .search-input {
        padding: 0.45rem 0.75rem 0.45rem 2rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.78rem;
        width: 230px;
        background: #f9fafb;
        outline: none;
        transition: border-color 0.15s;
    }
    .search-input:focus { border-color: #f97316; }

    /* ── Table ── */
    .table-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .table-card .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .table-header-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #111827;
    }
    .table-header-count {
        font-size: 0.75rem;
        color: #6b7280;
        background: #f3f4f6;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.7rem 1rem;
        text-align: left;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .data-table th.text-right { text-align: right; }
    .data-table th.text-center { text-align: center; }

    .data-table td {
        padding: 0.8rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #374151;
    }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #fff7ed; }

    .invoice-num {
        font-family: 'DM Mono', monospace;
        font-size: 0.72rem;
        font-weight: 500;
        color: #1e293b;
        background: #f1f5f9;
        padding: 0.2rem 0.5rem;
        border-radius: 5px;
        white-space: nowrap;
    }
    .date-primary { font-size: 0.78rem; font-weight: 500; color: #1e293b; }
    .date-secondary { font-size: 0.68rem; color: #9ca3af; margin-top: 1px; }

    .item-chip {
        display: inline-block;
        background: #f1f5f9;
        color: #475569;
        border-radius: 5px;
        padding: 0.15rem 0.5rem;
        font-size: 0.7rem;
        margin: 0.1rem 0.1rem 0.1rem 0;
        white-space: nowrap;
        max-width: 110px;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }
    .item-more { font-size: 0.68rem; color: #94a3b8; }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.22rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .badge-cash   { background: #d1fae5; color: #065f46; }
    .badge-card   { background: #dbeafe; color: #1e40af; }
    .badge-mobile { background: #fef3c7; color: #92400e; }
    .badge-other  { background: #f3f4f6; color: #374151; }

    .amount-cell { text-align: right; font-weight: 700; font-size: 0.82rem; color: #1e293b; white-space: nowrap; }
    .action-link { color: #ea580c; text-decoration: none; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.6rem; border-radius: 6px; transition: background 0.15s; }
    .action-link:hover { background: #fff7ed; color: #c2410c; }

    .empty-state { text-align: center; padding: 3.5rem 1rem; color: #9ca3af; }
    .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4; }
    .empty-state p { font-size: 0.85rem; margin: 0; }

    .pagination-wrap { padding: 0.9rem 1.25rem; border-top: 1px solid #f1f5f9; }

    /* Customer type pill */
    .ctype { font-size: 0.72rem; color: #6b7280; }
</style>

<div class="space-y-5">

    {{-- ── Hero Banner ── --}}
    <div class="hero-banner">
        <div style="display:flex; justify-content:space-between; align-items:center; position:relative; z-index:1;">
            <div>
                <h2 class="hero-title"><i class="fas fa-chart-line" style="margin-right:0.5rem; opacity:0.85;"></i>My Sales Report</h2>
                <p class="hero-sub">Track and review your personal sales performance</p>
            </div>
            <div class="hero-meta">
                <p class="cashier-name"><i class="fas fa-user" style="margin-right:0.3rem;"></i>{{ Auth::user()->first_name ?? 'N/A' }}</p>
                <p class="date-range">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="stats-grid">
        <div class="stat-card green">
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-label">Total Sales</div>
            <div class="stat-value">UGX {{ number_format($stats['total_sales'], 0) }}</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fas fa-receipt"></i></div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
            <div class="stat-label">Average Order</div>
            <div class="stat-value">UGX {{ number_format($stats['avg_order'], 0) }}</div>
        </div>
        <div class="stat-card red">
            <div class="stat-icon"><i class="fas fa-boxes"></i></div>
            <div class="stat-label">Items Sold</div>
            <div class="stat-value">{{ number_format($stats['total_items']) }}</div>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar">
        <div class="filter-row">
            {{-- Left: period + dates + apply/reset --}}
            <form method="GET" action="{{ route('restaurant.cashier.my-sales') }}" class="filter-left">
                <select name="period" id="periodSelect" class="filter-select">
                    <option value="today"      {{ $period == 'today'      ? 'selected' : '' }}>Today</option>
                    <option value="yesterday"  {{ $period == 'yesterday'  ? 'selected' : '' }}>Yesterday</option>
                    <option value="this_week"  {{ $period == 'this_week'  ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="custom"     {{ request('start_date')   ? 'selected' : '' }}>Custom Range</option>
                </select>

                <div id="customDateRange" style="display:{{ request('start_date') ? 'flex' : 'none' }}; gap:0.5rem; align-items:center;">
                    <input type="date" name="start_date" class="filter-date" value="{{ request('start_date', $from) }}">
                    <span style="color:#9ca3af; font-size:0.75rem;">to</span>
                    <input type="date" name="end_date"   class="filter-date" value="{{ request('end_date',   $to) }}">
                </div>

                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{ route('restaurant.cashier.my-sales') }}" class="btn btn-secondary">Reset</a>
            </form>

            {{-- Right: search + exports --}}
            <div class="filter-right">
                <div class="search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="liveSearch" class="search-input" placeholder="Search invoice or item…" value="{{ $search }}">
                </div>
                <a href="{{ route('restaurant.cashier.my-sales.export.excel') }}" class="btn btn-export">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('restaurant.cashier.my-sales.export.pdf') }}" class="btn btn-export pdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ── Sales Table ── --}}
    <div class="table-card">
        <div class="table-header">
            <span class="table-header-title">Sales Transactions</span>
            <span class="table-header-count">{{ $orders->total() }} record{{ $orders->total() != 1 ? 's' : '' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date &amp; Time</th>
                        <th>Customer Type</th>
                        <th>Items</th>
                        <th>Payment</th>
                        <th class="text-right">Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="salesTableBody">
                    @forelse($orders as $order)
                    <tr class="sale-row">
                        <td><span class="invoice-num">{{ $order->order_number }}</span></td>
                        <td>
                            <div class="date-primary">{{ $order->created_at->format('d M Y') }}</div>
                            <div class="date-secondary">{{ $order->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="ctype">{{ ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')) }}</td>
                        <td>
                            @foreach($order->items->take(3) as $item)
                                <span class="item-chip" title="{{ $item->item_name }}">{{ \Str::limit($item->item_name, 18) }}</span>
                            @endforeach
                            @if($order->items->count() > 3)
                                <span class="item-more">+{{ $order->items->count() - 3 }} more</span>
                            @endif
                        </td>
                        <td>
                            @if($order->payment_method == 'cash')
                                <span class="badge badge-cash"><i class="fas fa-money-bill-wave"></i> Cash</span>
                            @elseif($order->payment_method == 'card')
                                <span class="badge badge-card"><i class="fas fa-credit-card"></i> Card</span>
                            @elseif($order->payment_method == 'mobile_money')
                                <span class="badge badge-mobile"><i class="fas fa-mobile-alt"></i> Mobile Money</span>
                            @else
                                <span class="badge badge-other">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                            @endif
                        </td>
                        <td class="amount-cell">UGX {{ number_format($order->total_amount, 0) }}</td>
                        <td style="text-align:center;">
                            <a href="{{ route('restaurant.cashier.receipt', $order->id) }}" class="action-link">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-receipt"></i>
                                <p>No sales found for this period.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap" id="paginationLinks">
            {{ $orders->appends(['period' => $period, 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'search' => $search])->links() }}
        </div>
    </div>

</div>

<script>
    // ── Live Search ──
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const tbody       = document.getElementById('salesTableBody');
    const paginationDiv = document.getElementById('paginationLinks');
    let currentPage = 1;

    function performSearch() {
        const searchTerm = searchInput.value;
        let url = '{{ route("restaurant.cashier.my-sales") }}?search=' + encodeURIComponent(searchTerm);
        url += '&period={{ $period }}';
        @if(request('start_date')) url += '&start_date={{ request("start_date") }}'; @endif
        @if(request('end_date'))   url += '&end_date={{ request("end_date") }}';     @endif
        url += '&page=' + currentPage;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.html)       tbody.innerHTML = data.html;
                if (data.pagination) paginationDiv.innerHTML = data.pagination;
                if (data.stats) {
                    const cards = document.querySelectorAll('.stat-value');
                    if (cards.length >= 4) {
                        cards[0].innerText = 'UGX ' + data.stats.total_sales.toLocaleString();
                        cards[1].innerText = data.stats.total_orders.toLocaleString();
                        cards[2].innerText = 'UGX ' + Math.round(data.stats.avg_order).toLocaleString();
                        cards[3].innerText = data.stats.total_items.toLocaleString();
                    }
                }
                const u = new URL(window.location.href);
                searchTerm ? u.searchParams.set('search', searchTerm) : u.searchParams.delete('search');
                window.history.pushState({}, '', u);
            })
            .catch(e => console.error('Search error:', e));
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            currentPage = 1;
            searchTimeout = setTimeout(performSearch, 500);
        });
    }

    // ── Period select toggle ──
    const periodSelect = document.getElementById('periodSelect');
    if (periodSelect) {
        periodSelect.addEventListener('change', function () {
            const customDiv = document.getElementById('customDateRange');
            if (customDiv) customDiv.style.display = this.value === 'custom' ? 'flex' : 'none';
        });
    }

    // ── Pagination via AJAX ──
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();
            const pageMatch = link.getAttribute('href').match(/[?&]page=(\d+)/);
            if (pageMatch) { currentPage = pageMatch[1]; performSearch(); }
        }
    });
</script>
@endsection
