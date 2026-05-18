<?php
// app/Exports/BarCashierSalesExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BarCashierSalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $orders;
    protected $startDate;
    protected $endDate;
    protected $exportType;

    public function __construct($orders, $startDate = null, $endDate = null, $exportType = 'current')
    {
        $this->orders = $orders;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->exportType = $exportType;
    }

    public function collection()
    {
        // Transform orders into rows (one row per item)
        $rows = collect();

        foreach ($this->orders as $order) {
            $firstItem = true;
            $itemCount = $order->items->count();

            foreach ($order->items as $item) {
                $rows->push((object)[
                    'order_number' => $order->order_number,
                    'date' => $order->created_at->format('d/m/Y'),
                    'time' => $order->created_at->format('h:i A'),
                    'customer_type' => ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')),
                    'payment_method' => ucfirst($order->payment_method ?? 'N/A'),
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'item_total' => $item->total_price,
                    'amount_paid' => $order->amount_paid ?? $order->total_amount,
                    'change_amount' => $order->change_amount ?? 0,
                    'order_total' => $order->total_amount,
                    'is_first_item' => $firstItem,
                    'items_count' => $itemCount
                ]);

                $firstItem = false;
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Date',
            'Time',
            'Customer Type',
            'Payment Method',
            'Item Name',
            'Quantity',
            'Unit Price (UGX)',
            'Item Total (UGX)',
            'Amount Paid (UGX)',
            'Change (UGX)',
            'Order Total (UGX)'
        ];
    }

    public function map($row): array
    {
        return [
            $row->order_number,
            $row->date,
            $row->time,
            $row->customer_type,
            $row->payment_method,
            $row->item_name,
            $row->quantity,
            $row->unit_price,
            $row->item_total,
            $row->amount_paid,
            $row->change_amount,
            $row->order_total,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDE68A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 12,
            'C' => 10,
            'D' => 15,
            'E' => 15,
            'F' => 35,
            'G' => 10,
            'H' => 18,
            'I' => 18,
            'J' => 18,
            'K' => 15,
            'L' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rows = $this->collection();
                $currentRow = 2;
                $rowSpanMap = [];

                // First pass: calculate row spans
                foreach ($rows as $index => $row) {
                    $orderNumber = $row->order_number;
                    if (!isset($rowSpanMap[$orderNumber])) {
                        $rowSpanMap[$orderNumber] = 0;
                    }
                    $rowSpanMap[$orderNumber]++;
                }

                // Second pass: apply merging
                $processedOrders = [];
                foreach ($rows as $index => $row) {
                    $orderNumber = $row->order_number;

                    if (!in_array($orderNumber, $processedOrders)) {
                        $span = $rowSpanMap[$orderNumber];
                        if ($span > 1) {
                            $endRow = $currentRow + $span - 1;

                            // Merge columns A through E and J through L
                            $sheet->mergeCells("A{$currentRow}:A{$endRow}");
                            $sheet->mergeCells("B{$currentRow}:B{$endRow}");
                            $sheet->mergeCells("C{$currentRow}:C{$endRow}");
                            $sheet->mergeCells("D{$currentRow}:D{$endRow}");
                            $sheet->mergeCells("E{$currentRow}:E{$endRow}");
                            $sheet->mergeCells("J{$currentRow}:J{$endRow}");
                            $sheet->mergeCells("K{$currentRow}:K{$endRow}");
                            $sheet->mergeCells("L{$currentRow}:L{$endRow}");

                            // Center align merged cells vertically
                            $sheet->getStyle("A{$currentRow}:A{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("B{$currentRow}:B{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("C{$currentRow}:C{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("D{$currentRow}:D{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("E{$currentRow}:E{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }
                        $processedOrders[] = $orderNumber;
                    }
                    $currentRow++;
                }

                // Add borders to all data
                $lastRow = $currentRow - 1;
                if ($lastRow >= 2) {
                    $sheet->getStyle("A1:L{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // Freeze the header row
                $sheet->freezePane('A2');
            },
        ];
    }
}
