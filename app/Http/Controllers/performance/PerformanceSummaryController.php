<?php
// app/Http/Controllers/Performance/PerformanceSummaryController.php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\PerformanceSummary;
use App\Models\PerformanceItem;
use App\Models\Department;
use App\Models\DailySale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PerformanceSummaryController extends Controller
{
    /**
     * Display list of performance summaries.
     */
    public function index(Request $request)
    {
        $query = PerformanceSummary::with(['department']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        $summaries = $query->orderBy('created_at', 'desc')->paginate(20);
        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE'])->get();

        return view('performance.summaries.index', compact('summaries', 'departments'));
    }

    /**
     * Show form to create a new performance summary.
     */
    public function create()
    {
        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE'])->get();

        return view('performance.summaries.create', compact('departments'));
    }

    /**
     * Display the specified performance summary.
     */
    public function show($id)
    {
        $summary = PerformanceSummary::with([
            'department',
            'topItems.menuItem',
            'bottomItems.menuItem',
            'allItems.menuItem'
        ])->findOrFail($id);

        return view('performance.summaries.show', compact('summary'));
    }

    /**
     * Print the performance summary.
     */
    public function print($id)
    {
        $summary = PerformanceSummary::with([
            'department',
            'topItems.menuItem',
            'bottomItems.menuItem',
            'allItems.menuItem'
        ])->findOrFail($id);

        return view('performance.summaries.print', compact('summary'));
    }

    /**
     * Download performance summary as PDF.
     */
    public function downloadPdf($id)
    {
        $summary = PerformanceSummary::with([
            'department',
            'topItems.menuItem',
            'bottomItems.menuItem',
            'allItems.menuItem'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('performance.summaries.pdf', compact('summary'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Performance_Summary_' . $summary->id . '.pdf');
    }

    /**
     * Finalize/approve the performance summary.
     */
    public function finalize($id)
    {
        DB::beginTransaction();

        try {
            $summary = PerformanceSummary::findOrFail($id);

            if ($summary->status === 'finalized') {
                return redirect()->back()->with('error', 'Summary is already finalized.');
            }

            $summary->status = 'finalized';
            $summary->save();

            DB::commit();

            Log::info('Performance summary finalized', [
                'user_id' => Auth::id(),
                'summary_id' => $summary->id,
                'department' => $summary->department_id,
            ]);

            return redirect()->route('performance.summaries.show', $summary->id)
                ->with('success', 'Performance summary finalized successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to finalize: ' . $e->getMessage());
        }
    }

    /**
     * Delete performance summary.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $summary = PerformanceSummary::findOrFail($id);

            // Delete associated performance items first
            PerformanceItem::where('performance_summary_id', $id)->delete();
            $summary->delete();

            DB::commit();

            return redirect()->route('performance.summaries.index')
                ->with('success', 'Performance summary deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate performance summary from daily sales.
     */
    public function regenerate($id)
    {
        DB::beginTransaction();

        try {
            $summary = PerformanceSummary::findOrFail($id);

            // Delete existing performance items
            PerformanceItem::where('performance_summary_id', $id)->delete();

            // Regenerate from daily sales
            $newSummary = PerformanceSummary::generateFromDailySales(
                $summary->department_id,
                $summary->period_start,
                $summary->period_end,
                $summary->period_type
            );

            $summary->total_quantity_sold = $newSummary->total_quantity_sold;
            $summary->total_sales_amount = $newSummary->total_sales_amount;
            $summary->total_cogs = $newSummary->total_cogs;
            $summary->total_profit = $newSummary->total_profit;
            $summary->cogs_percentage = $newSummary->cogs_percentage;
            $summary->profit_margin = $newSummary->profit_margin;
            $summary->save();

            $summary->generateTopBottomItems();

            DB::commit();

            return redirect()->route('performance.summaries.show', $summary->id)
                ->with('success', 'Performance summary regenerated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to regenerate: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get summary data for dashboard.
     */
    public function getDashboardData(Request $request)
    {
        $departmentId = $request->get('department_id');
        $period = $request->get('period', 'current_month');

        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        if ($period === 'current_week') {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
        } elseif ($period === 'last_month') {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
        }

        $summary = PerformanceSummary::generateFromDailySales($departmentId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => [
                'total_sales' => $summary->total_sales_amount,
                'total_cogs' => $summary->total_cogs,
                'total_profit' => $summary->total_profit,
                'cogs_percentage' => $summary->cogs_percentage,
                'profit_margin' => $summary->profit_margin,
                'period_start' => $startDate->format('Y-m-d'),
                'period_end' => $endDate->format('Y-m-d'),
            ]
        ]);
    }
}
