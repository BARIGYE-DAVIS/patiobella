@extends('layouts.management')

@section('title', 'Requisition Details')
@section('page-title', 'Requisition Details')

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center no-print">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('management.requisitions.index') }}" class="text-gray-600 hover:text-gray-800 text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    {{-- Printable Section --}}
    <div id="print-section" class="p-6">

        {{-- Logo and Header --}}
        <div class="flex justify-between items-start mb-6 pb-4 border-b">
            <div>
                @php
                    $logo = \App\Models\BusinessSetting::getLogo();
                    $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
                @endphp
                @if($logo)
                    <img src="{{ $logo }}" class="h-12 w-auto" alt="Logo">
                @else
                    <h2 class="text-xl font-bold text-gray-800">{{ $companyName }}</h2>
                @endif
            </div>
            <div class="text-right">
                <h1 class="text-xl font-bold text-green-600">REQUISITION</h1>
                <p class="text-sm text-gray-500">{{ $requisition->requisition_number }}</p>
            </div>
        </div>

        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusConfig = [
                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Pending GM Approval'],
                    'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Approved'],
                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Rejected'],
                    'ordered' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Ordered'],
                    'fulfilled' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'label' => 'Fulfilled'],
                    'lpo_created' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'LPO Created'],
                    'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Cancelled'],
                ];
                $config = $statusConfig[$requisition->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($requisition->status)];
            @endphp
            <span class="inline-flex px-3 py-1 text-sm rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                {{ $config['label'] }}
            </span>
        </div>

        {{-- Rejection Reason --}}
        @if($requisition->status == 'rejected' && $requisition->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $requisition->rejection_reason }}</p>
                    @if($requisition->approved_by)
                        <p class="text-xs text-red-600 mt-2">
                            Rejected by: {{ $requisition->approvedBy->first_name ?? '' }} {{ $requisition->approvedBy->last_name ?? '' }}
                            @if($requisition->approved_at) on {{ \Carbon\Carbon::parse($requisition->approved_at)->format('F d, Y g:i A') }} @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- GM Notes --}}
        @if($requisition->gm_notes)
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-yellow-800">GM Notes</h4>
                    <p class="text-sm text-yellow-700 mt-1">{{ $requisition->gm_notes }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Requisition Info Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Requisition Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-xs text-gray-500">Requisition No:</span>
                        <span class="text-xs font-mono text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-xs text-gray-500">Requisition Type:</span>
                        <span class="text-xs">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $requisition->requisition_type == 'emergency' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $requisition->requisition_type == 'emergency' ? 'EMERGENCY' : 'Normal' }}
                            </span>
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-xs text-gray-500">Date Needed:</span>
                        <span class="text-xs text-gray-800">{{ $requisition->date_needed ? \Carbon\Carbon::parse($requisition->date_needed)->format('F d, Y') : 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-xs text-gray-500">Requested By:</span>
                        <span class="text-xs text-gray-800">{{ $requisition->requestedBy ? $requisition->requestedBy->first_name . ' ' . $requisition->requestedBy->last_name : '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-xs text-gray-500">Requested At:</span>
                        <span class="text-xs text-gray-800">{{ $requisition->created_at->format('F d, Y g:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Response Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-xs text-gray-500">Responded By:</span>
                        <span class="text-xs text-gray-800">
                            @if(in_array($requisition->status, ['approved', 'rejected']) && $requisition->approved_by)
                                {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : '—' }}
                            @else
                                <span class="text-yellow-600">Not yet responded</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-xs text-gray-500">Responded At:</span>
                        <span class="text-xs text-gray-800">{{ $requisition->approved_at ? \Carbon\Carbon::parse($requisition->approved_at)->format('F d, Y g:i A') : '—' }}</span>
                    </div>
                    @if($requisition->status == 'ordered' || $requisition->status == 'lpo_created')
                    <div class="flex">
                        <span class="w-36 text-xs text-gray-500">LPO Status:</span>
                        <span class="text-xs text-purple-600 font-medium">LPO Created - Being Processed</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Original Notes --}}
        @if($requisition->notes)
        <div class="mb-6">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Original Notes (from Store)</h4>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <p class="text-sm text-gray-700">{{ $requisition->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table with Separate Columns --}}
        <div class="mt-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Requested Items</h4>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Batch No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Expiry Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pack Info</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Metrics</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Unit Cost</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Batch Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Requested</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Approved</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @php
                            $totalRequested = 0;
                            $totalApproved = 0;

                            function fmtQty($val) {
                                if ($val == floor($val)) {
                                    return number_format($val, 0);
                                }
                                return rtrim(rtrim(number_format($val, 2), '0'), '.');
                            }
                        @endphp
                        @foreach($requisition->items as $item)
                        @php
                            $totalRequested += $item->quantity_requested;
                            $totalApproved += $item->quantity_approved;

                            $batch = $item->batch;

                            // Stock of the specific batch
                            $batchStock = $batch ? $batch->remaining_quantity : 0;

                            // Total stock across ALL batches for this item
                            $totalStock = 0;
                            if ($item->inventory_item_id) {
                                $totalStock = \App\Models\Batch::where('inventory_item_id', $item->inventory_item_id)
                                    ->where('batch_status', 'active')
                                    ->where('remaining_quantity', '>', 0)
                                    ->sum('remaining_quantity');
                            }

                            $batchStockClass = $batchStock <= 0 ? 'text-red-600' : ($batchStock < 10 ? 'text-orange-500' : 'text-green-600');
                            $totalStockClass = $totalStock <= 0 ? 'text-red-600' : ($totalStock < 10 ? 'text-orange-500' : 'text-green-600');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-800">{{ $item->item_name ?: ($item->inventoryItem ? $item->inventoryItem->name : 'N/A') }}</div>
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <div class="text-xs text-gray-400">Code: {{ $item->inventoryItem->item_code }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">
                                    {{ $item->category_name ?: ($item->inventoryItem && $item->inventoryItem->category ? $item->inventoryItem->category->name : '—') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-mono text-sm font-semibold text-gray-800">{{ $batch->batch_number ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($batch && $batch->expiry_date)
                                    @php
                                        $daysLeft = now()->diffInDays($batch->expiry_date, false);
                                        $expiryClass = $daysLeft <= 0 ? 'text-red-600 font-semibold' : ($daysLeft <= 30 ? 'text-orange-500' : 'text-gray-500');
                                    @endphp
                                    <span class="{{ $expiryClass }}">
                                        {{ $batch->expiry_date->format('d M Y') }}
                                        @if($daysLeft <= 0)
                                            (EXPIRED)
                                        @elseif($daysLeft <= 30)
                                            ({{ $daysLeft }} days left)
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($batch && $batch->pack_type && $batch->pack_type != 'Direct')
                                    <span class="text-blue-500 text-xs">📦 {{ $batch->pack_type }} ({{ $batch->pack_size }}/pack)</span>
                                @else
                                    <span class="text-gray-400 text-xs">Direct</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">
                                    {{ $item->metrics ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <span class="font-mono">UGX {{ number_format($item->unit_cost ?? 0, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <div class="font-semibold {{ $batchStockClass }}">
                                    {{ fmtQty($batchStock) }}
                                </div>
                                <div class="text-xs {{ $batchStockClass }}">
                                    @if($batchStock <= 0)
                                        Out of Stock
                                    @elseif($batchStock < 10)
                                        Low Stock
                                    @else
                                        In Stock
                                    @endif
                                </div>
                                @if(($item->batch_stock_at_request ?? 0) > 0 && $item->batch_stock_at_request != $batchStock)
                                    <div class="text-xs text-gray-400 mt-1">
                                        At request: {{ fmtQty($item->batch_stock_at_request) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <div class="font-semibold {{ $totalStockClass }}">
                                    {{ fmtQty($totalStock) }}
                                </div>
                                <div class="text-xs {{ $totalStockClass }}">
                                    @if($totalStock <= 0)
                                        Out of Stock
                                    @elseif($totalStock < 10)
                                        Low Stock
                                    @else
                                        In Stock
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-gray-800">
                                {{ fmtQty($item->quantity_requested) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                @if(in_array($requisition->status, ['approved', 'ordered', 'lpo_created']))
                                    <span class="font-semibold text-green-600">{{ fmtQty($item->quantity_approved) }}</span>
                                    @if($item->quantity_approved < $item->quantity_requested)
                                        <span class="text-xs text-orange-500 block">(Partial)</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">{{ fmtQty($item->quantity_approved) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">
                                {{ $item->notes ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t">
                        <tr>
                            <td colspan="9" class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">TOTALS</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ fmtQty($totalRequested) }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-right">
                                @if(in_array($requisition->status, ['approved', 'ordered', 'lpo_created']))
                                    <span class="text-green-600">{{ fmtQty($totalApproved) }}</span>
                                @else
                                    <span class="text-gray-600">{{ fmtQty($totalApproved) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 text-center">
                <p class="text-xs text-blue-600 font-medium">Total Items</p>
                <p class="text-2xl font-bold text-blue-800">{{ $requisition->items->count() }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200 text-center">
                <p class="text-xs text-yellow-600 font-medium">Total Requested</p>
                <p class="text-2xl font-bold text-yellow-800">{{ fmtQty($totalRequested) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200 text-center">
                <p class="text-xs text-green-600 font-medium">Total Approved</p>
                <p class="text-2xl font-bold text-green-800">{{ fmtQty($totalApproved) }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200 text-center">
                <p class="text-xs text-purple-600 font-medium">Approval Rate</p>
                <p class="text-2xl font-bold text-purple-800">
                    {{ $totalRequested > 0 ? number_format(($totalApproved / $totalRequested) * 100, 1) : 0 }}%
                </p>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="grid grid-cols-2 gap-8 mt-8 pt-6 border-t">
            <div class="text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Requested By:</p>
                @php $requester = $requisition->requestedBy; @endphp
                @if($requester && $requester->signature_url)
                    <img src="{{ $requester->signature_url }}" class="h-12 mx-auto mb-2" alt="Signature">
                @else
                    <div class="h-12 mb-2"></div>
                @endif
                <div class="border-t w-32 mx-auto pt-2"></div>
                <p class="text-sm font-medium text-gray-700 mt-2">{{ $requester->first_name ?? '' }} {{ $requester->last_name ?? '' }}</p>
                <p class="text-xs text-gray-400">{{ $requisition->created_at->format('d M Y') }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Approved By:</p>
                @if(in_array($requisition->status, ['approved', 'ordered', 'lpo_created']) && $requisition->approvedBy)
                    <img src="{{ $requisition->approvedBy->signature_url }}" class="h-12 mx-auto mb-2" alt="Signature">
                @else
                    <div class="h-12 mb-2"></div>
                @endif
                <div class="border-t w-32 mx-auto pt-2"></div>
                <p class="text-sm font-medium text-gray-700 mt-2">{{ $requisition->approvedBy->first_name ?? '' }} {{ $requisition->approvedBy->last_name ?? '' }}</p>
                @if($requisition->approved_at)
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($requisition->approved_at)->format('d M Y') }}</p>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t text-center">
            <p class="text-xs text-gray-400">This is a system generated document with digital signatures</p>
            <p class="text-xs text-gray-400">{{ $companyName }} - All Rights Reserved</p>
        </div>
    </div>

    {{-- Action Buttons --}}
    @if($requisition->status == 'pending')
    <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3 no-print">
        <button onclick="showRejectModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">Reject</button>
        <a href="{{ route('management.requisitions.approve-form', $requisition->id) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">Approve</a>
    </div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 no-print">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="bg-red-600 px-6 py-4 rounded-t-xl">
            <h3 class="text-lg font-semibold text-white">Reject Requisition</h3>
            <p class="text-sm text-red-100 mt-1">#{{ $requisition->requisition_number }}</p>
        </div>
        <form action="{{ route('management.requisitions.reject', $requisition->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Reason for Rejection <span class="text-red-500">*</span></label>
                <textarea name="rejection_reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" placeholder="Please provide a detailed reason..." required></textarea>
                <p class="text-xs text-gray-400 mt-2">This reason will be visible to the store manager.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end gap-3">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').style.display = 'flex';
}
function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').style.display = 'none';
}
// Close modal when clicking outside
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) hideRejectModal();
});
</script>
@endsection
