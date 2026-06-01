<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class RequisitionController extends Controller
{
    /**
     * Display a listing of kitchen requisitions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = DepartmentRequisition::with(['items', 'approvedBy'])
            ->where('department_id', $user->department_id)
            ->where('requested_by', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('requisition_type')) {
            $query->where('requisition_type', $request->requisition_type);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('kitchen.requisitions.index', compact('requisitions'));
    }

    /**
     * Show form to create a new requisition.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $items = InventoryItem::where('is_active', true)->orderBy('name')->get();
        $requisitionTypes = DepartmentRequisition::getRequisitionTypes();

        // Prepare a plain PHP array for the frontend (safe for @json)
        $itemsForJs = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->item_code ?? 'N/A',
                'unit_of_measurement' => $item->unit_of_measurement ?? 'piece',
            ];
        })->values()->all();

        // Prepare a lightweight current user object for JS (avoid serializing relations)
        $authUser = Auth::user();
        $currentUserForJs = $authUser ? [
            'id' => $authUser->id,
            'first_name' => $authUser->first_name ?? null,
            'last_name' => $authUser->last_name ?? null,
            'signature_path' => $authUser->signature_path ?? null,
            'email' => $authUser->email ?? null,
        ] : null;

        return view('kitchen.requisitions.create', compact('items', 'requisitionTypes', 'itemsForJs', 'currentUserForJs'));
    }

    /**
     * Store a newly created requisition.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'requisition_type'               => 'required|in:daily,weekly,monthly',
            'date_needed'                    => 'nullable|date',
            'department_notes'               => 'nullable|string',
            'items'                          => 'required|array|min:1',
            'items.*.inventory_item_id'      => 'required|exists:inventory_items,id',
            'items.*.quantity'               => 'required|numeric|min:0.01',
            'items.*.pack_type'              => 'nullable|string',
            'items.*.pack_size'              => 'nullable|numeric|min:1',
            'items.*.metrics'                => 'nullable|string',
            'items.*.notes'                  => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $requisitionNumber = 'KIT-REQ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $requisition = DepartmentRequisition::create([
                'requisition_number' => $requisitionNumber,
                'requisition_type'   => $validated['requisition_type'],
                'department_id'      => $user->department_id,
                'requested_by'       => $user->id,
                'date_needed'        => $validated['date_needed'],
                'department_notes'   => $validated['department_notes'],
                'status'             => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                DepartmentRequisitionItem::create([
                    'department_requisition_id' => $requisition->id,
                    'inventory_item_id'         => $item['inventory_item_id'],
                    'quantity_requested'        => $item['quantity'],
                    'requested_pack_type'       => $item['pack_type'] ?? null,
                    'requested_pack_size'       => $item['pack_size'] ?? null,
                    'metrics'                   => $item['metrics'] ?? null,
                    'notes'                     => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Kitchen requisition created', [
                'user_id'              => Auth::id(),
                'requisition_id'       => $requisition->id,
                'requisition_number'   => $requisitionNumber,
                'requisition_type'     => $validated['requisition_type'],
            ]);

            return redirect()->route('kitchen.requisitions.show', $requisition->id)
                ->with('success', 'Requisition #' . $requisitionNumber . ' created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating kitchen requisition', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create requisition: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API endpoint to get item details for auto-fill
     */
    public function getItemDetails($id)
    {
        try {
            $item = InventoryItem::findOrFail($id);

            $unitOfMeasurement = $item->unit_of_measurement ?? 'piece';

            $packType = null;
            $packSize = null;
            $metrics = $unitOfMeasurement;
            $baseUnit = $unitOfMeasurement;

            if ($item->default_unit_of_measure_id) {
                $packTypeValue = strtolower($item->default_unit_of_measure_id);
                $packTypes = ['carton', 'box', 'crate', 'dozen', 'pack', 'bag', 'sack', 'bottle'];

                if (in_array($packTypeValue, $packTypes)) {
                    $packType = $packTypeValue;
                    $packSize = $item->pack_size ?? null;
                }
            }

            $isPackable = !is_null($packType) && $packSize > 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'item_code' => $item->item_code,
                    'base_unit' => $baseUnit,
                    'metrics' => $metrics,
                    'pack_type' => $packType,
                    'pack_size' => $packSize,
                    'is_packable' => $isPackable,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }
    }

    /**
     * Display the specified requisition.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with([
            'items.inventoryItem',
            'requestedBy',
            'approvedBy'
        ])
        ->where('department_id', $user->department_id)
        ->where('requested_by', $user->id)
        ->findOrFail($id);

        return view('kitchen.requisitions.show', compact('requisition'));
    }

    /**
     * Cancel a requisition (only if pending).
     */
    public function cancel($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be cancelled.');
            }

            $requisition->status = 'cancelled';
            $requisition->save();

            DB::commit();

            Log::info('Kitchen requisition cancelled', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number
            ]);

            return redirect()->route('kitchen.requisitions.index')
                ->with('success', 'Requisition cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling kitchen requisition', [
                'user_id' => Auth::id(),
                'requisition_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error cancelling requisition: ' . $e->getMessage());
        }
    }

    /**
     * Preview requisition before printing.
     */
    public function preview($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with([
            'items.inventoryItem',
            'requestedBy',
            'approvedBy'
        ])
        ->where('department_id', $user->department_id)
        ->where('requested_by', $user->id)
        ->findOrFail($id);

        return view('kitchen.requisitions.preview', compact('requisition'));
    }

    /**
     * Print requisition (HTML view for printing).
     */
    public function print($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with([
            'items.inventoryItem',
            'requestedBy',
            'approvedBy'
        ])
        ->where('department_id', $user->department_id)
        ->where('requested_by', $user->id)
        ->findOrFail($id);

        return view('kitchen.requisitions.print', compact('requisition'));
    }

    /**
     * Download requisition as PDF.
     */
    public function downloadPdf($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with([
            'items.inventoryItem',
            'requestedBy',
            'approvedBy'
        ])
        ->where('department_id', $user->department_id)
        ->where('requested_by', $user->id)
        ->findOrFail($id);

        $pdf = Pdf::loadView('kitchen.requisitions.pdf', compact('requisition'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Requisition_' . $requisition->requisition_number . '.pdf');
    }
}
