<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\InventoryItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class GoodsReceivedController extends Controller
{
    // Show list of GRNs
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

    // Show form to create GRN
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get sent purchase orders that have items to receive
        $purchaseOrders = PurchaseOrder::with(['vendor', 'items'])
            ->where('status', 'sent')
            ->whereHas('items', function($query) {
                $query->whereRaw('quantity_received < quantity_ordered');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('procurement.goods_received.create', compact('purchaseOrders'));
    }

    // Show PO items for receiving
    public function getPoItems($poId)
    {
        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])
            ->findOrFail($poId);

        // Calculate PO total amount
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

    // Store GRN

// Store GRN
public function store(Request $request)
{
    $user = Auth::user();
    if (!$user->department || $user->department->name !== 'PROCUREMENT') {
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

        // Calculate PO total amount
        $poTotalAmount = collect($po->items)->sum(function($item) {
            return $item->quantity_ordered * $item->unit_cost;
        });

        // Generate GRN number
        $grnNumber = 'GRN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Calculate total received value
        $totalReceivedValue = 0;
        foreach ($request->items as $itemData) {
            $quantityReceived = floatval($itemData['quantity_received']);
            $quantityRejected = floatval($itemData['quantity_rejected'] ?? 0);
            $quantityAccepted = $quantityReceived - $quantityRejected;
            $poItem = PurchaseOrderItem::find($itemData['po_item_id']);
            $totalReceivedValue += $poItem->unit_cost * $quantityAccepted;
        }

        // Create GRN
        $grn = GoodsReceivedNote::create([
            'grn_number' => $grnNumber,
            'purchase_order_id' => $po->id,
            'vendor_id' => $po->vendor_id,
            'received_date' => $request->received_date,
            'delivery_note_number' => $request->delivery_note_number,
            'po_total_amount' => $poTotalAmount,
            'grn_total_amount' => $totalReceivedValue,
            'notes' => $request->notes,
            'status' => 'completed',
            'created_by' => Auth::id(),
        ]);

        $totalReceivedValue = 0;
        $totalAcceptedQty = 0;

        foreach ($request->items as $itemData) {
            $poItem = PurchaseOrderItem::findOrFail($itemData['po_item_id']);
            $quantityReceived = floatval($itemData['quantity_received']);
            $quantityRejected = floatval($itemData['quantity_rejected'] ?? 0);
            $quantityAccepted = $quantityReceived - $quantityRejected;

            // Calculate PO item total amount (ordered amount)
            $poItemTotalAmount = $poItem->quantity_ordered * $poItem->unit_cost;

            // Calculate total cost for accepted items
            $itemTotalCost = $poItem->unit_cost * $quantityAccepted;

            // Create GRN item - WITHOUT unit_id
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

            // Update PO item received quantity
            $poItem->quantity_received = ($poItem->quantity_received ?? 0) + $quantityAccepted;
            $poItem->save();

            $totalReceivedValue += $itemTotalCost;
            $totalAcceptedQty += $quantityAccepted;
        }

        // Update GRN totals
        $grn->grn_total_amount = $totalReceivedValue;
        $grn->save();

        // Update PO status if all items fully received
        $po->refresh();
        $allItemsFullyReceived = $po->items->every(function($item) {
            return ($item->quantity_received ?? 0) >= $item->quantity_ordered;
        });

        if ($allItemsFullyReceived) {
            $po->status = 'fully_received';
            $po->save();
        } elseif ($po->status == 'sent') {
            $po->status = 'partially_received';
            $po->save();
        }

        DB::commit();

        return redirect()->route('procurement.goods-received.show', $grn->id)
            ->with('success', 'Goods Received Note created successfully. Total value: UGX ' . number_format($totalReceivedValue, 2));

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating GRN', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => Auth::id()
        ]);
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error creating GRN: ' . $e->getMessage());
    }
}

    // Show GRN details
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
            'items.unit',
            'createdBy'
        ])->findOrFail($id);

        return view('procurement.goods_received.show', compact('grn'));
    }




    // Send to store for inventory update
    public function sendToStore($id)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $grn = GoodsReceivedNote::with(['items.inventoryItem'])->findOrFail($id);

            if ($grn->status == 'completed') {
                // You can add notification/email logic here
            }

            return redirect()->route('procurement.goods-received.show', $grn->id)
                ->with('success', 'GRN #' . $grn->grn_number . ' has been sent to Store department for inventory update.');

        } catch (\Exception $e) {
            Log::error('Error sending GRN to store', [
                'error' => $e->getMessage(),
                'grn_id' => $id
            ]);
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

public function sendEmail(Request $request, $id)
{
    $user = Auth::user();
    if (!$user->department || $user->department->name !== 'PROCUREMENT') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $request->validate([
        'email'   => 'required|email',
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

        // Generate PDF
        $pdf = Pdf::loadView('procurement.goods_received.pdf', compact('grn'));
        $pdfContent = $pdf->output();
        $fileName = 'GRN_' . $grn->grn_number . '.pdf';

        // Send email with PDF attached
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
                 ->attachData($pdfContent, $fileName, [
                     'mime' => 'application/pdf',
                 ]);
        });

        Log::info('GRN email sent', [
            'grn_id'     => $grn->id,
            'grn_number' => $grn->grn_number,
            'sent_to'    => $request->email,
            'sent_by'    => Auth::id(),
        ]);

        return redirect()->route('procurement.goods-received.show', $id)
            ->with('success', 'GRN emailed successfully to ' . $request->email . ' with PDF attached.');

    } catch (\Exception $e) {
        Log::error('Failed to send GRN email', [
            'grn_id' => $id,
            'error'  => $e->getMessage()
        ]);
        return redirect()->back()
            ->with('error', 'Failed to send email: ' . $e->getMessage());
    }
}
    // Print GRN
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
            'items.unit',
            'createdBy'
        ])->findOrFail($id);

        return view('procurement.goods_received.print', compact('grn'));
    }

// Download GRN PDF
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

}
