{{-- resources/views/management/department-requisitions/show.blade.php --}}

@extends('layouts.management')

@section('title', 'Requisition Details')
@section('page-title', 'Requisition Details')

@section('content')
<style>
    /* Core visuals (screen) */
    .status-badge { display:inline-block; padding:.25rem .75rem; border-radius:9999px; font-size:.75rem; font-weight:600; }
    .status-pending { background:#fef3c7; color:#92400e; }
    .status-approved { background:#dbeafe; color:#1e40af; }
    .status-issued { background:#d1fae5; color:#065f46; }
    .status-partially_issued { background:#fed7aa; color:#9c4221; }
    .status-partially_consumed { background:#fef3c7; color:#92400e; }
    .status-fully_consumed { background:#d1fae5; color:#065f46; }
    .status-partially_returned { background:#e9d5ff; color:#6b21a5; }
    .status-returned { background:#e5e7eb; color:#374151; }
    .status-rejected { background:#fee2e2; color:#991b1b; }
    .status-cancelled { background:#f3f4f6; color:#6b7280; }
    .status-completed { background:#d1fae5; color:#065f46; }

    .info-label { font-weight:600; color:#4b5563; width:140px; display:inline-block; }

    .data-table { width:100%; border-collapse:collapse; font-size:.75rem; }
    .data-table th { background:#f8fafc; padding:.75rem; text-align:left; font-weight:600; color:#475569; border-bottom:2px solid #e2e8f0; }
    .data-table td { padding:.75rem; border-bottom:1px solid #e2e8f0; vertical-align:middle; }

    .badge-consumed { background:#fef3c7; color:#92400e; padding:.2rem .6rem; border-radius:20px; font-size:.7rem; display:inline-block; }
    .badge-returned { background:#e9d5ff; color:#6b21a5; padding:.2rem .6rem; border-radius:20px; font-size:.7rem; display:inline-block; }
    .badge-remaining { background:#dbeafe; color:#1e40af; padding:.2rem .6rem; border-radius:20px; font-size:.7rem; display:inline-block; }
    .badge-approved { background:#dbeafe; color:#1e40af; padding:.2rem .6rem; border-radius:20px; font-size:.7rem; display:inline-block; }
    .badge-stock-good { background:#dcfce7; color:#166534; padding:.2rem .6rem; border-radius:20px; font-size:.7rem; display:inline-block; }
    .badge-stock-low { background:#fed7aa; color:#9c4221; padding:.2rem .6rem; border-radius:20px; font-size:.7rem; display:inline-block; }
    .badge-stock-critical { background:#fee2e2; color:#991b1b; padding:.2rem .6rem; border-radius:20px; font-size:.7rem; display:inline-block; }

    .stock-level-bar { height:4px; background:#e2e8f0; border-radius:2px; overflow:hidden; margin-top:5px; }
    .stock-level-fill { height:100%; border-radius:2px; transition: width .3s ease; }
    .stock-level-fill.good { background:#22c55e; }
    .stock-level-fill.low { background:#f97316; }
    .stock-level-fill.critical { background:#ef4444; }

    .sig-img { max-height:64px; max-width:240px; object-fit:contain; display:block; margin: 6px auto 0; }

    .signature-section { margin-top:40px; padding-top:30px; border-top:2px dashed #e2e8f0; }
    /* Signature layout for screen: vertical inside each box, grid two columns */
    .signature-grid { display:grid; grid-template-columns: 1fr; gap:1rem; }
    @media (min-width: 768px) {
        .signature-grid { grid-template-columns: 1fr 1fr; }
    }

    .signature-box {
        text-align:center;
        padding:16px;
        background:#f8fafc;
        border-radius:12px;
        border:1px solid #e2e8f0;
        display:flex;
        flex-direction:column; /* name above, signature below */
        align-items:center;
        gap:8px;
        page-break-inside:avoid;
    }

    .signature-line { border-top:1px solid #cbd5e1; width:180px; height:0; margin: 6px 0; }

    /* Print-specific: place both signature boxes on same printed row, keep name above signature inside each box */
    @media print {
        /* general print cleanup */
        body { color:#000; background:#fff; }
        .no-print { display:none !important; }

        /* ensure the signature grid is two columns when printing */
        .signature-grid { grid-template-columns: 1fr 1fr !important; gap: 1rem; }

        /* minimal box visuals for print */
        .signature-box {
            background: transparent !important;
            border: none !important;
            padding: 6px !important;
            text-align: left !important;
            flex-direction: column !important; /* name above signature */
            align-items: flex-start !important;
        }

        .signature-box .sig-img {
            display:block !important;
            margin: 6px 0 0 0 !important;
            max-height: 80px !important;
        }

        /* text left aligned for printed signature area */
        .signature-box .name { font-weight:600; color:#000; text-align:left !important; }
        .signature-box .meta { font-size:12px; color:#333; text-align:left !important; }

        /* avoid page breaks inside signatures */
        .signature-box, .signature-grid { page-break-inside:avoid !important; page-break-after:avoid !important; }

        /* hide action controls on print */
        .actions-print-hide { display:none !important; }
    }
</style>

<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $requisition->requisition_number }}</h2>
                    <span class="status-badge status-{{ str_replace('_', '-', $requisition->status) }}">
                        {{ ucfirst(str_replace('_', ' ', $requisition->status)) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Created: {{ $requisition->created_at->format('F d, Y h:i A') }}
                </p>
            </div>

            <div class="flex gap-2 items-center actions-print-hide">
                <button onclick="window.print()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-print mr-1"></i> Print
                </button>

                <a href="{{ route('management.department-requisitions.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>

                @if($requisition->status === 'pending')
                <a href="{{ route('management.department-requisitions.approve-form', $requisition->id) }}"
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-check mr-1"></i> Approve
                </a>
                <button type="button" onclick="openRejectModal()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-times mr-1"></i> Reject
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Left Column - Requisition Info --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Requisition Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                    <i class="fas fa-info-circle mr-2"></i> Requisition Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Requisition Number:</span>
                        <span class="text-sm font-mono font-medium text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Department:</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">
                            <i class="fas fa-building text-xs"></i> {{ $requisition->department->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Requisition Type:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->requisition_type_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Date Needed:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->date_needed ? date('F d, Y', strtotime($requisition->date_needed)) : 'Not specified' }}</span>
                    </div>
                </div>
            </div>

            {{-- Requestor Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                    <i class="fas fa-user mr-2"></i> Requested By
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Name:</span>
                        <span class="text-sm text-gray-800 font-medium">{{ $requisition->requestedBy->first_name ?? 'N/A' }} {{ $requisition->requestedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Email:</span>
                        <span class="text-sm text-gray-600">{{ $requisition->requestedBy->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Requested On:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Approval Information --}}
            @if($requisition->approved_at)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                    <i class="fas fa-check-circle mr-2 text-green-500"></i> Approval Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Approved By:</span>
                        <span class="text-sm text-gray-800 font-medium">{{ $requisition->approvedBy->first_name ?? 'Management' }} {{ $requisition->approvedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Approved On:</span>
                        <span class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($requisition->approved_at)->format('F d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Rejection Information --}}
            @if($requisition->status === 'rejected' && $requisition->rejection_reason)
            <div class="bg-red-50 border border-red-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-red-700 uppercase tracking-wider mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Rejection Reason
                </h3>
                <p class="text-sm text-red-600">{{ $requisition->rejection_reason }}</p>
            </div>
            @endif

            {{-- Department / Store Notes --}}
            @if($requisition->department_notes)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-blue-700 uppercase tracking-wider mb-3">
                    <i class="fas fa-sticky-note mr-2"></i> Department Notes
                </h3>
                <p class="text-sm text-blue-600">{{ $requisition->department_notes }}</p>
            </div>
            @endif

            @if($requisition->store_notes)
            <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wider mb-3">
                    <i class="fas fa-store mr-2"></i> Store Notes
                </h3>
                <p class="text-sm text-green-600">{{ $requisition->store_notes }}</p>
            </div>
            @endif
        </div>

        {{-- Right Column - Items Table --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
                    <p class="text-xs text-gray-500">Total Items</p>
                    <p class="text-xl font-bold text-gray-800">{{ $requisition->total_items }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
                    <p class="text-xs text-gray-500">Requested Qty</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($requisition->total_quantity_requested, 2) }}</p>
                </div>
                @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'completed', 'partially_returned']))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
                    <p class="text-xs text-gray-500">Issued Qty</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($requisition->total_quantity_issued, 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
                    <p class="text-xs text-gray-500">Completion</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $requisition->issued_percentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $requisition->issued_percentage }}%</p>
                </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">
                        <i class="fas fa-boxes mr-2"></i> Requested Items
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Metrics</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Requested</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Approved</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Available Stock</th>
                                @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'completed', 'partially_returned']))
                                <th class="px-4 py-3 text-center text-xs font-semibold text-green-600">Issued</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-orange-600">Used</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600">Returned</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">In Dept</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($requisition->items as $item)
                            @php
                                $approvedQty = $item->quantity_approved ?? $item->quantity_requested;
                                $used = ($item->quantity_consumed ?? 0) + ($item->quantity_sold ?? 0);
                                $remainingInDept = max(0, ($item->issued_total_pieces ?? 0) - $used - ($item->quantity_returned ?? 0));
                                $availableStock = $item->total_available_stock ?? 0;
                                $stockPercentage = $item->stock_percentage ?? 0;
                                $stockStatus = $item->stock_status ?? 'critical';
                                $stockBadgeClass = $stockStatus == 'good' ? 'badge-stock-good' : ($stockStatus == 'low' ? 'badge-stock-low' : 'badge-stock-critical');
                                $stockFillClass = $stockStatus;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                    @if($item->requested_pack_type)
                                    <div class="text-xs text-gray-400 mt-1">Pack: {{ ucfirst($item->requested_pack_type) }} × {{ $item->requested_pack_size }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs text-gray-500">{{ $item->metrics ?? ($item->inventoryItem->unit_of_measurement ?? 'units') }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold">{{ number_format($item->quantity_requested, 2) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge-approved">{{ number_format($approvedQty, 2) }}</span>
                                    @if($item->approved_pack_type)
                                    <div class="text-xs text-gray-400">{{ ucfirst($item->approved_pack_type) }} × {{ $item->approved_pack_size }}</div>
                                    @endif
                                    @if($item->approval_notes)
                                    <div class="text-xs text-gray-400 italic">{{ $item->approval_notes }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div>
                                        <span class="{{ $stockBadgeClass }} inline-block px-2 py-1 rounded text-xs font-semibold">
                                            {{ number_format($availableStock, 2) }}
                                        </span>
                                    </div>
                                    <div class="stock-level-bar w-24 mx-auto mt-1">
                                        <div class="stock-level-fill {{ $stockFillClass }}" style="width: {{ $stockPercentage }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $stockPercentage }}% of requested
                                    </div>
                                </td>
                                @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'completed', 'partially_returned']))
                                <td class="px-4 py-3 text-center border-l border-gray-100">
                                    <span class="text-green-600 font-semibold">{{ number_format($item->quantity_issued ?? 0, 2) }}</span>
                                    @if($item->issued_pack_type)
                                    <div class="text-xs text-gray-400">{{ ucfirst($item->issued_pack_type) }} × {{ $item->issued_pack_size }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge-consumed">{{ number_format($used, 2) }}</span>
                                    <div class="text-xs text-gray-400">(consumed + sold)</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge-returned">{{ number_format($item->quantity_returned ?? 0, 2) }}</span>
                                    @if($item->returned_pack_type)
                                    <div class="text-xs text-gray-400">{{ ucfirst($item->returned_pack_type) }} × {{ $item->returned_pack_size }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge-remaining {{ $remainingInDept > 0 ? '' : 'text-gray-400' }}">{{ number_format($remainingInDept, 2) }}</span>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-xs font-semibold text-gray-500">Totals</td>
                                <td class="px-4 py-3 text-center font-semibold">{{ number_format($requisition->total_quantity_requested, 2) }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-blue-600">
                                    @php
                                        $totalApproved = $requisition->items->sum(function($item) {
                                            return $item->quantity_approved ?? $item->quantity_requested;
                                        });
                                    @endphp
                                    {{ number_format($totalApproved, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold">
                                    @php
                                        $totalAvailable = $requisition->items->sum(function($item) {
                                            return $item->total_available_stock ?? 0;
                                        });
                                    @endphp
                                    <span class="{{ $totalAvailable >= $totalApproved ? 'text-emerald-600' : ($totalAvailable >= ($totalApproved / 2) ? 'text-amber-600' : 'text-red-600') }}">
                                        {{ number_format($totalAvailable, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">-</td>
                                @if(in_array($requisition->status, ['issued', 'partially_issued', 'partially_consumed', 'completed', 'partially_returned']))
                                <td class="px-4 py-3 text-center font-semibold text-green-600">{{ number_format($requisition->total_quantity_issued, 2) }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-orange-600">{{ number_format($requisition->total_quantity_consumed + $requisition->total_quantity_sold, 2) }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-purple-600">{{ number_format($requisition->total_quantity_returned, 2) }}</td>
                                <td class="px-4 py-3 text-center font-semibold">{{ number_format($requisition->total_quantity_issued - ($requisition->total_quantity_consumed + $requisition->total_quantity_sold + $requisition->total_quantity_returned), 2) }}</td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- SIGNATURES SECTION: NAME above, signature below for each side, and both boxes appear side-by-side on print --}}
    <div class="signature-section">
        <div class="signature-grid">
            {{-- Requested By --}}
            <div class="signature-box">
                <div class="name">{{ $requisition->requestedBy->first_name ?? 'N/A' }} {{ $requisition->requestedBy->last_name ?? '' }}</div>
                <div class="meta text-xs text-gray-500">{{ $requisition->requestedBy->email ?? '' }}</div>

                @if(!empty($requisition->requestedBy->signature_path))
                    <img src="{{ asset('storage/' . $requisition->requestedBy->signature_path) }}" alt="Requested by signature" class="sig-img">
                @else
                    <div class="signature-line" aria-hidden="true"></div>
                    <div class="text-xs text-gray-400 italic mt-1">No signature uploaded</div>
                @endif

                <div class="text-xs text-gray-500 mt-2">{{ $requisition->created_at->format('F d, Y g:i A') }}</div>
            </div>

            {{-- Approved By --}}
            <div class="signature-box">
                <div class="name">
                    @if($requisition->approvedBy)
                        {{ $requisition->approvedBy->first_name ?? '' }} {{ $requisition->approvedBy->last_name ?? '' }}
                    @else
                        _________________________
                    @endif
                </div>
                <div class="meta text-xs text-gray-500">
                    @if($requisition->approvedBy)
                        {{ $requisition->approvedBy->email ?? '' }}
                    @else
                        Approved By
                    @endif
                </div>

                @if($requisition->approvedBy && !empty($requisition->approvedBy->signature_path))
                    <img src="{{ asset('storage/' . $requisition->approvedBy->signature_path) }}" alt="Approved by signature" class="sig-img">
                @else
                    <div class="signature-line" aria-hidden="true"></div>
                    <div class="text-xs text-gray-400 italic mt-1">
                        @if($requisition->status == 'approved' || in_array($requisition->status, ['partially_issued','issued']))
                            Pending signature
                        @else
                            Not yet approved
                        @endif
                    </div>
                @endif

                <div class="text-xs text-gray-500 mt-2">
                    @if($requisition->approved_at)
                        {{ \Carbon\Carbon::parse($requisition->approved_at)->format('F d, Y g:i A') }}
                    @else
                        {{ __('') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject modal (unchanged) --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.45)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Reject Requisition</h3>
            </div>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="rejectForm" method="POST" action="{{ route('management.department-requisitions.reject', $requisition->id) }}">
            @csrf
            <div class="p-6 space-y-5">
                <div class="bg-gray-50 rounded-lg p-3 text-sm">
                    <p class="font-medium text-gray-700">Requisition: {{ $requisition->requisition_number }}</p>
                    <p class="text-gray-500 text-xs mt-1">Department: {{ $requisition->department->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500"
                              placeholder="Please provide a detailed reason for rejecting this requisition..." required></textarea>
                    <p class="text-xs text-gray-500 mt-1">This reason will be visible to the department.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Internal Notes (Optional)
                    </label>
                    <textarea name="store_notes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500"
                              placeholder="Any internal notes for management/staff..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Only visible to management and store staff.</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-check mr-1"></i> Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal() { document.getElementById('rejectModal').classList.remove('hidden'); }
    function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }

    document.getElementById('rejectModal')?.addEventListener('click', function(e) { if (e.target === this) closeRejectModal(); });

    document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
        const reason = document.querySelector('#rejectForm textarea[name="rejection_reason"]').value.trim();
        if (!reason) { e.preventDefault(); alert('Please provide a reason for rejecting this requisition.'); return false; }
        if (reason.length < 10) { e.preventDefault(); alert('Please provide a detailed reason (at least 10 characters).'); return false; }
    });
</script>
@endsection
