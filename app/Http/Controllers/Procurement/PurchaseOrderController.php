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

    // For AJAX requests, return ONLY the table rows HTML
    if ($request->ajax()) {
        return view('procurement.purchase_orders._table_rows', compact('purchaseOrders'))->render();
    }

    return view('procurement.purchase_orders.index', compact('purchaseOrders', 'vendors'));
}    // Show create form
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

        // Calculate subtotal
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_cost'];
        }

        // Calculate VAT
        $vatRate = $request->vat_rate ?? 0;
        $vatAmount = ($subtotal * $vatRate) / 100;
        $totalAmount = $subtotal + $vatAmount;

        $po = new PurchaseOrder();
        $po->po_number = $poNumber;
        $po->type = $request->type;
        $po->vendor_id = $request->vendor_id;
        $po->store_id = null;
        $po->ordered_by = Auth::id();
        $po->po_date = now();
        $po->expected_delivery_date = $request->expected_delivery_date ?? null;
        $po->subtotal = $subtotal;
        $po->vat_rate = $vatRate;
        $po->vat_amount = $vatAmount;
        $po->total_amount = $totalAmount;
        $po->status = 'sent';
        $po->payment_method = $request->payment_method;
        $po->created_by = Auth::id();
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

    $purchaseOrder = PurchaseOrder::with([
        'vendor',
        'items.inventoryItem',
        'creator',              // created_by  — Prepared By signature
        'orderedBy',            // ordered_by  — person who raised the PO
        'approvedBy',           // approved_by — set if PO itself was approved (may be null)
        'lpo.approvedBy',       // source LPO's director approver — THIS is the real "Approved By"
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


/**
 * Attach document to purchase order
 */
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
            'purchase_order_id' => $po->id,  // Keep for backward compatibility
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
/**
 * Preview document
 */
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

/**
 * Download document
 */
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
/**
 * Delete document
 */
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
