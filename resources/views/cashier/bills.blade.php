@extends('layouts.cashier')

@section('title', 'Bills')

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Bills Management</h1>
        <p class="text-sm text-gray-500 mt-0.5">View and process customer bills</p>
    </div>

    {{-- Search Bar --}}
    <div class="mb-4">
        <div class="relative max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchBillInput" placeholder="Search by Order #, Table, or Waiter..."
                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 mb-6">
        <button onclick="showTab('printed')" id="tabPrintedBtn" class="px-6 py-2 text-sm font-medium border-b-2 transition-all">
            <i class="fas fa-print mr-2"></i> Printed Bills
            <span class="ml-1 px-2 py-0.5 bg-orange-100 text-orange-600 rounded-full text-xs">{{ $printedBills->count() }}</span>
        </button>
        <button onclick="showTab('notprinted')" id="tabNotPrintedBtn" class="px-6 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition-all">
            <i class="fas fa-clock mr-2"></i> Not Printed
            <span class="ml-1 px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full text-xs">{{ $notPrintedBills->count() }}</span>
        </button>
        <button onclick="showTab('settled')" id="tabSettledBtn" class="px-6 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition-all">
            <i class="fas fa-check-circle mr-2"></i> Settled
            <span class="ml-1 px-2 py-0.5 bg-green-100 text-green-600 rounded-full text-xs">{{ $settledBills->count() }}</span>
        </button>
    </div>

    {{-- Printed Bills Tab --}}
    <div id="printedTab" class="tab-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-print text-orange-500 mr-2"></i>
                    Printed Bills (Ready for Payment)
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Bills that have been printed and are waiting for payment</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Table</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waiter</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="printedBillsTable">
                        @forelse($printedBills as $order)
                        <tr class="hover:bg-gray-50 transition bill-row"
                            data-order-number="{{ $order->order_number }}"
                            data-table-number="{{ $order->table_number }}"
                            data-waiter-name="{{ $order->waiter->first_name ?? '' }} {{ $order->waiter->last_name ?? '' }}">
                            <td class="px-6 py-4 text-sm font-mono font-bold text-gray-800">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Table {{ $order->table_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->waiter->first_name ?? '' }} {{ $order->waiter->last_name ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->items->count() }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ number_format($order->total_amount, 0) }} UGX</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('h:i A') }}</td>
                            <td class="px-6 py-4">
                                <button onclick="openPaymentModal({{ $order->id }}, {{ $order->total_amount }})"
                                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition">
                                    <i class="fas fa-credit-card mr-1"></i> Settle Bill
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-check-circle text-3xl mb-2 block"></i>
                                <p class="text-sm">No printed bills waiting for payment</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Not Printed Bills Tab --}}
    <div id="notPrintedTab" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-clock text-yellow-500 mr-2"></i>
                    Not Printed Bills
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Bills that have not been printed yet</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Table</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waiter</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="notPrintedBillsTable">
                        @forelse($notPrintedBills as $order)
                        <tr class="hover:bg-gray-50 transition bill-row"
                            data-order-number="{{ $order->order_number }}"
                            data-table-number="{{ $order->table_number }}"
                            data-waiter-name="{{ $order->waiter->first_name ?? '' }} {{ $order->waiter->last_name ?? '' }}">
                            <td class="px-6 py-4 text-sm font-mono font-bold text-gray-800">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Table {{ $order->table_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->waiter->first_name ?? '' }} {{ $order->waiter->last_name ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->items->count() }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ number_format($order->total_amount, 0) }} UGX</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-check-circle text-3xl mb-2 block"></i>
                                <p class="text-sm">No pending bills</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Settled Bills Tab --}}
    <div id="settledTab" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Settled Bills
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Bills that have been paid</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Table</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waiter</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cashier</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="settledBillsTable">
                        @forelse($settledBills as $order)
                        <tr class="hover:bg-gray-50 transition bill-row"
                            data-order-number="{{ $order->order_number }}"
                            data-table-number="{{ $order->table_number }}"
                            data-waiter-name="{{ $order->waiter->first_name ?? '' }} {{ $order->waiter->last_name ?? '' }}">
                            <td class="px-6 py-4 text-sm font-mono font-bold text-gray-800">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Table {{ $order->table_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->waiter->first_name ?? '' }} {{ $order->waiter->last_name ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->cashier->first_name ?? '' }} {{ $order->cashier->last_name ?? '' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ number_format($order->total_amount, 0) }} UGX</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1 text-xs"></i> {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'cash')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $order->updated_at->format('h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-receipt text-3xl mb-2 block"></i>
                                <p class="text-sm">No settled bills</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden no-print">
    <div class="bg-white rounded-xl shadow-xl w-96 max-w-full p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">
                <i class="fas fa-credit-card text-green-600"></i> Settle Bill
            </h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-100 p-3 rounded text-center">
                <span class="text-sm text-gray-600">Total Amount</span>
                <div class="text-2xl font-bold text-orange-600" id="modalTotalAmount">UGX 0</div>
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
                <i class="fas fa-check-circle"></i> Confirm Payment
            </button>
        </div>
    </div>
</div>

<script>
    let currentOrderId = null;
    let currentTotalAmount = 0;

    // Search functionality
    const searchInput = document.getElementById('searchBillInput');

    function filterBills() {
        const searchTerm = searchInput.value.toLowerCase();
        const allRows = document.querySelectorAll('.bill-row');

        allRows.forEach(row => {
            const orderNumber = row.getAttribute('data-order-number') || '';
            const tableNumber = row.getAttribute('data-table-number') || '';
            const waiterName = row.getAttribute('data-waiter-name') || '';

            if (orderNumber.toLowerCase().includes(searchTerm) ||
                tableNumber.toLowerCase().includes(searchTerm) ||
                waiterName.toLowerCase().includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', filterBills);
    }

    function showTab(tab) {
        document.getElementById('printedTab').classList.add('hidden');
        document.getElementById('notPrintedTab').classList.add('hidden');
        document.getElementById('settledTab').classList.add('hidden');

        document.getElementById('tabPrintedBtn').classList.remove('border-orange-500', 'text-orange-600');
        document.getElementById('tabPrintedBtn').classList.add('text-gray-500', 'border-transparent');
        document.getElementById('tabNotPrintedBtn').classList.remove('border-orange-500', 'text-orange-600');
        document.getElementById('tabNotPrintedBtn').classList.add('text-gray-500', 'border-transparent');
        document.getElementById('tabSettledBtn').classList.remove('border-orange-500', 'text-orange-600');
        document.getElementById('tabSettledBtn').classList.add('text-gray-500', 'border-transparent');

        if (tab === 'printed') {
            document.getElementById('printedTab').classList.remove('hidden');
            document.getElementById('tabPrintedBtn').classList.add('border-orange-500', 'text-orange-600');
        } else if (tab === 'notprinted') {
            document.getElementById('notPrintedTab').classList.remove('hidden');
            document.getElementById('tabNotPrintedBtn').classList.add('border-orange-500', 'text-orange-600');
        } else if (tab === 'settled') {
            document.getElementById('settledTab').classList.remove('hidden');
            document.getElementById('tabSettledBtn').classList.add('border-orange-500', 'text-orange-600');
        }
    }

    function openPaymentModal(orderId, totalAmount) {
        currentOrderId = orderId;
        currentTotalAmount = totalAmount;

        document.getElementById('modalTotalAmount').innerHTML = 'UGX ' + totalAmount.toLocaleString();
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('amountReceived').value = '';
        document.getElementById('changeDue').innerHTML = '';

        // Reset to cash by default
        document.getElementById('paymentMethod').value = 'cash';
        document.getElementById('cashSection').style.display = 'block';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        currentOrderId = null;
    }

    // Payment method change
    const paymentMethodSelect = document.getElementById('paymentMethod');
    const cashSection = document.getElementById('cashSection');
    const amountReceived = document.getElementById('amountReceived');
    const changeDue = document.getElementById('changeDue');

    paymentMethodSelect.addEventListener('change', function () {
        cashSection.style.display = this.value === 'cash' ? 'block' : 'none';
    });

    amountReceived.addEventListener('input', function () {
        const received = parseFloat(this.value) || 0;
        const diff = received - currentTotalAmount;
        if (received <= 0) {
            changeDue.innerHTML = '';
        } else if (diff >= 0) {
            changeDue.innerHTML = `<span class="text-green-600 font-semibold">Change: UGX ${diff.toLocaleString()}</span>`;
        } else {
            changeDue.innerHTML = `<span class="text-red-600 font-semibold">Remaining: UGX ${Math.abs(diff).toLocaleString()}</span>`;
        }
    });

    function processPayment() {
        const method = paymentMethodSelect.value;
        const btn = event.target;

        if (method === 'cash') {
            const received = parseFloat(amountReceived.value) || 0;
            if (received < currentTotalAmount) {
                alert('Insufficient amount received. Please enter the full amount.');
                return;
            }
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        fetch('/cashier/mark-paid/' + currentOrderId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ payment_method: method })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Permission denied');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.open('/cashier/receipt/' + currentOrderId, '_blank', 'width=400,height=600');
                showToast('Bill settled successfully!', 'success');
                closePaymentModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Payment';
            }
        })
        .catch(err => {
            showToast(err.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Payment';
        });
    }

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-lg shadow-lg text-white text-sm ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        toast.innerHTML = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endsection
