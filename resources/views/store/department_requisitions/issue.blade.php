{{-- resources/views/store/department_requisitions/issue.blade.php --}}

@extends('layouts.store')

@section('title', 'Issue Items')

@section('page-title', 'Issue Items to Department')

@section('content')
<div class="space-y-4">

    {{-- Page Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('store.department-requisitions.show', $requisition->id) }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
            <div class="h-5 w-px bg-gray-200"></div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 leading-tight">
                    Issue Items — {{ $requisition->requisition_number }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $requisition->department->name ?? 'Department' }} &middot; {{ $requisition->created_at->format('F d, Y') }}
                </p>
            </div>
        </div>
        <button type="button" id="previewBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
            <i class="fas fa-eye"></i> Preview Issue
        </button>
    </div>

    {{-- Info Note --}}
    <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg px-5 py-4">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>FIFO (First In, First Out):</strong> Items will be issued from the oldest batches first (by expiry date).
            The system automatically suggests quantities following FIFO. You can adjust as needed.
        </p>
    </div>

    <form method="POST" action="{{ route('store.department-requisitions.issue', $requisition->id) }}" id="issueForm">
        @csrf

        {{-- Taken By (Receiver) --}}
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
            <label class="font-semibold text-green-800 text-sm block mb-2">
                <i class="fas fa-user-check mr-1"></i> <span class="text-red-500">*</span> Who is receiving these items?
            </label>
            <input type="text"
                   name="taken_by"
                   id="taken_by"
                   value="{{ old('taken_by') }}"
                   class="w-full px-4 py-2.5 border border-green-200 rounded-lg text-sm focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 bg-white"
                   placeholder="Enter full name of the department staff receiving the items"
                   required>
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-signature text-amber-500 mr-1"></i> This person will sign as the receiver.
            </p>
        </div>

        {{-- Items Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <i class="fas fa-boxes mr-1"></i> Items to Issue (FIFO - Oldest Batches First)
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
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Already Issued</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-600 bg-green-50">Batches Available (FIFO)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Total to Issue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requisition->items as $index => $item)
                        @php
                            $unit = $item->inventoryItem->unit_of_measurement ?? 'piece';
                            $requestedQty = (float) $item->quantity_requested;
                            $approvedQty = (float) ($item->quantity_approved ?? $requestedQty);
                            $alreadyIssued = (float) ($item->issued_total_pieces ?? 0);
                            $remainingToIssue = max(0, $approvedQty - $alreadyIssued);

                            // Get batches with remaining quantity (FIFO - oldest expiry first)
                            $batches = \App\Models\Batch::where('inventory_item_id', $item->inventory_item_id)
                                ->where('batch_status', 'active')
                                ->where('remaining_quantity', '>', 0)
                                ->orderBy('expiry_date', 'asc')
                                ->orderBy('created_at', 'asc')
                                ->get();

                            $totalAvailable = $batches->sum('remaining_quantity');
                            $canFulfill = $totalAvailable >= $remainingToIssue;

                            // Calculate suggested FIFO quantities
                            $suggestedQtys = [];
                            $remaining = $remainingToIssue;
                            foreach ($batches as $batch) {
                                $take = min($remaining, $batch->remaining_quantity);
                                if ($take > 0) {
                                    $suggestedQtys[$batch->id] = $take;
                                }
                                $remaining -= $take;
                                if ($remaining <= 0) break;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50" data-item-index="{{ $index }}" data-remaining="{{ $remainingToIssue }}" data-approved="{{ $approvedQty }}" data-requested="{{ $requestedQty }}">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $item->inventoryItem->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $item->inventoryItem->item_code ?? '' }}</p>
                                <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $index }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $unit }}</td>
                            <td class="px-4 py-3 text-center font-semibold">{{ number_format($requestedQty, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">{{ number_format($approvedQty, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($alreadyIssued > 0)
                                    <span class="text-orange-600">{{ number_format($alreadyIssued, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($remainingToIssue <= 0)
                                    <div class="text-green-600 font-medium">✓ Fully issued</div>
                                @elseif($totalAvailable <= 0)
                                    <div class="text-red-600 font-medium">✗ Out of stock</div>
                                @else
                                    <div class="space-y-2">
                                        @foreach($batches as $batchIdx => $batch)
                                            @php
                                                $suggestedQty = $suggestedQtys[$batch->id] ?? 0;
                                                $maxFromBatch = min($batch->remaining_quantity, $remainingToIssue);
                                                $batchUnit = $batch->unit_of_measurement ?? $unit;
                                            @endphp
                                            @if($suggestedQty > 0)
                                            <div class="batch-item border border-gray-200 rounded-lg p-2 bg-gray-50">
                                                <div class="flex justify-between items-center mb-2">
                                                    <div>
                                                        <span class="font-mono text-xs font-semibold text-gray-700">{{ $batch->batch_number }}</span>
                                                        @if($batch->expiry_date)
                                                            <span class="text-xs text-gray-400 ml-2">
                                                                <i class="fas fa-calendar-alt"></i> Exp: {{ date('d/m/Y', strtotime($batch->expiry_date)) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-gray-500">Available: {{ number_format($batch->remaining_quantity, 2) }} {{ $batchUnit }}</span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <label class="text-xs text-gray-600">Qty to take:</label>
                                                    <input type="hidden" name="batches[{{ $index }}][{{ $batchIdx }}][batch_id]" value="{{ $batch->id }}">
                                                    <input type="number"
                                                           name="batches[{{ $index }}][{{ $batchIdx }}][quantity]"
                                                           id="batch_qty_{{ $index }}_{{ $batchIdx }}"
                                                           class="batch-qty w-28 px-2 py-1 border border-gray-300 rounded-lg text-sm text-center"
                                                           value="{{ $suggestedQty }}"
                                                           min="0"
                                                           max="{{ $maxFromBatch }}"
                                                           step="0.01"
                                                           data-max="{{ $maxFromBatch }}"
                                                           data-item-index="{{ $index }}">
                                                    <span class="text-xs text-gray-500">{{ $batchUnit }}</span>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach

                                        <div class="text-right mt-2 pt-2 border-t border-gray-200">
                                            <span class="text-xs text-gray-600">Total to issue:</span>
                                            <span id="item_total_{{ $index }}" class="font-bold text-green-600 ml-2">0.00</span>
                                            <span class="text-xs text-gray-500"> {{ $unit }}</span>
                                            <input type="hidden" name="items[{{ $index }}][quantity_issued]" id="item_quantity_{{ $index }}" value="0">
                                            <input type="hidden" name="items[{{ $index }}][issued_total_pieces]" id="item_total_pieces_{{ $index }}" value="0">
                                        </div>

                                        @if(!$canFulfill)
                                            <div class="text-amber-600 text-xs mt-1">
                                                <i class="fas fa-exclamation-triangle"></i> Only {{ number_format($totalAvailable, 2) }} {{ $unit }} available. {{ number_format($remainingToIssue - $totalAvailable, 2) }} will not be issued.
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span id="display_total_{{ $index }}" class="text-gray-600">0.00</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-600">Grand Total:</td>
                            <td class="px-4 py-3 text-center font-bold text-green-600" id="grandTotal">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Store Notes --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-5 mt-4">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                <i class="fas fa-sticky-note mr-1"></i> Store Notes (Optional)
            </label>
            <textarea name="store_notes" id="store_notes" rows="2"
                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Any notes for the department..."></textarea>
        </div>

        {{-- Actions --}}
        <div class="mt-4 flex justify-end gap-3">
            <a href="{{ route('store.department-requisitions.show', $requisition->id) }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-green-600 text-sm text-white rounded-lg hover:bg-green-700 transition font-medium">
                <i class="fas fa-check-circle mr-1"></i> Confirm Issue
            </button>
        </div>

    </form>
</div>

{{-- Preview Modal --}}
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
    <div class="min-h-screen p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-600"></i> Issue Preview
                </h3>
                <div class="flex gap-2">
                    <button onclick="printPreview()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button onclick="closePreviewModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            <div id="previewContent" class="p-6"></div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemRows = document.querySelectorAll('tbody tr[data-item-index]');

        itemRows.forEach(row => {
            const itemIndex = row.getAttribute('data-item-index');
            const batchInputs = document.querySelectorAll(`[id^="batch_qty_${itemIndex}_"]`);

            // If there are no batch inputs for this row (fully issued / out of stock),
            // skip setting up listeners — the DOM elements won't exist.
            if (!batchInputs.length) return;

            function updateItemTotal() {
                let total = 0;
                batchInputs.forEach(input => {
                    total += parseFloat(input.value) || 0;
                });

                const remainingNeeded = parseFloat(row.getAttribute('data-remaining')) || 0;
                if (total > remainingNeeded) total = remainingNeeded;

                // Guard: elements only exist when the batch section is rendered
                const itemTotalEl        = document.getElementById(`item_total_${itemIndex}`);
                const displayTotalEl     = document.getElementById(`display_total_${itemIndex}`);
                const itemQuantityEl     = document.getElementById(`item_quantity_${itemIndex}`);
                const itemTotalPiecesEl  = document.getElementById(`item_total_pieces_${itemIndex}`);

                if (itemTotalEl)       itemTotalEl.innerText        = total.toFixed(2);
                if (displayTotalEl)    displayTotalEl.innerText     = total.toFixed(2);
                if (itemQuantityEl)    itemQuantityEl.value         = total;
                if (itemTotalPiecesEl) itemTotalPiecesEl.value      = total;

                updateGrandTotal();
            }

            batchInputs.forEach(input => {
                input.addEventListener('change', function() { updateItemTotal(); });
                input.addEventListener('input', function() {
                    let val = parseFloat(this.value) || 0;
                    const max = parseFloat(this.getAttribute('max')) || 0;
                    if (val > max) this.value = max;
                    if (val < 0) this.value = 0;
                    updateItemTotal();
                });
            });

            updateItemTotal();
        });
    });

    function updateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('[id^="item_total_"]').forEach(el => {
            grand += parseFloat(el.innerText) || 0;
        });
        const grandTotalEl = document.getElementById('grandTotal');
        if (grandTotalEl) grandTotalEl.innerText = grand.toFixed(2);
    }

    document.getElementById('issueForm').addEventListener('submit', function(e) {
        const takenBy = document.getElementById('taken_by').value.trim();
        if (!takenBy) {
            e.preventDefault();
            alert('Please enter the name of the person receiving these items.');
            document.getElementById('taken_by').focus();
            return false;
        }

        let hasQty = false;
        document.querySelectorAll('[id^="item_quantity_"]').forEach(input => {
            if (parseFloat(input.value) > 0) hasQty = true;
        });

        if (!hasQty) {
            e.preventDefault();
            alert('No items to issue. Please specify quantities for at least one item.');
            return false;
        }
    });

    function generatePreviewHTML() {
        const requisition = @json($requisition);
        const items = [];

        document.querySelectorAll('tbody tr[data-item-index]').forEach(row => {
            const itemIndex = row.getAttribute('data-item-index');
            const itemName = row.querySelector('.font-medium')?.innerText || 'N/A';
            const unit = row.querySelector('td:nth-child(2)')?.innerText || 'piece';
            const approved = row.getAttribute('data-approved') || 0;
            const requested = row.getAttribute('data-requested') || 0;
            const quantityToIssue = parseFloat(document.getElementById(`item_quantity_${itemIndex}`)?.value || 0);

            if (quantityToIssue > 0) {
                items.push({ name: itemName, unit: unit, approved: approved, requested: requested, issued: quantityToIssue });
            }
        });

        const takenBy = document.getElementById('taken_by').value || 'Not specified';
        const storeNotes = document.getElementById('store_notes').value || '';
        const now = new Date();
        const formattedDate = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        const currentUser = @json(Auth::user());

        let requestedBySignature = '';
        if (requisition.requested_by && requisition.requested_by.signature_path) {
            requestedBySignature = `<img src="{{ asset('storage') }}/${requisition.requested_by.signature_path}" style="max-height:50px; max-width:150px;">`;
        }

        let approvedBySignature = '';
        if (requisition.approved_by && requisition.approved_by.signature_path) {
            approvedBySignature = `<img src="{{ asset('storage') }}/${requisition.approved_by.signature_path}" style="max-height:50px; max-width:150px;">`;
        }

        let issuedBySignature = '';
        if (currentUser && currentUser.signature_path) {
            issuedBySignature = `<img src="{{ asset('storage') }}/${currentUser.signature_path}" style="max-height:50px; max-width:150px;">`;
        }

        return `
            <div style="font-family: Arial, sans-serif; max-width: 100%; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px;">
                    <h2 style="margin: 0; color: #111827;">ISSUE NOTE</h2>
                    <p style="margin: 4px 0 0; color: #6b7280;">Department Issue Slip</p>
                    <p style="margin: 4px 0 0; font-size: 12px;">Requisition: ${requisition.requisition_number}</p>
                </div>

                <div style="margin-bottom: 18px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <tr><td style="padding: 4px;"><strong>Date Issued:</strong> ${formattedDate}</td><td style="padding: 4px;"><strong>Department:</strong> ${requisition.department?.name || 'N/A'}</td></tr>
                        <tr><td style="padding: 4px;"><strong>Received By:</strong> ${takenBy}</td><td style="padding: 4px;"><strong>Issued By:</strong> ${currentUser?.first_name || ''} ${currentUser?.last_name || ''}</td></tr>
                    </table>
                </div>

                ${storeNotes ? `<div style="margin-bottom: 16px; padding: 8px; background: #f9fafb; border-left: 4px solid #22c55e;"><strong>Notes:</strong><p style="margin: 6px 0 0;">${storeNotes}</p></div>` : ''}

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr><th style="border:1px solid #e5e7eb; padding:8px; background:#22c55e; color:#fff;">#</th>
                            <th style="border:1px solid #e5e7eb; padding:8px; background:#22c55e; color:#fff;">Item</th>
                            <th style="border:1px solid #e5e7eb; padding:8px; background:#22c55e; color:#fff;">Requested</th>
                            <th style="border:1px solid #e5e7eb; padding:8px; background:#22c55e; color:#fff;">Approved</th>
                            <th style="border:1px solid #e5e7eb; padding:8px; background:#22c55e; color:#fff;">Issued</th>
                            <th style="border:1px solid #e5e7eb; padding:8px; background:#22c55e; color:#fff;">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map((item, idx) => `
                            <tr>
                                <td style="border:1px solid #e5e7eb; padding:8px;">${idx+1}</td>
                                <td style="border:1px solid #e5e7eb; padding:8px;">${item.name}</td>
                                <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;">${parseFloat(item.requested).toFixed(2)}</td>
                                <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;">${parseFloat(item.approved).toFixed(2)}</td>
                                <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;"><strong>${item.issued.toFixed(2)}</strong></td>
                                <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;">${item.unit}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                    <tfoot>
                        <tr><td colspan="4" style="border:1px solid #e5e7eb; padding:8px; text-align:right;"><strong>Total Issued:</strong></td>
                            <td style="border:1px solid #e5e7eb; padding:8px; text-align:center;"><strong>${items.reduce((sum, i) => sum + i.issued, 0).toFixed(2)}</strong></td>
                            <td style="border:1px solid #e5e7eb; padding:8px;"></td>
                        </tr>
                    </tfoot>
                </table>

                <div style="margin-top: 30px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 33%; text-align: center;">
                                <div style="border-top: 1px solid #111827; width: 80%; margin: 0 auto 10px auto;"></div>
                                ${requestedBySignature}
                                <div><strong>${requisition.requested_by?.first_name || ''} ${requisition.requested_by?.last_name || ''}</strong></div>
                                <div style="font-size: 11px;">Requested By</div>
                            </td>
                            <td style="width: 33%; text-align: center;">
                                <div style="border-top: 1px solid #111827; width: 80%; margin: 0 auto 10px auto;"></div>
                                ${approvedBySignature}
                                <div><strong>${requisition.approved_by?.first_name || ''} ${requisition.approved_by?.last_name || ''}</strong></div>
                                <div style="font-size: 11px;">Approved By</div>
                            </td>
                            <td style="width: 33%; text-align: center;">
                                <div style="border-top: 1px solid #111827; width: 80%; margin: 0 auto 10px auto;"></div>
                                ${issuedBySignature}
                                <div><strong>${currentUser?.first_name || ''} ${currentUser?.last_name || ''}</strong></div>
                                <div style="font-size: 11px;">Issued By</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding-top: 20px;">
                                <div style="border-top: 1px solid #111827; width: 80%; margin: 0 auto 10px auto;"></div>
                                <div><strong>${takenBy}</strong></div>
                                <div style="font-size: 11px;">Received By</div>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </table>
                </div>

                <div style="margin-top: 20px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #eef2f7; padding-top: 12px;">
                    <p>This is a system-generated issue note. Please verify all items before signing.</p>
                </div>
            </div>
        `;
    }

    function showPreviewModal() {
        const takenBy = document.getElementById('taken_by').value.trim();
        if (!takenBy) {
            alert('Please enter the name of the person receiving the items before previewing.');
            document.getElementById('taken_by').focus();
            return;
        }

        let hasQty = false;
        document.querySelectorAll('[id^="item_quantity_"]').forEach(input => {
            if (parseFloat(input.value) > 0) hasQty = true;
        });

        if (!hasQty) {
            alert('Please specify quantities for at least one item before previewing.');
            return;
        }

        document.getElementById('previewContent').innerHTML = generatePreviewHTML();
        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreviewModal() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    function printPreview() {
        const printContent = document.getElementById('previewContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html><head><title>Issue Preview</title>
            <style>body{font-family:Arial;padding:20px;} @media print{body{margin:0;padding:0;}}</style>
            </head><body>${printContent}
            <script>window.onload=function(){window.print();window.close();};<\/script>
            </body></html>
        `);
        printWindow.document.close();
    }

    document.getElementById('previewBtn').addEventListener('click', showPreviewModal);
</script>

<style>
    .batch-item { transition: all 0.2s; }
    .batch-item:hover { background-color: #fef3c7; border-color: #fcd34d; }
    .batch-qty:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2); }
    .hidden { display: none !important; }
</style>
@endsection
