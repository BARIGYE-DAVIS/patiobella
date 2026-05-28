<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.form')
                ->with('error', 'Please login to access this page.');
        }

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login.form')
                ->with('error', 'Your account has been deactivated.');
        }

        if ($user->is_super_admin) {
            return $next($request);
        }

        // List of routes that don't require permission checks
        $publicRoutes = [
            'dashboard',
            'logout',
            'profile',
            'profile.update',
            'notifications.index',
            'notifications.mark-all-read',
            'notifications.mark-read',
        ];

        $currentRouteName = $request->route()->getName();

        if (in_array($currentRouteName, $publicRoutes)) {
            return $next($request);
        }

        // ONLY check user-specific direct permissions (extra_permissions)
        // IGNORES role-based permissions completely
        if (!$this->userHasDirectPermission($user, $permission)) {
            $errorMessage = $this->getErrorMessage($permission, $request);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 403);
            }

            return redirect()->back()
                ->with('error', $errorMessage);
        }

        return $next($request);
    }

    /**
     * Check ONLY user-specific direct permissions (extra_permissions)
     * IGNORES role-based permissions
     */
    private function userHasDirectPermission($user, $permissionCode)
    {
        // Check only user extra permissions (allowed)
        foreach ($user->userPermissions as $permission) {
            if ($permission->pivot->is_allowed && $permission->code === $permissionCode) {
                return true;
            }
        }

        return false;
    }

    private function getErrorMessage($permission, $request)
    {
        // Map permission codes to user-friendly messages
        $messages = [
            // User permissions
            'view_users' => 'You do not have permission to view users.',
            'create_users' => 'You do not have permission to create users.',
            'edit_users' => 'You do not have permission to edit users.',
            'delete_users' => 'You do not have permission to delete users.',
            'assign_roles' => 'You do not have permission to assign roles.',

            // Role permissions
            'view_roles' => 'You do not have permission to view roles.',
            'create_roles' => 'You do not have permission to create roles.',
            'edit_roles' => 'You do not have permission to edit roles.',
            'delete_roles' => 'You do not have permission to delete roles.',

            // Permission permissions
            'view_permissions' => 'You do not have permission to view permissions.',
            'create_permissions' => 'You do not have permission to create permissions.',
            'edit_permissions' => 'You do not have permission to edit permissions.',
            'delete_permissions' => 'You do not have permission to delete permissions.',

            // Department permissions
            'view_departments' => 'You do not have permission to view departments.',
            'manage_departments' => 'You do not have permission to manage departments.',

            // Inventory permissions
            'view_inventory' => 'You do not have permission to view inventory.',
            'create_inventory' => 'You do not have permission to add inventory items.',
            'edit_inventory' => 'You do not have permission to edit inventory items.',
            'delete_inventory' => 'You do not have permission to delete inventory items.',
            'adjust_inventory' => 'You do not have permission to adjust stock counts.',
            'transfer_inventory' => 'You do not have permission to transfer stock between stores.',

            // Purchase order permissions
            'view_purchase_orders' => 'You do not have permission to view purchase orders.',
            'create_purchase_orders' => 'You do not have permission to create purchase orders.',
            'edit_purchase_orders' => 'You do not have permission to edit purchase orders.',
            'approve_purchase_orders' => 'You do not have permission to approve purchase orders.',
            'send_purchase_orders' => 'You do not have permission to send purchase orders.',
            'delete_purchase_orders' => 'You do not have permission to delete purchase orders.',

            // Vendor permissions
            'view_vendors' => 'You do not have permission to view vendors.',
            'create_vendors' => 'You do not have permission to create vendors.',
            'edit_vendors' => 'You do not have permission to edit vendors.',
            'delete_vendors' => 'You do not have permission to delete vendors.',
            'rate_vendors' => 'You do not have permission to rate vendors.',
            'delete_vendor_ratings' => 'You do not have permission to delete vendor ratings.',

            // Goods received permissions
            'view_goods_received' => 'You do not have permission to view goods received notes.',
            'receive_goods' => 'You do not have permission to receive goods.',

            // Requisition permissions
            'view_requisitions' => 'You do not have permission to view requisitions.',
            'create_requisitions' => 'You do not have permission to create requisitions.',
            'edit_requisitions' => 'You do not have permission to edit requisitions.',
            'delete_requisitions' => 'You do not have permission to delete requisitions.',
            'approve_requisitions' => 'You do not have permission to approve requisitions.',
            'issue_items' => 'You do not have permission to issue items.',
            'return_items' => 'You do not have permission to return items.',

            // Stock movement permissions
            'view_stock_movements' => 'You do not have permission to view stock movements.',
            'create_stock_movements' => 'You do not have permission to create stock movements.',

            // Stock count permissions
            'view_stock_counts' => 'You do not have permission to view stock counts.',
            'create_stock_counts' => 'You do not have permission to create stock counts.',
            'edit_stock_counts' => 'You do not have permission to edit stock counts.',
            'approve_stock_counts' => 'You do not have permission to approve stock counts.',
            'manage_stock_variance_reasons' => 'You do not have permission to manage variance reasons.',

            // Menu permissions
            'view_menus' => 'You do not have permission to view menus.',
            'create_menus' => 'You do not have permission to create menus.',
            'edit_menus' => 'You do not have permission to edit menus.',
            'delete_menus' => 'You do not have permission to delete menus.',

            // Menu item permissions
            'view_menu_items' => 'You do not have permission to view menu items.',
            'create_menu_items' => 'You do not have permission to create menu items.',
            'edit_menu_items' => 'You do not have permission to edit menu items.',
            'delete_menu_items' => 'You do not have permission to delete menu items.',
            'view_menu_item_categories' => 'You do not have permission to view menu item categories.',
            'manage_menu_item_categories' => 'You do not have permission to manage menu item categories.',

            // Recipe permissions
            'view_recipes' => 'You do not have permission to view recipes.',
            'manage_recipes' => 'You do not have permission to manage recipes.',

            // Price permissions
            'view_prices' => 'You do not have permission to view prices.',
            'edit_prices' => 'You do not have permission to edit prices.',

            // Report permissions
            'view_financial_reports' => 'You do not have permission to view financial reports.',
            'view_sales_reports' => 'You do not have permission to view sales reports.',
            'export_reports' => 'You do not have permission to export reports.',

            // Sales permissions
            'view_sales' => 'You do not have permission to view sales.',
            'create_sales' => 'You do not have permission to create sales.',
            'access_pos' => 'You do not have permission to access the Point of Sale.',
            'process_payments' => 'You do not have permission to process payments.',
            'view_own_sales' => 'You do not have permission to view your sales.',
            'view_cashiers' => 'You do not have permission to view cashiers.',

            // Stock permissions
            'view_stock' => 'You do not have permission to view stock.',
            'view_returns' => 'You do not have permission to view returns.',
            'create_returns' => 'You do not have permission to create returns.',
            'record_consumption' => 'You do not have permission to record consumption.',
            'view_consumption' => 'You do not have permission to view consumption.',

            // Profile permissions
            'view_profile' => 'You do not have permission to view profile.',
            'edit_profile' => 'You do not have permission to edit profile.',
            'view_settings' => 'You do not have permission to view settings.',

            // LPO permissions
            'view_lpos' => 'You do not have permission to view LPOs.',
            'approve_lpos' => 'You do not have permission to approve LPOs.',

            // Category permissions
            'view_categories' => 'You do not have permission to view categories.',
            'manage_categories' => 'You do not have permission to manage categories.',

            // Module access permissions
            'access_store_module' => 'You do not have permission to access the Store module.',
            'access_procurement_module' => 'You do not have permission to access the Procurement module.',
            'access_management_module' => 'You do not have permission to access the Management module.',
            'access_director_module' => 'You do not have permission to access the Director module.',
            'access_kitchen_module' => 'You do not have permission to access the Kitchen module.',
            'access_restaurant_module' => 'You do not have permission to access the Restaurant module.',
            'access_cashier_module' => 'You do not have permission to access the Cashier module.',
            'access_bar_module' => 'You do not have permission to access the Bar module.',
            // Add to the existing $messages array
            'download_files' => 'You do not have permission to download files.',
            'read_documents' => 'You do not have permission to view documents.',
            'upload_documents' => 'You do not have permission to upload documents.',

            // View permissions for each module
            'view_procurement' => 'You do not have permission to view Procurement.',
            'view_restaurant' => 'You do not have permission to view Restaurant.',
            'view_bar' => 'You do not have permission to view Bar.',
            'view_notifications' => 'You do not have permission to view notifications.',
        ];

        if (isset($messages[$permission])) {
            return $messages[$permission];
        }

        $routeName = $request->route()->getName();
        if ($routeName) {
            $parts = explode('.', $routeName);
            $action = end($parts);
            $resource = $parts[count($parts) - 2] ?? 'page';

            $actionMessages = [
                'index' => 'view',
                'create' => 'create',
                'store' => 'create',
                'show' => 'view',
                'edit' => 'edit',
                'update' => 'edit',
                'destroy' => 'delete',
                'approve' => 'approve',
                'reject' => 'reject',
                'activate' => 'activate',
                'deactivate' => 'deactivate',
            ];

            $actionText = $actionMessages[$action] ?? 'access';
            $resourceName = str_replace('-', ' ', $resource);

            return "You do not have permission to {$actionText} {$resourceName}.";
        }

        return 'You do not have permission to access this page.';
    }
}
