@php
    $statusConfig = [
        'pending_director'  => ['pill' => 'status-pending',  'label' => 'Pending'],
        'director_approved' => ['pill' => 'status-approved', 'label' => 'Approved'],
        'director_rejected' => ['pill' => 'status-rejected', 'label' => 'Rejected'],
    ];
@endphp

@forelse($lpos as $lpo)
@php
    $sc = $statusConfig[$lpo->status] ?? ['pill' => 'bg-gray-100 text-gray-500', 'label' => ucfirst(str_replace('_', ' ', $lpo->status))];
@endphp
<tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
    <td class="px-4 py-3">
        <span class="font-mono text-xs font-semibold text-gray-700">
            {{ $lpo->lpo_number }}
        </span>
    </td>
    <td class="px-4 py-3">
        <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 border border-gray-200 font-mono">
            {{ $lpo->requisition->requisition_number ?? 'N/A' }}
        </span>
    </td>
    <td class="px-4 py-3">
        <span class="text-sm font-medium text-gray-800">
            {{ $lpo->vendor->name ?? 'N/A' }}
        </span>
        @if($lpo->type == 'emergency')
            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-medium bg-red-100 text-red-700">
                <i class="fas fa-exclamation-triangle mr-0.5 text-[8px]"></i> Emergency
            </span>
        @endif
    </td>
    <td class="px-4 py-3 text-center text-gray-500 text-sm">
        {{ $lpo->lpo_date ? $lpo->lpo_date->format('Y-m-d') : '—' }}
    </td>
    <td class="px-4 py-3 text-right tabular-nums">
        <span class="text-sm font-semibold text-gray-800">
            UGX {{ number_format($lpo->total_amount, 2) }}
        </span>
    </td>
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $sc['pill'] }}">
            {{ $sc['label'] }}
        </span>
    </td>
    <td class="px-4 py-3 text-center">
        <a href="{{ route('director.lpos.show', $lpo->id) }}"
           class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-gray-200 rounded-lg px-2.5 py-1.5 hover:bg-blue-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            View Details
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="px-4 py-14 text-center">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
        </svg>
        <p class="text-sm text-gray-500">No LPOs found matching your criteria.</p>
        <p class="text-xs text-gray-400 mt-1">Try adjusting your search or filters</p>
    </td>
</tr>
@endforelse
