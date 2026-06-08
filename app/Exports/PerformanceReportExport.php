<?php

namespace App\Exports;

use App\Models\PerformanceReport;
use App\Models\BusinessSetting;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PerformanceReportExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $report;
    protected $businessSettings;

    public function __construct(PerformanceReport $report)
    {
        $this->report = $report;

        // Get business settings dynamically
        $this->businessSettings = [];
        $settings = BusinessSetting::where('is_active', true)->get();
        foreach($settings as $setting) {
            $this->businessSettings[$setting->key] = $setting->value;
        }
    }

    private function getBusinessValue($key, $default = '')
    {
        return $this->businessSettings[$key] ?? $default;
    }

    public function array(): array
    {
        $companyName = $this->getBusinessValue('company_name', 'PATIO BELLA');
        $companyAddress = $this->getBusinessValue('address', '');
        $companyCity = $this->getBusinessValue('city', '');
        $companyCountry = $this->getBusinessValue('country', 'Uganda');
        $companyPhone = $this->getBusinessValue('phone', '');
        $companyEmail = $this->getBusinessValue('email', '');

        $fullAddress = trim($companyAddress . ' ' . $companyCity . ' ' . $companyCountry);

        $data = [];

        // ============================================
        // HEADER WITH COMPANY INFO
        // ============================================
        $data[] = [strtoupper($companyName)];
        $data[] = ['Performance Report'];
        $data[] = [$fullAddress];
        $data[] = ['Tel: ' . $companyPhone . ' | Email: ' . $companyEmail];
        $data[] = [];
        $data[] = [];

        // ============================================
        // REPORT INFORMATION
        // ============================================
        $data[] = ['REPORT INFORMATION'];
        $data[] = ['Report #:', $this->report->report_number];
        $data[] = ['Department:', $this->report->department->name ?? 'N/A'];
        $data[] = ['Report Date:', $this->report->report_date->format('F d, Y')];
        $data[] = ['Generated On:', now()->format('F d, Y H:i:s')];
        $data[] = [];

        // ============================================
        // SECTION 1: WITH GIFTS INCLUDED
        // ============================================
        $data[] = ['SALES (GIFTS INCLUDED)'];
        $data[] = ['Total Sales:', number_format($this->report->total_sales, 0) . ' UGX'];
        $data[] = ['Cost of Goods Sold:', number_format($this->report->total_cogs, 0) . ' UGX'];
        $data[] = ['Gifts:', number_format($this->report->gifts_amount ?? 0, 0) . ' UGX'];
        $data[] = ['Profit:', number_format($this->report->total_profit, 0) . ' UGX'];
        $data[] = ['Profit Margin:', number_format($this->report->profit_margin, 2) . '%'];
        $data[] = ['COGS %:', number_format($this->report->total_sales > 0 ? ($this->report->total_cogs / $this->report->total_sales) * 100 : 0, 2) . '%'];
        $data[] = [];

        // ============================================
        // SECTION 2: WITHOUT GIFTS
        // ============================================
        $salesWithoutGifts = $this->report->sales_without_gifts ?? ($this->report->total_sales - ($this->report->gifts_amount ?? 0));
        $profitWithoutGifts = $this->report->profit_without_gifts ?? ($salesWithoutGifts - $this->report->total_cogs);
        $marginWithoutGifts = $this->report->profit_margin_without_gifts ?? ($salesWithoutGifts > 0 ? ($profitWithoutGifts / $salesWithoutGifts) * 100 : 0);

        $data[] = ['PERFORMANCE SUMMARY: GIFTS EXCLUDED'];
        $data[] = ['Sales (Gifts Removed):', number_format($salesWithoutGifts, 0) . ' UGX'];
        $data[] = ['Cost of Goods Sold:', number_format($this->report->total_cogs, 0) . ' UGX'];
        $data[] = ['Profit:', number_format($profitWithoutGifts, 0) . ' UGX'];
        $data[] = ['Profit Margin:', number_format($marginWithoutGifts, 2) . '%'];
        $data[] = ['COGS %:', number_format($salesWithoutGifts > 0 ? ($this->report->total_cogs / $salesWithoutGifts) * 100 : 0, 2) . '%'];
        $data[] = [];

        // ============================================
        // SECTION 3: MENU ITEMS
        // ============================================
        $data[] = ['MENU ITEMS BREAKDOWN'];
        $data[] = ['Menu Item', 'Qty Sold', 'Selling Price (UGX)', 'COGS (UGX)', 'Profit Margin %', 'Profit (UGX)'];

        $groupedItems = $this->report->items->groupBy('menu_item_id');
        foreach($groupedItems as $menuItemId => $items) {
            $firstItem = $items->first();
            $totalCogs = $items->sum('cogs');
            $totalRevenue = $firstItem->quantity_sold * $firstItem->selling_price;
            $profit = $totalRevenue - $totalCogs;
            $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;

            $data[] = [
                $firstItem->menuItem->name ?? 'N/A',
                $firstItem->quantity_sold,
                number_format($firstItem->selling_price, 0),
                number_format($totalCogs, 0),
                number_format($profitMargin, 1),
                number_format($profit, 0),
            ];
        }

        // Totals row for menu items
        $data[] = ['TOTAL', '', '', number_format($this->report->total_cogs, 0), number_format($this->report->profit_margin, 1) . '%', number_format($this->report->total_profit, 0)];
        $data[] = [];

        // ============================================
        // SECTION 4: TOP MOVING STOCK ITEMS
        // ============================================
        $data[] = ['TOP MOVING STOCK ITEMS (MOST USED)'];
        $data[] = ['Item Name', 'UOM', 'Quantity Used', 'COGS (UGX)', '% of Total COGS'];

        $ingredientUsage = [];
        foreach($this->report->items as $item) {
            $inventoryId = $item->inventory_item_id;
            if (!isset($ingredientUsage[$inventoryId])) {
                $ingredientUsage[$inventoryId] = [
                    'name' => $item->inventoryItem->name ?? 'N/A',
                    'uom' => $item->inventoryItem->unit_of_measurement ?? 'piece',
                    'used' => 0,
                    'cogs' => 0
                ];
            }
            $ingredientUsage[$inventoryId]['used'] += $item->used_quantity;
            $ingredientUsage[$inventoryId]['cogs'] += $item->cogs;
        }
        usort($ingredientUsage, function($a, $b) {
            return $b['used'] <=> $a['used'];
        });

        foreach(array_slice($ingredientUsage, 0, 15) as $item) {
            $percentage = $this->report->total_cogs > 0 ? ($item['cogs'] / $this->report->total_cogs) * 100 : 0;
            $data[] = [
                $item['name'],
                $item['uom'],
                number_format($item['used'], 2),
                number_format($item['cogs'], 0),
                number_format($percentage, 1) . '%'
            ];
        }
        $data[] = [];

        // ============================================
        // SECTION 5: GENERAL STOCK SUMMARY
        // ============================================
        $data[] = ['GENERAL STOCK SUMMARY'];
        $data[] = ['Item Name', 'UOM', 'Opening Stock', 'Added Stock', 'Used Stock', 'Closing Stock'];

        $stockSummary = [];
        foreach($this->report->items as $item) {
            $inventoryId = $item->inventory_item_id;
            if (!isset($stockSummary[$inventoryId])) {
                $stockSummary[$inventoryId] = [
                    'name' => $item->inventoryItem->name ?? 'N/A',
                    'uom' => $item->inventoryItem->unit_of_measurement ?? 'piece',
                    'opening' => $item->opening_stock,
                    'added' => $item->added_stock ?? 0,
                    'used' => 0,
                    'closing' => $item->closing_stock,
                ];
            }
            $stockSummary[$inventoryId]['used'] += $item->used_quantity;
        }

        foreach($stockSummary as $stock) {
            $data[] = [
                $stock['name'],
                $stock['uom'],
                number_format($stock['opening'], 2),
                number_format($stock['added'], 2),
                number_format($stock['used'], 2),
                number_format($stock['closing'], 2),
            ];
        }

        // ============================================
        // FOOTER (DYNAMIC)
        // ============================================
        $data[] = [];
        $data[] = [];
        $data[] = ['Report generated by: ' . ($this->report->createdBy->first_name ?? 'N/A') . ' ' . ($this->report->createdBy->last_name ?? '')];
        $data[] = ['Generated by: ' . $companyName . ' Management System'];
        $data[] = [$fullAddress];
        $data[] = ['Tel: ' . $companyPhone . ' | Email: ' . $companyEmail];
        $data[] = ['© ' . date('Y') . ' ' . $companyName . ' - All Rights Reserved'];

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $companyName = $this->getBusinessValue('company_name', 'PATIO BELLA');

        // Style for company name header
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FF2563EB');

        // Style for report title
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style for address and contact
        $sheet->mergeCells('A3:F3');
        $sheet->mergeCells('A4:F4');
        $sheet->getStyle('A3:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:A4')->getFont()->setSize(9);

        // Style for section headers
        $sectionRows = [9, 16, 23, 30, 43, 50, 57];
        foreach($sectionRows as $row) {
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle('A' . $row)->getFill()->getStartColor()->setARGB('FF4472C4');
            $sheet->getStyle('A' . $row)->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // Style for table headers
        $tableHeaderRows = [10, 17, 24, 31, 44, 51, 58];
        foreach($tableHeaderRows as $row) {
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->getStartColor()->setARGB('FFD9E1F2');
            $sheet->getStyle('A' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Style for report info labels
        $sheet->getStyle('A7:A11')->getFont()->setBold(true);

        // Style for totals row
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A' . ($lastRow - 6) . ':F' . ($lastRow - 6))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($lastRow - 6) . ':F' . ($lastRow - 6))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A' . ($lastRow - 6) . ':F' . ($lastRow - 6))->getFill()->getStartColor()->setARGB('FFE2E8F0');

        // Style for footer
        $footerStartRow = $lastRow - 5;
        for($i = $footerStartRow; $i <= $lastRow; $i++) {
            $sheet->mergeCells('A' . $i . ':F' . $i);
            $sheet->getStyle('A' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $i)->getFont()->setSize(9);
        }

        // Add borders to all data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:F' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto-size columns
        foreach(range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 12,
            'C' => 20,
            'D' => 20,
            'E' => 18,
            'F' => 20,
        ];
    }
}
