{{-- resources/views/bar/cashier/pos.blade.php --}}

@extends('layouts.bar-cashier')

@section('title', 'Bar Point of Sale')

@section('page-title', 'Point of Sale')

@section('content')
<div class="flex gap-4 min-h-[calc(100vh-120px)]">

    {{-- LEFT PANEL: Items --}}
    <div class="flex-[2] bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
        <div class="p-3 border-b border-gray-200 bg-gray-50">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search products..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
        </div>

        <div class="p-2 border-b border-gray-200">
            <div class="flex flex-wrap gap-2">
                <button class="category-filter active px-4 py-1.5 text-sm rounded-full bg-orange-600 text-white transition" data-category="all">
                    <i class="fas fa-th-large mr-1"></i> All Products
                </button>
                @foreach($barCategories as $category)
                <button class="category-filter px-4 py-1.5 text-sm rounded-full bg-gray-200 text-gray-700 hover:bg-orange-600 hover:text-white transition" data-category="{{ $category->id }}">
                    <i class="fas fa-tag mr-1"></i> {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-2 max-h-[500px]" id="itemsList">
            {{-- Bar Products (Sellable Inventory Items) --}}
            <div class="category-group" data-group="products">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-1">
                    <i class="fas fa-wine-bottle mr-1"></i> Bar Products
                </h4>
                @foreach($sellableItems as $item)
                @php $stock = $currentStock[$item->id] ?? 0; @endphp
                <div class="product-item p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition mb-2"
                     data-name="{{ strtolower($item->name) }}"
                     data-category-id="{{ $item->category_id }}"
                     data-id="{{ $item->id }}"
                     data-type="inventory"
                     data-price="{{ $item->selling_price ?? 0 }}"
                     data-stock="{{ $stock }}"
                     data-unit="{{ $item->base_unit ?? 'piece' }}">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold text-gray-800">{{ $item->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                <i class="fas fa-cube mr-1"></i> {{ $item->base_unit ?? 'piece' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-orange-600">UGX {{ number_format($item->selling_price ?? 0, 0) }}</div>
                            <div class="text-xs {{ $stock > 10 ? 'text-green-600' : ($stock > 0 ? 'text-orange-600' : 'text-red-600') }} mt-0.5">
                                <i class="fas fa-boxes mr-1"></i> Stock: {{ $stock }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- RIGHT PANEL: Cart --}}
    <div class="w-96 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
        <div class="p-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-shopping-cart mr-2"></i> Current Order
            </h3>
            <span class="text-xs text-gray-500" id="itemCount">0 items</span>
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-2 max-h-[400px]" id="cartItems">
            <div class="text-center text-gray-400 py-8" id="emptyCartMsg">
                <i class="fas fa-shopping-cart text-4xl mb-2 block"></i>
                <p>Cart is empty</p>
                <p class="text-xs mt-1">Click on products to add</p>
            </div>
        </div>

        <div class="border-t border-gray-200 p-3 space-y-3">
            <div class="flex justify-between text-lg font-bold">
                <span>Total:</span>
                <span class="text-orange-600" id="total">UGX 0</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Order Type</label>
                    <select id="customerType" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                        <option value="dine_in">🍽️ Dine In</option>
                        <option value="takeaway">🥡 Takeaway</option>
                        <option value="delivery">🚚 Delivery</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="invoiceBtn" class="w-full bg-orange-600 text-white py-2 rounded-lg font-semibold hover:bg-orange-700 transition flex items-center justify-center gap-2">
                        <i class="fas fa-file-invoice"></i> Invoice (Pay Later)
                    </button>
                </div>
            </div>

            <button id="payNowBtn" class="w-full bg-green-600 text-white py-2.5 rounded-lg font-semibold hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-credit-card"></i> Pay Now
            </button>

            <button id="clearCartBtn" class="w-full bg-gray-200 text-gray-700 py-2.5 rounded-lg font-semibold hover:bg-gray-300 transition flex items-center justify-center gap-2">
                <i class="fas fa-trash-alt"></i> Clear Cart
            </button>
        </div>
    </div>
</div>

{{-- Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden no-print">
    <div class="bg-white rounded-xl shadow-xl w-96 max-w-full p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">
                <i class="fas fa-credit-card text-green-600"></i> Process Payment
            </h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-100 p-3 rounded text-center">
                <span class="text-sm text-gray-600">Total Amount</span>
                <div class="text-2xl font-bold text-orange-600" id="modalTotal">UGX 0</div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Payment Method</label>
                <select id="paymentMethod" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="cash">💵 Cash</option>
                    <option value="card">💳 Card</option>
                    <option value="mobile_money">📱 Mobile Money</option>
                </select>
            </div>

            <div id="cashSection">
                <label class="block text-sm font-medium mb-1">Amount Received</label>
                <input type="number" id="amountReceived" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Enter amount received">
                <div id="changeDue" class="mt-2 text-sm text-right"></div>
            </div>

            <button onclick="processPayment()" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700">
                <i class="fas fa-check-circle"></i> Complete Payment
            </button>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let currentStock = @json($currentStock);

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function updateCartDisplay() {
        const cartContainer = document.getElementById('cartItems');
        const emptyMsg = document.getElementById('emptyCartMsg');
        const totalSpan = document.getElementById('total');
        const itemCountSpan = document.getElementById('itemCount');

        if (!cartContainer) return;

        if (cart.length === 0) {
            cartContainer.innerHTML = '<div class="text-center text-gray-400 py-8" id="emptyCartMsg"><i class="fas fa-shopping-cart text-4xl mb-2 block"></i><p>Cart is empty</p><p class="text-xs mt-1">Click on products to add</p></div>';
            if (totalSpan) totalSpan.innerText = 'UGX 0';
            if (itemCountSpan) itemCountSpan.innerText = '0 items';
            return;
        }

        let cartHtml = '';
        let total = 0;
        let itemCount = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            itemCount += item.quantity;

            const stockInfo = `<span class="text-xs ${item.stock - item.quantity >= 5 ? 'text-green-600' : (item.stock - item.quantity > 0 ? 'text-orange-600' : 'text-red-600')} ml-2">
                <i class="fas fa-boxes mr-1"></i>Left: ${item.stock - item.quantity}
               </span>`;

            cartHtml += `
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="font-medium text-sm">${escapeHtml(item.name)}</div>
                        <button class="text-red-500 text-xs remove-item p-1" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <div class="text-xs text-gray-500">
                            <i class="fas fa-tag mr-1"></i> UGX ${item.price.toLocaleString()} × ${item.quantity}
                        </div>
                        <div class="font-semibold text-sm">UGX ${itemTotal.toLocaleString()}</div>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <button class="text-xs bg-gray-200 px-2 py-1 rounded quantity-minus" data-index="${index}">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="text-sm font-medium">${item.quantity}</span>
                        <button class="text-xs bg-gray-200 px-2 py-1 rounded quantity-plus" data-index="${index}">
                            <i class="fas fa-plus"></i>
                        </button>
                        ${stockInfo}
                    </div>
                </div>
            `;
        });

        cartContainer.innerHTML = cartHtml;
        if (totalSpan) totalSpan.innerText = `UGX ${total.toLocaleString()}`;
        if (itemCountSpan) itemCountSpan.innerText = `${itemCount} item${itemCount !== 1 ? 's' : ''}`;

        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.removeEventListener('click', handleRemoveItem);
            btn.addEventListener('click', handleRemoveItem);
        });

        document.querySelectorAll('.quantity-minus').forEach(btn => {
            btn.removeEventListener('click', handleQuantityMinus);
            btn.addEventListener('click', handleQuantityMinus);
        });

        document.querySelectorAll('.quantity-plus').forEach(btn => {
            btn.removeEventListener('click', handleQuantityPlus);
            btn.addEventListener('click', handleQuantityPlus);
        });
    }

    function handleRemoveItem(e) {
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index)) {
            cart.splice(index, 1);
            updateCartDisplay();
        }
    }

    function handleQuantityMinus(e) {
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && cart[index] && cart[index].quantity > 1) {
            cart[index].quantity--;
            updateCartDisplay();
        }
    }

    function handleQuantityPlus(e) {
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && cart[index]) {
            const maxAvailable = cart[index].stock;
            if (cart[index].quantity + 1 <= maxAvailable) {
                cart[index].quantity++;
                updateCartDisplay();
            } else {
                alert(`Only ${maxAvailable} ${cart[index].unit} available in stock!`);
            }
        }
    }

    function addToCart(item) {
        const existing = cart.find(i => i.id === item.id && i.type === item.type);

        // Check stock from requisitions
        const availableStock = currentStock[item.id] || 0;

        if (availableStock <= 0) {
            alert(`"${item.name}" is out of stock!`);
            return;
        }

        if (existing) {
            if (existing.quantity + 1 <= availableStock) {
                existing.quantity++;
                updateCartDisplay();
            } else {
                alert(`Only ${availableStock} ${item.unit} available in stock!`);
            }
        } else {
            cart.push({
                id: item.id,
                type: item.type,
                name: item.name,
                price: item.price,
                quantity: 1,
                stock: availableStock,
                unit: item.unit || 'units'
            });
            updateCartDisplay();
        }
    }

    // Product click handler
    document.querySelectorAll('.product-item').forEach(el => {
        el.addEventListener('click', () => {
            addToCart({
                id: parseInt(el.dataset.id),
                type: el.dataset.type,
                name: el.dataset.name,
                price: parseFloat(el.dataset.price),
                unit: el.dataset.unit || 'units',
                stock: parseInt(el.dataset.stock || 0)
            });
        });
    });

    // Category filter
    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.category-filter').forEach(b => {
                b.classList.remove('active', 'bg-orange-600', 'text-white');
                b.classList.add('bg-gray-200', 'text-gray-700');
            });
            btn.classList.add('active', 'bg-orange-600', 'text-white');
            btn.classList.remove('bg-gray-200', 'text-gray-700');

            const categoryId = btn.dataset.category;
            const products = document.querySelectorAll('.product-item');

            products.forEach(product => {
                if (categoryId === 'all' || product.dataset.categoryId == categoryId) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    });

    // Live search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const search = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.dataset.name;
                if (name && name.includes(search)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Clear cart
    const clearCartBtn = document.getElementById('clearCartBtn');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', () => {
            if (confirm('Clear entire cart?')) {
                cart = [];
                updateCartDisplay();
            }
        });
    }

    // Create Invoice (Pay Later)
    const invoiceBtn = document.getElementById('invoiceBtn');
    if (invoiceBtn) {
        invoiceBtn.addEventListener('click', async () => {
            if (cart.length === 0) {
                alert('Cart is empty');
                return;
            }

            invoiceBtn.disabled = true;
            invoiceBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Creating...';

            const items = cart.map(item => ({
                item_id: parseInt(item.id),
                item_type: item.type,
                quantity: item.quantity,
                unit_price: item.price
            }));

            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const customerType = document.getElementById('customerType');

            try {
                const response = await fetch('{{ route("bar.cashier.create-invoice") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        items: items,
                        total_amount: total,
                        customer_type: customerType ? customerType.value : 'dine_in'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    cart = [];
                    updateCartDisplay();
                    window.location.href = `{{ url("bar/cashier/invoice") }}/${data.order_id}`;
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                alert('Error creating invoice: ' + error.message);
            } finally {
                invoiceBtn.disabled = false;
                invoiceBtn.innerHTML = '<i class="fas fa-file-invoice"></i> Invoice (Pay Later)';
            }
        });
    }

    // Pay Now
    const payNowBtn = document.getElementById('payNowBtn');
    if (payNowBtn) {
        payNowBtn.addEventListener('click', () => {
            if (cart.length === 0) {
                alert('Cart is empty');
                return;
            }

            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('modalTotal').innerText = `UGX ${total.toLocaleString()}`;
            document.getElementById('paymentModal').classList.remove('hidden');
        });
    }

    // Payment Modal Functions
    function openPaymentModal() {
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('amountReceived').value = '';
        document.getElementById('changeDue').innerHTML = '';
    }

    // Amount received change calculation
    const amountReceived = document.getElementById('amountReceived');
    const changeDue = document.getElementById('changeDue');
    let modalTotal = 0;

    if (amountReceived) {
        amountReceived.addEventListener('input', function() {
            const total = parseFloat(document.getElementById('modalTotal').innerText.replace('UGX ', '').replace(/,/g, '')) || 0;
            const received = parseFloat(this.value) || 0;
            const diff = received - total;
            if (received <= 0) {
                changeDue.innerHTML = '';
            } else if (diff >= 0) {
                changeDue.innerHTML = `<span class="text-green-600 font-semibold">Change: UGX ${diff.toLocaleString()}</span>`;
            } else {
                changeDue.innerHTML = `<span class="text-red-600 font-semibold">Remaining: UGX ${Math.abs(diff).toLocaleString()}</span>`;
            }
        });
    }

    // Process Payment
    async function processPayment() {
        const method = document.getElementById('paymentMethod').value;
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let amountPaid = total;
        let changeAmount = 0;

        if (method === 'cash') {
            const received = parseFloat(document.getElementById('amountReceived').value) || 0;
            if (received < total) {
                alert('Insufficient amount received. Please enter the full amount.');
                return;
            }
            amountPaid = received;
            changeAmount = received - total;
        }

        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        const items = cart.map(item => ({
            item_id: parseInt(item.id),
            item_type: item.type,
            quantity: item.quantity,
            unit_price: item.price
        }));

        const customerType = document.getElementById('customerType');

        try {
            // Create invoice first
            const invoiceResponse = await fetch('{{ route("bar.cashier.create-invoice") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    items: items,
                    total_amount: total,
                    customer_type: customerType ? customerType.value : 'dine_in'
                })
            });

            const invoiceData = await invoiceResponse.json();

            if (!invoiceData.success) {
                alert('Error creating invoice: ' + invoiceData.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now';
                return;
            }

            // Then mark as paid
            const payResponse = await fetch(`{{ url("bar/cashier/sale") }}/${invoiceData.order_id}/pay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    payment_method: method,
                    amount_paid: amountPaid,
                    change_amount: changeAmount
                })
            });

            const payData = await payResponse.json();

            if (payData.success) {
                cart = [];
                updateCartDisplay();
                window.location.href = `{{ url("bar/cashier/receipt") }}/${invoiceData.order_id}`;
            } else {
                alert('Payment failed: ' + payData.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now';
            }
        } catch (error) {
            alert('Error: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now';
        }
    }
</script>
@endsection
