@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Cashier Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">View table status and manage bills</p>
    </div>

    {{-- Search Bar --}}
    <div class="mb-4">
        <div class="relative max-w-xs">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchTable" placeholder="Search table by number..."
                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Total Tables</p>
                    <p class="text-xl font-bold text-gray-800">{{ $tables->count() }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chair text-blue-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Occupied</p>
                    <p class="text-xl font-bold text-gray-800">{{ $tables->where('is_occupied', 1)->count() }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-chair text-yellow-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Awaiting Payment</p>
                    <p class="text-xl font-bold text-gray-800" id="awaitingPaymentCount">0</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-receipt text-red-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Available</p>
                    <p class="text-xl font-bold text-gray-800">{{ $tables->where('is_occupied', 0)->count() }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-chair text-green-600 text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Tables Grid --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
            <h2 class="text-md font-semibold text-gray-800">
                <i class="fas fa-chair text-orange-500 mr-2"></i>
                Tables Status
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                <span class="inline-block w-3 h-3 rounded-full bg-gray-300 mr-1"></span> Available &nbsp;
                <span class="inline-block w-3 h-3 rounded-full bg-yellow-400 mr-1 ml-2"></span> Occupied &nbsp;
                <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-1 ml-2"></span> Awaiting Payment
            </p>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-2" id="tablesGrid">
                @foreach($tables as $table)
                @php
                    $orderForTable = $orders->get($table->id);
                    $statusClass = '';
                    $statusText = '';
                    $badgeClass = '';

                    if (!$table->is_occupied && !$table->is_reserved) {
                        $statusClass = 'border-gray-200 bg-white';
                        $statusText = 'Available';
                        $badgeClass = 'bg-green-100 text-green-700';
                    } elseif ($table->is_reserved && !$table->is_occupied) {
                        $statusClass = 'border-blue-500 bg-blue-50';
                        $statusText = 'Reserved';
                        $badgeClass = 'bg-blue-100 text-blue-700';
                    } elseif ($orderForTable && $orderForTable->is_printed == 1 && $orderForTable->payment_status == 'unpaid') {
                        $statusClass = 'border-red-500 bg-red-50';
                        $statusText = 'Awaiting Payment';
                        $badgeClass = 'bg-red-100 text-red-700';
                    } else {
                        $statusClass = 'border-yellow-500 bg-yellow-50';
                        $statusText = 'Occupied';
                        $badgeClass = 'bg-yellow-100 text-yellow-700';
                    }
                @endphp
                <div class="table-card rounded-lg border-2 p-2 text-center transition-all cursor-pointer hover:shadow-md {{ $statusClass }}"
                     data-table-id="{{ $table->id }}"
                     data-table-number="{{ $table->table_number }}"
                     onclick="viewBill({{ $table->id }})">
                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center mb-1
                                {{ $table->is_occupied || $table->is_reserved ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-500' }}">
                        <i class="fas fa-chair text-sm"></i>
                    </div>
                    <p class="font-bold text-xs text-gray-800">Table {{ $table->table_number }}</p>
                    <p class="text-xs text-gray-500">{{ $table->capacity }} seats</p>
                    <span class="inline-block mt-1 px-1.5 py-0.5 text-xs rounded-full {{ $badgeClass }}">
                        {{ $statusText }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    let allTables = @json($tables);
    let ordersData = @json($orders);

    function viewBill(tableId) {
        window.location.href = '/cashier/bills';
    }

    // Live search
    const searchInput = document.getElementById('searchTable');
    const awaitingPaymentCount = document.getElementById('awaitingPaymentCount');

    function updateAwaitingPaymentCount() {
        let count = 0;
        document.querySelectorAll('.table-card').forEach(card => {
            if (card.classList.contains('border-red-500')) {
                count++;
            }
        });
        if (awaitingPaymentCount) {
            awaitingPaymentCount.textContent = count;
        }
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.table-card');

            cards.forEach(card => {
                const tableNumber = card.getAttribute('data-table-number');
                if (tableNumber.toLowerCase().includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            updateAwaitingPaymentCount();
        });
    }

    setTimeout(updateAwaitingPaymentCount, 100);
</script>
@endsection
