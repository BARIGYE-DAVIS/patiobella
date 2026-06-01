<?php

namespace App\Http\Controllers\Procurement;

use App\Mail\PurchaseOrderMail;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Requisition;
use App\Models\Vendor;
use App\Models\Lpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    // Index/listing
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = PurchaseOrder::with(['vendor'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('search')) {
            $query->where('po_number', 'like', "%{$request->search}%");
        }

        $purchaseOrders = $query->paginate(20);
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();

        if ($request->ajax()) {
            return view('procurement.purchase_orders._table_rows', compact('purchaseOrders'))->render();
        }

        return view('procurement.purchase_orders.index', compact('purchaseOrders', 'vendors'));
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

    // Store PO from approved LPO (the correct way)
    public function storeFromLpo(Request $request, $lpoId)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $lpo = Lpo::with(['items.inventoryItem', 'vendor'])->findOrFail($lpoId);

        if ($lpo->status !== 'director_approved') {
            return redirect()->back()->with('error', 'Only director approved LPOs can be converted to PO.');
        }

        DB::beginTransaction();

        try {
            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $po = new PurchaseOrder();
            $po->po_number = $poNumber;
            $po->lpo_id = $lpo->id;  // ← CRITICAL: Save LPO ID for signature
            $po->type = $lpo->type;
            $po->vendor_id = $lpo->vendor_id;
            $po->store_id = null;
            $po->ordered_by = Auth::id();
            $po->created_by = Auth::id();
            $po->po_date = now();
            $po->expected_delivery_date = $request->expected_delivery_date ?? $lpo->expected_delivery_date;
            $po->payment_method = $lpo->payment_method;
            $po->delivery_address = $request->delivery_address ?? $lpo->delivery_address;
            $po->delivery_terms = $request->delivery_terms ?? $lpo->delivery_instructions;
            $po->notes = $request->notes ?? $lpo->notes;
            $po->subtotal = $lpo->subtotal;
            $po->vat_rate = $lpo->vat_rate;
            $po->vat_amount = $lpo->vat_amount;
            $po->total_amount = $lpo->total_amount;
            $po->status = 'sent';
            $po->save();

            // Copy items from LPO to PO
            foreach ($lpo->items as $lpoItem) {
                $poItem = new PurchaseOrderItem();
                $poItem->purchase_order_id = $po->id;
                $poItem->inventory_item_id = $lpoItem->inventory_item_id;
                $poItem->quantity_ordered = $lpoItem->quantity_approved;
                $poItem->unit_cost = $lpoItem->unit_cost;
                $poItem->total_cost = $lpoItem->quantity_approved * $lpoItem->unit_cost;
                $poItem->quantity_received = 0;
                $poItem->notes = $lpoItem->notes;
                $poItem->created_by = Auth::id();
                $poItem->save();
            }

            // Update LPO status
            $lpo->status = 'converted_to_epo';
            $lpo->save();

            DB::commit();

            // Send email with PDF attachment
            $this->sendPurchaseOrderEmail($po);

            return redirect()->route('procurement.purchase-orders.show', $po->id)
                ->with('success', 'Purchase Order created successfully from LPO and sent to vendor.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating PO from LPO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lpo_id' => $lpoId,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Error creating PO: ' . $e->getMessage());
        }
    }

    // Store PO (original method - keep for backward compatibility)
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'requisition_id' => 'nullable|exists:requisitions,id',
            'vendor_id' => 'required|exists:vendors,id',
            'type' => 'required|in:normal,emergency',
            'payment_method' => 'required|in:cash,credit,bank_transfer,mobile_money,cheque',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
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

            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }

            $vatRate = $request->vat_rate ?? 0;
            $vatAmount = ($subtotal * $vatRate) / 100;
            $totalAmount = $subtotal + $vatAmount;

            $po = new PurchaseOrder();
            $po->po_number = $poNumber;
            $po->type = $request->type;
            $po->vendor_id = $request->vendor_id;
            $po->store_id = null;
            $po->ordered_by = Auth::id();
            $po->created_by = Auth::id();
            $po->po_date = now();
            $po->expected_delivery_date = $request->expected_delivery_date ?? null;
            $po->subtotal = $subtotal;
            $po->vat_rate = $vatRate;
            $po->vat_amount = $vatAmount;
            $po->total_amount = $totalAmount;
            $po->status = 'sent';
            $po->payment_method = $request->payment_method;
            $po->delivery_address = $request->delivery_address;
            $po->delivery_terms = $request->delivery_terms;
            $po->notes = $request->notes;
            $po->save();

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
            }

            DB::commit();

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
            $po = PurchaseOrder::with(['vendor', 'items.inventoryItem', 'lpo.approvedBy'])->find($purchaseOrder->id);

            $pdf = Pdf::loadView('procurement.purchase_orders.pdf', compact('po'));
            $pdfContent = $pdf->output();

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

        $purchaseOrder = PurchaseOrder::with([
            'vendor',
            'items.inventoryItem',
            'creator',
            'orderedBy',
            'approvedBy',
            'lpo',              // Load LPO
            'lpo.approvedBy'    // Load the director who approved the LPO
        ])->findOrFail($id);

        return view('procurement.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $purchaseOrder = PurchaseOrder::with(['items.inventoryItem', 'vendor'])->findOrFail($id);

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

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $po = PurchaseOrder::findOrFail($id);

        try {
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

    public function resendEmail($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $po = PurchaseOrder::with(['vendor'])->findOrFail($id);

            $pdf = Pdf::loadView('procurement.purchase_orders.pdf', compact('po'));
            $pdfContent = $pdf->output();

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

    public function downloadPdf($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem', 'lpo.approvedBy'])->findOrFail($id);

        $pdf = Pdf::loadView('procurement.purchase_orders.pdf', compact('po'));

        return $pdf->download('PO_' . $po->po_number . '.pdf');
    }

    public function attachDocument(Request $request)
    {
        try {
            $request->validate([
                'po_id' => 'required|exists:purchase_orders,id',
                'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'description' => 'nullable|string|max:255'
            ]);

            $po = PurchaseOrder::findOrFail($request->po_id);
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
            $directory = "documents/po_{$po->id}";
            $path = $file->storeAs($directory, $fileName, 'public');

            $document = \App\Models\Document::create([
                'po_id' => $po->id,
                'purchase_order_id' => $po->id,
                'document_type' => $request->description ?: 'Purchase Order Attachment',
                'filename' => $fileName,
                'original_name' => $originalName,
                'path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document attached successfully!',
                'document' => $document
            ]);

        } catch (\Exception $e) {
            Log::error('Error attaching document to PO', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function previewDocument($id)
    {
        $document = \App\Models\Document::findOrFail($id);
        $filePath = storage_path('app/public/' . $document->path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        $fileContent = file_get_contents($filePath);
        return response($fileContent, 200)
            ->header('Content-Type', $document->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $document->original_name . '"');
    }

    public function downloadDocument($id)
    {
        $document = \App\Models\Document::findOrFail($id);
        $filePath = storage_path('app/public/' . $document->path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $document->original_name, [
            'Content-Type' => $document->mime_type
        ]);
    }

    public function deleteDocument($id)
    {
        try {
            $document = \App\Models\Document::findOrFail($id);
            $filePath = storage_path('app/public/' . $document->path);

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $document->delete();

            return redirect()->back()->with('success', 'Document deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting document', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }
}
