@forelse($purchaseOrders as $po)
<tr class="border-b hover:bg-gray-50">
    <td class="px-3 py-2 text-xs font-mono font-semibold text-blue-600">{{ $po->po_number }}</td>
    <td class="px-3 py-2 text-xs">{{ $po->vendor->name ?? '—' }}</td>
    <td class="px-3 py-2 text-xs">{{ $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('Y-m-d') : '—' }}</td>
    <td class="px-3 py-2 text-xs">{{ $po->expected_delivery_date ? \Carbon\Carbon::parse($po->expected_delivery_date)->format('Y-m-d') : '—' }}</td>
    <td class="px-3 py-2 text-center">
        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold
            @if($po->status == 'draft') bg-yellow-100 text-yellow-800
            @elseif($po->status == 'sent') bg-blue-100 text-blue-800
            @elseif($po->status == 'partially_received') bg-orange-100 text-orange-800
            @elseif($po->status == 'fully_received') bg-green-100 text-green-800
            @elseif($po->status == 'cancelled') bg-red-100 text-red-800
            @else bg-gray-100 text-gray-800 @endif">
            {{ ucfirst(str_replace('_', ' ', $po->status)) }}
        </span>
    </td>
    <td class="px-3 py-2 text-right font-mono text-xs">{{ number_format($po->total_amount, 2) }}</td>
    <td class="px-3 py-2 text-center">
        @php
            $docs = \App\Models\Document::where('purchase_order_id', $po->id)->get();
        @endphp

        <div class="flex items-center justify-center gap-1 flex-wrap">
            @foreach($docs as $doc)
                <div class="flex items-center gap-0.5">
                    {{-- View Button --}}
                    <button type="button"
                            onclick="viewDocument({{ $doc->id }}, '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                            class="document-badge"
                            title="{{ $doc->original_name }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>View</span>
                    </button>

                    {{-- Delete Document Button --}}
                    <form action="{{ route('procurement.purchase-orders.delete-document', $doc->id) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Delete this document? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded transition" title="Delete document">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endforeach

            {{-- Attach Button --}}
            <button type="button"
                    onclick="openDocUploadModal({{ $po->id }})"
                    class="attach-btn"
                    title="{{ $docs->count() > 0 ? 'Attach more documents' : 'Attach document' }}">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>{{ $docs->count() > 0 ? 'Add' : 'Attach' }}</span>
            </button>
        </div>
    </td>
    <td class="px-3 py-2 text-center">
        <div class="flex items-center justify-center gap-1">
            <a href="{{ route('procurement.purchase-orders.show', $po->id) }}"
               class="text-blue-600 hover:text-blue-800" title="View">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </a>
            @if($po->status == 'draft')
            <a href="{{ route('procurement.purchase-orders.edit', $po->id) }}"
               class="text-green-600 hover:text-green-800" title="Edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            @endif
            <a href="{{ route('procurement.purchase-orders.download-pdf', $po->id) }}"
               class="text-red-600 hover:text-red-800" title="Download PDF">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </a>
        </div>
    </td>
</tr>

@empty
<tr>
    <td colspan="8" class="px-3 py-8 text-center text-gray-500 text-sm">
        No purchase orders found.
    </td>
</tr>
@endforelse
