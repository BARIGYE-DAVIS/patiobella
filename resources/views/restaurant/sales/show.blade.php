{{-- resources/views/restaurant/sales/show.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'Sale Details')
@section('page-title', 'Sale Details')

@section('content')

<style>
    /* =========================================
       SCREEN STYLES (normal view)
    ========================================= */
    .detail-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .detail-header {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
    }
    .detail-body { padding: 1.5rem; }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 0.8rem;
        white-space: nowrap;
    }
    .info-value {
        font-weight: 500;
        color: #1f2937;
        font-size: 0.85rem;
        text-align: right;
    }

    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table th.text-right  { text-align: right; }
    .data-table th.text-center { text-align: center; }
    .data-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #374151;
    }
    .data-table td.text-right  { text-align: right; }
    .data-table td.text-center { text-align: center; }
    .data-table tfoot td {
        padding: 0.75rem 1rem;
        background: #f9fafb;
        font-weight: 600;
    }
    .data-table tfoot tr.total-row td {
        font-size: 1rem;
        font-weight: 700;
        border-top: 2px solid #e2e8f0;
        color: #ea580c;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .badge-paid    { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef3c7; color: #92400e; }

    /* Action buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1.1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: background 0.2s;
    }
    .btn-back  { background: #6b7280; color: white; }
    .btn-back:hover  { background: #4b5563; }
    .btn-print { background: #3b82f6; color: white; }
    .btn-print:hover { background: #2563eb; }

    /* Hide layout header on print */
    @media print {
        header, nav, .sidebar, .main-header, .page-header, .navbar, .cashier-navbar {
            display: none !important;
        }
        body {
            margin: 0;
            padding: 0;
        }
    }

    /* =========================================
       PRINT STYLES — POS / Thermal Receipt
    ========================================= */
    @media print {

        /* Hide screen-only elements */
        .no-print { display: none !important; }

        /* Show the receipt */
        #receipt-print { display: block !important; }

        /* Reset page */
        @page {
            size: 80mm auto;   /* standard thermal roll width */
            margin: 4mm 3mm;
        }

        /* Receipt wrapper */
        #receipt-print {
            width: 72mm;          /* usable area on 80mm roll */
            margin: 0 auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            color: #000;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ---- Restaurant header ---- */
        .rct-header {
            text-align: center;
            margin-bottom: 4mm;
        }
        .rct-header .rct-logo {
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .rct-header .rct-tagline {
            font-size: 7.5pt;
            margin-top: 1mm;
        }
        .rct-header .rct-address {
            font-size: 7.5pt;
            margin-top: 0.5mm;
        }

        /* ---- Dividers ---- */
        .rct-divider       { border: none; border-top: 1px dashed #000; margin: 2.5mm 0; }
        .rct-divider-solid { border: none; border-top: 1px solid  #000; margin: 2.5mm 0; }

        /* ---- Key-value info rows ---- */
        .rct-info-row {
            display: flex;
            justify-content: space-between;
            font-size: 8pt;
            margin-bottom: 0.8mm;
        }
        .rct-info-row .rct-info-label { font-weight: 700; }
        .rct-info-row .rct-info-val   { text-align: right; }

        /* ---- Items table ---- */
        .rct-items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin: 1mm 0;
        }
        .rct-items-table thead th {
            font-weight: 700;
            text-align: left;
            padding-bottom: 1mm;
            border-bottom: 1px solid #000;
        }
        .rct-items-table thead th.right { text-align: right; }
        .rct-items-table tbody td {
            padding: 0.8mm 0;
            vertical-align: top;
        }
        .rct-items-table tbody td.right { text-align: right; white-space: nowrap; }
        .rct-items-table tbody .item-desc {
            font-size: 7.5pt;
            color: #333;
            padding-left: 2mm;
        }

        /* ---- Totals block ---- */
        .rct-totals { margin-top: 1mm; }
        .rct-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 8.5pt;
            margin-bottom: 0.8mm;
        }
        .rct-total-row.grand {
            font-size: 11pt;
            font-weight: 700;
            margin-top: 1.5mm;
            padding-top: 1.5mm;
            border-top: 1px solid #000;
        }
        .rct-total-row .rct-tlabel { font-weight: 700; }
        .rct-total-row .rct-tval   { text-align: right; }

        /* ---- Footer ---- */
        .rct-footer {
            text-align: center;
            margin-top: 4mm;
            font-size: 7.5pt;
        }
        .rct-footer .rct-thank-you {
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* ---- QR / barcode placeholder ---- */
        .rct-barcode {
            text-align: center;
            margin: 2mm 0;
            font-size: 7pt;
            letter-spacing: 3px;
        }
    }
</style>

{{-- ===== SCREEN: action bar ===== --}}
<div class="no-print mb-4 flex justify-between items-center">
    <a href="{{ route('restaurant.sales.index') }}" class="btn btn-back">
        <i class="fas fa-arrow-left"></i> Back to Sales
    </a>
    <button onclick="window.print()" class="btn btn-print">
        <i class="fas fa-print"></i> Print Receipt
    </button>
</div>

{{-- ===== SCREEN: Sale Information card ===== --}}
<div class="detail-card no-print">
    <div class="detail-header">
        <i class="fas fa-receipt mr-2 text-orange-600"></i> Sale Information
    </div>
    <div class="detail-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="info-row">
                    <span class="info-label">Invoice Number</span>
                    <span class="info-value font-mono font-bold">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date &amp; Time</span>
                    <span class="info-value">{{ $order->created_at->format('d/m/Y h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Cashier</span>
                    <span class="info-value">
                        {{ $order->cashier->first_name ?? 'N/A' }} {{ $order->cashier->last_name ?? '' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer Type</span>
                    <span class="info-value">
                        {{ ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')) }}
                    </span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value">
                        @if($order->payment_method === 'cash')        💵 Cash
                        @elseif($order->payment_method === 'card')    💳 Card
                        @elseif($order->payment_method === 'mobile_money') 📱 Mobile Money
                        @else {{ ucfirst($order->payment_method ?? 'N/A') }}
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Status</span>
                    <span class="info-value">
                        <span class="badge {{ $order->payment_status === 'paid' ? 'badge-paid' : 'badge-pending' }}">
                            {{ ucfirst($order->payment_status ?? 'pending') }}
                        </span>
                    </span>
                </div>
                @if($order->amount_paid)
                <div class="info-row">
                    <span class="info-label">Amount Paid</span>
                    <span class="info-value text-green-600 font-semibold">
                        UGX {{ number_format($order->amount_paid, 0) }}
                    </span>
                </div>
                @endif
                @if($order->change_amount && $order->change_amount > 0)
                <div class="info-row">
                    <span class="info-label">Change Returned</span>
                    <span class="info-value text-orange-600 font-semibold">
                        UGX {{ number_format($order->change_amount, 0) }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== SCREEN: Items Sold card ===== --}}
<div class="detail-card no-print">
    <div class="detail-header">
        <i class="fas fa-boxes mr-2 text-orange-600"></i> Items Sold
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td class="font-medium">{{ $item->item_name }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($item->unit_price, 0) }}</td>
                    <td class="text-right font-semibold">UGX {{ number_format($item->total_price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">Subtotal</td
                    <td class="text-right">UGX {{ number_format($order->subtotal ?? $order->total_amount, 0) }}</td
                </tr>
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL</td
                    <td class="text-right">UGX {{ number_format($order->total_amount, 0) }}</td
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ===== SCREEN: Additional Information card ===== --}}
<div class="detail-card no-print">
    <div class="detail-header">
        <i class="fas fa-info-circle mr-2 text-orange-600"></i> Additional Information
    </div>
    <div class="detail-body">
        <div class="info-row">
            <span class="info-label">Order Status</span>
            <span class="info-value">{{ ucfirst($order->status ?? 'completed') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Created At</span>
            <span class="info-value">{{ $order->created_at->format('d/m/Y h:i A') }}</span>
        </div>
        @if($order->updated_at && $order->updated_at != $order->created_at)
        <div class="info-row">
            <span class="info-label">Last Updated</span>
            <span class="info-value">{{ $order->updated_at->format('d/m/Y h:i A') }}</span>
        </div>
        @endif
        @if($order->notes)
        <div class="info-row">
            <span class="info-label">Notes</span>
            <span class="info-value">{{ $order->notes }}</span>
        </div>
        @endif
    </div>
</div>

{{-- =====================================================================
     PRINT ONLY: POS / Thermal Receipt  (hidden on screen, shown on print)
====================================================================== --}}
<div id="receipt-print" style="display: none;">

    {{-- Restaurant header --}}
    <div class="rct-header">
        <div class="rct-logo">{{ config('app.restaurant_name', 'PATIO BELLA') }}</div>
        <div class="rct-tagline">Restaurant & Lounge</div>
        <div class="rct-address">
            Kampala, Uganda &bull; Tel: +256 XXX XXX XXX
        </div>
    </div>

    <hr class="rct-divider-solid">

    {{-- Receipt label --}}
    <div style="text-align:center; font-weight:700; font-size:9pt; margin-bottom:2mm; letter-spacing:1px;">
        *** OFFICIAL RECEIPT ***
    </div>

    {{-- Order meta --}}
    <div class="rct-info-row">
        <span class="rct-info-label">Invoice #</span>
        <span class="rct-info-val">{{ $order->order_number }}</span>
    </div>
    <div class="rct-info-row">
        <span class="rct-info-label">Date</span>
        <span class="rct-info-val">{{ $order->created_at->format('d/m/Y') }}</span>
    </div>
    <div class="rct-info-row">
        <span class="rct-info-label">Time</span>
        <span class="rct-info-val">{{ $order->created_at->format('h:i A') }}</span>
    </div>
    <div class="rct-info-row">
        <span class="rct-info-label">Cashier</span>
        <span class="rct-info-val">
            {{ $order->cashier->first_name ?? 'N/A' }} {{ $order->cashier->last_name ?? '' }}
        </span>
    </div>
    <div class="rct-info-row">
        <span class="rct-info-label">Type</span>
        <span class="rct-info-val">
            {{ ucfirst(str_replace('_', ' ', $order->customer_type ?? 'Dine In')) }}
        </span>
    </div>

    <hr class="rct-divider">

    {{-- Items --}}
    <table class="rct-items-table">
        <thead>
            <tr>
                <th style="width:55%;">Item</th>
                <th style="width:10%; text-align:center;">Qty</th>
                <th class="right" style="width:35%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td style="text-align:center;">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->total_price, 0) }}</td>
            </tr>
            @if($item->quantity > 1)
            <tr>
                <td class="item-desc" colspan="2">
                    {{ number_format($item->quantity, 0) }} x {{ number_format($item->unit_price, 0) }}
                </td>
                <td></td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <hr class="rct-divider">

    {{-- Totals --}}
    <div class="rct-totals">
        <div class="rct-total-row">
            <span class="rct-tlabel">Subtotal</span>
            <span class="rct-tval">UGX {{ number_format($order->subtotal ?? $order->total_amount, 0) }}</span>
        </div>

        @if(isset($order->tax_amount) && $order->tax_amount > 0)
        <div class="rct-total-row">
            <span class="rct-tlabel">Tax (18% VAT)</span>
            <span class="rct-tval">UGX {{ number_format($order->tax_amount, 0) }}</span>
        </div>
        @endif

        @if(isset($order->discount_amount) && $order->discount_amount > 0)
        <div class="rct-total-row">
            <span class="rct-tlabel">Discount</span>
            <span class="rct-tval">- UGX {{ number_format($order->discount_amount, 0) }}</span>
        </div>
        @endif

        <div class="rct-total-row grand">
            <span class="rct-tlabel">TOTAL</span>
            <span class="rct-tval">UGX {{ number_format($order->total_amount, 0) }}</span>
        </div>
    </div>

    <hr class="rct-divider">

    {{-- Payment info --}}
    <div class="rct-info-row">
        <span class="rct-info-label">Payment</span>
        <span class="rct-info-val">
            @if($order->payment_method === 'cash')        Cash
            @elseif($order->payment_method === 'card')    Card
            @elseif($order->payment_method === 'mobile_money') Mobile Money
            @else {{ ucfirst($order->payment_method ?? '') }}
            @endif
        </span>
    </div>

    @if($order->amount_paid)
    <div class="rct-info-row">
        <span class="rct-info-label">Amount Paid</span>
        <span class="rct-info-val">UGX {{ number_format($order->amount_paid, 0) }}</span>
    </div>
    @endif

    @if($order->change_amount && $order->change_amount > 0)
    <div class="rct-info-row">
        <span class="rct-info-label">Change</span>
        <span class="rct-info-val">UGX {{ number_format($order->change_amount, 0) }}</span>
    </div>
    @endif

    @if($order->notes)
    <hr class="rct-divider">
    <div style="font-size:7.5pt; margin-bottom:1mm;"><strong>Note:</strong> {{ $order->notes }}</div>
    @endif

    <hr class="rct-divider">

    {{-- Barcode-style invoice reference --}}
    <div class="rct-barcode">
        {{ $order->order_number }}
    </div>

    {{-- Footer --}}
    <div class="rct-footer">
        <div class="rct-thank-you">THANK YOU!</div>
        <div style="margin-top:1.5mm;">Please come again &bull; Enjoy your meal</div>
        <div style="margin-top:1mm;">This is your official receipt.</div>
        <div style="margin-top:0.5mm; font-size:7pt;">
            Powered by PatioBellaPOS &bull; {{ now()->format('Y') }}
        </div>
    </div>

</div>{{-- end #receipt-print --}}

<script>
(function () {
    // Remove inline display:none before printing so CSS @media print can take over,
    // then restore it after so the receipt stays hidden on screen.
    window.addEventListener('beforeprint', function () {
        document.getElementById('receipt-print').style.removeProperty('display');
    });
    window.addEventListener('afterprint', function () {
        document.getElementById('receipt-print').style.display = 'none';
    });
})();
</script>

@endsection
