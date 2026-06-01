@extends('layouts.procurement')

@section('title', 'Requisition Details')
@section('page-title', 'Requisition Details')

@section('content')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #print-section, #print-section * {
            visibility: visible;
        }
        #print-section {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px;
        }
        .no-print {
            display: none !important;
        }
        button, .btn, .action-buttons {
            display: none !important;
        }
        .company-logo, .print-logo {
            max-height: 40px !important;
            width: auto !important;
        }
    }
    .company-logo {
        max-height: 60px;
        width: auto;
    }
    .signature-img {
        max-height: 50px;
        max-width: 150px;
    }
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
    }
    .type-normal {
        background: #d1fae5;
        color: #065f46;
    }
    .type-emergency {
        background: #fee2e2;
        color: #991b1b;
    }
    .batch-info {
        font-size: 10px;
        padding: 2px 4px;
        border-radius: 4px;
        display: inline-block;
    }
    .batch-low {
        background: #fee2e2;
        color: #dc2626;
    }
    .batch-ok {
        background: #dcfce7;
        color: #16a34a;
    }
    .batch-warning {
        background: #fef3c7;
        color: #d97706;
    }
    .batch-expired {
        background: #fee2e2;
        color: #dc2626;
    }
    .batch-expiring-soon {
        background: #fef3c7;
        color: #d97706;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header with Print Button --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center no-print">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('procurement.requisitions.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-1">
                <i class="fas fa-arrow-left text-xs"></i> Back to List
            </a>
            <button onclick="printRequisition()" class="ml-4 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Printable Section --}}
    <div id="print-section" class="p-6">

        {{-- Logo and Header --}}
        <div class="flex justify-between items-start mb-6 pb-4 border-b">
            <div>
                @php
                    $logo = \App\Models\BusinessSetting::getLogo();
                    $companyName = \App\Models\BusinessSetting::get('company_name', 'Company Name');
                @endphp
                @if($logo)
                    @php
                        $logoPath = public_path(parse_url($logo, PHP_URL_PATH));
                        $logoExists = file_exists($logoPath);
                        $logoMime = $logoExists ? mime_content_type($logoPath) : 'image/png';
                        $logoB64 = $logoExists ? base64_encode(file_get_contents($logoPath)) : null;
                    @endphp
                    @if($logoB64)
                        <img src="data:{{ $logoMime }};base64,{{ $logoB64 }}" class="company-logo print-logo" alt="Logo">
                    @else
                        <img src="{{ $logo }}" class="company-logo print-logo" alt="Logo">
                    @endif
                @else
                    <h2 class="text-xl font-bold text-gray-800">{{ $companyName }}</h2>
                @endif
            </div>
            <div class="text-right">
                <h1 class="text-xl font-bold text-blue-600">REQUISITION FORM</h1>
                <p class="text-sm text-gray-500">{{ $requisition->requisition_number }}</p>
            </div>
        </div>

        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'ordered' => 'bg-blue-100 text-blue-800',
                    'fulfilled' => 'bg-purple-100 text-purple-800',
                ];
                $statusText = [
                    'pending' => 'Pending GM Approval',
                    'approved' => 'GM Approved - Ready for LPO',
                    'rejected' => 'Rejected by GM',
                    'ordered' => 'LPO Created',
                    'fulfilled' => 'Fulfilled',
                ];
            @endphp
            <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$requisition->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusText[$requisition->status] ?? ucfirst($requisition->status) }}
            </span>
        </div>

        {{-- Rejection Reason --}}
        @if($requisition->status == 'rejected' && $requisition->rejection_reason)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle text-red-600 text-sm mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $requisition->rejection_reason }}</p>
                    @if($requisition->approvedBy)
                        <p class="text-xs text-red-600 mt-2">Rejected by: {{ $requisition->approvedBy->first_name }} {{ $requisition->approvedBy->last_name }} on {{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '' }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- GM Notes --}}
        @if($requisition->gm_notes)
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-2">
                <i class="fas fa-sticky-note text-yellow-600 text-sm mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-semibold text-yellow-800">GM Notes</h4>
                    <p class="text-sm text-yellow-700 mt-1">{{ $requisition->gm_notes }}</p>
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
                        <span class="w-32 text-sm text-gray-500">Requisition No:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $requisition->requisition_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Requisition Type:</span>
                        <span class="text-sm">
                            <span class="type-badge {{ $requisition->requisition_type == 'emergency' ? 'type-emergency' : 'type-normal' }}">
                                {{ $requisition->requisition_type == 'emergency' ? 'EMERGENCY' : 'Normal' }}
                            </span>
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Store:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->store ? $requisition->store->name : '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Date Needed:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->date_needed ? $requisition->date_needed->format('F d, Y') : 'Not specified' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Requested By:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->requestedBy ? $requisition->requestedBy->first_name . ' ' . $requisition->requestedBy->last_name : '—' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">GM Approval Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Approved By:</span>
                        <span class="text-sm text-gray-800">
                            {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : '—' }}
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Approved At:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($requisition->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Store Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $requisition->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table with Batch Stock and Total Stock --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">Approved Items (by GM)</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch No.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pack Info</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Unit Cost</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Batch Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Total Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Requested</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Approved</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $totalRequested = 0;
                            $totalApproved = 0;

                            function fmtQty($val) {
                                if ($val == floor($val)) {
                                    return number_format($val, 0);
                                }
                                return rtrim(rtrim(number_format($val, 2), '0'), '.');
                            }
                        @endphp
                        @foreach($requisition->items as $item)
                        @php
                            $totalRequested += $item->quantity_requested;
                            $totalApproved += $item->quantity_approved;

                            $batch = $item->batch;
                            $batchNumber = $batch ? $batch->batch_number : 'N/A';
                            $expiryDate = $batch ? $batch->expiry_date : null;
                            $expiryClass = '';
                            $daysLeft = 0;
                            if ($expiryDate) {
                                $daysLeft = now()->diffInDays($expiryDate, false);
                                if ($daysLeft <= 0) {
                                    $expiryClass = 'batch-expired';
                                } elseif ($daysLeft <= 30) {
                                    $expiryClass = 'batch-expiring-soon';
                                }
                            }

                            $packInfo = '';
                            if ($batch && $batch->pack_type && $batch->pack_type != 'Direct' && $batch->pack_size > 1) {
                                $packInfo = $batch->pack_type . ' (' . $batch->pack_size . ' ' . ($batch->unit_of_measurement ?? 'units') . '/pack)';
                            } else {
                                $packInfo = 'Direct';
                            }

                            // Batch Stock (specific batch)
                            $batchStock = $batch ? $batch->remaining_quantity : 0;
                            $batchStockClass = $batchStock <= 0 ? 'batch-low' : ($batchStock < 10 ? 'batch-warning' : 'batch-ok');
                            $batchStockText = $batchStock <= 0 ? 'Out of Stock' : ($batchStock < 10 ? 'Low Stock' : 'In Stock');

                            // Total Stock (all batches combined)
                            $totalStock = 0;
                            if ($item->inventory_item_id) {
                                $totalStock = \App\Models\Batch::where('inventory_item_id', $item->inventory_item_id)
                                    ->where('batch_status', 'active')
                                    ->where('remaining_quantity', '>', 0)
                                    ->sum('remaining_quantity');
                            }
                            $totalStockClass = $totalStock <= 0 ? 'batch-low' : ($totalStock < 10 ? 'batch-warning' : 'batch-ok');
                            $totalStockText = $totalStock <= 0 ? 'Out of Stock' : ($totalStock < 10 ? 'Low Stock' : 'In Stock');

                            $unitCost = $item->unit_cost ?? ($batch ? $batch->unit_cost : 0);
                            $itemName = $item->item_name ?: ($item->inventoryItem ? $item->inventoryItem->name : 'Unknown');
                            $itemCode = $item->inventoryItem ? $item->inventoryItem->item_code : null;
                            $categoryName = $item->category_name ?: ($item->inventoryItem && $item->inventoryItem->category ? $item->inventoryItem->category->name : '—');
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $itemName }}
                                @if($itemCode)
                                    <br>
                                    <span class="text-xs text-gray-500">Code: {{ $itemCode }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $categoryName }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-600">
                                {{ $batchNumber }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($expiryDate)
                                    <span class="batch-info {{ $expiryClass }}">
                                        {{ $expiryDate->format('d M Y') }}
                                        @if($daysLeft <= 0)
                                            (EXPIRED)
                                        @elseif($daysLeft <= 30)
                                            ({{ $daysLeft }} days left)
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $packInfo }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->metrics ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold">
                                UGX {{ number_format($unitCost, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <div class="font-semibold {{ $batchStockClass }}">
                                    {{ fmtQty($batchStock) }}
                                </div>
                                <div class="text-xs {{ $batchStockClass }}">
                                    {{ $batchStockText }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <div class="font-semibold {{ $totalStockClass }}">
                                    {{ fmtQty($totalStock) }}
                                </div>
                                <div class="text-xs {{ $totalStockClass }}">
                                    {{ $totalStockText }}
                                </div>
                             </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                {{ fmtQty($item->quantity_requested) }}
                             </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                                {{ fmtQty($item->quantity_approved) }}
                                @if($item->quantity_approved < $item->quantity_requested)
                                    <span class="text-xs text-orange-500 block">(Partial)</span>
                                @endif
                             </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $item->notes ?? '—' }}
                             </td>
                         </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td class="px-4 py-3 text-sm font-bold text-gray-700" colspan="9">TOTALS</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ fmtQty($totalRequested) }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-green-600 text-right">{{ fmtQty($totalApproved) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-sm text-blue-600">Total Items</p>
                <p class="text-2xl font-bold text-blue-800">{{ $requisition->items->count() }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                <p class="text-sm text-yellow-600">Total Requested</p>
                <p class="text-2xl font-bold text-yellow-800">{{ fmtQty($totalRequested) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600">GM Approved</p>
                <p class="text-2xl font-bold text-green-800">{{ fmtQty($totalApproved) }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <p class="text-sm text-purple-600">Total Value</p>
                <p class="text-2xl font-bold text-purple-800">
                    UGX {{ number_format($requisition->items->sum(function($item) {
                        return $item->quantity_approved * ($item->unit_cost ?? 0);
                    }), 2) }}
                </p>
            </div>
        </div>

        {{-- Signatures Section --}}
        <div class="mt-8 pt-4 border-t">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Requester Signature --}}
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-2">Requested By:</p>
                    @php $requester = $requisition->requestedBy; @endphp
                    @if($requester && $requester->signature_url)
                        @php
                            $sigUrl = $requester->signature_url;
                            $sigPath = public_path(parse_url($sigUrl, PHP_URL_PATH));
                            $sigExists = file_exists($sigPath);
                            $sigMime = $sigExists ? mime_content_type($sigPath) : 'image/png';
                            $sigB64 = $sigExists ? base64_encode(file_get_contents($sigPath)) : null;
                        @endphp
                        @if($sigB64)
                            <img src="data:{{ $sigMime }};base64,{{ $sigB64 }}" class="signature-img mx-auto" alt="Signature">
                        @else
                            <img src="{{ $sigUrl }}" class="signature-img mx-auto" alt="Signature">
                        @endif
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                    <p class="text-xs text-gray-600 mt-1">{{ $requester->first_name ?? '' }} {{ $requester->last_name ?? '' }}</p>
                    <p class="text-xs text-gray-400">{{ $requisition->created_at ? $requisition->created_at->format('d M Y') : '' }}</p>
                </div>

                {{-- Approver Signature (GM) --}}
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-2">Approved By (General Manager):</p>
                    @if($requisition->status == 'approved' && $requisition->approvedBy)
                        @php
                            $approver = $requisition->approvedBy;
                            $approverSigUrl = $approver->signature_url;
                            $approverSigPath = public_path(parse_url($approverSigUrl, PHP_URL_PATH));
                            $approverSigExists = file_exists($approverSigPath);
                            $approverSigMime = $approverSigExists ? mime_content_type($approverSigPath) : 'image/png';
                            $approverSigB64 = $approverSigExists ? base64_encode(file_get_contents($approverSigPath)) : null;
                        @endphp
                        @if($approverSigB64)
                            <img src="data:{{ $approverSigMime }};base64,{{ $approverSigB64 }}" class="signature-img mx-auto" alt="Signature">
                        @elseif($approverSigUrl)
                            <img src="{{ $approverSigUrl }}" class="signature-img mx-auto" alt="Signature">
                        @else
                            <div style="height: 50px;"></div>
                            <p class="text-xs text-gray-400 mt-2">No signature on file</p>
                        @endif
                        <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                        <p class="text-xs text-gray-600 mt-1">{{ $approver->first_name ?? '' }} {{ $approver->last_name ?? '' }}</p>
                        <p class="text-xs text-gray-400">{{ $requisition->approved_at ? \Carbon\Carbon::parse($requisition->approved_at)->format('d M Y') : '' }}</p>
                    @else
                        <div style="height: 50px;"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                        <p class="text-xs text-gray-400 mt-1">Not Yet Approved</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t text-center">
            <p class="text-xs text-gray-400">This is a computer generated document. Valid without signature.</p>
            <p class="text-xs text-gray-400">{{ $companyName }} - All Rights Reserved</p>
        </div>

        {{-- Action Buttons --}}
        @if($requisition->status == 'approved')
        <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end no-print">
            <a href="{{ route('procurement.lpo.create', $requisition->id) }}"
               class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-file-invoice mr-1"></i>
                Create Local Purchase Order (LPO)
            </a>
        </div>
        @endif

        @if($requisition->status == 'ordered' && $requisition->lpo_id)
        <div class="mt-6 pt-4 border-t border-gray-200 no-print">
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600">LPO Created</p>
                        <p class="text-lg font-semibold text-purple-800">LPO has been created for this requisition</p>
                    </div>
                    <a href="{{ route('procurement.lpo.show', $requisition->lpo_id) }}"
                       class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-eye mr-1"></i> View LPO
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function printRequisition() {
        const printContents = document.getElementById('print-section').innerHTML;
        const originalTitle = document.title;
        document.title = 'Requisition {{ $requisition->requisition_number }}';

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Requisition {{ $requisition->requisition_number }}</title>
                <style>
                    body { padding: 20px; font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .company-logo, .print-logo { max-height: 40px !important; width: auto !important; }
                    .signature-img { max-height: 50px; max-width: 150px; }
                    .batch-info { padding: 2px 4px; border-radius: 4px; display: inline-block; font-size: 10px; }
                    .batch-low { background: #fee2e2; color: #dc2626; }
                    .batch-ok { background: #dcfce7; color: #16a34a; }
                    .batch-warning { background: #fef3c7; color: #d97706; }
                    .batch-expired { background: #fee2e2; color: #dc2626; }
                    .batch-expiring-soon { background: #fef3c7; color: #d97706; }
                    .type-badge { padding: 2px 8px; border-radius: 999px; font-size: 10px; }
                    .type-normal { background: #d1fae5; color: #065f46; }
                    .type-emergency { background: #fee2e2; color: #991b1b; }
                    @media print {
                        body { margin: 0; padding: 20px; }
                    }
                </style>
            </head>
            <body>${printContents}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
        document.title = originalTitle;
    }
</script>
@endsection
