@extends('layouts.procurement')

@section('title', 'Vendor Ratings')
@section('page-title', 'Vendor Ratings')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <h4 class="text-lg font-semibold mb-4">All Ratings for {{ $vendor->name }}</h4>

    <div class="space-y-3">
        @foreach($vendor->ratings as $rating)
        <div class="border rounded-lg p-4">
            <div class="flex justify-between">
                <div>
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating->rating)
                            <i class="fas fa-star text-yellow-400"></i>
                        @else
                            <i class="far fa-star text-gray-300"></i>
                        @endif
                    @endfor
                    <span class="ml-2 text-sm">{{ $rating->rating }}/5</span>
                </div>
                <span class="text-xs text-gray-400">{{ $rating->rated_at->format('M d, Y') }}</span>
            </div>
            @if($rating->comment)
                <p class="text-gray-600 mt-2">{{ $rating->comment }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-2">GRN: {{ $rating->goodsReceivedNote->grn_number ?? 'N/A' }}</p>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        <a href="{{ route('procurement.vendors.show', $vendor->id) }}" class="text-blue-600">Back to Vendor</a>
    </div>
</div>
@endsection
