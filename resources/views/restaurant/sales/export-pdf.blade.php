{{-- resources/views/restaurant/sales/export-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Report {{ $from }} to {{ $to }}</title>
<style>
    /* DomPDF-safe CSS — table-based layout, no flex/grid */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 9.5px;
        color: #1f2937;
        background: #fff;
        padding: 18px 22px;
    }

    /* ── STAT CARDS ── */
    .stats-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 14px; }
    .stat-cell {
        width: 25%;
        padding: 9px 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        vertical-align: middle;
    }
    .stat-cell.c-green  { border-left: 4px solid #10b981; }
    .stat-cell.c-blue   { border-left: 4px solid #3b82f6; }
    .stat-cell.c-amber  { border-left: 4px solid #f59e0b; }
    .stat-cell.c-red    { border-left: 4px solid #ef4444; }
    .slabel { font-size: 7.5px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 3px; }
    .svalue { font-size: 13px; font-weight: 700; }
    .svalue.green  { color: #059669; }
    .svalue.blue   { color: #2563eb; }
    .svalue.amber  { color: #d97706; }
    .svalue.red    { color: #dc2626; }

    /* ── PAYMENT BREAKDOWN ── */
    .pay-row { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 14px; }
    .pay-cell {
        padding: 7px 12px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 5px;
        text-align: center;
        font-size: 8.5px;
        color: #065f46;
    }
    .pay-cell.card-c   { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
    .pay-cell.mobile-c { background: #fffbeb; border-color: #fde68a; color: #92400e; }
    .pay-cell.menu-c   { background: #f5f3ff; border-color: #ddd6fe; color: #5b21b6; }
    .pay-cell.other-c  { background: #fff7ed; border-color: #fed7aa; color: #9a3412; }
    .pay-amount { font-size: 11px; font-weight: 700; display: block; margin-top: 2px; }

    /* ── SECTION HEADINGS ── */
    .sec-head {
        font-size: 11px;
        font-weight: 700;
        color: #ffffff;
        background-color: #ea580c;
        padding: 6px 10px;
        border-radius: 4px;
        margin-top: 16px;
        margin-bottom: 4px;
    }
    .sec-head.green  { background-color: #059669; }
    .sec-head.gray   { background-color: #4b5563; }
    .sec-head.blue   { background-color: #2563eb; }

    /* ── DATA TABLES ── */
    table.data {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5px;
        margin-bottom: 6px;
    }
    table.data thead th {
        padding: 6px 8px;
        text-align: left;
        font-weight: 700;
        font-size: 7.5px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #374151;
        background: #f3f4f6;
        border-bottom: 2px solid #d1d5db;
        white-space: nowrap;
    }
    table.data thead th.tr { text-align: right; }
    table.data thead th.tc { text-align: center; }
    table.data tbody td {
        padding: 5px 8px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }
    table.data tbody tr:nth-child(even) td { background: #fafafa; }
    table.data tbody tr.menu-row:nth-child(even) td  { background: #f0fdf4; }
    table.data tbody tr.other-row:nth-child(even) td { background: #fffbeb; }
    table.data tfoot td {
        padding: 6px 8px;
        font-weight: 700;
        font-size: 9px;
        border-top: 2px solid #d1d5db;
        background: #f9fafb;
    }

    .tr   { text-align: right; }
    .tc   { text-align: center; }
    .fw   { font-weight: 700; }
    .mono { font-family: 'Courier New', monospace; font-weight: 700; font-size: 8px; }
    .muted { color: #6b7280; font-size: 8px; }
    .nw   { white-space: nowrap; }

    /* item tag */
    .itag {
        display: inline-block;
        margin: 1px 2px 1px 0;
        padding: 1px 5px;
        border-radius: 4px;
        font-size: 7.5px;
    }
    .itag-menu  { background: #d1fae5; color: #065f46; }
    .itag-other { background: #fef3c7; color: #92400e; }

    /* payment pill */
    .ppill { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 7.5px; font-weight: 600; }
    .p-cash   { background: #d1fae5; color: #065f46; }
    .p-card   { background: #dbeafe; color: #1e40af; }
    .p-mobile { background: #fef3c7; color: #92400e; }

    /* rank circle */
    .rank { display: inline-block; width: 16px; height: 16px; border-radius: 50%;
            background: #ea580c; color: #fff; text-align: center; line-height: 16px;
            font-size: 7.5px; font-weight: 700; }
    .rank.g { background: #059669; }
    .rank.gr { background: #6b7280; }

    /* page breaks */
    .page-break { page-break-before: always; }
    .no-break   { page-break-inside: avoid; }

    /* footer */
    .footer {
        margin-top: 20px;
        padding-top: 8px;
        border-top: 1px solid #e5e7eb;
        text-align: center;
        color: #9ca3af;
        font-size: 7.5px;
    }

    .gap { height: 10px; }
    .gap-sm { height: 5px; }
</style>
</head>
<body>

@php
    use Carbon\Carbon;
    $mnl = $menuItemNames ?? [];

    // Categorise orders
    $menuOrders  = $salesList->filter(fn($s) => $s->items->filter(fn($i) =>  in_array($i->item_name, $mnl))->count() > 0)->values();
    $otherOrders = $salesList->filter(fn($s) => $s->items->filter(fn($i) => !in_array($i->item_name, $mnl))->count() > 0)->values();

    $menuRevenue  = $menuOrders->sum('total_amount');
    $otherRevenue = $otherOrders->sum('total_amount');

    $topMenuProds  = $topProducts->filter(fn($p) =>  in_array($p->item_name, $mnl))->values();
    $topOtherProds = $topProducts->filter(fn($p) => !in_array($p->item_name, $mnl))->values();

    $payLabel = ['cash' => 'Cash', 'card' => 'Card', 'mobile_money' => 'Mobile Money'];
    $payCss   = ['cash' => 'p-cash', 'card' => 'p-card', 'mobile_money' => 'p-mobile'];
@endphp


{{-- ══════════════════════════════
     PAGE 1: SUMMARY + TOP PRODUCTS
     ══════════════════════════════ --}}

{{-- HEADER BAR --}}
<table style="width:100%; background:#ea580c; border-radius:6px; margin-bottom:14px;">
    <tr>
        <td style="padding:14px 20px; color:#fff;">
            <div style="font-size:17px; font-weight:700;">Sales Report</div>
            <div style="font-size:9.5px; color:#fed7aa; margin-top:3px;">Restaurant Sales Analytics &amp; Transaction History</div>
        </td>
        <td style="text-align:right; padding:14px 20px; color:#fff; white-space:nowrap; vertical-align:top;">
            <div style="font-size:8px; color:#fed7aa;">Reporting Period</div>
            <div style="font-size:12px; font-weight:700; margin-top:2px;">
                {{ Carbon::parse($from)->format('d M Y') }} &ndash; {{ Carbon::parse($to)->format('d M Y') }}
            </div>
            <div style="font-size:8px; color:#fed7aa; margin-top:3px;">Generated: {{ now()->format('d/m/Y H:i') }}</div>
        </td>
    </tr>
</table>

{{-- STAT CARDS --}}
<table class="stats-table">
    <tr>
        <td class="stat-cell c-green">
            <div class="slabel">Total Revenue</div>
            <div class="svalue green">UGX {{ number_format($salesData['total_sales'], 0) }}</div>
        </td>
        <td class="stat-cell c-blue">
            <div class="slabel">Total Orders</div>
            <div class="svalue blue">{{ number_format($salesData['total_orders']) }}</div>
        </td>
        <td class="stat-cell c-amber">
            <div class="slabel">Avg Order Value</div>
            <div class="svalue amber">UGX {{ number_format($salesData['average_order_value'], 0) }}</div>
        </td>
        <td class="stat-cell c-red">
            <div class="slabel">Items Sold</div>
            <div class="svalue red">{{ number_format($salesData['total_items_sold']) }}</div>
        </td>
    </tr>
</table>

{{-- PAYMENT + CATEGORY BREAKDOWN --}}
<table class="pay-row">
    <tr>
        <td class="pay-cell">
            Cash Sales<span class="pay-amount">UGX {{ number_format($salesData['cash_sales'], 0) }}</span>
        </td>
        <td class="pay-cell card-c">
            Card Sales<span class="pay-amount">UGX {{ number_format($salesData['card_sales'], 0) }}</span>
        </td>
        <td class="pay-cell mobile-c">
            Mobile Money<span class="pay-amount">UGX {{ number_format($salesData['mobile_sales'], 0) }}</span>
        </td>
        <td class="pay-cell menu-c">
            Menu Items Revenue<span class="pay-amount">UGX {{ number_format($menuRevenue, 0) }}</span>
        </td>
        <td class="pay-cell other-c">
            Other Items Revenue<span class="pay-amount">UGX {{ number_format($otherRevenue, 0) }}</span>
        </td>
    </tr>
</table>

{{-- TOP MENU PRODUCTS --}}
<div class="sec-head green">Top Selling Menu Items</div>
@if($topMenuProds->count())
<table class="data">
    <thead>
        <tr>
            <th class="tc" style="width:26px;">#</th>
            <th>Menu Item</th>
            <th class="tr" style="width:70px;">Qty Sold</th>
            <th class="tr" style="width:100px;">Revenue (UGX)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topMenuProds as $i => $p)
        <tr class="menu-row">
            <td class="tc"><span class="rank g">{{ $i + 1 }}</span></td>
            <td>{{ $p->item_name }}</td>
            <td class="tr fw">{{ number_format($p->total_quantity) }}</td>
            <td class="tr fw">{{ number_format($p->total_revenue, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">MENU ITEMS TOTAL</td>
            <td class="tr">{{ number_format($topMenuProds->sum('total_quantity')) }}</td>
            <td class="tr">UGX {{ number_format($topMenuProds->sum('total_revenue'), 0) }}</td>
        </tr>
    </tfoot>
</table>
@else
<p style="color:#9ca3af; font-size:8.5px; padding:8px 0;">No menu item sales in this period.</p>
@endif

{{-- TOP OTHER PRODUCTS --}}
<div class="sec-head gray">Top Selling Other Items</div>
@if($topOtherProds->count())
<table class="data">
    <thead>
        <tr>
            <th class="tc" style="width:26px;">#</th>
            <th>Item</th>
            <th class="tr" style="width:70px;">Qty Sold</th>
            <th class="tr" style="width:100px;">Revenue (UGX)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topOtherProds as $i => $p)
        <tr class="other-row">
            <td class="tc"><span class="rank gr">{{ $i + 1 }}</span></td>
            <td>{{ $p->item_name }}</td>
            <td class="tr fw">{{ number_format($p->total_quantity) }}</td>
            <td class="tr fw">{{ number_format($p->total_revenue, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">OTHER ITEMS TOTAL</td>
            <td class="tr">{{ number_format($topOtherProds->sum('total_quantity')) }}</td>
            <td class="tr">UGX {{ number_format($topOtherProds->sum('total_revenue'), 0) }}</td>
        </tr>
    </tfoot>
</table>
@else
<p style="color:#9ca3af; font-size:8.5px; padding:8px 0;">No other item sales in this period.</p>
@endif


{{-- ══════════════════════════════
     PAGE 2: MENU ITEM ORDERS
     ══════════════════════════════ --}}
<div class="page-break"></div>

<div class="sec-head green">Menu Item Orders &mdash; {{ $menuOrders->count() }} orders | UGX {{ number_format($menuRevenue, 0) }}</div>

@if($menuOrders->count())
<table class="data">
    <thead>
        <tr>
            <th style="width:85px;">Invoice #</th>
            <th style="width:65px;">Date</th>
            <th style="width:52px;">Time</th>
            <th>Menu Items</th>
            <th style="width:62px;">Payment</th>
            <th class="tr" style="width:80px;">Amount (UGX)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($menuOrders as $sale)
        @php $mItems = $sale->items->filter(fn($i) => in_array($i->item_name, $mnl)); @endphp
        <tr class="menu-row">
            <td class="mono">{{ $sale->order_number }}</td>
            <td class="nw">{{ $sale->created_at->format('d/m/Y') }}</td>
            <td class="muted nw">{{ $sale->created_at->format('h:i A') }}</td>
            <td>
                @foreach($mItems as $item)
                    <span class="itag itag-menu">{{ $item->item_name }} &times;{{ $item->quantity }}</span>
                @endforeach
                @if($sale->items->filter(fn($i) => !in_array($i->item_name, $mnl))->count() > 0)
                    <span style="font-size:7px; color:#9ca3af;">&nbsp;+other items</span>
                @endif
            </td>
            <td>
                <span class="ppill {{ $payCss[$sale->payment_method] ?? 'p-cash' }}">
                    {{ $payLabel[$sale->payment_method] ?? ucfirst($sale->payment_method) }}
                </span>
            </td>
            <td class="tr fw">{{ number_format($sale->total_amount, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">MENU ORDERS TOTAL ({{ $menuOrders->count() }} orders)</td>
            <td class="tr">UGX {{ number_format($menuRevenue, 0) }}</td>
        </tr>
    </tfoot>
</table>
@else
<p style="color:#9ca3af; font-size:8.5px; padding:10px 0;">No menu item orders in this period.</p>
@endif


{{-- ══════════════════════════════
     PAGE 3: OTHER ITEM ORDERS
     ══════════════════════════════ --}}
<div class="page-break"></div>

<div class="sec-head gray">Other Item Orders &mdash; {{ $otherOrders->count() }} orders | UGX {{ number_format($otherRevenue, 0) }}</div>

@if($otherOrders->count())
<table class="data">
    <thead>
        <tr>
            <th style="width:85px;">Invoice #</th>
            <th style="width:65px;">Date</th>
            <th style="width:52px;">Time</th>
            <th>Other Items</th>
            <th style="width:62px;">Payment</th>
            <th class="tr" style="width:80px;">Amount (UGX)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($otherOrders as $sale)
        @php $oItems = $sale->items->filter(fn($i) => !in_array($i->item_name, $mnl)); @endphp
        <tr class="other-row">
            <td class="mono">{{ $sale->order_number }}</td>
            <td class="nw">{{ $sale->created_at->format('d/m/Y') }}</td>
            <td class="muted nw">{{ $sale->created_at->format('h:i A') }}</td>
            <td>
                @foreach($oItems as $item)
                    <span class="itag itag-other">{{ $item->item_name }} &times;{{ $item->quantity }}</span>
                @endforeach
                @if($sale->items->filter(fn($i) => in_array($i->item_name, $mnl))->count() > 0)
                    <span style="font-size:7px; color:#9ca3af;">&nbsp;+menu items</span>
                @endif
            </td>
            <td>
                <span class="ppill {{ $payCss[$sale->payment_method] ?? 'p-cash' }}">
                    {{ $payLabel[$sale->payment_method] ?? ucfirst($sale->payment_method) }}
                </span>
            </td>
            <td class="tr fw">{{ number_format($sale->total_amount, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">OTHER ORDERS TOTAL ({{ $otherOrders->count() }} orders)</td>
            <td class="tr">UGX {{ number_format($otherRevenue, 0) }}</td>
        </tr>
    </tfoot>
</table>
@else
<p style="color:#9ca3af; font-size:8.5px; padding:10px 0;">No other item orders in this period.</p>
@endif


{{-- ══════════════════════════════
     PAGE 4: COMPLETE TRANSACTION LIST
     ══════════════════════════════ --}}
<div class="page-break"></div>

<div class="sec-head">All Sales Transactions &mdash; {{ $salesList->count() }} orders</div>

<table class="data">
    <thead>
        <tr>
            <th style="width:85px;">Invoice #</th>
            <th style="width:65px;">Date</th>
            <th style="width:52px;">Time</th>
            <th>Items (green = menu, yellow = other)</th>
            <th style="width:62px;">Payment</th>
            <th class="tr" style="width:80px;">Amount (UGX)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salesList as $sale)
        <tr>
            <td class="mono">{{ $sale->order_number }}</td>
            <td class="nw">{{ $sale->created_at->format('d/m/Y') }}</td>
            <td class="muted nw">{{ $sale->created_at->format('h:i A') }}</td>
            <td>
                @foreach($sale->items as $item)
                @php $im = in_array($item->item_name, $mnl); @endphp
                    <span class="itag {{ $im ? 'itag-menu' : 'itag-other' }}">
                        {{ $item->item_name }} &times;{{ $item->quantity }}
                    </span>
                @endforeach
            </td>
            <td>
                <span class="ppill {{ $payCss[$sale->payment_method] ?? 'p-cash' }}">
                    {{ $payLabel[$sale->payment_method] ?? ucfirst($sale->payment_method) }}
                </span>
            </td>
            <td class="tr fw">{{ number_format($sale->total_amount, 0) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center; color:#9ca3af; padding:16px;">No sales in this period.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="font-size:10px;">GRAND TOTAL — {{ $salesList->count() }} Orders</td>
            <td class="tr" style="font-size:11px; color:#ea580c;">UGX {{ number_format($salesData['total_sales'], 0) }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    Sales Report &bull; {{ Carbon::parse($from)->format('d M Y') }} &ndash; {{ Carbon::parse($to)->format('d M Y') }}
    &bull; Generated {{ now()->format('d/m/Y H:i:s') }} &bull; Confidential
</div>

</body>
</html>
