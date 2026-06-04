@extends('layouts.procurement')

@section('title', 'Vendor Details')

@section('page-title', 'Vendor Details')

@push('styles')
<style>
    .info-card {
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.625rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .detail-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .detail-value {
        font-weight: 500;
        color: #0f172a;
        font-size: 0.8rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    .status-active::before {
        content: "●";
        font-size: 0.5rem;
        color: #10b981;
    }
    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-inactive::before {
        content: "●";
        font-size: 0.5rem;
        color: #ef4444;
    }
    .status-blacklisted {
        background: #fef3c7;
        color: #92400e;
    }
    .status-blacklisted::before {
        content: "●";
        font-size: 0.5rem;
        color: #f59e0b;
    }
    .rating-star {
        color: #fbbf24;
        font-size: 1rem;
    }
    .rating-star-empty {
        color: #e5e7eb;
        font-size: 1rem;
    }
    .rating-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
        padding: 1rem;
        transition: all 0.3s ease;
    }
    .rating-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .info-section-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-section-title i {
        color: #059669;
        font-size: 0.85rem;
    }
    .btn-back, .btn-edit {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-back {
        background: #64748b;
        color: white;
    }
    .btn-back:hover {
        background: #475569;
        transform: translateY(-1px);
    }
    .btn-edit {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .btn-edit:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        transform: translateY(-1px);
    }
    .metric-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
    }
    .metric-label {
        font-size: 0.6rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .review-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.2s ease;
    }
    .review-card:hover {
        border-color: #fbbf24;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .category-badge {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        margin: 2px;
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Header with Stats --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden info-card">
        <div class="bg-gradient-to-r from-emerald-700 to-green-700 px-6 py-4">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $vendor->name }}</h3>
                        <p class="text-sm text-emerald-100 mt-0.5">Vendor Code: {{ $vendor->vendor_code }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('procurement.vendors.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('procurement.vendors.edit', $vendor->id) }}" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        {{-- Status and Quick Stats --}}
        <div class="p-5 border-b border-gray-100">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    @php
                        $statusColors = [
                            'active' => 'status-active',
                            'inactive' => 'status-inactive',
                            'blacklisted' => 'status-blacklisted',
                        ];
                    @endphp
                    <span class="status-badge {{ $statusColors[$vendor->status] ?? 'status-active' }}">
                        {{ ucfirst($vendor->status) }}
                    </span>
                </div>
                <div class="flex gap-6">
                    <div class="text-center">
                        <p class="metric-value">{{ $vendor->purchaseOrders->count() ?? 0 }}</p>
                        <p class="metric-label">Total Orders</p>
                    </div>
                    <div class="text-center">
                        <p class="metric-value">{{ number_format($vendor->purchaseOrders->where('status', 'approved')->sum('total_amount') ?? 0, 0) }}</p>
                        <p class="metric-label">Total Spent</p>
                    </div>
                    <div class="text-center">
                        <p class="metric-value">{{ $vendor->goodsReceivedNotes()->count() ?? 0 }}</p>
                        <p class="metric-label">Deliveries</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rating Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Overall Rating Card --}}
        <div class="lg:col-span-1">
            <div class="rating-card">
                <div class="text-center mb-3">
                    <div class="mt-3">
                        <div class="flex justify-center mb-2">
                            @php
                                $avgRating = $vendor->average_rating ?? 0;
                                $fullStars = floor($avgRating);
                                $halfStar = ($avgRating - $fullStars) >= 0.5;
                                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                            @endphp
                            @for($i = 1; $i <= $fullStars; $i++)
                                <i class="fas fa-star rating-star"></i>
                            @endfor
                            @if($halfStar)
                                <i class="fas fa-star-half-alt rating-star"></i>
                            @endif
                            @for($i = 1; $i <= $emptyStars; $i++)
                                <i class="far fa-star rating-star-empty"></i>
                            @endfor
                        </div>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($avgRating, 1) }} / 5.0</p>
                        <p class="text-xs text-gray-500 mt-1">Based on {{ $vendor->total_ratings ?? 0 }} rating(s)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Reviews --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 p-5 info-card">
                <h4 class="info-section-title">
                    <i class="fas fa-star"></i> Recent Reviews
                </h4>
                @php
                    $recentRatings = $vendor->ratings()->with(['goodsReceivedNote', 'ratedBy'])->latest('rated_at')->take(5)->get();
                @endphp

                @if($recentRatings->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentRatings as $rating)
                        <div class="review-card">
                            <div class="flex justify-between items-start flex-wrap gap-2">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating)
                                                <i class="fas fa-star text-amber-400 text-xs"></i>
                                            @else
                                                <i class="far fa-star text-gray-300 text-xs"></i>
                                            @endif
                                        @endfor
                                        <span class="text-xs font-semibold text-gray-700">{{ $rating->rating }}/5</span>
                                    </div>
                                    @if($rating->comment)
                                        <p class="text-sm text-gray-600 mb-1">"{{ $rating->comment }}"</p>
                                    @endif
                                    <div class="flex items-center gap-3 text-xs text-gray-400">
                                        <span>GRN: {{ $rating->goodsReceivedNote->grn_number ?? 'N/A' }}</span>
                                        <span>•</span>
                                        <span>{{ \Carbon\Carbon::parse($rating->rated_at)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500">by {{ $rating->ratedBy->first_name ?? 'User' }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($vendor->ratings()->count() > 5)
                    <div class="mt-3 text-center">
                        <a href="{{ route('procurement.vendor-ratings.vendor', $vendor->id) }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                            View all {{ $vendor->total_ratings }} reviews →
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-star text-2xl mb-2 block opacity-50"></i>
                        <p class="text-sm">No ratings yet for this vendor</p>
                        <p class="text-xs mt-1">Ratings will appear here after deliveries are rated</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Information Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Contact Information --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden info-card">
            <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <h4 class="info-section-title mb-0">
                    <i class="fas fa-address-card"></i> Contact Information
                </h4>
            </div>
            <div class="p-5">
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-user mr-2 text-gray-400"></i> Contact Person</span>
                    <span class="detail-value">{{ $vendor->contact_person ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-phone mr-2 text-gray-400"></i> Phone</span>
                    <span class="detail-value">{{ $vendor->phone ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-phone-alt mr-2 text-gray-400"></i> Alternative Phone</span>
                    <span class="detail-value">{{ $vendor->alternative_phone ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-envelope mr-2 text-gray-400"></i> Email</span>
                    <span class="detail-value">{{ $vendor->email ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Address Information --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden info-card">
            <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <h4 class="info-section-title mb-0">
                    <i class="fas fa-location-dot"></i> Address Information
                </h4>
            </div>
            <div class="p-5">
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i> Address</span>
                    <span class="detail-value">{{ $vendor->address ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-city mr-2 text-gray-400"></i> City</span>
                    <span class="detail-value">{{ $vendor->city ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-flag mr-2 text-gray-400"></i> Country</span>
                    <span class="detail-value">{{ $vendor->country ?? 'Uganda' }}</span>
                </div>
            </div>
        </div>

        {{-- Financial Information --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden info-card">
            <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <h4 class="info-section-title mb-0">
                    <i class="fas fa-chart-pie"></i> Financial Information
                </h4>
            </div>
            <div class="p-5">
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-id-card mr-2 text-gray-400"></i> Tax ID (TIN)</span>
                    <span class="detail-value">{{ $vendor->tax_id ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-credit-card mr-2 text-gray-400"></i> Payment Method</span>
                    <span class="detail-value">{{ ucfirst($vendor->payment_method ?? 'cash') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-chart-line mr-2 text-gray-400"></i> Credit Limit</span>
                    <span class="detail-value">{{ $vendor->credit_limit ? number_format($vendor->credit_limit, 2) : '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Categories Supplied --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden info-card">
            <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <h4 class="info-section-title mb-0">
                    <i class="fas fa-tags"></i> Categories Supplied
                </h4>
            </div>
            <div class="p-5">
                @if($vendor->categories && $vendor->categories->count() > 0)
                    <div class="flex flex-wrap gap-1">
                        @foreach($vendor->categories as $category)
                            <span class="category-badge">{{ $category->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm">No categories assigned yet</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Additional Information --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden info-card">
        <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
            <h4 class="info-section-title mb-0">
                <i class="fas fa-info-circle"></i> Additional Information
            </h4>
        </div>
        <div class="p-5">
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-sticky-note mr-2 text-gray-400"></i> Notes</span>
                <span class="detail-value">{{ $vendor->notes ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Audit Information --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
            <h4 class="info-section-title mb-0">
                <i class="fas fa-history"></i> Audit Trail
            </h4>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-user-plus text-blue-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Created By</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $vendor->creator ? $vendor->creator->first_name . ' ' . $vendor->creator->last_name : 'System' }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $vendor->created_at ? $vendor->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                        <i class="fas fa-user-edit text-amber-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Updated By</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $vendor->updater ? $vendor->updater->first_name . ' ' . $vendor->updater->last_name : 'Never' }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $vendor->updated_at ? $vendor->updated_at->format('M d, Y h:i A') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
