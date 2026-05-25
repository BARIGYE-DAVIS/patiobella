@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Empty Bottle / Container Weight Management</h4>
                <a href="{{ route('store.dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="mdi mdi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline"></i>
                                <strong>What is this?</strong> Set the weight of empty bottles/containers for items.
                                This is used to calculate product consumption by weighing bottles before and after use
                                (e.g., for bar inventory management).
                            </div>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('store.empty-bottle-weights.index') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Item</label>
                                <input type="text" name="search" id="search" class="form-control"
                                       placeholder="Search by name, code or barcode..."
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="weight_status" class="form-label">Weight Status</label>
                                <select name="weight_status" id="weight_status" class="form-select">
                                    <option value="">All Items</option>
                                    <option value="has_weight" {{ request('weight_status') == 'has_weight' ? 'selected' : '' }}>
                                        Has Weight Set (> 0 kg)
                                    </option>
                                    <option value="no_weight" {{ request('weight_status') == 'no_weight' ? 'selected' : '' }}>
                                        No Weight Set (= 0 kg)
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Item Code</th>
                                    <th width="30%">Item Name</th>
                                    <th width="15%">Category</th>
                                    <th width="15%">Base Unit</th>
                                    <th width="15%">Empty Bottle Weight (kg)</th>
                                    <th width="5%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                    <tr>
                                        <td>{{ $items->firstItem() + $index }}</td>
                                        <td>
                                            <code>{{ $item->item_code ?? 'N/A' }}</code>
                                            @if($item->barcode)
                                                <br><small class="text-muted">Barcode: {{ $item->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $item->name }}</strong>
                                            @if($item->description)
                                                <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->category->name ?? 'Uncategorized' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $item->base_unit }}</span>
                                        </td>
                                        <td>
                                            @if($item->empty_bottle_weight > 0)
                                                <span class="badge bg-success">
                                                    {{ number_format($item->empty_bottle_weight, 3) }} kg
                                                </span>
                                            @else
                                                <span class="badge bg-danger">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('store.empty-bottle-weights.edit', $item->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-pencil"></i> Set Weight
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="mdi mdi-package-variant-closed fs-1 text-muted"></i>
                                            <p class="mt-2 text-muted">No inventory items found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-end mt-3">
                        {{ $items->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85rem;
        padding: 5px 10px;
    }
    .alert-info {
        background-color: #e8f4fd;
        border-color: #b8e2f8;
        color: #0c5e8c;
    }
</style>
@endpush
