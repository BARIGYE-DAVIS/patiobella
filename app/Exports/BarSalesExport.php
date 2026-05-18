<?php
// app/Exports/BarSalesExport.php

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

class BarSalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $orders;
    protected $startDate;
    protected $endDate;
    protected $exportType;
    protected $rowData;

    public function __construct($orders, $startDate = null, $endDate = null, $exportType = 'current')
    {
        $this->orders = $orders;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->exportType = $exportType;
        $this->rowData = [];
    }

    public function collection()
    {
        // Transform orders into rows (one row per item)
        $rows = collect();

        foreach ($this->orders as $order) {
            $firstItem = true;
            $itemCount = $order->items->count();
            $currentRow = 0;

            foreach ($order->items as $item) {
                $rows->push((object)[
                    'order_number' => $firstItem ? $order->order_number : '',
                    'date' => $firstItem ? $order->created_at->format('d/m/Y') : '',
                    'time' => $firstItem ? $order->created_at->format('h:i A') : '',
                    'cashier' => $firstItem ? ($order->cashier->first_name ?? 'N/A') : '',
                    'customer_type' => $firstItem ? ucfirst(str_replace('_', ' ', $order->customer_type ?? 'dine_in')) : '',
                    'payment_method' => $firstItem ? ucfirst($order->payment_method ?? 'N/A') : '',
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'item_total' => $item->total_price,
                    'amount_paid' => $firstItem ? ($order->amount_paid ?? $order->total_amount) : '',
                    'change_amount' => $firstItem ? ($order->change_amount ?? 0) : '',
                    'order_total' => $firstItem ? $order->total_amount : '',
                    'is_first_item' => $firstItem,
                    'items_count' => $itemCount,
                    'row_index' => $currentRow
                ]);

                $firstItem = false;
                $currentRow++;
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
            'Cashier',
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
            $row->cashier,
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
            'A' => 20,
            'B' => 12,
            'C' => 10,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 30,
            'H' => 10,
            'I' => 15,
            'J' => 15,
            'K' => 18,
            'L' => 15,
            'M' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rows = $this->collection();
                $rowIndex = 2; // Start from row 2 (after header)

                foreach ($rows as $row) {
                    if ($row->is_first_item && $row->items_count > 1) {
                        // Merge cells for invoice number
                        $endRow = $rowIndex + $row->items_count - 1;
                        if ($endRow > $rowIndex) {
                            $sheet->mergeCells("A{$rowIndex}:A{$endRow}");
                            $sheet->mergeCells("B{$rowIndex}:B{$endRow}");
                            $sheet->mergeCells("C{$rowIndex}:C{$endRow}");
                            $sheet->mergeCells("D{$rowIndex}:D{$endRow}");
                            $sheet->mergeCells("E{$rowIndex}:E{$endRow}");
                            $sheet->mergeCells("F{$rowIndex}:F{$endRow}");
                            $sheet->mergeCells("K{$rowIndex}:K{$endRow}");
                            $sheet->mergeCells("L{$rowIndex}:L{$endRow}");
                            $sheet->mergeCells("M{$rowIndex}:M{$endRow}");

                            // Center align merged cells
                            $sheet->getStyle("A{$rowIndex}:A{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("B{$rowIndex}:B{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("C{$rowIndex}:C{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("D{$rowIndex}:D{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("E{$rowIndex}:E{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle("F{$rowIndex}:F{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }
                    }
                    $rowIndex++;
                }

                // Add border around the data
                $lastRow = $rowIndex - 1;
                $sheet->getStyle("A1:M{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
