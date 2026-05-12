@extends('layouts.management')
@section('title', 'Requisitions Management')
@section('page-title', 'Requisitions Management')

@section('content')

@php
    $activeTab = request('tab', 'pending');
    $tabs = [
        'pending'  => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'all'      => 'All Requisitions',
    ];
    $statusConfig = [
        'pending'   => ['pill' => 'bg-amber-50 text-amber-700 border-amber-200',  'label' => 'Pending'],
        'approved'  => ['pill' => 'bg-green-50 text-green-700 border-green-200',  'label' => 'Approved'],
        'rejected'  => ['pill' => 'bg-red-50 text-red-700 border-red-200',        'label' => 'Rejected'],
        'fulfilled' => ['pill' => 'bg-blue-50 text-blue-700 border-blue-200',     'label' => 'Fulfilled'],
    ];
@endphp

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Requisitions Management</h2>
            <p class="text-xs text-gray-500 mt-0.5">Review and action store requisition requests</p>
        </div>
        <span class="text-xs font-medium px-3 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
            {{ $requisitions->total() }} total
        </span>
    </div>

    {{-- Tabs + Filters Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Tabs --}}
        <div class="border-b border-gray-200 px-6">
            <nav class="flex gap-0 -mb-px">
                @foreach($tabs as $key => $label)
                <a href="{{ route('management.requisitions.index') }}?tab={{ $key }}"
                   class="px-4 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap
                       {{ $activeTab === $key
                           ? 'border-blue-500 text-blue-600'
                           : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $label }}
                </a>
                @endforeach
            </nav>
        </div>

        {{-- Filter Bar --}}
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('management.requisitions.index') }}" class="flex flex-wrap gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Date from</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="text-sm px-3 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Date to</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="text-sm px-3 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex flex-col gap-1 flex-1 min-w-[180px]">
                    <label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Requisition # or store…"
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .78 1.63l-6.28 7.53V19a1 1 0 0 1-1.45.89l-4-2A1 1 0 0 1 9 17v-3.84L3.22 5.63A1 1 0 0 1 3 4z"/>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('management.requisitions.index') }}?tab={{ $activeTab }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Req #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Store</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Requested by</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Date needed</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-500">Items</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-500">Total qty</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requisitions as $req)
                    @php
                        $sc = $statusConfig[$req->status] ?? ['pill' => 'bg-gray-100 text-gray-500 border-gray-200', 'label' => ucfirst(str_replace('_', ' ', $req->status))];
                        $person = $req->requestedBy
                            ? trim($req->requestedBy->first_name . ' ' . $req->requestedBy->last_name)
                            : 'N/A';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-500">
                            {{ $req->requisition_number }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $req->store->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $person }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $req->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">
                            @if($req->date_needed)
                                @php $dn = \Carbon\Carbon::parse($req->date_needed); @endphp
                                <span class="{{ $dn->isPast() && $req->status === 'pending' ? 'text-red-500 font-medium' : '' }}">
                                    {{ $dn->format('Y-m-d') }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums text-gray-700">{{ $req->items->count() }}</td>
                        <td class="px-4 py-3 text-right tabular-nums text-gray-700">{{ number_format($req->items->sum('quantity_requested'), 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $sc['pill'] }}">
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('management.requisitions.show', $req->id) }}"
                               class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-gray-200 rounded-lg px-2.5 py-1.5 hover:bg-blue-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-14 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                            </svg>
                            <p class="text-sm text-gray-400">No requisitions found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($requisitions->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between flex-wrap gap-2">
            <p class="text-xs text-gray-500">
                Showing {{ $requisitions->firstItem() }}–{{ $requisitions->lastItem() }} of {{ $requisitions->total() }} requisitions
            </p>
            <div>{{ $requisitions->appends(request()->query())->links() }}</div>
        </div>
        @endif

    </div>
</div>

@endsection
