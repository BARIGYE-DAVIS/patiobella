@extends('layouts.kitchen')

@section('title', 'Requisition Details')
@section('page-title', 'Requisition Details')

@section('content')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-approved { background-color: #dbeafe; color: #1e40af; }
    .status-issued { background-color: #d1fae5; color: #065f46; }
    .status-partially_issued { background-color: #fed7aa; color: #9c4221; }
    .status-partially_consumed { background-color: #fef3c7; color: #92400e; }
    .status-fully_consumed { background-color: #d1fae5; color: #065f46; }
    .status-partially_returned { background-color: #e9d5ff; color: #6b21a5; }
    .status-returned { background-color: #e5e7eb; color: #374151; }
    .status-rejected { background-color: #fee2e2; color: #991b1b; }
    .status-cancelled { background-color: #f3f4f6; color: #6b7280; }

    .info-label {
        font-weight: 600;
        color: #4b5563;
        width: 140px;
        display: inline-block;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .text-center { text-align: center; }

    .badge-consumed {
        background: #fef3c7;
        color: #92400e;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
    .badge-returned {
        background: #e9d5ff;
        color: #6b21a5;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
    .badge-remaining {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }
    .badge-approved {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
    }

    .sig-img {
        max-height: 64px;
        max-width: 240px;
        object-fit: contain;
        display: block;
    }

    .signature-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px dashed #e2e8f0;
    }

    .signature-box {
        text-align: center;
        padding: 20px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .signature-line {
        border-top: 1px solid #cbd5e1;
        width: 80%;
        margin: 20px auto 10px auto;
    }

    .signature-label {
        font-size: 0.7rem;
        color: #64748b;
        letter-spacing: 1px;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('kitchen.requisitions.index') }}" class="text-gray-600 hover:text-gray-800 inline-flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="text-sm">Back</span>
            </a>

            @if($requisition->status == 'pending')
            <form method="POST" action="{{ route('kitchen.requisitions.cancel', $requisition->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Cancel this requisition?')">
                    Cancel
                </button>
            </form>
            @endif
        </div>
    </div>

    <div id="requisitionContent" class="p-6">
        {{-- Status Badge --}}
        <div class="mb-6">
            <span class="status-badge status-{{ str_replace('_', '-', $requisition->status) }}">
                {{ ucfirst(str_replace('_', ' ', $requisition->status)) }}
            </span>
        </div>

        {{-- Rejection Reason --}}
        @if($requisition->status == 'rejected' && $requisition->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $requisition->rejection_reason }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Requisition Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Requisition Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="info-label">Requisition No:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Date Needed:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->date_needed ? date('F d, Y', strtotime($requisition->date_needed)) : 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Requested By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->requestedBy->first_name ?? '' }} {{ $requisition->requestedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Department:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->department->name ?? 'Kitchen' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Store Response</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="info-label">Approved By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approvedBy->first_name ?? '' }} {{ $requisition->approvedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex">
                        <span class="info-label">Approved At:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approved_at ? date('F d, Y g:i A', strtotime($requisition->approved_at)) : 'Not yet' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Statistics --}}
        @php
            $totalApproved = $requisition->items->sum(function($item) {
                return $item->quantity_approved ?? $item->quantity_requested;
            });
            $totalIssued    = $requisition->items->sum('issued_total_pieces');
            $totalConsumed  = $requisition->items->sum('quantity_consumed');
            $totalReturned  = $requisition->items->sum('returned_total_pieces');
            $totalRemaining = $totalIssued - ($totalConsumed + $totalReturned);
        @endphp

        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-5 gap-4 text-center">
                <div>
                    <p class="text-xs text-gray-500">Total Approved</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($totalApproved, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Issued</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($totalIssued, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Consumed</p>
                    <p class="text-xl font-bold text-amber-600">{{ number_format($totalConsumed, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Returned</p>
                    <p class="text-xl font-bold text-purple-600">{{ number_format($totalReturned, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Remaining</p>
                    <p class="text-xl font-bold text-emerald-600">{{ number_format($totalRemaining, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($requisition->department_notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Kitchen Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $requisition->department_notes }}</p>
            </div>
        </div>
        @endif

        @if($requisition->store_notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Store Notes</h4>
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-sm text-blue-700">{{ $requisition->store_notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">Requested Items</h4>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-left" style="width: 20%">Item</th>
                            <th class="text-left" style="width: 8%">Metrics</th>
                            <th class="text-center" style="width: 10%">Requested</th>
                            <th class="text-center" style="width: 10%">Approved</th>
                            <th class="text-center" style="width: 10%">Issued</th>
                            <th class="text-center" style="width: 10%">Consumed</th>
                            <th class="text-center" style="width: 10%">Returned</th>
                            <th class="text-center" style="width: 10%">Remaining</th>
                            <th class="text-left" style="width: 12%">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisition->items as $item)
                        @php
                            $unit       = $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units');
                            $approved   = $item->quantity_approved ?? $item->quantity_requested;
                            $issued     = $item->issued_total_pieces   ?? $item->quantity_issued   ?? 0;
                            $returned   = $item->returned_total_pieces ?? $item->quantity_returned ?? 0;
                            $consumed   = $item->quantity_consumed     ?? 0;
                            $remaining  = $issued - ($consumed + $returned);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $item->inventoryItem->item_code ?? '' }}</div>
                                @if($item->issued_pack_type && $item->issued_pack_size)
                                    <div class="text-xs text-blue-500 mt-0.5">
                                        {{ $item->quantity_issued }} {{ $item->issued_pack_type }}(s) &times; {{ $item->issued_pack_size }} = {{ number_format($issued, 2) }} pcs
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $unit }}</td>
                            <td class="px-4 py-3 text-center">
                                {{ number_format($item->quantity_requested, 2) }}
                                @if($item->requested_pack_type)
                                    <div class="text-xs text-gray-400">{{ $item->requested_pack_type }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge-approved">{{ number_format($approved, 2) }}</span>
                                @if($item->approved_pack_type && $item->approved_pack_size)
                                    <div class="text-xs text-gray-400">{{ $item->approved_pack_type }} × {{ $item->approved_pack_size }}</div>
                                @endif
                                @if($item->approval_notes)
                                    <div class="text-xs text-gray-400 italic mt-0.5">{{ Str::limit($item->approval_notes, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-green-600 font-semibold">
                                {{ number_format($issued, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge-consumed">{{ number_format($consumed, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($returned > 0)
                                    <span class="badge-returned">{{ number_format($returned, 2) }}</span>
                                    @if($item->returned_pack_type && $item->quantity_returned > 0)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $item->quantity_returned }} {{ $item->returned_pack_type }}(s)</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                             </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge-remaining">{{ number_format($remaining, 2) }}</span>
                             </td>
                            <td class="px-4 py-3 text-gray-500">{{ $item->notes ?? '—' }}</td>
                         </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-4 py-3 font-bold">TOTALS</td>
                            <td class="px-4 py-3 text-center font-bold">{{ number_format($requisition->items->sum('quantity_requested'), 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600">{{ number_format($totalApproved, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-green-600">{{ number_format($totalIssued, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-amber-600">{{ number_format($totalConsumed, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-purple-600">{{ number_format($totalReturned, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-emerald-600">{{ number_format($totalRemaining, 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- SIGNATURES SECTION AT THE BOTTOM --}}
        <div class="signature-section">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Requested By Signature --}}
                <div class="signature-box">
                    <div class="mb-3">
                        <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                        </svg>
                    </div>
                    <div class="font-semibold text-gray-700 mb-2">REQUESTED BY</div>
                    @if(!empty($requisition->requestedBy->signature_path))
                        <img src="{{ asset('storage/' . $requisition->requestedBy->signature_path) }}" alt="Requested by signature" class="sig-img mx-auto">
                    @else
                        <div class="signature-line"></div>
                        <div class="text-xs text-gray-400 italic mt-2">No signature uploaded</div>
                    @endif
                    <div class="mt-3">
                        <div class="font-medium text-gray-800">{{ $requisition->requestedBy->first_name ?? '' }} {{ $requisition->requestedBy->last_name ?? '' }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $requisition->created_at->format('F d, Y g:i A') }}</div>
                    </div>
                </div>

                {{-- Approved By Signature --}}
                <div class="signature-box">
                    <div class="mb-3">
                        <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                        </svg>
                    </div>
                    <div class="font-semibold text-gray-700 mb-2">APPROVED BY</div>
                    @if($requisition->approvedBy && !empty($requisition->approvedBy->signature_path))
                        <img src="{{ asset('storage/' . $requisition->approvedBy->signature_path) }}" alt="Approved by signature" class="sig-img mx-auto">
                    @else
                        <div class="signature-line"></div>
                        <div class="text-xs text-gray-400 italic mt-2">
                            @if($requisition->status == 'approved' || $requisition->status == 'partially_issued' || $requisition->status == 'issued')
                                Pending signature
                            @else
                                Not yet approved
                            @endif
                        </div>
                    @endif
                    <div class="mt-3">
                        <div class="font-medium text-gray-800">
                            @if($requisition->approvedBy)
                                {{ $requisition->approvedBy->first_name ?? '' }} {{ $requisition->approvedBy->last_name ?? '' }}
                            @else
                                _________________________
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            @if($requisition->approved_at)
                                {{ date('F d, Y g:i A', strtotime($requisition->approved_at)) }}
                            @else
                                Pending approval
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- html2pdf (client-side fallback) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    function downloadClientPdf() {
        const element = document.getElementById('requisitionContent');
        if (!element) return alert('Nothing to export.');

        const opt = {
            margin:       0.4,
            filename:     'Requisition_{{ $requisition->requisition_number }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save();
    }
</script>
@endsection
