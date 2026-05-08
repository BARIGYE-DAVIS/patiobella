<?php

namespace App\Http\Controllers\Procurement;

use App\Mail\PurchaseOrderMail;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Requisition;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    // Index/listing
    public function index()
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $purchaseOrders = PurchaseOrder::with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('procurement.purchase_orders.index', compact('purchaseOrders'));
    }

    // Show create form
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $approvedRequisitions = Requisition::with(['store'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        $vendors = Vendor::all();
        $selectedVendor = null;
        $requisition = null;

        if ($request->filled('requisition_id')) {
            $requisition = Requisition::with([
                'items.inventoryItem', 'store', 'requestedBy'
            ])->findOrFail($request->requisition_id);

            $selectedVendor = $vendors->first();
        }

        return view('procurement.purchase_orders.create', compact(
            'approvedRequisitions',
            'vendors',
            'requisition',
            'selectedVendor'
        ));
    }

    // Store PO and send email with PDF
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'requisition_id' => 'nullable|exists:requisitions,id',
            'vendor_id' => 'required|exists:vendors,id',
            'expected_delivery_date' => 'nullable|date',
            'delivery_address' => 'nullable|string|max:255',
            'delivery_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $po = new PurchaseOrder();
            $po->po_number = $poNumber;
            $po->vendor_id = $request->vendor_id;
            $po->store_id = null;
            $po->ordered_by = Auth::id();
            $po->po_date = now();
            $po->expected_delivery_date = $request->expected_delivery_date ?? null;
            $po->subtotal = 0;
            $po->tax_amount = 0;
            $po->total_amount = 0;

            // Set status as 'sent' immediately
            $po->status = 'sent';

            $po->created_by = Auth::id();
            $po->delivery_address = $request->delivery_address;
            $po->delivery_terms = $request->delivery_terms;
            $po->notes = $request->notes;
            $po->save();

            $subtotal = 0;
            $reqItemsByInvId = [];
            $requisition = null;

            if ($request->filled('requisition_id')) {
                $requisition = Requisition::with('items')->find($request->requisition_id);
                if ($requisition) {
                    foreach ($requisition->items as $ri) {
                        if ($ri->inventory_item_id) {
                            $reqItemsByInvId[$ri->inventory_item_id] = $ri;
                        }
                    }
                    $requisition->status = 'ordered';
                    $requisition->save();
                }
            }

            foreach ($request->items as $itemArr) {
                $notes = $itemArr['notes'] ?? null;
                if ((!$notes || trim($notes) == "") && !empty($itemArr['inventory_item_id']) && isset($reqItemsByInvId[$itemArr['inventory_item_id']])) {
                    $notes = $reqItemsByInvId[$itemArr['inventory_item_id']]->notes ?? null;
                }

                $line = new PurchaseOrderItem();
                $line->purchase_order_id = $po->id;
                $line->inventory_item_id = $itemArr['inventory_item_id'] ?? null;
                $line->unit_id = $itemArr['unit_id'] ?? null;
                $line->quantity_ordered = $itemArr['quantity'];
                $line->unit_cost = $itemArr['unit_cost'];
                $line->total_cost = $itemArr['quantity'] * $itemArr['unit_cost'];
                $line->quantity_received = 0;
                $line->notes = $notes;
                $line->created_by = Auth::id();
                $line->save();

                $subtotal += $line->total_cost;
            }

            $po->subtotal = $subtotal;
            $po->tax_amount = 0;
            $po->total_amount = $subtotal;
            $po->save();

            DB::commit();

            // Send email with PDF attachment
            $this->sendPurchaseOrderEmail($po);

            return redirect()->route('procurement.purchase-orders.show', $po->id)
                ->with('success', 'Purchase Order created successfully and sent to vendor.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating PO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating PO: ' . $e->getMessage());
        }
    }

    // Send PO email with PDF attachment
    private function sendPurchaseOrderEmail($purchaseOrder)
    {
        try {
            $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])->find($purchaseOrder->id);

            // Generate PDF
            $pdf = Pdf::loadView('procurement.purchase_orders.pdf', compact('po'));
            $pdfContent = $pdf->output();

            // Send email using Mailable
            Mail::to($po->vendor->email)->send(new PurchaseOrderMail($po, $pdfContent));

            Log::info('PO email sent successfully', [
                'po_id' => $po->id,
                'vendor_email' => $po->vendor->email
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send PO email', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $purchaseOrder = PurchaseOrder::with(['vendor', 'items.inventoryItem'])->findOrFail($id);

        return view('procurement.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $purchaseOrder = PurchaseOrder::with(['items.inventoryItem', 'vendor'])->findOrFail($id);

        // Only allow editing of draft POs
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('procurement.purchase-orders.show', $id)
                ->with('error', 'Only draft POs can be edited.');
        }

        $vendors = Vendor::all();

        return view('procurement.purchase_orders.edit', compact('purchaseOrder', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'expected_delivery_date' => 'nullable|date',
            'delivery_address' => 'nullable|string|max:255',
            'delivery_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id);

            // Only allow editing of draft POs
            if ($po->status !== 'draft') {
                throw new \Exception('Only draft POs can be edited.');
            }

            $po->vendor_id = $request->vendor_id;
            $po->expected_delivery_date = $request->expected_delivery_date ?? null;
            $po->delivery_address = $request->delivery_address;
            $po->delivery_terms = $request->delivery_terms;
            $po->notes = $request->notes;
            $po->updated_by = Auth::id();

            $subtotal = 0;
            $po->items()->delete();

            foreach ($request->items as $itemArr) {
                $line = new PurchaseOrderItem();
                $line->purchase_order_id = $po->id;
                $line->inventory_item_id = $itemArr['inventory_item_id'] ?? null;
                $line->unit_id = $itemArr['unit_id'] ?? null;
                $line->quantity_ordered = $itemArr['quantity'];
                $line->unit_cost = $itemArr['unit_cost'];
                $line->total_cost = $itemArr['quantity'] * $itemArr['unit_cost'];
                $line->quantity_received = 0;
                $line->notes = $itemArr['notes'] ?? null;
                $line->created_by = Auth::id();
                $line->save();

                $subtotal += $line->total_cost;
            }

            $po->subtotal = $subtotal;
            $po->tax_amount = 0;
            $po->total_amount = $subtotal;
            $po->save();

            DB::commit();

            return redirect()->route('procurement.purchase-orders.show', $po->id)
                ->with('success', 'Purchase Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating PO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'po_id' => $id,
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating PO: ' . $e->getMessage());
        }
    }

    // Destroy/delete
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $po = PurchaseOrder::findOrFail($id);

        try {
            // Only allow deletion of draft POs
            if ($po->status !== 'draft') {
                return redirect()->back()
                    ->with('error', 'Only draft POs can be deleted.');
            }

            $po->items()->delete();
            $po->delete();

            return redirect()->route('procurement.purchase-orders.index')
                ->with('success', 'Purchase Order deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting PO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'po_id' => $id,
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Error deleting PO: ' . $e->getMessage());
        }
    }

    // Send PO to vendor (change status from draft to sent)
    public function send($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])->findOrFail($id);

            if ($po->status !== 'draft') {
                return redirect()->back()->with('error', 'Only draft POs can be sent.');
            }

            $po->status = 'sent';
            $po->save();

            // Generate PDF and send email
            $pdf = Pdf::loadView('procurement.purchase_orders.pdf', compact('po'));
            $pdfContent = $pdf->output();

            Mail::to($po->vendor->email)->send(new PurchaseOrderMail($po, $pdfContent));

            return redirect()->route('procurement.purchase-orders.show', $po->id)
                ->with('success', 'PO sent to vendor successfully.');

        } catch (\Exception $e) {
            Log::error('Error sending PO', [
                'error' => $e->getMessage(),
                'po_id' => $id
            ]);
            return redirect()->back()->with('error', 'Error sending PO: ' . $e->getMessage());
        }
    }

    // Resend PO email
    public function resendEmail($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $po = PurchaseOrder::with(['vendor'])->findOrFail($id);

            // Generate PDF
            $pdf = Pdf::loadView('procurement.purchase_orders.pdf', compact('po'));
            $pdfContent = $pdf->output();

            // Send email
            Mail::to($po->vendor->email)->send(new PurchaseOrderMail($po, $pdfContent));

            return redirect()->back()->with('success', 'PO email resent successfully to ' . $po->vendor->email);

        } catch (\Exception $e) {
            Log::error('Error resending PO email', [
                'error' => $e->getMessage(),
                'po_id' => $id
            ]);
            return redirect()->back()->with('error', 'Error sending email: ' . $e->getMessage());
        }
    }

    // Download PDF
    public function downloadPdf($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])->findOrFail($id);

        $pdf = Pdf::loadView('procurement.purchase_orders.pdf', compact('po'));

        return $pdf->download('PO_' . $po->po_number . '.pdf');
    }
}
