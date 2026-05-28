@extends('layouts.procurement')
@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')

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
    .status-draft { background: #fef3c7; color: #92400e; }
    .status-sent { background: #dbeafe; color: #1e40af; }
    .status-partially_received { background: #fed7aa; color: #9a3412; }
    .status-fully_received { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
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
    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    .loading-spinner {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    /* GRN-style preview modal */
    .preview-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #1e40af 100%);
        position: relative;
        overflow: hidden;
    }
    .preview-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 80% 20%, rgba(59,130,246,0.18) 0%, transparent 60%);
    }
    .preview-kpi {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        position: relative;
        overflow: hidden;
    }
    .preview-kpi::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
    }
    .preview-kpi.blue::after { background: #3b82f6; }
    .preview-kpi.green::after { background: #10b981; }
    .preview-kpi.amber::after { background: #f59e0b; }
    .preview-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    .preview-table th {
        background: #f8fafc;
        padding: 8px 10px;
        text-align: left;
        font-weight: 700;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
    }
    .preview-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #f1f5f9;
    }
</style>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-600"></div>
        <span class="text-xs text-gray-700">Processing...</span>
    </div>
</div>

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

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-4">
        {{-- Filters Row --}}
        <div class="flex flex-wrap gap-3 mb-4 pb-3 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 font-medium">Status:</span>
                <div class="flex gap-1">
                    <button type="button" data-status="" class="status-filter px-2 py-1 text-xs rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition">All</button>
                    <button type="button" data-status="draft" class="status-filter px-2 py-1 text-xs rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Draft</button>
                    <button type="button" data-status="sent" class="status-filter px-2 py-1 text-xs rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Sent</button>
                    <button type="button" data-status="partially_received" class="status-filter px-2 py-1 text-xs rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Partial</button>
                    <button type="button" data-status="fully_received" class="status-filter px-2 py-1 text-xs rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Full</button>
                    <button type="button" data-status="cancelled" class="status-filter px-2 py-1 text-xs rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Cancelled</button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 font-medium">Vendor:</span>
                <select id="vendorFilter" class="px-2 py-1 text-xs border border-gray-300 rounded-md">
                    <option value="">All Vendors</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1"></div>

            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search PO number..."
                       class="px-3 py-1.5 text-xs border border-gray-300 rounded-md w-64">
            </div>

            <a href="{{ route('procurement.purchase-orders.create') }}"
               class="px-3 py-1.5 bg-orange-600 text-white rounded-md text-xs font-semibold hover:bg-orange-700 transition">
                Create PO
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b text-left text-[10px] font-bold uppercase text-gray-500">PO Number</th>
                        <th class="px-3 py-2 border-b text-left text-[10px] font-bold uppercase text-gray-500">Vendor</th>
                        <th class="px-3 py-2 border-b text-left text-[10px] font-bold uppercase text-gray-500">PO Date</th>
                        <th class="px-3 py-2 border-b text-left text-[10px] font-bold uppercase text-gray-500">Expected Delivery</th>
                        <th class="px-3 py-2 border-b text-center text-[10px] font-bold uppercase text-gray-500">Status</th>
                        <th class="px-3 py-2 border-b text-right text-[10px] font-bold uppercase text-gray-500">Total Amount</th>
                        <th class="px-3 py-2 border-b text-center text-[10px] font-bold uppercase text-gray-500">Documents</th>
                        <th class="px-3 py-2 border-b text-center text-[10px] font-bold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody id="poTableBody">
                    @include('procurement.purchase_orders._table_rows', ['purchaseOrders' => $purchaseOrders])
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4" id="paginationLinks">
            {{ $purchaseOrders->links() }}
        </div>
    </div>
</div>

{{-- Document Upload Modal --}}
<div id="docUploadModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl w-full max-w-md p-5 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-gray-800">Attach Document to PO</h3>
            <button type="button" onclick="closeDocUploadModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="docUploadForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="po_id" id="uploadPoId">

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Select Document</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-500 transition cursor-pointer" id="uploadDropzone">
                    <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m-4-4l-4 4m6-20h2m-6 0h.01M12 40h24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text-xs text-gray-500 mt-1">Click or drag PDF/JPG/PNG (max 5MB)</p>
                    <input type="file" name="document" id="docFile" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div id="filePreviewUpload" class="hidden mt-2 p-2 bg-gray-50 rounded flex items-center justify-between">
                    <span id="uploadFileName" class="text-xs text-gray-700"></span>
                    <button type="button" onclick="clearSelectedFile()" class="text-red-500 text-xs">Remove</button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description (Optional)</label>
                <input type="text" name="description" class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-md" placeholder="e.g., Signed PO, Quotation">
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="closeDocUploadModal()" class="flex-1 px-3 py-1.5 bg-gray-200 text-gray-700 rounded-md text-xs font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs font-medium">Upload</button>
            </div>
        </form>
    </div>
</div>

{{-- Document Preview Modal (GRN Style) --}}
<div id="docPreviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden items-center justify-center overflow-y-auto py-8">
    <div class="bg-white rounded-xl w-full max-w-5xl max-h-[90vh] overflow-hidden shadow-2xl">
        {{-- Modal Header --}}
        <div class="preview-header px-6 py-4">
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-blue-300 text-[10px] font-semibold tracking-widest uppercase">Document Preview</span>
                        </div>
                        <h2 class="text-xl font-bold text-white" id="previewDocTitle">Document</h2>
                    </div>
                    <button type="button" onclick="closeDocPreviewModal()" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Content --}}
        <div id="previewDocContent" class="p-6 overflow-auto max-h-[calc(90vh-140px)] bg-gray-50">
            <div class="flex items-center justify-center min-h-[300px]">
                <div class="text-center text-gray-500">
                    <svg class="animate-spin w-8 h-8 mx-auto mb-3 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm">Loading document preview...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let currentStatus = '';
let currentVendor = '';
let currentSearch = '';

function loadPurchaseOrders(page = 1) {
    currentPage = page;

    let url = '{{ route("procurement.purchase-orders.index") }}?page=' + page;
    if (currentStatus) url += '&status=' + encodeURIComponent(currentStatus);
    if (currentVendor) url += '&vendor_id=' + encodeURIComponent(currentVendor);
    if (currentSearch) url += '&search=' + encodeURIComponent(currentSearch);

    document.getElementById('loadingOverlay').style.display = 'flex';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('poTableBody').innerHTML = html;
    })
    .catch(err => console.error('Error:', err))
    .finally(() => {
        document.getElementById('loadingOverlay').style.display = 'none';
    });
}

function attachPaginationEvents() {
    document.querySelectorAll('#paginationLinks a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(this.href);
            const page = url.searchParams.get('page');
            if (page) loadPurchaseOrders(page);
        });
    });
}

// Filter handlers
document.querySelectorAll('.status-filter').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.status-filter').forEach(b => b.classList.remove('bg-orange-600', 'text-white'));
        this.classList.add('bg-orange-600', 'text-white');
        currentStatus = this.getAttribute('data-status');
        loadPurchaseOrders(1);
    });
});

document.getElementById('vendorFilter').addEventListener('change', function() {
    currentVendor = this.value;
    loadPurchaseOrders(1);
});

let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentSearch = this.value;
        loadPurchaseOrders(1);
    }, 500);
});

// Document functions
let selectedDocumentFile = null;

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
    document.querySelector('#docUploadForm input[name="description"]').value = '';
}

const dropzone = document.getElementById('uploadDropzone');
const fileInput = document.getElementById('docFile');

if (dropzone) {
    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-indigo-500', 'bg-indigo-50');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');
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

    document.getElementById('loadingOverlay').style.display = 'flex';

    try {
        const response = await fetch('{{ route("procurement.purchase-orders.attach-document") }}', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            alert('Document attached successfully!');
            closeDocUploadModal();
            loadPurchaseOrders(currentPage);
        } else {
            alert('Error: ' + data.message);
        }
    } catch (err) {
        alert('Upload failed: ' + err.message);
    } finally {
        document.getElementById('loadingOverlay').style.display = 'none';
    }
});

// GRN-Style Document Preview Function
function viewDocument(docId, filename, mimeType) {
    const modal = document.getElementById('docPreviewModal');
    const previewContent = document.getElementById('previewDocContent');
    const previewTitle = document.getElementById('previewDocTitle');

    previewTitle.innerText = filename;
    modal.classList.remove('hidden');
    modal.style.display = 'flex';

    previewContent.innerHTML = '<div class="flex items-center justify-center min-h-[300px]"><div class="text-center text-gray-500"><svg class="animate-spin w-8 h-8 mx-auto mb-3 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><p class="text-sm">Loading document preview...</p></div></div>';

    const previewUrl = `/procurement/purchase-orders/preview-document/${docId}`;

    if (mimeType === 'application/pdf') {
        // Use PDF.js to render as image (prevents direct download)
        const container = document.createElement('div');
        container.style.width = '100%';
        container.style.display = 'flex';
        container.style.justifyContent = 'center';
        container.style.alignItems = 'center';
        container.style.flexDirection = 'column';

        const canvas = document.createElement('canvas');
        canvas.style.maxWidth = '100%';
        canvas.style.height = 'auto';
        canvas.style.borderRadius = '8px';
        canvas.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
        container.appendChild(canvas);

        previewContent.innerHTML = '';
        previewContent.appendChild(container);

        // Load PDF.js if not already loaded
        if (typeof pdfjsLib === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js';
            script.onload = function() {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
                renderPdfAsImage(previewUrl, canvas);
            };
            document.head.appendChild(script);
        } else {
            renderPdfAsImage(previewUrl, canvas);
        }
    } else if (mimeType.startsWith('image/')) {
        const img = new Image();
        img.onload = function() {
            previewContent.innerHTML = '';
            img.style.maxWidth = '100%';
            img.style.maxHeight = '70vh';
            img.style.borderRadius = '8px';
            img.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
            previewContent.appendChild(img);
        };
        img.src = previewUrl;
    } else {
        previewContent.innerHTML = `
            <div class="flex items-center justify-center min-h-[300px]">
                <div class="text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-gray-600 mb-3">Preview not available for this file type.</p>
                    <a href="${previewUrl}" download="${filename}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download File
                    </a>
                </div>
            </div>
        `;
    }
}

function renderPdfAsImage(url, canvas) {
    pdfjsLib.getDocument(url).promise.then(function(pdf) {
        return pdf.getPage(1);
    }).then(function(page) {
        const viewport = page.getViewport({ scale: 1.5 });
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        const context = canvas.getContext('2d');
        page.render({ canvasContext: context, viewport: viewport });
    }).catch(function(error) {
        console.error('Error rendering PDF:', error);
        document.getElementById('previewDocContent').innerHTML = `
            <div class="flex items-center justify-center min-h-[300px]">
                <div class="text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-gray-600">Could not load PDF preview.</p>
                    <a href="${url}" download class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Download PDF</a>
                </div>
            </div>
        `;
    });
}

function closeDocPreviewModal() {
    document.getElementById('docPreviewModal').classList.add('hidden');
    document.getElementById('docPreviewModal').style.display = 'none';
    document.getElementById('previewDocContent').innerHTML = '<div class="flex items-center justify-center min-h-[300px]"><div class="text-center text-gray-500"><svg class="animate-spin w-8 h-8 mx-auto mb-3 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><p class="text-sm">Loading document preview...</p></div></div>';
}

function deleteDocument(docId) {
    if (confirm('Delete this document? This cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/procurement/purchase-orders/delete-document/${docId}`;
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeDocUploadModal();
        closeDocPreviewModal();
    }
});

// Initial pagination events
document.addEventListener('DOMContentLoaded', function() {
    attachPaginationEvents();
});
</script>
@endsection
