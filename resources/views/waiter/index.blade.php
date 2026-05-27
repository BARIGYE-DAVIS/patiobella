@extends('layouts.waiter')

@section('title', 'Waiter Dashboard')

@section('content')
<style>
    .toast { animation: slideIn 0.2s ease; }
    @keyframes slideIn { from { opacity:0; transform:translateX(16px); } to { opacity:1; transform:translateX(0); } }
    .table-row-item:hover:not(.occupied) { border-color:#EA580C; background:#FFF7ED; }
    .table-row-item.occupied { background:#FFFBEB; border-color:#FCD34D; cursor:not-allowed; }
    .cat-btn.active { background:#EA580C; color:#fff; border-color:#EA580C; }
    .product-row:hover { border-color:#EA580C; background:#FFF7ED; }
    .qty-btn:hover { border-color:#EA580C; color:#EA580C; }
    .menu-selector {
        transition: all 0.2s ease;
    }
    .menu-selector.active {
        background-color: #EA580C;
        color: white;
        border-color: #EA580C;
    }
    .menu-section {
        transition: all 0.3s ease;
    }
    .menu-section.hidden-section {
        display: none;
    }
    .warning-modal {
        animation: fadeIn 0.2s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<div id="toastContainer" class="fixed bottom-5 right-5 flex flex-col gap-2 z-50"></div>

{{-- Warning Modal for Stock Alerts --}}
<div id="warningModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-xl warning-modal">
        <div class="bg-yellow-500 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-yellow-600 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Stock Alert</h3>
                    <p class="text-sm text-yellow-100">Some items have low stock</p>
                </div>
            </div>
        </div>
        <div class="p-6" id="warningModalContent">
            <div class="space-y-3 text-sm text-gray-600">
                <!-- Warning content will be inserted here -->
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <button onclick="closeWarningModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                Close
            </button>
            <button onclick="proceedAfterWarning()" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-semibold transition">
                Continue Order
            </button>
        </div>
    </div>
</div>

<div class="min-h-screen bg-gray-50">

    {{-- HEADER --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40 px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-orange-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-concierge-bell text-white"></i>
            </div>
            <div>
                <p class="font-bold text-gray-800 text-sm leading-tight">Waiter Portal</p>
                <p class="text-xs text-gray-400">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }} · <span id="liveClock"></span></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="fetchActiveOrders()" class="relative w-9 h-9 border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:border-orange-500 hover:text-orange-600 transition">
                <i class="fas fa-bell text-sm"></i>
                <span id="orderCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-4 h-4 items-center justify-center hidden">0</span>
            </button>
            <button onclick="document.getElementById('logout-form').submit()" class="w-9 h-9 border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:border-red-400 hover:text-red-500 transition">
                <i class="fas fa-sign-out-alt text-sm"></i>
            </button>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    </div>

    <div class="p-6">

        {{-- TABLE VIEW --}}
        <div id="tableView" class="max-w-lg mx-auto">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Select Table</h2>
                    <p class="text-xs text-gray-400">Choose an available table to begin</p>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    <input type="text" id="tableSearchInput" placeholder="Search tables..."
                           class="pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 w-44"
                           oninput="filterTables(this.value)">
                </div>
            </div>

            <div class="flex gap-2 mb-4 flex-wrap">
                <span class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-white border border-gray-200 rounded-full text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    Free: <strong id="freeCount" class="text-gray-800">0</strong>
                </span>
                <span class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-white border border-gray-200 rounded-full text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-yellow-500 inline-block"></span>
                    Occupied: <strong id="busyCount" class="text-gray-800">0</strong>
                </span>
                <span class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-white border border-gray-200 rounded-full text-gray-500">
                    Total: <strong id="totalCount" class="text-gray-800">0</strong>
                </span>
            </div>

            <div class="flex flex-col gap-2" id="tablesListContainer">
                @foreach($tables as $table)
                <div class="table-row-item {{ $table->is_occupied ? 'occupied' : '' }} flex items-center gap-3 p-3 bg-white border-2 border-gray-200 rounded-xl cursor-pointer transition"
                     data-table-id="{{ $table->id }}"
                     data-table-number="{{ $table->table_number }}"
                     data-table-occupied="{{ $table->is_occupied ? 'true' : 'false' }}"
                     onclick="selectTable(this)">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $table->is_occupied ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600' }}">
                        <i class="fas fa-chair"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-800">Table {{ $table->table_number }}</p>
                        <p class="text-xs text-gray-400"><i class="fas fa-users mr-1"></i>{{ $table->capacity }} seats</p>
                    </div>
                    <span class="table-status-badge text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0
                        {{ $table->is_occupied ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                        {{ $table->is_occupied ? 'Occupied' : 'Available' }}
                    </span>
                    <i class="fas fa-chevron-right text-gray-300 text-xs table-arrow {{ $table->is_occupied ? 'invisible' : '' }}"></i>
                </div>
                @endforeach
            </div>

            <div id="noTablesFound" class="hidden text-center py-12 text-gray-400">
                <i class="fas fa-search text-3xl mb-3 block opacity-30"></i>
                <p class="text-sm">No tables match your search</p>
            </div>
        </div>

        {{-- ORDER VIEW --}}
        <div id="orderView" class="hidden">
            {{-- Top bar --}}
            <div class="flex items-center gap-3 mb-5 flex-wrap">
                <button onclick="backToTables()" class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:border-orange-500 hover:text-orange-600 transition bg-white">
                    <i class="fas fa-arrow-left text-xs"></i> Tables
                </button>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        New Order
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 border border-orange-200 font-semibold">
                            <i class="fas fa-chair" style="font-size:10px"></i> Table <span id="selectedTableNumberDisplay"></span>
                        </span>
                    </h2>
                    <p class="text-xs text-gray-400">Select a menu, then add items</p>
                </div>
            </div>

            {{-- Menu Selector --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-2">Select Menu</label>
                <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide" id="menusContainer">
                    @foreach($menus as $menu)
                    <button class="menu-selector px-4 py-2 text-sm font-medium rounded-xl border-2 border-gray-200 bg-white text-gray-700 whitespace-nowrap transition-all hover:border-orange-300"
                            data-menu-id="{{ $menu->id }}"
                            data-menu-name="{{ $menu->name }}"
                            onclick="selectMenu({{ $menu->id }}, '{{ $menu->name }}')">
                        <i class="fas fa-utensils mr-1"></i> {{ $menu->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Categories and Items Section (Hidden until menu selected) --}}
            <div id="menuContentSection" class="hidden-section hidden">
                <div class="flex gap-5 items-start">

                    {{-- LEFT: Menu --}}
                    <div class="flex-1 min-w-0 flex flex-col gap-4">

                        {{-- Categories --}}
                        <div class="bg-white border border-gray-200 rounded-xl p-3">
                            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide" id="categoriesContainer">
                                <button class="cat-btn text-xs px-3 py-1.5 rounded-full border-2 border-gray-200 text-gray-600 whitespace-nowrap transition font-medium" data-category="all" onclick="filterByCategory('all')">All</button>
                                @foreach($categories as $category)
                                <button class="cat-btn text-xs px-3 py-1.5 rounded-full border-2 border-gray-200 text-gray-600 whitespace-nowrap transition font-medium" data-category="{{ $category->id }}" onclick="filterByCategory({{ $category->id }})">{{ $category->name }}</button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Products --}}
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <span class="font-semibold text-sm text-gray-700">Menu Items</span>
                                <div class="relative">
                                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                    <input type="text" id="searchInput" placeholder="Search..."
                                           class="pl-7 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 w-36">
                                </div>
                            </div>
                            <div class="p-3 flex flex-col gap-1.5 max-h-[440px] overflow-y-auto" id="productsContainer">
                                <div class="text-center py-8 text-gray-400 text-sm">
                                    <i class="fas fa-utensils text-2xl mb-2 block opacity-30"></i>
                                    Select a menu to view items
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Cart --}}
                    <div class="w-80 flex-shrink-0 bg-white border border-gray-200 rounded-xl overflow-hidden sticky top-20 flex flex-col" style="max-height:calc(100vh - 100px)">
                        <div class="bg-orange-600 px-4 py-3 text-white">
                            <p class="font-bold text-sm"><i class="fas fa-receipt mr-2"></i>Current Order</p>
                            <p class="text-xs text-orange-100 mt-0.5">Table <span id="cartTableNumber"></span></p>
                        </div>

                        <div class="flex-1 overflow-y-auto p-3" id="cartContainer">
                            <div class="text-center py-10 text-gray-400">
                                <i class="fas fa-shopping-basket text-3xl mb-2 block opacity-20"></i>
                                <p class="text-sm">No items yet</p>
                                <p class="text-xs mt-1">Tap menu items to add</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 p-3 bg-gray-50">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-500 font-medium">Total</span>
                                <span class="text-xl font-black text-orange-600" id="cartTotal">UGX 0</span>
                            </div>
                            <textarea id="orderNotes" rows="2" placeholder="Special notes..."
                                      class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-orange-500 resize-none mb-2"></textarea>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="clearCart()" class="py-2 text-xs font-semibold border border-gray-200 rounded-lg text-gray-500 hover:border-red-300 hover:text-red-500 transition bg-white">
                                    <i class="fas fa-trash-alt mr-1"></i>Clear
                                </button>
                                <button id="placeOrderBtn" onclick="submitOrder()" class="py-2 text-xs font-bold bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                                    <i class="fas fa-paper-plane mr-1"></i>Place Order
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

{{-- ITEM MODAL --}}
<div id="itemModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-bold text-gray-800 text-base" id="modalItemName"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times"></i></button>
        </div>

        <div class="mb-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Quantity</p>
            <div class="flex items-center gap-3">
                <button onclick="decreaseQuantity()" class="w-9 h-9 border-2 border-gray-200 rounded-lg flex items-center justify-center text-gray-600 hover:border-orange-500 hover:text-orange-600 transition font-bold text-lg">−</button>
                <span id="modalQuantity" class="text-2xl font-black w-10 text-center text-gray-800">1</span>
                <button onclick="increaseQuantity()" class="w-9 h-9 border-2 border-gray-200 rounded-lg flex items-center justify-center text-gray-600 hover:border-orange-500 hover:text-orange-600 transition font-bold text-lg">+</button>
            </div>
        </div>

        <div class="mb-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Supplement <span class="normal-case text-gray-400 font-normal">(optional)</span></p>
            <input type="text" id="modalSupplement" placeholder="e.g. Extra cheese, Less ice..."
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button onclick="closeModal()" class="py-2.5 border border-gray-200 rounded-lg text-sm font-semibold text-gray-500 hover:bg-gray-50 transition">Cancel</button>
            <button onclick="addToCartFromModal()" class="py-2.5 bg-orange-600 text-white rounded-lg text-sm font-bold hover:bg-orange-700 transition">
                <i class="fas fa-plus mr-1"></i>Add to Order
            </button>
        </div>
    </div>
</div>

<script>
    let selectedTableId = null, selectedTableNumber = null;
    let selectedMenuId = null, selectedMenuName = null;
    let cart = [], currentCategory = 'all', currentProduct = null, modalQuantity = 1;
    let pendingOrderCallback = null;

    // Clock
    function updateClock() {
        document.getElementById('liveClock').textContent = new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
    }
    updateClock(); setInterval(updateClock, 10000);

    // Table stats
    function updateTableStats() {
        const rows = document.querySelectorAll('.table-row-item');
        let free = 0, busy = 0;
        rows.forEach(r => r.getAttribute('data-table-occupied') === 'true' ? busy++ : free++);
        document.getElementById('freeCount').textContent = free;
        document.getElementById('busyCount').textContent = busy;
        document.getElementById('totalCount').textContent = rows.length;
    }
    updateTableStats();

    // Table search
    function filterTables(query) {
        const q = query.toLowerCase().trim();
        let visible = 0;
        document.querySelectorAll('.table-row-item').forEach(row => {
            const match = !q || row.getAttribute('data-table-number').toLowerCase().includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('noTablesFound').classList.toggle('hidden', visible > 0);
    }

    // Warning Modal functions
    let pendingOrderData = null;

    function showWarningModal(warningMessage, warnings, orderData) {
        pendingOrderData = orderData;

        const modal = document.getElementById('warningModal');
        const content = document.getElementById('warningModalContent');

        let html = '<div class="space-y-3 text-sm">';

        // Group warnings by department and item
        let grouped = {};
        warnings.forEach(w => {
            let key = `${w.department} - ${w.item_name}`;
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(w);
        });

        for (let [itemKey, items] of Object.entries(grouped)) {
            html += `<div class="border-l-4 border-yellow-400 pl-3 py-2">`;
            html += `<div class="font-semibold text-gray-800 mb-1">📌 ${itemKey}</div>`;
            items.forEach(w => {
                if (w.type === 'recipe') {
                    html += `<div class="text-xs text-gray-600 ml-2 mb-1">`;
                    html += `<i class="fas fa-utensils mr-1 text-yellow-500"></i>`;
                    html += `Missing <span class="font-medium">${w.ingredient}</span>: Need ${w.required} ${w.unit}, `;
                    html += `<span class="text-red-600">Only ${w.available} ${w.unit} available</span> in ${w.department}`;
                    html += `</div>`;
                } else {
                    html += `<div class="text-xs text-gray-600 ml-2 mb-1">`;
                    html += `<i class="fas fa-glass-cheers mr-1 text-yellow-500"></i>`;
                    html += `Low stock on <span class="font-medium">${w.item_name}</span>: Need ${w.required} ${w.unit}(s), `;
                    html += `<span class="text-red-600">Only ${w.available} ${w.unit}(s) available</span> in ${w.department}`;
                    html += `</div>`;
                }
            });
            html += `<div class="text-xs text-yellow-600 mt-1 ml-2">→ Order will still be processed. Items will be restocked soon.</div>`;
            html += `</div>`;
        }

        html += '</div>';
        content.innerHTML = html;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeWarningModal() {
        document.getElementById('warningModal').classList.add('hidden');
        document.getElementById('warningModal').classList.remove('flex');
        pendingOrderData = null;
    }

    function proceedAfterWarning() {
        closeWarningModal();
        if (pendingOrderData) {
            finalizeOrder(pendingOrderData);
        }
    }

    function finalizeOrder(data) {
        // Clear cart and reset UI
        cart = [];
        document.getElementById('orderNotes').value = '';
        renderCart();

        document.getElementById('orderView').classList.add('hidden');
        document.getElementById('tableView').classList.remove('hidden');

        const row = document.querySelector(`.table-row-item[data-table-id="${selectedTableId}"]`);
        if (row) {
            row.setAttribute('data-table-occupied','true');
            row.classList.add('occupied');
            row.querySelector('.table-status-badge').textContent = 'Occupied';
            row.querySelector('.table-status-badge').className = 'table-status-badge text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 bg-yellow-100 text-yellow-700';
            row.querySelector('.table-arrow')?.classList.add('invisible');
            const icon = row.querySelector('.w-10');
            if (icon) {
                icon.className = 'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-yellow-100 text-yellow-600';
            }
        }

        selectedTableId = selectedTableNumber = null;
        selectedMenuId = selectedMenuName = null;
        updateTableStats();
        fetchActiveOrders();

        // Reset menu UI
        document.querySelectorAll('.menu-selector').forEach(btn => {
            btn.classList.remove('active', 'bg-orange-600', 'text-white', 'border-orange-600');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });

        const menuContentSection = document.getElementById('menuContentSection');
        menuContentSection.classList.add('hidden-section', 'hidden');
        menuContentSection.classList.remove('block');

        showToast(data.message || `Order #${data.order_number} placed! UGX ${data.total.toLocaleString()}`, 'success');
    }

    // Menu selection
    function selectMenu(menuId, menuName) {
        selectedMenuId = menuId;
        selectedMenuName = menuName;

        // Update UI for menu buttons
        document.querySelectorAll('.menu-selector').forEach(btn => {
            btn.classList.remove('active', 'bg-orange-600', 'text-white', 'border-orange-600');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });
        const activeBtn = document.querySelector(`.menu-selector[data-menu-id="${menuId}"]`);
        activeBtn.classList.add('active', 'bg-orange-600', 'text-white', 'border-orange-600');
        activeBtn.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');

        // Show the menu content section (categories and items)
        const menuContentSection = document.getElementById('menuContentSection');
        menuContentSection.classList.remove('hidden-section', 'hidden');
        menuContentSection.classList.add('block');

        // Reset category to 'all' and load products
        currentCategory = 'all';
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-category') == 'all');
        });

        loadProductsByCategory('all');
    }

    // Load products by category with selected menu filter
    function loadProductsByCategory(categoryId) {
        if (!selectedMenuId) {
            document.getElementById('productsContainer').innerHTML = `
                <div class="text-center py-8 text-gray-400 text-sm">
                    <i class="fas fa-utensils text-2xl mb-2 block opacity-30"></i>
                    Select a menu first
                </div>`;
            return;
        }

        let url = '{{ url("waiter/products/category") }}/' + categoryId + '?menu_id=' + selectedMenuId;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(renderProducts)
            .catch(e => {
                console.error(e);
                document.getElementById('productsContainer').innerHTML = `
                    <div class="text-center py-8 text-red-400 text-sm">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2 block"></i>
                        Failed to load items
                    </div>`;
            });
    }

    // Select table
    function selectTable(el) {
        if (el.getAttribute('data-table-occupied') === 'true') {
            showToast('Table is occupied. Choose a free table.', 'error');
            return;
        }
        selectedTableId = el.getAttribute('data-table-id');
        selectedTableNumber = el.getAttribute('data-table-number');
        document.getElementById('selectedTableNumberDisplay').textContent = selectedTableNumber;
        document.getElementById('cartTableNumber').textContent = selectedTableNumber;
        document.getElementById('tableView').classList.add('hidden');
        document.getElementById('orderView').classList.remove('hidden');

        // Reset menu selection when entering order view
        selectedMenuId = null;
        selectedMenuName = null;
        document.querySelectorAll('.menu-selector').forEach(btn => {
            btn.classList.remove('active', 'bg-orange-600', 'text-white', 'border-orange-600');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });

        // Hide menu content section
        const menuContentSection = document.getElementById('menuContentSection');
        menuContentSection.classList.add('hidden-section', 'hidden');
        menuContentSection.classList.remove('block');

        document.getElementById('productsContainer').innerHTML = `
            <div class="text-center py-8 text-gray-400 text-sm">
                <i class="fas fa-utensils text-2xl mb-2 block opacity-30"></i>
                Select a menu to view items
            </div>`;
    }

    function backToTables() {
        if (cart.length > 0 && !confirm('Clear order and go back?')) return;
        cart = [];
        selectedMenuId = null;
        selectedMenuName = null;
        renderCart();
        document.getElementById('orderView').classList.add('hidden');
        document.getElementById('tableView').classList.remove('hidden');
        document.getElementById('tableSearchInput').value = '';
        filterTables('');
        selectedTableId = selectedTableNumber = null;

        // Reset menu UI
        document.querySelectorAll('.menu-selector').forEach(btn => {
            btn.classList.remove('active', 'bg-orange-600', 'text-white', 'border-orange-600');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });

        const menuContentSection = document.getElementById('menuContentSection');
        menuContentSection.classList.add('hidden-section', 'hidden');
        menuContentSection.classList.remove('block');
    }

    // Modal
    function openItemModal(el) {
        currentProduct = {
            id: el.getAttribute('data-product-id'),
            name: el.getAttribute('data-product-name'),
            price: parseFloat(el.getAttribute('data-product-price'))
        };
        modalQuantity = 1;
        document.getElementById('modalItemName').textContent = currentProduct.name;
        document.getElementById('modalQuantity').textContent = 1;
        document.getElementById('modalSupplement').value = '';
        document.getElementById('itemModal').classList.remove('hidden');
        document.getElementById('itemModal').classList.add('flex');
    }
    function increaseQuantity() { document.getElementById('modalQuantity').textContent = ++modalQuantity; }
    function decreaseQuantity() { if (modalQuantity > 1) document.getElementById('modalQuantity').textContent = --modalQuantity; }
    function closeModal() { document.getElementById('itemModal').classList.add('hidden'); document.getElementById('itemModal').classList.remove('flex'); }
    document.getElementById('itemModal').addEventListener('click', e => { if (e.target === document.getElementById('itemModal')) closeModal(); });

    function addToCartFromModal() {
        const supplement = document.getElementById('modalSupplement').value.trim();
        const existing = cart.find(i => i.id === currentProduct.id && i.supplement === supplement);
        if (existing) { existing.quantity += modalQuantity; }
        else { cart.push({ id: currentProduct.id, name: currentProduct.name, price: currentProduct.price, quantity: modalQuantity, supplement }); }
        renderCart();
        showToast(`${currentProduct.name} added`, 'success');
        closeModal();
    }

    // Render cart
    function renderCart() {
        const container = document.getElementById('cartContainer');
        const totalEl = document.getElementById('cartTotal');
        if (!cart.length) {
            container.innerHTML = `<div class="text-center py-10 text-gray-400"><i class="fas fa-shopping-basket text-3xl mb-2 block opacity-20"></i><p class="text-sm">No items yet</p></div>`;
            totalEl.textContent = 'UGX 0'; return;
        }
        let total = 0, html = '';
        cart.forEach((item, i) => {
            const sub = item.price * item.quantity; total += sub;
            html += `<div class="flex items-start gap-2 p-2.5 border border-gray-100 rounded-lg bg-gray-50 mb-1.5">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">${escapeHtml(item.name)}</p>
                    <p class="text-xs text-gray-400">UGX ${item.price.toLocaleString()}</p>
                    ${item.supplement ? `<p class="text-xs text-orange-500 mt-0.5"><i class="fas fa-plus-circle mr-1" style="font-size:9px"></i>${escapeHtml(item.supplement)}</p>` : ''}
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <button onclick="updateQuantity(${i},-1)" class="qty-btn w-5 h-5 border border-gray-200 rounded text-xs flex items-center justify-center bg-white transition"><i class="fas fa-minus" style="font-size:9px"></i></button>
                        <span class="text-xs font-bold w-5 text-center">${item.quantity}</span>
                        <button onclick="updateQuantity(${i},1)" class="qty-btn w-5 h-5 border border-gray-200 rounded text-xs flex items-center justify-center bg-white transition"><i class="fas fa-plus" style="font-size:9px"></i></button>
                        <span class="flex-1 text-right text-xs font-bold text-orange-600">UGX ${sub.toLocaleString()}</span>
                        <button onclick="removeItem(${i})" class="text-gray-300 hover:text-red-500 transition ml-1"><i class="fas fa-times text-xs"></i></button>
                    </div>
                </div>
            </div>`;
        });
        container.innerHTML = html;
        totalEl.textContent = `UGX ${total.toLocaleString()}`;
    }

    function updateQuantity(i, d) { const q = cart[i].quantity + d; if (q <= 0) cart.splice(i,1); else cart[i].quantity = q; renderCart(); }
    function removeItem(i) { cart.splice(i,1); renderCart(); showToast('Item removed','info'); }
    function clearCart() { if (!cart.length) return; if (confirm('Clear all items?')) { cart=[]; renderCart(); showToast('Cart cleared','info'); } }

    // Submit order
    function submitOrder() {
        if (!cart.length) { showToast('Add items first.', 'error'); return; }
        const btn = document.getElementById('placeOrderBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Placing...';

        fetch('{{ route("waiter.place-order") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ table_id: selectedTableId, items: cart, notes: document.getElementById('orderNotes').value })
        })
        .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(data => {
            if (data.success) {
                // Check if there are stock warnings
                if (data.warnings && data.warnings.length > 0) {
                    // Show warning modal with details, then proceed after confirmation
                    showWarningModal(data.warning_message, data.warnings, data);
                } else {
                    // No warnings, proceed directly
                    finalizeOrder(data);
                }
            } else {
                showToast(data.message || 'Failed to place order.', 'error');
            }
        })
        .catch(e => {
            console.error(e);
            showToast('Something went wrong.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i>Place Order';
        });
    }

    // Products
    function filterByCategory(categoryId) {
        currentCategory = categoryId;
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-category') == categoryId);
        });
        loadProductsByCategory(categoryId);
    }

    function renderProducts(products) {
        const c = document.getElementById('productsContainer');
        if (!products.length) {
            c.innerHTML = '<div class="text-center py-8 text-gray-400 text-sm"><i class="fas fa-utensils text-2xl mb-2 block opacity-30"></i>No items found in this menu</div>';
            return;
        }
        c.innerHTML = products.map(p => `
            <div class="product-row flex items-center justify-between p-3 border-2 border-gray-200 rounded-lg cursor-pointer transition"
                 data-product-id="${p.id}" data-product-name="${escapeHtml(p.name)}" data-product-price="${p.selling_price}" onclick="openItemModal(this)">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-sm text-gray-800 truncate">${escapeHtml(p.name)}</p>
                    <p class="text-xs text-gray-400 mt-0.5">${p.menu_item_category?.name || 'General'}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                    <span class="text-sm font-bold text-orange-600">UGX ${parseInt(p.selling_price).toLocaleString()}</span>
                    <i class="fas fa-plus-circle text-orange-400 text-base"></i>
                </div>
            </div>`).join('');
    }

    // Search
    document.getElementById('searchInput').addEventListener('input', function() {
        const s = this.value.trim();
        if (s.length < 2) {
            if (selectedMenuId) {
                loadProductsByCategory(currentCategory);
            }
            return;
        }
        fetch(`{{ route("waiter.products.search") }}?search=${encodeURIComponent(s)}&menu_id=${selectedMenuId || ''}`)
            .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
            .then(renderProducts).catch(console.error);
    });

    // Active orders
    function fetchActiveOrders() {
        fetch('{{ route("waiter.active-orders") }}')
            .then(r => r.json())
            .then(orders => {
                const badge = document.getElementById('orderCount');
                if (orders && orders.length) {
                    badge.textContent = orders.length;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }
            }).catch(console.error);
    }
    fetchActiveOrders();
    setInterval(fetchActiveOrders, 30000);

    // Toast
    function showToast(msg, type='info') {
        const colors = { success:'border-green-500 text-green-700', error:'border-red-500 text-red-700', info:'border-blue-500 text-blue-700' };
        const icons = { success:'fa-check-circle', error:'fa-exclamation-circle', info:'fa-info-circle' };
        const t = document.createElement('div');
        t.className = `toast bg-white border-l-4 ${colors[type]} shadow-lg rounded-lg px-4 py-3 text-xs font-semibold flex items-center gap-2 max-w-xs`;
        t.innerHTML = `<i class="fas ${icons[type]}"></i><span>${msg}</span>`;
        document.getElementById('toastContainer').appendChild(t);
        setTimeout(() => t.remove(), 4000);
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;')
                   .replace(/</g, '&lt;')
                   .replace(/>/g, '&gt;')
                   .replace(/"/g, '&quot;')
                   .replace(/'/g, '&#39;');
    }
</script>
@endsection
