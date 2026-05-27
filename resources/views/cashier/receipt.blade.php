<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
            background: white;
        }
        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        hr {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 8px 0;
        }
        .border-double {
            border-top: 2px double #000;
            padding-top: 5px;
        }
        .bold {
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            button {
                display: none;
            }
        }
        .print-btn {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 20px auto;
            padding: 10px;
            background: #ea580c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
        }
        .print-btn:hover {
            background: #c2410c;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="text-center">
            <h3 style="margin: 0;">PATIO BELLA RESTAURANT</h3>
            <p style="margin: 0; font-size: 10px;">Arena Mall, Kampala</p>
            <p style="margin: 0; font-size: 10px;">Tel: +256 777 143 020</p>
            <hr>
        </div>

        <div>
            <p><strong>Receipt #:</strong> {{ $order->order_number }}</p>
            <p><strong>Date:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</p>
            <p><strong>Table:</strong> {{ $order->table_number ?? 'N/A' }}</p>
            <p><strong>Waiter:</strong> {{ $order->waiter->first_name ?? '' }} {{ $order->waiter->last_name ?? '' }}</p>
            <p><strong>Cashier:</strong> {{ Auth::user()->first_name ?? '' }} {{ Auth::user()->last_name ?? '' }}</p>
            <hr>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th class="text-left">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="text-left">{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0) }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right bold pt-2">TOTAL:</td>
                    <td class="text-right bold pt-2">{{ number_format($order->total_amount, 0) }} UGX</td>
                </tr>
                @if($order->amount_paid)
                <tr>
                    <td colspan="3" class="text-right">Amount Paid:</td>
                    <td class="text-right">{{ number_format($order->amount_paid, 0) }} UGX</td>
                </tr>
                @endif
                @if($order->change_amount > 0)
                <tr>
                    <td colspan="3" class="text-right">Change:</td>
                    <td class="text-right">{{ number_format($order->change_amount, 0) }} UGX</td>
                </tr>
                @endif
            </tfoot>
        </table>

        <hr>

        <div>
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
            <p><strong>Status:</strong> Paid</p>
        </div>

        <hr>

        <div class="text-center">
            <p>Thank you for dining with us!</p>
            <p>Please come again</p>
            <hr>
            <p style="font-size: 10px;">Print Time: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
