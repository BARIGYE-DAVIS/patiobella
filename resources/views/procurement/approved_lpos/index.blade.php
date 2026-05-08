@extends('layouts.procurement')

@section('title', 'Approved LPOs')

@section('page-title', 'Director Approved LPOs')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">LPOs Approved by Director</h3>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Convert approved LPOs to External Purchase Orders and send to vendors</p>
    </div>

    <div class="p-2 sm:p-6">
        {{-- Mobile card view (xs to md) --}}
        <div class="block md:hidden space-y-3">
            @forelse($approvedLpos as $lpo)
            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-mono font-semibold text-gray-800 text-sm">{{ $lpo->lpo_number }}</span>
                    <span class="text-xs text-gray-500">{{ $lpo->lpo_date->format('Y-m-d') }}</span>
                </div>
                <div class="text-xs text-gray-500 mb-0.5">Req #: <span class="text-gray-700">{{ $lpo->requisition->requisition_number ?? 'N/A' }}</span></div>
                <div class="text-sm text-gray-800 font-medium mb-2">{{ $lpo->vendor->name ?? 'N/A' }}</div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                    <span class="text-sm font-semibold text-green-600">UGX {{ number_format($lpo->total_amount, 2) }}</span>
                    <div class="flex items-center gap-2">
                        @if($lpo->director_notes)
                            <button type="button"
                                    onclick="showNotes('{{ addslashes($lpo->lpo_number) }}', '{{ addslashes($lpo->director_notes) }}')"
                                    class="text-xs text-yellow-600 hover:text-yellow-800 underline">
                                Notes
                            </button>
                        @endif
                        <a href="{{ route('procurement.approved-lpos.convert-to-epo', $lpo->id) }}"
                           class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Convert to EPO
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-8">No director approved LPOs found.</p>
            @endforelse
        </div>

        {{-- Desktop table view (md and up) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">LPO Number</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Requisition #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase whitespace-nowrap">LPO Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Total Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Director Notes</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($approvedLpos as $lpo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-800 whitespace-nowrap">{{ $lpo->lpo_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $lpo->requisition->requisition_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">{{ $lpo->vendor->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 text-center whitespace-nowrap">{{ $lpo->lpo_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600 whitespace-nowrap">UGX {{ number_format($lpo->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($lpo->director_notes)
                                <button type="button"
                                        onclick="showNotes('{{ addslashes($lpo->lpo_number) }}', '{{ addslashes($lpo->director_notes) }}')"
                                        class="text-yellow-600 hover:text-yellow-800 cursor-pointer text-sm">
                                    📝 View Notes
                                </button>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('procurement.approved-lpos.convert-to-epo', $lpo->id) }}"
                               class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm inline-flex items-center gap-1 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Convert to EPO
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No director approved LPOs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-2 sm:px-0">
            {{ $approvedLpos->links() }}
        </div>
    </div>
</div>

{{-- Notes Modal --}}
<div id="notesModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden px-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
        <div class="bg-yellow-600 px-6 py-4 rounded-t-lg flex justify-between items-center">
            <h3 class="text-base font-semibold text-white" id="modalTitle">Director Notes</h3>
            <button type="button" onclick="closeNotesModal()" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-1">For LPO: <span id="modalLpoNumber" class="font-semibold text-gray-700"></span></p>
                    <div class="bg-yellow-50 rounded-lg p-4 mt-2">
                        <p id="modalNotes" class="text-gray-700 whitespace-pre-wrap text-sm"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end">
            <button type="button" onclick="closeNotesModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 text-sm">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function showNotes(lpoNumber, notes) {
        document.getElementById('modalLpoNumber').innerText = lpoNumber;
        document.getElementById('modalNotes').innerText = notes;
        document.getElementById('notesModal').classList.remove('hidden');
    }

    function closeNotesModal() {
        document.getElementById('notesModal').classList.add('hidden');
    }
</script>
@endsection
