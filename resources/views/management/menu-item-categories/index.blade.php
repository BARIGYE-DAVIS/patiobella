{{-- resources/views/management/menu-item-categories/index.blade.php --}}

@extends('layouts.management')

@section('title', 'Menu Item Categories')

@section('page-title', 'Menu Item Categories')

@push('styles')
<style>
    .category-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        border-color: #f97316;
    }
    .gradient-bg {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    }
    .modal-enter {
        animation: modalFadeIn 0.2s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }
    .btn-icon {
        transition: all 0.2s ease;
    }
    .btn-icon:hover {
        transform: scale(1.08);
    }
    .search-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        outline: none;
    }
    .category-card.hidden-card {
        display: none;
    }
    .no-results {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-12">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Menu Item Categories</h1>
            <p class="text-sm text-gray-500 mt-0.5">Organize your menu items by categories like Appetizers, Main Course, Desserts, etc.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Live Search Input --}}
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="liveSearch"
                       placeholder="Search categories..."
                       class="pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 w-64 search-input">
            </div>
            <button type="button" id="createCategoryBtn"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <i class="fas fa-plus"></i> Create Category
            </button>
        </div>
    </div>

    {{-- Search Results Count --}}
    <div id="searchResultsCount" class="text-xs text-gray-500 mb-3 hidden">
        Found <span id="visibleCount">0</span> categories
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Categories Grid --}}
    <div id="categoriesGrid">
        @if($categories->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($categories as $category)
                    <div class="category-card bg-white rounded-xl overflow-hidden shadow-sm"
                         data-category-id="{{ $category->id }}"
                         data-category-name="{{ strtolower($category->name) }}"
                         data-category-code="{{ strtolower($category->code ?? '') }}"
                         data-category-description="{{ strtolower($category->description ?? '') }}">

                        {{-- Card Header with Gradient --}}
                        <div class="gradient-bg px-4 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-white/80 flex items-center justify-center">
                                    <i class="fas fa-tag text-orange-500 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-sm category-name">{{ $category->name }}</h3>
                                    <p class="text-xs text-gray-500">Code: {{ $category->code ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                @if($category->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                        <i class="fas fa-circle text-[5px]"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                        <i class="fas fa-circle text-[5px]"></i> Inactive
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-4 space-y-3">
                            {{-- Description --}}
                            <div class="min-h-[60px]">
                                <p class="text-xs text-gray-500 line-clamp-2 category-description">{{ $category->description ?? 'No description provided.' }}</p>
                            </div>

                            {{-- Stats --}}
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-utensils text-xs text-gray-400"></i>
                                    <span class="text-xs text-gray-500">{{ $category->menuItems->count() }} menu items</span>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Sort: {{ $category->sort_order }}
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-2 pt-2">
                                <button type="button"
                                        class="edit-category flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg transition-colors"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-code="{{ $category->code }}"
                                        data-description="{{ $category->description }}"
                                        data-sort-order="{{ $category->sort_order }}"
                                        data-is-active="{{ $category->is_active }}">
                                    <i class="fas fa-edit text-xs"></i> Edit
                                </button>
                                <button type="button"
                                        class="delete-category flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg transition-colors"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}">
                                    <i class="fas fa-trash text-xs"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div id="emptyState" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tags text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">No Categories Yet</h3>
                <p class="text-sm text-gray-500 mb-4">Create categories to organize your menu items</p>
                <button type="button" id="emptyCreateBtn"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                    <i class="fas fa-plus"></i> Create First Category
                </button>
            </div>
        @endif
    </div>

    {{-- No Results Message (shown when search returns nothing) --}}
    <div id="noResultsMessage" class="hidden bg-white rounded-2xl border border-gray-200 shadow-sm p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-search text-3xl text-gray-300"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-1">No matching categories</h3>
        <p class="text-sm text-gray-500 mb-4">Try adjusting your search or create a new category</p>
        <button type="button" id="clearSearchBtn"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg transition-colors">
            <i class="fas fa-times"></i> Clear Search
        </button>
    </div>
</div>

{{-- Create/Edit Category Modal --}}
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-xl modal-enter">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center">
                    <i class="fas fa-tag text-white text-sm"></i>
                </div>
                <div>
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-800">Create Category</h3>
                    <p id="modalSubtitle" class="text-sm text-gray-500">Add a new category for menu items</p>
                </div>
            </div>
            <button type="button" class="closeModal text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" name="category_id" id="category_id">
                <input type="hidden" name="_method" id="form_method" value="POST">

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="category_name"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                               placeholder="e.g., Appetizers, Main Course, Desserts"
                               required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Code <span class="text-gray-400 font-normal">(optional - auto-generated if empty)</span>
                        </label>
                        <input type="text" name="code" id="category_code"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                               placeholder="e.g., APP, MAIN, DES">
                        <p class="text-xs text-gray-400 mt-1">Unique identifier. Leave empty to auto-generate.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                        <textarea name="description" id="category_description" rows="3"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none"
                                  placeholder="Brief description of this category..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" id="category_sort_order"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                               value="0">
                        <p class="text-xs text-gray-400 mt-1">Lower numbers appear first in the list.</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="is_active" id="category_is_active" checked class="accent-orange-500">
                            <span>Active (visible in menus)</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" class="closeModal px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                        <i class="fas fa-save mr-1"></i> <span id="submitBtnText">Save Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-xl modal-enter">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-red-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Delete Category</h3>
                    <p class="text-sm text-gray-500">This action cannot be undone</p>
                </div>
            </div>
            <button type="button" class="closeDeleteModal text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Are you sure you want to delete category <strong id="deleteCategoryName"></strong>?
            </p>
            <p class="text-xs text-red-500 mb-4">
                <i class="fas fa-warning mr-1"></i>
                Menu items in this category will become uncategorized. This action cannot be undone.
            </p>

            <div class="flex justify-end gap-3">
                <button type="button" class="closeDeleteModal px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                    Cancel
                </button>
                <button type="button" id="confirmDelete" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors">
                    <i class="fas fa-trash mr-1"></i> Delete Category
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    let deleteCategoryId = null;

    // ── LIVE SEARCH FUNCTIONALITY ─────────────────────────────────────
    function performLiveSearch() {
        let searchTerm = $('#liveSearch').val().toLowerCase().trim();
        let visibleCount = 0;

        if (searchTerm === '') {
            // Show all categories
            $('.category-card').each(function() {
                $(this).removeClass('hidden-card').show();
                visibleCount++;
            });
            $('#searchResultsCount').addClass('hidden');
            $('#noResultsMessage').addClass('hidden');
            if ($('#emptyState').length) {
                $('#emptyState').removeClass('hidden');
            }
        } else {
            // Filter categories
            $('.category-card').each(function() {
                let card = $(this);
                let categoryName = card.data('category-name') || '';
                let categoryCode = card.data('category-code') || '';
                let categoryDescription = card.data('category-description') || '';

                let matches = categoryName.includes(searchTerm) ||
                             categoryCode.includes(searchTerm) ||
                             categoryDescription.includes(searchTerm);

                if (matches) {
                    card.removeClass('hidden-card').show();
                    visibleCount++;
                } else {
                    card.addClass('hidden-card').hide();
                }
            });

            // Update results count
            if (visibleCount > 0) {
                $('#visibleCount').text(visibleCount);
                $('#searchResultsCount').removeClass('hidden');
                $('#noResultsMessage').addClass('hidden');
            } else {
                $('#searchResultsCount').addClass('hidden');
                $('#noResultsMessage').removeClass('hidden');
            }

            // Hide empty state if it exists
            if ($('#emptyState').length) {
                $('#emptyState').addClass('hidden');
            }
        }
    }

    // Debounce function for better performance
    let searchTimeout;
    $('#liveSearch').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performLiveSearch, 300);
    });

    // Clear search button
    $('#clearSearchBtn').on('click', function() {
        $('#liveSearch').val('');
        performLiveSearch();
    });

    // ── Open Create Modal ────────────────────────────────────────────
    $('#createCategoryBtn, #emptyCreateBtn').on('click', function() {
        $('#modalTitle').text('Create Category');
        $('#modalSubtitle').text('Add a new category for menu items');
        $('#submitBtnText').text('Save Category');
        $('#category_id').val('');
        $('#form_method').val('POST');
        $('#category_name').val('');
        $('#category_code').val('');
        $('#category_description').val('');
        $('#category_sort_order').val('0');
        $('#category_is_active').prop('checked', true);

        $('#categoryForm').attr('action', '{{ route("management.menu-item-categories.store") }}');
        $('#categoryForm').attr('method', 'POST');

        $('#categoryModal').removeClass('hidden').addClass('flex');
    });

    // ── Open Edit Modal ─────────────────────────────────────────────
    $('.edit-category').on('click', function() {
        let btn = $(this);
        let id = btn.data('id');
        let name = btn.data('name');
        let code = btn.data('code');
        let description = btn.data('description');
        let sortOrder = btn.data('sort-order');
        let isActive = btn.data('is-active');

        $('#modalTitle').text('Edit Category');
        $('#modalSubtitle').text('Update category information');
        $('#submitBtnText').text('Update Category');
        $('#category_id').val(id);
        $('#category_name').val(name);
        $('#category_code').val(code || '');
        $('#category_description').val(description || '');
        $('#category_sort_order').val(sortOrder || 0);
        $('#category_is_active').prop('checked', isActive == 1);

        let updateUrl = '{{ route("management.menu-item-categories.update", ":id") }}'.replace(':id', id);
        $('#categoryForm').attr('action', updateUrl);
        $('#form_method').val('PUT');

        $('#categoryModal').removeClass('hidden').addClass('flex');
    });

    // ── Submit Form (Create/Update) ─────────────────────────────────
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let method = $('#form_method').val();

        let formData = {
            _token: '{{ csrf_token() }}',
            name: $('#category_name').val(),
            code: $('#category_code').val(),
            description: $('#category_description').val(),
            sort_order: $('#category_sort_order').val(),
            is_active: $('#category_is_active').is(':checked') ? 1 : 0
        };

        if (method === 'PUT') {
            formData._method = 'PUT';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#categoryModal').addClass('hidden').removeClass('flex');
                    location.reload();
                } else {
                    alert(response.message || 'Failed to save category.');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMsg = Object.values(errors).flat().join('\n');
                    alert(errorMsg);
                } else {
                    alert('Failed to save category.');
                }
            }
        });
    });

    // ── Open Delete Modal ───────────────────────────────────────────
    $('.delete-category').on('click', function() {
        let btn = $(this);
        deleteCategoryId = btn.data('id');
        let categoryName = btn.data('name');

        $('#deleteCategoryName').text(categoryName);
        $('#deleteModal').removeClass('hidden').addClass('flex');
    });

    // ── Confirm Delete ──────────────────────────────────────────────
    $('#confirmDelete').on('click', function() {
        if (!deleteCategoryId) return;

        let deleteUrl = '{{ route("management.menu-item-categories.destroy", ":id") }}'.replace(':id', deleteCategoryId);

        $.ajax({
            url: deleteUrl,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').addClass('hidden').removeClass('flex');
                    location.reload();
                } else {
                    alert(response.message || 'Failed to delete category.');
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Failed to delete category.';
                alert(msg);
            }
        });
    });

    // ── Close Modals ────────────────────────────────────────────────
    $('.closeModal, .closeDeleteModal').on('click', function() {
        $('#categoryModal, #deleteModal').addClass('hidden').removeClass('flex');
    });

    $(window).on('click', function(e) {
        if ($(e.target).is('#categoryModal')) {
            $('#categoryModal').addClass('hidden').removeClass('flex');
        }
        if ($(e.target).is('#deleteModal')) {
            $('#deleteModal').addClass('hidden').removeClass('flex');
        }
    });

});
</script>
@endpush
