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

    // Inventory Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\InventoryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Store\InventoryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Store\InventoryController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Store\InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'destroy'])->name('destroy');
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

    // Stock Movements
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\StockMovementController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Store\StockMovementController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Store\StockMovementController::class, 'store'])->name('store');
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

// Approved LPOs for External PO conversion
Route::get('/approved-lpos', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'approvedLpos'])->name('approved-lpos');
Route::get('/approved-lpos/{id}/convert-to-epo', [App\Http\Controllers\Procurement\LocalPurchaseOrderController::class, 'convertToEpo'])->name('convert-to-epo');
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
        Route::post('/{id}/send-to-store', [App\Http\Controllers\Procurement\GoodsReceivedController::class, 'sendToStore'])->name('send-to-store');
    });


});

// =====================================================
// MANAGEMENT MODULE Routes
// =====================================================
Route::prefix('management')->name('management.')->middleware(['auth', 'management'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Management\ManagementController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports/purchase-orders', [App\Http\Controllers\Management\ManagementController::class, 'reportsPurchaseOrders'])->name('reports.purchase-orders');
    Route::get('/analytics/procurement', [App\Http\Controllers\Management\ManagementController::class, 'analyticsProcurement'])->name('analytics.procurement');
    Route::get('/vendors', [App\Http\Controllers\Management\ManagementController::class, 'vendorsIndex'])->name('vendors.index');


    Route::get('/vendors/{id}', [App\Http\Controllers\Management\ManagementController::class, 'vendorsShow'])->name('vendors.show');

    // In routes/web.php - inside the management group
Route::prefix('requisitions')->name('requisitions.')->group(function () {
    Route::get('/', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'index'])->name('index');
    Route::get('/all', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'all'])->name('all');
    Route::get('/{id}', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'show'])->name('show');
    Route::get('/{id}/approve', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'approveForm'])->name('approve-form');
    Route::post('/{id}/approve', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'approve'])->name('approve');
    Route::get('/requisitions/{id}/edit', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'edit'])->name('requisitions.edit');
    Route::put('/requisitions/{id}', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'update'])->name('requisitions.update');
    Route::post('/{id}/reject', [App\Http\Controllers\Management\ManagementRequisitionController::class, 'reject'])->name('reject');
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
