@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Set Empty Bottle / Container Weight</h4>
                <a href="{{ route('store.empty-bottle-weights.index') }}" class="btn btn-secondary btn-sm">
                    <i class="mdi mdi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-scale-balance me-2"></i> Set Empty Container Weight</h5>
                </div>
                <div class="card-body">
                    <!-- Item Information Display -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-light border">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Item Name:</strong>
                                        <p class="mb-0">{{ $item->name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Item Code:</strong>
                                        <p class="mb-0">{{ $item->item_code ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Category:</strong>
                                        <p class="mb-0">{{ $item->category->name ?? 'Uncategorized' }}</p>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Base Unit:</strong>
                                        <p class="mb-0">
                                            <span class="badge bg-info">{{ $item->base_unit }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Current Empty Weight:</strong>
                                        <p class="mb-0">
                                            @if($item->empty_bottle_weight > 0)
                                                <span class="badge bg-success fs-6">
                                                    {{ number_format($item->empty_bottle_weight, 6) }} kg
                                                </span>
                                            @else
                                                <span class="badge bg-danger">Not Set</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Alert -->
                    <div class="alert alert-info mb-4">
                        <i class="mdi mdi-information-outline"></i>
                        <strong>Why set empty bottle weight?</strong>
                        <ul class="mb-0 mt-2">
                            <li>Used for consumption tracking by weighing full vs empty bottles</li>
                            <li>Essential for bar inventory management to track liquor consumption</li>
                            <li>Helps calculate actual product used when weighing bottles before and after service</li>
                            <li>Weight should be in kilograms (kg)</li>
                        </ul>
                    </div>

                    <!-- Edit Form -->
                    <form action="{{ route('store.empty-bottle-weights.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="empty_bottle_weight" class="form-label">
                                        Empty Bottle/Container Weight (kg) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               name="empty_bottle_weight"
                                               id="empty_bottle_weight"
                                               class="form-control @error('empty_bottle_weight') is-invalid @enderror"
                                               value="{{ old('empty_bottle_weight', $item->empty_bottle_weight) }}"
                                               step="0.000001"
                                               min="0"
                                               max="999999.999999"
                                               placeholder="e.g., 0.450 for a 450g bottle">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    <div class="form-text">
                                        Enter the weight of the EMPTY bottle/container in kilograms.
                                        Examples: Beer bottle (0.250 kg), Wine bottle (0.500 kg), Soda can (0.015 kg)
                                    </div>
                                    @error('empty_bottle_weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <i class="mdi mdi-alert"></i>
                                    <strong>Important:</strong> Setting this weight will be used for inventory calculations.
                                    Please ensure you enter the correct weight of the empty container only (not including the product).
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('store.empty-bottle-weights.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-cancel"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i> Save Weight
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Helper Card: Common Weights Reference -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="mdi mdi-help-circle"></i> Common Empty Container Weights (Reference)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0">
                                <li><strong>Beer bottle (330ml)</strong> - ~0.200 - 0.250 kg</li>
                                <li><strong>Beer bottle (500ml)</strong> - ~0.250 - 0.350 kg</li>
                                <li><strong>Wine bottle (750ml)</strong> - ~0.400 - 0.600 kg</li>
                                <li><strong>Soda can (330ml)</strong> - ~0.015 - 0.020 kg</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0">
                                <li><strong>Spirit bottle (750ml)</strong> - ~0.400 - 0.800 kg</li>
                                <li><strong>Water bottle (500ml plastic)</strong> - ~0.010 - 0.015 kg</li>
                                <li><strong>Glass (tumbler)</strong> - ~0.150 - 0.250 kg</li>
                                <li><strong>Plastic cup</strong> - ~0.005 - 0.010 kg</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .alert-light {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    // Optional: Add keyboard shortcut (Ctrl+S to save)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            document.querySelector('form').submit();
        }
    });
</script>
@endpush
