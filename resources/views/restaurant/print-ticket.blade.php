<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Ticket - {{ $ticket->ticket_number }}</title>
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
        .ticket-container {
            max-width: 280px;
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
        .supplement {
            color: #ea580c;
            font-size: 10px;
            margin-left: 4px;
            margin-top: 2px;
        }
        .comment {
            color: #666;
            font-size: 10px;
            font-style: italic;
            margin-left: 8px;
            margin-top: 2px;
        }
        .ingredients {
            color: #4b5563;
            font-size: 9px;
            margin-top: 4px;
            margin-left: 4px;
            padding-left: 8px;
            border-left: 2px solid #ea580c;
        }
        .ingredient-item {
            margin-top: 2px;
        }
        .item-name {
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
            max-width: 280px;
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 4px 0;
            vertical-align: top;
        }
        .qty-col {
            text-align: center;
            width: 40px;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="text-center">
            <h3 style="margin: 0;">PATIO BELLA</h3>
            <p style="margin: 0; font-size: 10px;">Cafe Order Ticket</p>
            <hr>
        </div>

        <div>
            <p><strong>Ticket:</strong> {{ $ticket->ticket_number }}</p>
            <p><strong>Table:</strong> {{ $ticket->table_number }}</p>
            <p><strong>Waiter:</strong> {{ $ticket->waiter_name }}</p>
            <p><strong>Time:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            <hr>
        </div>

        <table>
            @foreach($items as $item)
            <tr>
                <td class="text-left">
                    <div class="item-name">{{ $item['quantity'] }}x {{ $item['item_name'] }}</div>
                    @if(!empty($item['supplement']))
                    <div class="supplement">➕ {{ $item['supplement'] }}</div>
                    @endif
                    @if(!empty($item['ingredients']) && count($item['ingredients']) > 0)
                    <div class="ingredients">
                        <div>📋 Ingredients:</div>
                        @foreach($item['ingredients'] as $ingredient)
                        <div class="ingredient-item">• {{ number_format($ingredient['quantity'], 2) }} {{ $ingredient['unit'] }} {{ $ingredient['name'] }}</div>
                        @endforeach
                    </div>
                    @endif
                    @if(!empty($item['comments']))
                    <div class="comment">💬 {{ $item['comments'] }}</div>
                    @endif
                </td>
                <td class="qty-col">{{ $item['quantity'] }}</td>
            </tr>
            @endforeach
        </table>

        <hr>

        <div class="text-center">
            <p>--- Please prepare ---</p>
            <hr>
            <p style="font-size: 10px;">Print Time: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">🖨️ Print Ticket</button>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
