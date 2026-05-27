@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Cashier Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Select an occupied table to process payment</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Tables</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $tables->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chair text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Occupied Tables</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $tables->where('is_occupied', 1)->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-chair text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Free Tables</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $tables->where('is_occupied', 0)->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-chair text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($tables as $table)
        <div onclick="showPaymentModal({{ $table->id }})"
             class="table-card bg-white rounded-xl border-2 p-4 text-center cursor-pointer transition-all hover:shadow-lg
                    {{ $table->is_occupied ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-orange-300' }}">
            <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-2
                        {{ $table->is_occupied ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-500' }}">
                <i class="fas fa-chair text-xl"></i>
            </div>
            <p class="font-bold text-gray-800">Table {{ $table->table_number }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $table->capacity }} seats</p>
            @if($table->is_occupied)
                <span class="inline-block mt-2 px-2 py-0.5 bg-yellow-200 text-yellow-700 text-xs rounded-full">
                    Occupied
                </span>
            @else
                <span class="inline-block mt-2 px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">
                    Available
                </span>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto shadow-xl">
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b border-gray-200 sticky top-0">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Process Payment</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <p id="modalOrderNumber" class="text-sm text-gray-500 mt-1"></p>
        </div>

        <div class="p-6" id="paymentModalContent">
            <div class="text-center py-8 text-gray-500">
                <div class="spinner mb-3"></div>
                Loading order details...
            </div>
        </div>
    </div>
</div>

<style>
    .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #ea580c;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .table-card {
        transition: all 0.2s ease;
    }
</style>

<script>
    let currentOrderId = null;
    let currentTotalAmount = 0;

    function showPaymentModal(tableId) {
        const modal = document.getElementById('paymentModal');
        const content = document.getElementById('paymentModalContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        content.innerHTML = '<div class="text-center py-8 text-gray-500"><div class="spinner mb-3"></div>Loading order details...</div>';

        fetch('/cashier/table/' + tableId + '/order')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentOrderId = data.order.id;
                    currentTotalAmount = data.order.total_amount;
                    renderPaymentForm(data.order);
                } else {
                    content.innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2 block"></i><p class="text-sm">' + data.message + '</p><button onclick="closePaymentModal()" class="mt-4 px-4 py-2 bg-orange-500 text-white rounded-lg">Close</button></div>';
                }
            })
            .catch(error => {
                content.innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2 block"></i><p class="text-sm">Error: ' + error.message + '</p><button onclick="closePaymentModal()" class="mt-4 px-4 py-2 bg-orange-500 text-white rounded-lg">Close</button></div>';
            });
    }

    function renderPaymentForm(order) {
        const content = document.getElementById('paymentModalContent');
        document.getElementById('modalOrderNumber').textContent = 'Table ' + order.table_number + ' - ' + order.order_number;

        let itemsHtml = '';
        order.items.forEach(item => {
            itemsHtml += `
                <div class="flex justify-between text-sm py-1 border-b border-gray-100">
                    <span>${item.quantity}x ${item.name}</span>
                    <span class="font-semibold">${formatMoney(item.total_price)}</span>
                </div>
            `;
        });

        content.innerHTML = `
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Table:</span>
                        <span class="font-semibold">${order.table_number}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Waiter:</span>
                        <span class="font-semibold">${order.waiter_name}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order #:</span>
                        <span class="font-semibold font-mono">${order.order_number}</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-3">
                    <div class="font-semibold mb-2">Order Items:</div>
                    ${itemsHtml}
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <div class="flex justify-between">
                            <span class="font-bold text-lg">Total Amount:</span>
                            <span class="font-bold text-lg text-orange-600">${formatMoney(order.total_amount)}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <button type="button" onclick="selectPaymentMethod('cash')" id="methodCash" class="px-3 py-2 border rounded-lg text-sm font-medium hover:border-orange-500 transition">Cash</button>
                        <button type="button" onclick="selectPaymentMethod('card')" id="methodCard" class="px-3 py-2 border rounded-lg text-sm font-medium hover:border-orange-500 transition">Card</button>
                        <button type="button" onclick="selectPaymentMethod('mobile_money')" id="methodMobile" class="px-3 py-2 border rounded-lg text-sm font-medium hover:border-orange-500 transition">Mobile Money</button>
                    </div>

                    <div id="cashAmountField" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount Paid (UGX)</label>
                        <input type="number" id="amountPaid" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500" placeholder="Enter amount paid">
                    </div>

                    <input type="hidden" id="selectedPaymentMethod" value="">
                </div>

                <div class="flex gap-3 pt-4">
                    <button onclick="closePaymentModal()" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">Cancel</button>
                    <button onclick="processPayment()" class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-semibold transition">Complete Payment</button>
                </div>
            </div>
        `;

        selectPaymentMethod('cash');
    }

    function selectPaymentMethod(method) {
        document.getElementById('selectedPaymentMethod').value = method;

        ['methodCash', 'methodCard', 'methodMobile'].forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.classList.remove('border-orange-500', 'bg-orange-50');
                btn.classList.add('border-gray-300');
            }
        });

        const activeBtn = document.getElementById('method' + method.charAt(0).toUpperCase() + method.slice(1));
        if (activeBtn) {
            activeBtn.classList.add('border-orange-500', 'bg-orange-50');
        }

        const cashField = document.getElementById('cashAmountField');
        if (method === 'cash') {
            cashField.classList.remove('hidden');
        } else {
            cashField.classList.add('hidden');
        }
    }

    function processPayment() {
        const paymentMethod = document.getElementById('selectedPaymentMethod').value;
        let amountPaid = currentTotalAmount;

        if (paymentMethod === 'cash') {
            amountPaid = parseFloat(document.getElementById('amountPaid').value);
            if (isNaN(amountPaid) || amountPaid < currentTotalAmount) {
                showToast('Amount paid must be at least ' + formatMoney(currentTotalAmount), 'error');
                return;
            }
        }

        fetch('/cashier/order/' + currentOrderId + '/pay', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                amount_paid: amountPaid,
                payment_method: paymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Payment successful!', 'success');
                window.open('/cashier/receipt/' + currentOrderId, '_blank', 'width=400,height=600');
                closePaymentModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Payment failed: ' + error.message, 'error');
        });
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentModal').classList.remove('flex');
        currentOrderId = null;
    }

    function formatMoney(amount) {
        return Math.round(amount || 0).toLocaleString('en-UG') + ' UGX';
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.className = `toast-notification ${bgColor} text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[250px]`;
        toast.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endsection
