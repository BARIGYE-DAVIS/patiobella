<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorRating;
use App\Models\GoodsReceivedNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorRatingController extends Controller
{
    /**
     * Show form to rate a vendor for a specific GRN.
     */
    public function create($grnId)
    {
        $grn = GoodsReceivedNote::with(['vendor', 'purchaseOrder'])
            ->findOrFail($grnId);

        // Check if already rated
        if ($grn->isRated()) {
            return redirect()->route('management.grn.show', $grnId)
                ->with('error', 'This delivery has already been rated.');
        }

        // Only allow rating for completed GRNs
        if ($grn->status !== 'inventory_updated' && $grn->status !== 'completed') {
            return redirect()->route('management.grn.show', $grnId)
                ->with('error', 'Only completed deliveries can be rated.');
        }

        return view('management.vendor-ratings.create', compact('grn'));
    }

    /**
     * Store the vendor rating.
     */
    public function store(Request $request, $grnId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $grn = GoodsReceivedNote::with('vendor')->findOrFail($grnId);

            // Check if already rated
            if ($grn->isRated()) {
                return redirect()->back()->with('error', 'This delivery has already been rated.');
            }

            // Create the rating
            $rating = VendorRating::create([
                'vendor_id' => $grn->vendor_id,
                'goods_received_note_id' => $grnId,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'rated_by' => Auth::id(),
                'rated_at' => now(),
            ]);

            // Update vendor's average rating
            $grn->vendor->updateAverageRating();

            DB::commit();

            Log::info('Vendor rating created', [
                'user_id' => Auth::id(),
                'vendor_id' => $grn->vendor_id,
                'grn_id' => $grnId,
                'rating' => $validated['rating'],
            ]);

            return redirect()->route('management.grn.show', $grnId)
                ->with('success', 'Thank you! Your rating has been submitted.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create vendor rating', [
                'user_id' => Auth::id(),
                'grn_id' => $grnId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to submit rating: ' . $e->getMessage());
        }
    }

    /**
     * Display all ratings for a vendor.
     */
    public function vendorRatings($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $ratings = VendorRating::with(['goodsReceivedNote', 'ratedBy'])
            ->where('vendor_id', $vendorId)
            ->orderBy('rated_at', 'desc')
            ->paginate(20);

        return view('management.vendor-ratings.vendor-ratings', compact('vendor', 'ratings'));
    }

    /**
     * Display all ratings across all vendors (management view).
     */
    public function index(Request $request)
    {
        $query = VendorRating::with(['vendor', 'goodsReceivedNote', 'ratedBy'])
            ->orderBy('rated_at', 'desc');

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $ratings = $query->paginate(20);

        $vendors = Vendor::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('management.vendor-ratings.index', compact('ratings', 'vendors'));
    }

    /**
     * Delete a rating (admin only).
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $rating = VendorRating::with('vendor')->findOrFail($id);
            $vendor = $rating->vendor;
            $rating->delete();

            // Update vendor's average rating
            $vendor->updateAverageRating();

            DB::commit();

            Log::info('Vendor rating deleted', [
                'user_id' => Auth::id(),
                'rating_id' => $id,
                'vendor_id' => $vendor->id,
            ]);

            return redirect()->back()
                ->with('success', 'Rating deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete rating: ' . $e->getMessage());
        }
    }
}
