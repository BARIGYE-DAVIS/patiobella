@extends('layouts.procurement')
@section('title', 'Purchase Order Details')
@section('page-title', 'Purchase Order Details')

@section('content')
<style>
    .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-sent { background: #dbeafe; color: #1e40af; }
    .status-draft { background: #fef3c7; color: #92400e; }
    .status-partially_received { background: #fed7aa; color: #9a3412; }
    .status-fully_received { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }

    .info-label {
        font-weight: 600;
        color: #64748b;
        width: 130px;
        display: inline-block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .document-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f3e8ff;
        color: #7e22ce;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 500;
        cursor: pointer;
    }
    .document-badge:hover {
        background: #e9d5ff;
    }
    .attach-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #e0e7ff;
        color: #4338ca;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 500;
        cursor: pointer;
        border: none;
    }
    .attach-btn:hover {
        background: #c7d2fe;
    }
    .btn-orange {
        background: #ea580c;
        color: white;
        transition: all 0.2s;
    }
    .btn-orange:hover {
        background: #c2410c;
    }
    .btn-outline-orange {
        border: 1px solid #ea580c;
        color: #ea580c;
        background: transparent;
        transition: all 0.2s;
    }
    .btn-outline-orange:hover {
        background: #ea580c;
        color: white;
    }

    /* Custom Document Preview Modal */
    #docPreviewModal {
        font-family: inherit;
    }
    .preview-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        background: #1e293b;
        border-bottom: 1px solid #334155;
        user-select: none;
    }
    .preview-toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    .preview-toolbar-right {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    .preview-doc-icon {
        width: 28px;
        height: 28px;
        background: #ea580c;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .preview-doc-title {
        font-size: 13px;
        font-weight: 600;
        color: #f1f5f9;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 400px;
    }
    .preview-doc-meta {
        font-size: 10px;
        color: #94a3b8;
        margin-top: 1px;
    }
    .preview-close-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background: #334155;
        border: none;
        border-radius: 6px;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.15s;
    }
    .preview-close-btn:hover {
        background: #ef4444;
        color: white;
    }
    .preview-body {
        background: #0f172a;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: auto;
        max-height: calc(90vh - 60px);
        position: relative;
    }
    .preview-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        color: #64748b;
    }
    .preview-loading svg {
        width: 32px;
        height: 32px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .preview-loading p {
        font-size: 12px;
    }
    /* PDF rendered inside custom container — no browser chrome */
    .preview-body iframe {
        width: 100%;
        height: 70vh;
        border: none;
        display: block;
        /* pointer-events trick to block right-click context menu on iframe */
    }
    .preview-body img {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
        display: block;
        border-radius: 4px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.5);
        margin: 16px;
    }
    .preview-unsupported {
        text-align: center;
        color: #64748b;
        padding: 40px;
    }
    .preview-unsupported svg {
        width: 40px;
        height: 40px;
        margin: 0 auto 12px;
        opacity: 0.4;
    }
    .preview-unsupported p {
        font-size: 12px;
    }
    /* Overlay on top of iframe to block right-click / selection */
    .iframe-overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: transparent;
        pointer-events: none;
    }
</style>

@if(session('success'))
    <div class="mb-3 bg-green-50 border-l-4 border-green-500 text-green-700 p-2 rounded text-xs">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-3 bg-red-50 border-l-4 border-red-500 text-red-700 p-2 rounded text-xs">
        {{ session('error') }}
    </div>
@endif

<div class="space-y-4">

    {{-- Header with Actions --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-700 to-orange-600 px-4 py-3 flex justify-between items-center">
            <div>
                <h2 class="text-sm font-bold text-white">PO #{{ $purchaseOrder->po_number }}</h2>
                <p class="text-orange-100 text-[11px] mt-0.5">Created: {{ $purchaseOrder->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                @if($purchaseOrder->status == 'draft')
                    <a href="{{ route('procurement.purchase-orders.edit', $purchaseOrder->id) }}"
                       class="px-2 py-1 bg-yellow-500 text-white rounded text-[11px] font-semibold hover:bg-yellow-600 transition">
                        Edit
                    </a>
                    <form action="{{ route('procurement.purchase-orders.send', $purchaseOrder->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded text-[11px] font-semibold hover:bg-blue-700 transition">
                            Send
                        </button>
                    </form>
                @endif
                @if(in_array($purchaseOrder->status, ['draft', 'sent']))
                    <form action="{{ route('procurement.purchase-orders.destroy', $purchaseOrder->id) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete this PO? Cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded text-[11px] font-semibold hover:bg-red-700 transition">
                            Delete
                        </button>
                    </form>
                @endif
                <a href="{{ route('procurement.purchase-orders.index') }}"
                   class="px-2 py-1 bg-gray-600 text-white rounded text-[11px] font-semibold hover:bg-gray-700 transition">
                    Back
                </a>
            </div>
        </div>
    </div>

    {{-- Status Banner --}}
    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-semibold text-gray-500">Status:</span>
                <span class="status-badge status-{{ str_replace('_', '-', $purchaseOrder->status) }}">
                    {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}
                </span>
            </div>
            <span class="text-[10px] text-gray-400">Updated: {{ $purchaseOrder->updated_at->diffForHumans() }}</span>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Vendor Information --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-2">
            <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-3 py-2">
                <h3 class="text-xs font-semibold text-white flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Vendor
                </h3>
            </div>
            <div class="p-3 space-y-2">
                <div class="flex">
                    <span class="info-label">Name:</span>
                    <span class="text-xs text-gray-700">{{ $purchaseOrder->vendor->name ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="info-label">Contact:</span>
                    <span class="text-xs text-gray-700">{{ $purchaseOrder->vendor->contact_person ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="info-label">Phone:</span>
                    <span class="text-xs text-gray-700">{{ $purchaseOrder->vendor->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="info-label">Email:</span>
                    <span class="text-xs text-blue-600">{{ $purchaseOrder->vendor->email ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="info-label">Address:</span>
                    <span class="text-xs text-gray-700">{{ $purchaseOrder->vendor->address ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-3 py-2">
                <h3 class="text-xs font-semibold text-white flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Summary
                </h3>
            </div>
            <div class="p-3 space-y-2">
                <div class="flex justify-between">
                    <span class="text-[11px] text-gray-500">Subtotal:</span>
                    <span class="text-[11px] font-mono">UGX {{ number_format($purchaseOrder->subtotal, 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[11px] text-gray-500">Tax:</span>
                    <span class="text-[11px] font-mono">UGX {{ number_format($purchaseOrder->tax_amount, 0) }}</span>
                </div>
                <div class="border-t pt-2 flex justify-between">
                    <span class="text-xs font-bold text-gray-700">Total:</span>
                    <span class="text-sm font-bold text-orange-600">UGX {{ number_format($purchaseOrder->total_amount, 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Delivery Details --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-3 py-2">
            <h3 class="text-xs font-semibold text-white flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Delivery
            </h3>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <span class="text-[10px] font-semibold text-gray-500 block">Expected Delivery</span>
                    <span class="text-xs text-gray-700">{{ $purchaseOrder->expected_delivery_date ? date('d M Y', strtotime($purchaseOrder->expected_delivery_date)) : '—' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-gray-500 block">Address</span>
                    <span class="text-xs text-gray-700">{{ $purchaseOrder->delivery_address ?: '—' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-gray-500 block">Terms</span>
                    <span class="text-xs text-gray-700">{{ $purchaseOrder->delivery_terms ?: '—' }}</span>
                </div>
            </div>
            @if($purchaseOrder->notes)
                <div class="mt-2 pt-2 border-t">
                    <span class="text-[10px] font-semibold text-gray-500 block">Notes</span>
                    <span class="text-xs text-gray-700">{{ $purchaseOrder->notes }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-3 py-2">
            <h3 class="text-xs font-semibold text-white flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Items
            </h3>
        </div>
        <div class="p-3 overflow-x-auto">
            <table class="w-full border border-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2 border text-left">Item</th>
                        <th class="p-2 border text-center w-20">Qty</th>
                        <th class="p-2 border text-right w-28">Unit Cost</th>
                        <th class="p-2 border text-right w-28">Total</th>
                        <th class="p-2 border text-left">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrder->items as $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2 border">
                                <span class="font-semibold">{{ $item->inventoryItem->name ?? 'N/A' }}</span>
                                @if($item->inventoryItem && $item->inventoryItem->item_code)
                                    <br><span class="text-[10px] text-gray-400">Code: {{ $item->inventoryItem->item_code }}</span>
                                @endif
                            </td>
                            <td class="p-2 border text-center">{{ number_format($item->quantity_ordered, 2) }}</td>
                            <td class="p-2 border text-right">UGX {{ number_format($item->unit_cost, 0) }}</td>
                            <td class="p-2 border text-right font-semibold">UGX {{ number_format($item->total_cost, 0) }}</td>
                            <td class="p-2 border text-gray-500">{{ $item->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-400">No items found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="p-2 border text-right font-bold">Grand Total:</td>
                        <td class="p-2 border text-right font-bold text-orange-600">UGX {{ number_format($purchaseOrder->total_amount, 0) }}</td>
                        <td class="p-2 border"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Attached Documents Section --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-3 py-2 flex justify-between items-center">
            <div class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xs font-semibold text-white">Documents</h3>
            </div>
            @if(Auth::user()->is_super_admin || Auth::user()->can('upload_documents'))
            <button type="button" onclick="openDocUploadModal({{ $purchaseOrder->id }})"
                    class="attach-btn text-[10px] py-0.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Attach
            </button>
            @endif
        </div>
        <div class="p-3">
            @php
             $docs = \App\Models\Document::where('purchase_order_id', $purchaseOrder->id)->get();
            @endphp
            @if($docs->count() > 0)
                <div class="space-y-2">
                    @foreach($docs as $doc)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded bg-purple-100 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-800">{{ $doc->original_name }}</p>
                                    <p class="text-[10px] text-gray-400">
                                        {{ number_format($doc->file_size / 1024, 2) }} KB
                                        @if($doc->document_type) · {{ $doc->document_type }} @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <button type="button" onclick="viewDocument({{ $doc->id }}, '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                        class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="View">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('procurement.purchase-orders.download-document', $doc->id) }}"
                                   class="p-1 text-green-600 hover:bg-green-50 rounded" title="Download">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                @if(Auth::user()->is_super_admin || Auth::user()->can('delete_documents'))
                                <form action="{{ route('procurement.purchase-orders.delete-document', $doc->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded" title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-400 text-[11px] py-3">No documents attached</p>
            @endif
        </div>
    </div>
</div>

{{-- Document Upload Modal --}}
<div id="docUploadModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-md p-4 shadow-xl">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-bold text-gray-800">Attach Document to PO</h3>
            <button type="button" onclick="closeDocUploadModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="docUploadForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="po_id" id="uploadPoId">

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Select Document</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-orange-500 transition cursor-pointer" id="uploadDropzone">
                    <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m-4-4l-4 4m6-20h2m-6 0h.01M12 40h24" stroke-width="2"/>
                    </svg>
                    <p class="text-xs text-gray-500 mt-1">Click or drag PDF/JPG/PNG (max 5MB)</p>
                    <input type="file" name="document" id="docFile" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div id="filePreviewUpload" class="hidden mt-2 p-2 bg-gray-50 rounded flex items-center justify-between">
                    <span id="uploadFileName" class="text-xs text-gray-700"></span>
                    <button type="button" onclick="clearSelectedFile()" class="text-red-500 text-xs">Remove</button>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <input type="text" name="description" class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md" placeholder="e.g., Signed PO">
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="closeDocUploadModal()" class="flex-1 px-2 py-1 bg-gray-200 text-gray-700 rounded-md text-xs">Cancel</button>
                <button type="submit" class="flex-1 px-2 py-1 bg-orange-600 text-white rounded-md text-xs">Upload</button>
            </div>
        </form>
    </div>
</div>

{{-- Custom Document Preview Modal --}}
<div id="docPreviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden items-center justify-center">
    <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col" style="max-height: 90vh;">

        {{-- Custom Toolbar (no browser chrome inside) --}}
        <div class="preview-toolbar">
            <div class="preview-toolbar-left">
                <div class="preview-doc-icon">
                    <svg id="previewDocTypeIcon" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div style="min-width:0">
                    <div id="previewDocTitle" class="preview-doc-title">Document Preview</div>
                    <div id="previewDocMeta" class="preview-doc-meta">Loading...</div>
                </div>
            </div>
            <div class="preview-toolbar-right">
                <button type="button" onclick="closeDocPreviewModal()" class="preview-close-btn" title="Close">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Preview Body --}}
        <div id="previewDocContent" class="preview-body">
            <div class="preview-loading">
                <svg fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p>Loading preview...</p>
            </div>
        </div>

    </div>
</div>

<script>
function openDocUploadModal(poId) {
    document.getElementById('uploadPoId').value = poId;
    document.getElementById('docUploadModal').classList.remove('hidden');
    document.getElementById('docUploadModal').style.display = 'flex';
}

function closeDocUploadModal() {
    document.getElementById('docUploadModal').classList.add('hidden');
    document.getElementById('docUploadModal').style.display = 'none';
    resetUploadForm();
}

function resetUploadForm() {
    selectedDocumentFile = null;
    document.getElementById('docFile').value = '';
    document.getElementById('filePreviewUpload').classList.add('hidden');
}

let selectedDocumentFile = null;
const dropzone = document.getElementById('uploadDropzone');
const fileInput = document.getElementById('docFile');

if (dropzone) {
    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-orange-500', 'bg-orange-50');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-orange-500', 'bg-orange-50');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-orange-500', 'bg-orange-50');
        const file = e.dataTransfer.files[0];
        if (file) handleFileSelect(file);
    });
}

fileInput.addEventListener('change', function() {
    if (this.files[0]) handleFileSelect(this.files[0]);
});

function handleFileSelect(file) {
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        return;
    }
    const allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
    if (!allowed.includes(file.type)) {
        alert('Only PDF, JPG, PNG files allowed');
        return;
    }
    selectedDocumentFile = file;
    document.getElementById('uploadFileName').innerText = file.name;
    document.getElementById('filePreviewUpload').classList.remove('hidden');
}

function clearSelectedFile() {
    selectedDocumentFile = null;
    fileInput.value = '';
    document.getElementById('filePreviewUpload').classList.add('hidden');
}

document.getElementById('docUploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!selectedDocumentFile) {
        alert('Please select a file to upload');
        return;
    }

    const formData = new FormData();
    formData.append('document', selectedDocumentFile);
    formData.append('po_id', document.getElementById('uploadPoId').value);
    formData.append('description', document.querySelector('#docUploadForm input[name="description"]').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    try {
        const response = await fetch('{{ route("procurement.purchase-orders.attach-document") }}', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            alert('Document attached successfully!');
            closeDocUploadModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (err) {
        alert('Upload failed: ' + err.message);
    }
});

function viewDocument(docId, filename, mimeType) {
    const modal = document.getElementById('docPreviewModal');
    const previewContent = document.getElementById('previewDocContent');
    const previewTitle = document.getElementById('previewDocTitle');
    const previewMeta = document.getElementById('previewDocMeta');

    // Set toolbar info
    previewTitle.innerText = filename;
    previewMeta.innerText = mimeType === 'application/pdf' ? 'PDF Document' : 'Image';

    // Show modal
    modal.classList.remove('hidden');
    modal.style.display = 'flex';

    // Show loading state
    previewContent.innerHTML = `
        <div class="preview-loading">
            <svg fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p>Loading preview...</p>
        </div>`;

    const previewUrl = `/procurement/purchase-orders/preview-document/${docId}`;

    if (mimeType === 'application/pdf') {
        // Use iframe with toolbar=0 to suppress browser PDF controls (download/print buttons)
        const iframe = document.createElement('iframe');
        iframe.src = previewUrl + '#toolbar=0&navpanes=0&scrollbar=0&view=FitH';
        iframe.style.cssText = 'width:100%;height:70vh;border:none;display:block;';
        // Disable right-click inside iframe to prevent "Save as"
        iframe.onload = function() {
            try {
                iframe.contentDocument.addEventListener('contextmenu', e => e.preventDefault());
            } catch(e) {
                // Cross-origin; silently ignore
            }
        };
        previewContent.innerHTML = '';
        previewContent.style.padding = '0';
        previewContent.appendChild(iframe);
    } else if (mimeType && mimeType.startsWith('image/')) {
        const img = new Image();
        img.onload = () => {
            previewContent.innerHTML = '';
            previewContent.style.padding = '16px';
            img.style.cssText = 'max-width:100%;max-height:70vh;object-fit:contain;border-radius:6px;box-shadow:0 4px 32px rgba(0,0,0,0.5);display:block;';
            // Disable right-click on image to prevent "Save image as"
            img.addEventListener('contextmenu', e => e.preventDefault());
            img.draggable = false;
            previewContent.appendChild(img);
        };
        img.onerror = () => {
            previewContent.innerHTML = `
                <div class="preview-unsupported">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p>Failed to load image.</p>
                </div>`;
        };
        img.src = previewUrl;
    } else {
        previewContent.innerHTML = `
            <div class="preview-unsupported">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>Preview not available for this file type.</p>
            </div>`;
    }
}

function closeDocPreviewModal() {
    const modal = document.getElementById('docPreviewModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    // Clear content so iframe stops loading
    document.getElementById('previewDocContent').innerHTML = '';
}

// Close modal on backdrop click
document.getElementById('docPreviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeDocPreviewModal();
});
</script>
@endsection
