{{-- resources/views/management/performance/create.blade.php --}}

@extends('layouts.management')

@section('title', 'Performance Stock Take')
@section('page-title', 'Performance Stock Take')

@section('content')
<style>
    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .used-qty {
        background-color: #eff6ff;
    }
    .used-qty:focus {
        outline: none;
        border-color: #3b82f6;
        ring: 2px solid #3b82f6;
    }
</style>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-blue-600"></i>
            Performance Stock Take
        </h3>
        <p class="text-xs text-gray-500 mt-1">
            Opening stock loads from current department stock. Enter Used quantity. Closing and COGS calculate automatically.
        </p>
    </div>

    <div class="p-4 sm:p-6">
        <form method="POST" action="{{ route('management.performance.store') }}" id="stockForm">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Department <span class="text-red-500">*</span>
                    </label>
                    <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Stock Take Date
                    </label>
                    <input type="text" id="stock_date_display" value="{{ date('d M Y') }}"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-100 text-gray-600" readonly>
                    <input type="hidden" name="stock_date" id="stock_date" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="mt-6 mb-3">
                <h4 class="font-semibold text-gray-700">Department Inventory Items</h4>
                <p class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    <strong>Only enter Used quantity.</strong> Opening is auto-loaded from current department stock.
                </p>
            </div>

            {{-- Container for items --}}
            <div id="itemsContainer">
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Select a department to load items...</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('management.performance.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 sm:px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Stock Take
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let allItems = [];
    let currentView = 'desktop';

    document.getElementById('department_id').addEventListener('change', function() {
        const departmentId = this.value;
        if (departmentId) {
            loadDepartmentData(departmentId);
        } else {
            showLoadingMessage('Select a department to load items...');
        }
    });

    function loadDepartmentData(departmentId) {
        showLoadingMessage('Loading items...');

        fetch(`/management/performance/department-stock-data/${departmentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.items && data.items.length > 0) {
                    allItems = data.items;
                    renderResponsiveView();
                } else {
                    showLoadingMessage(data.message || 'No items found for this department.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showLoadingMessage('Failed to load department data. Please try again.');
            });
    }

    function renderResponsiveView() {
        const container = document.getElementById('itemsContainer');
        const isMobile = window.innerWidth < 1024;

        if (isMobile) {
            renderMobileView(container);
            currentView = 'mobile';
        } else {
            renderDesktopView(container);
            currentView = 'desktop';
        }

        attachEventListeners();
        calculateAllTotals();
    }

    function renderDesktopView(container) {
        let html = `
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm min-w-[800px]">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600">Item</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Unit</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Opening</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Added</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-blue-600 bg-blue-50">Used</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Closing</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600">Unit Cost</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600">COGS</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
        `;

        allItems.forEach((item, index) => {
            const opening = Math.floor(item.opening_stock || 0);
            const added = Math.floor(item.added_today || 0);
            const closingInitial = opening + added;

            html += `
                <tr class="item-row border-b border-gray-100 hover:bg-gray-50" data-index="${index}">
                    <td class="px-3 py-2">
                        <input type="hidden" name="items[${index}][inventory_item_id]" value="${item.inventory_item_id}">
                        <input type="hidden" name="items[${index}][unit_cost]" value="${Math.floor(item.unit_cost)}">
                        <input type="hidden" name="items[${index}][opening_quantity]" class="opening-qty-${index}" value="${opening}">
                        <input type="hidden" name="items[${index}][added_quantity]" class="added-qty-${index}" value="${added}">
                        <p class="font-medium text-gray-800">${escapeHtml(item.item_name)}</p>
                        <p class="text-xs text-gray-400">${escapeHtml(item.item_code || '')}</p>
                    </td>
                    <td class="px-3 py-2 text-center text-gray-500">${escapeHtml(item.unit_of_measurement || 'piece')}</td>
                    <td class="px-3 py-2 text-center"><span class="opening-display-${index} font-semibold">${opening}</span></td>
                    <td class="px-3 py-2 text-center"><span class="added-display-${index} font-semibold">${added}</span></td>
                    <td class="px-3 py-2 text-center">
                        <input type="number" name="items[${index}][used_quantity]" step="1"
                               class="used-qty w-20 sm:w-24 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                               value="0" data-index="${index}">
                    </td>
                    <td class="px-3 py-2 text-center">
                        <span class="closing-display-${index} font-semibold">${closingInitial}</span>
                        <input type="hidden" name="items[${index}][closing_quantity]" class="closing-qty-${index}" value="${closingInitial}">
                    </td>
                    <td class="px-3 py-2 text-right">${Math.floor(item.unit_cost)}</td>
                    <td class="px-3 py-2 text-right">
                        <span class="cogs-display-${index} text-red-600 font-semibold">0</span>
                        <input type="hidden" name="items[${index}][cogs]" class="cogs-qty-${index}" value="0">
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200 font-semibold">
                        <tr>
                            <td colspan="5" class="px-3 py-3 text-right">TOTALS:</td>
                            <td class="px-3 py-3 text-center" id="totalClosingDisplay">0</td>
                            <td class="px-3 py-3 text-right">-</td>
                            <td class="px-3 py-3 text-right text-red-600" id="totalCogsDisplay">0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    function renderMobileView(container) {
        let html = `<div class="space-y-3" id="mobileCardsContainer">`;

        allItems.forEach((item, index) => {
            const opening = Math.floor(item.opening_stock || 0);
            const added = Math.floor(item.added_today || 0);
            const closingInitial = opening + added;

            html += `
                <div class="border border-gray-200 rounded-lg p-3 bg-white item-row" data-index="${index}">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold text-gray-800">${escapeHtml(item.item_name)}</p>
                            <p class="text-xs text-gray-400">${escapeHtml(item.item_code || '')}</p>
                        </div>
                        <span class="text-xs text-gray-500">${escapeHtml(item.unit_of_measurement || 'piece')}</span>
                    </div>
                    <input type="hidden" name="items[${index}][inventory_item_id]" value="${item.inventory_item_id}">
                    <input type="hidden" name="items[${index}][unit_cost]" value="${Math.floor(item.unit_cost)}">
                    <input type="hidden" name="items[${index}][opening_quantity]" class="opening-qty-${index}" value="${opening}">
                    <input type="hidden" name="items[${index}][added_quantity]" class="added-qty-${index}" value="${added}">

                    <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                        <div>
                            <label class="text-gray-500 block">Opening</label>
                            <span class="opening-display-${index} font-semibold block text-center py-1 bg-gray-50 rounded">${opening}</span>
                        </div>
                        <div>
                            <label class="text-gray-500 block">Added</label>
                            <span class="added-display-${index} font-semibold block text-center py-1 bg-gray-50 rounded">${added}</span>
                        </div>
                        <div>
                            <label class="text-gray-500 block">Used</label>
                            <input type="number" name="items[${index}][used_quantity]" step="1"
                                   class="used-qty w-full px-2 py-1 border border-gray-300 rounded-lg text-center text-sm"
                                   value="0" data-index="${index}">
                        </div>
                        <div>
                            <label class="text-gray-500 block">Closing</label>
                            <span class="closing-display-${index} font-semibold block text-center py-1 bg-gray-50 rounded">${closingInitial}</span>
                            <input type="hidden" name="items[${index}][closing_quantity]" class="closing-qty-${index}" value="${closingInitial}">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs pt-2 border-t border-gray-100">
                        <span class="text-gray-500">Unit Cost: ${Math.floor(item.unit_cost)}</span>
                        <span class="cogs-display-${index} text-red-600 font-semibold">COGS: 0</span>
                        <input type="hidden" name="items[${index}][cogs]" class="cogs-qty-${index}" value="0">
                    </div>
                </div>
            `;
        });

        html += `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 font-semibold">
                        <div class="flex justify-between">
                            <span>TOTAL CLOSING:</span>
                            <span id="totalClosingDisplay" class="text-blue-600">0</span>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span>TOTAL COGS:</span>
                            <span id="totalCogsDisplay" class="text-red-600">0</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = html;
    }

    function attachEventListeners() {
        document.querySelectorAll('.used-qty').forEach(input => {
            input.removeEventListener('input', handleUsedInput);
            input.addEventListener('input', handleUsedInput);
        });
    }

    function handleUsedInput(event) {
        const index = parseInt(event.target.getAttribute('data-index'));
        if (!isNaN(index)) {
            calculateRow(index);
            calculateAllTotals();
        }
    }

    function calculateRow(index) {
        const opening = parseInt(document.querySelector(`.opening-qty-${index}`)?.value) || 0;
        const added = parseInt(document.querySelector(`.added-qty-${index}`)?.value) || 0;
        const used = parseInt(document.querySelector(`.used-qty[data-index="${index}"]`)?.value) || 0;
        const unitCost = parseInt(document.querySelector(`input[name*="items[${index}]"][name*="unit_cost"]`)?.value) || 0;

        let closing = opening + added - used;
        if (closing < 0) closing = 0;

        const cogs = used * unitCost;

        const closingDisplay = document.querySelector(`.closing-display-${index}`);
        const closingHidden = document.querySelector(`.closing-qty-${index}`);
        const cogsDisplay = document.querySelector(`.cogs-display-${index}`);
        const cogsHidden = document.querySelector(`.cogs-qty-${index}`);

        if (closingDisplay) closingDisplay.innerText = closing;
        if (closingHidden) closingHidden.value = closing;
        if (cogsDisplay) cogsDisplay.innerText = cogs;
        if (cogsHidden) cogsHidden.value = cogs;

        return { closing, cogs };
    }

    function calculateAllTotals() {
        let totalClosing = 0;
        let totalCogs = 0;

        for (let i = 0; i < allItems.length; i++) {
            const { closing, cogs } = calculateRow(i);
            totalClosing += closing;
            totalCogs += cogs;
        }

        const totalClosingDisplay = document.getElementById('totalClosingDisplay');
        const totalCogsDisplay = document.getElementById('totalCogsDisplay');

        if (totalClosingDisplay) totalClosingDisplay.innerText = totalClosing;
        if (totalCogsDisplay) totalCogsDisplay.innerText = totalCogs;
    }

    function showLoadingMessage(message) {
        const container = document.getElementById('itemsContainer');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>${message}</p>
                </div>
            `;
        }
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

    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            const isMobile = window.innerWidth < 1024;
            if ((isMobile && currentView !== 'mobile') || (!isMobile && currentView !== 'desktop')) {
                if (allItems.length > 0) {
                    renderResponsiveView();
                }
            }
        }, 250);
    });

    // Load on page load if department preselected
    document.addEventListener('DOMContentLoaded', function() {
        const preselectedDept = document.getElementById('department_id').value;
        if (preselectedDept) {
            loadDepartmentData(preselectedDept);
        }
    });
</script>
@endsection
 