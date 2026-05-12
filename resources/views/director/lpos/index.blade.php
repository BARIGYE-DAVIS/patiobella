@extends('layouts.director')
@section('title', 'Local Purchase Orders')
@section('page-title', 'Local Purchase Orders')

@section('content')

@php
    $activeTab = request('tab', 'pending');
    $tabs = [
        'pending'  => ['label' => 'Pending Approval', 'count' => $pendingCount,  'count_pill' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'approved' => ['label' => 'Approved',         'count' => $approvedCount, 'count_pill' => 'bg-green-50 text-green-700 border-green-200'],
        'rejected' => ['label' => 'Rejected',         'count' => $rejectedCount, 'count_pill' => 'bg-red-50 text-red-700 border-red-200'],
    ];
    $statusConfig = [
        'pending_director'  => ['pill' => 'bg-amber-50 text-amber-700 border-amber-200',  'label' => 'Pending'],
        'director_approved' => ['pill' => 'bg-green-50 text-green-700 border-green-200',  'label' => 'Approved'],
        'director_rejected' => ['pill' => 'bg-red-50 text-red-700 border-red-200',        'label' => 'Rejected'],
    ];
@endphp

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Local Purchase Orders</h2>
            <p class="text-xs text-gray-500 mt-0.5">Review and approve or reject LPOs submitted for director sign-off</p>
        </div>
        <div class="flex items-center gap-2">
            @foreach($tabs as $key => $tab)
            <span class="text-xs font-medium px-2.5 py-1 rounded-full border {{ $tab['count_pill'] }}">
                {{ $tab['count'] }} {{ strtolower($tab['label']) }}
            </span>
            @endforeach
        </div>
    </div>

    {{-- Tabs + Table Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Tabs --}}
        <div class="border-b border-gray-200 px-6">
            <nav class="flex gap-0 -mb-px">
                @foreach($tabs as $key => $tab)
                <a href="{{ route('director.lpos.index') }}?tab={{ $key }}"
                   class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap
                       {{ $activeTab === $key
                           ? 'border-blue-500 text-blue-600'
                           : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $tab['label'] }}
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full border {{ $tab['count_pill'] }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
                @endforeach
            </nav>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">LPO number</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Requisition #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Vendor</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">LPO date</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-500">Total amount</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lpos as $lpo)
                    @php
                        $sc = $statusConfig[$lpo->status] ?? ['pill' => 'bg-gray-100 text-gray-500 border-gray-200', 'label' => ucfirst(str_replace('_', ' ', $lpo->status))];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-500">
                            {{ $lpo->lpo_number }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 border border-gray-200 font-mono">
                                {{ $lpo->requisition->requisition_number ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 font-medium">
                            {{ $lpo->vendor->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $lpo->lpo_date->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums font-semibold text-gray-800">
                            <span class="text-xs text-gray-400 mr-0.5">UGX</span>{{ number_format($lpo->total_amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $sc['pill'] }}">
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('director.lpos.show', $lpo->id) }}"
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
                        <td colspan="7" class="px-4 py-14 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                            </svg>
                            <p class="text-sm text-gray-400">No LPOs found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($lpos->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between flex-wrap gap-2">
            <p class="text-xs text-gray-500">
                Showing {{ $lpos->firstItem() }}–{{ $lpos->lastItem() }} of {{ $lpos->total() }} LPOs
            </p>
            <div>{{ $lpos->appends(request()->query())->links() }}</div>
        </div>
        @endif

    </div> 

@endsection
