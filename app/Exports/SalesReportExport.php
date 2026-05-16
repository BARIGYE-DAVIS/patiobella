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
 *   Sheet 2 — Menu Item Orders
 *   Sheet 3 — Other Item Orders
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
    protected string $MENU_BG  = 'D1FAE5';  // green tint for menu rows
    protected string $OTHER_BG = 'FEF3C7';  // amber tint for other rows
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

    // Build totals row appended to a collection
    protected function totalsRow(array $cells): array
    {
        return $cells;
    }
}

// ══════════════════════════════════════════════════════════════════
// SHEET 1 — SUMMARY
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
        // Bold the section headings
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
// SHEET 2 — MENU ITEM ORDERS
// ══════════════════════════════════════════════════════════════════
class ExcelMenuOrdersSheet extends BaseExcelSheet
{
    private Collection $menuOrders;

    public function __construct(
        private string     $from,
        private string     $to,
        private Collection $salesList,
        private array      $menuItemNames
    ) {
        $this->menuOrders = $salesList->filter(
            fn($s) => $s->items->filter(fn($i) => in_array($i->item_name, $menuItemNames))->count() > 0
        )->values();
    }

    public function title(): string { return 'Menu Item Orders'; }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 14, 'C' => 12, 'D' => 18, 'E' => 50, 'F' => 18, 'G' => 20];
    }

    public function headings(): array
    {
        return ['Invoice #', 'Date', 'Time', 'Cashier', 'Menu Items Ordered', 'Payment Method', 'Amount (UGX)'];
    }

    public function collection(): Collection
    {
        $rows = $this->menuOrders->map(function ($order) {
            $menuItems = $order->items
                ->filter(fn($i) => in_array($i->item_name, $this->menuItemNames))
                ->map(fn($i) => $i->item_name . ' x' . $i->quantity)
                ->implode(', ');

            return [
                $order->order_number,
                $order->created_at->format('d/m/Y'),
                $order->created_at->format('h:i A'),
                $order->cashier->first_name ?? '—',
                $menuItems,
                ucwords(str_replace('_', ' ', $order->payment_method)),
                number_format($order->total_amount, 0),
            ];
        });

        // Totals row
        $rows->push([
            'TOTAL',
            '',
            '',
            $this->menuOrders->count() . ' orders',
            '',
            '',
            number_format($this->menuOrders->sum('total_amount'), 0),
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header row
        $styles = [
            1 => $this->headerStyle($this->GREEN),
        ];

        // Zebra rows — green tint for menu
        for ($r = 2; $r < $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:G{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                ]);
            }
        }

        // Totals row
        $sheet->getStyle("A{$lastRow}:G{$lastRow}")->applyFromArray($this->totalRowStyle());

        // Borders
        $sheet->getStyle("A2:G{$lastRow}")->applyFromArray($this->borderStyle());

        return $styles;
    }
}

// ══════════════════════════════════════════════════════════════════
// SHEET 3 — OTHER ITEM ORDERS
// ══════════════════════════════════════════════════════════════════
class ExcelOtherOrdersSheet extends BaseExcelSheet
{
    private Collection $otherOrders;

    public function __construct(
        private string     $from,
        private string     $to,
        private Collection $salesList,
        private array      $menuItemNames
    ) {
        $this->otherOrders = $salesList->filter(
            fn($s) => $s->items->filter(fn($i) => !in_array($i->item_name, $menuItemNames))->count() > 0
        )->values();
    }

    public function title(): string { return 'Other Item Orders'; }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 14, 'C' => 12, 'D' => 18, 'E' => 50, 'F' => 18, 'G' => 20];
    }

    public function headings(): array
    {
        return ['Invoice #', 'Date', 'Time', 'Cashier', 'Other Items Ordered', 'Payment Method', 'Amount (UGX)'];
    }

    public function collection(): Collection
    {
        $rows = $this->otherOrders->map(function ($order) {
            $otherItems = $order->items
                ->filter(fn($i) => !in_array($i->item_name, $this->menuItemNames))
                ->map(fn($i) => $i->item_name . ' x' . $i->quantity)
                ->implode(', ');

            return [
                $order->order_number,
                $order->created_at->format('d/m/Y'),
                $order->created_at->format('h:i A'),
                $order->cashier->first_name ?? '—',
                $otherItems,
                ucwords(str_replace('_', ' ', $order->payment_method)),
                number_format($order->total_amount, 0),
            ];
        });

        // Totals row
        $rows->push([
            'TOTAL',
            '',
            '',
            $this->otherOrders->count() . ' orders',
            '',
            '',
            number_format($this->otherOrders->sum('total_amount'), 0),
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $styles = [
            1 => $this->headerStyle($this->GRAY),
        ];

        // Zebra rows — amber tint
        for ($r = 2; $r < $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:G{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']],
                ]);
            }
        }

        $sheet->getStyle("A{$lastRow}:G{$lastRow}")->applyFromArray($this->totalRowStyle());
        $sheet->getStyle("A2:G{$lastRow}")->applyFromArray($this->borderStyle());

        return $styles;
    }
}

// ══════════════════════════════════════════════════════════════════
// SHEET 4 — TOP PRODUCTS (menu + other, side by side)
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

        // Totals
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

        // Left side header — green
        $sheet->getStyle('A1:D1')->applyFromArray($this->headerStyle($this->GREEN));
        // Right side header — gray
        $sheet->getStyle('E1:H1')->applyFromArray($this->headerStyle($this->GRAY));

        // Zebra - left (green tint) / right (amber tint)
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

        // Totals row
        $sheet->getStyle("A{$lastRow}:D{$lastRow}")->applyFromArray($this->totalRowStyle());
        $sheet->getStyle("E{$lastRow}:H{$lastRow}")->applyFromArray($this->totalRowStyle());

        // Borders
        $sheet->getStyle("A2:H{$lastRow}")->applyFromArray($this->borderStyle());

        return [];
    }
}
