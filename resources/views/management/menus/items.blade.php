@extends('layouts.app')

@section('title', 'Menu Items - ' . $menu->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Menu Items: {{ $menu->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('management.menus.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Menus
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addItemModal">
                            <i class="fas fa-plus"></i> Add Menu Item
                        </button>
                        <a href="{{ route('management.menus.items.recalculate', $menu->id) }}" class="btn btn-warning btn-sm" onclick="return confirm('Recalculate costs for all menu items?')">
                            <i class="fas fa-calculator"></i> Recalculate Costs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Selling Price</th>
                                    <th>Material Cost</th>
                                    <th>Margin %</th>
                                    <th>Glovo Price</th>
                                    <th>Final Margin</th>
                                    <th>Status</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menuItems as $index => $item)
                                <tr id="item-row-{{ $item->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $item->name }}
                                        @if($item->description)
                                            <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->category ? $item->category->name : 'Uncategorized' }}</td>
                                    <td class="selling-price-{{ $item->id }}">{{ number_format($item->selling_price, 2) }} UGX</td>
                                    <td class="material-cost-{{ $item->id }}">{{ number_format($item->material_cost ?? 0, 2) }} UGX</td>
                                    <td class="margin-{{ $item->id }}">
                                        @php
                                            $margin = $item->selling_price > 0 ? (($item->selling_price - ($item->material_cost ?? 0)) / $item->selling_price) * 100 : 0;
                                        @endphp
                                        <span class="badge {{ $margin >= 50 ? 'badge-success' : ($margin >= 30 ? 'badge-warning' : 'badge-danger') }}">
                                            {{ number_format($margin, 2) }}%
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $glovoPrice = $item->selling_price * 0.7;
                                            $glovoCommission = $glovoPrice * 0.2;
                                            $glovoMargin = $glovoPrice - ($item->material_cost ?? 0) - $glovoCommission;
                                        @endphp
                                        {{ number_format($glovoPrice, 2) }} UGX
                                    </td>
                                    <td>
                                        <span class="badge {{ $glovoMargin > 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ number_format($glovoMargin, 2) }} UGX
                                        </span>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-item-status"
                                                   id="toggle-{{ $item->id }}" data-id="{{ $item->id }}"
                                                   {{ $item->is_active ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="toggle-{{ $item->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-item"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-description="{{ $item->description }}"
                                                data-category-id="{{ $item->menu_item_category_id }}"
                                                data-price="{{ $item->selling_price }}"
                                                data-time="{{ $item->preparation_time }}"
                                                data-allergen="{{ $item->allergen_info }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary manage-recipe"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}">
                                            <i class="fas fa-utensils"></i> Recipe
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-item"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No menu items found. Click "Add Menu Item" to create one.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================== -->
<!-- MODALS -->
<!-- ===================================================== -->

<!-- Add/Edit Menu Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalTitle">Add Menu Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="itemForm" method="POST">
                @csrf
                <input type="hidden" id="item_id" name="item_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Item Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="menu_item_category_id">Category</label>
                                <select class="form-control" id="menu_item_category_id" name="menu_item_category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="selling_price">Selling Price (UGX) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="selling_price" name="selling_price" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="preparation_time">Preparation Time (minutes)</label>
                                <input type="number" class="form-control" id="preparation_time" name="preparation_time">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="allergen_info">Allergen Information</label>
                                <textarea class="form-control" id="allergen_info" name="allergen_info" rows="2" placeholder="e.g., Contains dairy, gluten, nuts"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">Active (visible on menu)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Recipe Items Modal (Ingredients) -->
<div class="modal fade" id="recipeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recipe Ingredients: <span id="recipeMenuItemName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="recipe_menu_item_id" value="">

                <!-- Cost Summary Card -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-tags"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Selling Price</span>
                                <span class="info-box-number" id="summary_selling_price">0 UGX</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Material Cost</span>
                                <span class="info-box-number" id="summary_material_cost">0 UGX</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Current Margin</span>
                                <span class="info-box-number" id="summary_margin">0%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Ingredient Form -->
                <div class="card card-primary card-outline mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Add Ingredient</h5>
                    </div>
                    <div class="card-body">
                        <form id="addIngredientForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="inventory_item_id">Ingredient <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="inventory_item_id" name="inventory_item_id" required style="width: 100%">
                                            <option value="">-- Search Ingredient --</option>
                                            @foreach($inventoryItems as $invItem)
                                                <option value="{{ $invItem->id }}" data-unit="{{ $invItem->base_unit }}" data-cost="{{ $invItem->unit_cost }}">
                                                    {{ $invItem->name }} ({{ $invItem->base_unit }} - {{ number_format($invItem->unit_cost, 2) }} UGX)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="quantity_required">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" step="0.001" class="form-control" id="quantity_required" name="quantity_required" placeholder="e.g., 0.050" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="unit_of_measure_id">Unit</label>
                                        <select class="form-control" id="unit_of_measure_id" name="unit_of_measure_id">
                                            @foreach($unitsOfMeasure as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->symbol ?? $unit->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="wastage_percentage">Wastage %</label>
                                        <input type="number" step="0.1" class="form-control" id="wastage_percentage" name="wastage_percentage" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-sm">Add Ingredient</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Ingredients List Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="recipeItemsTable">
                        <thead>
                            <tr>
                                <th>Ingredient</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Unit Cost</th>
                                <th>Total Cost</th>
                                <th>Wastage</th>
                                <th width="80">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recipeItemsList">
                            <tr>
                                <td colspan="7" class="text-center">Select a menu item to view ingredients</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total Material Cost:</th>
                                <th colspan="3" id="totalMaterialCost">0 UGX</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        // Initialize Select2 for ingredient search
        $('.select2').select2({
            dropdownParent: $('#recipeModal'),
            placeholder: 'Search ingredient...',
            allowClear: true
        });

        // =====================================================
        // ADD / EDIT MENU ITEM
        // =====================================================

        // Add Item button
        $('#addItemModal').on('show.bs.modal', function(e) {
            $('#itemModalTitle').text('Add Menu Item');
            $('#itemForm').attr('action', '{{ route("management.menus.items.store", $menu->id) }}');
            $('#item_id').val('');
            $('#name').val('');
            $('#description').val('');
            $('#menu_item_category_id').val('');
            $('#selling_price').val('');
            $('#preparation_time').val('');
            $('#allergen_info').val('');
            $('#is_active').prop('checked', true);
            $('#itemForm').trigger('reset');
        });

        // Edit Item button
        $('.edit-item').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var description = $(this).data('description');
            var categoryId = $(this).data('category-id');
            var price = $(this).data('price');
            var time = $(this).data('time');
            var allergen = $(this).data('allergen');

            $('#itemModalTitle').text('Edit Menu Item');
            $('#item_id').val(id);
            $('#name').val(name);
            $('#description').val(description);
            $('#menu_item_category_id').val(categoryId);
            $('#selling_price').val(price);
            $('#preparation_time').val(time);
            $('#allergen_info').val(allergen);
            $('#is_active').prop('checked', true);

            var url = '{{ route("management.menus.items.update", ["menuId" => $menu->id, "itemId" => ":id"]) }}';
            url = url.replace(':id', id);
            $('#itemForm').attr('action', url);

            $('#addItemModal').modal('show');
        });

        // Submit Item Form
        $('#itemForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var method = $('#item_id').val() ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addItemModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        var errorMsg = Object.values(errors).flat().join('\n');
                        alert(errorMsg);
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });

        // =====================================================
        // DELETE MENU ITEM
        // =====================================================

        var deleteId = null;

        $('.delete-item').on('click', function() {
            deleteId = $(this).data('id');
            var name = $(this).data('name');
            $('#deleteMessage').text('Are you sure you want to delete "' + name + '"? This will also remove all recipe ingredients.');
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').on('click', function() {
            if (deleteId) {
                var url = '{{ route("management.menus.items.delete", ["menuId" => $menu->id, "itemId" => ":id"]) }}';
                url = url.replace(':id', deleteId);

                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $('#deleteModal').modal('hide');
                            $('#item-row-' + deleteId).remove();
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Failed to delete menu item.');
                    }
                });
            }
        });

        // =====================================================
        // TOGGLE MENU ITEM STATUS
        // =====================================================

        $('.toggle-item-status').on('change', function() {
            var id = $(this).data('id');
            var url = '{{ route("management.menus.items.toggle", ["menuId" => $menu->id, "itemId" => ":id"]) }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (!response.success) {
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to toggle status.');
                    location.reload();
                }
            });
        });

        // =====================================================
        // RECIPE ITEMS (INGREDIENTS)
        // =====================================================

        $('.manage-recipe').on('click', function() {
            var menuItemId = $(this).data('id');
            var menuItemName = $(this).data('name');

            $('#recipeMenuItemName').text(menuItemName);
            $('#recipe_menu_item_id').val(menuItemId);

            // Load recipe items
            loadRecipeItems(menuItemId);

            $('#recipeModal').modal('show');
        });

        function loadRecipeItems(menuItemId) {
            var url = '{{ route("management.menus.items.recipe.get", ["menuId" => $menu->id, "itemId" => ":itemId"]) }}';
            url = url.replace(':itemId', menuItemId);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Update summary
                        $('#summary_selling_price').text(parseFloat(response.selling_price).toLocaleString() + ' UGX');
                        $('#summary_material_cost').text(parseFloat(response.material_cost).toLocaleString() + ' UGX');
                        $('#summary_margin').text(response.current_margin + '%');
                        $('#totalMaterialCost').text(parseFloat(response.material_cost).toLocaleString() + ' UGX');

                        // Render recipe items table
                        var tbody = $('#recipeItemsList');
                        tbody.empty();

                        if (response.recipe_items.length === 0) {
                            tbody.append('<tr><td colspan="7" class="text-center">No ingredients added yet.</td></tr>');
                        } else {
                            $.each(response.recipe_items, function(index, item) {
                                var totalCost = (item.quantity_required * item.inventory_item.unit_cost).toFixed(2);
                                var wastageText = item.wastage_percentage > 0 ? item.wastage_percentage + '%' : '-';

                                var row = '<tr id="recipe-row-' + item.id + '">';
                                row += '<td>' + item.inventory_item.name + '</td>';
                                row += '<td>' + item.quantity_required + '</td>';
                                row += '<td>' + (item.unit_of_measure ? item.unit_of_measure.symbol : item.inventory_item.base_unit) + '</td>';
                                row += '<td>' + parseFloat(item.inventory_item.unit_cost).toLocaleString() + ' UGX</td>';
                                row += '<td>' + parseFloat(totalCost).toLocaleString() + ' UGX</td>';
                                row += '<td>' + wastageText + '</td>';
                                row += '<td>';
                                row += '<button type="button" class="btn btn-sm btn-warning edit-recipe" data-id="' + item.id + '" data-qty="' + item.quantity_required + '" data-unit="' + (item.unit_of_measure_id || '') + '" data-wastage="' + (item.wastage_percentage || 0) + '"><i class="fas fa-edit"></i></button> ';
                                row += '<button type="button" class="btn btn-sm btn-danger delete-recipe" data-id="' + item.id + '" data-name="' + item.inventory_item.name + '"><i class="fas fa-trash"></i></button>';
                                row += '</td>';
                                row += '</tr>';
                                tbody.append(row);
                            });
                        }
                    }
                },
                error: function() {
                    alert('Failed to load recipe items.');
                }
            });
        }

        // Add Ingredient
        $('#addIngredientForm').on('submit', function(e) {
            e.preventDefault();

            var menuItemId = $('#recipe_menu_item_id').val();
            if (!menuItemId) {
                alert('Please select a menu item first.');
                return;
            }

            var inventoryItemId = $('#inventory_item_id').val();
            var quantity = $('#quantity_required').val();
            var unitId = $('#unit_of_measure_id').val();
            var wastage = $('#wastage_percentage').val() || 0;

            if (!inventoryItemId || !quantity) {
                alert('Please select an ingredient and enter quantity.');
                return;
            }

            var url = '{{ route("management.menus.items.recipe.store", ["menuId" => $menu->id, "itemId" => ":itemId"]) }}';
            url = url.replace(':itemId', menuItemId);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    inventory_item_id: inventoryItemId,
                    quantity_required: quantity,
                    unit_of_measure_id: unitId,
                    wastage_percentage: wastage
                },
                success: function(response) {
                    if (response.success) {
                        $('#addIngredientForm')[0].reset();
                        $('#inventory_item_id').val('').trigger('change');
                        loadRecipeItems(menuItemId);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON?.message || 'Failed to add ingredient.';
                    alert(msg);
                }
            });
        });

        // Delete Recipe Item
        $(document).on('click', '.delete-recipe', function() {
            var recipeId = $(this).data('id');
            var ingredientName = $(this).data('name');
            var menuItemId = $('#recipe_menu_item_id').val();

            if (confirm('Remove "' + ingredientName + '" from this recipe?')) {
                var url = '{{ route("management.menus.items.recipe.delete", ["menuId" => $menu->id, "itemId" => ":itemId", "recipeId" => ":recipeId"]) }}';
                url = url.replace(':itemId', menuItemId);
                url = url.replace(':recipeId', recipeId);

                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            loadRecipeItems(menuItemId);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Failed to remove ingredient.');
                    }
                });
            }
        });

        // Edit Recipe Item
        $(document).on('click', '.edit-recipe', function() {
            var recipeId = $(this).data('id');
            var currentQty = $(this).data('qty');
            var currentUnit = $(this).data('unit');
            var currentWastage = $(this).data('wastage');
            var menuItemId = $('#recipe_menu_item_id').val();

            var newQty = prompt('Enter new quantity:', currentQty);
            if (newQty && !isNaN(newQty) && parseFloat(newQty) > 0) {
                var url = '{{ route("management.menus.items.recipe.update", ["menuId" => $menu->id, "itemId" => ":itemId", "recipeId" => ":recipeId"]) }}';
                url = url.replace(':itemId', menuItemId);
                url = url.replace(':recipeId', recipeId);

                $.ajax({
                    url: url,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        quantity_required: newQty,
                        unit_of_measure_id: currentUnit,
                        wastage_percentage: currentWastage
                    },
                    success: function(response) {
                        if (response.success) {
                            loadRecipeItems(menuItemId);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Failed to update quantity.');
                    }
                });
            }
        });

        // Update unit cost display when ingredient selected
        $('#inventory_item_id').on('change', function() {
            var selected = $(this).find('option:selected');
            var unit = selected.data('unit');
            var cost = selected.data('cost');

            $('#quantity_required').attr('placeholder', 'e.g., 0.050 (in ' + unit + ')');
        });
    });
</script>
@endpush
