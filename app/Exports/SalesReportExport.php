<?php
// app/Exports/SalesReportExport.php

namespace App\Exports;

use App\Models\MenuItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

/**
 * 4-sheet Excel export:
 *   Sheet 1 — Summary stats + payment breakdown
 *   Sheet 2 — Menu Item Orders (with grouped items per invoice)
 *   Sheet 3 — Other Item Orders (with grouped items per invoice)
 *   Sheet 4 — Top Products (menu + other side-by-side)
 */
class SalesReportExport implements WithMultipleSheets
{
    private array   $menuItemNames;
    private Collection $salesList;
    private Collection $topProducts;

    public function __construct(
        private string $from,
        private string $to
    ) {
        $this->menuItemNames = MenuItem::where('is_active', true)->pluck('name')->toArray();

        $this->salesList = SalesOrder::with('items')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->topProducts = SalesOrderItem::selectRaw(
                'item_name, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue'
            )
            ->whereHas('salesOrder', fn($q) =>
                $q->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to)
                  ->where('payment_status', 'paid')
            )
            ->groupBy('item_name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    public function sheets(): array
    {
        return [
            new ExcelSummarySheet($this->from, $this->to, $this->salesList, $this->menuItemNames),
            new ExcelMenuOrdersSheet($this->from, $this->to, $this->salesList, $this->menuItemNames),
            new ExcelOtherOrdersSheet($this->from, $this->to, $this->salesList, $this->menuItemNames),
            new ExcelTopProductsSheet($this->from, $this->to, $this->topProducts, $this->menuItemNames),
        ];
    }
}

// ══════════════════════════════════════════════════════════════════
// BASE SHEET — shared styling helpers
// ══════════════════════════════════════════════════════════════════
abstract class BaseExcelSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected string $ORANGE = 'EA580C';
    protected string $GREEN  = '059669';
    protected string $GRAY   = '4B5563';
    protected string $WHITE  = 'FFFFFF';
    protected string $LIGHT  = 'F9FAFB';
    protected string $MENU_BG  = 'D1FAE5';
    protected string $OTHER_BG = 'FEF3C7';
    protected string $MENU_FG  = '065F46';
    protected string $OTHER_FG = '92400E';

    protected function headerStyle(string $bgHex): array
    {
        return [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgHex]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
    }

    protected function totalRowStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF7ED']],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'EA580C']]],
        ];
    }

    protected function borderStyle(): array
    {
        return [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
            ],
        ];
    }
}

// ══════════════════════════════════════════════════════════════════
// SHEET 1 — SUMMARY (unchanged)
// ══════════════════════════════════════════════════════════════════
class ExcelSummarySheet extends BaseExcelSheet
{
    public function __construct(
        private string     $from,
        private string     $to,
        private Collection $salesList,
        private array      $menuItemNames
    ) {}

    public function title(): string { return 'Summary'; }

    public function columnWidths(): array
    {
        return ['A' => 32, 'B' => 28, 'C' => 28, 'D' => 28];
    }

    public function headings(): array
    {
        return ['Metric', 'Value', '', ''];
    }

    public function collection(): Collection
    {
        $orders = $this->salesList;

        $totalSales   = $orders->sum('total_amount');
        $totalOrders  = $orders->count();
        $avgOrder     = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        $cashSales    = $orders->where('payment_method', 'cash')->sum('total_amount');
        $cardSales    = $orders->where('payment_method', 'card')->sum('total_amount');
        $mobileSales  = $orders->where('payment_method', 'mobile_money')->sum('total_amount');

        $menuRevenue  = $orders->filter(fn($o) => $o->items->filter(fn($i) => in_array($i->item_name, $this->menuItemNames))->count() > 0)->sum('total_amount');
        $otherRevenue = $orders->filter(fn($o) => $o->items->filter(fn($i) => !in_array($i->item_name, $this->menuItemNames))->count() > 0)->sum('total_amount');

        return collect([
            ['── REPORT INFO ──',             '',                                  '', ''],
            ['Report Period',                  $this->from . ' to ' . $this->to,   '', ''],
            ['Generated At',                   now()->format('d/m/Y H:i:s'),       '', ''],
            ['',                               '',                                  '', ''],
            ['── REVENUE SUMMARY ──',          '',                                  '', ''],
            ['Total Revenue (UGX)',             number_format($totalSales, 0),      '', ''],
            ['Total Orders',                    $totalOrders,                        '', ''],
            ['Average Order Value (UGX)',       number_format($avgOrder, 0),         '', ''],
            ['',                               '',                                  '', ''],
            ['── PAYMENT METHODS ──',          '',                                  '', ''],
            ['Cash Sales (UGX)',               number_format($cashSales, 0),        '', ''],
            ['Card Sales (UGX)',               number_format($cardSales, 0),         '', ''],
            ['Mobile Money Sales (UGX)',        number_format($mobileSales, 0),      '', ''],
            ['',                               '',                                  '', ''],
            ['── BY ITEM CATEGORY ──',         '',                                  '', ''],
            ['Menu Item Orders Revenue (UGX)',  number_format($menuRevenue, 0),      '', ''],
            ['Other Item Orders Revenue (UGX)', number_format($otherRevenue, 0),     '', ''],
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        foreach ([1, 5, 10, 15] as $row) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $this->ORANGE]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF7ED']],
            ]);
        }

        return [
            1 => $this->headerStyle($this->ORANGE),
        ];
    }
}

// ══════════════════════════════════════════════════════════════════
// SHEET 2 — MENU ITEM ORDERS (with grouped items per invoice)
// ══════════════════════════════════════════════════════════════════
class ExcelMenuOrdersSheet extends BaseExcelSheet implements WithEvents
{
    private Collection $menuOrders;
    private array $rowSpanMap = [];

    public function __construct(
        private string     $from,
        private string     $to,
        private Collection $salesList,
        private array      $menuItemNames
    ) {
        // Get orders that contain menu items
        $this->menuOrders = $salesList->filter(
            fn($s) => $s->items->filter(fn($i) => in_array($i->item_name, $menuItemNames))->count() > 0
        )->values();

        // Calculate row spans for each order
        foreach ($this->menuOrders as $order) {
            $itemCount = $order->items->filter(fn($i) => in_array($i->item_name, $menuItemNames))->count();
            $this->rowSpanMap[$order->id] = $itemCount;
        }
    }

    public function title(): string { return 'Menu Item Orders'; }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 14, 'C' => 12, 'D' => 18, 'E' => 50, 'F' => 18, 'G' => 20, 'H' => 18, 'I' => 15];
    }

    public function headings(): array
    {
        return ['Invoice #', 'Date', 'Time', 'Cashier', 'Payment Method', 'Item Name', 'Qty', 'Item Total', 'Amount Paid', 'Change'];
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->menuOrders as $order) {
            $menuItems = $order->items->filter(fn($i) => in_array($i->item_name, $this->menuItemNames));
            $firstItem = true;

            foreach ($menuItems as $item) {
                $rows->push([
                    'order_number' => $firstItem ? $order->order_number : '',
                    'date' => $firstItem ? $order->created_at->format('d/m/Y') : '',
                    'time' => $firstItem ? $order->created_at->format('h:i A') : '',
                    'cashier' => $firstItem ? ($order->cashier->first_name ?? '—') : '',
                    'payment_method' => $firstItem ? ucwords(str_replace('_', ' ', $order->payment_method ?? 'N/A')) : '',
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'item_total' => number_format($item->total_price, 0),
                    'amount_paid' => $firstItem ? number_format($order->amount_paid ?? $order->total_amount, 0) : '',
                    'change_amount' => $firstItem ? number_format($order->change_amount ?? 0, 0) : '',
                ]);
                $firstItem = false;
            }
        }

        // Add totals row
        $rows->push([
            'TOTAL', '', '', '', '',
            '',
            number_format($this->menuOrders->sum(fn($o) => $o->items->filter(fn($i) => in_array($i->item_name, $this->menuItemNames))->sum('quantity'))),
            number_format($this->menuOrders->sum(fn($o) => $o->items->filter(fn($i) => in_array($i->item_name, $this->menuItemNames))->sum('total_price')), 0),
            '',
            '',
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => $this->headerStyle($this->GREEN),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rows = $this->collection();
                $currentRow = 2;
                $processedOrders = [];

                // Apply row spans and styling
                foreach ($this->menuOrders as $order) {
                    $span = $this->rowSpanMap[$order->id];

                    if ($span > 1) {
                        $endRow = $currentRow + $span - 1;

                        // Merge columns A, B, C, D, E, I, J (invoice details)
                        $sheet->mergeCells("A{$currentRow}:A{$endRow}");
                        $sheet->mergeCells("B{$currentRow}:B{$endRow}");
                        $sheet->mergeCells("C{$currentRow}:C{$endRow}");
                        $sheet->mergeCells("D{$currentRow}:D{$endRow}");
                        $sheet->mergeCells("E{$currentRow}:E{$endRow}");
                        $sheet->mergeCells("I{$currentRow}:I{$endRow}");
                        $sheet->mergeCells("J{$currentRow}:J{$endRow}");

                        // Center align merged cells vertically
                        $sheet->getStyle("A{$currentRow}:A{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("B{$currentRow}:B{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("C{$currentRow}:C{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("D{$currentRow}:D{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("E{$currentRow}:E{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    $currentRow += $span;
                }

                // Apply zebra striping
                $lastRow = $sheet->getHighestRow();
                for ($r = 2; $r < $lastRow; $r++) {
                    if ($r % 2 === 0) {
                        $sheet->getStyle("A{$r}:J{$r}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                        ]);
                    }
                }

                // Apply borders
                $sheet->getStyle("A2:J{$lastRow}")->applyFromArray($this->borderStyle());

                // Totals row style
                $sheet->getStyle("A{$lastRow}:J{$lastRow}")->applyFromArray($this->totalRowStyle());
            },
        ];
    }
}

// ══════════════════════════════════════════════════════════════════
// SHEET 3 — OTHER ITEM ORDERS (with grouped items per invoice)
// ══════════════════════════════════════════════════════════════════
class ExcelOtherOrdersSheet extends BaseExcelSheet implements WithEvents
{
    private Collection $otherOrders;
    private array $rowSpanMap = [];

    public function __construct(
        private string     $from,
        private string     $to,
        private Collection $salesList,
        private array      $menuItemNames
    ) {
        // Get orders that contain other (non-menu) items
        $this->otherOrders = $salesList->filter(
            fn($s) => $s->items->filter(fn($i) => !in_array($i->item_name, $menuItemNames))->count() > 0
        )->values();

        // Calculate row spans for each order
        foreach ($this->otherOrders as $order) {
            $itemCount = $order->items->filter(fn($i) => !in_array($i->item_name, $menuItemNames))->count();
            $this->rowSpanMap[$order->id] = $itemCount;
        }
    }

    public function title(): string { return 'Other Item Orders'; }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 14, 'C' => 12, 'D' => 18, 'E' => 18, 'F' => 50, 'G' => 10, 'H' => 18, 'I' => 18, 'J' => 15];
    }

    public function headings(): array
    {
        return ['Invoice #', 'Date', 'Time', 'Cashier', 'Payment Method', 'Item Name', 'Qty', 'Item Total', 'Amount Paid', 'Change'];
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->otherOrders as $order) {
            $otherItems = $order->items->filter(fn($i) => !in_array($i->item_name, $this->menuItemNames));
            $firstItem = true;

            foreach ($otherItems as $item) {
                $rows->push([
                    'order_number' => $firstItem ? $order->order_number : '',
                    'date' => $firstItem ? $order->created_at->format('d/m/Y') : '',
                    'time' => $firstItem ? $order->created_at->format('h:i A') : '',
                    'cashier' => $firstItem ? ($order->cashier->first_name ?? '—') : '',
                    'payment_method' => $firstItem ? ucwords(str_replace('_', ' ', $order->payment_method ?? 'N/A')) : '',
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'item_total' => number_format($item->total_price, 0),
                    'amount_paid' => $firstItem ? number_format($order->amount_paid ?? $order->total_amount, 0) : '',
                    'change_amount' => $firstItem ? number_format($order->change_amount ?? 0, 0) : '',
                ]);
                $firstItem = false;
            }
        }

        // Add totals row
        $rows->push([
            'TOTAL', '', '', '', '',
            '',
            number_format($this->otherOrders->sum(fn($o) => $o->items->filter(fn($i) => !in_array($i->item_name, $this->menuItemNames))->sum('quantity'))),
            number_format($this->otherOrders->sum(fn($o) => $o->items->filter(fn($i) => !in_array($i->item_name, $this->menuItemNames))->sum('total_price')), 0),
            '',
            '',
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => $this->headerStyle($this->GRAY),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $currentRow = 2;

                // Apply row spans and styling
                foreach ($this->otherOrders as $order) {
                    $span = $this->rowSpanMap[$order->id];

                    if ($span > 1) {
                        $endRow = $currentRow + $span - 1;

                        // Merge columns A, B, C, D, E, I, J (invoice details)
                        $sheet->mergeCells("A{$currentRow}:A{$endRow}");
                        $sheet->mergeCells("B{$currentRow}:B{$endRow}");
                        $sheet->mergeCells("C{$currentRow}:C{$endRow}");
                        $sheet->mergeCells("D{$currentRow}:D{$endRow}");
                        $sheet->mergeCells("E{$currentRow}:E{$endRow}");
                        $sheet->mergeCells("I{$currentRow}:I{$endRow}");
                        $sheet->mergeCells("J{$currentRow}:J{$endRow}");

                        // Center align merged cells vertically
                        $sheet->getStyle("A{$currentRow}:A{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("B{$currentRow}:B{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("C{$currentRow}:C{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("D{$currentRow}:D{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("E{$currentRow}:E{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    $currentRow += $span;
                }

                // Apply zebra striping
                $lastRow = $sheet->getHighestRow();
                for ($r = 2; $r < $lastRow; $r++) {
                    if ($r % 2 === 0) {
                        $sheet->getStyle("A{$r}:J{$r}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']],
                        ]);
                    }
                }

                // Apply borders
                $sheet->getStyle("A2:J{$lastRow}")->applyFromArray($this->borderStyle());

                // Totals row style
                $sheet->getStyle("A{$lastRow}:J{$lastRow}")->applyFromArray($this->totalRowStyle());
            },
        ];
    }
}

// ══════════════════════════════════════════════════════════════════
// SHEET 4 — TOP PRODUCTS (unchanged)
// ══════════════════════════════════════════════════════════════════
class ExcelTopProductsSheet extends BaseExcelSheet
{
    private Collection $menuProds;
    private Collection $otherProds;

    public function __construct(
        private string     $from,
        private string     $to,
        private Collection $topProducts,
        private array      $menuItemNames
    ) {
        $this->menuProds  = $topProducts->filter(fn($p) =>  in_array($p->item_name, $menuItemNames))->values();
        $this->otherProds = $topProducts->filter(fn($p) => !in_array($p->item_name, $menuItemNames))->values();
    }

    public function title(): string { return 'Top Products'; }

    public function columnWidths(): array
    {
        return ['A' => 4, 'B' => 35, 'C' => 14, 'D' => 18, 'E' => 4, 'F' => 35, 'G' => 14, 'H' => 18];
    }

    public function headings(): array
    {
        return ['#', 'Menu Item', 'Qty Sold', 'Revenue (UGX)', '#', 'Other Item', 'Qty Sold', 'Revenue (UGX)'];
    }

    public function collection(): Collection
    {
        $count = max($this->menuProds->count(), $this->otherProds->count());
        $rows  = collect();

        for ($i = 0; $i < $count; $i++) {
            $m = $this->menuProds->get($i);
            $o = $this->otherProds->get($i);

            $rows->push([
                $m ? ($i + 1) : '',
                $m ? $m->item_name   : '',
                $m ? number_format($m->total_quantity) : '',
                $m ? number_format($m->total_revenue, 0) : '',
                $o ? ($i + 1) : '',
                $o ? $o->item_name   : '',
                $o ? number_format($o->total_quantity) : '',
                $o ? number_format($o->total_revenue, 0) : '',
            ]);
        }

        $rows->push([
            '',
            'MENU TOTAL',
            number_format($this->menuProds->sum('total_quantity')),
            number_format($this->menuProds->sum('total_revenue'), 0),
            '',
            'OTHER TOTAL',
            number_format($this->otherProds->sum('total_quantity')),
            number_format($this->otherProds->sum('total_revenue'), 0),
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:D1')->applyFromArray($this->headerStyle($this->GREEN));
        $sheet->getStyle('E1:H1')->applyFromArray($this->headerStyle($this->GRAY));

        for ($r = 2; $r < $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                ]);
                $sheet->getStyle("E{$r}:H{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']],
                ]);
            }
        }

        $sheet->getStyle("A{$lastRow}:D{$lastRow}")->applyFromArray($this->totalRowStyle());
        $sheet->getStyle("E{$lastRow}:H{$lastRow}")->applyFromArray($this->totalRowStyle());
        $sheet->getStyle("A2:H{$lastRow}")->applyFromArray($this->borderStyle());

        return [];
    }
}
