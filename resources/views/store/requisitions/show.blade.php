@extends('layouts.store')

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
    .stock-info {
        font-size: 10px;
        padding: 2px 4px;
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
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header with Print Button --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center no-print">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Requisition #{{ $requisition->requisition_number }}</h3>
            <p class="text-sm text-gray-500">Created on {{ $requisition->created_at->format('F d, Y g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('store.requisitions.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <button onclick="printRequisition()" class="ml-4 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            @if($requisition->status == 'pending')
            <a href="{{ route('store.requisitions.edit', $requisition->id) }}" class="text-amber-600 hover:text-amber-800 flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <form method="POST" action="{{ route('store.requisitions.cancel', $requisition->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Cancel this requisition?')">
                    Cancel
                </button>
            </form>
            @endif
            @if($requisition->status == 'rejected')
            <a href="{{ route('store.requisitions.edit', $requisition->id) }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l3-3m0 0l3 3m-3-3v10m9-10l3-3m0 0l3 3m-3-3v10"/>
                </svg>
                Respond
            </a>
            @endif
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

        {{-- Approval Info - FIXED: Show for ALL approved requisitions, not just status == 'approved' --}}
        @php
            // CRITICAL FIX: Check if approval actually happened (approved_by AND approved_at exist)
            // This ensures signature shows even if status changed to 'fulfilled', 'ordered', 'lpo_created', etc.
            $wasApproved = $requisition->approved_by && $requisition->approved_at;
        @endphp

        @if($wasApproved && $requisition->status != 'rejected')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-green-800">Approval Confirmation</h4>
                    <p class="text-sm text-green-700 mt-1">This requisition has been approved by General Manager.</p>
                    @if($requisition->approvedBy)
                        <p class="text-xs text-green-600 mt-2">Approved by: {{ $requisition->approvedBy->first_name }} {{ $requisition->approvedBy->last_name }} on {{ $requisition->approved_at ? $requisition->approved_at->format('F d, Y g:i A') : '' }}</p>
                    @endif
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
                            @if($wasApproved && $requisition->status != 'rejected')
                                {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : 'Approved' }}
                            @elseif($requisition->status == 'rejected')
                                {{ $requisition->approvedBy ? $requisition->approvedBy->first_name . ' ' . $requisition->approvedBy->last_name : 'Rejected' }}
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

        {{-- Notes --}}
        @if($requisition->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $requisition->notes }}</p>
            </div>
        </div>
        @endif

        {{-- GM Notes (if any) --}}
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

        {{-- Items Table with Stock Info --}}
        <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">Requested Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Metrics</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Current Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Requested Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Approved Qty</th>
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
                                {{ $item->metrics ?: '—' }}
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
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                @if($wasApproved)
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
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800" colspan="3">Total Items: {{ $requisition->items->count() }}</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 text-right">{{ number_format($totalRequested, 2) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 text-right">{{ number_format($totalApproved, 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Signatures Section - FIXED: Show if approval happened, not based on status only --}}
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

                {{-- Approver Signature - FIXED: Check if approval actually happened --}}
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-2">Approved By (Management):</p>

                    @php
                        // CRITICAL FIX: Check if approval actually happened (approved_by AND approved_at exist)
                        // NOT based on status! Because status may change to 'fulfilled', 'ordered', 'lpo_created', etc.
                        $wasApproved = $requisition->approved_by && $requisition->approved_at;
                        $approver = $wasApproved ? $requisition->approvedBy : null;
                    @endphp

                    @if($wasApproved && $approver && $requisition->status != 'rejected')
                        @php
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
                    @elseif($requisition->status == 'rejected')
                        <div style="height: 50px;"></div>
                        <div class="border-t border-gray-300 mt-2 pt-1 w-48 mx-auto"></div>
                        <p class="text-xs text-red-600 mt-1">REJECTED</p>
                        @if($requisition->approvedBy)
                            <p class="text-xs text-gray-500">By: {{ $requisition->approvedBy->first_name }} {{ $requisition->approvedBy->last_name }}</p>
                        @endif
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
                    .stock-info { padding: 2px 4px; border-radius: 4px; display: inline-block; font-size: 10px; }
                    .stock-low { background: #fee2e2; color: #dc2626; }
                    .stock-ok { background: #dcfce7; color: #16a34a; }
                    .stock-warning { background: #fef3c7; color: #d97706; }
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
