{{-- resources/views/restaurant/cashier/receipt.blade.php --}}

@extends('layouts.cashier')

@section('title', 'Receipt')

@section('page-title', 'Payment Receipt')

@section('content')
<style>
    /* Screen styles */
    .receipt-wrap {
        max-width: 320px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

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

    /* Print styles - exact match to sales receipt */
    @media print {
        .no-print { display: none !important; }
        #receipt-print { display: block !important; }

        @page {
            size: 80mm auto;
            margin: 4mm 3mm;
        }

        #receipt-print {
            width: 72mm;
            margin: 0 auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            color: #000;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .rct-header { text-align: center; margin-bottom: 4mm; }
        .rct-header .rct-logo { font-size: 14pt; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .rct-header .rct-tagline { font-size: 7.5pt; margin-top: 1mm; }
        .rct-header .rct-address { font-size: 7.5pt; margin-top: 0.5mm; }

        .rct-divider { border: none; border-top: 1px dashed #000; margin: 2.5mm 0; }
        .rct-divider-solid { border: none; border-top: 1px solid #000; margin: 2.5mm 0; }

        .rct-info-row {
            display: flex;
            justify-content: space-between;
            font-size: 8pt;
            margin-bottom: 0.8mm;
        }
        .rct-info-row .rct-info-label { font-weight: 700; }
        .rct-info-row .rct-info-val { text-align: right; }

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
        .rct-total-row .rct-tval { text-align: right; }

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

        .rct-barcode {
            text-align: center;
            margin: 2mm 0;
            font-size: 7pt;
            letter-spacing: 3px;
        }
    }
</style>

{{-- Screen: Action Buttons --}}
<div class="no-print" style="max-width: 320px; margin: 0 auto 12px auto;">
    <div class="flex justify-between gap-2">
        <a href="{{ route('restaurant.cashier.orders') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">← Back to Orders</a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">🖨️ Print</button>
            <a href="{{ route('restaurant.cashier.pos') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm">💰 New Sale</a>
        </div>
    </div>
</div>

{{-- Screen: Receipt Preview --}}
<div class="receipt-wrap no-print">
    <div style="padding:12px; font-family:'Courier New',monospace; font-size:11px;">
        {{-- Header --}}
        <div style="text-align:center;">
            <div style="font-size:15px; font-weight:bold;">PATIO BELLA</div>
            <div style="font-size:9px;">Restaurant & Lounge</div>
            <div style="font-size:9px;">Kampala Road, Kampala</div>
            <div style="font-size:9px;">Tel: +256 XXX XXX XXX</div>
            <div class="border-t border-dashed border-gray-300 my-2"></div>
            <div style="font-size:11px; font-weight:bold;">PAYMENT RECEIPT</div>
            <div style="font-size:10px; font-weight:bold;">{{ $order->order_number }}</div>
            <div class="border-t border-dashed border-gray-300 my-2"></div>
        </div>

        {{-- Details --}}
        <div>
            <div style="display:flex; justify-content:space-between; font-size:9px;">
                <span>Date:</span>
                <span>{{ $order->created_at->format('d/m/Y h:i A') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:9px; margin-top:3px;">
                <span>Cashier:</span>
                <span>{{ Auth::user()->first_name ?? 'N/A' }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:9px; margin-top:3px;">
                <span>Payment:</span>
                <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'cash')) }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:9px; margin-top:3px;">
                <span>Status:</span>
                <span style="color:#2ecc71; font-weight:bold;">PAID</span>
            </div>
        </div>
        <div class="border-t border-dashed border-gray-300 my-2"></div>

        {{-- Items --}}
        <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:9px; border-bottom:1px dotted #000; padding-bottom:3px;">
            <span style="width:46%;">ITEM</span>
            <span style="width:14%; text-align:center;">QTY</span>
            <span style="width:20%; text-align:right;">PRICE</span>
            <span style="width:20%; text-align:right;">TOTAL</span>
        </div>

        @foreach($order->items as $item)
        <div style="display:flex; justify-content:space-between; font-size:9px; margin:4px 0;">
            <span style="width:46%;">{{ \Str::limit($item->item_name, 18) }}</span>
            <span style="width:14%; text-align:center;">{{ number_format($item->quantity, 0) }}</span>
            <span style="width:20%; text-align:right;">{{ number_format($item->unit_price, 0) }}</span>
            <span style="width:20%; text-align:right; font-weight:bold;">{{ number_format($item->total_price, 0) }}</span>
        </div>
        @endforeach

        <div class="border-t border-dashed border-gray-300 my-2"></div>

        {{-- Payment Details --}}
        <div style="margin: 8px 0;">
            <div style="display:flex; justify-content:space-between; font-size:10px;">
                <span>Total Amount:</span>
                <span>UGX {{ number_format($order->total_amount, 0) }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:10px; margin-top:4px;">
                <span>Amount Paid:</span>
                <span>UGX {{ number_format($order->amount_paid ?? $order->total_amount, 0) }}</span>
            </div>
            @if(($order->change_amount ?? 0) > 0)
            <div style="display:flex; justify-content:space-between; font-size:10px; margin-top:4px; color:#e67e22;">
                <span>Change Returned:</span>
                <span>UGX {{ number_format($order->change_amount, 0) }}</span>
            </div>
            @endif
        </div>
        <div class="border-t border-dashed border-gray-300 my-2"></div>

        {{-- Total Paid --}}
        <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:bold; margin:4px 0;">
            <span>TOTAL PAID:</span>
            <span style="color:#2ecc71;">UGX {{ number_format($order->amount_paid ?? $order->total_amount, 0) }}</span>
        </div>
        <div class="border-t border-dashed border-gray-300 my-2"></div>

        {{-- Footer --}}
        <div style="text-align:center; font-size:9px;">
            <div>Thank you for your business!</div>
            <div>** Paid & Confirmed **</div>
        </div>
    </div>
</div>

{{-- PRINT ONLY: Exact same format as sales receipt --}}
<div id="receipt-print" style="display: none;">

    {{-- Restaurant header --}}
    <div class="rct-header">
        <div class="rct-logo">PATIO BELLA</div>
        <div class="rct-tagline">Restaurant & Lounge</div>
        <div class="rct-address">
            Kampala, Uganda &bull; Tel: +256 XXX XXX XXX
        </div>
    </div>

    <hr class="rct-divider-solid">

    {{-- Receipt label --}}
    <div style="text-align:center; font-weight:700; font-size:9pt; margin-bottom:2mm; letter-spacing:1px;">
        *** PAYMENT RECEIPT ***
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
                <td>\n                </td
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

</div>

<script>
(function () {
    window.addEventListener('beforeprint', function () {
        document.getElementById('receipt-print').style.removeProperty('display');
    });
    window.addEventListener('afterprint', function () {
        document.getElementById('receipt-print').style.display = 'none';
    });
})();
</script>
@endsection
