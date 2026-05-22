<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\StockVarianceReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockVarianceReasonController extends Controller
{
    /**
     * Display a listing of variance reasons.
     */
    public function index(Request $request)
    {
        $query = StockVarianceReason::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('requires_approval')) {
            $query->where('requires_approval', $request->requires_approval);
        }

        $reasons = $query->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('management.variance-reasons.index', compact('reasons'));
    }

    /**
     * Show form to create a new variance reason.
     */
    public function create()
    {
        return view('management.variance-reasons.create');
    }

    /**
     * Store a newly created variance reason.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:stock_variance_reasons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_approval' => 'boolean',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $reason = StockVarianceReason::create([
                'code' => strtoupper($validated['code']),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'requires_approval' => $validated['requires_approval'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            Log::info('Stock variance reason created', [
                'user_id' => Auth::id(),
                'reason_id' => $reason->id,
                'code' => $reason->code,
            ]);

            return redirect()->route('management.variance-reasons.index')
                ->with('success', "Variance reason '{$reason->name}' created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create variance reason', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create variance reason: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified variance reason.
     */
    public function show($id)
    {
        $reason = StockVarianceReason::findOrFail($id);

        return view('management.variance-reasons.show', compact('reason'));
    }

    /**
     * Show form to edit variance reason.
     */
    public function edit($id)
    {
        $reason = StockVarianceReason::findOrFail($id);

        return view('management.variance-reasons.edit', compact('reason'));
    }

    /**
     * Update the specified variance reason.
     */
    public function update(Request $request, $id)
    {
        $reason = StockVarianceReason::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:stock_variance_reasons,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_approval' => 'boolean',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $reason->update([
                'code' => strtoupper($validated['code']),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'requires_approval' => $validated['requires_approval'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            Log::info('Stock variance reason updated', [
                'user_id' => Auth::id(),
                'reason_id' => $reason->id,
                'code' => $reason->code,
            ]);

            return redirect()->route('management.variance-reasons.index')
                ->with('success', "Variance reason '{$reason->name}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update variance reason', [
                'user_id' => Auth::id(),
                'reason_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update variance reason: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete (soft delete) the specified variance reason.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $reason = StockVarianceReason::findOrFail($id);
            $reasonName = $reason->name;
            $reason->delete();

            DB::commit();

            Log::info('Stock variance reason deleted', [
                'user_id' => Auth::id(),
                'reason_id' => $id,
                'code' => $reason->code,
            ]);

            return redirect()->route('management.variance-reasons.index')
                ->with('success', "Variance reason '{$reasonName}' deleted successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete variance reason', [
                'user_id' => Auth::id(),
                'reason_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete variance reason: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status of variance reason.
     */
    public function toggleActive($id)
    {
        DB::beginTransaction();

        try {
            $reason = StockVarianceReason::findOrFail($id);
            $reason->is_active = !$reason->is_active;
            $reason->save();

            DB::commit();

            $status = $reason->is_active ? 'activated' : 'deactivated';

            return redirect()->back()
                ->with('success', "Variance reason '{$reason->name}' {$status} successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to toggle status: ' . $e->getMessage());
        }
    }

    /**
     * Get all active variance reasons for API/dropdown.
     */
    public function getActiveReasons()
    {
        $reasons = StockVarianceReason::active()
            ->orderBy('sort_order')
            ->get(['id', 'code', 'name', 'requires_approval']);

        return response()->json([
            'success' => true,
            'reasons' => $reasons,
        ]);
    }
}
