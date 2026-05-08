<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Lpo;
use App\Models\LpoItem;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Mail\PurchaseOrderMail;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PurchaseOrderItem;

class LocalPurchaseOrderController extends Controller
{
    // Show form to create LPO from requisition
    public function createFromRequisition($requisitionId)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::with(['items.inventoryItem', 'store'])
            ->where('status', 'approved')
            ->findOrFail($requisitionId);

        $vendors = Vendor::where('status', 'active')->get();

        return view('procurement.lpo.create', compact('requisition', 'vendors'));
    }

    // Store LPO for Director approval
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'requisition_id' => 'required|exists:requisitions,id',
            'vendor_id' => 'required|exists:vendors,id',
            'lpo_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'delivery_address' => 'nullable|string|max:255',
            'delivery_instructions' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.requisition_item_id' => 'required|exists:requisition_items,id',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $requisition = Requisition::findOrFail($request->requisition_id);

            // Generate LPO Number
            $lpoNumber = 'LPO-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }

            // Create LPO (Internal - pending Director approval)
            $lpo = Lpo::create([
                'lpo_number' => $lpoNumber,
                'requisition_id' => $request->requisition_id,
                 'vendor_id' => $request->vendor_id,
                'created_by' => Auth::id(),
                'lpo_date' => $request->lpo_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'delivery_address' => $request->delivery_address,
                'delivery_instructions' => $request->delivery_instructions,
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'total_amount' => $subtotal,
                'status' => 'pending_director',
                'notes' => $request->notes,
            ]);

            Log::info('LPO Created - Pending Director Approval', [
                'lpo_id' => $lpo->id,
                'lpo_number' => $lpoNumber,
                'requisition_id' => $requisition->id,
                'status' => 'pending_director'
            ]);

            // Create LPO Items
            foreach ($request->items as $index => $item) {
                $totalCost = $item['quantity'] * $item['unit_cost'];

                // Get requisition item to get metrics
                $reqItem = RequisitionItem::find($item['requisition_item_id']);

                LpoItem::create([
                    'lpo_id' => $lpo->id,
                    'requisition_item_id' => $item['requisition_item_id'],
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity_approved' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $totalCost,
                    'metrics' => $reqItem->metrics ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Update requisition status
            $requisition->status = 'lpo_created';
            $requisition->save();

            DB::commit();

            return redirect()->route('procurement.lpo.show', $lpo->id)
                ->with('success', 'LPO #' . $lpoNumber . ' created successfully and sent to Director for approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating LPO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating LPO: ' . $e->getMessage());
        }
    }

    // Show LPO details
    public function show($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $lpo = Lpo::with(['vendor', 'requisition', 'items.inventoryItem', 'createdBy'])
            ->findOrFail($id);

        return view('procurement.lpo.show', compact('lpo'));
    }

    // List all LPOs
    public function index()
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $lpos = Lpo::with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('procurement.lpo.index', compact('lpos'));
    }


    // Show list of Director Approved LPOs ready for External PO
public function approvedLpos()
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'PROCUREMENT') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $approvedLpos = Lpo::with(['vendor', 'requisition'])
        ->where('status', 'director_approved')
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    return view('procurement.approved_lpos.index', compact('approvedLpos'));
}

// Show form to convert LPO to External PO
public function convertToEpo($id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'PROCUREMENT') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $lpo = Lpo::with(['vendor', 'items.inventoryItem', 'requisition'])
        ->where('status', 'director_approved')
        ->findOrFail($id);

    return view('procurement.approved_lpos.convert', compact('lpo'));
}

// Store External Purchase Order and send email


public function storeExternalPo(Request $request, $id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'PROCUREMENT') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $request->validate([
        'po_date' => 'required|date',
        'delivery_date' => 'nullable|date',
        'delivery_address' => 'nullable|string',
        'delivery_terms' => 'nullable|string',
        'notes' => 'nullable|string',
        'items' => 'required|array',
        'items.*.inventory_item_id' => 'required',
        'items.*.quantity' => 'required|numeric|min:0',
        'items.*.unit_cost' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        $lpo = Lpo::with(['vendor'])->findOrFail($id);

        if ($lpo->status !== 'director_approved') {
            throw new \Exception('Only director approved LPOs can be converted to External PO.');
        }

        // Calculate totals from edited items
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_cost'];
        }

        // Generate External PO Number
        $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Create External Purchase Order - FIX STATUS
        $externalPo = PurchaseOrder::create([
            'po_number' => $poNumber,
            'vendor_id' => $lpo->vendor_id,
            'lpo_id' => $lpo->id,
            'ordered_by' => Auth::id(),
            'po_date' => $request->po_date,
            'expected_delivery_date' => $request->delivery_date,
            'delivery_address' => $request->delivery_address,
            'delivery_terms' => $request->delivery_terms,
            'notes' => $request->notes,
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'total_amount' => $subtotal,
            'status' => 'sent',  // Changed from 'epo_created' to 'sent'
            'created_by' => Auth::id(),
        ]);

        // Create External PO Items
        foreach ($request->items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $externalPo->id,
                'inventory_item_id' => $item['inventory_item_id'],
                'quantity_ordered' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'total_cost' => $item['quantity'] * $item['unit_cost'],
                'quantity_received' => 0,
                'notes' => $item['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);
        }

        // Update LPO status
        $lpo->status = 'converted_to_epo';
        $lpo->external_po_id = $externalPo->id;
        $lpo->save();

        DB::commit();

        // Send email to vendor with PDF
        $this->sendExternalPoEmail($externalPo);

        return redirect()->route('procurement.purchase-orders.show', $externalPo->id)
            ->with('success', 'External Purchase Order created and sent to vendor successfully. You can download the PDF below.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating External PO', [
            'error' => $e->getMessage(),
            'lpo_id' => $id
        ]);
        return redirect()->back()->with('error', 'Error creating External PO: ' . $e->getMessage());
    }
}
// Send External PO email with PDF
private function sendExternalPoEmail($purchaseOrder)
{
    try {
        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])->find($purchaseOrder->id);

        $pdf = Pdf::loadView('procurement.purchase_orders.pdf', compact('po'));
        $pdfContent = $pdf->output();

        Mail::to($po->vendor->email)->send(new PurchaseOrderMail($po, $pdfContent));

        Log::info('External PO email sent successfully', [
            'po_id' => $po->id,
            'vendor_email' => $po->vendor->email
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to send External PO email', [
            'po_id' => $purchaseOrder->id,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

}
