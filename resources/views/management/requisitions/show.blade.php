@extends('layouts.management')

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
        /* Smaller logo on print */
        .print-logo {
            max-height: 40px !important;
            width: auto !important;
        }
    }
    .stock-info {
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }
    .stock-low {
        background: #fee2e2;
        color: #dc2626;
    }
    .stock-ok {
        background: #dcfce7;
        color: #16a34a;
    }
    .stock-warning {
        background: #fef3c7;
        color: #d97706;
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
    .signature-img {
        max-height: 50px;
        max-width: 150px;
    }
    /* Logo styling */
    .company-logo {
        max-height: 60px;
        width: auto;
    }
</style>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    {{-- Header with Print Button --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center no-print">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('management.requisitions.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
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
                <h1 class="text-xl font-bold text-green-600">REQUISITION FORM</h1>
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
                    'fulfilled' => 'bg-blue-100 text-blue-800',
                    'cancelled' => 'bg-gray-100 text-gray-800',
                ];
                $statusText = [
                    'pending' => 'Pending GM Approval',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'fulfilled' => 'Fulfilled',
                    'cancelled' => 'Cancelled',
                ];
            @endphp
            <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$requisition->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusText[$requisition->status] ?? ucfirst($requisition->status) }}
            </span>
        </div>

        {{-- Rejection Reason --}}
        @if($requisition->status == 'rejected')
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Rejection Reason</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $requisition->rejection_reason ?? 'No reason provided' }}</p>
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
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
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
                <h4 class="text-sm font-medium text-gray-500 mb-2">Response Information</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Responded By:</span>
                        <span class="text-sm text-gray-800">
                            @if($requisition->status == 'approved' || $requisition->status == 'rejected')
                                {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : 'Not yet responded' }}
                            @else
                                Not yet responded
                            @endif
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Responded At:</span>
                        <span class="text-sm text-gray-800">{{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Original Notes --}}
        @if($requisition->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Original Notes (from Store)</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $requisition->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table with Stock Info --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">Requested Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Metrics</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Current Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Requested Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Approved Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $totalRequested = 0;
                            $totalApproved = 0;
                        @endphp
                        @foreach($requisition->items as $item)
                        @php
                            $totalRequested += $item->quantity_requested;
                            $totalApproved += $item->quantity_approved;
                            $currentStock = $item->inventoryItem ? $item->inventoryItem->current_stock : 0;
                            $stockClass = $currentStock <= 0 ? 'stock-low' : ($currentStock < 10 ? 'stock-warning' : 'stock-ok');
                            $stockText = $currentStock <= 0 ? 'Out of Stock' : ($currentStock < 10 ? 'Low Stock' : 'In Stock');
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $item->inventoryItem ? $item->inventoryItem->name : 'Item not found' }}
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br>
                                    <span class="text-xs text-gray-500">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->category_name ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    {{ $item->metrics ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="stock-info {{ $stockClass }}">
                                    {{ number_format($currentStock, 2) }} {{ $item->inventoryItem->base_unit ?? 'pcs' }}
                                    <br>
                                    <span class="text-xs">{{ $stockText }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right font-semibold">
                                {{ number_format($item->quantity_requested, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                @if($requisition->status == 'approved')
                                    <span class="font-semibold text-green-600">{{ number_format($item->quantity_approved, 2) }}</span>
                                    @if($item->quantity_approved < $item->quantity_requested)
                                        <br>
                                        <span class="text-xs text-orange-500">(Partial)</span>
                                    @endif
                                @else
                                    {{ number_format($item->quantity_approved, 2) }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td class="px-4 py-3 text-sm font-bold text-gray-700" colspan="4">TOTALS</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ number_format($totalRequested, 2) }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-right">
                                @if($requisition->status == 'approved')
                                    <span class="text-green-600">{{ number_format($totalApproved, 2) }}</span>
                                    <br>
                                    <span class="text-xs {{ $totalApproved == $totalRequested ? 'text-green-500' : 'text-orange-500' }}">
                                        ({{ $totalApproved == $totalRequested ? 'Fully Approved' : number_format(($totalApproved / $totalRequested) * 100, 1) . '% Approved' }})
                                    </span>
                                @else
                                    {{ number_format($totalApproved, 2) }}
                                @endif
                            </td>
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
                <p class="text-sm text-yellow-600">Total Requested Quantity</p>
                <p class="text-2xl font-bold text-yellow-800">{{ number_format($totalRequested, 2) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600">Total Approved Quantity</p>
                <p class="text-2xl font-bold text-green-800">{{ number_format($totalApproved, 2) }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <p class="text-sm text-purple-600">Approval Rate</p>
                <p class="text-2xl font-bold text-purple-800">
                    {{ $totalRequested > 0 ? number_format(($totalApproved / $totalRequested) * 100, 1) : 0 }}%
                </p>
            </div>
        </div>

        {{-- Signatures Section --}}
        <div class="mt-8 pt-4 border-t flex justify-between">
            {{-- Requester Signature --}}
            <div class="text-center w-1/2">
                <p class="text-xs text-gray-500 mb-2">Requested By:</p>
                @php
                    $requester = $requisition->requestedBy;
                @endphp
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
                <div class="border-t border-gray-300 mt-2 pt-1"></div>
                <p class="text-xs text-gray-600 mt-1">{{ $requester->first_name ?? '' }} {{ $requester->last_name ?? '' }}</p>
                <p class="text-xs text-gray-400">{{ $requisition->created_at ? $requisition->created_at->format('d M Y') : '' }}</p>
            </div>

            {{-- Approver Signature --}}
            <div class="text-center w-1/2">
                <p class="text-xs text-gray-500 mb-2">Approved By (Management):</p>
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
                    @endif
                    <div class="border-t border-gray-300 mt-2 pt-1"></div>
                    <p class="text-xs text-gray-600 mt-1">{{ $approver->first_name ?? '' }} {{ $approver->last_name ?? '' }}</p>
                    <p class="text-xs text-gray-400">{{ $requisition->approved_at ? \Carbon\Carbon::parse($requisition->approved_at)->format('d M Y') : '' }}</p>
                @else
                    <div style="height: 50px;"></div>
                    <div class="border-t border-gray-300 mt-2 pt-1"></div>
                    <p class="text-xs text-gray-400 mt-1">Not Yet Approved</p>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t text-center">
            <p class="text-xs text-gray-400">This is a  system generated document. with digital signatures</p>
            <p class="text-xs text-gray-400">{{ $companyName }} - All Rights Reserved</p>
        </div>
    </div>

    {{-- Action Buttons (only for pending requisitions) --}}
    @if($requisition->status == 'pending')
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-4 no-print">
        <button type="button" onclick="openRejectModal()"
                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Reject Requisition
        </button>
        <a href="{{ route('management.requisitions.approve-form', $requisition->id) }}"
           class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Approve Requisition
        </a>
    </div>
    @endif
</div>

{{-- Rejection Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden no-print">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 rounded-t-xl">
            <h3 class="text-lg font-semibold text-white">Reject Requisition</h3>
        </div>
        <form action="{{ route('management.requisitions.reject', $requisition->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-semibold mb-2 text-gray-700">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="4" class="form-textarea w-full border-gray-300 rounded-lg"
                          placeholder="Please provide a reason for rejecting this requisition..." required></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
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
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .stock-info { padding: 2px 6px; border-radius: 4px; display: inline-block; font-size: 10px; }
                .stock-low { background: #fee2e2; color: #dc2626; }
                .stock-ok { background: #dcfce7; color: #16a34a; }
                .stock-warning { background: #fef3c7; color: #d97706; }
                .type-badge { padding: 2px 8px; border-radius: 999px; font-size: 10px; }
                .type-normal { background: #d1fae5; color: #065f46; }
                .type-emergency { background: #fee2e2; color: #991b1b; }
                .signature-img { max-height: 50px; max-width: 150px; }
                /* Smaller logo on print */
                .company-logo, .print-logo {
                    max-height: 40px !important;
                    width: auto !important;
                }
                @media print {
                    body { margin: 0; padding: 20px; }
                    .company-logo, .print-logo {
                        max-height: 40px !important;
                        width: auto !important;
                    }
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
