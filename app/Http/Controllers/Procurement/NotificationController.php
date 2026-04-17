<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all pending requisitions for notifications.
     */
    public function check(Request $request)
    {
        $user = Auth::user();
        
        // Only procurement department can access
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get all pending requisitions
        $pendingRequisitions = Requisition::with(['requestedBy', 'items'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $pendingCount = $pendingRequisitions->count();
        
        $requisitionData = [];
        foreach ($pendingRequisitions as $req) {
            $requisitionData[] = [
                'id' => $req->id,
                'requisition_number' => $req->requisition_number,
                'requested_by' => $req->requestedBy ? $req->requestedBy->first_name . ' ' . $req->requestedBy->last_name : 'Unknown',
                'items_count' => $req->items->count(),
                'date_needed' => $req->date_needed ? $req->date_needed->format('M d, Y') : 'Not specified',
                'created_at' => $req->created_at->diffForHumans(),
            ];
        }
        
        return response()->json([
            'success' => true,
            'pending_count' => $pendingCount,
            'pending_requisitions' => $requisitionData,
        ]);
    }
}