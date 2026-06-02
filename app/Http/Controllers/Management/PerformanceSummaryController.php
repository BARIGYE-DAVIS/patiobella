<?php
// app/Http/Controllers/Management/PerformanceSummaryController.php

namespace App\Http\Controllers\Management;

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
        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();

        return view('management.performance.summaries', compact('summaries', 'departments'));
    }

    /**
     * Show form to create a new performance summary.
     */
    public function create()
    {
        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();

        return view('management.performance.summary-create', compact('departments'));
    }

    /**
     * Generate performance summary from daily sales.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'period_type' => 'required|in:daily,weekly,monthly',
        ]);

        DB::beginTransaction();

        try {
            // Check if summary already exists
            $existing = PerformanceSummary::where('department_id', $validated['department_id'])
                ->where('period_start', $validated['period_start'])
                ->where('period_end', $validated['period_end'])
                ->first();

            if ($existing) {
                return redirect()->route('management.performance.summaries.show', $existing->id)
                    ->with('info', 'Summary already exists for this period.');
            }

            // Generate new summary
            $summary = PerformanceSummary::generateFromDailySales(
                $validated['department_id'],
                $validated['period_start'],
                $validated['period_end'],
                $validated['period_type']
            );

            $summary->save();
            $summary->generateTopBottomItems();

            DB::commit();

            Log::info('Performance summary generated', [
                'user_id' => Auth::id(),
                'summary_id' => $summary->id,
                'department_id' => $validated['department_id'],
                'period' => $validated['period_start'] . ' to ' . $validated['period_end'],
            ]);

            return redirect()->route('management.performance.summaries.show', $summary->id)
                ->with('success', 'Performance summary generated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to generate performance summary', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to generate summary: ' . $e->getMessage())
                ->withInput();
        }
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

        return view('management.performance.show', compact('summary'));
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

        return view('management.performance.print', compact('summary'));
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

        $pdf = Pdf::loadView('management.performance.pdf', compact('summary'));
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

            return redirect()->route('management.performance.summaries.show', $summary->id)
                ->with('success', 'Performance summary finalized successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to finalize: ' . $e->getMessage());
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

            Log::info('Performance summary regenerated', [
                'user_id' => Auth::id(),
                'summary_id' => $summary->id,
            ]);

            return redirect()->route('management.performance.summaries.show', $summary->id)
                ->with('success', 'Performance summary regenerated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to regenerate: ' . $e->getMessage());
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

            Log::info('Performance summary deleted', [
                'user_id' => Auth::id(),
                'summary_id' => $id,
            ]);

            return redirect()->route('management.performance.summaries.index')
                ->with('success', 'Performance summary deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete: ' . $e->getMessage());
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
