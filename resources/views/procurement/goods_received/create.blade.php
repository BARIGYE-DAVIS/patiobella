@extends('layouts.procurement')
@section('title', 'Create Goods Received Note')
@section('page-title', 'Create Goods Received Note')

@section('content')
<style>
    .po-row {
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .po-row:hover {
        background-color: #fff7ed;
    }
    .compact-table th, .compact-table td {
        padding: 6px 8px;
        font-size: 12px;
    }
    .filter-card {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 16px;
    }
</style>

@if(session('error'))
    <div class="mb-3 bg-red-50 border-l-4 border-red-500 text-red-700 p-2 rounded text-xs">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="mb-3 bg-green-50 border-l-4 border-green-500 text-green-700 p-2 rounded text-xs">
        {{ session('success') }}
    </div>
@endif

<div class="space-y-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-700 to-orange-600 px-4 py-2">
            <h2 class="text-sm font-semibold text-white">
                <i class="fa fa-search mr-2 text-xs"></i>Find & Select Purchase Order
            </h2>
            <p class="text-orange-100 text-[11px] mt-0.5">Search, filter, and click on any PO row to receive goods</p>
        </div>

        <div class="p-3">
            {{-- Search and Filter Form --}}
            <form method="GET" action="{{ route('procurement.goods-received.create') }}" id="filterForm">
                <div class="filter-card">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        {{-- Search by PO number or Item name --}}
                        <div>
                            <label class="block text-[11px] font-medium text-gray-700 mb-1">
                                <i class="fa fa-search text-gray-400 mr-1"></i> Search
                            </label>
                            <input type="text"
                                   name="search"
                                   id="searchInput"
                                   value="{{ request('search') }}"
                                   class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="PO number or item name...">
                            <p class="text-[10px] text-gray-400 mt-0.5">Search by PO # or product name</p>
                        </div>

                        {{-- Vendor Filter --}}
                        <div>
                            <label class="block text-[11px] font-medium text-gray-700 mb-1">
                                <i class="fa fa-building text-gray-400 mr-1"></i> Vendor
                            </label>
                            <select name="vendor_id" id="vendorFilter" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-orange-500 focus:border-orange-500">
                                <option value="">All Vendors</option>
                                @php
                                    use App\Models\Vendor;
                                    $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
                                @endphp
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Payment Method Filter --}}
                        <div>
                            <label class="block text-[11px] font-medium text-gray-700 mb-1">
                                <i class="fa fa-credit-card text-gray-400 mr-1"></i> Payment Method
                            </label>
                            <select name="payment_method" id="paymentMethodFilter" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-orange-500 focus:border-orange-500">
                                <option value="">All Methods</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile" {{ request('payment_method') == 'mobile' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                        </div>

                        {{-- Date Range Filter --}}
                        <div>
                            <label class="block text-[11px] font-medium text-gray-700 mb-1">
                                <i class="fa fa-calendar text-gray-400 mr-1"></i> PO Date Range
                            </label>
                            <div class="flex gap-2">
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                       class="w-1/2 px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="From">
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                       class="w-1/2 px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="To">
                            </div>
                        </div>
                    </div>

                    {{-- Filter Action Buttons --}}
                    <div class="flex justify-end gap-2 mt-3 pt-2 border-t border-gray-200">
                        <a href="{{ route('procurement.goods-received.create') }}"
                           class="px-3 py-1.5 bg-gray-500 text-white rounded text-[11px] hover:bg-gray-600 transition flex items-center gap-1">
                            <i class="fa fa-undo text-[10px]"></i> Reset
                        </a>
                        <button type="submit"
                                class="px-3 py-1.5 bg-orange-600 text-white rounded text-[11px] hover:bg-orange-700 transition flex items-center gap-1">
                            <i class="fa fa-filter text-[10px]"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>

            {{-- Results Summary --}}
            <div class="mb-2 flex justify-between items-center">
                <div class="text-[11px] text-gray-500">
                    <i class="fa fa-list mr-1"></i> Found <strong>{{ $purchaseOrders->total() }}</strong> purchase order(s)
                </div>
                <div class="text-[11px] text-gray-500">
                    Showing {{ $purchaseOrders->firstItem() ?? 0 }} - {{ $purchaseOrders->lastItem() ?? 0 }} of {{ $purchaseOrders->total() }}
                </div>
            </div>

            {{-- PO Listing Table --}}
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded compact-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border-b text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Select</th>
                            <th class="border-b text-[10px] font-medium text-gray-500 uppercase py-2 px-2">PO Number</th>
                            <th class="border-b text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Vendor</th>
                            <th class="border-b text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Date</th>
                            <th class="border-b text-right text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Amount</th>
                            <th class="border-b text-center text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Status</th>
                            <th class="border-b text-left text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Payment</th>
                            <th class="border-b text-left text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            <tr class="po-row border-b hover:bg-orange-50 transition"
                                onclick="window.location.href='{{ route('procurement.goods-received.create-for-po', $po->id) }}'"
                                style="cursor: pointer;">
                                <td class="py-2 px-2 border-b text-center">
                                    <div class="w-3 h-3 rounded-full border-2 border-gray-400 mx-auto"></div>
                                </td>
                                <td class="py-2 px-2 border-b font-medium text-orange-600 text-xs">{{ $po->po_number }}</td>
                                <td class="py-2 px-2 border-b text-xs">{{ $po->vendor->name }}</td>
                                <td class="py-2 px-2 border-b text-xs">{{ $po->po_date ?? $po->created_at->format('Y-m-d') }}</td>
                                <td class="py-2 px-2 border-b text-right font-semibold text-xs">UGX {{ number_format($po->total_amount, 2) }}</td>
                                <td class="py-2 px-2 border-b text-center">
                                    @php
                                        $statusClass = $po->status == 'sent' ? 'bg-yellow-100 text-yellow-700' : ($po->status == 'partially_received' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700');
                                        $statusText = $po->status == 'sent' ? 'Sent' : ($po->status == 'partially_received' ? 'Partial' : 'Full');
                                    @endphp
                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="py-2 px-2 border-b capitalize text-xs">{{ $po->vendor->payment_method ?? '-' }}</td>
                                <td class="py-2 px-2 border-b text-xs">
                                    {{ $po->items->count() }} item(s)
                                    @php
                                        $hasPartial = $po->items->contains(function($item) {
                                            return ($item->quantity_received ?? 0) > 0 && ($item->quantity_received ?? 0) < $item->quantity_ordered;
                                        });
                                    @endphp
                                    @if($hasPartial)
                                        <span class="ml-1 text-[9px] text-orange-500">(partial)</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-500 text-xs">
                                    <i class="fa fa-inbox mr-1"></i> No purchase orders found
                                    @if(request('search') || request('vendor_id') || request('payment_method') || request('date_from') || request('date_to'))
                                        <br>
                                        <a href="{{ route('procurement.goods-received.create') }}" class="text-orange-500 hover:underline text-[11px] mt-1 inline-block">Clear all filters</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $purchaseOrders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<script>
// Auto-submit form when filters change (optional)
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit on vendor change
    const vendorFilter = document.getElementById('vendorFilter');
    if (vendorFilter) {
        vendorFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }

    // Auto-submit on payment method change
    const paymentFilter = document.getElementById('paymentMethodFilter');
    if (paymentFilter) {
        paymentFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }

    // Debounced search (auto-submit after typing stops)
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }

    // Auto-submit on date change
    const dateFrom = document.querySelector('input[name="date_from"]');
    const dateTo = document.querySelector('input[name="date_to"]');
    if (dateFrom) {
        dateFrom.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    if (dateTo) {
        dateTo.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});
</script>
@endsection
