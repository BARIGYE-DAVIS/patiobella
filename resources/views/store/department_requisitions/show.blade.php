@extends('layouts.store')

@section('title', 'Requisition Details')
@section('page-title', 'Requisition Details')

@section('content')

@php
    $statusConfig = [
        'pending'            => ['pill' => 'bg-amber-50 text-amber-700 border-amber-200',       'label' => 'Pending'],
        'approved'           => ['pill' => 'bg-blue-50 text-blue-700 border-blue-200',          'label' => 'Approved'],
        'issued'             => ['pill' => 'bg-green-50 text-green-700 border-green-200',       'label' => 'Issued'],
        'partially_issued'   => ['pill' => 'bg-orange-50 text-orange-700 border-orange-200',    'label' => 'Partially Issued'],
        'partially_consumed' => ['pill' => 'bg-yellow-50 text-yellow-700 border-yellow-200',    'label' => 'Partially Consumed'],
        'fully_consumed'     => ['pill' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Fully Consumed'],
        'completed'          => ['pill' => 'bg-teal-50 text-teal-700 border-teal-200',          'label' => 'Completed'],
        'partially_returned' => ['pill' => 'bg-purple-50 text-purple-700 border-purple-200',    'label' => 'Partially Returned'],
        'returned'           => ['pill' => 'bg-indigo-50 text-indigo-700 border-indigo-200',    'label' => 'Returned'],
        'rejected'           => ['pill' => 'bg-red-50 text-red-700 border-red-200',             'label' => 'Rejected'],
        'cancelled'          => ['pill' => 'bg-gray-100 text-gray-500 border-gray-200',         'label' => 'Cancelled'],
    ];
    $sc = $statusConfig[$requisition->status] ?? ['pill' => 'bg-gray-100 text-gray-500 border-gray-200', 'label' => ucfirst($requisition->status)];

    $issueMovement = \App\Models\StockMovement::whereHas('inventoryItem', function($q) use ($requisition) {
            $q->whereIn('id', $requisition->items->pluck('inventory_item_id'));
        })
        ->where('movement_type_id', 5)
        ->where('reason', 'like', '%' . $requisition->requisition_number . '%')
        ->orderBy('created_at', 'desc')
        ->first();

    $takenBy = $issueMovement->taken_by ?? $requisition->taken_by ?? null;
    $issuedBy = $issueMovement->createdBy ?? null;

    $approver     = $requisition->approved_by ? \App\Models\User::find($requisition->approved_by) : null;
    $approverName = $approver ? trim(($approver->first_name ?? '') . ' ' . ($approver->last_name ?? '')) : null;
@endphp

<div id="requisitionPrintArea">

    {{-- Page Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4 flex items-center justify-between flex-wrap gap-3 print-hide">
        <div class="flex items-center gap-3">
            <a href="{{ route('store.department-requisitions.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
            <div class="h-5 w-px bg-gray-200"></div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 leading-tight">
                    {{ $requisition->requisition_number }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $requisition->department->name ?? 'N/A' }} &middot; {{ $requisition->created_at->format('F d, Y') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 print-hide">
            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full border {{ $sc['pill'] }}">
                <i class="fas fa-tag mr-1 text-xs"></i> {{ $sc['label'] }}
            </span>
            <button onclick="printRequisition()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    {{-- PRINT HEADER - Only visible when printing --}}
    <div class="print-header hidden">
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 15px;">
            <h1 style="margin: 0; color: #1f2937; font-size: 24px;">STORE REQUISITION FORM</h1>
            <p style="margin: 5px 0 0; color: #6b7280; font-size: 12px;">Official Department Requisition Document</p>
        </div>
    </div>

    {{-- Info Cards Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-gray-400"></i> Requisition Information
            </h3>
            <dl class="space-y-3">
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Requisition No</dt>
                    <dd class="text-sm font-mono font-semibold text-gray-800">{{ $requisition->requisition_number }}</dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Department</dt>
                    <dd class="text-sm text-gray-800">{{ $requisition->department->name ?? 'N/A' }}</dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Requisition Type</dt>
                    <dd class="text-sm text-gray-800 capitalize">{{ $requisition->requisition_type ?? 'Normal' }}</dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Requested By</dt>
                    <dd class="text-sm text-gray-800">
                        {{ trim(($requisition->requestedBy->first_name ?? '') . ' ' . ($requisition->requestedBy->last_name ?? '')) ?: '—' }}
                    </dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Date Needed</dt>
                    <dd class="text-sm text-gray-800">
                        {{ $requisition->date_needed ? date('F d, Y', strtotime($requisition->date_needed)) : 'Not specified' }}
                    </dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Date Created</dt>
                    <dd class="text-sm text-gray-800">{{ $requisition->created_at->format('F d, Y \a\t h:i A') }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4 flex items-center gap-2">
                <i class="fas fa-cogs text-gray-400"></i> Processing Information
            </h3>
            <dl class="space-y-3">
                @if($approverName)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Approved By</dt>
                    <dd class="text-sm text-gray-800">{{ $approverName }}</dd>
                </div>
                @endif
                @if($requisition->approved_at)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Approved At</dt>
                    <dd class="text-sm text-gray-800">
                        {{ \Carbon\Carbon::parse($requisition->approved_at)->format('F d, Y \a\t h:i A') }}
                    </dd>
                </div>
                @endif
                @if($takenBy)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Received By</dt>
                    <dd class="text-sm font-semibold text-green-700">{{ $takenBy }}</dd>
                </div>
                @endif
                @if($issuedBy)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Issued By</dt>
                    <dd class="text-sm text-gray-800">{{ $issuedBy->first_name ?? '' }} {{ $issuedBy->last_name ?? '' }}</dd>
                </div>
                @endif
                @if($requisition->returned_by)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0">Returned By</dt>
                    <dd class="text-sm font-semibold text-purple-700">{{ $requisition->returned_by }}</dd>
                </div>
                @endif
                @if(!$approverName && !$takenBy && !$requisition->returned_by)
                <p class="text-sm text-gray-400 italic">No processing activity yet.</p>
                @endif
            </dl>
        </div>
    </div>

    {{-- Notes Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3 flex items-center gap-2">
                <i class="fas fa-sticky-note text-gray-400"></i> Department Notes
            </h3>
            <p class="text-sm text-gray-700 leading-relaxed">
                {{ $requisition->department_notes ?: 'No notes provided.' }}
            </p>
        </div>
        @if($requisition->store_notes)
        <div class="bg-white rounded-xl border border-amber-200 shadow-sm px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-amber-500 mb-3 flex items-center gap-2">
                <i class="fas fa-store text-amber-500"></i> Store Notes
            </h3>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $requisition->store_notes }}</p>
        </div>
        @endif
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-2">
                <i class="fas fa-boxes text-gray-400"></i> Requested Items
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Unit</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Requested</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 bg-blue-50">Approved</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 bg-green-50">Issued</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600 bg-purple-50">Returned</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($requisition->items as $item)
                    @php
                        $unit          = $item->metrics ?? ($item->inventoryItem->unit_of_measurement ?? 'units');
                        $requested     = (float) $item->quantity_requested;
                        $approvedQty   = (float) ($item->quantity_approved ?? $requested);
                        $issuedTotal   = (float) ($item->issued_total_pieces ?? 0);
                        $returnedTotal = (float) ($item->returned_total_pieces ?? 0);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 text-sm">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $item->inventoryItem->item_code ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-sm">{{ $unit }}</td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ number_format($requested, 2) }}</td>
                        <td class="px-4 py-3 text-center bg-blue-50">
                            <span class="font-semibold text-blue-700">{{ number_format($approvedQty, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($issuedTotal > 0)
                                <span class="font-semibold text-green-600">{{ number_format($issuedTotal, 2) }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($returnedTotal > 0)
                                <span class="font-semibold text-purple-600">{{ number_format($returnedTotal, 2) }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Totals</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-800">
                            {{ number_format($requisition->items->sum('quantity_requested'), 2) }}
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-blue-600 bg-blue-50">
                            {{ number_format($requisition->items->sum('quantity_approved'), 2) }}
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-green-600">
                            {{ number_format($requisition->items->sum('issued_total_pieces'), 2) }}
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-purple-600">
                            {{ number_format($requisition->items->sum('returned_total_pieces'), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- SIGNATURES SECTION - Professional Layout --}}
    <div class="signatures-print mt-8 pt-6 border-t-2 border-dashed border-gray-300">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                <div class="text-xs text-gray-500 mt-1">Issued By</div>
                <div class="text-xs text-gray-400 mt-0.5">
                    {{ $issueMovement ? $issueMovement->created_at->format('F d, Y') : '________' }}
                </div>
            </div>

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

    {{-- Action Buttons - Hidden when printing --}}
    <div class="print-hide">
        @if($requisition->status === 'pending')
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
                <i class="fas fa-times"></i> Reject
            </button>
            <form action="{{ route('store.department-requisitions.approve', $requisition->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check"></i> Approve Requisition
                </button>
            </form>
        </div>
        @endif

        @if($requisition->status === 'approved')
        <div class="flex justify-end mt-6">
            <a href="{{ route('store.department-requisitions.issue-form', $requisition->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-truck"></i> Issue Items
            </a>
        </div>
        @endif

        @php
            $canProcessReturn = in_array($requisition->status, ['issued', 'partially_issued', 'partially_returned', 'partially_consumed']);
        @endphp

        @if($canProcessReturn)
        <div class="flex justify-end mt-4">
            <a href="{{ route('store.department-requisitions.return-form', $requisition->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition">
                <i class="fas fa-undo-alt"></i> Process Return
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Rejection Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center hidden print-hide" style="background:rgba(0,0,0,0.45)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-times text-red-600 text-sm"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Reject Requisition</h3>
            </div>
            <button onclick="document.getElementById('rejectModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('store.department-requisitions.reject', $requisition->id) }}" method="POST">
            @csrf
            <div class="px-6 py-5">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Reason for rejection <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" rows="4" required
                    placeholder="Please provide a reason for rejecting this requisition…"
                    class="w-full text-sm px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none"></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Print Styles - Professional Document */
    @media print {
        /* Hide all print-hide elements */
        .print-hide {
            display: none !important;
        }

        /* Show print header */
        .print-header {
            display: block !important;
        }

        /* Reset body for print */
        body {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
            font-family: 'Segoe UI', 'Arial', sans-serif !important;
        }

        /* Ensure all containers have white background */
        .bg-white, .rounded-xl, .border, .shadow-sm {
            background: white !important;
            box-shadow: none !important;
            border-color: #e5e7eb !important;
        }

        /* Remove borders on print for cleaner look */
        .border-t-2 {
            border-top: 1px solid #d1d5db !important;
        }

        /* Ensure signatures display properly */
        .signatures-print {
            margin-top: 40px !important;
            page-break-inside: avoid !important;
        }

        /* Keep table borders clean */
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

        /* Page break controls */
        .page-break-before {
            page-break-before: always !important;
        }

        .page-break-inside-avoid {
            page-break-inside: avoid !important;
        }

        /* Signature images */
        img {
            max-height: 60px !important;
            max-width: 180px !important;
            object-fit: contain !important;
        }

        /* Hide icons on print */
        i, .fas, .far {
            display: none !important;
        }

        /* Professional spacing */
        .mt-8 {
            margin-top: 2rem !important;
        }

        .pt-6 {
            padding-top: 1.5rem !important;
        }

        /* Ensure text is readable */
        .text-gray-400, .text-gray-500 {
            color: #6b7280 !important;
        }

        .text-gray-800, .text-gray-900 {
            color: #1f2937 !important;
        }

        /* Keep grid layout for signatures on print */
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
    }

    /* Print header - hidden by default */
    .print-header {
        display: none;
    }
</style>

<script>
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });

    function printRequisition() {
        window.print();
    }
</script>
@endsection
