@extends('layouts.store')

@section('title', isset($po) ? 'Receive Goods' : 'Create Goods Received Note')
@section('page-title', isset($po) ? 'Receive Goods for PO #' . $po->po_number : 'Create Goods Received Note')

@section('content')
@php
    use App\Models\BusinessSetting;

    $companyName = BusinessSetting::get('company_name', 'Company Name');
    if ($companyName && (str_starts_with($companyName, '"') || str_starts_with($companyName, "'"))) {
        $companyName = trim($companyName, '"\'');
    }

    $currentUser = Auth::user();

    // ── Logo (base64) ──────────────────────────────────────────────────────────
    $companyLogoB64 = null;
    $rawLogo = BusinessSetting::getLogo();
    if ($rawLogo) {
        $logoPath = public_path(parse_url($rawLogo, PHP_URL_PATH));
        if (file_exists($logoPath)) {
            $mime = mime_content_type($logoPath);
            $companyLogoB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            $companyLogoB64 = $rawLogo; // fallback to URL
        }
    }

    // ── Stamp (base64) ────────────────────────────────────────────────────────
    $companyStampB64 = null;
    $rawStamp = BusinessSetting::getStamp();
    if ($rawStamp) {
        $stampPath = public_path(parse_url($rawStamp, PHP_URL_PATH));
        if (file_exists($stampPath)) {
            $mime = mime_content_type($stampPath);
            $companyStampB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($stampPath));
        } else {
            $companyStampB64 = $rawStamp;
        }
    }

    // ── User Signature (base64) ───────────────────────────────────────────────
    $currentUserSignatureB64 = null;
    if ($currentUser->signature_path) {
        $sigPath = public_path(parse_url(asset($currentUser->signature_path), PHP_URL_PATH));
        if (file_exists($sigPath)) {
            $mime = mime_content_type($sigPath);
            $currentUserSignatureB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sigPath));
        } else {
            // try storage path directly
            $sigPath2 = storage_path('app/public/' . ltrim($currentUser->signature_path, '/'));
            if (file_exists($sigPath2)) {
                $mime = mime_content_type($sigPath2);
                $currentUserSignatureB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sigPath2));
            }
        }
    }
@endphp

@if(isset($po))
    <style>
        .amount-to-pay { font-weight: 600; color: #dc2626; font-size: 12px; }
        .summary-box { background-color: #fefce8; border: 1px solid #fde68a; border-radius: 6px; padding: 10px; }
        .payment-summary { background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 10px; }
        .received-input, .rejected-input { transition: all 0.1s ease; }
        .receiving-method-selector { background-color: #fef9c3; border: 1px solid #fde047; border-radius: 6px; padding: 4px 8px; font-size: 11px; font-weight: 500; }
        .pack-fields { background-color: #f0fdf4; padding: 6px; border-radius: 6px; margin-top: 4px; }
        .unit-cost-input { border: 1px solid #d1fae5; background: #f0fdf4; }
        .unit-cost-input:focus { border-color: #059669; outline: none; box-shadow: 0 0 0 2px rgba(5,150,105,0.15); }

        .preview-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .preview-modal.active { display: flex; }
        .preview-content {
            background: white;
            width: 90%;
            max-width: 1000px;
            max-height: 90vh;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
        }
        .preview-header {
            background: linear-gradient(to right, #1e40af, #1e3a8a);
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .preview-body { padding: 20px; max-height: calc(90vh - 120px); overflow-y: auto; }
        .preview-footer { padding: 12px 20px; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px; }
        .preview-row { display: flex; margin-bottom: 8px; font-size: 13px; }
        .preview-label { width: 180px; font-weight: 600; color: #4b5563; }
        .preview-value { flex: 1; color: #1f2937; }
        .preview-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 10px; }
        .preview-table th, .preview-table td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        .preview-table th { background: #f3f4f6; font-weight: 600; }
        .signature-img { max-height: 60px; max-width: 150px; }
        .stamp-img { max-height: 80px; max-width: 120px; }
    </style>

    @if(session('error'))
        <div class="mb-3 bg-red-50 border-l-4 border-red-500 text-red-700 p-2 rounded text-xs">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-green-700 to-green-600 px-4 py-2 flex justify-between items-center">
            <h2 class="text-sm font-semibold text-white">
                <i class="fa fa-truck mr-2 text-xs"></i> Receive Goods for PO #{{ $po->po_number }}
            </h2>
            <a href="{{ route('store.goods-received.create') }}" class="px-2 py-1 bg-white text-gray-700 rounded text-xs hover:bg-gray-100 transition">
                <i class="fa fa-arrow-left mr-1 text-xs"></i> Back to PO List
            </a>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('store.goods-received.store') }}" id="grnForm">
                @csrf
                <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">
                <input type="hidden" name="vat_rate" id="vat_rate" value="18">

                {{-- Vendor and Delivery Info --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Vendor</label>
                        <input type="text" value="{{ $po->vendor->name }}" class="w-full px-2 py-1 border border-gray-200 rounded bg-gray-50 text-xs" readonly>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">PO Number</label>
                        <input type="text" value="{{ $po->po_number }}" class="w-full px-2 py-1 border border-gray-200 rounded bg-gray-50 text-xs" readonly>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">PO Date</label>
                        <input type="text" value="{{ $po->po_date ?? $po->created_at->format('Y-m-d') }}" class="w-full px-2 py-1 border border-gray-200 rounded bg-gray-50 text-xs" readonly>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Received Date <span class="text-red-500">*</span></label>
                        <input type="date" name="received_date" id="received_date" value="{{ date('Y-m-d') }}" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" required>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Delivery Note #</label>
                        <input type="text" name="delivery_note_number" id="delivery_note_number" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" placeholder="Optional">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Received By <span class="text-red-500">*</span></label>
                        <input type="text" name="received_by" id="received_by" value="{{ $currentUser->first_name }} {{ $currentUser->last_name }}" class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-gray-50" readonly>
                        <input type="hidden" name="received_by_user_id" value="{{ $currentUser->id }}">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Delivered By Name</label>
                        <input type="text" name="delivered_by_name" id="delivered_by_name" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" placeholder="Supplier delivery person">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Delivered By Phone</label>
                        <input type="text" name="delivered_by_phone" id="delivered_by_phone" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" placeholder="Phone number">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Delivered By Email</label>
                        <input type="email" name="delivered_by_email" id="delivered_by_email" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" placeholder="Email address">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">General Notes</label>
                    <textarea name="notes" id="notes" rows="1" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" placeholder="General notes..."></textarea>
                </div>

                {{-- Items Table --}}
                <div class="overflow-x-auto mt-4">
                    <table class="w-full border border-gray-200 rounded text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border-b p-1.5 text-left">Item</th>
                                <th class="border-b p-1.5 text-center w-16">Ordered</th>
                                <th class="border-b p-1.5 text-center w-32">Unit Cost (UGX)</th>
                                <th class="border-b p-1.5 text-center w-28">Receiving Method</th>
                                <th class="border-b p-1.5 text-center w-20">Received Qty</th>
                                <th class="border-b p-1.5 text-center w-20">Rejected Qty</th>
                                <th class="border-b p-1.5 text-right w-28">Subtotal (excl VAT)</th>
                                <th class="border-b p-1.5 text-right w-24">VAT (18%)</th>
                                <th class="border-b p-1.5 text-right w-28">Total (incl VAT)</th>
                                <th class="border-b p-1.5 text-left w-32">Rejection Reason</th>
                            </tr>
                        </thead>
                        <tbody id="items-table-body">
                            @foreach($po->items as $index => $item)
                                @php
                                    $remainingToReceive = $item->quantity_ordered - ($item->quantity_received ?? 0);
                                    $inventoryItem = $item->inventoryItem;
                                    $unitOfMeasure = $inventoryItem->unit_of_measurement ?? $inventoryItem->base_unit ?? 'piece';
                                @endphp
                                <tr class="item-row" data-index="{{ $index }}">
                                    <td class="p-1.5 border-b">
                                        {{ $inventoryItem->name ?? 'Unknown Item' }}
                                        @if($inventoryItem && $inventoryItem->item_code)
                                            <br><span class="text-[9px] text-gray-400">Code: {{ $inventoryItem->item_code }}</span>
                                        @endif
                                        <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][quantity_ordered]" value="{{ $item->quantity_ordered }}">
                                        <input type="hidden" name="items[{{ $index }}][base_unit]" value="{{ $unitOfMeasure }}">
                                    </td>

                                    <td class="p-1.5 border-b text-center">
                                        {{ number_format($item->quantity_ordered, 2) }} {{ $unitOfMeasure }}
                                    </td>

                                    {{-- ✅ EDITABLE UNIT COST --}}
                                    <td class="p-1.5 border-b text-center">
                                        <input type="number"
                                               class="unit-cost-input w-full px-1 py-0.5 rounded text-xs text-right"
                                               name="items[{{ $index }}][unit_cost]"
                                               data-index="{{ $index }}"
                                               value="{{ $item->unit_cost }}"
                                               min="0" step="0.01"
                                               oninput="calculateRowAmount({{ $index }})">
                                        <div class="text-[9px] text-gray-400 mt-0.5 text-right">
                                            PO: {{ number_format($item->unit_cost, 2) }}
                                        </div>
                                    </td>

                                    {{-- RECEIVING METHOD SELECTOR --}}
                                    <td class="p-1.5 border-b">
                                        <select class="receiving-method w-full px-1 py-0.5 border border-gray-300 rounded text-xs"
                                                data-index="{{ $index }}"
                                                onchange="togglePackFields({{ $index }})">
                                            <option value="direct">Direct (Individual {{ $unitOfMeasure }})</option>
                                            <option value="pack">By Pack (Box/Carton/Crate)</option>
                                        </select>

                                        {{-- Pack Fields (hidden by default) --}}
                                        <div class="pack-fields hidden" id="pack-fields-{{ $index }}">
                                            <div class="grid grid-cols-2 gap-1 mt-1">
                                                <div>
                                                    <label class="text-[9px] text-gray-500">Pack Type</label>
                                                    <select class="pack-type w-full px-1 py-0.5 border border-gray-300 rounded text-xs" data-index="{{ $index }}">
                                                        <option value="Box">Box</option>
                                                        <option value="Carton">Carton</option>
                                                        <option value="Crate">Crate</option>
                                                        <option value="Dozen">Dozen</option>
                                                        <option value="Pack">Pack</option>
                                                        <option value="Sack">Sack</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[9px] text-gray-500">Pack Size ({{ $unitOfMeasure }}/pack)</label>
                                                    <input type="number" class="pack-size w-full px-1 py-0.5 border border-gray-300 rounded text-xs"
                                                           data-index="{{ $index }}" value="1" min="1" step="1">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] text-gray-500"># of Packs</label>
                                                    <input type="number" class="number-of-packs w-full px-1 py-0.5 border border-gray-300 rounded text-xs"
                                                           data-index="{{ $index }}" value="0" min="0" step="1"
                                                           oninput="calculateFromPacks({{ $index }})">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] text-gray-500">Total {{ $unitOfMeasure }}</label>
                                                    <input type="text" class="total-base-units w-full px-1 py-0.5 border border-gray-300 rounded text-xs bg-gray-50"
                                                           data-index="{{ $index }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Store pack data in hidden fields --}}
                                        <input type="hidden" name="items[{{ $index }}][pack_type]" id="pack_type_{{ $index }}" value="">
                                        <input type="hidden" name="items[{{ $index }}][pack_size]" id="pack_size_{{ $index }}" value="">
                                        <input type="hidden" name="items[{{ $index }}][number_of_packs]" id="number_of_packs_{{ $index }}" value="">
                                    </td>

                                    <td class="p-1.5 border-b text-center">
                                        <input type="number" class="received-input w-20 px-1 py-0.5 border border-gray-300 rounded text-center text-xs"
                                               data-index="{{ $index }}"
                                               value="0" min="0" max="{{ $remainingToReceive }}" step="0.01"
                                               oninput="calculateRowAmount({{ $index }})">
                                        <input type="hidden" name="items[{{ $index }}][quantity_received]" id="received_hidden_{{ $index }}" value="0">
                                    </td>

                                    <td class="p-1.5 border-b text-center">
                                        <input type="number" class="rejected-input w-16 px-1 py-0.5 border border-gray-300 rounded text-center text-xs"
                                               data-index="{{ $index }}"
                                               value="0" min="0" max="{{ $remainingToReceive }}" step="0.01"
                                               oninput="calculateRowAmount({{ $index }})">
                                        <input type="hidden" name="items[{{ $index }}][quantity_rejected]" id="rejected_hidden_{{ $index }}" value="0">
                                    </td>

                                    <td class="p-1.5 border-b text-right subtotal-cell" id="subtotal_{{ $index }}">UGX 0.00</td>
                                    <td class="p-1.5 border-b text-right vat-cell" id="vat_{{ $index }}">UGX 0.00</td>
                                    <td class="p-1.5 border-b text-right font-semibold total-cell" id="total_with_vat_{{ $index }}">UGX 0.00</td>

                                    <td class="p-1.5 border-b">
                                        <input type="text" name="items[{{ $index }}][rejection_reason]" class="rejection-reason w-full px-1 py-0.5 border border-gray-300 rounded text-xs" placeholder="Reason if rejected">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="6" class="p-1.5 text-right font-semibold">Totals:</td>
                                <td class="p-1.5 text-right font-bold" id="total_subtotal">UGX 0.00</td>
                                <td class="p-1.5 text-right font-bold" id="total_vat">UGX 0.00</td>
                                <td class="p-1.5 text-right font-bold text-green-700" id="total_grand">UGX 0.00</td>
                                <td class="p-1.5"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                    <a href="{{ route('store.goods-received.create') }}" class="px-3 py-1.5 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-xs flex items-center gap-1">
                        <i class="fa fa-times text-xs"></i> Cancel
                    </a>
                    <button type="button" onclick="showPreview()" class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-xs flex items-center gap-1">
                        <i class="fa fa-eye text-xs"></i> Preview GRN
                    </button>
                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-xs flex items-center gap-1">
                        <i class="fa fa-save text-xs"></i> Create GRN
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- PREVIEW MODAL --}}
    <div id="previewModal" class="preview-modal">
        <div class="preview-content">
            <div class="preview-header">
                <h3 class="font-semibold text-sm"><i class="fa fa-file-text-o mr-2"></i> Goods Received Note Preview</h3>
                <button onclick="closePreview()" class="text-white hover:text-gray-200"><i class="fa fa-times"></i></button>
            </div>
            <div class="preview-body" id="previewBody"></div>
            <div class="preview-footer">
                <button onclick="closePreview()" class="px-3 py-1.5 bg-gray-500 text-white rounded text-xs hover:bg-gray-600">
                    <i class="fa fa-close"></i> Close
                </button>
                <button onclick="submitForm()" class="px-3 py-1.5 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                    <i class="fa fa-check"></i> Confirm & Submit
                </button>
            </div>
        </div>
    </div>

    <script>
    const VAT_RATE = 18;
    // All images are now base64 data URIs — no more 404s
    const COMPANY_LOGO      = @json($companyLogoB64);
    const COMPANY_STAMP     = @json($companyStampB64);
    const COMPANY_NAME      = @json($companyName);
    const USER_SIGNATURE    = @json($currentUserSignatureB64);
    const CURRENT_USER_NAME = @json($currentUser->first_name . ' ' . $currentUser->last_name);
    const PO_NUMBER         = @json($po->po_number);
    const VENDOR_NAME       = @json($po->vendor->name);

    // ── Helpers ────────────────────────────────────────────────────────────────
    function fmt(n) {
        return 'UGX ' + n.toLocaleString(undefined, { minimumFractionDigits: 2 });
    }

    // ── Pack fields toggle ─────────────────────────────────────────────────────
    function togglePackFields(index) {
        const method = document.querySelector(`.receiving-method[data-index="${index}"]`).value;
        const packFields = document.getElementById(`pack-fields-${index}`);
        if (method === 'pack') {
            packFields.classList.remove('hidden');
        } else {
            packFields.classList.add('hidden');
            document.querySelector(`.number-of-packs[data-index="${index}"]`).value = 0;
            document.querySelector(`.total-base-units[data-index="${index}"]`).value = '';
        }
    }

    // ── Calculate from packs ───────────────────────────────────────────────────
    function calculateFromPacks(index) {
        const packSize     = parseFloat(document.querySelector(`.pack-size[data-index="${index}"]`).value) || 1;
        const numberOfPacks = parseFloat(document.querySelector(`.number-of-packs[data-index="${index}"]`).value) || 0;
        const totalUnits   = packSize * numberOfPacks;

        document.querySelector(`.total-base-units[data-index="${index}"]`).value = totalUnits.toFixed(2);

        const receivedInput = document.querySelector(`.received-input[data-index="${index}"]`);
        if (receivedInput) {
            receivedInput.value = totalUnits;
            receivedInput.dispatchEvent(new Event('input'));
        }

        const packType = document.querySelector(`.pack-type[data-index="${index}"]`).value;
        document.getElementById(`pack_type_${index}`).value    = packType;
        document.getElementById(`pack_size_${index}`).value    = packSize;
        document.getElementById(`number_of_packs_${index}`).value = numberOfPacks;
    }

    // ── Row amount calculation ─────────────────────────────────────────────────
    function calculateRowAmount(index) {
        const receivedInput = document.querySelector(`.received-input[data-index="${index}"]`);
        const rejectedInput = document.querySelector(`.rejected-input[data-index="${index}"]`);

        // ✅ Read from the editable unit-cost input
        const unitCost = parseFloat(
            document.querySelector(`.unit-cost-input[data-index="${index}"]`)?.value
        ) || 0;

        const method = document.querySelector(`.receiving-method[data-index="${index}"]`)?.value || 'direct';

        if (!receivedInput || !rejectedInput) return;

        let received = parseFloat(receivedInput.value) || 0;
        let rejected = parseFloat(rejectedInput.value) || 0;
        const maxReceive = parseFloat(receivedInput.getAttribute('max')) || 0;

        if (received > maxReceive) { received = maxReceive; receivedInput.value = maxReceive; }
        if (rejected > received)   { rejected = received;   rejectedInput.value = received; }

        if (method === 'pack') {
            const packSize = parseFloat(document.querySelector(`.pack-size[data-index="${index}"]`).value) || 1;
            if (packSize > 0 && received > 0) {
                const numberOfPacks = Math.ceil(received / packSize);
                document.querySelector(`.number-of-packs[data-index="${index}"]`).value = numberOfPacks;
                document.querySelector(`.total-base-units[data-index="${index}"]`).value = (packSize * numberOfPacks).toFixed(2);
                document.getElementById(`pack_size_${index}`).value         = packSize;
                document.getElementById(`number_of_packs_${index}`).value   = numberOfPacks;
            }
        }

        document.getElementById(`received_hidden_${index}`).value = received;
        document.getElementById(`rejected_hidden_${index}`).value = rejected;

        const accepted     = received - rejected;
        const subtotal     = accepted * unitCost;
        const vatAmount    = subtotal * (VAT_RATE / 100);
        const totalWithVat = subtotal + vatAmount;

        document.getElementById(`subtotal_${index}`).innerText       = fmt(subtotal);
        document.getElementById(`vat_${index}`).innerText            = fmt(vatAmount);
        document.getElementById(`total_with_vat_${index}`).innerText = fmt(totalWithVat);

        updateTotals();
    }

    function updateTotals() {
        let totalSubtotal = 0, totalVat = 0, totalGrand = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const index    = row.getAttribute('data-index');
            const received = parseFloat(document.getElementById(`received_hidden_${index}`)?.value) || 0;
            const rejected = parseFloat(document.getElementById(`rejected_hidden_${index}`)?.value) || 0;
            const unitCost = parseFloat(document.querySelector(`.unit-cost-input[data-index="${index}"]`)?.value) || 0;
            const accepted = Math.max(0, received - rejected);
            const subtotal = accepted * unitCost;
            const vatAmount = subtotal * (VAT_RATE / 100);
            totalSubtotal += subtotal;
            totalVat      += vatAmount;
            totalGrand    += (subtotal + vatAmount);
        });

        document.getElementById('total_subtotal').innerHTML = fmt(totalSubtotal);
        document.getElementById('total_vat').innerHTML      = fmt(totalVat);
        document.getElementById('total_grand').innerHTML    = fmt(totalGrand);
    }

    // ── Preview modal ──────────────────────────────────────────────────────────
    function showPreview() {
        const receivedDate    = document.getElementById('received_date').value;
        const deliveryNote    = document.getElementById('delivery_note_number').value || 'N/A';
        const deliveredByName = document.getElementById('delivered_by_name').value || 'N/A';
        const deliveredByPhone= document.getElementById('delivered_by_phone').value || '';
        const deliveredByEmail= document.getElementById('delivered_by_email').value || '';
        const notes           = document.getElementById('notes').value || '';

        const items = [];
        let hasItems = false;

        document.querySelectorAll('.item-row').forEach(row => {
            const index         = row.getAttribute('data-index');
            const itemName      = row.querySelector('td:first-child').innerHTML.split('<br')[0];
            const ordered       = parseFloat(document.querySelector(`input[name="items[${index}][quantity_ordered]"]`)?.value) || 0;
            const unitCost      = parseFloat(document.querySelector(`.unit-cost-input[data-index="${index}"]`)?.value) || 0;
            const received      = parseFloat(document.getElementById(`received_hidden_${index}`)?.value) || 0;
            const rejected      = parseFloat(document.getElementById(`rejected_hidden_${index}`)?.value) || 0;
            const rejectionReason = row.querySelector('.rejection-reason')?.value || '';
            const method        = document.querySelector(`.receiving-method[data-index="${index}"]`)?.value || 'direct';
            const packType      = document.getElementById(`pack_type_${index}`)?.value || '';
            const packSize      = document.getElementById(`pack_size_${index}`)?.value || '';
            const numberOfPacks = document.getElementById(`number_of_packs_${index}`)?.value || '';
            const subtotal      = parseFloat(document.getElementById(`subtotal_${index}`).innerText.replace('UGX ', '').replace(/,/g, '')) || 0;
            const vat           = parseFloat(document.getElementById(`vat_${index}`).innerText.replace('UGX ', '').replace(/,/g, '')) || 0;
            const total         = parseFloat(document.getElementById(`total_with_vat_${index}`).innerText.replace('UGX ', '').replace(/,/g, '')) || 0;

            if (received > 0 || rejected > 0) {
                hasItems = true;
                let receivingDetail = '';
                if (method === 'pack' && numberOfPacks > 0) {
                    receivingDetail = `<br><small style="color:#6b7280;">📦 ${numberOfPacks} × ${packType} (${packSize} units/pack)</small>`;
                }
                items.push({ itemName, ordered, unitCost, received, rejected, accepted: received - rejected, rejectionReason, subtotal, vat, total, receivingDetail });
            }
        });

        if (!hasItems) {
            alert('Please enter at least one received item before previewing.');
            return;
        }

        const totalSubtotal = parseFloat(document.getElementById('total_subtotal').innerText.replace('UGX ', '').replace(/,/g, '')) || 0;
        const totalVat      = parseFloat(document.getElementById('total_vat').innerText.replace('UGX ', '').replace(/,/g, '')) || 0;
        const totalGrand    = parseFloat(document.getElementById('total_grand').innerText.replace('UGX ', '').replace(/,/g, '')) || 0;

        // ✅ All images are base64 — always load, never 404
        const signatureHtml = USER_SIGNATURE
            ? `<img src="${USER_SIGNATURE}" class="signature-img" alt="Signature" style="display:block;margin:0 auto;">`
            : '<div style="height:50px;"></div><p style="font-size:10px;color:#9ca3af;text-align:center;">No signature on file</p>';

        const stampHtml = COMPANY_STAMP
            ? `<img src="${COMPANY_STAMP}" class="stamp-img" alt="Company Stamp" style="display:block;margin:0 auto;">`
            : '<div style="height:60px;"></div><p style="font-size:10px;color:#9ca3af;text-align:center;">No stamp</p>';

        const logoHtml = COMPANY_LOGO
            ? `<img src="${COMPANY_LOGO}" style="max-height:60px;width:auto;margin-bottom:5px;" alt="Logo">`
            : `<h2 style="margin:5px 0;color:#1e40af;">${COMPANY_NAME}</h2>`;

        const previewHtml = `
            <div style="font-family:Arial,sans-serif;">
                <div style="text-align:center;margin-bottom:20px;border-bottom:2px solid #1e40af;padding-bottom:10px;">
                    ${logoHtml}
                    <h3 style="margin:5px 0 0;font-size:16px;">GOODS RECEIVED NOTE</h3>
                    <p style="margin:5px 0 0;font-size:12px;">PO: ${PO_NUMBER} | Vendor: ${VENDOR_NAME}</p>
                </div>

                <div style="margin-bottom:20px;">
                    <div class="preview-row"><div class="preview-label">Received Date:</div><div class="preview-value">${receivedDate}</div></div>
                    <div class="preview-row"><div class="preview-label">Delivery Note #:</div><div class="preview-value">${deliveryNote}</div></div>
                    <div class="preview-row"><div class="preview-label">Received By:</div><div class="preview-value">${CURRENT_USER_NAME}</div></div>
                    <div class="preview-row">
                        <div class="preview-label">Delivered By:</div>
                        <div class="preview-value">${deliveredByName}${deliveredByPhone ? ' ('+deliveredByPhone+')' : ''}${deliveredByEmail ? ' – '+deliveredByEmail : ''}</div>
                    </div>
                </div>

                <h4 style="margin:15px 0 10px;color:#374151;">Received Items</h4>
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>Item</th><th>Ordered</th><th>Unit Cost</th>
                            <th>Received</th><th>Rejected</th><th>Accepted</th>
                            <th>Subtotal</th><th>VAT 18%</th><th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(item => `
                            <tr>
                                <td>${item.itemName}${item.receivingDetail}</td>
                                <td>${item.ordered.toLocaleString()}</td>
                                <td>UGX ${item.unitCost.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                                <td>${item.received.toLocaleString()}</td>
                                <td>${item.rejected.toLocaleString()}${item.rejectionReason ? `<br><small style="color:#666;">(${item.rejectionReason})</small>` : ''}</td>
                                <td>${item.accepted.toLocaleString()}</td>
                                <td>UGX ${item.subtotal.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                                <td>UGX ${item.vat.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                                <td>UGX ${item.total.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                    <tfoot>
                        <tr style="background:#f3f4f6;font-weight:bold;">
                            <td colspan="6" style="text-align:right;">TOTALS:</td>
                            <td>UGX ${totalSubtotal.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                            <td>UGX ${totalVat.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                            <td>UGX ${totalGrand.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                        </tr>
                    </tfoot>
                </table>

                ${notes ? `<div class="preview-row" style="margin-top:12px;"><div class="preview-label">Notes:</div><div class="preview-value">${notes}</div></div>` : ''}

                <div style="margin-top:30px;padding-top:20px;border-top:1px solid #ccc;">
                    <div style="display:flex;justify-content:space-between;">
                        <div style="text-align:center;flex:1;">
                            <p style="font-size:11px;font-weight:600;margin-bottom:5px;">RECEIVED BY:</p>
                            ${signatureHtml}
                            <div style="border-top:1px solid #999;margin-top:8px;padding-top:4px;width:80%;margin-left:auto;margin-right:auto;"></div>
                            <p style="font-size:11px;margin-top:5px;">${CURRENT_USER_NAME}</p>
                            <p style="font-size:10px;color:#666;">${new Date().toLocaleDateString()}</p>
                        </div>
                        <div style="text-align:center;flex:1;">
                            <p style="font-size:11px;font-weight:600;margin-bottom:5px;">VERIFIED BY:</p>
                            <div style="height:60px;display:flex;align-items:center;justify-content:center;">
                                <p style="font-size:10px;color:#f59e0b;">Pending Verification</p>
                            </div>
                            <div style="border-top:1px solid #999;margin-top:8px;padding-top:4px;width:80%;margin-left:auto;margin-right:auto;"></div>
                            <p style="font-size:10px;color:#666;">To be verified by manager</p>
                        </div>
                        <div style="text-align:center;flex:1;">
                            <p style="font-size:11px;font-weight:600;margin-bottom:5px;">COMPANY STAMP:</p>
                            ${stampHtml}
                            <div style="border-top:1px solid #999;margin-top:8px;padding-top:4px;width:80%;margin-left:auto;margin-right:auto;"></div>
                            <p style="font-size:10px;color:#666;">Authorized Signature</p>
                        </div>
                    </div>
                </div>

                <div style="margin-top:20px;text-align:center;font-size:9px;color:#999;border-top:1px solid #eee;padding-top:10px;">
                    <p>This is a computer generated document.</p>
                    <p>${COMPANY_NAME} – All Rights Reserved</p>
                </div>
            </div>
        `;

        document.getElementById('previewBody').innerHTML = previewHtml;
        document.getElementById('previewModal').classList.add('active');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.remove('active');
    }

    function submitForm() {
        closePreview();
        document.getElementById('grnForm').submit();
    }

    document.getElementById('previewModal').addEventListener('click', function(e) {
        if (e.target === this) closePreview();
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.item-row').forEach(row => {
            const index = row.getAttribute('data-index');
            if (index !== null) calculateRowAmount(parseInt(index));
        });
    });
    </script>

@else
    {{-- ── PO SELECTION PAGE ──────────────────────────────────────────────── --}}
    <style>
        .po-row { cursor: pointer; transition: all 0.15s ease; }
        .po-row:hover { background-color: #fff7ed; }
        .compact-table th, .compact-table td { padding: 6px 8px; font-size: 12px; }
        .filter-card { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-bottom: 16px; }
        .status-badge { display: inline-flex; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 600; }
        .status-sent { background: #dbeafe; color: #1e40af; }
        .status-partially_received { background: #fed7aa; color: #9a3412; }
    </style>

    @if(session('error'))
        <div class="mb-3 bg-red-50 border-l-4 border-red-500 text-red-700 p-2 rounded text-xs">{{ session('error') }}</div>
    @endif

    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-orange-700 to-orange-600 px-4 py-2">
                <h2 class="text-sm font-semibold text-white">
                    <i class="fa fa-search mr-2 text-xs"></i> Find & Select Purchase Order
                </h2>
                <p class="text-orange-100 text-[11px] mt-0.5">Search, filter, and click on any PO row to receive goods</p>
            </div>

            <div class="p-3">
                <form method="GET" action="{{ route('store.goods-received.create') }}" id="filterForm">
                    <div class="filter-card">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-medium text-gray-700 mb-1">
                                    <i class="fa fa-search text-gray-400 mr-1"></i> Search
                                </label>
                                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="PO number or item name...">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-700 mb-1">
                                    <i class="fa fa-building text-gray-400 mr-1"></i> Vendor
                                </label>
                                <select name="vendor_id" id="vendorFilter" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                                    <option value="">All Vendors</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-700 mb-1">
                                    <i class="fa fa-calendar text-gray-400 mr-1"></i> PO Date From
                                </label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-2 border-t border-gray-200">
                            <a href="{{ route('store.goods-received.create') }}" class="px-3 py-1.5 bg-gray-500 text-white rounded text-[11px] hover:bg-gray-600 transition flex items-center gap-1">
                                <i class="fa fa-undo text-[10px]"></i> Reset
                            </a>
                            <button type="submit" class="px-3 py-1.5 bg-orange-600 text-white rounded text-[11px] hover:bg-orange-700 transition flex items-center gap-1">
                                <i class="fa fa-filter text-[10px]"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded compact-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border-b text-[10px] font-medium text-gray-500 uppercase py-2 px-2">PO Number</th>
                                <th class="border-b text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Vendor</th>
                                <th class="border-b text-[10px] font-medium text-gray-500 uppercase py-2 px-2">PO Date</th>
                                <th class="border-b text-right text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Amount</th>
                                <th class="border-b text-center text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Status</th>
                                <th class="border-b text-center text-[10px] font-medium text-gray-500 uppercase py-2 px-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrders as $po)
                                @php
                                    $statusClass = $po->status == 'sent' ? 'status-sent' : 'status-partially_received';
                                    $statusText  = $po->status == 'sent' ? 'Sent' : 'Partial';
                                @endphp
                                <tr class="po-row border-b hover:bg-orange-50 transition">
                                    <td class="py-2 px-2 border-b font-medium text-orange-600 text-xs">{{ $po->po_number }}</td>
                                    <td class="py-2 px-2 border-b text-xs">{{ $po->vendor->name }}</td>
                                    <td class="py-2 px-2 border-b text-xs">{{ $po->po_date ?? $po->created_at->format('Y-m-d') }}</td>
                                    <td class="py-2 px-2 border-b text-right font-semibold text-xs">UGX {{ number_format($po->total_amount, 2) }}</td>
                                    <td class="py-2 px-2 border-b text-center">
                                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="py-2 px-2 border-b text-center">
                                        <a href="{{ route('store.goods-received.create-for-po', $po->id) }}"
                                           class="inline-flex items-center gap-1 px-2 py-1 bg-orange-600 text-white rounded text-[10px] hover:bg-orange-700 transition">
                                            <i class="fa fa-truck"></i> Receive
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-500 text-xs">
                                        <i class="fa fa-inbox mr-1"></i> No purchase orders found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $purchaseOrders->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const vendorFilter = document.getElementById('vendorFilter');
        if (vendorFilter) vendorFilter.addEventListener('change', function() { document.getElementById('filterForm').submit(); });

        const searchInput = document.getElementById('searchInput');
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => { document.getElementById('filterForm').submit(); }, 500);
            });
        }
    });
    </script>
@endif
@endsection
