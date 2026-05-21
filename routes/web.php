<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Middleware\AllowFirstUserRegistration;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Procurement\PurchaseOrderController;
use App\Http\Controllers\Store\StoreDashboardController;
use App\Http\Controllers\Management\ManagerStockMovementController;
// =====================================================
// GUEST ROUTES
// =====================================================

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login.form');
});

Route::middleware([AllowFirstUserRegistration::class])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =====================================================
// PROTECTED ROUTES (Authentication required)
// =====================================================

Route::middleware(['auth'])->group(function () {

    // Main Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // =====================================================
    // USER Management Routes
    // =====================================================
    Route::resource('users', UserController::class);
    Route::patch('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::put('/users/{id}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');

    // =====================================================
    // DEPARTMENT Management Routes
    // =====================================================
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('show');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::patch('/{department}/activate', [DepartmentController::class, 'activate'])->name('activate');
        Route::patch('/{department}/deactivate', [DepartmentController::class, 'deactivate'])->name('deactivate');
        Route::patch('/{id}/restore', [DepartmentController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [DepartmentController::class, 'forceDelete'])->name('force-delete');
    });

    // =====================================================
    // ROLE Management Routes
    // =====================================================
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{id}', [RoleController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/activate', [RoleController::class, 'activate'])->name('activate');
        Route::patch('/{id}/deactivate', [RoleController::class, 'deactivate'])->name('deactivate');
    });

    // =====================================================
    // PERMISSION Management Routes
    // =====================================================
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/store', [PermissionController::class, 'store'])->name('store');
        Route::get('/{id}', [PermissionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/activate', [PermissionController::class, 'activate'])->name('activate');
        Route::patch('/{id}/deactivate', [PermissionController::class, 'deactivate'])->name('deactivate');
        Route::get('/group/{group}', [PermissionController::class, 'getByGroup'])->name('by-group');
        Route::get('/groups/list', [PermissionController::class, 'getGroups'])->name('groups');
    });
});

// =====================================================
// STORE MODULE Routes
// =====================================================
Route::prefix('store')->name('store.')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Store\StoreDashboardController::class, 'index'])->name('dashboard');

 // Inventory Management Routes (Full CRUD)
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/',                   [App\Http\Controllers\Store\InventoryController::class, 'index'])       ->name('index');
    Route::get('/create',             [App\Http\Controllers\Store\InventoryController::class, 'create'])      ->name('create');
    Route::post('/',                  [App\Http\Controllers\Store\InventoryController::class, 'store'])       ->name('store');
    Route::post('/store-from-grn',    [App\Http\Controllers\Store\InventoryController::class, 'storeFromGrn'])->name('store-from-grn');
    Route::get('/get-grn-items/{grnId}', [App\Http\Controllers\Store\InventoryController::class, 'getGrnItems'])->name('get-grn-items');
    Route::get('/barcode-lookup', [App\Http\Controllers\Store\InventoryController::class, 'barcodeLookup'])->name('barcode-lookup');
    // ⚠️  {id} routes must come AFTER named static routes
    Route::get('/{id}',              [App\Http\Controllers\Store\InventoryController::class, 'show'])         ->name('show');
    Route::get('/{id}/edit',         [App\Http\Controllers\Store\InventoryController::class, 'edit'])         ->name('edit');
    Route::put('/{id}',              [App\Http\Controllers\Store\InventoryController::class, 'update'])       ->name('update');
    Route::delete('/{id}',           [App\Http\Controllers\Store\InventoryController::class, 'destroy'])      ->name('destroy');

    // Stock adjustment — accessible as route('store.inventory.adjust', $id)
    Route::patch('/{id}/adjust',     [App\Http\Controllers\Store\InventoryController::class, 'adjustStock'])  ->name('adjust');
});

// Store - Department Requisitions Routes
Route::prefix('department-requisitions')->name('department-requisitions.')->group(function () {
    Route::get('/', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'show'])->name('show');
    Route::post('/{id}/approve', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'reject'])->name('reject');
    Route::get('/{id}/issue-form', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'issueForm'])->name('issue-form');
    Route::post('/{id}/issue', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'issue'])->name('issue');
    Route::get('/{id}/return-form', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'returnForm'])->name('return-form');
    Route::post('/{id}/return', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'processReturn'])->name('process-return');
});

    // Categories Routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [App\Http\Controllers\Store\CategoryController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Store\CategoryController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Store\CategoryController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [App\Http\Controllers\Store\CategoryController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\Store\CategoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\Store\CategoryController::class, 'destroy'])->name('destroy');
});


  // Stock Movements Routes
Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
    Route::get('/', [App\Http\Controllers\Store\StockMovementController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Store\StockMovementController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Store\StockMovementController::class, 'store'])->name('store');

    // Static routes FIRST (must come before {id})
    Route::get('/export-excel', [App\Http\Controllers\Store\StockMovementController::class, 'exportExcel'])->name('export-excel');
    Route::get('/export-pdf', [App\Http\Controllers\Store\StockMovementController::class, 'exportPdf'])->name('export-pdf');
    Route::get('/item/{itemId}/movements', [App\Http\Controllers\Store\StockMovementController::class, 'getItemMovements'])->name('item-movements');

    // Dynamic route LAST
    Route::get('/{id}', [App\Http\Controllers\Store\StockMovementController::class, 'show'])->name('show');
});

    // Requisitions (Store requests for stock)
    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\RequisitionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Store\RequisitionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Store\RequisitionController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Store\RequisitionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Store\RequisitionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Store\RequisitionController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Store\RequisitionController::class, 'destroy'])->name('destroy');
        Route::delete('/{id}/cancel', [App\Http\Controllers\Store\RequisitionController::class, 'cancel'])->name('cancel');
    });



    // Reports

});

// =====================================================
// PROCUREMENT MODULE Routes
// =====================================================
Route::prefix('procurement')->name('procurement.')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Procurement\ProcurementDashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications/check', [App\Http\Controllers\Procurement\NotificationController::class, 'check'])->name('notifications.check');

    // Requisitions
    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'reject'])->name('reject');
    });


    // Local Purchase Order (LPO) Routes
Route::prefix('lpo')->name('lpo.')->group(function () {
    Route::get('/create/{requisition_id}', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'createFromRequisition'])->name('create');
    Route::post('/store', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'show'])->name('show');
    Route::get('/', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'index'])->name('index');


});
Route::prefix('approved-lpos')->name('approved-lpos.')->group(function () {
    Route::get('/', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'approvedLpos'])->name('index');
    Route::get('/{id}/convert-to-epo', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'convertToEpo'])->name('convert-to-epo');
    Route::post('/{id}/store-epo', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'storeExternalPo'])->name('store-epo');
});

// Approved LPOs for External PO conversion
  // Purchase Orders
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/send', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'send'])->name('send');
        Route::post('/{id}/resend-email', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'resendEmail'])->name('resend-email');
        Route::get('/{id}/download-pdf', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'downloadPdf'])->name('download-pdf');
    });

    // Vendors
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\VendorController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Procurement\VendorController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Procurement\VendorController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Procurement\VendorController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Procurement\VendorController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Procurement\VendorController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Procurement\VendorController::class, 'destroy'])->name('destroy');
    });

    // Goods Received Routes
    Route::prefix('goods-received')->name('goods-received.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'store'])->name('store');
        Route::get('/get-po-items/{poId}', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'getPoItems'])->name('get-po-items');
        Route::get('/{id}', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'show'])->name('show');

     Route::get('/goods-received/{id}/download-pdf', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'downloadPdf'])->name('goods-received.download-pdf');

    Route::post('/{id}/send-email', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'sendEmail'])->name('send-email');
     Route::get('/{id}/download-pdf', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{id}/send-to-store', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'sendToStore'])->name('send-to-store');
    });

    Route::prefix('cost-prices')->name('cost-prices.')->group(function () {

        // Main index page
        Route::get('/', [App\Http\Controllers\Procurement\CostPriceController::class, 'index'])
            ->name('index');

        // Edit form
        Route::get('/{id}/edit', [App\Http\Controllers\Procurement\CostPriceController::class, 'edit'])
            ->name('edit');

        // Update simple item (per unit)
        Route::put('/{id}/simple', [App\Http\Controllers\Procurement\CostPriceController::class, 'updateSimple'])
            ->name('update.simple');

        // Update bulk item (per pack)
        Route::put('/{id}/bulk', [App\Http\Controllers\Procurement\CostPriceController::class, 'updateBulk'])
            ->name('update.bulk');

        // Get price history (AJAX)
        Route::get('/{id}/history', [App\Http\Controllers\Procurement\CostPriceController::class, 'getHistory'])
            ->name('history');

        // Bulk update multiple items
        Route::post('/bulk', [App\Http\Controllers\Procurement\CostPriceController::class, 'bulkUpdate'])
            ->name('bulk');
    });


});

/// =====================================================
// MANAGEMENT MODULE Routes
// =====================================================
Route::prefix('management')->name('management.')->middleware(['auth', 'management'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Management\ManagementController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports/purchase-orders', [App\Http\Controllers\Management\ManagementController::class, 'reportsPurchaseOrders'])->name('reports.purchase-orders');
    Route::get('/analytics/procurement', [App\Http\Controllers\Management\ManagementController::class, 'analyticsProcurement'])->name('analytics.procurement');
    Route::get('/vendors', [App\Http\Controllers\Management\ManagementController::class, 'vendorsIndex'])->name('vendors.index');
    Route::get('/vendors/{id}', [App\Http\Controllers\Management\ManagementController::class, 'vendorsShow'])->name('vendors.show');

    // Requisitions routes
    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'index'])->name('index');
        Route::get('/all', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'all'])->name('all');
        Route::get('/{id}', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'show'])->name('show');
        Route::get('/{id}/approve', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'approveForm'])->name('approve-form');
        Route::post('/{id}/approve', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'approve'])->name('approve');
        Route::get('/{id}/edit', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'update'])->name('update');
        Route::post('/{id}/reject', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'reject'])->name('reject');
    });

    // =====================================================
    // MENU MANAGEMENT ROUTES
    // =====================================================
    Route::prefix('menus')->name('menus.')->group(function () {

        // Menu CRUD
        Route::get('/', [App\Http\Controllers\Management\MenuController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Management\MenuController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Management\MenuController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Management\MenuController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Management\MenuController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Management\MenuController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Management\MenuController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Management\MenuController::class, 'toggleStatus'])->name('toggle-status');

        // Menu Items Management (under specific menu)
        Route::get('/{menuId}/items', [App\Http\Controllers\Management\MenuController::class, 'items'])->name('items');
        Route::post('/{menuId}/items', [App\Http\Controllers\Management\MenuController::class, 'storeItem'])->name('items.store');
        Route::put('/{menuId}/items/{itemId}', [App\Http\Controllers\Management\MenuController::class, 'updateItem'])->name('items.update');
        Route::delete('/{menuId}/items/{itemId}', [App\Http\Controllers\Management\MenuController::class, 'deleteItem'])->name('items.delete');
        Route::post('/{menuId}/items/{itemId}/toggle-status', [App\Http\Controllers\Management\MenuController::class, 'toggleItemStatus'])->name('items.toggle-status');

        // Recipe Items (Ingredients) under menu item
        Route::get('/{menuId}/items/{itemId}/recipe', [App\Http\Controllers\Management\MenuController::class, 'getRecipeItems'])->name('items.recipe.get');
        Route::post('/{menuId}/items/{itemId}/recipe', [App\Http\Controllers\Management\MenuController::class, 'storeRecipeItem'])->name('items.recipe.store');
        Route::put('/{menuId}/items/{itemId}/recipe/{recipeId}', [App\Http\Controllers\Management\MenuController::class, 'updateRecipeItem'])->name('items.recipe.update');
        Route::delete('/{menuId}/items/{itemId}/recipe/{recipeId}', [App\Http\Controllers\Management\MenuController::class, 'deleteRecipeItem'])->name('items.recipe.delete');

        // Bulk Operations & AJAX
        Route::get('/recalculate-costs', [App\Http\Controllers\Management\MenuController::class, 'recalculateAllCosts'])->name('recalculate');
        Route::get('/inventory-items', [App\Http\Controllers\Management\MenuController::class, 'getInventoryItems'])->name('inventory-items');
    });

    // =====================================================
    // STANDALONE MENU ITEMS (All items across all menus)
    // =====================================================
    Route::prefix('menu-items')->name('menu-items.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\MenuController::class, 'allItems'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Management\MenuController::class, 'getMenuItem'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Management\MenuController::class, 'updateMenuItem'])->name('update');
        Route::get('/{id}/recipe', [App\Http\Controllers\Management\MenuController::class, 'getMenuItemRecipe'])->name('recipe');
    });

    // =====================================================
    // PRICE MANAGEMENT ROUTES
    // =====================================================
    Route::prefix('prices')->name('prices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\PriceManagementController::class, 'index'])->name('index');
        Route::put('/menu/{id}', [App\Http\Controllers\Management\PriceManagementController::class, 'updateMenuItemPrice'])->name('update.menu');
        Route::put('/inventory/{id}', [App\Http\Controllers\Management\PriceManagementController::class, 'updateInventoryPrice'])->name('update.inventory');
        Route::patch('/toggle-sellable/{id}', [App\Http\Controllers\Management\PriceManagementController::class, 'toggleSellable'])->name('toggle-sellable');
        Route::post('/bulk-make-sellable', [App\Http\Controllers\Management\PriceManagementController::class, 'bulkMakeSellable'])->name('bulk-make-sellable');
        Route::post('/bulk-remove-sellable', [App\Http\Controllers\Management\PriceManagementController::class, 'bulkRemoveSellable'])->name('bulk-remove-sellable');
    });

    // =====================================================
    // STOCK MOVEMENTS & DISTRIBUTION (for Management/Manager)
    // =====================================================
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/', [ManagerStockMovementController::class, 'index'])->name('index');
        Route::get('/{id}', [ManagerStockMovementController::class, 'show'])->name('show');
        Route::get('/distribution/by-department', [ManagerStockMovementController::class, 'getDistributionByDepartment'])->name('distribution.by-department');
        Route::get('/export/excel', [ManagerStockMovementController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [ManagerStockMovementController::class, 'exportPdf'])->name('export.pdf');
    });

    // =====================================================
    // PURCHASE ORDERS MANAGEMENT
    // =====================================================
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\ManagerPurchaseOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Management\ManagerPurchaseOrderController::class, 'show'])->name('show');
        Route::get('/export/excel', [App\Http\Controllers\Management\ManagerPurchaseOrderController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Management\ManagerPurchaseOrderController::class, 'exportPdf'])->name('export.pdf');
    });

    // =====================================================
    // GOODS RECEIVED NOTES MANAGEMENT
    // =====================================================
    Route::prefix('grns')->name('grns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\ManagerGrnController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Management\ManagerGrnController::class, 'show'])->name('show');
        Route::get('/export/excel', [App\Http\Controllers\Management\ManagerGrnController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Management\ManagerGrnController::class, 'exportPdf'])->name('export.pdf');
    });

// =====================================================
// MENU ITEM CATEGORIES MANAGEMENT
// =====================================================
Route::prefix('menu-item-categories')->name('menu-item-categories.')->group(function () {
    Route::get('/', [App\Http\Controllers\Management\MenuItemCategoryController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\Management\MenuItemCategoryController::class, 'store'])->name('store');
    Route::put('/{id}', [App\Http\Controllers\Management\MenuItemCategoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\Management\MenuItemCategoryController::class, 'destroy'])->name('destroy');
});

});

// Director Module Routes
Route::prefix('director')->name('director.')->middleware(['auth', 'director'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Director\DirectorController::class, 'dashboard'])->name('dashboard');
    Route::get('/lpos', [App\Http\Controllers\Director\LpoController::class, 'index'])->name('lpos.index');
    Route::get('/lpos/{id}', [App\Http\Controllers\Director\LpoController::class, 'show'])->name('lpos.show');
    Route::post('/lpos/{id}/approve', [App\Http\Controllers\Director\LpoController::class, 'approve'])->name('lpos.approve');
    Route::post('/lpos/{id}/reject', [App\Http\Controllers\Director\LpoController::class, 'reject'])->name('lpos.reject');
    Route::get('/lpos/{id}/download-pdf', [App\Http\Controllers\Director\LpoController::class, 'downloadPdf'])->name('lpos.download-pdf');
});


// Kitchen Module Routes
Route::prefix('kitchen')->name('kitchen.')->middleware(['auth', 'kitchen'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Kitchen\KitchenController::class, 'dashboard'])->name('dashboard');

    // Kitchen Requisitions Routes
    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Kitchen\RequisitionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Kitchen\RequisitionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Kitchen\RequisitionController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Kitchen\RequisitionController::class, 'show'])->name('show');
        Route::delete('/{id}/cancel', [App\Http\Controllers\Kitchen\RequisitionController::class, 'cancel'])->name('cancel');
    });

        // Consumption Routes
    Route::prefix('consumption')->name('consumption.')->group(function () {

        // Main consumption index (list of requisitions with stock)
        Route::get('/', [App\Http\Controllers\Kitchen\KitchenConsumptionController::class, 'index'])
            ->name('index');

        // Show consumption form for a specific requisition
        Route::get('/{requisitionId}/create', [App\Http\Controllers\Kitchen\KitchenConsumptionController::class, 'create'])
            ->name('create');

        // Store consumption records
        Route::post('/{requisitionId}/store', [App\Http\Controllers\Kitchen\KitchenConsumptionController::class, 'store'])
            ->name('store');

        // View consumption history
        Route::get('/history', [App\Http\Controllers\Kitchen\KitchenConsumptionController::class, 'history'])
            ->name('history');
    });
});



// =====================================================
// RESTAURANT MODULE Routes
// =====================================================

Route::prefix('restaurant')->name('restaurant.')->middleware(['auth', 'restaurant'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Restaurant\RestaurantDashboardController::class, 'index'])->name('dashboard');

    // =====================================================
    // NOTIFICATIONS (AJAX endpoint for pending requisitions)
    // =====================================================
    Route::get('/notifications/check', [App\Http\Controllers\Restaurant\NotificationController::class, 'check'])->name('notifications.check');

    // =====================================================
    // REQUISITIONS (Restaurant requests items from store)
    // =====================================================
    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'show'])->name('show');
        Route::delete('/{id}/cancel', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'cancel'])->name('cancel');
        Route::get('/{id}/consume', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'consumeForm'])->name('consume');
        Route::post('/{id}/record-consumption', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'recordConsumption'])->name('record-consumption');
    });



    // =====================================================
    // MENU MANAGEMENT (Customer-facing menu)
    // =====================================================
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\MenuController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Restaurant\MenuController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Restaurant\MenuController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\Restaurant\MenuController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Restaurant\MenuController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Restaurant\MenuController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [App\Http\Controllers\Restaurant\MenuController::class, 'toggleStatus'])->name('toggle-status');
    });

    // =====================================================
    // SALES & CASHIER (Recording customer orders)
    // =====================================================
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\SalesController::class, 'index'])->name('index');
        Route::get('/pos', [App\Http\Controllers\Restaurant\SalesController::class, 'pos'])->name('pos');
        Route::post('/store', [App\Http\Controllers\Restaurant\SalesController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Restaurant\SalesController::class, 'show'])->name('show');
        Route::get('/receipt/{id}', [App\Http\Controllers\Restaurant\SalesController::class, 'receipt'])->name('receipt');
        Route::get('/report/daily', [App\Http\Controllers\Restaurant\SalesController::class, 'dailyReport'])->name('report.daily');
        Route::get('/export/excel', [App\Http\Controllers\Restaurant\SalesController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Restaurant\SalesController::class, 'exportPdf'])->name('export.pdf');
    });


    // =====================================================
    // STOCK (Restaurant's received items)
    // =====================================================
       Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\RestaurantStockController::class, 'index'])->name('index');
        Route::get('/summary', [App\Http\Controllers\Restaurant\RestaurantStockController::class, 'getSummary'])->name('summary');
    });

    // =====================================================
    // RETURNS (Restaurant returns items to store)
    // =====================================================
    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\RestaurantReturnController::class, 'index'])->name('index');
        Route::get('/create/{requisition_id}', [App\Http\Controllers\Restaurant\RestaurantReturnController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Restaurant\RestaurantReturnController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Restaurant\RestaurantReturnController::class, 'show'])->name('show');
    });



    // =====================================================
    // PROFILE & SETTINGS
    // =====================================================
    Route::get('/profile', [App\Http\Controllers\Restaurant\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Restaurant\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [App\Http\Controllers\Restaurant\SettingsController::class, 'index'])->name('settings');
});


// =====================================================
// CASHIER MODULE Routes (All URLs under /restaurant/cashier)
// =====================================================
Route::prefix('restaurant/cashier')->name('restaurant.cashier.')->middleware(['auth', 'cashier'])->group(function () {

    // =====================================================
    // DASHBOARD & MAIN PAGES
    // =====================================================

    Route::get('/dashboard', [App\Http\Controllers\Restaurant\CashierPosController::class, 'dashboard'])->name('dashboard');
    Route::get('/pos', [App\Http\Controllers\Restaurant\CashierPosController::class, 'pos'])->name('pos');
    Route::get('/menu', [App\Http\Controllers\Restaurant\CashierPosController::class, 'menu'])->name('menu');
    Route::get('/orders', [App\Http\Controllers\Restaurant\CashierPosController::class, 'orders'])->name('orders');

    // =====================================================
    // INVOICE & SALE PROCESSING
    // =====================================================

    Route::post('/create-invoice', [App\Http\Controllers\Restaurant\CashierPosController::class, 'createInvoice'])->name('create-invoice');
    Route::get('/invoice/{id}', [App\Http\Controllers\Restaurant\CashierPosController::class, 'getInvoice'])->name('invoice');
    Route::post('/sale/{id}/pay', [App\Http\Controllers\Restaurant\CashierPosController::class, 'markAsPaid'])->name('mark-as-paid');
    Route::get('/receipt/{id}', [App\Http\Controllers\Restaurant\CashierPosController::class, 'getReceipt'])->name('receipt');
    Route::get('/orders/{id}', [App\Http\Controllers\Restaurant\CashierPosController::class, 'showOrder'])->name('orders.show');

    // =====================================================
    // STOCK & AJAX
    // =====================================================
Route::get('/my-sales', [App\Http\Controllers\Restaurant\CashierSalesController::class, 'index'])->name('my-sales');
Route::get('/my-sales/export/excel', [App\Http\Controllers\Restaurant\CashierSalesController::class, 'exportExcel'])->name('my-sales.export.excel');
Route::get('/my-sales/export/pdf', [App\Http\Controllers\Restaurant\CashierSalesController::class, 'exportPdf'])->name('my-sales.export.pdf');
    // =====================================================
    // REPORTS
    // =====================================================

    Route::get('/daily-summary', [App\Http\Controllers\Restaurant\CashierPosController::class, 'dailySummary'])->name('daily-summary');
    Route::get('/reports', [App\Http\Controllers\Restaurant\CashierPosController::class, 'reports'])->name('reports');
    Route::get('/reports/daily', [App\Http\Controllers\Restaurant\CashierPosController::class, 'dailyReport'])->name('reports.daily');
    Route::get('/reports/export/excel', [App\Http\Controllers\Restaurant\CashierPosController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [App\Http\Controllers\Restaurant\CashierPosController::class, 'exportPdf'])->name('reports.export.pdf');
});

// =====================================================
// BAR MODULE Routes
// =====================================================
Route::prefix('bar')->name('bar.')->middleware(['auth', 'bar'])->group(function () {

    // =====================================================
    // BAR MANAGER / ADMIN Routes
    // =====================================================

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Bar\BarDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Bar\BarProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Bar\BarProfileController::class, 'update'])->name('profile.update');

    // Point of Sale (POS) - Manager can also access
    Route::get('/pos', [App\Http\Controllers\Bar\BarPosController::class, 'index'])->name('pos');
    Route::post('/create-invoice', [App\Http\Controllers\Bar\BarPosController::class, 'createInvoice'])->name('create-invoice');
    Route::post('/sale/{id}/pay', [App\Http\Controllers\Bar\BarPosController::class, 'markAsPaid'])->name('mark-as-paid');
    Route::get('/orders', [App\Http\Controllers\Bar\BarPosController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'showOrder'])->name('orders.show');
    Route::get('/invoice/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'getInvoice'])->name('invoice');
    Route::get('/receipt/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'getReceipt'])->name('receipt');

    // Stock Management
    Route::get('/stock', [App\Http\Controllers\Bar\BarStockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{id}', [App\Http\Controllers\Bar\BarStockController::class, 'show'])->name('stock.show');

    // =====================================================
    // REQUISITIONS (Complete)
    // =====================================================
    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bar\BarRequisitionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Bar\BarRequisitionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Bar\BarRequisitionController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Bar\BarRequisitionController::class, 'show'])->name('show');
        Route::put('/{id}/cancel', [App\Http\Controllers\Bar\BarRequisitionController::class, 'cancel'])->name('cancel');
        Route::get('/{id}/consume', [App\Http\Controllers\Bar\BarRequisitionController::class, 'consumeForm'])->name('consume');
        Route::post('/{id}/consume', [App\Http\Controllers\Bar\BarRequisitionController::class, 'recordConsumption'])->name('consume.store');
    });

    // =====================================================
    // SALES REPORTS
    // =====================================================
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bar\BarSalesController::class, 'index'])->name('index');
        Route::get('/export/excel', [App\Http\Controllers\Bar\BarSalesController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Bar\BarSalesController::class, 'exportPdf'])->name('export.pdf');
    });

    // =====================================================
    // INVOICES / PAYSLIPS
    // =====================================================
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bar\BarInvoiceController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Bar\BarInvoiceController::class, 'show'])->name('show');
        Route::get('/receipt/{id}', [App\Http\Controllers\Bar\BarInvoiceController::class, 'receipt'])->name('receipt');
    });

    // =====================================================
    // CASHIERS MANAGEMENT (Manager only)
    // =====================================================
    Route::prefix('cashiers')->name('cashiers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bar\BarCashierController::class, 'index'])->name('index');
        Route::get('/{id}/sales', [App\Http\Controllers\Bar\BarCashierController::class, 'sales'])->name('sales');
    });

    // =====================================================
    // REPORTS
    // =====================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [App\Http\Controllers\Bar\BarReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [App\Http\Controllers\Bar\BarReportController::class, 'monthly'])->name('monthly');
        Route::get('/export/excel', [App\Http\Controllers\Bar\BarReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Bar\BarReportController::class, 'exportPdf'])->name('export.pdf');
    });

    // =====================================================
    // NOTIFICATIONS
    // =====================================================
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/check', [App\Http\Controllers\Bar\BarNotificationController::class, 'check'])->name('check');
        Route::post('/mark-read/{id}', [App\Http\Controllers\Bar\BarNotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [App\Http\Controllers\Bar\BarNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // =====================================================
    // MY SALES (Cashier personal sales)
    // =====================================================
    Route::get('/my-sales', [App\Http\Controllers\Bar\BarCashierController::class, 'mySales'])->name('my-sales');
    Route::get('/my-sales/export/excel', [App\Http\Controllers\Bar\BarCashierController::class, 'exportExcel'])->name('my-sales.export.excel');
    Route::get('/my-sales/export/pdf', [App\Http\Controllers\Bar\BarCashierController::class, 'exportPdf'])->name('my-sales.export.pdf');

    // =====================================================
    // BAR CASHIER Routes (Same as Restaurant Cashier)
    // =====================================================
    // =====================================================
// BAR CASHIER Routes (Same as Restaurant Cashier)
// =====================================================
Route::prefix('cashier')->name('cashier.')->group(function () {

    // Cashier Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Bar\BarCashierController::class, 'dashboard'])->name('dashboard');

    // Point of Sale
    Route::get('/pos', [App\Http\Controllers\Bar\BarPosController::class, 'index'])->name('pos');
    Route::post('/create-invoice', [App\Http\Controllers\Bar\BarPosController::class, 'createInvoice'])->name('create-invoice');
    Route::post('/sale/{id}/pay', [App\Http\Controllers\Bar\BarPosController::class, 'markAsPaid'])->name('mark-as-paid');

    // Bar Menu (Read-only for cashier)
    Route::get('/menu', [App\Http\Controllers\Bar\BarPosController::class, 'menu'])->name('menu');

    // Orders
    Route::get('/orders', [App\Http\Controllers\Bar\BarPosController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'showOrder'])->name('orders.show');
    // Add this route to your bar/cashier group
    Route::post('/create-and-pay', [App\Http\Controllers\Bar\BarPosController::class, 'createAndPayInvoice'])->name('create-and-pay');
    // Invoice & Receipt
    Route::get('/invoice/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'getInvoice'])->name('invoice');
    Route::get('/receipt/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'getReceipt'])->name('receipt');
    // Invoices list
    Route::get('/invoices', [App\Http\Controllers\Bar\BarPosController::class, 'invoices'])->name('invoices');
    // My Sales (Cashier personal sales)
    Route::get('/my-sales', [App\Http\Controllers\Bar\BarCashierController::class, 'mySales'])->name('my-sales');
    Route::get('/my-sales/export/excel', [App\Http\Controllers\Bar\BarCashierController::class, 'exportExcel'])->name('my-sales.export.excel');
    Route::get('/my-sales/export/pdf', [App\Http\Controllers\Bar\BarCashierController::class, 'exportPdf'])->name('my-sales.export.pdf');
    // Bar Products (Sellable inventory items for cashier)
    Route::get('/products', [App\Http\Controllers\Bar\BarProductController::class, 'index'])->name('products');
    // Reports

    Route::get('/reports/monthly', [App\Http\Controllers\Bar\BarCashierController::class, 'monthlyReport'])->name('reports.monthly');
Route::get('/reports/export/excel', [App\Http\Controllers\Bar\BarCashierController::class, 'exportReportExcel'])->name('reports.export.excel');
Route::get('/reports/export/pdf', [App\Http\Controllers\Bar\BarCashierController::class, 'exportReportPdf'])->name('reports.export.pdf');
    Route::get('/reports', [App\Http\Controllers\Bar\BarCashierController::class, 'reports'])->name('reports');
    Route::get('/daily-summary', [App\Http\Controllers\Bar\BarPosController::class, 'dailySummary'])->name('daily-summary');
});

});

