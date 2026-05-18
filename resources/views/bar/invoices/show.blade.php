{{-- resources/views/bar/invoices/show.blade.php --}}

@extends('layouts.bar')

@section('title', 'Invoice Details')

@section('page-title', 'Invoice Details')

@section('content')
<style>
    .info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .info-header {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
    }
    .info-body {
        padding: 1.5rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .info-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 0.8rem;
    }
    .info-value {
        font-weight: 500;
        color: #1f2937;
        font-size: 0.85rem;
    }
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
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
    .btn-back {
        background: #6b7280;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
    }
    .btn-back:hover {
        background: #4b5563;
    }
    .btn-print {
        background: #3b82f6;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
    }
    .btn-print:hover {
        background: #2563eb;
    }
</style>

<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('bar.invoices.index') }}" class="btn-back">
        <i class="fas fa-arrow-left mr-1"></i> Back to Invoices
    </a>
    <button onclick="window.print()" class="btn-print">
        <i class="fas fa-print mr-1"></i> Print
    </button>
</div>

<div class="info-card">
    <div class="info-header">
        <i class="fas fa-info-circle mr-2 text-blue-600"></i> Invoice Information
    </div>
    <div class="info-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="info-row">
                    <span class="info-label">Invoice Number:</span>
                    <span class="info-value font-mono font-bold">{{ $invoice->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date Created:</span>
                    <span class="info-value">{{ $invoice->created_at->format('d/m/Y h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer Type:</span>
                    <span class="info-value">{{ ucfirst(str_replace('_', ' ', $invoice->customer_type ?? 'dine_in')) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Made By:</span>
                    <span class="info-value font-semibold text-blue-600">
                        <i class="fas fa-user mr-1"></i> {{ $invoice->cashier->first_name ?? 'N/A' }} {{ $invoice->cashier->last_name ?? '' }}
                    </span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge {{ $invoice->payment_status === 'unpaid' ? 'status-unpaid' : 'status-paid' }}">
                            {{ ucfirst($invoice->payment_status ?? 'unpaid') }}
                        </span>
                    </span>
                </div>
                @if($invoice->payment_status === 'paid')
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">
                        @if($invoice->payment_method == 'cash') 💵 Cash
                        @elseif($invoice->payment_method == 'card') 💳 Card
                        @elseif($invoice->payment_method == 'mobile_money') 📱 Mobile Money
                        @else {{ ucfirst($invoice->payment_method ?? 'N/A') }}
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Processed By:</span>
                    <span class="info-value">
                        <i class="fas fa-cash-register mr-1"></i> {{ $invoice->cashier->first_name ?? 'N/A' }} {{ $invoice->cashier->last_name ?? '' }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($invoice->payment_status === 'paid')
<div class="info-card">
    <div class="info-header">
        <i class="fas fa-credit-card mr-2 text-green-600"></i> Payment Details
    </div>
    <div class="info-body">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="info-row">
                <span class="info-label">Total Amount:</span>
                <span class="info-value font-semibold">UGX {{ number_format($invoice->total_amount, 0) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Amount Paid:</span>
                <span class="info-value font-semibold text-green-600">UGX {{ number_format($invoice->amount_paid ?? $invoice->total_amount, 0) }}</span>
            </div>
            @if(($invoice->change_amount ?? 0) > 0)
            <div class="info-row">
                <span class="info-label">Change Returned:</span>
                <span class="info-value font-semibold text-orange-600">UGX {{ number_format($invoice->change_amount, 0) }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<div class="info-card">
    <div class="info-header">
        <i class="fas fa-boxes mr-2 text-blue-600"></i> Items Sold
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </td>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @foreach($invoice->items as $item)
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td class="font-medium">{{ $item->item_name }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($item->unit_price, 0) }}</td>
                    <td class="text-right font-semibold">UGX {{ number_format($item->total_price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="text-right font-bold">Subtotal:</td>
                    <td class="text-right font-bold">UGX {{ number_format($invoice->subtotal ?? $invoice->total_amount, 0) }}</td>
                </tr>
                @if($invoice->tax_amount && $invoice->tax_amount > 0)
                <tr>
                    <td colspan="4" class="text-right">Tax (18%):</td>
                    <td class="text-right">UGX {{ number_format($invoice->tax_amount, 0) }}</td>
                </tr>
                @endif
                <tr class="border-t-2 border-gray-200">
                    <td colspan="4" class="text-right font-bold text-lg">TOTAL:</td>
                    <td class="text-right font-bold text-lg text-orange-600">UGX {{ number_format($invoice->total_amount, 0) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($invoice->department_notes)
<div class="info-card">
    <div class="info-header">
        <i class="fas fa-sticky-note mr-2 text-blue-600"></i> Notes
    </div>
    <div class="info-body">
        <p class="text-sm text-gray-700">{{ $invoice->department_notes }}</p>
    </div>
</div>
@endif

<script>
    window.onbeforeprint = function() {
        document.body.style.margin = '0';
        document.body.style.padding = '0';
    };
    window.onafterprint = function() {
        document.body.style.margin = '';
        document.body.style.padding = '';
    };
</script>
@endsection
