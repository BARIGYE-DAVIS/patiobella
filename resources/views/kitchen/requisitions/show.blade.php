@extends('layouts.kitchen')

@section('title', 'Requisition Details')
@section('page-title', 'Requisition Details')

@section('content')

@php
    $stockMovement = \App\Models\StockMovement::where('reason', 'LIKE', '%' . $requisition->requisition_number . '%')->first();
    $issueMovement = $stockMovement;

    $approver = $requisition->approved_by ? \App\Models\User::find($requisition->approved_by) : null;
    $approverName = $approver ? trim(($approver->first_name ?? '') . ' ' . ($approver->last_name ?? '')) : null;
    $issuedBy = $issueMovement ? $issueMovement->createdBy : null;
    $takenBy = $issueMovement->taken_by ?? $requisition->taken_by ?? null;

    $statusConfig = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'approved' => 'bg-blue-100 text-blue-800',
        'issued' => 'bg-green-100 text-green-800',
        'partially_issued' => 'bg-orange-100 text-orange-800',
        'partially_consumed' => 'bg-yellow-100 text-yellow-800',
        'fully_consumed' => 'bg-green-100 text-green-800',
        'partially_returned' => 'bg-purple-100 text-purple-800',
        'returned' => 'bg-gray-100 text-gray-800',
        'rejected' => 'bg-red-100 text-red-800',
        'cancelled' => 'bg-gray-100 text-gray-600',
    ];
    $statusClass = $statusConfig[$requisition->status] ?? 'bg-gray-100 text-gray-800';
@endphp

<div id="requisitionPrintArea">
    {{-- Header - Visible on screen only --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden print-hide">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-wrap gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
                <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('kitchen.requisitions.index') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium transition flex items-center gap-1">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                @if($requisition->status == 'pending')
                <form method="POST" action="{{ route('kitchen.requisitions.cancel', $requisition->id) }}" class="inline" onsubmit="return confirm('Cancel this requisition?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition flex items-center gap-1">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- PRINT HEADER - Only visible when printing --}}
    <div class="print-header hidden">
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #f97316; padding-bottom: 15px;">
            <h1 style="margin: 0; color: #1f2937; font-size: 24px;">KITCHEN REQUISITION FORM</h1>
            <p style="margin: 5px 0 0; color: #6b7280; font-size: 12px;">Official Kitchen Requisition Document</p>
        </div>
    </div>

    <div class="p-6">
        {{-- Status Badge --}}
        <div class="mb-6">
            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                <i class="fas fa-tag mr-1 text-xs"></i>
                {{ ucfirst(str_replace('_', ' ', $requisition->status)) }}
            </span>
        </div>

        {{-- Rejection Reason --}}
        @if($requisition->status == 'rejected' && $requisition->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
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
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                    <i class="fas fa-info-circle mr-1"></i> Requisition Information
                </h4>
                <div class="space-y-2">
                    <div class="flex flex-wrap">
                        <span class="text-sm font-medium text-gray-500 w-32">Requisition No:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex flex-wrap">
                        <span class="text-sm font-medium text-gray-500 w-32">Date Needed:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->date_needed ? date('F d, Y', strtotime($requisition->date_needed)) : 'Not specified' }}</span>
                    </div>
                    <div class="flex flex-wrap">
                        <span class="text-sm font-medium text-gray-500 w-32">Requested By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->requestedBy->first_name ?? '' }} {{ $requisition->requestedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex flex-wrap">
                        <span class="text-sm font-medium text-gray-500 w-32">Department:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->department->name ?? 'Kitchen' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                    <i class="fas fa-store mr-1"></i> Store Response
                </h4>
                <div class="space-y-2">
                    <div class="flex flex-wrap">
                        <span class="text-sm font-medium text-gray-500 w-32">Approved By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approvedBy->first_name ?? '' }} {{ $requisition->approvedBy->last_name ?? '' }}</span>
                    </div>
                    <div class="flex flex-wrap">
                        <span class="text-sm font-medium text-gray-500 w-32">Approved At:</span>
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
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
                <div>
                    <i class="fas fa-check-circle text-blue-400 text-lg mb-1"></i>
                    <p class="text-xs text-gray-500">Total Approved</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($totalApproved, 2) }}</p>
                </div>
                <div>
                    <i class="fas fa-box-open text-green-400 text-lg mb-1"></i>
                    <p class="text-xs text-gray-500">Total Issued</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($totalIssued, 2) }}</p>
                </div>
                <div>
                    <i class="fas fa-utensils text-amber-400 text-lg mb-1"></i>
                    <p class="text-xs text-gray-500">Total Consumed</p>
                    <p class="text-xl font-bold text-amber-600">{{ number_format($totalConsumed, 2) }}</p>
                </div>
                <div>
                    <i class="fas fa-undo-alt text-purple-400 text-lg mb-1"></i>
                    <p class="text-xs text-gray-500">Total Returned</p>
                    <p class="text-xl font-bold text-purple-600">{{ number_format($totalReturned, 2) }}</p>
                </div>
                <div>
                    <i class="fas fa-chart-line text-emerald-400 text-lg mb-1"></i>
                    <p class="text-xs text-gray-500">Remaining</p>
                    <p class="text-xl font-bold text-emerald-600">{{ number_format($totalRemaining, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($requisition->department_notes)
        <div class="mb-6">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                <i class="fas fa-sticky-note mr-1"></i> Kitchen Notes
            </h4>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <p class="text-sm text-gray-700">{{ $requisition->department_notes }}</p>
            </div>
        </div>
        @endif

        @if($requisition->store_notes)
        <div class="mb-6">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                <i class="fas fa-store mr-1"></i> Store Notes
            </h4>
            <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                <p class="text-sm text-blue-700">{{ $requisition->store_notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        <div>
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                <i class="fas fa-list mr-1"></i> Requested Items
            </h4>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 w-[18%]">Item</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 w-[7%]">Unit</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600 w-[9%]">Requested</th>
                            <th class="text-center px-4 py-3 font-semibold text-blue-600 bg-blue-50 w-[9%]">Approved</th>
                            <th class="text-center px-4 py-3 font-semibold text-green-600 bg-green-50 w-[9%]">Issued</th>
                            <th class="text-center px-4 py-3 font-semibold text-amber-600 bg-amber-50 w-[9%]">Consumed</th>
                            <th class="text-center px-4 py-3 font-semibold text-purple-600 bg-purple-50 w-[9%]">Returned</th>
                            <th class="text-center px-4 py-3 font-semibold text-emerald-600 bg-emerald-50 w-[9%]">Remaining</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 w-[11%]">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requisition->items as $item)
                        @php
                            $unit       = $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units');
                            $approved   = $item->quantity_approved ?? $item->quantity_requested;
                            $issued     = $item->issued_total_pieces   ?? $item->quantity_issued   ?? 0;
                            $returned   = $item->returned_total_pieces ?? $item->quantity_returned ?? 0;
                            $consumed   = $item->quantity_consumed     ?? 0;
                            $remaining  = $issued - ($consumed + $returned);
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $item->inventoryItem->item_code ?? '' }}</div>
                                @if($item->issued_pack_type && $item->issued_pack_size)
                                    <div class="text-xs text-blue-500 mt-0.5">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $item->quantity_issued }} {{ $item->issued_pack_type }}(s) × {{ $item->issued_pack_size }} = {{ number_format($issued, 2) }} pcs
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
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                    <i class="fas fa-check mr-1 text-xs"></i>{{ number_format($approved, 2) }}
                                </span>
                                @if($item->approved_pack_type && $item->approved_pack_size)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $item->approved_pack_type }} × {{ $item->approved_pack_size }}</div>
                                @endif
                                @if($item->approval_notes)
                                    <div class="text-xs text-gray-400 italic mt-0.5">{{ Str::limit($item->approval_notes, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-green-600 font-semibold">
                                {{ number_format($issued, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                                    <i class="fas fa-utensils mr-1 text-xs"></i>{{ number_format($consumed, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($returned > 0)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                        <i class="fas fa-undo-alt mr-1 text-xs"></i>{{ number_format($returned, 2) }}
                                    </span>
                                    @if($item->returned_pack_type && $item->quantity_returned > 0)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $item->quantity_returned }} {{ $item->returned_pack_type }}(s)</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                             </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-chart-line mr-1 text-xs"></i>{{ number_format($remaining, 2) }}
                                </span>
                             </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $item->notes ?? '—' }}</td>
                         </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="2" class="px-4 py-3 font-bold text-gray-700">TOTALS</td>
                            <td class="px-4 py-3 text-center font-bold text-gray-700">{{ number_format($requisition->items->sum('quantity_requested'), 2) }}</td>
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

        {{-- SIGNATURES SECTION - Professional Layout --}}
        <div class="signatures-print mt-8 pt-6 border-t-2 border-dashed border-gray-300">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Requested By Signature --}}
                <div class="text-center">
                    <div class="border-t border-gray-400 w-40 mx-auto mb-3"></div>
                    @if($requisition->requestedBy && $requisition->requestedBy->signature_path)
                        <img src="{{ asset('storage/' . $requisition->requestedBy->signature_path) }}" alt="Requested By Signature" class="h-14 mx-auto mb-2 object-contain">
                    @else
                        <div class="h-14 mb-2"></div>
                    @endif
                    <div class="font-semibold text-gray-800 text-sm">
                        {{ trim(($requisition->requestedBy->first_name ?? '') . ' ' . ($requisition->requestedBy->last_name ?? '')) ?: '_________________' }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Requested By</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $requisition->created_at->format('F d, Y') }}</div>
                </div>

                {{-- Approved By Signature --}}
                <div class="text-center">
                    <div class="border-t border-gray-400 w-40 mx-auto mb-3"></div>
                    @if($approver && $approver->signature_path)
                        <img src="{{ asset('storage/' . $approver->signature_path) }}" alt="Approved By Signature" class="h-14 mx-auto mb-2 object-contain">
                    @else
                        <div class="h-14 mb-2"></div>
                    @endif
                    <div class="font-semibold text-gray-800 text-sm">
                        {{ $approverName ?: ($requisition->status == 'approved' ? 'Pending Signature' : '_________________') }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Approved By</div>
                    <div class="text-xs text-gray-400 mt-0.5">
                        {{ $requisition->approved_at ? date('F d, Y', strtotime($requisition->approved_at)) : '________' }}
                    </div>
                </div>

                {{-- Issued By Signature --}}
                <div class="text-center">
                    <div class="border-t border-gray-400 w-40 mx-auto mb-3"></div>
                    @if($issuedBy && $issuedBy->signature_path)
                        <img src="{{ asset('storage/' . $issuedBy->signature_path) }}" alt="Issued By Signature" class="h-14 mx-auto mb-2 object-contain">
                    @else
                        <div class="h-14 mb-2"></div>
                    @endif
                    <div class="font-semibold text-gray-800 text-sm">
                        {{ $issuedBy ? (($issuedBy->first_name ?? '') . ' ' . ($issuedBy->last_name ?? '')) : ($takenBy ? 'Pending Signature' : '_________________') }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Issued By (Store)</div>
                    <div class="text-xs text-gray-400 mt-0.5">
                        {{ $issueMovement ? $issueMovement->created_at->format('F d, Y') : '________' }}
                    </div>
                </div>

                {{-- Received By (Taken By) --}}
                <div class="text-center">
                    <div class="border-t border-gray-400 w-40 mx-auto mb-3"></div>
                    <div class="h-14 mb-2"></div>
                    <div class="font-semibold text-gray-800 text-sm">
                        {{ $takenBy ?: '_________________' }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Received By</div>
                    <div class="text-xs text-gray-400 mt-0.5">
                        {{ $issueMovement ? $issueMovement->created_at->format('F d, Y') : '________' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Print Styles --}}
<style>
    .print-hide {
        display: block;
    }

    .print-header {
        display: none;
    }

    @media print {
        .print-hide {
            display: none !important;
        }

        .print-header {
            display: block !important;
        }

        body {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .bg-white, .rounded-lg, .shadow-sm, .border {
            background: white !important;
            box-shadow: none !important;
            border-color: #e5e7eb !important;
        }

        .p-6 {
            padding: 1.5rem !important;
        }

        .signatures-print {
            margin-top: 40px !important;
            page-break-inside: avoid !important;
        }

        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        th, td {
            border: 1px solid #e5e7eb !important;
            padding: 8px !important;
        }

        thead th {
            background: #f9fafb !important;
        }

        img {
            max-height: 60px !important;
            max-width: 180px !important;
            object-fit: contain !important;
        }

        i, .fas, .far {
            display: none !important;
        }

        .grid {
            display: grid !important;
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
        }

        .md\:grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }

        .gap-6 {
            gap: 1.5rem !important;
        }

        .border-t-2 {
            border-top-width: 1px !important;
        }

        .text-center {
            text-align: center !important;
        }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@endsection
