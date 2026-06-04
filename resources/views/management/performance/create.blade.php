@extends('layouts.management')

@section('title', 'Performance Stock Take')
@section('page-title', 'Performance Stock Take')

@section('content')
<style>
    .stock-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .stock-table th, .stock-table td {
        border: 1px solid #e5e7eb;
        padding: 8px 6px;
        vertical-align: middle;
    }
    .stock-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-align: center;
    }
    .stock-input {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        text-align: center;
        font-size: 13px;
    }
    .stock-input:focus {
        outline: none;
        border-color: #3b82f6;
        ring: 2px solid #3b82f6;
    }
    .readonly-input {
        background-color: #f3f4f6;
    }
    .ingredient-row {
        background-color: #fefce8;
    }
    .beverage-row {
        background-color: #e0f2fe;
    }
</style>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
            Performance Stock Take
        </h3>
        <p class="text-xs text-gray-500 mt-1">Enter quantity sold for each menu item. System calculates ingredient usage and stock automatically.</p>
    </div>

    <div class="p-4">
        <form method="POST" action="{{ route('management.performance.store') }}" id="performanceForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Department *</label>
                    <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Report Date</label>
                    <input type="date" name="report_date" id="report_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div class="flex items-end">
                    <button type="button" id="loadBtn" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
                        <i class="fas fa-refresh"></i> Load Menu Items
                    </button>
                </div>
            </div>

            <div id="itemsContainer" class="overflow-x-auto">
                <div class="text-center py-8 text-gray-400 border-2 border-dashed rounded-lg">
                    <i class="fas fa-utensils text-3xl mb-2 block"></i>
                    <p>Select a department and click "Load Menu Items"</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <a href="{{ route('management.performance.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Cancel</a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
                    <i class="fas fa-save"></i> Save Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let menuItemsData = [];

document.getElementById('loadBtn').addEventListener('click', function() {
    const departmentId = document.getElementById('department_id').value;
    if (!departmentId) {
        alert('Please select a department first.');
        return;
    }
    loadDepartmentData(departmentId);
});

function loadDepartmentData(departmentId) {
    showLoading();

    fetch(`/management/performance/department-stock-data/${departmentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items && data.items.length > 0) {
                menuItemsData = data.items;
                renderTable();
            } else {
                showEmpty(data.message || 'No menu items found for this department.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showEmpty('Failed to load data. Please try again.');
        });
}

function renderTable() {
    const container = document.getElementById('itemsContainer');

    let html = `
        <table class="stock-table">
            <thead>
                <tr>
                    <th rowspan="2">MENU ITEM</th>
                    <th rowspan="2">QUANTITY<br>SOLD</th>
                    <th rowspan="2">SELLING<br>PRICE</th>
                    <th colspan="4">INGREDIENTS</th>
                    <th colspan="5">GENERAL STOCK</th>
                    <th rowspan="2">COGS</th>
                    <th rowspan="2">PROFIT<br>MARGIN</th>
                    <th rowspan="2">PROFIT/<br>MARK UP</th>
                </tr>
                <tr>
                    <th>NAME</th>
                    <th>QUANTITY</th>
                    <th>UOM</th>
                    <th>COST</th>
                    <th>ITEM NAME</th>
                    <th>UOM</th>
                    <th>OPENING</th>
                    <th>USED</th>
                    <th>CLOSING</th>
                </tr>
            </thead>
            <tbody>
    `;

    for (let mi = 0; mi < menuItemsData.length; mi++) {
        const item = menuItemsData[mi];
        const ingredients = item.ingredients;
        const numIngredients = ingredients.length;

        for (let ig = 0; ig < numIngredients; ig++) {
            const ing = ingredients[ig];
            const isFirstRow = (ig === 0);
            const rowspan = isFirstRow ? numIngredients : 1;

            html += `<tr class="${item.is_beverage ? 'beverage-row' : 'ingredient-row'}">`;

            // MENU ITEM, QUANTITY SOLD, SELLING PRICE (span multiple rows)
            if (isFirstRow) {
                html += `<td rowspan="${rowspan}" class="font-semibold">${escapeHtml(item.menu_item_name)}</td>`;
                html += `<td rowspan="${rowspan}"><input type="number" class="stock-input qty-sold" data-menu-index="${mi}" value="0" step="1" min="0"></td>`;
                html += `<td rowspan="${rowspan}"><input type="number" class="stock-input selling-price" data-menu-index="${mi}" value="${item.selling_price}" step="100" min="0"></td>`;
            }

            // INGREDIENTS columns
            html += `<td>${escapeHtml(ing.inventory_item_name)}</td>`;
            html += `<td><input type="text" class="stock-input readonly-input" value="${ing.quantity_required}" readonly></td>`;
            html += `<td>${escapeHtml(ing.uom)}</td>`;
            html += `<td class="font-mono">${formatMoney(ing.unit_cost)}</td>`;

            // GENERAL STOCK columns (5 columns: ITEM NAME, UOM, OPENING, USED, CLOSING)
            html += `<td>${escapeHtml(ing.inventory_item_name)}</td>`;
            html += `<td>${escapeHtml(ing.uom)}</td>`;
            html += `<td><input type="number" class="stock-input opening-stock" data-menu-index="${mi}" data-ing-index="${ig}" value="${ing.opening_stock}" step="1" min="0"></td>`;
            html += `<td class="used-display-${mi}-${ig}">0</td>`;
            html += `<td class="closing-display-${mi}-${ig}">${ing.opening_stock}</td>`;

            // COGS, PROFIT MARGIN, PROFIT MARK UP (only on first row)
            if (isFirstRow) {
                html += `<td rowspan="${rowspan}"><span class="cogs-display-${mi}">0 UGX</span></td>`;
                html += `<td rowspan="${rowspan}"><span class="margin-display-${mi}">0%</span></td>`;
                html += `<td rowspan="${rowspan}"><span class="profit-display-${mi}">0 UGX</span></td>`;
            }

            // Hidden fields for database storage
            html += `<input type="hidden" class="inventory-id-${mi}-${ig}" value="${ing.inventory_item_id}">`;
            html += `<input type="hidden" class="quantity-required-${mi}-${ig}" value="${ing.quantity_required}">`;
            html += `<input type="hidden" class="unit-cost-hidden-${mi}-${ig}" value="${ing.unit_cost}">`;
            html += `<input type="hidden" class="used-hidden-${mi}-${ig}" value="0">`;
            html += `<input type="hidden" class="opening-hidden-${mi}-${ig}" value="${ing.opening_stock}">`;
            html += `<input type="hidden" class="closing-hidden-${mi}-${ig}" value="${ing.opening_stock}">`;

            html += `</tr>`;
        }
    }

    html += `
            </tbody>
            <tfoot class="bg-gray-100 font-semibold">
                <tr>
                    <td colspan="8" class="text-right">TOTALS:</td>
                    <td id="totalOpening">0</td>
                    <td id="totalUsed">0</td>
                    <td id="totalClosing">0</td>
                    <td id="totalCogs">0 UGX</td>
                    <td id="totalMargin">0%</td>
                    <td id="totalProfit">0 UGX</td>
                </tr>
            </tfoot>
        </table>
    `;

    container.innerHTML = html;
    attachEventListeners();
}

function attachEventListeners() {
    // Quantity sold inputs
    document.querySelectorAll('.qty-sold').forEach(input => {
        input.addEventListener('input', function() {
            const menuIndex = parseInt(this.dataset.menuIndex);
            calculateMenuRow(menuIndex);
            calculateTotals();
        });
    });

    // Selling price inputs
    document.querySelectorAll('.selling-price').forEach(input => {
        input.addEventListener('input', function() {
            const menuIndex = parseInt(this.dataset.menuIndex);
            calculateMenuRow(menuIndex);
            calculateTotals();
        });
    });

    // Opening stock inputs
    document.querySelectorAll('.opening-stock').forEach(input => {
        input.addEventListener('input', function() {
            const menuIndex = parseInt(this.dataset.menuIndex);
            const ingIndex = parseInt(this.dataset.ingIndex);
            const value = parseFloat(this.value) || 0;

            const used = parseFloat(document.querySelector(`.used-hidden-${menuIndex}-${ingIndex}`)?.value) || 0;
            let closing = value - used;
            if (closing < 0) closing = 0;

            const closingDisplay = document.querySelector(`.closing-display-${menuIndex}-${ingIndex}`);
            const closingHidden = document.querySelector(`.closing-hidden-${menuIndex}-${ingIndex}`);

            if (closingDisplay) closingDisplay.innerText = closing;
            if (closingHidden) closingHidden.value = closing;

            calculateTotals();
        });
    });
}

function calculateMenuRow(menuIndex) {
    const item = menuItemsData[menuIndex];
    const ingredients = item.ingredients;
    const numIngredients = ingredients.length;

    const qtySold = parseFloat(document.querySelector(`.qty-sold[data-menu-index="${menuIndex}"]`)?.value) || 0;
    const sellingPrice = parseFloat(document.querySelector(`.selling-price[data-menu-index="${menuIndex}"]`)?.value) || 0;

    let totalCogs = 0;

    for (let ig = 0; ig < numIngredients; ig++) {
        const ing = ingredients[ig];
        const quantityRequired = ing.quantity_required;
        const unitCost = ing.unit_cost;
        const openingStock = parseFloat(document.querySelector(`.opening-stock[data-menu-index="${menuIndex}"][data-ing-index="${ig}"]`)?.value) || 0;

        // USED = Quantity Sold × Quantity Required
        const used = qtySold * quantityRequired;
        let closing = openingStock - used;
        if (closing < 0) closing = 0;

        // Update displays
        const usedDisplay = document.querySelector(`.used-display-${menuIndex}-${ig}`);
        const closingDisplay = document.querySelector(`.closing-display-${menuIndex}-${ig}`);
        const usedHidden = document.querySelector(`.used-hidden-${menuIndex}-${ig}`);
        const closingHidden = document.querySelector(`.closing-hidden-${menuIndex}-${ig}`);

        if (usedDisplay) usedDisplay.innerText = used.toFixed(2);
        if (closingDisplay) closingDisplay.innerText = closing.toFixed(2);
        if (usedHidden) usedHidden.value = used;
        if (closingHidden) closingHidden.value = closing;

        // Calculate COGS
        const cogs = used * unitCost;
        totalCogs += cogs;
    }

    const totalRevenue = qtySold * sellingPrice;
    const profit = totalRevenue - totalCogs;
    const profitMargin = totalRevenue > 0 ? (profit / totalRevenue) * 100 : 0;

    // Update menu row totals
    const cogsDisplay = document.querySelector(`.cogs-display-${menuIndex}`);
    const marginDisplay = document.querySelector(`.margin-display-${menuIndex}`);
    const profitDisplay = document.querySelector(`.profit-display-${menuIndex}`);

    if (cogsDisplay) cogsDisplay.innerText = formatMoney(totalCogs);
    if (marginDisplay) marginDisplay.innerText = profitMargin.toFixed(1) + '%';
    if (profitDisplay) profitDisplay.innerText = formatMoney(profit);
}

function calculateTotals() {
    let totalOpening = 0;
    let totalUsed = 0;
    let totalClosing = 0;
    let totalCogs = 0;
    let totalRevenue = 0;
    let totalProfit = 0;

    for (let mi = 0; mi < menuItemsData.length; mi++) {
        const item = menuItemsData[mi];
        const numIngredients = item.ingredients.length;
        const qtySold = parseFloat(document.querySelector(`.qty-sold[data-menu-index="${mi}"]`)?.value) || 0;
        const sellingPrice = parseFloat(document.querySelector(`.selling-price[data-menu-index="${mi}"]`)?.value) || 0;

        let menuCogs = 0;

        for (let ig = 0; ig < numIngredients; ig++) {
            const opening = parseFloat(document.querySelector(`.opening-stock[data-menu-index="${mi}"][data-ing-index="${ig}"]`)?.value) || 0;
            const used = parseFloat(document.querySelector(`.used-hidden-${mi}-${ig}`)?.value) || 0;
            const closing = parseFloat(document.querySelector(`.closing-hidden-${mi}-${ig}`)?.value) || 0;
            const unitCost = parseFloat(document.querySelector(`.unit-cost-hidden-${mi}-${ig}`)?.value) || 0;

            totalOpening += opening;
            totalUsed += used;
            totalClosing += closing;
            menuCogs += used * unitCost;
        }

        totalCogs += menuCogs;
        totalRevenue += qtySold * sellingPrice;
        totalProfit += (qtySold * sellingPrice) - menuCogs;
    }

    const totalMargin = totalRevenue > 0 ? (totalProfit / totalRevenue) * 100 : 0;

    document.getElementById('totalOpening').innerText = totalOpening.toFixed(2);
    document.getElementById('totalUsed').innerText = totalUsed.toFixed(2);
    document.getElementById('totalClosing').innerText = totalClosing.toFixed(2);
    document.getElementById('totalCogs').innerText = formatMoney(totalCogs);
    document.getElementById('totalMargin').innerText = totalMargin.toFixed(1) + '%';
    document.getElementById('totalProfit').innerText = formatMoney(totalProfit);
}

function formatMoney(amount) {
    return Math.round(parseFloat(amount) || 0).toLocaleString('en-UG') + ' UGX';
}

function showLoading() {
    const container = document.getElementById('itemsContainer');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Loading menu items...</p>
            </div>
        `;
    }
}

function showEmpty(message) {
    const container = document.getElementById('itemsContainer');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-400 border-2 border-dashed rounded-lg">
                <i class="fas fa-utensils text-3xl mb-2 block"></i>
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

// Form submission - stores ALL data to database
document.getElementById('performanceForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const departmentId = document.getElementById('department_id').value;
    const reportDate = document.getElementById('report_date').value;

    if (!departmentId) {
        alert('Please select a department.');
        return;
    }

    const salesData = [];

    for (let mi = 0; mi < menuItemsData.length; mi++) {
        const item = menuItemsData[mi];
        const qtySold = parseFloat(document.querySelector(`.qty-sold[data-menu-index="${mi}"]`)?.value) || 0;
        const sellingPrice = parseFloat(document.querySelector(`.selling-price[data-menu-index="${mi}"]`)?.value) || 0;

        if (qtySold === 0) continue;

        const ingredients = [];
        const numIngredients = item.ingredients.length;

        for (let ig = 0; ig < numIngredients; ig++) {
            const inventoryItemId = document.querySelector(`.inventory-id-${mi}-${ig}`)?.value;
            const quantityRequired = parseFloat(document.querySelector(`.quantity-required-${mi}-${ig}`)?.value) || 0;
            const usedQuantity = parseFloat(document.querySelector(`.used-hidden-${mi}-${ig}`)?.value) || 0;
            const openingStock = parseFloat(document.querySelector(`.opening-stock[data-menu-index="${mi}"][data-ing-index="${ig}"]`)?.value) || 0;
            const closingStock = parseFloat(document.querySelector(`.closing-hidden-${mi}-${ig}`)?.value) || 0;
            const unitCost = parseFloat(document.querySelector(`.unit-cost-hidden-${mi}-${ig}`)?.value) || 0;

            ingredients.push({
                inventory_item_id: inventoryItemId,
                quantity_required: quantityRequired,
                used_quantity: usedQuantity,
                opening_stock: openingStock,
                closing_stock: closingStock,
                unit_cost: unitCost,
            });
        }

        salesData.push({
            menu_item_id: item.menu_item_id,
            quantity_sold: qtySold,
            selling_price: sellingPrice,
            ingredients: ingredients,
        });
    }

    if (salesData.length === 0) {
        alert('Please enter quantity sold for at least one menu item.');
        return;
    }

    fetch('{{ route("management.performance.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            department_id: departmentId,
            report_date: reportDate,
            sales_data: salesData,
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'Failed to save report.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>
@endsection
