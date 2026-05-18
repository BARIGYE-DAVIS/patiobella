{{-- resources/views/bar/cashier/invoice.blade.php --}}

@extends('layouts.bar-cashier')

@section('title', 'Bar Invoice')

@section('page-title', 'Invoice')

@section('content')
<style>
    /* Screen styles */
    .invoice-wrap {
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
        header, nav, .sidebar, .main-header, .page-header, .navbar, .bar-cashier-navbar {
            display: none !important;
        }
        body {
            margin: 0;
            padding: 0;
        }
    }

    /* Print styles */
    @media print {
        .no-print { display: none !important; }
        #invoice-print { display: block !important; }

        @page {
            size: 80mm auto;
            margin: 4mm 3mm;
        }

        #invoice-print {
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

        .status-unpaid {
            color: #e67e22;
            font-weight: bold;
        }
    }
</style>

{{-- Screen: Action Buttons --}}
<div class="no-print" style="max-width: 320px; margin: 0 auto 12px auto;">
    <div class="flex justify-between gap-2">
        <a href="{{ route('bar.cashier.orders') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="openPaymentModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
                <i class="fas fa-credit-card"></i> Pay Now
            </button>
        </div>
    </div>
</div>

{{-- Screen: Invoice Preview --}}
<div class="invoice-wrap no-print">
    <div style="padding:12px; font-family:'Courier New',monospace; font-size:11px;">
        {{-- Header --}}
        <div style="text-align:center;">
            <div style="font-size:15px; font-weight:bold;">PATIO BELLA - BAR</div>
            <div style="font-size:9px;">Bar & Lounge</div>
            <div style="font-size:9px;">Kampala Road, Kampala</div>
            <div style="font-size:9px;">Tel: +256 XXX XXX XXX</div>
            <div class="border-t border-dashed border-gray-300 my-2"></div>
            <div style="font-size:11px; font-weight:bold;">TAX INVOICE</div>
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
                <span>Order Type:</span>
                <span>{{ ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')) }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:9px; margin-top:3px;">
                <span>Status:</span>
                <span style="color:#e67e22; font-weight:bold;">UNPAID</span>
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

        {{-- Total --}}
        <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:bold; margin:4px 0;">
            <span>TOTAL:</span>
            <span>UGX {{ number_format($order->total_amount, 0) }}</span>
        </div>
        <div class="border-t border-dashed border-gray-300 my-2"></div>

        {{-- Footer --}}
        <div style="text-align:center; font-size:9px;">
            <div>Thank you for your business!</div>
            <div>** Original Invoice **</div>
        </div>
    </div>
</div>

{{-- PRINT ONLY --}}
<div id="invoice-print" style="display: none;">

    {{-- Header --}}
    <div class="rct-header">
        <div class="rct-logo">PATIO BELLA - BAR</div>
        <div class="rct-tagline">Bar & Lounge</div>
        <div class="rct-address">
            Kampala, Uganda &bull; Tel: +256 XXX XXX XXX
        </div>
    </div>

    <hr class="rct-divider-solid">

    {{-- Invoice label --}}
    <div style="text-align:center; font-weight:700; font-size:9pt; margin-bottom:2mm; letter-spacing:1px;">
        *** TAX INVOICE ***
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
    <div class="rct-info-row">
        <span class="rct-info-label">Status</span>
        <span class="rct-info-val status-unpaid">UNPAID</span>
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
                <td></td
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
            <span class="rct-tlabel">TOTAL DUE</span>
            <span class="rct-tval">UGX {{ number_format($order->total_amount, 0) }}</span>
        </div>
    </div>

    <hr class="rct-divider">

    {{-- Footer --}}
    <div class="rct-footer">
        <div class="rct-thank-you">THANK YOU!</div>
        <div style="margin-top:1.5mm;">Please present this invoice for payment</div>
        <div style="margin-top:1mm;">** Original Invoice **</div>
        <div style="margin-top:0.5mm; font-size:7pt;">
            Powered by PatioBellaPOS &bull; {{ now()->format('Y') }}
        </div>
    </div>

</div>

{{-- Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden no-print">
    <div class="bg-white rounded-xl shadow-xl w-96 max-w-full p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">
                <i class="fas fa-credit-card text-green-600"></i> Process Payment
            </h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-100 p-3 rounded text-center">
                <span class="text-sm text-gray-600">Total Amount</span>
                <div class="text-2xl font-bold text-orange-600">UGX {{ number_format($order->total_amount, 0) }}</div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Payment Method</label>
                <select id="paymentMethod" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="cash">💵 Cash</option>
                    <option value="card">💳 Card</option>
                    <option value="mobile_money">📱 Mobile Money</option>
                </select>
            </div>

            <div id="cashSection">
                <label class="block text-sm font-medium mb-1">Amount Received</label>
                <input type="number" id="amountReceived" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Enter amount received">
                <div id="changeDue" class="mt-2 text-sm text-right"></div>
            </div>

            <button onclick="processPayment()" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700">
                <i class="fas fa-check-circle"></i> Complete Payment
            </button>
        </div>
    </div>
</div>

<script>
    const totalAmount = {{ $order->total_amount }};
    const paymentMethodSelect = document.getElementById('paymentMethod');
    const cashSection = document.getElementById('cashSection');
    const amountReceived = document.getElementById('amountReceived');
    const changeDue = document.getElementById('changeDue');

    paymentMethodSelect.addEventListener('change', function () {
        cashSection.style.display = this.value === 'cash' ? 'block' : 'none';
    });

    if (amountReceived) {
        amountReceived.addEventListener('input', function () {
            const received = parseFloat(this.value) || 0;
            const diff = received - totalAmount;
            if (received <= 0) {
                changeDue.innerHTML = '';
            } else if (diff >= 0) {
                changeDue.innerHTML = `<span class="text-green-600 font-semibold">Change: UGX ${diff.toLocaleString()}</span>`;
            } else {
                changeDue.innerHTML = `<span class="text-red-600 font-semibold">Remaining: UGX ${Math.abs(diff).toLocaleString()}</span>`;
            }
        });
    }

    function openPaymentModal() {
        document.getElementById('paymentModal').classList.remove('hidden');
        if (amountReceived) amountReceived.value = '';
        if (changeDue) changeDue.innerHTML = '';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    function processPayment() {
        const method = paymentMethodSelect.value;
        if (method === 'cash') {
            const received = parseFloat(amountReceived.value) || 0;
            if (received < totalAmount) {
                alert('Insufficient amount received. Please enter the full amount.');
                return;
            }
        }

        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        fetch('{{ route("bar.cashier.mark-as-paid", $order->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payment_method: method,
                amount_paid: method === 'cash' ? parseFloat(amountReceived.value) : totalAmount,
                change_amount: method === 'cash' ? (parseFloat(amountReceived.value) - totalAmount) : 0
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("bar.cashier.receipt", $order->id) }}';
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Payment';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Payment';
        });
    }
</script>

<script>
(function () {
    window.addEventListener('beforeprint', function () {
        document.getElementById('invoice-print').style.removeProperty('display');
    });
    window.addEventListener('afterprint', function () {
        document.getElementById('invoice-print').style.display = 'none';
    });
})();
</script>
@endsection
