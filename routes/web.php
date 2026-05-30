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
// GUEST ROUTES (No permission checks)
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

    // Main Dashboard - NO permission check
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // =====================================================
    // USER Management Routes (with permission checks)
    // =====================================================
    Route::resource('users', UserController::class)->middleware('permission:view_users');
    Route::patch('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate')->middleware('permission:edit_users');
    Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate')->middleware('permission:edit_users');
    Route::put('/users/{id}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password')->middleware('permission:edit_users');

    // =====================================================
    // DEPARTMENT Management Routes (with permission checks)
    // =====================================================
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index')->middleware('permission:view_departments');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create')->middleware('permission:manage_departments');
        Route::post('/', [DepartmentController::class, 'store'])->name('store')->middleware('permission:manage_departments');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('show')->middleware('permission:view_departments');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit')->middleware('permission:manage_departments');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update')->middleware('permission:manage_departments');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy')->middleware('permission:manage_departments');
        Route::patch('/{department}/activate', [DepartmentController::class, 'activate'])->name('activate')->middleware('permission:manage_departments');
        Route::patch('/{department}/deactivate', [DepartmentController::class, 'deactivate'])->name('deactivate')->middleware('permission:manage_departments');
        Route::patch('/{id}/restore', [DepartmentController::class, 'restore'])->name('restore')->middleware('permission:manage_departments');
        Route::delete('/{id}/force-delete', [DepartmentController::class, 'forceDelete'])->name('force-delete')->middleware('permission:manage_departments');
    });

    // =====================================================
    // ROLE Management Routes (with permission checks)
    // =====================================================
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('permission:view_roles');
        Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:create_roles');
        Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('permission:create_roles');
        Route::get('/{id}', [RoleController::class, 'show'])->name('show')->middleware('permission:view_roles');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:edit_roles');
        Route::put('/{id}', [RoleController::class, 'update'])->name('update')->middleware('permission:edit_roles');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:delete_roles');
        Route::patch('/{id}/activate', [RoleController::class, 'activate'])->name('activate')->middleware('permission:edit_roles');
        Route::patch('/{id}/deactivate', [RoleController::class, 'deactivate'])->name('deactivate')->middleware('permission:edit_roles');
    });

    // =====================================================
    // PERMISSION Management Routes (with permission checks)
    // =====================================================
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index')->middleware('permission:view_permissions');
        Route::get('/create', [PermissionController::class, 'create'])->name('create')->middleware('permission:create_permissions');
        Route::post('/store', [PermissionController::class, 'store'])->name('store')->middleware('permission:create_permissions');
        Route::get('/{id}', [PermissionController::class, 'show'])->name('show')->middleware('permission:view_permissions');
        Route::get('/{id}/edit', [PermissionController::class, 'edit'])->name('edit')->middleware('permission:edit_permissions');
        Route::put('/{id}', [PermissionController::class, 'update'])->name('update')->middleware('permission:edit_permissions');
        Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('destroy')->middleware('permission:delete_permissions');
        Route::patch('/{id}/activate', [PermissionController::class, 'activate'])->name('activate')->middleware('permission:edit_permissions');
        Route::patch('/{id}/deactivate', [PermissionController::class, 'deactivate'])->name('deactivate')->middleware('permission:edit_permissions');
        Route::get('/group/{group}', [PermissionController::class, 'getByGroup'])->name('by-group')->middleware('permission:view_permissions');
        Route::get('/groups/list', [PermissionController::class, 'getGroups'])->name('groups')->middleware('permission:view_permissions');
    });
});

// =====================================================
// STORE MODULE Routes
// =====================================================
Route::prefix('store')->name('store.')->middleware(['auth'])->group(function () {

    // Dashboard - NO permission check
    Route::get('/dashboard', [App\Http\Controllers\Store\StoreDashboardController::class, 'index'])->name('dashboard');

    // Inventory Management Routes (with permission checks)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\InventoryController::class, 'index'])->name('index')->middleware('permission:view_inventory');
        Route::get('/create', [App\Http\Controllers\Store\InventoryController::class, 'create'])->name('create')->middleware('permission:create_inventory');
        Route::post('/', [App\Http\Controllers\Store\InventoryController::class, 'store'])->name('store')->middleware('permission:create_inventory');
        Route::post('/store-from-grn', [App\Http\Controllers\Store\InventoryController::class, 'storeFromGrn'])->name('store-from-grn')->middleware('permission:create_inventory');
        Route::get('/get-grn-items/{grnId}', [App\Http\Controllers\Store\InventoryController::class, 'getGrnItems'])->name('get-grn-items')->middleware('permission:view_inventory');
        Route::get('/barcode-lookup', [App\Http\Controllers\Store\InventoryController::class, 'barcodeLookup'])->name('barcode-lookup')->middleware('permission:view_inventory');
        Route::get('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'show'])->name('show')->middleware('permission:view_inventory');
        Route::get('/{id}/edit', [App\Http\Controllers\Store\InventoryController::class, 'edit'])->name('edit')->middleware('permission:edit_inventory');
        Route::put('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'update'])->name('update')->middleware('permission:edit_inventory');
        Route::delete('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'destroy'])->name('destroy')->middleware('permission:delete_inventory');
        Route::patch('/{id}/adjust', [App\Http\Controllers\Store\InventoryController::class, 'adjustStock'])->name('adjust')->middleware('permission:adjust_inventory');
    });

    // Empty Bottle Weight Management Routes
    Route::prefix('empty-bottle-weights')->name('empty-bottle-weights.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\EmptyBottleWeightController::class, 'index'])->name('index')->middleware('permission:view_inventory');
        Route::get('/{id}/edit', [App\Http\Controllers\Store\EmptyBottleWeightController::class, 'edit'])->name('edit')->middleware('permission:edit_inventory');
        Route::put('/{id}', [App\Http\Controllers\Store\EmptyBottleWeightController::class, 'update'])->name('update')->middleware('permission:edit_inventory');
    });

    Route::prefix('stock-counts')->name('stock-counts.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\StoreStockCountController::class, 'index'])->name('index')->middleware('permission:view_stock_counts');
        Route::get('/create', [App\Http\Controllers\Store\StoreStockCountController::class, 'create'])->name('create')->middleware('permission:create_stock_counts');
        Route::post('/', [App\Http\Controllers\Store\StoreStockCountController::class, 'store'])->name('store')->middleware('permission:create_stock_counts');
        Route::get('/get-item-stock/{id}', [App\Http\Controllers\Store\StoreStockCountController::class, 'getItemStock'])->name('get-item-stock')->middleware('permission:view_stock_counts');
        Route::get('/get-department-items/{departmentId}', [App\Http\Controllers\Store\StoreStockCountController::class, 'getDepartmentItems'])->name('get-department-items')->middleware('permission:view_stock_counts');
        Route::post('/calculate-net-quantity', [App\Http\Controllers\Store\StoreStockCountController::class, 'calculateNetQuantity'])->name('calculate-net-quantity')->middleware('permission:view_stock_counts');
        Route::get('/{id}/download-pdf', [App\Http\Controllers\Store\StoreStockCountController::class, 'downloadPdf'])->name('download-pdf')->middleware('permission:export_reports');
        Route::get('/{id}/review', [App\Http\Controllers\Store\StoreStockCountController::class, 'review'])->name('review')->middleware('permission:approve_stock_counts');
        Route::post('/{id}/review-approve', [App\Http\Controllers\Store\StoreStockCountController::class, 'reviewApprove'])->name('review-approve')->middleware('permission:approve_stock_counts');
        Route::get('/{id}/edit', [App\Http\Controllers\Store\StoreStockCountController::class, 'editCount'])->name('edit-count')->middleware('permission:edit_stock_counts');
        Route::get('/{id}/approve-count', [App\Http\Controllers\Store\StoreStockCountController::class, 'approveCountForm'])->name('approve-count')->middleware('permission:approve_stock_counts');
        Route::post('/{id}/approve-count-submit', [App\Http\Controllers\Store\StoreStockCountController::class, 'approveCountSubmit'])->name('approve-count-submit')->middleware('permission:approve_stock_counts');
        Route::get('/{id}', [App\Http\Controllers\Store\StoreStockCountController::class, 'show'])->name('show')->middleware('permission:view_stock_counts');
        Route::put('/{id}/items', [App\Http\Controllers\Store\StoreStockCountController::class, 'updateItems'])->name('update-items')->middleware('permission:edit_stock_counts');

        Route::post('/{id}/submit', [App\Http\Controllers\Store\StoreStockCountController::class, 'submit'])->name('submit')->middleware('permission:edit_stock_counts');
        Route::post('/{id}/complete', [App\Http\Controllers\Store\StoreStockCountController::class, 'complete'])->name('complete')->middleware('permission:edit_stock_counts');
        Route::delete('/{id}/cancel', [App\Http\Controllers\Store\StoreStockCountController::class, 'cancel'])->name('cancel')->middleware('permission:edit_stock_counts');
        Route::post('/{id}/items/{itemId}/approve-variance', [App\Http\Controllers\Store\StoreStockCountController::class, 'approveVariance'])->name('approve-variance')->middleware('permission:approve_stock_counts');
    });

    // Store - Department Requisitions Routes
    Route::prefix('department-requisitions')->name('department-requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'index'])->name('index')->middleware('permission:view_requisitions');
        Route::get('/{id}', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'show'])->name('show')->middleware('permission:view_requisitions');
        Route::post('/{id}/approve', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'approve'])->name('approve')->middleware('permission:approve_requisitions');
        Route::post('/{id}/reject', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'reject'])->name('reject')->middleware('permission:approve_requisitions');
        Route::get('/{id}/issue-form', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'issueForm'])->name('issue-form')->middleware('permission:issue_items');
        Route::post('/{id}/issue', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'issue'])->name('issue')->middleware('permission:issue_items');
        Route::get('/{id}/return-form', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'returnForm'])->name('return-form')->middleware('permission:return_items');
        Route::post('/{id}/return', [App\Http\Controllers\Store\DepartmentRequisitionController::class, 'processReturn'])->name('process-return')->middleware('permission:return_items');
    });

    // Categories Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\CategoryController::class, 'index'])->name('index')->middleware('permission:view_categories');
        Route::get('/create', [App\Http\Controllers\Store\CategoryController::class, 'create'])->name('create')->middleware('permission:manage_categories');
        Route::post('/', [App\Http\Controllers\Store\CategoryController::class, 'store'])->name('store')->middleware('permission:manage_categories');
        Route::get('/{id}/edit', [App\Http\Controllers\Store\CategoryController::class, 'edit'])->name('edit')->middleware('permission:manage_categories');
        Route::put('/{id}', [App\Http\Controllers\Store\CategoryController::class, 'update'])->name('update')->middleware('permission:manage_categories');
        Route::delete('/{id}', [App\Http\Controllers\Store\CategoryController::class, 'destroy'])->name('destroy')->middleware('permission:manage_categories');
    });
// Batch Management Routes
Route::prefix('batches')->name('batches.')->group(function () {
    Route::get('/', [App\Http\Controllers\Store\BatchController::class, 'index'])->name('index')->middleware('permission:view_batches');
    Route::get('/{id}', [App\Http\Controllers\Store\BatchController::class, 'show'])->name('show')->middleware('permission:view_batches');
    Route::get('/{id}/edit', [App\Http\Controllers\Store\BatchController::class, 'edit'])->name('edit')->middleware('permission:edit_batches');
    Route::put('/{id}/expiry', [App\Http\Controllers\Store\BatchController::class, 'updateExpiry'])->name('update-expiry')->middleware('permission:edit_batches');
    Route::put('/{id}/manufacture-date', [App\Http\Controllers\Store\BatchController::class, 'updateManufactureDate'])->name('update-manufacture-date')->middleware('permission:edit_batches');
    Route::put('/{id}/quantity', [App\Http\Controllers\Store\BatchController::class, 'adjustQuantity'])->name('adjust-quantity')->middleware('permission:adjust_batch_quantity');
});
    // Stock Movements Routes
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\StockMovementController::class, 'index'])->name('index')->middleware('permission:view_stock_movements');
        Route::get('/create', [App\Http\Controllers\Store\StockMovementController::class, 'create'])->name('create')->middleware('permission:create_stock_movements');
        Route::post('/', [App\Http\Controllers\Store\StockMovementController::class, 'store'])->name('store')->middleware('permission:create_stock_movements');
        Route::get('/export-excel', [App\Http\Controllers\Store\StockMovementController::class, 'exportExcel'])->name('export-excel')->middleware('permission:export_reports');
        Route::get('/export-pdf', [App\Http\Controllers\Store\StockMovementController::class, 'exportPdf'])->name('export-pdf')->middleware('permission:export_reports');
        Route::get('/item/{itemId}/movements', [App\Http\Controllers\Store\StockMovementController::class, 'getItemMovements'])->name('item-movements')->middleware('permission:view_stock_movements');
        Route::get('/{id}', [App\Http\Controllers\Store\StockMovementController::class, 'show'])->name('show')->middleware('permission:view_stock_movements');
    });

// Requisitions (Store requests for stock)
Route::prefix('requisitions')->name('requisitions.')->group(function () {
    Route::get('/', [App\Http\Controllers\Store\RequisitionController::class, 'index'])->name('index')->middleware('permission:view_requisitions');
    Route::get('/create', [App\Http\Controllers\Store\RequisitionController::class, 'create'])->name('create')->middleware('permission:create_requisitions');
    Route::post('/', [App\Http\Controllers\Store\RequisitionController::class, 'store'])->name('store')->middleware('permission:create_requisitions');
    Route::get('/{id}', [App\Http\Controllers\Store\RequisitionController::class, 'show'])->name('show')->middleware('permission:view_requisitions');
    Route::get('/{id}/edit', [App\Http\Controllers\Store\RequisitionController::class, 'edit'])->name('edit')->middleware('permission:edit_requisitions');
    Route::put('/{id}', [App\Http\Controllers\Store\RequisitionController::class, 'update'])->name('update')->middleware('permission:edit_requisitions');
    Route::delete('/{id}', [App\Http\Controllers\Store\RequisitionController::class, 'destroy'])->name('destroy')->middleware('permission:delete_requisitions');
    Route::delete('/{id}/cancel', [App\Http\Controllers\Store\RequisitionController::class, 'cancel'])->name('cancel')->middleware('permission:edit_requisitions');

    // New routes
    Route::get('/item-stock/{id}', [App\Http\Controllers\Store\RequisitionController::class, 'getItemStock'])->name('item-stock')->middleware('permission:view_requisitions');
    Route::get('/{id}/pdf', [App\Http\Controllers\Store\RequisitionController::class, 'exportPdf'])->name('pdf')->middleware('permission:view_requisitions');
});
});

// =====================================================
// PROCUREMENT MODULE Routes
// =====================================================
Route::prefix('procurement')->name('procurement.')->middleware(['auth'])->group(function () {

    // Dashboard - NO permission check
    Route::get('/dashboard', [App\Http\Controllers\Procurement\ProcurementDashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifications/check', [App\Http\Controllers\Procurement\NotificationController::class, 'check'])->name('notifications.check')->middleware('permission:view_procurement');

    // Requisitions
    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'index'])->name('index')->middleware('permission:view_requisitions');
        Route::get('/{id}', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'show'])->name('show')->middleware('permission:view_requisitions');
        Route::post('/{id}/approve', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'approve'])->name('approve')->middleware('permission:approve_requisitions');
        Route::post('/{id}/reject', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'reject'])->name('reject')->middleware('permission:approve_requisitions');
    });

    // Vendor Ratings
    Route::prefix('vendor-ratings')->name('vendor-ratings.')->group(function () {
        Route::get('/create/{grnId}', [App\Http\Controllers\Procurement\VendorRatingController::class, 'create'])->name('create')->middleware('permission:rate_vendors');
        Route::post('/store/{grnId}', [App\Http\Controllers\Procurement\VendorRatingController::class, 'store'])->name('store')->middleware('permission:rate_vendors');
        Route::get('/vendor/{vendorId}', [App\Http\Controllers\Procurement\VendorRatingController::class, 'vendorRatings'])->name('vendor')->middleware('permission:view_vendors');
        Route::get('/', [App\Http\Controllers\Procurement\VendorRatingController::class, 'index'])->name('index')->middleware('permission:view_vendors');
        Route::delete('/{id}', [App\Http\Controllers\Procurement\VendorRatingController::class, 'destroy'])->name('destroy')->middleware('permission:delete_vendor_ratings');
    });

    // Local Purchase Order (LPO)
    Route::prefix('lpo')->name('lpo.')->group(function () {
        Route::get('/create/{requisition_id}', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'createFromRequisition'])->name('create')->middleware('permission:create_purchase_orders');
        Route::post('/store', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'store'])->name('store')->middleware('permission:create_purchase_orders');
        Route::get('/{id}', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'show'])->name('show')->middleware('permission:view_purchase_orders');
        Route::get('/', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'index'])->name('index')->middleware('permission:view_purchase_orders');
    });

    Route::prefix('approved-lpos')->name('approved-lpos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'approvedLpos'])->name('index')->middleware('permission:view_purchase_orders');
        Route::get('/{id}/convert-to-epo', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'convertToEpo'])->name('convert-to-epo')->middleware('permission:create_purchase_orders');
        Route::post('/{id}/store-epo', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'storeExternalPo'])->name('store-epo')->middleware('permission:create_purchase_orders');
    });

    // Purchase Orders
Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
    Route::get('/', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'index'])->name('index')->middleware('permission:view_purchase_orders');
    Route::get('/create', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'create'])->name('create')->middleware('permission:create_purchase_orders');
    Route::post('/', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'store'])->name('store')->middleware('permission:create_purchase_orders');
    Route::get('/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'show'])->name('show')->middleware('permission:view_purchase_orders');
    Route::get('/{id}/edit', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'edit'])->name('edit')->middleware('permission:edit_purchase_orders');
    Route::put('/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'update'])->name('update')->middleware('permission:edit_purchase_orders');
    Route::delete('/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'destroy'])->name('destroy')->middleware('permission:delete_purchase_orders');
    Route::post('/{id}/send', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'send'])->name('send')->middleware('permission:send_purchase_orders');
    Route::post('/{id}/resend-email', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'resendEmail'])->name('resend-email')->middleware('permission:send_purchase_orders');
    Route::get('/{id}/download-pdf', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'downloadPdf'])->name('download-pdf')->middleware('permission:view_purchase_orders');

    // Document routes
    Route::post('/attach-document', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'attachDocument'])->name('attach-document')->middleware('permission:upload_documents');
    Route::get('/preview-document/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'previewDocument'])->name('preview-document')->middleware('permission:read_documents');
    Route::get('/download-document/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'downloadDocument'])->name('download-document')->middleware('permission:download_files');
    Route::delete('/delete-document/{id}', [App\Http\Controllers\Procurement\PurchaseOrderController::class, 'deleteDocument'])->name('delete-document')->middleware('permission:delete_documents');
});

    // Vendors
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\VendorController::class, 'index'])->name('index')->middleware('permission:view_vendors');
        Route::get('/create', [App\Http\Controllers\Procurement\VendorController::class, 'create'])->name('create')->middleware('permission:create_vendors');
        Route::post('/', [App\Http\Controllers\Procurement\VendorController::class, 'store'])->name('store')->middleware('permission:create_vendors');
        Route::get('/{id}', [App\Http\Controllers\Procurement\VendorController::class, 'show'])->name('show')->middleware('permission:view_vendors');
        Route::get('/{id}/edit', [App\Http\Controllers\Procurement\VendorController::class, 'edit'])->name('edit')->middleware('permission:edit_vendors');
        Route::put('/{id}', [App\Http\Controllers\Procurement\VendorController::class, 'update'])->name('update')->middleware('permission:edit_vendors');
        Route::delete('/{id}', [App\Http\Controllers\Procurement\VendorController::class, 'destroy'])->name('destroy')->middleware('permission:delete_vendors');
    });

Route::prefix('goods-received')->name('goods-received.')->group(function () {
    Route::get('/', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'index'])
        ->name('index')
        ->middleware('permission:view_goods_received');

    Route::get('/create', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'create'])
        ->name('create')
        ->middleware('permission:receive_goods');

    Route::post('/store', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'store'])
        ->name('store')
        ->middleware('permission:receive_goods');

    Route::get('/get-purchase-orders', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'getPurchaseOrders'])
        ->name('get-purchase-orders')
        ->middleware('permission:view_purchase_orders');

    Route::get('/create-form/{poId}', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'createFormAjax'])
        ->name('create-form')
        ->middleware('permission:receive_goods');

    Route::get('/create-for-po/{poId}', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'createForPo'])
        ->name('create-for-po')
        ->middleware('permission:receive_goods');

    Route::get('/{id}', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'show'])
        ->name('show')
        ->middleware('permission:view_goods_received');

    Route::get('/{id}/download-pdf', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'downloadPdf'])
        ->name('download-pdf')
        ->middleware('permission:download_files');

    Route::get('/{id}/print', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'print'])
        ->name('print')
        ->middleware('permission:view_goods_received');

    Route::post('/{id}/send-email', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'sendEmail'])
        ->name('send-email')
        ->middleware('permission:receive_goods');

    Route::post('/{id}/send-to-store', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'sendToStore'])
        ->name('send-to-store')
        ->middleware('permission:receive_goods');

    // Document routes - USING THE NEW PERMISSIONS
    Route::post('/{id}/attach-document', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'attachDocument'])
        ->name('attach-document')
        ->middleware('permission:upload_documents');

    Route::get('/download-document/{id}', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'downloadDocument'])
        ->name('download-document')
        ->middleware('permission:download_files');

    Route::get('/preview-document/{id}', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'previewDocument'])
        ->name('preview-document')
        ->middleware('permission:read_documents');
        Route::delete('/delete-document/{id}', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'deleteDocument'])
    ->name('delete-document')
    ->middleware('permission:delete_documents');
});

    // Cost Prices
    Route::prefix('cost-prices')->name('cost-prices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\CostPriceController::class, 'index'])->name('index')->middleware('permission:view_inventory');
        Route::get('/{id}/edit', [App\Http\Controllers\Procurement\CostPriceController::class, 'edit'])->name('edit')->middleware('permission:edit_inventory');
        Route::put('/{id}/simple', [App\Http\Controllers\Procurement\CostPriceController::class, 'updateSimple'])->name('update.simple')->middleware('permission:edit_inventory');
        Route::put('/{id}/bulk', [App\Http\Controllers\Procurement\CostPriceController::class, 'updateBulk'])->name('update.bulk')->middleware('permission:edit_inventory');
        Route::get('/{id}/history', [App\Http\Controllers\Procurement\CostPriceController::class, 'getHistory'])->name('history')->middleware('permission:view_inventory');
        Route::post('/bulk', [App\Http\Controllers\Procurement\CostPriceController::class, 'bulkUpdate'])->name('bulk')->middleware('permission:edit_inventory');
    });
});

// =====================================================
// MANAGEMENT MODULE Routes
// =====================================================
Route::prefix('management')->name('management.')->middleware(['auth'])->group(function () {

    // Dashboard - NO permission check
    Route::get('/dashboard', [App\Http\Controllers\Management\ManagementController::class, 'dashboard'])->name('dashboard');

    Route::get('/reports/purchase-orders', [App\Http\Controllers\Management\ManagementController::class, 'reportsPurchaseOrders'])->name('reports.purchase-orders')->middleware('permission:view_financial_reports');
    Route::get('/analytics/procurement', [App\Http\Controllers\Management\ManagementController::class, 'analyticsProcurement'])->name('analytics.procurement')->middleware('permission:view_financial_reports');
    Route::get('/vendors', [App\Http\Controllers\Management\ManagementController::class, 'vendorsIndex'])->name('vendors.index')->middleware('permission:view_vendors');
    Route::get('/vendors/{id}', [App\Http\Controllers\Management\ManagementController::class, 'vendorsShow'])->name('vendors.show')->middleware('permission:view_vendors');

    Route::get('/department-requisitions', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'index'])->name('department-requisitions.index')->middleware('permission:view_requisitions');
    Route::get('/department-requisitions/json', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'getRequisitionsJson'])->name('department-requisitions.json')->middleware('permission:view_requisitions');
    Route::get('/department-requisitions/summary', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'getSummary'])->name('department-requisitions.summary')->middleware('permission:view_requisitions');
    Route::get('/department-requisitions/{id}', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'show'])->name('department-requisitions.show')->middleware('permission:view_requisitions');
    Route::get('/department-requisitions/{id}/approve', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'approveForm'])->name('department-requisitions.approve-form')->middleware('permission:approve_requisitions');
    Route::post('/department-requisitions/{id}/approve', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'processApproval'])->name('department-requisitions.approve')->middleware('permission:approve_requisitions');
    Route::get('/department-requisitions/{id}/reject', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'rejectForm'])->name('department-requisitions.reject-form')->middleware('permission:approve_requisitions');
    Route::post('/department-requisitions/{id}/reject', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'processRejection'])->name('department-requisitions.reject')->middleware('permission:approve_requisitions');
    Route::put('/department-requisitions/{id}/quantities', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'updateApprovedQuantities'])->name('department-requisitions.update-quantities')->middleware('permission:approve_requisitions');
    Route::post('/department-requisitions/bulk-approve', [App\Http\Controllers\Management\DepartmentRequisitionController::class, 'bulkApprove'])->name('department-requisitions.bulk-approve')->middleware('permission:approve_requisitions');

    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'index'])->name('index')->middleware('permission:view_requisitions');
        Route::get('/all', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'all'])->name('all')->middleware('permission:view_requisitions');
        Route::get('/{id}', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'show'])->name('show')->middleware('permission:view_requisitions');
        Route::get('/{id}/approve', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'approveForm'])->name('approve-form')->middleware('permission:approve_requisitions');
        Route::post('/{id}/approve', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'approve'])->name('approve')->middleware('permission:approve_requisitions');
        Route::get('/{id}/edit', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'edit'])->name('edit')->middleware('permission:edit_requisitions');
        Route::put('/{id}', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'update'])->name('update')->middleware('permission:edit_requisitions');
        Route::post('/{id}/reject', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'reject'])->name('reject')->middleware('permission:approve_requisitions');
    });

    Route::prefix('stock-counts')->name('stock-counts.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\StockCountController::class, 'index'])->name('index')->middleware('permission:view_stock_counts');
        Route::get('/create', [App\Http\Controllers\Management\StockCountController::class, 'create'])->name('create')->middleware('permission:create_stock_counts');
        Route::post('/', [App\Http\Controllers\Management\StockCountController::class, 'store'])->name('store')->middleware('permission:create_stock_counts');
        Route::get('/get-item-stock/{id}', [App\Http\Controllers\Management\StockCountController::class, 'getItemStock'])->name('get-item-stock')->middleware('permission:view_stock_counts');
        Route::get('/get-department-items/{departmentId}', [App\Http\Controllers\Management\StockCountController::class, 'getDepartmentItems'])->name('get-department-items')->middleware('permission:view_stock_counts');
        Route::post('/calculate-net-quantity', [App\Http\Controllers\Management\StockCountController::class, 'calculateNetQuantity'])->name('calculate-net-quantity')->middleware('permission:view_stock_counts');
        Route::get('/{id}/download-pdf', [App\Http\Controllers\Management\StockCountController::class, 'downloadPdf'])->name('download-pdf')->middleware('permission:export_reports');
       Route::get('/{id}/review', [App\Http\Controllers\Management\StockCountController::class, 'review'])->name('review')->middleware('permission:approve_stock_counts');
        Route::post('/{id}/review-approve', [App\Http\Controllers\Management\StockCountController::class, 'reviewApprove'])->name('review-approve')->middleware('permission:approve_stock_counts');
        Route::get('/{id}/edit', [App\Http\Controllers\Management\StockCountController::class, 'editCount'])->name('edit-count')->middleware('permission:edit_stock_counts');
        Route::get('/{id}/approve-count', [App\Http\Controllers\Management\StockCountController::class, 'approveCountForm'])->name('approve-count')->middleware('permission:approve_stock_counts');
        Route::post('/{id}/approve-count-submit', [App\Http\Controllers\Management\StockCountController::class, 'approveCountSubmit'])->name('approve-count-submit')->middleware('permission:approve_stock_counts');
        Route::get('/{id}', [App\Http\Controllers\Management\StockCountController::class, 'show'])->name('show')->middleware('permission:view_stock_counts');
        Route::put('/{id}/items', [App\Http\Controllers\Management\StockCountController::class, 'updateItems'])->name('update-items')->middleware('permission:edit_stock_counts');
        Route::post('/{id}/submit', [App\Http\Controllers\Management\StockCountController::class, 'submit'])->name('submit')->middleware('permission:edit_stock_counts');
        Route::post('/{id}/complete', [App\Http\Controllers\Management\StockCountController::class, 'complete'])->name('complete')->middleware('permission:edit_stock_counts');
        Route::delete('/{id}/cancel', [App\Http\Controllers\Management\StockCountController::class, 'cancel'])->name('cancel')->middleware('permission:edit_stock_counts');
        Route::post('/{id}/items/{itemId}/approve-variance', [App\Http\Controllers\Management\StockCountController::class, 'approveVariance'])->name('approve-variance')->middleware('permission:approve_stock_counts');
    });

    Route::prefix('variance-reasons')->name('variance-reasons.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'index'])->name('index')->middleware('permission:view_stock_counts');
        Route::get('/create', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'create'])->name('create')->middleware('permission:manage_stock_variance_reasons');
        Route::post('/', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'store'])->name('store')->middleware('permission:manage_stock_variance_reasons');
        Route::get('/{id}', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'show'])->name('show')->middleware('permission:view_stock_counts');
        Route::get('/{id}/edit', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'edit'])->name('edit')->middleware('permission:manage_stock_variance_reasons');
        Route::put('/{id}', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'update'])->name('update')->middleware('permission:manage_stock_variance_reasons');
        Route::delete('/{id}', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'destroy'])->name('destroy')->middleware('permission:manage_stock_variance_reasons');
        Route::post('/{id}/toggle-active', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'toggleActive'])->name('toggle-active')->middleware('permission:manage_stock_variance_reasons');
        Route::get('/{id}/approve-variances', [App\Http\Controllers\Management\StockCountController::class, 'approveVariancesForm'])->name('approve-variances')->middleware('permission:approve_stock_counts');
        Route::get('/api/active', [App\Http\Controllers\Management\StockVarianceReasonController::class, 'getActiveReasons'])->name('api.active')->middleware('permission:view_stock_counts');
    });

    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\MenuController::class, 'index'])->name('index')->middleware('permission:view_menus');
        Route::get('/create', [App\Http\Controllers\Management\MenuController::class, 'create'])->name('create')->middleware('permission:create_menus');
        Route::post('/', [App\Http\Controllers\Management\MenuController::class, 'store'])->name('store')->middleware('permission:create_menus');
        Route::get('/{id}', [App\Http\Controllers\Management\MenuController::class, 'show'])->name('show')->middleware('permission:view_menus');
        Route::get('/{id}/edit', [App\Http\Controllers\Management\MenuController::class, 'edit'])->name('edit')->middleware('permission:edit_menus');
        Route::put('/{id}', [App\Http\Controllers\Management\MenuController::class, 'update'])->name('update')->middleware('permission:edit_menus');
        Route::delete('/{id}', [App\Http\Controllers\Management\MenuController::class, 'destroy'])->name('destroy')->middleware('permission:delete_menus');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Management\MenuController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:edit_menus');
        Route::get('/{menuId}/items', [App\Http\Controllers\Management\MenuController::class, 'items'])->name('items')->middleware('permission:view_menu_items');
        Route::post('/{menuId}/items', [App\Http\Controllers\Management\MenuController::class, 'storeItem'])->name('items.store')->middleware('permission:create_menu_items');
        Route::put('/{menuId}/items/{itemId}', [App\Http\Controllers\Management\MenuController::class, 'updateItem'])->name('items.update')->middleware('permission:edit_menu_items');
        Route::delete('/{menuId}/items/{itemId}', [App\Http\Controllers\Management\MenuController::class, 'deleteItem'])->name('items.delete')->middleware('permission:delete_menu_items');
        Route::post('/{menuId}/items/{itemId}/toggle-status', [App\Http\Controllers\Management\MenuController::class, 'toggleItemStatus'])->name('items.toggle-status')->middleware('permission:edit_menu_items');
        Route::get('/{menuId}/items/{itemId}/recipe', [App\Http\Controllers\Management\MenuController::class, 'getRecipeItems'])->name('items.recipe.get')->middleware('permission:view_recipes');
        Route::post('/{menuId}/items/{itemId}/recipe', [App\Http\Controllers\Management\MenuController::class, 'storeRecipeItem'])->name('items.recipe.store')->middleware('permission:manage_recipes');
        Route::put('/{menuId}/items/{itemId}/recipe/{recipeId}', [App\Http\Controllers\Management\MenuController::class, 'updateRecipeItem'])->name('items.recipe.update')->middleware('permission:manage_recipes');
        Route::delete('/{menuId}/items/{itemId}/recipe/{recipeId}', [App\Http\Controllers\Management\MenuController::class, 'deleteRecipeItem'])->name('items.recipe.delete')->middleware('permission:manage_recipes');
        Route::get('/recalculate-costs', [App\Http\Controllers\Management\MenuController::class, 'recalculateAllCosts'])->name('recalculate')->middleware('permission:manage_recipes');
        Route::get('/inventory-items', [App\Http\Controllers\Management\MenuController::class, 'getInventoryItems'])->name('inventory-items')->middleware('permission:view_inventory');
    });

    Route::prefix('menu-items')->name('menu-items.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\MenuController::class, 'allItems'])->name('index')->middleware('permission:view_menu_items');
        Route::get('/create', [App\Http\Controllers\Management\MenuController::class, 'createItem'])->name('create')->middleware('permission:create_menu_items');
        Route::post('/', [App\Http\Controllers\Management\MenuController::class, 'storeItemStandalone'])->name('store')->middleware('permission:create_menu_items');
        Route::get('/{id}/edit', [App\Http\Controllers\Management\MenuController::class, 'editItem'])->name('edit')->middleware('permission:edit_menu_items');
        Route::get('/{id}', [App\Http\Controllers\Management\MenuController::class, 'getMenuItem'])->name('show')->middleware('permission:view_menu_items');
        Route::put('/{id}', [App\Http\Controllers\Management\MenuController::class, 'updateMenuItem'])->name('update')->middleware('permission:edit_menu_items');
        Route::delete('/{id}', [App\Http\Controllers\Management\MenuController::class, 'deleteMenuItem'])->name('destroy')->middleware('permission:delete_menu_items');
        Route::get('/{id}/recipe', [App\Http\Controllers\Management\MenuController::class, 'getMenuItemRecipe'])->name('recipe')->middleware('permission:view_recipes');
    });

    Route::prefix('prices')->name('prices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\PriceManagementController::class, 'index'])->name('index')->middleware('permission:view_prices');
        Route::put('/menu/{id}', [App\Http\Controllers\Management\PriceManagementController::class, 'updateMenuItemPrice'])->name('update.menu')->middleware('permission:edit_prices');
        Route::put('/inventory/{id}', [App\Http\Controllers\Management\PriceManagementController::class, 'updateInventoryPrice'])->name('update.inventory')->middleware('permission:edit_prices');
        Route::patch('/toggle-sellable/{id}', [App\Http\Controllers\Management\PriceManagementController::class, 'toggleSellable'])->name('toggle-sellable')->middleware('permission:edit_prices');
        Route::post('/bulk-make-sellable', [App\Http\Controllers\Management\PriceManagementController::class, 'bulkMakeSellable'])->name('bulk-make-sellable')->middleware('permission:edit_prices');
        Route::post('/bulk-remove-sellable', [App\Http\Controllers\Management\PriceManagementController::class, 'bulkRemoveSellable'])->name('bulk-remove-sellable')->middleware('permission:edit_prices');
    });

    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/', [ManagerStockMovementController::class, 'index'])->name('index')->middleware('permission:view_stock_movements');
        Route::get('/{id}', [ManagerStockMovementController::class, 'show'])->name('show')->middleware('permission:view_stock_movements');
        Route::get('/distribution/by-department', [ManagerStockMovementController::class, 'getDistributionByDepartment'])->name('distribution.by-department')->middleware('permission:view_stock_movements');
        Route::get('/export/excel', [ManagerStockMovementController::class, 'exportExcel'])->name('export.excel')->middleware('permission:export_reports');
        Route::get('/export/pdf', [ManagerStockMovementController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:export_reports');
    });

    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\ManagerPurchaseOrderController::class, 'index'])->name('index')->middleware('permission:view_purchase_orders');
        Route::get('/{id}', [App\Http\Controllers\Management\ManagerPurchaseOrderController::class, 'show'])->name('show')->middleware('permission:view_purchase_orders');
        Route::get('/export/excel', [App\Http\Controllers\Management\ManagerPurchaseOrderController::class, 'exportExcel'])->name('export.excel')->middleware('permission:export_reports');
        Route::get('/export/pdf', [App\Http\Controllers\Management\ManagerPurchaseOrderController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:export_reports');
    });

    Route::prefix('grns')->name('grns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\ManagerGrnController::class, 'index'])->name('index')->middleware('permission:view_goods_received');
        Route::get('/{id}', [App\Http\Controllers\Management\ManagerGrnController::class, 'show'])->name('show')->middleware('permission:view_goods_received');
        Route::get('/export/excel', [App\Http\Controllers\Management\ManagerGrnController::class, 'exportExcel'])->name('export.excel')->middleware('permission:export_reports');
        Route::get('/export/pdf', [App\Http\Controllers\Management\ManagerGrnController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:export_reports');
    });

    Route::prefix('menu-item-categories')->name('menu-item-categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\Management\MenuItemCategoryController::class, 'index'])->name('index')->middleware('permission:view_menu_item_categories');
        Route::post('/', [App\Http\Controllers\Management\MenuItemCategoryController::class, 'store'])->name('store')->middleware('permission:manage_menu_item_categories');
        Route::put('/{id}', [App\Http\Controllers\Management\MenuItemCategoryController::class, 'update'])->name('update')->middleware('permission:manage_menu_item_categories');
        Route::delete('/{id}', [App\Http\Controllers\Management\MenuItemCategoryController::class, 'destroy'])->name('destroy')->middleware('permission:manage_menu_item_categories');
    });


    // =====================================================
// RESTAURANT TABLES MANAGEMENT (Under Management Module)
// =====================================================
Route::prefix('tables')->name('tables.')->group(function () {
    Route::get('/', [App\Http\Controllers\Management\RestaurantTableController::class, 'index'])->name('index')->middleware('permission:view_tables');
    Route::get('/create', [App\Http\Controllers\Management\RestaurantTableController::class, 'create'])->name('create')->middleware('permission:create_tables');
    Route::post('/', [App\Http\Controllers\Management\RestaurantTableController::class, 'store'])->name('store')->middleware('permission:create_tables');
    Route::get('/{id}', [App\Http\Controllers\Management\RestaurantTableController::class, 'show'])->name('show')->middleware('permission:view_tables');
    Route::get('/{id}/edit', [App\Http\Controllers\Management\RestaurantTableController::class, 'edit'])->name('edit')->middleware('permission:edit_tables');
    Route::put('/{id}', [App\Http\Controllers\Management\RestaurantTableController::class, 'update'])->name('update')->middleware('permission:edit_tables');
    Route::delete('/{id}', [App\Http\Controllers\Management\RestaurantTableController::class, 'destroy'])->name('destroy')->middleware('permission:delete_tables');
    Route::post('/{id}/toggle-reserved', [App\Http\Controllers\Management\RestaurantTableController::class, 'toggleReserved'])->name('toggle-reserved')->middleware('permission:edit_tables');
    Route::post('/{id}/toggle-active', [App\Http\Controllers\Management\RestaurantTableController::class, 'toggleActive'])->name('toggle-active')->middleware('permission:edit_tables');
});

// =====================================================
// TABLE RESERVATIONS MANAGEMENT (Under Management Module)
// =====================================================
Route::prefix('reservations')->name('reservations.')->group(function () {
    Route::get('/', [App\Http\Controllers\Management\TableReservationController::class, 'index'])->name('index')->middleware('permission:view_reservations');
    Route::get('/create', [App\Http\Controllers\Management\TableReservationController::class, 'create'])->name('create')->middleware('permission:create_reservations');
    Route::post('/', [App\Http\Controllers\Management\TableReservationController::class, 'store'])->name('store')->middleware('permission:create_reservations');
    Route::get('/{id}', [App\Http\Controllers\Management\TableReservationController::class, 'show'])->name('show')->middleware('permission:view_reservations');
    Route::get('/{id}/edit', [App\Http\Controllers\Management\TableReservationController::class, 'edit'])->name('edit')->middleware('permission:edit_reservations');
    Route::put('/{id}', [App\Http\Controllers\Management\TableReservationController::class, 'update'])->name('update')->middleware('permission:edit_reservations');
    Route::delete('/{id}', [App\Http\Controllers\Management\TableReservationController::class, 'destroy'])->name('destroy')->middleware('permission:delete_reservations');
    Route::put('/{id}/update-status', [App\Http\Controllers\Management\TableReservationController::class, 'updateStatus'])->name('update-status')->middleware('permission:edit_reservations');
    Route::get('/available-tables', [App\Http\Controllers\Management\TableReservationController::class, 'getAvailableTables'])->name('available-tables')->middleware('permission:view_reservations');
});

});

// =====================================================
// DIRECTOR MODULE Routes
// =====================================================
Route::prefix('director')->name('director.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Director\DirectorController::class, 'dashboard'])->name('dashboard');
    Route::get('/lpos', [App\Http\Controllers\Director\LpoController::class, 'index'])->name('lpos.index')->middleware('permission:view_lpos');
    Route::get('/lpos/{id}', [App\Http\Controllers\Director\LpoController::class, 'show'])->name('lpos.show')->middleware('permission:view_lpos');
    Route::post('/lpos/{id}/approve', [App\Http\Controllers\Director\LpoController::class, 'approve'])->name('lpos.approve')->middleware('permission:approve_lpos');
    Route::post('/lpos/{id}/reject', [App\Http\Controllers\Director\LpoController::class, 'reject'])->name('lpos.reject')->middleware('permission:approve_lpos');
    Route::get('/lpos/{id}/download-pdf', [App\Http\Controllers\Director\LpoController::class, 'downloadPdf'])->name('lpos.download-pdf')->middleware('permission:view_lpos');
});

// =====================================================
// KITCHEN MODULE Routes
// =====================================================
Route::prefix('kitchen')->name('kitchen.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Kitchen\KitchenController::class, 'dashboard'])->name('dashboard');

    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Kitchen\RequisitionController::class, 'index'])->name('index')->middleware('permission:view_requisitions');
        Route::get('/create', [App\Http\Controllers\Kitchen\RequisitionController::class, 'create'])->name('create')->middleware('permission:create_requisitions');
        Route::post('/', [App\Http\Controllers\Kitchen\RequisitionController::class, 'store'])->name('store')->middleware('permission:create_requisitions');
        Route::get('/{id}', [App\Http\Controllers\Kitchen\RequisitionController::class, 'show'])->name('show')->middleware('permission:view_requisitions');
        Route::delete('/{id}/cancel', [App\Http\Controllers\Kitchen\RequisitionController::class, 'cancel'])->name('cancel')->middleware('permission:edit_requisitions');
    });

    Route::prefix('consumption')->name('consumption.')->group(function () {
        Route::get('/', [App\Http\Controllers\Kitchen\KitchenConsumptionController::class, 'index'])->name('index')->middleware('permission:view_consumption');
        Route::get('/{requisitionId}/create', [App\Http\Controllers\Kitchen\KitchenConsumptionController::class, 'create'])->name('create')->middleware('permission:record_consumption');
        Route::post('/{requisitionId}/store', [App\Http\Controllers\Kitchen\KitchenConsumptionController::class, 'store'])->name('store')->middleware('permission:record_consumption');
        Route::get('/history', [App\Http\Controllers\Kitchen\KitchenConsumptionController::class, 'history'])->name('history')->middleware('permission:view_consumption');
    });
});

// =====================================================
// RESTAURANT MODULE Routes
// =====================================================
Route::prefix('restaurant')->name('restaurant.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Restaurant\RestaurantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications/check', [App\Http\Controllers\Restaurant\NotificationController::class, 'check'])->name('notifications.check')->middleware('permission:view_restaurant');

    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'index'])->name('index')->middleware('permission:view_requisitions');
        Route::get('/create', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'create'])->name('create')->middleware('permission:create_requisitions');
        Route::post('/', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'store'])->name('store')->middleware('permission:create_requisitions');
        Route::get('/{id}', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'show'])->name('show')->middleware('permission:view_requisitions');
        Route::delete('/{id}/cancel', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'cancel'])->name('cancel')->middleware('permission:edit_requisitions');
        Route::get('/{id}/consume', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'consumeForm'])->name('consume')->middleware('permission:record_consumption');
        Route::post('/{id}/record-consumption', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'recordConsumption'])->name('record-consumption')->middleware('permission:record_consumption');
    });

    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\MenuController::class, 'index'])->name('index')->middleware('permission:view_menus');
        Route::get('/create', [App\Http\Controllers\Restaurant\MenuController::class, 'create'])->name('create')->middleware('permission:create_menus');
        Route::post('/', [App\Http\Controllers\Restaurant\MenuController::class, 'store'])->name('store')->middleware('permission:create_menus');
        Route::get('/{id}/edit', [App\Http\Controllers\Restaurant\MenuController::class, 'edit'])->name('edit')->middleware('permission:edit_menus');
        Route::put('/{id}', [App\Http\Controllers\Restaurant\MenuController::class, 'update'])->name('update')->middleware('permission:edit_menus');
        Route::delete('/{id}', [App\Http\Controllers\Restaurant\MenuController::class, 'destroy'])->name('destroy')->middleware('permission:delete_menus');
        Route::patch('/{id}/toggle-status', [App\Http\Controllers\Restaurant\MenuController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:edit_menus');
    });

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\SalesController::class, 'index'])->name('index')->middleware('permission:view_sales');
        Route::get('/pos', [App\Http\Controllers\Restaurant\SalesController::class, 'pos'])->name('pos')->middleware('permission:access_pos');
        Route::post('/store', [App\Http\Controllers\Restaurant\SalesController::class, 'store'])->name('store')->middleware('permission:create_sales');
        Route::get('/{id}', [App\Http\Controllers\Restaurant\SalesController::class, 'show'])->name('show')->middleware('permission:view_sales');
        Route::get('/receipt/{id}', [App\Http\Controllers\Restaurant\SalesController::class, 'receipt'])->name('receipt')->middleware('permission:view_sales');
        Route::get('/report/daily', [App\Http\Controllers\Restaurant\SalesController::class, 'dailyReport'])->name('report.daily')->middleware('permission:view_sales_reports');
        Route::get('/export/excel', [App\Http\Controllers\Restaurant\SalesController::class, 'exportExcel'])->name('export.excel')->middleware('permission:export_reports');
        Route::get('/export/pdf', [App\Http\Controllers\Restaurant\SalesController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:export_reports');
    });

    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\RestaurantStockController::class, 'index'])->name('index')->middleware('permission:view_stock');
        Route::get('/summary', [App\Http\Controllers\Restaurant\RestaurantStockController::class, 'getSummary'])->name('summary')->middleware('permission:view_stock');
    });

    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Restaurant\RestaurantReturnController::class, 'index'])->name('index')->middleware('permission:view_returns');
        Route::get('/create/{requisition_id}', [App\Http\Controllers\Restaurant\RestaurantReturnController::class, 'create'])->name('create')->middleware('permission:create_returns');
        Route::post('/store', [App\Http\Controllers\Restaurant\RestaurantReturnController::class, 'store'])->name('store')->middleware('permission:create_returns');
        Route::get('/{id}', [App\Http\Controllers\Restaurant\RestaurantReturnController::class, 'show'])->name('show')->middleware('permission:view_returns');
    });

    Route::get('/profile', [App\Http\Controllers\Restaurant\ProfileController::class, 'index'])->name('profile')->middleware('permission:view_profile');
    Route::put('/profile', [App\Http\Controllers\Restaurant\ProfileController::class, 'update'])->name('profile.update')->middleware('permission:edit_profile');
    Route::get('/settings', [App\Http\Controllers\Restaurant\SettingsController::class, 'index'])->name('settings')->middleware('permission:view_settings');
});

// =====================================================
// CASHIER MODULE Routes
// =====================================================
Route::prefix('restaurant/cashier')->name('restaurant.cashier.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Restaurant\CashierPosController::class, 'dashboard'])->name('dashboard');
    Route::get('/pos', [App\Http\Controllers\Restaurant\CashierPosController::class, 'pos'])->name('pos')->middleware('permission:access_pos');
    Route::get('/menu', [App\Http\Controllers\Restaurant\CashierPosController::class, 'menu'])->name('menu')->middleware('permission:view_menus');
    Route::get('/orders', [App\Http\Controllers\Restaurant\CashierPosController::class, 'orders'])->name('orders')->middleware('permission:view_sales');
    Route::post('/create-invoice', [App\Http\Controllers\Restaurant\CashierPosController::class, 'createInvoice'])->name('create-invoice')->middleware('permission:create_sales');
    Route::get('/invoice/{id}', [App\Http\Controllers\Restaurant\CashierPosController::class, 'getInvoice'])->name('invoice')->middleware('permission:view_sales');
    Route::post('/sale/{id}/pay', [App\Http\Controllers\Restaurant\CashierPosController::class, 'markAsPaid'])->name('mark-as-paid')->middleware('permission:process_payments');
    Route::get('/receipt/{id}', [App\Http\Controllers\Restaurant\CashierPosController::class, 'getReceipt'])->name('receipt')->middleware('permission:view_sales');
    Route::get('/orders/{id}', [App\Http\Controllers\Restaurant\CashierPosController::class, 'showOrder'])->name('orders.show')->middleware('permission:view_sales');
    Route::get('/my-sales', [App\Http\Controllers\Restaurant\CashierSalesController::class, 'index'])->name('my-sales')->middleware('permission:view_own_sales');
    Route::get('/my-sales/export/excel', [App\Http\Controllers\Restaurant\CashierSalesController::class, 'exportExcel'])->name('my-sales.export.excel')->middleware('permission:export_reports');
    Route::get('/my-sales/export/pdf', [App\Http\Controllers\Restaurant\CashierSalesController::class, 'exportPdf'])->name('my-sales.export.pdf')->middleware('permission:export_reports');
    Route::get('/daily-summary', [App\Http\Controllers\Restaurant\CashierPosController::class, 'dailySummary'])->name('daily-summary')->middleware('permission:view_sales_reports');
    Route::get('/reports', [App\Http\Controllers\Restaurant\CashierPosController::class, 'reports'])->name('reports')->middleware('permission:view_sales_reports');
    Route::get('/reports/daily', [App\Http\Controllers\Restaurant\CashierPosController::class, 'dailyReport'])->name('reports.daily')->middleware('permission:view_sales_reports');
    Route::get('/reports/export/excel', [App\Http\Controllers\Restaurant\CashierPosController::class, 'exportExcel'])->name('reports.export.excel')->middleware('permission:export_reports');
    Route::get('/reports/export/pdf', [App\Http\Controllers\Restaurant\CashierPosController::class, 'exportPdf'])->name('reports.export.pdf')->middleware('permission:export_reports');
});

// =====================================================
// BAR MODULE Routes
// =====================================================
Route::prefix('bar')->name('bar.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Bar\BarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Bar\BarProfileController::class, 'edit'])->name('profile.edit')->middleware('permission:view_profile');
    Route::put('/profile', [App\Http\Controllers\Bar\BarProfileController::class, 'update'])->name('profile.update')->middleware('permission:edit_profile');
    Route::get('/pos', [App\Http\Controllers\Bar\BarPosController::class, 'index'])->name('pos')->middleware('permission:access_pos');
    Route::post('/create-invoice', [App\Http\Controllers\Bar\BarPosController::class, 'createInvoice'])->name('create-invoice')->middleware('permission:create_sales');
    Route::post('/sale/{id}/pay', [App\Http\Controllers\Bar\BarPosController::class, 'markAsPaid'])->name('mark-as-paid')->middleware('permission:process_payments');
    Route::get('/orders', [App\Http\Controllers\Bar\BarPosController::class, 'orders'])->name('orders')->middleware('permission:view_sales');
    Route::get('/orders/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'showOrder'])->name('orders.show')->middleware('permission:view_sales');
    Route::get('/invoice/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'getInvoice'])->name('invoice')->middleware('permission:view_sales');
    Route::get('/receipt/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'getReceipt'])->name('receipt')->middleware('permission:view_sales');
    Route::get('/stock', [App\Http\Controllers\Bar\BarStockController::class, 'index'])->name('stock.index')->middleware('permission:view_stock');
    Route::get('/stock/{id}', [App\Http\Controllers\Bar\BarStockController::class, 'show'])->name('stock.show')->middleware('permission:view_stock');

    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bar\BarRequisitionController::class, 'index'])->name('index')->middleware('permission:view_requisitions');
        Route::get('/create', [App\Http\Controllers\Bar\BarRequisitionController::class, 'create'])->name('create')->middleware('permission:create_requisitions');
        Route::post('/', [App\Http\Controllers\Bar\BarRequisitionController::class, 'store'])->name('store')->middleware('permission:create_requisitions');
        Route::get('/{id}', [App\Http\Controllers\Bar\BarRequisitionController::class, 'show'])->name('show')->middleware('permission:view_requisitions');
        Route::put('/{id}/cancel', [App\Http\Controllers\Bar\BarRequisitionController::class, 'cancel'])->name('cancel')->middleware('permission:edit_requisitions');
        Route::get('/{id}/consume', [App\Http\Controllers\Bar\BarRequisitionController::class, 'consumeForm'])->name('consume')->middleware('permission:record_consumption');
        Route::post('/{id}/consume', [App\Http\Controllers\Bar\BarRequisitionController::class, 'recordConsumption'])->name('consume.store')->middleware('permission:record_consumption');
    });

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bar\BarSalesController::class, 'index'])->name('index')->middleware('permission:view_sales');
        Route::get('/export/excel', [App\Http\Controllers\Bar\BarSalesController::class, 'exportExcel'])->name('export.excel')->middleware('permission:export_reports');
        Route::get('/export/pdf', [App\Http\Controllers\Bar\BarSalesController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:export_reports');
    });

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bar\BarInvoiceController::class, 'index'])->name('index')->middleware('permission:view_sales');
        Route::get('/{id}', [App\Http\Controllers\Bar\BarInvoiceController::class, 'show'])->name('show')->middleware('permission:view_sales');
        Route::get('/receipt/{id}', [App\Http\Controllers\Bar\BarInvoiceController::class, 'receipt'])->name('receipt')->middleware('permission:view_sales');
    });

    Route::prefix('cashiers')->name('cashiers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Bar\BarCashierController::class, 'index'])->name('index')->middleware('permission:view_cashiers');
        Route::get('/{id}/sales', [App\Http\Controllers\Bar\BarCashierController::class, 'sales'])->name('sales')->middleware('permission:view_sales');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [App\Http\Controllers\Bar\BarReportController::class, 'daily'])->name('daily')->middleware('permission:view_sales_reports');
        Route::get('/monthly', [App\Http\Controllers\Bar\BarReportController::class, 'monthly'])->name('monthly')->middleware('permission:view_sales_reports');
        Route::get('/export/excel', [App\Http\Controllers\Bar\BarReportController::class, 'exportExcel'])->name('export.excel')->middleware('permission:export_reports');
        Route::get('/export/pdf', [App\Http\Controllers\Bar\BarReportController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:export_reports');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/check', [App\Http\Controllers\Bar\BarNotificationController::class, 'check'])->name('check')->middleware('permission:view_bar');
        Route::post('/mark-read/{id}', [App\Http\Controllers\Bar\BarNotificationController::class, 'markAsRead'])->name('mark-read')->middleware('permission:view_bar');
        Route::post('/mark-all-read', [App\Http\Controllers\Bar\BarNotificationController::class, 'markAllAsRead'])->name('mark-all-read')->middleware('permission:view_bar');
    });

    Route::get('/my-sales', [App\Http\Controllers\Bar\BarCashierController::class, 'mySales'])->name('my-sales')->middleware('permission:view_own_sales');
    Route::get('/my-sales/export/excel', [App\Http\Controllers\Bar\BarCashierController::class, 'exportExcel'])->name('my-sales.export.excel')->middleware('permission:export_reports');
    Route::get('/my-sales/export/pdf', [App\Http\Controllers\Bar\BarCashierController::class, 'exportPdf'])->name('my-sales.export.pdf')->middleware('permission:export_reports');

    Route::prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Bar\BarCashierController::class, 'dashboard'])->name('dashboard');
        Route::get('/pos', [App\Http\Controllers\Bar\BarPosController::class, 'index'])->name('pos')->middleware('permission:access_pos');
        Route::post('/create-invoice', [App\Http\Controllers\Bar\BarPosController::class, 'createInvoice'])->name('create-invoice')->middleware('permission:create_sales');
        Route::post('/sale/{id}/pay', [App\Http\Controllers\Bar\BarPosController::class, 'markAsPaid'])->name('mark-as-paid')->middleware('permission:process_payments');
        Route::get('/menu', [App\Http\Controllers\Bar\BarPosController::class, 'menu'])->name('menu')->middleware('permission:view_menus');
        Route::get('/orders', [App\Http\Controllers\Bar\BarPosController::class, 'orders'])->name('orders')->middleware('permission:view_sales');
        Route::get('/orders/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'showOrder'])->name('orders.show')->middleware('permission:view_sales');
        Route::post('/create-and-pay', [App\Http\Controllers\Bar\BarPosController::class, 'createAndPayInvoice'])->name('create-and-pay')->middleware('permission:create_sales');
        Route::get('/invoice/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'getInvoice'])->name('invoice')->middleware('permission:view_sales');
        Route::get('/receipt/{id}', [App\Http\Controllers\Bar\BarPosController::class, 'getReceipt'])->name('receipt')->middleware('permission:view_sales');
        Route::get('/invoices', [App\Http\Controllers\Bar\BarPosController::class, 'invoices'])->name('invoices')->middleware('permission:view_sales');
        Route::get('/my-sales', [App\Http\Controllers\Bar\BarCashierController::class, 'mySales'])->name('my-sales')->middleware('permission:view_own_sales');
        Route::get('/my-sales/export/excel', [App\Http\Controllers\Bar\BarCashierController::class, 'exportExcel'])->name('my-sales.export.excel')->middleware('permission:export_reports');
        Route::get('/my-sales/export/pdf', [App\Http\Controllers\Bar\BarCashierController::class, 'exportPdf'])->name('my-sales.export.pdf')->middleware('permission:export_reports');
        Route::get('/products', [App\Http\Controllers\Bar\BarProductController::class, 'index'])->name('products')->middleware('permission:view_inventory');
        Route::get('/reports/monthly', [App\Http\Controllers\Bar\BarCashierController::class, 'monthlyReport'])->name('reports.monthly')->middleware('permission:view_sales_reports');
        Route::get('/reports/export/excel', [App\Http\Controllers\Bar\BarCashierController::class, 'exportReportExcel'])->name('reports.export.excel')->middleware('permission:export_reports');
        Route::get('/reports/export/pdf', [App\Http\Controllers\Bar\BarCashierController::class, 'exportReportPdf'])->name('reports.export.pdf')->middleware('permission:export_reports');
        Route::get('/reports', [App\Http\Controllers\Bar\BarCashierController::class, 'reports'])->name('reports')->middleware('permission:view_sales_reports');
        Route::get('/daily-summary', [App\Http\Controllers\Bar\BarPosController::class, 'dailySummary'])->name('daily-summary')->middleware('permission:view_sales_reports');
    });
});

// =====================================================
// API AND AJAX ROUTES
// =====================================================
Route::get('/kitchen/requisitions/item-details/{id}', [App\Http\Controllers\Kitchen\RequisitionController::class, 'getItemDetails'])->name('kitchen.requisitions.item-details')->middleware(['auth', 'permission:view_requisitions']);
Route::get('/restaurant/requisitions/item-details/{id}', [App\Http\Controllers\Restaurant\RestaurantRequisitionController::class, 'getItemDetails'])->name('restaurant.requisitions.item-details')->middleware(['auth', 'permission:view_requisitions']);

// =====================================================
// NOTIFICATIONS - Global for all users
// =====================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.mark-read');
});


// =====================================================
// WAITER ROUTES
// =====================================================
Route::prefix('waiter')->name('waiter.')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Waiter\WaiterController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:access_waiter_module');

    // Orders
    Route::post('/place-order', [App\Http\Controllers\Waiter\WaiterController::class, 'placeOrder'])
        ->name('place-order')
        ->middleware('permission:create_orders');

    Route::get('/orders/active', [App\Http\Controllers\Waiter\WaiterController::class, 'getActiveOrders'])
        ->name('active-orders')
        ->middleware('permission:view_orders');

    Route::get('/order/{orderId}/status', [App\Http\Controllers\Waiter\WaiterController::class, 'getOrderStatus'])
        ->name('order-status')
        ->middleware('permission:view_orders');

    // Bills
    Route::get('/bills', [App\Http\Controllers\Waiter\WaiterController::class, 'getBills'])
        ->name('bills.index')
        ->middleware('permission:view_orders');

    Route::get('/bills/{orderId}/print', [App\Http\Controllers\Waiter\WaiterController::class, 'printBill'])
        ->name('bills.print')
        ->middleware('permission:print_bills');

    // Products & Categories (AJAX)
    Route::get('/products/category/{categoryId}', [App\Http\Controllers\Waiter\WaiterController::class, 'getProductsByCategory'])
        ->name('products.by-category')
        ->middleware('permission:view_menu');

    Route::get('/products/search', [App\Http\Controllers\Waiter\WaiterController::class, 'searchProducts'])
        ->name('products.search')
        ->middleware('permission:view_menu');
});

// =====================================================
// BAR ORDER TICKETS
// =====================================================
Route::prefix('bar')->name('bar.')->middleware(['auth'])->group(function () {
    Route::get('/order-tickets', [App\Http\Controllers\BarOrdersController::class, 'index'])->name('order-tickets');
    Route::get('/tickets/{id}/print', [App\Http\Controllers\BarOrdersController::class, 'printTicket'])->name('tickets.print');
    Route::post('/tickets/{id}/complete', [App\Http\Controllers\BarOrdersController::class, 'completeTicket'])->name('tickets.complete');
});

// =====================================================
// KITCHEN ORDER TICKETS
// =====================================================
Route::prefix('kitchen')->name('kitchen.')->middleware(['auth'])->group(function () {
    Route::get('/order-tickets', [App\Http\Controllers\KitchenOrdersController::class, 'index'])->name('order-tickets');
    Route::get('/tickets/{id}/print', [App\Http\Controllers\KitchenOrdersController::class, 'printTicket'])->name('tickets.print');
    Route::post('/tickets/{id}/complete', [App\Http\Controllers\KitchenOrdersController::class, 'completeTicket'])->name('tickets.complete');
});

Route::prefix('cafe')->name('cafe.')->middleware(['auth'])->group(function () {
    Route::get('/order-tickets', [App\Http\Controllers\CafeOrdersController::class, 'index'])->name('order-tickets');
    Route::get('/tickets/{id}/print', [App\Http\Controllers\CafeOrdersController::class, 'printTicket'])->name('tickets.print');
    Route::post('/tickets/{id}/complete', [App\Http\Controllers\CafeOrdersController::class, 'completeTicket'])->name('tickets.complete');
});


// =====================================================
// CASHIER ROUTES
// =====================================================
Route::prefix('cashier')->name('cashier.')->middleware(['auth'])->group(function () {

    // Dashboard - Tables view
    Route::get('/', [App\Http\Controllers\CashierController::class, 'index'])
        ->name('index');

    // Bills Management
    Route::get('/bills', [App\Http\Controllers\CashierController::class, 'bills'])
        ->name('bills')
        ->middleware('permission:view_orders');

    // Invoice View
    Route::get('/invoice/{id}', [App\Http\Controllers\CashierController::class, 'getInvoice'])
        ->name('invoice')
        ->middleware('permission:view_orders');

    // Mark as Paid
    Route::post('/mark-paid/{id}', [App\Http\Controllers\CashierController::class, 'markAsPaid'])
        ->name('mark-paid')
        ->middleware('permission:process_payments');

    // Table Order (AJAX)
    Route::get('/table/{tableId}/order', [App\Http\Controllers\CashierController::class, 'getOrderByTable'])
        ->name('table.order')
        ->middleware('permission:view_orders');

    // Process Payment (with amount)
    Route::post('/order/{id}/pay', [App\Http\Controllers\CashierController::class, 'processPayment'])
        ->name('order.pay')
        ->middleware('permission:process_payments');

    // Print Receipt
    Route::get('/receipt/{id}', [App\Http\Controllers\CashierController::class, 'printReceipt'])
        ->name('receipt')
        ->middleware('permission:print_receipts');
});


// =====================================================
// KITCHEN STOCK ROUTES
// =====================================================
Route::prefix('kitchen')->name('kitchen.')->middleware(['auth'])->group(function () {

    // Stock Export PDF - MUST be BEFORE the {id} route
    Route::get('/stock/export-pdf', [App\Http\Controllers\Kitchen\KitchenStockController::class, 'exportPdf'])
        ->name('stock.export-pdf');
        //->middleware('permission:export_stock');

    // Stock View
    Route::get('/stock', [App\Http\Controllers\Kitchen\KitchenStockController::class, 'index'])
        ->name('stock.index')
        ->middleware('permission:view_stock');

    // Stock Details - MUST be AFTER export-pdf
    Route::get('/stock/{id}', [App\Http\Controllers\Kitchen\KitchenStockController::class, 'show'])
        ->name('stock.show')
        ->middleware('permission:view_stock');
});


// Admin Business Settings Routes
Route::prefix('admin/settings')->name('admin.settings.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\BusinessSettingController::class, 'index'])->name('index');
    Route::post('/general', [App\Http\Controllers\Admin\BusinessSettingController::class, 'updateGeneral'])->name('general');
    Route::post('/contact', [App\Http\Controllers\Admin\BusinessSettingController::class, 'updateContact'])->name('contact');
    Route::post('/email', [App\Http\Controllers\Admin\BusinessSettingController::class, 'updateEmail'])->name('email');
    Route::post('/locations', [App\Http\Controllers\Admin\BusinessSettingController::class, 'updateLocations'])->name('locations');
    Route::post('/test-email', [App\Http\Controllers\Admin\BusinessSettingController::class, 'testEmail'])->name('test-email');
    Route::post('/remove-image', [App\Http\Controllers\Admin\BusinessSettingController::class, 'removeLogo'])->name('remove-image');
});

// Signature routes
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/{id}/signature', [App\Http\Controllers\UserController::class, 'signatureForm'])->name('signature-form');
    Route::post('/{id}/signature', [App\Http\Controllers\UserController::class, 'uploadSignature'])->name('upload-signature');
    Route::delete('/{id}/signature', [App\Http\Controllers\UserController::class, 'removeSignature'])->name('remove-signature');
});


// Store Goods Received Notes (GRN) Routes
Route::prefix('store/goods-received')->name('store.goods-received.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Store\GoodsReceivedController::class, 'index'])->name('index')->middleware('permission:view_goods_received');
    Route::get('/create', [App\Http\Controllers\Store\GoodsReceivedController::class, 'create'])->name('create')->middleware('permission:create_goods_received');
    Route::get('/create-for-po/{poId}', [App\Http\Controllers\Store\GoodsReceivedController::class, 'createForPo'])->name('create-for-po')->middleware('permission:create_goods_received');
    Route::post('/', [App\Http\Controllers\Store\GoodsReceivedController::class, 'store'])->name('store')->middleware('permission:create_goods_received');
    Route::get('/{id}', [App\Http\Controllers\Store\GoodsReceivedController::class, 'show'])->name('show')->middleware('permission:view_goods_received');
    Route::get('/{id}/print', [App\Http\Controllers\Store\GoodsReceivedController::class, 'print'])->name('print')->middleware('permission:view_goods_received');
    Route::get('/{id}/pdf', [App\Http\Controllers\Store\GoodsReceivedController::class, 'downloadPdf'])->name('pdf')->middleware('permission:view_goods_received');
});
