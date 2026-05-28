@extends('layouts.cashier')

@section('title', 'Invoice')

@section('content')
<style>
    .invoice-wrap {
        max-width: 320px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    @media print {
        header, nav, .sidebar, .main-header, .page-header, .navbar, .cashier-navbar, .no-print {
            display: none !important;
        }
        body {
            margin: 0;
            padding: 0;
        }
        #invoice-print {
            display: block !important;
        }
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
    }
</style>

<div class="no-print" style="max-width: 320px; margin: 0 auto 12px auto;">
    <div class="flex justify-between gap-2">
        <a href="{{ route('cashier.bills') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Back to Bills
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
                <i class="fas fa-print"></i> Print
            </button>
            @if($order->payment_status == 'unpaid')
            <button onclick="openPaymentModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
                <i class="fas fa-credit-card"></i> Pay Now
            </button>
            @endif
        </div>
    </div>
</div>

<div class="invoice-wrap no-print">
    <div style="padding:12px; font-family:'Courier New',monospace; font-size:11px;">
        <div style="text-align:center;">
            <div style="font-size:15px; font-weight:bold;">PATIO BELLA</div>
            <div style="font-size:9px;">Restaurant & Lounge</div>
            <div style="font-size:9px;">Kampala Road, Kampala</div>
            <div style="font-size:9px;">Tel: +256 XXX XXX XXX</div>
            <div class="border-t border-dashed border-gray-300 my-2"></div>
            <div style="font-size:11px; font-weight:bold;">TAX INVOICE</div>
            <div style="font-size:10px; font-weight:bold;">{{ $order->order_number }}</div>
            <div class="border-t border-dashed border-gray-300 my-2"></div>
        </div>

        <div>
            <div style="display:flex; justify-content:space-between; font-size:9px;">
                <span>Date:</span>
                <span>{{ $order->created_at->format('d/m/Y h:i A') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:9px; margin-top:3px;">
                <span>Table:</span>
                <span>{{ $order->table_number ?? 'N/A' }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:9px; margin-top:3px;">
                <span>Waiter:</span>
                <span>{{ $order->waiter->first_name ?? '' }} {{ $order->waiter->last_name ?? '' }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:9px; margin-top:3px;">
                <span>Status:</span>
                <span style="{{ $order->payment_status == 'unpaid' ? 'color:#e67e22;' : 'color:#10b981;' }} font-weight:bold;">
                    {{ $order->payment_status == 'unpaid' ? 'UNPAID' : 'PAID' }}
                </span>
            </div>
        </div>
        <div class="border-t border-dashed border-gray-300 my-2"></div>

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

        <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:bold; margin:4px 0;">
            <span>TOTAL:</span>
            <span>UGX {{ number_format($order->total_amount, 0) }}</span>
        </div>
        <div class="border-t border-dashed border-gray-300 my-2"></div>

        <div style="text-align:center; font-size:9px;">
            <div>Thank you for your business!</div>
        </div>
    </div>
</div>

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
    const orderId = {{ $order->id }};
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

        fetch('{{ route("cashier.mark-paid", $order->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ payment_method: method })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("cashier.bills") }}';
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
@endsection
