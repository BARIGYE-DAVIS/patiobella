<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ManagerGrnController extends Controller
{
    /**
     * Display a listing of goods received notes.
     */
   /**
 * Display a listing of goods received notes.
 */
public function index(Request $request)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    try {
        $query = GoodsReceivedNote::with(['vendor', 'purchaseOrder', 'createdBy']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('grn_number', 'like', "%{$search}%")
                  ->orWhereHas('vendor', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhere('delivery_note_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('received_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('received_date', '<=', $request->date_to);
        }

        $grns = $query->orderBy('received_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistics for cards
        $totalGRNs = GoodsReceivedNote::count();
        $totalValue = GoodsReceivedNote::sum('grn_total_amount');
        $pendingGRNs = GoodsReceivedNote::where('status', 'draft')->count();
        $completedGRNs = GoodsReceivedNote::where('status', 'completed')->count();
        $inventoryUpdatedGRNs = GoodsReceivedNote::where('status', 'inventory_updated')->count();

        // ============================================================
        // DAILY TREND DATA (Last 30 days)
        // ============================================================
        $dailyLabels = [];
        $dailyValues = [];
        $dailyCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = now()->subDays($i)->format('M d');
            $dailyTotal = GoodsReceivedNote::whereDate('received_date', $date)->sum('grn_total_amount');
            $dailyCount = GoodsReceivedNote::whereDate('received_date', $date)->count();
            $dailyValues[] = (float)$dailyTotal;
            $dailyCounts[] = $dailyCount;
        }

        $avgDailyValue = count($dailyValues) > 0 ? array_sum($dailyValues) / count($dailyValues) : 0;
        $maxDailyValue = max($dailyValues);
        $maxDailyIndex = array_search($maxDailyValue, $dailyValues);
        $peakDay = $maxDailyIndex !== false ? $dailyLabels[$maxDailyIndex] : '—';

        // ============================================================
        // WEEKLY TREND DATA (Last 12 weeks)
        // ============================================================
        $weeklyLabels = [];
        $weeklyValues = [];
        $weeklyCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            $weekNumber = now()->subWeeks($i)->weekOfYear;
            $yearNumber = now()->subWeeks($i)->year;
            $weeklyLabels[] = "W{$weekNumber} '{$yearNumber}";
            $weeklyTotal = GoodsReceivedNote::whereBetween('received_date', [$startDate, $endDate])->sum('grn_total_amount');
            $weeklyCount = GoodsReceivedNote::whereBetween('received_date', [$startDate, $endDate])->count();
            $weeklyValues[] = (float)$weeklyTotal;
            $weeklyCounts[] = $weeklyCount;
        }

        $avgWeeklyValue = count($weeklyValues) > 0 ? array_sum($weeklyValues) / count($weeklyValues) : 0;
        $maxWeeklyValue = max($weeklyValues);
        $maxWeeklyIndex = array_search($maxWeeklyValue, $weeklyValues);
        $peakWeek = $maxWeeklyIndex !== false ? $weeklyLabels[$maxWeeklyIndex] : '—';
        $wowChange = count($weeklyValues) >= 2 ? (($weeklyValues[0] - $weeklyValues[1]) / ($weeklyValues[1] ?: 1)) * 100 : 0;

        // ============================================================
        // MONTHLY TREND DATA (Last 6 months)
        // ============================================================
        $monthlyData = GoodsReceivedNote::select(
            DB::raw("DATE_FORMAT(received_date, '%Y-%m') as month"),
            DB::raw('SUM(grn_total_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(6)
        ->get()
        ->reverse()
        ->values();

        $monthlyLabels = [];
        $monthlyValues = [];
        $monthlyCounts = [];
        foreach ($monthlyData as $data) {
            $monthlyLabels[] = $data->month;
            $monthlyValues[] = (float)$data->total;
            $monthlyCounts[] = $data->count;
        }

        $avgMonthlyValue = count($monthlyValues) > 0 ? array_sum($monthlyValues) / count($monthlyValues) : 0;
        $maxMonthlyValue = max($monthlyValues);
        $maxMonthlyIndex = array_search($maxMonthlyValue, $monthlyValues);
        $peakMonth = $maxMonthlyIndex !== false ? $monthlyLabels[$maxMonthlyIndex] : '—';
        $momChange = count($monthlyValues) >= 2 ? (($monthlyValues[count($monthlyValues)-1] - $monthlyValues[count($monthlyValues)-2]) / ($monthlyValues[count($monthlyValues)-2] ?: 1)) * 100 : 0;

        // Monthly data for chart
        $monthlyChartData = GoodsReceivedNote::select(
            DB::raw("DATE_FORMAT(received_date, '%Y-%m') as month"),
            DB::raw('SUM(grn_total_amount) as total')
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(6)
        ->get();

        // Chart data: Status distribution
        $statusData = [
            'labels' => ['Draft', 'Completed', 'Inventory Updated'],
            'data' => [
                GoodsReceivedNote::where('status', 'draft')->count(),
                GoodsReceivedNote::where('status', 'completed')->count(),
                GoodsReceivedNote::where('status', 'inventory_updated')->count(),
            ]
        ];

        // Filters data
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $statuses = ['draft', 'completed', 'inventory_updated'];

        return view('management.grns.index', compact(
            'grns',
            'totalGRNs',
            'totalValue',
            'pendingGRNs',
            'completedGRNs',
            'inventoryUpdatedGRNs',
            'monthlyChartData',
            'statusData',
            'vendors',
            'statuses',
            // Daily trend data
            'dailyLabels',
            'dailyValues',
            'dailyCounts',
            'avgDailyValue',
            'maxDailyValue',
            'peakDay',
            // Weekly trend data
            'weeklyLabels',
            'weeklyValues',
            'weeklyCounts',
            'avgWeeklyValue',
            'maxWeeklyValue',
            'peakWeek',
            'wowChange',
            // Monthly trend data
            'monthlyLabels',
            'monthlyValues',
            'monthlyCounts',
            'avgMonthlyValue',
            'maxMonthlyValue',
            'peakMonth',
            'momChange'
        ));

    } catch (\Exception $e) {
        Log::error('Failed to load GRNs', [
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);
        return redirect()->route('management.dashboard')->with('error', 'Failed to load goods received notes.');
    }
}
    /**
     * Display the specified GRN.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $grn = GoodsReceivedNote::with([
                'vendor',
                'purchaseOrder',
                'purchaseOrder.vendor',
                'createdBy',
                'updatedBy',
                'items',
                'items.inventoryItem'
            ])->findOrFail($id);

            return view('management.grns.show', compact('grn'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('management.grns.index')->with('error', 'Goods received note not found.');
        } catch (\Exception $e) {
            Log::error('Failed to load GRN', [
                'user_id' => Auth::id(),
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('management.grns.index')->with('error', 'Failed to load goods received note details.');
        }
    }

    /**
     * Export to Excel (CSV)
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = GoodsReceivedNote::with(['vendor', 'purchaseOrder', 'createdBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('received_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('received_date', '<=', $request->date_to);
        }

        $grns = $query->orderBy('received_date', 'desc')->get();

        $exportData = [];
        foreach ($grns as $grn) {
            $exportData[] = [
                'GRN Number' => $grn->grn_number,
                'Received Date' => $grn->received_date->format('Y-m-d'),
                'Vendor' => $grn->vendor->name ?? 'N/A',
                'PO Number' => $grn->purchaseOrder->po_number ?? 'N/A',
                'Delivery Note #' => $grn->delivery_note_number ?? 'N/A',
                'Status' => ucfirst(str_replace('_', ' ', $grn->status)),
                'Subtotal' => $grn->subtotal,
                'Tax Amount' => $grn->tax_amount,
                'Total Amount' => $grn->grn_total_amount,
                'Created By' => $grn->createdBy->name ?? 'N/A',
                'Notes' => $grn->notes ?? 'N/A',
            ];
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="goods_received_notes_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($exportData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            if (!empty($exportData)) {
                fputcsv($file, array_keys($exportData[0]));
                foreach ($exportData as $row) {
                    fputcsv($file, array_values($row));
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = GoodsReceivedNote::with(['vendor', 'purchaseOrder', 'createdBy', 'items.inventoryItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('received_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('received_date', '<=', $request->date_to);
        }

        $grns = $query->orderBy('received_date', 'desc')->get();

        $totalValue = $grns->sum('grn_total_amount');

        $pdf = Pdf::loadView('management.grns.export_pdf', [
            'grns' => $grns,
            'export_date' => now()->format('F d, Y H:i:s'),
            'total_grns' => $grns->count(),
            'total_value' => $totalValue,
        ]);

        return $pdf->download('goods_received_notes_' . date('Y-m-d') . '.pdf');
    }
}
