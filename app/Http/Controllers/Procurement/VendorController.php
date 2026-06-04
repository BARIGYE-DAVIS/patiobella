<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    /**
     * Generate auto vendor code
     * Format: VEND-001, VEND-002, VEND-003, etc.
     */
    private function generateVendorCode()
    {
        $lastVendor = Vendor::withTrashed()->orderBy('id', 'desc')->first();

        if ($lastVendor && $lastVendor->vendor_code) {
            $lastNumber = intval(substr($lastVendor->vendor_code, 5));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'VEND-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = Vendor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('vendor_code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vendors = $query->orderBy('name')->paginate(20);

        return view('procurement.vendors.index', compact('vendors'));
    }

    /**
     * Show form to create a new vendor.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $nextVendorCode = $this->generateVendorCode();

        return view('procurement.vendors.create', compact('categories', 'nextVendorCode'));
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'alternative_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'payment_method' => 'nullable|in:cash,bank,mobile',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,blacklisted',
            'notes' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        DB::beginTransaction();

        try {
            $validated['vendor_code'] = $this->generateVendorCode();
            $validated['created_by'] = Auth::id();

            $vendor = Vendor::create($validated);

            // Sync categories supplied by this vendor
            if ($request->has('categories') && !empty($request->categories)) {
                $vendor->categories()->sync($request->categories);
            }

            DB::commit();

            Log::info('Vendor created', [
                'user_id' => Auth::id(),
                'vendor_id' => $vendor->id,
                'vendor_code' => $vendor->vendor_code,
                'vendor_name' => $vendor->name
            ]);

            return redirect()->route('procurement.vendors.index')
                ->with('success', "Vendor '{$vendor->name}' created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create vendor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified vendor.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $vendor = Vendor::with(['creator', 'updater', 'categories'])->findOrFail($id);

        return view('procurement.vendors.show', compact('vendor'));
    }

    /**
     * Show form to edit a vendor.
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $vendor = Vendor::with('categories')->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $selectedCategories = $vendor->categories->pluck('id')->toArray();

        return view('procurement.vendors.edit', compact('vendor', 'categories', 'selectedCategories'));
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'alternative_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'payment_method' => 'nullable|in:cash,bank,mobile',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,blacklisted',
            'notes' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        DB::beginTransaction();

        try {
            $validated['updated_by'] = Auth::id();

            $vendor->update($validated);

            // Sync categories supplied by this vendor
            if ($request->has('categories')) {
                $vendor->categories()->sync($request->categories);
            } else {
                $vendor->categories()->sync([]);
            }

            DB::commit();

            Log::info('Vendor updated', [
                'user_id' => Auth::id(),
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name
            ]);

            return redirect()->route('procurement.vendors.show', $vendor->id)
                ->with('success', "Vendor '{$vendor->name}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor update failed', [
                'user_id' => Auth::id(),
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update vendor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete the specified vendor.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $vendor = Vendor::findOrFail($id);
        $vendorName = $vendor->name;

        $vendor->delete();

        Log::warning('Vendor deleted', [
            'user_id' => Auth::id(),
            'vendor_id' => $id,
            'vendor_name' => $vendorName
        ]);

        return redirect()->route('procurement.vendors.index')
            ->with('success', "Vendor '{$vendorName}' deleted successfully.");
    }
}
