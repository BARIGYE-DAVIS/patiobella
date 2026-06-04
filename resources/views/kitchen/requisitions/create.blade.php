@extends('layouts.kitchen')

@section('title', 'Create Requisition')
@section('page-title', 'Create New Requisition')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-orange-600"></i>
            Create New Requisition
        </h3>
        <p class="text-xs text-gray-500 mt-1">Request items from the store for kitchen operations</p>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('kitchen.requisitions.store') }}" id="requisitionForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Requisition Type <span class="text-red-500">*</span>
                    </label>
                    <select name="requisition_type" id="requisition_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500" required>
                        <option value="">Select Type</option>
                        @foreach($requisitionTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('requisition_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Date Needed</label>
                    <input type="date" name="date_needed" id="date_needed" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500" value="{{ old('date_needed') }}">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Notes (for Store)</label>
                    <textarea name="department_notes" id="department_notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500" rows="2" placeholder="Any special instructions for the store...">{{ old('department_notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 mb-3">
                <h4 class="font-semibold text-gray-700">Items Requested</h4>
            </div>

            <div class=" mb-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b-2 border-gray-200">
                            <th class="w-[45%] px-3 py-3 text-left font-semibold text-gray-600">Item <span class="text-red-500">*</span></th>
                            <th class="w-[25%] px-3 py-3 text-left font-semibold text-gray-600">Quantity <span class="text-red-500">*</span></th>
                            <th class="w-[20%] px-3 py-3 text-left font-semibold text-gray-600">Metrics</th>
                            <th class="w-[10%] px-3 py-3 text-center font-semibold text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody"></tbody>
                </table>
            </div>

            <div class="mb-4 flex gap-3">
                <button type="button" id="addItemBtn" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> Add Item
                </button>
                <button type="button" id="previewBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-eye"></i> Preview & Print
                </button>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('kitchen.requisitions.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Submit Requisition
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
    <div class="min-h-screen p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-600"></i>
                    Requisition Preview
                </h3>
                <div class="flex gap-2">
                    <button onclick="printPreview()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button onclick="downloadPreviewPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button onclick="closePreviewModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            <div id="previewContent" class="p-6">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // Backend-provided lists (prepared in controller)
    let itemsList = @json($itemsForJs);
    let rowCounter = 0;
    let searchTimeout = null;
    let currentUser = @json($currentUserForJs);
    const storageBaseUrl = "{{ asset('storage') }}";

    async function fetchItemDetails(itemId, rowElement) {
        if (!itemId) return;

        const loadingSpinner = rowElement.querySelector('.loading-spinner');
        if (loadingSpinner) loadingSpinner.classList.remove('hidden');

        try {
            const response = await fetch(`/kitchen/requisitions/item-details/${itemId}`);
            const result = await response.json();

            if (result.success) {
                updateRowWithItemData(rowElement, result.data);
            }
        } catch (error) {
            console.error('Error fetching item details:', error);
        } finally {
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
        }
    }

    function updateRowWithItemData(rowElement, data) {
        const metricsInput = rowElement.querySelector('.item-metrics');

        if (metricsInput && data.metrics) {
            metricsInput.value = data.metrics;
        }
    }

    function filterItems(searchTerm) {
        if (!searchTerm.trim()) {
            return itemsList;
        }
        return itemsList.filter(item =>
            item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            item.code.toLowerCase().includes(searchTerm.toLowerCase())
        );
    }

    function renderDropdown(dropdownElement, items, searchInput, rowElement) {
        if (!dropdownElement) return;

        if (items.length === 0) {
            dropdownElement.innerHTML = '<div class="px-3 py-2 text-gray-500 text-sm">No items found</div>';
            dropdownElement.classList.remove('hidden');
            return;
        }

        dropdownElement.innerHTML = items.map(item => `
            <div class="search-result-item px-3 py-2 cursor-pointer hover:bg-orange-50 border-b border-gray-100 last:border-0 transition-colors" data-id="${item.id}" data-name="${escapeHtml(item.name)}" data-code="${escapeHtml(item.code)}" data-unit="${escapeHtml(item.unit_of_measurement)}">
                <div class="font-semibold text-sm text-gray-800">${escapeHtml(item.name)}</div>
                <div class="text-xs text-gray-500">Code: ${escapeHtml(item.code)} | Unit: ${escapeHtml(item.unit_of_measurement)}</div>
            </div>
        `).join('');
        dropdownElement.classList.remove('hidden');

        dropdownElement.querySelectorAll('.search-result-item').forEach(el => {
            el.addEventListener('click', async (e) => {
                e.stopPropagation();
                const itemId = el.dataset.id;
                const itemName = el.dataset.name;
                const itemCode = el.dataset.code;
                const unitOfMeasure = el.dataset.unit;
                const wrapper = searchInput.closest('.item-search-wrapper');
                const selectedBadge = wrapper.querySelector('.selected-item-badge');
                const selectedInfoSpan = selectedBadge.querySelector('.item-info');
                const hiddenId = wrapper.querySelector('.selected-item-id');
                const metricsInput = rowElement.querySelector('.item-metrics');

                hiddenId.value = itemId;
                searchInput.value = itemName;
                searchInput.classList.add('hidden');
                selectedInfoSpan.innerHTML = `${escapeHtml(itemName)} <span class="text-xs text-gray-500">(${escapeHtml(itemCode)})</span>`;
                selectedBadge.classList.remove('hidden');
                dropdownElement.classList.add('hidden');

                if (metricsInput) {
                    metricsInput.value = unitOfMeasure;
                }

                const quantityInput = rowElement.querySelector('.item-quantity');
                quantityInput.disabled = false;
                quantityInput.required = true;
            });
        });
    }

    function performLiveSearch(searchInput, dropdownElement, rowElement) {
        const searchTerm = searchInput.value;
        const filteredItems = filterItems(searchTerm);
        renderDropdown(dropdownElement, filteredItems, searchInput, rowElement);
    }

    function clearSelectedItem(wrapper, rowElement) {
        const searchInput = wrapper.querySelector('.item-search-input');
        const dropdown = wrapper.querySelector('.search-results-dropdown');
        const selectedBadge = wrapper.querySelector('.selected-item-badge');
        const hiddenId = wrapper.querySelector('.selected-item-id');
        const quantityInput = rowElement.querySelector('.item-quantity');
        const metricsInput = rowElement.querySelector('.item-metrics');

        searchInput.value = '';
        searchInput.classList.remove('hidden');
        selectedBadge.classList.add('hidden');
        hiddenId.value = '';
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';

        quantityInput.disabled = true;
        quantityInput.required = false;
        quantityInput.value = '';
        metricsInput.value = '';
    }

    function setupRowSearch(rowElement) {
        const wrapper = rowElement.querySelector('.item-search-wrapper');
        if (!wrapper) return;

        const searchInput = wrapper.querySelector('.item-search-input');
        const dropdown = wrapper.querySelector('.search-results-dropdown');
        const clearBtn = wrapper.querySelector('.clear-item-btn');

        searchInput.addEventListener('input', function() {
            const hiddenId = wrapper.querySelector('.selected-item-id');
            if (hiddenId.value) return;

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performLiveSearch(searchInput, dropdown, rowElement);
            }, 250);
        });

        searchInput.addEventListener('focus', function() {
            const hiddenId = wrapper.querySelector('.selected-item-id');
            if (!hiddenId.value) {
                performLiveSearch(searchInput, dropdown, rowElement);
            }
        });

        clearBtn.addEventListener('click', () => {
            clearSelectedItem(wrapper, rowElement);
        });

        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function createNewRow() {
        const index = rowCounter++;
        const newRow = document.createElement('tr');
        newRow.className = 'item-row border-b border-gray-100';
        newRow.dataset.index = index;

        newRow.innerHTML = `
            <td class="px-3 py-2 align-top">
                <div class="relative item-search-wrapper">
                    <input type="text" class="item-search-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500" placeholder="Type to search items..." autocomplete="off">
                    <div class="search-results-dropdown absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                    <div class="selected-item-badge hidden flex justify-between items-center bg-orange-50 px-3 py-2 rounded-lg border border-orange-200">
                        <span class="item-info font-semibold text-sm text-orange-800"></span>
                        <button type="button" class="clear-item-btn text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition-colors">✕ Remove</button>
                    </div>
                    <input type="hidden" name="items[${index}][inventory_item_id]" class="selected-item-id" value="">
                    <div class="loading-spinner hidden absolute right-3 top-2">
                        <div class="w-4 h-4 border-2 border-orange-600 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </td>
            <td class="px-3 py-2">
                <input type="number" name="items[${index}][quantity]" step="0.01" class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 disabled:bg-gray-100 disabled:cursor-not-allowed" placeholder="0.00" disabled>
            </td>
            <td class="px-3 py-2">
                <input type="text" name="items[${index}][metrics]" class="item-metrics w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 cursor-not-allowed" placeholder="Auto-filled" readonly>
            </td>
            <td class="px-3 py-2 text-center align-top">
                <button type="button" class="remove-item bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        return newRow;
    }

    function removeItemRow(button) {
        const row = button.closest('.item-row');
        if (row) {
            row.remove();
            reindexRows();
        }
    }

    function reindexRows() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, newIndex) => {
            row.dataset.index = newIndex;
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${newIndex}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
        rowCounter = rows.length;
    }

    function getFormItems() {
        const items = [];
        const rows = document.querySelectorAll('.item-row');

        rows.forEach(row => {
            const itemId = row.querySelector('.selected-item-id').value;
            const itemNameElement = row.querySelector('.selected-item-badge .item-info');
            let itemName = '';
            if (itemNameElement) {
                itemName = itemNameElement.innerText.split('(')[0].trim();
            }
            const quantity = row.querySelector('.item-quantity').value;
            const metrics = row.querySelector('.item-metrics').value;

            if (itemId && parseFloat(quantity) > 0) {
                items.push({
                    id: itemId,
                    name: itemName,
                    quantity: quantity,
                    metrics: metrics
                });
            }
        });

        return items;
    }

    function generatePreviewHTML() {
        const requisitionType = document.getElementById('requisition_type').value;
        let requisitionTypeLabel = requisitionType;
        const typeSelect = document.querySelector('select[name="requisition_type"]');
        if (typeSelect) {
            const selectedOption = typeSelect.options[typeSelect.selectedIndex];
            if (selectedOption) {
                requisitionTypeLabel = selectedOption.innerText;
            }
        }
        const dateNeeded = document.getElementById('date_needed').value;
        const departmentNotes = document.getElementById('department_notes').value;
        const items = getFormItems();

        const now = new Date();
        const formattedDate = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

        // Requested By signature block (image + name side-by-side)
        let requestedByBlock = `<div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div style="border-top: 1px solid #111827; padding-top: 10px; display:inline-block; min-width:200px; text-align:center;">
                <strong>Requested By Signature</strong>
            </div>
            <div style="margin-top:12px; text-align:center; width:100%;">
                <p style="margin:0; color:#6b7280; font-size:12px;">No signature available</p>
                <p style="margin-top:8px;"><strong>${currentUser ? (escapeHtml(currentUser.first_name || '') + ' ' + escapeHtml(currentUser.last_name || '')) : 'N/A'}</strong></p>
                <p style="font-size:12px; color:#6b7280; margin:0;">Requested By</p>
            </div>
        </div>`;

        if (currentUser && currentUser.signature_path) {
            const signatureUrl = storageBaseUrl + '/' + currentUser.signature_path;
            requestedByBlock = `
                <div style="display:flex; flex-direction:column; gap:8px; align-items:center;">
                    <div style="border-top: 1px solid #111827; padding-top: 10px; display:inline-block; min-width:200px; text-align:center;">
                        <strong>Requested By Signature</strong>
                    </div>
                    <div style="display:flex; align-items:center; gap:12px; margin-top:12px;">
                        <div style="flex-shrink:0;">
                            <img src="${signatureUrl}" style="max-height:60px; max-width:200px; display:block;">
                        </div>
                        <div style="text-align:left;">
                            <p style="margin:0;"><strong>${escapeHtml(currentUser.first_name || '')} ${escapeHtml(currentUser.last_name || '')}</strong></p>
                            <p style="font-size:12px; color:#6b7280; margin:2px 0 0;">Requested By</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Approved By placeholder block (keeps layout consistent)
        const approvedByBlock = `
            <div style="display:flex; flex-direction:column; gap:8px; align-items:center;">
                <div style="border-top: 1px solid #111827; padding-top: 10px; display:inline-block; min-width:200px; text-align:center;">
                    <strong>Approved By Signature</strong>
                </div>
                <div style="margin-top:12px; text-align:center; width:100%;">
                    <p style="color:#9ca3af; font-size:12px; margin:0;">(To be signed upon approval)</p>
                    <p style="margin-top:8px;"><strong>_________________________</strong></p>
                    <p style="font-size:12px; color:#6b7280; margin:0;">Approved By</p>
                </div>
            </div>
        `;

        // The preview uses inline styles to ensure print fidelity.
        return `
            <div style="font-family: Arial, sans-serif; max-width: 100%; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px;">
                    <h2 style="margin: 0; color: #111827;">KITCHEN REQUISITION FORM</h2>
                    <p style="margin: 4px 0 0; color: #6b7280;">Department Requisition Slip</p>
                </div>

                <div style="margin-bottom: 18px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 6px; width:50%;"><strong>Requisition Type:</strong> ${escapeHtml(requisitionTypeLabel)}</td>
                            <td style="padding: 6px; width:50%;"><strong>Date Created:</strong> ${formattedDate}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px;"><strong>Date Needed:</strong> ${dateNeeded || 'Not specified'}</td>
                            <td style="padding: 6px;"><strong>Department:</strong> Kitchen</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px;"><strong>Requested By:</strong> ${currentUser ? (escapeHtml(currentUser.first_name || '') + ' ' + escapeHtml(currentUser.last_name || '')) : 'N/A'}</td>
                            <td style="padding: 6px;"><strong>Status:</strong> Pending</td>
                        </tr>
                    </table>
                </div>

                ${departmentNotes ? `
                <div style="margin-bottom: 16px; padding: 8px; background: #f9fafb; border-left: 4px solid #f97316;">
                    <strong>Notes:</strong>
                    <p style="margin: 6px 0 0; color: #4b5563;">${escapeHtml(departmentNotes)}</p>
                </div>
                ` : ''}

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #e5e7eb; padding: 8px; background: #f97316; color: #fff; text-align: left;">#</th>
                            <th style="border: 1px solid #e5e7eb; padding: 8px; background: #f97316; color: #fff; text-align: left;">Item Name</th>
                            <th style="border: 1px solid #e5e7eb; padding: 8px; background: #f97316; color: #fff; text-align: left;">Quantity</th>
                            <th style="border: 1px solid #e5e7eb; padding: 8px; background: #f97316; color: #fff; text-align: left;">Metrics</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map((item, idx) => `
                            <tr>
                                <td style="border: 1px solid #e5e7eb; padding: 8px;">${idx + 1}</td>
                                <td style="border: 1px solid #e5e7eb; padding: 8px;">${escapeHtml(item.name)}</td>
                                <td style="border: 1px solid #e5e7eb; padding: 8px;">${parseFloat(item.quantity).toFixed(2)}</td>
                                <td style="border: 1px solid #e5e7eb; padding: 8px;">${escapeHtml(item.metrics) || 'pcs'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div style="margin-top: 24px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; padding: 18px; vertical-align: top;">
                                ${requestedByBlock}
                            </td>
                            <td style="width: 50%; padding: 18px; vertical-align: top;">
                                ${approvedByBlock}
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="margin-top: 24px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #eef2f7; padding-top: 12px;">
                    <p>This is a system-generated requisition. Please verify all items before approval.</p>
                </div>
            </div>
        `;
    }

    function showPreviewModal() {
        const requisitionType = document.getElementById('requisition_type').value;

        if (!requisitionType) {
            alert('Please select a requisition type first.');
            return;
        }

        // Prevent past Date Needed
        const dateNeeded = document.getElementById('date_needed').value;
        if (dateNeeded) {
            const picked = new Date(dateNeeded);
            // zero time portion for safe compare
            picked.setHours(0,0,0,0);
            const today = new Date();
            today.setHours(0,0,0,0);
            if (picked < today) {
                alert('Please select today or a future date for Date Needed.');
                return;
            }
        }

        const items = getFormItems();
        if (items.length === 0) {
            alert('Please add at least one item to preview.');
            return;
        }

        const previewContent = generatePreviewHTML();
        document.getElementById('previewContent').innerHTML = previewContent;
        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreviewModal() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    function printPreview() {
        const printContent = document.getElementById('previewContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Kitchen Requisition Preview</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        @media print {
                            body { margin: 0; padding: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>
                        window.onload = function() { window.print(); window.close(); };
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    function downloadPreviewPDF() {
        const element = document.getElementById('previewContent');
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: 'Requisition_Preview_' + new Date().toISOString().slice(0,19) + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, letterRendering: true, useCORS: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    // Add initial row and wire events
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newRow = createNewRow();
        tbody.appendChild(newRow);
        setupRowSearch(newRow);

        newRow.querySelector('.remove-item').addEventListener('click', function() {
            removeItemRow(this);
        });
    });

    document.getElementById('previewBtn').addEventListener('click', showPreviewModal);

    // Ensure at least one row on load
    if (document.querySelectorAll('.item-row').length === 0) {
        document.getElementById('addItemBtn').click();
    }

    // Form validation before submit
    document.getElementById('requisitionForm').addEventListener('submit', function(e) {
        let hasValidItem = false;
        const rows = document.querySelectorAll('.item-row');
        const requisitionType = document.querySelector('select[name="requisition_type"]').value;

        if (!requisitionType) {
            e.preventDefault();
            alert('Please select a requisition type (Daily, Weekly, or Monthly).');
            return false;
        }

        // Prevent submitting a past Date Needed
        const dateNeeded = document.getElementById('date_needed').value;
        if (dateNeeded) {
            const picked = new Date(dateNeeded); picked.setHours(0,0,0,0);
            const today = new Date(); today.setHours(0,0,0,0);
            if (picked < today) {
                e.preventDefault();
                alert('Please select today or a future date for Date Needed.');
                return false;
            }
        }

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const itemId = row.querySelector('.selected-item-id').value;
            const quantity = row.querySelector('.item-quantity').value;

            if (itemId && parseFloat(quantity) > 0) {
                hasValidItem = true;
                break;
            }
        }

        if (!hasValidItem) {
            e.preventDefault();
            alert('Please add at least one item with a valid quantity.');
        }
    });
</script>
@endsection
