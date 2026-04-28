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
    // STORE MODULE Routes
    // =====================================================
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
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
// STORE MODULE Routes
// =====================================================
Route::prefix('store')->name('store.')->group(function () {
    Route::get('/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
    
    // Inventory routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\InventoryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Store\InventoryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Store\InventoryController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Store\InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Store\InventoryController::class, 'destroy'])->name('destroy');
    });
});

// =====================================================
// STORE MODULE - Complete Routes
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
        Route::put('/requisitions/{id}', [RequisitionController::class, 'update'])->name('store.requisitions.update');
    });
    
    
    // Goods Received Notes (GRN)
    Route::prefix('grn')->name('grn.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\GoodsReceivedNoteController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Store\GoodsReceivedNoteController::class, 'show'])->name('show');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Store\ReportController::class, 'index'])->name('index');
        Route::get('/stock', [App\Http\Controllers\Store\ReportController::class, 'stock'])->name('stock');
        Route::get('/movements', [App\Http\Controllers\Store\ReportController::class, 'movements'])->name('movements');
    });
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


// =====================================================
// PROCUREMENT MODULE Routes
// =====================================================
Route::prefix('procurement')->name('procurement.')->middleware(['auth', 'procurement'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Procurement\ProcurementDashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications/check', [App\Http\Controllers\Procurement\NotificationController::class, 'check'])->name('notifications.check');
    // Requisitions (View only - from Store)
    Route::prefix('requisitions')->name('requisitions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'approve'])->name('approve');
       
        Route::post('/{id}/reject', [App\Http\Controllers\Procurement\ProcurementRequisitionController::class, 'reject'])->name('reject');
        // Procurement Notifications
       
    });


    // Management Routes
Route::prefix('management')->name('management.')->middleware(['auth', 'management'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Management\ManagementController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports/purchase-orders', [App\Http\Controllers\Management\ManagementController::class, 'reportsPurchaseOrders'])->name('reports.purchase-orders');
    Route::get('/analytics/procurement', [App\Http\Controllers\Management\ManagementController::class, 'analyticsProcurement'])->name('analytics.procurement');
    Route::get('/vendors', [App\Http\Controllers\Management\ManagementController::class, 'vendorsIndex'])->name('vendors.index');
    Route::get('/vendors/{id}', [App\Http\Controllers\Management\ManagementController::class, 'vendorsShow'])->name('vendors.show');
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
    // Add these routes inside the procurement group
Route::post('/purchase-orders/{id}/resend-email', [PurchaseOrderController::class, 'resendEmail'])->name('purchase-orders.resend-email');
Route::get('/purchase-orders/{id}/download-pdf', [PurchaseOrderController::class, 'downloadPdf'])->name('purchase-orders.download-pdf');

    
    
    
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
    
    // Goods Received Notes (GRN)
    Route::prefix('grn')->name('grn.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\GoodsReceivedNoteController::class, 'index'])->name('index');
        Route::get('/create/{purchase_order_id}', [App\Http\Controllers\Procurement\GoodsReceivedNoteController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Procurement\GoodsReceivedNoteController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Procurement\GoodsReceivedNoteController::class, 'show'])->name('show');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Procurement\ProcurementReportController::class, 'index'])->name('index');
        Route::get('/purchase-orders', [App\Http\Controllers\Procurement\ProcurementReportController::class, 'purchaseOrders'])->name('purchase-orders');
        Route::get('/vendors', [App\Http\Controllers\Procurement\ProcurementReportController::class, 'vendors'])->name('vendors');
    });
});

    

});