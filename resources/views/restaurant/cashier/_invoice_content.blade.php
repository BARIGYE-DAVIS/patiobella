{{-- resources/views/restaurant/cashier/_invoice_content.blade.php --}}
{{-- Shared by invoice.blade.php (screen + print) --}}

{{-- Header --}}
<div style="text-align:center; margin-bottom:6px;">
    <div style="font-size:15px; font-weight:bold; letter-spacing:1px;">PATIO BELLA</div>
    <div style="font-size:9px; margin-top:2px;">Restaurant & Lounge</div>
    <div style="font-size:9px;">Kampala Road, Kampala</div>
    <div style="font-size:9px;">Tel: +256 XXX XXX XXX</div>
    <div style="margin:5px 0; border-top:1px dashed #000;"></div>
    <div style="font-size:11px; font-weight:bold;">TAX INVOICE</div>
    <div style="font-size:10px; font-weight:bold;">{{ $order->order_number }}</div>
    <div style="margin:5px 0; border-top:1px dashed #000;"></div>
</div>

{{-- Details --}}
<div style="margin-bottom:4px;">
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
        <span style="color: {{ $order->payment_status === 'paid' ? '#2ecc71' : '#e67e22' }}; font-weight: bold;">
            {{ strtoupper($order->payment_status ?? 'UNPAID') }}
        </span>
    </div>
</div>
<div style="border-top:1px dashed #000; margin:5px 0;"></div>

{{-- Items header --}}
<div style="display:flex; justify-content:space-between; font-weight:bold; font-size:9px; border-bottom:1px dotted #000; padding-bottom:3px; margin-bottom:4px;">
    <span style="width:46%;">ITEM</span>
    <span style="width:14%; text-align:center;">QTY</span>
    <span style="width:20%; text-align:right;">PRICE</span>
    <span style="width:20%; text-align:right;">TOTAL</span>
</div>

{{-- Items --}}
@foreach($order->items as $item)
<div style="display:flex; justify-content:space-between; font-size:9px; margin:4px 0;">
    <span style="width:46%;">{{ \Str::limit($item->item_name, 18) }}</span>
    <span style="width:14%; text-align:center;">{{ number_format($item->quantity, 0) }}</span>
    <span style="width:20%; text-align:right;">{{ number_format($item->unit_price, 0) }}</span>
    <span style="width:20%; text-align:right; font-weight:bold;">{{ number_format($item->total_price, 0) }}</span>
</div>
@endforeach

<div style="border-top:1px dashed #000; margin:5px 0;"></div>

{{-- Total --}}
<div style="display:flex; justify-content:space-between; font-size:12px; font-weight:bold; margin:4px 0;">
    <span>TOTAL:</span>
    <span>UGX {{ number_format($order->total_amount, 0) }}</span>
</div>
<div style="border-top:1px dashed #000; margin:5px 0;"></div>

{{-- Footer --}}
<div style="text-align:center; margin-top:8px; font-size:9px;">
    <div>Thank you for your business!</div>
    <div style="margin-top:3px;">** Original Invoice **</div>
</div>
