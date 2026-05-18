{{-- resources/views/bar/cashier/invoices.blade.php --}}

@extends('layouts.bar-cashier')

@section('title', 'Invoices')

@section('page-title', 'Invoice Management')

@section('content')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .status-unpaid {
        background: #fef3c7;
        color: #92400e;
    }
    .status-paid {
        background: #d1fae5;
        color: #065f46;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
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
    .text-right {
        text-align: right;
    }
    .text-left {
        text-align: left;
    }
    .text-center {
        text-align: center;
    }
    .btn-pay {
        background: #10b981;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        border: none;
        cursor: pointer;
    }
    .btn-pay:hover {
        background: #059669;
    }
    .btn-view {
        background: #3b82f6;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
    }
    .btn-view:hover {
        background: #2563eb;
    }
    .filter-bar {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .search-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 250px;
    }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-receipt mr-2"></i>
                    Bar Invoices
                </h2>
                <p class="text-blue-100 mt-1">Manage invoices and process payments</p>
            </div>
            <div>
                <p class="text-sm"><i class="fas fa-chart-line mr-1"></i> Total Unpaid</p>
                <p class="text-2xl font-bold" id="totalUnpaidCount">{{ $unpaidInvoices->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-bar">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <div class="flex gap-3">
                <a href="{{ route('bar.cashier.invoices') }}" class="px-4 py-2 rounded-lg text-sm {{ request()->get('status', 'unpaid') == 'unpaid' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    <i class="fas fa-clock mr-1"></i> Unpaid
                </a>
                <a href="{{ route('bar.cashier.invoices', ['status' => 'paid']) }}" class="px-4 py-2 rounded-lg text-sm {{ request()->get('status') == 'paid' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    <i class="fas fa-check-circle mr-1"></i> Paid
                </a>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="searchInput" class="search-input pl-8" placeholder="Search by invoice #..." value="{{ request('search') }}">
            </div>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Invoice #</th>
                        <th class="text-left">Date & Time</th>
                        <th class="text-left">Cashier</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Amount Paid</th>
                        <th class="text-right">Change</th>
                        <th class="text-left">Payment</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="invoicesTableBody">
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="text-left font-mono text-xs font-bold">{{ $invoice->order_number }}</td>
                        <td class="text-left text-xs">{{ $invoice->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="text-left text-xs">{{ $invoice->cashier->first_name ?? 'N/A' }}</td>
                        <td class="text-right font-semibold">UGX {{ number_format($invoice->total_amount, 0) }}</td>
                        <td class="text-right">
                            @if($invoice->payment_status === 'paid' && $invoice->amount_paid)
                                <span class="text-green-600 font-semibold">UGX {{ number_format($invoice->amount_paid, 0) }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($invoice->payment_status === 'paid' && $invoice->change_amount && $invoice->change_amount > 0)
                                <span class="text-orange-600">UGX {{ number_format($invoice->change_amount, 0) }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="text-left text-xs">
                            @if($invoice->payment_method)
                                @if($invoice->payment_method == 'cash') 💵 Cash
                                @elseif($invoice->payment_method == 'card') 💳 Card
                                @elseif($invoice->payment_method == 'mobile_money') 📱 Mobile Money
                                @else {{ ucfirst($invoice->payment_method) }}
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-center">
                            @if($invoice->payment_status === 'unpaid')
                                <span class="status-badge status-unpaid">Unpaid</span>
                            @else
                                <span class="status-badge status-paid">Paid</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('bar.cashier.orders.show', $invoice->id) }}" class="btn-view">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                @if($invoice->payment_status === 'unpaid')
                                <button onclick="openPaymentModal({{ $invoice->id }}, {{ $invoice->total_amount }}, '{{ $invoice->order_number }}')" class="btn-pay">
                                    <i class="fas fa-credit-card mr-1"></i> Pay
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-400">
                            <i class="fas fa-receipt text-4xl mb-2 block"></i>
                            No invoices found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl w-96 max-w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">
                <i class="fas fa-credit-card text-green-600 mr-2"></i> Process Payment
            </h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-100 p-3 rounded text-center">
                <span class="text-sm text-gray-600">Invoice #</span>
                <div class="font-mono font-bold" id="modalInvoiceNumber"></div>
                <div class="text-2xl font-bold text-orange-600 mt-1" id="modalTotal">UGX 0</div>
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
                <i class="fas fa-check-circle mr-2"></i> Complete Payment
            </button>
        </div>
    </div>
</div>

<script>
    let currentInvoiceId = null;
    let currentTotalAmount = 0;

    function performSearch() {
        const searchTerm = document.getElementById('searchInput').value;
        const currentUrl = new URL(window.location.href);
        if (searchTerm) {
            currentUrl.searchParams.set('search', searchTerm);
        } else {
            currentUrl.searchParams.delete('search');
        }
        window.location.href = currentUrl.toString();
    }

    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    function openPaymentModal(invoiceId, totalAmount, invoiceNumber) {
        currentInvoiceId = invoiceId;
        currentTotalAmount = totalAmount;
        document.getElementById('modalInvoiceNumber').innerText = invoiceNumber;
        document.getElementById('modalTotal').innerText = `UGX ${totalAmount.toLocaleString()}`;
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('amountReceived').value = '';
        document.getElementById('changeDue').innerHTML = '';
        document.getElementById('paymentMethod').value = 'cash';
        document.getElementById('cashSection').style.display = 'block';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        currentInvoiceId = null;
        currentTotalAmount = 0;
    }

    document.getElementById('paymentMethod').addEventListener('change', function() {
        const cashSection = document.getElementById('cashSection');
        if (this.value === 'cash') {
            cashSection.style.display = 'block';
        } else {
            cashSection.style.display = 'none';
            document.getElementById('changeDue').innerHTML = '';
        }
    });

    document.getElementById('amountReceived').addEventListener('input', function() {
        const received = parseFloat(this.value) || 0;
        const total = currentTotalAmount;
        const diff = received - total;
        const changeDueDiv = document.getElementById('changeDue');

        if (received <= 0) {
            changeDueDiv.innerHTML = '';
        } else if (diff >= 0) {
            changeDueDiv.innerHTML = `<span class="text-green-600 font-semibold">Change: UGX ${diff.toLocaleString()}</span>`;
        } else {
            changeDueDiv.innerHTML = `<span class="text-red-600 font-semibold">Remaining: UGX ${Math.abs(diff).toLocaleString()}</span>`;
        }
    });

    async function processPayment() {
        const method = document.getElementById('paymentMethod').value;
        let amountPaid = currentTotalAmount;
        let changeAmount = 0;

        if (method === 'cash') {
            const received = parseFloat(document.getElementById('amountReceived').value) || 0;
            if (received < currentTotalAmount) {
                alert('Insufficient amount received. Please enter the full amount.');
                return;
            }
            amountPaid = received;
            changeAmount = received - currentTotalAmount;
        }

        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

        try {
            const response = await fetch(`{{ url("bar/cashier/sale") }}/${currentInvoiceId}/pay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    payment_method: method,
                    amount_paid: amountPaid,
                    change_amount: changeAmount
                })
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = `{{ url("bar/cashier/receipt") }}/${currentInvoiceId}`;
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Complete Payment';
            }
        } catch (error) {
            alert('Error: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Complete Payment';
        }
    }
</script>
@endsection
