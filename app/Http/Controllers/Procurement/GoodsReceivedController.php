<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\InventoryItem;
use App\Models\Vendor;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GoodsReceivedController extends Controller
{
    // ==================== LIST GRNs ====================
    public function index()
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $grns = GoodsReceivedNote::with(['vendor', 'purchaseOrder'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('procurement.goods_received.index', compact('grns'));
    }

    // ==================== CREATE PAGE (PO LISTING) ====================
public function create(Request $request)
{
    $user = Auth::user();
    if (!$user->department || $user->department->name !== 'PROCUREMENT') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $query = PurchaseOrder::with('vendor')
        ->where('status', 'sent')
        ->orderBy('created_at', 'desc');

    // Search by PO number or item name
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('po_number', 'like', "%{$search}%")
              ->orWhereHas('items.inventoryItem', function($qi) use ($search) {
                  $qi->where('name', 'like', "%{$search}%");
              });
        });
    }

    // Filter by vendor
    if ($request->filled('vendor_id')) {
        $query->where('vendor_id', $request->vendor_id);
    }

    // FIXED: Filter by payment method from VENDORS table using whereHas
    if ($request->filled('payment_method')) {
        $query->whereHas('vendor', function($q) use ($request) {
            $q->where('payment_method', $request->payment_method);
        });
    }

    // Filter by date range
    if ($request->filled('date_from')) {
        $query->whereDate('po_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('po_date', '<=', $request->date_to);
    }

    $purchaseOrders = $query->paginate(15);

    return view('procurement.goods_received.create', compact('purchaseOrders'));
}
    // ==================== AJAX: GET FILTERED POs ====================
public function getPurchaseOrders(Request $request)
{
    $query = PurchaseOrder::with('vendor')
        ->whereIn('status', ['sent', 'partially_received'])
        ->orderBy('created_at', 'desc');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('po_number', 'like', "%{$search}%")
              ->orWhereHas('items.inventoryItem', function($qi) use ($search) {
                  $qi->where('name', 'like', "%{$search}%");
              });
        });
    }

    if ($request->filled('vendor_id')) {
        $query->where('vendor_id', $request->vendor_id);
    }

    // FIXED: Filter by payment method from VENDOR table
    if ($request->filled('payment_method')) {
        $query->whereHas('vendor', function($q) use ($request) {
            $q->where('payment_method', $request->payment_method);
        });
    }

    if ($request->filled('date_from')) {
        $query->whereDate('po_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('po_date', '<=', $request->date_to);
    }

    $purchaseOrders = $query->paginate(10);

    $transformed = $purchaseOrders->map(function($po) {
        $hasPartial = $po->items->contains(function($item) {
            return ($item->quantity_received ?? 0) > 0 &&
                   ($item->quantity_received ?? 0) < $item->quantity_ordered;
        });

        return [
            'id' => $po->id,
            'po_number' => $po->po_number,
            'vendor_name' => $po->vendor->name ?? 'N/A',
            'po_date' => $po->po_date,
            'created_at' => $po->created_at,
            'total_amount' => $po->total_amount,
            'status' => $po->status,
            'payment_method' => $po->vendor->payment_method ?? null, // Get from vendor
            'item_count' => $po->items->count(),
            'has_partial' => $hasPartial,
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $transformed,
        'current_page' => $purchaseOrders->currentPage(),
        'last_page' => $purchaseOrders->lastPage(),
        'total' => $purchaseOrders->total(),
        'from' => $purchaseOrders->firstItem(),
        'to' => $purchaseOrders->lastItem(),
    ]);
}
    // ==================== SHOW PO ITEMS (for dropdown method) ====================
    public function getPoItems($poId)
    {
        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])
            ->findOrFail($poId);

        $poTotalAmount = collect($po->items)->sum(function($item) {
            return $item->quantity_ordered * $item->unit_cost;
        });

        $items = collect($po->items)->map(function($item) {
            $orderedAmount = $item->quantity_ordered * $item->unit_cost;
            $remainingToReceive = $item->quantity_ordered - ($item->quantity_received ?? 0);

            return [
                'id' => $item->id,
                'item_name' => $item->inventoryItem->name ?? 'Unknown Item',
                'quantity_ordered' => $item->quantity_ordered,
                'unit_cost' => $item->unit_cost,
                'ordered_amount' => $orderedAmount,
                'quantity_received_so_far' => $item->quantity_received ?? 0,
                'quantity_to_receive' => $remainingToReceive > 0 ? $remainingToReceive : 0,
            ];
        });

        return response()->json([
            'po_number' => $po->po_number,
            'vendor_name' => $po->vendor->name,
            'vendor_id' => $po->vendor_id,
            'po_total_amount' => $poTotalAmount,
            'items' => $items
        ]);
    }

    // ==================== AJAX: RETURN GRN FORM HTML ====================
    public function createFormAjax($poId)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])
            ->findOrFail($poId);

        return view('procurement.goods_received._form', compact('po'))->render();
    }

    // ==================== SEPARATE PAGE: CREATE GRN FOR PO ====================
    public function createForPo($poId)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])
            ->findOrFail($poId);

        return view('procurement.goods_received.create_form', compact('po'));
    }

    // ==================== STORE GRN ====================
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'delivery_note_number' => 'nullable|string|max:255',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.quantity_rejected' => 'nullable|numeric|min:0',
            'items.*.rejection_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $po = PurchaseOrder::with(['items.inventoryItem'])->findOrFail($request->purchase_order_id);
            $poTotalAmount = collect($po->items)->sum(function($item) {
                return $item->quantity_ordered * $item->unit_cost;
            });

            $grnNumber = 'GRN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $totalReceivedValue = 0;

            $grn = GoodsReceivedNote::create([
                'grn_number' => $grnNumber,
                'purchase_order_id' => $po->id,
                'vendor_id' => $po->vendor_id,
                'received_date' => $request->received_date,
                'delivery_note_number' => $request->delivery_note_number,
                'po_total_amount' => $poTotalAmount,
                'grn_total_amount' => 0,
                'notes' => $request->notes,
                'status' => 'completed',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $itemData) {
                $poItem = PurchaseOrderItem::findOrFail($itemData['po_item_id']);
                $quantityReceived = floatval($itemData['quantity_received']);
                $quantityRejected = floatval($itemData['quantity_rejected'] ?? 0);
                $quantityAccepted = max(0, $quantityReceived - $quantityRejected);

                $poItemTotalAmount = $poItem->quantity_ordered * $poItem->unit_cost;
                $itemTotalCost = $poItem->unit_cost * $quantityAccepted;

                GoodsReceivedNoteItem::create([
                    'goods_received_note_id' => $grn->id,
                    'purchase_order_item_id' => $poItem->id,
                    'inventory_item_id' => $poItem->inventory_item_id,
                    'quantity_ordered' => $poItem->quantity_ordered,
                    'quantity_received' => $quantityReceived,
                    'quantity_accepted' => $quantityAccepted,
                    'quantity_rejected' => $quantityRejected,
                    'rejection_reason' => $itemData['rejection_reason'] ?? null,
                    'unit_cost' => $poItem->unit_cost,
                    'po_item_total_amount' => $poItemTotalAmount,
                    'total_cost' => $itemTotalCost,
                    'notes' => $itemData['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);

                $poItem->quantity_received = ($poItem->quantity_received ?? 0) + $quantityAccepted;
                $poItem->save();

                $totalReceivedValue += $itemTotalCost;
            }

            $grn->grn_total_amount = $totalReceivedValue;
            $grn->save();

            // Update PO status
            $po->refresh();
            $allItemsFullyReceived = collect($po->items)->every(function($item) {
                return ($item->quantity_received ?? 0) >= $item->quantity_ordered;
            });

            if ($allItemsFullyReceived) {
                $po->status = 'fully_received';
            } elseif ($po->status == 'sent') {
                $po->status = 'partially_received';
            }
            $po->save();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'GRN created successfully!',
                    'grn_id' => $grn->id,
                    'grn_number' => $grn->grn_number,
                    'redirect_url' => route('procurement.goods-received.show', $grn->id)
                ]);
            }

            return redirect()->route('procurement.goods-received.show', $grn->id)
                ->with('success', 'Goods Received Note created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating GRN', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error creating GRN: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error creating GRN: ' . $e->getMessage());
        }
    }

    // ==================== SHOW GRN ====================
    public function show($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $grn = GoodsReceivedNote::with([
            'vendor',
            'purchaseOrder',
            'items.inventoryItem',
            'createdBy'
        ])->findOrFail($id);

        $documents = Document::where('grn_id', $id)->get();

        return view('procurement.goods_received.show', compact('grn', 'documents'));
    }

    // ==================== ATTACH PDF TO PO ====================
    public function attachDocument(Request $request, $id)
{
    try {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'description' => 'nullable|string|max:255'
        ]);

        $grn = GoodsReceivedNote::with('purchaseOrder')->findOrFail($id);

        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
        $directory = "documents/po_{$grn->purchase_order_id}";

        $path = $file->storeAs($directory, $fileName, 'public');

        $document = \App\Models\Document::create([
            'purchase_order_id' => $grn->purchase_order_id,
            'grn_id' => $grn->id,
            'document_type' => $request->description ?: 'GRN Attachment',
            'filename' => $fileName,
            'original_name' => $originalName,
            'path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => Auth::id(),  // Using Auth::id() instead of Auth::user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document attached successfully!',
            'document' => $document
        ]);

    } catch (\Exception $e) {
        Log::error('Error attaching document', ['error' => $e->getMessage(), 'grn_id' => $id]);
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    // ==================== DOWNLOAD GRN PDF ====================
    public function downloadPdf($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $grn = GoodsReceivedNote::with(['vendor', 'purchaseOrder', 'items.inventoryItem'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('procurement.goods_received.pdf', compact('grn'));
        return $pdf->download('GRN_' . $grn->grn_number . '.pdf');
    }

    // ==================== PRINT GRN ====================
    public function print($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $grn = GoodsReceivedNote::with([
            'vendor',
            'purchaseOrder',
            'items.inventoryItem',
            'createdBy'
        ])->findOrFail($id);

        return view('procurement.goods_received.print', compact('grn'));
    }

    // ==================== SEND EMAIL WITH PDF ====================
    public function sendEmail(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string',
        ]);

        try {
            $grn = GoodsReceivedNote::with([
                'vendor',
                'purchaseOrder',
                'items.inventoryItem',
                'createdBy'
            ])->findOrFail($id);

            $pdf = Pdf::loadView('procurement.goods_received.pdf', compact('grn'));
            $pdfContent = $pdf->output();
            $fileName = 'GRN_' . $grn->grn_number . '.pdf';

            Mail::send([], [], function ($mail) use ($request, $grn, $pdfContent, $fileName) {
                $mail->to($request->email)
                     ->subject($request->subject)
                     ->html(
                        '<p>' . nl2br(e($request->message ?? '')) . '</p>
                         <br>
                         <p style="color:#64748b;font-size:12px;">
                             This email was sent from the Procurement Module.<br>
                             GRN: <strong>' . $grn->grn_number . '</strong> |
                             PO: <strong>' . ($grn->purchaseOrder->po_number ?? 'N/A') . '</strong>
                         </p>'
                     )
                     ->attachData($pdfContent, $fileName, ['mime' => 'application/pdf']);
            });

            return redirect()->route('procurement.goods-received.show', $id)
                ->with('success', 'GRN emailed successfully to ' . $request->email);

        } catch (\Exception $e) {
            Log::error('Failed to send GRN email', ['grn_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    // ==================== SEND TO STORE ====================
    public function sendToStore($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $grn = GoodsReceivedNote::findOrFail($id);

            $grn->update([
                'status' => 'sent_to_store',
                'sent_to_store_at' => now(),
                'sent_to_store_by' => Auth::id(),
            ]);

            return redirect()->route('procurement.goods-received.show', $grn->id)
                ->with('success', 'GRN #' . $grn->grn_number . ' has been sent to Store department.');

        } catch (\Exception $e) {
            Log::error('Error sending GRN to store', ['error' => $e->getMessage(), 'grn_id' => $id]);
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ==================== VENDOR RATING ====================
    public function rateVendor(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        try {
            $grn = GoodsReceivedNote::findOrFail($id);

            // Check if already rated
            $existingRating = \App\Models\VendorRating::where('goods_received_note_id', $id)
                ->where('rated_by', Auth::id())
                ->first();

            if ($existingRating) {
                return redirect()->back()->with('error', 'You have already rated this GRN.');
            }

            // Create rating
            $rating = \App\Models\VendorRating::create([
                'vendor_id' => $grn->vendor_id,
                'goods_received_note_id' => $id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'rated_by' => Auth::id(),
                'rated_at' => now(),
            ]);

            // Update vendor average rating
            $vendor = Vendor::find($grn->vendor_id);
            $avgRating = \App\Models\VendorRating::where('vendor_id', $vendor->id)->avg('rating');
            $totalRatings = \App\Models\VendorRating::where('vendor_id', $vendor->id)->count();

            $vendor->update([
                'average_rating' => round($avgRating, 1),
                'total_ratings' => $totalRatings,
            ]);

            return redirect()->route('procurement.goods-received.show', $id)
                ->with('success', 'Vendor rated successfully! Rating: ' . $request->rating . '/5');

        } catch (\Exception $e) {
            Log::error('Error rating vendor', ['error' => $e->getMessage(), 'grn_id' => $id]);
            return redirect()->back()->with('error', 'Failed to rate vendor: ' . $e->getMessage());
        }
    }

    // ==================== GET VENDOR RATINGS ====================
    public function getVendorRatings($vendorId)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ratings = \App\Models\VendorRating::with('ratedBy')
            ->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'ratings' => $ratings,
            'average' => $ratings->avg('rating'),
            'total' => $ratings->count(),
        ]);
    }

public function deleteDocument($id)
{
    try {
        $document = \App\Models\Document::findOrFail($id);

        // Delete physical file
        $filePath = storage_path('app/public/' . $document->path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete database record
        $document->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Document deleted successfully');

    } catch (\Exception $e) {
        Log::error('Error deleting document', ['error' => $e->getMessage(), 'document_id' => $id]);

        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', 'Error deleting document');
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

}
