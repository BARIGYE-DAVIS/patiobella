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
    .stock-row {
        background-color: #f0fdf4;
    }
    .side-by-side {
        display: flex;
        gap: 20px;
        overflow-x: auto;
    }
    .menu-section {
        flex: 2;
        min-width: 800px;
    }
    .stock-section {
        flex: 1;
        min-width: 300px;
    }
    .section-title {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 10px;
        padding: 8px;
        background-color: #e5e7eb;
        border-radius: 6px;
    }
    .totals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
    }
    .total-card {
        background: white;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    .total-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 5px;
    }
    .total-value {
        font-size: 20px;
        font-weight: bold;
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

            <div id="itemsContainer" class="side-by-side">
                <div class="text-center py-8 text-gray-400 border-2 border-dashed rounded-lg w-full">
                    <i class="fas fa-utensils text-3xl mb-2 block"></i>
                    <p>Select a department and click "Load Menu Items"</p>
                </div>
            </div>

            <!-- TOTALS SECTION -->
            <div id="totalsSection" class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-200" style="display: none;">
                <h4 class="font-semibold text-gray-800 mb-3">SUMMARY TOTALS</h4>
                <div class="totals-grid">
                    <div class="total-card">
                        <div class="total-label">TOTAL SALES</div>
                        <div class="total-value text-emerald-600" id="totalSalesAmount">0 UGX</div>
                    </div>
                    <div class="total-card">
                        <div class="total-label">TOTAL COGS</div>
                        <div class="total-value text-red-600" id="totalCogsAmount">0 UGX</div>
                    </div>
                    <div class="total-card">
                        <div class="total-label">TOTAL PROFIT</div>
                        <div class="total-value text-blue-600" id="totalProfitAmount">0 UGX</div>
                    </div>
                    <div class="total-card">
                        <div class="total-label">AVERAGE PROFIT MARGIN</div>
                        <div class="total-value text-purple-600" id="avgMarginDisplay">0%</div>
                    </div>
                    <div class="total-card">
                        <div class="total-label">AVERAGE PROFIT</div>
                        <div class="total-value text-orange-600" id="avgProfitDisplay">0 UGX</div>
                    </div>
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
let stockItemsData = [];

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
            if (data.success) {
                menuItemsData = data.items || [];
                stockItemsData = data.stock_items || [];
                renderSideBySide();
                document.getElementById('totalsSection').style.display = 'block';
            } else {
                showEmpty(data.message || 'No menu items found for this department.');
                document.getElementById('totalsSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showEmpty('Failed to load data. Please try again.');
            document.getElementById('totalsSection').style.display = 'none';
        });
}

function renderSideBySide() {
    const container = document.getElementById('itemsContainer');

    let html = `
        <div class="menu-section">
            <div class="section-title">MENU ITEMS & INGREDIENTS</div>
            <table class="stock-table">
                <thead>
                    <tr>
                        <th rowspan="2">MENU ITEM</th>
                        <th rowspan="2">QTY<br>SOLD</th>
                        <th rowspan="2">SELLING<br>PRICE</th>
                        <th colspan="4">INGREDIENTS</th>
                        <th rowspan="2">COGS</th>
                        <th rowspan="2">PROFIT<br>MARGIN</th>
                        <th rowspan="2">PROFIT/<br>MARK UP</th>
                    </tr>
                    <tr>
                        <th>NAME</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>COST</th>
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

            if (isFirstRow) {
                html += `<td rowspan="${rowspan}" class="font-semibold">${escapeHtml(item.menu_item_name)}</td>`;
                html += `<td rowspan="${rowspan}"><input type="number" class="stock-input qty-sold" data-menu-index="${mi}" value="0" step="1" min="0"></td>`;
                html += `<td rowspan="${rowspan}"><input type="number" class="stock-input selling-price" data-menu-index="${mi}" value="${item.selling_price}" step="100" min="0"></td>`;
            }

            html += `<td>${escapeHtml(ing.inventory_item_name)}</td>`;
            html += `<td><input type="text" class="stock-input readonly-input" value="${ing.quantity_required}" readonly></td>`;
            html += `<td>${escapeHtml(ing.uom)}</td>`;
            html += `<td class="font-mono">${formatMoney(ing.unit_cost)}</td>`;

            if (isFirstRow) {
                html += `<td rowspan="${rowspan}"><span class="cogs-display-${mi}">0 UGX</span></td>`;
                html += `<td rowspan="${rowspan}"><span class="margin-display-${mi}">0%</span></td>`;
                html += `<td rowspan="${rowspan}"><span class="profit-display-${mi}">0 UGX</span></td>`;
            }

            // Hidden fields
            html += `<input type="hidden" class="inventory-id-${mi}-${ig}" value="${ing.inventory_item_id}">`;
            html += `<input type="hidden" class="quantity-required-${mi}-${ig}" value="${ing.quantity_required}">`;
            html += `<input type="hidden" class="unit-cost-hidden-${mi}-${ig}" value="${ing.unit_cost}">`;
            html += `<input type="hidden" class="used-hidden-${mi}-${ig}" value="0">`;

            html += `</tr>`;
        }
    }

    html += `
                </tbody>
            </table>
        </div>

        <div class="stock-section">
            <div class="section-title">GENERAL STOCK</div>
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>UOM</th>
                        <th>OPENING</th>
                        <th>USED</th>
                        <th>CLOSING</th>
                    </tr>
                </thead>
                <tbody>
    `;

    for (let si = 0; si < stockItemsData.length; si++) {
        const stockItem = stockItemsData[si];

        html += `
            <tr class="stock-row">
                <td>${escapeHtml(stockItem.inventory_item_name)}</td>
                <td>${escapeHtml(stockItem.uom)}</td>
                <td><input type="number" class="stock-input opening-stock-input" data-stock-index="${si}" value="${stockItem.opening_stock}" step="1" min="0"></td>
                <td class="stock-used-display-${si}">0</td>
                <td class="stock-closing-display-${si}">${stockItem.opening_stock}</td>
            </tr>
        `;

        // Hidden fields
        html += `<input type="hidden" class="stock-inventory-id-${si}" value="${stockItem.inventory_item_id}">`;
        html += `<input type="hidden" class="stock-unit-cost-${si}" value="${stockItem.unit_cost}">`;
        html += `<input type="hidden" class="stock-opening-hidden-${si}" value="${stockItem.opening_stock}">`;
        html += `<input type="hidden" class="stock-used-hidden-${si}" value="0">`;
        html += `<input type="hidden" class="stock-closing-hidden-${si}" value="${stockItem.opening_stock}">`;
    }

    html += `
                </tbody>
                <tfoot class="bg-gray-100 font-semibold">
                    <tr>
                        <td colspan="2" class="text-right">TOTALS:</td>
                        <td id="totalOpening">0</td>
                        <td id="totalUsed">0</td>
                        <td id="totalClosing">0</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

    container.innerHTML = html;
    attachEventListeners();
}

function attachEventListeners() {
    document.querySelectorAll('.qty-sold').forEach(input => {
        input.addEventListener('input', function() {
            const menuIndex = parseInt(this.dataset.menuIndex);
            calculateMenuRow(menuIndex);
            calculateAllUsage();
            calculateTotals();
            calculateSummaryTotals();
        });
    });

    document.querySelectorAll('.selling-price').forEach(input => {
        input.addEventListener('input', function() {
            const menuIndex = parseInt(this.dataset.menuIndex);
            calculateMenuRow(menuIndex);
            calculateSummaryTotals();
        });
    });

    document.querySelectorAll('.opening-stock-input').forEach(input => {
        input.addEventListener('input', function() {
            const stockIndex = parseInt(this.dataset.stockIndex);
            const value = parseFloat(this.value) || 0;
            const used = parseFloat(document.querySelector(`.stock-used-hidden-${stockIndex}`)?.value) || 0;
            let closing = value - used;
            if (closing < 0) closing = 0;

            document.querySelector(`.stock-closing-display-${stockIndex}`).innerText = closing;
            document.querySelector(`.stock-closing-hidden-${stockIndex}`).value = closing;
            document.querySelector(`.stock-opening-hidden-${stockIndex}`).value = value;

            calculateTotals();
        });
    });
}

function calculateMenuRow(menuIndex) {
    const item = menuItemsData[menuIndex];
    const ingredients = item.ingredients;
    const qtySold = parseFloat(document.querySelector(`.qty-sold[data-menu-index="${menuIndex}"]`)?.value) || 0;
    const sellingPrice = parseFloat(document.querySelector(`.selling-price[data-menu-index="${menuIndex}"]`)?.value) || 0;

    let totalCogs = 0;

    for (let ig = 0; ig < ingredients.length; ig++) {
        const quantityRequired = ingredients[ig].quantity_required;
        const unitCost = ingredients[ig].unit_cost;
        const used = qtySold * quantityRequired;

        document.querySelector(`.used-hidden-${menuIndex}-${ig}`).value = used;
        totalCogs += used * unitCost;
    }

    const totalRevenue = qtySold * sellingPrice;
    const profit = totalRevenue - totalCogs;
    const profitMargin = totalRevenue > 0 ? (profit / totalRevenue) * 100 : 0;

    document.querySelector(`.cogs-display-${menuIndex}`).innerText = formatMoney(totalCogs);
    document.querySelector(`.margin-display-${menuIndex}`).innerText = profitMargin.toFixed(1) + '%';
    document.querySelector(`.profit-display-${menuIndex}`).innerText = formatMoney(profit);
}

function calculateAllUsage() {
    for (let si = 0; si < stockItemsData.length; si++) {
        document.querySelector(`.stock-used-hidden-${si}`).value = 0;
    }

    for (let mi = 0; mi < menuItemsData.length; mi++) {
        const qtySold = parseFloat(document.querySelector(`.qty-sold[data-menu-index="${mi}"]`)?.value) || 0;
        if (qtySold === 0) continue;

        const ingredients = menuItemsData[mi].ingredients;

        for (let ig = 0; ig < ingredients.length; ig++) {
            const ing = ingredients[ig];
            const inventoryItemId = ing.inventory_item_id;
            const used = qtySold * ing.quantity_required;

            for (let si = 0; si < stockItemsData.length; si++) {
                if (stockItemsData[si].inventory_item_id == inventoryItemId) {
                    const currentUsed = parseFloat(document.querySelector(`.stock-used-hidden-${si}`)?.value) || 0;
                    const newUsed = currentUsed + used;
                    document.querySelector(`.stock-used-hidden-${si}`).value = newUsed;
                    document.querySelector(`.stock-used-display-${si}`).innerText = newUsed.toFixed(2);

                    const opening = parseFloat(document.querySelector(`.opening-stock-input[data-stock-index="${si}"]`)?.value) || 0;
                    let closing = opening - newUsed;
                    if (closing < 0) closing = 0;
                    document.querySelector(`.stock-closing-display-${si}`).innerText = closing;
                    document.querySelector(`.stock-closing-hidden-${si}`).value = closing;
                    break;
                }
            }
        }
    }
}

function calculateTotals() {
    let totalOpening = 0, totalUsed = 0, totalClosing = 0;

    for (let si = 0; si < stockItemsData.length; si++) {
        totalOpening += parseFloat(document.querySelector(`.opening-stock-input[data-stock-index="${si}"]`)?.value) || 0;
        totalUsed += parseFloat(document.querySelector(`.stock-used-hidden-${si}`)?.value) || 0;
        totalClosing += parseFloat(document.querySelector(`.stock-closing-hidden-${si}`)?.value) || 0;
    }

    document.getElementById('totalOpening').innerText = totalOpening.toFixed(2);
    document.getElementById('totalUsed').innerText = totalUsed.toFixed(2);
    document.getElementById('totalClosing').innerText = totalClosing.toFixed(2);
}

function calculateSummaryTotals() {
    let totalSales = 0;
    let totalCogs = 0;
    let totalProfit = 0;
    let itemCount = 0;

    for (let mi = 0; mi < menuItemsData.length; mi++) {
        const qtySold = parseFloat(document.querySelector(`.qty-sold[data-menu-index="${mi}"]`)?.value) || 0;
        if (qtySold === 0) continue;

        const sellingPrice = parseFloat(document.querySelector(`.selling-price[data-menu-index="${mi}"]`)?.value) || 0;
        const salesAmount = qtySold * sellingPrice;

        const cogsText = document.querySelector(`.cogs-display-${mi}`)?.innerText || '0 UGX';
        const cogs = parseFloat(cogsText.replace(' UGX', '').replace(/,/g, '')) || 0;

        totalSales += salesAmount;
        totalCogs += cogs;
        totalProfit += (salesAmount - cogs);
        itemCount++;
    }

    const avgMargin = totalSales > 0 ? (totalProfit / totalSales) * 100 : 0;
    const avgProfit = itemCount > 0 ? totalProfit / itemCount : 0;

    document.getElementById('totalSalesAmount').innerText = formatMoney(totalSales);
    document.getElementById('totalCogsAmount').innerText = formatMoney(totalCogs);
    document.getElementById('totalProfitAmount').innerText = formatMoney(totalProfit);
    document.getElementById('avgMarginDisplay').innerText = avgMargin.toFixed(1) + '%';
    document.getElementById('avgProfitDisplay').innerText = formatMoney(avgProfit);
}

function formatMoney(amount) {
    return Math.round(parseFloat(amount) || 0).toLocaleString('en-UG') + ' UGX';
}

function showLoading() {
    document.getElementById('itemsContainer').innerHTML = `<div class="text-center py-8 text-gray-400 w-full"><i class="fas fa-spinner fa-spin text-2xl mb-2"></i><p>Loading menu items...</p></div>`;
}

function showEmpty(message) {
    document.getElementById('itemsContainer').innerHTML = `<div class="text-center py-8 text-gray-400 border-2 border-dashed rounded-lg w-full"><i class="fas fa-utensils text-3xl mb-2 block"></i><p>${message}</p></div>`;
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

// Form submission
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
            const unitCost = parseFloat(document.querySelector(`.unit-cost-hidden-${mi}-${ig}`)?.value) || 0;

            let openingStock = 0, closingStock = 0;
            for (let si = 0; si < stockItemsData.length; si++) {
                if (stockItemsData[si].inventory_item_id == inventoryItemId) {
                    openingStock = parseFloat(document.querySelector(`.opening-stock-input[data-stock-index="${si}"]`)?.value) || 0;
                    closingStock = parseFloat(document.querySelector(`.stock-closing-hidden-${si}`)?.value) || 0;
                    break;
                }
            }

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
