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

    $takenBy = $issueMovement->taken_by ?? null;

    $approver     = $requisition->approved_by ? \App\Models\User::find($requisition->approved_by) : null;
    $approverName = $approver ? trim(($approver->first_name ?? '') . ' ' . ($approver->last_name ?? '')) : null;
@endphp

<div class="space-y-4">

    {{-- ── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('store.department-requisitions.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
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
        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full border {{ $sc['pill'] }}">
            {{ $sc['label'] }}
        </span>
    </div>

    {{-- ── Info Cards Row ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Requisition Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Requisition Information</h3>
            <dl class="space-y-3">
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Requisition No</dt>
                    <dd class="text-sm font-mono font-semibold text-gray-800">{{ $requisition->requisition_number }}</dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Department</dt>
                    <dd>
                        <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                            {{ $requisition->department->name ?? 'N/A' }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Requested By</dt>
                    <dd class="text-sm text-gray-800">
                        {{ trim(($requisition->requestedBy->first_name ?? '') . ' ' . ($requisition->requestedBy->last_name ?? '')) ?: '—' }}
                    </dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Date Needed</dt>
                    <dd class="text-sm text-gray-800">
                        {{ $requisition->date_needed ? date('F d, Y', strtotime($requisition->date_needed)) : 'Not specified' }}
                    </dd>
                </div>
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Date Created</dt>
                    <dd class="text-sm text-gray-800">{{ $requisition->created_at->format('F d, Y \a\t h:i A') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Processing Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Processing Information</h3>
            <dl class="space-y-3">
                @if($approverName)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Approved By</dt>
                    <dd class="text-sm text-gray-800">{{ $approverName }}</dd>
                </div>
                @endif
                @if($requisition->approved_at)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Approved At</dt>
                    <dd class="text-sm text-gray-800">
                        {{ \Carbon\Carbon::parse($requisition->approved_at)->format('F d, Y \a\t h:i A') }}
                    </dd>
                </div>
                @endif
                @if($takenBy)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Taken By</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $takenBy }}
                        </span>
                    </dd>
                </div>
                @endif
                @if($requisition->returned_by)
                <div class="flex items-start gap-2">
                    <dt class="text-xs font-medium text-gray-500 w-36 shrink-0 pt-0.5">Returned By</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M3 10h10a8 8 0 018 8v2M3 10l6 6M3 10l6-6"/>
                            </svg>
                            {{ $requisition->returned_by }}
                        </span>
                    </dd>
                </div>
                @endif
                @if(!$approverName && !$takenBy && !$requisition->returned_by)
                <p class="text-sm text-gray-400 italic">No processing activity yet.</p>
                @endif
            </dl>
        </div>
    </div>

    {{-- ── Notes Row ────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Department Notes</h3>
            <p class="text-sm text-gray-700 leading-relaxed">
                {{ $requisition->department_notes ?: 'No notes provided.' }}
            </p>
        </div>
        @if($requisition->store_notes)
        <div class="bg-white rounded-xl border border-amber-200 shadow-sm px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-amber-500 mb-3">Store Notes</h3>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $requisition->store_notes }}</p>
        </div>
        @endif
    </div>

    {{-- ── Items Table ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Requested Items</h3>
            <span class="text-xs text-gray-500">
                {{ $requisition->items->count() }} item{{ $requisition->items->count() !== 1 ? 's' : '' }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Item</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Metrics</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Requested</th>

                        {{-- APPROVED --}}
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-blue-600 border-l border-gray-200 bg-blue-50">
                            Approved
                        </th>

                        {{-- ISSUED group --}}
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-green-600 border-l border-gray-200 bg-green-50">
                            Issued
                        </th>

                        {{-- RETURNED group --}}
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-purple-600 border-l border-gray-200 bg-purple-50">
                            Returned
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($requisition->items as $item)
                    @php
                        $unit          = $item->metrics ?? ($item->inventoryItem->base_unit ?? 'units');
                        $requested     = (float) $item->quantity_requested;
                        $approvedQty   = (float) ($item->quantity_approved ?? $requested);
                        $issuedTotal   = (float) ($item->issued_total_pieces ?? 0);
                        $fullyIssued   = $issuedTotal >= $requested;
                        $returnedTotal = (float) ($item->returned_total_pieces ?? 0);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">

                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 text-sm">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $item->inventoryItem->item_code ?? '' }}</p>
                        </td>

                        <td class="px-4 py-3 text-gray-500 text-sm">{{ $unit }}</td>

                        {{-- Requested --}}
                        <td class="px-4 py-3 text-center tabular-nums font-semibold text-gray-800">
                            {{ number_format($requested, 2) }}
                            <div class="text-xs text-gray-400 font-normal">{{ $unit }}</div>
                        </td>

                        {{-- Approved --}}
                        <td class="px-4 py-3 text-center tabular-nums bg-blue-50 border-l border-gray-100">
                            <span class="font-semibold text-blue-700">{{ number_format($approvedQty, 2) }}</span>
                            <div class="text-xs text-gray-400">{{ $unit }}</div>
                        </td>

                        {{-- Issued Total --}}
                        <td class="px-4 py-3 text-center tabular-nums font-semibold border-l border-gray-100">
                            @if($issuedTotal > 0)
                                <span class="{{ $fullyIssued ? 'text-green-600' : 'text-orange-500' }}">
                                    {{ number_format($issuedTotal, 2) }}
                                    <span class="text-xs text-gray-400 font-normal">{{ $unit }}</span>
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Returned Total --}}
                        <td class="px-4 py-3 text-center tabular-nums font-semibold border-l border-gray-100">
                            @if($returnedTotal > 0)
                                <span class="text-purple-600">
                                    {{ number_format($returnedTotal, 2) }}
                                    <span class="text-xs text-gray-400 font-normal">{{ $unit }}</span>
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>

                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Totals</td>
                        <td class="px-4 py-3 text-center tabular-nums font-bold text-gray-800">
                            {{ number_format($requisition->items->sum('quantity_requested'), 2) }}
                        </td>
                        <td class="px-4 py-3 text-center tabular-nums font-bold text-blue-600 bg-blue-50 border-l border-gray-200">
                            {{ number_format($requisition->items->sum('quantity_approved'), 2) }}
                            <div class="text-xs text-gray-400 font-normal">base units</div>
                        </td>
                        <td class="px-4 py-3 text-center tabular-nums font-bold text-green-600 border-l border-gray-200">
                            {{ number_format($requisition->items->sum('issued_total_pieces'), 2) }}
                            <div class="text-xs text-gray-400 font-normal">base units</div>
                        </td>
                        <td class="px-4 py-3 text-center tabular-nums font-bold text-purple-600 border-l border-gray-200">
                            {{ number_format($requisition->items->sum('returned_total_pieces'), 2) }}
                            <div class="text-xs text-gray-400 font-normal">base units</div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── Action Buttons ───────────────────────────────────────────────────── --}}

    @if($requisition->status === 'pending')
    <div class="flex justify-end gap-3">
        <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium hidden text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
            Reject
        </button>
        <form action="{{ route('store.department-requisitions.approve', $requisition->id) }}" method="POST">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm hidden font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                Approve Requisition
            </button>
        </form>
    </div>
    @endif

    @if($requisition->status === 'approved')
    <div class="flex justify-end">
        <a href="{{ route('store.department-requisitions.issue-form', $requisition->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
            Issue Items
        </a>
    </div>
    @endif

    @php
        $canProcessReturn = in_array($requisition->status, ['issued', 'partially_issued', 'partially_returned', 'partially_consumed']);
    @endphp

    @if($canProcessReturn)
    <div class="flex justify-end mt-2">
        <a href="{{ route('store.department-requisitions.return-form', $requisition->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition">
            Process Return
        </a>
    </div>
    @endif

</div>

{{-- ── Rejection Modal ──────────────────────────────────────────────────────── --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.45)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Reject Requisition</h3>
            </div>
            <button onclick="document.getElementById('rejectModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('store.department-requisitions.reject', $requisition->id) }}" method="POST">
            @csrf
            <div class="px-6 py-5">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Reason for rejection <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="rejection_reason"
                    rows="4"
                    required
                    placeholder="Please provide a reason for rejecting this requisition…"
                    class="w-full text-sm px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none text-gray-800 placeholder-gray-400"
                ></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button"
                        onclick="document.getElementById('rejectModal').classList.add('hidden')"
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

<script>
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>

@endsection
