@extends('layouts.store')

@section('title', 'Process Return')

@section('page-title', 'Process Return')

@section('content')
<style>
    .compact-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .compact-header {
        background-color: #f9fafb;
        padding: 8px 12px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .compact-body {
        padding: 10px 12px;
    }
    .stat-issued { background: #d1fae5; color: #065f46; }
    .stat-returned { background: #fed7aa; color: #9c4221; }
    .stat-available { background: #dbeafe; color: #1e40af; }
    .unit-tag {
        background: #e5e7eb;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
    }
    .return-input {
        width: 100px;
        text-align: center;
        padding: 4px 6px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
    }
    .pack-input {
        width: 80px;
        text-align: center;
        padding: 4px 6px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 12px;
    }
    .calculation-detail {
        background: #fef3c7;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .issued-info-box {
        background: #dbeafe;
        border-left: 3px solid #2563eb;
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 12px;
    }
    .total-footer {
        background: #fef3c7;
        padding: 10px 15px;
        border-radius: 10px;
        margin-top: 15px;
    }
    .return-section {
        background: #f9fafb;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
    }
    .return-section-title {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .divider {
        border-top: 1px dashed #e5e7eb;
        margin: 12px 0;
        position: relative;
    }
    .divider span {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 0 10px;
        font-size: 10px;
        color: #9ca3af;
    }
</style>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200 bg-orange-50">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-md font-semibold text-gray-800">Return Items</h3>
                <p class="text-xs text-gray-500">{{ $requisition->requisition_number }} · {{ $requisition->department->name ?? 'Department' }}</p>
            </div>
        </div>
    </div>

    <div class="p-4">
        <form method="POST" action="{{ route('store.department-requisitions.process-return', $requisition->id) }}" id="returnForm">
            @csrf

            {{-- RETURNED BY SECTION --}}
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                <label for="returned_by" class="block text-sm font-semibold text-purple-800 mb-2">
                    <span class="text-red-500">*</span> Who is returning these items?
                </label>
                <input type="text"
                       name="returned_by"
                       id="returned_by"
                       value="{{ old('returned_by') }}"
                       class="w-full px-4 py-2 border border-purple-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white"
                       placeholder="Enter full name of the department staff returning the items"
                       required>
            </div>

            @foreach($requisition->items as $itemIndex => $item)
            @php
                // Get issued pack information
                $issuedPackType = $item->issued_pack_type;      // "carton"
                $issuedPackSize = $item->issued_pack_size;      // 12
                $quantityIssued = $item->quantity_issued;       // 5 cartons
                $quantityReturnedPacks = $item->quantity_returned ?? 0;  // already returned in packs

                // Calculate remaining in packs and pieces
                $remainingPacks = $quantityIssued - $quantityReturnedPacks;
                $remainingPieces = $remainingPacks * $issuedPackSize;

                $itemName = $item->inventoryItem->name ?? 'N/A';
                $baseUnit = $item->inventoryItem->base_unit ?? 'pieces';
                $hasPackInfo = !empty($issuedPackType) && $issuedPackSize > 0;
            @endphp
            <div class="compact-card">
                <div class="compact-header">
                    <div>
                        <span class="font-medium text-sm text-gray-800">{{ $itemName }}</span>
                    </div>
                    <span class="unit-tag">{{ $baseUnit }}</span>
                </div>
                <div class="compact-body">
                    {{-- Stats Row --}}
                    <div class="grid grid-cols-3 gap-2 mb-3 text-center">
                        <div class="bg-green-50 rounded-lg p-2">
                            <p class="text-xs text-gray-500">Issued</p>
                            <p class="text-sm font-bold text-green-700">{{ number_format($quantityIssued, 2) }} {{ $issuedPackType }}(s)</p>
                            <p class="text-xs text-gray-500">({{ number_format($quantityIssued * $issuedPackSize, 2) }} {{ $baseUnit }})</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-2">
                            <p class="text-xs text-gray-500">Already Returned</p>
                            <p class="text-sm font-bold text-orange-700">{{ number_format($quantityReturnedPacks, 2) }} {{ $issuedPackType }}(s)</p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-2">
                            <p class="text-xs text-gray-500">Available to Return</p>
                            <p class="text-sm font-bold text-blue-700">{{ number_format($remainingPacks, 2) }} {{ $issuedPackType }}(s)</p>
                            <p class="text-xs text-gray-500">({{ number_format($remainingPieces, 2) }} {{ $baseUnit }})</p>
                        </div>
                    </div>

                    <input type="hidden" name="items[{{ $itemIndex }}][item_id]" value="{{ $item->id }}">
                    <input type="hidden" name="items[{{ $itemIndex }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                    <input type="hidden" name="items[{{ $itemIndex }}][pack_size]" value="{{ $issuedPackSize }}" id="pack_size_{{ $itemIndex }}">

                    {{-- ISSUED PACK INFORMATION DISPLAY --}}
                    <div class="issued-info-box">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs font-semibold text-blue-800">Originally Issued As:</span>
                        </div>
                        <div class="text-sm mt-1">
                            <strong>{{ $quantityIssued }}</strong> {{ $issuedPackType }}(s) × <strong>{{ $issuedPackSize }}</strong> {{ $baseUnit }} per {{ $issuedPackType }}
                            = <strong>{{ $quantityIssued * $issuedPackSize }}</strong> {{ $baseUnit }}
                        </div>
                    </div>

                    {{-- RETURN SECTION --}}
                    <div class="return-section">
                        <div class="return-section-title">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Return in {{ ucfirst($issuedPackType) }}(s)
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="number"
                                   name="items[{{ $itemIndex }}][number_of_packs]"
                                   id="packs_{{ $itemIndex }}"
                                   class="pack-input border-gray-300 focus:border-orange-500"
                                   placeholder="Packs"
                                   value="0"
                                   min="0"
                                   max="{{ $remainingPacks }}"
                                   step="1"
                                   data-packsize="{{ $issuedPackSize }}"
                                   data-packtype="{{ $issuedPackType }}"
                                   data-baseunit="{{ $baseUnit }}"
                                   data-maxpacks="{{ $remainingPacks }}"
                                   oninput="calculateReturn({{ $itemIndex }})">
                            <span class="text-sm text-gray-600">{{ $issuedPackType }}(s)</span>
                            <span class="text-gray-400">→</span>
                            <span id="pack_return_pieces_{{ $itemIndex }}" class="text-sm font-semibold text-orange-600">0</span>
                            <span class="text-xs text-gray-500">{{ $baseUnit }}</span>
                        </div>
                    </div>

                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <div class="return-section">
                        <div class="return-section-title">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Return Individual {{ ucfirst($baseUnit) }}(s)
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="number"
                                   name="items[{{ $itemIndex }}][quantity_returned]"
                                   id="pieces_{{ $itemIndex }}"
                                   class="return-input border-gray-300 focus:border-orange-500"
                                   placeholder="Pieces"
                                   value="0"
                                   min="0"
                                   max="{{ $remainingPieces }}"
                                   step="1"
                                   data-maxpieces="{{ $remainingPieces }}"
                                   data-baseunit="{{ $baseUnit }}"
                                   oninput="calculatePieceReturn({{ $itemIndex }})">
                            <span class="text-sm text-gray-600">{{ $baseUnit }}(s)</span>
                        </div>
                    </div>

                    {{-- Calculation Display --}}
                    <div id="calc_display_{{ $itemIndex }}" class="calculation-detail mt-3 hidden"></div>
                    <div id="warning_{{ $itemIndex }}" class="text-xs text-red-500 mt-2 hidden"></div>

                    <input type="hidden" name="items[{{ $itemIndex }}][total_pieces]" id="total_pieces_{{ $itemIndex }}" value="0">
                    <input type="hidden" name="items[{{ $itemIndex }}][pack_return_total]" id="pack_return_total_{{ $itemIndex }}" value="0">
                    <input type="hidden" name="items[{{ $itemIndex }}][piece_return_total]" id="piece_return_total_{{ $itemIndex }}" value="0">

                    {{-- Return Reason --}}
                    <div class="mt-3">
                        <input type="text"
                               name="items[{{ $itemIndex }}][return_reason]"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                               placeholder="Reason for return (e.g., damaged, expired, wrong item, etc.)">
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Global Fields --}}
            <div class="grid grid-cols-2 gap-3 mt-4">
                <input type="text" name="global_return_reason" class="px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Global return reason">
                <textarea name="store_notes" rows="1" class="px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Store notes"></textarea>
            </div>

            {{-- Total Footer --}}
            <div class="total-footer">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Total to Return:</span>
                    <div>
                        <span id="grandTotalPacks" class="text-lg font-bold text-orange-600">0</span>
                        <span class="text-xs text-gray-500"> packs</span>
                        <span class="mx-2 text-gray-400">+</span>
                        <span id="grandTotalPieces" class="text-lg font-bold text-blue-600">0</span>
                        <span class="text-xs text-gray-500"> pieces</span>
                        <span class="mx-2 text-gray-400">=</span>
                        <span id="grandTotal" class="text-xl font-bold text-green-600">0</span>
                        <span class="text-xs text-gray-500"> total {{ $baseUnit }}</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3 mt-4">
                <a href="{{ route('store.department-requisitions.show', $requisition->id) }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700">Confirm Return</button>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateReturn(index) {
        const packsInput = document.getElementById(`packs_${index}`);
        const packSize = parseFloat(packsInput.getAttribute('data-packsize')) || 1;
        const packType = packsInput.getAttribute('data-packtype');
        const baseUnit = packsInput.getAttribute('data-baseunit');
        const maxPacks = parseFloat(packsInput.getAttribute('data-maxpacks')) || 0;

        let packs = parseFloat(packsInput.value) || 0;

        if (packs < 0) {
            packsInput.value = 0;
            packs = 0;
        }

        if (packs > maxPacks) {
            packsInput.value = maxPacks;
            packs = maxPacks;
        }

        const packReturnPieces = packs * packSize;
        document.getElementById(`pack_return_pieces_${index}`).innerText = packReturnPieces;
        document.getElementById(`pack_return_total_${index}`).value = packReturnPieces;

        // Get piece return value
        const piecesInput = document.getElementById(`pieces_${index}`);
        let pieces = parseFloat(piecesInput.value) || 0;
        const maxPieces = parseFloat(piecesInput.getAttribute('data-maxpieces')) || 0;

        // Validate total doesn't exceed available
        const totalReturning = packReturnPieces + pieces;
        if (totalReturning > maxPieces) {
            const warningDiv = document.getElementById(`warning_${index}`);
            warningDiv.innerHTML = `⚠️ Total returning (${totalReturning} ${baseUnit}) exceeds available (${maxPieces} ${baseUnit})`;
            warningDiv.classList.remove('hidden');
        } else {
            document.getElementById(`warning_${index}`).classList.add('hidden');
        }

        // Update calculation display
        const calcDiv = document.getElementById(`calc_display_${index}`);
        if (packs > 0 && pieces > 0) {
            calcDiv.innerHTML = `📊 ${packs} ${packType}(s) (${packReturnPieces} ${baseUnit}) + ${pieces} ${baseUnit} = ${totalReturning} ${baseUnit}`;
            calcDiv.classList.remove('hidden');
        } else if (packs > 0) {
            calcDiv.innerHTML = `📊 ${packs} ${packType}(s) × ${packSize} = ${packReturnPieces} ${baseUnit}`;
            calcDiv.classList.remove('hidden');
        } else if (pieces > 0) {
            calcDiv.innerHTML = `📊 ${pieces} ${baseUnit}(s) = ${pieces} ${baseUnit}`;
            calcDiv.classList.remove('hidden');
        } else {
            calcDiv.classList.add('hidden');
        }

        document.getElementById(`piece_return_total_${index}`).value = pieces;
        document.getElementById(`total_pieces_${index}`).value = totalReturning;

        updateGrandTotal();
    }

    function calculatePieceReturn(index) {
        const piecesInput = document.getElementById(`pieces_${index}`);
        const maxPieces = parseFloat(piecesInput.getAttribute('data-maxpieces')) || 0;
        const baseUnit = piecesInput.getAttribute('data-baseunit');

        let pieces = parseFloat(piecesInput.value) || 0;

        if (pieces < 0) {
            piecesInput.value = 0;
            pieces = 0;
        }

        if (pieces > maxPieces) {
            piecesInput.value = maxPieces;
            pieces = maxPieces;
        }

        document.getElementById(`piece_return_total_${index}`).value = pieces;

        // Get pack return value
        const packsInput = document.getElementById(`packs_${index}`);
        const packSize = parseFloat(packsInput.getAttribute('data-packsize')) || 1;
        const maxPacks = parseFloat(packsInput.getAttribute('data-maxpacks')) || 0;
        let packs = parseFloat(packsInput.value) || 0;

        const packReturnPieces = packs * packSize;
        const totalReturning = packReturnPieces + pieces;

        // Validate
        if (totalReturning > maxPieces) {
            const warningDiv = document.getElementById(`warning_${index}`);
            warningDiv.innerHTML = `⚠️ Total returning (${totalReturning} ${baseUnit}) exceeds available (${maxPieces} ${baseUnit})`;
            warningDiv.classList.remove('hidden');
        } else {
            document.getElementById(`warning_${index}`).classList.add('hidden');
        }

        // Update calculation display
        const calcDiv = document.getElementById(`calc_display_${index}`);
        const packType = packsInput.getAttribute('data-packtype');
        if (packs > 0 && pieces > 0) {
            calcDiv.innerHTML = `📊 ${packs} ${packType}(s) (${packReturnPieces} ${baseUnit}) + ${pieces} ${baseUnit} = ${totalReturning} ${baseUnit}`;
            calcDiv.classList.remove('hidden');
        } else if (packs > 0) {
            calcDiv.innerHTML = `📊 ${packs} ${packType}(s) × ${packSize} = ${packReturnPieces} ${baseUnit}`;
            calcDiv.classList.remove('hidden');
        } else if (pieces > 0) {
            calcDiv.innerHTML = `📊 ${pieces} ${baseUnit}(s) = ${pieces} ${baseUnit}`;
            calcDiv.classList.remove('hidden');
        } else {
            calcDiv.classList.add('hidden');
        }

        document.getElementById(`total_pieces_${index}`).value = totalReturning;

        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grandTotalPacks = 0;
        let grandTotalPieces = 0;
        let grandTotalAll = 0;
        const itemCount = {{ count($requisition->items) }};

        for (let i = 0; i < itemCount; i++) {
            const packsInput = document.getElementById(`packs_${i}`);
            const piecesInput = document.getElementById(`pieces_${i}`);

            if (packsInput) {
                let packs = parseFloat(packsInput.value) || 0;
                grandTotalPacks += packs;
            }
            if (piecesInput) {
                let pieces = parseFloat(piecesInput.value) || 0;
                grandTotalPieces += pieces;
            }
            const total = parseFloat(document.getElementById(`total_pieces_${i}`)?.value) || 0;
            grandTotalAll += total;
        }

        document.getElementById('grandTotal').innerText = grandTotalAll;
        document.getElementById('grandTotalPacks').innerText = grandTotalPacks;
        document.getElementById('grandTotalPieces').innerText = grandTotalPieces;
    }
</script>
@endsection
