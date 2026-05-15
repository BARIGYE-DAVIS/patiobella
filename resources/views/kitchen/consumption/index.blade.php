{{-- resources/views/kitchen/consumption/index.blade.php --}}

@extends('layouts.kitchen')

@section('title', 'Record Consumption')
@section('page-title', 'Record Consumption')

@section('content')

<style>
    :root {
        --orange: #ea580c;
        --orange-dark: #c2410c;
        --orange-light: #fff7ed;
        --border: #e5e7eb;
        --surface: #f8fafc;
        --text-muted: #6b7280;
        --text-main: #111827;
        --radius: 12px;
        --radius-sm: 8px;
    }

    /* ── Layout ── */
    .kc-wrap { display: flex; flex-direction: column; gap: 1.25rem; }

    /* ── Header Banner ── */
    .kc-banner {
        background: var(--orange);
        border-radius: var(--radius);
        padding: 1.1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .kc-banner-left h2 {
        color: #fff;
        font-size: 1.15rem;
        font-weight: 600;
        margin: 0;
    }
    .kc-banner-left p {
        color: #fed7aa;
        font-size: 0.78rem;
        margin: 0.2rem 0 0;
    }

    /* ── Live Search ── */
    .kc-search-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: var(--radius-sm);
        padding: 0.4rem 0.85rem;
        min-width: 220px;
    }
    .kc-search-box i { color: #fed7aa; font-size: 0.9rem; }
    .kc-search-box input {
        background: transparent;
        border: none;
        outline: none;
        color: #fff;
        font-size: 0.8rem;
        width: 100%;
    }
    .kc-search-box input::placeholder { color: #fdba74; }

    /* ── Stat Cards ── */
    .kc-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.85rem;
    }
    .kc-stat {
        background: #fff;
        border-radius: var(--radius);
        padding: 1rem 1.15rem;
        border: 1px solid var(--border);
        border-left: 4px solid;
    }
    .kc-stat.blue  { border-left-color: #3b82f6; }
    .kc-stat.amber { border-left-color: #f59e0b; }
    .kc-stat.purple{ border-left-color: #8b5cf6; }
    .kc-stat label {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
    }
    .kc-stat .val {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1;
    }
    .kc-stat .sub {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
    }

    /* ── Filter Toolbar ── */
    .kc-toolbar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .kc-filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border: 1px solid var(--border);
        background: #fff;
        border-radius: 20px;
        padding: 0.28rem 0.85rem;
        font-size: 0.75rem;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s;
    }
    .kc-filter-btn:hover { border-color: var(--orange); color: var(--orange); }
    .kc-filter-btn.active { background: var(--orange); color: #fff; border-color: var(--orange); }
    .kc-result-count {
        margin-left: auto;
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    /* ── Requisition Card ── */
    .kc-req-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    .kc-req-card:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.08); }

    .kc-req-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.6rem;
        padding: 0.85rem 1.25rem;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
    }
    .kc-req-head-left .req-num {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-main);
    }
    .kc-req-head-left .req-date {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    /* ── Action Buttons ── */
    .btn-record {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: var(--orange);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        padding: 0.35rem 0.9rem;
        font-size: 0.72rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-record:hover { background: var(--orange-dark); color: #fff; }
    .badge-done {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: #d1fae5;
        color: #065f46;
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        font-size: 0.72rem;
        font-weight: 500;
    }

    /* ── Progress Bar ── */
    .kc-prog {
        padding: 0.7rem 1.25rem;
        border-bottom: 1px solid var(--border);
    }
    .kc-prog-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-bottom: 0.3rem;
    }
    .kc-prog-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
    }
    .kc-prog-fill {
        height: 100%;
        border-radius: 10px;
        background: #f59e0b;
    }

    /* ── Data Table ── */
    .kc-table-wrap { overflow-x: auto; }
    .kc-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
        table-layout: fixed;
    }
    .kc-table thead th {
        background: var(--surface);
        padding: 0.6rem 0.9rem;
        text-align: left;
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
        border-bottom: 2px solid var(--border);
        white-space: nowrap;
    }
    .kc-table thead th.tr { text-align: right; }
    .kc-table tbody td {
        padding: 0.65rem 0.9rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-main);
        vertical-align: middle;
    }
    .kc-table tbody tr:last-child td { border-bottom: none; }
    .kc-table tbody tr:hover td { background: #fafafa; }
    .kc-table td.tr { text-align: right; }

    .item-name {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 500;
    }
    .item-icon {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: var(--orange-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--orange);
        font-size: 0.65rem;
        flex-shrink: 0;
    }
    .consumed-val { color: var(--orange); font-weight: 600; }
    .rem-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        border-radius: 20px;
        padding: 0.15rem 0.55rem;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .rem-high   { background: #d1fae5; color: #065f46; }
    .rem-medium { background: #fef3c7; color: #92400e; }
    .rem-low    { background: #fee2e2; color: #991b1b; }

    /* ── Column widths ── */
    .col-item { width: 32%; }
    .col-unit { width: 12%; }
    .col-num  { width: 14%; }

    /* ── Empty State ── */
    .kc-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }
    .kc-empty i { font-size: 2.8rem; display: block; margin-bottom: 0.75rem; color: #d1d5db; }
    .kc-empty h3 { font-size: 1rem; color: #374151; margin-bottom: 0.3rem; }
    .kc-empty p  { font-size: 0.8rem; }

    /* ── Hidden util ── */
    .kc-hidden { display: none !important; }
</style>

<div class="kc-wrap">

    {{-- ── Banner + Search ── --}}
    <div class="kc-banner">
        <div class="kc-banner-left">
            <h2><i class="fas fa-fire me-2"></i>Kitchen Consumption</h2>
            <p>Record items used in food preparation</p>
        </div>
        <div class="kc-search-box">
            <i class="fas fa-search"></i>
            <input type="text"
                   id="kcSearch"
                   placeholder="Search items or requisitions…"
                   oninput="kcFilter()">
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    @php
        $totalIssued    = 0;
        $totalConsumed  = 0;
        $totalRemaining = 0;
        foreach ($requisitions as $req) {
            foreach ($req->items as $item) {
                $totalIssued    += $item->quantity_issued;
                $totalConsumed  += $item->quantity_consumed;
                $totalRemaining += ($item->quantity_issued - $item->quantity_consumed);
            }
        }
    @endphp

    <div class="kc-stats">
        <div class="kc-stat blue">
            <label><i class="fas fa-boxes"></i> Total Issued</label>
            <div class="val">{{ number_format($totalIssued, 2) }}</div>
            <div class="sub">units across all requisitions</div>
        </div>
        <div class="kc-stat amber">
            <label><i class="fas fa-fire-alt"></i> Total Consumed</label>
            <div class="val">{{ number_format($totalConsumed, 2) }}</div>
            <div class="sub">units used in preparation</div>
        </div>
        <div class="kc-stat purple">
            <label><i class="fas fa-box-open"></i> Remaining Stock</label>
            <div class="val">{{ number_format($totalRemaining, 2) }}</div>
            <div class="sub">units still available</div>
        </div>
    </div>

    {{-- ── Filter Toolbar ── --}}
    <div class="kc-toolbar">
        <button class="kc-filter-btn active" id="fb-all"    onclick="kcSetFilter('all',    this)">
            <i class="fas fa-list"></i> All
        </button>
        <button class="kc-filter-btn"        id="fb-active" onclick="kcSetFilter('active', this)">
            <i class="fas fa-clock"></i> Active
        </button>
        <button class="kc-filter-btn"        id="fb-done"   onclick="kcSetFilter('done',   this)">
            <i class="fas fa-check-circle"></i> Fully Used
        </button>
        <span class="kc-result-count" id="kcCount"></span>
    </div>

    {{-- ── Requisition Cards ── --}}
    <div id="kcReqList">

        @forelse ($requisitions as $requisition)
        @php
            $reqIssued    = 0;
            $reqConsumed  = 0;
            $reqRemaining = 0;
            foreach ($requisition->items as $item) {
                $reqIssued    += $item->quantity_issued;
                $reqConsumed  += $item->quantity_consumed;
                $reqRemaining += ($item->quantity_issued - $item->quantity_consumed);
            }
            $pct  = $reqIssued > 0 ? round(($reqConsumed / $reqIssued) * 100, 1) : 0;
            $done = $reqRemaining <= 0;
        @endphp

        <div class="kc-req-card"
             data-status="{{ $done ? 'done' : 'active' }}"
             data-searchable="{{ strtolower($requisition->requisition_number . ' ' . $requisition->items->pluck('inventoryItem.name')->implode(' ')) }}">

            {{-- Head --}}
            <div class="kc-req-head">
                <div class="kc-req-head-left">
                    <div class="req-num">
                        <i class="fas fa-file-alt text-orange-500"></i>
                        {{ $requisition->requisition_number }}
                    </div>
                    <div class="req-date">
                        <i class="fas fa-calendar-alt"></i>
                        Issued on {{ $requisition->created_at->format('d M Y') }}
                    </div>
                </div>
                <div>
                    @if (!$done)
                        <a href="{{ route('kitchen.consumption.create', $requisition->id) }}" class="btn-record">
                            <i class="fas fa-plus-circle"></i> Record Consumption
                        </a>
                    @else
                        <span class="badge-done">
                            <i class="fas fa-check-circle"></i> Fully Used
                        </span>
                    @endif
                </div>
            </div>

            {{-- Progress --}}
            <div class="kc-prog">
                <div class="kc-prog-labels">
                    <span><i class="fas fa-fire-alt me-1" style="color:#f59e0b"></i>
                        Consumed: {{ number_format($reqConsumed, 2) }} units &nbsp;({{ $pct }}%)
                    </span>
                    <span><i class="fas fa-box-open me-1"></i>
                        Remaining: {{ number_format($reqRemaining, 2) }} units
                    </span>
                </div>
                <div class="kc-prog-bar">
                    <div class="kc-prog-fill" style="width:{{ $pct }}%"></div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="kc-table-wrap">
                <table class="kc-table">
                    <thead>
                        <tr>
                            <th class="col-item"><i class="fas fa-carrot me-1"></i> Item</th>
                            <th class="col-unit"><i class="fas fa-ruler me-1"></i> Unit</th>
                            <th class="col-num tr"><i class="fas fa-inbox me-1"></i> Issued</th>
                            <th class="col-num tr"><i class="fas fa-fire-alt me-1"></i> Consumed</th>
                            <th class="col-num tr"><i class="fas fa-layer-group me-1"></i> Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requisition->items as $item)
                        @php
                            $unit      = $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units');
                            $remaining = $item->quantity_issued - $item->quantity_consumed;
                            $remClass  = $remaining > 10 ? 'rem-high' : ($remaining > 0 ? 'rem-medium' : 'rem-low');
                            $remIcon   = $remaining > 10 ? 'fa-check'   : ($remaining > 0 ? 'fa-exclamation' : 'fa-times');
                        @endphp
                        <tr>
                            <td>
                                <div class="item-name">
                                    <span class="item-icon"><i class="fas fa-utensils"></i></span>
                                    {{ $item->inventoryItem->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td style="color:#6b7280">{{ $unit }}</td>
                            <td class="tr">{{ number_format($item->quantity_issued,  2) }}</td>
                            <td class="tr consumed-val">{{ number_format($item->quantity_consumed, 2) }}</td>
                            <td class="tr">
                                <span class="rem-badge {{ $remClass }}">
                                    <i class="fas {{ $remIcon }}"></i>
                                    {{ number_format($remaining, 2) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>{{-- /.kc-req-card --}}

        @empty
        <div class="kc-empty">
            <i class="fas fa-box-open"></i>
            <h3>No Active Requisitions</h3>
            <p>No items have been issued to the kitchen yet.</p>
        </div>
        @endforelse

    </div>{{-- /#kcReqList --}}

</div>{{-- /.kc-wrap --}}

{{-- ── Live Search & Filter Script ── --}}
<script>
(function () {
    var filter = 'all';

    function cards() {
        return document.querySelectorAll('#kcReqList .kc-req-card');
    }

    function update() {
        var q   = (document.getElementById('kcSearch').value || '').toLowerCase().trim();
        var all = cards();
        var vis = 0;

        all.forEach(function (card) {
            var status      = card.dataset.status;
            var searchable  = card.dataset.searchable || '';
            var matchFilter = filter === 'all' || filter === status;
            var matchSearch = !q || searchable.indexOf(q) !== -1;

            if (matchFilter && matchSearch) {
                card.classList.remove('kc-hidden');
                vis++;
            } else {
                card.classList.add('kc-hidden');
            }
        });

        var countEl = document.getElementById('kcCount');
        if (countEl) {
            countEl.textContent = vis + ' requisition' + (vis !== 1 ? 's' : '') + ' shown';
        }
    }

    window.kcFilter = update;

    window.kcSetFilter = function (f, btn) {
        filter = f;
        document.querySelectorAll('.kc-filter-btn').forEach(function (b) {
            b.classList.remove('active');
        });
        if (btn) btn.classList.add('active');
        update();
    };

    /* initialise count on page load */
    document.addEventListener('DOMContentLoaded', update);
})();
</script>

@endsection
